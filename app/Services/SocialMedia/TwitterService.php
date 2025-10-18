<?php

namespace App\Services\SocialMedia;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Twitter/X API v2 Service
 * FREE Tier Available - Basic access
 * $100/month for higher limits
 */
class TwitterService
{
    protected $apiKey;
    protected $apiSecret;
    protected $bearerToken;
    protected $baseUrl = 'https://api.twitter.com/2';
    protected $oauthUrl = 'https://api.twitter.com/oauth';

    public function __construct()
    {
        $this->apiKey = env('TWITTER_API_KEY');
        $this->apiSecret = env('TWITTER_API_SECRET');
        $this->bearerToken = env('TWITTER_BEARER_TOKEN');
    }

    /**
     * Generate OAuth 2.0 authorization URL
     */
    public function getAuthUrl(string $redirectUri, string $state = null): array
    {
        try {
            // Generate code verifier and challenge for PKCE
            $codeVerifier = $this->generateCodeVerifier();
            $codeChallenge = $this->generateCodeChallenge($codeVerifier);

            $scopes = [
                'tweet.read',
                'tweet.write',
                'users.read',
                'offline.access', // For refresh token
            ];

            $params = http_build_query([
                'response_type' => 'code',
                'client_id' => $this->apiKey,
                'redirect_uri' => $redirectUri,
                'scope' => implode(' ', $scopes),
                'state' => $state ?? bin2hex(random_bytes(16)),
                'code_challenge' => $codeChallenge,
                'code_challenge_method' => 'S256',
            ]);

            return [
                'success' => true,
                'url' => "https://twitter.com/i/oauth2/authorize?{$params}",
                'code_verifier' => $codeVerifier,
                'state' => $state,
            ];

        } catch (\Exception $e) {
            Log::error('Twitter getAuthUrl error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Exchange authorization code for access token
     */
    public function getAccessToken(string $code, string $redirectUri, string $codeVerifier): array
    {
        try {
            $response = Http::asForm()->post('https://api.twitter.com/2/oauth2/token', [
                'code' => $code,
                'grant_type' => 'authorization_code',
                'client_id' => $this->apiKey,
                'redirect_uri' => $redirectUri,
                'code_verifier' => $codeVerifier,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'] ?? null,
                    'expires_in' => $data['expires_in'] ?? 7200,
                    'token_type' => $data['token_type'] ?? 'bearer',
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error_description'] ?? 'Failed to get access token',
            ];

        } catch (\Exception $e) {
            Log::error('Twitter getAccessToken error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Refresh access token
     */
    public function refreshAccessToken(string $refreshToken): array
    {
        try {
            $response = Http::asForm()->post('https://api.twitter.com/2/oauth2/token', [
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
                'client_id' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'] ?? $refreshToken,
                    'expires_in' => $data['expires_in'] ?? 7200,
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to refresh token',
            ];

        } catch (\Exception $e) {
            Log::error('Twitter refreshAccessToken error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get authenticated user info
     */
    public function getUserInfo(string $accessToken): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/users/me", [
                    'user.fields' => 'id,name,username,profile_image_url,public_metrics',
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'user' => $response->json()['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get user info',
            ];

        } catch (\Exception $e) {
            Log::error('Twitter getUserInfo error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Post a tweet
     */
    public function createTweet(string $accessToken, array $tweetData): array
    {
        try {
            $payload = [
                'text' => $tweetData['text'] ?? '',
            ];

            // Add media if provided
            if (!empty($tweetData['media_ids'])) {
                $payload['media'] = [
                    'media_ids' => $tweetData['media_ids'],
                ];
            }

            // Add poll if provided
            if (!empty($tweetData['poll'])) {
                $payload['poll'] = $tweetData['poll'];
            }

            // Reply to tweet
            if (!empty($tweetData['reply_to'])) {
                $payload['reply'] = [
                    'in_reply_to_tweet_id' => $tweetData['reply_to'],
                ];
            }

            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/tweets", $payload);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'tweet_id' => $data['data']['id'] ?? null,
                    'text' => $data['data']['text'] ?? '',
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['detail'] ?? 'Failed to create tweet',
            ];

        } catch (\Exception $e) {
            Log::error('Twitter createTweet error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Upload media to Twitter
     */
    public function uploadMedia(string $accessToken, string $mediaUrl): array
    {
        try {
            // Download media
            $mediaContent = file_get_contents($mediaUrl);

            if ($mediaContent === false) {
                return [
                    'success' => false,
                    'error' => 'Failed to download media',
                ];
            }

            // Save temporarily
            $tempFile = tempnam(sys_get_temp_dir(), 'twitter_media_');
            file_put_contents($tempFile, $mediaContent);

            // Upload to Twitter using v1.1 API (media upload is still on v1.1)
            $response = Http::withToken($accessToken)
                ->attach('media', file_get_contents($tempFile), basename($tempFile))
                ->post('https://upload.twitter.com/1.1/media/upload.json');

            unlink($tempFile);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'media_id' => $data['media_id_string'],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to upload media',
            ];

        } catch (\Exception $e) {
            Log::error('Twitter uploadMedia error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Delete a tweet
     */
    public function deleteTweet(string $accessToken, string $tweetId): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->delete("{$this->baseUrl}/tweets/{$tweetId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Tweet deleted successfully',
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to delete tweet',
            ];

        } catch (\Exception $e) {
            Log::error('Twitter deleteTweet error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get user's tweets
     */
    public function getUserTweets(string $accessToken, string $userId, int $maxResults = 10): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/users/{$userId}/tweets", [
                    'max_results' => $maxResults,
                    'tweet.fields' => 'created_at,public_metrics,attachments',
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'tweets' => $response->json()['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get tweets',
            ];

        } catch (\Exception $e) {
            Log::error('Twitter getUserTweets error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get tweet analytics
     */
    public function getTweetMetrics(string $accessToken, string $tweetId): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/tweets/{$tweetId}", [
                    'tweet.fields' => 'public_metrics,non_public_metrics,organic_metrics',
                ]);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];

                return [
                    'success' => true,
                    'metrics' => [
                        'public' => $data['public_metrics'] ?? [],
                        'non_public' => $data['non_public_metrics'] ?? [],
                        'organic' => $data['organic_metrics'] ?? [],
                    ],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get tweet metrics',
            ];

        } catch (\Exception $e) {
            Log::error('Twitter getTweetMetrics error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate code verifier for PKCE
     */
    private function generateCodeVerifier(): string
    {
        $length = 128;
        $bytes = random_bytes($length);
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }

    /**
     * Generate code challenge from verifier
     */
    private function generateCodeChallenge(string $verifier): string
    {
        $hash = hash('sha256', $verifier, true);
        return rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');
    }

    /**
     * Check if service is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->apiSecret);
    }
}
