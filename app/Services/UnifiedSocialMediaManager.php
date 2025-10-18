<?php

namespace App\Services;

use App\Services\SocialMedia\FacebookService;
use App\Services\SocialMedia\TwitterService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Unified Social Media Manager
 * Manages all social media platforms from one place
 */
class UnifiedSocialMediaManager
{
    protected $facebook;
    protected $twitter;

    public function __construct(
        FacebookService $facebook,
        TwitterService $twitter
    ) {
        $this->facebook = $facebook;
        $this->twitter = $twitter;
    }

    /**
     * Get OAuth URL for any platform
     */
    public function getAuthUrl(string $platform, string $redirectUri): array
    {
        try {
            return match ($platform) {
                'facebook', 'instagram' => $this->facebook->getAuthUrl($redirectUri),
                'twitter' => $this->twitter->getAuthUrl($redirectUri),
                'linkedin' => $this->getLinkedInAuthUrl($redirectUri),
                'tiktok' => $this->getTikTokAuthUrl($redirectUri),
                'youtube' => $this->getYouTubeAuthUrl($redirectUri),
                default => [
                    'success' => false,
                    'error' => "Platform '{$platform}' not supported",
                ],
            };
        } catch (\Exception $e) {
            Log::error("getAuthUrl error for {$platform}: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle OAuth callback and get access token
     */
    public function handleCallback(string $platform, string $code, string $redirectUri, array $additionalData = []): array
    {
        try {
            return match ($platform) {
                'facebook', 'instagram' => $this->facebook->getAccessToken($code, $redirectUri),
                'twitter' => $this->twitter->getAccessToken(
                    $code,
                    $redirectUri,
                    $additionalData['code_verifier'] ?? ''
                ),
                'linkedin' => $this->getLinkedInAccessToken($code, $redirectUri),
                'tiktok' => $this->getTikTokAccessToken($code, $redirectUri),
                'youtube' => $this->getYouTubeAccessToken($code, $redirectUri),
                default => [
                    'success' => false,
                    'error' => "Platform '{$platform}' not supported",
                ],
            };
        } catch (\Exception $e) {
            Log::error("handleCallback error for {$platform}: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Publish post to one or multiple platforms
     */
    public function publishPost(array $platforms, array $postData, int $userId): array
    {
        $results = [];
        $errors = [];

        foreach ($platforms as $platform) {
            try {
                // Get user's access token for this platform
                $account = DB::table('social_accounts')
                    ->where('user_id', $userId)
                    ->where('platform', $platform)
                    ->where('is_active', true)
                    ->first();

                if (!$account) {
                    $errors[$platform] = "Account not connected";
                    continue;
                }

                $result = $this->publishToSinglePlatform(
                    $platform,
                    $postData,
                    $account->access_token,
                    $account->platform_user_id ?? null
                );

                if ($result['success']) {
                    $results[$platform] = $result;
                } else {
                    $errors[$platform] = $result['error'] ?? 'Failed to publish';
                }

            } catch (\Exception $e) {
                Log::error("publishPost error for {$platform}: " . $e->getMessage());
                $errors[$platform] = $e->getMessage();
            }
        }

        return [
            'success' => count($results) > 0,
            'results' => $results,
            'errors' => $errors,
            'published_count' => count($results),
            'failed_count' => count($errors),
        ];
    }

    /**
     * Publish to a single platform
     */
    private function publishToSinglePlatform(
        string $platform,
        array $postData,
        string $accessToken,
        ?string $platformUserId
    ): array {
        return match ($platform) {
            'facebook' => $this->facebook->publishPost(
                $platformUserId,
                $accessToken,
                [
                    'message' => $postData['content'],
                    'photo_url' => $postData['media'][0] ?? null,
                    'scheduled_time' => $postData['scheduled_at'] ?? null,
                ]
            ),
            'instagram' => $this->facebook->publishInstagramPost(
                $platformUserId,
                $accessToken,
                [
                    'caption' => $postData['content'],
                    'image_url' => $postData['media'][0] ?? null,
                ]
            ),
            'twitter' => $this->twitter->createTweet($accessToken, [
                'text' => $postData['content'],
            ]),
            'linkedin' => $this->publishToLinkedIn($accessToken, $postData),
            'tiktok' => $this->publishToTikTok($accessToken, $postData),
            'youtube' => $this->publishToYouTube($accessToken, $postData),
            default => [
                'success' => false,
                'error' => "Platform '{$platform}' not supported",
            ],
        };
    }

    /**
     * Get analytics from all connected platforms
     */
    public function getAnalytics(int $userId, ?string $platform = null): array
    {
        $analytics = [];

        $query = DB::table('social_accounts')
            ->where('user_id', $userId)
            ->where('is_active', true);

        if ($platform) {
            $query->where('platform', $platform);
        }

        $accounts = $query->get();

        foreach ($accounts as $account) {
            try {
                $result = $this->getAnalyticsForPlatform(
                    $account->platform,
                    $account->access_token,
                    $account->platform_user_id
                );

                if ($result['success']) {
                    $analytics[$account->platform] = $result['analytics'];
                }

            } catch (\Exception $e) {
                Log::error("getAnalytics error for {$account->platform}: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'analytics' => $analytics,
        ];
    }

    /**
     * Get analytics for a single platform
     */
    private function getAnalyticsForPlatform(
        string $platform,
        string $accessToken,
        ?string $platformUserId
    ): array {
        return match ($platform) {
            'facebook' => $this->facebook->getPageInsights($platformUserId, $accessToken),
            'instagram' => $this->facebook->getInstagramInsights($platformUserId, $accessToken),
            'twitter' => $this->twitter->getUserTweets($accessToken, $platformUserId),
            'linkedin' => $this->getLinkedInAnalytics($accessToken),
            'youtube' => $this->getYouTubeAnalytics($accessToken),
            default => [
                'success' => false,
                'error' => "Analytics not available for {$platform}",
            ],
        };
    }

    /**
     * Disconnect a platform
     */
    public function disconnectPlatform(int $userId, string $platform): bool
    {
        try {
            $deleted = DB::table('social_accounts')
                ->where('user_id', $userId)
                ->where('platform', $platform)
                ->delete();

            return $deleted > 0;

        } catch (\Exception $e) {
            Log::error("disconnectPlatform error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check platform configuration status
     */
    public function getPlatformStatus(): array
    {
        return [
            'facebook' => [
                'configured' => $this->facebook->isConfigured(),
                'name' => 'Facebook',
                'supports' => ['posts', 'images', 'videos', 'scheduling', 'analytics'],
            ],
            'instagram' => [
                'configured' => $this->facebook->isConfigured(),
                'name' => 'Instagram',
                'supports' => ['posts', 'images', 'videos', 'stories', 'analytics'],
            ],
            'twitter' => [
                'configured' => $this->twitter->isConfigured(),
                'name' => 'Twitter / X',
                'supports' => ['tweets', 'images', 'videos', 'analytics'],
            ],
            'linkedin' => [
                'configured' => !empty(env('LINKEDIN_CLIENT_ID')),
                'name' => 'LinkedIn',
                'supports' => ['posts', 'images', 'articles', 'analytics'],
            ],
            'tiktok' => [
                'configured' => !empty(env('TIKTOK_CLIENT_KEY')),
                'name' => 'TikTok',
                'supports' => ['videos', 'analytics'],
            ],
            'youtube' => [
                'configured' => !empty(env('YOUTUBE_CLIENT_ID')),
                'name' => 'YouTube',
                'supports' => ['videos', 'analytics'],
            ],
        ];
    }

    // LinkedIn methods (basic implementation)
    private function getLinkedInAuthUrl(string $redirectUri): array
    {
        $clientId = env('LINKEDIN_CLIENT_ID');
        $state = bin2hex(random_bytes(16));

        $params = http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => 'r_liteprofile r_emailaddress w_member_social',
            'state' => $state,
        ]);

        return [
            'success' => true,
            'url' => "https://www.linkedin.com/oauth/v2/authorization?{$params}",
            'state' => $state,
        ];
    }

    private function getLinkedInAccessToken(string $code, string $redirectUri): array
    {
        // Implementation similar to Facebook/Twitter
        // ... (يمكن إكمالها لاحقاً)
        return ['success' => false, 'error' => 'LinkedIn integration coming soon'];
    }

    private function publishToLinkedIn(string $accessToken, array $postData): array
    {
        return ['success' => false, 'error' => 'LinkedIn publishing coming soon'];
    }

    private function getLinkedInAnalytics(string $accessToken): array
    {
        return ['success' => false, 'error' => 'LinkedIn analytics coming soon'];
    }

    // TikTok methods (basic implementation)
    private function getTikTokAuthUrl(string $redirectUri): array
    {
        return ['success' => false, 'error' => 'TikTok integration coming soon'];
    }

    private function getTikTokAccessToken(string $code, string $redirectUri): array
    {
        return ['success' => false, 'error' => 'TikTok integration coming soon'];
    }

    private function publishToTikTok(string $accessToken, array $postData): array
    {
        return ['success' => false, 'error' => 'TikTok publishing coming soon'];
    }

    // YouTube methods (basic implementation)
    private function getYouTubeAuthUrl(string $redirectUri): array
    {
        $clientId = env('YOUTUBE_CLIENT_ID');
        $state = bin2hex(random_bytes(16));

        $params = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/youtube.upload https://www.googleapis.com/auth/youtube',
            'access_type' => 'offline',
            'state' => $state,
        ]);

        return [
            'success' => true,
            'url' => "https://accounts.google.com/o/oauth2/v2/auth?{$params}",
            'state' => $state,
        ];
    }

    private function getYouTubeAccessToken(string $code, string $redirectUri): array
    {
        return ['success' => false, 'error' => 'YouTube integration coming soon'];
    }

    private function publishToYouTube(string $accessToken, array $postData): array
    {
        return ['success' => false, 'error' => 'YouTube publishing coming soon'];
    }

    private function getYouTubeAnalytics(string $accessToken): array
    {
        return ['success' => false, 'error' => 'YouTube analytics coming soon'];
    }
}
