@extends('layouts.admin')

@section('title', __('Landing Page Management'))

@section('content')
<div style="max-width: 1200px;">
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 800; background: linear-gradient(135deg, #6366f1, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 0.5rem;">
            {{ __('Landing Page Management') }}
        </h1>
        <p style="color: #94a3b8; font-size: 1rem;">
            {{ __('Manage your landing page content, branding, and settings') }}
        </p>
    </div>

    <form method="POST" action="{{ route('admin.settings.landing-page.update') }}" style="background: #1e293b; border: 1px solid rgba(148, 163, 184, 0.1); border-radius: 1rem; padding: 2rem;">
        @csrf

        <!-- Branding Section -->
        <div style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #f8fafc; margin-bottom: 1.5rem;">
                {{ __('Branding') }}
            </h2>

            <div style="display: grid; gap: 1.5rem;">
                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem;">
                        {{ __('App Name') }} *
                    </label>
                    <input type="text" name="app_name" value="{{ $settings['app_name'] ?? 'Social Media Manager' }}"
                           required
                           style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem;">
                </div>

                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem;">
                        {{ __('App Tagline') }}
                    </label>
                    <input type="text" name="app_tagline" value="{{ $settings['app_tagline'] ?? '' }}"
                           style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem;">
                </div>

                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem;">
                        {{ __('Logo URL') }}
                    </label>
                    <input type="url" name="logo_url" value="{{ $settings['logo_url'] ?? '' }}"
                           style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem;">
                </div>

                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem;">
                        {{ __('Company Name') }}
                    </label>
                    <input type="text" name="company_name" value="{{ $settings['company_name'] ?? '' }}"
                           style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem;">
                            {{ __('Support Email') }}
                        </label>
                        <input type="email" name="support_email" value="{{ $settings['support_email'] ?? '' }}"
                               style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem;">
                    </div>

                    <div>
                        <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem;">
                            {{ __('Support Phone') }}
                        </label>
                        <input type="text" name="support_phone" value="{{ $settings['support_phone'] ?? '' }}"
                               style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Theme Colors Section -->
        <div style="margin-bottom: 2rem; padding-top: 2rem; border-top: 1px solid rgba(148, 163, 184, 0.1);">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #f8fafc; margin-bottom: 1.5rem;">
                {{ __('Theme Colors') }}
            </h2>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem;">
                        {{ __('Primary Color') }}
                    </label>
                    <input type="color" name="theme_primary_color" value="{{ $settings['theme_primary_color'] ?? '#6366F1' }}"
                           style="width: 100%; height: 50px; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; cursor: pointer;">
                </div>

                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem;">
                        {{ __('Secondary Color') }}
                    </label>
                    <input type="color" name="theme_secondary_color" value="{{ $settings['theme_secondary_color'] ?? '#8B5CF6' }}"
                           style="width: 100%; height: 50px; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; cursor: pointer;">
                </div>

                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem;">
                        {{ __('Accent Color') }}
                    </label>
                    <input type="color" name="theme_accent_color" value="{{ $settings['theme_accent_color'] ?? '#EC4899' }}"
                           style="width: 100%; height: 50px; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; cursor: pointer;">
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div style="padding-top: 2rem; border-top: 1px solid rgba(148, 163, 184, 0.1);">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #f8fafc; margin-bottom: 1.5rem;">
                {{ __('Features') }}
            </h2>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <label style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="ai_enabled" value="1" {{ ($settings['ai_enabled'] ?? true) ? 'checked' : '' }}
                           style="width: 1.25rem; height: 1.25rem; border-radius: 0.25rem; cursor: pointer;">
                    <span style="color: #e2e8f0; font-weight: 600;">{{ __('Enable AI Features') }}</span>
                </label>

                <label style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="analytics_enabled" value="1" {{ ($settings['analytics_enabled'] ?? true) ? 'checked' : '' }}
                           style="width: 1.25rem; height: 1.25rem; border-radius: 0.25rem; cursor: pointer;">
                    <span style="color: #e2e8f0; font-weight: 600;">{{ __('Enable Analytics') }}</span>
                </label>

                <label style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="ads_enabled" value="1" {{ ($settings['ads_enabled'] ?? true) ? 'checked' : '' }}
                           style="width: 1.25rem; height: 1.25rem; border-radius: 0.25rem; cursor: pointer;">
                    <span style="color: #e2e8f0; font-weight: 600;">{{ __('Enable Ads Management') }}</span>
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(148, 163, 184, 0.1); display: flex; gap: 1rem;">
            <button type="submit" style="padding: 0.875rem 2rem; background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none; border-radius: 0.5rem; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; transition: all 0.3s ease;">
                {{ __('Save Changes') }}
            </button>

            <a href="{{ route('dashboard') }}" style="padding: 0.875rem 2rem; background: #1e293b; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #e2e8f0; font-weight: 600; font-size: 0.875rem; text-decoration: none; display: inline-block; transition: all 0.3s ease;">
                {{ __('Cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
