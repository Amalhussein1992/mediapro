@extends('layouts.admin')

@section('title', __('Edit Page') . ' - ' . ucfirst($page))

@section('content')
<div style="max-width: 1200px;">
    <!-- Header -->
    <div style="margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 800; background: linear-gradient(135deg, #14b8a6, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 0.5rem;">
                {{ __('Edit Page') }}: {{ ucfirst(str_replace('-', ' ', $page)) }}
            </h1>
            <p style="color: #94a3b8; font-size: 1rem;">
                {{ __('Modify the content and settings for this page') }}
            </p>
        </div>
        <a href="{{ route('admin.settings.pages') }}"
           style="padding: 0.75rem 1.5rem; background: #1e293b; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #e2e8f0; font-weight: 600; font-size: 0.875rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease;">
            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('Back') }}
        </a>
    </div>

    <!-- Preview Current Page -->
    <div style="margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(139, 92, 246, 0.05)); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 1rem;">
        <div style="display: flex; gap: 1rem; align-items: center;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #6366f1;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div style="flex: 1;">
                <h4 style="font-size: 1rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.25rem;">
                    {{ __('Current Page URL') }}
                </h4>
                <a href="{{ route($page) }}" target="_blank" style="color: #6366f1; font-size: 0.875rem; text-decoration: none;">
                    {{ url('/') }}/{{ $page }}
                    <svg style="width: 0.875rem; height: 0.875rem; display: inline; margin-left: 0.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <form method="POST" action="{{ route('admin.settings.pages.update', $page) }}" style="background: #1e293b; border: 1px solid rgba(148, 163, 184, 0.1); border-radius: 1rem; padding: 2rem;">
        @csrf

        <!-- Page Title (English) -->
        <div style="margin-bottom: 2rem;">
            <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                {{ __('Page Title') }} (English) *
            </label>
            <input type="text" name="title"
                   value="{{ old('title', $pageData->title) }}"
                   required
                   placeholder="e.g., About Us"
                   style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem; transition: all 0.3s ease;">
            <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.5rem;">
                {{ __('This will be displayed as the page heading') }}
            </p>
        </div>

        <!-- Page Title (Arabic) -->
        <div style="margin-bottom: 2rem;">
            <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                {{ __('Page Title') }} (عربي)
            </label>
            <input type="text" name="title_ar"
                   value="{{ old('title_ar', $pageData->title_ar) }}"
                   placeholder="مثال: من نحن"
                   style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem; transition: all 0.3s ease; direction: rtl;">
            <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.5rem;">
                {{ __('Arabic translation of the page title') }}
            </p>
        </div>

        <!-- Page Content (English) -->
        <div style="margin-bottom: 2rem;">
            <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                {{ __('Page Content') }} (English) *
            </label>
            <textarea name="content"
                      required
                      rows="15"
                      placeholder="Enter the page content here..."
                      style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem; font-family: 'Courier New', monospace; resize: vertical; transition: all 0.3s ease;">{{ old('content', $pageData->content) }}</textarea>
            <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.5rem;">
                {{ __('Supports HTML and Markdown formatting') }}
            </p>
        </div>

        <!-- Page Content (Arabic) -->
        <div style="margin-bottom: 2rem;">
            <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                {{ __('Page Content') }} (عربي)
            </label>
            <textarea name="content_ar"
                      rows="15"
                      placeholder="أدخل محتوى الصفحة هنا..."
                      style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem; font-family: 'Courier New', monospace; resize: vertical; transition: all 0.3s ease; direction: rtl;">{{ old('content_ar', $pageData->content_ar) }}</textarea>
            <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.5rem;">
                {{ __('Arabic translation of the page content') }}
            </p>
        </div>

        <!-- SEO Section -->
        <div style="padding-top: 2rem; border-top: 1px solid rgba(148, 163, 184, 0.1); margin-bottom: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #f8fafc; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg style="width: 1.25rem; height: 1.25rem; color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                {{ __('SEO Settings') }}
            </h3>

            <!-- Meta Description (English) -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                    {{ __('Meta Description') }} (English)
                </label>
                <textarea name="meta_description"
                          rows="3"
                          maxlength="160"
                          placeholder="Brief description of this page (max 160 characters)"
                          style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem; resize: vertical;">{{ old('meta_description', $pageData->meta_description) }}</textarea>
                <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.5rem;">
                    {{ __('Appears in search engine results (recommended: 120-160 characters)') }}
                </p>
            </div>

            <!-- Meta Description (Arabic) -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                    {{ __('Meta Description') }} (عربي)
                </label>
                <textarea name="meta_description_ar"
                          rows="3"
                          maxlength="160"
                          placeholder="وصف مختصر لهذه الصفحة (بحد أقصى 160 حرف)"
                          style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem; resize: vertical; direction: rtl;">{{ old('meta_description_ar', $pageData->meta_description_ar) }}</textarea>
                <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.5rem;">
                    {{ __('Arabic translation of meta description') }}
                </p>
            </div>

            <!-- Meta Keywords -->
            <div>
                <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                    {{ __('Meta Keywords') }}
                </label>
                <input type="text" name="meta_keywords"
                       value="{{ old('meta_keywords', $pageData->meta_keywords) }}"
                       placeholder="keyword1, keyword2, keyword3"
                       style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem;">
                <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.5rem;">
                    {{ __('Comma-separated keywords for search engines') }}
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 1rem; align-items: center; padding-top: 2rem; border-top: 1px solid rgba(148, 163, 184, 0.1);">
            <button type="submit"
                    style="padding: 0.875rem 2rem; background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none; border-radius: 0.5rem; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 0.5rem;">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ __('Save Changes') }}
            </button>

            <a href="{{ route($page) }}" target="_blank"
               style="padding: 0.875rem 1.5rem; background: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.3); border-radius: 0.5rem; color: #06b6d4; font-weight: 600; font-size: 0.875rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease;">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                {{ __('Preview Page') }}
            </a>

            <a href="{{ route('admin.settings.pages') }}"
               style="padding: 0.875rem 1.5rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #94a3b8; font-weight: 600; font-size: 0.875rem; text-decoration: none; transition: all 0.3s ease;">
                {{ __('Cancel') }}
            </a>
        </div>
    </form>

    <!-- Help Section -->
    <div style="margin-top: 2rem; padding: 1.5rem; background: linear-gradient(135deg, rgba(139, 92, 246, 0.05), rgba(168, 85, 247, 0.05)); border: 1px solid rgba(139, 92, 246, 0.2); border-radius: 1rem;">
        <h4 style="font-size: 1rem; font-weight: 700; color: #f8fafc; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <svg style="width: 1.25rem; height: 1.25rem; color: #8b5cf6;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            {{ __('Editor Tips') }}
        </h4>
        <ul style="color: #cbd5e1; font-size: 0.875rem; line-height: 1.8; padding-left: 1.5rem;">
            <li>Use HTML tags for formatting: <code style="background: #0f172a; padding: 0.125rem 0.375rem; border-radius: 0.25rem;">&lt;h1&gt;</code>, <code style="background: #0f172a; padding: 0.125rem 0.375rem; border-radius: 0.25rem;">&lt;p&gt;</code>, <code style="background: #0f172a; padding: 0.125rem 0.375rem; border-radius: 0.25rem;">&lt;strong&gt;</code></li>
            <li>Add links: <code style="background: #0f172a; padding: 0.125rem 0.375rem; border-radius: 0.25rem;">&lt;a href="url"&gt;text&lt;/a&gt;</code></li>
            <li>Insert images: <code style="background: #0f172a; padding: 0.125rem 0.375rem; border-radius: 0.25rem;">&lt;img src="url" alt="description"&gt;</code></li>
            <li>Create lists with <code style="background: #0f172a; padding: 0.125rem 0.375rem; border-radius: 0.25rem;">&lt;ul&gt;</code> and <code style="background: #0f172a; padding: 0.125rem 0.375rem; border-radius: 0.25rem;">&lt;li&gt;</code> tags</li>
            <li>Preview your changes before publishing</li>
        </ul>
    </div>
</div>

<style>
    input:focus, textarea:focus {
        outline: none;
        border-color: rgba(99, 102, 241, 0.5);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.5);
    }

    a:hover {
        opacity: 0.8;
    }
</style>
@endsection
