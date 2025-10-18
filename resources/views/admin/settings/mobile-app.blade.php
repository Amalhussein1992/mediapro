@extends('layouts.admin')

@section('title', __('Mobile App Settings'))

@section('content')
<div style="max-width: 1200px;">
    <!-- Header -->
    <div style="margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 800; background: linear-gradient(135deg, #14b8a6, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 0.5rem;">
                {{ __('Mobile App Settings') }}
            </h1>
            <p style="color: #94a3b8; font-size: 1rem;">
                {{ __('Configure your mobile application settings and theme') }}
            </p>
        </div>
        <a href="http://localhost:8081/api/config" target="_blank"
           style="padding: 0.75rem 1.5rem; background: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.3); border-radius: 0.5rem; color: #06b6d4; font-weight: 600; font-size: 0.875rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease;">
            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
            </svg>
            {{ __('View API') }}
        </a>
    </div>

    <!-- Info Card -->
    <div style="margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(139, 92, 246, 0.05)); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 1rem;">
        <div style="display: flex; gap: 1rem; align-items: center;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #6366f1; flex-shrink: 0;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div style="flex: 1;">
                <h4 style="font-size: 1rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.25rem;">
                    {{ __('Automatic Sync') }}
                </h4>
                <p style="color: #94a3b8; font-size: 0.875rem; margin: 0;">
                    {{ __('All changes are automatically synced to the mobile app via the API. The app refreshes settings every 24 hours.') }}
                </p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.mobile-app.update') }}">
        @csrf

        <!-- App Branding Section -->
        <div style="background: #1e293b; border: 1px solid rgba(148, 163, 184, 0.1); border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg style="width: 1.5rem; height: 1.5rem; color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                {{ __('App Branding') }}
            </h2>
            <p style="color: #64748b; font-size: 0.875rem; margin-bottom: 1.5rem;">
                {{ __('Configure app name, tagline, and logo') }}
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <!-- App Name -->
                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                        {{ __('App Name') }} *
                    </label>
                    <input type="text" name="app_name"
                           value="{{ $settings['app_name'] ?? 'Media Pro' }}"
                           required
                           placeholder="e.g., Media Pro"
                           style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem; transition: all 0.3s ease;">
                    <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.5rem;">
                        {{ __('Displayed in app header and splash screen') }}
                    </p>
                </div>

                <!-- App Tagline -->
                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                        {{ __('App Tagline') }}
                    </label>
                    <input type="text" name="app_tagline"
                           value="{{ $settings['app_tagline'] ?? 'Professional Social Media Management' }}"
                           placeholder="e.g., Professional Social Media Management"
                           style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem; transition: all 0.3s ease;">
                    <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.5rem;">
                        {{ __('Short description shown under app name') }}
                    </p>
                </div>

                <!-- Logo URL -->
                <div style="grid-column: 1 / -1;">
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                        {{ __('Logo URL') }}
                    </label>
                    <input type="url" name="logo_url"
                           value="{{ $settings['logo_url'] ?? '' }}"
                           placeholder="https://example.com/logo.png"
                           style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem; transition: all 0.3s ease;">
                    <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.5rem;">
                        {{ __('URL to your app logo (recommended: 200x200px PNG)') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Theme Colors Section -->
        <div style="background: #1e293b; border: 1px solid rgba(148, 163, 184, 0.1); border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg style="width: 1.5rem; height: 1.5rem; color: #8b5cf6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
                {{ __('Theme Colors') }}
            </h2>
            <p style="color: #64748b; font-size: 0.875rem; margin-bottom: 1.5rem;">
                {{ __('Customize the color scheme of your mobile app') }}
            </p>

            <!-- Primary Colors -->
            <h3 style="font-size: 1.125rem; font-weight: 600; color: #e2e8f0; margin-bottom: 1rem;">
                {{ __('Primary Colors') }}
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                        {{ __('Primary') }}
                    </label>
                    <input type="color" name="theme_primary"
                           value="{{ $settings['theme_primary'] ?? '#6366F1' }}"
                           style="width: 100%; height: 50px; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; cursor: pointer;">
                    <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.5rem;">
                        {{ __('Main buttons and highlights') }}
                    </p>
                </div>

                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                        {{ __('Primary Dark') }}
                    </label>
                    <input type="color" name="theme_primary_dark"
                           value="{{ $settings['theme_primary_dark'] ?? '#4F46E5' }}"
                           style="width: 100%; height: 50px; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; cursor: pointer;">
                    <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.5rem;">
                        {{ __('Hover and pressed states') }}
                    </p>
                </div>

                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                        {{ __('Primary Light') }}
                    </label>
                    <input type="color" name="theme_primary_light"
                           value="{{ $settings['theme_primary_light'] ?? '#818CF8' }}"
                           style="width: 100%; height: 50px; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; cursor: pointer;">
                    <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.5rem;">
                        {{ __('Subtle backgrounds') }}
                    </p>
                </div>
            </div>

            <!-- Accent Colors -->
            <h3 style="font-size: 1.125rem; font-weight: 600; color: #e2e8f0; margin-bottom: 1rem;">
                {{ __('Accent Colors') }}
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                        {{ __('Accent') }}
                    </label>
                    <input type="color" name="theme_accent"
                           value="{{ $settings['theme_accent'] ?? '#8B5CF6' }}"
                           style="width: 100%; height: 50px; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; cursor: pointer;">
                    <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.5rem;">
                        {{ __('Secondary buttons and links') }}
                    </p>
                </div>

                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                        {{ __('Accent Dark') }}
                    </label>
                    <input type="color" name="theme_accent_dark"
                           value="{{ $settings['theme_accent_dark'] ?? '#7C3AED' }}"
                           style="width: 100%; height: 50px; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; cursor: pointer;">
                    <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.5rem;">
                        {{ __('Accent hover states') }}
                    </p>
                </div>

                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                        {{ __('Accent Light') }}
                    </label>
                    <input type="color" name="theme_accent_light"
                           value="{{ $settings['theme_accent_light'] ?? '#A78BFA' }}"
                           style="width: 100%; height: 50px; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; cursor: pointer;">
                    <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.5rem;">
                        {{ __('Accent backgrounds') }}
                    </p>
                </div>
            </div>

            <!-- Gradient Colors -->
            <h3 style="font-size: 1.125rem; font-weight: 600; color: #e2e8f0; margin-bottom: 1rem;">
                {{ __('Gradient Colors') }}
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                        {{ __('Gradient Start') }}
                    </label>
                    <input type="color" name="theme_gradient_start"
                           value="{{ $settings['theme_gradient_start'] ?? '#6366F1' }}"
                           style="width: 100%; height: 50px; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; cursor: pointer;">
                    <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.5rem;">
                        {{ __('Starting color for gradients') }}
                    </p>
                </div>

                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                        {{ __('Gradient End') }}
                    </label>
                    <input type="color" name="theme_gradient_end"
                           value="{{ $settings['theme_gradient_end'] ?? '#8B5CF6' }}"
                           style="width: 100%; height: 50px; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; cursor: pointer;">
                    <p style="color: #64748b; font-size: 0.75rem; margin-top: 0.5rem;">
                        {{ __('Ending color for gradients') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div style="background: #1e293b; border: 1px solid rgba(148, 163, 184, 0.1); border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg style="width: 1.5rem; height: 1.5rem; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('Features') }}
            </h2>
            <p style="color: #64748b; font-size: 0.875rem; margin-bottom: 1.5rem;">
                {{ __('Enable or disable app features') }}
            </p>

            <div style="display: grid; gap: 1rem;">
                <!-- AI Feature -->
                <label style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; cursor: pointer; transition: all 0.3s ease;">
                    <input type="checkbox" name="feature_ai_enabled" value="1"
                           {{ ($settings['feature_ai_enabled'] ?? true) ? 'checked' : '' }}
                           style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #f8fafc; margin-bottom: 0.25rem;">
                            {{ __('AI Content Generation') }}
                        </div>
                        <div style="font-size: 0.875rem; color: #64748b;">
                            {{ __('Enable AI-powered content creation features') }}
                        </div>
                    </div>
                </label>

                <!-- Analytics Feature -->
                <label style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; cursor: pointer; transition: all 0.3s ease;">
                    <input type="checkbox" name="feature_analytics_enabled" value="1"
                           {{ ($settings['feature_analytics_enabled'] ?? true) ? 'checked' : '' }}
                           style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #f8fafc; margin-bottom: 0.25rem;">
                            {{ __('Analytics Dashboard') }}
                        </div>
                        <div style="font-size: 0.875rem; color: #64748b;">
                            {{ __('Show analytics and insights section') }}
                        </div>
                    </div>
                </label>

                <!-- Ads Feature -->
                <label style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; cursor: pointer; transition: all 0.3s ease;">
                    <input type="checkbox" name="feature_ads_enabled" value="1"
                           {{ ($settings['feature_ads_enabled'] ?? true) ? 'checked' : '' }}
                           style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #f8fafc; margin-bottom: 0.25rem;">
                            {{ __('Ads Campaigns') }}
                        </div>
                        <div style="font-size: 0.875rem; color: #64748b;">
                            {{ __('Enable ads campaign management') }}
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Company Info Section -->
        <div style="background: #1e293b; border: 1px solid rgba(148, 163, 184, 0.1); border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #f8fafc; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg style="width: 1.5rem; height: 1.5rem; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                {{ __('Company Information') }}
            </h2>
            <p style="color: #64748b; font-size: 0.875rem; margin-bottom: 1.5rem;">
                {{ __('Contact information displayed in the app') }}
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <!-- Company Name -->
                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                        {{ __('Company Name') }}
                    </label>
                    <input type="text" name="company_name"
                           value="{{ $settings['company_name'] ?? '' }}"
                           placeholder="e.g., Your Company Inc."
                           style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem; transition: all 0.3s ease;">
                </div>

                <!-- Support Email -->
                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                        {{ __('Support Email') }}
                    </label>
                    <input type="email" name="support_email"
                           value="{{ $settings['support_email'] ?? '' }}"
                           placeholder="support@example.com"
                           style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem; transition: all 0.3s ease;">
                </div>

                <!-- Support Phone -->
                <div>
                    <label style="display: block; color: #e2e8f0; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">
                        {{ __('Support Phone') }}
                    </label>
                    <input type="tel" name="support_phone"
                           value="{{ $settings['support_phone'] ?? '' }}"
                           placeholder="+1 (555) 123-4567"
                           style="width: 100%; padding: 0.875rem 1rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #f8fafc; font-size: 0.875rem; transition: all 0.3s ease;">
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 1rem; align-items: center;">
            <button type="submit"
                    style="padding: 0.875rem 2rem; background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none; border-radius: 0.5rem; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 0.5rem;">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ __('Save Changes') }}
            </button>

            <a href="{{ route('dashboard') }}"
               style="padding: 0.875rem 1.5rem; background: #0f172a; border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 0.5rem; color: #94a3b8; font-weight: 600; font-size: 0.875rem; text-decoration: none; transition: all 0.3s ease;">
                {{ __('Cancel') }}
            </a>
        </div>
    </form>
</div>

<style>
    input:focus, textarea:focus {
        outline: none;
        border-color: rgba(99, 102, 241, 0.5) !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.5);
    }

    a:hover {
        opacity: 0.8;
    }

    label:has(input[type="checkbox"]):hover {
        background: rgba(99, 102, 241, 0.05) !important;
        border-color: rgba(99, 102, 241, 0.3) !important;
    }
</style>
@endsection
