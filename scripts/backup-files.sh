#!/bin/bash

###############################################################################
# Files Backup Script
# Social Media Manager - Production
###############################################################################
# This script creates automated backups of application files
# Usage: ./backup-files.sh
# Schedule with cron: 0 3 * * 0 /path/to/backup-files.sh (weekly on Sunday)
###############################################################################

# Configuration
BACKUP_DIR="/var/backups/social-media-manager/files"
DAYS_TO_KEEP=90
APP_DIR="/var/www/social-media-manager"

# Notification settings
NOTIFY_EMAIL="admin@yourdomain.com"
SEND_NOTIFICATION=true

# S3 Configuration (optional)
ENABLE_S3_BACKUP=false
S3_BUCKET="s3://your-bucket-name/file-backups"
AWS_PROFILE="default"

# Directories to backup
BACKUP_TARGETS=(
    "backend-laravel/storage/app"
    "backend-laravel/storage/logs"
    "backend-laravel/.env"
    "backend-laravel/public/uploads"
)

###############################################################################
# Script Start
###############################################################################

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Timestamp
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DATE=$(date +"%Y-%m-%d %H:%M:%S")
BACKUP_FILE="smm_files_${TIMESTAMP}.tar.gz"

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Log function
log() {
    echo -e "${GREEN}[${DATE}]${NC} $1"
}

error() {
    echo -e "${RED}[${DATE}] ERROR:${NC} $1" >&2
}

# Start backup
log "========================================="
log "Starting Files Backup"
log "========================================="

cd "$APP_DIR" || exit 1

# Create backup
log "Creating file backup archive..."
tar -czf "$BACKUP_DIR/$BACKUP_FILE" "${BACKUP_TARGETS[@]}" 2>/dev/null

if [ $? -eq 0 ]; then
    FILE_SIZE=$(du -h "$BACKUP_DIR/$BACKUP_FILE" | cut -f1)
    log "File backup created successfully (Size: $FILE_SIZE)"

    # Upload to S3 if enabled
    if [ "$ENABLE_S3_BACKUP" = true ]; then
        log "Uploading to S3..."
        aws s3 cp "$BACKUP_DIR/$BACKUP_FILE" "$S3_BUCKET/" --profile $AWS_PROFILE
    fi
else
    error "File backup failed"
    exit 1
fi

# Cleanup old backups
log "Cleaning up old backups..."
find "$BACKUP_DIR" -name "smm_files_*.tar.gz" -type f -mtime +$DAYS_TO_KEEP -delete

log "========================================="
log "Files backup completed"
log "========================================="

exit 0
