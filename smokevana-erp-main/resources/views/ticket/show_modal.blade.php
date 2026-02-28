<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Ticket {{ $ticket->reference_no ?? '#'.$ticket->id }} - {{ $ticket->lead ? $ticket->lead->store_name : 'N/A' }}</h4>
            <div class="tw-flex tw-justify-end tw-gap-2" style="margin-top: -30px">
                <button type="button" class="tw-dw-btn tw-dw-btn-danger tw-text-white no-print" data-dismiss="modal"
                    id='close_button'>Close</button>
            </div>
        </div>
        <div class="modal-body" style="padding: 0;">
            <!-- Ticket Information -->
            <div style="padding: 15px; background: #f9f9f9; border-bottom: 2px solid #e0e0e0;">
                <div class="row">
                    <div class="col-md-2">
                        <small style="color: #666; font-weight: 600;">REFERENCE NO</small><br>
                        <strong style="color: #2196F3;">{{ $ticket->reference_no ?? 'N/A' }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small style="color: #666; font-weight: 600;">LEAD</small><br>
                        <strong style="color: #333;">{{ $ticket->lead ? $ticket->lead->store_name : 'N/A' }}</strong>
                    </div>
                    <div class="col-md-2">
                        <small style="color: #666; font-weight: 600;">ASSIGNED TO</small><br>
                        <strong style="color: #333;">{{ $ticket->user ? $ticket->user->first_name . ' ' . $ticket->user->last_name : 'N/A' }}</strong>
                    </div>
                    <div class="col-md-2">
                        <small style="color: #666; font-weight: 600;">STATUS</small><br>
                        @php
                            $statusColors = [
                                'open' => 'success',
                                'in_progress' => 'info',
                                'closed' => 'default',
                                'pending' => 'warning',
                                'resolved' => 'primary'
                            ];
                            $color = $statusColors[$ticket->status] ?? 'default';
                        @endphp
                        <span class="label label-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                    </div>
                    <div class="col-md-3">
                        <small style="color: #666; font-weight: 600;">CREATED</small><br>
                        <strong style="color: #333;">{{ @format_date($ticket->created_at) }}</strong>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    @if($ticket->issue_type)
                    <div class="col-md-3">
                        <small style="color: #666; font-weight: 600;">ISSUE TYPE</small><br>
                        @php
                            $issueTypeColors = [
                                'technical' => 'danger',
                                'billing' => 'warning',
                                'product' => 'info',
                                'service' => 'primary',
                                'complaint' => 'danger',
                                'inquiry' => 'success',
                                'other' => 'default'
                            ];
                            $issueColor = $issueTypeColors[$ticket->issue_type] ?? 'default';
                        @endphp
                        <span class="label label-{{ $issueColor }}">{{ ucfirst(str_replace('_', ' ', $ticket->issue_type)) }}</span>
                    </div>
                    @endif
                    @if($ticket->issue_priority)
                    <div class="col-md-3">
                        <small style="color: #666; font-weight: 600;">PRIORITY</small><br>
                        @php
                            $priorityColors = [
                                'low' => 'success',
                                'medium' => 'info',
                                'high' => 'warning',
                                'urgent' => 'danger'
                            ];
                            $priorityColor = $priorityColors[$ticket->issue_priority] ?? 'info';
                        @endphp
                        <span class="label label-{{ $priorityColor }}">{{ ucfirst($ticket->issue_priority) }}</span>
                    </div>
                    @endif
                    @if($ticket->closed_by && $ticket->closed_at)
                    <div class="col-md-3">
                        <small style="color: #666; font-weight: 600;">CLOSED BY</small><br>
                        <strong style="color: #333;">{{ $ticket->closedBy ? $ticket->closedBy->first_name . ' ' . $ticket->closedBy->last_name : 'N/A' }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small style="color: #666; font-weight: 600;">CLOSED AT</small><br>
                        <strong style="color: #333;">{{ @format_datetime($ticket->closed_at) }}</strong>
                    </div>
                    @endif
                </div>
                @if($ticket->ticket_description)
                    <div style="margin-top: 10px; padding: 10px; background: white; border-radius: 5px; border-left: 3px solid #2196F3;">
                        <small style="color: #666; font-weight: 600;">DESCRIPTION</small><br>
                        <span style="color: #555;">{{ $ticket->ticket_description }}</span>
                    </div>
                @endif
                @if($ticket->initial_image)
                    <div style="margin-top: 10px; padding: 10px; background: white; border-radius: 5px; border-left: 3px solid #4CAF50;">
                        <small style="color: #666; font-weight: 600;">INITIAL IMAGE</small><br>
                        <a href="{{ url('uploads/tickets/' . $ticket->initial_image) }}" target="_blank">
                            <img src="{{ url('uploads/tickets/' . $ticket->initial_image) }}" 
                                 style="max-width: 200px; max-height: 200px; border-radius: 8px; margin-top: 8px; cursor: pointer; border: 2px solid #e0e0e0;" 
                                 alt="Initial Issue Image">
                        </a>
                    </div>
                @endif
            </div>

            <!-- Chat Messages Container -->
            <div style="height: 450px; overflow-y: auto; padding: 20px; background: #ffffff;" id="chat-messages">
                @if($ticket->activities && $ticket->activities->count() > 0)
                    @foreach($ticket->activities as $activity)
                        @if(in_array($activity->activity_type, ['text', 'image', 'file']))
                            <!-- Chat Message -->
                            @php
                                $isOwnMessage = $activity->user_id == auth()->user()->id;
                                $alignClass = $isOwnMessage ? 'text-right' : 'text-left';
                            @endphp
                            <div class="chat-message {{ $alignClass }}" style="margin-bottom: 20px;">
                                <div style="display: inline-block; max-width: 70%; text-align: left;">
                                    <!-- User Info -->
                                    <div style="margin-bottom: 5px;">
                                        <small style="color: {{ $isOwnMessage ? '#2196F3' : '#666' }}; font-weight: 600;">
                                            {{ $activity->user ? $activity->user->first_name . ' ' . $activity->user->last_name : 'Unknown' }}
                                        </small>
                                        <small style="color: #999; margin-left: 8px;">
                                            {{ @format_datetime($activity->created_at) }}
                                        </small>
                                    </div>
                                    
                                    <!-- Message Bubble -->
                                    <div style="background: {{ $isOwnMessage ? '#2196F3' : '#e8e8e8' }}; 
                                                color: {{ $isOwnMessage ? '#ffffff' : '#333333' }}; 
                                                padding: 12px 15px; 
                                                border-radius: {{ $isOwnMessage ? '15px 15px 0 15px' : '15px 15px 15px 0' }}; 
                                                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                                word-wrap: break-word;">
                                        
                                        <!-- Text Message -->
                                        @if($activity->activity_details)
                                            <div style="margin-bottom: {{ ($activity->attachment) ? '10px' : '0' }};">
                                                {{ $activity->activity_details }}
                                            </div>
                                        @endif

                                        <!-- Image Display -->
                                        @if($activity->activity_type == 'image' && $activity->attachment)
                                            <div style="margin-top: 8px;">
                                                <a href="{{ asset('uploads/tickets/' . $activity->attachment) }}" target="_blank">
                                                    <img src="{{ asset('uploads/tickets/' . $activity->attachment) }}" 
                                                         style="max-width: 100%; 
                                                                max-height: 250px; 
                                                                border-radius: 8px; 
                                                                cursor: pointer;
                                                                display: block;" 
                                                         alt="Image">
                                                </a>
                                            </div>
                                        @endif

                                        <!-- File Download -->
                                        @if($activity->activity_type == 'file' && $activity->attachment)
                                            <div style="margin-top: 8px; 
                                                        padding: 10px; 
                                                        background: {{ $isOwnMessage ? 'rgba(255,255,255,0.2)' : 'rgba(0,0,0,0.05)' }}; 
                                                        border-radius: 5px;">
                                                <a href="{{ asset('uploads/tickets/' . $activity->attachment) }}" 
                                                   target="_blank" 
                                                   style="color: {{ $isOwnMessage ? '#ffffff' : '#2196F3' }}; text-decoration: none;"
                                                   download>
                                                    <i class="fa fa-file"></i> 
                                                    <strong>{{ basename($activity->attachment) }}</strong>
                                                    <i class="fa fa-download" style="margin-left: 5px;"></i>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- System Activity -->
                            <div class="system-activity text-center" style="margin: 15px 0;">
                                @php
                                    $activityTypes = [
                                        'created' => 'success',
                                        'updated' => 'info',
                                        'status_changed' => 'warning',
                                    ];
                                    $activityColor = $activityTypes[$activity->activity_type] ?? 'default';
                                @endphp
                                <div style="display: inline-block; padding: 6px 15px; background: #f5f5f5; border-radius: 20px; border: 1px solid #e0e0e0;">
                                    <small style="color: #666;">
                                        <i class="fa fa-info-circle"></i>
                                        <span class="label label-{{ $activityColor }}" style="margin: 0 5px;">{{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}</span>
                                        {{ $activity->activity_details }}
                                        <span style="color: #999;">· {{ @format_datetime($activity->created_at) }}</span>
                                    </small>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div style="text-align: center; padding: 60px 20px; color: #999;">
                        <i class="fa fa-comments-o" style="font-size: 64px; color: #ddd; margin-bottom: 20px;"></i>
                        <p style="font-size: 16px;">No conversation yet</p>
                        <p style="font-size: 14px;">Start the conversation by sending a message below</p>
                    </div>
                @endif
            </div>

            <!-- Message Input Area - Fixed at Bottom -->
            <div style="padding: 15px; background: #f9f9f9; border-top: 2px solid #e0e0e0;">
                <form id="add_message_form" enctype="multipart/form-data">
                    <!-- File Preview Area -->
                    <div id="file_preview" style="display: none; margin-bottom: 10px; padding: 10px; background: white; border-radius: 8px; border: 2px dashed #2196F3;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center;">
                                <i class="fa fa-file" style="font-size: 24px; color: #2196F3; margin-right: 10px;"></i>
                                <div>
                                    <strong id="file_name" style="color: #333;"></strong><br>
                                    <small id="file_size" style="color: #666;"></small>
                                </div>
                            </div>
                            <button type="button" onclick="clearFileInput()" style="background: #f44336; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer;">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Message Input Box -->
                    <div style="display: flex; gap: 10px; align-items: flex-end;">
                        <div style="flex: 1;">
                            <textarea name="message" 
                                      id="message_input" 
                                      class="form-control" 
                                      rows="2" 
                                      placeholder="Type your message here..."
                                      style="border: 2px solid #e0e0e0; border-radius: 20px; padding: 12px 15px; resize: none; font-size: 14px;"></textarea>
                        </div>
                        
                        <!-- File Upload Button -->
                        <div>
                            <input type="file" name="attachment" id="attachment_input" style="display: none;" onchange="handleFileSelect(this)">
                            <button type="button" 
                                    onclick="$('#attachment_input').click();" 
                                    class="tw-dw-btn tw-dw-btn-default"
                                    style="height: 48px; width: 48px; border-radius: 50%; padding: 0; border: 2px solid #e0e0e0;"
                                    title="Attach file">
                                <i class="fa fa-paperclip" style="font-size: 18px;"></i>
                            </button>
                        </div>
                        
                        <!-- Send Button -->
                        <div>
                            <button type="submit" 
                                    class="tw-dw-btn tw-dw-btn-primary tw-text-white"
                                    style="height: 48px; width: 48px; border-radius: 50%; padding: 0;"
                                    title="Send message">
                                <i class="fa fa-paper-plane" style="font-size: 18px;"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div style="margin-top: 8px;">
                        <small style="color: #999;">
                            <i class="fa fa-info-circle"></i> 
                            Supported: Images, PDF, Word, Excel (Max 10MB)
                        </small>
                    </div>
                </form>
            </div>

            <!-- Status Update Section - Collapsible -->
            <div style="padding: 15px; background: #fff8e1; border-top: 1px solid #ffeb3b;">
                <div style="cursor: pointer;" onclick="$('#status_section').slideToggle();">
                    <strong style="color: #f57c00;">
                        <i class="fa fa-refresh"></i> Update Ticket Status
                        <i class="fa fa-chevron-down" style="float: right; margin-top: 5px;"></i>
                    </strong>
                </div>
                <div id="status_section" style="display: none; margin-top: 15px;">
                    <form id="update_status_form">
                        <div class="row">
                            <div class="col-md-8">
                                <select name="status" id="status_select" class="form-control" style="height: 40px; border: 2px solid #ffb300; border-radius: 8px;">
                                    <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="pending" {{ $ticket->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed (Admin Only)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="tw-dw-btn tw-dw-btn-warning tw-text-white" style="width: 100%; height: 40px;">
                                    <i class="fa fa-check"></i> Update Status
                                </button>
                            </div>
                        </div>
                    </form>
                    <div style="margin-top: 10px;">
                        <small style="color: #f57c00;">
                            <i class="fa fa-info-circle"></i> 
                            <strong>Note:</strong> Only administrators can close tickets.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var ticket_id = {{ $ticket->id }};
    var current_user_id = {{ auth()->user()->id }};
    
    // File input handling
    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            var file = input.files[0];
            var fileSize = (file.size / 1024 / 1024).toFixed(2); // Size in MB
            
            $('#file_name').text(file.name);
            $('#file_size').text(fileSize + ' MB');
            $('#file_preview').slideDown();
        }
    }
    
    function clearFileInput() {
        $('#attachment_input').val('');
        $('#file_preview').slideUp();
    }
    
    // Scroll to bottom of chat
    function scrollToBottom() {
        var chatMessages = $('#chat-messages');
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
    }
    
    // Format datetime
    function formatDateTime(dateString) {
        var date = new Date(dateString);
        return date.toLocaleString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit'
        });
    }
    
    // Append new message to chat
    function appendMessageToChat(activity) {
        var isOwnMessage = activity.user_id == current_user_id;
        var alignClass = isOwnMessage ? 'text-right' : 'text-left';
        var bgColor = isOwnMessage ? '#2196F3' : '#e8e8e8';
        var textColor = isOwnMessage ? '#ffffff' : '#333333';
        var borderRadius = isOwnMessage ? '15px 15px 0 15px' : '15px 15px 15px 0';
        var userColor = isOwnMessage ? '#2196F3' : '#666';
        
        // Check if it's a chat message (text, image, file) or system activity
        if (['text', 'image', 'file'].includes(activity.activity_type)) {
            var messageHtml = '<div class="chat-message ' + alignClass + '" style="margin-bottom: 20px;">' +
                '<div style="display: inline-block; max-width: 70%; text-align: left;">' +
                    '<div style="margin-bottom: 5px;">' +
                        '<small style="color: ' + userColor + '; font-weight: 600;">' +
                            activity.user.name +
                        '</small>' +
                        '<small style="color: #999; margin-left: 8px;">' +
                            formatDateTime(activity.created_at) +
                        '</small>' +
                    '</div>' +
                    '<div style="background: ' + bgColor + '; color: ' + textColor + '; padding: 12px 15px; border-radius: ' + borderRadius + '; box-shadow: 0 2px 4px rgba(0,0,0,0.1); word-wrap: break-word;">';
            
            // Add text message
            if (activity.activity_details) {
                messageHtml += '<div style="margin-bottom: ' + (activity.attachment ? '10px' : '0') + ';">' +
                    activity.activity_details +
                    '</div>';
            }
            
            // Add image
            if (activity.activity_type === 'image' && activity.attachment) {
                messageHtml += '<div style="margin-top: 8px;">' +
                    '<a href="' + activity.file_url + '" target="_blank">' +
                        '<img src="' + activity.file_url + '" style="max-width: 100%; max-height: 250px; border-radius: 8px; cursor: pointer; display: block;" alt="Image">' +
                    '</a>' +
                    '</div>';
            }
            
            // Add file download
            if (activity.activity_type === 'file' && activity.attachment) {
                var fileBg = isOwnMessage ? 'rgba(255,255,255,0.2)' : 'rgba(0,0,0,0.05)';
                var fileColor = isOwnMessage ? '#ffffff' : '#2196F3';
                messageHtml += '<div style="margin-top: 8px; padding: 10px; background: ' + fileBg + '; border-radius: 5px;">' +
                    '<a href="' + activity.file_url + '" target="_blank" style="color: ' + fileColor + '; text-decoration: none;" download>' +
                        '<i class="fa fa-file"></i> <strong>' + activity.attachment.split('/').pop() + '</strong>' +
                        '<i class="fa fa-download" style="margin-left: 5px;"></i>' +
                    '</a>' +
                    '</div>';
            }
            
            messageHtml += '</div>' +
                '</div>' +
                '</div>';
                
            $('#chat-messages').append(messageHtml);
        } else {
            // System activity
            var activityColors = {
                'created': 'success',
                'updated': 'info',
                'status_changed': 'warning'
            };
            var activityColor = activityColors[activity.activity_type] || 'default';
            
            var systemHtml = '<div class="system-activity text-center" style="margin: 15px 0;">' +
                '<div style="display: inline-block; padding: 6px 15px; background: #f5f5f5; border-radius: 20px; border: 1px solid #e0e0e0;">' +
                    '<small style="color: #666;">' +
                        '<i class="fa fa-info-circle"></i>' +
                        '<span class="label label-' + activityColor + '" style="margin: 0 5px;">' + 
                            activity.activity_type.replace('_', ' ').charAt(0).toUpperCase() + activity.activity_type.replace('_', ' ').slice(1) +
                        '</span>' +
                        activity.activity_details +
                        '<span style="color: #999;"> · ' + formatDateTime(activity.created_at) + '</span>' +
                    '</small>' +
                '</div>' +
                '</div>';
                
            $('#chat-messages').append(systemHtml);
        }
        
        scrollToBottom();
    }
    
    $(document).ready(function() {
        scrollToBottom();

        // Handle message submission with file upload
        $('#add_message_form').submit(function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            var message = $('#message_input').val();
            var attachment = $('#attachment_input')[0].files[0];
            
            if (!message.trim() && !attachment) {
                toastr.error('Please enter a message or upload a file');
                return;
            }

            // Disable send button
            var sendBtn = $(this).find('button[type="submit"]');
            sendBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

            $.ajax({
                method: "POST",
                url: "{{ url('/tickets') }}/" + ticket_id + "/add-message",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(result) {
                    if (result.success == true) {
                        toastr.success(result.msg);
                        $('#add_message_form')[0].reset();
                        clearFileInput();
                        
                        // Append the message to chat immediately (for sender)
                        if (result.activity) {
                            appendMessageToChat(result.activity);
                        }
                        
                        // Re-enable send button
                        sendBtn.prop('disabled', false).html('<i class="fa fa-paper-plane" style="font-size: 18px;"></i>');
                    } else {
                        toastr.error(result.msg);
                        sendBtn.prop('disabled', false).html('<i class="fa fa-paper-plane" style="font-size: 18px;"></i>');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Error sending message. Please try again.');
                    sendBtn.prop('disabled', false).html('<i class="fa fa-paper-plane" style="font-size: 18px;"></i>');
                }
            });
        });

        // Handle status update
        $('#update_status_form').submit(function(e) {
            e.preventDefault();
            var status = $('#status_select').val();

            $.ajax({
                method: "POST",
                url: "{{ url('/tickets') }}/" + ticket_id + "/update-status",
                dataType: "json",
                data: { status: status },
                success: function(result) {
                    if (result.success == true) {
                        toastr.success(result.msg);
                        
                        // Update the status badge in the header without full reload
                        var statusColors = {
                            'open': 'success',
                            'in_progress': 'info',
                            'closed': 'default',
                            'pending': 'warning',
                            'resolved': 'primary'
                        };
                        var color = statusColors[status] || 'default';
                        var statusText = status.replace('_', ' ').charAt(0).toUpperCase() + status.replace('_', ' ').slice(1);
                        $('.modal-header').find('.label').attr('class', 'label label-' + color).text(statusText);
                        
                        // Hide the status section
                        $('#status_section').slideUp();
                        
                        // Reload the datatable if it exists
                        if (typeof $.fn.DataTable !== 'undefined' && $('#tickets_table').length) {
                            $('#tickets_table').DataTable().ajax.reload();
                        }
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });

        // Enter to send (Shift+Enter for new line)
        $('#message_input').keypress(function(e) {
            if (e.which == 13 && !e.shiftKey) {
                e.preventDefault();
                $('#add_message_form').submit();
            }
        });

        // WebSocket: Listen for new messages on this ticket
        if (typeof window.Echo !== 'undefined') {
            console.log('[WebSocket] Setting up listener for ticket.' + ticket_id);
            console.log('[WebSocket] Current user ID:', current_user_id);
            
            window.Echo.private('ticket.' + ticket_id)
                .listen('.ticket.message', (e) => {
                    console.log('[WebSocket] ✓ Received message event:', e);
                    console.log('[WebSocket] Activity data:', e.activity);
                    
                    // Append the new message to chat
                    if (e.activity) {
                        console.log('[WebSocket] Appending message to chat...');
                        appendMessageToChat(e.activity);
                        
                        // Show notification sound/alert if message is from someone else
                        if (e.activity.user_id != current_user_id) {
                            console.log('[WebSocket] Message from another user - showing notification');
                            
                            // Optional: Play notification sound
                            if (typeof notificationSound !== 'undefined') {
                                notificationSound.play();
                            }
                            
                            // Optional: Show toast notification
                            if (typeof toastr !== 'undefined') {
                                toastr.info(e.activity.user.name + ' sent a message', 'New Message');
                            }
                        } else {
                            console.log('[WebSocket] Message from current user - no notification');
                        }
                    } else {
                        console.error('[WebSocket] No activity data in event!');
                    }
                })
                .subscribed(() => {
                    console.log('[WebSocket] ✓ Successfully subscribed to ticket.' + ticket_id);
                })
                .error((error) => {
                    console.error('[WebSocket] ✗ Subscription error:', error);
                    toastr.error('WebSocket connection failed. Real-time updates may not work.');
                });
        } else {
            console.warn('[WebSocket] ✗ Echo is not available. WebSocket functionality will not work.');
            toastr.warning('WebSocket not initialized. Real-time updates disabled.');
        }
    });
    
    // Clean up WebSocket connection when modal is closed
    $('.ticket_modal').on('hidden.bs.modal', function () {
        if (typeof window.Echo !== 'undefined') {
            window.Echo.leave('ticket.' + ticket_id);
            console.log('Left WebSocket channel for ticket.' + ticket_id);
        }
    });
</script>
