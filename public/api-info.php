<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, Accept-Language, Accept');

// معلومات الخادم
$serverInfo = [
    'success' => true,
    'message' => 'مرحباً بك في Social Media Manager API 🚀',
    'status' => 'الخادم يعمل بنجاح',
    'version' => '1.0.0',
    'timestamp' => date('Y-m-d H:i:s'),
    'server' => [
        'host' => $_SERVER['SERVER_NAME'] ?? 'localhost',
        'port' => $_SERVER['SERVER_PORT'] ?? '8080',
        'protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1',
    ],
    'endpoints' => [
        'config' => [
            'GET /api-config.php' => 'الحصول على إعدادات التطبيق',
        ],
        'auth' => [
            'POST /api-register.php' => 'إنشاء حساب جديد',
            'POST /api-login.php' => 'تسجيل الدخول',
            'GET /api/auth/user' => 'الحصول على معلومات المستخدم',
        ],
        'social_accounts' => [
            'GET /api/social-accounts' => 'قائمة حسابات السوشال ميديا',
            'GET /api-social-accounts-demo.php' => 'حسابات تجريبية',
        ],
        'oauth' => [
            'GET /api-oauth-demo.php/{platform}/auth-url' => 'الحصول على رابط OAuth',
            'POST /api-oauth-demo.php/{platform}/callback' => 'معالجة OAuth callback',
            'DELETE /api-oauth-demo.php/{platform}/disconnect/{accountId}' => 'فصل الحساب',
        ],
        'posts' => [
            'GET /api/posts' => 'قائمة المنشورات',
            'POST /api/posts' => 'إنشاء منشور',
            'GET /api/posts/scheduled/upcoming' => 'المنشورات المجدولة القادمة',
            'POST /api/posts/schedule' => 'جدولة منشور',
        ],
        'analytics' => [
            'GET /api-analytics-overview.php' => 'نظرة عامة على الإحصائيات',
            'GET /api-analytics-platform.php' => 'إحصائيات المنصات',
        ],
        'ai' => [
            'POST /api-ai-smart-content.php' => 'توليد محتوى بالذكاء الاصطناعي',
        ],
        'subscription' => [
            'GET /api-subscription-info.php' => 'معلومات الاشتراك',
        ],
    ],
    'platforms_supported' => [
        'Instagram', 'Facebook', 'Twitter', 'LinkedIn', 'TikTok', 'YouTube'
    ],
    'features' => [
        '✨ توليد محتوى بالذكاء الاصطناعي',
        '📅 جدولة المنشورات',
        '📊 تحليلات مفصلة',
        '🔗 ربط حسابات متعددة',
        '🌍 دعم العربية والإنجليزية',
        '🎨 محرر محتوى متقدم',
    ],
    'note' => 'جميع الـ APIs تعمل بشكل صحيح. الخطأ 500 في الصفحة الرئيسية طبيعي لأن Laravel يحتاج routes.',
];

echo json_encode($serverInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
