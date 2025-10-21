#!/bin/bash

# MediaPro Laravel Deployment Script for Plesk
# This script deploys the backend-laravel folder to Plesk server

echo "🚀 Starting MediaPro Backend Deployment..."
echo "=========================================="

# Configuration
REPO_DIR="/var/www/vhosts/mediapro.social/mediapro-repo"
APP_DIR="/var/www/vhosts/mediapro.social/httpdocs"
LARAVEL_SOURCE="$REPO_DIR/backend-laravel"

# Step 1: Pull latest changes from Git
echo "📥 Step 1: Pulling latest changes from GitHub..."
cd $REPO_DIR
git fetch origin main
git reset --hard origin/main
echo "✅ Git pull completed"

# Step 2: Copy Laravel files to httpdocs
echo "📂 Step 2: Copying Laravel files..."
rsync -av --delete \
  --exclude='.git' \
  --exclude='node_modules' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  --exclude='.env' \
  $LARAVEL_SOURCE/ $APP_DIR/

echo "✅ Files copied successfully"

# Step 3: Set correct permissions
echo "🔒 Step 3: Setting permissions..."
cd $APP_DIR
chown -R mediapro-user:psacln .
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
echo "✅ Permissions set"

# Step 4: Install Composer dependencies
echo "📦 Step 4: Installing Composer dependencies..."
cd $APP_DIR
composer install --no-dev --optimize-autoloader --no-interaction
echo "✅ Composer dependencies installed"

# Step 5: Clear and cache Laravel
echo "🧹 Step 5: Clearing and caching Laravel..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Laravel caches cleared and rebuilt"

# Step 6: Run migrations (optional - remove if not needed)
# echo "🗄️ Step 6: Running database migrations..."
# php artisan migrate --force
# echo "✅ Migrations completed"

# Step 7: Restart PHP-FPM
echo "🔄 Step 7: Restarting PHP-FPM..."
systemctl restart plesk-php81-fpm
echo "✅ PHP-FPM restarted"

echo "=========================================="
echo "✅ Deployment completed successfully!"
echo "🌐 Your app is live at: https://www.mediapro.social"
echo "=========================================="
