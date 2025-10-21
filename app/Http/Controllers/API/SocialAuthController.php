<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Google_Client;

class SocialAuthController extends Controller
{
    /**
     * Google Sign In
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function googleAuth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_token' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
            'google_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verify Google ID Token (optional but recommended)
            // $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
            // $payload = $client->verifyIdToken($request->id_token);

            // if (!$payload) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Invalid Google token'
            //     ], 401);
            // }

            // Check if user exists with this email
            $user = User::where('email', $request->email)->first();

            if ($user) {
                // Update Google ID if not set
                if (!$user->google_id) {
                    $user->google_id = $request->google_id;
                    $user->save();
                }
            } else {
                // Create new user
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'google_id' => $request->google_id,
                    'profile_image' => $request->photo ?? null,
                    'password' => Hash::make(Str::random(32)), // Random password
                    'email_verified_at' => now(), // Auto-verify social login
                    'account_type' => 'individual',
                ]);
            }

            // Create token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Apple Sign In
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function appleAuth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity_token' => 'required|string',
            'apple_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Decode JWT token from Apple
            $tokenParts = explode('.', $request->identity_token);

            if (count($tokenParts) !== 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Apple token format'
                ], 401);
            }

            $payload = json_decode(base64_decode($tokenParts[1]), true);

            if (!$payload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Apple token'
                ], 401);
            }

            // Extract email from token or request
            $email = $payload['email'] ?? $request->email;
            $appleId = $request->apple_id;

            // If no email provided, use Apple ID as email
            if (!$email) {
                $email = $appleId . '@privaterelay.appleid.com';
            }

            // Check if user exists
            $user = User::where('apple_id', $appleId)
                ->orWhere('email', $email)
                ->first();

            if ($user) {
                // Update Apple ID if not set
                if (!$user->apple_id) {
                    $user->apple_id = $appleId;
                    $user->save();
                }
            } else {
                // Create new user
                $user = User::create([
                    'name' => $request->name ?? 'Apple User',
                    'email' => $email,
                    'apple_id' => $appleId,
                    'password' => Hash::make(Str::random(32)), // Random password
                    'email_verified_at' => now(), // Auto-verify social login
                    'account_type' => 'individual',
                ]);
            }

            // Create token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Facebook Sign In (for future use)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function facebookAuth(Request $request)
    {
        // Similar implementation to Google
        return response()->json([
            'success' => false,
            'message' => 'Facebook authentication coming soon'
        ], 501);
    }
}
