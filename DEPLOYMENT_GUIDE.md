# 🚀 Media Pro - Deployment & Setup Guide

## Table of Contents
1. [System Requirements](#system-requirements)
2. [Local Development Setup](#local-development-setup)
3. [Environment Configuration](#environment-configuration)
4. [Database Setup](#database-setup)
5. [Testing](#testing)
6. [Production Deployment](#production-deployment)
7. [API Testing](#api-testing)
8. [Troubleshooting](#troubleshooting)

---

## System Requirements

### Minimum Requirements
- **PHP**: 8.1 or higher
- **Composer**: 2.x
- **Node.js**: 16.x or higher
- **NPM**: 8.x or higher
- **Database**: MySQL 8.0+ / PostgreSQL 13+ / SQLite 3.35+
- **Web Server**: Apache 2.4+ / Nginx 1.18+

### Recommended for Production
- **PHP**: 8.2+
- **Memory**: 512MB minimum, 1GB+ recommended
- **Storage**: 1GB+ for application and logs
- **Redis**: For caching and queues (optional but recommended)

---

## Local Development Setup

### Step 1: Clone the Repository

```bash
cd backend-laravel
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install Node Dependencies

```bash
npm install
```

### Step 4: Environment Setup

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 5: Configure Environment Variables

Edit `.env` file with your settings:

```env
# Application
APP_NAME="Media Pro"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (Choose one)
# Option 1: SQLite (Default for development)
DB_CONNECTION=sqlite

# Option 2: MySQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=media_pro
# DB_USERNAME=root
# DB_PASSWORD=

# Option 3: PostgreSQL
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=media_pro
# DB_USERNAME=postgres
# DB_PASSWORD=

# Mail Configuration (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="noreply@mediapro.com"
MAIL_FROM_NAME="${APP_NAME}"

# Optional: Redis (for caching and queues)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Optional: AWS S3 (for file storage)
# AWS_ACCESS_KEY_ID=
# AWS_SECRET_ACCESS_KEY=
# AWS_DEFAULT_REGION=us-east-1
# AWS_BUCKET=
```

### Step 6: Database Setup

```bash
# Create database tables
php artisan migrate

# Seed initial data (subscription plans, admin user)
php artisan db:seed
```

**Default Admin Credentials:**
- Email: `admin@admin.com`
- Password: `admin123`

⚠️ **Important**: Change the admin password immediately after first login!

### Step 7: Create Storage Link

```bash
# Create symbolic link for public storage
php artisan storage:link
```

### Step 8: Compile Frontend Assets

```bash
# Development mode (with hot reload)
npm run dev

# Production build
npm run build
```

### Step 9: Start Development Server

```bash
# Start Laravel development server
php artisan serve

# The application will be available at:
# http://127.0.0.1:8000
```

---

## Environment Configuration

### Database Options

#### SQLite (Easiest for development)
```env
DB_CONNECTION=sqlite
```

SQLite file will be created at: `database/database.sqlite`

#### MySQL Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE media_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=media_pro
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### PostgreSQL Setup
```bash
# Create database
psql -U postgres
CREATE DATABASE media_pro;
\q
```

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=media_pro
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### File Storage Configuration

#### Local Storage (Default)
```env
FILESYSTEM_DISK=local
```

#### AWS S3 Storage
```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket_name
```

### Cache & Queue Configuration

#### Database Driver (Default)
```env
CACHE_STORE=database
QUEUE_CONNECTION=database
```

#### Redis Driver (Recommended for production)
```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

To use Redis, install the PHP Redis extension:
```bash
# Ubuntu/Debian
sudo apt-get install php-redis

# macOS (Homebrew)
brew install php-redis

# Windows
# Download from: https://pecl.php.net/package/redis
```

---

## Database Setup

### Running Migrations

```bash
# Run all migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Reset database (WARNING: Deletes all data)
php artisan migrate:fresh

# Reset and seed
php artisan migrate:fresh --seed
```

### Database Seeding

```bash
# Seed all data
php artisan db:seed

# Seed specific seeder
php artisan db:seed --class=SubscriptionPlanSeeder
```

### Database Schema Overview

The application includes these tables:
- `users` - User accounts
- `posts` - Social media posts
- `social_accounts` - Connected social media accounts
- `analytics` - Post analytics data
- `brand_kits` - Brand identity kits
- `subscription_plans` - Available subscription plans
- `subscriptions` - User subscriptions
- `payments` - Payment transactions
- `translations` - Multi-language support

---

## Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/AuthTest.php

# Run with coverage
php artisan test --coverage
```

### API Testing with Postman

1. **Import Collection**: Import `Media_Pro_API.postman_collection.json` into Postman
2. **Set Environment Variables**:
   - `base_url`: http://127.0.0.1:8000
   - `auth_token`: (auto-set after login)
3. **Test Flow**:
   - Register/Login → Get token
   - Test other endpoints with token

### Manual API Testing

```bash
# Test registration
curl -X POST http://127.0.0.1:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"password123","password_confirmation":"password123"}'

# Test login
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'

# Test authenticated endpoint
curl -X GET http://127.0.0.1:8000/api/auth/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## Production Deployment

### Pre-Deployment Checklist

- [ ] Update `.env` with production values
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure production database
- [ ] Set up proper mail service
- [ ] Configure file storage (S3 recommended)
- [ ] Set up Redis for caching
- [ ] Configure SSL certificate
- [ ] Set up automated backups
- [ ] Configure monitoring/logging

### Step 1: Server Setup

#### Ubuntu/Debian Server

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2 and extensions
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-pgsql \
  php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip \
  php8.2-gd php8.2-redis php8.2-bcmath

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Nginx
sudo apt install -y nginx

# Install MySQL
sudo apt install -y mysql-server

# Install Redis
sudo apt install -y redis-server
```

### Step 2: Application Deployment

```bash
# Clone repository
cd /var/www
git clone your-repo-url media-pro
cd media-pro/backend-laravel

# Set permissions
sudo chown -R www-data:www-data /var/www/media-pro
sudo chmod -R 755 /var/www/media-pro

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Set up environment
cp .env.example .env
php artisan key:generate

# Configure .env for production
nano .env

# Run migrations
php artisan migrate --force

# Seed initial data
php artisan db:seed --force

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
php artisan storage:link
```

### Step 3: Nginx Configuration

Create file: `/etc/nginx/sites-available/media-pro`

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/media-pro/backend-laravel/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # File upload size limits
    client_max_body_size 20M;
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/media-pro /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Step 4: SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal is configured automatically
# Test renewal
sudo certbot renew --dry-run
```

### Step 5: Queue Worker Setup

Create file: `/etc/systemd/system/media-pro-worker.service`

```ini
[Unit]
Description=Media Pro Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/media-pro/backend-laravel/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

Enable and start:
```bash
sudo systemctl enable media-pro-worker
sudo systemctl start media-pro-worker
sudo systemctl status media-pro-worker
```

### Step 6: Scheduled Tasks (Cron)

```bash
# Edit crontab for www-data user
sudo crontab -u www-data -e

# Add this line:
* * * * * cd /var/www/media-pro/backend-laravel && php artisan schedule:run >> /dev/null 2>&1
```

### Step 7: Monitoring & Logging

```bash
# Set up log rotation
sudo nano /etc/logrotate.d/media-pro

# Add:
/var/www/media-pro/backend-laravel/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

---

## API Testing

### Using Postman

1. Import `Media_Pro_API.postman_collection.json`
2. Set environment variable `base_url` to your API URL
3. Start with Authentication → Register or Login
4. Token is automatically saved to environment
5. Test other endpoints

### Using cURL

See examples in [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

---

## Troubleshooting

### Common Issues

#### 1. Permission Issues

```bash
# Fix storage and cache permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### 2. 500 Internal Server Error

```bash
# Check error logs
tail -f storage/logs/laravel.log

# Clear and rebuild cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
```

#### 3. Database Connection Issues

```bash
# Test database connection
php artisan tinker
DB::connection()->getPdo();
```

#### 4. Queue Not Processing

```bash
# Check queue worker status
sudo systemctl status media-pro-worker

# Restart queue worker
sudo systemctl restart media-pro-worker

# View queue jobs
php artisan queue:work --once
```

#### 5. Storage Link Missing

```bash
# Recreate storage link
php artisan storage:link
```

#### 6. File Upload Issues

Check `php.ini` settings:
```ini
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.2-fpm
```

### Debug Mode

**For Development Only:**

```env
APP_DEBUG=true
APP_ENV=local
```

**Never enable debug mode in production!**

### Getting Help

- Check logs: `storage/logs/laravel.log`
- Enable query logging in `.env`:
  ```env
  DB_LOG_QUERIES=true
  ```
- Use Laravel Telescope for debugging (dev only)
- Check Nginx error logs: `/var/log/nginx/error.log`
- Check PHP-FPM logs: `/var/log/php8.2-fpm.log`

---

## Performance Optimization

### Production Optimization Commands

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Enable OPcache in php.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
```

### Database Optimization

```bash
# Add indexes to frequently queried columns
# Already optimized in migrations

# Enable query caching in MySQL
# Add to my.cnf:
query_cache_type = 1
query_cache_size = 128M
```

### Caching Strategy

```bash
# Use Redis for cache and sessions in .env:
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

---

## Backup Strategy

### Database Backup

```bash
# MySQL backup
mysqldump -u username -p media_pro > backup_$(date +%Y%m%d).sql

# PostgreSQL backup
pg_dump media_pro > backup_$(date +%Y%m%d).sql

# Automated daily backup script
sudo nano /usr/local/bin/backup-media-pro.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/media-pro"
DATE=$(date +%Y%m%d_%H%M%S)

# Database backup
mysqldump -u username -p'password' media_pro | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# File backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/media-pro/backend-laravel/storage

# Keep only last 30 days
find $BACKUP_DIR -type f -mtime +30 -delete
```

```bash
sudo chmod +x /usr/local/bin/backup-media-pro.sh

# Add to crontab
sudo crontab -e
# Add: 0 2 * * * /usr/local/bin/backup-media-pro.sh
```

---

## Security Best Practices

1. **Always use HTTPS** in production
2. **Keep secrets in `.env`** - never commit to git
3. **Update dependencies** regularly:
   ```bash
   composer update
   npm update
   ```
4. **Enable rate limiting** (already configured in routes)
5. **Use strong passwords** for database and admin accounts
6. **Regular backups** of database and files
7. **Monitor logs** for suspicious activity
8. **Keep PHP and Laravel updated**
9. **Use firewall** to restrict access
10. **Enable CSRF protection** (already enabled)

---

## Maintenance Mode

```bash
# Enable maintenance mode
php artisan down

# Enable with custom message
php artisan down --message="Scheduled maintenance in progress"

# Allow specific IPs
php artisan down --allow=203.0.113.1 --allow=203.0.113.2

# Disable maintenance mode
php artisan up
```

---

## Updating the Application

```bash
# Pull latest code
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Run migrations
php artisan migrate --force

# Clear and rebuild cache
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart media-pro-worker
```

---

## Support

For issues and questions:
- Documentation: Check `README_AR.md` and `API_DOCUMENTATION.md`
- Email: support@mediapro.com
- GitHub Issues: [Create an issue](https://github.com/yourrepo/issues)

---

**Happy Deploying! 🚀**
