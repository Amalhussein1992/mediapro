<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $search = $request->input('search');

            $query = User::query();

            // Search by name or email if provided
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Order by created_at descending
            $query->orderBy('created_at', 'desc');

            $users = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Users retrieved successfully',
                'data' => $users,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created user in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', 'confirmed', Password::min(8)],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            // Remove password from response
            $user->makeHidden('password');

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified user in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
                'password' => ['sometimes', 'required', 'confirmed', Password::min(8)],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();

            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);

            // Remove password from response
            $user->makeHidden('password');

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current user's subscription information with usage stats
     *
     * @return JsonResponse
     */
    public function getSubscriptionInfo(): JsonResponse
    {
        try {
            $user = Auth::user();

            // Get subscription plan details
            $subscriptionPlan = DB::table('subscription_plans')
                ->where('id', $user->subscription_plan_id)
                ->first();

            if (!$subscriptionPlan) {
                return response()->json([
                    'success' => false,
                    'message' => 'No subscription plan found',
                ], 404);
            }

            // Count posts this month
            $postsThisMonth = Post::where('user_id', $user->id)
                ->whereIn('status', ['published', 'scheduled'])
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();

            // Count active social accounts
            $activeSocialAccounts = SocialAccount::where('user_id', $user->id)
                ->where('is_active', true)
                ->count();

            // Calculate usage percentages
            $postsUsagePercent = ($postsThisMonth / $subscriptionPlan->max_posts_per_month) * 100;
            $accountsUsagePercent = ($activeSocialAccounts / $subscriptionPlan->max_social_accounts) * 100;

            return response()->json([
                'success' => true,
                'message' => 'Subscription info retrieved successfully',
                'data' => [
                    'plan' => [
                        'id' => $subscriptionPlan->id,
                        'name' => $subscriptionPlan->name,
                        'slug' => $subscriptionPlan->slug,
                        'description' => $subscriptionPlan->description,
                        'price' => $subscriptionPlan->price,
                        'billing_cycle' => $subscriptionPlan->billing_cycle,
                        'features' => [
                            'max_posts_per_month' => $subscriptionPlan->max_posts_per_month,
                            'max_social_accounts' => $subscriptionPlan->max_social_accounts,
                            'ai_features' => (bool) $subscriptionPlan->ai_features,
                            'analytics' => (bool) $subscriptionPlan->analytics,
                            'priority_support' => (bool) $subscriptionPlan->priority_support,
                        ],
                    ],
                    'subscription' => [
                        'status' => $user->subscription_status ?? 'active',
                        'start_date' => $user->subscription_start_date,
                        'end_date' => $user->subscription_end_date,
                    ],
                    'usage' => [
                        'posts' => [
                            'current' => $postsThisMonth,
                            'limit' => $subscriptionPlan->max_posts_per_month,
                            'remaining' => $subscriptionPlan->max_posts_per_month - $postsThisMonth,
                            'percentage' => round($postsUsagePercent, 2),
                            'is_limit_reached' => $postsThisMonth >= $subscriptionPlan->max_posts_per_month,
                        ],
                        'social_accounts' => [
                            'current' => $activeSocialAccounts,
                            'limit' => $subscriptionPlan->max_social_accounts,
                            'remaining' => $subscriptionPlan->max_social_accounts - $activeSocialAccounts,
                            'percentage' => round($accountsUsagePercent, 2),
                            'is_limit_reached' => $activeSocialAccounts >= $subscriptionPlan->max_social_accounts,
                        ],
                    ],
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve subscription info',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
