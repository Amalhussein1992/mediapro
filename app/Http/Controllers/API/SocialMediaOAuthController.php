<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SocialMediaOAuthController extends Controller
{
    /**
     * Get OAuth URL for connecting social media account
     */
    public function getAuthUrl(Request $request): JsonResponse
    {
        $platform = $request->input('platform');
        $user = Auth::user();

        if (!$platform) {
            return response()->json([
                'success' => false,
                'message' => 'Platform parameter is required',
            ], 400);
        }

        try {
            $authUrl = $this->generateAuthUrl($platform, $user->id);

            return response()->json([
                'success' => true,
                'url' => $authUrl,
                'platform' => $platform,
            ]);

        } catch (\Exception $e) {
            Log::error("Auth URL generation error for {$platform}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate authorization URL',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle OAuth callback
     */
    public function handleCallback(Request $request): JsonResponse
    {
        $platform = $request->input('platform');
        $code = $request->input('code');
        $user = Auth::user();

        if (!$platform || !$code) {
            return response()->json([
                'success' => false,
                'message' => 'Platform and code are required',
            ], 400);
        }

        try {
            // Exchange code for access token
            $tokenData = $this->exchangeCodeForToken($platform, $code);

            // Get user profile from platform
            $profileData = $this->getUserProfile($platform, $tokenData['access_token']);

            // Check if account already exists
            $existingAccount = SocialAccount::where('user_id', $user->id)
                ->where('platform', $platform)
                ->where('platform_user_id', $profileData['id'])
                ->first();

            if ($existingAccount) {
                // Update existing account
                $existingAccount->update([
                    'access_token' => $tokenData['access_token'],
                    'refresh_token' => $tokenData['refresh_token'] ?? null,
                    'token_expires_at' => isset($tokenData['expires_in'])
                        ? now()->addSeconds($tokenData['expires_in'])
                        : null,
                    'is_active' => true,
                    'profile_data' => $profileData,
                ]);

                $account = $existingAccount;
            } else {
                // Create new account
                $account = SocialAccount::create([
                    'user_id' => $user->id,
                    'platform' => $platform,
                    'platform_user_id' => $profileData['id'],
                    'account_name' => $profileData['name'] ?? $profileData['username'] ?? 'Unknown',
                    'username' => $profileData['username'] ?? null,
                    'profile_image' => $profileData['profile_image'] ?? null,
                    'access_token' => $tokenData['access_token'],
                    'refresh_token' => $tokenData['refresh_token'] ?? null,
                    'token_expires_at' => isset($tokenData['expires_in'])
                        ? now()->addSeconds($tokenData['expires_in'])
                        : null,
                    'is_active' => true,
                    'profile_data' => $profileData,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => ucfirst($platform) . ' account connected successfully',
                'account' => $account,
            ], 201);

        } catch (\Exception $e) {
            Log::error("OAuth callback error for {$platform}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to connect account',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get connected social media accounts
     */
    public function getConnectedAccounts(Request $request): JsonResponse
    {
        $user = Auth::user();
        $platform = $request->input('platform');

        try {
            $query = SocialAccount::where('user_id', $user->id);

            if ($platform) {
                $query->where('platform', $platform);
            }

            $accounts = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'accounts' => $accounts,
            ]);

        } catch (\Exception $e) {
            Log::error('Get connected accounts error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch connected accounts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Disconnect social media account
     */
    public function disconnect(Request $request, string $accountId): JsonResponse
    {
        $user = Auth::user();

        try {
            $account = SocialAccount::where('id', $accountId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Revoke access token if possible
            $this->revokeAccessToken($account->platform, $account->access_token);

            $account->delete();

            return response()->json([
                'success' => true,
                'message' => ucfirst($account->platform) . ' account disconnected successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Disconnect account error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to disconnect account',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Refresh account token
     */
    public function refreshToken(Request $request, string $accountId): JsonResponse
    {
        $user = Auth::user();

        try {
            $account = SocialAccount::where('id', $accountId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            if (!$account->refresh_token) {
                return response()->json([
                    'success' => false,
                    'message' => 'No refresh token available',
                ], 400);
            }

            // Refresh the token
            $tokenData = $this->refreshAccessToken($account->platform, $account->refresh_token);

            $account->update([
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? $account->refresh_token,
                'token_expires_at' => isset($tokenData['expires_in'])
                    ? now()->addSeconds($tokenData['expires_in'])
                    : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'account' => $account,
            ]);

        } catch (\Exception $e) {
            Log::error('Refresh token error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ==================== FACEBOOK ====================

    private function generateFacebookAuthUrl(int $userId): string
    {
        $appId = config('services.facebook.client_id');
        $redirectUri = config('services.facebook.redirect_uri');
        $state = base64_encode(json_encode(['user_id' => $userId, 'platform' => 'facebook']));

        $scopes = [
            'public_profile',
            'email',
            'pages_show_list',
            'pages_read_engagement',
            'pages_manage_posts',
            'pages_read_user_content',
            'instagram_basic',
            'instagram_content_publish',
        ];

        return 'https://www.facebook.com/v18.0/dialog/oauth?' . http_build_query([
            'client_id' => $appId,
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'scope' => implode(',', $scopes),
            'response_type' => 'code',
        ]);
    }

    private function exchangeFacebookCode(string $code): array
    {
        $response = Http::get('https://graph.facebook.com/v18.0/oauth/access_token', [
            'client_id' => config('services.facebook.client_id'),
            'client_secret' => config('services.facebook.client_secret'),
            'redirect_uri' => config('services.facebook.redirect_uri'),
            'code' => $code,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to exchange Facebook code for token');
        }

        return $response->json();
    }

    private function getFacebookProfile(string $accessToken): array
    {
        $response = Http::get('https://graph.facebook.com/v18.0/me', [
            'access_token' => $accessToken,
            'fields' => 'id,name,email,picture.type(large)',
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get Facebook profile');
        }

        $data = $response->json();

        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'username' => $data['name'],
            'profile_image' => $data['picture']['data']['url'] ?? null,
        ];
    }

    // ==================== INSTAGRAM ====================

    private function generateInstagramAuthUrl(int $userId): string
    {
        // Instagram uses Facebook OAuth
        return $this->generateFacebookAuthUrl($userId);
    }

    private function exchangeInstagramCode(string $code): array
    {
        // Instagram uses Facebook OAuth
        return $this->exchangeFacebookCode($code);
    }

    private function getInstagramProfile(string $accessToken): array
    {
        // Get Instagram Business Account
        $response = Http::get('https://graph.facebook.com/v18.0/me/accounts', [
            'access_token' => $accessToken,
            'fields' => 'instagram_business_account',
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get Instagram account');
        }

        $data = $response->json();

        if (empty($data['data'])) {
            throw new \Exception('No Instagram Business Account found');
        }

        $igAccountId = $data['data'][0]['instagram_business_account']['id'] ?? null;

        if (!$igAccountId) {
            throw new \Exception('Instagram Business Account not linked to this Facebook Page');
        }

        // Get Instagram profile info
        $profileResponse = Http::get("https://graph.facebook.com/v18.0/{$igAccountId}", [
            'access_token' => $accessToken,
            'fields' => 'id,username,name,profile_picture_url,followers_count',
        ]);

        if (!$profileResponse->successful()) {
            throw new \Exception('Failed to get Instagram profile');
        }

        $profile = $profileResponse->json();

        return [
            'id' => $profile['id'],
            'name' => $profile['name'] ?? $profile['username'],
            'username' => $profile['username'],
            'profile_image' => $profile['profile_picture_url'] ?? null,
            'followers_count' => $profile['followers_count'] ?? 0,
        ];
    }

    // ==================== TWITTER/X ====================

    private function generateTwitterAuthUrl(int $userId): string
    {
        $clientId = config('services.twitter.client_id');
        $redirectUri = config('services.twitter.redirect_uri');
        $state = base64_encode(json_encode(['user_id' => $userId, 'platform' => 'twitter']));

        $scopes = [
            'tweet.read',
            'tweet.write',
            'users.read',
            'offline.access',
        ];

        return 'https://twitter.com/i/oauth2/authorize?' . http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => implode(' ', $scopes),
            'state' => $state,
            'code_challenge' => 'challenge',
            'code_challenge_method' => 'plain',
        ]);
    }

    private function exchangeTwitterCode(string $code): array
    {
        $response = Http::asForm()->post('https://api.twitter.com/2/oauth2/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => config('services.twitter.redirect_uri'),
            'client_id' => config('services.twitter.client_id'),
            'code_verifier' => 'challenge',
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to exchange Twitter code for token');
        }

        return $response->json();
    }

    private function getTwitterProfile(string $accessToken): array
    {
        $response = Http::withToken($accessToken)
            ->get('https://api.twitter.com/2/users/me', [
                'user.fields' => 'id,name,username,profile_image_url,public_metrics',
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get Twitter profile');
        }

        $data = $response->json()['data'];

        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'username' => $data['username'],
            'profile_image' => $data['profile_image_url'] ?? null,
            'followers_count' => $data['public_metrics']['followers_count'] ?? 0,
        ];
    }

    // ==================== LINKEDIN ====================

    private function generateLinkedInAuthUrl(int $userId): string
    {
        $clientId = config('services.linkedin.client_id');
        $redirectUri = config('services.linkedin.redirect_uri');
        $state = base64_encode(json_encode(['user_id' => $userId, 'platform' => 'linkedin']));

        $scopes = [
            'r_liteprofile',
            'r_emailaddress',
            'w_member_social',
        ];

        return 'https://www.linkedin.com/oauth/v2/authorization?' . http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'scope' => implode(' ', $scopes),
        ]);
    }

    private function exchangeLinkedInCode(string $code): array
    {
        $response = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => config('services.linkedin.client_id'),
            'client_secret' => config('services.linkedin.client_secret'),
            'redirect_uri' => config('services.linkedin.redirect_uri'),
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to exchange LinkedIn code for token');
        }

        return $response->json();
    }

    private function getLinkedInProfile(string $accessToken): array
    {
        $response = Http::withToken($accessToken)
            ->get('https://api.linkedin.com/v2/me');

        if (!$response->successful()) {
            throw new \Exception('Failed to get LinkedIn profile');
        }

        $data = $response->json();

        return [
            'id' => $data['id'],
            'name' => ($data['localizedFirstName'] ?? '') . ' ' . ($data['localizedLastName'] ?? ''),
            'username' => $data['localizedFirstName'] ?? 'LinkedIn User',
            'profile_image' => null, // LinkedIn API v2 requires separate call for profile picture
        ];
    }

    // ==================== TIKTOK ====================

    private function generateTikTokAuthUrl(int $userId): string
    {
        $clientKey = config('services.tiktok.client_key');
        $redirectUri = config('services.tiktok.redirect_uri');
        $state = base64_encode(json_encode(['user_id' => $userId, 'platform' => 'tiktok']));

        $scopes = [
            'user.info.basic',
            'video.list',
            'video.upload',
        ];

        return 'https://www.tiktok.com/auth/authorize/?' . http_build_query([
            'client_key' => $clientKey,
            'scope' => implode(',', $scopes),
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'state' => $state,
        ]);
    }

    private function exchangeTikTokCode(string $code): array
    {
        $response = Http::asForm()->post('https://open-api.tiktok.com/oauth/access_token/', [
            'client_key' => config('services.tiktok.client_key'),
            'client_secret' => config('services.tiktok.client_secret'),
            'code' => $code,
            'grant_type' => 'authorization_code',
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to exchange TikTok code for token');
        }

        $data = $response->json()['data'];

        return [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires_in' => $data['expires_in'],
        ];
    }

    private function getTikTokProfile(string $accessToken): array
    {
        $response = Http::withToken($accessToken)
            ->get('https://open-api.tiktok.com/user/info/', [
                'fields' => 'open_id,union_id,avatar_url,display_name',
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get TikTok profile');
        }

        $data = $response->json()['data']['user'];

        return [
            'id' => $data['open_id'],
            'name' => $data['display_name'],
            'username' => $data['display_name'],
            'profile_image' => $data['avatar_url'] ?? null,
        ];
    }

    // ==================== HELPER METHODS ====================

    private function generateAuthUrl(string $platform, int $userId): string
    {
        return match($platform) {
            'facebook' => $this->generateFacebookAuthUrl($userId),
            'instagram' => $this->generateInstagramAuthUrl($userId),
            'twitter' => $this->generateTwitterAuthUrl($userId),
            'linkedin' => $this->generateLinkedInAuthUrl($userId),
            'tiktok' => $this->generateTikTokAuthUrl($userId),
            default => throw new \Exception("Unsupported platform: {$platform}"),
        };
    }

    private function exchangeCodeForToken(string $platform, string $code): array
    {
        return match($platform) {
            'facebook' => $this->exchangeFacebookCode($code),
            'instagram' => $this->exchangeInstagramCode($code),
            'twitter' => $this->exchangeTwitterCode($code),
            'linkedin' => $this->exchangeLinkedInCode($code),
            'tiktok' => $this->exchangeTikTokCode($code),
            default => throw new \Exception("Unsupported platform: {$platform}"),
        };
    }

    private function getUserProfile(string $platform, string $accessToken): array
    {
        return match($platform) {
            'facebook' => $this->getFacebookProfile($accessToken),
            'instagram' => $this->getInstagramProfile($accessToken),
            'twitter' => $this->getTwitterProfile($accessToken),
            'linkedin' => $this->getLinkedInProfile($accessToken),
            'tiktok' => $this->getTikTokProfile($accessToken),
            default => throw new \Exception("Unsupported platform: {$platform}"),
        };
    }

    private function refreshAccessToken(string $platform, string $refreshToken): array
    {
        // Implementation for each platform
        return match($platform) {
            'facebook' => $this->refreshFacebookToken($refreshToken),
            'twitter' => $this->refreshTwitterToken($refreshToken),
            'linkedin' => $this->refreshLinkedInToken($refreshToken),
            'tiktok' => $this->refreshTikTokToken($refreshToken),
            default => throw new \Exception("Token refresh not supported for platform: {$platform}"),
        };
    }

    private function revokeAccessToken(string $platform, string $accessToken): void
    {
        try {
            match($platform) {
                'facebook' => Http::delete("https://graph.facebook.com/v18.0/me/permissions?access_token={$accessToken}"),
                'twitter' => Http::post('https://api.twitter.com/2/oauth2/revoke', ['token' => $accessToken]),
                default => null,
            };
        } catch (\Exception $e) {
            Log::warning("Failed to revoke {$platform} token: " . $e->getMessage());
        }
    }

    private function refreshFacebookToken(string $refreshToken): array
    {
        // Facebook long-lived tokens don't expire, so return the same token
        throw new \Exception('Facebook tokens do not need refresh');
    }

    private function refreshTwitterToken(string $refreshToken): array
    {
        $response = Http::asForm()->post('https://api.twitter.com/2/oauth2/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => config('services.twitter.client_id'),
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to refresh Twitter token');
        }

        return $response->json();
    }

    private function refreshLinkedInToken(string $refreshToken): array
    {
        $response = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => config('services.linkedin.client_id'),
            'client_secret' => config('services.linkedin.client_secret'),
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to refresh LinkedIn token');
        }

        return $response->json();
    }

    private function refreshTikTokToken(string $refreshToken): array
    {
        $response = Http::asForm()->post('https://open-api.tiktok.com/oauth/refresh_token/', [
            'client_key' => config('services.tiktok.client_key'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to refresh TikTok token');
        }

        return $response->json()['data'];
    }
}
