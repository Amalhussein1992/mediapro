# Media Pro API Documentation

## Base URL
```
http://your-domain.com/api
```

## Authentication
All protected endpoints require Bearer token authentication using Laravel Sanctum.

Include the token in the Authorization header:
```
Authorization: Bearer {your_token}
```

---

## 🔐 Authentication Endpoints

### Register
```http
POST /auth/register
```

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "1|abc123..."
  }
}
```

### Login
```http
POST /auth/login
```

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "1|abc123..."
  }
}
```

### Logout
```http
POST /auth/logout
```
*Requires authentication*

**Response:**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

### Get Current User
```http
GET /auth/user
```
*Requires authentication*

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "subscription_status": "active"
  }
}
```

---

## 📝 Posts Endpoints

### List Posts
```http
GET /posts
```
*Requires authentication*

**Query Parameters:**
- `page` (integer): Page number
- `per_page` (integer): Items per page
- `status` (string): Filter by status (draft, scheduled, published)

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "content": "Post content here...",
        "status": "published",
        "platforms": ["instagram", "facebook"],
        "scheduled_at": "2025-01-15 10:00:00",
        "published_at": "2025-01-15 10:00:00",
        "analytics": {
          "likes": 150,
          "comments": 25,
          "shares": 10,
          "views": 1000
        }
      }
    ],
    "total": 100
  }
}
```

### Create Post
```http
POST /posts
```
*Requires authentication*

**Request Body:**
```json
{
  "content": "Your post content here...",
  "platforms": ["instagram", "facebook", "twitter"],
  "status": "scheduled",
  "scheduled_at": "2025-01-15 10:00:00",
  "media": ["url1", "url2"]
}
```

### Update Post
```http
PUT /posts/{id}
```
*Requires authentication*

### Delete Post
```http
DELETE /posts/{id}
```
*Requires authentication*

---

## 📊 Analytics Endpoints

### Dashboard Analytics
```http
GET /analytics/dashboard
```
*Requires authentication*

**Query Parameters:**
- `start_date` (date): Start date (default: 30 days ago)
- `end_date` (date): End date (default: today)

**Response:**
```json
{
  "success": true,
  "data": {
    "stats": {
      "total_posts": 150,
      "published_posts": 120,
      "scheduled_posts": 20,
      "draft_posts": 10,
      "total_social_accounts": 5,
      "active_social_accounts": 5
    },
    "posts_timeline": [...],
    "platform_stats": [
      {
        "platform": "instagram",
        "count": 45
      }
    ],
    "engagement": {
      "total_likes": 5000,
      "total_comments": 800,
      "total_shares": 300,
      "total_views": 25000
    },
    "top_posts": [...]
  }
}
```

### Post Analytics
```http
GET /analytics/posts/{postId}
```
*Requires authentication*

**Response:**
```json
{
  "success": true,
  "data": {
    "post": {...},
    "metrics": {
      "likes": 150,
      "comments": 25,
      "shares": 10,
      "views": 1000
    },
    "engagement_rate": 15.5,
    "platforms_performance": [...]
  }
}
```

### Account Analytics
```http
GET /analytics/accounts/{accountId}
```
*Requires authentication*

### Engagement Trends
```http
GET /analytics/trends
```
*Requires authentication*

**Query Parameters:**
- `days` (integer): Number of days (default: 30)

### Best Posting Times
```http
GET /analytics/best-times
```
*Requires authentication*

**Response:**
```json
{
  "success": true,
  "data": {
    "best_hours": [...],
    "best_days": [...],
    "recommendation": {
      "best_hour": 14,
      "best_day": "Monday",
      "message": "Best time to post is on Monday at 14:00"
    }
  }
}
```

---

## 🤖 AI Content Generation Endpoints

### Generate Caption
```http
POST /ai/generate-caption
```
*Requires authentication*

**Request Body:**
```json
{
  "topic": "Social media marketing tips",
  "tone": "professional",
  "platform": "instagram",
  "length": "medium"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "caption": "Generated caption here...",
    "variations": ["Variation 1", "Variation 2", "Variation 3"],
    "metadata": {
      "tone": "professional",
      "platform": "instagram",
      "length": "medium",
      "word_count": 45
    }
  }
}
```

### Generate Hashtags
```http
POST /ai/generate-hashtags
```
*Requires authentication*

**Request Body:**
```json
{
  "content": "Your post content",
  "platform": "instagram",
  "count": 10
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "hashtags": ["#SocialMedia", "#Marketing", ...],
    "trending": ["#Trending1", "#Trending2"],
    "recommended_count": {
      "min": 5,
      "max": 30,
      "optimal": 11
    }
  }
}
```

### Improve Content
```http
POST /ai/improve-content
```
*Requires authentication*

**Request Body:**
```json
{
  "content": "Your content to improve",
  "improvements": ["grammar", "engagement", "clarity", "seo", "emojis"]
}
```

### Generate Content Ideas
```http
POST /ai/generate-ideas
```
*Requires authentication*

**Request Body:**
```json
{
  "niche": "fitness",
  "platform": "instagram",
  "count": 10
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "ideas": [
      {
        "title": "10 Tips for fitness Success",
        "type": "listicle",
        "engagement_potential": "high"
      }
    ],
    "trending_topics": [...],
    "content_calendar_suggestions": [...]
  }
}
```

---

## 🔗 Social Accounts Endpoints

### List Social Accounts
```http
GET /social-accounts
```
*Requires authentication*

### Connect Social Account
```http
POST /social-accounts
```
*Requires authentication*

**Request Body:**
```json
{
  "platform": "instagram",
  "account_name": "myaccount",
  "access_token": "token_here",
  "is_active": true
}
```

### Update Social Account
```http
PUT /social-accounts/{id}
```
*Requires authentication*

### Delete Social Account
```http
DELETE /social-accounts/{id}
```
*Requires authentication*

---

## 🎨 Brand Kit Endpoints

### List Brand Kits
```http
GET /brand-kits
```
*Requires authentication*

### Create Brand Kit
```http
POST /brand-kits
```
*Requires authentication*

**Request Body (multipart/form-data):**
```json
{
  "name": "My Brand",
  "primary_color": "#6366F1",
  "secondary_color": "#A855F7",
  "accent_color": "#EC4899",
  "logo": "file upload",
  "fonts": ["Arial", "Helvetica"],
  "templates": []
}
```

### Update Brand Kit
```http
PUT /brand-kits/{id}
```
*Requires authentication*

### Delete Brand Kit
```http
DELETE /brand-kits/{id}
```
*Requires authentication*

---

## 💳 Subscription Endpoints

### List Subscription Plans
```http
GET /subscription-plans
```
*Public endpoint*

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Starter",
      "price": 19.99,
      "billing_cycle": "monthly",
      "features": {
        "social_accounts": 5,
        "posts_per_month": 30,
        "analytics": "basic",
        "ai_content": true
      }
    }
  ]
}
```

### Subscribe to Plan
```http
POST /subscriptions/subscribe
```
*Requires authentication*

**Request Body:**
```json
{
  "plan_id": 1,
  "payment_method": "stripe",
  "billing_cycle": "monthly"
}
```

### Get Current Subscription
```http
GET /subscriptions/current
```
*Requires authentication*

### Cancel Subscription
```http
POST /subscriptions/cancel
```
*Requires authentication*

---

## 💰 Payment Endpoints

### List Payments
```http
GET /payments
```
*Requires authentication*

### Create Payment
```http
POST /payments
```
*Requires authentication*

**Request Body:**
```json
{
  "amount": 19.99,
  "currency": "USD",
  "payment_method": "stripe",
  "subscription_plan_id": 1
}
```

---

## 📤 Response Format

### Success Response
```json
{
  "success": true,
  "data": {...},
  "message": "Optional success message"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message here",
  "errors": {
    "field": ["Error details"]
  }
}
```

---

## 🔢 Status Codes

- `200` - OK
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

---

## 📱 Mobile App Integration Guide

### Step 1: Authentication
1. Implement login/register screens
2. Store the authentication token securely
3. Include token in all API requests

### Step 2: Core Features
- **Dashboard**: Use `/analytics/dashboard` endpoint
- **Create Post**: Use `/posts` endpoint
- **Schedule Post**: Set `scheduled_at` field
- **AI Features**: Use `/ai/*` endpoints
- **Analytics**: Use `/analytics/*` endpoints

### Step 3: Real-time Updates
- Implement polling for analytics updates
- Use WebSockets for real-time notifications (coming soon)

### Step 4: Offline Support
- Cache user data locally
- Sync when connection is restored
- Queue posts for scheduling

---

## 🎯 Best Practices

1. **Rate Limiting**: Respect API rate limits (100 requests/minute)
2. **Error Handling**: Always handle API errors gracefully
3. **Token Management**: Refresh tokens before expiry
4. **Caching**: Cache static data (subscription plans, etc.)
5. **Pagination**: Use pagination for list endpoints
6. **Validation**: Validate input before sending to API
7. **Security**: Never store sensitive data in plain text

---

## 📞 Support

For API support, contact: support@mediapro.com

## 🔄 Changelog

### Version 1.0.0 (2025-01-01)
- Initial API release
- Authentication endpoints
- Posts management
- Analytics endpoints
- AI content generation
- Brand kit management
- Subscription system
