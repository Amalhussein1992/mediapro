<?php
/**
 * User Login Endpoint
 * POST /api-login.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept-Language');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Database config
$pdo = new PDO('mysql:host=127.0.0.1;dbname=socialmedia_manager;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get request data
$data = json_decode(file_get_contents('php://input'), true) ?? [];

// Get locale
$locale = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en';
$locale = strpos($locale, 'ar') !== false ? 'ar' : 'en';

try {
    // Validation
    if (empty($data['email']) || empty($data['password'])) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => $locale === 'ar' ? 'البريد الإلكتروني وكلمة المرور مطلوبان' : 'Email and password are required',
            'errors' => [
                'email' => empty($data['email']) ? ($locale === 'ar' ? 'البريد الإلكتروني مطلوب' : 'Email is required') : null,
                'password' => empty($data['password']) ? ($locale === 'ar' ? 'كلمة المرور مطلوبة' : 'Password is required') : null
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Find user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($data['password'], $user['password'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => $locale === 'ar' ? 'البريد الإلكتروني أو كلمة المرور غير صحيحة' : 'Invalid credentials',
            'errors' => [
                'email' => $locale === 'ar' ? 'البيانات المدخلة غير صحيحة' : 'The provided credentials are incorrect'
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Generate new token
    $token = bin2hex(random_bytes(32));
    $hashedToken = hash('sha256', $token);

    $stmt = $pdo->prepare("
        INSERT INTO personal_access_tokens (tokenable_type, tokenable_id, name, token, abilities, created_at, updated_at)
        VALUES ('App\\\\Models\\\\User', ?, 'auth_token', ?, ?, NOW(), NOW())
    ");

    $stmt->execute([
        $user['id'],
        $hashedToken,
        json_encode(['*'])
    ]);

    // Remove password from user object
    unset($user['password']);
    unset($user['remember_token']);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => $locale === 'ar' ? 'تم تسجيل الدخول بنجاح' : 'Login successful',
        'user' => $user,
        'token' => $token,
        'token_type' => 'Bearer'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $locale === 'ar' ? 'حدث خطأ أثناء تسجيل الدخول' : 'Login failed',
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
