<?php
/**
 * AI Content Generation API Endpoint
 * Generates posts, images, and video scripts using AI models
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Database connection
try {
    $db = new PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'] . ";charset=utf8mb4",
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Get authorization token
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
$token = str_replace('Bearer ', '', $authHeader);

if (empty($token)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit();
}

// Verify token and get user
try {
    $hashedToken = hash('sha256', $token);
    $stmt = $db->prepare("
        SELECT u.*
        FROM users u
        JOIN personal_access_tokens pat ON u.id = pat.tokenable_id
        WHERE pat.token = :token
        AND pat.tokenable_type = 'App\\\\Models\\\\User'
        LIMIT 1
    ");
    $stmt->execute(['token' => $hashedToken]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid token']);
        exit();
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Authentication error']);
    exit();
}

// Get request data
$input = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $_GET['action'] ?? 'generate-post';

/**
 * Generate AI content using Gemini API
 */
function generateWithGemini($prompt) {
    $apiKey = $_ENV['GEMINI_API_KEY'] ?? '';

    if (empty($apiKey)) {
        return null;
    }

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}";

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
 * Generate AI content using OpenAI API
 */
function generateWithOpenAI($prompt) {
    $apiKey = $_ENV['OPENAI_API_KEY'] ?? '';

    if (empty($apiKey)) {
        return null;
    }

    $url = "https://api.openai.com/v1/chat/completions";

    $data = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'user', 'content' => $prompt]
        ],
        'temperature' => 0.9,
        'max_tokens' => 2048
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
 * Generate content with fallback to multiple AI providers
 */
function generateAIContent($prompt) {
    // Try Gemini first
    $content = generateWithGemini($prompt);
    if ($content) {
        return ['content' => $content, 'provider' => 'gemini'];
    }

    // Fallback to OpenAI
    $content = generateWithOpenAI($prompt);
    if ($content) {
        return ['content' => $content, 'provider' => 'openai'];
    }

    return null;
}

/**
 * Handle different actions
 */
try {
    switch ($action) {
        case 'generate-post':
            $topic = $input['topic'] ?? '';
            $platform = $input['platform'] ?? 'instagram';
            $language = $input['language'] ?? 'ar';

            if (empty($topic)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Topic is required']);
                exit();
            }

            // Build prompt based on language
            if ($language === 'ar') {
                $prompt = "أنت خبير في كتابة المحتوى على منصات التواصل الاجتماعي.\n\n";
                $prompt .= "اكتب منشور جذاب وملفت للنظر عن: {$topic}\n\n";
                $prompt .= "المنصة: {$platform}\n";
                $prompt .= "اللغة: العربية\n\n";
                $prompt .= "يجب أن يتضمن المنشور:\n";
                $prompt .= "1. عنوان جذاب\n";
                $prompt .= "2. نص المنشور (100-150 كلمة)\n";
                $prompt .= "3. 5-10 هاشتاجات مناسبة\n";
                $prompt .= "4. دعوة لاتخاذ إجراء (Call to Action)\n\n";
                $prompt .= "اجعل المحتوى احترافياً ومثيراً للاهتمام.";
            } else {
                $prompt = "You are an expert social media content writer.\n\n";
                $prompt .= "Write an engaging and eye-catching post about: {$topic}\n\n";
                $prompt .= "Platform: {$platform}\n";
                $prompt .= "Language: English\n\n";
                $prompt .= "The post should include:\n";
                $prompt .= "1. An attention-grabbing headline\n";
                $prompt .= "2. Post content (100-150 words)\n";
                $prompt .= "3. 5-10 relevant hashtags\n";
                $prompt .= "4. A Call to Action\n\n";
                $prompt .= "Make it professional and engaging.";
            }

            $result = generateAIContent($prompt);

            if ($result) {
                // Parse the response to extract caption and hashtags
                $content = $result['content'];
                $lines = explode("\n", $content);

                $caption = '';
                $hashtags = [];

                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;

                    // Check if line contains hashtags
                    if (preg_match('/#\w+/', $line)) {
                        preg_match_all('/#\w+/', $line, $matches);
                        $hashtags = array_merge($hashtags, $matches[0]);
                    } else {
                        $caption .= $line . "\n";
                    }
                }

                $caption = trim($caption);
                $hashtagsText = implode(' ', array_unique($hashtags));

                echo json_encode([
                    'success' => true,
                    'data' => [
                        'caption' => $caption,
                        'hashtags' => $hashtagsText,
                        'fullContent' => $content,
                        'provider' => $result['provider']
                    ]
                ]);
            } else {
                // Fallback content
                echo json_encode([
                    'success' => true,
                    'fallback' => true,
                    'data' => [
                        'caption' => "محتوى رائع عن {$topic}! 🌟\n\nنحن متحمسون لمشاركة هذا معكم. تابعونا لمزيد من المحتوى المميز!",
                        'hashtags' => '#محتوى #تسويق #سوشيال_ميديا #' . str_replace(' ', '_', $topic),
                        'fullContent' => "محتوى رائع عن {$topic}",
                        'provider' => 'fallback'
                    ]
                ]);
            }
            break;

        case 'generate-image':
            // This would typically call DALL-E or similar image generation API
            // For now, return a placeholder
            echo json_encode([
                'success' => true,
                'data' => [
                    'url' => 'https://via.placeholder.com/1024x1024/667eea/ffffff?text=AI+Generated+Image',
                    'provider' => 'placeholder'
                ]
            ]);
            break;

        case 'generate-video-script':
            $topic = $input['topic'] ?? '';
            $duration = $input['duration'] ?? 60;
            $language = $input['language'] ?? 'ar';

            if (empty($topic)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Topic is required']);
                exit();
            }

            if ($language === 'ar') {
                $prompt = "أنت كاتب سكريبتات فيديو محترف.\n\n";
                $prompt .= "اكتب سكريبت فيديو مدته {$duration} ثانية عن: {$topic}\n\n";
                $prompt .= "يجب أن يتضمن السكريبت:\n";
                $prompt .= "1. مقدمة جذابة (5-10 ثواني)\n";
                $prompt .= "2. المحتوى الرئيسي\n";
                $prompt .= "3. خاتمة مع دعوة لاتخاذ إجراء\n";
                $prompt .= "4. وصف المشاهد المرئية\n\n";
                $prompt .= "اجعله مناسباً للتصوير والإنتاج.";
            } else {
                $prompt = "You are a professional video script writer.\n\n";
                $prompt .= "Write a {$duration}-second video script about: {$topic}\n\n";
                $prompt .= "The script should include:\n";
                $prompt .= "1. Engaging introduction (5-10 seconds)\n";
                $prompt .= "2. Main content\n";
                $prompt .= "3. Conclusion with call to action\n";
                $prompt .= "4. Visual scene descriptions\n\n";
                $prompt .= "Make it suitable for filming and production.";
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
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'fallback' => true,
                    'data' => [
                        'script' => "سكريبت فيديو عن {$topic}\n\nالمقدمة: مرحباً بكم...\nالمحتوى: ...\nالخاتمة: لا تنسوا الاشتراك!",
                        'duration' => $duration,
                        'provider' => 'fallback'
                    ]
                ]);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error generating content',
        'error' => $e->getMessage()
    ]);
}
