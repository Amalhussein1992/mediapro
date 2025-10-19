# 🚀 إعداد Git Auto Deployment في Plesk

## المزايا ✨
- تعدل على الكود من جهازك
- `git push` وتلقائياً يرفع على السيرفر
- Laravel commands تشتغل تلقائياً
- Cache يتحدث تلقائياً

---

## الخطوات الكاملة

### 1️⃣ إنشاء GitHub Repository

#### على GitHub:

1. اذهب إلى: https://github.com/new
2. اسم الـ Repository: `mediapro-backend`
3. اختر: **Private** (للأمان)
4. اضغط **Create repository**

---

### 2️⃣ ربط المشروع بـ Git (على جهازك)

في مجلد الباك اند، شغّل في Terminal:

```bash
cd c:\Users\HP\Desktop\social-media-app\SocialMediaManager\backend-laravel

# Initialize Git (إذا لم يكن موجود)
git init

# Add all files
git add .

# First commit
git commit -m "Initial commit: MediaPro Laravel Backend"

# Add GitHub remote
git remote add origin https://github.com/YOUR_USERNAME/mediapro-backend.git

# Push to GitHub
git branch -M main
git push -u origin main
```

**ملاحظة:** استبدل `YOUR_USERNAME` باسم مستخدم GitHub

---

### 3️⃣ إعداد Git في Plesk

#### في Plesk Panel:

1. **Websites & Domains** → **mediapro.social**

2. ابحث عن **Git** أو **Git for Websites**

3. **Add Repository** أو **Connect Repository**

4. **املأ البيانات:**
   ```
   Repository URL: https://github.com/YOUR_USERNAME/mediapro-backend.git
   Repository path: /httpdocs
   Branch: main
   ```

5. **Authentication:**
   - إذا Repository خاص، أضف SSH Key أو Personal Access Token

---

### 4️⃣ تعديل Deployment Settings في Plesk

#### في صفحة Git في Plesk:

**1. Deployment Mode:**
   - ✅ اختر **Automatic** (مهم!)
   - هذا يخلي كل push يرفع تلقائياً

**2. Deployment Script:**
   - اضغط **Edit script**
   - احذف السكريبت الموجود
   - انسخ السكريبت التالي:

```bash
#!/bin/bash

# MediaPro - Laravel Deployment Script
set -e

echo "Starting MediaPro deployment..."

# Navigate to project
cd /var/www/vhosts/mediapro.social/httpdocs

# Install Composer dependencies
echo "[1/7] Installing dependencies..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Setup environment
echo "[2/7] Checking .env..."
if [ ! -f ".env" ]; then
    if [ -f ".env.server" ]; then
        cp .env.server .env
        echo "Created .env from .env.server"
    fi
fi

# Clear caches
echo "[3/7] Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations
echo "[4/7] Running migrations..."
php artisan migrate --force

# Build caches
echo "[5/7] Building caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage link
echo "[6/7] Storage link..."
php artisan storage:link || true

# Fix permissions
echo "[7/7] Fixing permissions..."
chmod -R 775 storage bootstrap/cache
chown -R $USER:psacln storage bootstrap/cache || true

echo "✓ Deployment complete!"
echo "Time: $(date)"
```

   - اضغط **Save** أو **OK**

**3. Deployment Steps:**
   تأكد من تفعيل هذه الخطوات:
   - ✅ Fetch source code
   - ✅ Deploy source code from Git
   - ✅ Install composer.json dependencies
   - ✅ Run deployment script
   - ⬜ Enable/Disable maintenance mode (اختياري)
   - ⬜ Install package.json dependencies (غير مطلوب للـ API)

**4. اضغط Submit**

---

### 5️⃣ إعداد GitHub Webhook (للـ Auto Deploy)

#### في GitHub Repository:

1. اذهب إلى: **Settings** → **Webhooks**

2. **Add webhook**

3. **Payload URL:**
   ```
   https://65-159-66-148.host.secureserver.net:8443/modules/git/public/web-hook.php?uuid=16a7feec-5a60-224d-2881-8bb9e4e5607d
   ```
   (هذا الرابط اللي شفته في Plesk)

4. **Content type:**
   - `application/json`

5. **Which events:**
   - ✅ Just the push event

6. **Active:**
   - ✅ تأكد أنه مفعّل

7. **Add webhook**

---

### 6️⃣ اختبار Auto Deployment

#### على جهازك:

1. **عدّل أي ملف (اختبار):**
   ```bash
   cd c:\Users\HP\Desktop\social-media-app\SocialMediaManager\backend-laravel

   echo "// Test auto deployment" >> routes/api.php
   ```

2. **Commit و Push:**
   ```bash
   git add .
   git commit -m "Test: Auto deployment"
   git push origin main
   ```

3. **راقب Plesk:**
   - في صفحة Git في Plesk
   - راح تشوف **Deployment Status**
   - راح يبدأ Deploy تلقائياً!

4. **تحقق من الموقع:**
   ```
   https://mediapro.social/api/config
   ```

---

## 🎯 سير العمل اليومي (Workflow)

بعد الإعداد، كل ما تبي تعدل:

```bash
# 1. عدّل الملفات في VS Code أو أي محرر
# 2. في Terminal:

cd c:\Users\HP\Desktop\social-media-app\SocialMediaManager\backend-laravel

# 3. Add changes
git add .

# 4. Commit with message
git commit -m "وصف التعديل"

# 5. Push (هنا يصير Auto Deploy!)
git push origin main

# 6. انتظر 10-30 ثانية
# 7. افتح الموقع - التعديلات موجودة!
```

---

## 🔧 إذا واجهت مشاكل

### المشكلة: Deployment فشل

**الحل:**
1. افتح صفحة Git في Plesk
2. اضغط **Deploy** يدوياً
3. شوف الـ Logs - راح يعرض الخطأ
4. أرسل لي الـ Error

---

### المشكلة: Webhook ما يشتغل

**الحل:**
1. في GitHub → Settings → Webhooks
2. اضغط على الـ Webhook
3. شوف **Recent Deliveries**
4. إذا في أخطاء، تأكد من الرابط صحيح

---

### المشكلة: Permission denied

**الحل:**
شغّل عبر SSH:
```bash
cd /var/www/vhosts/mediapro.social/httpdocs
chmod -R 775 storage bootstrap/cache
chown -R $USER:psacln storage bootstrap/cache
```

---

## 📁 ملفات يجب إضافتها لـ .gitignore

تأكد من أن هذه الملفات **لا** ترفع على Git:

```
.env
.env.backup.*
vendor/
node_modules/
storage/*.key
.phpunit.result.cache
storage/logs/*.log
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*
public/storage
```

ملف [.gitignore](SocialMediaManager/backend-laravel/.gitignore) موجود بالفعل.

---

## ⚙️ إعدادات متقدمة

### Auto-rollback عند الفشل:

أضف هذا في بداية الـ Deployment Script:

```bash
#!/bin/bash
set -e  # Exit on error

# Backup current state
BACKUP_DIR="/var/www/vhosts/mediapro.social/backups"
mkdir -p $BACKUP_DIR
tar -czf $BACKUP_DIR/backup-$(date +%Y%m%d-%H%M%S).tar.gz \
    --exclude='vendor' \
    --exclude='node_modules' \
    --exclude='storage/logs' \
    /var/www/vhosts/mediapro.social/httpdocs

# Continue with deployment...
```

---

### Notifications على Discord/Slack:

أضف في نهاية السكريبت:

```bash
# Send notification
curl -X POST -H 'Content-type: application/json' \
--data '{"text":"✓ MediaPro deployed successfully!"}' \
YOUR_WEBHOOK_URL
```

---

## 📊 مراقبة Deployments

في Plesk، صفحة Git:
- **Deployment History** - تشوف كل الـ deployments
- **Logs** - تشوف تفاصيل كل deployment
- **Status** - هل نجح أو فشل

---

## 🎯 الخلاصة

بعد الإعداد:
1. ✅ تعدل على جهازك
2. ✅ `git push` فقط
3. ✅ كل شي يرفع تلقائياً
4. ✅ Laravel commands تشتغل تلقائياً
5. ✅ الموقع يتحدث فوراً

**لا تحتاج:**
- ❌ FTP/SFTP
- ❌ رفع يدوي
- ❌ SSH commands كل مرة
- ❌ مسح Cache يدوياً

---

## 📞 إذا احتجت مساعدة

أرسل لي:
1. لقطة شاشة من صفحة Git في Plesk
2. Deployment Logs (إذا في خطأ)
3. GitHub Webhook Delivery logs

---

**ابدأ الآن بالخطوة 1️⃣!** 🚀
