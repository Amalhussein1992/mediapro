# 🚀 دليل الرفع والتعديل المباشر على Plesk

## الوضع الحالي ❌
- الموقع: https://mediapro.social
- المشكلة: خطأ 500 Server Error
- السبب المحتمل: إعدادات Plesk أو ملفات مفقودة

---

## الحل السريع (5 دقائق) ⚡

### الخطوة 1️⃣: تجهيز الملفات للرفع

شغّل الأمر التالي في نفس مجلد الباك اند:

```bash
upload-simple.bat
```

هذا سيُنشئ ملف `mediapro-upload.zip` جاهز للرفع.

---

### الخطوة 2️⃣: رفع الملفات على Plesk

1. **افتح Plesk Panel**
   - اذهب إلى: https://mediapro.social:8443 (أو رابط Plesk الخاص بك)
   - سجل دخول

2. **اذهب إلى File Manager**
   - Websites & Domains → mediapro.social → File Manager
   - اذهب إلى مجلد `httpdocs`

3. **ارفع وفك ضغط الملف**
   - اضغط على Upload
   - ارفع ملف `mediapro-upload.zip`
   - اضغط بزر الماوس الأيمن على الملف → Extract Files
   - احذف الملف المضغوط بعد فك الضغط

---

### الخطوة 3️⃣: تعديل Document Root

1. في Plesk، اذهب إلى:
   **Websites & Domains → mediapro.social → Hosting Settings**

2. ابحث عن **Document Root**

3. غيّر القيمة من:
   ```
   httpdocs
   ```
   إلى:
   ```
   httpdocs/public
   ```

4. اضغط **OK** أو **Apply**

---

### الخطوة 4️⃣: تشغيل الأوامر عبر SSH

1. في Plesk، اذهب إلى:
   **Tools & Settings → SSH Terminal** (أو **Websites & Domains → SSH Access**)

2. شغّل الأوامر التالية:

```bash
# انتقل إلى مجلد الموقع
cd /var/www/vhosts/mediapro.social/httpdocs

# تثبيت dependencies
composer install --no-dev --optimize-autoloader

# تشغيل قاعدة البيانات
php artisan migrate --force

# مسح وإعادة بناء Cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# إصلاح الصلاحيات
chmod -R 775 storage bootstrap/cache
chown -R $USER:psacln storage bootstrap/cache
```

---

### الخطوة 5️⃣: اختبار الموقع ✅

1. **اختبار التشخيص:**
   افتح في المتصفح:
   ```
   https://mediapro.social/test.php
   ```

   يجب أن يعرض معلومات عن السيرفر واختبارات Laravel

2. **اختبار API:**
   ```
   https://mediapro.social/api/config
   ```

   يجب أن يعرض:
   ```json
   {
     "app_name": "Media Pro",
     "version": "1.0.0",
     ...
   }
   ```

3. **الموقع الرئيسي:**
   ```
   https://mediapro.social
   ```

---

## للتعديل والرفع المباشر مستقبلاً 🔄

### طريقة 1: عبر Plesk File Manager (سهلة)

1. افتح Plesk File Manager
2. اذهب إلى الملف المطلوب
3. اضغط **Edit** أو **Edit in Code Editor**
4. عدّل الملف
5. احفظ (Ctrl+S أو Save button)

**ملاحظة:** قد تحتاج لمسح Cache بعد التعديل:
```bash
php artisan config:clear
php artisan cache:clear
```

---

### طريقة 2: عبر Git (احترافية)

1. **إعداد Git في Plesk:**
   ```bash
   cd /var/www/vhosts/mediapro.social/httpdocs
   git init
   git remote add origin YOUR_REPO_URL
   ```

2. **عند كل تعديل:**
   ```bash
   # في جهازك المحلي
   git add .
   git commit -m "وصف التعديل"
   git push

   # في السيرفر (عبر SSH)
   cd /var/www/vhosts/mediapro.social/httpdocs
   git pull
   php artisan config:clear
   php artisan cache:clear
   ```

---

### طريقة 3: عبر FTP/SFTP (مباشرة)

استخدم برنامج مثل:
- **FileZilla**
- **WinSCP**
- **Cyberduck**

**معلومات الاتصال:**
- Host: `mediapro.social`
- Protocol: SFTP (SSH File Transfer Protocol)
- Port: 22
- Username: اسم المستخدم في Plesk
- Password: كلمة المرور

---

## الملفات المهمة التي قد تحتاج تعديلها 📝

### 1. إعدادات التطبيق
```
.env
```
→ إعدادات قاعدة البيانات، API Keys، إلخ

### 2. Routes (المسارات)
```
routes/api.php
routes/web.php
```

### 3. Controllers (المتحكمات)
```
app/Http/Controllers/
```

### 4. Models (النماذج)
```
app/Models/
```

### 5. Database Migrations
```
database/migrations/
```

### 6. Config Files
```
config/
```

---

## حل المشاكل الشائعة 🔧

### المشكلة: خطأ 500 بعد الرفع
**الحل:**
```bash
php artisan config:clear
php artisan cache:clear
chmod -R 775 storage bootstrap/cache
```

### المشكلة: خطأ في قاعدة البيانات
**الحل:**
1. تحقق من `.env` - معلومات الاتصال صحيحة
2. شغّل: `php artisan migrate:fresh --force`

### المشكلة: الصور/الملفات لا تُرفع
**الحل:**
```bash
chmod -R 775 storage/app/public
php artisan storage:link
```

### المشكلة: Routes لا تعمل
**الحل:**
```bash
php artisan route:clear
php artisan route:cache
```

---

## فحص الأخطاء (Logs) 📋

### عبر Plesk File Manager:
1. اذهب إلى: `storage/logs/`
2. افتح آخر ملف `laravel.log`
3. ابحث عن `[ERROR]`

### عبر SSH:
```bash
tail -n 100 storage/logs/laravel.log
```

---

## نصائح مهمة ⚠️

1. ✅ **دائماً احتفظ بنسخة احتياطية** قبل أي تعديل كبير
2. ✅ **امسح Cache** بعد تعديل ملفات config أو routes
3. ✅ **افحص Logs** إذا حدث خطأ
4. ✅ **استخدم `.env.server`** كمرجع لإعدادات الإنتاج
5. ❌ **لا تُفعّل** `APP_DEBUG=true` في الإنتاج
6. ❌ **لا تنسى** تشغيل `composer install` بعد تحديث dependencies

---

## الأوامر المفيدة في SSH 🛠️

```bash
# مسح كل Cache
php artisan optimize:clear

# إعادة بناء كل Cache
php artisan optimize

# التحقق من Laravel
php artisan about

# قائمة Routes
php artisan route:list

# قائمة الأوامر المتاحة
php artisan list

# فحص الاتصال بقاعدة البيانات
php artisan db:show

# إنشاء مستخدم Admin (إذا كان موجود)
php artisan make:admin

# التحقق من الصلاحيات
ls -la storage
ls -la bootstrap/cache
```

---

## معلومات الاتصال الحالية 📊

- **Domain:** mediapro.social
- **Database:** socialmedia_manager
- **DB User:** admin_mediapro
- **DB Password:** (موجود في `.env`)
- **Laravel Version:** 12.33.0
- **PHP Required:** 8.2+

---

## الملفات التي أنشأتها لك 📦

1. ✅ **`.env.server`** - إعدادات production صحيحة
2. ✅ **`upload-simple.bat`** - سكريبت لتجهيز الملفات
3. ✅ **`upload-to-plesk.ps1`** - سكريبت متقدم للرفع
4. ✅ **`public/test.php`** - صفحة تشخيص السيرفر
5. ✅ **`FIX_500_ERROR_AR.md`** - دليل حل الأخطاء
6. ✅ **`ابدأ_هنا_PLESK.md`** - هذا الملف

---

## المساعدة والدعم 💬

إذا واجهت أي مشكلة:

1. **افتح ملف Log:**
   ```
   storage/logs/laravel.log
   ```

2. **شغّل صفحة التشخيص:**
   ```
   https://mediapro.social/test.php
   ```

3. **أرسل لي:**
   - لقطة شاشة من الخطأ
   - آخر 50 سطر من `laravel.log`
   - نتيجة `test.php`

---

## ابدأ الآن! 🎯

```bash
# الخطوة 1
upload-simple.bat

# الخطوة 2-3: ارفع على Plesk وغيّر Document Root

# الخطوة 4: عبر SSH
cd /var/www/vhosts/mediapro.social/httpdocs
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
chmod -R 775 storage bootstrap/cache

# الخطوة 5: افتح
https://mediapro.social/test.php
```

---

**بالتوفيق! 🚀**
