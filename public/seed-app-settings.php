<?php
/**
 * Initialize App Settings
 * Run this once then DELETE this file for security
 */

// Database configuration
$host = '127.0.0.1';
$db = 'socialmedia_manager';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "<!DOCTYPE html>
<html lang='en' dir='ltr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Initialize App Settings</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 2rem;
        }
        .step {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px 20px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .step.success {
            border-color: #10b981;
            background: #d1fae5;
        }
        .step.error {
            border-color: #ef4444;
            background: #fee2e2;
        }
        .step h3 {
            color: #333;
            margin-bottom: 8px;
            font-size: 1.1rem;
        }
        .step p {
            color: #666;
            line-height: 1.6;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-box h4 {
            font-size: 2rem;
            margin-bottom: 5px;
        }
        .stat-box p {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .warning {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .warning h3 {
            color: #b45309;
            margin-bottom: 10px;
        }
        .warning p {
            color: #92400e;
            line-height: 1.6;
        }
        code {
            background: #1f2937;
            color: #10b981;
            padding: 2px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
<div class='container'>
    <h1>🚀 Initialize App Settings</h1>
";

    // Default settings to seed
    $defaults = [
        // General Settings
        ['key' => 'app_name', 'value' => 'Social Media Manager', 'type' => 'string', 'group' => 'general', 'description' => 'Application name'],
        ['key' => 'app_version', 'value' => '1.0.0', 'type' => 'string', 'group' => 'general', 'description' => 'Current app version'],
        ['key' => 'maintenance_mode', 'value' => 'false', 'type' => 'boolean', 'group' => 'general', 'description' => 'Enable maintenance mode'],
        ['key' => 'default_language', 'value' => 'ar', 'type' => 'string', 'group' => 'general', 'description' => 'Default language (en, ar)'],
        ['key' => 'supported_languages', 'value' => json_encode(['en', 'ar']), 'type' => 'json', 'group' => 'general', 'description' => 'Supported languages'],

        // Theme Settings
        ['key' => 'primary_color', 'value' => '#6366f1', 'type' => 'string', 'group' => 'theme', 'description' => 'Primary brand color'],
        ['key' => 'secondary_color', 'value' => '#8b5cf6', 'type' => 'string', 'group' => 'theme', 'description' => 'Secondary brand color'],
        ['key' => 'dark_mode_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'theme', 'description' => 'Allow dark mode'],
        ['key' => 'default_theme', 'value' => 'light', 'type' => 'string', 'group' => 'theme', 'description' => 'Default theme (light/dark)'],

        // Branding Settings
        ['key' => 'company_name', 'value' => 'Media Pro', 'type' => 'string', 'group' => 'branding', 'description' => 'Company name'],
        ['key' => 'company_logo_url', 'value' => '', 'type' => 'string', 'group' => 'branding', 'description' => 'Company logo URL'],
        ['key' => 'support_email', 'value' => 'support@mediapro.social', 'type' => 'string', 'group' => 'branding', 'description' => 'Support email address'],
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
    $updated = 0;
    $skipped = 0;

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM app_settings WHERE `key` = ?");
    $insertStmt = $pdo->prepare("
        INSERT INTO app_settings (`key`, `value`, `type`, `group`, `description`, `created_at`, `updated_at`)
        VALUES (?, ?, ?, ?, ?, NOW(), NOW())
    ");

    foreach ($defaults as $setting) {
        $stmt->execute([$setting['key']]);
        $exists = $stmt->fetchColumn();

        if (!$exists) {
            $insertStmt->execute([
                $setting['key'],
                $setting['value'],
                $setting['type'],
                $setting['group'],
                $setting['description']
            ]);
            $created++;
            echo "<div class='step success'>
                    <h3>✅ Created: {$setting['key']}</h3>
                    <p>{$setting['description']}</p>
                  </div>";
        } else {
            $skipped++;
        }
    }

    echo "
    <div class='stats'>
        <div class='stat-box'>
            <h4>$created</h4>
            <p>Settings Created</p>
        </div>
        <div class='stat-box'>
            <h4>$skipped</h4>
            <p>Already Existed</p>
        </div>
        <div class='stat-box'>
            <h4>" . count($defaults) . "</h4>
            <p>Total Settings</p>
        </div>
    </div>

    <div class='step success'>
        <h3>🎉 Success!</h3>
        <p>App settings have been initialized successfully!</p>
    </div>

    <div class='warning'>
        <h3>⚠️ Security Warning</h3>
        <p><strong>IMPORTANT:</strong> For security reasons, you must DELETE this file immediately after use!</p>
        <p>Delete the file: <code>public/seed-app-settings.php</code></p>
    </div>

    <div class='step'>
        <h3>📱 Next Steps</h3>
        <p>1. Test the mobile app - settings should now load properly</p>
        <p>2. Visit: <code>http://192.168.1.15:8000/api/settings/public</code> to verify</p>
        <p>3. DELETE this file: <code>seed-app-settings.php</code></p>
    </div>
</div>
</body>
</html>";

} catch (PDOException $e) {
    echo "<!DOCTYPE html>
<html>
<head><title>Error</title></head>
<body style='font-family: Arial; padding: 40px; background: #fee2e2;'>
    <div style='background: white; padding: 30px; border-radius: 10px; border-left: 4px solid #ef4444;'>
        <h1 style='color: #dc2626;'>❌ Database Error</h1>
        <p style='color: #666;'>Failed to connect to database or seed settings.</p>
        <pre style='background: #f3f4f6; padding: 15px; border-radius: 5px; overflow-x: auto;'>" . htmlspecialchars($e->getMessage()) . "</pre>
        <p><strong>Please check your database configuration.</strong></p>
    </div>
</body>
</html>";
}
