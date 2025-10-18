<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OpenAIService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.openai.com/v1';
    protected $provider; // 'openai', 'claude', 'gemini'
    protected $model;

    public function __construct()
    {
        // Get AI settings from database
        $settings = $this->getAISettings();

        $this->provider = $settings['provider'] ?? 'openai';
        $this->apiKey = $settings['api_key'] ?? env('OPENAI_API_KEY');
        $this->model = $settings['model'] ?? 'gpt-4';

        // Set base URL based on provider
        $this->baseUrl = match($this->provider) {
            'claude' => 'https://api.anthropic.com/v1',
            'gemini' => 'https://generativelanguage.googleapis.com/v1beta',
            default => 'https://api.openai.com/v1',
        };
    }

    /**
     * Get AI settings from database
     */
    private function getAISettings(): array
    {
        $dbSettings = DB::table('app_settings')
            ->whereIn('key', ['ai_provider', 'openai_api_key', 'openai_model', 'claude_api_key', 'gemini_api_key', 'ai_enabled'])
            ->get()
            ->keyBy('key');

        // Check if AI is enabled
        $aiEnabled = $dbSettings->get('ai_enabled')?->value ?? true;
        if (!$aiEnabled) {
            return ['provider' => 'fallback', 'api_key' => null];
        }

        $provider = $dbSettings->get('ai_provider')?->value ?? 'openai';

        $apiKey = match($provider) {
            'claude' => $dbSettings->get('claude_api_key')?->value ?? env('ANTHROPIC_API_KEY'),
            'gemini' => $dbSettings->get('gemini_api_key')?->value ?? env('GOOGLE_AI_API_KEY'),
            default => $dbSettings->get('openai_api_key')?->value ?? env('OPENAI_API_KEY'),
        };

        $model = match($provider) {
            'claude' => 'claude-3-5-sonnet-20241022',
            'gemini' => 'gemini-pro',
            default => $dbSettings->get('openai_model')?->value ?? 'gpt-4',
        };

        return [
            'provider' => $provider,
            'api_key' => $apiKey,
            'model' => $model,
        ];
    }

    /**
     * Generate caption using ChatGPT
     */
    public function generateCaption(string $topic, string $tone = 'professional', string $platform = 'instagram', string $length = 'medium'): array
    {
        if (!$this->apiKey) {
            return $this->getFallbackCaption($topic, $tone);
        }

        try {
            $prompt = $this->buildCaptionPrompt($topic, $tone, $platform, $length);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post("{$this->baseUrl}/chat/completions", [
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert social media content creator. Generate engaging, platform-appropriate captions that drive engagement.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.8,
                'max_tokens' => $this->getMaxTokens($length),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $caption = $data['choices'][0]['message']['content'] ?? '';

                return [
                    'caption' => trim($caption),
                    'variations' => $this->generateVariations($caption, 3),
                    'source' => 'openai'
                ];
            }

            Log::warning('OpenAI API failed, using fallback', ['response' => $response->body()]);
            return $this->getFallbackCaption($topic, $tone);

        } catch (\Exception $e) {
            Log::error('OpenAI API error: ' . $e->getMessage());
            return $this->getFallbackCaption($topic, $tone);
        }
    }

    /**
     * Generate hashtags using ChatGPT
     */
    public function generateHashtags(string $content, string $platform = 'instagram', int $count = 10): array
    {
        if (!$this->apiKey) {
            return $this->getFallbackHashtags($content, $count);
        }

        try {
            $prompt = "Generate {$count} relevant, trending hashtags for this {$platform} content: {$content}. Return only the hashtags, one per line, with # symbol.";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(20)->post("{$this->baseUrl}/chat/completions", [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a social media hashtag expert. Generate relevant, trending hashtags.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 200,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $hashtagsText = $data['choices'][0]['message']['content'] ?? '';
                $hashtags = $this->parseHashtags($hashtagsText);

                return [
                    'hashtags' => array_slice($hashtags, 0, $count),
                    'source' => 'openai'
                ];
            }

            return $this->getFallbackHashtags($content, $count);

        } catch (\Exception $e) {
            Log::error('OpenAI Hashtags error: ' . $e->getMessage());
            return $this->getFallbackHashtags($content, $count);
        }
    }

    /**
     * Generate content ideas using ChatGPT
     */
    public function generateIdeas(string $niche, string $platform = 'all', int $count = 10): array
    {
        if (!$this->apiKey) {
            return $this->getFallbackIdeas($niche, $count);
        }

        try {
            $prompt = "Generate {$count} creative, engaging content ideas for {$niche} on {$platform}. For each idea, provide: title, type (listicle/educational/tutorial/etc), and engagement potential (low/medium/high/very high). Format as JSON array.";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post("{$this->baseUrl}/chat/completions", [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a social media content strategist. Generate creative, platform-appropriate content ideas.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.9,
                'max_tokens' => 800,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $ideasText = $data['choices'][0]['message']['content'] ?? '';
                $ideas = $this->parseIdeas($ideasText, $niche);

                return [
                    'ideas' => array_slice($ideas, 0, $count),
                    'source' => 'openai'
                ];
            }

            return $this->getFallbackIdeas($niche, $count);

        } catch (\Exception $e) {
            Log::error('OpenAI Ideas error: ' . $e->getMessage());
            return $this->getFallbackIdeas($niche, $count);
        }
    }

    /**
     * Improve content using ChatGPT
     */
    public function improveContent(string $content, array $improvements = []): array
    {
        if (!$this->apiKey) {
            return $this->getFallbackImprovement($content, $improvements);
        }

        try {
            $improvementTypes = implode(', ', $improvements);
            $prompt = "Improve this social media content focusing on: {$improvementTypes}. Original: {$content}";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(20)->post("{$this->baseUrl}/chat/completions", [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a social media content editor. Improve content for better engagement.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 300,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $improved = $data['choices'][0]['message']['content'] ?? $content;

                return [
                    'improved' => trim($improved),
                    'source' => 'openai'
                ];
            }

            return $this->getFallbackImprovement($content, $improvements);

        } catch (\Exception $e) {
            Log::error('OpenAI Improve error: ' . $e->getMessage());
            return $this->getFallbackImprovement($content, $improvements);
        }
    }

    // Helper Methods

    private function buildCaptionPrompt($topic, $tone, $platform, $length): string
    {
        $lengthGuide = [
            'short' => '1-2 sentences (30-50 words)',
            'medium' => '2-3 sentences (50-100 words)',
            'long' => '3-5 sentences (100-200 words)',
        ];

        return "Create a {$tone} {$platform} caption about: {$topic}. Length: {$lengthGuide[$length]}. Make it engaging and include relevant emojis.";
    }

    private function getMaxTokens($length): int
    {
        return match($length) {
            'short' => 100,
            'medium' => 200,
            'long' => 400,
            default => 200,
        };
    }

    private function generateVariations($caption, $count): array
    {
        $variations = [];
        $prefixes = ['✨', '🚀', '💡', '🎯', '⭐'];
        $suffixes = [
            ' What do you think?',
            ' Share your thoughts!',
            ' Let me know in the comments!',
            ' Tag someone who needs this!',
            ' Double tap if you agree! ❤️'
        ];

        for ($i = 0; $i < $count; $i++) {
            $variation = ($prefixes[$i] ?? '') . ' ' . $caption;
            if (isset($suffixes[$i])) {
                $variation .= $suffixes[$i];
            }
            $variations[] = trim($variation);
        }

        return $variations;
    }

    private function parseHashtags($text): array
    {
        $lines = explode("\n", $text);
        $hashtags = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (str_starts_with($line, '#')) {
                $hashtags[] = $line;
            } elseif (preg_match('/#\w+/', $line, $matches)) {
                $hashtags[] = $matches[0];
            }
        }

        return $hashtags;
    }

    private function parseIdeas($text, $niche): array
    {
        // Try to parse JSON first
        $decoded = json_decode($text, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Fallback to text parsing
        $ideas = [];
        $lines = explode("\n", $text);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strlen($line) < 10) continue;

            $ideas[] = [
                'title' => $line,
                'type' => 'general',
                'engagement_potential' => 'medium'
            ];
        }

        return $ideas;
    }

    // Fallback methods (use existing simulation logic)

    private function getFallbackCaption($topic, $tone): array
    {
        $captions = [
            'professional' => "Excited to share insights about {$topic}. Our latest analysis reveals innovative approaches that drive real results. #Innovation #Business",
            'casual' => "Let's talk about {$topic}! 🎉 Here's my take on what's really working right now. Drop a comment if you agree! ✨",
            'friendly' => "Hey friends! 👋 Wanted to share my thoughts on {$topic}. It's been such an amazing journey! 💫",
        ];

        $caption = $captions[$tone] ?? $captions['professional'];

        return [
            'caption' => $caption,
            'variations' => $this->generateVariations($caption, 3),
            'source' => 'fallback'
        ];
    }

    private function getFallbackHashtags($content, $count): array
    {
        $words = str_word_count($content, 1);
        $hashtags = array_map(fn($word) => '#' . ucfirst(strtolower($word)), array_slice($words, 0, $count));

        return [
            'hashtags' => $hashtags,
            'source' => 'fallback'
        ];
    }

    private function getFallbackIdeas($niche, $count): array
    {
        $ideas = [
            ['title' => "10 Tips for {$niche} Success", 'type' => 'listicle', 'engagement_potential' => 'high'],
            ['title' => "Behind the Scenes: {$niche} Process", 'type' => 'educational', 'engagement_potential' => 'medium'],
            ['title' => "Common {$niche} Mistakes to Avoid", 'type' => 'educational', 'engagement_potential' => 'high'],
        ];

        return [
            'ideas' => array_slice($ideas, 0, $count),
            'source' => 'fallback'
        ];
    }

    private function getFallbackImprovement($content, $improvements): array
    {
        $improved = ucfirst($content);
        if (in_array('engagement', $improvements)) {
            $improved .= ' What do you think?';
        }
        if (in_array('emojis', $improvements)) {
            $improved = '✨ ' . $improved . ' 🚀';
        }

        return [
            'improved' => $improved,
            'source' => 'fallback'
        ];
    }
}
