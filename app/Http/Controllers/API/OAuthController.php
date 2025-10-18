<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OAuthController extends Controller
{
    /**
     * Get OAuth authorization URL for a platform
     */
    public function getAuthUrl(Request $request, $platform)
    {
        // Check if credentials are configured for this platform
        if (!$this->isConfigured($platform)) {
            return response()->json([
                'success' => false,
                'message' => 'OAuth credentials not configured for this platform',
                'platform' => $platform
            ]);
        }

        $userId = $request->user()->id;
        $state = base64_encode(json_encode([
            'user_id' => $userId,
            'platform' => $platform,
            'csrf_token' => Str::random(40)
        ]));

        $authUrl = match ($platform) {
            'facebook' => $this->getFacebookAuthUrl($state),
            'instagram' => $this->getInstagramAuthUrl($state),
            'twitter' => $this->getTwitterAuthUrl($state),
            'linkedin' => $this->getLinkedInAuthUrl($state),
            'tiktok' => $this->getTikTokAuthUrl($state),
            'youtube' => $this->getYouTubeAuthUrl($state),
            'pinterest' => $this->getPinterestAuthUrl($state),
            default => null
        };

        if (!$authUrl) {
            return response()->json([
                'success' => false,
                'error' => 'Unsupported platform'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'authUrl' => $authUrl,
                'state' => $state
            ]
        ]);
    }

    /**
     * Check if OAuth credentials are configured for a platform
     */
    private function isConfigured($platform)
    {
        $clientId = config("services.{$platform}.client_id");
        $clientSecret = config("services.{$platform}.client_secret");
        $redirect = config("services.{$platform}.redirect");

        return !empty($clientId) && !empty($clientSecret) && !empty($redirect);
    }

    /**
     * Handle OAuth callback
     */
    public function handleCallback(Request $request, $platform)
    {
        $code = $request->input('code');
        $state = $request->input('state');

        if (!$code || !$state) {
            return response()->json(['error' => 'Invalid callback'], 400);
        }

        // Decode state to get user_id
        $stateData = json_decode(base64_decode($state), true);
        $userId = $stateData['user_id'] ?? null;

        if (!$userId) {
            return response()->json(['error' => 'Invalid state'], 400);
        }

        // Exchange code for access token
        $tokenData = match ($platform) {
            'facebook' => $this->exchangeFacebookCode($code),
            'instagram' => $this->exchangeInstagramCode($code),
            'twitter' => $this->exchangeTwitterCode($code),
            'linkedin' => $this->exchangeLinkedInCode($code),
            'tiktok' => $this->exchangeTikTokCode($code),
            'youtube' => $this->exchangeYouTubeCode($code),
            'pinterest' => $this->exchangePinterestCode($code),
            default => null
        };

        if (!$tokenData) {
            return response()->json(['error' => 'Failed to exchange code'], 500);
        }

        // Get account info
        $accountInfo = match ($platform) {
            'facebook' => $this->getFacebookAccountInfo($tokenData['access_token']),
            'instagram' => $this->getInstagramAccountInfo($tokenData['access_token']),
            'twitter' => $this->getTwitterAccountInfo($tokenData['access_token']),
            'linkedin' => $this->getLinkedInAccountInfo($tokenData['access_token']),
            'tiktok' => $this->getTikTokAccountInfo($tokenData['access_token']),
            'youtube' => $this->getYouTubeAccountInfo($tokenData['access_token']),
            'pinterest' => $this->getPinterestAccountInfo($tokenData['access_token']),
            default => null
        };

        if (!$accountInfo) {
            return response()->json(['error' => 'Failed to get account info'], 500);
        }

        // Save to database
        $socialAccount = SocialAccount::updateOrCreate(
            [
                'user_id' => $userId,
                'platform' => $platform,
                'platform_user_id' => $accountInfo['id']
            ],
            [
                'account_name' => $accountInfo['username'] ?? $accountInfo['name'],
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? null,
                'token_expires_at' => isset($tokenData['expires_in'])
                    ? now()->addSeconds($tokenData['expires_in'])
                    : null,
                'profile_picture' => $accountInfo['profile_picture'] ?? null,
                'metrics' => [
                    'followers' => $accountInfo['followers'] ?? 0,
                    'following' => $accountInfo['following'] ?? 0,
                    'posts' => $accountInfo['posts'] ?? 0,
                    'engagement' => $accountInfo['engagement'] ?? 0,
                ],
                'is_active' => true,
                'last_sync' => now()
            ]
        );

        return response()->json([
            'message' => 'Account connected successfully',
            'account' => $socialAccount
        ]);
    }

    // Facebook OAuth Methods
    private function getFacebookAuthUrl($state)
    {
        $clientId = config('services.facebook.client_id');
        $redirectUri = config('services.facebook.redirect');
        $scope = 'pages_show_list,pages_read_engagement,pages_manage_posts,pages_read_user_content';

        return "https://www.facebook.com/v18.0/dialog/oauth?" . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'scope' => $scope,
            'response_type' => 'code'
        ]);
    }

    private function exchangeFacebookCode($code)
    {
        $response = Http::get('https://graph.facebook.com/v18.0/oauth/access_token', [
            'client_id' => config('services.facebook.client_id'),
            'client_secret' => config('services.facebook.client_secret'),
            'redirect_uri' => config('services.facebook.redirect'),
            'code' => $code
        ]);

        return $response->json();
    }

    private function getFacebookAccountInfo($accessToken)
    {
        $response = Http::get('https://graph.facebook.com/v18.0/me', [
            'fields' => 'id,name,picture,accounts',
            'access_token' => $accessToken
        ]);

        $data = $response->json();
        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'username' => $data['name'],
            'profile_picture' => $data['picture']['data']['url'] ?? null,
            'followers' => 0, // Requires additional API calls
            'posts' => 0,
        ];
    }

    // Instagram OAuth Methods
    private function getInstagramAuthUrl($state)
    {
        $clientId = config('services.instagram.client_id');
        $redirectUri = config('services.instagram.redirect');
        $scope = 'instagram_basic,instagram_content_publish,instagram_manage_comments,instagram_manage_insights';

        return "https://api.instagram.com/oauth/authorize?" . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scope,
            'response_type' => 'code',
            'state' => $state
        ]);
    }

    private function exchangeInstagramCode($code)
    {
        $response = Http::asForm()->post('https://api.instagram.com/oauth/access_token', [
            'client_id' => config('services.instagram.client_id'),
            'client_secret' => config('services.instagram.client_secret'),
            'grant_type' => 'authorization_code',
            'redirect_uri' => config('services.instagram.redirect'),
            'code' => $code
        ]);

        return $response->json();
    }

    private function getInstagramAccountInfo($accessToken)
    {
        $response = Http::get("https://graph.instagram.com/me", [
            'fields' => 'id,username,account_type,media_count',
            'access_token' => $accessToken
        ]);

        $data = $response->json();
        return [
            'id' => $data['id'],
            'username' => $data['username'],
            'followers' => 0, // Requires Business/Creator account
            'posts' => $data['media_count'] ?? 0,
        ];
    }

    // Twitter OAuth Methods
    private function getTwitterAuthUrl($state)
    {
        // Twitter OAuth 2.0 with PKCE
        $clientId = config('services.twitter.client_id');
        $redirectUri = config('services.twitter.redirect');
        $scope = 'tweet.read tweet.write users.read offline.access';

        $codeVerifier = Str::random(128);
        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

        // Store code_verifier in session or cache with state as key
        cache()->put("twitter_code_verifier_{$state}", $codeVerifier, now()->addMinutes(10));

        return "https://twitter.com/i/oauth2/authorize?" . http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scope,
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256'
        ]);
    }

    private function exchangeTwitterCode($code)
    {
        // Implementation for Twitter OAuth 2.0
        // Requires code_verifier from cache
        return [];
    }

    private function getTwitterAccountInfo($accessToken)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}"
        ])->get('https://api.twitter.com/2/users/me', [
            'user.fields' => 'id,name,username,profile_image_url,public_metrics'
        ]);

        $data = $response->json()['data'] ?? [];
        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'username' => $data['username'],
            'profile_picture' => $data['profile_image_url'] ?? null,
            'followers' => $data['public_metrics']['followers_count'] ?? 0,
            'posts' => $data['public_metrics']['tweet_count'] ?? 0,
        ];
    }

    // LinkedIn OAuth Methods
    private function getLinkedInAuthUrl($state)
    {
        $clientId = config('services.linkedin.client_id');
        $redirectUri = config('services.linkedin.redirect');
        $scope = 'r_liteprofile r_emailaddress w_member_social';

        return "https://www.linkedin.com/oauth/v2/authorization?" . http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'scope' => $scope
        ]);
    }

    private function exchangeLinkedInCode($code)
    {
        $response = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => config('services.linkedin.redirect'),
            'client_id' => config('services.linkedin.client_id'),
            'client_secret' => config('services.linkedin.client_secret')
        ]);

        return $response->json();
    }

    private function getLinkedInAccountInfo($accessToken)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}"
        ])->get('https://api.linkedin.com/v2/me');

        $data = $response->json();
        return [
            'id' => $data['id'],
            'name' => $data['localizedFirstName'] . ' ' . $data['localizedLastName'],
            'username' => $data['localizedFirstName'] . ' ' . $data['localizedLastName'],
            'followers' => 0,
            'posts' => 0,
        ];
    }

    // TikTok OAuth Methods
    private function getTikTokAuthUrl($state)
    {
        $clientKey = config('services.tiktok.client_id');
        $redirectUri = config('services.tiktok.redirect');
        $scope = 'user.info.basic,video.list,video.upload';

        return "https://www.tiktok.com/auth/authorize/?" . http_build_query([
            'client_key' => $clientKey,
            'scope' => $scope,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'state' => $state
        ]);
    }

    private function exchangeTikTokCode($code)
    {
        $response = Http::asForm()->post('https://open-api.tiktok.com/oauth/access_token/', [
            'client_key' => config('services.tiktok.client_id'),
            'client_secret' => config('services.tiktok.client_secret'),
            'code' => $code,
            'grant_type' => 'authorization_code'
        ]);

        return $response->json()['data'] ?? [];
    }

    private function getTikTokAccountInfo($accessToken)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}"
        ])->get('https://open-api.tiktok.com/user/info/', [
            'fields' => 'open_id,union_id,avatar_url,display_name'
        ]);

        $data = $response->json()['data']['user'] ?? [];
        return [
            'id' => $data['open_id'],
            'username' => $data['display_name'],
            'profile_picture' => $data['avatar_url'] ?? null,
            'followers' => 0,
            'posts' => 0,
        ];
    }

    // YouTube OAuth Methods
    private function getYouTubeAuthUrl($state)
    {
        $clientId = config('services.youtube.client_id');
        $redirectUri = config('services.youtube.redirect');
        $scope = 'https://www.googleapis.com/auth/youtube.readonly https://www.googleapis.com/auth/youtube.upload';

        return "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $state,
            'access_type' => 'offline',
            'prompt' => 'consent'
        ]);
    }

    private function exchangeYouTubeCode($code)
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => config('services.youtube.client_id'),
            'client_secret' => config('services.youtube.client_secret'),
            'redirect_uri' => config('services.youtube.redirect'),
            'grant_type' => 'authorization_code'
        ]);

        return $response->json();
    }

    private function getYouTubeAccountInfo($accessToken)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}"
        ])->get('https://www.googleapis.com/youtube/v3/channels', [
            'part' => 'snippet,statistics',
            'mine' => 'true'
        ]);

        $data = $response->json()['items'][0] ?? [];
        return [
            'id' => $data['id'],
            'username' => $data['snippet']['title'],
            'profile_picture' => $data['snippet']['thumbnails']['default']['url'] ?? null,
            'followers' => $data['statistics']['subscriberCount'] ?? 0,
            'posts' => $data['statistics']['videoCount'] ?? 0,
        ];
    }

    // Pinterest OAuth Methods
    private function getPinterestAuthUrl($state)
    {
        $clientId = config('services.pinterest.client_id');
        $redirectUri = config('services.pinterest.redirect');
        $scope = 'boards:read,pins:read,pins:write';

        return "https://www.pinterest.com/oauth/?" . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $state
        ]);
    }

    private function exchangePinterestCode($code)
    {
        $response = Http::asForm()->post('https://api.pinterest.com/v5/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => config('services.pinterest.redirect')
        ])->withBasicAuth(
            config('services.pinterest.client_id'),
            config('services.pinterest.client_secret')
        );

        return $response->json();
    }

    private function getPinterestAccountInfo($accessToken)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}"
        ])->get('https://api.pinterest.com/v5/user_account');

        $data = $response->json();
        return [
            'id' => $data['username'],
            'username' => $data['username'],
            'profile_picture' => $data['profile_image'] ?? null,
            'followers' => $data['follower_count'] ?? 0,
            'posts' => $data['pin_count'] ?? 0,
        ];
    }
}
