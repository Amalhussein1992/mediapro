<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BrandKit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AIMediaController extends Controller
{
    /**
     * Generate a post with image using AI
     */
    public function generatePostWithImage(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:500',
            'platform' => 'required|string|in:instagram,facebook,twitter,linkedin,tiktok',
            'language' => 'required|string|in:ar,en,es,fr,de',
            'brandKitId' => 'nullable|string',
            'imageStyle' => 'nullable|string|in:vivid,natural',
        ]);

        $user = $request->user();
        $topic = $request->topic;
        $platform = $request->platform;
        $language = $request->language;
        $imageStyle = $request->input('imageStyle', 'vivid');

        // Get brand kit if provided
        $brandKit = null;
        if ($request->brandKitId) {
            $brandKit = $user->brandKits()->find($request->brandKitId);
        }

        // Generate caption based on brand kit and language
        $caption = $this->generateCaption($topic, $platform, $language, $brandKit);

        // Generate hashtags
        $hashtags = $this->generateHashtags($topic, $platform, $language, $brandKit);

        // Generate image URL (mock - in production, integrate with DALL-E or similar)
        $imageUrl = $this->generateMockImageUrl($topic, $imageStyle);

        return response()->json([
            'success' => true,
            'data' => [
                'caption' => $caption,
                'image' => [
                    'url' => $imageUrl,
                    'width' => 1024,
                    'height' => 1024,
                ],
                'hashtags' => $hashtags,
            ],
            'fallback' => [
                'caption' => $caption,
                'image' => [
                    'url' => $imageUrl,
                    'width' => 1024,
                    'height' => 1024,
                ],
                'hashtags' => $hashtags,
            ]
        ]);
    }

    /**
     * Generate an AI image
     */
    public function generateImage(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'size' => 'nullable|string|in:1024x1024,1792x1024,1024x1792',
            'style' => 'nullable|string|in:vivid,natural',
            'brandKitId' => 'nullable|string',
        ]);

        $prompt = $request->prompt;
        $size = $request->input('size', '1024x1024');
        $style = $request->input('style', 'vivid');

        // Generate image URL (mock)
        $imageUrl = $this->generateMockImageUrl($prompt, $style);

        return response()->json([
            'success' => true,
            'data' => [
                'url' => $imageUrl,
                'width' => (int) explode('x', $size)[0],
                'height' => (int) explode('x', $size)[1],
                'revised_prompt' => $prompt,
            ],
            'fallback' => [
                'url' => $imageUrl,
                'width' => (int) explode('x', $size)[0],
                'height' => (int) explode('x', $size)[1],
            ]
        ]);
    }

    /**
     * Generate a video script
     */
    public function generateVideoScript(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:500',
            'duration' => 'required|integer|min:15|max:300',
            'platform' => 'required|string|in:instagram,facebook,twitter,linkedin,tiktok,youtube',
            'language' => 'required|string|in:ar,en,es,fr,de',
            'brandKitId' => 'nullable|string',
        ]);

        $user = $request->user();
        $topic = $request->topic;
        $duration = $request->duration;
        $platform = $request->platform;
        $language = $request->language;

        // Get brand kit if provided
        $brandKit = null;
        if ($request->brandKitId) {
            $brandKit = $user->brandKits()->find($request->brandKitId);
        }

        // Generate script
        $script = $this->generateScript($topic, $duration, $platform, $language, $brandKit);

        return response()->json([
            'success' => true,
            'data' => [
                'script' => $script,
                'duration' => $duration,
                'scenes' => $this->generateScenes($duration),
                'hooks' => $this->generateHooks($language),
                'cta' => $this->generateCTA($language),
            ]
        ]);
    }

    /**
     * Generate image variations
     */
    public function generateImageVariations(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'count' => 'nullable|integer|min:1|max:4',
            'size' => 'nullable|string|in:1024x1024,1792x1024,1024x1792',
        ]);

        $prompt = $request->prompt;
        $count = $request->input('count', 3);
        $size = $request->input('size', '1024x1024');

        $variations = [];
        for ($i = 0; $i < $count; $i++) {
            $variations[] = [
                'url' => $this->generateMockImageUrl($prompt . ' variation ' . ($i + 1), 'vivid'),
                'width' => (int) explode('x', $size)[0],
                'height' => (int) explode('x', $size)[1],
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'variations' => $variations,
            ]
        ]);
    }

    /**
     * Generate a caption
     */
    private function generateCaption($topic, $platform, $language, $brandKit = null)
    {
        $tone = $brandKit->tone_of_voice['style'] ?? 'professional';
        $keywords = $brandKit->tone_of_voice['keywords'] ?? [];

        $captions = [
            'ar' => [
                'professional' => "نفخر بتقديم {$topic} - حل مبتكر يلبي احتياجاتك. اكتشف المزيد من الإمكانيات والفرص المتاحة.",
                'casual' => "لازم تجرب {$topic}! 🎉 شيء رهيب وهيعجبك جداً. شاركنا رأيك في التعليقات! ✨",
                'friendly' => "مرحباً! ��� سعداء بمشاركة {$topic} معكم. نأمل أن ينال إعجابكم! 💙",
            ],
            'en' => [
                'professional' => "Introducing {$topic} - an innovative solution designed to meet your needs. Discover new possibilities and opportunities.",
                'casual' => "You gotta try {$topic}! 🎉 It's absolutely amazing. Let us know what you think! ✨",
                'friendly' => "Hey there! 👋 We're excited to share {$topic} with you. Hope you love it! 💙",
            ],
        ];

        return $captions[$language][$tone] ?? $captions['en']['professional'];
    }

    /**
     * Generate hashtags
     */
    private function generateHashtags($topic, $platform, $language, $brandKit = null)
    {
        $brandHashtags = [];
        if ($brandKit) {
            if ($language === 'ar' && isset($brandKit->hashtags['primaryArabic'])) {
                $brandHashtags = array_slice($brandKit->hashtags['primaryArabic'], 0, 3);
            } elseif (isset($brandKit->hashtags['primary'])) {
                $brandHashtags = array_slice($brandKit->hashtags['primary'], 0, 3);
            }
        }

        $keywords = explode(' ', $topic);
        $topicHashtags = array_map(function ($word) {
            return '#' . ucfirst(strtolower(trim($word)));
        }, array_slice($keywords, 0, 3));

        $platformHashtags = [
            'instagram' => $language === 'ar' ? ['#إنستغرام', '#تصوير', '#محتوى'] : ['#Instagram', '#Photography', '#Content'],
            'facebook' => $language === 'ar' ? ['#فيسبوك', '#مشاركة', '#تواصل'] : ['#Facebook', '#Share', '#Connect'],
            'twitter' => $language === 'ar' ? ['#تويتر', '#أخبار', '#نقاش'] : ['#Twitter', '#News', '#Discussion'],
            'linkedin' => $language === 'ar' ? ['#لينكد_إن', '#مهني', '#أعمال'] : ['#LinkedIn', '#Professional', '#Business'],
            'tiktok' => $language === 'ar' ? ['#تيك_توك', '#فيديو', '#ترفيه'] : ['#TikTok', '#Video', '#Entertainment'],
        ];

        $allHashtags = array_merge($brandHashtags, $topicHashtags, $platformHashtags[$platform] ?? []);
        return implode(' ', array_unique($allHashtags));
    }

    /**
     * Generate a mock image URL
     */
    private function generateMockImageUrl($prompt, $style)
    {
        // In production, integrate with DALL-E, Midjourney, or similar
        // For now, return a placeholder image
        $seed = crc32($prompt . $style);
        return "https://picsum.photos/seed/{$seed}/1024/1024";
    }

    /**
     * Generate a video script
     */
    private function generateScript($topic, $duration, $platform, $language, $brandKit = null)
    {
        $sections = [
            [
                'time' => '0:00-0:05',
                'scene' => $language === 'ar' ? 'مقدمة جذابة' : 'Engaging Hook',
                'text' => $language === 'ar'
                    ? "هل تعلم أن {$topic} يمكن أن يغير حياتك؟"
                    : "Did you know that {$topic} can change your life?",
            ],
            [
                'time' => '0:05-0:20',
                'scene' => $language === 'ar' ? 'المشكلة' : 'The Problem',
                'text' => $language === 'ar'
                    ? "كثير من الناس يواجهون تحديات مع هذا الموضوع..."
                    : "Many people face challenges with this topic...",
            ],
            [
                'time' => '0:20-0:45',
                'scene' => $language === 'ar' ? 'الحل' : 'The Solution',
                'text' => $language === 'ar'
                    ? "لكن {$topic} يوفر حلاً مثالياً من خلال..."
                    : "But {$topic} provides the perfect solution through...",
            ],
            [
                'time' => '0:45-0:60',
                'scene' => $language === 'ar' ? 'دعوة للعمل' : 'Call to Action',
                'text' => $language === 'ar'
                    ? "ابدأ الآن واكتشف الفرق! اضغط على الرابط في الوصف."
                    : "Start now and discover the difference! Click the link in bio.",
            ],
        ];

        $script = $language === 'ar' ? "سكريبت فيديو: {$topic}\n\n" : "Video Script: {$topic}\n\n";
        foreach ($sections as $section) {
            $script .= "[{$section['time']}] {$section['scene']}\n";
            $script .= "{$section['text']}\n\n";
        }

        return $script;
    }

    /**
     * Generate scenes breakdown
     */
    private function generateScenes($duration)
    {
        $scenesCount = (int) ceil($duration / 15);
        $scenes = [];

        for ($i = 0; $i < $scenesCount; $i++) {
            $scenes[] = [
                'number' => $i + 1,
                'duration' => min(15, $duration - ($i * 15)),
                'type' => ['intro', 'main', 'transition', 'outro'][$i % 4],
            ];
        }

        return $scenes;
    }

    /**
     * Generate hook suggestions
     */
    private function generateHooks($language)
    {
        $hooks = [
            'ar' => [
                'هل تعلم أن...؟',
                'اكتشف السر وراء...',
                'لن تصدق ما سيحدث...',
                'هذا ما لا يخبرك به أحد عن...',
            ],
            'en' => [
                'Did you know that...?',
                'Discover the secret behind...',
                'You won\'t believe what happens...',
                'What nobody tells you about...',
            ],
        ];

        return $hooks[$language] ?? $hooks['en'];
    }

    /**
     * Generate CTA suggestions
     */
    private function generateCTA($language)
    {
        $ctas = [
            'ar' => [
                'اضغط على الرابط في الوصف',
                'تابعنا لمزيد من المحتوى',
                'شارك هذا الفيديو',
                'اشترك الآن',
            ],
            'en' => [
                'Click the link in bio',
                'Follow for more content',
                'Share this video',
                'Subscribe now',
            ],
        ];

        return $ctas[$language] ?? $ctas['en'];
    }
}
