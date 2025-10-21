<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

// Parse the request
$path = parse_url($requestUri, PHP_URL_PATH);
$path = str_replace('/api-oauth-demo.php', '', $path);

// Helper function to send JSON response
function sendJson($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

// OAuth Configuration (Demo)
$oauthConfig = [
    'instagram' => [
        'client_id' => 'demo_instagram_client_id',
        'client_secret' => 'demo_instagram_client_secret',
        'redirect_uri' => 'http://192.168.1.15:8080/oauth/callback/instagram',
        'auth_url' => 'https://api.instagram.com/oauth/authorize',
        'token_url' => 'https://api.instagram.com/oauth/access_token',
        'scopes' => ['user_profile', 'user_media'],
    ],
    'facebook' => [
        'client_id' => 'demo_facebook_app_id',
        'client_secret' => 'demo_facebook_app_secret',
        'redirect_uri' => 'http://192.168.1.15:8080/oauth/callback/facebook',
        'auth_url' => 'https://www.facebook.com/v18.0/dialog/oauth',
        'token_url' => 'https://graph.facebook.com/v18.0/oauth/access_token',
        'scopes' => ['pages_manage_posts', 'pages_read_engagement', 'pages_show_list'],
    ],
    'twitter' => [
        'client_id' => 'demo_twitter_client_id',
        'client_secret' => 'demo_twitter_client_secret',
        'redirect_uri' => 'http://192.168.1.15:8080/oauth/callback/twitter',
        'auth_url' => 'https://twitter.com/i/oauth2/authorize',
        'token_url' => 'https://api.twitter.com/2/oauth2/token',
        'scopes' => ['tweet.read', 'tweet.write', 'users.read'],
    ],
    'linkedin' => [
        'client_id' => 'demo_linkedin_client_id',
        'client_secret' => 'demo_linkedin_client_secret',
        'redirect_uri' => 'http://192.168.1.15:8080/oauth/callback/linkedin',
        'auth_url' => 'https://www.linkedin.com/oauth/v2/authorization',
        'token_url' => 'https://www.linkedin.com/oauth/v2/accessToken',
        'scopes' => ['w_member_social', 'r_liteprofile', 'r_organization_social'],
    ],
    'tiktok' => [
        'client_id' => 'demo_tiktok_client_key',
        'client_secret' => 'demo_tiktok_client_secret',
        'redirect_uri' => 'http://192.168.1.15:8080/oauth/callback/tiktok',
        'auth_url' => 'https://www.tiktok.com/auth/authorize/',
        'token_url' => 'https://open-api.tiktok.com/oauth/access_token/',
        'scopes' => ['user.info.basic', 'video.publish', 'video.list'],
    ],
    'youtube' => [
        'client_id' => 'demo_youtube_client_id',
        'client_secret' => 'demo_youtube_client_secret',
        'redirect_uri' => 'http://192.168.1.15:8080/oauth/callback/youtube',
        'auth_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
        'token_url' => 'https://oauth2.googleapis.com/token',
        'scopes' => ['https://www.googleapis.com/auth/youtube.upload', 'https://www.googleapis.com/auth/youtube.readonly'],
    ],
];

// Router
if ($method === 'GET' && preg_match('#^/([a-z]+)/auth-url$#', $path, $matches)) {
    // GET /instagram/auth-url
    // GET /facebook/auth-url
    // etc.

    $platform = $matches[1];

    if (!isset($oauthConfig[$platform])) {
        sendJson([
            'success' => false,
            'message' => "النظام الأساسي غير مدعوم: {$platform}",
            'message_en' => "Platform not supported: {$platform}"
        ], 400);
    }

    $config = $oauthConfig[$platform];
    $state = bin2hex(random_bytes(16)); // Generate random state for CSRF protection

    // Build authorization URL
    $params = [
        'client_id' => $config['client_id'],
        'redirect_uri' => $config['redirect_uri'],
        'response_type' => 'code',
        'scope' => implode(' ', $config['scopes']),
        'state' => $state,
    ];

    // Platform-specific adjustments
    if ($platform === 'instagram' || $platform === 'facebook') {
        $params['scope'] = implode(',', $config['scopes']);
    }

    $authUrl = $config['auth_url'] . '?' . http_build_query($params);

    sendJson([
        'success' => true,
        'data' => [
            'authUrl' => $authUrl,
            'state' => $state,
            'platform' => $platform,
        ],
        'message' => 'تم إنشاء رابط المصادقة بنجاح',
        'message_en' => 'Authorization URL generated successfully'
    ]);

} elseif ($method === 'POST' && preg_match('#^/([a-z]+)/callback$#', $path, $matches)) {
    // POST /instagram/callback
    // POST /facebook/callback
    // etc.

    $platform = $matches[1];
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($oauthConfig[$platform])) {
        sendJson([
            'success' => false,
            'message' => "النظام الأساسي غير مدعوم: {$platform}",
            'message_en' => "Platform not supported: {$platform}"
        ], 400);
    }

    $code = $input['code'] ?? null;
    $state = $input['state'] ?? null;

    if (!$code) {
        sendJson([
            'success' => false,
            'message' => 'رمز التفويض مفقود',
            'message_en' => 'Authorization code is missing'
        ], 400);
    }

    // In production, exchange code for access token
    // For demo, we'll simulate successful connection

    $demoAccounts = [
        'instagram' => [
            'id' => 'ig_demo_' . time(),
            'username' => 'mediapro_saudi',
            'display_name' => 'ميديا برو السعودية',
            'followers_count' => 45230,
            'profile_picture' => 'https://ui-avatars.com/api/?name=ميديا+برو&background=E1306C&color=fff&size=200',
        ],
        'facebook' => [
            'id' => 'fb_demo_' . time(),
            'username' => 'mediapro.sa',
            'display_name' => 'ميديا برو - السعودية',
            'followers_count' => 125430,
            'profile_picture' => 'https://ui-avatars.com/api/?name=ميديا+برو&background=1877F2&color=fff&size=200',
        ],
        'twitter' => [
            'id' => 'tw_demo_' . time(),
            'username' => '@mediapro_sa',
            'display_name' => 'ميديا برو 🇸🇦',
            'followers_count' => 89340,
            'profile_picture' => 'https://ui-avatars.com/api/?name=ميديا+برو&background=1DA1F2&color=fff&size=200',
        ],
        'linkedin' => [
            'id' => 'li_demo_' . time(),
            'username' => 'mediapro-saudi',
            'display_name' => 'ميديا برو للتسويق الرقمي',
            'followers_count' => 34520,
            'profile_picture' => 'https://ui-avatars.com/api/?name=ميديا+برو&background=0A66C2&color=fff&size=200',
        ],
        'tiktok' => [
            'id' => 'tt_demo_' . time(),
            'username' => '@mediapro.sa',
            'display_name' => 'ميديا برو ✨',
            'followers_count' => 156870,
            'profile_picture' => 'https://ui-avatars.com/api/?name=ميديا+برو&background=000000&color=fff&size=200',
        ],
        'youtube' => [
            'id' => 'yt_demo_' . time(),
            'username' => 'MediaProSaudi',
            'display_name' => 'ميديا برو - قناة التسويق الرقمي',
            'followers_count' => 67890,
            'profile_picture' => 'https://ui-avatars.com/api/?name=ميديا+برو&background=FF0000&color=fff&size=200',
        ],
    ];

    $accountData = $demoAccounts[$platform] ?? null;

    if (!$accountData) {
        sendJson([
            'success' => false,
            'message' => 'فشل في الحصول على بيانات الحساب',
            'message_en' => 'Failed to fetch account data'
        ], 500);
    }

    sendJson([
        'success' => true,
        'data' => [
            'account' => [
                'id' => $accountData['id'],
                'platform' => $platform,
                'username' => $accountData['username'],
                'display_name' => $accountData['display_name'],
                'followers_count' => $accountData['followers_count'],
                'profile_picture' => $accountData['profile_picture'],
                'is_connected' => true,
                'status' => 'active',
                'connected_at' => date('Y-m-d H:i:s'),
                'engagement_rate' => rand(35, 78) / 10, // 3.5 - 7.8%
                'posts_count' => rand(150, 500),
            ],
            'access_token' => 'demo_access_token_' . bin2hex(random_bytes(16)),
            'refresh_token' => 'demo_refresh_token_' . bin2hex(random_bytes(16)),
            'expires_in' => 3600 * 24 * 60, // 60 days
        ],
        'message' => "تم ربط حساب {$platform} بنجاح! 🎉",
        'message_en' => "Successfully connected {$platform} account! 🎉"
    ]);

} elseif ($method === 'DELETE' && preg_match('#^/([a-z]+)/disconnect/([a-zA-Z0-9_]+)$#', $path, $matches)) {
    // DELETE /instagram/disconnect/ig_demo_12345

    $platform = $matches[1];
    $accountId = $matches[2];

    sendJson([
        'success' => true,
        'message' => "تم فصل حساب {$platform} بنجاح",
        'message_en' => "Successfully disconnected {$platform} account"
    ]);

} elseif ($method === 'POST' && $path === '/refresh-token') {
    // POST /refresh-token

    $input = json_decode(file_get_contents('php://input'), true);
    $refreshToken = $input['refresh_token'] ?? null;

    if (!$refreshToken) {
        sendJson([
            'success' => false,
            'message' => 'رمز التحديث مفقود',
            'message_en' => 'Refresh token is missing'
        ], 400);
    }

    sendJson([
        'success' => true,
        'data' => [
            'access_token' => 'demo_new_access_token_' . bin2hex(random_bytes(16)),
            'refresh_token' => 'demo_new_refresh_token_' . bin2hex(random_bytes(16)),
            'expires_in' => 3600 * 24 * 60,
        ],
        'message' => 'تم تحديث رمز الوصول بنجاح',
        'message_en' => 'Access token refreshed successfully'
    ]);

} else {
    sendJson([
        'success' => false,
        'message' => 'المسار غير موجود',
        'message_en' => 'Endpoint not found',
        'available_endpoints' => [
            'GET /{platform}/auth-url' => 'الحصول على رابط المصادقة',
            'POST /{platform}/callback' => 'معالجة callback من OAuth',
            'DELETE /{platform}/disconnect/{accountId}' => 'فصل الحساب',
            'POST /refresh-token' => 'تحديث رمز الوصول',
        ],
        'supported_platforms' => array_keys($oauthConfig),
    ], 404);
}
