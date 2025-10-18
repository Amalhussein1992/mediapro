#!/bin/bash

###############################################################################
# Setup Cron Jobs
# Social Media Manager - Production
###############################################################################
# This script sets up automated cron jobs for backups and maintenance
# Usage: sudo ./setup-cron.sh
###############################################################################

echo "Setting up cron jobs for Social Media Manager..."

# Get the current script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Create cron job entries
CRON_JOBS=$(cat <<EOF
# Social Media Manager - Automated Tasks

# Daily database backup at 2 AM
0 2 * * * $SCRIPT_DIR/backup-database.sh >> /var/log/smm-backup-db.log 2>&1

# Weekly files backup at 3 AM every Sunday
0 3 * * 0 $SCRIPT_DIR/backup-files.sh >> /var/log/smm-backup-files.log 2>&1

# Laravel scheduler (every minute)
* * * * * cd /var/www/social-media-manager/backend-laravel && php artisan schedule:run >> /var/log/smm-scheduler.log 2>&1

# Weekly database optimization on Monday at 4 AM
0 4 * * 1 mysql -u root -p < $SCRIPT_DIR/../database/scripts/optimize-database.sql >> /var/log/smm-optimize.log 2>&1

# Clear Laravel cache daily at 1 AM
0 1 * * * cd /var/www/social-media-manager/backend-laravel && php artisan cache:clear >> /var/log/smm-cache.log 2>&1

# Clean up old logs weekly
0 5 * * 0 find /var/www/social-media-manager/backend-laravel/storage/logs -name "*.log" -type f -mtime +30 -delete

EOF
)

# Add to crontab
echo "$CRON_JOBS" | crontab -

echo "✅ Cron jobs installed successfully!"
echo ""
echo "Current crontab:"
crontab -l
echo ""
echo "Log files will be created at:"
echo "  - /var/log/smm-backup-db.log"
echo "  - /var/log/smm-backup-files.log"
echo "  - /var/log/smm-scheduler.log"
echo "  - /var/log/smm-optimize.log"
echo "  - /var/log/smm-cache.log"
