# 📦 دليل ضغط وتحضير الباك إند للرفع على السيرفر

## ✨ الطريقة السريعة (الموصى بها)

### استخدام PowerShell Script:

1. **افتح PowerShell في مجلد backend-laravel:**
   ```powershell
   # انقر بالزر الأيمن على مجلد backend-laravel
   # اختر "Open in Terminal" أو "Open PowerShell window here"
   ```

2. **شغّل الـ Script:**
   ```powershell
   .\create-clean-zip.ps1
   ```

3. **✅ تم!** سيتم إنشاء ملف:
   ```
   backend-laravel-deployment-YYYY-MM-DD_HHMMSS.zip
   ```

**الملف سيكون نظيفاً تماماً بدون:**
- ❌ node_modules
- ❌ vendor
- ❌ .git
- ❌ .env.backup.*
- ❌ storage/logs/*.log

---

## 🛠️ الطريقة اليدوية (إذا لم تعمل الـ Script)

### الخطوة 1: حذف المجلدات الكبيرة مؤقتاً

```bash
cd c:\Users\HP\Desktop\social-media-app\SocialMediaManager\backend-laravel

# احذف (سيتم إعادة تثبيتها على السيرفر):
rmdir /s /q node_modules
rmdir /s /q vendor

# احذف ملفات backup:
del .env.backup.*

# احذف logs:
del storage\logs\*.log
```

### الخطوة 2: ضغط المجلد

**باستخدام Windows (مدمج):**
1. انقر بالزر الأيمن على مجلد `backend-laravel`
2. اختر **Send to** → **Compressed (zipped) folder**
3. أعد تسمية الملف إلى: `backend-laravel-deployment.zip`

**باستخدام 7-Zip (إذا مثبت):**
1. انقر بالزر الأيمن على مجلد `backend-laravel`
2. اختر **7-Zip** → **Add to archive**
3. الاسم: `backend-laravel-deployment.zip`
4. اضغط **OK**

### الخطوة 3: إعادة تثبيت ما تم حذفه (للاستمرار في التطوير محلياً)

```bash
# إعادة تثبيت vendor
composer install

# إعادة تثبيت node_modules (إذا كان موجود package.json)
npm install
```

---

## 📋 ما سيتم تضمينه في الملف المضغوط

✅ **سيتم تضمينه:**
```
app/                  - كل ملفات التطبيق
bootstrap/            - Bootstrap files
config/               - Configuration
database/             - Migrations & Seeders
public/               - Public assets
resources/            - Views & Assets
routes/               - Route files
storage/              - Storage (بدون logs)
.env.example          - Environment example
.htaccess             - Apache config
artisan               - Laravel CLI
composer.json         - Dependencies
composer.lock         - Lock file
```

❌ **لن يتم تضمينه:**
```
node_modules/         - سيتم تثبيته على السيرفر
vendor/               - سيتم تثبيته على السيرفر
.git/                 - غير مطلوب
.env.backup.*         - ملفات backup قديمة
storage/logs/*.log    - ملفات logs
```

---

## 🚀 بعد إنشاء الملف المضغوط

### 1. تحقق من حجم الملف:
```
يجب أن يكون: 5-20 MB تقريباً
إذا كان > 50 MB، ربما تم تضمين node_modules أو vendor!
```

### 2. ارفع على السيرفر:
1. اذهب إلى: https://148.66.159.65:8443/smb/web/add-domain
2. اختر **"Upload files - From a local machine"**
3. ارفع `backend-laravel-deployment.zip`
4. فك الضغط على السيرفر

### 3. على السيرفر، نفّذ:
```bash
# فك الضغط
unzip backend-laravel-deployment.zip

# انتقل للمجلد
cd backend-laravel

# ثبّت dependencies
composer install --no-dev --optimize-autoloader

# باقي الخطوات موجودة في الدليل الكامل
```

---

## ⚠️ ملاحظات مهمة

1. **لا تنسَ تحديث `.env` على السيرفر** بمعلومات قاعدة البيانات الصحيحة

2. **تأكد من وجود `composer.json`** في الملف المضغوط

3. **لا تضمّن `.env` الحقيقي** - فقط `.env.example`

4. **احتفظ بنسخة احتياطية محلية** من المشروع قبل الحذف

---

## 🐛 حل المشاكل

### المشكلة: "الملف كبير جداً (> 100 MB)"
**الحل:** تحقق أنك حذفت `node_modules` و `vendor`

### المشكلة: "PowerShell script لا يعمل"
**الحل:**
```powershell
# فعّل تنفيذ scripts
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser

# ثم أعد المحاولة
.\create-clean-zip.ps1
```

### المشكلة: "Access Denied عند الحذف"
**الحل:** أغلق VSCode أو أي editor مفتوح على المجلد ثم أعد المحاولة

---

## 📞 المساعدة

إذا واجهت أي مشكلة:
1. راجع [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md) للدليل الكامل
2. تأكد من إغلاق جميع البرامج المفتوحة على المجلد
3. استخدم الطريقة اليدوية إذا لم تعمل الـ Script

---

**تم إنشاؤه:** 2025-10-18
**الإصدار:** 1.0.0
