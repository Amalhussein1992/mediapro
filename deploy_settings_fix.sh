#!/bin/bash

# =============================================================================
# Deploy Settings Fix Script for MediaPro.social
# =============================================================================

echo "=================================="
echo "MediaPro.social - Settings Fix"
echo "=================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "ℹ $1"
}

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    print_warning "Running as root. Make sure file permissions are correct after deployment."
fi

# =============================================================================
# Step 1: Backup
# =============================================================================
echo ""
echo "Step 1: Creating Backups..."
echo "----------------------------"

# Backup .env
if [ -f .env ]; then
    BACKUP_DATE=$(date +%Y%m%d_%H%M%S)
    cp .env .env.backup.$BACKUP_DATE
    print_success ".env backed up to .env.backup.$BACKUP_DATE"
else
    print_error ".env file not found!"
    exit 1
fi

# Backup database (if mysql credentials are in .env)
DB_NAME=$(grep DB_DATABASE .env | cut -d '=' -f2 | tr -d '"' | tr -d "'")
DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f2 | tr -d '"' | tr -d "'")
DB_PASS=$(grep DB_PASSWORD .env | cut -d '=' -f2 | tr -d '"' | tr -d "'")

if [ ! -z "$DB_NAME" ] && [ ! -z "$DB_USER" ]; then
    print_info "Creating database backup..."
    if mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "backup_${DB_NAME}_${BACKUP_DATE}.sql" 2>/dev/null; then
        print_success "Database backed up to backup_${DB_NAME}_${BACKUP_DATE}.sql"
    else
        print_warning "Could not create database backup (might need manual backup)"
    fi
fi

# =============================================================================
# Step 2: Remove Duplicate Migration
# =============================================================================
echo ""
echo "Step 2: Checking for Duplicate Migrations..."
echo "---------------------------------------------"

DUPLICATE_MIGRATION="database/migrations/2025_10_18_094822_create_brand_kits_table.php"
if [ -f "$DUPLICATE_MIGRATION" ]; then
    rm "$DUPLICATE_MIGRATION"
    print_success "Removed duplicate migration: $DUPLICATE_MIGRATION"
else
    print_info "No duplicate migration found (already clean)"
fi

# =============================================================================
# Step 3: User Confirmation for APP_KEY Regeneration
# =============================================================================
echo ""
echo "Step 3: APP_KEY Regeneration"
echo "-----------------------------"
print_warning "IMPORTANT: Regenerating APP_KEY will log out all users!"
print_warning "All existing sessions and tokens will be invalidated."
echo ""
read -p "Do you want to proceed? (yes/no): " CONFIRM

if [ "$CONFIRM" != "yes" ]; then
    print_info "Deployment cancelled by user."
    exit 0
fi

# =============================================================================
# Step 4: Generate New APP_KEY
# =============================================================================
echo ""
echo "Step 4: Generating New APP_KEY..."
echo "-----------------------------------"

php artisan key:generate --force
if [ $? -eq 0 ]; then
    print_success "APP_KEY generated successfully"
else
    print_error "Failed to generate APP_KEY"
    exit 1
fi

# =============================================================================
# Step 5: Run Migrations (if any pending)
# =============================================================================
echo ""
echo "Step 5: Checking Migrations..."
echo "-------------------------------"

php artisan migrate:status
echo ""
read -p "Run pending migrations? (yes/no): " RUN_MIGRATE

if [ "$RUN_MIGRATE" = "yes" ]; then
    php artisan migrate --force
    if [ $? -eq 0 ]; then
        print_success "Migrations executed successfully"
    else
        print_error "Migration failed"
    fi
fi

# =============================================================================
# Step 6: Clear All Caches
# =============================================================================
echo ""
echo "Step 6: Clearing Caches..."
echo "--------------------------"

php artisan cache:clear
print_success "Cache cleared"

php artisan config:clear
print_success "Config cache cleared"

php artisan route:clear
print_success "Route cache cleared"

php artisan view:clear
print_success "View cache cleared"

# Rebuild caches
php artisan config:cache
print_success "Config cache rebuilt"

php artisan route:cache
print_success "Route cache rebuilt"

# =============================================================================
# Step 7: Fix Permissions
# =============================================================================
echo ""
echo "Step 7: Fixing Permissions..."
echo "------------------------------"

chmod 644 .env
print_success ".env permissions set to 644"

chmod -R 775 storage
print_success "storage/ permissions set to 775"

chmod -R 775 bootstrap/cache
print_success "bootstrap/cache/ permissions set to 775"

# Set ownership (if running as root or with sudo)
if [ "$EUID" -eq 0 ]; then
    chown -R www-data:www-data storage
    chown -R www-data:www-data bootstrap/cache
    print_success "Ownership set to www-data"
fi

# =============================================================================
# Step 8: Run Tests
# =============================================================================
echo ""
echo "Step 8: Running Basic Tests..."
echo "-------------------------------"

# Test database connection
print_info "Testing database connection..."
php artisan tinker --execute="echo DB::connection()->getDatabaseName(); exit();" 2>/dev/null
if [ $? -eq 0 ]; then
    print_success "Database connection OK"
else
    print_error "Database connection failed"
fi

# Test settings table
print_info "Testing app_settings table..."
SETTINGS_COUNT=$(php artisan tinker --execute="echo DB::table('app_settings')->count(); exit();" 2>/dev/null | tail -1)
if [ ! -z "$SETTINGS_COUNT" ]; then
    print_success "app_settings table accessible (found $SETTINGS_COUNT settings)"
else
    print_error "Could not access app_settings table"
fi

# =============================================================================
# Completion
# =============================================================================
echo ""
echo "=================================="
echo "Deployment Completed!"
echo "=================================="
echo ""
print_success "Settings fix has been deployed successfully"
echo ""
print_warning "Next Steps:"
echo "  1. Test login at: https://www.mediapro.social/api/auth/login"
echo "  2. Test settings at: https://www.mediapro.social/admin/settings"
echo "  3. All users need to login again (sessions invalidated)"
echo ""
print_info "Backup files created:"
echo "  - .env.backup.$BACKUP_DATE"
if [ ! -z "$DB_NAME" ]; then
    echo "  - backup_${DB_NAME}_${BACKUP_DATE}.sql"
fi
echo ""
print_info "To rollback, restore the .env backup:"
echo "  cp .env.backup.$BACKUP_DATE .env"
echo ""
