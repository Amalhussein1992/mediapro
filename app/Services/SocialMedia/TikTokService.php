<?php

namespace App\Services\SocialMedia;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * TikTok API Service
 * FREE - No monthly costs
 * Supports: TikTok Video Publishing
 * Note: Requires Business Account approval from TikTok
 */
class TikTokService
{
    protected $clientKey;
    protected $clientSecret;
    protected $baseUrl = 'https://open-api.tiktok.com';

    public function __construct()
    {
        $this->clientKey = env('TIKTOK_CLIENT_KEY');
        $this->clientSecret = env('TIKTOK_CLIENT_SECRET');
    }

    /**
     * Generate OAuth URL for user authorization
     */
    public function getAuthUrl(string $redirectUri, string $state = null): array
    {
        try {
            $state = $state ?? bin2hex(random_bytes(16));
            $codeVerifier = $this->generateCodeVerifier();

            $scopes = [
                'user.info.basic',
                'video.publish',
                'video.upload',
            ];

            $params = http_build_query([
                'client_key' => $this->clientKey,
                'scope' => implode(',', $scopes),
                'response_type' => 'code',
                'redirect_uri' => $redirectUri,
                'state' => $state,
            ]);

            return [
                'success' => true,
                'url' => "https://www.tiktok.com/auth/authorize/?{$params}",
                'state' => $state,
                'code_verifier' => $codeVerifier,
            ];

        } catch (\Exception $e) {
            Log::error('TikTok getAuthUrl error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Exchange authorization code for access token
     */
    public function getAccessToken(string $code, string $redirectUri): array
    {
        try {
            $response = Http::asForm()->post("{$this->baseUrl}/oauth/access_token/", [
                'client_key' => $this->clientKey,
                'client_secret' => $this->clientSecret,
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];

                return [
                    'success' => true,
                    'access_token' => $data['access_token'] ?? '',
                    'refresh_token' => $data['refresh_token'] ?? '',
                    'expires_in' => $data['expires_in'] ?? 86400,
                    'open_id' => $data['open_id'] ?? '',
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Failed to get access token',
            ];

        } catch (\Exception $e) {
            Log::error('TikTok getAccessToken error: ' . $e->getMessage());
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
            $response = Http::asForm()->post("{$this->baseUrl}/oauth/refresh_token/", [
                'client_key' => $this->clientKey,
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];

                return [
                    'success' => true,
                    'access_token' => $data['access_token'] ?? '',
                    'refresh_token' => $data['refresh_token'] ?? '',
                    'expires_in' => $data['expires_in'] ?? 86400,
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to refresh token',
            ];

        } catch (\Exception $e) {
            Log::error('TikTok refreshAccessToken error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get user info
     */
    public function getUserInfo(string $accessToken, string $openId): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/user/info/", [
                    'open_id' => $openId,
                    'fields' => 'open_id,union_id,avatar_url,display_name,follower_count,following_count,likes_count,video_count',
                ]);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];
                $user = $data['user'] ?? [];

                return [
                    'success' => true,
                    'user' => $user,
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get user info',
            ];

        } catch (\Exception $e) {
            Log::error('TikTok getUserInfo error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Initialize video upload
     */
    public function initializeUpload(string $accessToken, string $openId): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/share/video/upload/", [
                    'open_id' => $openId,
                ]);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];

                return [
                    'success' => true,
                    'upload_url' => $data['upload_url'] ?? '',
                    'video_id' => $data['video_id'] ?? '',
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to initialize upload',
            ];

        } catch (\Exception $e) {
            Log::error('TikTok initializeUpload error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Upload video file
     */
    public function uploadVideo(string $uploadUrl, string $videoPath): array
    {
        try {
            $videoContent = file_get_contents($videoPath);

            $response = Http::withBody($videoContent, 'video/mp4')
                ->put($uploadUrl);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Video uploaded successfully',
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to upload video',
            ];

        } catch (\Exception $e) {
            Log::error('TikTok uploadVideo error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Publish video
     */
    public function publishVideo(string $accessToken, string $openId, array $videoData): array
    {
        try {
            // Step 1: Initialize upload
            $initResult = $this->initializeUpload($accessToken, $openId);
            if (!$initResult['success']) {
                return $initResult;
            }

            // Step 2: Upload video
            $uploadResult = $this->uploadVideo(
                $initResult['upload_url'],
                $videoData['video_path']
            );

            if (!$uploadResult['success']) {
                return $uploadResult;
            }

            // Step 3: Publish video
            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/share/video/publish/", [
                    'open_id' => $openId,
                    'video_id' => $initResult['video_id'],
                    'description' => $videoData['description'] ?? '',
                    'privacy_level' => $videoData['privacy_level'] ?? 'SELF_ONLY', // SELF_ONLY, MUTUAL_FOLLOW_FRIENDS, PUBLIC_TO_EVERYONE
                    'disable_duet' => $videoData['disable_duet'] ?? false,
                    'disable_comment' => $videoData['disable_comment'] ?? false,
                    'disable_stitch' => $videoData['disable_stitch'] ?? false,
                ]);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];

                return [
                    'success' => true,
                    'video_id' => $data['publish_id'] ?? '',
                    'message' => 'Video published successfully',
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Failed to publish video',
            ];

        } catch (\Exception $e) {
            Log::error('TikTok publishVideo error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get video list
     */
    public function getVideoList(string $accessToken, string $openId, int $maxCount = 20): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/video/list/", [
                    'open_id' => $openId,
                    'fields' => 'id,create_time,cover_image_url,share_url,video_description,duration,height,width,title,embed_html,embed_link,like_count,comment_count,share_count,view_count',
                    'max_count' => $maxCount,
                ]);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];

                return [
                    'success' => true,
                    'videos' => $data['videos'] ?? [],
                    'cursor' => $data['cursor'] ?? 0,
                    'has_more' => $data['has_more'] ?? false,
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get video list',
            ];

        } catch (\Exception $e) {
            Log::error('TikTok getVideoList error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get video analytics
     */
    public function getVideoAnalytics(string $accessToken, string $openId, array $videoIds): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/video/query/", [
                    'open_id' => $openId,
                    'filters' => [
                        'video_ids' => $videoIds,
                    ],
                    'fields' => 'id,like_count,comment_count,share_count,view_count',
                ]);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];

                return [
                    'success' => true,
                    'videos' => $data['videos'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get video analytics',
            ];

        } catch (\Exception $e) {
            Log::error('TikTok getVideoAnalytics error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate code verifier for OAuth
     */
    private function generateCodeVerifier(): string
    {
        $length = 128;
        $bytes = random_bytes($length);
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }

    /**
     * Check if service is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->clientKey) && !empty($this->clientSecret);
    }
}
