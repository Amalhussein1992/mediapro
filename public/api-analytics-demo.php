<?php
/**
 * Demo Analytics API Endpoint
 * Returns realistic Arabic analytics data
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$timeRange = $_GET['timeRange'] ?? '30d';
$socialAccountId = $_GET['socialAccountId'] ?? null;

// Generate realistic analytics data
$generateChartData = function($days, $baseValue, $variance) {
    $data = [];
    $labels = [];
    $now = time();

    for ($i = $days - 1; $i >= 0; $i--) {
        $date = date('Y-m-d', $now - ($i * 86400));
        $dayName = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'][date('w', strtotime($date))];

        $labels[] = $i > 7 ? date('d/m', strtotime($date)) : $dayName;
        $data[] = $baseValue + rand(-$variance, $variance);
    }

    return ['labels' => $labels, 'data' => $data];
};

// Days mapping
$daysMap = [
    '7d' => 7,
    '30d' => 30,
    '90d' => 90,
];
$days = $daysMap[$timeRange] ?? 30;

// Overview stats
$overview = [
    'total_followers' => 310894,
    'followers_growth' => 12.5,
    'total_posts' => 7931,
    'posts_this_period' => 145,
    'total_engagement' => 45230,
    'engagement_growth' => 8.3,
    'avg_engagement_rate' => 5.8,
    'reach' => 567890,
    'reach_growth' => 15.2,
];

// Engagement chart
$engagementChart = $generateChartData($days, 450, 150);

// Followers growth chart
$followersChart = $generateChartData($days, 120, 40);

// Top performing posts
$topPosts = [
    [
        'id' => '6',
        'content' => '🌟 شكراً لكم! وصلنا إلى 100,000 متابع...',
        'platform' => 'instagram',
        'engagement' => 4222,
        'engagement_rate' => 8.9,
        'likes' => 3421,
        'comments' => 234,
        'shares' => 567,
        'thumbnail' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=200',
    ],
    [
        'id' => '2',
        'content' => '💡 نصيحة اليوم: أفضل وقت للنشر...',
        'platform' => 'instagram',
        'engagement' => 1412,
        'engagement_rate' => 6.8,
        'likes' => 1245,
        'comments' => 78,
        'shares' => 89,
        'thumbnail' => 'https://images.unsplash.com/photo-1432888622747-4eb9a8f2c293?w=200',
    ],
    [
        'id' => '1',
        'content' => '🎉 أطلقنا اليوم ميزة جديدة...',
        'platform' => 'facebook',
        'engagement' => 1064,
        'engagement_rate' => 5.2,
        'likes' => 892,
        'comments' => 45,
        'shares' => 127,
        'thumbnail' => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=200',
    ],
];

// Platform breakdown
$platformStats = [
    [
        'platform' => 'instagram',
        'platform_ar' => 'إنستغرام',
        'followers' => 45230,
        'engagement' => 23456,
        'engagement_rate' => 4.8,
        'growth' => 15.3,
        'color' => '#E4405F',
    ],
    [
        'platform' => 'facebook',
        'platform_ar' => 'فيسبوك',
        'followers' => 67890,
        'engagement' => 34521,
        'engagement_rate' => 5.6,
        'growth' => 10.2,
        'color' => '#4267B2',
    ],
    [
        'platform' => 'twitter',
        'platform_ar' => 'تويتر',
        'followers' => 28450,
        'engagement' => 12345,
        'engagement_rate' => 3.2,
        'growth' => 8.7,
        'color' => '#1DA1F2',
    ],
    [
        'platform' => 'linkedin',
        'platform_ar' => 'لينكد إن',
        'followers' => 12340,
        'engagement' => 8901,
        'engagement_rate' => 6.2,
        'growth' => 18.5,
        'color' => '#0077B5',
    ],
    [
        'platform' => 'tiktok',
        'platform_ar' => 'تيك توك',
        'followers' => 156780,
        'engagement' => 89012,
        'engagement_rate' => 8.9,
        'growth' => 25.4,
        'color' => '#000000',
    ],
];

// Audience demographics
$audienceDemographics = [
    'gender' => [
        ['label' => 'ذكور', 'value' => 45, 'color' => '#6366F1'],
        ['label' => 'إناث', 'value' => 52, 'color' => '#EC4899'],
        ['label' => 'غير محدد', 'value' => 3, 'color' => '#94A3B8'],
    ],
    'age_groups' => [
        ['label' => '13-17', 'value' => 8],
        ['label' => '18-24', 'value' => 28],
        ['label' => '25-34', 'value' => 35],
        ['label' => '35-44', 'value' => 18],
        ['label' => '45-54', 'value' => 8],
        ['label' => '55+', 'value' => 3],
    ],
    'top_cities' => [
        ['city' => 'الرياض', 'percentage' => 32],
        ['city' => 'جدة', 'percentage' => 24],
        ['city' => 'الدمام', 'percentage' => 15],
        ['city' => 'مكة المكرمة', 'percentage' => 12],
        ['city' => 'المدينة المنورة', 'percentage' => 9],
        ['city' => 'أخرى', 'percentage' => 8],
    ],
];

// Best posting times
$bestPostingTimes = [
    'weekdays' => [
        ['day' => 'الأحد', 'best_time' => '11:00 - 14:00', 'engagement' => 8.5],
        ['day' => 'الاثنين', 'best_time' => '10:00 - 13:00', 'engagement' => 7.8],
        ['day' => 'الثلاثاء', 'best_time' => '11:00 - 14:00', 'engagement' => 8.2],
        ['day' => 'الأربعاء', 'best_time' => '12:00 - 15:00', 'engagement' => 8.9],
        ['day' => 'الخميس', 'best_time' => '10:00 - 13:00', 'engagement' => 9.2],
        ['day' => 'الجمعة', 'best_time' => '14:00 - 18:00', 'engagement' => 6.5],
        ['day' => 'السبت', 'best_time' => '15:00 - 19:00', 'engagement' => 7.1],
    ],
    'overall_best' => [
        'day' => 'الخميس',
        'time' => '11:00 صباحاً',
        'engagement_boost' => '+45%',
    ],
];

// Content performance
$contentTypes = [
    ['type' => 'صور', 'type_en' => 'images', 'count' => 234, 'avg_engagement' => 6.8, 'color' => '#8B5CF6'],
    ['type' => 'فيديو', 'type_en' => 'videos', 'count' => 89, 'avg_engagement' => 9.2, 'color' => '#EC4899'],
    ['type' => 'نص فقط', 'type_en' => 'text', 'count' => 156, 'avg_engagement' => 4.5, 'color' => '#6366F1'],
    ['type' => 'كاروسيل', 'type_en' => 'carousel', 'count' => 67, 'avg_engagement' => 7.3, 'color' => '#F59E0B'],
];

$response = [
    'success' => true,
    'time_range' => $timeRange,
    'overview' => $overview,
    'charts' => [
        'engagement' => $engagementChart,
        'followers_growth' => $followersChart,
    ],
    'top_posts' => $topPosts,
    'platform_stats' => $platformStats,
    'audience_demographics' => $audienceDemographics,
    'best_posting_times' => $bestPostingTimes,
    'content_performance' => $contentTypes,
    'insights' => [
        [
            'icon' => '📈',
            'title' => 'نمو ممتاز',
            'description' => 'زيادة 15.2% في الوصول خلال الشهر الماضي',
            'type' => 'positive',
        ],
        [
            'icon' => '⏰',
            'title' => 'وقت النشر المثالي',
            'description' => 'أفضل أوقاتك للنشر: الخميس 11 صباحاً',
            'type' => 'info',
        ],
        [
            'icon' => '🎥',
            'title' => 'المحتوى المرئي',
            'description' => 'الفيديوهات تحقق تفاعل أعلى بنسبة 35%',
            'type' => 'positive',
        ],
        [
            'icon' => '💡',
            'title' => 'فرصة للتحسين',
            'description' => 'نشاطك على تويتر أقل من المتوسط',
            'type' => 'warning',
        ],
    ],
    'timestamp' => date('c'),
];

http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
