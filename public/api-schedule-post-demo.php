<?php
/**
 * Demo Schedule Post API Endpoint
 * Returns success response for scheduling posts
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate required fields
$requiredFields = ['content', 'platforms', 'scheduledAt'];
$missingFields = [];

foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        $missingFields[] = $field;
    }
}

if (!empty($missingFields)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'الحقول المطلوبة مفقودة: ' . implode(', ', $missingFields),
        'missing_fields' => $missingFields
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Generate a random post ID
$postId = 'post_' . uniqid();

// Create scheduled post response
$scheduledPost = [
    'id' => $postId,
    'content' => $data['content'],
    'platforms' => $data['platforms'],
    'status' => 'scheduled',
    'scheduled_at' => $data['scheduledAt'],
    'published_at' => null,
    'media' => $data['media'] ?? [],
    'analytics' => null,
    'created_at' => date('c'),
    'user_id' => 'demo-user-1',
];

$response = [
    'success' => true,
    'message' => 'تم جدولة المنشور بنجاح! سيتم نشره في الوقت المحدد.',
    'data' => $scheduledPost,
    'scheduled_for' => $data['scheduledAt'],
    'platforms_count' => count($data['platforms']),
    'timestamp' => date('c'),
];

http_response_code(201); // Created
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
