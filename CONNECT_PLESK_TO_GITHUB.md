# 🔗 ربط Plesk بـ GitHub Repository الموجود

## المعلومات الحالية ✅

- **Repository:** `git@github.com:Amalhussein1992/mediapro.git`
- **HTTPS URL:** `https://github.com/Amalhussein1992/mediapro.git`
- **Owner:** Amalhussein1992
- **Repo Name:** mediapro

---

## الخطوات الكاملة

### 1️⃣ إعداد Git في Plesk

#### في Plesk Panel:

1. **اذهب إلى:**
   ```
   Websites & Domains → mediapro.social → Git
   ```

2. **إذا كان Repository مربوط بالفعل:**
   - اضغط **Manage domain** (شايفه في الصورة اللي أرسلتها)
   - تحقق من الإعدادات

3. **إذا لم يكن مربوط:**
   - اضغط **Add Repository** أو **Pull Updates**

---

### 2️⃣ إعداد Git Repository في Plesk

#### الطريقة 1: استخدام HTTPS (الأسهل)

في صفحة Git Settings:

```
Repository URL: https://github.com/Amalhussein1992/mediapro.git
Repository Path: /httpdocs
Branch: main
```

**Authentication:**
- Username: `Amalhussein1992`
- Password/Token: **Personal Access Token** من GitHub

**كيف تحصل على Personal Access Token:**

1. اذهب إلى GitHub: https://github.com/settings/tokens
2. **Developer settings** → **Personal access tokens** → **Tokens (classic)**
3. **Generate new token** → **Generate new token (classic)**
4. اسم التوكن: `Plesk mediapro.social`
5. فعّل الصلاحيات:
   - ✅ `repo` (كل الصلاحيات)
   - ✅ `admin:repo_hook`
6. **Generate token**
7. **انسخ التوكن** (يظهر مرة واحدة فقط!)
8. استخدمه كـ Password في Plesk

---

#### الطريقة 2: استخدام SSH Key (أفضل للأمان)

**الخطوة 1: توليد SSH Key في Plesk**

عبر SSH Terminal في Plesk:

```bash
# Login to server
cd ~/.ssh

# Generate SSH key
ssh-keygen -t ed25519 -C "plesk-mediapro@mediapro.social"

# Press Enter for default location
# Press Enter twice for no passphrase

# Copy the public key
cat ~/.ssh/id_ed25519.pub
```

**الخطوة 2: إضافة SSH Key في GitHub**

1. انسخ محتوى `id_ed25519.pub`
2. اذهب إلى GitHub: https://github.com/Amalhussein1992/mediapro/settings/keys
3. **Deploy keys** → **Add deploy key**
4. Title: `Plesk mediapro.social`
5. Key: الصق المفتاح العام
6. ✅ **Allow write access** (إذا كنت تريد Push من السيرفر)
7. **Add key**

**الخطوة 3: استخدام SSH URL في Plesk**

```
Repository URL: git@github.com:Amalhussein1992/mediapro.git
Repository Path: /httpdocs
Branch: main
```

---

### 3️⃣ تحديث Deployment Settings

تأكد من:

**Deployment mode:**
- ✅ **Automatic**

**Deployment steps:**
```
⬜ 1. Enable maintenance mode
✅ 2. Fetch source code
✅ 3. Deploy source code from Git
✅ 4. Install composer.json dependencies
⬜ 5. Install package.json dependencies  (غير مطلوب للباك اند)
✅ 6. Run deployment script
⬜ 7. Disable maintenance mode
```

**Deployment script:** (اضغط Edit script)

```bash
#!/bin/bash
set -e

echo "========================================="
echo "MediaPro - Deployment Started"
echo "========================================="

cd /var/www/vhosts/mediapro.social/httpdocs

# Install dependencies
echo "[1/8] Installing Composer dependencies..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Setup .env
echo "[2/8] Environment setup..."
if [ ! -f ".env" ]; then
    if [ -f ".env.server" ]; then
        cp .env.server .env
        echo "✓ Created .env"
    fi
fi

# Clear caches
echo "[3/8] Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Migrations
echo "[4/8] Running migrations..."
php artisan migrate --force

# Build caches
echo "[5/8] Building caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage link
echo "[6/8] Storage link..."
php artisan storage:link || true

# Permissions
echo "[7/8] Fixing permissions..."
chmod -R 775 storage bootstrap/cache
chown -R $USER:psacln storage bootstrap/cache || true

# Verify
echo "[8/8] Verification..."
php artisan --version

echo "========================================="
echo "✓ Deployment Complete!"
echo "Time: $(date)"
echo "========================================="
```

---

### 4️⃣ إعداد GitHub Webhook

#### في GitHub Repository:

1. اذهب إلى: https://github.com/Amalhussein1992/mediapro/settings/hooks

2. **Add webhook**

3. **Payload URL:**
   ```
   https://65-159-66-148.host.secureserver.net:8443/modules/git/public/web-hook.php?uuid=16a7feec-5a60-224d-2881-8bb9e4e5607d
   ```

4. **Content type:**
   ```
   application/json
   ```

5. **Which events would you like to trigger this webhook?**
   - ⚪ Just the push event (اختر هذا)

6. **Active:**
   - ✅ تأكد أنه مفعّل

7. **Add webhook**

---

### 5️⃣ اختبار Auto Deployment

#### على جهازك:

```bash
cd c:\Users\HP\Desktop\social-media-app\SocialMediaManager\backend-laravel

# عدّل ملف بسيط (اختبار)
echo "// Auto deployment test" >> routes/api.php

# Commit
git add .
git commit -m "Test: Auto deployment from Plesk"

# Push
git push origin main
```

#### راقب Plesk:

1. اذهب إلى: **Websites & Domains** → **mediapro.social** → **Git**
2. شوف **Deployment Status**
3. يجب أن يبدأ تلقائياً خلال 10-30 ثانية
4. اضغط **Logs** لمشاهدة التفاصيل

---

### 6️⃣ التحقق من النجاح

**افتح في المتصفح:**

```
https://mediapro.social/test.php
https://mediapro.social/api/config
```

يجب أن يعرض المعلومات بشكل صحيح.

---

## 🔧 حل المشاكل

### المشكلة: Authentication failed

**الحل 1 - HTTPS:**
- تأكد من Personal Access Token صحيح
- تأكد من الصلاحيات: `repo` و `admin:repo_hook`

**الحل 2 - SSH:**
- تأكد من إضافة SSH Key في GitHub Deploy keys
- تأكد من تفعيل "Allow write access"

---

### المشكلة: Webhook لا يعمل

**الحل:**

1. في GitHub → Webhooks → اضغط على الـ Webhook
2. شوف **Recent Deliveries**
3. إذا في أخطاء:
   - تأكد من الرابط صحيح
   - تأكد من `uuid` نفس اللي في Plesk
4. اضغط **Redeliver** للإعادة

---

### المشكلة: Deployment يفشل

**الحل:**

1. في Plesk Git → **Logs**
2. اقرأ رسالة الخطأ
3. الأخطاء الشائعة:
   - **Composer install failed:** تحقق من `composer.json`
   - **Migration failed:** تحقق من `.env` database settings
   - **Permission denied:** شغّل عبر SSH:
     ```bash
     chmod -R 775 /var/www/vhosts/mediapro.social/httpdocs/storage
     chmod -R 775 /var/www/vhosts/mediapro.social/httpdocs/bootstrap/cache
     ```

---

## 📝 ملاحظات مهمة

### ملفات .gitignore

تأكد من عدم رفع هذه الملفات:

```
.env              ← يحتوي معلومات حساسة
vendor/           ← يُثبّت عبر composer
node_modules/     ← يُثبّت عبر npm
storage/logs/*.log
.phpunit.result.cache
```

ملف `.gitignore` موجود بالفعل ويحمي هذه الملفات.

---

### هيكل المشروع على السيرفر

```
/var/www/vhosts/mediapro.social/
├── httpdocs/              ← Git repository هنا
│   ├── app/
│   ├── config/
│   ├── public/            ← Document Root يشير هنا
│   ├── vendor/            ← يُنشأ بواسطة composer
│   ├── .env               ← ينسخ من .env.server
│   └── ...
└── logs/
```

---

## 🎯 سير العمل اليومي

بعد الإعداد الكامل:

```bash
# 1. عدّل الكود على جهازك

# 2. Commit و Push
git add .
git commit -m "وصف التعديل"
git push origin main

# 3. انتظر 10-30 ثانية

# 4. ✓ الموقع يتحدث تلقائياً!
```

---

## 📚 الملفات المساعدة

- **plesk-deployment-script.sh** - السكريبت الكامل
- **GIT_AUTO_DEPLOYMENT_AR.md** - الدليل الشامل
- **QUICK_GIT_SETUP_AR.txt** - الإعداد السريع

---

## ✅ Checklist

- [ ] Repository مربوط في Plesk
- [ ] Authentication تعمل (HTTPS Token أو SSH Key)
- [ ] Deployment mode = Automatic
- [ ] Deployment script محدّث
- [ ] GitHub Webhook مضاف
- [ ] اختبار deployment نجح
- [ ] الموقع يعمل: https://mediapro.social

---

**جاهز للبدء؟** 🚀

ابدأ بالخطوة 1: إعداد Authentication (HTTPS Token أو SSH Key)
