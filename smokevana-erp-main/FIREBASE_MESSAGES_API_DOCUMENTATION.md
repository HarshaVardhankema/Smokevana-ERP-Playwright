# Firebase Messages API Documentation

## Overview
This API provides complete message management functionality for Firebase push notifications with database storage. Messages are automatically saved when push notifications are sent and can be managed through various endpoints. Messages support priority levels (urgent/non_urgent) and status tracking.

## Base URL
```
https://your-domain.com/api/notifications
```

## Authentication
All endpoints require authentication using Laravel Sanctum tokens. Include the token in the Authorization header:
```
Authorization: Bearer {your_token}
```

## Message Status Flow
- **unread** → **read** (when user marks as read)
- **unread/read** → **deleted** (when user deletes - soft delete)
- **deleted** → **unread/read** (when restored)

## Priority Levels
- **urgent** - High priority messages that need immediate attention
- **non_urgent** - Regular priority messages (default)

---

## 📨 Message Management Endpoints

### 1. Get All Messages
**GET** `/api/notifications/messages`

Get all non-deleted messages for the authenticated user (paginated).

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "New Order Received",
        "message": "You have received a new order #12345",
        "user_id": 8,
        "status": "unread",
        "priority": "urgent",
        "deleted": false,
        "created_at": "2026-02-05T12:00:00.000000Z",
        "updated_at": "2026-02-05T12:00:00.000000Z"
      }
    ],
    "first_page_url": "http://domain/api/notifications/messages?page=1",
    "from": 1,
    "last_page": 1,
    "per_page": 20,
    "to": 1,
    "total": 1
  }
}
```

### 2. Get Unread Messages
**GET** `/api/notifications/messages/unread`

Get all unread messages for the authenticated user.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "New Order Received",
      "message": "You have received a new order #12345",
      "user_id": 8,
      "status": "unread",
      "priority": "urgent",
      "deleted": false,
      "created_at": "2026-02-05T12:00:00.000000Z",
      "updated_at": "2026-02-05T12:00:00.000000Z"
    }
  ],
  "unread_count": 1
}
```

### 3. Get Read Messages
**GET** `/api/notifications/messages/read`

Get all read messages for the authenticated user (paginated).

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 2,
        "title": "Order Completed",
        "message": "Order #12344 has been completed successfully",
        "user_id": 8,
        "status": "read",
        "priority": "non_urgent",
        "deleted": false,
        "created_at": "2026-02-04T10:00:00.000000Z",
        "updated_at": "2026-02-04T10:30:00.000000Z"
      }
    ],
    "first_page_url": "http://domain/api/notifications/messages/read?page=1",
    "from": 1,
    "last_page": 1,
    "per_page": 20,
    "to": 1,
    "total": 1
  }
}
```

### 4. Get Urgent Messages
**GET** `/api/notifications/messages/urgent`

Get all urgent messages for the authenticated user (paginated).

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "Critical System Alert",
        "message": "Server CPU usage is above 90%",
        "user_id": 8,
        "status": "unread",
        "priority": "urgent",
        "deleted": false,
        "created_at": "2026-02-05T12:00:00.000000Z",
        "updated_at": "2026-02-05T12:00:00.000000Z"
      }
    ],
    "first_page_url": "http://domain/api/notifications/messages/urgent?page=1",
    "from": 1,
    "last_page": 1,
    "per_page": 20,
    "to": 1,
    "total": 1
  }
}
```

### 5. Get Non-Urgent Messages
**GET** `/api/notifications/messages/non-urgent`

Get all non-urgent messages for the authenticated user (paginated).

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 3,
        "title": "Newsletter",
        "message": "Check out our latest updates",
        "user_id": 8,
        "status": "read",
        "priority": "non_urgent",
        "deleted": false,
        "created_at": "2026-02-03T08:00:00.000000Z",
        "updated_at": "2026-02-03T08:30:00.000000Z"
      }
    ],
    "first_page_url": "http://domain/api/notifications/messages/non-urgent?page=1",
    "from": 1,
    "last_page": 1,
    "per_page": 20,
    "to": 1,
    "total": 1
  }
}
```

### 6. Get Deleted Messages
**GET** `/api/notifications/messages/deleted`

Get all soft-deleted messages for the authenticated user (paginated).

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 4,
        "title": "Old Notification",
        "message": "This message has been deleted",
        "user_id": 8,
        "status": "read",
        "priority": "non_urgent",
        "deleted": true,
        "created_at": "2026-02-03T08:00:00.000000Z",
        "updated_at": "2026-02-05T09:00:00.000000Z"
      }
    ],
    "first_page_url": "http://domain/api/notifications/messages/deleted?page=1",
    "from": 1,
    "last_page": 1,
    "per_page": 20,
    "to": 1,
    "total": 1
  }
}
```

---

## 🔧 Message Actions

### 7. Mark Message as Read
**PATCH** `/api/notifications/messages/{messageId}/read`

Mark a specific message as read.

**Parameters:**
- `messageId` (integer, required): ID of the message to mark as read

**Response:**
```json
{
  "success": true,
  "message": "Message marked as read",
  "data": {
    "id": 1,
    "title": "New Order Received",
    "message": "You have received a new order #12345",
    "user_id": 8,
    "status": "read",
    "priority": "urgent",
    "deleted": false,
    "created_at": "2026-02-05T12:00:00.000000Z",
    "updated_at": "2026-02-05T12:30:00.000000Z"
  }
}
```

### 8. Mark Message as Unread
**PATCH** `/api/notifications/messages/{messageId}/unread`

Mark a specific message as unread.

**Parameters:**
- `messageId` (integer, required): ID of the message to mark as unread

**Response:**
```json
{
  "success": true,
  "message": "Message marked as unread",
  "data": {
    "id": 1,
    "title": "New Order Received",
    "message": "You have received a new order #12345",
    "user_id": 8,
    "status": "unread",
    "priority": "urgent",
    "deleted": false,
    "created_at": "2026-02-05T12:00:00.000000Z",
    "updated_at": "2026-02-05T12:35:00.000000Z"
  }
}
```

### 9. Soft Delete Message
**DELETE** `/api/notifications/messages/{messageId}`

Soft delete a message (sets `deleted` to true, doesn't remove from database).

**Parameters:**
- `messageId` (integer, required): ID of the message to delete

**Response:**
```json
{
  "success": true,
  "message": "Message deleted successfully"
}
```

### 10. Restore Deleted Message
**PATCH** `/api/notifications/messages/{messageId}/restore`

Restore a soft-deleted message.

**Parameters:**
- `messageId` (integer, required): ID of the message to restore

**Response:**
```json
{
  "success": true,
  "message": "Message restored successfully",
  "data": {
    "id": 3,
    "title": "Old Notification",
    "message": "This message has been restored",
    "user_id": 8,
    "status": "read",
    "priority": "non_urgent",
    "deleted": false,
    "created_at": "2026-02-03T08:00:00.000000Z",
    "updated_at": "2026-02-05T12:40:00.000000Z"
  }
}
```

---

## 📱 Push Notification Endpoints

### 11. Store Push Notification Token
**POST** `/api/firebase/store-push-notification-token`

Store FCM token for push notifications.

**Request Body:**
```json
{
  "token": "fcm_device_token_here",
  "platform": "android", // or "ios"
  "device_id": "unique_device_identifier"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Push notification token stored successfully"
}
```

### 12. Send Test Notification
**POST** `/api/firebase/send-test-notification`

Send a test push notification to a user with optional priority.

**Request Body:**
```json
{
  "title": "Test Notification",
  "message": "This is a test notification",
  "user_id": 8,
  "priority": "urgent", // optional: "urgent" or "non_urgent" (default: "non_urgent")
  "data": {
    "custom_key": "custom_value"
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Push notification sent successfully",
  "data": {
    "success": true,
    "message": "Push notification sent successfully",
    "user_id": 8,
    "tokens_sent": 1,
    "message_id": 123,
    "response": {
      "multicast_id": "123456789",
      "success": 1,
      "failure": 0,
      "results": [{}]
    }
  }
}
```

---

## 🚨 Error Responses

All endpoints return consistent error responses:

### Authentication Error (401)
```json
{
  "message": "Unauthenticated."
}
```

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### Not Found Error (404)
```json
{
  "success": false,
  "message": "Message not found"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Internal server error: Error details"
}
```

---

## 💻 Frontend Integration Examples

### JavaScript/Fetch API

```javascript
// Get urgent messages
const getUrgentMessages = async () => {
  try {
    const response = await fetch('/api/notifications/messages/urgent', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });
    
    const data = await response.json();
    if (data.success) {
      console.log('Urgent messages:', data.data);
    }
  } catch (error) {
    console.error('Error:', error);
  }
};

// Send urgent notification
const sendUrgentNotification = async (userId, title, message) => {
  try {
    const response = await fetch('/api/firebase/send-test-notification', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        title,
        message,
        user_id: userId,
        priority: 'urgent'
      })
    });
    
    const data = await response.json();
    if (data.success) {
      console.log('Urgent notification sent:', data.data);
    }
  } catch (error) {
    console.error('Error:', error);
  }
};
```

### React Hook Example with Priority

```javascript
import { useState, useEffect } from 'react';

const useMessages = () => {
  const [urgentMessages, setUrgentMessages] = useState([]);
  const [nonUrgentMessages, setNonUrgentMessages] = useState([]);
  const [unreadCount, setUnreadCount] = useState(0);
  const [loading, setLoading] = useState(false);

  const getMessagesByPriority = async (priority) => {
    setLoading(true);
    try {
      const endpoint = priority === 'urgent' ? 
        '/api/notifications/messages/urgent' : 
        '/api/notifications/messages/non-urgent';
        
      const response = await fetch(endpoint, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      });
      
      const data = await response.json();
      if (data.success) {
        if (priority === 'urgent') {
          setUrgentMessages(data.data.data || data.data);
        } else {
          setNonUrgentMessages(data.data.data || data.data);
        }
      }
    } catch (error) {
      console.error(`Error fetching ${priority} messages:`, error);
    } finally {
      setLoading(false);
    }
  };

  const getUnreadCount = async () => {
    try {
      const response = await fetch('/api/notifications/messages/unread', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        }
      });
      
      const data = await response.json();
      if (data.success) {
        setUnreadCount(data.unread_count);
      }
    } catch (error) {
      console.error('Error fetching unread count:', error);
    }
  };

  useEffect(() => {
    getMessagesByPriority('urgent');
    getMessagesByPriority('non_urgent');
    getUnreadCount();
  }, []);

  return {
    urgentMessages,
    nonUrgentMessages,
    unreadCount,
    loading,
    getMessagesByPriority,
    getUnreadCount
  };
};

// Usage in component
const MessageComponent = () => {
  const { urgentMessages, nonUrgentMessages, unreadCount } = useMessages();

  return (
    <div>
      <h2>Messages ({unreadCount} unread)</h2>
      
      {/* Urgent Messages Section */}
      <div className="urgent-section">
        <h3>🚨 Urgent Messages</h3>
        {urgentMessages.map(message => (
          <div key={message.id} className="urgent-message">
            <h4>{message.title}</h4>
            <p>{message.message}</p>
            <small>{new Date(message.created_at).toLocaleString()}</small>
          </div>
        ))}
      </div>
      
      {/* Non-Urgent Messages Section */}
      <div className="non-urgent-section">
        <h3>📝 Regular Messages</h3>
        {nonUrgentMessages.map(message => (
          <div key={message.id} className="message">
            <h4>{message.title}</h4>
            <p>{message.message}</p>
            <small>{new Date(message.created_at).toLocaleString()}</small>
          </div>
        ))}
      </div>
    </div>
  );
};
```

---

## 📊 Database Schema

### Messages Table Structure

```sql
CREATE TABLE `sent_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `status` enum('unread','read') NOT NULL DEFAULT 'unread',
  `priority` enum('urgent','non_urgent') NOT NULL DEFAULT 'non_urgent',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sent_messages_user_id_priority_status_deleted_index` (`user_id`,`priority`,`status`,`deleted`),
  CONSTRAINT `sent_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);
```

---

## 🔄 Message Lifecycle

1. **Message Created**: When a push notification is sent, a message record is automatically created with:
   - `status = 'unread'`
   - `priority = 'urgent'` or `'non_urgent'` (specified during send)
   - `deleted = false`

2. **Mark as Read**: User marks message as read → `status = 'read'`.

3. **Mark as Unread**: User marks message as unread → `status = 'unread'`.

4. **Soft Delete**: User deletes message → `deleted = true` (message stays in database but hidden from normal views).

5. **Restore**: User restores deleted message → `deleted = false`.

---

## 🎯 Best Practices

1. **Use Priority Appropriately**:
   - `urgent`: Critical alerts, security issues, time-sensitive notifications
   - `non_urgent`: Regular updates, newsletters, general information

2. **Always check the `success` field** in API responses before processing data.

3. **Handle pagination** for endpoints that return paginated data.

4. **Update local state immediately** after successful actions for better UX.

5. **Display urgent messages prominently** in the UI with visual indicators (red color, alert icons).

6. **Implement pull-to-refresh** for message lists.

7. **Handle network errors gracefully** with retry mechanisms.

8. **Use proper loading states** during API calls.

9. **Show unread count** prominently, especially for urgent messages.

10. **Consider real-time updates** for urgent messages using WebSocket or polling.

---

## 📞 Support

For any issues or questions regarding the Messages API, please contact the development team.
