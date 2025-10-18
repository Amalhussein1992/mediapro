<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AyrshareService
{
    protected $apiKey;
    protected $baseUrl = 'https://app.ayrshare.com/api';

    public function __construct()
    {
        // Get Ayrshare API key from database settings
        $setting = DB::table('app_settings')
            ->where('key', 'ayrshare_api_key')
            ->first();

        $this->apiKey = $setting?->value ?? env('AYRSHARE_API_KEY');
    }

    /**
     * Get authorization URL for connecting social media accounts
     */
    public function getAuthUrl(string $platform): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/profiles/generateJWT", [
                'domain' => env('APP_URL', 'https://yourdomain.com'),
                'privateKey' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'url' => "https://app.ayrshare.com/authorize?jwt={$data['jwt']}&platform={$platform}",
                    'jwt' => $data['jwt'],
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Failed to generate auth URL',
            ];

        } catch (\Exception $e) {
            Log::error('Ayrshare getAuthUrl error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get user's connected social media accounts
     */
    public function getConnectedAccounts(string $profileKey = null): array
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ];

            if ($profileKey) {
                $headers['Profile-Key'] = $profileKey;
            }

            $response = Http::withHeaders($headers)
                ->get("{$this->baseUrl}/profiles");

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'accounts' => $data['profiles'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Failed to get accounts',
            ];

        } catch (\Exception $e) {
            Log::error('Ayrshare getConnectedAccounts error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create and publish a post to social media
     */
    public function createPost(array $postData, string $profileKey = null): array
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ];

            if ($profileKey) {
                $headers['Profile-Key'] = $profileKey;
            }

            // Build the post payload
            $payload = [
                'post' => $postData['content'] ?? '',
                'platforms' => $postData['platforms'] ?? ['facebook', 'twitter', 'instagram'],
            ];

            // Add media URLs if provided
            if (!empty($postData['media'])) {
                $payload['mediaUrls'] = is_array($postData['media'])
                    ? $postData['media']
                    : [$postData['media']];
            }

            // Add scheduling if provided
            if (!empty($postData['scheduled_at'])) {
                $payload['scheduleDate'] = date('Y-m-d\TH:i:s\Z', strtotime($postData['scheduled_at']));
            }

            // Add hashtags if provided
            if (!empty($postData['hashtags'])) {
                $hashtags = is_array($postData['hashtags'])
                    ? implode(' ', $postData['hashtags'])
                    : $postData['hashtags'];
                $payload['post'] .= "\n\n" . $hashtags;
            }

            // Add platform-specific configurations
            if (!empty($postData['platform_specific'])) {
                $payload = array_merge($payload, $postData['platform_specific']);
            }

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post("{$this->baseUrl}/post", $payload);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'id' => $data['id'] ?? null,
                    'status' => $data['status'] ?? 'published',
                    'post_ids' => $data['postIds'] ?? [],
                    'errors' => $data['errors'] ?? [],
                ];
            }

            $error = $response->json();
            Log::error('Ayrshare createPost failed', ['error' => $error]);

            return [
                'success' => false,
                'error' => $error['message'] ?? 'Failed to create post',
                'errors' => $error['errors'] ?? [],
            ];

        } catch (\Exception $e) {
            Log::error('Ayrshare createPost error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Delete a scheduled post
     */
    public function deletePost(string $postId, string $profileKey = null): array
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ];

            if ($profileKey) {
                $headers['Profile-Key'] = $profileKey;
            }

            $response = Http::withHeaders($headers)
                ->delete("{$this->baseUrl}/post/{$postId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Post deleted successfully',
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Failed to delete post',
            ];

        } catch (\Exception $e) {
            Log::error('Ayrshare deletePost error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get analytics for posts
     */
    public function getAnalytics(string $platform = null, string $profileKey = null): array
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ];

            if ($profileKey) {
                $headers['Profile-Key'] = $profileKey;
            }

            $url = "{$this->baseUrl}/analytics/post";
            if ($platform) {
                $url .= "?platform={$platform}";
            }

            $response = Http::withHeaders($headers)->get($url);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'analytics' => $data['posts'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Failed to get analytics',
            ];

        } catch (\Exception $e) {
            Log::error('Ayrshare getAnalytics error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get social media account analytics
     */
    public function getAccountAnalytics(string $platform, string $profileKey = null): array
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ];

            if ($profileKey) {
                $headers['Profile-Key'] = $profileKey;
            }

            $response = Http::withHeaders($headers)
                ->get("{$this->baseUrl}/analytics/social?platforms[]={$platform}");

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'analytics' => $data,
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Failed to get account analytics',
            ];

        } catch (\Exception $e) {
            Log::error('Ayrshare getAccountAnalytics error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Disconnect a social media account
     */
    public function disconnectAccount(string $platform, string $profileKey = null): array
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ];

            if ($profileKey) {
                $headers['Profile-Key'] = $profileKey;
            }

            $response = Http::withHeaders($headers)
                ->delete("{$this->baseUrl}/profiles/{$platform}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Account disconnected successfully',
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Failed to disconnect account',
            ];

        } catch (\Exception $e) {
            Log::error('Ayrshare disconnectAccount error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get post history
     */
    public function getPostHistory(int $limit = 50, string $profileKey = null): array
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ];

            if ($profileKey) {
                $headers['Profile-Key'] = $profileKey;
            }

            $response = Http::withHeaders($headers)
                ->get("{$this->baseUrl}/history?limit={$limit}");

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'posts' => $data['posts'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Failed to get post history',
            ];

        } catch (\Exception $e) {
            Log::error('Ayrshare getPostHistory error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Upload media to Ayrshare
     */
    public function uploadMedia(string $fileUrl, string $profileKey = null): array
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ];

            if ($profileKey) {
                $headers['Profile-Key'] = $profileKey;
            }

            $response = Http::withHeaders($headers)
                ->post("{$this->baseUrl}/media/upload", [
                    'url' => $fileUrl,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'url' => $data['url'] ?? $fileUrl,
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Failed to upload media',
            ];

        } catch (\Exception $e) {
            Log::error('Ayrshare uploadMedia error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check if Ayrshare is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Get current API key
     */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }
}
