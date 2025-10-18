# 🚀 دليل رفع Laravel على Plesk - خطوة بخطوة

## ⚠️ المشكلة التي واجهتها:
استخدمت خيار "Laravel" في Plesk وفشل لأن Plesk يحاول إنشاء مشروع جديد وليس رفع مشروع موجود.

---

## ✅ الحل الصحيح: استخدام File Manager

### **الطريقة 1: رفع عبر File Manager (الأسهل)**

#### الخطوة 1: تسجيل الدخول إلى Plesk
```
https://148.66.159.65:8443
```

#### الخطوة 2: الذهاب إلى File Manager
1. اضغط على **"Websites & Domains"**
2. اختر الدومين الخاص بك (مثلاً: alenwanapp.net)
3. اضغط على **"File Manager"**

#### الخطوة 3: رفع الملفات
يوجد طريقتان:

**أ. رفع ملف مضغوط (موصى به):**
1. في File Manager، انتقل إلى مجلد `httpdocs/` (أو `public_html/`)
2. احذف محتويات المجلد الافتراضية
3. اضغط **"Upload"** أو **"Add File"**
4. ارفع ملف `backend-laravel-deployment.zip`
5. بعد الرفع، انقر بالزر الأيمن على الملف → **"Extract"**
6. **مهم:** انقل محتويات المجلد المستخرج إلى `httpdocs/` مباشرة

**ب. رفع الملفات مباشرة:**
1. اضغط جميع ملفات `backend-laravel/` محلياً
2. ارفعها مباشرة إلى `httpdocs/`

---

### **الطريقة 2: استخدام FTP (للملفات الكبيرة)**

#### الخطوة 1: الحصول على معلومات FTP
في Plesk:
1. **Websites & Domains** → دومينك
2. **FTP Access**
3. ستجد:
   - FTP Server: `148.66.159.65` أو `alenwanapp.net`
   - Username: مثلاً `alenwanapp`
   - Password: كلمة مرور FTP

#### الخطوة 2: الاتصال عبر FileZilla
1. حمّل **FileZilla** من: https://filezilla-project.org
2. فتح FileZilla
3. أدخل:
   - Host: `ftp://148.66.159.65`
   - Username: `alenwanapp` (أو اسم المستخدم الخاص بك)
   - Password: كلمة مرور FTP
   - Port: `21`
4. اضغط **Quickconnect**

#### الخطوة 3: رفع الملفات
1. في الجانب الأيسر (Local): انتقل إلى مجلد `backend-laravel/`
2. في الجانب الأيمن (Remote): انتقل إلى `/httpdocs/`
3. حدد جميع الملفات من الجانب الأيسر
4. اسحبها إلى الجانب الأيمن

---

## 🔧 بعد رفع الملفات على Plesk

### **1. ضبط Document Root**

في Plesk:
1. **Websites & Domains** → دومينك
2. **Hosting Settings**
3. **Document root**: غيّره إلى:
   ```
   /httpdocs/public
   ```
   (لأن Laravel يجب أن يشير إلى مجلد `public/`)
4. **Save**

---

### **2. تثبيت Composer Dependencies**

في Plesk:
1. **Websites & Domains** → دومينك
2. **PHP Composer** (أو استخدم SSH)
3. اضغط **"Install"**
4. أو عبر SSH:
   ```bash
   cd /var/www/vhosts/alenwanapp.net/httpdocs
   composer install --no-dev --optimize-autoloader
   ```

---

### **3. إعداد ملف .env**

**عبر File Manager:**
1. في `httpdocs/`، انسخ `.env.example` إلى `.env`
2. عدّل `.env`:
   ```env
   APP_NAME="Media Pro"
   APP_ENV=production
   APP_KEY=              # سيتم توليده
   APP_DEBUG=false
   APP_URL=https://alenwanapp.net

   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=alenwana_socialmedia
   DB_USERNAME=alenwana_dbuser
   DB_PASSWORD=your_db_password
   ```

**أو عبر SSH:**
```bash
cd /var/www/vhosts/alenwanapp.net/httpdocs
cp .env.example .env
nano .env  # عدّل الإعدادات
```

---

### **4. توليد Application Key**

**عبر SSH:**
```bash
cd /var/www/vhosts/alenwanapp.net/httpdocs
php artisan key:generate
```

**أو عبر Plesk → Scheduled Tasks:**
أضف task مؤقت:
```bash
cd /var/www/vhosts/alenwanapp.net/httpdocs && php artisan key:generate
```

---

### **5. تشغيل Migrations**

```bash
php artisan migrate --force
```

---

### **6. ضبط الصلاحيات**

```bash
chmod -R 755 storage bootstrap/cache
chown -R username:psacln storage bootstrap/cache
```

استبدل `username` باسم مستخدم Plesk الخاص بك.

---

### **7. تحسين الأداء**

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## 📋 Checklist النهائي

```
☑️ 1. رفع الملفات إلى /httpdocs/
☑️ 2. ضبط Document Root إلى /httpdocs/public
☑️ 3. تثبيت Composer dependencies
☑️ 4. إنشاء وتعديل ملف .env
☑️ 5. إنشاء قاعدة البيانات في Plesk
☑️ 6. توليد APP_KEY
☑️ 7. تشغيل migrations
☑️ 8. ضبط الصلاحيات
☑️ 9. تحسين الأداء (cache)
☑️ 10. اختبار الموقع (https://alenwanapp.net)
```

---

## 🌐 إنشاء قاعدة البيانات في Plesk

1. **Websites & Domains** → **Databases**
2. **Add Database**
   - Database name: `socialmedia`
   - سيصبح: `alenwana_socialmedia`
3. **Add Database User**
   - Username: `dbuser`
   - Password: كلمة مرور قوية
   - Assign to database: `alenwana_socialmedia`
4. استخدم هذه المعلومات في `.env`

---

## 🔐 SSL Certificate

في Plesk:
1. **Websites & Domains** → **SSL/TLS Certificates**
2. اختر **"Install a free Let's Encrypt certificate"**
3. حدد الدومين
4. اضغط **"Get it free"**

---

## 🐛 استكشاف الأخطاء

### "500 Internal Server Error"
```bash
# فحص logs
tail -f /var/www/vhosts/alenwanapp.net/logs/error_log

# فحص Laravel logs
tail -f /var/www/vhosts/alenwanapp.net/httpdocs/storage/logs/laravel.log
```

### "Permission Denied"
```bash
chmod -R 755 storage bootstrap/cache
```

### "APP_KEY not set"
```bash
php artisan key:generate
```

---

## 📞 روابط مفيدة

- **Plesk**: https://148.66.159.65:8443
- **FileZilla**: https://filezilla-project.org
- **Laravel Docs**: https://laravel.com/docs

---

## 🎯 الخلاصة

**لا تستخدم خيار "Laravel" في Plesk!**

استخدم:
1. ✅ **File Manager** → Upload files
2. ✅ **FTP** → Upload via FileZilla
3. ✅ **SSH** → Git clone (إذا كان موجود على GitHub)

ثم اتبع خطوات الإعداد أعلاه.

---

**تم إنشاؤه:** 2025-10-18
**الموقع:** alenwanapp.net
**السيرفر:** 148.66.159.65
