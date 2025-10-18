<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SocialAccountController extends Controller
{
    /**
     * Display a listing of the social accounts for authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $platform = $request->input('platform');
            $isActive = $request->input('is_active');

            $query = SocialAccount::where('user_id', Auth::id())
                ->with('user:id,name,email');

            // Filter by platform if provided
            if ($platform) {
                $query->where('platform', $platform);
            }

            // Filter by active status if provided
            if ($isActive !== null) {
                $query->where('is_active', filter_var($isActive, FILTER_VALIDATE_BOOLEAN));
            }

            // Order by created_at descending
            $query->orderBy('created_at', 'desc');

            $accounts = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Social accounts retrieved successfully',
                'data' => $accounts,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve social accounts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created social account in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'platform' => 'required|string|in:facebook,twitter,instagram,linkedin,tiktok,youtube',
                'account_name' => 'required|string|max:255',
                'account_id' => 'required|string|max:255',
                'access_token' => 'required|string',
                'refresh_token' => 'nullable|string',
                'token_expires_at' => 'nullable|date',
                'is_active' => 'boolean',
                'settings' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Check subscription limits for social accounts
            $user = Auth::user();
            $subscriptionPlan = \DB::table('subscription_plans')
                ->where('id', $user->subscription_plan_id)
                ->first();

            if ($subscriptionPlan) {
                // Count current active social accounts
                $activeAccountsCount = SocialAccount::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->count();

                if ($activeAccountsCount >= $subscriptionPlan->max_social_accounts) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have reached your social accounts limit. Please upgrade your plan.',
                        'limit' => $subscriptionPlan->max_social_accounts,
                        'current' => $activeAccountsCount,
                    ], 403);
                }
            }

            $data = $validator->validated();
            $data['user_id'] = Auth::id();

            // Check if account already exists
            $existingAccount = SocialAccount::where('user_id', Auth::id())
                ->where('platform', $data['platform'])
                ->where('account_id', $data['account_id'])
                ->first();

            if ($existingAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Social account already exists',
                ], 409);
            }

            $account = SocialAccount::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Social account created successfully',
                'data' => $account->load('user:id,name,email'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create social account',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified social account.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $account = SocialAccount::where('id', $id)
                ->where('user_id', Auth::id())
                ->with('user:id,name,email')
                ->first();

            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Social account not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Social account retrieved successfully',
                'data' => $account,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve social account',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified social account in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $account = SocialAccount::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Social account not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'platform' => 'sometimes|required|string|in:facebook,twitter,instagram,linkedin,tiktok,youtube',
                'account_name' => 'sometimes|required|string|max:255',
                'account_id' => 'sometimes|required|string|max:255',
                'access_token' => 'sometimes|required|string',
                'refresh_token' => 'nullable|string',
                'token_expires_at' => 'nullable|date',
                'is_active' => 'boolean',
                'settings' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $account->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Social account updated successfully',
                'data' => $account->load('user:id,name,email'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update social account',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified social account from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $account = SocialAccount::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Social account not found',
                ], 404);
            }

            $account->delete();

            return response()->json([
                'success' => true,
                'message' => 'Social account deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete social account',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Refresh the metrics for a specific social account.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function refresh(int $id): JsonResponse
    {
        try {
            $account = SocialAccount::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Social account not found',
                ], 404);
            }

            // Here you would normally call the social media API to get fresh metrics
            // For now, we'll simulate a refresh by updating the last_sync timestamp
            // and potentially refreshing the access token if it's expired

            $account->last_sync = now();

            // In a real implementation, you would:
            // 1. Check if access_token is expired
            // 2. Refresh the token if needed using refresh_token
            // 3. Fetch latest metrics from the platform API
            // 4. Update the metrics field

            // Simulated metrics update (remove this in production and fetch real data)
            if ($account->metrics) {
                $currentMetrics = $account->metrics;
                $currentMetrics['followers'] = ($currentMetrics['followers'] ?? 0) + rand(0, 100);
                $currentMetrics['engagement'] = rand(2, 10);
                $currentMetrics['posts'] = ($currentMetrics['posts'] ?? 0) + rand(0, 5);
                $account->metrics = $currentMetrics;
            }

            $account->save();

            return response()->json([
                'success' => true,
                'message' => 'Social account metrics refreshed successfully',
                'data' => $account,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh social account',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
