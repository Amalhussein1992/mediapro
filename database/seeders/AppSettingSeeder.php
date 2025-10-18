<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AppSetting;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Branding Settings
            [
                'key' => 'app_name',
                'value' => 'Media Pro',
                'type' => 'string',
                'group' => 'branding',
                'description' => 'Application name displayed in the mobile app',
            ],
            [
                'key' => 'app_tagline',
                'value' => 'Professional Social Media Management',
                'type' => 'string',
                'group' => 'branding',
                'description' => 'Application tagline displayed on splash and login screens',
            ],
            [
                'key' => 'logo_url',
                'value' => url('/logo.jpeg'),
                'type' => 'string',
                'group' => 'branding',
                'description' => 'URL to the application logo',
            ],

            // Theme Settings - Primary Colors
            [
                'key' => 'theme_primary',
                'value' => '#6366F1',
                'type' => 'string',
                'group' => 'theme',
                'description' => 'Primary brand color (Indigo)',
            ],
            [
                'key' => 'theme_primary_dark',
                'value' => '#4F46E5',
                'type' => 'string',
                'group' => 'theme',
                'description' => 'Dark variant of primary color',
            ],
            [
                'key' => 'theme_primary_light',
                'value' => '#818CF8',
                'type' => 'string',
                'group' => 'theme',
                'description' => 'Light variant of primary color',
            ],

            // Theme Settings - Accent Colors
            [
                'key' => 'theme_accent',
                'value' => '#8B5CF6',
                'type' => 'string',
                'group' => 'theme',
                'description' => 'Accent color (Purple)',
            ],
            [
                'key' => 'theme_accent_dark',
                'value' => '#7C3AED',
                'type' => 'string',
                'group' => 'theme',
                'description' => 'Dark variant of accent color',
            ],
            [
                'key' => 'theme_accent_light',
                'value' => '#A78BFA',
                'type' => 'string',
                'group' => 'theme',
                'description' => 'Light variant of accent color',
            ],

            // Theme Settings - Gradient Colors
            [
                'key' => 'theme_gradient_start',
                'value' => '#6366F1',
                'type' => 'string',
                'group' => 'theme',
                'description' => 'Gradient start color',
            ],
            [
                'key' => 'theme_gradient_end',
                'value' => '#8B5CF6',
                'type' => 'string',
                'group' => 'theme',
                'description' => 'Gradient end color',
            ],

            // Feature Flags
            [
                'key' => 'feature_ai_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'features',
                'description' => 'Enable AI content generation features',
            ],
            [
                'key' => 'feature_analytics_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'features',
                'description' => 'Enable analytics and insights features',
            ],
            [
                'key' => 'feature_ads_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'features',
                'description' => 'Enable ads campaign management features',
            ],
        ];

        foreach ($settings as $setting) {
            AppSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
