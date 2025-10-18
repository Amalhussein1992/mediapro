#!/bin/bash

###############################################################################
# Database Backup Script
# Social Media Manager - Production
###############################################################################
# This script creates automated backups of the MySQL database
# Usage: ./backup-database.sh
# Schedule with cron: 0 2 * * * /path/to/backup-database.sh
###############################################################################

# Configuration
BACKUP_DIR="/var/backups/social-media-manager/database"
DAYS_TO_KEEP=30
DB_HOST="localhost"
DB_PORT="3306"
DB_NAME="social_media_manager"
DB_USER="your_db_username"
DB_PASSWORD="your_db_password"

# Notification settings
NOTIFY_EMAIL="admin@yourdomain.com"
SEND_NOTIFICATION=true

# S3 Configuration (optional - for cloud backup)
ENABLE_S3_BACKUP=false
S3_BUCKET="s3://your-bucket-name/database-backups"
AWS_PROFILE="default"

###############################################################################
# Script Start
###############################################################################

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Timestamp
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DATE=$(date +"%Y-%m-%d %H:%M:%S")
BACKUP_FILE="smm_backup_${TIMESTAMP}.sql"
BACKUP_FILE_GZ="${BACKUP_FILE}.gz"

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

# Log function
log() {
    echo -e "${GREEN}[${DATE}]${NC} $1"
}

error() {
    echo -e "${RED}[${DATE}] ERROR:${NC} $1" >&2
}

warning() {
    echo -e "${YELLOW}[${DATE}] WARNING:${NC} $1"
}

# Function to send notification
send_notification() {
    local subject="$1"
    local message="$2"

    if [ "$SEND_NOTIFICATION" = true ]; then
        echo "$message" | mail -s "$subject" "$NOTIFY_EMAIL"
    fi
}

# Start backup
log "========================================="
log "Starting Database Backup"
log "========================================="
log "Database: $DB_NAME"
log "Backup Directory: $BACKUP_DIR"
log "Backup File: $BACKUP_FILE_GZ"

# Check if mysqldump is available
if ! command -v mysqldump &> /dev/null; then
    error "mysqldump command not found. Please install MySQL client tools."
    send_notification "Backup Failed" "mysqldump command not found on $(hostname)"
    exit 1
fi

# Perform the backup
log "Creating database backup..."

MYSQL_PWD=$DB_PASSWORD mysqldump \
    --host=$DB_HOST \
    --port=$DB_PORT \
    --user=$DB_USER \
    --single-transaction \
    --routines \
    --triggers \
    --events \
    --add-drop-database \
    --databases $DB_NAME \
    > "$BACKUP_DIR/$BACKUP_FILE"

# Check if backup was successful
if [ $? -eq 0 ]; then
    log "Database backup created successfully"

    # Compress the backup
    log "Compressing backup file..."
    gzip "$BACKUP_DIR/$BACKUP_FILE"

    if [ $? -eq 0 ]; then
        log "Backup compressed successfully"

        # Get file size
        FILE_SIZE=$(du -h "$BACKUP_DIR/$BACKUP_FILE_GZ" | cut -f1)
        log "Backup file size: $FILE_SIZE"

        # Upload to S3 if enabled
        if [ "$ENABLE_S3_BACKUP" = true ]; then
            log "Uploading backup to S3..."
            aws s3 cp "$BACKUP_DIR/$BACKUP_FILE_GZ" "$S3_BUCKET/" --profile $AWS_PROFILE

            if [ $? -eq 0 ]; then
                log "Backup uploaded to S3 successfully"
            else
                warning "Failed to upload backup to S3"
            fi
        fi

        # Success notification
        send_notification \
            "Database Backup Successful" \
            "Database backup completed successfully on $(hostname)\n\nBackup File: $BACKUP_FILE_GZ\nSize: $FILE_SIZE\nTimestamp: $DATE"

    else
        error "Failed to compress backup file"
        send_notification "Backup Warning" "Backup created but compression failed on $(hostname)"
    fi
else
    error "Database backup failed"
    send_notification "Backup Failed" "Database backup failed on $(hostname)\n\nTimestamp: $DATE"
    exit 1
fi

# Clean up old backups
log "Cleaning up old backups (keeping last $DAYS_TO_KEEP days)..."
find "$BACKUP_DIR" -name "smm_backup_*.sql.gz" -type f -mtime +$DAYS_TO_KEEP -delete
CLEANED=$(find "$BACKUP_DIR" -name "smm_backup_*.sql.gz" -type f -mtime +$DAYS_TO_KEEP 2>/dev/null | wc -l)
log "Removed $CLEANED old backup files"

# Show disk usage
log "Current backup directory size:"
du -sh "$BACKUP_DIR"

# List recent backups
log "Recent backups:"
ls -lh "$BACKUP_DIR" | tail -5

log "========================================="
log "Backup completed successfully"
log "========================================="

exit 0
