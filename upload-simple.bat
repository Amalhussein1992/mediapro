@echo off
chcp 65001 >nul
cls
echo.
echo ========================================
echo   MediaPro - Upload to Plesk
echo ========================================
echo.

REM Create temp directory
echo Creating temporary directory...
if exist "temp-upload" rmdir /s /q "temp-upload"
mkdir "temp-upload"

echo.
echo [1/8] Copying app files...
xcopy /s /e /i /y /q "app" "temp-upload\app"

echo [2/8] Copying config files...
xcopy /s /e /i /y /q "config" "temp-upload\config"

echo [3/8] Copying database files...
xcopy /s /e /i /y /q "database" "temp-upload\database"

echo [4/8] Copying routes...
xcopy /s /e /i /y /q "routes" "temp-upload\routes"

echo [5/8] Copying public files...
xcopy /s /e /i /y /q "public" "temp-upload\public"

echo [6/8] Copying bootstrap and resources...
xcopy /s /e /i /y /q "bootstrap" "temp-upload\bootstrap"
xcopy /s /e /i /y /q "resources" "temp-upload\resources"
xcopy /s /e /i /y /q "lang" "temp-upload\lang"

echo [7/8] Copying root files...
copy /y "artisan" "temp-upload\" >nul 2>&1
copy /y "composer.json" "temp-upload\" >nul 2>&1
copy /y "composer.lock" "temp-upload\" >nul 2>&1
copy /y ".env.server" "temp-upload\.env" >nul 2>&1

REM Create root .htaccess
(
echo ^<IfModule mod_rewrite.c^>
echo     RewriteEngine On
echo     RewriteRule ^^^(.*^)$ public/$1 [L]
echo ^</IfModule^>
) > "temp-upload\.htaccess"

echo [8/8] Creating ZIP file...
powershell -NoProfile -ExecutionPolicy Bypass -Command "if(Test-Path 'mediapro-upload.zip'){Remove-Item 'mediapro-upload.zip' -Force}; Compress-Archive -Path 'temp-upload\*' -DestinationPath 'mediapro-upload.zip' -CompressionLevel Optimal"

echo.
echo Cleaning up...
rmdir /s /q "temp-upload"

echo.
echo ========================================
echo   SUCCESS - Files Ready!
echo ========================================
echo.
echo ZIP File: mediapro-upload.zip
echo.
echo NEXT STEPS:
echo.
echo 1. Open Plesk Panel for mediapro.social
echo.
echo 2. Go to File Manager
echo    - Navigate to httpdocs folder
echo    - Upload mediapro-upload.zip
echo    - Extract it (right-click ^> Extract)
echo.
echo 3. Change Document Root:
echo    - Hosting Settings ^> Document Root
echo    - Change to: httpdocs/public
echo.
echo 4. Run via SSH Terminal:
echo.
echo    cd /var/www/vhosts/mediapro.social/httpdocs
echo    composer install --no-dev --optimize-autoloader
echo    php artisan migrate --force
echo    php artisan optimize
echo    chmod -R 775 storage bootstrap/cache
echo.
echo 5. Test the site:
echo    https://mediapro.social/test.php
echo.
echo ========================================
echo.
pause
