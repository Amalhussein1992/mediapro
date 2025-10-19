#!/bin/bash

# MediaPro - Plesk Git Deployment Script for Laravel
# This script runs automatically after each git push

set -e

echo "========================================="
echo "MediaPro - Starting Deployment"
echo "========================================="

# Navigate to project root
cd /var/www/vhosts/mediapro.social/httpdocs

# Install/Update Composer dependencies (production mode)
echo "[1/7] Installing Composer dependencies..."
if [ -f "composer.json" ]; then
    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
    echo "✓ Composer dependencies installed"
else
    echo "⚠ Warning: composer.json not found"
fi

# Copy environment file if not exists
echo "[2/7] Checking environment file..."
if [ ! -f ".env" ]; then
    if [ -f ".env.server" ]; then
        cp .env.server .env
        echo "✓ .env file created from .env.server"
    else
        echo "⚠ Warning: .env file not found"
    fi
else
    echo "✓ .env file exists"
fi

# Clear all Laravel caches
echo "[3/7] Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo "✓ Caches cleared"

# Run database migrations
echo "[4/7] Running database migrations..."
php artisan migrate --force
echo "✓ Migrations completed"

# Rebuild caches
echo "[5/7] Building caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✓ Caches built"

# Create storage link if needed
echo "[6/7] Creating storage link..."
php artisan storage:link || echo "Storage link already exists"

# Fix permissions
echo "[7/7] Fixing permissions..."
chmod -R 775 storage bootstrap/cache
chown -R $USER:psacln storage bootstrap/cache || true
echo "✓ Permissions fixed"

echo "========================================="
echo "✓ Deployment Complete!"
echo "========================================="
echo "Site: https://mediapro.social"
echo "Time: $(date)"
echo "========================================="
