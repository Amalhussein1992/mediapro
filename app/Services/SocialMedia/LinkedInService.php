<?php

namespace App\Services\SocialMedia;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * LinkedIn API Service
 * FREE - No monthly costs
 * Supports: LinkedIn Posts, LinkedIn Pages
 */
class LinkedInService
{
    protected $clientId;
    protected $clientSecret;
    protected $apiVersion = 'v2';
    protected $baseUrl = 'https://api.linkedin.com';

    public function __construct()
    {
        $this->clientId = env('LINKEDIN_CLIENT_ID');
        $this->clientSecret = env('LINKEDIN_CLIENT_SECRET');
    }

    /**
     * Generate OAuth URL for user authorization
     */
    public function getAuthUrl(string $redirectUri, array $scopes = []): string
    {
        $defaultScopes = [
            'r_liteprofile',
            'r_emailaddress',
            'w_member_social',
            'r_organization_social',
            'w_organization_social',
        ];

        $scopes = !empty($scopes) ? $scopes : $defaultScopes;
        $scopeString = implode(' ', $scopes);

        $state = bin2hex(random_bytes(16));

        $params = http_build_query([
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scopeString,
            'state' => $state,
        ]);

        return "https://www.linkedin.com/oauth/v2/authorization?{$params}";
    }

    /**
     * Exchange authorization code for access token
     */
    public function getAccessToken(string $code, string $redirectUri): array
    {
        try {
            $response = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirectUri,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'access_token' => $data['access_token'],
                    'expires_in' => $data['expires_in'] ?? 5184000, // 60 days
                    'token_type' => 'Bearer',
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error_description'] ?? 'Failed to get access token',
            ];

        } catch (\Exception $e) {
            Log::error('LinkedIn getAccessToken error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get authenticated user profile
     */
    public function getUserProfile(string $accessToken): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/v2/me");

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'profile' => [
                        'id' => $data['id'],
                        'first_name' => $data['localizedFirstName'] ?? '',
                        'last_name' => $data['localizedLastName'] ?? '',
                    ],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get user profile',
            ];

        } catch (\Exception $e) {
            Log::error('LinkedIn getUserProfile error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get user's person URN (required for posting)
     */
    public function getPersonUrn(string $accessToken): array
    {
        $profile = $this->getUserProfile($accessToken);

        if ($profile['success']) {
            return [
                'success' => true,
                'urn' => 'urn:li:person:' . $profile['profile']['id'],
            ];
        }

        return $profile;
    }

    /**
     * Create a text post on LinkedIn
     */
    public function createPost(string $accessToken, array $postData): array
    {
        try {
            // Get person URN
            $urnResult = $this->getPersonUrn($accessToken);
            if (!$urnResult['success']) {
                return $urnResult;
            }

            $personUrn = $urnResult['urn'];

            // Build post payload
            $payload = [
                'author' => $personUrn,
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => [
                            'text' => $postData['text'] ?? '',
                        ],
                        'shareMediaCategory' => 'NONE',
                    ],
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
                ],
            ];

            // Add media if provided
            if (!empty($postData['media_url'])) {
                $payload['specificContent']['com.linkedin.ugc.ShareContent']['shareMediaCategory'] = 'IMAGE';
                $payload['specificContent']['com.linkedin.ugc.ShareContent']['media'] = [
                    [
                        'status' => 'READY',
                        'media' => $postData['media_url'],
                    ],
                ];
            }

            $response = Http::withToken($accessToken)
                ->withHeaders([
                    'X-Restli-Protocol-Version' => '2.0.0',
                ])
                ->post("{$this->baseUrl}/v2/ugcPosts", $payload);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'post_id' => $data['id'] ?? null,
                    'message' => 'Post published successfully',
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Failed to create post',
            ];

        } catch (\Exception $e) {
            Log::error('LinkedIn createPost error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Upload image to LinkedIn
     */
    public function uploadImage(string $accessToken, string $imageUrl): array
    {
        try {
            // Step 1: Register upload
            $urnResult = $this->getPersonUrn($accessToken);
            if (!$urnResult['success']) {
                return $urnResult;
            }

            $registerResponse = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/v2/assets?action=registerUpload", [
                    'registerUploadRequest' => [
                        'recipes' => ['urn:li:digitalmediaRecipe:feedshare-image'],
                        'owner' => $urnResult['urn'],
                        'serviceRelationships' => [
                            [
                                'relationshipType' => 'OWNER',
                                'identifier' => 'urn:li:userGeneratedContent',
                            ],
                        ],
                    ],
                ]);

            if (!$registerResponse->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to register upload',
                ];
            }

            $uploadData = $registerResponse->json();
            $uploadUrl = $uploadData['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
            $asset = $uploadData['value']['asset'];

            // Step 2: Upload image
            $imageContent = file_get_contents($imageUrl);
            $uploadResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->withBody($imageContent, 'application/octet-stream')
                ->post($uploadUrl);

            if ($uploadResponse->successful()) {
                return [
                    'success' => true,
                    'asset' => $asset,
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to upload image',
            ];

        } catch (\Exception $e) {
            Log::error('LinkedIn uploadImage error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get user's organizations (companies)
     */
    public function getUserOrganizations(string $accessToken): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/v2/organizationalEntityAcls?q=roleAssignee");

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'organizations' => $data['elements'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get organizations',
            ];

        } catch (\Exception $e) {
            Log::error('LinkedIn getUserOrganizations error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get post analytics
     */
    public function getPostAnalytics(string $accessToken, string $postUrn): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/v2/socialActions/{$postUrn}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'analytics' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get analytics',
            ];

        } catch (\Exception $e) {
            Log::error('LinkedIn getPostAnalytics error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Delete a post
     */
    public function deletePost(string $accessToken, string $postId): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->delete("{$this->baseUrl}/v2/ugcPosts/{$postId}");

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
            Log::error('LinkedIn deletePost error: ' . $e->getMessage());
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
