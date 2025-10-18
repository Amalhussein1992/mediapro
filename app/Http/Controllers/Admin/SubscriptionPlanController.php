<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of subscription plans.
     */
    public function index()
    {
        $plans = DB::table('subscription_plans')
            ->orderBy('price', 'asc')
            ->get();

        // Get user counts for each plan
        foreach ($plans as $plan) {
            $plan->users_count = DB::table('users')
                ->where('current_subscription_plan_id', $plan->id)
                ->where('subscription_status', 'active')
                ->count();

            $plan->total_revenue = DB::table('users')
                ->where('current_subscription_plan_id', $plan->id)
                ->where('subscription_status', 'active')
                ->count() * $plan->price;
        }

        // Get default currency from settings
        $currency = $this->getCurrency();

        return view('admin.subscription-plans.index', compact('plans', 'currency'));
    }

    /**
     * Show the form for creating a new subscription plan.
     */
    public function create()
    {
        $currency = $this->getCurrency();
        return view('admin.subscription-plans.create', compact('currency'));
    }

    /**
     * Store a newly created subscription plan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'max_posts_per_month' => 'required|integer|min:0',
            'max_social_accounts' => 'required|integer|min:0',
            'ai_features' => 'boolean',
            'analytics' => 'boolean',
            'priority_support' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Set boolean defaults
        $validated['ai_features'] = $request->has('ai_features');
        $validated['analytics'] = $request->has('analytics');
        $validated['priority_support'] = $request->has('priority_support');
        $validated['is_active'] = $request->has('is_active');

        DB::table('subscription_plans')->insert([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'billing_cycle' => $validated['billing_cycle'],
            'max_posts_per_month' => $validated['max_posts_per_month'],
            'max_social_accounts' => $validated['max_social_accounts'],
            'ai_features' => $validated['ai_features'],
            'analytics' => $validated['analytics'],
            'priority_support' => $validated['priority_support'],
            'is_active' => $validated['is_active'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', __('Subscription plan created successfully'));
    }

    /**
     * Show the form for editing a subscription plan.
     */
    public function edit($id)
    {
        $subscriptionPlan = DB::table('subscription_plans')->where('id', $id)->first();

        if (!$subscriptionPlan) {
            return redirect()->route('admin.subscription-plans.index')
                ->with('error', __('Subscription plan not found'));
        }

        $currency = $this->getCurrency();
        return view('admin.subscription-plans.edit', compact('subscriptionPlan', 'currency'));
    }

    /**
     * Update the specified subscription plan.
     */
    public function update(Request $request, $id)
    {
        $plan = DB::table('subscription_plans')->where('id', $id)->first();

        if (!$plan) {
            return redirect()->route('admin.subscription-plans.index')
                ->with('error', __('Subscription plan not found'));
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:subscription_plans,slug,' . $id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'max_posts_per_month' => 'nullable|integer|min:0',
            'max_social_accounts' => 'nullable|integer|min:0',
            'max_team_members' => 'nullable|integer|min:0',
            'features' => 'nullable|string',
            'ai_features' => 'boolean',
            'analytics' => 'boolean',
            'priority_support' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Set boolean defaults
        $validated['ai_features'] = $request->has('ai_features');
        $validated['analytics'] = $request->has('analytics');
        $validated['priority_support'] = $request->has('priority_support');
        $validated['is_active'] = $request->has('is_active');

        // Process features from textarea (line-separated) to JSON
        $features = null;
        if (!empty($validated['features'])) {
            $featuresArray = array_filter(array_map('trim', explode("\n", $validated['features'])));
            $features = !empty($featuresArray) ? json_encode($featuresArray) : null;
        }

        DB::table('subscription_plans')
            ->where('id', $id)
            ->update([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'billing_cycle' => $validated['billing_cycle'],
                'max_posts_per_month' => $validated['max_posts_per_month'] ?? 0,
                'max_social_accounts' => $validated['max_social_accounts'] ?? 0,
                'max_team_members' => $validated['max_team_members'] ?? null,
                'features' => $features,
                'ai_features' => $validated['ai_features'],
                'analytics' => $validated['analytics'],
                'priority_support' => $validated['priority_support'],
                'is_active' => $validated['is_active'],
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', __('Subscription plan updated successfully'));
    }

    /**
     * Remove the specified subscription plan.
     */
    public function destroy($id)
    {
        // Check if any users are using this plan
        $usersCount = DB::table('users')
            ->where('current_subscription_plan_id', $id)
            ->count();

        if ($usersCount > 0) {
            return redirect()->route('admin.subscription-plans.index')
                ->with('error', __('Cannot delete plan. :count users are subscribed to this plan.', ['count' => $usersCount]));
        }

        DB::table('subscription_plans')->where('id', $id)->delete();

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', __('Subscription plan deleted successfully'));
    }

    /**
     * Toggle plan active status.
     */
    public function toggleActive($id)
    {
        $plan = DB::table('subscription_plans')->where('id', $id)->first();

        if (!$plan) {
            return response()->json(['success' => false, 'message' => 'Plan not found'], 404);
        }

        DB::table('subscription_plans')
            ->where('id', $id)
            ->update([
                'is_active' => !$plan->is_active,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => __('Plan status updated successfully'),
            'is_active' => !$plan->is_active
        ]);
    }

    /**
     * Get currency from settings with symbol and format.
     */
    private function getCurrency()
    {
        $currencySetting = DB::table('app_settings')
            ->where('key', 'currency')
            ->first();

        $currencyCode = $currencySetting ? $currencySetting->value : 'USD';

        $currencies = [
            'USD' => ['symbol' => '$', 'name' => 'US Dollar', 'name_ar' => 'دولار أمريكي'],
            'EUR' => ['symbol' => '€', 'name' => 'Euro', 'name_ar' => 'يورو'],
            'GBP' => ['symbol' => '£', 'name' => 'British Pound', 'name_ar' => 'جنيه إسترليني'],
            'SAR' => ['symbol' => 'ر.س', 'name' => 'Saudi Riyal', 'name_ar' => 'ريال سعودي'],
            'AED' => ['symbol' => 'د.إ', 'name' => 'UAE Dirham', 'name_ar' => 'درهم إماراتي'],
            'EGP' => ['symbol' => 'ج.م', 'name' => 'Egyptian Pound', 'name_ar' => 'جنيه مصري'],
            'KWD' => ['symbol' => 'د.ك', 'name' => 'Kuwaiti Dinar', 'name_ar' => 'دينار كويتي'],
            'QAR' => ['symbol' => 'ر.ق', 'name' => 'Qatari Riyal', 'name_ar' => 'ريال قطري'],
            'BHD' => ['symbol' => 'د.ب', 'name' => 'Bahraini Dinar', 'name_ar' => 'دينار بحريني'],
            'OMR' => ['symbol' => 'ر.ع', 'name' => 'Omani Rial', 'name_ar' => 'ريال عماني'],
            'JOD' => ['symbol' => 'د.أ', 'name' => 'Jordanian Dinar', 'name_ar' => 'دينار أردني'],
            'IQD' => ['symbol' => 'د.ع', 'name' => 'Iraqi Dinar', 'name_ar' => 'دينار عراقي'],
        ];

        return [
            'code' => $currencyCode,
            'symbol' => $currencies[$currencyCode]['symbol'] ?? '$',
            'name' => $currencies[$currencyCode]['name'] ?? 'US Dollar',
            'name_ar' => $currencies[$currencyCode]['name_ar'] ?? 'دولار أمريكي',
        ];
    }
}
