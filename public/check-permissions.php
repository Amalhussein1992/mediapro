<?php
/**
 * Check File Permissions and Database Connection
 *
 * Upload to: public/check-permissions.php
 * Visit: https://www.mediapro.social/check-permissions.php
 *
 * DELETE AFTER USE!
 */

$baseDir = dirname(__DIR__);
$envPath = $baseDir . '/.env';
$storagePath = $baseDir . '/storage';

echo "<!DOCTYPE html>
<html dir='rtl' lang='ar'>
<head>
    <meta charset='UTF-8'>
    <title>Permissions Check - MediaPro</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .check {
            background: #f0f0f0;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #999;
            border-radius: 4px;
        }
        .success {
            background: #e8f5e9;
            border-left-color: #4CAF50;
        }
        .error {
            background: #ffebee;
            border-left-color: #f44336;
        }
        .warning {
            background: #fff3e0;
            border-left-color: #ff9800;
        }
        pre {
            background: #263238;
            color: #aed581;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
            direction: ltr;
            text-align: left;
            font-size: 12px;
        }
        h3 { color: #1976D2; margin-top: 25px; }
    </style>
</head>
<body>
<div class='container'>
    <h1>🔍 فحص الصلاحيات والاتصالات</h1>
    <p><strong>Base Directory:</strong> <code>{$baseDir}</code></p>
";

function checkItem($title, $status, $details = '') {
    $class = $status ? 'success' : 'error';
    $icon = $status ? '✅' : '❌';
    echo "<div class='check {$class}'>";
    echo "<strong>{$icon} {$title}</strong>";
    if ($details) {
        echo "<br><small>{$details}</small>";
    }
    echo "</div>";
}

// 1. Check PHP Version
echo "<h3>📋 معلومات PHP</h3>";
checkItem('PHP Version', version_compare(PHP_VERSION, '8.1.0', '>='), 'Current: ' . PHP_VERSION);

// 2. Check .env file
echo "<h3>📄 ملف .env</h3>";
$envExists = file_exists($envPath);
checkItem('ملف .env موجود', $envExists, $envPath);

if ($envExists) {
    $envReadable = is_readable($envPath);
    checkItem('يمكن قراءة الملف', $envReadable);

    $envWritable = is_writable($envPath);
    checkItem('يمكن الكتابة على الملف', $envWritable);

    if (!$envWritable) {
        echo "<div class='check error'><strong>⚠️ حل المشكلة:</strong><br>";
        echo "الملف .env غير قابل للكتابة. يجب تغيير الصلاحيات:<br>";
        echo "<pre>chmod 664 .env</pre>";
        echo "أو من Plesk File Manager → حقوق الملف → ضع علامة على Write</div>";
    }

    // Try to read .env
    if ($envReadable) {
        $envContent = file_get_contents($envPath);
        $envLines = explode("\n", $envContent);
        echo "<div class='check success'>";
        echo "<strong>📝 محتوى .env (أول 20 سطر):</strong><br>";
        echo "<pre>" . htmlspecialchars(implode("\n", array_slice($envLines, 0, 20))) . "</pre>";
        echo "</div>";

        // Check important keys
        $hasAppKey = strpos($envContent, 'APP_KEY=') !== false;
        checkItem('APP_KEY موجود', $hasAppKey);

        $hasDbConfig = strpos($envContent, 'DB_DATABASE=') !== false;
        checkItem('إعدادات قاعدة البيانات موجودة', $hasDbConfig);
    }
}

// 3. Check storage directory
echo "<h3>💾 مجلد Storage</h3>";
$storageExists = is_dir($storagePath);
checkItem('مجلد storage موجود', $storageExists, $storagePath);

if ($storageExists) {
    $storageWritable = is_writable($storagePath);
    checkItem('يمكن الكتابة في storage', $storageWritable);

    if (!$storageWritable) {
        echo "<div class='check error'><strong>⚠️ حل المشكلة:</strong><br>";
        echo "مجلد storage غير قابل للكتابة:<br>";
        echo "<pre>chmod -R 775 storage\nchmod -R 775 bootstrap/cache</pre>";
        echo "</div>";
    }

    // Check subdirectories
    $dirs = ['logs', 'framework', 'framework/cache', 'framework/sessions', 'framework/views'];
    foreach ($dirs as $dir) {
        $fullPath = $storagePath . '/' . $dir;
        if (is_dir($fullPath)) {
            checkItem("storage/{$dir}", is_writable($fullPath), is_writable($fullPath) ? 'قابل للكتابة' : 'غير قابل للكتابة');
        }
    }
}

// 4. Check database connection
echo "<h3>🗄️ قاعدة البيانات</h3>";

// Load .env values
if (file_exists($envPath)) {
    $env = parse_ini_file($envPath);

    $dbHost = $env['DB_HOST'] ?? 'localhost';
    $dbPort = $env['DB_PORT'] ?? '3306';
    $dbName = $env['DB_DATABASE'] ?? '';
    $dbUser = $env['DB_USERNAME'] ?? '';
    $dbPass = $env['DB_PASSWORD'] ?? '';

    echo "<div class='check'>";
    echo "<strong>📊 إعدادات الاتصال:</strong><br>";
    echo "Host: {$dbHost}:{$dbPort}<br>";
    echo "Database: {$dbName}<br>";
    echo "Username: {$dbUser}<br>";
    echo "Password: " . (empty($dbPass) ? '(فارغ)' : '***') . "<br>";
    echo "</div>";

    // Try to connect
    try {
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        checkItem('الاتصال بقاعدة البيانات', true, 'تم الاتصال بنجاح');

        // Check app_settings table
        try {
            $stmt = $pdo->query("DESCRIBE app_settings");
            $columns = $stmt->fetchAll();
            checkItem('جدول app_settings موجود', true, count($columns) . ' أعمدة');

            // Count records
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM app_settings");
            $result = $stmt->fetch();
            checkItem('عدد الإعدادات في قاعدة البيانات', true, $result['count'] . ' إعداد محفوظ');

            // Show sample settings
            $stmt = $pdo->query("SELECT `key`, `value`, `group` FROM app_settings LIMIT 10");
            $settings = $stmt->fetchAll();

            if (!empty($settings)) {
                echo "<div class='check success'>";
                echo "<strong>📋 نماذج من الإعدادات المحفوظة:</strong><br>";
                echo "<pre>";
                foreach ($settings as $setting) {
                    echo htmlspecialchars($setting['key']) . " = " . htmlspecialchars(substr($setting['value'], 0, 50)) . " [" . $setting['group'] . "]\n";
                }
                echo "</pre>";
                echo "</div>";
            }

            // Test INSERT permission
            try {
                $testKey = 'test_write_' . time();
                $stmt = $pdo->prepare("INSERT INTO app_settings (`key`, `value`, `type`, `group`) VALUES (?, ?, ?, ?)");
                $stmt->execute([$testKey, 'test_value', 'string', 'test']);

                checkItem('الكتابة في قاعدة البيانات', true, 'تم إضافة سجل اختباري');

                // Delete test record
                $pdo->exec("DELETE FROM app_settings WHERE `key` = '{$testKey}'");

            } catch (Exception $e) {
                checkItem('الكتابة في قاعدة البيانات', false, 'خطأ: ' . $e->getMessage());
            }

        } catch (Exception $e) {
            checkItem('جدول app_settings', false, 'غير موجود أو خطأ: ' . $e->getMessage());

            echo "<div class='check error'><strong>⚠️ حل المشكلة:</strong><br>";
            echo "جدول app_settings غير موجود. يجب تشغيل migrations:<br>";
            echo "<pre>php artisan migrate --force</pre>";
            echo "</div>";
        }

    } catch (Exception $e) {
        checkItem('الاتصال بقاعدة البيانات', false, 'خطأ: ' . $e->getMessage());

        echo "<div class='check error'><strong>⚠️ حل المشكلة:</strong><br>";
        echo "تحقق من إعدادات قاعدة البيانات في ملف .env:<br>";
        echo "<pre>DB_HOST={$dbHost}\nDB_PORT={$dbPort}\nDB_DATABASE={$dbName}\nDB_USERNAME={$dbUser}\nDB_PASSWORD=***</pre>";
        echo "</div>";
    }
}

// 5. Test .env write
echo "<h3>✍️ اختبار الكتابة على .env</h3>";

if ($envExists && is_writable($envPath)) {
    try {
        $testContent = file_get_contents($envPath);
        $testKey = "\n# TEST_WRITE_" . time() . "=test_value";

        // Backup
        copy($envPath, $envPath . '.backup.test');

        // Try to write
        if (file_put_contents($envPath, $testContent . $testKey)) {
            checkItem('الكتابة على ملف .env', true, 'تمت الكتابة بنجاح');

            // Restore original
            file_put_contents($envPath, $testContent);
            unlink($envPath . '.backup.test');
        } else {
            checkItem('الكتابة على ملف .env', false, 'فشلت الكتابة');
        }

    } catch (Exception $e) {
        checkItem('الكتابة على ملف .env', false, 'خطأ: ' . $e->getMessage());
    }
} else {
    echo "<div class='check warning'><strong>⚠️ تخطي الاختبار:</strong> الملف غير قابل للكتابة</div>";
}

// 6. Laravel environment info
echo "<h3>⚙️ معلومات Laravel</h3>";

if (file_exists($baseDir . '/artisan')) {
    checkItem('ملف artisan موجود', true);

    if (file_exists($baseDir . '/vendor/autoload.php')) {
        checkItem('مجلد vendor موجود', true, 'Composer dependencies مثبتة');

        // Try to load Laravel
        try {
            require $baseDir . '/vendor/autoload.php';
            $app = require_once $baseDir . '/bootstrap/app.php';

            checkItem('Laravel Environment', true, 'يمكن تحميل Laravel');

        } catch (Exception $e) {
            checkItem('Laravel Environment', false, 'خطأ: ' . $e->getMessage());
        }
    } else {
        checkItem('مجلد vendor', false, 'غير موجود - يجب تشغيل composer install');
    }
}

// Summary
echo "<hr><h3>📊 الخلاصة</h3>";

$issues = [];
if (!$envWritable) $issues[] = 'ملف .env غير قابل للكتابة';
if (!$storageWritable) $issues[] = 'مجلد storage غير قابل للكتابة';

if (empty($issues)) {
    echo "<div class='check success'><strong>✅ كل شيء يبدو جيداً!</strong><br>";
    echo "الصلاحيات صحيحة وقاعدة البيانات تعمل. يمكنك حفظ الإعدادات الآن.</div>";
} else {
    echo "<div class='check error'><strong>❌ مشاكل يجب حلها:</strong><br>";
    echo "<ul>";
    foreach ($issues as $issue) {
        echo "<li>{$issue}</li>";
    }
    echo "</ul></div>";
}

echo "<hr>";
echo "<div class='check error' style='margin-top: 20px;'>";
echo "<h3>⚠️ احذف هذا الملف فوراً بعد الفحص!</h3>";
echo "<p>من Plesk File Manager أو عبر SSH:</p>";
echo "<pre>rm public/check-permissions.php</pre>";
echo "</div>";

echo "</div></body></html>";
?>
