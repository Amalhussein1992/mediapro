<?php
/**
 * Analytics Overview API Endpoint
 * Returns analytics overview data for dashboard
 *
 * Endpoint: GET /api-analytics-overview.php
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

    // Get query parameters
    $timeRange = $_GET['timeRange'] ?? '30d';
    $socialAccountId = $_GET['socialAccountId'] ?? null;

    // Calculate date range
    $daysMap = ['7d' => 7, '30d' => 30, '90d' => 90, '1y' => 365];
    $days = $daysMap[$timeRange] ?? 30;

    $startDate = date('Y-m-d H:i:s', strtotime("-$days days"));
    $endDate = date('Y-m-d H:i:s');

    // Get total posts count
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM posts
        WHERE user_id = ?
        AND created_at BETWEEN ? AND ?
    ");
    $stmt->execute([$userId, $startDate, $endDate]);
    $totalPosts = (int)$stmt->fetchColumn();

    // Mock analytics data (replace with real data from your analytics tables)
    $response = [
        'success' => true,
        'data' => [
            'overview' => [
                'totalPosts' => $totalPosts,
                'totalEngagement' => rand(1000, 10000),
                'totalReach' => rand(50000, 500000),
                'totalImpressions' => rand(100000, 1000000),
                'engagementRate' => round(rand(20, 80) / 10, 1),
                'growthRate' => round(rand(-20, 50) / 10, 1),
                'topPost' => null,
            ],
            'platformStats' => [
                [
                    'platform' => 'facebook',
                    'metrics' => [
                        'followers' => rand(1000, 50000),
                        'engagement' => round(rand(20, 80) / 10, 1),
                        'posts' => rand(5, 50),
                        'reach' => rand(10000, 100000),
                    ]
                ],
                [
                    'platform' => 'instagram',
                    'metrics' => [
                        'followers' => rand(1000, 50000),
                        'engagement' => round(rand(20, 80) / 10, 1),
                        'posts' => rand(5, 50),
                        'reach' => rand(10000, 100000),
                    ]
                ],
                [
                    'platform' => 'twitter',
                    'metrics' => [
                        'followers' => rand(1000, 50000),
                        'engagement' => round(rand(20, 80) / 10, 1),
                        'posts' => rand(5, 50),
                        'reach' => rand(10000, 100000),
                    ]
                ],
            ],
            'engagementByDay' => [
                ['date' => date('Y-m-d', strtotime('-6 days')), 'likes' => rand(50, 500), 'comments' => rand(10, 100), 'shares' => rand(5, 50)],
                ['date' => date('Y-m-d', strtotime('-5 days')), 'likes' => rand(50, 500), 'comments' => rand(10, 100), 'shares' => rand(5, 50)],
                ['date' => date('Y-m-d', strtotime('-4 days')), 'likes' => rand(50, 500), 'comments' => rand(10, 100), 'shares' => rand(5, 50)],
                ['date' => date('Y-m-d', strtotime('-3 days')), 'likes' => rand(50, 500), 'comments' => rand(10, 100), 'shares' => rand(5, 50)],
                ['date' => date('Y-m-d', strtotime('-2 days')), 'likes' => rand(50, 500), 'comments' => rand(10, 100), 'shares' => rand(5, 50)],
                ['date' => date('Y-m-d', strtotime('-1 day')), 'likes' => rand(50, 500), 'comments' => rand(10, 100), 'shares' => rand(5, 50)],
                ['date' => date('Y-m-d'), 'likes' => rand(50, 500), 'comments' => rand(10, 100), 'shares' => rand(5, 50)],
            ],
            'bestTimes' => [
                ['hour' => 9, 'day' => 'Monday', 'avgEngagement' => 4.2],
                ['hour' => 14, 'day' => 'Tuesday', 'avgEngagement' => 5.1],
                ['hour' => 18, 'day' => 'Wednesday', 'avgEngagement' => 4.8],
                ['hour' => 20, 'day' => 'Thursday', 'avgEngagement' => 4.5],
                ['hour' => 22, 'day' => 'Friday', 'avgEngagement' => 3.9],
            ],
            'topHashtags' => [
                ['tag' => 'marketing', 'uses' => 45, 'avgEngagement' => 4.2, 'avgReach' => 98000],
                ['tag' => 'socialmedia', 'uses' => 38, 'avgEngagement' => 3.8, 'avgReach' => 76000],
                ['tag' => 'business', 'uses' => 32, 'avgEngagement' => 4.5, 'avgReach' => 65000],
                ['tag' => 'digitalmarketing', 'uses' => 28, 'avgEngagement' => 3.9, 'avgReach' => 58000],
                ['tag' => 'contentcreator', 'uses' => 24, 'avgEngagement' => 4.1, 'avgReach' => 52000],
            ],
        ]
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
