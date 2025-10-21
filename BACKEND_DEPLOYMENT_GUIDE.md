# 🚀 Media Pro Backend - دليل النشر الشامل على السيرفر

## 📋 المحتويات
1. [التحضيرات المطلوبة](#التحضيرات-المطلوبة)
2. [رفع الكود على GitHub](#رفع-الكود-على-github)
3. [إعداد السيرفر](#إعداد-السيرفر)
4. [نشر التطبيق](#نشر-التطبيق)
5. [تشغيل الـ Seeders](#تشغيل-الـ-seeders)
6. [التحقق من النشر](#التحقق-من-النشر)
7. [استكشاف الأخطاء](#استكشاف-الأخطاء)

---

## التحضيرات المطلوبة

### ✅ البيانات الموجودة:
- **السيرفر:** www.mediapro.social (148.66.159.65)
- **Laravel Path:** `/var/www/mediapro`
- **Database:** MySQL
- **Web Server:** Nginx/Apache

### 📦 الملفات الجاهزة للنشر:
```
✅ backend-laravel/
   ├── app/                           # All controllers & models
   ├── database/
   │   ├── migrations/               # All migrations
   │   └── seeders/
   │       └── ComprehensiveDatabaseSeeder.php  # NEW!
   ├── resources/views/
   │   ├── admin/                     # Admin panel views
   │   └── pages/                     # Public pages (Privacy, Terms, About)
   ├── routes/
   │   ├── api.php                    # API routes
   │   └── web.php                    # Web routes
   ├── .env.example
   ├── composer.json
   └── package.json
```

---

## رفع الكود على GitHub

### الخطوة 1: إضافة جميع التغييرات

```bash
cd C:\Users\HP\Desktop\social-media-app\SocialMediaManager\backend-laravel

# إضافة Seeder الجديد
git add database/seeders/ComprehensiveDatabaseSeeder.php

# إضافة أي ملفات أخرى محدثة
git add app/
git add resources/views/
git add routes/
git add .env.example

# مراجعة الملفات المضافة
git status
```

### الخطوة 2: عمل Commit

```bash
git commit -m "🚀 Complete backend with comprehensive seeders and admin panel

Features:
- Comprehensive database seeder with 100+ users
- Subscription plans (Free, Starter, Pro, Enterprise)
- 500+ realistic posts across all platforms
- Social accounts for all major platforms
- Brand kits and analytics data
- Payment history and notifications
- Admin dashboard enhancements
- Updated public pages (Privacy, Terms, About)

Database Seeding:
- Users: 100+ with realistic Arabic & English names
- Posts: 500+ across Facebook, Instagram, Twitter, etc.
- Subscriptions: 70% of users with active plans
- Social Accounts: 1-3 accounts per user
- Payments: Complete payment history
- Notifications: 5-20 notifications per user

Ready for production deployment!

🤖 Generated with [Claude Code](https://claude.com/claude-code)

Co-Authored-By: Claude <noreply@anthropic.com>"
```

### الخطوة 3: Push إلى GitHub

```bash
git push origin main
```

**ملاحظة:** إذا طلب منك authentication:
- **Username:** Your GitHub username
- **Password:** Personal Access Token (not your GitHub password)

---

## إعداد السيرفر

### الخطوة 1: الاتصال بالسيرفر

```bash
ssh root@www.mediapro.social
# أو
ssh root@148.66.159.65
```

### الخطوة 2: التحقق من متطلبات السيرفر

```bash
# التحقق من PHP
php -v
# يجب أن يكون >= 8.1

# التحقق من Composer
composer --version

# التحقق من Node.js
node -v
npm -v

# التحقق من MySQL
mysql --version
```

### الخطوة 3: تحديث الصلاحيات

```bash
cd /var/www/mediapro

# تأكد من الصلاحيات الصحيحة
sudo chown -R www-data:www-data /var/www/mediapro
sudo chmod -R 755 /var/www/mediapro
sudo chmod -R 775 /var/www/mediapro/storage
sudo chmod -R 775 /var/www/mediapro/bootstrap/cache
```

---

## نشر التطبيق

### الخطوة 1: سحب آخر التحديثات من GitHub

```bash
cd /var/www/mediapro
git pull origin main
```

### الخطوة 2: تثبيت Dependencies

```bash
# تثبيت Composer packages
composer install --optimize-autoloader --no-dev

# تثبيت NPM packages
npm install

# Build assets
npm run build
# أو
npm run production
```

### الخطوة 3: إعداد Environment

```bash
# نسخ .env.example إذا لم يكن .env موجود
cp .env.example .env

# تعديل .env
nano .env
```

**محتوى .env المطلوب:**

```env
APP_NAME="Media Pro"
APP_ENV=production
APP_KEY=base64:...  # سيتم توليده
APP_DEBUG=false
APP_URL=https://www.mediapro.social

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mediapro
DB_USERNAME=mediapro_user
DB_PASSWORD=your_secure_password

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=public
QUEUE_CONNECTION=database
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@mediapro.social"
MAIL_FROM_NAME="${APP_NAME}"

# Social Media API Keys
FACEBOOK_APP_ID=
FACEBOOK_APP_SECRET=
INSTAGRAM_CLIENT_ID=
INSTAGRAM_CLIENT_SECRET=
TWITTER_API_KEY=
TWITTER_API_SECRET=

# Payment Gateways
STRIPE_KEY=
STRIPE_SECRET=
PAYPAL_CLIENT_ID=
PAYPAL_SECRET=

# AI Services
OPENAI_API_KEY=
```

### الخطوة 4: توليد APP_KEY

```bash
php artisan key:generate
```

### الخطوة 5: تشغيل Migrations

```bash
# مراجعة الـ migrations الموجودة
php artisan migrate:status

# تشغيل الـ migrations (حذر!)
php artisan migrate --force

# أو إعادة بناء قاعدة البيانات كاملة (سيحذف البيانات!)
php artisan migrate:fresh --force
```

---

## تشغيل الـ Seeders

### الخطوة 1: تشغيل Seeder الشامل

```bash
php artisan db:seed --class=ComprehensiveDatabaseSeeder
```

سيقوم الـ Seeder بإنشاء:
- ✅ 100+ مستخدم (عربي + إنجليزي)
- ✅ 4 خطط اشتراك (Free, Starter, Pro, Enterprise)
- ✅ 70 اشتراك نشط
- ✅ 150+ حساب social media
- ✅ 50 brand kit
- ✅ 500+ منشور (منشور، مجدول، مسودة)
- ✅ 300+ دفعة
- ✅ 500+ إشعار
- ✅ إعدادات التطبيق

### الخطوة 2: التحقق من البيانات

```bash
# التحقق من عدد المستخدمين
php artisan tinker
>>> User::count()
>>> Post::count()
>>> Subscription::count()
>>> exit
```

---

## تنظيف وتحسين الأداء

### الخطوة 1: تنظيف Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### الخطوة 2: تحسين الأداء

```bash
# Cache التكوينات
php artisan config:cache

# Cache الـ routes
php artisan route:cache

# Cache الـ views
php artisan view:cache

# Optimize Composer
composer dump-autoload --optimize
```

### الخطوة 3: Storage Link

```bash
php artisan storage:link
```

### الخطوة 4: إعادة تشغيل الخدمات

```bash
# إعادة تشغيل Nginx
sudo systemctl restart nginx

# أو Apache
sudo systemctl restart apache2

# إعادة تشغيل PHP-FPM
sudo systemctl restart php8.2-fpm

# إعادة تشغيل Redis
sudo systemctl restart redis-server
```

---

## التحقق من النشر

### 1. التحقق من الموقع

افتح المتصفح وتحقق من:

#### الصفحات العامة:
- ✅ https://www.mediapro.social
- ✅ https://www.mediapro.social/privacy
- ✅ https://www.mediapro.social/terms
- ✅ https://www.mediapro.social/about

#### Admin Panel:
- ✅ https://www.mediapro.social/admin
- **Login:** admin@mediapro.social
- **Password:** password

#### API Endpoints:
```bash
# Test API health
curl https://www.mediapro.social/api

# Test admin stats
curl -X GET https://www.mediapro.social/api/admin/dashboard/stats \
  -H "Accept: application/json"
```

### 2. التحقق من الأداء

```bash
# Test response time
time curl https://www.mediapro.social
# يجب أن يكون < 500ms

# Check logs
tail -f /var/www/mediapro/storage/logs/laravel.log
```

### 3. التحقق من Database

```bash
mysql -u mediapro_user -p mediapro

# داخل MySQL
SELECT COUNT(*) FROM users;
SELECT COUNT(*) FROM posts;
SELECT COUNT(*) FROM subscriptions;
SELECT COUNT(*) FROM social_accounts;
```

---

## استكشاف الأخطاء

### مشكلة: 500 Internal Server Error

**الحل:**

```bash
# تحقق من الـ logs
tail -f /var/www/mediapro/storage/logs/laravel.log
tail -f /var/log/nginx/error.log

# تأكد من الصلاحيات
sudo chown -R www-data:www-data /var/www/mediapro/storage
sudo chmod -R 775 /var/www/mediapro/storage

# نظف الـ cache
php artisan cache:clear
php artisan config:clear
```

### مشكلة: Database Connection Error

**الحل:**

```bash
# تحقق من .env
cat /var/www/mediapro/.env | grep DB_

# اختبر الاتصال بـ MySQL
mysql -u mediapro_user -p -h 127.0.0.1

# تأكد من وجود Database
mysql -u root -p
>>> CREATE DATABASE IF NOT EXISTS mediapro;
>>> GRANT ALL ON mediapro.* TO 'mediapro_user'@'localhost';
>>> FLUSH PRIVILEGES;
```

### مشكلة: Seeder Fails

**الحل:**

```bash
# تحقق من الـ migrations أولاً
php artisan migrate:status

# شغل migrations إذا لم تكن موجودة
php artisan migrate --force

# جرب seeder مرة أخرى
php artisan db:seed --class=ComprehensiveDatabaseSeeder

# إذا فشل، شغله مع verbose
php artisan db:seed --class=ComprehensiveDatabaseSeeder --verbose
```

### مشكلة: CSS/JS لا تظهر

**الحل:**

```bash
# تأكد من storage link
php artisan storage:link

# Build assets مرة أخرى
npm run build

# تحقق من صلاحيات public
sudo chmod -R 755 /var/www/mediapro/public
```

---

## الصيانة الدورية

### يومياً:

```bash
# تحقق من الـ logs
tail -f /var/www/mediapro/storage/logs/laravel.log

# تحقق من disk space
df -h

# تحقق من memory usage
free -h
```

### أسبوعياً:

```bash
# تنظيف old logs
find /var/www/mediapro/storage/logs -name "*.log" -mtime +30 -delete

# تحديث packages
composer update
npm update
```

### شهرياً:

```bash
# Backup database
mysqldump -u mediapro_user -p mediapro > backup_$(date +%Y%m%d).sql

# Optimize database
php artisan optimize

# Clear old cache
php artisan cache:clear
```

---

## 🔐 الأمان

### 1. تفعيل HTTPS:

```bash
# إذا لم يكن SSL موجود
sudo certbot --nginx -d www.mediapro.social
```

### 2. تفعيل Firewall:

```bash
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 22/tcp
sudo ufw enable
```

### 3. Rate Limiting:

في `app/Http/Kernel.php`:
```php
'api' => [
    'throttle:60,1',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

---

## 📊 مراقبة الأداء

### تثبيت Laravel Telescope (Development):

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### تفعيل Laravel Horizon (Queue):

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan horizon
```

---

## 🎯 ملخص سريع

```bash
# النشر السريع (Quick Deploy)
cd /var/www/mediapro
git pull origin main
composer install --optimize-autoloader --no-dev
npm install && npm run build
php artisan migrate --force
php artisan db:seed --class=ComprehensiveDatabaseSeeder
php artisan cache:clear
php artisan config:cache
php artisan route:cache
sudo systemctl restart nginx
```

---

## 📞 الدعم

إذا واجهت أي مشاكل:

1. **Check Logs:**
   ```bash
   tail -f /var/www/mediapro/storage/logs/laravel.log
   ```

2. **Laravel Debug Mode (مؤقتاً فقط!):**
   ```bash
   # في .env
   APP_DEBUG=true
   php artisan config:clear
   ```

3. **Contact:**
   - Email: support@mediapro.social
   - Docs: docs.mediapro.social

---

**✅ تهانينا! الباك اند جاهز للعمل بكامل قوته! 🎉**

---

Generated with ❤️ by Claude Code
Last Updated: 21 يناير 2025
