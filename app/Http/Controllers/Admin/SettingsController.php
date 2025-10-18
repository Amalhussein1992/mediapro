<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        // Get settings from database
        $dbSettings = DB::table('app_settings')->get()->keyBy('key');

        $settings = [
            'app_name' => $this->getSetting($dbSettings, 'app_name', config('app.name', 'Media Pro')),
            'app_url' => config('app.url', 'http://localhost:8000'),
            'default_language' => $this->getSetting($dbSettings, 'default_language', 'ar'),
            'available_languages' => ['ar', 'en'],
            'timezone' => config('app.timezone', 'Asia/Dubai'),
            'currency' => $this->getSetting($dbSettings, 'currency', 'USD'),
            'maintenance_mode' => $this->getSetting($dbSettings, 'maintenance_mode', false, 'boolean'),
            'openai_enabled' => !empty(env('OPENAI_API_KEY')),
            'email_notifications' => $this->getSetting($dbSettings, 'enable_email_notifications', true, 'boolean'),
            'push_notifications' => $this->getSetting($dbSettings, 'enable_push_notifications', true, 'boolean'),
        ];

        // Available currencies
        $currencies = [
            'USD' => ['name' => 'US Dollar', 'symbol' => '$', 'name_ar' => 'دولار أمريكي'],
            'EUR' => ['name' => 'Euro', 'symbol' => '€', 'name_ar' => 'يورو'],
            'GBP' => ['name' => 'British Pound', 'symbol' => '£', 'name_ar' => 'جنيه إسترليني'],
            'SAR' => ['name' => 'Saudi Riyal', 'symbol' => 'ر.س', 'name_ar' => 'ريال سعودي'],
            'AED' => ['name' => 'UAE Dirham', 'symbol' => 'د.إ', 'name_ar' => 'درهم إماراتي'],
            'EGP' => ['name' => 'Egyptian Pound', 'symbol' => 'ج.م', 'name_ar' => 'جنيه مصري'],
            'KWD' => ['name' => 'Kuwaiti Dinar', 'symbol' => 'د.ك', 'name_ar' => 'دينار كويتي'],
            'QAR' => ['name' => 'Qatari Riyal', 'symbol' => 'ر.ق', 'name_ar' => 'ريال قطري'],
            'BHD' => ['name' => 'Bahraini Dinar', 'symbol' => 'د.ب', 'name_ar' => 'دينار بحريني'],
            'OMR' => ['name' => 'Omani Rial', 'symbol' => 'ر.ع', 'name_ar' => 'ريال عماني'],
            'JOD' => ['name' => 'Jordanian Dinar', 'symbol' => 'د.أ', 'name_ar' => 'دينار أردني'],
            'IQD' => ['name' => 'Iraqi Dinar', 'symbol' => 'د.ع', 'name_ar' => 'دينار عراقي'],
        ];

        // Read .env file content
        $envPath = base_path('.env');
        $envContent = File::exists($envPath) ? File::get($envPath) : '';

        return view('admin.settings', compact('settings', 'envContent', 'currencies'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'nullable|string|max:255',
            'default_language' => 'nullable|in:ar,en',
            'timezone' => 'nullable|string',
            'currency' => 'nullable|string|max:10',
            'email_notifications' => 'nullable|boolean',
            'push_notifications' => 'nullable|boolean',

            // AI Settings
            'openai_api_key' => 'nullable|string',
            'openai_model' => 'nullable|string',
            'openai_max_tokens' => 'nullable|integer|min:100|max:8000',
            'gemini_api_key' => 'nullable|string',
            'gemini_model' => 'nullable|string',
            'claude_api_key' => 'nullable|string',
            'claude_model' => 'nullable|string',
            'claude_max_tokens' => 'nullable|integer|min:100|max:8000',
            'default_ai_provider' => 'nullable|in:openai,gemini,claude',
            'whisper_api_key' => 'nullable|string',
            'google_cloud_project_id' => 'nullable|string',
            'ai_features_enabled' => 'nullable|boolean',

            // Social Media OAuth Settings
            'facebook_client_id' => 'nullable|string',
            'facebook_client_secret' => 'nullable|string',
            'instagram_client_id' => 'nullable|string',
            'instagram_client_secret' => 'nullable|string',
            'twitter_client_id' => 'nullable|string',
            'twitter_client_secret' => 'nullable|string',
            'linkedin_client_id' => 'nullable|string',
            'linkedin_client_secret' => 'nullable|string',
            'tiktok_client_id' => 'nullable|string',
            'tiktok_client_secret' => 'nullable|string',
            'youtube_client_id' => 'nullable|string',
            'youtube_client_secret' => 'nullable|string',
            'pinterest_client_id' => 'nullable|string',
            'pinterest_client_secret' => 'nullable|string',
            'snapchat_client_id' => 'nullable|string',
            'snapchat_client_secret' => 'nullable|string',

            // User Authentication OAuth (Google & Apple)
            'google_client_id' => 'nullable|string',
            'google_client_secret' => 'nullable|string',
            'apple_client_id' => 'nullable|string',
            'apple_team_id' => 'nullable|string',
            'apple_key_id' => 'nullable|string',
            'apple_private_key' => 'nullable|string',

            // Payment Services
            'stripe_key' => 'nullable|string',
            'stripe_secret' => 'nullable|string',
            'stripe_webhook_secret' => 'nullable|string',
            'paypal_mode' => 'nullable|in:sandbox,live',
            'paypal_client_id' => 'nullable|string',
            'paypal_secret' => 'nullable|string',

            // Google Pay
            'google_pay_merchant_id' => 'nullable|string',
            'google_pay_merchant_name' => 'nullable|string',
            'google_pay_gateway_id' => 'nullable|string',

            // Apple Pay
            'apple_pay_merchant_id' => 'nullable|string',
            'apple_pay_merchant_name' => 'nullable|string',
            'apple_pay_merchant_domain' => 'nullable|string',
            'apple_pay_certificate' => 'nullable|string',

            // AWS Services
            'aws_access_key_id' => 'nullable|string',
            'aws_secret_access_key' => 'nullable|string',
            'aws_default_region' => 'nullable|string',
            'aws_bucket' => 'nullable|string',

            // Database Configuration
            'db_connection' => 'nullable|string',
            'db_host' => 'nullable|string',
            'db_port' => 'nullable|integer',
            'db_database' => 'nullable|string',
            'db_username' => 'nullable|string',
            'db_password' => 'nullable|string',

            // Mail Configuration
            'mail_mailer' => 'nullable|string',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string',
        ]);

        // Handle logo upload
        if ($request->hasFile('app_logo')) {
            $file = $request->file('app_logo');

            // Validate file
            if ($file->isValid()) {
                $extension = $file->getClientOriginalExtension();
                $filename = 'logo.' . $extension;

                // Ensure storage directory exists
                $storagePath = public_path('storage');
                if (!File::exists($storagePath)) {
                    File::makeDirectory($storagePath, 0755, true);
                }

                // Store in public/storage
                $file->move($storagePath, $filename);

                // If different extension, also save as logo.png for fallback
                if ($extension !== 'png') {
                    copy(public_path('storage/' . $filename), public_path('storage/logo.png'));
                }
            }
        }

        // Update settings in database
        DB::beginTransaction();
        try {
            // Update app_name in both database and .env
            if (isset($validated['app_name'])) {
                $this->updateOrCreateSetting('app_name', $validated['app_name'], 'string', 'general');
                $this->updateEnvVariable('APP_NAME', $validated['app_name']);
            }

            // Update default_language
            if (isset($validated['default_language'])) {
                $this->updateOrCreateSetting('default_language', $validated['default_language'], 'string', 'general');
            }

            // Update timezone in .env
            if (isset($validated['timezone'])) {
                $this->updateEnvVariable('APP_TIMEZONE', $validated['timezone']);
            }

            // Update currency
            if (isset($validated['currency'])) {
                $this->updateOrCreateSetting('currency', $validated['currency'], 'string', 'general');
            }

            // Update email notifications
            if (isset($validated['email_notifications'])) {
                $this->updateOrCreateSetting('enable_email_notifications', $validated['email_notifications'] ? 'true' : 'false', 'boolean', 'notifications');
            }

            // Update push notifications
            if (isset($validated['push_notifications'])) {
                $this->updateOrCreateSetting('enable_push_notifications', $validated['push_notifications'] ? 'true' : 'false', 'boolean', 'notifications');
            }

            // Update AI Settings in .env
            if (isset($validated['openai_api_key'])) {
                $this->updateEnvVariable('OPENAI_API_KEY', $validated['openai_api_key']);
            }
            if (isset($validated['openai_model'])) {
                $this->updateEnvVariable('OPENAI_MODEL', $validated['openai_model']);
            }
            if (isset($validated['openai_max_tokens'])) {
                $this->updateEnvVariable('OPENAI_MAX_TOKENS', $validated['openai_max_tokens']);
            }

            if (isset($validated['gemini_api_key'])) {
                $this->updateEnvVariable('GEMINI_API_KEY', $validated['gemini_api_key']);
            }
            if (isset($validated['gemini_model'])) {
                $this->updateEnvVariable('GEMINI_MODEL', $validated['gemini_model']);
            }

            if (isset($validated['claude_api_key'])) {
                $this->updateEnvVariable('CLAUDE_API_KEY', $validated['claude_api_key']);
            }
            if (isset($validated['claude_model'])) {
                $this->updateEnvVariable('CLAUDE_MODEL', $validated['claude_model']);
            }
            if (isset($validated['claude_max_tokens'])) {
                $this->updateEnvVariable('CLAUDE_MAX_TOKENS', $validated['claude_max_tokens']);
            }

            if (isset($validated['default_ai_provider'])) {
                $this->updateEnvVariable('DEFAULT_AI_PROVIDER', $validated['default_ai_provider']);
            }

            if (isset($validated['whisper_api_key'])) {
                $this->updateEnvVariable('WHISPER_API_KEY', $validated['whisper_api_key']);
            }

            if (isset($validated['google_cloud_project_id'])) {
                $this->updateEnvVariable('GOOGLE_CLOUD_PROJECT_ID', $validated['google_cloud_project_id']);
            }

            if (isset($validated['ai_features_enabled'])) {
                $this->updateEnvVariable('AI_FEATURES_ENABLED', $validated['ai_features_enabled'] ? 'true' : 'false');
            }

            // Update Social Media OAuth Settings in .env
            $socialOAuthFields = [
                'facebook_client_id' => 'FACEBOOK_CLIENT_ID',
                'facebook_client_secret' => 'FACEBOOK_CLIENT_SECRET',
                'instagram_client_id' => 'INSTAGRAM_CLIENT_ID',
                'instagram_client_secret' => 'INSTAGRAM_CLIENT_SECRET',
                'twitter_client_id' => 'TWITTER_CLIENT_ID',
                'twitter_client_secret' => 'TWITTER_CLIENT_SECRET',
                'linkedin_client_id' => 'LINKEDIN_CLIENT_ID',
                'linkedin_client_secret' => 'LINKEDIN_CLIENT_SECRET',
                'tiktok_client_id' => 'TIKTOK_CLIENT_ID',
                'tiktok_client_secret' => 'TIKTOK_CLIENT_SECRET',
                'youtube_client_id' => 'YOUTUBE_CLIENT_ID',
                'youtube_client_secret' => 'YOUTUBE_CLIENT_SECRET',
                'pinterest_client_id' => 'PINTEREST_CLIENT_ID',
                'pinterest_client_secret' => 'PINTEREST_CLIENT_SECRET',
                'snapchat_client_id' => 'SNAPCHAT_CLIENT_ID',
                'snapchat_client_secret' => 'SNAPCHAT_CLIENT_SECRET',
            ];

            foreach ($socialOAuthFields as $field => $envKey) {
                if (isset($validated[$field])) {
                    $this->updateEnvVariable($envKey, $validated[$field]);
                }
            }

            // Update User Authentication OAuth (Google & Apple)
            $userAuthFields = [
                'google_client_id' => 'GOOGLE_CLIENT_ID',
                'google_client_secret' => 'GOOGLE_CLIENT_SECRET',
                'apple_client_id' => 'APPLE_CLIENT_ID',
                'apple_team_id' => 'APPLE_TEAM_ID',
                'apple_key_id' => 'APPLE_KEY_ID',
                'apple_private_key' => 'APPLE_PRIVATE_KEY',
            ];

            foreach ($userAuthFields as $field => $envKey) {
                if (isset($validated[$field])) {
                    $this->updateEnvVariable($envKey, $validated[$field]);
                }
            }

            // Update Payment Services
            $paymentFields = [
                'stripe_key' => 'STRIPE_KEY',
                'stripe_secret' => 'STRIPE_SECRET',
                'stripe_webhook_secret' => 'STRIPE_WEBHOOK_SECRET',
                'paypal_mode' => 'PAYPAL_MODE',
                'paypal_client_id' => 'PAYPAL_CLIENT_ID',
                'paypal_secret' => 'PAYPAL_SECRET',
                'google_pay_merchant_id' => 'GOOGLE_PAY_MERCHANT_ID',
                'google_pay_merchant_name' => 'GOOGLE_PAY_MERCHANT_NAME',
                'google_pay_gateway_id' => 'GOOGLE_PAY_GATEWAY_ID',
                'apple_pay_merchant_id' => 'APPLE_PAY_MERCHANT_ID',
                'apple_pay_merchant_name' => 'APPLE_PAY_MERCHANT_NAME',
                'apple_pay_merchant_domain' => 'APPLE_PAY_MERCHANT_DOMAIN',
                'apple_pay_certificate' => 'APPLE_PAY_CERTIFICATE',
            ];

            foreach ($paymentFields as $field => $envKey) {
                if (isset($validated[$field])) {
                    $this->updateEnvVariable($envKey, $validated[$field]);
                }
            }

            // Update AWS Services
            $awsFields = [
                'aws_access_key_id' => 'AWS_ACCESS_KEY_ID',
                'aws_secret_access_key' => 'AWS_SECRET_ACCESS_KEY',
                'aws_default_region' => 'AWS_DEFAULT_REGION',
                'aws_bucket' => 'AWS_BUCKET',
            ];

            foreach ($awsFields as $field => $envKey) {
                if (isset($validated[$field])) {
                    $this->updateEnvVariable($envKey, $validated[$field]);
                }
            }

            // Update Database Configuration
            $dbFields = [
                'db_connection' => 'DB_CONNECTION',
                'db_host' => 'DB_HOST',
                'db_port' => 'DB_PORT',
                'db_database' => 'DB_DATABASE',
                'db_username' => 'DB_USERNAME',
                'db_password' => 'DB_PASSWORD',
            ];

            foreach ($dbFields as $field => $envKey) {
                if (isset($validated[$field])) {
                    $this->updateEnvVariable($envKey, $validated[$field]);
                }
            }

            // Update Mail Configuration
            $mailFields = [
                'mail_mailer' => 'MAIL_MAILER',
                'mail_host' => 'MAIL_HOST',
                'mail_port' => 'MAIL_PORT',
                'mail_username' => 'MAIL_USERNAME',
                'mail_password' => 'MAIL_PASSWORD',
                'mail_from_address' => 'MAIL_FROM_ADDRESS',
                'mail_from_name' => 'MAIL_FROM_NAME',
            ];

            foreach ($mailFields as $field => $envKey) {
                if (isset($validated[$field])) {
                    $this->updateEnvVariable($envKey, $validated[$field]);
                }
            }

            DB::commit();

            // Clear cache
            Cache::forget('app_settings_all');
            Cache::forget('app_settings_public');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update settings: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to update settings');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم تحديث الإعدادات بنجاح في قاعدة البيانات وملف التطبيق (.env). تم إنشاء نسخة احتياطية.'
                    : 'Settings updated successfully in database and application file (.env). Backup created.'
            ]);
        }

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تم تحديث الإعدادات بنجاح في قاعدة البيانات وملف التطبيق (.env)'
            : 'Settings updated successfully in database and application file (.env)');
    }

    /**
     * Get setting value from database with fallback
     */
    private function getSetting($dbSettings, $key, $default = null, $type = 'string')
    {
        if (!isset($dbSettings[$key])) {
            return $default;
        }

        $value = $dbSettings[$key]->value;

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
     * Update or create setting in database
     */
    private function updateOrCreateSetting($key, $value, $type, $group, $description = null)
    {
        $existing = DB::table('app_settings')->where('key', $key)->first();

        $data = [
            'value' => $value,
            'type' => $type,
            'group' => $group,
            'updated_at' => now(),
        ];

        if ($description) {
            $data['description'] = $description;
        }

        if ($existing) {
            DB::table('app_settings')->where('key', $key)->update($data);
        } else {
            $data['key'] = $key;
            $data['created_at'] = now();
            DB::table('app_settings')->insert($data);
        }
    }

    public function clearCache()
    {
        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => trans('messages.Cache cleared successfully')
        ]);
    }

    public function toggleMaintenance(Request $request)
    {
        if ($request->input('enabled')) {
            \Artisan::call('down');
            $message = trans('messages.Maintenance mode enabled');
        } else {
            \Artisan::call('up');
            $message = trans('messages.Maintenance mode disabled');
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Update .env file
     */
    public function updateEnv(Request $request)
    {
        $request->validate([
            'env_content' => 'required|string',
        ]);

        try {
            $envPath = base_path('.env');

            // Create backup
            $backupPath = base_path('.env.backup.' . date('Y-m-d_H-i-s'));
            if (File::exists($envPath)) {
                File::copy($envPath, $backupPath);
            }

            // Write new content
            File::put($envPath, $request->input('env_content'));

            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'تم حفظ ملف .env بنجاح. تم إنشاء نسخة احتياطية.'
                    : '.env file saved successfully. Backup created.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar'
                    ? 'فشل حفظ ملف .env: ' . $e->getMessage()
                    : 'Failed to save .env file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get .env file content
     */
    public function getEnv()
    {
        try {
            $envPath = base_path('.env');
            $content = File::exists($envPath) ? File::get($envPath) : '';

            return response()->json([
                'success' => true,
                'data' => $content
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to read .env file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update .env variable
     */
    private function updateEnvVariable($key, $value)
    {
        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            return false;
        }

        // Create backup on first update in this session
        static $backupCreated = false;
        if (!$backupCreated) {
            $backupPath = base_path('.env.backup.' . date('Y-m-d_H-i-s'));
            File::copy($envPath, $backupPath);
            $backupCreated = true;
        }

        $content = File::get($envPath);

        // Escape special characters in value
        $escapedValue = str_replace('"', '\"', $value);

        // Check if key exists
        if (preg_match("/^{$key}=.*/m", $content)) {
            // Update existing key
            $content = preg_replace(
                "/^{$key}=.*/m",
                "{$key}=\"{$escapedValue}\"",
                $content
            );
        } else {
            // Add new key at the end
            $content .= "\n{$key}=\"{$escapedValue}\"";
        }

        File::put($envPath, $content);

        return true;
    }

    /**
     * Update a single setting via AJAX.
     */
    public function updateSingle(Request $request)
    {
        try {
            $settingKey = $request->input('key');
            $settingValue = $request->input('value');
            $settingGroup = $request->input('group', 'general');

            // Define validation rules for each setting
            $validationRules = [
                'app_name' => 'nullable|string|max:255',
                'default_language' => 'nullable|in:ar,en',
                'currency' => 'nullable|string|max:10',
                'timezone' => 'nullable|string|max:100',
                'enable_email_notifications' => 'nullable|boolean',
                'enable_push_notifications' => 'nullable|boolean',
                'enable_sms_notifications' => 'nullable|boolean',
                'smtp_host' => 'nullable|string|max:255',
                'smtp_port' => 'nullable|integer',
                'smtp_username' => 'nullable|string|max:255',
                'smtp_password' => 'nullable|string|max:255',
                'smtp_encryption' => 'nullable|in:tls,ssl,none',
                'smtp_from_address' => 'nullable|email|max:255',
                'smtp_from_name' => 'nullable|string|max:255',
                'stripe_publishable_key' => 'nullable|string',
                'stripe_secret_key' => 'nullable|string',
                'stripe_webhook_secret' => 'nullable|string',
                'paypal_mode' => 'nullable|in:sandbox,live',
                'paypal_client_id' => 'nullable|string',
                'paypal_secret' => 'nullable|string',
                'openai_api_key' => 'nullable|string',
                'gemini_api_key' => 'nullable|string',
                'claude_api_key' => 'nullable|string',
                'default_ai_provider' => 'nullable|in:openai,gemini,claude',
                'facebook_app_id' => 'nullable|string',
                'facebook_app_secret' => 'nullable|string',
                'twitter_api_key' => 'nullable|string',
                'twitter_api_secret' => 'nullable|string',
                'instagram_client_id' => 'nullable|string',
                'instagram_client_secret' => 'nullable|string',
                'linkedin_client_id' => 'nullable|string',
                'linkedin_client_secret' => 'nullable|string',
                'tiktok_client_key' => 'nullable|string',
                'tiktok_client_secret' => 'nullable|string',
                'youtube_client_id' => 'nullable|string',
                'youtube_client_secret' => 'nullable|string',
                'pinterest_app_id' => 'nullable|string',
                'pinterest_app_secret' => 'nullable|string',
            ];

            // Validate if rule exists
            if (isset($validationRules[$settingKey])) {
                $request->validate([
                    'value' => $validationRules[$settingKey],
                ]);
            }

            // Determine setting type
            $type = 'string';
            if (in_array($settingKey, ['enable_email_notifications', 'enable_push_notifications', 'enable_sms_notifications'])) {
                $type = 'boolean';
                $settingValue = filter_var($settingValue, FILTER_VALIDATE_BOOLEAN);
            } elseif (in_array($settingKey, ['smtp_port'])) {
                $type = 'integer';
            }

            // Update in database
            DB::table('app_settings')->updateOrInsert(
                ['key' => $settingKey],
                [
                    'value' => $settingValue,
                    'type' => $type,
                    'group' => $settingGroup,
                    'updated_at' => now(),
                ]
            );

            // Update .env file for specific keys
            $envKeys = [
                'stripe_publishable_key' => 'STRIPE_PUBLISHABLE_KEY',
                'stripe_secret_key' => 'STRIPE_SECRET_KEY',
                'stripe_webhook_secret' => 'STRIPE_WEBHOOK_SECRET',
                'paypal_mode' => 'PAYPAL_MODE',
                'paypal_client_id' => 'PAYPAL_CLIENT_ID',
                'paypal_secret' => 'PAYPAL_SECRET',
                'openai_api_key' => 'OPENAI_API_KEY',
                'gemini_api_key' => 'GEMINI_API_KEY',
                'claude_api_key' => 'CLAUDE_API_KEY',
                'facebook_app_id' => 'FACEBOOK_APP_ID',
                'facebook_app_secret' => 'FACEBOOK_APP_SECRET',
                'twitter_api_key' => 'TWITTER_API_KEY',
                'twitter_api_secret' => 'TWITTER_API_SECRET',
                'instagram_client_id' => 'INSTAGRAM_CLIENT_ID',
                'instagram_client_secret' => 'INSTAGRAM_CLIENT_SECRET',
                'linkedin_client_id' => 'LINKEDIN_CLIENT_ID',
                'linkedin_client_secret' => 'LINKEDIN_CLIENT_SECRET',
                'tiktok_client_key' => 'TIKTOK_CLIENT_KEY',
                'tiktok_client_secret' => 'TIKTOK_CLIENT_SECRET',
                'youtube_client_id' => 'YOUTUBE_CLIENT_ID',
                'youtube_client_secret' => 'YOUTUBE_CLIENT_SECRET',
                'pinterest_app_id' => 'PINTEREST_APP_ID',
                'pinterest_app_secret' => 'PINTEREST_APP_SECRET',
            ];

            if (isset($envKeys[$settingKey])) {
                $this->updateEnvVariable($envKeys[$settingKey], $settingValue);
            }

            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar' ? 'تم حفظ الإعداد بنجاح' : 'Setting saved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() === 'ar' ? 'حدث خطأ أثناء حفظ الإعداد: ' . $e->getMessage() : 'Error saving setting: ' . $e->getMessage(),
            ], 500);
        }
    }
}
