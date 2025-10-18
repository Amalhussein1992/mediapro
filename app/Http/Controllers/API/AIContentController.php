<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AIContentController extends Controller
{
    protected $openAI;

    public function __construct(OpenAIService $openAI)
    {
        $this->openAI = $openAI;
    }
    /**
     * Generate caption using AI
     */
    public function generateCaption(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:500',
            'tone' => 'nullable|string|in:professional,casual,friendly,enthusiastic,informative',
            'platform' => 'nullable|string|in:instagram,facebook,twitter,linkedin,tiktok',
            'length' => 'nullable|string|in:short,medium,long',
        ]);

        $topic = $request->input('topic');
        $tone = $request->input('tone', 'professional');
        $platform = $request->input('platform', 'instagram');
        $length = $request->input('length', 'medium');

        // Use OpenAI Service to generate caption
        $result = $this->openAI->generateCaption($topic, $tone, $platform, $length);

        return response()->json([
            'success' => true,
            'data' => [
                'caption' => $result['caption'],
                'variations' => $result['variations'],
                'metadata' => [
                    'tone' => $tone,
                    'platform' => $platform,
                    'length' => $length,
                    'word_count' => str_word_count($result['caption']),
                    'source' => $result['source'] ?? 'openai',
                ]
            ]
        ]);
    }

    /**
     * Generate hashtags using AI
     */
    public function generateHashtags(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'platform' => 'nullable|string|in:instagram,facebook,twitter,linkedin,tiktok',
            'count' => 'nullable|integer|min:1|max:30',
        ]);

        $content = $request->input('content');
        $platform = $request->input('platform', 'instagram');
        $count = $request->input('count', 10);

        // Generate hashtags (This is a simulation)
        $hashtags = $this->simulateAIHashtags($content, $platform, $count);

        return response()->json([
            'success' => true,
            'data' => [
                'hashtags' => $hashtags,
                'trending' => $this->getTrendingHashtags($platform, 5),
                'recommended_count' => $this->getRecommendedHashtagCount($platform),
            ]
        ]);
    }

    /**
     * Improve existing content
     */
    public function improveContent(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'improvements' => 'nullable|array',
            'improvements.*' => 'string|in:grammar,engagement,clarity,seo,emojis',
        ]);

        $content = $request->input('content');
        $improvements = $request->input('improvements', ['grammar', 'engagement', 'clarity']);

        // Improve content (This is a simulation)
        $improvedContent = $this->simulateContentImprovement($content, $improvements);

        return response()->json([
            'success' => true,
            'data' => [
                'original' => $content,
                'improved' => $improvedContent,
                'improvements_applied' => $improvements,
                'suggestions' => $this->generateSuggestions($content),
            ]
        ]);
    }

    /**
     * Generate content ideas
     */
    public function generateIdeas(Request $request)
    {
        $request->validate([
            'niche' => 'required|string|max:200',
            'platform' => 'nullable|string',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        $niche = $request->input('niche');
        $platform = $request->input('platform', 'all');
        $count = $request->input('count', 10);

        // Generate ideas (This is a simulation)
        $ideas = $this->simulateContentIdeas($niche, $platform, $count);

        return response()->json([
            'success' => true,
            'data' => [
                'ideas' => $ideas,
                'trending_topics' => $this->getTrendingTopics($niche, 5),
                'content_calendar_suggestions' => $this->generateCalendarSuggestions(),
            ]
        ]);
    }

    /**
     * Simulate AI caption generation
     */
    private function simulateAICaption($topic, $tone, $platform, $length)
    {
        $lengthMap = [
            'short' => 50,
            'medium' => 100,
            'long' => 200,
        ];

        $words = $lengthMap[$length] ?? 100;

        $captions = [
            'professional' => [
                "Excited to share insights about {$topic}. Our latest analysis reveals innovative approaches that drive real results. #Innovation #Business",
                "Breaking down {$topic} into actionable strategies. Here's what you need to know to stay ahead. #Strategy #Growth",
                "Exploring the future of {$topic}. Data-driven insights for forward-thinking professionals. #Leadership #Excellence",
            ],
            'casual' => [
                "Let's talk about {$topic}! 🎉 Here's my take on what's really working right now. Drop a comment if you agree! ✨",
                "Just discovered something cool about {$topic}... and I had to share! 🚀 What do you think? #Community",
                "Real talk: {$topic} is changing the game! Here's why you should care 👇 #Authentic",
            ],
            'friendly' => [
                "Hey friends! 👋 Wanted to share my thoughts on {$topic}. It's been such an amazing journey! 💫",
                "Happy to discuss {$topic} with you all! Your feedback means everything to me 💙 #Community #Together",
                "Sharing some love about {$topic} today! Hope this brightens your day ☀️ #Positivity",
            ],
        ];

        $toneKey = array_key_exists($tone, $captions) ? $tone : 'professional';
        return $captions[$toneKey][array_rand($captions[$toneKey])];
    }

    /**
     * Generate caption variations
     */
    private function generateVariations($caption, $count)
    {
        $variations = [];
        for ($i = 0; $i < $count; $i++) {
            $variations[] = $this->modifyCaption($caption, $i);
        }
        return $variations;
    }

    /**
     * Modify caption for variation
     */
    private function modifyCaption($caption, $index)
    {
        $prefixes = [
            "✨ ",
            "🚀 ",
            "💡 ",
        ];

        $suffixes = [
            " What are your thoughts?",
            " Let me know in the comments!",
            " Share if you agree!",
        ];

        return ($prefixes[$index] ?? '') . $caption . ($suffixes[$index] ?? '');
    }

    /**
     * Simulate AI hashtag generation
     */
    private function simulateAIHashtags($content, $platform, $count)
    {
        $words = str_word_count($content, 1);
        $keywords = array_slice($words, 0, min(5, count($words)));

        $hashtags = [];
        foreach ($keywords as $keyword) {
            $hashtags[] = '#' . ucfirst(strtolower($keyword));
        }

        // Add platform-specific hashtags
        $platformTags = [
            'instagram' => ['#InstaGood', '#PhotoOfTheDay', '#InstaDaily'],
            'twitter' => ['#Trending', '#News', '#Discussion'],
            'linkedin' => ['#Professional', '#Business', '#Career'],
            'facebook' => ['#Community', '#Share', '#Connect'],
            'tiktok' => ['#FYP', '#Viral', '#Trending'],
        ];

        $hashtags = array_merge($hashtags, $platformTags[$platform] ?? []);

        return array_slice(array_unique($hashtags), 0, $count);
    }

    /**
     * Get trending hashtags
     */
    private function getTrendingHashtags($platform, $count)
    {
        $trending = [
            '#Trending',
            '#Viral',
            '#MustSee',
            '#DontMiss',
            '#ContentCreator',
        ];

        return array_slice($trending, 0, $count);
    }

    /**
     * Get recommended hashtag count
     */
    private function getRecommendedHashtagCount($platform)
    {
        $recommendations = [
            'instagram' => ['min' => 5, 'max' => 30, 'optimal' => 11],
            'twitter' => ['min' => 1, 'max' => 2, 'optimal' => 1],
            'linkedin' => ['min' => 3, 'max' => 5, 'optimal' => 3],
            'facebook' => ['min' => 1, 'max' => 3, 'optimal' => 2],
            'tiktok' => ['min' => 3, 'max' => 5, 'optimal' => 4],
        ];

        return $recommendations[$platform] ?? ['min' => 3, 'max' => 10, 'optimal' => 5];
    }

    /**
     * Simulate content improvement
     */
    private function simulateContentImprovement($content, $improvements)
    {
        $improved = $content;

        if (in_array('grammar', $improvements)) {
            // Capitalize first letter
            $improved = ucfirst($improved);
        }

        if (in_array('engagement', $improvements)) {
            // Add call to action
            if (!Str::contains($improved, ['?', '!'])) {
                $improved .= " What do you think?";
            }
        }

        if (in_array('emojis', $improvements)) {
            // Add relevant emojis
            $improved = "✨ " . $improved . " 🚀";
        }

        return $improved;
    }

    /**
     * Generate content suggestions
     */
    private function generateSuggestions($content)
    {
        return [
            "Add a call-to-action at the end",
            "Include relevant hashtags",
            "Consider adding emojis for engagement",
            "Mention your audience directly",
            "Keep paragraphs short for better readability",
        ];
    }

    /**
     * Simulate content ideas generation
     */
    private function simulateContentIdeas($niche, $platform, $count)
    {
        $ideas = [
            [
                'title' => "10 Tips for {$niche} Success",
                'type' => 'listicle',
                'engagement_potential' => 'high',
            ],
            [
                'title' => "Behind the Scenes: {$niche} Process",
                'type' => 'educational',
                'engagement_potential' => 'medium',
            ],
            [
                'title' => "Common {$niche} Mistakes to Avoid",
                'type' => 'educational',
                'engagement_potential' => 'high',
            ],
            [
                'title' => "My {$niche} Journey: A Personal Story",
                'type' => 'storytelling',
                'engagement_potential' => 'medium',
            ],
            [
                'title' => "Quick {$niche} Tutorial",
                'type' => 'tutorial',
                'engagement_potential' => 'high',
            ],
            [
                'title' => "Ask Me Anything: {$niche} Edition",
                'type' => 'interactive',
                'engagement_potential' => 'very high',
            ],
            [
                'title' => "{$niche} Trends for 2025",
                'type' => 'trend',
                'engagement_potential' => 'high',
            ],
            [
                'title' => "Before & After: {$niche} Transformation",
                'type' => 'showcase',
                'engagement_potential' => 'very high',
            ],
        ];

        return array_slice($ideas, 0, $count);
    }

    /**
     * Get trending topics
     */
    private function getTrendingTopics($niche, $count)
    {
        return [
            ['topic' => 'AI Integration', 'volume' => 'high'],
            ['topic' => 'Sustainability', 'volume' => 'medium'],
            ['topic' => 'Digital Transformation', 'volume' => 'high'],
            ['topic' => 'Community Building', 'volume' => 'medium'],
            ['topic' => 'Innovation', 'volume' => 'high'],
        ];
    }

    /**
     * Generate calendar suggestions
     */
    private function generateCalendarSuggestions()
    {
        return [
            ['day' => 'Monday', 'type' => 'Motivational', 'best_time' => '9:00 AM'],
            ['day' => 'Wednesday', 'type' => 'Educational', 'best_time' => '12:00 PM'],
            ['day' => 'Friday', 'type' => 'Fun/Interactive', 'best_time' => '5:00 PM'],
        ];
    }
}
