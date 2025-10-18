<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        // In a real application, you would fetch settings from a database or config
        $settings = [
            'app_name' => config('app.name', 'Social Media Manager'),
            'app_url' => config('app.url'),
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'nullable|string|max:255',
            'app_url' => 'nullable|url',
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string|max:255',
            'facebook_api_key' => 'nullable|string',
            'facebook_api_secret' => 'nullable|string',
            'twitter_api_key' => 'nullable|string',
            'twitter_api_secret' => 'nullable|string',
            'instagram_api_key' => 'nullable|string',
            'instagram_api_secret' => 'nullable|string',
            'linkedin_api_key' => 'nullable|string',
            'linkedin_api_secret' => 'nullable|string',
        ]);

        // In a real application, you would save these to a database or update .env file
        // For now, we'll just redirect with a success message

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully. Note: To persist these changes, update your .env file or use a settings database table.');
    }

    /**
     * Display the landing page management.
     */
    public function landingPage()
    {
        // Fetch landing page settings from database
        $settings = \App\Models\AppSetting::whereIn('key', [
            'app_name', 'app_tagline', 'logo_url', 'company_name',
            'support_email', 'support_phone',
            'theme_primary_color', 'theme_secondary_color', 'theme_accent_color',
            'ai_enabled', 'analytics_enabled', 'ads_enabled'
        ])->pluck('value', 'key')->toArray();

        return view('admin.settings.landing-page', compact('settings'));
    }

    /**
     * Update landing page settings.
     */
    public function updateLandingPage(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_tagline' => 'nullable|string|max:500',
            'logo_url' => 'nullable|url',
            'company_name' => 'nullable|string|max:255',
            'support_email' => 'nullable|email',
            'support_phone' => 'nullable|string|max:50',
            'theme_primary_color' => 'nullable|string|max:7',
            'theme_secondary_color' => 'nullable|string|max:7',
            'theme_accent_color' => 'nullable|string|max:7',
            'ai_enabled' => 'nullable|boolean',
            'analytics_enabled' => 'nullable|boolean',
            'ads_enabled' => 'nullable|boolean',
        ]);

        // Update settings in database
        foreach ($validated as $key => $value) {
            \App\Models\AppSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'general', 'type' => is_bool($value) ? 'boolean' : 'string']
            );
        }

        return redirect()->route('admin.settings.landing-page')
            ->with('success', __('Landing page settings updated successfully.'));
    }

    /**
     * Display pages management.
     */
    public function pages()
    {
        // For now, return a simple view with available pages
        $pages = [
            'about' => __('About'),
            'contact' => __('Contact'),
            'privacy' => __('Privacy Policy'),
            'terms' => __('Terms of Service'),
            'features' => __('Features'),
            'pricing' => __('Pricing'),
            'help' => __('Help Center'),
            'blog' => __('Blog'),
            'community' => __('Community'),
            'api' => __('API Documentation'),
            'security' => __('Security'),
        ];

        return view('admin.settings.pages', compact('pages'));
    }

    /**
     * Show edit form for a specific page.
     */
    public function editPage($page)
    {
        // Get page from database
        $pageData = \App\Models\Page::where('slug', $page)->first();

        if (!$pageData) {
            abort(404, 'Page not found');
        }

        return view('admin.settings.edit-page', compact('page', 'pageData'));
    }

    /**
     * Update a specific page.
     */
    public function updatePage(Request $request, $page)
    {
        // Validate and update page content
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'content' => 'required|string',
            'content_ar' => 'nullable|string',
            'meta_description' => 'nullable|string|max:160',
            'meta_description_ar' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        // Find and update page in database
        $pageData = \App\Models\Page::where('slug', $page)->first();

        if (!$pageData) {
            abort(404, 'Page not found');
        }

        $pageData->update($validated);

        return redirect()->route('admin.settings.pages')
            ->with('success', __('Page updated successfully.'));
    }

    /**
     * Display mobile app settings management.
     */
    public function mobileApp()
    {
        // Fetch mobile app settings from database
        $settings = \App\Models\AppSetting::whereIn('key', [
            // App Branding
            'app_name', 'app_tagline', 'logo_url',

            // Theme Colors
            'theme_primary', 'theme_primary_dark', 'theme_primary_light',
            'theme_accent', 'theme_accent_dark', 'theme_accent_light',
            'theme_gradient_start', 'theme_gradient_end',

            // Features
            'feature_ai_enabled', 'feature_analytics_enabled', 'feature_ads_enabled',

            // Company Info
            'company_name', 'support_email', 'support_phone',
        ])->pluck('value', 'key')->toArray();

        return view('admin.settings.mobile-app', compact('settings'));
    }

    /**
     * Update mobile app settings.
     */
    public function updateMobileApp(Request $request)
    {
        $validated = $request->validate([
            // App Branding
            'app_name' => 'required|string|max:255',
            'app_tagline' => 'nullable|string|max:500',
            'logo_url' => 'nullable|url',

            // Theme Colors
            'theme_primary' => 'nullable|string|max:7',
            'theme_primary_dark' => 'nullable|string|max:7',
            'theme_primary_light' => 'nullable|string|max:7',
            'theme_accent' => 'nullable|string|max:7',
            'theme_accent_dark' => 'nullable|string|max:7',
            'theme_accent_light' => 'nullable|string|max:7',
            'theme_gradient_start' => 'nullable|string|max:7',
            'theme_gradient_end' => 'nullable|string|max:7',

            // Features
            'feature_ai_enabled' => 'nullable|boolean',
            'feature_analytics_enabled' => 'nullable|boolean',
            'feature_ads_enabled' => 'nullable|boolean',

            // Company Info
            'company_name' => 'nullable|string|max:255',
            'support_email' => 'nullable|email',
            'support_phone' => 'nullable|string|max:50',
        ]);

        // Update settings in database
        foreach ($validated as $key => $value) {
            \App\Models\AppSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => 'mobile_app',
                    'type' => is_bool($value) ? 'boolean' : 'string'
                ]
            );
        }

        return redirect()->route('admin.settings.mobile-app')
            ->with('success', __('Mobile app settings updated successfully. Changes will sync to the app automatically.'));
    }
}
