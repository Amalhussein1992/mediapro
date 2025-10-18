<?php

namespace App\Services;

use App\Models\SocialAccount;
use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class SocialMediaPublisher
{
    /**
     * Publish a post to a specific platform
     */
    public function publish(Post $post, SocialAccount $account): array
    {
        try {
            $result = match ($account->platform) {
                'facebook' => $this->publishToFacebook($post, $account),
                'instagram' => $this->publishToInstagram($post, $account),
                'twitter' => $this->publishToTwitter($post, $account),
                'linkedin' => $this->publishToLinkedIn($post, $account),
                'tiktok' => $this->publishToTikTok($post, $account),
                'youtube' => $this->publishToYouTube($post, $account),
                'pinterest' => $this->publishToPinterest($post, $account),
                default => throw new Exception("Unsupported platform: {$account->platform}")
            };

            Log::info("Successfully published to {$account->platform}", [
                'post_id' => $post->id,
                'account_id' => $account->id,
                'platform_post_id' => $result['id'] ?? null
            ]);

            return [
                'success' => true,
                'platform_post_id' => $result['id'] ?? null,
                'platform_url' => $result['url'] ?? null,
                'published_at' => now(),
            ];
        } catch (Exception $e) {
            Log::error("Failed to publish to {$account->platform}", [
                'post_id' => $post->id,
                'account_id' => $account->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Publish to Facebook Page
     */
    private function publishToFacebook(Post $post, SocialAccount $account): array
    {
        // Get page access token (user token needs to be exchanged for page token)
        $pageAccessToken = $this->getFacebookPageAccessToken($account->access_token);

        $endpoint = "https://graph.facebook.com/v18.0/{$account->platform_user_id}/";

        // Check if post has media
        if ($post->media && count($post->media) > 0) {
            $media = $post->media[0];

            if ($media['type'] === 'video') {
                // Video post
                $endpoint .= 'videos';
                $response = Http::post($endpoint, [
                    'description' => $post->caption,
                    'file_url' => $media['url'],
                    'access_token' => $pageAccessToken,
                ]);
            } elseif (count($post->media) > 1) {
                // Multiple photos (album)
                $photoIds = [];
                foreach ($post->media as $item) {
                    $photoResponse = Http::post("https://graph.facebook.com/v18.0/{$account->platform_user_id}/photos", [
                        'url' => $item['url'],
                        'published' => false,
                        'access_token' => $pageAccessToken,
                    ]);
                    $photoIds[] = ['media_fbid' => $photoResponse->json()['id']];
                }

                $response = Http::post("https://graph.facebook.com/v18.0/{$account->platform_user_id}/feed", [
                    'message' => $post->caption,
                    'attached_media' => $photoIds,
                    'access_token' => $pageAccessToken,
                ]);
            } else {
                // Single photo
                $endpoint .= 'photos';
                $response = Http::post($endpoint, [
                    'url' => $media['url'],
                    'caption' => $post->caption,
                    'access_token' => $pageAccessToken,
                ]);
            }
        } else {
            // Text only post
            $endpoint .= 'feed';
            $response = Http::post($endpoint, [
                'message' => $post->caption,
                'access_token' => $pageAccessToken,
            ]);
        }

        if (!$response->successful()) {
            throw new Exception("Facebook API error: " . $response->body());
        }

        $data = $response->json();
        return [
            'id' => $data['id'] ?? $data['post_id'],
            'url' => "https://facebook.com/{$data['id'] ?? $data['post_id']}"
        ];
    }

    /**
     * Get Facebook Page Access Token
     */
    private function getFacebookPageAccessToken(string $userAccessToken): string
    {
        $response = Http::get('https://graph.facebook.com/v18.0/me/accounts', [
            'access_token' => $userAccessToken
        ]);

        $pages = $response->json()['data'] ?? [];
        if (empty($pages)) {
            throw new Exception('No Facebook pages found');
        }

        // Return the first page's access token
        return $pages[0]['access_token'];
    }

    /**
     * Publish to Instagram
     */
    private function publishToInstagram(Post $post, SocialAccount $account): array
    {
        if (!$post->media || count($post->media) === 0) {
            throw new Exception('Instagram posts require at least one image or video');
        }

        $media = $post->media[0];

        // Step 1: Create media container
        $containerEndpoint = "https://graph.instagram.com/v18.0/{$account->platform_user_id}/media";

        $params = [
            'access_token' => $account->access_token,
        ];

        if ($media['type'] === 'video') {
            $params['media_type'] = 'VIDEO';
            $params['video_url'] = $media['url'];
        } else {
            $params['image_url'] = $media['url'];
        }

        if ($post->caption) {
            $params['caption'] = $post->caption;
        }

        $containerResponse = Http::post($containerEndpoint, $params);

        if (!$containerResponse->successful()) {
            throw new Exception("Instagram container creation failed: " . $containerResponse->body());
        }

        $containerId = $containerResponse->json()['id'];

        // Step 2: Publish the container
        $publishEndpoint = "https://graph.instagram.com/v18.0/{$account->platform_user_id}/media_publish";
        $publishResponse = Http::post($publishEndpoint, [
            'creation_id' => $containerId,
            'access_token' => $account->access_token,
        ]);

        if (!$publishResponse->successful()) {
            throw new Exception("Instagram publish failed: " . $publishResponse->body());
        }

        $mediaId = $publishResponse->json()['id'];

        return [
            'id' => $mediaId,
            'url' => "https://instagram.com/p/{$mediaId}"
        ];
    }

    /**
     * Publish to Twitter/X
     */
    private function publishToTwitter(Post $post, SocialAccount $account): array
    {
        $endpoint = 'https://api.twitter.com/2/tweets';

        $payload = [
            'text' => $post->caption,
        ];

        // Upload media if present
        if ($post->media && count($post->media) > 0) {
            $mediaIds = [];
            foreach ($post->media as $media) {
                $mediaId = $this->uploadMediaToTwitter($media['url'], $account->access_token);
                $mediaIds[] = $mediaId;
            }

            if (!empty($mediaIds)) {
                $payload['media'] = ['media_ids' => $mediaIds];
            }
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$account->access_token}",
            'Content-Type' => 'application/json',
        ])->post($endpoint, $payload);

        if (!$response->successful()) {
            throw new Exception("Twitter API error: " . $response->body());
        }

        $data = $response->json()['data'];
        return [
            'id' => $data['id'],
            'url' => "https://twitter.com/i/status/{$data['id']}"
        ];
    }

    /**
     * Upload media to Twitter
     */
    private function uploadMediaToTwitter(string $mediaUrl, string $accessToken): string
    {
        // Download media first
        $mediaContent = file_get_contents($mediaUrl);

        // Upload to Twitter
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
        ])->attach('media', $mediaContent, 'media.jpg')
          ->post('https://upload.twitter.com/1.1/media/upload.json');

        return $response->json()['media_id_string'];
    }

    /**
     * Publish to LinkedIn
     */
    private function publishToLinkedIn(Post $post, SocialAccount $account): array
    {
        $authorUrn = "urn:li:person:{$account->platform_user_id}";

        $payload = [
            'author' => $authorUrn,
            'lifecycleState' => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.ugc.ShareContent' => [
                    'shareCommentary' => [
                        'text' => $post->caption
                    ],
                    'shareMediaCategory' => 'NONE'
                ]
            ],
            'visibility' => [
                'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'
            ]
        ];

        // Add media if present
        if ($post->media && count($post->media) > 0) {
            $media = $post->media[0];

            // Upload media to LinkedIn first
            $mediaUrn = $this->uploadMediaToLinkedIn($media['url'], $account);

            $payload['specificContent']['com.linkedin.ugc.ShareContent']['shareMediaCategory'] = 'IMAGE';
            $payload['specificContent']['com.linkedin.ugc.ShareContent']['media'] = [[
                'status' => 'READY',
                'media' => $mediaUrn
            ]];
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$account->access_token}",
            'Content-Type' => 'application/json',
            'X-Restli-Protocol-Version' => '2.0.0'
        ])->post('https://api.linkedin.com/v2/ugcPosts', $payload);

        if (!$response->successful()) {
            throw new Exception("LinkedIn API error: " . $response->body());
        }

        $data = $response->json();
        $postId = $data['id'];

        return [
            'id' => $postId,
            'url' => "https://linkedin.com/feed/update/{$postId}"
        ];
    }

    /**
     * Upload media to LinkedIn
     */
    private function uploadMediaToLinkedIn(string $mediaUrl, SocialAccount $account): string
    {
        // Register upload
        $registerResponse = Http::withHeaders([
            'Authorization' => "Bearer {$account->access_token}",
            'Content-Type' => 'application/json',
        ])->post('https://api.linkedin.com/v2/assets?action=registerUpload', [
            'registerUploadRequest' => [
                'recipes' => ['urn:li:digitalmediaRecipe:feedshare-image'],
                'owner' => "urn:li:person:{$account->platform_user_id}",
                'serviceRelationships' => [[
                    'relationshipType' => 'OWNER',
                    'identifier' => 'urn:li:userGeneratedContent'
                ]]
            ]
        ]);

        $uploadUrl = $registerResponse->json()['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
        $asset = $registerResponse->json()['value']['asset'];

        // Upload the actual media
        $mediaContent = file_get_contents($mediaUrl);
        Http::withHeaders([
            'Authorization' => "Bearer {$account->access_token}",
        ])->withBody($mediaContent, 'application/octet-stream')
          ->put($uploadUrl);

        return $asset;
    }

    /**
     * Publish to TikTok
     */
    private function publishToTikTok(Post $post, SocialAccount $account): array
    {
        if (!$post->media || count($post->media) === 0 || $post->media[0]['type'] !== 'video') {
            throw new Exception('TikTok posts require a video');
        }

        // TikTok Content Posting API
        $endpoint = 'https://open-api.tiktok.com/share/video/upload/';

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$account->access_token}",
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'video_url' => $post->media[0]['url'],
            'description' => $post->caption,
        ]);

        if (!$response->successful()) {
            throw new Exception("TikTok API error: " . $response->body());
        }

        $data = $response->json()['data'];
        return [
            'id' => $data['share_id'],
            'url' => $data['share_url'] ?? "https://tiktok.com/@{$account->account_name}"
        ];
    }

    /**
     * Publish to YouTube
     */
    private function publishToYouTube(Post $post, SocialAccount $account): array
    {
        if (!$post->media || count($post->media) === 0 || $post->media[0]['type'] !== 'video') {
            throw new Exception('YouTube posts require a video');
        }

        // YouTube Data API v3
        $endpoint = 'https://www.googleapis.com/upload/youtube/v3/videos?uploadType=resumable&part=snippet,status';

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$account->access_token}",
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'snippet' => [
                'title' => substr($post->caption, 0, 100),
                'description' => $post->caption,
                'tags' => $post->hashtags ?? [],
                'categoryId' => '22' // People & Blogs
            ],
            'status' => [
                'privacyStatus' => 'public',
                'selfDeclaredMadeForKids' => false
            ]
        ]);

        if (!$response->successful()) {
            throw new Exception("YouTube API error: " . $response->body());
        }

        $videoId = $response->json()['id'];

        return [
            'id' => $videoId,
            'url' => "https://youtube.com/watch?v={$videoId}"
        ];
    }

    /**
     * Publish to Pinterest
     */
    private function publishToPinterest(Post $post, SocialAccount $account): array
    {
        if (!$post->media || count($post->media) === 0) {
            throw new Exception('Pinterest posts require at least one image');
        }

        $endpoint = 'https://api.pinterest.com/v5/pins';

        $payload = [
            'title' => substr($post->caption, 0, 100),
            'description' => $post->caption,
            'link' => $post->media[0]['url'],
            'media_source' => [
                'source_type' => 'image_url',
                'url' => $post->media[0]['url']
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$account->access_token}",
            'Content-Type' => 'application/json',
        ])->post($endpoint, $payload);

        if (!$response->successful()) {
            throw new Exception("Pinterest API error: " . $response->body());
        }

        $data = $response->json();
        return [
            'id' => $data['id'],
            'url' => $data['link'] ?? "https://pinterest.com/pin/{$data['id']}"
        ];
    }
}
