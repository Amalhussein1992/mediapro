<?php
/**
 * Smart AI Content Generation Using Real AI APIs
 * Supports Claude, Gemini, and OpenAI
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Database connection
try {
    $db = new PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'] . ";charset=utf8mb4",
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed'], JSON_UNESCAPED_UNICODE);
    exit();
}

// Get authorization token
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
$token = str_replace('Bearer ', '', $authHeader);

if (empty($token)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required'], JSON_UNESCAPED_UNICODE);
    exit();
}

// Verify token
try {
    $hashedToken = hash('sha256', $token);
    $stmt = $db->prepare("
        SELECT u.* FROM users u
        JOIN personal_access_tokens pat ON u.id = pat.tokenable_id
        WHERE pat.token = :token AND pat.tokenable_type = 'App\\\\Models\\\\User'
        LIMIT 1
    ");
    $stmt->execute(['token' => $hashedToken]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid token'], JSON_UNESCAPED_UNICODE);
        exit();
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Authentication error'], JSON_UNESCAPED_UNICODE);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $_GET['action'] ?? 'generate-post';

/**
 * Call Claude API (Anthropic)
 */
function callClaudeAPI($prompt) {
    $apiKey = $_ENV['CLAUDE_API_KEY'] ?? '';
    if (empty($apiKey)) return null;

    $url = "https://api.anthropic.com/v1/messages";

    $data = [
        'model' => $_ENV['CLAUDE_MODEL'] ?? 'claude-3-5-sonnet-20241022',
        'max_tokens' => intval($_ENV['CLAUDE_MAX_TOKENS'] ?? 4096),
        'messages' => [
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'x-api-key: ' . $apiKey,
        'anthropic-version: 2023-06-01'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $result = json_decode($response, true);
        if (isset($result['content'][0]['text'])) {
            return $result['content'][0]['text'];
        }
    }

    return null;
}

/**
 * Call Gemini API (Google)
 */
function callGeminiAPI($prompt) {
    $apiKey = $_ENV['GEMINI_API_KEY'] ?? '';
    if (empty($apiKey)) return null;

    $model = $_ENV['GEMINI_MODEL'] ?? 'gemini-1.5-flash';
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.9,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 2048,
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $result = json_decode($response, true);
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return $result['candidates'][0]['content']['parts'][0]['text'];
        }
    }

    return null;
}

/**
 * Call OpenAI API
 */
function callOpenAIAPI($prompt) {
    $apiKey = $_ENV['OPENAI_API_KEY'] ?? '';
    if (empty($apiKey)) return null;

    $url = "https://api.openai.com/v1/chat/completions";

    $data = [
        'model' => $_ENV['OPENAI_MODEL'] ?? 'gpt-4o-mini',
        'messages' => [
            ['role' => 'user', 'content' => $prompt]
        ],
        'temperature' => 0.9,
        'max_tokens' => intval($_ENV['OPENAI_MAX_TOKENS'] ?? 2000)
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: Bearer {$apiKey}"
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $result = json_decode($response, true);
        if (isset($result['choices'][0]['message']['content'])) {
            return $result['choices'][0]['message']['content'];
        }
    }

    return null;
}

/**
 * Generate content using AI with fallback chain
 */
function generateAIContent($prompt) {
    $defaultProvider = $_ENV['DEFAULT_AI_PROVIDER'] ?? 'claude';

    // Try default provider first
    if ($defaultProvider === 'claude') {
        $content = callClaudeAPI($prompt);
        if ($content) return ['content' => $content, 'provider' => 'claude'];
    } elseif ($defaultProvider === 'gemini') {
        $content = callGeminiAPI($prompt);
        if ($content) return ['content' => $content, 'provider' => 'gemini'];
    } elseif ($defaultProvider === 'openai') {
        $content = callOpenAIAPI($prompt);
        if ($content) return ['content' => $content, 'provider' => 'openai'];
    }

    // Fallback chain: Claude -> Gemini -> OpenAI
    $content = callClaudeAPI($prompt);
    if ($content) return ['content' => $content, 'provider' => 'claude'];

    $content = callGeminiAPI($prompt);
    if ($content) return ['content' => $content, 'provider' => 'gemini'];

    $content = callOpenAIAPI($prompt);
    if ($content) return ['content' => $content, 'provider' => 'openai'];

    return null;
}

/**
 * Generate hashtags based on content
 */
function generateHashtags($topic, $platform, $language = 'ar') {
    $baseHashtags = [
        'ar' => [
            'common' => ['تسويق', 'سوشيال_ميديا', 'محتوى', 'نجاح', 'تطوير'],
            'instagram' => ['انستغرام', 'تصوير', 'تصميم', 'فن', 'إبداع'],
            'facebook' => ['فيسبوك', 'مجتمع', 'تواصل', 'مشاركة'],
            'twitter' => ['تويتر', 'أخبار', 'رأي', 'نقاش'],
            'linkedin' => ['لينكد_ان', 'وظائف', 'مهني', 'أعمال', 'ريادة'],
        ],
        'en' => [
            'common' => ['marketing', 'socialmedia', 'content', 'success', 'growth'],
            'instagram' => ['instagram', 'photography', 'design', 'art', 'creative'],
            'facebook' => ['facebook', 'community', 'connection', 'share'],
            'twitter' => ['twitter', 'news', 'trending', 'discussion'],
            'linkedin' => ['linkedin', 'jobs', 'professional', 'business', 'career'],
        ],
    ];

    $hashtags = $baseHashtags[$language]['common'];

    if (isset($baseHashtags[$language][$platform])) {
        $hashtags = array_merge($hashtags, $baseHashtags[$language][$platform]);
    }

    // Add topic-based hashtags
    $topicWords = preg_split('/[\s]+/u', $topic);
    foreach ($topicWords as $word) {
        $word = trim($word);
        if (mb_strlen($word) > 3) {
            $hashtags[] = str_replace(' ', '_', $word);
        }
    }

    shuffle($hashtags);
    $selected = array_slice(array_unique($hashtags), 0, 8);

    return implode(' ', array_map(function($tag) {
        return '#' . $tag;
    }, $selected));
}

// Handle actions
try {
    switch ($action) {
        case 'generate-post':
            $topic = $input['topic'] ?? '';
            $platform = $input['platform'] ?? 'instagram';
            $language = $input['language'] ?? 'ar';
            $contentType = $input['contentType'] ?? 'product';

            if (empty($topic)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'الموضوع مطلوب'], JSON_UNESCAPED_UNICODE);
                exit();
            }

            // Build AI prompt
            if ($language === 'ar') {
                $prompt = "أنت خبير في كتابة المحتوى التسويقي على منصات التواصل الاجتماعي.\n\n";
                $prompt .= "اكتب منشوراً تسويقياً احترافياً وجذاباً عن: {$topic}\n\n";
                $prompt .= "المنصة: {$platform}\n";
                $prompt .= "نوع المحتوى: {$contentType}\n\n";
                $prompt .= "المطلوب:\n";
                $prompt .= "1. مقدمة قوية تجذب الانتباه مع إيموجي مناسب\n";
                $prompt .= "2. محتوى غني بالمعلومات (100-150 كلمة)\n";
                $prompt .= "3. دعوة قوية لاتخاذ إجراء (Call to Action)\n";
                $prompt .= "4. استخدم لغة عربية سليمة ومهنية\n";
                $prompt .= "5. أضف إيموجي في الأماكن المناسبة\n\n";
                $prompt .= "اجعل المحتوى مناسباً لـ {$platform} وملهماً للقراء.";
            } else {
                $prompt = "You are an expert social media marketing content writer.\n\n";
                $prompt .= "Write a professional and engaging marketing post about: {$topic}\n\n";
                $prompt .= "Platform: {$platform}\n";
                $prompt .= "Content type: {$contentType}\n\n";
                $prompt .= "Requirements:\n";
                $prompt .= "1. Strong attention-grabbing opening with relevant emoji\n";
                $prompt .= "2. Rich informative content (100-150 words)\n";
                $prompt .= "3. Strong Call to Action\n";
                $prompt .= "4. Use professional language\n";
                $prompt .= "5. Add emojis where appropriate\n\n";
                $prompt .= "Make it suitable for {$platform} and inspiring for readers.";
            }

            // Generate content using AI
            $result = generateAIContent($prompt);

            if ($result) {
                $caption = $result['content'];
                $provider = $result['provider'];

                // Extract hashtags if present in AI response
                $lines = explode("\n", $caption);
                $cleanCaption = '';
                $extractedHashtags = [];

                foreach ($lines as $line) {
                    if (preg_match('/#\w+/', $line)) {
                        preg_match_all('/#[\p{L}\p{N}_]+/u', $line, $matches);
                        $extractedHashtags = array_merge($extractedHashtags, $matches[0]);
                    } else {
                        $cleanCaption .= $line . "\n";
                    }
                }

                $caption = trim($cleanCaption);

                // Generate additional hashtags
                $generatedHashtags = generateHashtags($topic, $platform, $language);

                // Combine hashtags
                $allHashtags = array_unique(array_merge($extractedHashtags, explode(' ', $generatedHashtags)));
                $hashtags = implode(' ', array_slice($allHashtags, 0, 10));

                echo json_encode([
                    'success' => true,
                    'data' => [
                        'caption' => $caption,
                        'hashtags' => $hashtags,
                        'provider' => $provider,
                        'fullContent' => $caption . "\n\n" . $hashtags
                    ]
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'فشل توليد المحتوى. يرجى المحاولة مرة أخرى.'
                ], JSON_UNESCAPED_UNICODE);
            }
            break;

        case 'generate-video-script':
            $topic = $input['topic'] ?? '';
            $duration = $input['duration'] ?? 60;
            $language = $input['language'] ?? 'ar';

            if (empty($topic)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'الموضوع مطلوب'], JSON_UNESCAPED_UNICODE);
                exit();
            }

            if ($language === 'ar') {
                $prompt = "أنت كاتب سكريبتات فيديو محترف.\n\n";
                $prompt .= "اكتب سكريبت فيديو احترافي مدته {$duration} ثانية عن: {$topic}\n\n";
                $prompt .= "السكريبت يجب أن يتضمن:\n";
                $prompt .= "1. مقدمة جذابة (5-10 ثواني) تلفت انتباه المشاهد\n";
                $prompt .= "2. محتوى رئيسي مقسم إلى نقاط واضحة\n";
                $prompt .= "3. خاتمة قوية مع دعوة لاتخاذ إجراء\n";
                $prompt .= "4. وصف المشاهد المرئية والموسيقى المقترحة\n";
                $prompt .= "5. توقيت كل مشهد\n\n";
                $prompt .= "اكتبه بأسلوب مشوق وسهل الفهم.";
            } else {
                $prompt = "You are a professional video script writer.\n\n";
                $prompt .= "Write a professional {$duration}-second video script about: {$topic}\n\n";
                $prompt .= "The script must include:\n";
                $prompt .= "1. Engaging opening (5-10 seconds) to capture attention\n";
                $prompt .= "2. Main content divided into clear points\n";
                $prompt .= "3. Strong conclusion with call to action\n";
                $prompt .= "4. Visual scene descriptions and suggested music\n";
                $prompt .= "5. Timing for each scene\n\n";
                $prompt .= "Write it in an exciting and easy-to-understand style.";
            }

            $result = generateAIContent($prompt);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'script' => $result['content'],
                        'duration' => $duration,
                        'provider' => $result['provider']
                    ]
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'فشل توليد السكريبت. يرجى المحاولة مرة أخرى.'
                ], JSON_UNESCAPED_UNICODE);
            }
            break;

        case 'generate-image':
            // Placeholder for now - can integrate DALL-E or Stable Diffusion later
            echo json_encode([
                'success' => true,
                'data' => [
                    'url' => 'https://via.placeholder.com/1024x1024/667eea/ffffff?text=AI+Generated',
                    'provider' => 'placeholder'
                ]
            ], JSON_UNESCAPED_UNICODE);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'إجراء غير صالح'], JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ أثناء توليد المحتوى',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
