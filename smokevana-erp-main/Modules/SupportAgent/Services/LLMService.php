<?php

namespace Modules\SupportAgent\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class LLMService
{
    protected $config;
    protected $provider;
    protected $model;
    protected $apiKey;
    protected $maxTokens;
    protected $temperature;
    protected $endpoint;

    public function __construct(array $config = [])
    {
        $this->config = $config ?: config('supportagent.llm');
        $this->provider = $this->config['provider'] ?? 'gemini';
        $this->model = $this->config['model'] ?? 'gemini-2.5-flash';
        $this->apiKey = $this->config['api_key'] ?? env('GEMINI_API_KEY');
        $this->maxTokens = $this->config['max_tokens'] ?? 2000;
        $this->temperature = $this->config['temperature'] ?? 0.7;
        $this->endpoint = $this->config['endpoint'] ?? 'https://generativelanguage.googleapis.com/v1beta/models';
    }

    /**
     * Send a message to the LLM and get a response
     *
     * @param string $userMessage
     * @param array $context
     * @param array $conversationHistory
     * @return array
     */
    public function chat(string $userMessage, array $context = [], array $conversationHistory = []): array
    {
        try {
            // Check if API key is configured
            if (empty($this->apiKey)) {
                Log::warning('SupportAgent: No API key configured. Provider: ' . $this->provider);
                return [
                    'success' => false,
                    'message' => 'AI service is not configured. Please add your API key to the .env file (GEMINI_API_KEY or SUPPORT_AGENT_API_KEY).',
                    'error' => 'No API key configured',
                ];
            }

            $systemPrompt = $this->buildSystemPrompt($context);
            $messages = $this->buildMessages($systemPrompt, $userMessage, $conversationHistory);

            Log::info('SupportAgent: Sending request to ' . $this->provider . ' using model: ' . $this->model);
            
            $response = $this->sendRequest($messages);
            
            return [
                'success' => true,
                'message' => $response['content'] ?? 'I apologize, but I couldn\'t generate a response.',
                'usage' => $response['usage'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('SupportAgent LLM Error: ' . $e->getMessage(), [
                'provider' => $this->provider,
                'model' => $this->model,
                'api_key_set' => !empty($this->apiKey),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return [
                'success' => false,
                'message' => 'I apologize, but I\'m having trouble connecting to the AI service. Error: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build the system prompt with context
     *
     * @param array $context
     * @return string
     */
    protected function buildSystemPrompt(array $context = []): string
    {
        $featureContext = $context['feature_context'] ?? '';
        $currentPage = $context['current_page'] ?? '';
        $userRole = $context['user_role'] ?? '';
        $mcpContext = $context['mcp_context'] ?? '';
        
        $prompt = <<<PROMPT
You are a friendly Support Agent for Smokevana ERP. Your role is to help users understand and use the system effectively.

## CRITICAL RULES:
1. **NEVER show code, file paths, or technical implementation details to users**
2. **Use the MCP context internally** to understand features, but explain in plain, user-friendly language
3. **Focus on what users can DO**, not how it's built
4. **Describe UI elements**: buttons, menus, forms, fields - things users can see and interact with

## Your Capabilities:
1. **Feature Guidance**: Explain how to use features with step-by-step instructions
2. **Workflow Assistance**: Guide users through business processes
3. **Troubleshooting**: Help resolve common issues
4. **Best Practices**: Suggest optimal ways to work

## System Modules:
- **POS (Point of Sale)**: Process sales, manage transactions, handle payments
- **Inventory Management**: Stock tracking, adjustments, transfers
- **Contacts**: Customers, suppliers, leads management
- **Products**: Catalog, variations, pricing, categories, brands
- **Offer Management System**: Discounts, coupons, promotions, BOGO offers
- **Sales & Invoicing**: Quotations, orders, invoices, returns
- **Purchases**: Purchase orders, receiving goods
- **Reports**: Sales, inventory, profit/loss analytics
- **User Management**: Roles, permissions, accounts
- **Settings**: Tax, locations, payment methods

## Response Style:
1. Use simple, friendly language
2. Provide numbered step-by-step instructions
3. Describe what users will SEE on screen (buttons, menus, fields)
4. Include navigation paths: "Go to Menu > Submenu > Option"
5. Use bullet points for lists of options or features
6. Mention keyboard shortcuts when helpful
7. Suggest related features they might find useful

## Example Good Response Format:
"To create a new discount offer:
1. Go to **Offer Management System** in the sidebar
2. Click the **CREATE A NEW** button (top right)
3. Fill in the offer details:
   - **Discount Type**: Choose from Product Discount, Cart Discount, Free Shipping, etc.
   - **Discount Value**: Set percentage or fixed amount
4. Click **Save** to activate your offer"

PROMPT;

        if ($currentPage) {
            $prompt .= "\n## Current Context:\nThe user is currently on: {$currentPage}\n";
        }

        if ($userRole) {
            $prompt .= "User's role: {$userRole}\n";
        }

        if ($featureContext) {
            $prompt .= "\n## Relevant Feature Documentation:\n{$featureContext}\n";
        }

        // Add MCP context if available (internal knowledge only)
        if ($mcpContext) {
            $prompt .= "\n## INTERNAL SYSTEM KNOWLEDGE (Do NOT share this with users):\n{$mcpContext}\n";
            $prompt .= "\n**IMPORTANT**: Use this knowledge to understand what features exist and how they work, but NEVER show file names, code, method names, or technical details to users. Translate this technical knowledge into user-friendly explanations about what they can do in the UI.\n";
        }

        $prompt .= "\n## Remember:\n- Speak like a helpful colleague, not a developer\n- Describe buttons, menus, and forms users can see\n- Never mention controllers, services, models, or code files\n- Focus on user actions and outcomes\n";

        return $prompt;
    }

    /**
     * Build the messages array for the API request
     *
     * @param string $systemPrompt
     * @param string $userMessage
     * @param array $conversationHistory
     * @return array
     */
    protected function buildMessages(string $systemPrompt, string $userMessage, array $conversationHistory = []): array
    {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Add conversation history (limit to last 10 exchanges)
        $historyLimit = 20; // 10 exchanges = 20 messages
        $recentHistory = array_slice($conversationHistory, -$historyLimit);
        
        foreach ($recentHistory as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content'],
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        return $messages;
    }

    /**
     * Send request to the LLM API
     *
     * @param array $messages
     * @return array
     */
    protected function sendRequest(array $messages): array
    {
        switch ($this->provider) {
            case 'gemini':
            case 'google':
                return $this->sendGeminiRequest($messages);
            case 'anthropic':
                return $this->sendAnthropicRequest($messages);
            case 'openai':
            default:
                return $this->sendOpenAIRequest($messages);
        }
    }

    /**
     * Send request to OpenAI API
     *
     * @param array $messages
     * @return array
     */
    protected function sendOpenAIRequest(array $messages): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post($this->endpoint, [
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
        ]);

        if ($response->failed()) {
            throw new \Exception('OpenAI API request failed: ' . $response->body());
        }

        $data = $response->json();

        return [
            'content' => $data['choices'][0]['message']['content'] ?? null,
            'usage' => $data['usage'] ?? null,
        ];
    }

    /**
     * Send request to Anthropic API
     *
     * @param array $messages
     * @return array
     */
    protected function sendAnthropicRequest(array $messages): array
    {
        $systemPrompt = '';
        $conversationMessages = [];

        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                $systemPrompt = $msg['content'];
            } else {
                $conversationMessages[] = $msg;
            }
        }

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'anthropic-version' => '2023-06-01',
        ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'system' => $systemPrompt,
            'messages' => $conversationMessages,
        ]);

        if ($response->failed()) {
            throw new \Exception('Anthropic API request failed: ' . $response->body());
        }

        $data = $response->json();

        return [
            'content' => $data['content'][0]['text'] ?? null,
            'usage' => [
                'input_tokens' => $data['usage']['input_tokens'] ?? 0,
                'output_tokens' => $data['usage']['output_tokens'] ?? 0,
            ],
        ];
    }

    /**
     * Send request to Google Gemini API
     *
     * @param array $messages
     * @return array
     */
    protected function sendGeminiRequest(array $messages): array
    {
        // Build Gemini-compatible content format
        $contents = [];
        $systemInstruction = '';

        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                // Gemini uses systemInstruction for system prompts
                $systemInstruction = $msg['content'];
            } else {
                // Map OpenAI roles to Gemini roles
                $role = $msg['role'] === 'assistant' ? 'model' : 'user';
                $contents[] = [
                    'role' => $role,
                    'parts' => [
                        ['text' => $msg['content']]
                    ]
                ];
            }
        }

        // Build the API URL with model and API key
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        // Build request body
        $requestBody = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => $this->temperature,
                'maxOutputTokens' => $this->maxTokens,
                'topP' => 0.95,
                'topK' => 40,
            ],
        ];

        // Add system instruction if present
        if (!empty($systemInstruction)) {
            $requestBody['systemInstruction'] = [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ];
        }

        // Add safety settings to be less restrictive for business context
        $requestBody['safetySettings'] = [
            [
                'category' => 'HARM_CATEGORY_HARASSMENT',
                'threshold' => 'BLOCK_ONLY_HIGH'
            ],
            [
                'category' => 'HARM_CATEGORY_HATE_SPEECH',
                'threshold' => 'BLOCK_ONLY_HIGH'
            ],
            [
                'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                'threshold' => 'BLOCK_ONLY_HIGH'
            ],
            [
                'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                'threshold' => 'BLOCK_ONLY_HIGH'
            ],
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->timeout(60)->post($url, $requestBody);

        if ($response->failed()) {
            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? $response->body();
            throw new \Exception('Gemini API request failed: ' . $errorMessage);
        }

        $data = $response->json();

        // Extract the response text from Gemini's format
        $content = null;
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            $content = $data['candidates'][0]['content']['parts'][0]['text'];
        }

        // Extract usage metadata if available
        $usage = null;
        if (isset($data['usageMetadata'])) {
            $usage = [
                'prompt_tokens' => $data['usageMetadata']['promptTokenCount'] ?? 0,
                'completion_tokens' => $data['usageMetadata']['candidatesTokenCount'] ?? 0,
                'total_tokens' => $data['usageMetadata']['totalTokenCount'] ?? 0,
            ];
        }

        return [
            'content' => $content,
            'usage' => $usage,
        ];
    }

    /**
     * Get suggested questions based on the current context
     *
     * @param string $currentPage
     * @return array
     */
    public function getSuggestedQuestions(string $currentPage = ''): array
    {
        $suggestions = [
            'general' => [
                'How do I create a new sale?',
                'How do I add a new product?',
                'How do I manage inventory?',
                'How do I generate reports?',
            ],
            'pos' => [
                'How do I process a refund?',
                'How do I apply a discount?',
                'How do I suspend a sale?',
                'How do I use keyboard shortcuts in POS?',
            ],
            'products' => [
                'How do I create product variations?',
                'How do I set up pricing groups?',
                'How do I import products in bulk?',
                'How do I manage stock alerts?',
            ],
            'contacts' => [
                'How do I add a new customer?',
                'How do I manage customer groups?',
                'How do I view customer purchase history?',
                'How do I import contacts?',
            ],
            'inventory' => [
                'How do I do a stock adjustment?',
                'How do I create a purchase order?',
                'How do I transfer stock between locations?',
                'How do I view low stock items?',
            ],
            'reports' => [
                'How do I view sales summary?',
                'How do I export reports to Excel?',
                'How do I see profit/loss report?',
                'How do I view trending products?',
            ],
        ];

        // Determine which suggestions to show based on current page
        $pageSuggestions = $suggestions['general'];
        
        if (stripos($currentPage, 'pos') !== false) {
            $pageSuggestions = array_merge($suggestions['pos'], $suggestions['general']);
        } elseif (stripos($currentPage, 'product') !== false) {
            $pageSuggestions = array_merge($suggestions['products'], $suggestions['general']);
        } elseif (stripos($currentPage, 'contact') !== false || stripos($currentPage, 'customer') !== false) {
            $pageSuggestions = array_merge($suggestions['contacts'], $suggestions['general']);
        } elseif (stripos($currentPage, 'stock') !== false || stripos($currentPage, 'inventory') !== false) {
            $pageSuggestions = array_merge($suggestions['inventory'], $suggestions['general']);
        } elseif (stripos($currentPage, 'report') !== false) {
            $pageSuggestions = array_merge($suggestions['reports'], $suggestions['general']);
        }

        return array_slice($pageSuggestions, 0, 6);
    }

    /**
     * Check if the LLM service is properly configured
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
