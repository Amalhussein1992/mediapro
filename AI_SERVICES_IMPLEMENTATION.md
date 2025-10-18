# AI Services Implementation - Complete Documentation

## Overview

This document provides comprehensive documentation for the AI services integration in the Social Media Management application. The implementation includes support for multiple AI providers, voice transcription, and advanced content generation features.

## Features Implemented

### 1. Multi-Provider AI Service
- **OpenAI GPT-4o-mini**: Fast and cost-effective content generation
- **Google Gemini 1.5 Flash**: Google's latest AI model
- **Claude 3.5 Sonnet**: Anthropic's most advanced model
- **Automatic Fallback**: If one provider fails, automatically tries another
- **Rate Limiting Aware**: Detects rate limits and switches providers

### 2. Voice Transcription Service
- **OpenAI Whisper**: State-of-the-art speech recognition
- **Multi-format Support**: mp3, mp4, mpeg, mpga, m4a, wav, webm
- **Large File Handling**: Up to 25MB audio files
- **Language Detection**: Automatic language detection
- **Timestamps**: Optional timestamp generation for subtitles

### 3. Content Generation Features
- Social media post generation for all major platforms
- Hashtag generation optimized per platform
- Content enhancement (grammar, engagement, clarity)
- Multilingual content generation
- Voice-to-post conversion

## File Structure

```
backend-laravel/
├── app/
│   ├── Services/
│   │   ├── AIService.php                    # Main AI service with 3 providers
│   │   └── VoiceTranscriptionService.php    # Voice-to-text service
│   ├── Http/Controllers/Api/
│   │   └── AIController.php                 # Comprehensive AI endpoints
├── routes/
│   └── api.php                              # API routes
└── config/
    └── services.php                         # Service configurations
```

## API Endpoints

All endpoints require authentication (`auth:sanctum` middleware).

### 1. Generate Content
**POST** `/api/ai/generate-content`

Generate social media post content using AI.

**Request Body:**
```json
{
  "prompt": "Launch of new eco-friendly product line",
  "platform": "instagram",
  "tone": "enthusiastic",
  "length": "medium",
  "language": "en",
  "provider": "openai"
}
```

**Parameters:**
- `prompt` (required): Content topic or idea
- `platform` (optional): instagram, facebook, twitter, linkedin, tiktok, youtube (default: instagram)
- `tone` (optional): professional, casual, friendly, enthusiastic, informative (default: professional)
- `length` (optional): short, medium, long (default: medium)
- `language` (optional): en, ar, es, fr, de (default: en)
- `provider` (optional): openai, gemini, claude (uses fallback if not specified)

**Response:**
```json
{
  "success": true,
  "message": "Content generated successfully",
  "data": {
    "content": "🌿 Excited to introduce our new eco-friendly product line...",
    "provider": "openai",
    "metadata": {
      "platform": "instagram",
      "tone": "enthusiastic",
      "length": "medium",
      "language": "en",
      "word_count": 45,
      "char_count": 280
    }
  }
}
```

### 2. Generate Hashtags
**POST** `/api/ai/generate-hashtags`

Generate relevant hashtags for content.

**Request Body:**
```json
{
  "content": "Just launched our new sustainable product line!",
  "platform": "instagram",
  "count": 10,
  "provider": "openai"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Hashtags generated successfully",
  "data": {
    "hashtags": [
      "#Sustainable",
      "#EcoFriendly",
      "#GreenLiving",
      "#ProductLaunch",
      "#Innovation"
    ],
    "provider": "openai",
    "count": 5
  }
}
```

### 3. Transcribe Voice
**POST** `/api/ai/transcribe-voice`

Transcribe audio file to text.

**Request (multipart/form-data):**
- `audio` (required): Audio file (mp3, wav, etc.)
- `language` (optional): Language code (e.g., 'en', 'ar')

**Response:**
```json
{
  "success": true,
  "message": "Audio transcribed successfully",
  "data": {
    "text": "Hello, this is a test transcription...",
    "language": "en",
    "duration": 15.5,
    "segments": [...],
    "word_count": 25,
    "char_count": 150
  }
}
```

### 4. Voice to Post
**POST** `/api/ai/voice-to-post`

Transcribe audio and generate a social media post.

**Request (multipart/form-data):**
- `audio` (required): Audio file
- `platform` (required): Target platform
- `tone` (optional): Content tone
- `length` (optional): Content length
- `language` (optional): Target language

**Response:**
```json
{
  "success": true,
  "message": "Voice successfully converted to post",
  "data": {
    "transcript": {
      "text": "I want to talk about our new product...",
      "language": "en",
      "duration": 30.5,
      "word_count": 50
    },
    "post": {
      "content": "Excited to share insights about our new product...",
      "hashtags": ["#NewProduct", "#Innovation", "#Tech"],
      "platform": "instagram",
      "tone": "professional"
    },
    "metadata": {
      "ai_provider": "openai",
      "platform": "instagram",
      "language": "en"
    }
  }
}
```

### 5. Enhance Content
**POST** `/api/ai/enhance-content`

Improve existing content with AI.

**Request Body:**
```json
{
  "content": "we have a new product it is very good",
  "tone": "professional",
  "improvements": ["grammar", "engagement", "clarity"],
  "provider": "openai"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Content enhanced successfully",
  "data": {
    "original": "we have a new product it is very good",
    "enhanced": "We're excited to announce our innovative new product that delivers exceptional value!",
    "provider": "openai",
    "improvements_applied": ["grammar", "engagement", "clarity"]
  }
}
```

### 6. Generate Multilingual Content
**POST** `/api/ai/generate-multilingual`

Generate content in multiple languages.

**Request Body:**
```json
{
  "prompt": "New product launch announcement",
  "languages": ["en", "ar", "es"],
  "platform": "instagram",
  "tone": "professional",
  "length": "medium"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Multilingual content generated successfully",
  "data": {
    "translations": {
      "en": {
        "content": "We're thrilled to announce...",
        "metadata": {...}
      },
      "ar": {
        "content": "يسرنا أن نعلن...",
        "metadata": {...}
      },
      "es": {
        "content": "Nos complace anunciar...",
        "metadata": {...}
      }
    },
    "languages": ["en", "ar", "es"]
  }
}
```

### 7. Get Available Providers
**GET** `/api/ai/providers`

Get list of available AI providers and their status.

**Response:**
```json
{
  "success": true,
  "message": "Available AI providers retrieved",
  "data": {
    "providers": {
      "openai": {
        "name": "OpenAI GPT-4o-mini",
        "available": true,
        "model": "gpt-4o-mini"
      },
      "gemini": {
        "name": "Google Gemini 1.5 Flash",
        "available": true,
        "model": "gemini-1.5-flash"
      },
      "claude": {
        "name": "Claude 3.5 Sonnet",
        "available": false,
        "model": "claude-3-5-sonnet-20241022"
      }
    },
    "configured_count": 2,
    "total_count": 3
  }
}
```

### 8. Set Provider
**POST** `/api/ai/set-provider`

Set preferred AI provider for the user.

**Request Body:**
```json
{
  "provider": "openai"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Preferred AI provider set successfully",
  "data": {
    "provider": "openai",
    "name": "OpenAI GPT-4o-mini",
    "model": "gpt-4o-mini"
  }
}
```

### 9. Get Transcription Info
**GET** `/api/ai/transcription-info`

Get information about voice transcription capabilities.

**Response:**
```json
{
  "success": true,
  "data": {
    "supported_formats": ["mp3", "mp4", "mpeg", "mpga", "m4a", "wav", "webm"],
    "max_file_size": 26214400,
    "max_file_size_mb": 25,
    "model": "whisper-1",
    "provider": "OpenAI Whisper"
  }
}
```

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
# OpenAI API (GPT-4o-mini, Whisper)
OPENAI_API_KEY=sk-your-openai-api-key

# Google Gemini API (Gemini 1.5 Flash)
GEMINI_API_KEY=your-gemini-api-key

# Anthropic Claude API (Claude 3.5 Sonnet)
CLAUDE_API_KEY=your-claude-api-key

# AI Service Settings
AI_DEFAULT_PROVIDER=openai
AI_FALLBACK_ENABLED=true
AI_RATE_LIMIT_RETRY=true
```

### Getting API Keys

#### OpenAI API Key
1. Go to https://platform.openai.com/api-keys
2. Sign in or create an account
3. Click "Create new secret key"
4. Copy the key and add to `.env` as `OPENAI_API_KEY`

#### Google Gemini API Key
1. Go to https://makersuite.google.com/app/apikey
2. Sign in with Google account
3. Click "Create API Key"
4. Copy the key and add to `.env` as `GEMINI_API_KEY`

#### Anthropic Claude API Key
1. Go to https://console.anthropic.com/
2. Sign in or create an account
3. Navigate to API Keys section
4. Generate new key
5. Copy the key and add to `.env` as `CLAUDE_API_KEY`

## Usage Examples

### Example 1: Generate Instagram Post

```php
// Using cURL
curl -X POST https://your-domain.com/api/ai/generate-content \
  -H "Authorization: Bearer YOUR_AUTH_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "prompt": "Tips for staying productive while working from home",
    "platform": "instagram",
    "tone": "friendly",
    "length": "medium",
    "language": "en"
  }'
```

### Example 2: Convert Voice to Post

```php
// Using JavaScript Fetch API
const formData = new FormData();
formData.append('audio', audioFile);
formData.append('platform', 'instagram');
formData.append('tone', 'professional');

const response = await fetch('/api/ai/voice-to-post', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${authToken}`
  },
  body: formData
});

const result = await response.json();
console.log(result.data.post.content);
```

### Example 3: Generate Hashtags

```javascript
// Using Axios
const response = await axios.post('/api/ai/generate-hashtags', {
  content: 'Just launched our new fitness app!',
  platform: 'instagram',
  count: 15
}, {
  headers: {
    'Authorization': `Bearer ${authToken}`
  }
});

const hashtags = response.data.data.hashtags;
```

## Error Handling

All endpoints return consistent error responses:

```json
{
  "success": false,
  "message": "Failed to generate content",
  "error": "Detailed error message"
}
```

Common error scenarios:
- **API Key Not Configured**: Provider API key is missing
- **Rate Limit Hit**: Too many requests (auto-fallback enabled)
- **Invalid Audio Format**: Unsupported audio file format
- **File Too Large**: Audio file exceeds 25MB limit
- **Network Error**: Connection timeout or network issue

## Features & Capabilities

### 1. Multi-Provider Fallback System
The service automatically tries alternative providers if the primary fails:
- Primary provider fails → Try secondary provider
- Secondary fails → Try tertiary provider
- Logs all failures for monitoring

### 2. Rate Limiting Awareness
- Detects 429 (Too Many Requests) errors
- Automatically switches to alternative provider
- Tracks which providers hit rate limits
- Resumes using provider after cooldown

### 3. Language Support
Supported languages:
- **en**: English
- **ar**: Arabic (with RTL support)
- **es**: Spanish
- **fr**: French
- **de**: German

### 4. Platform Optimization
Content is optimized for each platform:
- **Instagram**: Emoji-friendly, hashtag-optimized (5-30 tags)
- **Twitter**: Character limit aware, 1-2 hashtags
- **LinkedIn**: Professional tone, 3-5 hashtags
- **Facebook**: Engagement-focused, 1-3 hashtags
- **TikTok**: Trendy language, 3-5 hashtags
- **YouTube**: Description-optimized, SEO-friendly

### 5. Content Tones
Available tones:
- **Professional**: Formal and business-like
- **Casual**: Relaxed and conversational
- **Friendly**: Warm and approachable
- **Enthusiastic**: Energetic and exciting
- **Informative**: Educational and clear

### 6. Content Lengths
- **Short**: 1-2 sentences
- **Medium**: 3-5 sentences
- **Long**: 6-10 sentences

## Best Practices

1. **API Key Management**
   - Never commit API keys to version control
   - Use `.env` file for configuration
   - Rotate keys regularly
   - Monitor usage and costs

2. **Error Handling**
   - Always check `success` field in response
   - Implement retry logic for network errors
   - Show user-friendly error messages
   - Log errors for debugging

3. **Performance**
   - Cache generated content when appropriate
   - Use preferred provider for consistent results
   - Implement request queuing for batch operations
   - Monitor response times

4. **Security**
   - Validate all user inputs
   - Sanitize file uploads
   - Limit file sizes appropriately
   - Implement rate limiting on frontend

5. **Cost Optimization**
   - Use GPT-4o-mini (cheapest) as default
   - Implement user quotas if needed
   - Cache frequently requested content
   - Monitor API usage per user

## Troubleshooting

### Issue: "OpenAI API key not configured"
**Solution**: Add `OPENAI_API_KEY` to your `.env` file

### Issue: "All AI providers failed"
**Solution**:
- Check if at least one API key is configured
- Verify API keys are valid
- Check internet connection
- Review API quota limits

### Issue: "Audio file size exceeds maximum limit"
**Solution**:
- Compress audio file before upload
- Maximum size is 25MB
- Use supported formats: mp3, wav, m4a

### Issue: Rate limit errors
**Solution**:
- Wait for rate limit to reset
- Use alternative provider
- Upgrade API plan
- Implement request queuing

## Performance Metrics

Typical response times:
- Content Generation: 2-5 seconds
- Hashtag Generation: 1-3 seconds
- Voice Transcription: 5-15 seconds (depends on file size)
- Voice-to-Post: 10-20 seconds (transcription + generation)

## Support & Resources

- **OpenAI Documentation**: https://platform.openai.com/docs
- **Gemini Documentation**: https://ai.google.dev/docs
- **Claude Documentation**: https://docs.anthropic.com/

## Future Enhancements

Planned features:
- Image generation with DALL-E 3
- Video script generation
- Content scheduling integration
- A/B testing for different AI providers
- Custom prompt templates
- Analytics on AI-generated content performance

## Conclusion

This comprehensive AI services integration provides a robust, production-ready solution for AI-powered social media content generation. With multi-provider support, automatic fallback, and extensive error handling, it ensures reliable service for your social media management application.
