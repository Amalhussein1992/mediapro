@extends('layouts.admin')

@section('title', 'View User')
@section('header', 'User Details')

@section('content')
<div class="max-w-4xl">
    <!-- Actions -->
    <div class="mb-4 flex items-center justify-between">
        <a
            href="{{ route('admin.users.index') }}"
            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors inline-flex items-center"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Users
        </a>
        <div class="flex items-center space-x-2">
            <a
                href="{{ route('admin.users.edit', $user) }}"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:shadow-lg transition-all"
            >
                Edit User
            </a>
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This will also delete all their posts and social accounts.');">
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

    <!-- User Details Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <!-- Profile -->
        <div class="flex items-center mb-6 pb-6 border-b border-gray-200">
            <div class="w-20 h-20 bg-gradient-to-r from-primary to-secondary rounded-full mr-4 flex items-center justify-center text-white text-2xl font-bold">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h2>
                <p class="text-gray-600">{{ $user->email }}</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4 mb-6 pb-6 border-b border-gray-200">
            <div class="text-center">
                <p class="text-3xl font-bold text-primary">{{ $user->posts->count() }}</p>
                <p class="text-sm text-gray-600">Posts</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-secondary">{{ $user->socialAccounts->count() }}</p>
                <p class="text-sm text-gray-600">Social Accounts</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-dark">{{ $user->posts->where('status', 'published')->count() }}</p>
                <p class="text-sm text-gray-600">Published</p>
            </div>
        </div>

        <!-- Timestamps -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-500 mb-2">Member Since</h3>
                <p class="text-gray-800">{{ $user->created_at->format('F d, Y') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 mb-2">Last Updated</h3>
                <p class="text-gray-800">{{ $user->updated_at->format('F d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Recent Posts -->
    @if($user->posts->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Recent Posts</h3>
        <div class="space-y-4">
            @foreach($user->posts->take(5) as $post)
            <div class="border-l-4 border-primary pl-4">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold px-2 py-1 rounded-full
                        {{ $post->status === 'published' ? 'bg-green-100 text-green-800' : ($post->status === 'scheduled' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($post->status) }}
                    </span>
                    <span class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-gray-700 line-clamp-2">{{ $post->content }}</p>
                <a href="{{ route('admin.posts.show', $post) }}" class="text-sm text-primary hover:text-secondary transition-colors">
                    View Post →
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Social Accounts -->
    @if($user->socialAccounts->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Social Accounts</h3>
        <div class="space-y-3">
            @foreach($user->socialAccounts as $account)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium mr-3">
                        {{ ucfirst($account->platform) }}
                    </span>
                    <span class="text-gray-700">{{ $account->account_name }}</span>
                </div>
                <span class="text-xs px-2 py-1 rounded-full
                    {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $account->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
