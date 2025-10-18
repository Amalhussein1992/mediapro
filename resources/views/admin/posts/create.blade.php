@extends('layouts.admin')

@section('title', __('Create Post'))

@section('content')
<style>
    body {
        font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
    }

    .create-post-page {
        padding: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
        border-radius: 24px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        {{ app()->getLocale() === 'ar' ? 'left: -20%;' : 'right: -20%;' }}
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(20px); }
    }

    .page-header-content {
        position: relative;
        z-index: 1;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 800;
        color: white;
        margin: 0 0 0.5rem 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .page-subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1rem;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .breadcrumb a {
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .breadcrumb a:hover {
        color: white;
    }

    /* Form Container */
    .form-container {
        background: #1e293b;
        border-radius: 20px;
        padding: 2.5rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
    }

    /* Form Group */
    .form-group {
        margin-bottom: 2rem;
    }

    .form-label {
        color: #e2e8f0;
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.75rem;
        display: block;
    }

    .form-label .required {
        color: #f87171;
        margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 0.25rem;
    }

    .form-help {
        color: #94a3b8;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: block;
    }

    /* Form Inputs */
    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        background: #0f172a;
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: #e2e8f0;
        padding: 0.875rem 1.125rem;
        border-radius: 12px;
        outline: none;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        background: #0f172a;
    }

    .form-input::placeholder,
    .form-textarea::placeholder {
        color: #64748b;
    }

    .form-textarea {
        resize: vertical;
        min-height: 150px;
        font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
    }

    .form-select {
        cursor: pointer;
    }

    /* Error Messages */
    .form-error {
        color: #f87171;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    /* Checkbox Group */
    .checkbox-group {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }

    .checkbox-item {
        background: #0f172a;
        border: 2px solid rgba(148, 163, 184, 0.1);
        border-radius: 12px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .checkbox-item:hover {
        border-color: rgba(99, 102, 241, 0.3);
        background: rgba(99, 102, 241, 0.05);
    }

    .checkbox-item input[type="checkbox"] {
        width: 20px;
        height: 20px;
        border-radius: 6px;
        border: 2px solid #475569;
        background: transparent;
        cursor: pointer;
        appearance: none;
        position: relative;
        transition: all 0.3s ease;
    }

    .checkbox-item input[type="checkbox"]:checked {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border-color: #6366f1;
    }

    .checkbox-item input[type="checkbox"]:checked::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 12px;
        font-weight: bold;
    }

    .checkbox-label {
        color: #e2e8f0;
        font-weight: 500;
        cursor: pointer;
        user-select: none;
    }

    .platform-icon {
        width: 24px;
        height: 24px;
    }

    /* Card Sections */
    .form-section {
        background: #0f172a;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
    }

    .section-title {
        color: #e2e8f0;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-icon {
        width: 24px;
        height: 24px;
        color: #818cf8;
    }

    /* Buttons */
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: {{ app()->getLocale() === 'ar' ? 'flex-start' : 'flex-end' }};
        flex-wrap: wrap;
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(148, 163, 184, 0.1);
    }

    .btn {
        padding: 0.875rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-cancel {
        background: rgba(148, 163, 184, 0.1);
        color: #cbd5e1;
        border: 1px solid rgba(148, 163, 184, 0.2);
    }

    .btn-cancel:hover {
        background: rgba(148, 163, 184, 0.2);
        transform: translateY(-2px);
    }

    .btn-primary {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
        color: white;
        box-shadow: 0 4px 6px rgba(99, 102, 241, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 12px rgba(99, 102, 241, 0.4);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .create-post-page {
            padding: 1rem;
        }

        .page-header {
            padding: 1.5rem;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .form-container {
            padding: 1.5rem;
        }

        .checkbox-group {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="create-post-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="breadcrumb">
                <a href="{{ route('admin.posts.index') }}">{{ __('Posts') }}</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>{{ __('Create New Post') }}</span>
            </div>
            <h1 class="page-title">{{ __('Create New Post') }}</h1>
            <p class="page-subtitle">{{ __('Create and schedule your social media content across multiple platforms') }}</p>
        </div>
    </div>

    <!-- Form -->
    <div class="form-container">
        <form action="{{ route('admin.posts.store') }}" method="POST">
            @csrf

            <!-- Basic Information Section -->
            <div class="form-section">
                <h2 class="section-title">
                    <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ __('Basic Information') }}
                </h2>

                <!-- User Selection -->
                <div class="form-group">
                    <label for="user_id" class="form-label">
                        {{ __('User') }}
                        <span class="required">*</span>
                    </label>
                    <select id="user_id" name="user_id" class="form-select" required>
                        <option value="">{{ __('Select User') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <span class="form-error">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <!-- Content -->
                <div class="form-group">
                    <label for="content" class="form-label">
                        {{ __('Content') }}
                        <span class="required">*</span>
                    </label>
                    <textarea
                        id="content"
                        name="content"
                        class="form-textarea"
                        placeholder="{{ __('Write your post content here...') }}"
                        required
                    >{{ old('content') }}</textarea>
                    <span class="form-help">{{ __('Write engaging content for your audience. You can use hashtags and mentions.') }}</span>
                    @error('content')
                        <span class="form-error">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <!-- Platforms Section -->
            <div class="form-section">
                <h2 class="section-title">
                    <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                    </svg>
                    {{ __('Select Platforms') }}
                    <span class="required">*</span>
                </h2>

                <div class="checkbox-group">
                    <label class="checkbox-item">
                        <input type="checkbox" name="platforms[]" value="facebook" {{ is_array(old('platforms')) && in_array('facebook', old('platforms')) ? 'checked' : '' }}>
                        <span class="checkbox-label">Facebook</span>
                    </label>

                    <label class="checkbox-item">
                        <input type="checkbox" name="platforms[]" value="twitter" {{ is_array(old('platforms')) && in_array('twitter', old('platforms')) ? 'checked' : '' }}>
                        <span class="checkbox-label">Twitter</span>
                    </label>

                    <label class="checkbox-item">
                        <input type="checkbox" name="platforms[]" value="instagram" {{ is_array(old('platforms')) && in_array('instagram', old('platforms')) ? 'checked' : '' }}>
                        <span class="checkbox-label">Instagram</span>
                    </label>

                    <label class="checkbox-item">
                        <input type="checkbox" name="platforms[]" value="linkedin" {{ is_array(old('platforms')) && in_array('linkedin', old('platforms')) ? 'checked' : '' }}>
                        <span class="checkbox-label">LinkedIn</span>
                    </label>

                    <label class="checkbox-item">
                        <input type="checkbox" name="platforms[]" value="tiktok" {{ is_array(old('platforms')) && in_array('tiktok', old('platforms')) ? 'checked' : '' }}>
                        <span class="checkbox-label">TikTok</span>
                    </label>
                </div>

                @error('platforms')
                    <span class="form-error">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <!-- Publishing Options Section -->
            <div class="form-section">
                <h2 class="section-title">
                    <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ __('Publishing Options') }}
                </h2>

                <!-- Status -->
                <div class="form-group">
                    <label for="status" class="form-label">
                        {{ __('Status') }}
                        <span class="required">*</span>
                    </label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                        <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>{{ __('Scheduled') }}</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
                    </select>
                    <span class="form-help">{{ __('Choose when to publish your post') }}</span>
                    @error('status')
                        <span class="form-error">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <!-- Scheduled At -->
                <div class="form-group">
                    <label for="scheduled_at" class="form-label">
                        {{ __('Schedule Date & Time') }}
                    </label>
                    <input
                        type="datetime-local"
                        id="scheduled_at"
                        name="scheduled_at"
                        value="{{ old('scheduled_at') }}"
                        class="form-input"
                    >
                    <span class="form-help">{{ __('Optional: Set a specific date and time to publish this post') }}</span>
                    @error('scheduled_at')
                        <span class="form-error">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.posts.index') }}" class="btn btn-cancel">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ __('Create Post') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
