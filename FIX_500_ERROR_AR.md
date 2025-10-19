# حل مشكلة 500 Server Error - خطوة بخطوة 🔧

## المشكلة الحالية
الموقع يعرض خطأ 500 على: https://mediapro.social

## الأسباب المحتملة والحلول

### 1️⃣ إعدادات Document Root في Plesk

**المشكلة:** Plesk يجب أن يشير إلى مجلد `public` وليس المجلد الرئيسي

**الحل:**
1. افتح Plesk Panel
2. اذهب إلى **Hosting Settings** للدومين `mediapro.social`
3. ابحث عن **Document Root**
4. غيّره إلى: `httpdocs/public` بدلاً من `httpdocs`
5. احفظ التغييرات

---

### 2️⃣ ملف .env غير موجود أو غير صحيح

**الحل:**
1. افتح **File Manager** في Plesk
2. اذهب إلى مجلد `httpdocs`
3. تأكد من وجود ملف `.env`
4. إذا لم يكن موجود، قم بتحميل الملف `.env.server` وأعد تسميته إلى `.env`

---

### 3️⃣ صلاحيات المجلدات

**المشكلة:** Laravel يحتاج صلاحيات كتابة على مجلدات معينة

**الحل عبر SSH:**
```bash
cd /var/www/vhosts/mediapro.social/httpdocs
chmod -R 775 storage bootstrap/cache
chown -R username:psacln storage bootstrap/cache
```

**أو عبر Plesk File Manager:**
1. افتح File Manager
2. اضغط بزر الماوس الأيمن على مجلد `storage`
3. اختر **Change Permissions**
4. حدد: Read, Write, Execute للمالك والمجموعة
5. كرر نفس العملية لمجلد `bootstrap/cache`

---

### 4️⃣ تشغيل أوامر Laravel

**عبر SSH في Plesk:**
```bash
cd /var/www/vhosts/mediapro.social/httpdocs
composer install --no-dev --optimize-autoloader
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### 5️⃣ فحص ملف .htaccess

**تأكد من وجود الملف:** `public/.htaccess`

**المحتوى الصحيح:**
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

### 6️⃣ فحص قاعدة البيانات

**تأكد من:**
1. قاعدة البيانات موجودة وتعمل
2. بيانات الاتصال في `.env` صحيحة:
   ```
   DB_HOST=localhost
   DB_DATABASE=socialmedia_manager
   DB_USERNAME=admin_mediapro
   DB_PASSWORD=d4hmy~5ZZM!lgcm6
   ```
3. تشغيل Migration:
   ```bash
   php artisan migrate --force
   ```

---

### 7️⃣ فحص ملفات Log

**لمعرفة السبب الدقيق للخطأ:**

1. افتح File Manager في Plesk
2. اذهب إلى: `storage/logs/`
3. افتح أحدث ملف `laravel.log`
4. ابحث عن آخر خطأ (ERROR)

**أو عبر SSH:**
```bash
tail -n 50 /var/www/vhosts/mediapro.social/httpdocs/storage/logs/laravel.log
```

---

## الخطوات السريعة للحل ⚡

### الطريقة الأسرع:

1. **عبر Plesk Panel:**
   - اذهب إلى **Domains** → **mediapro.social** → **Hosting Settings**
   - Document Root: `httpdocs/public`
   - احفظ

2. **عبر SSH Terminal في Plesk:**
   ```bash
   cd /var/www/vhosts/mediapro.social/httpdocs
   chmod -R 775 storage bootstrap/cache
   php artisan config:clear
   php artisan cache:clear
   php artisan config:cache
   ```

3. **افتح الموقع:**
   https://mediapro.social

---

## اختبار سريع

**اختبار أن Laravel يعمل:**
```
https://mediapro.social/api/config
```

**يجب أن يعرض:**
```json
{
  "app_name": "Media Pro",
  "version": "1.0.0",
  ...
}
```

---

## إذا استمرت المشكلة

1. افحص ملف Log: `storage/logs/laravel.log`
2. تأكد من إصدار PHP (يجب أن يكون 8.2 أو أحدث)
3. تأكد من تفعيل Extensions المطلوبة:
   - OpenSSL
   - PDO
   - Mbstring
   - Tokenizer
   - XML
   - Ctype
   - JSON
   - BCMath

---

## معلومات مهمة 📋

- **Domain:** mediapro.social
- **Database:** socialmedia_manager
- **DB User:** admin_mediapro
- **Laravel Version:** 12.33.0
- **PHP Required:** 8.2+

---

## للتواصل والدعم

إذا احتجت مساعدة إضافية، أرسل لي:
1. محتوى ملف `storage/logs/laravel.log` (آخر 50 سطر)
2. لقطة شاشة من إعدادات Hosting في Plesk
3. نتيجة الأمر: `php artisan --version`
