<?php
/**
 * Temporary file to seed pages
 * DELETE THIS FILE AFTER USE!
 */

// Password protection
$password = 'mediapro2025';
if (!isset($_GET['password']) || $_GET['password'] !== $password) {
    die('Access denied. Use: ?password=mediapro2025');
}

header('Content-Type: text/plain; charset=utf-8');

echo "========================================\n";
echo "MediaPro - Seeding Pages\n";
echo "========================================\n\n";

// Change to project root
chdir(__DIR__ . '/..');

// Check if Laravel exists
if (!file_exists('artisan')) {
    die("Error: Not in Laravel root directory\n");
}

// Run the seeder
echo "Running PagesSeeder...\n\n";

$output = shell_exec('php artisan db:seed --class=PagesSeeder 2>&1');
echo $output;

echo "\n========================================\n";
echo "Done! Check https://www.mediapro.social/features\n";
echo "========================================\n";
echo "\nIMPORTANT: Delete this file now!\n";
