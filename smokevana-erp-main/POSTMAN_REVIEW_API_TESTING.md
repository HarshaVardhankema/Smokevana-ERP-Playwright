# Product Review API Testing Guide for Postman

## Base URL
```
https://smokevanaerp.phantasm-agents.ai/api
```

## Authentication
All authenticated endpoints require a Bearer token in the Authorization header.

**Header:**
```
Authorization: Bearer YOUR_ACCESS_TOKEN
```

To get an access token, first login via:
```
POST /api/customer/login
```

---

## 1. Get Product Reviews (Public - No Auth Required)

**GET** `/product/{productId}/reviews`

### Query Parameters:
- `per_page` (optional, default: 10) - Number of reviews per page
- `page` (optional, default: 1) - Page number
- `sort_by` (optional, default: 'latest') - Sort by 'latest' or 'likes'

### Example Request:
```
GET https://smokevanaerp.phantasm-agents.ai/api/product/1/reviews?per_page=10&page=1&sort_by=latest
```

### Expected Response:
```json
{
    "status": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "customer_name": "John Doe",
                "title": "Great Product!",
                "description": "This product exceeded my expectations...",
                "rating": 5,
                "media_url": "https://example.com/image.jpg",
                "media_type": "photo",
                "created_at": "2026-01-20 15:30:00"
            }
        ],
        "last_page": 1,
        "total": 5,
        "per_page": 10,
        "from": 1,
        "to": 5
    }
}
```

---

## 2. Create Review for a Product

**POST** `/product/{productId}/reviews`

**Requires Authentication:** Yes

### Headers:
```
Authorization: Bearer YOUR_ACCESS_TOKEN
Content-Type: application/json
Accept: application/json
```

### Request Body:
```json
{
    "description": "This is a detailed review of the product. I really enjoyed using it and found it very useful.",
    "rating": 5,
    "title": "Excellent Product!",
    "public_name": "Happy Customer",
    "transaction_id": 123,
    "media_url": "https://example.com/review-photo.jpg",
    "media_type": "photo"
}
```

### Field Details:
- `description` (required, 5-2000 characters) - Review text
- `rating` (optional, 1-5) - Star rating
- `title` (optional, max 255) - Review title
- `public_name` (optional, max 255) - Display name instead of real name
- `transaction_id` (optional) - If not provided, system finds latest transaction
- `media_url` (optional, valid URL, max 2048) - Photo/video URL
- `media_type` (optional, 'photo' or 'video') - Media type

### Example Request:
```
POST https://smokevanaerp.phantasm-agents.ai/api/product/1/reviews
```

### Expected Response (Success - 201):
```json
{
    "status": true,
    "message": "Review created successfully",
    "data": {
        "id": 1,
        "product_id": 1,
        "transaction_id": 123,
        "title": "Excellent Product!",
        "description": "This is a detailed review...",
        "public_name": "Happy Customer",
        "rating": 5,
        "media_url": "https://example.com/review-photo.jpg",
        "media_type": "photo",
        "created_at": "2026-01-20 15:30:00"
    }
}
```

### Error Responses:
- **401** - Unauthorized (no token or invalid token)
- **403** - Forbidden (product not purchased or transaction invalid)
- **409** - Conflict (review already exists for this product)
- **422** - Validation error

---

## 3. Create Review (Alternative Endpoint)

**POST** `/customer/reviews`

**Requires Authentication:** Yes

### Headers:
```
Authorization: Bearer YOUR_ACCESS_TOKEN
Content-Type: application/json
Accept: application/json
```

### Request Body:
```json
{
    "product_id": 1,
    "description": "This is a detailed review of the product.",
    "rating": 4,
    "title": "Good Product",
    "public_name": "Anonymous Reviewer",
    "transaction_id": 123,
    "media_url": "https://example.com/video.mp4",
    "media_type": "video"
}
```

### Example Request:
```
POST https://smokevanaerp.phantasm-agents.ai/api/customer/reviews
```

---

## 4. Get Customer's Own Reviews

**GET** `/customer/reviews`

**Requires Authentication:** Yes

### Query Parameters:
- `per_page` (optional, default: 10)
- `page` (optional, default: 1)

### Headers:
```
Authorization: Bearer YOUR_ACCESS_TOKEN
```

### Example Request:
```
GET https://smokevanaerp.phantasm-agents.ai/api/customer/reviews?per_page=10&page=1
```

### Expected Response:
```json
{
    "status": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "product_id": 1,
                "product_name": "Product Name",
                "product_image": "https://example.com/product.jpg",
                "title": "My Review Title",
                "description": "My review description...",
                "public_name": "My Display Name",
                "rating": 5,
                "media_url": "https://example.com/image.jpg",
                "media_type": "photo",
                "is_active": 1,
                "created_at": "2026-01-20 15:30:00",
                "updated_at": "2026-01-20 15:30:00"
            }
        ],
        "last_page": 1,
        "total": 1,
        "per_page": 10,
        "from": 1,
        "to": 1
    }
}
```

---

## 5. Get Single Review by ID

**GET** `/customer/reviews/{id}`

**Requires Authentication:** Yes

### Headers:
```
Authorization: Bearer YOUR_ACCESS_TOKEN
```

### Example Request:
```
GET https://smokevanaerp.phantasm-agents.ai/api/customer/reviews/1
```

### Expected Response:
```json
{
    "status": true,
    "data": {
        "id": 1,
        "customer_name": "John Doe",
        "product_id": 1,
        "product_name": "Product Name",
        "title": "Review Title",
        "description": "Review description...",
        "rating": 5,
        "media_url": "https://example.com/image.jpg",
        "media_type": "photo",
        "is_active": 1,
        "created_at": "2026-01-20 15:30:00",
        "updated_at": "2026-01-20 15:30:00"
    }
}
```

---

## 6. Update Review

**PUT** `/customer/reviews/{id}`

**Requires Authentication:** Yes

### Headers:
```
Authorization: Bearer YOUR_ACCESS_TOKEN
Content-Type: application/json
Accept: application/json
```

### Request Body:
```json
{
    "description": "Updated review description with more details.",
    "rating": 5,
    "title": "Updated Title",
    "public_name": "Updated Name",
    "media_url": "https://example.com/new-image.jpg",
    "media_type": "photo"
}
```

### Field Details:
- `description` (required, 10-2000 characters)
- `rating` (optional, 1-5)
- `title` (optional)
- `public_name` (optional)
- `media_url` (optional)
- `media_type` (optional, 'photo' or 'video')

### Example Request:
```
PUT https://smokevanaerp.phantasm-agents.ai/api/customer/reviews/1
```

### Expected Response:
```json
{
    "status": true,
    "message": "Review updated successfully",
    "data": {
        "id": 1,
        "product_id": 1,
        "title": "Updated Title",
        "description": "Updated review description...",
        "public_name": "Updated Name",
        "rating": 5,
        "media_url": "https://example.com/new-image.jpg",
        "media_type": "photo",
        "updated_at": "2026-01-20 16:00:00"
    }
}
```

---

## 7. Delete Review (Soft Delete)

**DELETE** `/customer/reviews/{id}`

**Requires Authentication:** Yes

### Headers:
```
Authorization: Bearer YOUR_ACCESS_TOKEN
```

### Example Request:
```
DELETE https://smokevanaerp.phantasm-agents.ai/api/customer/reviews/1
```

### Expected Response:
```json
{
    "status": true,
    "message": "Review deleted successfully"
}
```

---

## 8. Update Review Rating Only

**POST** `/customer/reviews/{id}/like`

**Requires Authentication:** Yes

### Headers:
```
Authorization: Bearer YOUR_ACCESS_TOKEN
Content-Type: application/json
Accept: application/json
```

### Request Body:
```json
{
    "rating": 5
}
```

### Example Request:
```
POST https://smokevanaerp.phantasm-agents.ai/api/customer/reviews/1/like
```

### Expected Response:
```json
{
    "status": true,
    "message": "Review rating updated successfully",
    "data": {
        "id": 1,
        "product_id": 1,
        "rating": 5,
        "updated_at": "2026-01-20 16:00:00"
    }
}
```

---

## Postman Collection Setup

### Step 1: Create Environment Variables

Create a Postman environment with:
- `base_url`: `https://smokevanaerp.phantasm-agents.ai/api`
- `access_token`: (will be set after login)

### Step 2: Login First

**POST** `{{base_url}}/customer/login`

Body (form-data or JSON):
```json
{
    "email": "customer@example.com",
    "password": "password123"
}
```

Save the `token` from response to `access_token` environment variable.

### Step 3: Set Authorization

For authenticated requests:
1. Go to **Authorization** tab
2. Type: **Bearer Token**
3. Token: `{{access_token}}`

Or add to Headers manually:
```
Authorization: Bearer {{access_token}}
```

---

## Testing Checklist

1. ✅ Get product reviews (public)
2. ✅ Login to get access token
3. ✅ Create review with all fields
4. ✅ Create review without optional fields
5. ✅ Create review with transaction_id
6. ✅ Create review without transaction_id (auto-find)
7. ✅ Get customer's own reviews
8. ✅ Get single review by ID
9. ✅ Update review (all fields)
10. ✅ Update review (partial fields)
11. ✅ Update review rating only
12. ✅ Delete review
13. ✅ Verify product ratings updated (check `average_rating` and `total_reviews` in products table)

---

## Common Error Responses

### 401 Unauthorized
```json
{
    "status": false,
    "message": "User not authenticated"
}
```

### 403 Forbidden
```json
{
    "status": false,
    "message": "You must purchase and receive an invoice for this product before reviewing it."
}
```

### 409 Conflict
```json
{
    "status": false,
    "message": "You have already reviewed this product. You can update your existing review.",
    "review_id": 1
}
```

### 422 Validation Error
```json
{
    "status": false,
    "message": [
        {
            "field": "description",
            "messages": ["The description must be at least 5 characters."]
        }
    ]
}
```

---

## Notes

- Product ratings (`average_rating` and `total_reviews`) are automatically updated when reviews are created, updated, or deleted
- Reviews are soft-deleted (not permanently removed)
- Only active, non-deleted reviews count toward product ratings
- Customer must have purchased the product (via transaction) to create a review
- Each customer can only have one active review per product
