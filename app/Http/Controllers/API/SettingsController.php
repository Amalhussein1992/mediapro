<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Get all settings grouped by category
     */
    public function index()
    {
        try {
            $settings = Cache::remember('app_settings_all', 300, function () {
                return DB::table('app_settings')->get()->groupBy('group');
            });

            return response()->json([
                'success' => true,
                'data' => $settings,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load settings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get settings by group
     */
    public function getByGroup($group)
    {
        try {
            $settings = Cache::remember("app_settings_group_{$group}", 300, function () use ($group) {
                return DB::table('app_settings')->where('group', $group)->get();
            });

            return response()->json([
                'success' => true,
                'data' => $settings,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load settings group',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific setting by key
     */
    public function show($key)
    {
        try {
            $setting = DB::table('app_settings')->where('key', $key)->first();

            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Setting not found',
                ], 404);
            }

            // Parse value based on type
            $value = $this->parseValue($setting->value, $setting->type);

            return response()->json([
                'success' => true,
                'data' => [
                    'key' => $setting->key,
                    'value' => $value,
                    'type' => $setting->type,
                    'group' => $setting->group,
                    'description' => $setting->description,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load setting',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update or create a setting
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required',
            'type' => 'required|in:string,json,boolean,integer,float',
            'group' => 'required|in:general,theme,branding,features,integrations,notifications,security',
            'description' => 'nullable|string',
        ]);

        try {
            $oldSetting = DB::table('app_settings')->where('key', $request->key)->first();

            // Convert value based on type
            $value = $this->formatValue($request->value, $request->type);

            $settingData = [
                'key' => $request->key,
                'value' => $value,
                'type' => $request->type,
                'group' => $request->group,
                'description' => $request->description,
                'updated_at' => now(),
            ];

            if ($oldSetting) {
                // Update existing setting
                DB::table('app_settings')
                    ->where('key', $request->key)
                    ->update($settingData);

                // Log the action
                AuditLog::logAction(
                    'setting_updated',
                    auth()->id(),
                    'AppSettings',
                    $oldSetting->id,
                    ['value' => $oldSetting->value],
                    ['value' => $value],
                    "Updated setting: {$request->key}"
                );
            } else {
                // Create new setting
                $settingData['created_at'] = now();
                $id = DB::table('app_settings')->insertGetId($settingData);

                // Log the action
                AuditLog::logAction(
                    'setting_created',
                    auth()->id(),
                    'AppSettings',
                    $id,
                    null,
                    $settingData,
                    "Created setting: {$request->key}"
                );
            }

            // Clear cache
            Cache::forget('app_settings_all');
            Cache::forget("app_settings_group_{$request->group}");
            Cache::forget("app_setting_{$request->key}");

            return response()->json([
                'success' => true,
                'message' => $oldSetting ? 'Setting updated successfully' : 'Setting created successfully',
                'data' => $settingData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save setting',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update multiple settings at once
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $updated = 0;
            foreach ($request->settings as $setting) {
                $existing = DB::table('app_settings')->where('key', $setting['key'])->first();

                if ($existing) {
                    $value = $this->formatValue($setting['value'], $existing->type);

                    DB::table('app_settings')
                        ->where('key', $setting['key'])
                        ->update([
                            'value' => $value,
                            'updated_at' => now(),
                        ]);

                    // Log each update
                    AuditLog::logAction(
                        'setting_bulk_updated',
                        auth()->id(),
                        'AppSettings',
                        $existing->id,
                        ['value' => $existing->value],
                        ['value' => $value],
                        "Bulk updated setting: {$setting['key']}"
                    );

                    $updated++;
                }
            }

            DB::commit();

            // Clear all settings cache
            Cache::forget('app_settings_all');
            foreach (['general', 'theme', 'branding', 'features', 'integrations', 'notifications', 'security'] as $group) {
                Cache::forget("app_settings_group_{$group}");
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updated} settings",
                'data' => ['updated_count' => $updated],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update settings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a setting
     */
    public function destroy($key)
    {
        try {
            $setting = DB::table('app_settings')->where('key', $key)->first();

            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Setting not found',
                ], 404);
            }

            DB::table('app_settings')->where('key', $key)->delete();

            // Log the action
            AuditLog::logAction(
                'setting_deleted',
                auth()->id(),
                'AppSettings',
                $setting->id,
                [
                    'key' => $setting->key,
                    'value' => $setting->value,
                ],
                null,
                "Deleted setting: {$key}"
            );

            // Clear cache
            Cache::forget('app_settings_all');
            Cache::forget("app_settings_group_{$setting->group}");
            Cache::forget("app_setting_{$key}");

            return response()->json([
                'success' => true,
                'message' => 'Setting deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete setting',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Initialize default app settings
     */
    public function initializeDefaults()
    {
        try {
            $defaults = [
                // General Settings
                ['key' => 'app_name', 'value' => 'Social Media Manager', 'type' => 'string', 'group' => 'general', 'description' => 'Application name'],
                ['key' => 'app_version', 'value' => '1.0.0', 'type' => 'string', 'group' => 'general', 'description' => 'Current app version'],
                ['key' => 'maintenance_mode', 'value' => 'false', 'type' => 'boolean', 'group' => 'general', 'description' => 'Enable maintenance mode'],
                ['key' => 'default_language', 'value' => 'en', 'type' => 'string', 'group' => 'general', 'description' => 'Default language (en, ar)'],
                ['key' => 'supported_languages', 'value' => json_encode(['en', 'ar']), 'type' => 'json', 'group' => 'general', 'description' => 'Supported languages'],

                // Theme Settings
                ['key' => 'primary_color', 'value' => '#6366f1', 'type' => 'string', 'group' => 'theme', 'description' => 'Primary brand color'],
                ['key' => 'secondary_color', 'value' => '#8b5cf6', 'type' => 'string', 'group' => 'theme', 'description' => 'Secondary brand color'],
                ['key' => 'dark_mode_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'theme', 'description' => 'Allow dark mode'],
                ['key' => 'default_theme', 'value' => 'light', 'type' => 'string', 'group' => 'theme', 'description' => 'Default theme (light/dark)'],

                // Branding Settings
                ['key' => 'company_name', 'value' => 'Your Company', 'type' => 'string', 'group' => 'branding', 'description' => 'Company name'],
                ['key' => 'company_logo_url', 'value' => '', 'type' => 'string', 'group' => 'branding', 'description' => 'Company logo URL'],
                ['key' => 'support_email', 'value' => 'support@example.com', 'type' => 'string', 'group' => 'branding', 'description' => 'Support email address'],
                ['key' => 'support_phone', 'value' => '', 'type' => 'string', 'group' => 'branding', 'description' => 'Support phone number'],

                // Feature Toggles
                ['key' => 'enable_social_login', 'value' => 'true', 'type' => 'boolean', 'group' => 'features', 'description' => 'Enable social media login'],
                ['key' => 'enable_analytics', 'value' => 'true', 'type' => 'boolean', 'group' => 'features', 'description' => 'Enable analytics tracking'],
                ['key' => 'enable_ai_features', 'value' => 'true', 'type' => 'boolean', 'group' => 'features', 'description' => 'Enable AI-powered features'],
                ['key' => 'enable_multi_account', 'value' => 'true', 'type' => 'boolean', 'group' => 'features', 'description' => 'Allow multiple social accounts'],
                ['key' => 'max_social_accounts', 'value' => '10', 'type' => 'integer', 'group' => 'features', 'description' => 'Maximum social accounts per user'],
                ['key' => 'enable_scheduling', 'value' => 'true', 'type' => 'boolean', 'group' => 'features', 'description' => 'Enable post scheduling'],
                ['key' => 'enable_content_moderation', 'value' => 'true', 'type' => 'boolean', 'group' => 'features', 'description' => 'Enable content moderation'],

                // Integrations
                ['key' => 'enable_facebook', 'value' => 'true', 'type' => 'boolean', 'group' => 'integrations', 'description' => 'Enable Facebook integration'],
                ['key' => 'enable_instagram', 'value' => 'true', 'type' => 'boolean', 'group' => 'integrations', 'description' => 'Enable Instagram integration'],
                ['key' => 'enable_twitter', 'value' => 'true', 'type' => 'boolean', 'group' => 'integrations', 'description' => 'Enable Twitter/X integration'],
                ['key' => 'enable_linkedin', 'value' => 'true', 'type' => 'boolean', 'group' => 'integrations', 'description' => 'Enable LinkedIn integration'],
                ['key' => 'enable_tiktok', 'value' => 'true', 'type' => 'boolean', 'group' => 'integrations', 'description' => 'Enable TikTok integration'],

                // Notifications
                ['key' => 'enable_push_notifications', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Enable push notifications'],
                ['key' => 'enable_email_notifications', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Enable email notifications'],
                ['key' => 'notification_frequency', 'value' => 'instant', 'type' => 'string', 'group' => 'notifications', 'description' => 'Notification frequency (instant/daily/weekly)'],

                // Security
                ['key' => 'require_email_verification', 'value' => 'false', 'type' => 'boolean', 'group' => 'security', 'description' => 'Require email verification'],
                ['key' => 'enable_two_factor', 'value' => 'false', 'type' => 'boolean', 'group' => 'security', 'description' => 'Enable two-factor authentication'],
                ['key' => 'session_timeout', 'value' => '1440', 'type' => 'integer', 'group' => 'security', 'description' => 'Session timeout in minutes'],
                ['key' => 'max_login_attempts', 'value' => '5', 'type' => 'integer', 'group' => 'security', 'description' => 'Maximum login attempts'],
            ];

            $created = 0;
            foreach ($defaults as $setting) {
                $exists = DB::table('app_settings')->where('key', $setting['key'])->exists();
                if (!$exists) {
                    $setting['created_at'] = now();
                    $setting['updated_at'] = now();
                    DB::table('app_settings')->insert($setting);
                    $created++;
                }
            }

            // Clear cache
            Cache::forget('app_settings_all');

            return response()->json([
                'success' => true,
                'message' => "Initialized {$created} default settings",
                'data' => ['created_count' => $created],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize settings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get public settings (for mobile app)
     */
    public function getPublicSettings()
    {
        try {
            $settings = Cache::remember('app_settings_public', 300, function () {
                $allSettings = DB::table('app_settings')->get();

                $formatted = [];
                foreach ($allSettings as $setting) {
                    $formatted[$setting->key] = $this->parseValue($setting->value, $setting->type);
                }

                return $formatted;
            });

            return response()->json([
                'success' => true,
                'data' => $settings,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load public settings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Parse value based on type
     */
    private function parseValue($value, $type)
    {
        if ($value === null) {
            return null;
        }

        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Format value for storage
     */
    private function formatValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return $value ? 'true' : 'false';
            case 'json':
                return is_string($value) ? $value : json_encode($value);
            default:
                return (string) $value;
        }
    }
}
