<?php
/**
 * User Registration Endpoint
 * POST /api-register.php
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
    $errors = [];

    if (empty($data['name'])) {
        $errors['name'] = $locale === 'ar' ? 'الاسم مطلوب' : 'Name is required';
    }

    if (empty($data['email'])) {
        $errors['email'] = $locale === 'ar' ? 'البريد الإلكتروني مطلوب' : 'Email is required';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = $locale === 'ar' ? 'البريد الإلكتروني غير صحيح' : 'Email is invalid';
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            $errors['email'] = $locale === 'ar' ? 'البريد الإلكتروني مسجل مسبقاً' : 'Email already exists';
        }
    }

    if (empty($data['password'])) {
        $errors['password'] = $locale === 'ar' ? 'كلمة المرور مطلوبة' : 'Password is required';
    } elseif (strlen($data['password']) < 8) {
        $errors['password'] = $locale === 'ar' ? 'كلمة المرور يجب أن تكون 8 أحرف على الأقل' : 'Password must be at least 8 characters';
    }

    if (empty($data['account_type'])) {
        $data['account_type'] = 'individual'; // Default
    } elseif (!in_array($data['account_type'], ['individual', 'business'])) {
        $errors['account_type'] = $locale === 'ar' ? 'نوع الحساب غير صحيح' : 'Invalid account type';
    }

    if (!empty($errors)) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => $locale === 'ar' ? 'خطأ في البيانات المدخلة' : 'Validation failed',
            'errors' => $errors
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Create user
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, account_type, email_verified_at, created_at, updated_at)
        VALUES (?, ?, ?, ?, NULL, NOW(), NOW())
    ");

    $stmt->execute([
        $data['name'],
        $data['email'],
        password_hash($data['password'], PASSWORD_BCRYPT),
        $data['account_type']
    ]);

    $userId = $pdo->lastInsertId();

    // Generate token
    $token = bin2hex(random_bytes(32));
    $hashedToken = hash('sha256', $token);

    $stmt = $pdo->prepare("
        INSERT INTO personal_access_tokens (tokenable_type, tokenable_id, name, token, abilities, created_at, updated_at)
        VALUES ('App\\\\Models\\\\User', ?, 'auth_token', ?, ?, NOW(), NOW())
    ");

    $stmt->execute([
        $userId,
        $hashedToken,
        json_encode(['*'])
    ]);

    $pdo->commit();

    // Get user
    $stmt = $pdo->prepare("SELECT id, name, email, account_type, created_at FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => $locale === 'ar' ? 'تم التسجيل بنجاح' : 'Registration successful',
        'user' => $user,
        'token' => $token,
        'token_type' => 'Bearer'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $locale === 'ar' ? 'حدث خطأ أثناء التسجيل' : 'Registration failed',
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
