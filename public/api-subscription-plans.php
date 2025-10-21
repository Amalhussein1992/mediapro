<?php
/**
 * Subscription Plans API Endpoint
 * Returns all available subscription plans
 *
 * Endpoint: GET /api-subscription-plans.php
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

    // Get all active subscription plans
    $stmt = $pdo->prepare("
        SELECT *
        FROM subscription_plans
        WHERE is_active = 1
        ORDER BY price ASC
    ");
    $stmt->execute();
    $plans = $stmt->fetchAll();

    // If no plans exist in database, return default plans
    if (empty($plans)) {
        $plans = [
            [
                'id' => 1,
                'name' => 'Free Plan',
                'slug' => 'free',
                'description' => 'Perfect for getting started',
                'price' => '0.00',
                'billing_cycle' => 'monthly',
                'max_posts_per_month' => 10,
                'max_social_accounts' => 3,
                'ai_features' => false,
                'analytics' => false,
                'priority_support' => false,
            ],
            [
                'id' => 2,
                'name' => 'Pro Plan',
                'slug' => 'pro',
                'description' => 'For growing businesses',
                'price' => '29.00',
                'billing_cycle' => 'monthly',
                'max_posts_per_month' => 100,
                'max_social_accounts' => 10,
                'ai_features' => true,
                'analytics' => true,
                'priority_support' => false,
            ],
            [
                'id' => 3,
                'name' => 'Business Plan',
                'slug' => 'business',
                'description' => 'For established teams',
                'price' => '99.00',
                'billing_cycle' => 'monthly',
                'max_posts_per_month' => -1, // Unlimited
                'max_social_accounts' => 50,
                'ai_features' => true,
                'analytics' => true,
                'priority_support' => true,
            ],
        ];
    }

    // Format plans for response
    $formattedPlans = [];
    foreach ($plans as $plan) {
        $formattedPlans[] = [
            'id' => (int)$plan['id'],
            'name' => $plan['name'],
            'slug' => $plan['slug'],
            'description' => $plan['description'] ?? '',
            'price' => (float)$plan['price'],
            'billing_cycle' => $plan['billing_cycle'],
            'features' => [
                'max_posts_per_month' => (int)($plan['max_posts_per_month'] ?? 10),
                'max_social_accounts' => (int)($plan['max_social_accounts'] ?? 3),
                'ai_features' => (bool)($plan['ai_features'] ?? false),
                'analytics' => (bool)($plan['analytics'] ?? false),
                'priority_support' => (bool)($plan['priority_support'] ?? false),
            ],
        ];
    }

    $response = [
        'success' => true,
        'data' => $formattedPlans,
        'count' => count($formattedPlans),
    ];

    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server Error',
        'error' => $e->getMessage()
    ]);
}
