<?php

namespace App\Services\SocialMedia;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Snapchat Marketing API Service
 * FREE - No monthly costs
 * Supports: Snap Publishing, Stories
 * Note: Requires Business Account and approval
 */
class SnapchatService
{
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl = 'https://adsapi.snapchat.com/v1';
    protected $authUrl = 'https://accounts.snapchat.com';

    public function __construct()
    {
        $this->clientId = env('SNAPCHAT_CLIENT_ID');
        $this->clientSecret = env('SNAPCHAT_CLIENT_SECRET');
    }

    /**
     * Generate OAuth URL for user authorization
     */
    public function getAuthUrl(string $redirectUri, string $state = null): string
    {
        $state = $state ?? bin2hex(random_bytes(16));

        $scopes = [
            'snapchat-marketing-api',
            'snapchat-profile-api',
        ];

        $params = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => implode(' ', $scopes),
            'state' => $state,
        ]);

        return "{$this->authUrl}/oauth2/authorize?{$params}";
    }

    /**
     * Exchange authorization code for access token
     */
    public function getAccessToken(string $code, string $redirectUri): array
    {
        try {
            $response = Http::asForm()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->post("{$this->authUrl}/oauth2/token", [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $redirectUri,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'] ?? null,
                    'expires_in' => $data['expires_in'] ?? 3600,
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error_description'] ?? 'Failed to get access token',
            ];

        } catch (\Exception $e) {
            Log::error('Snapchat getAccessToken error: ' . $e->getMessage());
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
            $response = Http::asForm()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->post("{$this->authUrl}/oauth2/token", [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'] ?? $refreshToken,
                    'expires_in' => $data['expires_in'] ?? 3600,
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to refresh token',
            ];

        } catch (\Exception $e) {
            Log::error('Snapchat refreshAccessToken error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get user profile
     */
    public function getUserProfile(string $accessToken): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/me");

            if ($response->successful()) {
                $data = $response->json()['me'] ?? [];

                return [
                    'success' => true,
                    'profile' => [
                        'id' => $data['id'] ?? '',
                        'email' => $data['email'] ?? '',
                        'display_name' => $data['display_name'] ?? '',
                        'bitmoji_avatar_url' => $data['bitmoji_avatar_url'] ?? null,
                    ],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get user profile',
            ];

        } catch (\Exception $e) {
            Log::error('Snapchat getUserProfile error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get user's ad accounts (organizations)
     */
    public function getAdAccounts(string $accessToken): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/me/organizations");

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'organizations' => $data['organizations'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get ad accounts',
            ];

        } catch (\Exception $e) {
            Log::error('Snapchat getAdAccounts error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Upload media (photo or video)
     */
    public function uploadMedia(string $accessToken, string $adAccountId, string $mediaUrl, string $type = 'IMAGE'): array
    {
        try {
            // Download media first
            $mediaContent = file_get_contents($mediaUrl);
            if ($mediaContent === false) {
                return [
                    'success' => false,
                    'error' => 'Failed to download media',
                ];
            }

            // Create media
            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/adaccounts/{$adAccountId}/media", [
                    'media' => [
                        [
                            'name' => 'Media ' . time(),
                            'type' => $type, // IMAGE or VIDEO
                            'ad_account_id' => $adAccountId,
                        ],
                    ],
                ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to create media entry',
                ];
            }

            $mediaData = $response->json()['media'][0] ?? [];
            $uploadUrl = $mediaData['upload_urls'][0] ?? '';
            $mediaId = $mediaData['id'] ?? '';

            if (empty($uploadUrl)) {
                return [
                    'success' => false,
                    'error' => 'No upload URL received',
                ];
            }

            // Upload to signed URL
            $uploadResponse = Http::withBody($mediaContent, 'application/octet-stream')
                ->put($uploadUrl);

            if ($uploadResponse->successful()) {
                return [
                    'success' => true,
                    'media_id' => $mediaId,
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to upload media content',
            ];

        } catch (\Exception $e) {
            Log::error('Snapchat uploadMedia error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create Story (Snap Ad)
     */
    public function createStory(string $accessToken, string $adAccountId, array $storyData): array
    {
        try {
            // Note: Snapchat doesn't have direct "post to story" API like Instagram
            // This creates a Story Ad which can be used for marketing
            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/adaccounts/{$adAccountId}/creatives", [
                    'creatives' => [
                        [
                            'name' => $storyData['name'] ?? 'Story ' . time(),
                            'ad_account_id' => $adAccountId,
                            'type' => 'SNAP_AD',
                            'brand_name' => $storyData['brand_name'] ?? '',
                            'headline' => $storyData['headline'] ?? '',
                            'top_snap_media_id' => $storyData['media_id'],
                            'call_to_action' => $storyData['cta'] ?? 'VIEW_MORE',
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $creative = $data['creatives'][0] ?? [];

                return [
                    'success' => true,
                    'creative_id' => $creative['id'] ?? '',
                    'message' => 'Story created successfully',
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['request_status'] ?? 'Failed to create story',
            ];

        } catch (\Exception $e) {
            Log::error('Snapchat createStory error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get creatives (posts)
     */
    public function getCreatives(string $accessToken, string $adAccountId): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/adaccounts/{$adAccountId}/creatives");

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'creatives' => $data['creatives'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get creatives',
            ];

        } catch (\Exception $e) {
            Log::error('Snapchat getCreatives error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get analytics/stats
     */
    public function getStats(string $accessToken, string $adAccountId, array $filters = []): array
    {
        try {
            $params = [
                'granularity' => $filters['granularity'] ?? 'DAY',
                'start_time' => $filters['start_time'] ?? date('Y-m-d', strtotime('-7 days')),
                'end_time' => $filters['end_time'] ?? date('Y-m-d'),
                'fields' => implode(',', [
                    'impressions',
                    'swipes',
                    'spend',
                    'screen_time_millis',
                ]),
            ];

            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/adaccounts/{$adAccountId}/stats", $params);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'stats' => $data['timeseries_stats'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get stats',
            ];

        } catch (\Exception $e) {
            Log::error('Snapchat getStats error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Delete creative
     */
    public function deleteCreative(string $accessToken, string $creativeId): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->delete("{$this->baseUrl}/creatives/{$creativeId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Creative deleted successfully',
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to delete creative',
            ];

        } catch (\Exception $e) {
            Log::error('Snapchat deleteCreative error: ' . $e->getMessage());
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
        return !empty($this->clientId) && !empty($this->clientSecret);
    }
}
