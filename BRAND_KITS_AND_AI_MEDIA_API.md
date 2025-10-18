# Brand Kits & AI Media API - React Native Integration

## Overview
This document describes the API endpoints for Brand Kit management and AI Media generation, connecting the React Native mobile app screens to the Laravel backend.

## React Native Screens Supported
1. **BrandKitScreen.tsx** - Basic brand kit management
2. **EnhancedBrandKitScreen.tsx** - Enhanced brand kit UI with multilingual support
3. **AIContentGeneratorScreen.tsx** - AI-powered content generation with images

---

## Brand Kits API

### Base URL: `/api/brand-kits`

### 1. Get All Brand Kits
```
GET /api/brand-kits
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "_id": "123",
      "name": "My Brand Kit",
      "isDefault": true,
      "colors": {
        "primary": "#007AFF"
      },
      "languages": {
        "primary": "ar",
        "supported": ["ar", "en"],
        "arabicDialect": "msa"
      },
      "toneOfVoice": {
        "style": "professional",
        "formalityLevel": "formal",
        "keywordsArabic": ["جودة", "ابتكار"],
        "keywords": ["quality", "innovation"]
      },
      "guidelines": {
        "targetAudienceArabic": "الشباب المهتم بالتقنية",
        "targetAudience": "Tech-savvy young adults"
      },
      "hashtags": {
        "primaryArabic": ["#علامتي_التجارية"],
        "primary": ["#MyBrand"]
      },
      "arabicSettings": {
        "useArabicNumerals": true,
        "mixLanguages": false,
        "includeEnglishHashtags": true
      }
    }
  ]
}
```

### 2. Get Default Brand Kit
```
GET /api/brand-kits/default/get
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "_id": "123",
    "name": "My Default Brand Kit",
    "isDefault": true,
    ...
  }
}
```

### 3. Create Brand Kit
```
POST /api/brand-kits
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "Summer Campaign Brand",
  "colors": {
    "primary": "#FF6B6B"
  },
  "languages": {
    "primary": "ar",
    "supported": ["ar", "en"],
    "arabicDialect": "gulf"
  },
  "toneOfVoice": {
    "style": "casual",
    "formalityLevel": "informal",
    "keywordsArabic": ["صيف", "مرح", "إبداع"],
    "keywords": ["summer", "fun", "creative"],
    "avoidWordsArabic": ["قديم"],
    "avoidWords": ["old"]
  },
  "guidelines": {
    "targetAudienceArabic": "الشباب في دول الخليج",
    "targetAudience": "Young adults in Gulf countries"
  },
  "hashtags": {
    "primaryArabic": ["#صيف_٢٠٢٥", "#حملتنا"],
    "primary": ["#Summer2025", "#OurCampaign"]
  },
  "arabicSettings": {
    "useArabicNumerals": true,
    "mixLanguages": true,
    "includeEnglishHashtags": true
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Brand kit created successfully",
  "data": {
    "_id": "124",
    "name": "Summer Campaign Brand",
    "isDefault": false,
    ...
  }
}
```

### 4. Update Brand Kit
```
PUT /api/brand-kits/{id}
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:** (Same structure as Create)

**Response:**
```json
{
  "success": true,
  "message": "Brand kit updated successfully",
  "data": { ... }
}
```

### 5. Set Brand Kit as Default
```
POST /api/brand-kits/{id}/set-default
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Brand kit set as default successfully",
  "data": { ... }
}
```

### 6. Delete Brand Kit
```
DELETE /api/brand-kits/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Brand kit deleted successfully"
}
```

---

## AI Media Generation API

### Base URL: `/api/ai-media`

### 1. Generate Post with Image
```
POST /api/ai-media/generate-post-with-image
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "topic": "منتج جديد للعناية بالبشرة",
  "platform": "instagram",
  "language": "ar",
  "brandKitId": "123",
  "imageStyle": "vivid"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "caption": "نفخر بتقديم منتج جديد للعناية بالبشرة - حل مبتكر يلبي احتياجاتك. اكتشف المزيد من الإمكانيات والفرص المتاحة.",
    "image": {
      "url": "https://picsum.photos/seed/12345/1024/1024",
      "width": 1024,
      "height": 1024
    },
    "hashtags": "#علامتي_التجارية #منتج #جديد #إنستغرام #تصوير #محتوى"
  },
  "fallback": {
    "caption": "...",
    "image": { ... },
    "hashtags": "..."
  }
}
```

### 2. Generate AI Image Only
```
POST /api/ai-media/generate-image
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "prompt": "منظر طبيعي جميل مع غروب الشمس",
  "size": "1024x1024",
  "style": "vivid",
  "brandKitId": "123"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "url": "https://picsum.photos/seed/67890/1024/1024",
    "width": 1024,
    "height": 1024,
    "revised_prompt": "منظر طبيعي جميل مع غروب الشمس"
  },
  "fallback": {
    "url": "...",
    "width": 1024,
    "height": 1024
  }
}
```

### 3. Generate Video Script
```
POST /api/ai-media/generate-video-script
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "topic": "شرح كيفية استخدام المنتج",
  "duration": 60,
  "platform": "tiktok",
  "language": "ar",
  "brandKitId": "123"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "script": "سكريبت فيديو: شرح كيفية استخدام المنتج\n\n[0:00-0:05] مقدمة جذابة\nهل تعلم أن شرح كيفية استخدام المنتج يمكن أن يغير حياتك؟\n\n[0:05-0:20] المشكلة\nكثير من الناس يواجهون تحديات مع هذا الموضوع...\n\n...",
    "duration": 60,
    "scenes": [
      {
        "number": 1,
        "duration": 15,
        "type": "intro"
      },
      {
        "number": 2,
        "duration": 15,
        "type": "main"
      },
      ...
    ],
    "hooks": [
      "هل تعلم أن...؟",
      "اكتشف السر وراء...",
      "لن تصدق ما سيحدث...",
      "هذا ما لا يخبرك به أحد عن..."
    ],
    "cta": [
      "اضغط على الرابط في الوصف",
      "تابعنا لمزيد من المحتوى",
      "شارك هذا الفيديو",
      "اشترك الآن"
    ]
  }
}
```

### 4. Generate Image Variations
```
POST /api/ai-media/generate-image-variations
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "prompt": "منظر طبيعي جميل",
  "count": 3,
  "size": "1024x1024"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "variations": [
      {
        "url": "https://picsum.photos/seed/11111/1024/1024",
        "width": 1024,
        "height": 1024
      },
      {
        "url": "https://picsum.photos/seed/22222/1024/1024",
        "width": 1024,
        "height": 1024
      },
      {
        "url": "https://picsum.photos/seed/33333/1024/1024",
        "width": 1024,
        "height": 1024
      }
    ]
  }
}
```

---

## React Native Service Integration

### brandKitApi.ts
```typescript
import api from './api';

export const getBrandKits = async () => {
  const response = await api.get('/brand-kits');
  return response.data;
};

export const getDefaultBrandKit = async () => {
  const response = await api.get('/brand-kits/default/get');
  return response.data;
};

export const createBrandKit = async (data: any) => {
  const response = await api.post('/brand-kits', data);
  return response.data;
};

export const updateBrandKit = async (id: string, data: any) => {
  const response = await api.put(`/brand-kits/${id}`, data);
  return response.data;
};

export const setDefaultBrandKit = async (id: string) => {
  const response = await api.post(`/brand-kits/${id}/set-default`);
  return response.data;
};

export const deleteBrandKit = async (id: string) => {
  const response = await api.delete(`/brand-kits/${id}`);
  return response.data;
};
```

### aiMediaApi.ts
```typescript
import api from './api';

export const generatePostWithImage = async (data: {
  topic: string;
  platform: string;
  language: string;
  brandKitId?: string;
  imageStyle?: string;
}) => {
  const response = await api.post('/ai-media/generate-post-with-image', data);
  return response.data;
};

export const generateAIImage = async (data: {
  prompt: string;
  size?: string;
  style?: string;
  brandKitId?: string;
}) => {
  const response = await api.post('/ai-media/generate-image', data);
  return response.data;
};

export const generateVideoScript = async (data: {
  topic: string;
  duration: number;
  platform: string;
  language: string;
  brandKitId?: string;
}) => {
  const response = await api.post('/ai-media/generate-video-script', data);
  return response.data;
};

export const generateImageVariations = async (data: {
  prompt: string;
  count?: number;
  size?: string;
}) => {
  const response = await api.post('/ai-media/generate-image-variations', data);
  return response.data;
};
```

---

## Features

### Brand Kits
- ✅ Full CRUD operations
- ✅ Multilingual support (Arabic, English, Spanish, French, German)
- ✅ Arabic dialect selection (MSA, Egyptian, Gulf, Levantine, Moroccan)
- ✅ Tone of voice customization (Professional, Casual, Friendly, etc.)
- ✅ Formality levels (Formal, Informal, Mixed)
- ✅ Keywords and avoid words (Arabic & English)
- ✅ Target audience descriptions
- ✅ Custom hashtags
- ✅ Arabic-specific settings (numerals, language mixing, hashtags)
- ✅ Color scheme management
- ✅ Default brand kit system

### AI Media Generation
- ✅ Complete post generation (caption + image + hashtags)
- ✅ Standalone image generation with various sizes
- ✅ Video script generation with scenes breakdown
- ✅ Image variations for A/B testing
- ✅ Brand kit integration for consistent voice
- ✅ Platform-specific optimization
- ✅ Multilingual content generation
- ✅ Hook and CTA suggestions

---

## Database Schema

### brand_kits Table
```sql
CREATE TABLE brand_kits (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  name VARCHAR(255) NOT NULL,
  logo_url VARCHAR(255),
  colors JSON,
  fonts JSON,
  templates JSON,
  languages JSON,
  tone_of_voice JSON,
  guidelines JSON,
  hashtags JSON,
  arabic_settings JSON,
  is_default BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,

  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### JSON Structure Examples

**colors:**
```json
{
  "primary": "#007AFF",
  "secondary": "#5856D6",
  "accent": "#FF3B30"
}
```

**languages:**
```json
{
  "primary": "ar",
  "supported": ["ar", "en", "es"],
  "arabicDialect": "gulf"
}
```

**tone_of_voice:**
```json
{
  "style": "professional",
  "formalityLevel": "formal",
  "keywordsArabic": ["جودة", "ابتكار", "تميز"],
  "keywords": ["quality", "innovation", "excellence"],
  "avoidWordsArabic": ["قديم", "بطيء"],
  "avoidWords": ["old", "slow"]
}
```

**guidelines:**
```json
{
  "targetAudienceArabic": "الشباب المهتم بالتقنية من 18-35 سنة",
  "targetAudience": "Tech-savvy young adults aged 18-35"
}
```

**hashtags:**
```json
{
  "primaryArabic": ["#علامتي_التجارية", "#منتجاتنا", "#ابتكار"],
  "primary": ["#MyBrand", "#OurProducts", "#Innovation"]
}
```

**arabic_settings:**
```json
{
  "useArabicNumerals": true,
  "mixLanguages": false,
  "includeEnglishHashtags": true
}
```

---

## Notes

### Current Implementation
- ✅ All API endpoints created and tested
- ✅ Brand Kit model updated with multilingual fields
- ✅ Database migration applied successfully
- ✅ Routes registered in api.php
- ✅ React Native screens ready to connect

### Mock Data
- **Image Generation**: Currently using Picsum Photos as placeholder
- **In Production**: Integrate with DALL-E 3, Midjourney, or Stable Diffusion
- **Text Generation**: Using template-based generation
- **In Production**: Integrate with GPT-4, Claude, or similar LLM

### Security
- All endpoints require authentication via Laravel Sanctum
- Users can only access their own brand kits
- File uploads (logos) are validated and stored securely

### Performance
- Brand kits use JSON columns for flexible data storage
- Indices on user_id and is_default for fast queries
- Pagination not applied to brand kits (typically small dataset per user)

---

## Testing

### Test Brand Kit Creation
```bash
curl -X POST http://localhost:8000/api/brand-kits \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Brand",
    "colors": { "primary": "#007AFF" },
    "languages": {
      "primary": "ar",
      "supported": ["ar", "en"]
    }
  }'
```

### Test AI Image Generation
```bash
curl -X POST http://localhost:8000/api/ai-media/generate-post-with-image \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "topic": "Beautiful sunset",
    "platform": "instagram",
    "language": "en"
  }'
```

---

## Roadmap

### Phase 2 - Real AI Integration
- [ ] Integrate OpenAI GPT-4 for text generation
- [ ] Integrate DALL-E 3 for image generation
- [ ] Integrate Whisper for voice-to-text
- [ ] Add image editing capabilities
- [ ] Add video generation with AI

### Phase 3 - Advanced Features
- [ ] Brand kit templates marketplace
- [ ] AI-powered brand kit suggestions
- [ ] Multi-user collaboration on brand kits
- [ ] Version history for brand kits
- [ ] Export brand kits to PDF/Figma

---

## Support

For issues or questions:
- Backend API: Check logs in `storage/logs/laravel.log`
- Database: Verify migrations with `php artisan migrate:status`
- Routes: List all routes with `php artisan route:list`

**All systems are connected and ready for use!** 🚀
