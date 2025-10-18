<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        try {
            $url = Socialite::driver('google')
                ->stateless()
                ->redirect()
                ->getTargetUrl();

            return response()->json([
                'success' => true,
                'data' => [
                    'url' => $url
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في الاتصال بجوجل: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = $this->findOrCreateUser($googleUser, 'google');

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تسجيل الدخول: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Redirect to Apple OAuth
     */
    public function redirectToApple()
    {
        try {
            $url = Socialite::driver('apple')
                ->stateless()
                ->redirect()
                ->getTargetUrl();

            return response()->json([
                'success' => true,
                'data' => [
                    'url' => $url
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في الاتصال بأبل: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle Apple OAuth callback
     */
    public function handleAppleCallback(Request $request)
    {
        try {
            $appleUser = Socialite::driver('apple')->stateless()->user();

            $user = $this->findOrCreateUser($appleUser, 'apple');

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تسجيل الدخول: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Find or create user from social provider
     */
    private function findOrCreateUser($providerUser, $provider)
    {
        // Check if user exists by email
        $user = User::where('email', $providerUser->getEmail())->first();

        if ($user) {
            // Update provider info if needed
            if (!$user->{$provider . '_id'}) {
                $user->update([
                    $provider . '_id' => $providerUser->getId(),
                ]);
            }

            return $user;
        }

        // Create new user
        $user = User::create([
            'name' => $providerUser->getName() ?? $providerUser->getNickname() ?? 'User',
            'email' => $providerUser->getEmail(),
            'password' => Hash::make(Str::random(24)), // Random password
            $provider . '_id' => $providerUser->getId(),
            'email_verified_at' => now(),
            'account_type' => 'individual', // Default account type
        ]);

        return $user;
    }

    /**
     * Login with Google (Mobile App - ID Token)
     */
    public function loginWithGoogleToken(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            // Verify Google ID token
            $client = new \Google_Client(['client_id' => config('services.google.client_id')]);
            $payload = $client->verifyIdToken($request->id_token);

            if (!$payload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Google ID token'
                ], 401);
            }

            $googleUser = (object) [
                'id' => $payload['sub'],
                'email' => $payload['email'],
                'name' => $payload['name'] ?? '',
            ];

            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make(Str::random(24)),
                    'google_id' => $googleUser->id,
                    'email_verified_at' => now(),
                    'account_type' => 'individual',
                ]);
            } else {
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login with Apple (Mobile App - ID Token)
     */
    public function loginWithAppleToken(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
            'user' => 'sometimes|string', // Apple sends user info only on first login
        ]);

        try {
            // Decode Apple ID token (basic validation)
            $tokenParts = explode('.', $request->id_token);
            if (count($tokenParts) !== 3) {
                throw new \Exception('Invalid token format');
            }

            $payload = json_decode(base64_decode($tokenParts[1]), true);

            if (!$payload || !isset($payload['sub']) || !isset($payload['email'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Apple ID token'
                ], 401);
            }

            $appleUser = (object) [
                'id' => $payload['sub'],
                'email' => $payload['email'],
                'name' => $request->user ? json_decode($request->user)->name ?? 'User' : 'User',
            ];

            $user = User::where('email', $appleUser->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $appleUser->name,
                    'email' => $appleUser->email,
                    'password' => Hash::make(Str::random(24)),
                    'apple_id' => $appleUser->id,
                    'email_verified_at' => now(),
                    'account_type' => 'individual',
                ]);
            } else {
                if (!$user->apple_id) {
                    $user->update(['apple_id' => $appleUser->id]);
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
