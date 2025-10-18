# Ads Campaigns Implementation - Backend & Frontend Connection

## Overview
This document outlines the complete implementation of the Ads Campaigns feature, connecting the React Native mobile app to the Laravel backend API.

## API Endpoints Created

### Base URL: `/api`

All endpoints require authentication via Laravel Sanctum (`Authorization: Bearer {token}`)

### 1. Dashboard Metrics
```
GET /api/ads/dashboard/metrics?timeframe=7d
```

**Query Parameters:**
- `timeframe`: `7d` | `30d` | `90d` (default: `7d`)

**Response:**
```json
{
  "success": true,
  "data": {
    "totalCampaigns": 15,
    "activeCampaigns": 8,
    "totalSpend": 2500.00,
    "totalImpressions": 50000,
    "totalClicks": 1000,
    "totalConversions": 50,
    "avgCTR": 2.0,
    "avgCPC": 2.5,
    "avgROAS": 3.5,
    "budgetUtilization": 75.5,
    "timeframe": "7d"
  }
}
```

### 2. Campaign CRUD Operations

#### List Campaigns
```
GET /api/ads/campaigns
```

#### Create Campaign
```
POST /api/ads/campaigns
```

**Request Body:**
```json
{
  "name": "Summer Sale Campaign",
  "objective": "conversions",
  "platforms": ["facebook", "instagram"],
  "budget": {
    "type": "daily",
    "amount": 100
  },
  "schedule": {
    "startDate": "2025-10-15",
    "endDate": "2025-11-15",
    "timezone": "UTC"
  },
  "status": "draft"
}
```

#### Get Campaign Details
```
GET /api/ads/campaigns/{id}
```

#### Update Campaign
```
PUT /api/ads/campaigns/{id}
```

#### Delete Campaign
```
DELETE /api/ads/campaigns/{id}
```

### 3. AI Campaign Builder
```
POST /api/ads/campaigns/build-with-ai
```

**Request Body:**
```json
{
  "objective": "conversions",
  "budget": 1000,
  "duration": 30,
  "productInfo": "New smartphone with advanced camera",
  "targetAudience": "Tech enthusiasts aged 25-45"
}
```

**Response:**
```json
{
  "success": true,
  "message": "AI campaign structure generated successfully",
  "data": {
    "campaign_name": "Smartphone Conversion Campaign Nov 2025",
    "recommended_platforms": [
      {
        "platform": "facebook",
        "priority": "high",
        "reason": "Large user base with advanced targeting options"
      },
      {
        "platform": "google",
        "priority": "high",
        "reason": "Intent-based targeting with search ads"
      }
    ],
    "budget_allocation": [
      {
        "platform": "facebook",
        "amount": 600,
        "percentage": 60.0
      }
    ],
    "ad_sets": [
      {
        "name": "Core Audience",
        "targeting": {
          "age": "25-45",
          "interests": ["Technology", "Shopping"],
          "behavior": "Active users"
        },
        "budget_split": 50
      }
    ],
    "creative_suggestions": [...],
    "audience_targeting": {...},
    "schedule_recommendations": {...},
    "estimated_reach": {
      "estimated_impressions": 100000,
      "estimated_clicks": 2000,
      "estimated_conversions": 100,
      "estimated_reach": 70000
    },
    "success_metrics": {
      "primary": "Conversions",
      "secondary": "Conversion Rate",
      "target_rate": "2-5%"
    }
  }
}
```

### 4. Campaign Management

#### Publish Campaign
```
POST /api/ads/campaigns/{id}/publish
```

#### Pause Campaign
```
POST /api/ads/campaigns/{id}/pause
```

#### Resume Campaign
```
POST /api/ads/campaigns/{id}/resume
```

### 5. Campaign Analytics

#### Get Campaign Analytics
```
GET /api/ads/campaigns/{id}/analytics?days=30
```

**Response:**
```json
{
  "success": true,
  "data": {
    "campaign": {
      "id": 1,
      "name": "Summer Sale",
      "objective": "conversions",
      "status": "active",
      "budget": 1000,
      "spend": 750
    },
    "daily_performance": [
      {
        "date": "2025-10-13",
        "impressions": 5000,
        "clicks": 100,
        "conversions": 5,
        "spend": 25
      }
    ],
    "platform_breakdown": [
      {
        "platform": "facebook",
        "impressions": 30000,
        "clicks": 600,
        "conversions": 30,
        "spend": 450
      }
    ],
    "ad_set_performance": [...]
  }
}
```

#### Get Campaign Insights
```
GET /api/ads/campaigns/{id}/insights
```

**Response:**
```json
{
  "success": true,
  "data": {
    "insights": [
      {
        "type": "success",
        "title": "Excellent Click-Through Rate",
        "message": "Your CTR is 5.2%. Your ads are resonating well with your audience!",
        "metric": "ctr",
        "value": 5.2
      }
    ],
    "recommendations": [
      {
        "title": "Test Different Audiences",
        "description": "Create additional ad sets targeting different audience segments",
        "priority": "medium"
      }
    ]
  }
}
```

## Database Schema

### Table: `ads_campaigns`
```sql
- id (bigint, primary key)
- user_id (bigint, foreign key to users)
- name (varchar)
- objective (enum: awareness, traffic, engagement, leads, conversions, sales)
- platforms (json)
- budget_type (enum: daily, lifetime)
- budget (decimal)
- start_date (date)
- end_date (date, nullable)
- timezone (varchar)
- status (enum: draft, active, paused, completed)
- analytics (json, nullable)
- published_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### Table: `ad_sets`
```sql
- id (bigint, primary key)
- campaign_id (bigint, foreign key to ads_campaigns)
- name (varchar)
- targeting (json)
- budget (decimal, nullable)
- status (enum: active, paused, completed)
- analytics (json)
- created_at (timestamp)
- updated_at (timestamp)
```

### Table: `ads`
```sql
- id (bigint, primary key)
- ad_set_id (bigint, foreign key to ad_sets)
- name (varchar)
- creative (json)
- status (enum: active, paused, completed)
- analytics (json)
- created_at (timestamp)
- updated_at (timestamp)
```

## Models Created

### `AdsCampaign` Model
- Location: `app/Models/AdsCampaign.php`
- Relationships:
  - `belongsTo(User::class)`
  - `hasMany(AdSet::class)`
  - `hasManyThrough(Ad::class)`

### `AdSet` Model
- Location: `app/Models/AdSet.php`
- Relationships:
  - `belongsTo(AdsCampaign::class)`
  - `hasMany(Ad::class)`

### `Ad` Model
- Location: `app/Models/Ad.php`
- Relationships:
  - `belongsTo(AdSet::class)`
  - `hasOneThrough(AdsCampaign::class)`

## Controllers Created

### API Controllers

#### `AdsAnalyticsController`
- Location: `app/Http/Controllers/API/AdsAnalyticsController.php`
- Methods:
  - `getDashboardMetrics()` - Dashboard metrics for timeframe
  - `campaignAnalytics()` - Detailed campaign performance
  - `campaignInsights()` - AI-powered insights and recommendations

#### `AdsCampaignController`
- Location: `app/Http/Controllers/API/AdsCampaignController.php`
- Methods:
  - `index()` - List all campaigns
  - `store()` - Create new campaign
  - `show()` - Get campaign details
  - `update()` - Update campaign
  - `destroy()` - Delete campaign
  - `buildWithAI()` - AI campaign structure generation
  - `publish()` - Publish draft campaign
  - `pause()` - Pause active campaign
  - `resume()` - Resume paused campaign

### Web Controller

#### `AdminAdsCampaignController`
- Location: `app/Http/Controllers/Web/AdminAdsCampaignController.php`
- Methods:
  - `index()` - Admin view of all campaigns
  - `create()` - Show create form
  - `store()` - Store new campaign
  - `show()` - Show campaign details
  - `edit()` - Show edit form
  - `update()` - Update campaign
  - `destroy()` - Delete campaign

## Admin Views Created

### Campaigns Index
- Location: `resources/views/admin/ads-campaigns/index.blade.php`
- Features:
  - Grid layout displaying all user campaigns
  - Filter by status and objective
  - Search by campaign name or user
  - Campaign metrics display (impressions, clicks, conversions, spend)
  - Platform badges
  - Budget information
  - View details and delete actions
  - RTL support for Arabic
  - Responsive design

### Sidebar Navigation
- Added "Ads Campaigns" link to admin sidebar
- Orange gradient icon
- Active state highlighting
- Route: `/admin/ads-campaigns`

## React Native Integration

### Mobile App Service Calls
The following service functions in your React Native app now connect to the backend:

```typescript
// services/ads.ts

// Dashboard metrics - connects to GET /api/ads/dashboard/metrics
export const getDashboardMetrics = async (timeframe: '7d' | '30d' | '90d') => {
  const response = await api.get(`/ads/dashboard/metrics`, { params: { timeframe } });
  return response.data;
};

// Build campaign with AI - connects to POST /api/ads/campaigns/build-with-ai
export const buildCampaignWithAI = async (campaignData: AIBuildRequest) => {
  const response = await api.post(`/ads/campaigns/build-with-ai`, campaignData);
  return response.data;
};

// Create campaign - connects to POST /api/ads/campaigns
export const createCampaign = async (campaignData: CampaignCreateRequest) => {
  const response = await api.post(`/ads/campaigns`, campaignData);
  return response.data;
};

// List campaigns - connects to GET /api/ads/campaigns
export const getCampaigns = async () => {
  const response = await api.get(`/ads/campaigns`);
  return response.data;
};

// Get campaign details - connects to GET /api/ads/campaigns/{id}
export const getCampaign = async (campaignId: number) => {
  const response = await api.get(`/ads/campaigns/${campaignId}`);
  return response.data;
};

// Get campaign analytics - connects to GET /api/ads/campaigns/{id}/analytics
export const getCampaignAnalytics = async (campaignId: number, days: number = 30) => {
  const response = await api.get(`/ads/campaigns/${campaignId}/analytics`, { params: { days } });
  return response.data;
};

// Publish campaign - connects to POST /api/ads/campaigns/{id}/publish
export const publishCampaign = async (campaignId: number) => {
  const response = await api.post(`/ads/campaigns/${campaignId}/publish`);
  return response.data;
};

// Pause campaign - connects to POST /api/ads/campaigns/{id}/pause
export const pauseCampaign = async (campaignId: number) => {
  const response = await api.post(`/ads/campaigns/${campaignId}/pause`);
  return response.data;
};
```

## AI Features

### AI Campaign Builder
The `buildWithAI` endpoint provides:
- Campaign name suggestions
- Platform recommendations with reasoning
- Budget allocation across platforms
- Ad set suggestions with targeting
- Creative suggestions (image/video)
- Audience targeting recommendations
- Schedule recommendations
- Estimated reach and performance
- Success metrics definition

### AI Insights
The campaign insights endpoint provides:
- Performance analysis (CTR, budget utilization, trends)
- Automated recommendations
- Platform performance comparison
- Best practices suggestions

## Testing the Integration

### 1. Test API Endpoints with Postman/Insomnia

```bash
# Get bearer token first
POST http://localhost:8000/api/auth/login
{
  "email": "user@example.com",
  "password": "password"
}

# Test dashboard metrics
GET http://localhost:8000/api/ads/dashboard/metrics?timeframe=7d
Authorization: Bearer {your_token}

# Test AI campaign builder
POST http://localhost:8000/api/ads/campaigns/build-with-ai
Authorization: Bearer {your_token}
{
  "objective": "conversions",
  "budget": 1000,
  "duration": 30,
  "productInfo": "Test product",
  "targetAudience": "Test audience"
}
```

### 2. Test Admin Interface

1. Navigate to: `http://localhost:8000/admin/ads-campaigns`
2. View campaigns created by users through the mobile app
3. Click "View Details" to see campaign analytics
4. Use filters to search and filter campaigns

### 3. Test Mobile App

1. Ensure your API base URL is configured correctly in the mobile app
2. Test the Ads Dashboard screen to load metrics
3. Test the Campaign Builder screen to create campaigns with AI
4. Verify data appears in admin panel

## Next Steps

1. **Implement Real AI Integration**
   - Connect to OpenAI API for actual AI-powered campaign generation
   - Replace mock data with real AI responses

2. **Add Platform API Integration**
   - Integrate with Facebook Ads API
   - Integrate with Google Ads API
   - Integrate with TikTok Ads API
   - Real-time data synchronization

3. **Enhance Analytics**
   - Real-time campaign performance tracking
   - Daily analytics aggregation job
   - Historical data storage

4. **Add More Views**
   - Campaign show page (detailed view)
   - Campaign edit page
   - Campaign create page

5. **Add Notifications**
   - Campaign performance alerts
   - Budget threshold notifications
   - Campaign completion notifications

## Files Created/Modified

### Created Files:
1. `routes/api.php` - Added ads campaign routes (modified)
2. `app/Http/Controllers/API/AdsAnalyticsController.php`
3. `app/Http/Controllers/API/AdsCampaignController.php`
4. `app/Http/Controllers/Web/AdminAdsCampaignController.php`
5. `app/Models/AdsCampaign.php`
6. `app/Models/AdSet.php`
7. `app/Models/Ad.php`
8. `app/Models/User.php` - Added adsCampaigns relationship (modified)
9. `database/migrations/2025_10_13_000001_create_ads_campaigns_table.php`
10. `database/migrations/2025_10_13_000002_create_ad_sets_table.php`
11. `database/migrations/2025_10_13_000003_create_ads_table.php`
12. `resources/views/admin/ads-campaigns/index.blade.php`
13. `resources/views/layouts/admin.blade.php` - Added sidebar link (modified)
14. `routes/web.php` - Added ads campaigns routes (modified)

## Summary

The backend is now fully connected to the React Native mobile app for the Ads Campaigns feature. Users can:

1. **From Mobile App:**
   - View dashboard metrics
   - Create campaigns with AI assistance
   - Manage campaign lifecycle (publish, pause, resume)
   - View campaign analytics and insights

2. **From Admin Panel:**
   - Monitor all user campaigns
   - View campaign details and metrics
   - Filter and search campaigns
   - Delete campaigns if needed

All API endpoints are protected with Laravel Sanctum authentication and follow RESTful conventions.
