# SupportAgent Module

AI-powered Support Agent module that helps users understand and use system features using LLM and MCP integration.

## Features

- **AI Chat Interface**: Beautiful, modern chat interface for interacting with the AI assistant
- **Context-Aware Responses**: The AI understands your current page and provides relevant help
- **Feature Documentation**: Built-in knowledge base of all system features
- **Conversation History**: Maintains chat history within the session
- **Floating Widget**: Optional floating chat button for any page
- **Multiple LLM Support**: Works with Google Gemini (FREE!), OpenAI, Anthropic, or custom endpoints

## Installation

1. The module is already included in your installation
2. Run migrations:
   ```bash
   php artisan module:migrate SupportAgent
   ```

3. Configure your LLM API key in `.env`:
   ```env
   # Google Gemini (FREE TIER - Recommended!)
   GEMINI_API_KEY=AIzaSyDxxxxxxxxxxxxxxxxxxx
   SUPPORT_AGENT_LLM_PROVIDER=gemini
   SUPPORT_AGENT_LLM_MODEL=gemini-1.5-flash
   ```

## Configuration

Add these environment variables to your `.env` file:

```env
# ============================================
# OPTION 1: Google Gemini (FREE - RECOMMENDED)
# ============================================
# Get your free API key from: https://aistudio.google.com/apikey
GEMINI_API_KEY=AIzaSyDxxxxxxxxxxxxxxxxxxx
SUPPORT_AGENT_LLM_PROVIDER=gemini
SUPPORT_AGENT_LLM_MODEL=gemini-1.5-flash

# Available Gemini models:
# - gemini-1.5-flash (fastest, free tier)
# - gemini-1.5-pro (most capable)
# - gemini-1.0-pro

# ============================================
# OPTION 2: OpenAI (Paid)
# ============================================
# SUPPORT_AGENT_API_KEY=sk-your-openai-key
# SUPPORT_AGENT_LLM_PROVIDER=openai
# SUPPORT_AGENT_LLM_MODEL=gpt-4

# ============================================
# OPTION 3: Anthropic Claude (Paid)
# ============================================
# SUPPORT_AGENT_API_KEY=sk-ant-your-key
# SUPPORT_AGENT_LLM_PROVIDER=anthropic
# SUPPORT_AGENT_LLM_MODEL=claude-3-sonnet-20240229

# ============================================
# Optional settings (all providers)
# ============================================
SUPPORT_AGENT_MAX_TOKENS=2000
SUPPORT_AGENT_TEMPERATURE=0.7

# MCP Integration
SUPPORT_AGENT_MCP_ENABLED=true
SUPPORT_AGENT_CONTEXT_DEPTH=3
```

## Usage

### Accessing the Chat Interface

Navigate to **AI Support** in the sidebar menu, or visit `/support-agent`.

### Adding the Floating Widget

To add the floating chat widget to any page, include this in your Blade template:

```blade
@include('supportagent::partials.floating_widget')
```

### API Endpoints

- `POST /support-agent/chat` - Send a message and get AI response
- `GET /support-agent/suggestions` - Get suggested questions
- `POST /support-agent/clear-history` - Clear conversation history
- `GET /support-agent/documentation` - Get feature documentation

### Example API Usage

```javascript
// Send a message
fetch('/support-agent/chat', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({
        message: 'How do I create a new sale?',
        current_page: '/pos/create'
    })
})
.then(response => response.json())
.then(data => {
    console.log(data.message);
});
```

## MCP Integration (Model Context Protocol)

The MCP Service provides **real-time codebase analysis** to give the AI accurate context about how features are actually implemented. This is what makes the Support Agent truly understand your system.

### How MCP Works

1. **Query Analysis**: When a user asks a question, MCP extracts feature keywords (e.g., "pos", "product", "sale")

2. **File Discovery**: MCP maps keywords to relevant source files:
   - Controllers: `app/Http/Controllers/SellPosController.php`
   - Models: `app/Product.php`, `app/Transaction.php`
   - Utils: `app/Utils/TransactionUtil.php`
   - Modules: `Modules/Vendor/Http/Controllers/VendorController.php`

3. **Code Extraction**: Extracts relevant code snippets:
   - Controller method signatures
   - Model relationships and fillable fields
   - Route definitions

4. **Flow Analysis**: Provides step-by-step feature flows:
   ```
   POS Flow:
   1. User navigates to POS (SellPosController@create)
   2. Products loaded via AJAX
   3. Cart managed via JavaScript
   4. Sale saved (SellPosController@store)
   5. TransactionUtil creates transaction
   6. ProductUtil updates stock
   ```

5. **Context Injection**: All this context is injected into the LLM prompt, enabling accurate responses

### MCP API Endpoints

```bash
# Get code context for a query
GET /support-agent/mcp/context?query=how+do+I+create+a+sale

# Get codebase structure overview
GET /support-agent/mcp/structure
```

### MCP Configuration

```env
# Enable/Disable MCP
SUPPORT_AGENT_MCP_ENABLED=true

# How deep to analyze code relationships
SUPPORT_AGENT_CONTEXT_DEPTH=3
```

### What MCP Provides to the LLM

When a user asks "How do I process a refund?", MCP provides:

```
## MCP Code Context

### Feature Flows:
**Sales Return Flow**
1. Go to Sales > List Sales
2. Find the original sale
3. Click Return/Refund action
4. Select items to return (SellReturnController@add)
5. Submit (SellReturnController@store)
6. Stock is automatically added back via ProductUtil

### Available Routes:
- GET /sell-return - List returns
- POST /sell-return - Store return

### Code Structure:
**app/Http/Controllers/SellReturnController.php** (controller):
public function store(Request $request)...
public function add($id)...
```

## Permissions

- `support_agent.access` - Access to the Support Agent (enabled by default)

## Customization

### Adding Custom Documentation

Extend the `FeatureDocumentationService` to add custom documentation:

```php
// In your service provider
app()->extend(FeatureDocumentationService::class, function ($service) {
    // Add custom documentation
    return $service;
});
```

### Custom System Prompt

Override the system prompt by publishing the config:

```bash
php artisan vendor:publish --tag=supportagent-config
```

Then edit `config/supportagent.php`.

## Troubleshooting

### "AI Service Not Configured" Message

This means the API key is not set. Add your OpenAI API key to the `.env` file:

```env
OPENAI_API_KEY=sk-your-key-here
# or
SUPPORT_AGENT_API_KEY=sk-your-key-here
```

### Slow Responses

- Check your internet connection
- Consider using a faster model like `gpt-3.5-turbo`
- Reduce `SUPPORT_AGENT_MAX_TOKENS` for shorter responses

### Menu Not Appearing

Run these commands:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## License

MIT License
