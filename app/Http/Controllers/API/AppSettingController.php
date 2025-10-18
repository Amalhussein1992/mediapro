<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    /**
     * Get all app settings or by group
     */
    public function index(Request $request)
    {
        $group = $request->query('group');

        if ($group) {
            $settings = AppSetting::getByGroup($group);
        } else {
            $settings = AppSetting::all()->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->value];
            });
        }

        return response()->json([
            'settings' => $settings,
        ]);
    }

    /**
     * Get theme settings for mobile app
     */
    public function theme()
    {
        $themeSettings = AppSetting::getByGroup('theme');

        return response()->json([
            'theme' => $themeSettings,
        ]);
    }

    /**
     * Get branding settings for mobile app
     */
    public function branding()
    {
        $brandingSettings = AppSetting::getByGroup('branding');

        return response()->json([
            'branding' => $brandingSettings,
        ]);
    }

    /**
     * Get specific setting
     */
    public function show($key)
    {
        $setting = AppSetting::where('key', $key)->first();

        if (!$setting) {
            return response()->json([
                'message' => 'Setting not found',
            ], 404);
        }

        return response()->json([
            'key' => $setting->key,
            'value' => $setting->value,
            'type' => $setting->type,
            'group' => $setting->group,
        ]);
    }

    /**
     * Update setting (Admin only)
     */
    public function update(Request $request, $key)
    {
        $request->validate([
            'value' => 'required',
            'type' => 'sometimes|in:string,json,boolean,integer',
            'group' => 'sometimes|string',
            'description' => 'sometimes|string',
        ]);

        $setting = AppSetting::set(
            $key,
            $request->value,
            $request->type ?? 'string',
            $request->group ?? 'general',
            $request->description
        );

        return response()->json([
            'message' => 'Setting updated successfully',
            'setting' => [
                'key' => $setting->key,
                'value' => $setting->value,
                'type' => $setting->type,
                'group' => $setting->group,
            ],
        ]);
    }

    /**
     * Get complete app configuration
     */
    public function config()
    {
        return response()->json([
            'app_name' => AppSetting::get('app_name', 'Media Pro'),
            'app_tagline' => AppSetting::get('app_tagline', 'Professional Social Media Management'),
            'logo_url' => AppSetting::get('logo_url', url('/logo.jpeg')),
            'theme' => [
                'primary' => AppSetting::get('theme_primary', '#6366F1'),
                'primaryDark' => AppSetting::get('theme_primary_dark', '#4F46E5'),
                'primaryLight' => AppSetting::get('theme_primary_light', '#818CF8'),
                'accent' => AppSetting::get('theme_accent', '#8B5CF6'),
                'accentDark' => AppSetting::get('theme_accent_dark', '#7C3AED'),
                'accentLight' => AppSetting::get('theme_accent_light', '#A78BFA'),
                'gradientStart' => AppSetting::get('theme_gradient_start', '#6366F1'),
                'gradientEnd' => AppSetting::get('theme_gradient_end', '#8B5CF6'),
            ],
            'features' => [
                'ai_enabled' => AppSetting::get('feature_ai_enabled', true),
                'analytics_enabled' => AppSetting::get('feature_analytics_enabled', true),
                'ads_enabled' => AppSetting::get('feature_ads_enabled', true),
            ],
        ]);
    }
}
