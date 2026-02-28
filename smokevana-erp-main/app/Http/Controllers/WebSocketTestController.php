<?php

namespace App\Http\Controllers;

use App\Events\TestWebSocketEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebSocketTestController extends Controller
{
    /**
     * Test WebSocket functionality by dispatching a test event
     */
    public function testWebSocket(Request $request)
    {
        Log::info('WebSocket test endpoint hit', ['request' => $request->all()]);
        
        $message = $request->input('message', 'Test message from WebSocket!');
        
        try {
            // Dispatch the test event
            event(new TestWebSocketEvent($message));
            
            Log::info('WebSocket event dispatched successfully', ['message' => $message]);
            
            return response()->json([
                'success' => true,
                'message' => 'WebSocket event dispatched successfully',
                'data' => [
                    'message' => $message,
                    'timestamp' => now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error dispatching WebSocket event', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error dispatching event: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show WebSocket test page
     */
    public function showTestPage()
    {
        Log::info('WebSocket test page accessed');
        return view('websocket.test');
    }

    /**
     * Get WebSocket status
     */
    public function getStatus()
    {
        Log::info('WebSocket status endpoint hit');
        
        try {
            $config = [
                'broadcast_driver' => config('broadcasting.default'),
                'pusher_host' => config('broadcasting.connections.pusher.options.host'),
                'pusher_port' => config('broadcasting.connections.pusher.options.port'),
                'pusher_scheme' => config('broadcasting.connections.pusher.options.scheme')
            ];
            
            Log::info('WebSocket config retrieved', $config);
            
            return response()->json([
                'status' => 'connected',
                'timestamp' => now()->toISOString(),
                'config' => $config
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting WebSocket status', ['error' => $e->getMessage()]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error getting status: ' . $e->getMessage()
            ], 500);
        }
    }
} 