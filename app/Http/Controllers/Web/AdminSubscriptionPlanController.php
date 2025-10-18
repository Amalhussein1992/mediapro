<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminSubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the subscription plans.
     */
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('price', 'asc')->get();
        return view('admin.subscription-plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new subscription plan.
     */
    public function create()
    {
        return view('admin.subscription-plans.create');
    }

    /**
     * Store a newly created subscription plan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'max_posts_per_month' => 'nullable|integer|min:1',
            'max_social_accounts' => 'nullable|integer|min:1',
            'max_team_members' => 'nullable|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['slug'] = Str::slug($request->name);
        $validated['is_active'] = $request->has('is_active');

        // Handle features array
        $features = [];
        if ($request->has('features')) {
            $features = array_filter(array_map('trim', explode("\n", $request->features)));
        }
        $validated['features'] = $features;

        SubscriptionPlan::create($validated);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan created successfully.');
    }

    /**
     * Display the specified subscription plan.
     */
    public function show(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->load(['subscriptions' => function($query) {
            $query->latest()->take(10);
        }]);
        return view('admin.subscription-plans.show', compact('subscriptionPlan'));
    }

    /**
     * Show the form for editing the specified subscription plan.
     */
    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return view('admin.subscription-plans.edit', compact('subscriptionPlan'));
    }

    /**
     * Update the specified subscription plan.
     */
    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'max_posts_per_month' => 'nullable|integer|min:1',
            'max_social_accounts' => 'nullable|integer|min:1',
            'max_team_members' => 'nullable|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['slug'] = Str::slug($request->name);
        $validated['is_active'] = $request->has('is_active');

        // Handle features array
        $features = [];
        if ($request->has('features')) {
            $features = array_filter(array_map('trim', explode("\n", $request->features)));
        }
        $validated['features'] = $features;

        $subscriptionPlan->update($validated);

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan updated successfully.');
    }

    /**
     * Remove the specified subscription plan.
     */
    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        // Check if plan has active subscriptions
        if ($subscriptionPlan->subscriptions()->where('status', 'active')->count() > 0) {
            return redirect()->route('admin.subscription-plans.index')
                ->with('error', 'Cannot delete plan with active subscriptions.');
        }

        $subscriptionPlan->delete();

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan deleted successfully.');
    }
}
