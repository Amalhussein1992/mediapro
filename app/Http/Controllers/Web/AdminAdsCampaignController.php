<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AdsCampaign;
use App\Models\User;
use Illuminate\Http\Request;

class AdminAdsCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $campaigns = AdsCampaign::with('user')->latest()->paginate(20);
        return view('admin.ads-campaigns.index', compact('campaigns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('admin.ads-campaigns.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'objective' => 'required|in:awareness,traffic,engagement,leads,conversions,sales',
            'platforms' => 'required|array',
            'platforms.*' => 'string|in:facebook,instagram,twitter,linkedin,tiktok,google',
            'budget_type' => 'required|in:daily,lifetime',
            'budget' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'timezone' => 'nullable|string',
            'status' => 'nullable|in:draft,active,paused,completed',
        ]);

        $validated['analytics'] = [
            'impressions' => 0,
            'clicks' => 0,
            'conversions' => 0,
            'spend' => 0,
        ];

        AdsCampaign::create($validated);

        return redirect()->route('admin.ads-campaigns.index')
            ->with('success', __('Campaign created successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(AdsCampaign $adsCampaign)
    {
        $adsCampaign->load(['user', 'adSets', 'ads']);
        return view('admin.ads-campaigns.show', compact('adsCampaign'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdsCampaign $adsCampaign)
    {
        $users = User::all();
        return view('admin.ads-campaigns.edit', compact('adsCampaign', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdsCampaign $adsCampaign)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'objective' => 'required|in:awareness,traffic,engagement,leads,conversions,sales',
            'platforms' => 'required|array',
            'budget_type' => 'required|in:daily,lifetime',
            'budget' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'nullable|in:draft,active,paused,completed',
        ]);

        $adsCampaign->update($validated);

        return redirect()->route('admin.ads-campaigns.index')
            ->with('success', __('Campaign updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdsCampaign $adsCampaign)
    {
        $adsCampaign->delete();

        return redirect()->route('admin.ads-campaigns.index')
            ->with('success', __('Campaign deleted successfully'));
    }
}
