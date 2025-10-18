# 📱 Media Pro - منصة إدارة وسائل التواصل الاجتماعي

## 🎯 نظرة عامة

Media Pro هي منصة شاملة لإدارة وسائل التواصل الاجتماعي مبنية بـ Laravel، توفر API قوي للتطبيقات المحمولة ولوحة تحكم ويب متقدمة للإدارة.

---

## ✨ المميزات الرئيسية

### 🔐 نظام المصادقة والأمان
- تسجيل دخول وإنشاء حساب آمن
- مصادقة باستخدام Laravel Sanctum
- إدارة الجلسات والتوكنات
- حماية متقدمة للبيانات

### 📝 إدارة المنشورات
- إنشاء وتعديل المنشورات
- جدولة المنشورات التلقائية
- دعم منصات متعددة (Instagram, Facebook, Twitter, LinkedIn, TikTok)
- رفع الصور والفيديوهات
- حفظ المسودات
- إحصائيات الأداء لكل منشور

### 📊 التحليلات والإحصائيات
- لوحة تحكم شاملة مع رسوم بيانية
- تحليلات الأداء في الوقت الفعلي
- معدلات التفاعل (Likes, Comments, Shares, Views)
- أفضل أوقات النشر
- توزيع المنشورات على المنصات
- تقارير مفصلة قابلة للتصدير

### 🤖 توليد المحتوى بالذكاء الاصطناعي
- توليد تعليقات احترافية للمنشورات
- اقتراح هاشتاجات مناسبة
- تحسين المحتوى الموجود
- توليد أفكار محتوى جديدة
- تنويع الأسلوب (احترافي، ودي، حماسي)
- تخصيص حسب المنصة

### 🔗 إدارة حسابات التواصل الاجتماعي
- ربط حسابات متعددة
- إدارة الصلاحيات
- مراقبة الحالة (نشط/غير نشط)
- معلومات تفصيلية لكل حساب
- إحصائيات المتابعين

### 🎨 إدارة الهوية البصرية (Brand Kits)
- إنشاء مجموعات الهوية البصرية
- تخزين الشعارات والألوان
- الخطوط المفضلة
- قوالب جاهزة
- مزامنة عبر جميع المنشورات

### 💳 نظام الاشتراكات والدفع
- خطط اشتراك متعددة (Starter, Professional, Enterprise)
- دفع آمن
- إدارة الاشتراكات
- تاريخ المدفوعات
- تجديد تلقائي

### 👥 إدارة المستخدمين
- نظام أدوار شامل
- صلاحيات مخصصة
- تعاون الفريق
- تتبع النشاطات

---

## 🏗️ البنية التقنية

### Backend (Laravel)
```
app/
├── Http/
│   └── Controllers/
│       ├── API/
│       │   ├── AuthController.php         # المصادقة
│       │   ├── PostController.php         # المنشورات
│       │   ├── AnalyticsController.php    # التحليلات
│       │   ├── AIContentController.php    # الذكاء الاصطناعي
│       │   ├── BrandKitController.php     # الهوية البصرية
│       │   └── SocialAccountController.php
│       └── Web/
│           ├── DashboardController.php
│           ├── AdminPostController.php
│           └── ...
└── Models/
    ├── User.php
    ├── Post.php
    ├── SocialAccount.php
    ├── Analytics.php
    ├── BrandKit.php
    ├── Subscription.php
    └── Payment.php
```

### Database Schema
```sql
users
├── id
├── name
├── email
├── password
├── current_subscription_plan_id
└── subscription_status

posts
├── id
├── user_id
├── content
├── media (JSON)
├── platforms (JSON)
├── status (draft/scheduled/published)
├── scheduled_at
├── published_at
└── analytics (JSON)

social_accounts
├── id
├── user_id
├── platform
├── account_name
├── access_token
├── is_active
└── followers_count

brand_kits
├── id
├── user_id
├── name
├── logo_url
├── colors (JSON)
├── fonts (JSON)
└── templates (JSON)

subscriptions
├── id
├── user_id
├── plan_id
├── status
├── start_date
└── end_date
```

---

## 🚀 API Endpoints

### المصادقة
```
POST   /api/auth/register          # إنشاء حساب
POST   /api/auth/login            # تسجيل الدخول
POST   /api/auth/logout           # تسجيل الخروج
GET    /api/auth/user             # بيانات المستخدم الحالي
```

### المنشورات
```
GET    /api/posts                 # قائمة المنشورات
POST   /api/posts                 # إنشاء منشور جديد
GET    /api/posts/{id}            # تفاصيل منشور
PUT    /api/posts/{id}            # تحديث منشور
DELETE /api/posts/{id}            # حذف منشور
```

### التحليلات
```
GET    /api/analytics/dashboard              # لوحة التحكم
GET    /api/analytics/posts/{id}             # تحليلات منشور
GET    /api/analytics/accounts/{id}          # تحليلات حساب
GET    /api/analytics/trends                 # اتجاهات التفاعل
GET    /api/analytics/best-times             # أفضل أوقات النشر
```

### الذكاء الاصطناعي
```
POST   /api/ai/generate-caption              # توليد تعليق
POST   /api/ai/generate-hashtags             # توليد هاشتاجات
POST   /api/ai/improve-content               # تحسين محتوى
POST   /api/ai/generate-ideas                # توليد أفكار
```

### الهوية البصرية
```
GET    /api/brand-kits                       # قائمة Brand Kits
POST   /api/brand-kits                       # إنشاء Brand Kit
GET    /api/brand-kits/{id}                  # تفاصيل Brand Kit
PUT    /api/brand-kits/{id}                  # تحديث Brand Kit
DELETE /api/brand-kits/{id}                  # حذف Brand Kit
```

### الاشتراكات
```
GET    /api/subscription-plans               # خطط الاشتراك
POST   /api/subscriptions/subscribe          # اشتراك جديد
GET    /api/subscriptions/current            # الاشتراك الحالي
POST   /api/subscriptions/cancel             # إلغاء اشتراك
```

**للتوثيق الكامل، راجع:** `API_DOCUMENTATION.md`

---

## 📱 دليل تكامل تطبيق الموبايل

### 1. إعداد المصادقة

```dart
// مثال Flutter
class AuthService {
  final String baseUrl = 'http://your-domain.com/api';

  Future<User> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/login'),
      body: {
        'email': email,
        'password': password,
      },
    );

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      // حفظ التوكن
      await storage.write(key: 'token', value: data['data']['token']);
      return User.fromJson(data['data']['user']);
    }
    throw Exception('Login failed');
  }
}
```

### 2. إنشاء منشور

```dart
class PostService {
  Future<Post> createPost({
    required String content,
    required List<String> platforms,
    DateTime? scheduledAt,
  }) async {
    final token = await storage.read(key: 'token');

    final response = await http.post(
      Uri.parse('$baseUrl/posts'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
      body: json.encode({
        'content': content,
        'platforms': platforms,
        'status': scheduledAt != null ? 'scheduled' : 'draft',
        'scheduled_at': scheduledAt?.toIso8601String(),
      }),
    );

    return Post.fromJson(json.decode(response.body)['data']);
  }
}
```

### 3. الحصول على التحليلات

```dart
class AnalyticsService {
  Future<DashboardData> getDashboard() async {
    final token = await storage.read(key: 'token');

    final response = await http.get(
      Uri.parse('$baseUrl/analytics/dashboard'),
      headers: {
        'Authorization': 'Bearer $token',
      },
    );

    return DashboardData.fromJson(json.decode(response.body)['data']);
  }
}
```

### 4. استخدام الذكاء الاصطناعي

```dart
class AIService {
  Future<String> generateCaption({
    required String topic,
    String tone = 'professional',
    String platform = 'instagram',
  }) async {
    final token = await storage.read(key: 'token');

    final response = await http.post(
      Uri.parse('$baseUrl/ai/generate-caption'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
      body: json.encode({
        'topic': topic,
        'tone': tone,
        'platform': platform,
      }),
    );

    return json.decode(response.body)['data']['caption'];
  }
}
```

---

## 🎨 لوحة التحكم الإدارية

### المميزات
- إحصائيات شاملة في الوقت الفعلي
- رسوم بيانية تفاعلية (Chart.js)
- إدارة المستخدمين والمنشورات
- مراقبة الأداء
- تصميم حديث ومتجاوب

### الوصول
```
http://your-domain.com/dashboard
```

### الواجهة
- **الصفحة الرئيسية**: إحصائيات عامة
- **المنشورات**: إدارة المنشورات
- **المستخدمين**: إدارة الحسابات
- **التحليلات**: تقارير مفصلة
- **الإعدادات**: إعدادات النظام

---

## 🔧 التثبيت والإعداد

### المتطلبات
- PHP 8.1+
- Composer
- MySQL/PostgreSQL
- Node.js & NPM

### خطوات التثبيت

1. **استنساخ المشروع**
```bash
git clone https://github.com/yourrepo/media-pro.git
cd media-pro/backend-laravel
```

2. **تثبيت Dependencies**
```bash
composer install
npm install
```

3. **إعداد البيئة**
```bash
cp .env.example .env
php artisan key:generate
```

4. **إعداد قاعدة البيانات**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=media_pro
DB_USERNAME=root
DB_PASSWORD=
```

5. **تشغيل Migrations**
```bash
php artisan migrate
php artisan db:seed
```

6. **تشغيل السيرفر**
```bash
php artisan serve
npm run dev
```

---

## 📦 الحزم المستخدمة

### Backend
- **Laravel 10**: إطار العمل الأساسي
- **Laravel Sanctum**: المصادقة API
- **Intervention Image**: معالجة الصور
- **Laravel Excel**: تصدير التقارير

### Frontend (Dashboard)
- **Tailwind CSS**: التصميم
- **Chart.js**: الرسوم البيانية
- **Alpine.js**: التفاعل

---

## 🔒 الأمان

- ✅ تشفير كلمات المرور
- ✅ حماية CSRF
- ✅ حماية XSS
- ✅ مصادقة API باستخدام Tokens
- ✅ Rate Limiting
- ✅ Validation شاملة
- ✅ SQL Injection Protection

---

## 📈 الأداء

- **التخزين المؤقت**: Redis/Memcached
- **قوائم الانتظار**: Laravel Queues
- **فهرسة قاعدة البيانات**: مُحسّنة
- **CDN**: لملفات الوسائط
- **API Response Time**: < 200ms

---

## 🧪 الاختبار

```bash
# تشغيل الاختبارات
php artisan test

# اختبارات محددة
php artisan test --filter=PostTest
```

---

## 📝 To-Do List المستقبلية

- [ ] دعم WebSockets للتحديثات الفورية
- [ ] تكامل مع خدمات الدفع (Stripe, PayPal)
- [ ] AI محسّن مع OpenAI
- [ ] دعم فيديوهات أطول
- [ ] محرر صور متقدم
- [ ] تقارير PDF
- [ ] إشعارات Push للموبايل
- [ ] دعم لغات إضافية

---

## 📞 الدعم والتواصل

- **Email**: support@mediapro.com
- **Documentation**: [docs.mediapro.com](docs.mediapro.com)
- **Github**: [github.com/mediapro](github.com/mediapro)

---

## 📜 الترخيص

هذا المشروع مرخص تحت MIT License.

---

## 👥 المساهمون

- المطور الرئيسي: Your Name
- فريق التطوير: Media Pro Team

---

## 🎉 شكر خاص

شكراً لاستخدامك Media Pro! نحن ملتزمون بتقديم أفضل تجربة لإدارة وسائل التواصل الاجتماعي.

---

**Built with ❤️ by Media Pro Team**
