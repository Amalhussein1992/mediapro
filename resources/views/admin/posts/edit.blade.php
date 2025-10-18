@extends('layouts.admin')

@section('title', 'Edit Post')
@section('header', 'Edit Post')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.posts.update', $post) }}" method="POST">
            @csrf
            @method('PUT')

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
                        <option value="{{ $user->id }}" {{ (old('user_id', $post->user_id) == $user->id) ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content -->
            <div class="mb-4">
                <label for="content" class="block text-gray-700 text-sm font-semibold mb-2">
                    Content <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="content"
                    name="content"
                    rows="6"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                    placeholder="Write your post content..."
                    required
                >{{ old('content', $post->content) }}</textarea>
                @error('content')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Platforms -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    Platforms <span class="text-red-500">*</span>
                </label>
                @php
                    $selectedPlatforms = old('platforms', $post->platforms ?? []);
                @endphp
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="platforms[]" value="facebook" class="w-4 h-4 text-primary rounded focus:ring-2 focus:ring-primary" {{ in_array('facebook', $selectedPlatforms) ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">Facebook</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="platforms[]" value="twitter" class="w-4 h-4 text-primary rounded focus:ring-2 focus:ring-primary" {{ in_array('twitter', $selectedPlatforms) ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">Twitter</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="platforms[]" value="instagram" class="w-4 h-4 text-primary rounded focus:ring-2 focus:ring-primary" {{ in_array('instagram', $selectedPlatforms) ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">Instagram</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="platforms[]" value="linkedin" class="w-4 h-4 text-primary rounded focus:ring-2 focus:ring-primary" {{ in_array('linkedin', $selectedPlatforms) ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">LinkedIn</span>
                    </label>
                </div>
                @error('platforms')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label for="status" class="block text-gray-700 text-sm font-semibold mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select
                    id="status"
                    name="status"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                    required
                >
                    <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="scheduled" {{ old('status', $post->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Published</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Scheduled At -->
            <div class="mb-6">
                <label for="scheduled_at" class="block text-gray-700 text-sm font-semibold mb-2">
                    Scheduled At (Optional)
                </label>
                <input
                    type="datetime-local"
                    id="scheduled_at"
                    name="scheduled_at"
                    value="{{ old('scheduled_at', $post->scheduled_at ? $post->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                >
                @error('scheduled_at')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-between">
                <a
                    href="{{ route('admin.posts.index') }}"
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    class="px-6 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-lg hover:shadow-lg transition-all"
                >
                    Update Post
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
