@extends('layouts.app')
@section('title', __('supportagent::lang.support_agent'))

@section('css')
<!-- Amazon Ember Fonts - Applied globally -->
<link rel="stylesheet" href="{{ asset('css/amazon-ember-fonts.css') }}">
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root {
    --agent-primary: #6366f1;
    --agent-primary-dark: #4f46e5;
    --agent-primary-light: #a5b4fc;
    --agent-secondary: #10b981;
    --agent-bg: #0f0f23;
    --agent-surface: #1a1a2e;
    --agent-surface-2: #252542;
    --agent-border: #2d2d4a;
    --agent-text: #e4e4f0;
    --agent-text-muted: #9ca3af;
    --agent-user-bubble: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    --agent-bot-bubble: #252542;
    --agent-accent: #f472b6;
}

.support-agent-container {
    font-family: 'Amazon Ember', -apple-system, BlinkMacSystemFont, sans-serif;
    min-height: calc(100vh - 120px);
    background: var(--agent-bg);
    background-image: 
        radial-gradient(ellipse 80% 50% at 50% -20%, rgba(99, 102, 241, 0.15), transparent),
        radial-gradient(ellipse 60% 40% at 80% 80%, rgba(139, 92, 246, 0.1), transparent);
    padding: 20px;
    display: flex;
    flex-direction: column;
}

.agent-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 24px;
    background: var(--agent-surface);
    border-radius: 16px;
    margin-bottom: 20px;
    border: 1px solid var(--agent-border);
    backdrop-filter: blur(10px);
}

.agent-logo {
    display: flex;
    align-items: center;
    gap: 14px;
}

.agent-avatar {
    width: 48px;
    height: 48px;
    background: var(--agent-user-bubble);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
    animation: pulse-glow 3s ease-in-out infinite;
}

@keyframes pulse-glow {
    0%, 100% { box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3); }
    50% { box-shadow: 0 4px 30px rgba(99, 102, 241, 0.5); }
}

.agent-title-group h1 {
    font-size: 20px;
    font-weight: 700;
    color: var(--agent-text);
    margin: 0;
    letter-spacing: -0.02em;
}

.agent-title-group p {
    font-size: 13px;
    color: var(--agent-text-muted);
    margin: 4px 0 0;
    display: flex;
    align-items: center;
    gap: 6px;
}

.agent-status {
    width: 8px;
    height: 8px;
    background: var(--agent-secondary);
    border-radius: 50%;
    animation: blink 2s ease-in-out infinite;
}

.mcp-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    background: rgba(16, 185, 129, 0.15);
    border: 1px solid rgba(16, 185, 129, 0.3);
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    color: #10b981;
    margin-left: 12px;
}

.mcp-badge.disabled {
    background: rgba(245, 158, 11, 0.15);
    border-color: rgba(245, 158, 11, 0.3);
    color: #f59e0b;
}

.mcp-badge i {
    font-size: 10px;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.agent-actions {
    display: flex;
    gap: 10px;
}

.agent-btn {
    padding: 10px 18px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.agent-btn-outline {
    background: transparent;
    border: 1px solid var(--agent-border);
    color: var(--agent-text-muted);
}

.agent-btn-outline:hover {
    background: var(--agent-surface-2);
    border-color: var(--agent-primary-light);
    color: var(--agent-text);
}

.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: var(--agent-surface);
    border-radius: 20px;
    border: 1px solid var(--agent-border);
    overflow: hidden;
    max-height: calc(100vh - 280px);
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    scroll-behavior: smooth;
}

.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
    background: transparent;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: var(--agent-border);
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: var(--agent-primary-light);
}

.message {
    display: flex;
    gap: 14px;
    max-width: 85%;
    animation: messageSlide 0.3s ease-out;
}

@keyframes messageSlide {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message.user {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.message-avatar {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

.message.bot .message-avatar {
    background: var(--agent-user-bubble);
}

.message.user .message-avatar {
    background: var(--agent-secondary);
}

.message-content {
    flex: 1;
}

.message-bubble {
    padding: 14px 18px;
    border-radius: 16px;
    font-size: 14px;
    line-height: 1.6;
    color: var(--agent-text);
}

.message.bot .message-bubble {
    background: var(--agent-bot-bubble);
    border: 1px solid var(--agent-border);
    border-top-left-radius: 4px;
}

.message.user .message-bubble {
    background: var(--agent-user-bubble);
    border-top-right-radius: 4px;
}

.message-time {
    font-size: 11px;
    color: var(--agent-text-muted);
    margin-top: 6px;
    padding: 0 4px;
}

.message.user .message-time {
    text-align: right;
}

/* Markdown styling in messages */
.message-bubble h2, .message-bubble h3 {
    font-size: 15px;
    font-weight: 600;
    margin: 16px 0 8px;
    color: var(--agent-primary-light);
}

.message-bubble h2:first-child, .message-bubble h3:first-child {
    margin-top: 0;
}

.message-bubble ul, .message-bubble ol {
    margin: 8px 0;
    padding-left: 20px;
}

.message-bubble li {
    margin: 4px 0;
}

.message-bubble code {
    font-family: 'JetBrains Mono', monospace;
    background: rgba(99, 102, 241, 0.2);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 13px;
}

.message-bubble pre {
    background: var(--agent-bg);
    padding: 12px;
    border-radius: 8px;
    overflow-x: auto;
    margin: 10px 0;
}

.message-bubble pre code {
    background: none;
    padding: 0;
}

.message-bubble strong {
    color: var(--agent-accent);
    font-weight: 600;
}

/* Welcome state */
.welcome-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    text-align: center;
    flex: 1;
}

.welcome-icon {
    width: 80px;
    height: 80px;
    background: var(--agent-user-bubble);
    border-radius: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    margin-bottom: 24px;
    box-shadow: 0 8px 32px rgba(99, 102, 241, 0.3);
}

.welcome-state h2 {
    font-size: 24px;
    font-weight: 700;
    color: var(--agent-text);
    margin: 0 0 12px;
}

.welcome-state p {
    font-size: 15px;
    color: var(--agent-text-muted);
    max-width: 500px;
    margin: 0 0 32px;
    line-height: 1.6;
}

.suggestion-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    max-width: 700px;
}

.suggestion-chip {
    padding: 12px 20px;
    background: var(--agent-surface-2);
    border: 1px solid var(--agent-border);
    border-radius: 12px;
    font-size: 13px;
    color: var(--agent-text);
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: left;
}

.suggestion-chip:hover {
    background: rgba(99, 102, 241, 0.15);
    border-color: var(--agent-primary);
    transform: translateY(-2px);
}

.suggestion-chip i {
    color: var(--agent-primary-light);
    margin-right: 8px;
}

/* Chat input area */
.chat-input-area {
    padding: 20px 24px;
    background: var(--agent-surface);
    border-top: 1px solid var(--agent-border);
}

.chat-input-wrapper {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    background: var(--agent-bg);
    border: 1px solid var(--agent-border);
    border-radius: 16px;
    padding: 8px;
    transition: border-color 0.2s ease;
}

.chat-input-wrapper:focus-within {
    border-color: var(--agent-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.chat-input-wrapper textarea {
    flex: 1;
    background: none;
    border: none;
    color: var(--agent-text);
    font-size: 15px;
    font-family: inherit;
    padding: 10px 12px;
    resize: none;
    max-height: 150px;
    min-height: 44px;
    line-height: 1.5;
}

.chat-input-wrapper textarea::placeholder {
    color: var(--agent-text-muted);
}

.chat-input-wrapper textarea:focus {
    outline: none;
}

.send-btn {
    width: 44px;
    height: 44px;
    background: var(--agent-user-bubble);
    border: none;
    border-radius: 12px;
    color: white;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.send-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4);
}

.send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

/* Typing indicator */
.typing-indicator {
    display: none;
    gap: 14px;
    max-width: 85%;
}

.typing-indicator.active {
    display: flex;
}

.typing-dots {
    display: flex;
    gap: 4px;
    padding: 16px 20px;
    background: var(--agent-bot-bubble);
    border: 1px solid var(--agent-border);
    border-radius: 16px;
    border-top-left-radius: 4px;
}

.typing-dot {
    width: 8px;
    height: 8px;
    background: var(--agent-primary-light);
    border-radius: 50%;
    animation: typingBounce 1.4s ease-in-out infinite;
}

.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes typingBounce {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-6px); }
}

/* Not configured state */
.not-configured {
    background: rgba(245, 158, 11, 0.1);
    border: 1px solid rgba(245, 158, 11, 0.3);
    border-radius: 12px;
    padding: 20px;
    margin: 20px;
    text-align: center;
}

.not-configured i {
    font-size: 32px;
    color: #f59e0b;
    margin-bottom: 12px;
}

.not-configured h3 {
    color: #f59e0b;
    margin: 0 0 8px;
    font-size: 16px;
}

.not-configured p {
    color: var(--agent-text-muted);
    margin: 0;
    font-size: 14px;
}

/* Feature cards */
.feature-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 16px;
    padding: 0 20px 20px;
}

.feature-card {
    background: var(--agent-surface-2);
    border: 1px solid var(--agent-border);
    border-radius: 14px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.feature-card:hover {
    border-color: var(--agent-primary);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

.feature-card-icon {
    width: 44px;
    height: 44px;
    background: rgba(99, 102, 241, 0.15);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: var(--agent-primary-light);
    margin-bottom: 14px;
}

.feature-card h4 {
    font-size: 15px;
    font-weight: 600;
    color: var(--agent-text);
    margin: 0 0 6px;
}

.feature-card p {
    font-size: 13px;
    color: var(--agent-text-muted);
    margin: 0;
    line-height: 1.5;
}

/* Responsive */
@media (max-width: 768px) {
    .support-agent-container {
        padding: 12px;
    }
    
    .agent-header {
        flex-direction: column;
        gap: 16px;
        text-align: center;
    }
    
    .agent-logo {
        flex-direction: column;
    }
    
    .message {
        max-width: 95%;
    }
    
    .suggestion-chips {
        flex-direction: column;
        align-items: stretch;
    }
    
    .chat-main {
        max-height: calc(100vh - 320px);
    }
}
</style>
@endsection

@section('content')
<div class="support-agent-container">
    <!-- Header -->
    <header class="agent-header">
        <div class="agent-logo">
            <div class="agent-avatar">🤖</div>
            <div class="agent-title-group">
                <h1>
                    @lang('supportagent::lang.support_agent')
                    <span class="mcp-badge" id="mcpBadge" title="Model Context Protocol - Analyzes your codebase for accurate guidance">
                        <i class="fa fa-code"></i> MCP Active
                    </span>
                </h1>
                <p>
                    <span class="agent-status"></span>
                    @lang('supportagent::lang.powered_by_ai')
                </p>
            </div>
        </div>
        <div class="agent-actions">
            <button class="agent-btn agent-btn-outline" id="clearHistoryBtn" title="@lang('supportagent::lang.clear_history')">
                <i class="fa fa-trash"></i>
                <span class="hidden-xs">@lang('supportagent::lang.clear_chat')</span>
            </button>
        </div>
    </header>

    <!-- Main Chat Area -->
    <main class="chat-main">
        @if(!$isConfigured)
            <div class="not-configured">
                <i class="fa fa-exclamation-triangle"></i>
                <h3>@lang('supportagent::lang.not_configured_title')</h3>
                <p>@lang('supportagent::lang.not_configured_message')</p>
            </div>
        @endif
        
        <div class="chat-messages" id="chatMessages">
            <!-- Welcome state (shown when no messages) -->
            <div class="welcome-state" id="welcomeState">
                <div class="welcome-icon">✨</div>
                <h2>@lang('supportagent::lang.welcome_title')</h2>
                <p>@lang('supportagent::lang.welcome_description')</p>
                
                <div class="suggestion-chips">
                    @foreach($suggestedQuestions as $question)
                        <div class="suggestion-chip" data-question="{{ $question }}">
                            <i class="fa fa-lightbulb-o"></i>
                            {{ $question }}
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Messages will be appended here -->
            
            <!-- Typing indicator -->
            <div class="message bot typing-indicator" id="typingIndicator">
                <div class="message-avatar">🤖</div>
                <div class="typing-dots">
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                </div>
            </div>
        </div>
        
        <!-- Input Area -->
        <div class="chat-input-area">
            <div class="chat-input-wrapper">
                <textarea 
                    id="chatInput" 
                    placeholder="@lang('supportagent::lang.input_placeholder')"
                    rows="1"
                    @if(!$isConfigured) disabled @endif
                ></textarea>
                <button class="send-btn" id="sendBtn" @if(!$isConfigured) disabled @endif>
                    <i class="fa fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </main>
</div>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
$(document).ready(function() {
    const chatMessages = $('#chatMessages');
    const chatInput = $('#chatInput');
    const sendBtn = $('#sendBtn');
    const welcomeState = $('#welcomeState');
    const typingIndicator = $('#typingIndicator');
    const clearHistoryBtn = $('#clearHistoryBtn');
    
    let isProcessing = false;
    
    // Configure marked for markdown rendering
    if (typeof marked !== 'undefined') {
        marked.setOptions({
            breaks: true,
            gfm: true,
            headerIds: false,
            mangle: false
        });
    }
    
    // Load existing conversation history
    @if(!empty($conversationHistory))
        const history = @json($conversationHistory);
        if (history.length > 0) {
            welcomeState.hide();
            history.forEach(function(msg) {
                appendMessage(msg.role === 'user' ? 'user' : 'bot', msg.content, false);
            });
            scrollToBottom();
        }
    @endif
    
    // Auto-resize textarea
    chatInput.on('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 150) + 'px';
    });
    
    // Send message on Enter (Shift+Enter for new line)
    chatInput.on('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    
    // Send button click
    sendBtn.on('click', function() {
        sendMessage();
    });
    
    // Suggestion chip click
    $(document).on('click', '.suggestion-chip', function() {
        const question = $(this).data('question');
        chatInput.val(question);
        sendMessage();
    });
    
    // Clear history
    clearHistoryBtn.on('click', function() {
        if (confirm('@lang("supportagent::lang.clear_confirm")')) {
            $.ajax({
                url: '{{ route("supportagent.clear") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Remove all messages except welcome state and typing indicator
                    chatMessages.find('.message:not(.typing-indicator)').remove();
                    welcomeState.show();
                    toastr.success('@lang("supportagent::lang.history_cleared")');
                },
                error: function() {
                    toastr.error('@lang("messages.something_went_wrong")');
                }
            });
        }
    });
    
    function sendMessage() {
        const message = chatInput.val().trim();
        
        if (!message || isProcessing) return;
        
        isProcessing = true;
        sendBtn.prop('disabled', true);
        
        // Hide welcome state
        welcomeState.hide();
        
        // Add user message
        appendMessage('user', message);
        chatInput.val('').css('height', 'auto');
        
        // Show typing indicator
        typingIndicator.addClass('active');
        scrollToBottom();
        
        // Send to server
        $.ajax({
            url: '{{ route("supportagent.chat") }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                message: message,
                current_page: window.location.pathname
            },
            success: function(response) {
                typingIndicator.removeClass('active');
                
                if (response.success) {
                    appendMessage('bot', response.message);
                } else {
                    appendMessage('bot', response.message || '@lang("messages.something_went_wrong")');
                }
                
                scrollToBottom();
            },
            error: function(xhr) {
                typingIndicator.removeClass('active');
                
                let errorMsg = '@lang("supportagent::lang.error_message")';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                appendMessage('bot', errorMsg);
                scrollToBottom();
            },
            complete: function() {
                isProcessing = false;
                sendBtn.prop('disabled', false);
                chatInput.focus();
            }
        });
    }
    
    function appendMessage(type, content, animate = true) {
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const avatar = type === 'user' ? '👤' : '🤖';
        
        // Parse markdown for bot messages
        let displayContent = content;
        if (type === 'bot' && typeof marked !== 'undefined') {
            displayContent = marked.parse(content);
        } else {
            displayContent = escapeHtml(content);
        }
        
        const messageHtml = `
            <div class="message ${type}" ${!animate ? 'style="animation: none;"' : ''}>
                <div class="message-avatar">${avatar}</div>
                <div class="message-content">
                    <div class="message-bubble">${displayContent}</div>
                    <div class="message-time">${time}</div>
                </div>
            </div>
        `;
        
        // Insert before typing indicator
        $(messageHtml).insertBefore(typingIndicator);
    }
    
    function scrollToBottom() {
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
@endsection
