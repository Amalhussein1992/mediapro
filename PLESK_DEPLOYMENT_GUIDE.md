# 🚀 دليل رفع Laravel على Plesk
# Plesk Deployment Guide for MediaPro Backend

## 📋 المحتوى | Table of Contents

1. [طرق الرفع المتاحة](#deployment-methods)
2. [الطريقة 1: Git Deployment (الأسهل)](#method-1-git)
3. [الطريقة 2: FTP/SFTP](#method-2-ftp)
4. [الطريقة 3: File Manager](#method-3-file-manager)
5. [إعداد Laravel بعد الرفع](#laravel-setup)
6. [إعداد قاعدة البيانات](#database-setup)
7. [التحقق من التثبيت](#verification)

---

## 🎯 طرق الرفع المتاحة {#deployment-methods}

لديك 3 طرق لرفع Laravel على Plesk بدون SSH:

| الطريقة | السهولة | السرعة | التحديثات التلقائية |
|---------|---------|--------|---------------------|
| **Git Deployment** | ⭐⭐⭐⭐⭐ | ⚡⚡⚡ | ✅ نعم |
| **FTP/SFTP** | ⭐⭐⭐ | ⚡⚡ | ❌ لا |
| **File Manager** | ⭐⭐ | ⚡ | ❌ لا |

**الموصى به:** Git Deployment (الأسرع والأسهل)

---

## ✅ الطريقة 1: Git Deployment (الموصى بها) {#method-1-git}

### المميزات:
- ✅ تحديثات تلقائية من GitHub
- ✅ نسخ احتياطية تلقائية
- ✅ سهولة التراجع عن التغييرات
- ✅ لا حاجة لرفع الملفات يدوياً

### الخطوات:

#### 1️⃣ رفع الكود على GitHub

أولاً تأكد أن كل التغييرات موجودة على GitHub:

```bash
# في جهازك (Windows)
cd C:\Users\HP\Desktop\social-media-app\SocialMediaManager\backend-laravel

# تأكد من commit كل التغييرات
git status
git add .
git commit -m "✅ Ready for production deployment"
git push origin main
```

#### 2️⃣ إعداد Git في Plesk

**خطوة أ: ادخل على Plesk Panel**
```
URL: https://your-server-ip:8443
أو
URL: https://plesk.mediapro.social:8443
```

**خطوة ب: اذهب إلى Git**
```
Plesk → Websites & Domains → www.mediapro.social → Git
```

**خطوة ج: اضغط "Add Repository"**

**خطوة د: املأ المعلومات:**
```
Repository name: MediaPro Backend
Repository URL: https://github.com/Amalhussein1992/mediapro.git
Repository path: /var/www/vhosts/mediapro.social/mediapro-repo
Branch: main
Deployment mode: Deploy repository
```

⚠️ **مهم:** لا تضع الـ path مباشرة في `httpdocs`، بل في مجلد منفصل.

**خطوة هـ: اضغط "OK"** ثم **"Pull Updates"**

#### 3️⃣ إنشاء Deployment Script

**خطوة أ: في Plesk، اذهب إلى:**
```
Plesk → Websites & Domains → www.mediapro.social → File Manager
```

**خطوة ب: افتح مجلد:**
```
/var/www/vhosts/mediapro.social/mediapro-repo
```

**خطوة ج: ارفع ملف `deploy.sh`** (الموجود في المشروع)

**خطوة د: اجعل الملف قابل للتنفيذ:**

في Plesk File Manager:
- انقر بزر الماوس الأيمن على `deploy.sh`
- اختر "Change Permissions"
- اضبط على `755` أو اختر: Owner: Read, Write, Execute

#### 4️⃣ إعداد Post-Receive Hook

**خطوة أ: في Plesk Git Settings:**
```
Plesk → Git → [Your Repo] → Additional Settings
```

**خطوة ب: في "Actions After Repository Update"، أضف:**
```bash
cd /var/www/vhosts/mediapro.social/mediapro-repo
bash deploy.sh
```

**خطوة ج: احفظ التغييرات**

#### 5️⃣ أول deployment

اضغط **"Pull Updates"** في Plesk Git panel.

سيتم تلقائياً:
- ✅ سحب آخر تحديثات من GitHub
- ✅ نسخ ملفات Laravel إلى `httpdocs`
- ✅ تثبيت Composer dependencies
- ✅ ضبط الصلاحيات
- ✅ تحديث Laravel caches

---

## 🔧 الطريقة 2: FTP/SFTP {#method-2-ftp}

إذا لم تستطع استخدام Git، استخدم FTP:

### 1️⃣ الحصول على بيانات FTP من Plesk

**في Plesk Panel:**
```
Plesk → Websites & Domains → www.mediapro.social → FTP Access
```

ستجد:
```
Server: ftp.mediapro.social (or your server IP)
Username: mediapro-user (or as shown)
Password: [your password]
Port: 21 (FTP) or 22 (SFTP)
```

### 2️⃣ تحضير الملفات للرفع

**في جهازك:**

```bash
cd C:\Users\HP\Desktop\social-media-app\SocialMediaManager\backend-laravel

# احذف الملفات غير المطلوبة
rm -rf node_modules
rm -rf storage/logs/*
rm -rf storage/framework/cache/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*
rm -rf .git
```

### 3️⃣ استخدام FileZilla (أسهل برنامج FTP)

**تحميل FileZilla:**
```
https://filezilla-project.org/download.php?type=client
```

**الاتصال:**
1. افتح FileZilla
2. File → Site Manager → New Site
3. املأ البيانات:
   ```
   Protocol: SFTP (or FTP)
   Host: ftp.mediapro.social
   Port: 22 (SFTP) or 21 (FTP)
   User: [from Plesk]
   Password: [from Plesk]
   ```
4. اضغط "Connect"

**رفع الملفات:**
1. في الجهة اليسرى: انتقل إلى مجلد Laravel المحلي
2. في الجهة اليمنى: انتقل إلى `/httpdocs`
3. اسحب كل الملفات من اليسار إلى اليمين
4. انتظر حتى ينتهي الرفع (قد يأخذ 10-20 دقيقة)

⚠️ **مهم:** لا ترفع ملف `.env` من جهازك! سننشئه على السيرفر.

---

## 📁 الطريقة 3: File Manager في Plesk {#method-3-file-manager}

الأبطأ لكن الأسهل للملفات الصغيرة:

### 1️⃣ ضغط الملفات

**في جهازك:**

```bash
cd C:\Users\HP\Desktop\social-media-app\SocialMediaManager

# اضغط مجلد backend-laravel
# في Windows: انقر بزر الماوس الأيمن → Send to → Compressed (zipped) folder
```

### 2️⃣ رفع الملف المضغوط

**في Plesk:**
1. اذهب إلى: `File Manager`
2. انتقل إلى: `/httpdocs`
3. اضغط "Upload"
4. ارفع ملف `backend-laravel.zip`
5. بعد الرفع، انقر بزر الماوس الأيمن → "Extract"

---

## ⚙️ إعداد Laravel بعد الرفع {#laravel-setup}

بعد رفع الملفات بأي طريقة، لازم تعمل setup لـ Laravel:

### 1️⃣ إنشاء ملف .env

**في Plesk File Manager:**

1. اذهب إلى `/httpdocs`
2. اضغط "Create File"
3. اسم الملف: `.env`
4. افتح الملف وألصق:

```env
APP_NAME="Media Pro"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://www.mediapro.social

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=mediapro_db
DB_USERNAME=mediapro_user
DB_PASSWORD=YOUR_DB_PASSWORD_HERE

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

GEMINI_API_KEY=your_key_here
OPENAI_API_KEY=your_key_here
CLAUDE_API_KEY=your_key_here

# Stripe
STRIPE_KEY=pk_live_your_key
STRIPE_SECRET=sk_live_your_key
STRIPE_WEBHOOK_SECRET=whsec_your_key

# Facebook & Instagram
FACEBOOK_CLIENT_ID=your_app_id
FACEBOOK_CLIENT_SECRET=your_app_secret
FACEBOOK_REDIRECT_URI=https://www.mediapro.social/api/social-oauth/callback

# Twitter
TWITTER_CLIENT_ID=your_client_id
TWITTER_CLIENT_SECRET=your_client_secret
TWITTER_REDIRECT_URI=https://www.mediapro.social/api/social-oauth/callback

# LinkedIn
LINKEDIN_CLIENT_ID=your_client_id
LINKEDIN_CLIENT_SECRET=your_client_secret
LINKEDIN_REDIRECT_URI=https://www.mediapro.social/api/social-oauth/callback

# TikTok
TIKTOK_CLIENT_KEY=your_client_key
TIKTOK_CLIENT_SECRET=your_client_secret
TIKTOK_REDIRECT_URI=https://www.mediapro.social/api/social-oauth/callback
```

5. احفظ الملف

### 2️⃣ Generate Application Key

**في Plesk → SSH Terminal** (إذا متاح):

```bash
cd /var/www/vhosts/mediapro.social/httpdocs
php artisan key:generate
```

**أو يدوياً:**

1. في جهازك المحلي:
   ```bash
   php artisan key:generate --show
   ```
2. انسخ الـ key الذي يظهر (مثل: `base64:xxxxx...`)
3. في Plesk File Manager، افتح `.env`
4. ألصق الـ key في `APP_KEY=`

### 3️⃣ ضبط الصلاحيات

**في Plesk File Manager:**

اضبط صلاحيات هذه المجلدات إلى `775`:
- `/httpdocs/storage` (كل المجلدات الفرعية)
- `/httpdocs/bootstrap/cache`

**كيف:**
1. انقر بزر الماوس الأيمن على المجلد
2. "Change Permissions"
3. اختر: `775` أو اختر جميع صناديق Read, Write, Execute

### 4️⃣ تشغيل Composer

**في Plesk → PHP Composer:**

```
Plesk → Websites & Domains → www.mediapro.social → PHP Composer
```

اضغط **"Install"** أو شغل:
```bash
composer install --no-dev --optimize-autoloader
```

### 5️⃣ Clear Laravel Caches

إذا عندك SSH Terminal في Plesk:

```bash
cd /var/www/vhosts/mediapro.social/httpdocs
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:cache
php artisan route:cache
```

---

## 🗄️ إعداد قاعدة البيانات {#database-setup}

### 1️⃣ إنشاء Database في Plesk

**خطوة أ:**
```
Plesk → Websites & Domains → www.mediapro.social → Databases
```

**خطوة ب:** اضغط "Add Database"

**خطوة ج:** املأ:
```
Database name: mediapro_db
Database user: mediapro_user
Password: [كلمة سر قوية]
```

**خطوة د:** احفظ

### 2️⃣ تحديث ملف .env

افتح `.env` وحدث:
```env
DB_DATABASE=mediapro_db
DB_USERNAME=mediapro_user
DB_PASSWORD=YOUR_PASSWORD_HERE
```

### 3️⃣ تشغيل Migrations

**في SSH Terminal:**
```bash
cd /var/www/vhosts/mediapro.social/httpdocs
php artisan migrate --force
```

**أو استخدم phpMyAdmin:**
1. في Plesk → Databases → phpMyAdmin
2. افتح قاعدة البيانات `mediapro_db`
3. استورد ملف SQL (إذا عندك)

### 4️⃣ تشغيل Seeders (اختياري)

```bash
php artisan db:seed --class=ComprehensiveDatabaseSeeder --force
```

---

## ✅ التحقق من التثبيت {#verification}

### 1️⃣ اختبار الصفحة الرئيسية

افتح المتصفح:
```
https://www.mediapro.social
```

يجب أن تظهر Landing Page.

### 2️⃣ اختبار API

```
https://www.mediapro.social/api/config
```

يجب أن يرجع JSON response.

### 3️⃣ اختبار Admin Panel

```
https://www.mediapro.social/login
```

سجل دخول بحساب Admin:
```
Email: admin@mediapro.social
Password: password123
```

### 4️⃣ اختبار Database Connection

```
https://www.mediapro.social/api/subscription-plans
```

يجب أن يرجع قائمة الاشتراكات.

---

## 🔧 إعدادات إضافية مهمة

### 1️⃣ ضبط Document Root

**في Plesk:**
```
Plesk → Websites & Domains → www.mediapro.social → Hosting Settings
```

تأكد أن **Document Root** هو:
```
/httpdocs/public
```

⚠️ **مهم جداً!** Laravel يحتاج أن الـ Document Root يكون مجلد `public`.

### 2️⃣ ضبط PHP Settings

**في Plesk:**
```
Plesk → Websites & Domains → www.mediapro.social → PHP Settings
```

تأكد من:
```
PHP Version: 8.1 أو أحدث
memory_limit: 256M
max_execution_time: 300
upload_max_filesize: 64M
post_max_size: 64M
```

### 3️⃣ تفعيل Required Extensions

تأكد أن هذه الـ extensions مفعلة:
```
✅ BCMath
✅ Ctype
✅ cURL
✅ DOM
✅ Fileinfo
✅ JSON
✅ Mbstring
✅ OpenSSL
✅ PDO
✅ PDO_MySQL
✅ Tokenizer
✅ XML
✅ GD
```

### 4️⃣ إعداد Cron Jobs (للمهام المجدولة)

**في Plesk → Scheduled Tasks:**

أضف Cron Job:
```
* * * * * cd /var/www/vhosts/mediapro.social/httpdocs && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔄 التحديثات المستقبلية

### إذا استخدمت Git Deployment:

ببساطة في جهازك:
```bash
git add .
git commit -m "Update description"
git push origin main
```

ثم في Plesk → Git → اضغط **"Pull Updates"**

### إذا استخدمت FTP:

ارفع الملفات المحدثة فقط عبر FileZilla.

---

## ❌ حل المشاكل الشائعة

### مشكلة 1: 500 Internal Server Error

**الحل:**
```bash
# تأكد من الصلاحيات
chmod -R 755 /var/www/vhosts/mediapro.social/httpdocs
chmod -R 775 /var/www/vhosts/mediapro.social/httpdocs/storage
chmod -R 775 /var/www/vhosts/mediapro.social/httpdocs/bootstrap/cache

# Clear caches
php artisan config:clear
php artisan cache:clear
```

### مشكلة 2: Database Connection Error

**الحل:**
- تحقق من بيانات Database في `.env`
- تأكد أن Database user له صلاحيات كاملة
- جرب `DB_HOST=127.0.0.1` بدلاً من `localhost`

### مشكلة 3: 404 Not Found للـ routes

**الحل:**
- تأكد أن Document Root هو `/httpdocs/public`
- شغل: `php artisan route:cache`

### مشكلة 4: Composer dependencies مش موجودة

**الحل:**
```bash
cd /var/www/vhosts/mediapro.social/httpdocs
composer install --no-dev --optimize-autoloader
```

---

## 📞 الدعم

إذا واجهت أي مشكلة:

1. **تحقق من Laravel logs:**
   ```
   /httpdocs/storage/logs/laravel.log
   ```

2. **تحقق من PHP error logs في Plesk:**
   ```
   Plesk → Websites & Domains → Logs
   ```

3. **شغل الـ debug mode مؤقتاً:**
   في `.env`:
   ```
   APP_DEBUG=true
   ```
   ثم أعد تحميل الصفحة لترى الخطأ كاملاً.

---

## ✅ Checklist نهائي

قبل ما تعتبر الـ deployment مكتمل:

- [ ] ✅ الملفات مرفوعة بالكامل
- [ ] ✅ ملف `.env` موجود ومضبوط
- [ ] ✅ `APP_KEY` محدد
- [ ] ✅ Database موجود ومتصل
- [ ] ✅ Migrations مشغلة
- [ ] ✅ Composer dependencies مثبتة
- [ ] ✅ Permissions مضبوطة (755 للملفات، 775 للـ storage)
- [ ] ✅ Document Root = `/httpdocs/public`
- [ ] ✅ PHP Version >= 8.1
- [ ] ✅ Required Extensions مفعلة
- [ ] ✅ Laravel caches محدثة
- [ ] ✅ الموقع يعمل بدون أخطاء
- [ ] ✅ API endpoints تستجيب
- [ ] ✅ Admin panel يعمل
- [ ] ✅ Database connection يعمل

---

**🎉 مبروك! Laravel backend الآن شغال على Plesk!**

**الموقع:** https://www.mediapro.social
**Admin Panel:** https://www.mediapro.social/login
**API:** https://www.mediapro.social/api/*

---

**Last Updated:** January 2025
**Version:** 1.0 Production Ready
