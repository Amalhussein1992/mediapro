<?php
/**
 * MediaPro - Laravel Server Test
 * Simple diagnostic page to check server configuration
 */

header('Content-Type: application/json; charset=utf-8');

$results = [
    'status' => 'OK',
    'timestamp' => date('Y-m-d H:i:s'),
    'server_info' => [
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
        'script_filename' => __FILE__,
    ],
    'laravel_check' => [
        'autoload_exists' => file_exists(__DIR__ . '/../vendor/autoload.php'),
        'bootstrap_exists' => file_exists(__DIR__ . '/../bootstrap/app.php'),
        'env_exists' => file_exists(__DIR__ . '/../.env'),
        'storage_writable' => is_writable(__DIR__ . '/../storage'),
        'cache_writable' => is_writable(__DIR__ . '/../bootstrap/cache'),
    ],
    'required_extensions' => [
        'OpenSSL' => extension_loaded('openssl'),
        'PDO' => extension_loaded('pdo'),
        'PDO MySQL' => extension_loaded('pdo_mysql'),
        'Mbstring' => extension_loaded('mbstring'),
        'Tokenizer' => extension_loaded('tokenizer'),
        'XML' => extension_loaded('xml'),
        'Ctype' => extension_loaded('ctype'),
        'JSON' => extension_loaded('json'),
        'BCMath' => extension_loaded('bcmath'),
        'Fileinfo' => extension_loaded('fileinfo'),
    ],
    'permissions' => [
        'storage' => substr(sprintf('%o', fileperms(__DIR__ . '/../storage')), -4),
        'bootstrap_cache' => substr(sprintf('%o', fileperms(__DIR__ . '/../bootstrap/cache')), -4),
    ],
];

// Check if Laravel can boot
if ($results['laravel_check']['autoload_exists'] && $results['laravel_check']['bootstrap_exists']) {
    try {
        require __DIR__ . '/../vendor/autoload.php';
        $app = require_once __DIR__ . '/../bootstrap/app.php';
        $results['laravel_boot'] = 'SUCCESS';
        $results['app_name'] = env('APP_NAME', 'Unknown');
        $results['app_env'] = env('APP_ENV', 'Unknown');
        $results['app_debug'] = env('APP_DEBUG', false) ? 'true' : 'false';
    } catch (Exception $e) {
        $results['laravel_boot'] = 'FAILED';
        $results['error'] = $e->getMessage();
        $results['status'] = 'ERROR';
    }
} else {
    $results['laravel_boot'] = 'SKIPPED - Missing files';
    $results['status'] = 'WARNING';
}

// Check for common issues
$issues = [];

if (version_compare(PHP_VERSION, '8.2.0', '<')) {
    $issues[] = 'PHP version is too old. Laravel 12 requires PHP 8.2 or higher.';
}

foreach ($results['required_extensions'] as $ext => $loaded) {
    if (!$loaded) {
        $issues[] = "Required extension '{$ext}' is not loaded.";
    }
}

if (!$results['laravel_check']['storage_writable']) {
    $issues[] = 'Storage directory is not writable. Run: chmod -R 775 storage';
}

if (!$results['laravel_check']['cache_writable']) {
    $issues[] = 'Bootstrap cache directory is not writable. Run: chmod -R 775 bootstrap/cache';
}

if (!$results['laravel_check']['env_exists']) {
    $issues[] = '.env file is missing. Copy .env.example to .env and configure it.';
}

if (!empty($issues)) {
    $results['issues'] = $issues;
    $results['status'] = 'WARNING';
}

// Pretty print JSON
echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
