@extends('layouts.app')

@section('title', 'WebSocket Dashboard')

@section('content')
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">WebSocket Dashboard
            <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">Monitor WebSocket connections and statistics</small>
        </h1>
    </section>

    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => 'WebSocket Server Status'])
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!-- Server Status -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h4 class="mb-0"><i class="fa fa-server"></i> Server Status</h4>
                        </div>
                        <div class="card-body">
                            <div id="server-status" class="alert alert-info">
                                <i class="fa fa-spinner fa-spin"></i> Checking server status...
                            </div>
                        </div>
                    </div>

                    <!-- Configuration Info -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h4 class="mb-0"><i class="fa fa-cog"></i> Configuration</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Broadcast Driver:</th>
                                    <td><span id="config-driver">Loading...</span></td>
                                </tr>
                                <tr>
                                    <th>WebSocket Host:</th>
                                    <td><span id="config-host">Loading...</span></td>
                                </tr>
                                <tr>
                                    <th>WebSocket Port:</th>
                                    <td><span id="config-port">Loading...</span></td>
                                </tr>
                                <tr>
                                    <th>Scheme:</th>
                                    <td><span id="config-scheme">Loading...</span></td>
                                </tr>
                                <tr>
                                    <th>App Key:</th>
                                    <td><span id="config-app-key">{{ config('broadcasting.connections.pusher.key') }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h4 class="mb-0"><i class="fa fa-bolt"></i> Quick Actions</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('websocket.test') }}" class="btn btn-primary btn-block">
                                        <i class="fa fa-vial"></i> Test WebSocket
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <button id="refresh-status" class="btn btn-info btn-block">
                                        <i class="fa fa-sync"></i> Refresh Status
                                    </button>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <button id="test-connection" class="btn btn-success btn-block">
                                        <i class="fa fa-plug"></i> Test Connection
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Connection Test Results -->
                    <div class="card mb-3" id="connection-test-card" style="display: none;">
                        <div class="card-header">
                            <h4 class="mb-0"><i class="fa fa-network-wired"></i> Connection Test Results</h4>
                        </div>
                        <div class="card-body">
                            <div id="connection-test-results"></div>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0"><i class="fa fa-info-circle"></i> Instructions</h4>
                        </div>
                        <div class="card-body">
                            <h5>Starting the WebSocket Server</h5>
                            <p>To start the WebSocket server, run the following command in your terminal:</p>
                            <pre class="bg-light p-3"><code>php artisan websockets:serve</code></pre>
                            
                            <h5 class="mt-3">For Production</h5>
                            <p>In production, you should use a process manager like Supervisor to keep the WebSocket server running.</p>
                            
                            <h5 class="mt-3">Testing WebSocket</h5>
                            <ul>
                                <li>Click "Test WebSocket" to open the test page</li>
                                <li>Send messages and verify they are received in real-time</li>
                                <li>Open multiple tabs to see real-time synchronization</li>
                            </ul>

                            <h5 class="mt-3">Debug Mode</h5>
                            <p>To run the WebSocket server in debug mode with verbose output:</p>
                            <pre class="bg-light p-3"><code>php artisan websockets:serve --debug</code></pre>

                            <div class="alert alert-warning mt-3">
                                <i class="fa fa-exclamation-triangle"></i>
                                <strong>Note:</strong> Make sure the WebSocket server is running before testing. 
                                The server must be started separately from your Laravel application.
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
        // Check status on page load
        checkServerStatus();

        // Refresh button
        $('#refresh-status').on('click', function() {
            checkServerStatus();
        });

        // Test connection button
        $('#test-connection').on('click', function() {
            testWebSocketConnection();
        });

        function checkServerStatus() {
            $('#server-status').html('<i class="fa fa-spinner fa-spin"></i> Checking server status...');
            
            $.ajax({
                url: '/websocket/status',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.status === 'ok') {
                        $('#server-status').removeClass('alert-info alert-danger').addClass('alert-success');
                        $('#server-status').html('<i class="fa fa-check-circle"></i> WebSocket server is configured and ready');
                    } else {
                        $('#server-status').removeClass('alert-info alert-success').addClass('alert-warning');
                        $('#server-status').html('<i class="fa fa-exclamation-triangle"></i> WebSocket server status: ' + data.status);
                    }

                    // Update configuration
                    if (data.config) {
                        $('#config-driver').text(data.config.broadcast_driver || 'N/A');
                        $('#config-host').text(data.config.pusher_host || 'N/A');
                        $('#config-port').text(data.config.pusher_port || 'N/A');
                        $('#config-scheme').text(data.config.pusher_scheme || 'N/A');
                    }
                },
                error: function(xhr) {
                    $('#server-status').removeClass('alert-info alert-success').addClass('alert-danger');
                    $('#server-status').html('<i class="fa fa-times-circle"></i> Error checking server status: ' + xhr.statusText);
                }
            });
        }

        function testWebSocketConnection() {
            $('#connection-test-card').show();
            $('#connection-test-results').html('<i class="fa fa-spinner fa-spin"></i> Testing connection...');

            // Test if Echo is available
            if (typeof window.Echo !== 'undefined') {
                let results = '<div class="alert alert-success"><i class="fa fa-check"></i> Echo is available</div>';
                
                // Try to connect to a test channel
                try {
                    const testChannel = window.Echo.channel('test-channel');
                    results += '<div class="alert alert-success"><i class="fa fa-check"></i> Successfully subscribed to test channel</div>';
                    
                    // Try to send a test message
                    $.ajax({
                        url: '/websocket/test',
                        type: 'POST',
                        data: {
                            message: 'Dashboard connection test',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                results += '<div class="alert alert-success"><i class="fa fa-check"></i> Test message sent successfully</div>';
                            } else {
                                results += '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Test message response: ' + (response.message || 'Unknown') + '</div>';
                            }
                            $('#connection-test-results').html(results);
                        },
                        error: function(xhr) {
                            results += '<div class="alert alert-danger"><i class="fa fa-times"></i> Failed to send test message: ' + xhr.statusText + '</div>';
                            $('#connection-test-results').html(results);
                        }
                    });
                } catch (error) {
                    results += '<div class="alert alert-danger"><i class="fa fa-times"></i> Error connecting to channel: ' + error.message + '</div>';
                    $('#connection-test-results').html(results);
                }
            } else {
                $('#connection-test-results').html(
                    '<div class="alert alert-danger"><i class="fa fa-times"></i> Echo is not available. ' +
                    'Please check your Echo configuration in resources/js/bootstrap.js</div>'
                );
            }
        }
    });
</script>
@endsection

