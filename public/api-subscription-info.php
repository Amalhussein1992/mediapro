<?php
/**
 * Subscription Info API Endpoint
 * Returns current user's subscription information with usage stats
 *
 * Endpoint: GET /api-subscription-info.php
 * Requires: Authorization Bearer token
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
    // Get authorization header
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (empty($authHeader) || !preg_match('/Bearer\s+(.+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized. Please provide a valid token.'
        ]);
        exit;
    }

    $token = $matches[1];

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

    // Hash the token to compare with database
    $hashedToken = hash('sha256', $token);

    // Find user by token
    $stmt = $pdo->prepare("
        SELECT tokenable_id, tokenable_type
        FROM personal_access_tokens
        WHERE token = ?
        LIMIT 1
    ");
    $stmt->execute([$hashedToken]);
    $tokenData = $stmt->fetch();

    if (!$tokenData) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid or expired token'
        ]);
        exit;
    }

    $userId = $tokenData['tokenable_id'];

    // Get user's subscription plan (defaulting to free plan if none exists)
    $stmt = $pdo->prepare("
        SELECT sp.*
        FROM subscription_plans sp
        WHERE sp.slug = 'free'
        LIMIT 1
    ");
    $stmt->execute();
    $plan = $stmt->fetch();

    // If no free plan exists, create default plan data
    if (!$plan) {
        $plan = [
            'id' => 1,
            'name' => 'Free Plan',
            'slug' => 'free',
            'description' => 'Free tier with basic features',
            'price' => '0.00',
            'billing_cycle' => 'monthly',
            'max_posts_per_month' => 10,
            'max_social_accounts' => 3,
            'ai_features' => false,
            'analytics' => false,
            'priority_support' => false,
        ];
    }

    // Count user's current posts this month
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as post_count
        FROM posts
        WHERE user_id = ?
        AND MONTH(created_at) = MONTH(CURRENT_DATE())
        AND YEAR(created_at) = YEAR(CURRENT_DATE())
    ");
    $stmt->execute([$userId]);
    $postStats = $stmt->fetch();
    $currentPosts = (int)($postStats['post_count'] ?? 0);

    // Count user's connected social accounts
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as account_count
        FROM social_accounts
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
    $accountStats = $stmt->fetch();
    $currentAccounts = (int)($accountStats['account_count'] ?? 0);

    // Calculate usage stats
    $maxPosts = (int)($plan['max_posts_per_month'] ?? 10);
    $maxAccounts = (int)($plan['max_social_accounts'] ?? 3);

    $postsRemaining = max(0, $maxPosts - $currentPosts);
    $postsPercentage = $maxPosts > 0 ? min(100, ($currentPosts / $maxPosts) * 100) : 0;
    $postsLimitReached = $currentPosts >= $maxPosts;

    $accountsRemaining = max(0, $maxAccounts - $currentAccounts);
    $accountsPercentage = $maxAccounts > 0 ? min(100, ($currentAccounts / $maxAccounts) * 100) : 0;
    $accountsLimitReached = $currentAccounts >= $maxAccounts;

    // Build response
    $response = [
        'success' => true,
        'data' => [
            'plan' => [
                'id' => (int)$plan['id'],
                'name' => $plan['name'],
                'slug' => $plan['slug'],
                'description' => $plan['description'] ?? '',
                'price' => (float)$plan['price'],
                'billing_cycle' => $plan['billing_cycle'],
                'features' => [
                    'max_posts_per_month' => $maxPosts,
                    'max_social_accounts' => $maxAccounts,
                    'ai_features' => (bool)($plan['ai_features'] ?? false),
                    'analytics' => (bool)($plan['analytics'] ?? false),
                    'priority_support' => (bool)($plan['priority_support'] ?? false),
                ],
            ],
            'subscription' => [
                'status' => 'active',
                'start_date' => null,
                'end_date' => null,
            ],
            'usage' => [
                'posts' => [
                    'current' => $currentPosts,
                    'limit' => $maxPosts,
                    'remaining' => $postsRemaining,
                    'percentage' => round($postsPercentage, 1),
                    'is_limit_reached' => $postsLimitReached,
                ],
                'social_accounts' => [
                    'current' => $currentAccounts,
                    'limit' => $maxAccounts,
                    'remaining' => $accountsRemaining,
                    'percentage' => round($accountsPercentage, 1),
                    'is_limit_reached' => $accountsLimitReached,
                ],
            ],
        ],
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
