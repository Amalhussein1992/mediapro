# 🔗 دليل إعداد ربط حسابات السوشال ميديا
# Social Media OAuth Setup Guide

## 📋 المحتوى | Table of Contents

1. [نظرة عامة | Overview](#overview)
2. [Facebook & Instagram Setup](#facebook-instagram)
3. [Twitter/X Setup](#twitter)
4. [LinkedIn Setup](#linkedin)
5. [TikTok Setup](#tiktok)
6. [إعداد البيئة | Environment Configuration](#environment)
7. [اختبار الربط | Testing Connection](#testing)

---

## 🎯 نظرة عامة | Overview {#overview}

### ✅ What's Already Done

**Backend Complete:**
- ✅ `SocialMediaOAuthController.php` - Complete OAuth implementation
- ✅ API Routes configured in `routes/api.php`
- ✅ Support for 5 platforms: Facebook, Instagram, Twitter, LinkedIn, TikTok
- ✅ Access token refresh mechanism
- ✅ Account disconnection with token revocation

**Frontend Complete:**
- ✅ `socialAuth.service.ts` - OAuth service
- ✅ `socialMediaService.ts` - Platform integration
- ✅ All screens ready to connect accounts

### 🔧 What You Need to Do

Create developer accounts and get API credentials for each platform you want to support.

**Estimated Time:**
- Facebook: 15 minutes
- Instagram: 5 minutes (via Facebook)
- Twitter: 20 minutes
- LinkedIn: 15 minutes
- TikTok: 30 minutes

---

## 1️⃣ Facebook & Instagram Setup {#facebook-instagram}

### 📱 Why Facebook?

Facebook Graph API provides access to both:
- Facebook Pages (للنشر على الصفحات)
- Instagram Business Accounts (للنشر على الإنستجرام)

### Step 1: Create Facebook App

1. **Go to Facebook Developers:**
   - Visit: https://developers.facebook.com/
   - Click "My Apps" → "Create App"

2. **Select App Type:**
   - Choose: **"Business"**
   - App Name: `MediaPro Social Manager`
   - App Contact Email: Your email
   - Click "Create App"

3. **Add Facebook Login Product:**
   - In Dashboard, click "Add Product"
   - Find "Facebook Login" → Click "Set Up"

### Step 2: Configure OAuth Settings

1. **Go to Settings → Basic:**
   - Copy `App ID` (سنستخدمه في .env)
   - Copy `App Secret` (سنستخدمه في .env)
   - Add App Domains: `mediapro.social`

2. **Go to Facebook Login → Settings:**
   - Add Valid OAuth Redirect URIs:
   ```
   https://www.mediapro.social/api/social-oauth/callback
   http://localhost:8000/api/social-oauth/callback
   ```

### Step 3: Add Instagram Product

1. **In Dashboard:**
   - Click "Add Product"
   - Find "Instagram" → Click "Set Up"

2. **Configure Permissions:**
   - In "App Review" → "Permissions and Features"
   - Request these permissions:
     - ✅ `pages_show_list` - View pages
     - ✅ `pages_read_engagement` - Read engagement
     - ✅ `pages_manage_posts` - Create posts
     - ✅ `instagram_basic` - Basic Instagram access
     - ✅ `instagram_content_publish` - Publish to Instagram

### Step 4: Connect Instagram Business Account

**Important:** Instagram API requires a **Business Account** connected to a **Facebook Page**.

1. **Convert to Business Account:**
   - Open Instagram app
   - Go to Settings → Account
   - Tap "Switch to Professional Account"
   - Choose "Business"

2. **Connect to Facebook Page:**
   - In Instagram Settings → Account
   - Tap "Linked Accounts" → "Facebook"
   - Connect your Facebook Page

### Step 5: Test Permissions

1. **Use Graph API Explorer:**
   - Visit: https://developers.facebook.com/tools/explorer/
   - Select your app
   - Generate User Token with all permissions
   - Test query:
   ```
   GET /me/accounts
   ```
   - Should return your Facebook Pages

2. **Test Instagram Access:**
   ```
   GET /{page-id}?fields=instagram_business_account
   ```
   - Should return Instagram account ID

### 📝 Facebook Credentials Summary

```env
FACEBOOK_CLIENT_ID=your_app_id_here
FACEBOOK_CLIENT_SECRET=your_app_secret_here
FACEBOOK_REDIRECT_URI=https://www.mediapro.social/api/social-oauth/callback
```

### ✅ Facebook Checklist

- [ ] Facebook App created
- [ ] App ID and App Secret copied
- [ ] OAuth redirect URIs configured
- [ ] Instagram product added
- [ ] Instagram Business Account connected to Facebook Page
- [ ] Permissions requested and approved
- [ ] Test token works with Graph API Explorer

---

## 2️⃣ Twitter/X Setup {#twitter}

### 📱 Twitter API v2 with OAuth 2.0

### Step 1: Create Twitter Developer Account

1. **Apply for Developer Account:**
   - Visit: https://developer.twitter.com/
   - Click "Sign up"
   - Fill application form (takes 5-10 minutes)
   - Purpose: "Social Media Management Tool"

2. **Wait for Approval:**
   - Usually instant for basic access
   - Check email for confirmation

### Step 2: Create Twitter App

1. **Go to Developer Portal:**
   - Visit: https://developer.twitter.com/en/portal/dashboard
   - Click "Create Project"
   - Project Name: `MediaPro Social`

2. **Create App:**
   - App Name: `MediaPro App`
   - Environment: Production
   - Click "Create"

### Step 3: Configure OAuth 2.0

1. **Go to App Settings:**
   - Click on your app
   - Go to "Keys and tokens" tab

2. **Generate OAuth 2.0 Credentials:**
   - Find "OAuth 2.0 Client ID and Client Secret"
   - Click "Generate"
   - Copy `Client ID` (سنستخدمه في .env)
   - Copy `Client Secret` (سنستخدمه في .env)
   - ⚠️ **Important:** Save the secret now, you can't see it again!

3. **Configure Callback URL:**
   - Go to "User authentication settings"
   - Click "Set up"
   - Type of App: **"Web App"**
   - Callback URI:
   ```
   https://www.mediapro.social/api/social-oauth/callback
   ```
   - Website URL: `https://www.mediapro.social`

4. **Select Permissions:**
   - Read and write tweets
   - Read users
   - ✅ `tweet.read`
   - ✅ `tweet.write`
   - ✅ `users.read`
   - ✅ `offline.access` (for refresh tokens)

### Step 4: Test API Access

1. **Use Postman or curl:**
   ```bash
   curl -X GET "https://api.twitter.com/2/users/me" \
     -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
   ```

### 📝 Twitter Credentials Summary

```env
TWITTER_CLIENT_ID=your_client_id_here
TWITTER_CLIENT_SECRET=your_client_secret_here
TWITTER_REDIRECT_URI=https://www.mediapro.social/api/social-oauth/callback
```

### 💰 Twitter API Pricing

- **Free Tier:** 1,500 tweets/month
- **Basic ($100/month):** 3,000 tweets/month + higher limits
- **Pro ($5,000/month):** Full access

**Recommendation:** Start with Free tier for testing.

### ✅ Twitter Checklist

- [ ] Developer account approved
- [ ] Project and App created
- [ ] OAuth 2.0 Client ID and Secret generated
- [ ] Callback URL configured
- [ ] Permissions selected (read + write)
- [ ] API tested with user endpoint

---

## 3️⃣ LinkedIn Setup {#linkedin}

### 📱 LinkedIn API v2

### Step 1: Create LinkedIn App

1. **Go to LinkedIn Developers:**
   - Visit: https://www.linkedin.com/developers/
   - Click "Create app"

2. **Fill App Details:**
   - App Name: `MediaPro Social Manager`
   - LinkedIn Page: Select or create a company page
   - Privacy Policy URL: `https://www.mediapro.social/privacy`
   - App Logo: Upload your logo (minimum 300x300px)
   - Click "Create app"

### Step 2: Configure OAuth Settings

1. **Go to Auth Tab:**
   - Copy `Client ID` (سنستخدمه في .env)
   - Copy `Client Secret` (سنستخدمه في .env)

2. **Add Redirect URLs:**
   - Click "Add redirect URL"
   - Add:
   ```
   https://www.mediapro.social/api/social-oauth/callback
   http://localhost:8000/api/social-oauth/callback
   ```

### Step 3: Request API Access

1. **Go to Products Tab:**
   - Find "Share on LinkedIn" → Click "Request access"
   - Find "Sign In with LinkedIn" → Click "Request access"
   - Fill required information
   - Wait for approval (usually instant)

2. **Required Scopes:**
   - ✅ `r_liteprofile` - Read basic profile
   - ✅ `r_emailaddress` - Read email
   - ✅ `w_member_social` - Share content

### Step 4: Verify App

1. **Go to Settings Tab:**
   - Complete app verification
   - Add app logo and description
   - Submit for review (if required)

### 📝 LinkedIn Credentials Summary

```env
LINKEDIN_CLIENT_ID=your_client_id_here
LINKEDIN_CLIENT_SECRET=your_client_secret_here
LINKEDIN_REDIRECT_URI=https://www.mediapro.social/api/social-oauth/callback
```

### ✅ LinkedIn Checklist

- [ ] App created with company page
- [ ] Client ID and Secret copied
- [ ] Redirect URLs configured
- [ ] "Share on LinkedIn" product access approved
- [ ] Required scopes available
- [ ] App logo and details completed

---

## 4️⃣ TikTok Setup {#tiktok}

### 📱 TikTok for Developers

### Step 1: Register for TikTok Developer Account

1. **Go to TikTok Developers:**
   - Visit: https://developers.tiktok.com/
   - Click "Register"
   - Log in with TikTok account

2. **Complete Profile:**
   - Verify email
   - Accept Terms of Service

### Step 2: Create TikTok App

1. **Go to My Apps:**
   - Click "Create an app"
   - App Name: `MediaPro Social Manager`
   - App Type: **"Web app"**
   - Category: "Social Media"

2. **Fill App Details:**
   - Description: Brief description of your app
   - Website: `https://www.mediapro.social`
   - Redirect URI:
   ```
   https://www.mediapro.social/api/social-oauth/callback
   ```

### Step 3: Configure Products

1. **Add Products:**
   - Select "Login Kit"
   - Select "Content Posting API"

2. **Configure Scopes:**
   - ✅ `user.info.basic` - Basic user info
   - ✅ `video.list` - List user videos
   - ✅ `video.upload` - Upload videos

3. **Get Credentials:**
   - Go to "Credentials" tab
   - Copy `Client Key` (سنستخدمه في .env)
   - Copy `Client Secret` (سنستخدمه في .env)

### Step 4: Submit for Review

1. **Prepare for Review:**
   - Add app screenshots
   - Provide detailed use case
   - Explain why you need each permission

2. **Submit:**
   - Click "Submit for review"
   - Wait for approval (can take 1-2 weeks)

### 📝 TikTok Credentials Summary

```env
TIKTOK_CLIENT_KEY=your_client_key_here
TIKTOK_CLIENT_SECRET=your_client_secret_here
TIKTOK_REDIRECT_URI=https://www.mediapro.social/api/social-oauth/callback
```

### ⚠️ TikTok Important Notes

- **Review Process:** TikTok review can take 1-2 weeks
- **Limitations:** Content Posting API has daily limits
- **Business Account:** Some features require TikTok Business account
- **Testing:** You can test with your own account during development

### ✅ TikTok Checklist

- [ ] Developer account registered
- [ ] App created
- [ ] Client Key and Secret obtained
- [ ] Redirect URI configured
- [ ] Products selected (Login Kit + Content Posting)
- [ ] Scopes configured
- [ ] App submitted for review (or approved)

---

## 5️⃣ إعداد البيئة | Environment Configuration {#environment}

### Step 1: Update `.env` File

Add all credentials to your `.env` file:

```env
# ===========================================
# SOCIAL MEDIA OAUTH CREDENTIALS
# ===========================================

# Facebook & Instagram
FACEBOOK_CLIENT_ID=your_facebook_app_id
FACEBOOK_CLIENT_SECRET=your_facebook_app_secret
FACEBOOK_REDIRECT_URI=https://www.mediapro.social/api/social-oauth/callback

# Twitter/X
TWITTER_CLIENT_ID=your_twitter_client_id
TWITTER_CLIENT_SECRET=your_twitter_client_secret
TWITTER_REDIRECT_URI=https://www.mediapro.social/api/social-oauth/callback

# LinkedIn
LINKEDIN_CLIENT_ID=your_linkedin_client_id
LINKEDIN_CLIENT_SECRET=your_linkedin_client_secret
LINKEDIN_REDIRECT_URI=https://www.mediapro.social/api/social-oauth/callback

# TikTok
TIKTOK_CLIENT_KEY=your_tiktok_client_key
TIKTOK_CLIENT_SECRET=your_tiktok_client_secret
TIKTOK_REDIRECT_URI=https://www.mediapro.social/api/social-oauth/callback
```

### Step 2: Update `config/services.php`

Add these configurations:

```php
<?php

return [
    // ... existing services

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect_uri' => env('FACEBOOK_REDIRECT_URI'),
    ],

    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect_uri' => env('TWITTER_REDIRECT_URI'),
    ],

    'linkedin' => [
        'client_id' => env('LINKEDIN_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
        'redirect_uri' => env('LINKEDIN_REDIRECT_URI'),
    ],

    'tiktok' => [
        'client_key' => env('TIKTOK_CLIENT_KEY'),
        'client_secret' => env('TIKTOK_CLIENT_SECRET'),
        'redirect_uri' => env('TIKTOK_REDIRECT_URI'),
    ],
];
```

### Step 3: Clear Laravel Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

---

## 6️⃣ اختبار الربط | Testing Connection {#testing}

### Test 1: Generate OAuth URL

**Request:**
```bash
curl -X POST https://www.mediapro.social/api/social-oauth/auth-url \
  -H "Authorization: Bearer YOUR_AUTH_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "platform": "facebook"
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "url": "https://www.facebook.com/v18.0/dialog/oauth?client_id=...",
  "platform": "facebook"
}
```

### Test 2: Handle Callback

After user authorizes, they'll be redirected to callback URL with a `code` parameter.

**Mobile App Flow:**
```typescript
// In your React Native app
import { socialAuthService } from './services/socialAuth.service';

// 1. Get OAuth URL
const { url } = await socialAuthService.getOAuthUrl('facebook');

// 2. Open WebView or browser
Linking.openURL(url);

// 3. Handle callback (extract code from redirect)
const code = 'CODE_FROM_REDIRECT';

// 4. Send code to backend
const response = await fetch('/api/social-oauth/callback', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    platform: 'facebook',
    code: code,
  }),
});
```

### Test 3: Get Connected Accounts

**Request:**
```bash
curl -X GET https://www.mediapro.social/api/social-oauth/accounts \
  -H "Authorization: Bearer YOUR_AUTH_TOKEN"
```

**Expected Response:**
```json
{
  "success": true,
  "accounts": [
    {
      "id": 1,
      "platform": "facebook",
      "platform_user_id": "123456789",
      "username": "MediaPro Page",
      "email": "info@mediapro.social",
      "status": "active",
      "expires_at": "2025-12-21T10:00:00Z"
    }
  ]
}
```

### Test 4: Post to Social Media

**Request:**
```bash
curl -X POST https://www.mediapro.social/api/social-media/post \
  -H "Authorization: Bearer YOUR_AUTH_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "platforms": ["facebook", "twitter"],
    "content": "Hello from MediaPro! 🚀",
    "schedule_time": null
  }'
```

---

## 7️⃣ ترتيب الأولويات | Priority Order

### Recommended Implementation Order:

1. **🥇 Facebook (+ Instagram)** - Start Here
   - Most popular platform
   - Single setup for both Facebook and Instagram
   - Easiest to get approved
   - Best documentation

2. **🥈 Twitter/X**
   - Quick setup
   - Instant approval (Free tier)
   - Good for news and updates

3. **🥉 LinkedIn**
   - Professional network
   - Quick setup
   - Instant approval

4. **⏳ TikTok** - Do Last
   - Longer approval process (1-2 weeks)
   - More complex requirements
   - Can implement while others are working

---

## 8️⃣ استكشاف الأخطاء | Troubleshooting

### Common Issues:

#### 1. "redirect_uri_mismatch" Error

**Problem:** OAuth redirect URL doesn't match configured URL.

**Solution:**
- Check exact URL in platform settings
- Ensure `https://` vs `http://`
- Ensure trailing slash matches (or doesn't)
- Update `.env` file to match exactly

#### 2. "invalid_client" Error

**Problem:** Client ID or Secret is wrong.

**Solution:**
- Double-check credentials in `.env`
- Run `php artisan config:clear`
- Regenerate credentials if needed

#### 3. Token Expired

**Problem:** Access token expired.

**Solution:**
- Tokens are automatically refreshed by backend
- Or call refresh endpoint manually:
```bash
POST /api/social-oauth/accounts/{id}/refresh
```

#### 4. "insufficient_permissions" Error

**Problem:** Missing required API permissions.

**Solution:**
- Check requested scopes in platform settings
- Request additional permissions
- Re-authenticate user after permission changes

---

## 9️⃣ الأسئلة الشائعة | FAQ

### Q1: Do I need to setup all platforms?

**A:** No! Setup only the platforms your users need. Start with Facebook (includes Instagram), then add others based on demand.

### Q2: How long does approval take?

- **Facebook:** Instant for basic, 1-3 days for advanced permissions
- **Twitter:** Instant for Free tier
- **LinkedIn:** Instant for basic access
- **TikTok:** 1-2 weeks review process

### Q3: Are there any costs?

- **Facebook:** Free
- **Twitter:** Free (1,500 tweets/month), $100/month for Basic
- **LinkedIn:** Free for basic sharing
- **TikTok:** Free (with daily limits)

### Q4: Can I test before going live?

**A:** Yes! All platforms provide test/sandbox modes:
- Use localhost redirect URIs during development
- Facebook: Test with your own account
- Twitter: Free tier for testing
- Create test apps separate from production

### Q5: What happens if a token expires?

**A:** The backend automatically refreshes tokens using refresh_token. Users only need to re-authenticate if:
- They revoke access
- Permissions change
- Refresh token expires (usually after 60-90 days of inactivity)

---

## 🎯 Next Steps

### After Setup is Complete:

1. **✅ Test Each Platform:**
   - Connect account via app
   - Create a test post
   - Verify post appears on platform
   - Check analytics data

2. **✅ Update Mobile App:**
   - Test OAuth flow in React Native
   - Test WebView redirects
   - Test deep linking for callback

3. **✅ Monitor Performance:**
   - Check API usage in each platform's dashboard
   - Monitor rate limits
   - Set up alerts for errors

4. **✅ Document for Users:**
   - Create help articles for connecting accounts
   - Add troubleshooting guides
   - Provide video tutorials

---

## 📞 Support Resources

### Official Documentation:

- **Facebook:** https://developers.facebook.com/docs/
- **Instagram:** https://developers.facebook.com/docs/instagram-api/
- **Twitter:** https://developer.twitter.com/en/docs
- **LinkedIn:** https://docs.microsoft.com/en-us/linkedin/
- **TikTok:** https://developers.tiktok.com/doc/

### Rate Limits:

| Platform | Rate Limit | Notes |
|----------|-----------|-------|
| Facebook | 200 calls/hour | Per user token |
| Instagram | 200 calls/hour | Per user token |
| Twitter Free | 1,500 tweets/month | Per app |
| LinkedIn | 100 calls/day | Per user |
| TikTok | 50 videos/day | Per user |

---

## ✅ Final Checklist

Before going to production:

- [ ] All platforms tested in development
- [ ] Production credentials configured
- [ ] Callback URLs updated to production domain
- [ ] SSL certificate installed (HTTPS required)
- [ ] Error handling tested
- [ ] Token refresh mechanism tested
- [ ] Rate limiting implemented
- [ ] User help documentation created
- [ ] Analytics tracking setup
- [ ] Monitoring and alerts configured

---

**🎉 مبروك! | Congratulations!**

Your social media OAuth integration is now complete and ready to use!

Users can now connect their social media accounts and start posting content directly from your app to:
- ✅ Facebook Pages
- ✅ Instagram Business Accounts
- ✅ Twitter/X
- ✅ LinkedIn
- ✅ TikTok

**Need Help?** Check the troubleshooting section or consult the official platform documentation.

---

**Last Updated:** January 2025
**Version:** 1.0
**Status:** Production Ready 🚀
