<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Comprehensive AI Service for social media content generation
 * Supports multiple AI providers with automatic fallback
 * - OpenAI (GPT-4o-mini)
 * - Google Gemini (1.5-flash)
 * - Claude (3.5 Sonnet)
 */
class AIService
{
    // AI Provider Constants
    const PROVIDER_OPENAI = 'openai';
    const PROVIDER_GEMINI = 'gemini';
    const PROVIDER_CLAUDE = 'claude';

    // Supported Platforms
    const PLATFORMS = ['instagram', 'facebook', 'twitter', 'linkedin', 'tiktok', 'youtube'];

    // API Endpoints
    private $endpoints = [
        'openai' => 'https://api.openai.com/v1/chat/completions',
        'gemini' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent',
        'claude' => 'https://api.anthropic.com/v1/messages',
    ];

    // Default provider order for fallback
    private $providerOrder = [self::PROVIDER_OPENAI, self::PROVIDER_GEMINI, self::PROVIDER_CLAUDE];

    // User's preferred provider (can be set dynamically)
    private $preferredProvider = null;

    // Rate limiting tracking
    private $rateLimitHit = [];

    /**
     * Set preferred AI provider
     */
    public function setPreferredProvider($provider)
    {
        if (in_array($provider, [self::PROVIDER_OPENAI, self::PROVIDER_GEMINI, self::PROVIDER_CLAUDE])) {
            $this->preferredProvider = $provider;
            // Move preferred provider to the front of the list
            $this->providerOrder = array_unique(array_merge([$provider], $this->providerOrder));
        }
    }

    /**
     * Generate social media content using AI
     *
     * @param string $prompt The content prompt
     * @param array $options Additional options (platform, tone, length, language)
     * @return array Generated content with metadata
     */
    public function generateContent($prompt, $options = [])
    {
        $platform = $options['platform'] ?? 'instagram';
        $tone = $options['tone'] ?? 'professional';
        $length = $options['length'] ?? 'medium';
        $language = $options['language'] ?? 'en';

        // Build the AI prompt
        $aiPrompt = $this->buildContentPrompt($prompt, $platform, $tone, $length, $language);

        // Try to generate content with fallback
        $result = $this->executeWithFallback($aiPrompt, 'content_generation');

        return [
            'success' => true,
            'content' => $result['content'],
            'provider' => $result['provider'],
            'metadata' => [
                'platform' => $platform,
                'tone' => $tone,
                'length' => $length,
                'language' => $language,
                'word_count' => str_word_count($result['content']),
                'char_count' => mb_strlen($result['content']),
            ]
        ];
    }

    /**
     * Generate relevant hashtags for content
     *
     * @param string $content The post content
     * @param string $platform Target platform
     * @param int $count Number of hashtags to generate
     * @return array Generated hashtags
     */
    public function generateHashtags($content, $platform = 'instagram', $count = 10)
    {
        // Build hashtag generation prompt
        $aiPrompt = $this->buildHashtagPrompt($content, $platform, $count);

        // Execute with fallback
        $result = $this->executeWithFallback($aiPrompt, 'hashtag_generation');

        // Parse hashtags from response
        $hashtags = $this->parseHashtags($result['content'], $count);

        return [
            'success' => true,
            'hashtags' => $hashtags,
            'provider' => $result['provider'],
            'count' => count($hashtags),
        ];
    }

    /**
     * Enhance existing content with AI improvements
     *
     * @param string $content Original content
     * @param string $tone Desired tone
     * @param array $improvements Types of improvements (grammar, engagement, clarity)
     * @return array Enhanced content
     */
    public function enhanceContent($content, $tone = 'professional', $improvements = [])
    {
        if (empty($improvements)) {
            $improvements = ['grammar', 'engagement', 'clarity'];
        }

        // Build enhancement prompt
        $aiPrompt = $this->buildEnhancementPrompt($content, $tone, $improvements);

        // Execute with fallback
        $result = $this->executeWithFallback($aiPrompt, 'content_enhancement');

        return [
            'success' => true,
            'original' => $content,
            'enhanced' => $result['content'],
            'provider' => $result['provider'],
            'improvements_applied' => $improvements,
        ];
    }

    /**
     * Generate content in multiple languages
     *
     * @param string $prompt Content prompt
     * @param array $languages Array of language codes
     * @param array $options Additional options
     * @return array Content in multiple languages
     */
    public function generateMultilingualContent($prompt, $languages, $options = [])
    {
        $results = [];

        foreach ($languages as $language) {
            try {
                $options['language'] = $language;
                $result = $this->generateContent($prompt, $options);
                $results[$language] = $result;
            } catch (Exception $e) {
                Log::error("Failed to generate content in {$language}: " . $e->getMessage());
                $results[$language] = [
                    'success' => false,
                    'error' => 'Failed to generate content in this language',
                ];
            }
        }

        return [
            'success' => true,
            'translations' => $results,
            'languages' => $languages,
        ];
    }

    /**
     * Get list of available AI providers
     *
     * @return array Available providers with their status
     */
    public function getAvailableProviders()
    {
        return [
            'openai' => [
                'name' => 'OpenAI GPT-4o-mini',
                'available' => $this->isProviderConfigured(self::PROVIDER_OPENAI),
                'model' => 'gpt-4o-mini',
            ],
            'gemini' => [
                'name' => 'Google Gemini 1.5 Flash',
                'available' => $this->isProviderConfigured(self::PROVIDER_GEMINI),
                'model' => 'gemini-1.5-flash',
            ],
            'claude' => [
                'name' => 'Claude 3.5 Sonnet',
                'available' => $this->isProviderConfigured(self::PROVIDER_CLAUDE),
                'model' => 'claude-3-5-sonnet-20241022',
            ],
        ];
    }

    /**
     * Execute AI request with automatic fallback to other providers
     *
     * @param string $prompt The prompt to send
     * @param string $taskType Type of task (for logging)
     * @return array Response with content and provider used
     */
    private function executeWithFallback($prompt, $taskType = 'general')
    {
        $lastError = null;

        foreach ($this->providerOrder as $provider) {
            // Skip if provider hit rate limit
            if (isset($this->rateLimitHit[$provider])) {
                continue;
            }

            // Skip if provider not configured
            if (!$this->isProviderConfigured($provider)) {
                continue;
            }

            try {
                Log::info("Attempting {$taskType} with provider: {$provider}");
                $response = $this->callProvider($provider, $prompt);

                Log::info("Successfully completed {$taskType} with provider: {$provider}");
                return [
                    'content' => $response,
                    'provider' => $provider,
                ];
            } catch (Exception $e) {
                $lastError = $e;
                Log::warning("Provider {$provider} failed for {$taskType}: " . $e->getMessage());

                // Check if it's a rate limit error
                if ($this->isRateLimitError($e)) {
                    $this->rateLimitHit[$provider] = true;
                }

                continue; // Try next provider
            }
        }

        // All providers failed
        throw new Exception("All AI providers failed. Last error: " . ($lastError ? $lastError->getMessage() : 'Unknown error'));
    }

    /**
     * Call specific AI provider
     *
     * @param string $provider Provider name
     * @param string $prompt Prompt to send
     * @return string AI response
     */
    private function callProvider($provider, $prompt)
    {
        switch ($provider) {
            case self::PROVIDER_OPENAI:
                return $this->callOpenAI($prompt);

            case self::PROVIDER_GEMINI:
                return $this->callGemini($prompt);

            case self::PROVIDER_CLAUDE:
                return $this->callClaude($prompt);

            default:
                throw new Exception("Unknown provider: {$provider}");
        }
    }

    /**
     * Call OpenAI GPT-4o-mini API
     */
    private function callOpenAI($prompt)
    {
        $apiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY');

        if (!$apiKey) {
            throw new Exception('OpenAI API key not configured');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($this->endpoints['openai'], [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a professional social media content creator. Generate engaging, high-quality content.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => 1000,
            'temperature' => 0.7,
        ]);

        if (!$response->successful()) {
            throw new Exception('OpenAI API request failed: ' . $response->body());
        }

        $data = $response->json();
        return $data['choices'][0]['message']['content'] ?? '';
    }

    /**
     * Call Google Gemini 1.5 Flash API
     */
    private function callGemini($prompt)
    {
        $apiKey = config('services.gemini.api_key') ?? env('GEMINI_API_KEY');

        if (!$apiKey) {
            throw new Exception('Gemini API key not configured');
        }

        $url = $this->endpoints['gemini'] . '?key=' . $apiKey;

        $response = Http::timeout(30)->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 1000,
            ]
        ]);

        if (!$response->successful()) {
            throw new Exception('Gemini API request failed: ' . $response->body());
        }

        $data = $response->json();
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }

    /**
     * Call Claude 3.5 Sonnet API
     */
    private function callClaude($prompt)
    {
        $apiKey = config('services.claude.api_key') ?? env('CLAUDE_API_KEY');

        if (!$apiKey) {
            throw new Exception('Claude API key not configured');
        }

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($this->endpoints['claude'], [
            'model' => 'claude-3-5-sonnet-20241022',
            'max_tokens' => 1000,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'system' => 'You are a professional social media content creator. Generate engaging, high-quality content.',
        ]);

        if (!$response->successful()) {
            throw new Exception('Claude API request failed: ' . $response->body());
        }

        $data = $response->json();
        return $data['content'][0]['text'] ?? '';
    }

    /**
     * Build content generation prompt
     */
    private function buildContentPrompt($prompt, $platform, $tone, $length, $language)
    {
        $lengthGuide = [
            'short' => '1-2 sentences',
            'medium' => '3-5 sentences',
            'long' => '6-10 sentences',
        ];

        $languageNames = [
            'en' => 'English',
            'ar' => 'Arabic',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
        ];

        $toneDescriptions = [
            'professional' => 'formal and business-like',
            'casual' => 'relaxed and conversational',
            'friendly' => 'warm and approachable',
            'enthusiastic' => 'energetic and exciting',
            'informative' => 'educational and clear',
        ];

        $languageName = $languageNames[$language] ?? 'English';
        $toneDesc = $toneDescriptions[$tone] ?? 'professional';
        $lengthDesc = $lengthGuide[$length] ?? '3-5 sentences';

        return "Generate an engaging {$platform} post about: {$prompt}

Requirements:
- Language: {$languageName}
- Tone: {$toneDesc}
- Length: {$lengthDesc}
- Platform: {$platform}
- Include emojis if appropriate for the platform and tone
- Make it engaging and optimized for {$platform}
- For Arabic content, ensure proper RTL formatting

Return ONLY the post content, without any additional explanation.";
    }

    /**
     * Build hashtag generation prompt
     */
    private function buildHashtagPrompt($content, $platform, $count)
    {
        return "Generate {$count} relevant hashtags for this {$platform} post: {$content}

Requirements:
- Return ONLY hashtags, comma-separated
- Make them relevant and trending
- Mix of broad and specific hashtags
- Optimized for {$platform}
- No explanations, just hashtags

Example format: #Hashtag1, #Hashtag2, #Hashtag3";
    }

    /**
     * Build content enhancement prompt
     */
    private function buildEnhancementPrompt($content, $tone, $improvements)
    {
        $improvementsList = implode(', ', $improvements);

        return "Improve the following content with these enhancements: {$improvementsList}

Original content: {$content}

Requirements:
- Maintain the core message
- Apply a {$tone} tone
- Fix grammar and spelling errors
- Improve engagement and clarity
- Add appropriate emojis if needed
- Keep the same language as the original

Return ONLY the enhanced content, without explanations.";
    }

    /**
     * Parse hashtags from AI response
     */
    private function parseHashtags($response, $maxCount)
    {
        // Remove any non-hashtag text
        $response = trim($response);

        // Extract hashtags using regex
        preg_match_all('/#[\w\u0600-\u06FF]+/u', $response, $matches);

        if (!empty($matches[0])) {
            return array_slice(array_unique($matches[0]), 0, $maxCount);
        }

        // Fallback: split by comma and ensure # prefix
        $tags = explode(',', $response);
        $hashtags = [];

        foreach ($tags as $tag) {
            $tag = trim($tag);
            if (!empty($tag)) {
                $tag = str_starts_with($tag, '#') ? $tag : '#' . $tag;
                $hashtags[] = $tag;
            }
        }

        return array_slice(array_unique($hashtags), 0, $maxCount);
    }

    /**
     * Check if provider is configured
     */
    private function isProviderConfigured($provider)
    {
        switch ($provider) {
            case self::PROVIDER_OPENAI:
                return !empty(config('services.openai.api_key') ?? env('OPENAI_API_KEY'));

            case self::PROVIDER_GEMINI:
                return !empty(config('services.gemini.api_key') ?? env('GEMINI_API_KEY'));

            case self::PROVIDER_CLAUDE:
                return !empty(config('services.claude.api_key') ?? env('CLAUDE_API_KEY'));

            default:
                return false;
        }
    }

    /**
     * Check if error is a rate limit error
     */
    private function isRateLimitError(Exception $e)
    {
        $message = strtolower($e->getMessage());
        return str_contains($message, 'rate limit') ||
               str_contains($message, '429') ||
               str_contains($message, 'too many requests');
    }
}
