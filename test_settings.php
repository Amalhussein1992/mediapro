<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Settings Database and .env Save Functionality\n";
echo str_repeat("=", 60) . "\n\n";

// Test 1: Check if app_settings table exists
echo "Test 1: Checking if app_settings table exists...\n";
try {
    $count = DB::table('app_settings')->count();
    echo "✓ Table exists with {$count} settings\n\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Insert a test setting
echo "Test 2: Inserting a test setting...\n";
try {
    DB::table('app_settings')->updateOrInsert(
        ['key' => 'test_setting'],
        [
            'key' => 'test_setting',
            'value' => 'test_value_' . time(),
            'type' => 'string',
            'group' => 'general',
            'description' => 'Test setting',
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );
    echo "✓ Setting inserted/updated successfully\n\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 3: Read the setting back
echo "Test 3: Reading the setting back...\n";
try {
    $setting = DB::table('app_settings')->where('key', 'test_setting')->first();
    if ($setting) {
        echo "✓ Setting found: {$setting->key} = {$setting->value}\n\n";
    } else {
        echo "✗ Setting not found\n\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 4: Update .env file
echo "Test 4: Testing .env file update...\n";
try {
    $envPath = base_path('.env');
    if (!File::exists($envPath)) {
        echo "✗ .env file does not exist\n\n";
    } else {
        // Create backup
        $backupPath = base_path('.env.test_backup');
        File::copy($envPath, $backupPath);

        $content = File::get($envPath);
        $testKey = 'TEST_SETTING_KEY';
        $testValue = 'test_value_' . time();

        if (preg_match("/^{$testKey}=.*/m", $content)) {
            $content = preg_replace("/^{$testKey}=.*/m", "{$testKey}=\"{$testValue}\"", $content);
        } else {
            $content .= "\n{$testKey}=\"{$testValue}\"";
        }

        File::put($envPath, $content);

        // Verify
        $newContent = File::get($envPath);
        if (str_contains($newContent, "{$testKey}=\"{$testValue}\"")) {
            echo "✓ .env file updated successfully\n";
            echo "  Added/Updated: {$testKey}=\"{$testValue}\"\n\n";
        } else {
            echo "✗ Failed to update .env file\n\n";
        }

        // Restore backup
        File::copy($backupPath, $envPath);
        File::delete($backupPath);
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 5: Check file permissions
echo "Test 5: Checking file permissions...\n";
try {
    $envPath = base_path('.env');
    if (File::exists($envPath)) {
        if (is_writable($envPath)) {
            echo "✓ .env file is writable\n\n";
        } else {
            echo "✗ .env file is NOT writable\n\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

echo str_repeat("=", 60) . "\n";
echo "Tests completed!\n";
