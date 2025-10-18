@extends('layouts.admin')

@section('title', 'View Social Account')
@section('header', 'Social Account Details')

@section('content')
<div class="max-w-4xl">
    <!-- Actions -->
    <div class="mb-4 flex items-center justify-between">
        <a
            href="{{ route('admin.social-accounts.index') }}"
            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors inline-flex items-center"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Social Accounts
        </a>
        <div class="flex items-center space-x-2">
            <a
                href="{{ route('admin.social-accounts.edit', $socialAccount) }}"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:shadow-lg transition-all"
            >
                Edit Account
            </a>
            <form action="{{ route('admin.social-accounts.destroy', $socialAccount) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this social account?');">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:shadow-lg transition-all"
                >
                    Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Account Details Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Status Badge -->
        <div class="mb-4">
            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $socialAccount->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $socialAccount->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <!-- Platform & Account Info -->
        <div class="mb-6 pb-6 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-500 mb-3">Platform & Account</h3>
            <div class="flex items-center mb-3">
                <span class="px-4 py-2 bg-primary/10 text-primary rounded-lg text-lg font-semibold mr-4">
                    {{ ucfirst($socialAccount->platform) }}
                </span>
                <div>
                    <p class="font-semibold text-gray-800 text-lg">{{ $socialAccount->account_name }}</p>
                    <p class="text-sm text-gray-500">Account ID: {{ $socialAccount->account_id }}</p>
                </div>
            </div>
        </div>

        <!-- User Info -->
        <div class="mb-6 pb-6 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-500 mb-2">Owner</h3>
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-r from-primary to-secondary rounded-full mr-3"></div>
                <div>
                    <p class="font-semibold text-gray-800">{{ $socialAccount->user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $socialAccount->user->email }}</p>
                </div>
            </div>
        </div>

        <!-- Token Information -->
        @if($socialAccount->access_token || $socialAccount->refresh_token)
        <div class="mb-6 pb-6 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-500 mb-3">Token Information</h3>

            @if($socialAccount->access_token)
            <div class="mb-3">
                <p class="text-xs font-semibold text-gray-600 mb-1">Access Token</p>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs font-mono text-gray-700 break-all">{{ substr($socialAccount->access_token, 0, 50) }}...</p>
                </div>
            </div>
            @endif

            @if($socialAccount->refresh_token)
            <div class="mb-3">
                <p class="text-xs font-semibold text-gray-600 mb-1">Refresh Token</p>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs font-mono text-gray-700 break-all">{{ substr($socialAccount->refresh_token, 0, 50) }}...</p>
                </div>
            </div>
            @endif

            @if($socialAccount->token_expires_at)
            <div>
                <p class="text-xs font-semibold text-gray-600 mb-1">Token Expires At</p>
                <p class="text-sm text-gray-800">{{ $socialAccount->token_expires_at->format('F d, Y h:i A') }}</p>
                @if($socialAccount->token_expires_at->isPast())
                    <span class="text-xs text-red-600 font-semibold">Token Expired</span>
                @else
                    <span class="text-xs text-green-600 font-semibold">Valid for {{ $socialAccount->token_expires_at->diffForHumans() }}</span>
                @endif
            </div>
            @endif
        </div>
        @endif

        <!-- Timestamps -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-500 mb-2">Created At</h3>
                <p class="text-gray-800">{{ $socialAccount->created_at->format('F d, Y h:i A') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 mb-2">Last Updated</h3>
                <p class="text-gray-800">{{ $socialAccount->updated_at->format('F d, Y h:i A') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
