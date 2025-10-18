@extends('layouts.admin')

@section('title', 'Create Social Account')
@section('header', 'Create New Social Account')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.social-accounts.store') }}" method="POST">
            @csrf

            <!-- User Selection -->
            <div class="mb-4">
                <label for="user_id" class="block text-gray-700 text-sm font-semibold mb-2">
                    User <span class="text-red-500">*</span>
                </label>
                <select
                    id="user_id"
                    name="user_id"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                    required
                >
                    <option value="">Select User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Platform -->
            <div class="mb-4">
                <label for="platform" class="block text-gray-700 text-sm font-semibold mb-2">
                    Platform <span class="text-red-500">*</span>
                </label>
                <select
                    id="platform"
                    name="platform"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                    required
                >
                    <option value="">Select Platform</option>
                    <option value="facebook" {{ old('platform') == 'facebook' ? 'selected' : '' }}>Facebook</option>
                    <option value="twitter" {{ old('platform') == 'twitter' ? 'selected' : '' }}>Twitter</option>
                    <option value="instagram" {{ old('platform') == 'instagram' ? 'selected' : '' }}>Instagram</option>
                    <option value="linkedin" {{ old('platform') == 'linkedin' ? 'selected' : '' }}>LinkedIn</option>
                    <option value="tiktok" {{ old('platform') == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                </select>
                @error('platform')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Account Name -->
            <div class="mb-4">
                <label for="account_name" class="block text-gray-700 text-sm font-semibold mb-2">
                    Account Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="account_name"
                    name="account_name"
                    value="{{ old('account_name') }}"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                    placeholder="My Business Page"
                    required
                >
                @error('account_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Account ID -->
            <div class="mb-4">
                <label for="account_id" class="block text-gray-700 text-sm font-semibold mb-2">
                    Account ID <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="account_id"
                    name="account_id"
                    value="{{ old('account_id') }}"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                    placeholder="1234567890"
                    required
                >
                @error('account_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Access Token -->
            <div class="mb-4">
                <label for="access_token" class="block text-gray-700 text-sm font-semibold mb-2">
                    Access Token (Optional)
                </label>
                <textarea
                    id="access_token"
                    name="access_token"
                    rows="3"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                    placeholder="Paste access token here..."
                >{{ old('access_token') }}</textarea>
                @error('access_token')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Refresh Token -->
            <div class="mb-4">
                <label for="refresh_token" class="block text-gray-700 text-sm font-semibold mb-2">
                    Refresh Token (Optional)
                </label>
                <textarea
                    id="refresh_token"
                    name="refresh_token"
                    rows="3"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                    placeholder="Paste refresh token here..."
                >{{ old('refresh_token') }}</textarea>
                @error('refresh_token')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Token Expires At -->
            <div class="mb-4">
                <label for="token_expires_at" class="block text-gray-700 text-sm font-semibold mb-2">
                    Token Expires At (Optional)
                </label>
                <input
                    type="datetime-local"
                    id="token_expires_at"
                    name="token_expires_at"
                    value="{{ old('token_expires_at') }}"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                >
                @error('token_expires_at')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Active -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-2 focus:ring-primary"
                        {{ old('is_active', true) ? 'checked' : '' }}
                    >
                    <span class="ml-2 text-gray-700 font-semibold">Account is Active</span>
                </label>
                @error('is_active')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-between">
                <a
                    href="{{ route('admin.social-accounts.index') }}"
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    class="px-6 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-lg hover:shadow-lg transition-all"
                >
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
