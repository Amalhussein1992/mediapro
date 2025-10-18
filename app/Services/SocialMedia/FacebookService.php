<?php

namespace App\Services\SocialMedia;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Facebook Graph API Service
 * FREE - No monthly costs
 * Supports: Facebook Pages, Instagram Business Accounts
 */
class FacebookService
{
    protected $appId;
    protected $appSecret;
    protected $apiVersion = 'v18.0';
    protected $baseUrl;

    public function __construct()
    {
        $this->appId = env('FACEBOOK_APP_ID');
        $this->appSecret = env('FACEBOOK_APP_SECRET');
        $this->baseUrl = "https://graph.facebook.com/{$this->apiVersion}";
    }

    /**
     * Generate OAuth URL for user authorization
     */
    public function getAuthUrl(string $redirectUri, array $scopes = []): string
    {
        $defaultScopes = [
            'public_profile',
            'pages_show_list',
            'pages_read_engagement',
            'pages_manage_posts',
            'pages_manage_engagement',
            'instagram_basic',
            'instagram_content_publish',
            'business_management',
        ];

        $scopes = !empty($scopes) ? $scopes : $defaultScopes;
        $scopeString = implode(',', $scopes);

        $params = http_build_query([
            'client_id' => $this->appId,
            'redirect_uri' => $redirectUri,
            'scope' => $scopeString,
            'response_type' => 'code',
            'state' => csrf_token(),
        ]);

        return "https://www.facebook.com/{$this->apiVersion}/dialog/oauth?{$params}";
    }

    /**
     * Exchange authorization code for access token
     */
    public function getAccessToken(string $code, string $redirectUri): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/oauth/access_token", [
                'client_id' => $this->appId,
                'client_secret' => $this->appSecret,
                'redirect_uri' => $redirectUri,
                'code' => $code,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Exchange short-lived token for long-lived token
                return $this->getLongLivedToken($data['access_token']);
            }

            return [
                'success' => false,
                'error' => $response->json()['error']['message'] ?? 'Failed to get access token',
            ];

        } catch (\Exception $e) {
            Log::error('Facebook getAccessToken error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Exchange short-lived token for long-lived token (60 days)
     */
    public function getLongLivedToken(string $shortToken): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/oauth/access_token", [
                'grant_type' => 'fb_exchange_token',
                'client_id' => $this->appId,
                'client_secret' => $this->appSecret,
                'fb_exchange_token' => $shortToken,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'access_token' => $data['access_token'],
                    'token_type' => $data['token_type'] ?? 'bearer',
                    'expires_in' => $data['expires_in'] ?? 5184000, // 60 days
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get long-lived token',
            ];

        } catch (\Exception $e) {
            Log::error('Facebook getLongLivedToken error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get user's Facebook pages
     */
    public function getUserPages(string $accessToken): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/me/accounts", [
                'access_token' => $accessToken,
                'fields' => 'id,name,access_token,category,picture,fan_count,instagram_business_account',
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'pages' => $data['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error']['message'] ?? 'Failed to get pages',
            ];

        } catch (\Exception $e) {
            Log::error('Facebook getUserPages error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Publish post to Facebook page
     */
    public function publishPost(string $pageId, string $pageAccessToken, array $postData): array
    {
        try {
            $endpoint = "{$this->baseUrl}/{$pageId}/feed";

            $params = [
                'access_token' => $pageAccessToken,
                'message' => $postData['message'] ?? '',
            ];

            // Add link if provided
            if (!empty($postData['link'])) {
                $params['link'] = $postData['link'];
            }

            // Add photo if provided
            if (!empty($postData['photo_url'])) {
                $endpoint = "{$this->baseUrl}/{$pageId}/photos";
                $params['url'] = $postData['photo_url'];
                $params['caption'] = $params['message'];
                unset($params['message']);
            }

            // Add video if provided
            if (!empty($postData['video_url'])) {
                $endpoint = "{$this->baseUrl}/{$pageId}/videos";
                $params['file_url'] = $postData['video_url'];
                $params['description'] = $params['message'];
                unset($params['message']);
            }

            // Schedule post if scheduled_time is provided
            if (!empty($postData['scheduled_time'])) {
                $params['published'] = false;
                $params['scheduled_publish_time'] = strtotime($postData['scheduled_time']);
            }

            $response = Http::post($endpoint, $params);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'post_id' => $data['id'] ?? $data['post_id'] ?? null,
                    'message' => 'Post published successfully',
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error']['message'] ?? 'Failed to publish post',
            ];

        } catch (\Exception $e) {
            Log::error('Facebook publishPost error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get Instagram Business Account from Facebook Page
     */
    public function getInstagramAccount(string $pageId, string $pageAccessToken): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/{$pageId}", [
                'access_token' => $pageAccessToken,
                'fields' => 'instagram_business_account{id,username,profile_picture_url,followers_count,media_count}',
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['instagram_business_account'])) {
                    return [
                        'success' => true,
                        'account' => $data['instagram_business_account'],
                    ];
                }

                return [
                    'success' => false,
                    'error' => 'No Instagram Business Account connected to this page',
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get Instagram account',
            ];

        } catch (\Exception $e) {
            Log::error('Facebook getInstagramAccount error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Publish to Instagram (Image or Video)
     */
    public function publishInstagramPost(string $igAccountId, string $pageAccessToken, array $postData): array
    {
        try {
            // Step 1: Create media container
            $containerEndpoint = "{$this->baseUrl}/{$igAccountId}/media";

            $containerParams = [
                'access_token' => $pageAccessToken,
                'caption' => $postData['caption'] ?? '',
            ];

            // Add image or video
            if (!empty($postData['image_url'])) {
                $containerParams['image_url'] = $postData['image_url'];
            } elseif (!empty($postData['video_url'])) {
                $containerParams['media_type'] = 'VIDEO';
                $containerParams['video_url'] = $postData['video_url'];
            } else {
                return [
                    'success' => false,
                    'error' => 'Instagram requires image_url or video_url',
                ];
            }

            $containerResponse = Http::post($containerEndpoint, $containerParams);

            if (!$containerResponse->successful()) {
                return [
                    'success' => false,
                    'error' => $containerResponse->json()['error']['message'] ?? 'Failed to create media container',
                ];
            }

            $containerId = $containerResponse->json()['id'];

            // Step 2: Publish the container
            $publishEndpoint = "{$this->baseUrl}/{$igAccountId}/media_publish";
            $publishResponse = Http::post($publishEndpoint, [
                'access_token' => $pageAccessToken,
                'creation_id' => $containerId,
            ]);

            if ($publishResponse->successful()) {
                $data = $publishResponse->json();

                return [
                    'success' => true,
                    'post_id' => $data['id'],
                    'message' => 'Instagram post published successfully',
                ];
            }

            return [
                'success' => false,
                'error' => $publishResponse->json()['error']['message'] ?? 'Failed to publish Instagram post',
            ];

        } catch (\Exception $e) {
            Log::error('Instagram publishPost error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get Facebook Page insights (analytics)
     */
    public function getPageInsights(string $pageId, string $pageAccessToken, array $metrics = []): array
    {
        try {
            $defaultMetrics = [
                'page_impressions',
                'page_engaged_users',
                'page_post_engagements',
                'page_fans',
                'page_views_total',
            ];

            $metrics = !empty($metrics) ? $metrics : $defaultMetrics;
            $metricsString = implode(',', $metrics);

            $response = Http::get("{$this->baseUrl}/{$pageId}/insights", [
                'access_token' => $pageAccessToken,
                'metric' => $metricsString,
                'period' => 'day',
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'insights' => $response->json()['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get insights',
            ];

        } catch (\Exception $e) {
            Log::error('Facebook getPageInsights error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get Instagram insights
     */
    public function getInstagramInsights(string $igAccountId, string $pageAccessToken): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/{$igAccountId}/insights", [
                'access_token' => $pageAccessToken,
                'metric' => 'impressions,reach,profile_views,follower_count',
                'period' => 'day',
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'insights' => $response->json()['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get Instagram insights',
            ];

        } catch (\Exception $e) {
            Log::error('Instagram getInsights error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Delete a post
     */
    public function deletePost(string $postId, string $accessToken): array
    {
        try {
            $response = Http::delete("{$this->baseUrl}/{$postId}", [
                'access_token' => $accessToken,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Post deleted successfully',
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to delete post',
            ];

        } catch (\Exception $e) {
            Log::error('Facebook deletePost error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check if service is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->appId) && !empty($this->appSecret);
    }
}
