<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Get all active subscription plans
     */
    public function getPlans()
    {
        try {
            $plans = SubscriptionPlan::where('is_active', true)
                ->orderBy('price', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $plans
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription plans',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current user's subscription
     */
    public function getCurrentSubscription(Request $request)
    {
        try {
            $user = $request->user();

            $subscription = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->with('plan')
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => true,
                    'subscription' => null,
                    'message' => 'No active subscription found'
                ]);
            }

            return response()->json([
                'success' => true,
                'subscription' => [
                    'id' => $subscription->id,
                    'plan_id' => $subscription->plan_id,
                    'plan_name' => $subscription->plan->name,
                    'status' => $subscription->status,
                    'starts_at' => $subscription->starts_at,
                    'ends_at' => $subscription->ends_at,
                    'plan' => $subscription->plan
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe(Request $request)
    {
        try {
            $request->validate([
                'plan_id' => 'required|exists:subscription_plans,id',
                'payment_method' => 'required|string|in:card,applePay,googlePay',
                'payment_token' => 'required|string'
            ]);

            $user = $request->user();
            $plan = SubscriptionPlan::findOrFail($request->plan_id);

            // Check if user already has an active subscription
            $existingSubscription = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($existingSubscription) {
                // Cancel existing subscription first
                $existingSubscription->status = 'cancelled';
                $existingSubscription->save();
            }

            // In production, process payment with payment gateway here
            // For now, we'll simulate a successful payment
            $paymentSuccessful = true;

            if (!$paymentSuccessful) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed'
                ], 400);
            }

            // Create new subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addMonth(), // For monthly subscriptions
                'payment_method' => $request->payment_method,
                'payment_token' => $request->payment_token
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription created successfully',
                'subscription' => [
                    'id' => $subscription->id,
                    'plan_id' => $subscription->plan_id,
                    'plan_name' => $plan->name,
                    'status' => $subscription->status,
                    'starts_at' => $subscription->starts_at,
                    'ends_at' => $subscription->ends_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        try {
            $user = $request->user();

            $subscription = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active subscription found'
                ], 404);
            }

            $subscription->status = 'cancelled';
            $subscription->cancelled_at = Carbon::now();
            $subscription->save();

            return response()->json([
                'success' => true,
                'message' => 'Subscription cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user has access to a specific feature
     */
    public function checkFeatureAccess(Request $request)
    {
        try {
            $request->validate([
                'feature' => 'required|string'
            ]);

            $user = $request->user();
            $feature = $request->feature;

            $subscription = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->with('plan')
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => true,
                    'has_access' => false,
                    'message' => 'No active subscription'
                ]);
            }

            // Check if subscription is expired
            if (Carbon::parse($subscription->ends_at)->isPast()) {
                return response()->json([
                    'success' => true,
                    'has_access' => false,
                    'message' => 'Subscription expired'
                ]);
            }

            $plan = $subscription->plan;
            $hasAccess = false;

            // Check feature access based on plan
            switch ($feature) {
                case 'ai_features':
                    $hasAccess = $plan->ai_features;
                    break;
                case 'analytics':
                    $hasAccess = $plan->analytics;
                    break;
                case 'priority_support':
                    $hasAccess = $plan->priority_support;
                    break;
                default:
                    $hasAccess = false;
            }

            return response()->json([
                'success' => true,
                'has_access' => $hasAccess,
                'plan_name' => $plan->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check feature access',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subscription usage stats
     */
    public function getUsageStats(Request $request)
    {
        try {
            $user = $request->user();

            $subscription = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->with('plan')
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active subscription found'
                ], 404);
            }

            $plan = $subscription->plan;

            // Calculate current month's usage
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            // Count posts created this month
            $postsUsed = \App\Models\Post::where('user_id', $user->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            // Count connected social accounts
            $accountsUsed = \App\Models\SocialAccount::where('user_id', $user->id)
                ->where('is_active', true)
                ->count();

            return response()->json([
                'success' => true,
                'usage' => [
                    'posts' => [
                        'used' => $postsUsed,
                        'limit' => $plan->max_posts_per_month,
                        'percentage' => $plan->max_posts_per_month ? round(($postsUsed / $plan->max_posts_per_month) * 100, 2) : 0
                    ],
                    'accounts' => [
                        'used' => $accountsUsed,
                        'limit' => $plan->max_social_accounts,
                        'percentage' => $plan->max_social_accounts ? round(($accountsUsed / $plan->max_social_accounts) * 100, 2) : 0
                    ],
                    'period' => [
                        'start' => $startOfMonth->toDateString(),
                        'end' => $endOfMonth->toDateString()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch usage stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
