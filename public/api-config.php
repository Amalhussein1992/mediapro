<?php
/**
 * App Configuration API Endpoint
 * Returns app configuration including theme, features, and branding
 *
 * Endpoint: GET /api-config.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization, Accept-Language');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use GET.'
    ]);
    exit;
}

try {
    // Database configuration
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $dbname = getenv('DB_DATABASE') ?: 'socialmedia_manager';
    $username = getenv('DB_USERNAME') ?: 'root';
    $password = getenv('DB_PASSWORD') ?: '';

    // Connect to database
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Fetch all app settings from database
    $stmt = $pdo->prepare("SELECT `key`, `value`, `type` FROM app_settings");
    $stmt->execute();
    $settings = $stmt->fetchAll();

    // Convert settings array to associative array with proper type casting
    $config = [];
    foreach ($settings as $setting) {
        $key = $setting['key'];
        $value = $setting['value'];
        $type = $setting['type'];

        // Type cast based on the type field
        switch ($type) {
            case 'integer':
                $config[$key] = (int)$value;
                break;
            case 'float':
                $config[$key] = (float)$value;
                break;
            case 'boolean':
                $config[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                break;
            case 'json':
                $config[$key] = json_decode($value, true);
                break;
            default:
                $config[$key] = $value;
        }
    }

    // Build structured app configuration response with Arabic defaults
    $response = [
        'app_name' => $config['app_name'] ?? 'ميديا برو',
        'app_tagline' => $config['app_tagline'] ?? 'إدارة احترافية لوسائل التواصل الاجتماعي',
        'logo_url' => $config['company_logo_url'] ?? '',
        'theme' => [
            'primary' => $config['primary_color'] ?? '#6366F1',
            'primaryDark' => $config['primary_dark_color'] ?? '#4F46E5',
            'primaryLight' => $config['primary_light_color'] ?? '#818CF8',
            'accent' => $config['secondary_color'] ?? '#8B5CF6',
            'accentDark' => $config['accent_dark_color'] ?? '#7C3AED',
            'accentLight' => $config['accent_light_color'] ?? '#A78BFA',
            'gradientStart' => $config['gradient_start_color'] ?? '#6366F1',
            'gradientEnd' => $config['gradient_end_color'] ?? '#8B5CF6',
        ],
        'features' => [
            'ai_enabled' => $config['enable_ai_features'] ?? true,
            'analytics_enabled' => $config['enable_analytics'] ?? true,
            'ads_enabled' => $config['enable_ads_management'] ?? true,
        ],
        'branding' => [
            'company_name' => $config['company_name'] ?? 'ميديا برو',
            'support_email' => $config['support_email'] ?? 'support@mediapro.social',
            'support_phone' => $config['support_phone'] ?? '+966 50 123 4567',
        ],
        'general' => [
            'app_version' => $config['app_version'] ?? '1.0.0',
            'maintenance_mode' => $config['maintenance_mode'] ?? false,
            'default_language' => $config['default_language'] ?? 'ar',
            'supported_languages' => $config['supported_languages'] ?? ['ar', 'en'],
        ],
    ];

    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ]);
}
