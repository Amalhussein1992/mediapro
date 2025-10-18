# Translation API Documentation

## نظام إدارة الترجمات عبر API

هذا الدليل يشرح كيفية استخدام API لإدارة الترجمات العربية والإنجليزية من التطبيق.

---

## 🌐 Endpoints العامة (بدون مصادقة)

### 1. الحصول على الترجمات حسب اللغة
**GET** `/api/translations/{locale}`

احصل على جميع الترجمات بصيغة JSON منظمة حسب المجموعات.

**Parameters:**
- `locale`: `en` أو `ar`

**Example Request:**
```bash
GET /api/translations/ar
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "common": {
      "appName": "ميديا برو",
      "welcome": "مرحباً",
      "loading": "جاري التحميل..."
    },
    "auth": {
      "login": "تسجيل الدخول",
      "register": "إنشاء حساب"
    }
  },
  "locale": "ar"
}
```

### 2. تصدير الترجمات كملف JSON
**GET** `/api/translations/export/{locale}`

قم بتنزيل ملف JSON يحتوي على جميع الترجمات.

**Parameters:**
- `locale`: `en` أو `ar`

**Example Request:**
```bash
GET /api/translations/export/ar
```

---

## 🔒 Endpoints المحمية (تتطلب مصادقة Admin/Moderator)

### 3. الحصول على جميع الترجمات (مع التفاصيل)
**GET** `/api/translations`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters (اختياري):**
- `search`: البحث في المفاتيح والقيم
- `group`: تصفية حسب المجموعة

**Example Request:**
```bash
GET /api/translations?search=login&group=auth
```

**Example Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "key": "auth.login",
      "value_en": "Login",
      "value_ar": "تسجيل الدخول",
      "group": "auth",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "groups": ["common", "auth", "dashboard"]
}
```

### 4. إنشاء ترجمة جديدة
**POST** `/api/translations`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "key": "common.newKey",
  "value_en": "New Translation",
  "value_ar": "ترجمة جديدة",
  "group": "common"
}
```

**Example Response:**
```json
{
  "success": true,
  "message": "Translation created successfully",
  "data": {
    "id": 123,
    "key": "common.newKey",
    "value_en": "New Translation",
    "value_ar": "ترجمة جديدة",
    "group": "common"
  }
}
```

### 5. تحديث ترجمة موجودة
**PUT** `/api/translations/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "key": "common.appName",
  "value_en": "Media Pro",
  "value_ar": "ميديا برو",
  "group": "common"
}
```

**Example Response:**
```json
{
  "success": true,
  "message": "Translation updated successfully",
  "data": {
    "id": 1,
    "key": "common.appName",
    "value_en": "Media Pro",
    "value_ar": "ميديا برو",
    "group": "common"
  }
}
```

### 6. حذف ترجمة
**DELETE** `/api/translations/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Example Response:**
```json
{
  "success": true,
  "message": "Translation deleted successfully"
}
```

### 7. مزامنة الترجمات من التطبيق
**POST** `/api/translations/sync`

قم برفع ملف الترجمات من التطبيق (ar.json أو en.json) وقم بمزامنته مع قاعدة البيانات.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "locale": "ar",
  "translations": {
    "common": {
      "appName": "ميديا برو",
      "welcome": "مرحباً"
    },
    "auth": {
      "login": "تسجيل الدخول"
    }
  }
}
```

**Example Response:**
```json
{
  "success": true,
  "message": "Translations synced successfully",
  "stats": {
    "created": 5,
    "updated": 120
  }
}
```

### 8. الحصول على إحصائيات الترجمات
**GET** `/api/translations/stats`

**Headers:**
```
Authorization: Bearer {token}
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "total": 1250,
    "groups_count": 25,
    "missing_arabic": 5,
    "missing_english": 2,
    "groups": {
      "common": 50,
      "auth": 30,
      "dashboard": 100
    }
  }
}
```

---

## 📱 استخدام API في React Native

### تثبيت المكتبات
```bash
npm install axios
```

### إنشاء Translation Service

```typescript
// src/services/translationSync.ts
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const API_URL = 'https://your-backend.com/api';

export const translationSyncService = {
  // الحصول على الترجمات من الخادم
  async fetchTranslations(locale: 'en' | 'ar') {
    try {
      const response = await axios.get(`${API_URL}/translations/${locale}`);
      if (response.data.success) {
        // حفظ الترجمات محلياً
        await AsyncStorage.setItem(
          `translations_${locale}`,
          JSON.stringify(response.data.data)
        );
        return response.data.data;
      }
    } catch (error) {
      console.error('Failed to fetch translations:', error);
      // استخدام الترجمات المحلية المحفوظة
      const cached = await AsyncStorage.getItem(`translations_${locale}`);
      return cached ? JSON.parse(cached) : null;
    }
  },

  // رفع الترجمات المحلية إلى الخادم (للمسؤولين فقط)
  async syncTranslations(locale: 'en' | 'ar', translations: any) {
    try {
      const token = await AsyncStorage.getItem('auth_token');
      const response = await axios.post(
        `${API_URL}/translations/sync`,
        {
          locale,
          translations
        },
        {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        }
      );
      return response.data;
    } catch (error) {
      console.error('Failed to sync translations:', error);
      throw error;
    }
  },

  // إنشاء ترجمة جديدة
  async createTranslation(data: {
    key: string;
    value_en: string;
    value_ar: string;
    group: string;
  }) {
    try {
      const token = await AsyncStorage.getItem('auth_token');
      const response = await axios.post(
        `${API_URL}/translations`,
        data,
        {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        }
      );
      return response.data;
    } catch (error) {
      console.error('Failed to create translation:', error);
      throw error;
    }
  },

  // تحديث ترجمة
  async updateTranslation(id: number, data: {
    key: string;
    value_en: string;
    value_ar: string;
    group: string;
  }) {
    try {
      const token = await AsyncStorage.getItem('auth_token');
      const response = await axios.put(
        `${API_URL}/translations/${id}`,
        data,
        {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        }
      );
      return response.data;
    } catch (error) {
      console.error('Failed to update translation:', error);
      throw error;
    }
  }
};
```

### استخدام Translation Service في التطبيق

```typescript
// في App.tsx أو في useEffect
import { translationSyncService } from './src/services/translationSync';

const App = () => {
  useEffect(() => {
    const loadTranslations = async () => {
      // الحصول على اللغة المحفوظة
      const savedLanguage = await AsyncStorage.getItem('@app_language');
      const locale = savedLanguage || 'ar';

      // الحصول على الترجمات من الخادم
      const translations = await translationSyncService.fetchTranslations(locale);

      if (translations) {
        // تحديث i18n بالترجمات الجديدة
        i18n.addResourceBundle(locale, 'translation', translations, true, true);
      }
    };

    loadTranslations();
  }, []);

  return <YourApp />;
};
```

---

## 🔄 سيناريوهات الاستخدام

### 1. تحديث الترجمات تلقائياً عند بدء التطبيق
```typescript
// عند فتح التطبيق، احصل على أحدث الترجمات من الخادم
const translations = await translationSyncService.fetchTranslations('ar');
i18n.addResourceBundle('ar', 'translation', translations, true, true);
```

### 2. إدارة الترجمات من داخل التطبيق (للمسؤولين)
```typescript
// إنشاء ترجمة جديدة
await translationSyncService.createTranslation({
  key: 'settings.newFeature',
  value_en: 'New Feature',
  value_ar: 'ميزة جديدة',
  group: 'settings'
});

// تحديث ترجمة موجودة
await translationSyncService.updateTranslation(123, {
  key: 'common.appName',
  value_en: 'Media Pro',
  value_ar: 'ميديا برو',
  group: 'common'
});
```

### 3. مزامنة ملف الترجمات المحلي مع الخادم
```typescript
import arTranslations from './src/locales/ar.json';

await translationSyncService.syncTranslations('ar', arTranslations);
```

---

## 🎯 أفضل الممارسات

1. **التخزين المؤقت (Caching)**
   - احفظ الترجمات محلياً في AsyncStorage
   - استخدم الترجمات المحفوظة إذا فشل الاتصال بالخادم

2. **التحديث التلقائي**
   - افحص الترجمات الجديدة عند بدء التطبيق
   - استخدم versioning للتحقق من الحاجة للتحديث

3. **معالجة الأخطاء**
   - استخدم try-catch في جميع طلبات API
   - عرض رسائل خطأ واضحة للمستخدم

4. **الأمان**
   - تأكد من صلاحيات المستخدم قبل السماح بالتعديل
   - استخدم HTTPS فقط
   - لا تخزن tokens في أماكن غير آمنة

---

## 🐛 استكشاف الأخطاء

### خطأ 401 (Unauthorized)
```json
{
  "message": "Unauthenticated."
}
```
**الحل:** تأكد من إرسال Bearer token صحيح في header

### خطأ 422 (Validation Error)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "key": ["The key has already been taken."]
  }
}
```
**الحل:** تحقق من البيانات المرسلة والتأكد من عدم تكرار المفاتيح

### خطأ 403 (Forbidden)
```json
{
  "message": "This action is unauthorized."
}
```
**الحل:** تأكد من أن المستخدم لديه صلاحية admin أو moderator

---

## 📞 الدعم

للمزيد من المعلومات، راجع:
- `TRANSLATION_SYSTEM_SETUP.md` - إعداد النظام
- `TRANSLATION_QUICK_START.md` - دليل البداية السريعة
- `API_DOCUMENTATION.md` - توثيق API الكامل

---

**ملاحظة:** تأكد من تشغيل migrations قبل استخدام API:
```bash
php artisan migrate
php artisan db:seed --class=TranslationSeeder
```
