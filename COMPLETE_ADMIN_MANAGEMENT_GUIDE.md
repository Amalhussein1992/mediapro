# دليل الإدارة الشامل للتطبيق - Complete Admin Management Guide
## نظام SaaS متكامل لإدارة وسائل التواصل الاجتماعي

---

## 📋 المحتويات - Table of Contents

1. [نظرة عامة](#overview)
2. [إدارة المستخدمين والصلاحيات](#users-management)
3. [إدارة الباقات والاشتراكات](#subscriptions-management)
4. [إدارة الكوبونات والخصومات](#coupons-management)
5. [إدارة المنشورات](#posts-management)
6. [إدارة الإعلانات الممولة](#ads-management)
7. [إدارة الإشعارات](#notifications-management)
8. [خدمات الذكاء الاصطناعي](#ai-services)
9. [إدارة التطبيق والإعدادات](#app-settings)
10. [التقارير والإحصائيات](#reports-analytics)

---

## <a name="overview"></a>🎯 نظرة عامة - Overview

### ما هو التطبيق؟

تطبيقك هو **منصة SaaS متكاملة** لإدارة وسائل التواصل الاجتماعي مع الميزات التالية:

✅ **إدارة كاملة من الباك اند** - لا حاجة لأي تعديلات في التطبيق
✅ **نظام باقات واشتراكات** - خدمات مدفوعة مع فترات تجريبية
✅ **كوبونات وخصومات** - نظام متقدم للعروض الترويجية
✅ **ذكاء اصطناعي** - توليد محتوى، صور، فيديوهات، وتحويل صوت إلى نص
✅ **إعلانات ممولة** - إدارة الحملات الإعلانية
✅ **إشعارات push** - تواصل مباشر مع المستخدمين
✅ **متعدد اللغات** - عربي وإنجليزي
✅ **تحليلات وتقارير** - إحصائيات شاملة عن كل شيء

### المتطلبات - Requirements

```bash
# Laravel Backend (جاري العمل على بورت 8000)
✅ PHP 8.1+
✅ Laravel 11.x
✅ SQLite Database (للتطوير)
✅ Expo Push Notifications API

# React Native Frontend (جاري العمل على بورت 19000)
✅ React Native / Expo 54
✅ TypeScript
✅ Expo Notifications
```

### البنية الأساسية - Base URL

```
Backend API: http://localhost:8000/api
Admin API: http://localhost:8000/api/admin/v2
```

### المصادقة - Authentication

جميع الـ Admin endpoints تتطلب:
```http
Authorization: Bearer {admin_token}
```

---

## <a name="users-management"></a>👥 إدارة المستخدمين والصلاحيات

### 1. الأدوار والصلاحيات - Roles & Permissions

الأدوار المتاحة:
- **admin**: إدارة كاملة للنظام
- **moderator**: مراقبة المحتوى
- **user**: مستخدم عادي (مشترك)

### 2. عرض جميع المستخدمين

```http
GET /api/admin/v2/users?per_page=20&role=user&status=active
```

**Query Parameters:**
- `per_page`: عدد النتائج
- `role`: فلتر حسب الدور (admin, moderator, user)
- `status`: فلتر حسب الحالة (active, inactive)

### 3. البحث عن مستخدم

```http
GET /api/admin/v2/users/search?query=john@example.com
```

### 4. تعديل مستخدم

```http
PUT /api/admin/v2/users/{user_id}
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "role": "moderator",
  "is_active": true
}
```

### 5. حذف مستخدم

```http
DELETE /api/admin/v2/users/{user_id}
```

### 6. عمليات جماعية على المستخدمين

```http
POST /api/admin/v2/users/bulk
Content-Type: application/json

{
  "user_ids": [1, 2, 3, 5],
  "action": "deactivate",  // activate, deactivate, delete, change_role
  "role": "moderator"  // مطلوب فقط عند action=change_role
}
```

### 7. إنشاء مستخدم جديد

```http
POST /api/admin/v2/users
Content-Type: application/json

{
  "name": "New User",
  "email": "user@example.com",
  "password": "password123",
  "role": "user"
}
```

---

## <a name="subscriptions-management"></a>💳 إدارة الباقات والاشتراكات

### 1. عرض جميع الباقات

```http
GET /api/subscription-plans
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Basic Plan",
      "price": "9.99",
      "duration_days": 30,
      "features": {
        "posts_per_month": 50,
        "social_accounts": 3,
        "ai_content_generation": true
      }
    }
  ]
}
```

### 2. إنشاء باقة جديدة

```http
POST /api/admin/v2/subscription-plans
Content-Type: application/json

{
  "name": "Premium Plan",
  "name_ar": "الباقة المميزة",
  "description": "Best value for professionals",
  "description_ar": "أفضل قيمة للمحترفين",
  "price": 29.99,
  "currency": "USD",
  "duration_days": 30,
  "features": {
    "posts_per_month": 200,
    "social_accounts": 10,
    "ai_content_generation": true,
    "ai_image_generation": true,
    "ai_video_generation": true,
    "analytics": true,
    "team_members": 5
  },
  "is_active": true,
  "trial_days": 7
}
```

### 3. تعديل باقة

```http
PUT /api/admin/v2/subscription-plans/{plan_id}
Content-Type: application/json

{
  "price": 24.99,
  "features": {
    "posts_per_month": 300
  }
}
```

### 4. حذف باقة

```http
DELETE /api/admin/v2/subscription-plans/{plan_id}
```

### 5. عرض اشتراكات المستخدمين

```http
GET /api/admin/v2/subscriptions?status=active&per_page=20
```

**Query Parameters:**
- `status`: active, expired, cancelled
- `user_id`: فلتر حسب المستخدم
- `plan_id`: فلتر حسب الباقة

### 6. إحصائيات الاشتراكات

```http
GET /api/admin/v2/subscriptions/stats
```

**Response:**
```json
{
  "success": true,
  "data": {
    "overview": {
      "active": 85,
      "expiring_soon": 12,
      "expired": 34,
      "cancelled": 8,
      "revenue_this_month": 2550.00
    },
    "recent_subscriptions": [...],
    "expiring_soon": [...]
  }
}
```

### 7. إلغاء اشتراك

```http
POST /api/admin/v2/subscriptions/{subscription_id}/cancel
Content-Type: application/json

{
  "reason": "User requested cancellation"
}
```

### 8. تمديد اشتراك

```http
POST /api/admin/v2/subscriptions/{subscription_id}/extend
Content-Type: application/json

{
  "days": 30,
  "reason": "Compensation for service downtime"
}
```

---

## <a name="coupons-management"></a>🎁 إدارة الكوبونات والخصومات

### 1. عرض جميع الكوبونات

```http
GET /api/admin/v2/coupons?is_active=true
```

### 2. إنشاء كوبون جديد

```http
POST /api/admin/v2/coupons
Content-Type: application/json

{
  "code": "SUMMER2024",
  "type": "percentage",  // percentage أو fixed_amount
  "value": 50,  // 50% خصم أو $50 خصم ثابت
  "description": "خصم الصيف - 50% على جميع الباقات",
  "max_uses": 100,  // null = غير محدود
  "max_uses_per_user": 1,
  "applicable_plans": [1, 2, 3],  // null = جميع الباقات
  "min_purchase_amount": 10.00,  // الحد الأدنى للشراء
  "valid_from": "2024-06-01 00:00:00",
  "valid_until": "2024-08-31 23:59:59",
  "is_active": true
}
```

### 3. أمثلة كوبونات

**خصم 50% على جميع الباقات:**
```json
{
  "code": "WELCOME50",
  "type": "percentage",
  "value": 50,
  "description": "50% off for new users",
  "max_uses": null,
  "max_uses_per_user": 1,
  "applicable_plans": null,
  "valid_from": null,
  "valid_until": null
}
```

**خصم $10 على باقات معينة:**
```json
{
  "code": "SAVE10",
  "type": "fixed_amount",
  "value": 10,
  "description": "$10 off on Premium plans",
  "max_uses": 50,
  "max_uses_per_user": 1,
  "applicable_plans": [2, 3],
  "min_purchase_amount": 20.00,
  "valid_until": "2024-12-31 23:59:59"
}
```

### 4. تعديل كوبون

```http
PUT /api/admin/v2/coupons/{coupon_id}
Content-Type: application/json

{
  "max_uses": 200,
  "is_active": true
}
```

### 5. حذف كوبون

```http
DELETE /api/admin/v2/coupons/{coupon_id}
```

### 6. تعطيل/تفعيل كوبون

```http
PATCH /api/admin/v2/coupons/{coupon_id}/toggle
```

### 7. إحصائيات الكوبونات

```http
GET /api/admin/v2/coupons/{coupon_id}/stats
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_uses": 45,
    "total_discount_given": 450.50,
    "unique_users": 45,
    "remaining_uses": 55
  }
}
```

---

## <a name="posts-management"></a>📝 إدارة المنشورات

### 1. عرض جميع المنشورات

```http
GET /api/admin/v2/posts?status=published&per_page=20
```

**Query Parameters:**
- `status`: draft, scheduled, published, failed
- `user_id`: فلتر حسب المستخدم
- `moderation_status`: pending, approved, rejected
- `platform`: facebook, instagram, twitter, etc.

### 2. طابور المراجعة (Moderation Queue)

```http
GET /api/admin/v2/moderation/queue?status=pending
```

### 3. مراجعة منشور (موافقة/رفض)

```http
POST /api/admin/v2/posts/{post_id}/moderate
Content-Type: application/json

{
  "action": "approve",  // approve, reject, flag
  "note": "Content approved",
  "flag_reasons": ["spam", "inappropriate"]  // عند action=flag
}
```

### 4. عمليات جماعية على المنشورات

```http
POST /api/admin/v2/posts/bulk
Content-Type: application/json

{
  "post_ids": [1, 2, 3, 5],
  "action": "approve"  // approve, reject, delete, publish, unpublish
}
```

### 5. حذف منشور

```http
DELETE /api/admin/v2/posts/{post_id}
```

### 6. عرض المنشورات المجدولة

```http
GET /api/admin/v2/posts/scheduled?start_date=2024-01-01&end_date=2024-12-31
```

---

## <a name="ads-management"></a>📢 إدارة الإعلانات الممولة

### 1. عرض جميع الحملات الإعلانية

```http
GET /api/admin/v2/ads/campaigns?status=active
```

### 2. عرض تفاصيل حملة

```http
GET /api/admin/v2/ads/campaigns/{campaign_id}
```

### 3. الموافقة على حملة إعلانية

```http
POST /api/admin/v2/ads/campaigns/{campaign_id}/approve
Content-Type: application/json

{
  "note": "Campaign approved for publishing"
}
```

### 4. رفض حملة إعلانية

```http
POST /api/admin/v2/ads/campaigns/{campaign_id}/reject
Content-Type: application/json

{
  "reason": "Content violates advertising policies",
  "note": "Please review our advertising guidelines"
}
```

### 5. إيقاف حملة

```http
POST /api/admin/v2/ads/campaigns/{campaign_id}/pause
```

### 6. إحصائيات الحملات

```http
GET /api/admin/v2/ads/campaigns/{campaign_id}/analytics
```

---

## <a name="notifications-management"></a>🔔 إدارة الإشعارات

للمزيد من التفاصيل، راجع: `ADMIN_NOTIFICATIONS_GUIDE.md`

### 1. إرسال إشعار لمستخدم واحد

```http
POST /api/admin/v2/notifications/send-to-user
Content-Type: application/json

{
  "user_id": 5,
  "title": "مرحباً بك!",
  "message": "نشكرك على الاشتراك في الباقة المميزة",
  "type": "welcome",
  "priority": "normal"
}
```

### 2. إرسال إشعار جماعي

```http
POST /api/admin/v2/notifications/send-to-all
Content-Type: application/json

{
  "title": "تحديث جديد!",
  "message": "إصدار جديد من التطبيق متاح الآن",
  "type": "update",
  "priority": "high",
  "only_active": true
}
```

### 3. القوالب الجاهزة

```http
GET /api/admin/v2/notifications/templates
```

---

## <a name="ai-services"></a>🤖 خدمات الذكاء الاصطناعي

### نظرة عامة على خدمات AI

التطبيق يدعم الخدمات التالية:

1. **توليد المحتوى النصي** - OpenAI, Gemini, Claude
2. **توليد الصور** - DALL-E, Stable Diffusion
3. **توليد الفيديوهات** - AI Video Generation
4. **تحويل الصوت إلى نص** - Whisper API
5. **جلب الهاشتاجات** - Hashtag Suggestions
6. **تحليل المنافسين** - Competitor Analysis

### 1. توليد محتوى نصي

```http
POST /api/ai/content/generate
Content-Type: application/json
Authorization: Bearer {user_token}

{
  "topic": "Tips for social media marketing",
  "tone": "professional",  // casual, professional, friendly, formal
  "length": "medium",  // short, medium, long
  "platform": "instagram",
  "ai_provider": "openai"  // openai, gemini, claude
}
```

### 2. توليد صورة

```http
POST /api/ai/media/generate-image
Content-Type: application/json

{
  "prompt": "A beautiful sunset over the ocean",
  "style": "realistic",  // realistic, cartoon, artistic
  "size": "1024x1024",
  "provider": "dalle"  // dalle, stable-diffusion
}
```

### 3. توليد فيديو

```http
POST /api/ai/media/generate-video
Content-Type: application/json

{
  "script": "Welcome to our social media platform...",
  "duration": 30,
  "style": "modern"
}
```

### 4. تحويل صوت إلى نص

```http
POST /api/ai/transcribe
Content-Type: multipart/form-data

audio_file: (binary)
language: "ar"  // ar, en
```

### 5. جلب هاشتاجات

```http
POST /api/ai/hashtags
Content-Type: application/json

{
  "topic": "fitness motivation",
  "platform": "instagram",
  "count": 30
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "hashtags": [
      "#fitness",
      "#motivation",
      "#workout",
      "#fitnessmotivation",
      ...
    ]
  }
}
```

### 6. إعدادات AI من الأدمن

يمكنك التحكم في خدمات AI من إعدادات التطبيق:

```http
PUT /api/admin/v2/settings/bulk
Content-Type: application/json

{
  "settings": [
    {
      "key": "ai.openai.api_key",
      "value": "sk-xxxxxxxxxxxx",
      "group": "ai"
    },
    {
      "key": "ai.openai.model",
      "value": "gpt-4",
      "group": "ai"
    },
    {
      "key": "ai.enabled_services",
      "value": ["content_generation", "image_generation", "transcription"],
      "group": "ai"
    }
  ]
}
```

---

## <a name="app-settings"></a>⚙️ إدارة التطبيق والإعدادات

### 1. عرض جميع الإعدادات

```http
GET /api/admin/v2/settings
```

### 2. عرض إعدادات حسب المجموعة

```http
GET /api/admin/v2/settings/group/theme
```

المجموعات المتاحة:
- `theme`: ألوان وشكل التطبيق
- `features`: تفعيل/تعطيل الميزات
- `ai`: إعدادات الذكاء الاصطناعي
- `payment`: إعدادات الدفع
- `email`: إعدادات البريد الإلكتروني

### 3. تحديث إعدادات التطبيق

```http
PUT /api/admin/v2/settings/bulk
Content-Type: application/json

{
  "settings": [
    {
      "key": "theme.primary_color",
      "value": "#FF5733",
      "group": "theme"
    },
    {
      "key": "theme.app_name",
      "value": "My Social App",
      "group": "theme"
    },
    {
      "key": "features.ai_enabled",
      "value": true,
      "group": "features"
    }
  ]
}
```

### 4. إعدادات الثيم (Theme Settings)

```http
PUT /api/admin/v2/settings/bulk
Content-Type: application/json

{
  "settings": [
    {"key": "theme.primary_color", "value": "#6366f1", "group": "theme"},
    {"key": "theme.secondary_color", "value": "#8b5cf6", "group": "theme"},
    {"key": "theme.app_name", "value": "Social Media Manager", "group": "theme"},
    {"key": "theme.app_logo_url", "value": "https://...", "group": "theme"}
  ]
}
```

### 5. تفعيل/تعطيل الميزات

```http
PUT /api/admin/v2/settings/bulk
Content-Type: application/json

{
  "settings": [
    {"key": "features.ai_content_generation", "value": true, "group": "features"},
    {"key": "features.ai_image_generation", "value": true, "group": "features"},
    {"key": "features.analytics", "value": true, "group": "features"},
    {"key": "features.ads_campaigns", "value": true, "group": "features"}
  ]
}
```

---

## <a name="reports-analytics"></a>📊 التقارير والإحصائيات

### 1. لوحة التحكم الرئيسية

```http
GET /api/admin/v2/dashboard
```

**Response:**
```json
{
  "success": true,
  "data": {
    "users": {
      "total": 1250,
      "active": 890,
      "newToday": 15,
      "newThisWeek": 67
    },
    "posts": {
      "total": 5480,
      "published": 4230,
      "scheduled": 450,
      "draft": 800
    },
    "subscriptions": {
      "active": 340,
      "expiring_soon": 23,
      "revenue_this_month": 8550.00
    },
    "apiUsage": {
      "today": 1234,
      "avgResponseTime": 145
    }
  }
}
```

### 2. سجلات المراجعة (Audit Logs)

```http
GET /api/admin/v2/audit-logs?action=user_login&per_page=50
```

**Query Parameters:**
- `action`: نوع الحدث
- `user_id`: المستخدم
- `model_type`: نوع الكائن (User, Post, Subscription)

### 3. تحليلات API

```http
GET /api/admin/v2/api-usage?start_date=2024-01-01&end_date=2024-12-31
```

### 4. توليد تقرير شامل

```http
POST /api/admin/v2/reports/generate
Content-Type: application/json

{
  "type": "overview",  // overview, users, posts, analytics
  "start_date": "2024-01-01",
  "end_date": "2024-12-31"
}
```

---

## 🚀 البدء السريع - Quick Start

### 1. تسجيل دخول كأدمن

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

### 2. إنشاء باقة

```bash
curl -X POST http://localhost:8000/api/admin/v2/subscription-plans \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Basic Plan",
    "price": 9.99,
    "duration_days": 30,
    "features": {"posts_per_month": 50}
  }'
```

### 3. إنشاء كوبون

```bash
curl -X POST http://localhost:8000/api/admin/v2/coupons \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "WELCOME50",
    "type": "percentage",
    "value": 50
  }'
```

### 4. إرسال إشعار

```bash
curl -X POST http://localhost:8000/api/admin/v2/notifications/send-to-all \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "مرحباً!",
    "message": "شكراً لاستخدام تطبيقنا"
  }'
```

---

## 📚 ملفات إضافية للمراجعة

1. **ADMIN_NOTIFICATIONS_GUIDE.md** - دليل الإشعارات التفصيلي
2. **DEPLOYMENT_AND_ADMIN_GUIDE.md** - دليل النشر والإدارة

---

## 🔐 الأمان - Security

### أفضل الممارسات:

1. **لا تشارك API Keys أبداً**
2. **استخدم HTTPS في الإنتاج**
3. **قم بتحديث التوكنات بانتظام**
4. **راقب سجلات المراجعة**
5. **استخدم كلمات مرور قوية**

---

## 📞 الدعم - Support

إذا واجهت أي مشكلة:
1. تحقق من `storage/logs/laravel.log`
2. راجع وثائق Laravel
3. راجع وثائق Expo

---

## ✅ الخلاصة - Summary

تطبيقك جاهز بالكامل مع:

✅ **إدارة المستخدمين** - أدوار وصلاحيات متقدمة
✅ **نظام الباقات** - باقات مرنة مع فترة تجريبية
✅ **الكوبونات** - نظام خصومات متقدم
✅ **إدارة المنشورات** - جدولة ومراجعة
✅ **الإعلانات** - موافقة وإدارة الحملات
✅ **الإشعارات** - تواصل مباشر مع المستخدمين
✅ **الذكاء الاصطناعي** - كل الأدوات المطلوبة
✅ **التقارير** - إحصائيات شاملة

**كل شيء قابل للإدارة من الباك اند دون الحاجة لتعديل التطبيق!**
