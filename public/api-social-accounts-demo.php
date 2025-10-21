<?php
/**
 * Demo Social Accounts API Endpoint
 * Returns realistic Arabic social media accounts data
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

// Sample realistic social media accounts in Arabic
$accounts = [
    [
        'id' => '1',
        'platform' => 'instagram',
        'username' => 'mediapro_saudi',
        'display_name' => 'ميديا برو السعودية',
        'followers_count' => 45230,
        'following_count' => 892,
        'posts_count' => 1547,
        'profile_image' => 'https://ui-avatars.com/api/?name=ميديا+برو&background=E4405F&color=fff&size=200',
        'is_connected' => true,
        'connected_at' => '2025-01-15T10:30:00Z',
        'last_sync' => '2025-10-21T09:00:00Z',
        'engagement_rate' => 4.8,
        'status' => 'active',
        'bio' => 'نساعدك في إدارة حساباتك على وسائل التواصل الاجتماعي بطريقة احترافية 📱✨',
    ],
    [
        'id' => '2',
        'platform' => 'twitter',
        'username' => 'MediaProKSA',
        'display_name' => 'ميديا برو | إدارة المحتوى',
        'followers_count' => 28450,
        'following_count' => 456,
        'posts_count' => 3241,
        'profile_image' => 'https://ui-avatars.com/api/?name=Twitter&background=1DA1F2&color=fff&size=200',
        'is_connected' => true,
        'connected_at' => '2025-01-10T14:20:00Z',
        'last_sync' => '2025-10-21T08:45:00Z',
        'engagement_rate' => 3.2,
        'status' => 'active',
        'bio' => 'حلول ذكية لإدارة وسائل التواصل | المحتوى الإبداعي هو مفتاح النجاح 🚀',
    ],
    [
        'id' => '3',
        'platform' => 'facebook',
        'username' => 'MediaProSocial',
        'display_name' => 'ميديا برو - إدارة احترافية',
        'followers_count' => 67890,
        'following_count' => 0,
        'posts_count' => 2156,
        'profile_image' => 'https://ui-avatars.com/api/?name=Facebook&background=4267B2&color=fff&size=200',
        'is_connected' => true,
        'connected_at' => '2025-01-05T16:00:00Z',
        'last_sync' => '2025-10-21T09:15:00Z',
        'engagement_rate' => 5.6,
        'status' => 'active',
        'bio' => 'منصة متكاملة لإدارة المحتوى الرقمي وتحليل الأداء 📊',
    ],
    [
        'id' => '4',
        'platform' => 'linkedin',
        'username' => 'mediapro-business',
        'display_name' => 'Media Pro Business Solutions',
        'followers_count' => 12340,
        'following_count' => 234,
        'posts_count' => 567,
        'profile_image' => 'https://ui-avatars.com/api/?name=LinkedIn&background=0077B5&color=fff&size=200',
        'is_connected' => true,
        'connected_at' => '2025-02-01T11:00:00Z',
        'last_sync' => '2025-10-21T08:30:00Z',
        'engagement_rate' => 6.2,
        'status' => 'active',
        'bio' => 'حلول تسويقية احترافية للأعمال | نساعدك في بناء حضور قوي على LinkedIn',
    ],
    [
        'id' => '5',
        'platform' => 'tiktok',
        'username' => 'mediapro.tips',
        'display_name' => 'ميديا برو | نصائح تسويقية',
        'followers_count' => 156780,
        'following_count' => 89,
        'posts_count' => 423,
        'profile_image' => 'https://ui-avatars.com/api/?name=TikTok&background=000000&color=fff&size=200',
        'is_connected' => false,
        'connected_at' => null,
        'last_sync' => null,
        'engagement_rate' => 8.9,
        'status' => 'disconnected',
        'bio' => 'نصائح يومية لتطوير المحتوى 🎬 | تابعنا للمزيد من الأفكار الإبداعية',
    ],
];

// Calculate total stats
$totalFollowers = array_sum(array_column($accounts, 'followers_count'));
$totalPosts = array_sum(array_column($accounts, 'posts_count'));
$connectedAccounts = count(array_filter($accounts, fn($a) => $a['is_connected']));
$avgEngagement = round(array_sum(array_column($accounts, 'engagement_rate')) / count($accounts), 2);

$response = [
    'success' => true,
    'data' => $accounts,
    'summary' => [
        'total_accounts' => count($accounts),
        'connected_accounts' => $connectedAccounts,
        'total_followers' => $totalFollowers,
        'total_posts' => $totalPosts,
        'average_engagement' => $avgEngagement,
    ],
    'timestamp' => date('c'),
];

http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
