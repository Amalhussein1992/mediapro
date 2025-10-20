<?php
/**
 * Automatic Composer Installer for Plesk/Windows Server
 *
 * Upload this file to: public/install-composer.php
 * Then visit: https://www.mediapro.social/install-composer.php
 *
 * WARNING: DELETE THIS FILE AFTER USE!
 */

set_time_limit(600); // 10 minutes timeout

// Security check - only allow from specific IP (change this to your IP)
$allowedIPs = ['*']; // Use '*' to allow all (ONLY FOR TESTING!)

if (!in_array('*', $allowedIPs) && !in_array($_SERVER['REMOTE_ADDR'], $allowedIPs)) {
    die('Access denied. Your IP: ' . $_SERVER['REMOTE_ADDR']);
}

$baseDir = dirname(__DIR__); // Go up from public/ to root
$composerPath = $baseDir . '/composer.phar';
$vendorPath = $baseDir . '/vendor';

echo "<!DOCTYPE html>
<html dir='rtl' lang='ar'>
<head>
    <meta charset='UTF-8'>
    <title>Composer Installer - MediaPro</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .step {
            background: #e3f2fd;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #2196F3;
            border-radius: 4px;
        }
        .success {
            background: #e8f5e9;
            border-left-color: #4CAF50;
            color: #2e7d32;
        }
        .error {
            background: #ffebee;
            border-left-color: #f44336;
            color: #c62828;
        }
        .warning {
            background: #fff3e0;
            border-left-color: #ff9800;
            color: #e65100;
        }
        pre {
            background: #263238;
            color: #aed581;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            direction: ltr;
            text-align: left;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover { background: #1976D2; }
        .btn-danger { background: #f44336; }
        .btn-danger:hover { background: #d32f2f; }
    </style>
</head>
<body>
<div class='container'>
    <h1>🚀 MediaPro - Composer Installer</h1>
    <p><strong>Base Directory:</strong> <code>{$baseDir}</code></p>
";

function output($message, $type = 'step') {
    echo "<div class='step {$type}'>{$message}</div>";
    flush();
    ob_flush();
}

// Check if action is requested
$action = $_GET['action'] ?? 'check';

if ($action === 'check') {
    output("✅ PHP Version: " . PHP_VERSION, 'success');
    output("📁 Base Directory: {$baseDir}", 'step');

    // Check if vendor exists
    if (is_dir($vendorPath)) {
        output("⚠️ المجلد vendor موجود بالفعل!", 'warning');
        echo "<p>يبدو أن المكتبات مثبتة. إذا كانت هناك مشاكل، جرب:</p>";
        echo "<a href='?action=reinstall' class='btn btn-danger'>🔄 إعادة التثبيت</a>";
    } else {
        output("❌ المجلد vendor غير موجود - يحتاج للتثبيت", 'error');
        echo "<a href='?action=install' class='btn'>⬇️ تثبيت Composer Dependencies</a>";
    }

    // Check composer.phar
    if (file_exists($composerPath)) {
        output("✅ ملف composer.phar موجود", 'success');
    } else {
        output("❌ ملف composer.phar غير موجود - سيتم تنزيله", 'warning');
    }

    // Check composer.json
    $composerJsonPath = $baseDir . '/composer.json';
    if (file_exists($composerJsonPath)) {
        output("✅ ملف composer.json موجود", 'success');
    } else {
        output("❌ ملف composer.json غير موجود!", 'error');
        die("</div></body></html>");
    }

    echo "<hr><h3>⚠️ تحذير أمني</h3>";
    echo "<p class='warning' style='padding: 15px;'><strong>مهم جداً:</strong> احذف هذا الملف فوراً بعد الانتهاء من التثبيت!</p>";
    echo "<pre>rm public/install-composer.php</pre>";

} elseif ($action === 'install' || $action === 'reinstall') {

    output("🚀 بدء عملية التثبيت...", 'step');

    // Step 1: Download composer if not exists
    if (!file_exists($composerPath)) {
        output("📥 جاري تنزيل Composer...", 'step');

        $setupScript = file_get_contents('https://getcomposer.org/installer');
        if ($setupScript === false) {
            output("❌ فشل تنزيل Composer installer", 'error');
            die("</div></body></html>");
        }

        file_put_contents($baseDir . '/composer-setup.php', $setupScript);

        // Run composer setup
        $output = [];
        $returnCode = 0;
        exec("cd " . escapeshellarg($baseDir) . " && php composer-setup.php 2>&1", $output, $returnCode);

        if ($returnCode === 0) {
            output("✅ تم تنزيل Composer بنجاح", 'success');
            echo "<pre>" . implode("\n", $output) . "</pre>";
            unlink($baseDir . '/composer-setup.php');
        } else {
            output("❌ فشل تنزيل Composer", 'error');
            echo "<pre>" . implode("\n", $output) . "</pre>";
            die("</div></body></html>");
        }
    }

    // Step 2: Delete vendor if reinstalling
    if ($action === 'reinstall' && is_dir($vendorPath)) {
        output("🗑️ جاري حذف المجلد vendor القديم...", 'step');

        function deleteDirectory($dir) {
            if (!is_dir($dir)) return false;
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file) {
                $path = $dir . '/' . $file;
                is_dir($path) ? deleteDirectory($path) : unlink($path);
            }
            return rmdir($dir);
        }

        if (deleteDirectory($vendorPath)) {
            output("✅ تم حذف vendor", 'success');
        } else {
            output("⚠️ فشل حذف vendor - المتابعة على أي حال", 'warning');
        }
    }

    // Step 3: Run composer install
    output("📦 جاري تثبيت المكتبات... (قد يستغرق 2-5 دقائق)", 'step');

    $composerCmd = "cd " . escapeshellarg($baseDir) . " && php composer.phar install --no-dev --optimize-autoloader --no-interaction 2>&1";

    output("تنفيذ الأمر:", 'step');
    echo "<pre>{$composerCmd}</pre>";

    $output = [];
    $returnCode = 0;
    exec($composerCmd, $output, $returnCode);

    echo "<pre style='max-height: 400px; overflow-y: auto;'>" . implode("\n", $output) . "</pre>";

    if ($returnCode === 0) {
        output("✅ تم تثبيت المكتبات بنجاح!", 'success');

        // Step 4: Generate APP_KEY
        output("🔑 جاري توليد APP_KEY...", 'step');
        exec("cd " . escapeshellarg($baseDir) . " && php artisan key:generate --force 2>&1", $keyOutput, $keyReturn);

        if ($keyReturn === 0) {
            output("✅ تم توليد APP_KEY", 'success');
        } else {
            output("⚠️ تحذير: فشل توليد APP_KEY - قد تحتاج لتنفيذه يدوياً", 'warning');
            echo "<pre>" . implode("\n", $keyOutput) . "</pre>";
        }

        // Step 5: Clear caches
        output("🧹 جاري مسح الكاش...", 'step');
        $cacheCommands = [
            'config:clear',
            'cache:clear',
            'route:clear',
            'view:clear'
        ];

        foreach ($cacheCommands as $cmd) {
            exec("cd " . escapeshellarg($baseDir) . " && php artisan {$cmd} 2>&1", $cacheOutput);
        }
        output("✅ تم مسح الكاش", 'success');

        // Success message
        echo "<hr><div class='step success' style='padding: 20px; font-size: 18px;'>";
        echo "<h2>🎉 تم التثبيت بنجاح!</h2>";
        echo "<p>يمكنك الآن زيارة موقعك:</p>";
        echo "<a href='https://www.mediapro.social' class='btn'>🏠 الذهاب للصفحة الرئيسية</a>";
        echo "<a href='https://www.mediapro.social/admin/settings' class='btn'>⚙️ صفحة الإعدادات</a>";
        echo "</div>";

        echo "<hr><div class='step error' style='padding: 20px;'>";
        echo "<h3>⚠️ احذف هذا الملف فوراً!</h3>";
        echo "<p>لأسباب أمنية، يجب حذف هذا الملف الآن:</p>";
        echo "<pre>rm public/install-composer.php</pre>";
        echo "<p>أو احذفه من File Manager في Plesk</p>";
        echo "</div>";

    } else {
        output("❌ فشل تثبيت المكتبات", 'error');
        echo "<p>جرب تنفيذ الأمر يدوياً عبر SSH:</p>";
        echo "<pre>cd httpdocs\ncomposer install --no-dev --optimize-autoloader</pre>";
    }
}

echo "</div></body></html>";
?>
