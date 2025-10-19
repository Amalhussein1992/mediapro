<?php
/**
 * MediaPro - Server Setup Script
 * Run Laravel commands without SSH
 *
 * IMPORTANT: Delete this file after use!
 */

// Security: Add a simple password protection
$PASSWORD = 'mediapro2025'; // Change this!

if (!isset($_GET['password']) || $_GET['password'] !== $PASSWORD) {
    die('Access denied. Use: ?password=mediapro2025');
}

header('Content-Type: text/plain; charset=utf-8');

echo "========================================\n";
echo "MediaPro - Server Setup\n";
echo "========================================\n\n";

// Change to project root
chdir(__DIR__ . '/..');

echo "[1/10] Checking environment...\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Current Directory: " . getcwd() . "\n";
echo "User: " . get_current_user() . "\n\n";

// Check if required files exist
echo "[2/10] Checking Laravel files...\n";
$checks = [
    'vendor/autoload.php' => file_exists('vendor/autoload.php'),
    'bootstrap/app.php' => file_exists('bootstrap/app.php'),
    '.env' => file_exists('.env'),
    'artisan' => file_exists('artisan'),
];

foreach ($checks as $file => $exists) {
    echo "  - " . $file . ": " . ($exists ? "✓" : "✗") . "\n";
}
echo "\n";

if (!$checks['vendor/autoload.php']) {
    echo "⚠️  WARNING: vendor folder not found!\n";
    echo "You need to run: composer install\n";
    echo "This can be done via Plesk Composer or SSH.\n\n";
}

if (!$checks['.env']) {
    echo "⚠️  WARNING: .env file not found!\n";
    echo "Please upload .env.server as .env\n\n";
}

// Clear caches
echo "[3/10] Clearing configuration cache...\n";
$output = shell_exec('php artisan config:clear 2>&1');
echo $output . "\n";

echo "[4/10] Clearing application cache...\n";
$output = shell_exec('php artisan cache:clear 2>&1');
echo $output . "\n";

echo "[5/10] Clearing route cache...\n";
$output = shell_exec('php artisan route:clear 2>&1');
echo $output . "\n";

echo "[6/10] Clearing view cache...\n";
$output = shell_exec('php artisan view:clear 2>&1');
echo $output . "\n";

// Rebuild caches
echo "[7/10] Building configuration cache...\n";
$output = shell_exec('php artisan config:cache 2>&1');
echo $output . "\n";

echo "[8/10] Building route cache...\n";
$output = shell_exec('php artisan route:cache 2>&1');
echo $output . "\n";

echo "[9/10] Building view cache...\n";
$output = shell_exec('php artisan view:cache 2>&1');
echo $output . "\n";

// Check permissions
echo "[10/10] Checking permissions...\n";
$dirs = ['storage', 'bootstrap/cache'];
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        $writable = is_writable($dir) ? "✓ Writable" : "✗ Not writable";
        echo "  - {$dir}: {$perms} ({$writable})\n";

        // Try to fix permissions
        if (!is_writable($dir)) {
            echo "    Attempting to fix...\n";
            shell_exec("chmod -R 775 {$dir} 2>&1");

            $writable = is_writable($dir) ? "✓ Fixed" : "✗ Still not writable (need SSH)";
            echo "    Result: {$writable}\n";
        }
    }
}

echo "\n========================================\n";
echo "Setup Complete!\n";
echo "========================================\n\n";

echo "Next Steps:\n";
echo "1. Test the site: https://mediapro.social\n";
echo "2. Test API: https://mediapro.social/api/config\n";
echo "3. DELETE THIS FILE for security!\n\n";

echo "If you see errors above, you may need SSH access to:\n";
echo "  - Run: composer install --no-dev --optimize-autoloader\n";
echo "  - Run: php artisan migrate --force\n";
echo "  - Fix permissions: chmod -R 775 storage bootstrap/cache\n\n";

echo "========================================\n";
echo "Server Time: " . date('Y-m-d H:i:s') . "\n";
echo "========================================\n";
