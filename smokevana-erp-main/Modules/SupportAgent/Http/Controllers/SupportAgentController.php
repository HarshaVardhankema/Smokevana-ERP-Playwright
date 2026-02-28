<?php

namespace Modules\SupportAgent\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Modules\SupportAgent\Services\LLMService;
use Modules\SupportAgent\Services\FeatureDocumentationService;
use Modules\SupportAgent\Services\MCPService;

class SupportAgentController extends Controller
{
    protected $llmService;
    protected $docService;
    protected $mcpService;

    public function __construct(LLMService $llmService, FeatureDocumentationService $docService, MCPService $mcpService)
    {
        $this->llmService = $llmService;
        $this->docService = $docService;
        $this->mcpService = $mcpService;
    }

    /**
     * Display the support agent chat interface
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('support_agent.access')) {
            // Check if user has any permission (basic access fallback)
            if (!auth()->check()) {
                abort(403, 'Unauthorized action.');
            }
        }

        $isConfigured = $this->llmService->isConfigured();
        $currentPage = request()->header('referer', '');
        $suggestedQuestions = $this->llmService->getSuggestedQuestions($currentPage);
        
        // Get conversation history from session
        $conversationHistory = Session::get('support_agent_history', []);
        
        return view('supportagent::chat.index', compact(
            'isConfigured',
            'suggestedQuestions',
            'conversationHistory'
        ));
    }

    /**
     * Process a chat message and return AI response
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'current_page' => 'nullable|string',
        ]);

        $userMessage = $request->input('message');
        $currentPage = $request->input('current_page', '');
        
        // Get user context
        $userRole = auth()->user()->role ?? 'User';
        
        // Get relevant documentation based on the query
        $featureContext = $this->docService->getRelevantDocumentation($userMessage);
        
        // Get MCP code context - this analyzes the actual codebase
        $mcpData = $this->mcpService->getCodeContext($userMessage);
        $mcpContext = $mcpData['context'] ?? '';
        
        // Get conversation history from session
        $conversationHistory = Session::get('support_agent_history', []);
        
        // Build context for LLM with MCP data
        $context = [
            'feature_context' => $featureContext,
            'current_page' => $currentPage,
            'user_role' => $userRole,
            'mcp_context' => $mcpContext, // Add MCP context to LLM
        ];
        
        // Get AI response
        $response = $this->llmService->chat($userMessage, $context, $conversationHistory);
        
        if ($response['success']) {
            // Store conversation in session
            $conversationHistory[] = ['role' => 'user', 'content' => $userMessage];
            $conversationHistory[] = ['role' => 'assistant', 'content' => $response['message']];
            
            // Keep only last N messages
            $maxHistory = config('supportagent.chat.max_history', 50);
            if (count($conversationHistory) > $maxHistory) {
                $conversationHistory = array_slice($conversationHistory, -$maxHistory);
            }
            
            Session::put('support_agent_history', $conversationHistory);
        }
        
        return response()->json([
            'success' => $response['success'],
            'message' => $response['message'],
            'timestamp' => now()->format('H:i'),
            'mcp_enabled' => $mcpData['enabled'] ?? false,
            'mcp_files' => $mcpData['files'] ?? [],
        ]);
    }

    /**
     * Get suggested questions based on current context
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSuggestions(Request $request)
    {
        $currentPage = $request->input('current_page', '');
        $suggestions = $this->llmService->getSuggestedQuestions($currentPage);
        
        return response()->json([
            'success' => true,
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * Clear conversation history
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearHistory()
    {
        Session::forget('support_agent_history');
        
        return response()->json([
            'success' => true,
            'message' => 'Conversation history cleared.',
        ]);
    }

    /**
     * Get feature documentation
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDocumentation(Request $request)
    {
        $feature = $request->input('feature', '');
        
        if (empty($feature)) {
            return response()->json([
                'success' => false,
                'message' => 'Feature parameter is required.',
            ]);
        }
        
        $documentation = $this->docService->getRelevantDocumentation($feature);
        
        return response()->json([
            'success' => true,
            'documentation' => $documentation,
            'navigation_paths' => $this->docService->getNavigationPaths(),
        ]);
    }

    /**
     * Get quick help widget data (for embedding in other pages)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function widget(Request $request)
    {
        $currentPage = $request->input('current_page', '');
        $suggestions = $this->llmService->getSuggestedQuestions($currentPage);
        
        return response()->json([
            'success' => true,
            'is_configured' => $this->llmService->isConfigured(),
            'suggestions' => $suggestions,
            'welcome_message' => __('supportagent::lang.welcome_message'),
        ]);
    }

    /**
     * Get MCP code context analysis for a query
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMcpContext(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:500',
        ]);

        $query = $request->input('query');
        $mcpData = $this->mcpService->getCodeContext($query);

        return response()->json([
            'success' => true,
            'enabled' => $mcpData['enabled'],
            'keywords' => $mcpData['keywords'] ?? [],
            'files' => $mcpData['files'] ?? [],
            'routes' => $mcpData['routes'] ?? [],
            'flow' => $mcpData['flow'] ?? [],
            'context' => $mcpData['context'] ?? '',
        ]);
    }

    /**
     * Get codebase structure overview
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCodebaseStructure()
    {
        $structure = $this->mcpService->getCodebaseStructure();

        return response()->json([
            'success' => true,
            'structure' => $structure,
        ]);
    }
}
