#!/bin/bash

# Configuration
DB_NAME="financial_assistant_db"
DB_USER="quickrec_user"
DB_PASSWORD="Summer123!"
BACKUP_DIR="drive/folders/1rlQwx4cqaaNH9RgQMr4Uz7lNtJsgXymf"
DATE=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="$BACKUP_DIR/db_backup_$DATE.sql"
RCLONE_REMOTE="quickrecord_db_backup:1rlQwx4cqaaNH9RgQMr4Uz7lNtJsgXymf" # Replace 'quickrecord_db_backup' rclone remote name, '1rlQwx4cqaaNH9RgQMr4Uz7lNtJsgXymf' google drive folder

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

# Check if backup file exists
if [ -f "$BACKUP_FILE" ]; then
    echo "Backup file exists: $BACKUP_FILE"
else
    echo "Backup file does not exist!"
    exit 1
fi

# Step 2: Upload the backup to Google Drive
echo "Uploading backup to Google Drive..."
rclone copy "$BACKUP_FILE" "$RCLONE_REMOTE/" --verbose

if [ $? -ne 0 ]; then
    echo "Upload to Google Drive failed!"
    exit 1
fi
echo "Upload successful!"

# Optional: Clean up local backup file (uncomment if desired)
 rm "$BACKUP_FILE"
