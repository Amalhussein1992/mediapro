╔══════════════════════════════════════════════════════════════╗
║                                                              ║
║        MediaPro - دليل الرفع السريع على Plesk               ║
║                                                              ║
╚══════════════════════════════════════════════════════════════╝

📌 الملفات الجاهزة لك:
───────────────────────────────────────────────────────────────
✅ upload-simple.bat        → سكريبت تجهيز الملفات
✅ .env.server              → ملف إعدادات الإنتاج
✅ public/test.php          → صفحة تشخيص السيرفر
✅ FIX_500_ERROR_AR.md      → دليل حل الأخطاء
✅ ابدأ_هنا_PLESK.md         → الدليل الشامل


🚀 الرفع السريع (3 خطوات فقط):
───────────────────────────────────────────────────────────────

الخطوة 1️⃣: تجهيز الملفات
   ┗━━━> شغّل: upload-simple.bat
   ┗━━━> انتظر حتى يتم إنشاء: mediapro-upload.zip


الخطوة 2️⃣: الرفع على Plesk
   1. افتح Plesk Panel
   2. File Manager → httpdocs
   3. Upload → mediapro-upload.zip
   4. Extract Files (فك الضغط)

   5. Hosting Settings:
      Document Root: httpdocs/public ✓


الخطوة 3️⃣: التفعيل (عبر SSH Terminal)
   ┗━━━> cd /var/www/vhosts/mediapro.social/httpdocs
   ┗━━━> composer install --no-dev --optimize-autoloader
   ┗━━━> php artisan migrate --force
   ┗━━━> php artisan optimize
   ┗━━━> chmod -R 775 storage bootstrap/cache


✅ اختبار الموقع:
───────────────────────────────────────────────────────────────
🔗 https://mediapro.social/test.php  → صفحة التشخيص
🔗 https://mediapro.social/api/config → اختبار API


⚠️ حل خطأ 500:
───────────────────────────────────────────────────────────────
1. تأكد من Document Root: httpdocs/public
2. تأكد من ملف .env موجود
3. شغّل: php artisan config:clear
4. شغّل: chmod -R 775 storage bootstrap/cache


🔧 للتعديل المباشر مستقبلاً:
───────────────────────────────────────────────────────────────
→ Plesk File Manager → Edit الملف مباشرة
→ بعد التعديل: php artisan config:clear


📚 لمزيد من التفاصيل:
───────────────────────────────────────────────────────────────
افتح: ابدأ_هنا_PLESK.md


╔══════════════════════════════════════════════════════════════╗
║                    🎯 ابدأ الآن!                            ║
║                                                              ║
║  اضغط على ملف: upload-simple.bat                           ║
║  ثم اتبع التعليمات على الشاشة                             ║
║                                                              ║
╚══════════════════════════════════════════════════════════════╝
