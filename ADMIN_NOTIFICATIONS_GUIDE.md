# دليل إدارة الإشعارات - Admin Notifications Management Guide

## نظرة عامة - Overview

تم تفعيل نظام الإشعارات بالكامل الذي يسمح للأدمن بإرسال إشعارات push للمستخدمين مباشرة من الباك اند.

The notification system is fully enabled allowing admins to send push notifications to users directly from the backend.

---

## API Endpoints للأدمن - Admin API Endpoints

جميع الـ endpoints التالية تتطلب صلاحيات أدمن (`role:admin`)

All the following endpoints require admin role (`role:admin`)

**Base URL**: `http://localhost:8000/api/admin/v2/notifications`

### 1️⃣ إحصائيات الإشعارات - Get Notification Statistics

```http
GET /api/admin/v2/notifications/stats
Authorization: Bearer {admin_token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_notifications": 150,
    "unread_notifications": 45,
    "notifications_today": 12,
    "notifications_this_week": 67,
    "users_with_push_tokens": 85,
    "total_users": 100,
    "recent_notifications": [...],
    "notifications_by_type": [
      {"type": "welcome", "count": 20},
      {"type": "promotion", "count": 15}
    ]
  }
}
```

---

### 2️⃣ عرض جميع الإشعارات - Get All Notifications

```http
GET /api/admin/v2/notifications/all?per_page=20&type=welcome&user_id=5
Authorization: Bearer {admin_token}
```

**Query Parameters:**
- `per_page` (optional): عدد النتائج لكل صفحة
- `type` (optional): فلتر حسب نوع الإشعار
- `user_id` (optional): فلتر حسب المستخدم

---

### 3️⃣ القوالب الجاهزة - Get Notification Templates

```http
GET /api/admin/v2/notifications/templates
Authorization: Bearer {admin_token}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "welcome",
      "name": "رسالة ترحيب - Welcome Message",
      "title": "مرحباً بك! - Welcome!",
      "message": "نحن سعداء بانضمامك إلينا!",
      "type": "welcome",
      "priority": "normal"
    },
    {
      "id": "update",
      "name": "تحديث التطبيق - App Update",
      "title": "تحديث جديد متاح - New Update Available",
      "message": "إصدار جديد من التطبيق متاح الآن!",
      "type": "update",
      "priority": "high"
    }
  ]
}
```

**القوالب المتوفرة - Available Templates:**
- ✅ **welcome** - رسالة ترحيب
- 🔄 **update** - تحديث التطبيق
- 🔧 **maintenance** - صيانة مجدولة
- 🎁 **promotion** - عرض ترويجي
- 🔔 **reminder** - تذكير

---

### 4️⃣ إرسال إشعار لمستخدم واحد - Send to Single User

```http
POST /api/admin/v2/notifications/send-to-user
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "user_id": 5,
  "title": "مرحباً بك!",
  "message": "نحن سعداء بانضمامك إلى التطبيق",
  "type": "welcome",
  "priority": "normal",
  "action_url": "/profile",
  "data": {
    "custom_field": "value"
  }
}
```

**Parameters:**
- `user_id` (required): رقم المستخدم
- `title` (required): عنوان الإشعار
- `message` (required): نص الإشعار
- `type` (optional): نوع الإشعار (default: admin_message)
- `priority` (optional): low, normal, high, urgent (default: normal)
- `action_url` (optional): رابط عند الضغط على الإشعار
- `data` (optional): بيانات إضافية

**Response:**
```json
{
  "success": true,
  "message": "Notification sent successfully"
}
```

---

### 5️⃣ إرسال إشعار لعدة مستخدمين - Send to Multiple Users

```http
POST /api/admin/v2/notifications/send-to-multiple
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "user_ids": [1, 2, 3, 5, 8],
  "title": "عرض خاص!",
  "message": "لا تفوت العرض الخاص لفترة محدودة",
  "type": "promotion",
  "priority": "high"
}
```

**Parameters:**
- `user_ids` (required): مصفوفة أرقام المستخدمين
- باقي الحقول نفسها كالإرسال لمستخدم واحد

**Response:**
```json
{
  "success": true,
  "message": "Sent to 4 users, failed for 1 users",
  "sent_count": 4,
  "failed_count": 1
}
```

---

### 6️⃣ إرسال إشعار جماعي لكل المستخدمين - Broadcast to All Users

```http
POST /api/admin/v2/notifications/send-to-all
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "title": "تحديث جديد متاح",
  "message": "إصدار جديد من التطبيق متاح الآن! يرجى التحديث للحصول على أحدث الميزات",
  "type": "update",
  "priority": "high",
  "only_active": true
}
```

**Parameters:**
- `only_active` (optional): إذا كان `true` سيرسل فقط للمستخدمين النشطين (آخر تسجيل دخول خلال 30 يوم)

**Response:**
```json
{
  "success": true,
  "message": "Broadcast sent to 82 users, failed for 3 users",
  "total_users": 85,
  "sent_count": 82,
  "failed_count": 3
}
```

---

### 7️⃣ حذف إشعار - Delete Notification

```http
DELETE /api/admin/v2/notifications/{notification_id}
Authorization: Bearer {admin_token}
```

**Response:**
```json
{
  "success": true,
  "message": "Notification deleted successfully"
}
```

---

## أمثلة استخدام - Usage Examples

### مثال 1: إرسال رسالة ترحيب لمستخدم جديد

```bash
curl -X POST http://localhost:8000/api/admin/v2/notifications/send-to-user \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 10,
    "title": "مرحباً بك في تطبيقنا!",
    "message": "نحن سعداء بانضمامك. ابدأ رحلتك الآن!",
    "type": "welcome",
    "priority": "normal"
  }'
```

### مثال 2: إرسال إشعار صيانة لجميع المستخدمين

```bash
curl -X POST http://localhost:8000/api/admin/v2/notifications/send-to-all \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "صيانة مجدولة",
    "message": "سيكون التطبيق تحت الصيانة من الساعة 2 صباحاً حتى 4 صباحاً",
    "type": "maintenance",
    "priority": "urgent",
    "only_active": true
  }'
```

### مثال 3: إرسال عرض ترويجي لمجموعة مستخدمين

```bash
curl -X POST http://localhost:8000/api/admin/v2/notifications/send-to-multiple \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "user_ids": [1, 2, 5, 8, 12],
    "title": "عرض خاص لك!",
    "message": "خصم 50% لفترة محدودة على جميع الباقات",
    "type": "promotion",
    "priority": "high",
    "action_url": "/subscriptions"
  }'
```

---

## كيفية الحصول على Admin Token

### الطريقة 1: تسجيل دخول كأدمن

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "your_password"
  }'
```

### الطريقة 2: إنشاء أدمن من قاعدة البيانات

```bash
cd backend-laravel
php artisan tinker

# في Tinker:
$user = User::create([
    'name' => 'Admin',
    'email' => 'admin@socialapp.com',
    'password' => bcrypt('admin123'),
    'role' => 'admin'
]);
```

---

## أنواع الإشعارات - Notification Types

| Type | الاسم بالعربي | الاستخدام |
|------|--------------|-----------|
| `welcome` | رسالة ترحيب | عند تسجيل مستخدم جديد |
| `update` | تحديث | إصدار جديد من التطبيق |
| `maintenance` | صيانة | إشعار بصيانة مجدولة |
| `promotion` | عرض ترويجي | عروض وخصومات |
| `reminder` | تذكير | تذكير بمهام أو أحداث |
| `admin_message` | رسالة من الإدارة | رسائل عامة |
| `broadcast` | إذاعة | رسائل جماعية |

---

## مستويات الأولوية - Priority Levels

| Priority | الوصف | الاستخدام |
|----------|-------|----------|
| `low` | منخفض | إشعارات غير عاجلة |
| `normal` | عادي | إشعارات عامة (default) |
| `high` | عالي | إشعارات مهمة |
| `urgent` | عاجل | إشعارات حرجة تتطلب انتباه فوري |

---

## ملاحظات مهمة - Important Notes

1. **Push Tokens**: المستخدمون يجب أن يكون لديهم push token مسجل في قاعدة البيانات
2. **الصلاحيات**: جميع endpoints تتطلب صلاحيات admin
3. **الإحصائيات**: يمكنك متابعة عدد الإشعارات المرسلة والمقروءة
4. **التوطين**: يمكنك إرسال الإشعارات بالعربية أو الإنجليزية
5. **Custom Data**: يمكنك إرسال بيانات إضافية مع الإشعار في حقل `data`

---

## الملفات المرتبطة - Related Files

- **Controller**: `backend-laravel/app/Http/Controllers/API/AdminNotificationController.php`
- **Service**: `backend-laravel/app/Services/PushNotificationService.php`
- **Model**: `backend-laravel/app/Models/Notification.php`
- **Routes**: `backend-laravel/routes/api.php` (line 228-237)
- **Frontend Service**: `SocialMediaManager/src/services/notificationService.ts`

---

## الدعم والمساعدة - Support

إذا واجهت أي مشكلة:
1. تحقق من أن المستخدم لديه push token مسجل
2. تحقق من صلاحيات الأدمن
3. راجع الـ logs في Laravel: `storage/logs/laravel.log`
4. تحقق من أن الباك اند يعمل على `http://localhost:8000`

---

## أمثلة متقدمة - Advanced Examples

### إرسال إشعار مع بيانات مخصصة

```json
{
  "user_id": 5,
  "title": "منشور جديد",
  "message": "لديك منشور جديد جاهز للنشر",
  "type": "reminder",
  "priority": "normal",
  "action_url": "/posts/123",
  "data": {
    "post_id": 123,
    "post_type": "instagram",
    "scheduled_time": "2025-10-18 10:00:00"
  }
}
```

### إحصائيات متقدمة

```javascript
// في Frontend يمكنك استخدام:
import api from './services/api';

const stats = await api.get('/admin/v2/notifications/stats');
console.log('عدد المستخدمين الذين لديهم push tokens:', stats.data.users_with_push_tokens);
console.log('الإشعارات اليوم:', stats.data.notifications_today);
```

---

## ✅ النظام جاهز للاستخدام

نظام الإشعارات الآن مفعل بالكامل ويمكنك:
- ✅ إرسال إشعارات لمستخدم واحد
- ✅ إرسال إشعارات لعدة مستخدمين
- ✅ إرسال إشعارات جماعية لجميع المستخدمين
- ✅ استخدام قوالب جاهزة
- ✅ متابعة الإحصائيات
- ✅ إدارة الإشعارات من الباك اند بالكامل

**لا تحتاج لأي تعديلات في الفرونت اند - كل شيء يدار من الباك اند!**
