@extends('layouts.admin')

@section('title', __('Pages Management'))

@section('content')
<div style="max-width: 1200px;">
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 800; background: linear-gradient(135deg, #14b8a6, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 0.5rem;">
            {{ __('Pages Management') }}
        </h1>
        <p style="color: #94a3b8; font-size: 1rem;">
            {{ __('Manage static pages like About, Contact, Privacy Policy, etc.') }}
        </p>
    </div>

    <div style="display: grid; gap: 1.5rem;">
        @foreach($pages as $key => $title)
        <div style="background: #1e293b; border: 1px solid rgba(148, 163, 184, 0.1); border-radius: 1rem; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; transition: all 0.3s ease;">
            <div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.5rem;">
                    {{ $title }}
                </h3>
                <p style="color: #94a3b8; font-size: 0.875rem;">
                    /{{ $key }}
                </p>
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <a href="{{ route($key) }}" target="_blank"
                   style="padding: 0.625rem 1.25rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #e2e8f0; font-weight: 600; font-size: 0.875rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease;">
                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ __('View') }}
                </a>

                <a href="{{ route('admin.settings.pages.edit', $key) }}"
                   style="padding: 0.625rem 1.25rem; background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1)); border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 0.5rem; color: #a5b4fc; font-weight: 600; font-size: 0.875rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease;">
                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    {{ __('Edit') }}
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <div style="margin-top: 2rem; padding: 1.5rem; background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(139, 92, 246, 0.05)); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 1rem;">
        <div style="display: flex; gap: 1rem; align-items: start;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #6366f1; flex-shrink: 0; margin-top: 0.25rem;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h4 style="font-size: 1rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.5rem;">
                    {{ __('About Pages Management') }}
                </h4>
                <p style="color: #94a3b8; font-size: 0.875rem; line-height: 1.6;">
                    {{ __('These are the static pages of your website. Click "Edit" to modify the content of any page. The changes will be reflected immediately on the public website.') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
