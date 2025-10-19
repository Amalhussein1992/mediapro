<?php
/**
 * Debug page to check Laravel status
 * DELETE AFTER USE!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/plain; charset=utf-8');

echo "========================================\n";
echo "MediaPro - Laravel Debug\n";
echo "========================================\n\n";

// Check PHP
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Current Directory: " . __DIR__ . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n\n";

// Check Laravel files
echo "Checking Laravel files...\n";
chdir(__DIR__ . '/..');
echo "Project Root: " . getcwd() . "\n";

$files = [
    'vendor/autoload.php' => file_exists('vendor/autoload.php'),
    'bootstrap/app.php' => file_exists('bootstrap/app.php'),
    '.env' => file_exists('.env'),
    'artisan' => file_exists('artisan'),
    'storage' => is_dir('storage'),
    'storage writable' => is_writable('storage'),
    'bootstrap/cache' => is_dir('bootstrap/cache'),
    'bootstrap/cache writable' => is_writable('bootstrap/cache'),
];

foreach ($files as $file => $exists) {
    echo "  " . ($exists ? "✓" : "✗") . " $file\n";
}

echo "\n";

// Try to boot Laravel
echo "Attempting to boot Laravel...\n";
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    echo "✓ Laravel booted successfully!\n\n";

    // Check database
    echo "Checking database connection...\n";
    try {
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();

        DB::connection()->getPdo();
        echo "✓ Database connected!\n";
        echo "  Database: " . env('DB_DATABASE') . "\n";
        echo "  Host: " . env('DB_HOST') . "\n";

        // Check pages table
        echo "\nChecking pages table...\n";
        $count = DB::table('pages')->count();
        echo "  Pages in database: $count\n";

        if ($count == 0) {
            echo "  ⚠ Pages table is empty! Need to run seeder.\n";
        }

    } catch (\Exception $e) {
        echo "✗ Database error: " . $e->getMessage() . "\n";
    }

} catch (\Exception $e) {
    echo "✗ Laravel boot failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n========================================\n";
echo "Debug complete\n";
echo "========================================\n";
