{{-- Floating Support Agent Widget --}}
{{-- Include this partial on any page to show a floating help button --}}

<style>
.support-widget-fab {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
}

.support-widget-btn {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
    transition: all 0.3s ease;
    color: white;
    font-size: 24px;
}

.support-widget-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 28px rgba(99, 102, 241, 0.5);
}

.support-widget-btn .icon-open {
    display: block;
}

.support-widget-btn .icon-close {
    display: none;
}

.support-widget-btn.active .icon-open {
    display: none;
}

.support-widget-btn.active .icon-close {
    display: block;
}

.support-widget-popup {
    position: fixed;
    bottom: 96px;
    right: 24px;
    width: 380px;
    max-width: calc(100vw - 48px);
    height: 520px;
    max-height: calc(100vh - 140px);
    background: #1a1a2e;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    border: 1px solid #2d2d4a;
    display: none;
    flex-direction: column;
    overflow: hidden;
    z-index: 9998;
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.support-widget-popup.active {
    display: flex;
}

.widget-header {
    padding: 16px 20px;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    display: flex;
    align-items: center;
    gap: 12px;
}

.widget-avatar {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.widget-title {
    flex: 1;
}

.widget-title h4 {
    color: white;
    font-size: 15px;
    font-weight: 600;
    margin: 0;
}

.widget-title p {
    color: rgba(255,255,255,0.7);
    font-size: 12px;
    margin: 4px 0 0;
}

.widget-expand {
    background: rgba(255,255,255,0.2);
    border: none;
    border-radius: 8px;
    padding: 8px;
    cursor: pointer;
    color: white;
    transition: background 0.2s;
}

.widget-expand:hover {
    background: rgba(255,255,255,0.3);
}

.widget-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.widget-messages::-webkit-scrollbar {
    width: 4px;
}

.widget-messages::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 2px;
}

.widget-msg {
    max-width: 85%;
    padding: 12px 16px;
    border-radius: 16px;
    font-size: 13px;
    line-height: 1.5;
}

.widget-msg.bot {
    background: #252542;
    color: #e4e4f0;
    align-self: flex-start;
    border-bottom-left-radius: 4px;
}

.widget-msg.user {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    align-self: flex-end;
    border-bottom-right-radius: 4px;
}

.widget-input-area {
    padding: 12px 16px;
    background: #0f0f23;
    border-top: 1px solid #2d2d4a;
    display: flex;
    gap: 10px;
}

.widget-input-area input {
    flex: 1;
    background: #252542;
    border: 1px solid #2d2d4a;
    border-radius: 12px;
    padding: 12px 16px;
    color: #e4e4f0;
    font-size: 14px;
}

.widget-input-area input::placeholder {
    color: #6b7280;
}

.widget-input-area input:focus {
    outline: none;
    border-color: #6366f1;
}

.widget-send-btn {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border: none;
    border-radius: 12px;
    color: white;
    font-size: 16px;
    cursor: pointer;
    transition: transform 0.2s;
}

.widget-send-btn:hover {
    transform: scale(1.05);
}

.widget-typing {
    display: none;
    gap: 4px;
    padding: 12px 16px;
    background: #252542;
    align-self: flex-start;
    border-radius: 16px;
    border-bottom-left-radius: 4px;
}

.widget-typing.active {
    display: flex;
}

.widget-typing span {
    width: 6px;
    height: 6px;
    background: #a5b4fc;
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.widget-typing span:nth-child(2) { animation-delay: 0.2s; }
.widget-typing span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.5; }
    30% { transform: translateY(-4px); opacity: 1; }
}

@media (max-width: 480px) {
    .support-widget-popup {
        bottom: 0;
        right: 0;
        width: 100%;
        max-width: 100%;
        height: 100%;
        max-height: 100%;
        border-radius: 0;
    }
    
    .support-widget-fab {
        bottom: 16px;
        right: 16px;
    }
}
</style>

<div class="support-widget-fab">
    <button class="support-widget-btn" id="supportWidgetBtn" title="@lang('supportagent::lang.support_agent')">
        <span class="icon-open">💬</span>
        <span class="icon-close">✕</span>
    </button>
</div>

<div class="support-widget-popup" id="supportWidgetPopup">
    <div class="widget-header">
        <div class="widget-avatar">🤖</div>
        <div class="widget-title">
            <h4>@lang('supportagent::lang.support_agent')</h4>
            <p>@lang('supportagent::lang.powered_by_ai')</p>
        </div>
        <a href="{{ route('supportagent.index') }}" class="widget-expand" title="Open full view">
            <i class="fa fa-expand"></i>
        </a>
    </div>
    
    <div class="widget-messages" id="widgetMessages">
        <div class="widget-msg bot">
            @lang('supportagent::lang.welcome_message')
        </div>
        <div class="widget-typing" id="widgetTyping">
            <span></span><span></span><span></span>
        </div>
    </div>
    
    <div class="widget-input-area">
        <input type="text" id="widgetInput" placeholder="@lang('supportagent::lang.input_placeholder')">
        <button class="widget-send-btn" id="widgetSendBtn">
            <i class="fa fa-paper-plane"></i>
        </button>
    </div>
</div>

<script>
(function() {
    const widgetBtn = document.getElementById('supportWidgetBtn');
    const widgetPopup = document.getElementById('supportWidgetPopup');
    const widgetInput = document.getElementById('widgetInput');
    const widgetSendBtn = document.getElementById('widgetSendBtn');
    const widgetMessages = document.getElementById('widgetMessages');
    const widgetTyping = document.getElementById('widgetTyping');
    
    let isProcessing = false;
    
    widgetBtn.addEventListener('click', function() {
        widgetBtn.classList.toggle('active');
        widgetPopup.classList.toggle('active');
        if (widgetPopup.classList.contains('active')) {
            widgetInput.focus();
        }
    });
    
    widgetInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendWidgetMessage();
        }
    });
    
    widgetSendBtn.addEventListener('click', sendWidgetMessage);
    
    function sendWidgetMessage() {
        const message = widgetInput.value.trim();
        if (!message || isProcessing) return;
        
        isProcessing = true;
        
        // Add user message
        addWidgetMessage('user', message);
        widgetInput.value = '';
        
        // Show typing
        widgetTyping.classList.add('active');
        scrollWidgetToBottom();
        
        // Send request
        fetch('{{ route("supportagent.chat") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                message: message,
                current_page: window.location.pathname
            })
        })
        .then(response => response.json())
        .then(data => {
            widgetTyping.classList.remove('active');
            addWidgetMessage('bot', data.message);
            scrollWidgetToBottom();
        })
        .catch(error => {
            widgetTyping.classList.remove('active');
            addWidgetMessage('bot', '@lang("supportagent::lang.error_message")');
        })
        .finally(() => {
            isProcessing = false;
            widgetInput.focus();
        });
    }
    
    function addWidgetMessage(type, content) {
        const msg = document.createElement('div');
        msg.className = 'widget-msg ' + type;
        msg.textContent = content;
        widgetTyping.before(msg);
    }
    
    function scrollWidgetToBottom() {
        widgetMessages.scrollTop = widgetMessages.scrollHeight;
    }
})();
</script>
