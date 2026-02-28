@extends('layouts.app')

@section('title', 'WebSocket Test')

@section('content')
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">WebSocket Test
            <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">Test real-time WebSocket functionality</small>
        </h1>
    </section>

    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => 'WebSocket Test'])
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>WebSocket Test</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="message">Message:</label>
                                    <input type="text" id="message" class="form-control" value="Hello from WebSocket!"
                                        placeholder="Enter your message">
                                </div>
                                <div class="col-md-6">
                                    <label>&nbsp;</label>
                                    <button id="sendMessage" class="btn btn-primary btn-block">Send Message</button>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <button id="checkStatus" class="btn btn-info">Check WebSocket Status</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <h5>Received Messages:</h5>
                                    <div id="messages" class="border p-3"
                                        style="height: 300px; overflow-y: auto; background-color: #f8f9fa;">
                                        <p class="text-muted">No messages received yet...</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5>Status:</h5>
                                    <div id="status" class="border p-3" style="background-color: #f8f9fa;">
                                        <p class="text-muted">Checking status...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcomponent
    </section>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function () {
        console.log('WebSocket Test');
    });
    document.addEventListener('DOMContentLoaded', function () {
        const messageInput = document.getElementById('message');
        const sendButton = document.getElementById('sendMessage');
        const checkStatusButton = document.getElementById('checkStatus');
        const messagesDiv = document.getElementById('messages');
        const statusDiv = document.getElementById('status');

        // Get CSRF token
        function getCSRFToken() {
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                return token.getAttribute('content');
            }
            // Fallback: try to get from input field
            const csrfInput = document.querySelector('input[name="_token"]');
            if (csrfInput) {
                return csrfInput.value;
            }
            return null;
        }

        // Check WebSocket status on page load
        checkStatus();

        // Send message button click
        sendButton.addEventListener('click', function () {
            const message = messageInput.value;
            if (!message.trim()) {
                alert('Please enter a message');
                return;
            }

            addMessage('Sending message...', 'info');

            // Send AJAX request to dispatch WebSocket event
            fetch('/websocket/test', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: message })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        addMessage('Sent: ' + message, 'success');
                    } else {
                        addMessage('Error: ' + (data.message || 'Unknown error'), 'error');
                    }
                })
                .catch(error => {
                    addMessage('Error: ' + error.message, 'error');
                    console.error('Fetch error:', error);
                });
        });

        // Check status button click
        checkStatusButton.addEventListener('click', function () {
            checkStatus();
        });

        function checkStatus() {
            statusDiv.innerHTML = '<p class="text-muted">Checking status...</p>';
            
            fetch('/websocket/status')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    statusDiv.innerHTML = `
                        <p><strong>Status:</strong> ${data.status}</p>
                        <p><strong>Timestamp:</strong> ${data.timestamp}</p>
                        <p><strong>Broadcast Driver:</strong> ${data.config.broadcast_driver}</p>
                        <p><strong>Pusher Host:</strong> ${data.config.pusher_host}</p>
                        <p><strong>Pusher Port:</strong> ${data.config.pusher_port}</p>
                        <p><strong>Pusher Scheme:</strong> ${data.config.pusher_scheme}</p>
                    `;
                })
                .catch(error => {
                    statusDiv.innerHTML = '<p class="text-danger">Error checking status: ' + error.message + '</p>';
                    console.error('Status check error:', error);
                });
        }

        function addMessage(message, type) {
            const messageElement = document.createElement('p');
            let className = '';
            switch(type) {
                case 'success':
                    className = 'text-success';
                    break;
                case 'error':
                    className = 'text-danger';
                    break;
                case 'warning':
                    className = 'text-warning';
                    break;
                case 'info':
                    className = 'text-info';
                    break;
                default:
                    className = 'text-muted';
            }
            messageElement.className = className;
            messageElement.textContent = new Date().toLocaleTimeString() + ' - ' + message;
            messagesDiv.appendChild(messageElement);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        // Listen for WebSocket messages if Echo is available
        if (typeof window.Echo !== 'undefined') {
            addMessage('Echo is available. Listening for WebSocket messages...', 'info');
            window.Echo.channel('test-channel')
                .listen('TestEvent', (e) => {
                    addMessage('Received: ' + e.message + ' (from ' + e.user + ')', 'info');
                })
                .error((error) => {
                    addMessage('WebSocket error: ' + error.message, 'error');
                });
        } else {
            addMessage('Echo not available. Make sure WebSocket is properly configured.', 'warning');
        }

        // Add some debug info
        addMessage('Page loaded. CSRF token: ' + (getCSRFToken() ? 'Found' : 'Not found'), 'info');
    });
</script>
@endsection