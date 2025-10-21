<?php
/**
 * Standalone API Router
 * Provides essential API endpoints while Laravel routing is being fixed
 *
 * DELETE THIS FILE once Laravel routing is restored!
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database configuration
$dbConfig = [
    'host' => '127.0.0.1',
    'database' => 'socialmedia_manager',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

// Helper function to connect to database
function getDB($config) {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    return $pdo;
}

// Helper function to parse setting values
function parseValue($value, $type) {
    if ($value === null) return null;

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

// Helper function to send JSON response
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Get the request path
$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);
$requestPath = str_replace('/api-router.php', '', $requestPath);
$requestMethod = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = getDB($dbConfig);

    // ==================== ROUTES ====================

    // GET /settings/public - Get all public app settings
    if ($requestPath === '/settings/public' && $requestMethod === 'GET') {
        $stmt = $pdo->query("SELECT `key`, `value`, `type` FROM app_settings");
        $settings = [];

        while ($row = $stmt->fetch()) {
            $settings[$row['key']] = parseValue($row['value'], $row['type']);
        }

        jsonResponse([
            'success' => true,
            'data' => $settings,
            'message' => 'Settings retrieved successfully'
        ]);
    }

    // GET /settings/theme - Get theme settings
    elseif ($requestPath === '/settings/theme' && $requestMethod === 'GET') {
        $stmt = $pdo->query("SELECT `key`, `value`, `type` FROM app_settings WHERE `group` = 'theme'");
        $settings = [];

        while ($row = $stmt->fetch()) {
            $settings[$row['key']] = parseValue($row['value'], $row['type']);
        }

        jsonResponse([
            'success' => true,
            'data' => $settings
        ]);
    }

    // GET /settings/branding - Get branding settings
    elseif ($requestPath === '/settings/branding' && $requestMethod === 'GET') {
        $stmt = $pdo->query("SELECT `key`, `value`, `type` FROM app_settings WHERE `group` = 'branding'");
        $settings = [];

        while ($row = $stmt->fetch()) {
            $settings[$row['key']] = parseValue($row['value'], $row['type']);
        }

        jsonResponse([
            'success' => true,
            'data' => $settings
        ]);
    }

    // GET /config - Get app configuration
    elseif ($requestPath === '/config' && $requestMethod === 'GET') {
        // Get all settings
        $stmt = $pdo->query("SELECT `key`, `value`, `type`, `group` FROM app_settings");
        $allSettings = [];

        while ($row = $stmt->fetch()) {
            $allSettings[$row['key']] = parseValue($row['value'], $row['type']);
        }

        // Build config response
        $config = [
            'app_name' => $allSettings['app_name'] ?? 'Social Media Manager',
            'app_version' => $allSettings['app_version'] ?? '1.0.0',
            'maintenance_mode' => $allSettings['maintenance_mode'] ?? false,
            'languages' => [
                'default' => $allSettings['default_language'] ?? 'ar',
                'supported' => $allSettings['supported_languages'] ?? ['en', 'ar']
            ],
            'theme' => [
                'primary_color' => $allSettings['primary_color'] ?? '#6366f1',
                'secondary_color' => $allSettings['secondary_color'] ?? '#8b5cf6',
                'dark_mode_enabled' => $allSettings['dark_mode_enabled'] ?? true,
                'default_theme' => $allSettings['default_theme'] ?? 'light'
            ],
            'branding' => [
                'company_name' => $allSettings['company_name'] ?? 'Media Pro',
                'logo_url' => $allSettings['company_logo_url'] ?? '',
                'support_email' => $allSettings['support_email'] ?? 'support@mediapro.social',
                'support_phone' => $allSettings['support_phone'] ?? ''
            ],
            'features' => [
                'social_login' => $allSettings['enable_social_login'] ?? true,
                'analytics' => $allSettings['enable_analytics'] ?? true,
                'ai_features' => $allSettings['enable_ai_features'] ?? true,
                'multi_account' => $allSettings['enable_multi_account'] ?? true,
                'max_accounts' => $allSettings['max_social_accounts'] ?? 10,
                'scheduling' => $allSettings['enable_scheduling'] ?? true,
                'content_moderation' => $allSettings['enable_content_moderation'] ?? true
            ],
            'integrations' => [
                'facebook' => $allSettings['enable_facebook'] ?? true,
                'instagram' => $allSettings['enable_instagram'] ?? true,
                'twitter' => $allSettings['enable_twitter'] ?? true,
                'linkedin' => $allSettings['enable_linkedin'] ?? true,
                'tiktok' => $allSettings['enable_tiktok'] ?? true
            ],
            'notifications' => [
                'push' => $allSettings['enable_push_notifications'] ?? true,
                'email' => $allSettings['enable_email_notifications'] ?? true,
                'frequency' => $allSettings['notification_frequency'] ?? 'instant'
            ],
            'security' => [
                'email_verification' => $allSettings['require_email_verification'] ?? false,
                'two_factor' => $allSettings['enable_two_factor'] ?? false,
                'session_timeout' => $allSettings['session_timeout'] ?? 1440,
                'max_login_attempts' => $allSettings['max_login_attempts'] ?? 5
            ]
        ];

        jsonResponse([
            'success' => true,
            'data' => $config
        ]);
    }

    // GET /test-deployment - Test endpoint
    elseif ($requestPath === '/test-deployment' && $requestMethod === 'GET') {
        jsonResponse([
            'success' => true,
            'message' => 'API router is working!',
            'timestamp' => date('Y-m-d H:i:s'),
            'server' => 'Standalone PHP Router',
            'note' => 'This is a temporary solution while Laravel routing is being fixed'
        ]);
    }

    // Route not found
    else {
        jsonResponse([
            'success' => false,
            'message' => 'Route not found',
            'path' => $requestPath,
            'method' => $requestMethod,
            'available_routes' => [
                'GET /settings/public',
                'GET /settings/theme',
                'GET /settings/branding',
                'GET /config',
                'GET /test-deployment'
            ]
        ], 404);
    }

} catch (PDOException $e) {
    jsonResponse([
        'success' => false,
        'message' => 'Database error',
        'error' => $e->getMessage()
    ], 500);
} catch (Exception $e) {
    jsonResponse([
        'success' => false,
        'message' => 'Server error',
        'error' => $e->getMessage()
    ], 500);
}
