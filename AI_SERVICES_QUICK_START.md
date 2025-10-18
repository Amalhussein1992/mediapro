# AI Services - Quick Start Guide

## Setup (5 Minutes)

### 1. Configure API Keys

Add to your `.env` file:

```env
# At minimum, configure ONE provider
OPENAI_API_KEY=sk-your-key-here

# Optional: Add more providers for fallback
GEMINI_API_KEY=your-gemini-key
CLAUDE_API_KEY=your-claude-key
```

### 2. Test the Setup

```bash
# Check if services are configured
curl -X GET http://localhost:8000/api/ai/providers \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Quick Examples

### Generate a Post

```bash
curl -X POST http://localhost:8000/api/ai/generate-content \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "prompt": "New product launch",
    "platform": "instagram",
    "tone": "enthusiastic"
  }'
```

### Generate Hashtags

```bash
curl -X POST http://localhost:8000/api/ai/generate-hashtags \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "content": "Launching our new app today!",
    "platform": "instagram",
    "count": 10
  }'
```

### Transcribe Audio

```bash
curl -X POST http://localhost:8000/api/ai/transcribe-voice \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "audio=@/path/to/audio.mp3"
```

### Voice to Post (Complete Workflow)

```bash
curl -X POST http://localhost:8000/api/ai/voice-to-post \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "audio=@/path/to/audio.mp3" \
  -F "platform=instagram" \
  -F "tone=professional"
```

## API Endpoints Summary

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/api/ai/generate-content` | POST | Generate social media content |
| `/api/ai/generate-hashtags` | POST | Generate hashtags |
| `/api/ai/transcribe-voice` | POST | Transcribe audio to text |
| `/api/ai/voice-to-post` | POST | Audio → Text → Post |
| `/api/ai/enhance-content` | POST | Improve existing content |
| `/api/ai/generate-multilingual` | POST | Generate in multiple languages |
| `/api/ai/providers` | GET | List available AI providers |
| `/api/ai/set-provider` | POST | Set preferred provider |
| `/api/ai/transcription-info` | GET | Get transcription capabilities |

## Parameters Reference

### Content Generation

```json
{
  "prompt": "Your topic here",           // Required
  "platform": "instagram",               // Optional: instagram, facebook, twitter, linkedin, tiktok
  "tone": "professional",                // Optional: professional, casual, friendly, enthusiastic
  "length": "medium",                    // Optional: short, medium, long
  "language": "en",                      // Optional: en, ar, es, fr, de
  "provider": "openai"                   // Optional: openai, gemini, claude
}
```

### Hashtag Generation

```json
{
  "content": "Your post content",        // Required
  "platform": "instagram",               // Optional
  "count": 10                            // Optional: 1-30
}
```

### Voice Transcription

```
multipart/form-data:
- audio: Audio file (required)
- language: Language code (optional)
```

## Supported Features

### Platforms
✅ Instagram
✅ Facebook
✅ Twitter
✅ LinkedIn
✅ TikTok
✅ YouTube

### Languages
✅ English (en)
✅ Arabic (ar)
✅ Spanish (es)
✅ French (fr)
✅ German (de)

### Audio Formats
✅ MP3
✅ MP4
✅ MPEG
✅ MPGA
✅ M4A
✅ WAV
✅ WebM

### AI Providers
✅ OpenAI GPT-4o-mini
✅ Google Gemini 1.5 Flash
✅ Claude 3.5 Sonnet

## Common Response Format

### Success Response

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // Response data here
  }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Operation failed",
  "error": "Detailed error message"
}
```

## Getting API Keys

### OpenAI (Recommended - Easiest to Setup)
1. Visit: https://platform.openai.com/api-keys
2. Sign up/Login
3. Create new secret key
4. Copy to `.env` as `OPENAI_API_KEY`

### Google Gemini (Free Tier Available)
1. Visit: https://makersuite.google.com/app/apikey
2. Sign in with Google
3. Create API Key
4. Copy to `.env` as `GEMINI_API_KEY`

### Anthropic Claude (Premium)
1. Visit: https://console.anthropic.com/
2. Sign up/Login
3. Generate API key
4. Copy to `.env` as `CLAUDE_API_KEY`

## Pricing (Approximate)

### OpenAI GPT-4o-mini
- **Input**: $0.150 / 1M tokens
- **Output**: $0.600 / 1M tokens
- **Whisper**: $0.006 / minute

### Google Gemini 1.5 Flash
- **Free tier**: 15 requests/minute
- **Paid**: Very low cost

### Claude 3.5 Sonnet
- **Input**: $3.00 / 1M tokens
- **Output**: $15.00 / 1M tokens

## Troubleshooting

### Issue: All providers failing
```bash
# Check provider status
curl -X GET http://localhost:8000/api/ai/providers \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Issue: API key not working
1. Check `.env` file has correct key
2. Restart Laravel server: `php artisan serve`
3. Clear config cache: `php artisan config:clear`

### Issue: Audio file too large
- Maximum size: 25MB
- Compress audio before upload
- Use MP3 format for smaller files

## Best Practices

1. **Start with OpenAI**: Easiest to setup, most reliable
2. **Add Multiple Providers**: Enable automatic fallback
3. **Cache Results**: Save generated content to avoid re-generation
4. **Monitor Usage**: Track API costs in provider dashboards
5. **Handle Errors**: Always check `success` field in response

## Integration Example (JavaScript)

```javascript
// Generate content
async function generatePost(prompt, platform = 'instagram') {
  const response = await fetch('/api/ai/generate-content', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${authToken}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      prompt,
      platform,
      tone: 'professional',
      language: 'en'
    })
  });

  const data = await response.json();

  if (data.success) {
    return data.data.content;
  } else {
    throw new Error(data.error);
  }
}

// Voice to post
async function voiceToPost(audioFile, platform = 'instagram') {
  const formData = new FormData();
  formData.append('audio', audioFile);
  formData.append('platform', platform);
  formData.append('tone', 'friendly');

  const response = await fetch('/api/ai/voice-to-post', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${authToken}`
    },
    body: formData
  });

  const data = await response.json();

  if (data.success) {
    return {
      transcript: data.data.transcript.text,
      post: data.data.post.content,
      hashtags: data.data.post.hashtags
    };
  } else {
    throw new Error(data.error);
  }
}
```

## Need Help?

- Check logs: `storage/logs/laravel.log`
- Review documentation: `AI_SERVICES_IMPLEMENTATION.md`
- Test endpoints with Postman
- Verify API keys are valid

## Next Steps

1. ✅ Configure at least one AI provider
2. ✅ Test with `/api/ai/providers` endpoint
3. ✅ Try generating content
4. ✅ Integrate with your frontend
5. ✅ Monitor usage and costs

---

**Ready to use!** All endpoints are production-ready with comprehensive error handling and automatic fallback support.
