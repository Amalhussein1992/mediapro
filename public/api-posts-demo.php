<?php
/**
 * Demo Posts API Endpoint
 * Returns realistic Arabic social media posts data
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

// Sample realistic posts in Arabic
$posts = [
    [
        'id' => '1',
        'content' => '🎉 أطلقنا اليوم ميزة جديدة في ميديا برو! الآن يمكنك جدولة منشوراتك على جميع منصات التواصل الاجتماعي من مكان واحد. وفّر وقتك وزد من إنتاجيتك!

#ميديا_برو #تسويق_رقمي #إدارة_محتوى',
        'platforms' => ['instagram', 'twitter', 'facebook'],
        'status' => 'published',
        'scheduled_at' => '2025-10-21T10:00:00Z',
        'published_at' => '2025-10-21T10:00:00Z',
        'media' => [
            [
                'type' => 'image',
                'url' => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=800',
                'thumbnail' => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=200',
            ]
        ],
        'analytics' => [
            'views' => 4523,
            'likes' => 892,
            'comments' => 45,
            'shares' => 127,
            'engagement_rate' => 5.2,
        ],
        'created_at' => '2025-10-20T15:30:00Z',
    ],
    [
        'id' => '2',
        'content' => '💡 نصيحة اليوم: أفضل وقت للنشر على إنستغرام هو بين الساعة 11 صباحاً و 2 ظهراً.

استخدم أدوات التحليل في ميديا برو لمعرفة الأوقات المثالية لجمهورك الخاص!

#نصائح_تسويقية #سوشيال_ميديا #محتوى_رقمي',
        'platforms' => ['instagram'],
        'status' => 'published',
        'scheduled_at' => '2025-10-20T11:30:00Z',
        'published_at' => '2025-10-20T11:30:00Z',
        'media' => [
            [
                'type' => 'image',
                'url' => 'https://images.unsplash.com/photo-1432888622747-4eb9a8f2c293?w=800',
                'thumbnail' => 'https://images.unsplash.com/photo-1432888622747-4eb9a8f2c293?w=200',
            ]
        ],
        'analytics' => [
            'views' => 6234,
            'likes' => 1245,
            'comments' => 78,
            'shares' => 89,
            'engagement_rate' => 6.8,
        ],
        'created_at' => '2025-10-19T14:20:00Z',
    ],
    [
        'id' => '3',
        'content' => '📊 هل تعلم؟

• 75% من المسوقين يستخدمون أدوات جدولة المنشورات
• زيادة 45% في التفاعل عند النشر في الأوقات المثالية
• توفير 3 ساعات يومياً باستخدام الأتمتة

ابدأ رحلتك مع ميديا برو اليوم! 🚀

#إحصائيات #تسويق_ذكي #نجاح',
        'platforms' => ['twitter', 'linkedin'],
        'status' => 'published',
        'scheduled_at' => '2025-10-19T14:00:00Z',
        'published_at' => '2025-10-19T14:00:00Z',
        'media' => [
            [
                'type' => 'image',
                'url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800',
                'thumbnail' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=200',
            ]
        ],
        'analytics' => [
            'views' => 3456,
            'likes' => 567,
            'comments' => 34,
            'shares' => 156,
            'engagement_rate' => 4.5,
        ],
        'created_at' => '2025-10-18T10:00:00Z',
    ],
    [
        'id' => '4',
        'content' => '✨ محتوى جديد كل يوم!

سلسلة "نصائح التسويق الرقمي" - الحلقة 5:
كيف تكتب وصف جذاب لملفك الشخصي؟

1️⃣ اختصر رسالتك في سطر واحد
2️⃣ استخدم الإيموجي بذكاء
3️⃣ أضف دعوة للتفاعل (CTA)
4️⃣ حدّث معلوماتك بانتظام

#تسويق_المحتوى #سوشيال_ميديا',
        'platforms' => ['instagram', 'facebook'],
        'status' => 'scheduled',
        'scheduled_at' => '2025-10-22T16:00:00Z',
        'published_at' => null,
        'media' => [
            [
                'type' => 'image',
                'url' => 'https://images.unsplash.com/photo-1553877522-43269d4ea984?w=800',
                'thumbnail' => 'https://images.unsplash.com/photo-1553877522-43269d4ea984?w=200',
            ]
        ],
        'analytics' => null,
        'created_at' => '2025-10-21T09:15:00Z',
    ],
    [
        'id' => '5',
        'content' => '🎯 استراتيجية المحتوى الناجح:

الأسبوع القادم سننشر:
• الاثنين: نصائح تسويقية
• الأربعاء: قصص نجاح عملائنا
• الجمعة: عروض خاصة

تابعونا لتستفيدوا من جميع الميزات الجديدة!

#خطة_محتوى #تنظيم #نجاح_رقمي',
        'platforms' => ['twitter', 'linkedin', 'facebook'],
        'status' => 'draft',
        'scheduled_at' => null,
        'published_at' => null,
        'media' => [],
        'analytics' => null,
        'created_at' => '2025-10-21T08:30:00Z',
    ],
    [
        'id' => '6',
        'content' => '🌟 شكراً لكم!

وصلنا إلى 100,000 متابع على جميع منصاتنا!

هذا الإنجاز بفضل ثقتكم ودعمكم المستمر. نعدكم بتقديم المزيد من القيمة والمحتوى المفيد.

معاً نحو النجاح! 💪

#شكر_وتقدير #مجتمع_ميديا_برو #100k',
        'platforms' => ['instagram', 'twitter'],
        'status' => 'published',
        'scheduled_at' => '2025-10-18T18:00:00Z',
        'published_at' => '2025-10-18T18:00:00Z',
        'media' => [
            [
                'type' => 'image',
                'url' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800',
                'thumbnail' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=200',
            ]
        ],
        'analytics' => [
            'views' => 12456,
            'likes' => 3421,
            'comments' => 234,
            'shares' => 567,
            'engagement_rate' => 8.9,
        ],
        'created_at' => '2025-10-17T12:00:00Z',
    ],
];

// Filter by status if provided
$status = $_GET['status'] ?? null;
if ($status) {
    $posts = array_filter($posts, fn($p) => $p['status'] === $status);
    $posts = array_values($posts); // Re-index array
}

// Get upcoming scheduled posts
if (isset($_GET['upcoming']) && $_GET['upcoming'] === 'true') {
    $posts = array_filter($posts, function($p) {
        return $p['status'] === 'scheduled' &&
               $p['scheduled_at'] &&
               strtotime($p['scheduled_at']) > time();
    });
    $posts = array_values($posts);

    // Sort by scheduled date
    usort($posts, function($a, $b) {
        return strtotime($a['scheduled_at']) - strtotime($b['scheduled_at']);
    });
}

// Calculate stats
$publishedPosts = array_filter($posts, fn($p) => $p['status'] === 'published');
$scheduledPosts = array_filter($posts, fn($p) => $p['status'] === 'scheduled');
$draftPosts = array_filter($posts, fn($p) => $p['status'] === 'draft');

$response = [
    'success' => true,
    'data' => array_values($posts),
    'summary' => [
        'total' => count($posts),
        'published' => count($publishedPosts),
        'scheduled' => count($scheduledPosts),
        'drafts' => count($draftPosts),
    ],
    'timestamp' => date('c'),
];

http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
