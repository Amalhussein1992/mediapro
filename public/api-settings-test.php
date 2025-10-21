<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Database configuration
$host = '127.0.0.1';
$db = 'socialmedia_manager';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all settings
    $stmt = $pdo->query("SELECT `key`, `value`, `type` FROM app_settings");
    $settings = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $value = $row['value'];

        // Parse value based on type
        switch ($row['type']) {
            case 'boolean':
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                break;
            case 'integer':
                $value = (int) $value;
                break;
            case 'float':
                $value = (float) $value;
                break;
            case 'json':
                $value = json_decode($value, true);
                break;
        }

        $settings[$row['key']] = $value;
    }

    echo json_encode([
        'success' => true,
        'data' => $settings,
        'message' => 'Settings loaded successfully',
        'count' => count($settings)
    ], JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error',
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
