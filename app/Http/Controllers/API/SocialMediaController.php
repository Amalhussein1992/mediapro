<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AyrshareService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SocialMediaController extends Controller
{
    protected $ayrshare;

    public function __construct(AyrshareService $ayrshare)
    {
        $this->ayrshare = $ayrshare;
    }

    /**
     * Get authorization URL for connecting social media accounts
     */
    public function getAuthUrl(Request $request)
    {
        $request->validate([
            'platform' => 'required|string|in:facebook,instagram,twitter,linkedin,tiktok,youtube,pinterest,reddit,telegram'
        ]);

        try {
            if (!$this->ayrshare->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ayrshare is not configured. Please add your API key in settings.',
                ], 503);
            }

            $result = $this->ayrshare->getAuthUrl($request->platform);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'url' => $result['url'],
                    'jwt' => $result['jwt'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to generate authorization URL',
            ], 400);

        } catch (\Exception $e) {
            Log::error('SocialMedia getAuthUrl error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's connected social media accounts
     */
    public function getConnectedAccounts(Request $request)
    {
        try {
            $user = $request->user();
            $profileKey = $user->ayrshare_profile_key ?? null;

            if (!$this->ayrshare->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ayrshare is not configured',
                    'accounts' => [],
                ], 503);
            }

            $result = $this->ayrshare->getConnectedAccounts($profileKey);

            if ($result['success']) {
                // Transform Ayrshare accounts to our format
                $accounts = collect($result['accounts'])->map(function ($account) use ($user) {
                    return [
                        'id' => $account['id'] ?? uniqid(),
                        'platform' => $account['platform'] ?? 'unknown',
                        'username' => $account['username'] ?? $account['name'] ?? 'Unknown',
                        'profile_image' => $account['profileImage'] ?? null,
                        'is_active' => $account['active'] ?? true,
                        'followers_count' => $account['followersCount'] ?? 0,
                        'connected_at' => $account['connectedAt'] ?? now()->toISOString(),
                    ];
                });

                return response()->json([
                    'success' => true,
                    'accounts' => $accounts,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to fetch accounts',
                'accounts' => [],
            ], 400);

        } catch (\Exception $e) {
            Log::error('SocialMedia getConnectedAccounts error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
                'accounts' => [],
            ], 500);
        }
    }

    /**
     * Connect a social media account (callback after OAuth)
     */
    public function connectAccount(Request $request)
    {
        $request->validate([
            'platform' => 'required|string',
            'code' => 'required|string',
        ]);

        try {
            $user = $request->user();

            // Store the connection in database
            $account = DB::table('social_accounts')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'platform' => $request->platform,
                ],
                [
                    'username' => $request->username ?? 'Connected Account',
                    'access_token' => $request->code,
                    'is_active' => true,
                    'connected_at' => now(),
                    'updated_at' => now(),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Account connected successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('SocialMedia connectAccount error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect account: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Disconnect a social media account
     */
    public function disconnectAccount(Request $request, $platform)
    {
        try {
            $user = $request->user();
            $profileKey = $user->ayrshare_profile_key ?? null;

            if (!$this->ayrshare->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ayrshare is not configured',
                ], 503);
            }

            $result = $this->ayrshare->disconnectAccount($platform, $profileKey);

            if ($result['success']) {
                // Remove from our database
                DB::table('social_accounts')
                    ->where('user_id', $user->id)
                    ->where('platform', $platform)
                    ->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Account disconnected successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to disconnect account',
            ], 400);

        } catch (\Exception $e) {
            Log::error('SocialMedia disconnectAccount error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create and publish a post to social media
     */
    public function createPost(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'platforms' => 'required|array',
            'platforms.*' => 'string|in:facebook,instagram,twitter,linkedin,tiktok,youtube,pinterest,reddit,telegram',
            'media' => 'nullable|array',
            'media.*' => 'string|url',
            'scheduled_at' => 'nullable|date',
            'hashtags' => 'nullable|string',
        ]);

        try {
            $user = $request->user();
            $profileKey = $user->ayrshare_profile_key ?? null;

            if (!$this->ayrshare->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ayrshare is not configured. Please contact support.',
                ], 503);
            }

            // Prepare post data
            $postData = [
                'content' => $request->content,
                'platforms' => $request->platforms,
                'media' => $request->media ?? [],
                'scheduled_at' => $request->scheduled_at,
                'hashtags' => $request->hashtags,
            ];

            // Create post via Ayrshare
            $result = $this->ayrshare->createPost($postData, $profileKey);

            if ($result['success']) {
                // Save post to database
                $postId = DB::table('posts')->insertGetId([
                    'user_id' => $user->id,
                    'content' => $request->content,
                    'platforms' => json_encode($request->platforms),
                    'media_urls' => json_encode($request->media ?? []),
                    'scheduled_at' => $request->scheduled_at,
                    'status' => $request->scheduled_at ? 'scheduled' : 'published',
                    'ayrshare_id' => $result['id'] ?? null,
                    'platform_post_ids' => json_encode($result['post_ids'] ?? []),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $request->scheduled_at ? 'Post scheduled successfully' : 'Post published successfully',
                    'post' => [
                        'id' => $postId,
                        'ayrshare_id' => $result['id'],
                        'status' => $result['status'],
                        'post_ids' => $result['post_ids'],
                        'errors' => $result['errors'] ?? [],
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to create post',
                'errors' => $result['errors'] ?? [],
            ], 400);

        } catch (\Exception $e) {
            Log::error('SocialMedia createPost error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get post analytics from Ayrshare
     */
    public function getAnalytics(Request $request)
    {
        try {
            $user = $request->user();
            $profileKey = $user->ayrshare_profile_key ?? null;
            $platform = $request->query('platform');

            if (!$this->ayrshare->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ayrshare is not configured',
                    'analytics' => [],
                ], 503);
            }

            $result = $this->ayrshare->getAnalytics($platform, $profileKey);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'analytics' => $result['analytics'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to fetch analytics',
                'analytics' => [],
            ], 400);

        } catch (\Exception $e) {
            Log::error('SocialMedia getAnalytics error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
                'analytics' => [],
            ], 500);
        }
    }

    /**
     * Get social media account analytics
     */
    public function getAccountAnalytics(Request $request, $platform)
    {
        try {
            $user = $request->user();
            $profileKey = $user->ayrshare_profile_key ?? null;

            if (!$this->ayrshare->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ayrshare is not configured',
                    'analytics' => null,
                ], 503);
            }

            $result = $this->ayrshare->getAccountAnalytics($platform, $profileKey);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'analytics' => $result['analytics'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to fetch account analytics',
                'analytics' => null,
            ], 400);

        } catch (\Exception $e) {
            Log::error('SocialMedia getAccountAnalytics error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
                'analytics' => null,
            ], 500);
        }
    }

    /**
     * Get post history
     */
    public function getPostHistory(Request $request)
    {
        try {
            $user = $request->user();
            $profileKey = $user->ayrshare_profile_key ?? null;
            $limit = $request->query('limit', 50);

            if (!$this->ayrshare->isConfigured()) {
                // Fallback to database posts
                $posts = DB::table('posts')
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();

                return response()->json([
                    'success' => true,
                    'posts' => $posts,
                    'source' => 'database',
                ]);
            }

            $result = $this->ayrshare->getPostHistory($limit, $profileKey);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'posts' => $result['posts'],
                    'source' => 'ayrshare',
                ]);
            }

            // Fallback to database
            $posts = DB::table('posts')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'posts' => $posts,
                'source' => 'database',
            ]);

        } catch (\Exception $e) {
            Log::error('SocialMedia getPostHistory error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
                'posts' => [],
            ], 500);
        }
    }

    /**
     * Delete a post
     */
    public function deletePost(Request $request, $postId)
    {
        try {
            $user = $request->user();
            $profileKey = $user->ayrshare_profile_key ?? null;

            // Get post from database
            $post = DB::table('posts')
                ->where('id', $postId)
                ->where('user_id', $user->id)
                ->first();

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found',
                ], 404);
            }

            // Delete from Ayrshare if it was posted via Ayrshare
            if ($post->ayrshare_id && $this->ayrshare->isConfigured()) {
                $result = $this->ayrshare->deletePost($post->ayrshare_id, $profileKey);

                if (!$result['success']) {
                    Log::warning('Failed to delete post from Ayrshare: ' . ($result['error'] ?? 'Unknown error'));
                }
            }

            // Delete from database
            DB::table('posts')->where('id', $postId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('SocialMedia deletePost error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload media to Ayrshare
     */
    public function uploadMedia(Request $request)
    {
        $request->validate([
            'url' => 'required|string|url',
        ]);

        try {
            $user = $request->user();
            $profileKey = $user->ayrshare_profile_key ?? null;

            if (!$this->ayrshare->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ayrshare is not configured',
                ], 503);
            }

            $result = $this->ayrshare->uploadMedia($request->url, $profileKey);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'url' => $result['url'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to upload media',
            ], 400);

        } catch (\Exception $e) {
            Log::error('SocialMedia uploadMedia error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check Ayrshare configuration status
     */
    public function getStatus(Request $request)
    {
        return response()->json([
            'success' => true,
            'configured' => $this->ayrshare->isConfigured(),
            'has_api_key' => !empty($this->ayrshare->getApiKey()),
        ]);
    }
}
