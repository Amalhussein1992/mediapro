# ========================================
# Upload Modified Files to Plesk Server
# MediaPro Social Media Manager
# ========================================

param(
    [string]$server = "mediapro.social",
    [string]$username = "",
    [string]$password = "",
    [string]$remotePath = "/httpdocs"
)

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  MediaPro - Upload to Plesk Server" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if WinSCP is installed
$winscpPath = "C:\Program Files (x86)\WinSCP\WinSCP.com"
if (-not (Test-Path $winscpPath)) {
    Write-Host "Error: WinSCP not found!" -ForegroundColor Red
    Write-Host "Please install WinSCP from: https://winscp.net/eng/download.php" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Alternative: Use FTP/SFTP manually" -ForegroundColor Yellow
    Write-Host "Files to upload are ready in the current directory" -ForegroundColor Green
    exit 1
}

# Get credentials if not provided
if ([string]::IsNullOrEmpty($username)) {
    $username = Read-Host "Enter FTP/SFTP username"
}

if ([string]::IsNullOrEmpty($password)) {
    $securePassword = Read-Host "Enter FTP/SFTP password" -AsSecureString
    $BSTR = [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($securePassword)
    $password = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto($BSTR)
}

Write-Host ""
Write-Host "Preparing files for upload..." -ForegroundColor Yellow

# Create temp directory for upload
$tempDir = ".\temp-upload"
if (Test-Path $tempDir) {
    Remove-Item -Path $tempDir -Recurse -Force
}
New-Item -ItemType Directory -Path $tempDir | Out-Null

# Copy essential files
Write-Host "Copying essential files..." -ForegroundColor Yellow

# Copy app folder
Copy-Item -Path ".\app" -Destination "$tempDir\app" -Recurse -Force

# Copy config folder
Copy-Item -Path ".\config" -Destination "$tempDir\config" -Recurse -Force

# Copy database folder
Copy-Item -Path ".\database" -Destination "$tempDir\database" -Recurse -Force

# Copy routes folder
Copy-Item -Path ".\routes" -Destination "$tempDir\routes" -Recurse -Force

# Copy public folder
Copy-Item -Path ".\public" -Destination "$tempDir\public" -Recurse -Force

# Copy bootstrap folder
Copy-Item -Path ".\bootstrap" -Destination "$tempDir\bootstrap" -Recurse -Force

# Copy resources folder
Copy-Item -Path ".\resources" -Destination "$tempDir\resources" -Recurse -Force

# Copy lang folder
Copy-Item -Path ".\lang" -Destination "$tempDir\lang" -Recurse -Force

# Copy root files
Copy-Item -Path ".\artisan" -Destination "$tempDir\artisan" -Force
Copy-Item -Path ".\composer.json" -Destination "$tempDir\composer.json" -Force
Copy-Item -Path ".\composer.lock" -Destination "$tempDir\composer.lock" -Force
Copy-Item -Path ".\.env.server" -Destination "$tempDir\.env" -Force

# Create .htaccess for root if Plesk requires it
@"
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
"@ | Out-File -FilePath "$tempDir\.htaccess" -Encoding utf8

Write-Host "Files prepared successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  IMPORTANT: Manual Steps Required" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Upload files from temp-upload folder to your Plesk server" -ForegroundColor Yellow
Write-Host "   - Use Plesk File Manager or FTP/SFTP client" -ForegroundColor Gray
Write-Host "   - Upload to: /httpdocs or your domain root" -ForegroundColor Gray
Write-Host ""
Write-Host "2. Via Plesk SSH Terminal, run:" -ForegroundColor Yellow
Write-Host "   cd /var/www/vhosts/mediapro.social/httpdocs" -ForegroundColor Green
Write-Host "   composer install --no-dev --optimize-autoloader" -ForegroundColor Green
Write-Host "   php artisan migrate --force" -ForegroundColor Green
Write-Host "   php artisan config:cache" -ForegroundColor Green
Write-Host "   php artisan route:cache" -ForegroundColor Green
Write-Host "   php artisan view:cache" -ForegroundColor Green
Write-Host "   chmod -R 775 storage bootstrap/cache" -ForegroundColor Green
Write-Host "   chown -R username:psacln storage bootstrap/cache" -ForegroundColor Green
Write-Host ""
Write-Host "3. Set Document Root in Plesk to: /httpdocs/public" -ForegroundColor Yellow
Write-Host ""
Write-Host "4. Enable .htaccess support (Apache & nginx)" -ForegroundColor Yellow
Write-Host ""
Write-Host "Files are ready in: $tempDir" -ForegroundColor Cyan
Write-Host ""

# Ask if user wants to create a ZIP file for manual upload
$createZip = Read-Host "Create ZIP file for easier upload? (y/n)"
if ($createZip -eq "y" -or $createZip -eq "Y") {
    $zipFile = ".\mediapro-upload-$(Get-Date -Format 'yyyy-MM-dd-HHmmss').zip"
    Write-Host "Creating ZIP file..." -ForegroundColor Yellow

    Compress-Archive -Path "$tempDir\*" -DestinationPath $zipFile -Force

    Write-Host "ZIP created: $zipFile" -ForegroundColor Green
    Write-Host "Upload this ZIP to Plesk File Manager and extract it" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Upload preparation complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
