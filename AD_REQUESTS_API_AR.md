# دليل API لطلبات الإعلانات الممولة 🎯

## نظرة عامة

يوفر هذا API للمطورين القدرة على إنشاء وإدارة طلبات الإعلانات الممولة من تطبيق الموبايل. يدعم النظام إعلانات على منصات متعددة مثل Facebook, Instagram, Twitter, LinkedIn, TikTok, Snapchat, و YouTube.

## Base URL

```
http://localhost:8000/api
```

للإنتاج:
```
https://your-domain.com/api
```

## المصادقة (Authentication)

جميع endpoints تتطلب مصادقة باستخدام Laravel Sanctum. يجب إرفاق token في header:

```http
Authorization: Bearer YOUR_ACCESS_TOKEN
```

---

## 📋 Endpoints

### 1. الحصول على قائمة طلبات الإعلانات

**Endpoint:** `GET /api/ad-requests`

**الوصف:** استرجاع جميع طلبات الإعلانات الخاصة بالمستخدم المسجل دخوله

**Headers:**
```http
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Response 200 OK:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 15,
      "campaign_name": "حملة صيف 2025",
      "campaign_description": "إعلان لمنتجات الصيف الجديدة",
      "platform": "instagram",
      "ad_type": "image",
      "objective": "sales",
      "budget": 500.00,
      "currency": "USD",
      "duration_days": 7,
      "start_date": "2025-10-25",
      "end_date": "2025-11-01",
      "targeting": {
        "age_min": 18,
        "age_max": 35,
        "gender": "all",
        "locations": ["Saudi Arabia", "UAE"],
        "interests": ["shopping", "fashion"]
      },
      "creative_assets": [
        {
          "type": "image",
          "url": "https://example.com/image.jpg"
        }
      ],
      "ad_headline": "تخفيضات الصيف",
      "ad_copy": "احصل على خصم 50٪ على جميع المنتجات",
      "call_to_action": "Shop Now",
      "destination_url": "https://example.com/summer-sale",
      "status": "pending",
      "admin_notes": null,
      "rejection_reason": null,
      "reviewed_at": null,
      "started_at": null,
      "completed_at": null,
      "performance_metrics": null,
      "created_at": "2025-10-18T10:30:00.000000Z",
      "updated_at": "2025-10-18T10:30:00.000000Z"
    }
  ]
}
```

---

### 2. إنشاء طلب إعلان جديد

**Endpoint:** `POST /api/ad-requests`

**الوصف:** إنشاء طلب إعلان ممول جديد

**Headers:**
```http
Authorization: Bearer YOUR_ACCESS_TOKEN
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "campaign_name": "حملة صيف 2025",
  "campaign_description": "إعلان لمنتجات الصيف الجديدة",
  "platform": "instagram",
  "ad_type": "image",
  "objective": "sales",
  "budget": 500,
  "currency": "USD",
  "duration_days": 7,
  "start_date": "2025-10-25",
  "end_date": "2025-11-01",
  "targeting": {
    "age_min": 18,
    "age_max": 35,
    "gender": "all",
    "locations": ["Saudi Arabia", "UAE"],
    "interests": ["shopping", "fashion"]
  },
  "creative_assets": [
    {
      "type": "image",
      "url": "https://example.com/image.jpg",
      "alt_text": "صورة المنتج"
    }
  ],
  "ad_headline": "تخفيضات الصيف",
  "ad_copy": "احصل على خصم 50٪ على جميع المنتجات",
  "call_to_action": "Shop Now",
  "destination_url": "https://example.com/summer-sale"
}
```

**الحقول المطلوبة (Required Fields):**

| الحقل | النوع | الوصف | القيم المسموحة |
|------|------|-------|----------------|
| `campaign_name` | string | اسم الحملة الإعلانية | أقصى طول 255 حرف |
| `platform` | string | المنصة الإعلانية | `facebook`, `instagram`, `twitter`, `linkedin`, `tiktok`, `snapchat`, `youtube` |
| `ad_type` | string | نوع الإعلان | `image`, `video`, `carousel`, `story`, `collection` |
| `objective` | string | هدف الإعلان | `awareness`, `traffic`, `engagement`, `leads`, `sales`, `app_promotion` |
| `budget` | number | الميزانية | رقم أكبر من أو يساوي 1 |
| `duration_days` | integer | مدة الحملة بالأيام | من 1 إلى 365 |
| `start_date` | date | تاريخ البدء | بصيغة YYYY-MM-DD، اليوم أو في المستقبل |
| `end_date` | date | تاريخ الانتهاء | بصيغة YYYY-MM-DD، بعد start_date |

**الحقول الاختيارية (Optional Fields):**

| الحقل | النوع | الوصف |
|------|------|-------|
| `campaign_description` | string | وصف الحملة |
| `currency` | string | العملة (الافتراضي: USD) |
| `targeting` | object | خيارات الاستهداف |
| `targeting.age_min` | integer | العمر الأدنى (13-100) |
| `targeting.age_max` | integer | العمر الأعلى (13-100) |
| `targeting.gender` | string | الجنس (`all`, `male`, `female`) |
| `targeting.locations` | array | قائمة المواقع المستهدفة |
| `targeting.interests` | array | قائمة الاهتمامات |
| `creative_assets` | array | ملفات الوسائط (صور/فيديو) |
| `ad_headline` | string | عنوان الإعلان |
| `ad_copy` | string | نص الإعلان |
| `call_to_action` | string | زر الدعوة لإجراء |
| `destination_url` | string | رابط الوجهة |

**Response 201 Created:**
```json
{
  "success": true,
  "message": "Ad request submitted successfully. Our team will review it shortly.",
  "data": {
    "id": 1,
    "user_id": 15,
    "campaign_name": "حملة صيف 2025",
    "platform": "instagram",
    "status": "pending",
    "budget": 500.00,
    "created_at": "2025-10-18T10:30:00.000000Z",
    "updated_at": "2025-10-18T10:30:00.000000Z"
  }
}
```

**Response 422 Validation Error:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "campaign_name": ["The campaign name field is required."],
    "budget": ["The budget must be at least 1."]
  }
}
```

---

### 3. عرض تفاصيل طلب إعلان محدد

**Endpoint:** `GET /api/ad-requests/{id}`

**الوصف:** استرجاع تفاصيل طلب إعلان معين

**Parameters:**
- `id` (integer, required): معرّف طلب الإعلان

**Headers:**
```http
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Response 200 OK:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "user_id": 15,
    "campaign_name": "حملة صيف 2025",
    "campaign_description": "إعلان لمنتجات الصيف الجديدة",
    "platform": "instagram",
    "ad_type": "image",
    "objective": "sales",
    "budget": 500.00,
    "currency": "USD",
    "duration_days": 7,
    "start_date": "2025-10-25",
    "end_date": "2025-11-01",
    "targeting": {
      "age_min": 18,
      "age_max": 35,
      "gender": "all",
      "locations": ["Saudi Arabia", "UAE"],
      "interests": ["shopping", "fashion"]
    },
    "status": "in_review",
    "admin_notes": "سيتم المراجعة خلال 24 ساعة",
    "reviewed_at": "2025-10-18T12:00:00.000000Z",
    "created_at": "2025-10-18T10:30:00.000000Z",
    "updated_at": "2025-10-18T12:00:00.000000Z"
  }
}
```

**Response 404 Not Found:**
```json
{
  "success": false,
  "message": "Ad request not found"
}
```

---

### 4. تحديث طلب إعلان

**Endpoint:** `PUT /api/ad-requests/{id}`

**الوصف:** تحديث طلب إعلان (يمكن التحديث فقط عندما يكون الحالة `pending`)

**Parameters:**
- `id` (integer, required): معرّف طلب الإعلان

**Headers:**
```http
Authorization: Bearer YOUR_ACCESS_TOKEN
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "campaign_name": "حملة صيف 2025 - محدّثة",
  "budget": 750,
  "ad_copy": "احصل على خصم 60٪ على جميع المنتجات"
}
```

**ملاحظة:** يمكن إرسال الحقول التي تريد تحديثها فقط

**Response 200 OK:**
```json
{
  "success": true,
  "message": "Ad request updated successfully",
  "data": {
    "id": 1,
    "campaign_name": "حملة صيف 2025 - محدّثة",
    "budget": 750.00,
    "updated_at": "2025-10-18T14:30:00.000000Z"
  }
}
```

**Response 403 Forbidden:**
```json
{
  "success": false,
  "message": "Cannot update ad request. It is already being reviewed or running."
}
```

---

### 5. حذف طلب إعلان

**Endpoint:** `DELETE /api/ad-requests/{id}`

**الوصف:** حذف طلب إعلان (يمكن الحذف فقط عندما يكون الحالة `pending`)

**Parameters:**
- `id` (integer, required): معرّف طلب الإعلان

**Headers:**
```http
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Response 200 OK:**
```json
{
  "success": true,
  "message": "Ad request deleted successfully"
}
```

**Response 403 Forbidden:**
```json
{
  "success": false,
  "message": "Cannot delete ad request. It is already being reviewed or running."
}
```

---

### 6. إحصائيات طلبات الإعلانات

**Endpoint:** `GET /api/ad-requests/statistics`

**الوصف:** الحصول على إحصائيات شاملة لطلبات الإعلانات الخاصة بالمستخدم

**Headers:**
```http
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Response 200 OK:**
```json
{
  "success": true,
  "data": {
    "total": 15,
    "pending": 2,
    "in_review": 3,
    "approved": 4,
    "running": 3,
    "completed": 2,
    "rejected": 1,
    "total_budget": 8500.00,
    "running_budget": 2250.00
  }
}
```

---

## 📊 حالات طلب الإعلان (Status Values)

| الحالة | الوصف |
|-------|-------|
| `pending` | قيد الانتظار - لم تتم المراجعة بعد |
| `in_review` | قيد المراجعة - يتم مراجعتها من قبل الفريق |
| `approved` | موافق عليها - تمت الموافقة وجاهزة للتشغيل |
| `running` | قيد التشغيل - الحملة نشطة حالياً |
| `paused` | متوقفة مؤقتاً |
| `completed` | مكتملة - انتهت الحملة بنجاح |
| `rejected` | مرفوضة - تم رفض الطلب |

---

## 🎯 أنواع الأهداف (Objectives)

| الهدف | الوصف |
|------|-------|
| `awareness` | زيادة الوعي بالعلامة التجارية |
| `traffic` | زيادة الزيارات للموقع |
| `engagement` | زيادة التفاعل مع المحتوى |
| `leads` | جمع العملاء المحتملين |
| `sales` | زيادة المبيعات |
| `app_promotion` | الترويج للتطبيق |

---

## 📱 المنصات المدعومة (Platforms)

- `facebook` - فيسبوك
- `instagram` - إنستجرام
- `twitter` - تويتر (X)
- `linkedin` - لينكد إن
- `tiktok` - تيك توك
- `snapchat` - سناب شات
- `youtube` - يوتيوب

---

## 🖼️ أنواع الإعلانات (Ad Types)

- `image` - إعلان بالصورة
- `video` - إعلان بالفيديو
- `carousel` - إعلان متعدد الصور
- `story` - إعلان القصص
- `collection` - إعلان المجموعة

---

## 💡 أمثلة على الاستخدام

### مثال React Native / Expo

```javascript
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';
const ACCESS_TOKEN = 'your-access-token';

// 1. إنشاء طلب إعلان جديد
const createAdRequest = async () => {
  try {
    const response = await axios.post(
      `${API_BASE_URL}/ad-requests`,
      {
        campaign_name: 'حملة صيف 2025',
        campaign_description: 'إعلان لمنتجات الصيف الجديدة',
        platform: 'instagram',
        ad_type: 'image',
        objective: 'sales',
        budget: 500,
        currency: 'USD',
        duration_days: 7,
        start_date: '2025-10-25',
        end_date: '2025-11-01',
        targeting: {
          age_min: 18,
          age_max: 35,
          gender: 'all',
          locations: ['Saudi Arabia', 'UAE'],
          interests: ['shopping', 'fashion']
        },
        ad_headline: 'تخفيضات الصيف',
        ad_copy: 'احصل على خصم 50٪ على جميع المنتجات',
        call_to_action: 'Shop Now',
        destination_url: 'https://example.com/summer-sale'
      },
      {
        headers: {
          'Authorization': `Bearer ${ACCESS_TOKEN}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        }
      }
    );

    console.log('Success:', response.data);
    return response.data;
  } catch (error) {
    console.error('Error:', error.response?.data || error.message);
    throw error;
  }
};

// 2. الحصول على جميع طلبات الإعلانات
const getAdRequests = async () => {
  try {
    const response = await axios.get(
      `${API_BASE_URL}/ad-requests`,
      {
        headers: {
          'Authorization': `Bearer ${ACCESS_TOKEN}`,
          'Accept': 'application/json'
        }
      }
    );

    return response.data.data;
  } catch (error) {
    console.error('Error:', error.response?.data || error.message);
    throw error;
  }
};

// 3. الحصول على الإحصائيات
const getStatistics = async () => {
  try {
    const response = await axios.get(
      `${API_BASE_URL}/ad-requests/statistics`,
      {
        headers: {
          'Authorization': `Bearer ${ACCESS_TOKEN}`,
          'Accept': 'application/json'
        }
      }
    );

    return response.data.data;
  } catch (error) {
    console.error('Error:', error.response?.data || error.message);
    throw error;
  }
};

// 4. تحديث طلب إعلان
const updateAdRequest = async (adRequestId, updates) => {
  try {
    const response = await axios.put(
      `${API_BASE_URL}/ad-requests/${adRequestId}`,
      updates,
      {
        headers: {
          'Authorization': `Bearer ${ACCESS_TOKEN}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        }
      }
    );

    return response.data;
  } catch (error) {
    console.error('Error:', error.response?.data || error.message);
    throw error;
  }
};

// 5. حذف طلب إعلان
const deleteAdRequest = async (adRequestId) => {
  try {
    const response = await axios.delete(
      `${API_BASE_URL}/ad-requests/${adRequestId}`,
      {
        headers: {
          'Authorization': `Bearer ${ACCESS_TOKEN}`,
          'Accept': 'application/json'
        }
      }
    );

    return response.data;
  } catch (error) {
    console.error('Error:', error.response?.data || error.message);
    throw error;
  }
};
```

### مثال Flutter

```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

class AdRequestService {
  final String baseUrl = 'http://localhost:8000/api';
  final String accessToken = 'your-access-token';

  // 1. إنشاء طلب إعلان جديد
  Future<Map<String, dynamic>> createAdRequest(Map<String, dynamic> data) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/ad-requests'),
        headers: {
          'Authorization': 'Bearer $accessToken',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode(data),
      );

      if (response.statusCode == 201) {
        return jsonDecode(response.body);
      } else {
        throw Exception('Failed to create ad request: ${response.body}');
      }
    } catch (e) {
      print('Error: $e');
      rethrow;
    }
  }

  // 2. الحصول على جميع طلبات الإعلانات
  Future<List<dynamic>> getAdRequests() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/ad-requests'),
        headers: {
          'Authorization': 'Bearer $accessToken',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data['data'];
      } else {
        throw Exception('Failed to load ad requests');
      }
    } catch (e) {
      print('Error: $e');
      rethrow;
    }
  }

  // 3. الحصول على الإحصائيات
  Future<Map<String, dynamic>> getStatistics() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/ad-requests/statistics'),
        headers: {
          'Authorization': 'Bearer $accessToken',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data['data'];
      } else {
        throw Exception('Failed to load statistics');
      }
    } catch (e) {
      print('Error: $e');
      rethrow;
    }
  }
}

// مثال على الاستخدام
void main() async {
  final service = AdRequestService();

  // إنشاء طلب إعلان
  final adData = {
    'campaign_name': 'حملة صيف 2025',
    'platform': 'instagram',
    'ad_type': 'image',
    'objective': 'sales',
    'budget': 500,
    'duration_days': 7,
    'start_date': '2025-10-25',
    'end_date': '2025-11-01',
  };

  try {
    final result = await service.createAdRequest(adData);
    print('Ad request created: $result');
  } catch (e) {
    print('Error: $e');
  }
}
```

---

## ⚠️ معالجة الأخطاء (Error Handling)

### رموز حالة HTTP الشائعة

| الرمز | المعنى | الوصف |
|------|--------|-------|
| 200 | OK | نجح الطلب |
| 201 | Created | تم إنشاء المورد بنجاح |
| 401 | Unauthorized | فشل المصادقة - token غير صحيح أو منتهي |
| 403 | Forbidden | غير مسموح - لا يمكن تنفيذ العملية |
| 404 | Not Found | المورد غير موجود |
| 422 | Validation Error | خطأ في التحقق من البيانات |
| 500 | Server Error | خطأ في الخادم |

---

## 🔐 نصائح الأمان

1. **لا تُخزّن ACCESS_TOKEN في الكود** - استخدم متغيرات البيئة أو Secure Storage
2. **استخدم HTTPS** في الإنتاج
3. **تحقق من صلاحية التواريخ** قبل الإرسال
4. **تحقق من الميزانية** - تأكد من أن المستخدم لديه الصلاحيات المطلوبة

---

## 📞 الدعم

للحصول على الدعم أو الإبلاغ عن مشكلة، يرجى الاتصال بفريق التطوير.

---

**آخر تحديث:** 18 أكتوبر 2025
**الإصدار:** 1.0.0
