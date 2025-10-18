<?php

namespace App\Http\Controllers;

use App\Services\UnifiedSocialMediaManager;
use App\Services\SocialMedia\FacebookService;
use App\Services\SocialMedia\TwitterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminSocialMediaController extends Controller
{
    protected $socialMediaManager;
    protected $facebook;
    protected $twitter;

    public function __construct(
        UnifiedSocialMediaManager $socialMediaManager,
        FacebookService $facebook,
        TwitterService $twitter
    ) {
        $this->socialMediaManager = $socialMediaManager;
        $this->facebook = $facebook;
        $this->twitter = $twitter;
    }

    /**
     * Show social media settings page
     */
    public function index()
    {
        // Get all platform statuses
        $platforms = $this->socialMediaManager->getPlatformStatus();

        // Get current API keys from .env
        $apiKeys = [
            'facebook_app_id' => env('FACEBOOK_APP_ID', ''),
            'facebook_app_secret' => env('FACEBOOK_APP_SECRET', ''),
            'twitter_api_key' => env('TWITTER_API_KEY', ''),
            'twitter_api_secret' => env('TWITTER_API_SECRET', ''),
            'twitter_bearer_token' => env('TWITTER_BEARER_TOKEN', ''),
            'linkedin_client_id' => env('LINKEDIN_CLIENT_ID', ''),
            'linkedin_client_secret' => env('LINKEDIN_CLIENT_SECRET', ''),
            'tiktok_client_key' => env('TIKTOK_CLIENT_KEY', ''),
            'tiktok_client_secret' => env('TIKTOK_CLIENT_SECRET', ''),
            'youtube_client_id' => env('YOUTUBE_CLIENT_ID', ''),
            'youtube_client_secret' => env('YOUTUBE_CLIENT_SECRET', ''),
            'ayrshare_api_key' => env('AYRSHARE_API_KEY', ''),
        ];

        // Get statistics
        $stats = [
            'total_accounts' => DB::table('social_accounts')->where('is_active', true)->count(),
            'total_posts' => DB::table('posts')->count(),
            'published_posts' => DB::table('posts')->where('status', 'published')->count(),
            'scheduled_posts' => DB::table('posts')->where('status', 'scheduled')->count(),
            'users_with_accounts' => DB::table('social_accounts')->distinct('user_id')->count(),
        ];

        return view('admin.social-media', compact('platforms', 'apiKeys', 'stats'));
    }

    /**
     * Update API keys
     */
    public function updateApiKeys(Request $request)
    {
        $request->validate([
            'platform' => 'required|string',
        ]);

        try {
            $platform = $request->platform;
            $envFile = base_path('.env');
            $envContent = file_get_contents($envFile);

            if ($platform === 'facebook') {
                $appId = $request->input('facebook_app_id');
                $appSecret = $request->input('facebook_app_secret');

                $envContent = $this->updateEnvValue($envContent, 'FACEBOOK_APP_ID', $appId);
                $envContent = $this->updateEnvValue($envContent, 'FACEBOOK_APP_SECRET', $appSecret);

            } elseif ($platform === 'twitter') {
                $apiKey = $request->input('twitter_api_key');
                $apiSecret = $request->input('twitter_api_secret');
                $bearerToken = $request->input('twitter_bearer_token');

                $envContent = $this->updateEnvValue($envContent, 'TWITTER_API_KEY', $apiKey);
                $envContent = $this->updateEnvValue($envContent, 'TWITTER_API_SECRET', $apiSecret);
                $envContent = $this->updateEnvValue($envContent, 'TWITTER_BEARER_TOKEN', $bearerToken);

            } elseif ($platform === 'linkedin') {
                $clientId = $request->input('linkedin_client_id');
                $clientSecret = $request->input('linkedin_client_secret');

                $envContent = $this->updateEnvValue($envContent, 'LINKEDIN_CLIENT_ID', $clientId);
                $envContent = $this->updateEnvValue($envContent, 'LINKEDIN_CLIENT_SECRET', $clientSecret);

            } elseif ($platform === 'tiktok') {
                $clientKey = $request->input('tiktok_client_key');
                $clientSecret = $request->input('tiktok_client_secret');

                $envContent = $this->updateEnvValue($envContent, 'TIKTOK_CLIENT_KEY', $clientKey);
                $envContent = $this->updateEnvValue($envContent, 'TIKTOK_CLIENT_SECRET', $clientSecret);

            } elseif ($platform === 'youtube') {
                $clientId = $request->input('youtube_client_id');
                $clientSecret = $request->input('youtube_client_secret');

                $envContent = $this->updateEnvValue($envContent, 'YOUTUBE_CLIENT_ID', $clientId);
                $envContent = $this->updateEnvValue($envContent, 'YOUTUBE_CLIENT_SECRET', $clientSecret);

            } elseif ($platform === 'ayrshare') {
                $apiKey = $request->input('ayrshare_api_key');
                $envContent = $this->updateEnvValue($envContent, 'AYRSHARE_API_KEY', $apiKey);
            }

            file_put_contents($envFile, $envContent);

            return redirect()->back()->with('success', "إعدادات {$platform} تم تحديثها بنجاح!");

        } catch (\Exception $e) {
            Log::error('Error updating API keys: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Test platform connection
     */
    public function testConnection(Request $request)
    {
        $platform = $request->input('platform');

        try {
            $result = match ($platform) {
                'facebook' => [
                    'success' => $this->facebook->isConfigured(),
                    'message' => $this->facebook->isConfigured()
                        ? 'Facebook configured correctly ✅'
                        : 'Facebook not configured ❌',
                ],
                'twitter' => [
                    'success' => $this->twitter->isConfigured(),
                    'message' => $this->twitter->isConfigured()
                        ? 'Twitter configured correctly ✅'
                        : 'Twitter not configured ❌',
                ],
                default => [
                    'success' => false,
                    'message' => 'Platform not supported',
                ],
            };

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get platform statistics
     */
    public function getStats()
    {
        $stats = [];

        // Facebook accounts
        $stats['facebook'] = DB::table('social_accounts')
            ->where('platform', 'facebook')
            ->where('is_active', true)
            ->count();

        // Instagram accounts
        $stats['instagram'] = DB::table('social_accounts')
            ->where('platform', 'instagram')
            ->where('is_active', true)
            ->count();

        // Twitter accounts
        $stats['twitter'] = DB::table('social_accounts')
            ->where('platform', 'twitter')
            ->where('is_active', true)
            ->count();

        // Recent posts
        $stats['recent_posts'] = DB::table('posts')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($stats);
    }

    /**
     * View all connected accounts
     */
    public function viewAccounts()
    {
        $accounts = DB::table('social_accounts')
            ->join('users', 'social_accounts.user_id', '=', 'users.id')
            ->select(
                'social_accounts.*',
                'users.name as user_name',
                'users.email as user_email'
            )
            ->orderBy('social_accounts.connected_at', 'desc')
            ->get();

        return view('admin.social-media-accounts', compact('accounts'));
    }

    /**
     * View all posts
     */
    public function viewPosts()
    {
        $posts = DB::table('posts')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->select(
                'posts.*',
                'users.name as user_name',
                'users.email as user_email'
            )
            ->orderBy('posts.created_at', 'desc')
            ->paginate(20);

        return view('admin.social-media-posts', compact('posts'));
    }

    /**
     * Helper: Update .env value
     */
    private function updateEnvValue(string $content, string $key, $value): string
    {
        $pattern = "/^{$key}=.*/m";
        $replacement = "{$key}={$value}";

        if (preg_match($pattern, $content)) {
            return preg_replace($pattern, $replacement, $content);
        }

        return $content . "\n{$replacement}";
    }
}
