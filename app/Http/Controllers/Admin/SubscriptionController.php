<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of user subscriptions.
     */
    public function index(Request $request)
    {
        $query = User::query()
            ->whereNotNull('current_subscription_plan_id')
            ->with('posts');

        // Filter by subscription plan
        if ($request->filled('plan_id')) {
            $query->where('current_subscription_plan_id', $request->plan_id);
        }

        // Filter by subscription status
        if ($request->filled('status')) {
            $query->where('subscription_status', $request->status);
        }

        // Search by user name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $subscriptions = $query->orderBy('subscription_start_date', 'desc')->paginate(15);

        // Get plan details for each subscription
        foreach ($subscriptions as $subscription) {
            // Add user property (already is user, but for view compatibility)
            $subscription->user = $subscription;

            // Add subscriptionPlan property
            $subscription->subscriptionPlan = DB::table('subscription_plans')
                ->where('id', $subscription->current_subscription_plan_id)
                ->first();

            // Also keep plan for backward compatibility
            $subscription->plan = $subscription->subscriptionPlan;

            // Add date aliases for view compatibility
            $subscription->starts_at = $subscription->subscription_start_date ? \Carbon\Carbon::parse($subscription->subscription_start_date) : null;
            $subscription->ends_at = $subscription->subscription_end_date ? \Carbon\Carbon::parse($subscription->subscription_end_date) : null;

            // Calculate usage this month
            $subscription->posts_this_month = DB::table('posts')
                ->where('user_id', $subscription->id)
                ->whereIn('status', ['published', 'scheduled'])
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();

            $subscription->active_accounts = DB::table('social_accounts')
                ->where('user_id', $subscription->id)
                ->where('is_active', true)
                ->count();
        }

        // Get all plans for filter dropdown
        $plans = DB::table('subscription_plans')->orderBy('price', 'asc')->get();

        // Statistics
        $stats = [
            'total_subscriptions' => User::whereNotNull('current_subscription_plan_id')->count(),
            'active_subscriptions' => User::where('subscription_status', 'active')->count(),
            'cancelled_subscriptions' => User::where('subscription_status', 'cancelled')->count(),
            'expired_subscriptions' => User::where('subscription_status', 'expired')->count(),
            'total_mrr' => $this->calculateMRR(),
        ];

        return view('admin.subscriptions.index', compact('subscriptions', 'plans', 'stats'));
    }

    /**
     * Show subscription details.
     */
    public function show($id)
    {
        $subscription = User::with('posts')->findOrFail($id);

        if (!$subscription->current_subscription_plan_id) {
            return redirect()->route('admin.subscriptions.index')
                ->with('error', __('User does not have a subscription'));
        }

        // Add user property for view compatibility
        $subscription->user = $subscription;

        // Add date aliases for view compatibility
        $subscription->starts_at = $subscription->subscription_start_date ? \Carbon\Carbon::parse($subscription->subscription_start_date) : null;
        $subscription->ends_at = $subscription->subscription_end_date ? \Carbon\Carbon::parse($subscription->subscription_end_date) : null;
        $subscription->canceled_at = null; // Add this field if you have it in your users table
        $subscription->status = $subscription->subscription_status ?? 'active';

        // Get plan details
        $subscription->plan = DB::table('subscription_plans')
            ->where('id', $subscription->current_subscription_plan_id)
            ->first();

        // Add subscriptionPlan alias for view compatibility
        $subscription->subscriptionPlan = $subscription->plan;

        // Get usage statistics
        $subscription->total_posts = DB::table('posts')
            ->where('user_id', $subscription->id)
            ->count();

        $subscription->posts_this_month = DB::table('posts')
            ->where('user_id', $subscription->id)
            ->whereIn('status', ['published', 'scheduled'])
            ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
            ->count();

        $subscription->active_accounts = DB::table('social_accounts')
            ->where('user_id', $subscription->id)
            ->where('is_active', true)
            ->count();

        $subscription->total_accounts = DB::table('social_accounts')
            ->where('user_id', $subscription->id)
            ->count();

        // Get monthly usage history (last 6 months)
        $usageHistory = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $postsCount = DB::table('posts')
                ->where('user_id', $subscription->id)
                ->whereIn('status', ['published', 'scheduled'])
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $usageHistory[] = [
                'month' => $date->format('M Y'),
                'posts' => $postsCount,
            ];
        }

        $subscription->usage_history = $usageHistory;

        // Get recent posts
        $subscription->recent_posts = DB::table('posts')
            ->where('user_id', $subscription->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Update subscription status.
     */
    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:active,cancelled,expired',
        ]);

        $user->update([
            'subscription_status' => $validated['status'],
        ]);

        return redirect()->route('admin.subscriptions.show', $id)
            ->with('success', __('Subscription status updated successfully'));
    }

    /**
     * Change user's subscription plan.
     */
    public function changePlan(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $plan = DB::table('subscription_plans')->where('id', $validated['plan_id'])->first();

        $user->update([
            'current_subscription_plan_id' => $validated['plan_id'],
            'subscription_status' => 'active',
            'subscription_start_date' => now(),
            'subscription_end_date' => now()->addMonth(),
        ]);

        return redirect()->route('admin.subscriptions.show', $id)
            ->with('success', __('Subscription plan changed to :plan successfully', ['plan' => $plan->name]));
    }

    /**
     * Cancel a subscription.
     */
    public function cancel($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'subscription_status' => 'cancelled',
        ]);

        return redirect()->route('admin.subscriptions.show', $id)
            ->with('success', __('Subscription cancelled successfully'));
    }

    /**
     * Reactivate a subscription.
     */
    public function reactivate($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'subscription_status' => 'active',
            'subscription_end_date' => now()->addMonth(),
        ]);

        return redirect()->route('admin.subscriptions.show', $id)
            ->with('success', __('Subscription reactivated successfully'));
    }

    /**
     * Calculate Monthly Recurring Revenue (MRR).
     */
    private function calculateMRR()
    {
        $activeUsers = User::where('subscription_status', 'active')
            ->whereNotNull('current_subscription_plan_id')
            ->get();

        $mrr = 0;
        foreach ($activeUsers as $user) {
            $plan = DB::table('subscription_plans')->where('id', $user->current_subscription_plan_id)->first();
            if ($plan) {
                if ($plan->billing_cycle === 'monthly') {
                    $mrr += $plan->price;
                } elseif ($plan->billing_cycle === 'yearly') {
                    $mrr += $plan->price / 12;
                }
            }
        }

        return round($mrr, 2);
    }

    /**
     * Export subscriptions data.
     */
    public function export(Request $request)
    {
        $query = User::query()->whereNotNull('current_subscription_plan_id');

        // Apply same filters as index
        if ($request->filled('plan_id')) {
            $query->where('current_subscription_plan_id', $request->plan_id);
        }

        if ($request->filled('status')) {
            $query->where('subscription_status', $request->status);
        }

        $subscriptions = $query->get();

        $csvData = "Name,Email,Plan,Status,Start Date,End Date,Posts This Month,Active Accounts\n";

        foreach ($subscriptions as $subscription) {
            $plan = DB::table('subscription_plans')->where('id', $subscription->current_subscription_plan_id)->first();

            $postsThisMonth = DB::table('posts')
                ->where('user_id', $subscription->id)
                ->whereIn('status', ['published', 'scheduled'])
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();

            $activeAccounts = DB::table('social_accounts')
                ->where('user_id', $subscription->id)
                ->where('is_active', true)
                ->count();

            $csvData .= sprintf(
                '"%s","%s","%s","%s","%s","%s",%d,%d' . "\n",
                $subscription->name,
                $subscription->email,
                $plan->name ?? 'N/A',
                $subscription->subscription_status,
                $subscription->subscription_start_date ?? 'N/A',
                $subscription->subscription_end_date ?? 'N/A',
                $postsThisMonth,
                $activeAccounts
            );
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="subscriptions_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
