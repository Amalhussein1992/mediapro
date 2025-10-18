<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\Request;

class AdminSocialAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = SocialAccount::with('user')->latest()->paginate(20);
        return view('admin.social-accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('admin.social-accounts.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'platform' => 'required|in:facebook,twitter,instagram,linkedin,tiktok',
            'account_name' => 'required|string|max:255',
            'account_id' => 'required|string|max:255',
            'access_token' => 'nullable|string',
            'refresh_token' => 'nullable|string',
            'token_expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        SocialAccount::create($validated);

        return redirect()->route('admin.social-accounts.index')
            ->with('success', 'Social account created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SocialAccount $socialAccount)
    {
        $socialAccount->load('user');
        return view('admin.social-accounts.show', compact('socialAccount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SocialAccount $socialAccount)
    {
        $users = User::all();
        return view('admin.social-accounts.edit', compact('socialAccount', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SocialAccount $socialAccount)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'platform' => 'required|in:facebook,twitter,instagram,linkedin,tiktok',
            'account_name' => 'required|string|max:255',
            'account_id' => 'required|string|max:255',
            'access_token' => 'nullable|string',
            'refresh_token' => 'nullable|string',
            'token_expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $socialAccount->update($validated);

        return redirect()->route('admin.social-accounts.index')
            ->with('success', 'Social account updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SocialAccount $socialAccount)
    {
        $socialAccount->delete();

        return redirect()->route('admin.social-accounts.index')
            ->with('success', 'Social account deleted successfully.');
    }
}
