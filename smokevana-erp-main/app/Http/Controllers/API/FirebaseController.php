<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FirebasePushNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class FirebaseController extends Controller
{
    /**
     * Store FCM token
     * 
     * POST /api/firebase/store-push-notification-token
     */
    public function storePushNotificationToken(Request $request)
    {
        // Accept both userId (camelCase) and user_id (snake_case)
        $payload = $request->all();
        if (empty($payload['userId']) && !empty($payload['user_id'])) {
            $payload['userId'] = $payload['user_id'];
        }

        // Accept userId as either users.id or contacts.id (ECOM app authenticates as Contact)
        $validator = Validator::make($payload, [
            'token' => 'required|string|max:255',
            'userId' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $existsInUsers = \Illuminate\Support\Facades\DB::table('users')->where('id', $value)->exists();
                    $existsInContacts = \Illuminate\Support\Facades\DB::table('contacts')->where('id', $value)->exists();
                    if (!$existsInUsers && !$existsInContacts) {
                        $fail('The selected user id must exist in users or contacts table.');
                    }
                },
            ],
            'platform' => 'required|string|in:web,android,ios',
            'timestamp' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Deactivate existing tokens for this user and platform
            $userId = $payload['userId'];
            $platform = $payload['platform'];
            $token = $payload['token'];
            $timestamp = $payload['timestamp'];

            FirebasePushNotification::where('user_id', $userId)
                ->where('platform', $platform)
                ->update(['is_active' => false]);

            // Create or update the token
            $pushNotification = FirebasePushNotification::updateOrCreate(
                [
                    'token' => $token,
                    'user_id' => $userId
                ],
                [
                    'platform' => $platform,
                    'timestamp' => now()->parse($timestamp),
                    'is_active' => true
                ]
            );

            Log::info('FCM token stored', [
                'user_id' => $userId,
                'platform' => $platform,
                'token_id' => $pushNotification->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Token stored successfully',
                'data' => [
                    'id' => $pushNotification->id,
                    'token' => $pushNotification->token,
                    'platform' => $pushNotification->platform,
                    'isActive' => $pushNotification->is_active,
                    'timestamp' => $pushNotification->timestamp
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error storing FCM token', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get all notifications with unread count
     * 
     * GET /api/notifications/all-notifications?page=1&limit=10
     */
    public function getAllNotifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'integer|min:1',
            'limit' => 'integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = auth()->user();
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 10);

            // Get notifications (assuming you have a notifications table)
            // This is a placeholder - adjust based on your actual notification system
            $query = $user->notifications()->orderBy('created_at', 'desc');
            
            $total = $query->count();
            $unreadCount = $query->whereNull('read_at')->count();
            
            $notifications = $query
                ->offset(($page - 1) * $limit)
                ->limit($limit)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->data['title'] ?? 'Notification',
                        'body' => $notification->data['body'] ?? '',
                        'type' => $notification->type ?? 'general',
                        'readAt' => $notification->read_at,
                        'createdAt' => $notification->created_at,
                        'data' => $notification->data,
                        'orderId' => $notification->order_id,
                        'order_type' => $notification->order_type,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => $notifications,
                    'unReadCount' => $unreadCount,
                    'pagination' => [
                        'currentPage' => $page,
                        'limit' => $limit,
                        'total' => $total,
                        'totalPages' => ceil($total / $limit)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching notifications', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Mark notification as read
     * 
     * PATCH /api/notifications/mark-read/:notificationId
     */
    public function markAsRead($notificationId)
    {
        try {
            $user = auth()->user();
            
            // Find the notification
            $notification = $user->notifications()->find($notificationId);
            
            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            // Mark as read
            $notification->markAsRead();

            Log::info('Notification marked as read', [
                'notification_id' => $notificationId,
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'data' => [
                    'id' => $notification->id,
                    'readAt' => $notification->read_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking notification as read', [
                'error' => $e->getMessage(),
                'notification_id' => $notificationId,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Delete notification
     * 
     * DELETE /api/notifications/delete-notification/:notificationId
     */
    public function deleteNotification($notificationId)
    {
        try {
            $user = auth()->user();
            
            // Find the notification
            $notification = $user->notifications()->find($notificationId);
            
            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            // Delete the notification
            $notificationId = $notification->id;
            $notification->delete();

            Log::info('Notification deleted', [
                'notification_id' => $notificationId,
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully',
                'data' => [
                    'id' => $notificationId
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting notification', [
                'error' => $e->getMessage(),
                'notification_id' => $notificationId,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Send test notification
     * 
     * POST /api/firebase/send-test-notification
     */
    public function sendTestNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:500',
            'user_id' => 'required|integer',
            'order_id' => 'integer|nullable',
            'order_type' => 'string|nullable',
            'priority' => 'in:urgent,non_urgent'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $notificationUtil = new \App\Utils\NotificationUtil();
            
            // Prepare data array with order_id and order_type if provided
            $data = $request->get('data', []);
            if ($request->has('order_id')) {
                $data['order_id'] = $request->order_id;
            }
            if ($request->has('order_type')) {
                $data['order_type'] = $request->order_type;
            }
            
            $result = $notificationUtil->sendPushNotification(
                $request->title,
                $request->message,
                $request->user_id,
                $data,
                $request->get('priority', 'non_urgent')
            );

            // Get the created message to return full details
            $messageId = $result['message_id'] ?? null;
            $message = null;
            if ($messageId) {
                $message = \App\Models\SentMessage::find($messageId);
            }

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => $result,
                'sent_message' => $message ? $this->transformSingleMessage($message) : null
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending test notification', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a test notification with order data (for testing purposes)
     * 
     * POST /api/notifications/test-create
     * Requires authentication
     */
    public function createTestNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'message' => 'string|max:500',
            'order_id' => 'integer|nullable',
            'order_type' => 'string|nullable',
            'priority' => 'in:urgent,non_urgent'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $notificationUtil = new \App\Utils\NotificationUtil();
            
            $title = $request->get('title', 'Test Notification');
            $message = $request->get('message', 'This is a test notification with order data');
            $orderId = $request->get('order_id');
            $orderType = $request->get('order_type');
            $priority = $request->get('priority', 'non_urgent');
            
            $data = [];
            if ($orderId !== null) {
                $data['order_id'] = $orderId;
            }
            if ($orderType !== null) {
                $data['order_type'] = $orderType;
            }
            
            $result = $notificationUtil->sendPushNotification(
                $title,
                $message,
                $user->id,
                $data,
                $priority
            );

            // Get the created message
            $messageId = $result['message_id'] ?? null;
            $sentMessage = null;
            if ($messageId) {
                $sentMessage = \App\Models\SentMessage::find($messageId);
            }

            return response()->json([
                'success' => true,
                'message' => 'Test notification created successfully',
                'data' => [
                    'notification_result' => $result,
                    'sent_message' => $sentMessage ? $this->transformSingleMessage($sentMessage) : null
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating test notification', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all messages for the authenticated user
     * 
     * GET /api/notifications/messages?page=1&limit=50&order_id=123&order_type=sales_order
     */
    public function getMessages(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'page' => 'integer|min:1',
                'limit' => 'integer|min:1|max:100',
                'order_id' => 'integer|nullable',
                'order_type' => 'string|nullable'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $page = $request->get('page', 1);
            $limit = $request->get('limit', 20);
            $orderId = $request->get('order_id');
            $orderType = $request->get('order_type');

            $query = \App\Models\SentMessage::where('user_id', $request->user()->id)
                ->notDeleted();

            // Filter by order_id if provided
            if ($orderId !== null) {
                $query->where('order_id', $orderId);
            }

            // Filter by order_type if provided
            if ($orderType !== null) {
                $query->where('order_type', $orderType);
            }

            $messages = $query->orderBy('created_at', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            // Transform messages to include redirect field
            $messages = $this->transformMessages($messages);

            return response()->json([
                'success' => true,
                'data' => $messages
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting messages', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread messages for the authenticated user
     * 
     * GET /api/notifications/messages/unread
     */
    public function getUnreadMessages(Request $request)
    {
        try {
            $messages = \App\Models\SentMessage::where('user_id', $request->user()->id)
                ->unread()
                ->notDeleted()
                ->orderBy('created_at', 'desc')
                ->get();

            // Transform messages to include redirect field
            $messages = $this->transformMessages($messages);

            return response()->json([
                'success' => true,
                'data' => $messages,
                'unread_count' => $messages->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting unread messages', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get read messages for the authenticated user
     * 
     * GET /api/notifications/messages/read
     */
    public function getReadMessages(Request $request)
    {
        try {
            $messages = \App\Models\SentMessage::where('user_id', $request->user()->id)
                ->read()
                ->notDeleted()
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Transform messages to include redirect field
            $messages = $this->transformMessages($messages);

            return response()->json([
                'success' => true,
                'data' => $messages
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting read messages', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get urgent messages for the authenticated user
     * 
     * GET /api/notifications/messages/urgent
     */
    public function getUrgentMessages(Request $request)
    {
        try {
            $messages = \App\Models\SentMessage::where('user_id', $request->user()->id)
                ->urgent()
                ->notDeleted()
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Transform messages to include redirect field
            $messages = $this->transformMessages($messages);

            return response()->json([
                'success' => true,
                'data' => $messages
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting urgent messages', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get non-urgent messages for the authenticated user
     * 
     * GET /api/notifications/messages/non-urgent
     */
    public function getNonUrgentMessages(Request $request)
    {
        try {
            $messages = \App\Models\SentMessage::where('user_id', $request->user()->id)
                ->nonUrgent()
                ->notDeleted()
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Transform messages to include redirect field
            $messages = $this->transformMessages($messages);

            return response()->json([
                'success' => true,
                'data' => $messages
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting non-urgent messages', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark message as read
     * 
     * PATCH /api/notifications/messages/{messageId}/read
     */
    public function markMessageAsRead(Request $request, $messageId)
    {
        try {
            $message = \App\Models\SentMessage::where('id', $messageId)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $message->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Message marked as read',
                'data' => $this->transformSingleMessage($message)
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking message as read', [
                'error' => $e->getMessage(),
                'message_id' => $messageId,
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark message as unread
     * 
     * PATCH /api/notifications/messages/{messageId}/unread
     */
    public function markMessageAsUnread(Request $request, $messageId)
    {
        try {
            $message = \App\Models\SentMessage::where('id', $messageId)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $message->markAsUnread();

            return response()->json([
                'success' => true,
                'message' => 'Message marked as unread',
                'data' => $this->transformSingleMessage($message)
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking message as unread', [
                'error' => $e->getMessage(),
                'message_id' => $messageId,
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Soft delete message
     * 
     * DELETE /api/notifications/messages/{messageId}
     */
    public function softDeleteMessage(Request $request, $messageId)
    {
        try {
            $message = \App\Models\SentMessage::where('id', $messageId)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $message->softDelete();

            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error soft deleting message', [
                'error' => $e->getMessage(),
                'message_id' => $messageId,
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get deleted messages for the authenticated user
     * 
     * GET /api/notifications/messages/deleted
     */
    public function getDeletedMessages(Request $request)
    {
        try {
            $messages = \App\Models\SentMessage::where('user_id', $request->user()->id)
                ->deleted()
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Transform messages to include redirect field
            $messages = $this->transformMessages($messages);

            return response()->json([
                'success' => true,
                'data' => $messages
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting deleted messages', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore deleted message
     * 
     * PATCH /api/notifications/messages/{messageId}/restore
     */
    public function restoreMessage(Request $request, $messageId)
    {
        try {
            $message = \App\Models\SentMessage::where('id', $messageId)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $message->restore();

            return response()->json([
                'success' => true,
                'message' => 'Message restored successfully',
                'data' => $this->transformSingleMessage($message)
            ]);

        } catch (\Exception $e) {
            Log::error('Error restoring message', [
                'error' => $e->getMessage(),
                'message_id' => $messageId,
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transform message collection to include redirect field
     */
    private function transformMessages($messages)
    {
        if ($messages instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $transformed = $messages->getCollection()->map(function ($message) {
                return $this->transformSingleMessage($message);
            });
            $messages->setCollection($transformed);
            return $messages;
        } else {
            return $messages->map(function ($message) {
                return $this->transformSingleMessage($message);
            });
        }
    }

    /**
     * Transform a single message to include redirect field
     */
    private function transformSingleMessage($message)
    {
        return [
            'id' => $message->id,
            'title' => $message->title,
            'message' => $message->message,
            'user_id' => $message->user_id,
            'order_id' => $message->order_id,
            'order_type' => $message->order_type,
            'status' => $message->status,
            'priority' => $message->priority,
            'deleted' => $message->deleted,
            'redirect' => $message->order_id ? '/order-success' : null,
            'created_at' => $message->created_at,
            'updated_at' => $message->updated_at,
        ];
    }
}
