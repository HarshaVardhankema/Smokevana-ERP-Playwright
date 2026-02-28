<?php

return [
    'name' => 'SupportAgent',
    'alias' => 'supportagent',
    'description' => 'AI-powered Support Agent module that helps users understand and use system features.',
    'module_version' => '1.0.0',
    
    // LLM Configuration
    'llm' => [
        'provider' => env('SUPPORT_AGENT_LLM_PROVIDER', 'gemini'), // gemini, openai, anthropic
        'model' => env('SUPPORT_AGENT_LLM_MODEL', 'gemini-1.5-flash'),
        'api_key' => env('SUPPORT_AGENT_API_KEY', env('GEMINI_API_KEY')),
        'max_tokens' => env('SUPPORT_AGENT_MAX_TOKENS', 2000),
        'temperature' => env('SUPPORT_AGENT_TEMPERATURE', 0.7),
        'endpoint' => env('SUPPORT_AGENT_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/models'),
    ],
    
    // MCP Configuration for code context
    'mcp' => [
        'enabled' => env('SUPPORT_AGENT_MCP_ENABLED', true),
        'context_depth' => env('SUPPORT_AGENT_CONTEXT_DEPTH', 3), // How deep to analyze code relationships
    ],
    
    // Chat settings
    'chat' => [
        'max_history' => 50, // Maximum messages to keep in history
        'session_timeout' => 3600, // Session timeout in seconds
        'enable_code_snippets' => true,
        'enable_step_by_step' => true,
    ],
];
