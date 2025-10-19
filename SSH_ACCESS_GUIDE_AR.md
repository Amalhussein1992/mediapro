# 🔐 طريقة الوصول لـ SSH في Plesk

## الطريقة 1️⃣: SSH Terminal المباشر في Plesk

### الخطوات:

1. **من القائمة الرئيسية:**
   ```
   Websites & Domains → mediapro.social
   ```

2. **ابحث عن أحد هذه الخيارات:**
   - **SSH Terminal** (أيقونة شاشة سوداء)
   - **Web Terminal**
   - **Terminal**

3. **إذا لم تجدها، اذهب إلى:**
   ```
   Websites & Domains → mediapro.social → Web Hosting Access
   ```
   ثم فعّل: **Terminal access (SSH)**

---

## الطريقة 2️⃣: عبر SSH Client خارجي (موصى بها)

### باستخدام PuTTY أو PowerShell:

#### على Windows PowerShell:
```powershell
ssh username@mediapro.social
```

#### معلومات الاتصال:
- **Host:** mediapro.social
- **Port:** 22
- **Username:** اسم المستخدم في Plesk
- **Password:** كلمة مرور Plesk

---

## الطريقة 3️⃣: عبر File Manager ثم Terminal

1. اذهب إلى:
   ```
   Tools & Settings → Tools & Resources
   ```

2. ابحث عن واحد من هذه:
   - **SSH Terminal**
   - **Terminal**
   - **Scheduled Tasks (Cron jobs)** → هناك خيار Terminal

---

## الطريقة 4️⃣: تفعيل SSH Access

إذا لم يكن SSH متاح، فعّله:

### الخطوات:

1. **اذهب إلى:**
   ```
   Websites & Domains → mediapro.social
   ```

2. **اضغط على:**
   ```
   Web Hosting Access
   ```
   أو
   ```
   Hosting Settings
   ```

3. **ابحث عن:**
   - ✅ **Terminal access (SSH)** → فعّله
   - أو **Access to server over SSH** → فعّله

4. **احفظ التغييرات**

---

## البديل: استخدام Scheduled Tasks

إذا ما قدرت توصل للـ SSH Terminal:

1. اذهب إلى:
   ```
   Tools & Settings → Scheduled Tasks
   ```

2. **اضغط Add Task**

3. في **Command**، اكتب:
   ```bash
   cd /var/www/vhosts/mediapro.social/httpdocs && composer install --no-dev --optimize-autoloader && php artisan migrate --force && php artisan optimize && chmod -R 775 storage bootstrap/cache
   ```

4. اختر **Run Once** في وقت قريب (مثلاً بعد دقيقة)

5. احفظ

---

## الطريقة الأسهل: استخدام SSH عبر برنامج خارجي

### تحميل وتثبيت PuTTY:

1. **حمّل PuTTY:**
   https://www.putty.org/

2. **افتح PuTTY**

3. **املأ البيانات:**
   - Host Name: `mediapro.social`
   - Port: `22`
   - Connection Type: SSH

4. **اضغط Open**

5. **سجّل دخول:**
   - Username: اسم المستخدم في Plesk
   - Password: كلمة المرور

---

## ملاحظة مهمة ⚠️

**لو ما قدرت توصل لـ SSH، ممكن تسوي كل شي عبر File Manager:**

### بدائل بدون SSH:

#### 1. Composer Install:
يمكن تسويها من Plesk:
```
Applications → Composer
```

#### 2. Laravel Commands:
أنشئ ملف PHP مؤقت: `run-commands.php`

```php
<?php
// في مجلد httpdocs/run-commands.php

echo "Running Laravel commands...\n";

// Change to project directory
chdir(__DIR__);

// Run artisan commands
echo shell_exec('php artisan config:clear');
echo shell_exec('php artisan cache:clear');
echo shell_exec('php artisan route:clear');
echo shell_exec('php artisan view:clear');
echo shell_exec('php artisan config:cache');
echo shell_exec('php artisan route:cache');
echo shell_exec('php artisan view:cache');
echo shell_exec('php artisan migrate --force');

// Fix permissions
echo shell_exec('chmod -R 775 storage bootstrap/cache');

echo "\nDone!";
?>
```

ثم افتح في المتصفح:
```
https://mediapro.social/run-commands.php
```

**واحذفه بعد الاستخدام!**

---

## الأوامر اللي لازم تشغّلها:

بعد ما توصل للـ SSH Terminal، شغّل:

```bash
# انتقل لمجلد الموقع
cd /var/www/vhosts/mediapro.social/httpdocs

# تثبيت dependencies
composer install --no-dev --optimize-autoloader

# تشغيل migrations
php artisan migrate --force

# بناء cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# إصلاح الصلاحيات
chmod -R 775 storage bootstrap/cache
chown -R $USER:psacln storage bootstrap/cache
```

---

## إذا واجهت مشاكل:

أرسل لي لقطة شاشة من:
1. **Websites & Domains → mediapro.social** (الصفحة الرئيسية للدومين)
2. **Hosting Settings** (إعدادات الاستضافة)

وأنا أساعدك توصل للـ SSH أو نلاقي بديل!
