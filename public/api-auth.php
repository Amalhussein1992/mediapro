<?php
/**
 * Standalone Authentication API
 * Provides registration and login endpoints
 *
 * DELETE THIS FILE once Laravel routing is fixed!
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept-Language');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database configuration
$dbConfig = [
    'host' => '127.0.0.1',
    'database' => 'socialmedia_manager',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

// Helper function to connect to database
function getDB($config) {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    return $pdo;
}

// Helper function to send JSON response
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Helper function to generate token
function generateToken($length = 64) {
    return bin2hex(random_bytes($length / 2));
}

// Get the request path and method
$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);
$requestPath = str_replace('/api-auth.php', '', $requestPath);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Get request body
$requestBody = file_get_contents('php://input');
$data = json_decode($requestBody, true) ?? [];

// Get locale from header
$locale = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en';
$locale = strpos($locale, 'ar') !== false ? 'ar' : 'en';

try {
    $pdo = getDB($dbConfig);

    // ==================== ROUTES ====================

    // POST /register - User registration
    if ($requestPath === '/register' && $requestMethod === 'POST') {

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
            $errors['account_type'] = $locale === 'ar' ? 'نوع الحساب مطلوب' : 'Account type is required';
        } elseif (!in_array($data['account_type'], ['individual', 'business'])) {
            $errors['account_type'] = $locale === 'ar' ? 'نوع الحساب غير صحيح' : 'Invalid account type';
        }

        if (!empty($errors)) {
            jsonResponse([
                'success' => false,
                'message' => $locale === 'ar' ? 'خطأ في البيانات المدخلة' : 'Validation failed',
                'errors' => $errors
            ], 422);
        }

        // Create user
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        $token = generateToken();
        $tokenName = 'auth_token_' . time();

        try {
            $pdo->beginTransaction();

            // Insert user
            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, password, account_type, created_at, updated_at)
                VALUES (?, ?, ?, ?, NOW(), NOW())
            ");

            $stmt->execute([
                $data['name'],
                $data['email'],
                $hashedPassword,
                $data['account_type']
            ]);

            $userId = $pdo->lastInsertId();

            // Create token in personal_access_tokens table
            $stmt = $pdo->prepare("
                INSERT INTO personal_access_tokens (tokenable_type, tokenable_id, name, token, abilities, created_at, updated_at)
                VALUES ('App\\\\Models\\\\User', ?, ?, ?, ?, NOW(), NOW())
            ");

            $hashedToken = hash('sha256', $token);

            $stmt->execute([
                $userId,
                $tokenName,
                $hashedToken,
                json_encode(['*'])
            ]);

            $pdo->commit();

            // Get created user
            $stmt = $pdo->prepare("SELECT id, name, email, account_type, created_at FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            jsonResponse([
                'success' => true,
                'message' => $locale === 'ar' ? 'تم التسجيل بنجاح' : 'Registration successful',
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ], 201);

        } catch (PDOException $e) {
            $pdo->rollBack();
            jsonResponse([
                'success' => false,
                'message' => $locale === 'ar' ? 'حدث خطأ أثناء التسجيل' : 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // POST /login - User login
    elseif ($requestPath === '/login' && $requestMethod === 'POST') {

        // Validation
        if (empty($data['email']) || empty($data['password'])) {
            jsonResponse([
                'success' => false,
                'message' => $locale === 'ar' ? 'البريد الإلكتروني وكلمة المرور مطلوبان' : 'Email and password are required',
                'errors' => [
                    'email' => empty($data['email']) ? ($locale === 'ar' ? 'البريد الإلكتروني مطلوب' : 'Email is required') : null,
                    'password' => empty($data['password']) ? ($locale === 'ar' ? 'كلمة المرور مطلوبة' : 'Password is required') : null
                ]
            ], 422);
        }

        // Find user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($data['password'], $user['password'])) {
            jsonResponse([
                'success' => false,
                'message' => $locale === 'ar' ? 'البريد الإلكتروني أو كلمة المرور غير صحيحة' : 'Invalid credentials',
                'errors' => [
                    'email' => $locale === 'ar' ? 'البيانات المدخلة غير صحيحة' : 'The provided credentials are incorrect'
                ]
            ], 401);
        }

        // Generate new token
        $token = generateToken();
        $tokenName = 'auth_token_' . time();
        $hashedToken = hash('sha256', $token);

        $stmt = $pdo->prepare("
            INSERT INTO personal_access_tokens (tokenable_type, tokenable_id, name, token, abilities, created_at, updated_at)
            VALUES ('App\\\\Models\\\\User', ?, ?, ?, ?, NOW(), NOW())
        ");

        $stmt->execute([
            $user['id'],
            $tokenName,
            $hashedToken,
            json_encode(['*'])
        ]);

        // Remove password from user object
        unset($user['password']);

        jsonResponse([
            'success' => true,
            'message' => $locale === 'ar' ? 'تم تسجيل الدخول بنجاح' : 'Login successful',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    // GET /user - Get authenticated user (requires token)
    elseif ($requestPath === '/user' && $requestMethod === 'GET') {

        // Get token from Authorization header
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (empty($authHeader) || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            jsonResponse([
                'success' => false,
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized'
            ], 401);
        }

        $token = $matches[1];
        $hashedToken = hash('sha256', $token);

        // Find token
        $stmt = $pdo->prepare("
            SELECT tokenable_id FROM personal_access_tokens
            WHERE token = ? AND tokenable_type = 'App\\\\Models\\\\User'
        ");
        $stmt->execute([$hashedToken]);
        $tokenRecord = $stmt->fetch();

        if (!$tokenRecord) {
            jsonResponse([
                'success' => false,
                'message' => $locale === 'ar' ? 'غير مصرح' : 'Unauthorized'
            ], 401);
        }

        // Get user
        $stmt = $pdo->prepare("SELECT id, name, email, account_type, created_at FROM users WHERE id = ?");
        $stmt->execute([$tokenRecord['tokenable_id']]);
        $user = $stmt->fetch();

        if (!$user) {
            jsonResponse([
                'success' => false,
                'message' => $locale === 'ar' ? 'المستخدم غير موجود' : 'User not found'
            ], 404);
        }

        jsonResponse([
            'success' => true,
            'data' => $user
        ]);
    }

    // Route not found
    else {
        jsonResponse([
            'success' => false,
            'message' => 'Route not found',
            'path' => $requestPath,
            'method' => $requestMethod,
            'available_routes' => [
                'POST /register',
                'POST /login',
                'GET /user (requires Bearer token)'
            ]
        ], 404);
    }

} catch (PDOException $e) {
    jsonResponse([
        'success' => false,
        'message' => $locale === 'ar' ? 'خطأ في قاعدة البيانات' : 'Database error',
        'error' => $e->getMessage()
    ], 500);
} catch (Exception $e) {
    jsonResponse([
        'success' => false,
        'message' => $locale === 'ar' ? 'خطأ في الخادم' : 'Server error',
        'error' => $e->getMessage()
    ], 500);
}
