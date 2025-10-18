@extends('layouts.admin')

@section('title', 'View Post')
@section('header', 'Post Details')

@section('content')
<div class="max-w-4xl">
    <!-- Actions -->
    <div class="mb-4 flex items-center justify-between">
        <a
            href="{{ route('admin.posts.index') }}"
            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors inline-flex items-center"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Posts
        </a>
        <div class="flex items-center space-x-2">
            <a
                href="{{ route('admin.posts.edit', $post) }}"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:shadow-lg transition-all"
            >
                Edit Post
            </a>
            <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
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

    <!-- Post Details Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Status Badge -->
        <div class="mb-4">
            @php
                $statusColors = [
                    'draft' => 'bg-gray-100 text-gray-800',
                    'scheduled' => 'bg-yellow-100 text-yellow-800',
                    'published' => 'bg-green-100 text-green-800',
                ];
            @endphp
            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $statusColors[$post->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ ucfirst($post->status) }}
            </span>
        </div>

        <!-- User Info -->
        <div class="mb-6 pb-6 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-500 mb-2">Posted By</h3>
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-r from-primary to-secondary rounded-full mr-3"></div>
                <div>
                    <p class="font-semibold text-gray-800">{{ $post->user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $post->user->email }}</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="mb-6 pb-6 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-500 mb-2">Content</h3>
            <p class="text-gray-800 whitespace-pre-wrap">{{ $post->content }}</p>
        </div>

        <!-- Platforms -->
        <div class="mb-6 pb-6 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-500 mb-3">Platforms</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($post->platforms ?? [] as $platform)
                    <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium">
                        {{ ucfirst($platform) }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Scheduled At -->
        @if($post->scheduled_at)
        <div class="mb-6 pb-6 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-500 mb-2">Scheduled At</h3>
            <p class="text-gray-800">{{ $post->scheduled_at->format('F d, Y h:i A') }}</p>
        </div>
        @endif

        <!-- Timestamps -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-500 mb-2">Created At</h3>
                <p class="text-gray-800">{{ $post->created_at->format('F d, Y h:i A') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 mb-2">Last Updated</h3>
                <p class="text-gray-800">{{ $post->updated_at->format('F d, Y h:i A') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
