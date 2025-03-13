#!/bin/bash

# Configuration
DB_NAME="financial_assistant_db"
DB_USER="quickrec_user"
DB_PASSWORD="Summer123!"
BACKUP_DIR="https://drive.google.com/drive/folders/1rlQwx4cqaaNH9RgQMr4Uz7lNtJsgXymf"
DATE=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="$BACKUP_DIR/db_backup_$DATE.sql"
RCLONE_REMOTE="quickrecord_db_backup:https://drive.google.com/drive/folders/1rlQwx4cqaaNH9RgQMr4Uz7lNtJsgXymf" # Replace 'gdrive' with your rclone remote name

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

# Step 1: Backup the database
echo "Backing up the database..."
mysqldump -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" > "$BACKUP_FILE"

if [ $? -ne 0 ]; then
    echo "Database backup failed!"
    exit 1
fi
echo "Database backup successful: $BACKUP_FILE"
