# دليل ربط الأدوات والخدمات - Media Pro

## 📋 جدول المحتويات
1. [تسجيل الدخول بجوجل وأبل](#تسجيل-الدخول-بجوجل-وأبل)
2. [خدمات الذكاء الاصطناعي](#خدمات-الذكاء-الاصطناعي)
3. [خدمات الدفع](#خدمات-الدفع)
4. [منصات التواصل الاجتماعي](#منصات-التواصل-الاجتماعي)
5. [خدمات التخزين السحابي](#خدمات-التخزين-السحابي)

---

## 🔐 تسجيل الدخول بجوجل وأبل

### 1. Google OAuth

#### الحصول على المفاتيح:
1. اذهب إلى [Google Cloud Console](https://console.cloud.google.com/)
2. أنشئ مشروع جديد أو اختر مشروع موجود
3. من القائمة، اختر "APIs & Services" > "Credentials"
4. انقر على "+ CREATE CREDENTIALS" > "OAuth client ID"
5. اختر نوع التطبيق:
   - **Web application** (للموقع)
   - **iOS** (لتطبيق iOS)
   - **Android** (لتطبيق Android)
6. أضف Authorized redirect URIs:
   ```
   http://localhost:8000/auth/google/callback
   https://yourdomain.com/auth/google/callback
   ```
7. احصل على:
   - Client ID
   - Client Secret

#### إضافة المفاتيح في `.env`:
```env
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

#### API Endpoints:
```
# للويب (Web Flow)
GET  /api/auth/google           - الحصول على رابط تسجيل الدخول
GET  /api/auth/google/callback  - معالجة العودة من جوجل

# للموبايل (Token Flow)
POST /api/auth/google/token     - تسجيل الدخول باستخدام Google ID Token
Body: { "id_token": "..." }
```

---

### 2. Apple OAuth

#### الحصول على المفاتيح:
1. اذهب إلى [Apple Developer Portal](https://developer.apple.com/account/)
2. من القائمة، اختر "Certificates, IDs & Profiles"
3. اذهب إلى "Identifiers" وأنشئ App ID جديد
4. فعّل "Sign In with Apple" capability
5. أنشئ Service ID للويب
6. أنشئ مفتاح (Key) من نوع "Sign in with Apple"
7. احصل على:
   - Client ID (Service ID)
   - Team ID
   - Key ID
   - Private Key (.p8 file)

#### إضافة المفاتيح في `.env`:
```env
APPLE_CLIENT_ID=com.yourapp.service
APPLE_TEAM_ID=XXXXXXXXXX
APPLE_KEY_ID=XXXXXXXXXX
APPLE_PRIVATE_KEY=path/to/AuthKey_XXXXXXXXXX.p8
APPLE_REDIRECT_URI=http://localhost:8000/auth/apple/callback
```

#### API Endpoints:
```
# للويب (Web Flow)
GET  /api/auth/apple           - الحصول على رابط تسجيل الدخول
GET  /api/auth/apple/callback  - معالجة العودة من أبل

# للموبايل (Token Flow)
POST /api/auth/apple/token     - تسجيل الدخول باستخدام Apple ID Token
Body: { "id_token": "...", "user": "{...}" }
```

---

## 🤖 خدمات الذكاء الاصطناعي

### 1. OpenAI (GPT-4, DALL-E, Whisper)

#### الحصول على API Key:
1. اذهب إلى [OpenAI Platform](https://platform.openai.com/)
2. سجّل دخول أو أنشئ حساب
3. اذهب إلى [API Keys](https://platform.openai.com/api-keys)
4. انقر على "+ Create new secret key"
5. احفظ المفتاح (لن تستطيع رؤيته مرة أخرى)

#### إضافة المفتاح في `.env`:
```env
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxxxxxxxxxxxxx
OPENAI_MODEL=gpt-4o-mini
OPENAI_MAX_TOKENS=2000
```

#### الاستخدامات:
- توليد محتوى النصوص
- إنشاء الهاشتاقات
- تحسين النصوص
- تحويل الصوت إلى نص (Whisper)
- توليد الصور (DALL-E)

---

### 2. Google Gemini

#### الحصول على API Key:
1. اذهب إلى [Google AI Studio](https://makersuite.google.com/app/apikey)
2. انقر على "Get API Key"
3. اختر أو أنشئ مشروع Google Cloud
4. احصل على المفتاح

#### إضافة المفتاح في `.env`:
```env
GEMINI_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
GEMINI_MODEL=gemini-1.5-flash
```

#### الاستخدامات:
- توليد محتوى متعدد اللغات
- تحليل الصور
- المحادثات الذكية

---

### 3. Anthropic Claude

#### الحصول على API Key:
1. اذهب إلى [Anthropic Console](https://console.anthropic.com/)
2. أنشئ حساب أو سجّل دخول
3. اذهب إلى [API Keys](https://console.anthropic.com/settings/keys)
4. انقر على "Create Key"
5. احفظ المفتاح

#### إضافة المفتاح في `.env`:
```env
CLAUDE_API_KEY=sk-ant-api03-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
CLAUDE_MODEL=claude-3-5-sonnet-20241022
CLAUDE_MAX_TOKENS=4096
```

#### الاستخدامات:
- توليد محتوى احترافي
- تحليل النصوص الطويلة
- المساعدة في الكتابة

---

## 💳 خدمات الدفع

### 1. Stripe

#### الحصول على المفاتيح:
1. اذهب إلى [Stripe Dashboard](https://dashboard.stripe.com/)
2. أنشئ حساب أو سجّل دخول
3. من القائمة، اختر "Developers" > "API keys"
4. احصل على:
   - Publishable key (للواجهة الأمامية)
   - Secret key (للباك إند)
5. للـ Webhooks:
   - اذهب إلى "Developers" > "Webhooks"
   - أضف endpoint: `https://yourdomain.com/api/webhooks/stripe`
   - احصل على Webhook Secret

#### إضافة المفاتيح في `.env`:
```env
STRIPE_KEY=pk_test_xxxxxxxxxxxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxxxxxxxxxxxx
```

#### API Endpoint:
```
POST /api/payments/stripe/create-payment-intent
POST /api/webhooks/stripe  (للتحديثات التلقائية)
```

---

### 2. PayPal

#### الحصول على المفاتيح:
1. اذهب إلى [PayPal Developer](https://developer.paypal.com/)
2. سجّل دخول
3. اذهب إلى "Dashboard" > "My Apps & Credentials"
4. أنشئ تطبيق جديد (Sandbox للتجربة، Live للإنتاج)
5. احصل على:
   - Client ID
   - Secret

#### إضافة المفاتيح في `.env`:
```env
PAYPAL_MODE=sandbox  # أو live للإنتاج
PAYPAL_CLIENT_ID=xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
PAYPAL_SECRET=xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

---

## 📱 منصات التواصل الاجتماعي

### 1. Facebook & Instagram (نفس التطبيق)

#### الحصول على المفاتيح:
1. اذهب إلى [Meta for Developers](https://developers.facebook.com/)
2. أنشئ تطبيق جديد من نوع "Business"
3. أضف منتجات: Facebook Login, Instagram Basic Display
4. من "Settings" > "Basic"، احصل على:
   - App ID
   - App Secret
5. أضف Valid OAuth Redirect URIs

#### إضافة المفاتيح في `.env`:
```env
FACEBOOK_CLIENT_ID=your_app_id
FACEBOOK_CLIENT_SECRET=your_app_secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/oauth/facebook/callback

INSTAGRAM_CLIENT_ID=same_as_facebook
INSTAGRAM_CLIENT_SECRET=same_as_facebook
INSTAGRAM_REDIRECT_URI=http://localhost:8000/oauth/instagram/callback
```

---

### 2. Twitter/X

#### الحصول على المفاتيح:
1. اذهب إلى [Twitter Developer Portal](https://developer.twitter.com/en/portal/dashboard)
2. أنشئ مشروع وتطبيق
3. من "Keys and tokens"، احصل على:
   - API Key (Client ID)
   - API Secret Key (Client Secret)
4. فعّل OAuth 2.0

#### إضافة المفاتيح في `.env`:
```env
TWITTER_CLIENT_ID=your_api_key
TWITTER_CLIENT_SECRET=your_api_secret
TWITTER_REDIRECT_URI=http://localhost:8000/oauth/twitter/callback
```

---

### 3. LinkedIn

#### الحصول على المفاتيح:
1. اذهب إلى [LinkedIn Developers](https://www.linkedin.com/developers/)
2. أنشئ تطبيق جديد
3. من "Auth" tab، احصل على:
   - Client ID
   - Client Secret
4. أضف Redirect URLs

#### إضافة المفاتيح في `.env`:
```env
LINKEDIN_CLIENT_ID=xxxxxxxxxxxxxx
LINKEDIN_CLIENT_SECRET=xxxxxxxxxxxxxxxxxxxxxxxx
LINKEDIN_REDIRECT_URI=http://localhost:8000/oauth/linkedin/callback
```

---

### 4. TikTok

#### الحصول على المفاتيح:
1. اذهب إلى [TikTok for Developers](https://developers.tiktok.com/)
2. سجّل كمطور
3. أنشئ تطبيق جديد
4. احصل على:
   - Client Key
   - Client Secret

#### إضافة المفاتيح في `.env`:
```env
TIKTOK_CLIENT_ID=your_client_key
TIKTOK_CLIENT_SECRET=your_client_secret
TIKTOK_REDIRECT_URI=http://localhost:8000/oauth/tiktok/callback
```

---

### 5. YouTube (Google)

#### الحصول على المفاتيح:
1. نفس خطوات Google OAuth (أعلاه)
2. فعّل YouTube Data API v3
3. استخدم نفس OAuth Credentials

#### إضافة المفاتيح في `.env`:
```env
YOUTUBE_CLIENT_ID=same_as_google
YOUTUBE_CLIENT_SECRET=same_as_google
YOUTUBE_REDIRECT_URI=http://localhost:8000/oauth/youtube/callback
```

---

### 6. Pinterest

#### الحصول على المفاتيح:
1. اذهب إلى [Pinterest Developers](https://developers.pinterest.com/)
2. أنشئ تطبيق جديد
3. احصل على:
   - App ID
   - App Secret

#### إضافة المفاتيح في `.env`:
```env
PINTEREST_CLIENT_ID=xxxxxxxxx
PINTEREST_CLIENT_SECRET=xxxxxxxxxxxxxxxxxxxxxxxx
PINTEREST_REDIRECT_URI=http://localhost:8000/oauth/pinterest/callback
```

---

## ☁️ خدمات التخزين السحابي

### AWS S3 (لتخزين الصور والفيديوهات)

#### الحصول على المفاتيح:
1. اذهب إلى [AWS Console](https://console.aws.amazon.com/)
2. اذهب إلى "IAM" > "Users"
3. أنشئ مستخدم جديد مع "Programmatic access"
4. أضف سياسة "AmazonS3FullAccess"
5. احصل على:
   - Access Key ID
   - Secret Access Key
6. أنشئ S3 Bucket من [S3 Console](https://s3.console.aws.amazon.com/)

#### إضافة المفاتيح في `.env`:
```env
AWS_ACCESS_KEY_ID=AKIAXXXXXXXXXXXXXXXXX
AWS_SECRET_ACCESS_KEY=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
AWS_USE_PATH_STYLE_ENDPOINT=false
```

---

## 🧪 اختبار الربط

### اختبار Google OAuth:
```bash
# Web Flow
curl http://localhost:8000/api/auth/google

# Mobile Flow
curl -X POST http://localhost:8000/api/auth/google/token \
  -H "Content-Type: application/json" \
  -d '{"id_token":"YOUR_GOOGLE_ID_TOKEN"}'
```

### اختبار Apple OAuth:
```bash
# Web Flow
curl http://localhost:8000/api/auth/apple

# Mobile Flow
curl -X POST http://localhost:8000/api/auth/apple/token \
  -H "Content-Type: application/json" \
  -d '{"id_token":"YOUR_APPLE_ID_TOKEN"}'
```

### اختبار AI Services:
```bash
curl -X POST http://localhost:8000/api/ai/generate \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"prompt":"اكتب منشور عن..."}'
```

---

## 📝 ملاحظات مهمة

### الأمان:
- ✅ لا تشارك مفاتيح API أبداً
- ✅ استخدم `.env` ولا ترفعه على Git
- ✅ استخدم مفاتيح Test في التطوير
- ✅ استخدم مفاتيح Production فقط على السيرفر

### التكاليف:
- معظم الخدمات لديها خطط مجانية محدودة
- راقب استخدامك لتجنب التكاليف الزائدة
- استخدم Rate Limiting لحماية حسابك

### الدعم:
إذا واجهت أي مشاكل:
1. تأكد من صحة المفاتيح
2. تحقق من Redirect URIs
3. راجع سجلات الأخطاء: `storage/logs/laravel.log`

---

## 🔗 روابط مفيدة

- [Laravel Socialite](https://laravel.com/docs/11.x/socialite)
- [Stripe Documentation](https://stripe.com/docs)
- [OpenAI API Reference](https://platform.openai.com/docs)
- [Google OAuth 2.0](https://developers.google.com/identity/protocols/oauth2)
- [Apple Sign In](https://developer.apple.com/sign-in-with-apple/)

---

**تم بنجاح! جميع الأدوات جاهزة للربط 🎉**
