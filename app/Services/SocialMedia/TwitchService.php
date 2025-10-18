<?php

namespace App\Services\SocialMedia;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Twitch API Service
 * FREE - No monthly costs
 * Supports: Stream Management, Clips, VODs
 */
class TwitchService
{
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl = 'https://api.twitch.tv/helix';
    protected $authUrl = 'https://id.twitch.tv/oauth2';

    public function __construct()
    {
        $this->clientId = env('TWITCH_CLIENT_ID');
        $this->clientSecret = env('TWITCH_CLIENT_SECRET');
    }

    /**
     * Generate OAuth URL for user authorization
     */
    public function getAuthUrl(string $redirectUri, string $state = null): string
    {
        $state = $state ?? bin2hex(random_bytes(16));

        $scopes = [
            'user:read:email',
            'user:read:broadcast',
            'channel:manage:broadcast',
            'clips:edit',
            'channel:read:stream_key',
            'analytics:read:extensions',
            'analytics:read:games',
        ];

        $params = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => implode(' ', $scopes),
            'state' => $state,
            'force_verify' => 'false',
        ]);

        return "{$this->authUrl}/authorize?{$params}";
    }

    /**
     * Exchange authorization code for access token
     */
    public function getAccessToken(string $code, string $redirectUri): array
    {
        try {
            $response = Http::asForm()->post("{$this->authUrl}/token", [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $code,
                'grant_type' => 'authorization_code',
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
                'error' => $response->json()['message'] ?? 'Failed to get access token',
            ];

        } catch (\Exception $e) {
            Log::error('Twitch getAccessToken error: ' . $e->getMessage());
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
            $response = Http::asForm()->post("{$this->authUrl}/token", [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
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
            Log::error('Twitch refreshAccessToken error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get user info
     */
    public function getUserInfo(string $accessToken): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Client-Id' => $this->clientId,
            ])->get("{$this->baseUrl}/users");

            if ($response->successful()) {
                $data = $response->json()['data'][0] ?? [];

                return [
                    'success' => true,
                    'user' => [
                        'id' => $data['id'] ?? '',
                        'login' => $data['login'] ?? '',
                        'display_name' => $data['display_name'] ?? '',
                        'email' => $data['email'] ?? '',
                        'profile_image_url' => $data['profile_image_url'] ?? '',
                        'broadcaster_type' => $data['broadcaster_type'] ?? '',
                        'description' => $data['description'] ?? '',
                        'view_count' => $data['view_count'] ?? 0,
                    ],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get user info',
            ];

        } catch (\Exception $e) {
            Log::error('Twitch getUserInfo error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get channel information
     */
    public function getChannelInfo(string $accessToken, string $broadcasterId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Client-Id' => $this->clientId,
            ])->get("{$this->baseUrl}/channels", [
                'broadcaster_id' => $broadcasterId,
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'][0] ?? [];

                return [
                    'success' => true,
                    'channel' => $data,
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get channel info',
            ];

        } catch (\Exception $e) {
            Log::error('Twitch getChannelInfo error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Update channel information (title, game, language, etc.)
     */
    public function updateChannelInfo(string $accessToken, string $broadcasterId, array $channelData): array
    {
        try {
            $payload = [];

            if (isset($channelData['game_id'])) {
                $payload['game_id'] = $channelData['game_id'];
            }

            if (isset($channelData['title'])) {
                $payload['title'] = $channelData['title'];
            }

            if (isset($channelData['broadcaster_language'])) {
                $payload['broadcaster_language'] = $channelData['broadcaster_language'];
            }

            if (isset($channelData['delay'])) {
                $payload['delay'] = $channelData['delay'];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Client-Id' => $this->clientId,
                'Content-Type' => 'application/json',
            ])->patch("{$this->baseUrl}/channels?broadcaster_id={$broadcasterId}", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Channel updated successfully',
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Failed to update channel',
            ];

        } catch (\Exception $e) {
            Log::error('Twitch updateChannelInfo error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get stream information
     */
    public function getStreamInfo(string $accessToken, string $broadcasterId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Client-Id' => $this->clientId,
            ])->get("{$this->baseUrl}/streams", [
                'user_id' => $broadcasterId,
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];

                return [
                    'success' => true,
                    'is_live' => !empty($data),
                    'stream' => $data[0] ?? null,
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get stream info',
            ];

        } catch (\Exception $e) {
            Log::error('Twitch getStreamInfo error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a clip from current stream
     */
    public function createClip(string $accessToken, string $broadcasterId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Client-Id' => $this->clientId,
            ])->post("{$this->baseUrl}/clips", [
                'broadcaster_id' => $broadcasterId,
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'][0] ?? [];

                return [
                    'success' => true,
                    'clip_id' => $data['id'] ?? '',
                    'edit_url' => $data['edit_url'] ?? '',
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Failed to create clip',
            ];

        } catch (\Exception $e) {
            Log::error('Twitch createClip error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get clips
     */
    public function getClips(string $accessToken, string $broadcasterId, int $limit = 20): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Client-Id' => $this->clientId,
            ])->get("{$this->baseUrl}/clips", [
                'broadcaster_id' => $broadcasterId,
                'first' => $limit,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'clips' => $data['data'] ?? [],
                    'pagination' => $data['pagination'] ?? null,
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get clips',
            ];

        } catch (\Exception $e) {
            Log::error('Twitch getClips error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get videos (VODs)
     */
    public function getVideos(string $accessToken, string $broadcasterId, int $limit = 20): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Client-Id' => $this->clientId,
            ])->get("{$this->baseUrl}/videos", [
                'user_id' => $broadcasterId,
                'first' => $limit,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'videos' => $data['data'] ?? [],
                    'pagination' => $data['pagination'] ?? null,
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get videos',
            ];

        } catch (\Exception $e) {
            Log::error('Twitch getVideos error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get channel followers
     */
    public function getFollowers(string $accessToken, string $broadcasterId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Client-Id' => $this->clientId,
            ])->get("{$this->baseUrl}/users/follows", [
                'to_id' => $broadcasterId,
                'first' => 100,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'total' => $data['total'] ?? 0,
                    'followers' => $data['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get followers',
            ];

        } catch (\Exception $e) {
            Log::error('Twitch getFollowers error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get analytics
     */
    public function getAnalytics(string $accessToken, string $broadcasterId): array
    {
        try {
            // Get stream analytics
            $streamResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Client-Id' => $this->clientId,
            ])->get("{$this->baseUrl}/analytics/extensions", [
                'extension_id' => $broadcasterId,
            ]);

            $followerResult = $this->getFollowers($accessToken, $broadcasterId);
            $streamResult = $this->getStreamInfo($accessToken, $broadcasterId);

            return [
                'success' => true,
                'analytics' => [
                    'followers' => $followerResult['total'] ?? 0,
                    'is_live' => $streamResult['is_live'] ?? false,
                    'stream' => $streamResult['stream'] ?? null,
                ],
            ];

        } catch (\Exception $e) {
            Log::error('Twitch getAnalytics error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Search for games (categories)
     */
    public function searchGames(string $accessToken, string $query): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Client-Id' => $this->clientId,
            ])->get("{$this->baseUrl}/games", [
                'name' => $query,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'games' => $response->json()['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to search games',
            ];

        } catch (\Exception $e) {
            Log::error('Twitch searchGames error: ' . $e->getMessage());
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
