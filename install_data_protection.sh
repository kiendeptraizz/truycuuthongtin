#!/bin/bash
# install_data_protection.sh
# Comprehensive Data Protection System Installation Script

echo "🛡️ Installing Comprehensive Data Protection System..."
echo "=================================================="

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_step() {
    echo -e "${BLUE}[STEP]${NC} $1"
}

# Check if Laravel project
if [ ! -f "artisan" ]; then
    print_error "This script must be run from the Laravel project root directory"
    exit 1
fi

print_step "1. Creating backup directory structure..."
# Create backup directories
mkdir -p storage/backups/{hourly,daily,weekly,monthly,emergency,test}
mkdir -p storage/logs/alerts
mkdir -p storage/recovery
mkdir -p storage/temp

print_status "Backup directories created successfully"

print_step "2. Setting directory permissions..."
# Set permissions
chmod 755 storage/backups
chmod 755 storage/logs/alerts
chmod 755 storage/recovery
chmod 755 storage/temp

# Set recursive permissions for backup subdirectories
find storage/backups -type d -exec chmod 755 {} \;
find storage/logs -type d -exec chmod 755 {} \;

print_status "Directory permissions set successfully"

print_step "3. Checking environment configuration..."
# Check if .env file exists
if [ ! -f ".env" ]; then
    print_warning ".env file not found, copying from .env.example"
    cp .env.example .env
fi

# Generate backup encryption key if not exists
if ! grep -q "BACKUP_ENCRYPTION_KEY" .env; then
    print_step "4. Generating backup encryption key..."
    BACKUP_KEY=$(php artisan key:generate --show | sed 's/base64://')
    echo "BACKUP_ENCRYPTION_KEY=base64:${BACKUP_KEY}" >> .env
    print_status "Backup encryption key generated and added to .env"
else
    print_status "Backup encryption key already exists in .env"
fi

# Add data protection environment variables if not present
print_step "5. Adding data protection environment variables..."

ENV_VARS=(
    "BACKUP_ENABLED=true"
    "BACKUP_MAX_SIZE_GB=10"
    "BACKUP_COMPRESSION=true"
    "BACKUP_ENCRYPTION=true"
    "BACKUP_EMAIL_ALERTS=true"
    "BACKUP_ALERT_EMAILS=admin@yourbusiness.com"
    "BACKUP_DESKTOP_ALERTS=true"
    "BACKUP_VERIFICATION=true"
    "BACKUP_TEST_RESTORE=false"
    "DB_FOREIGN_KEY_CHECKS=true"
    "DB_STRICT_MODE=true"
    "DB_TRANSACTION_ISOLATION=READ_COMMITTED"
    "HEALTH_CHECK_ENABLED=true"
    "HEALTH_CHECK_INTERVAL=15"
    "STORAGE_WARNING_THRESHOLD=80"
    "STORAGE_CRITICAL_THRESHOLD=90"
    "ALERT_EMAIL_ENABLED=true"
    "ALERT_DESKTOP_ENABLED=true"
)

for var in "${ENV_VARS[@]}"; do
    key=$(echo $var | cut -d'=' -f1)
    if ! grep -q "^${key}=" .env; then
        echo "$var" >> .env
        print_status "Added $key to .env"
    else
        print_status "$key already exists in .env"
    fi
done

print_step "6. Installing required PHP packages..."
# Install required packages
composer require --dev phpunit/phpunit --quiet
composer require laravel/tinker --quiet

print_status "Required packages installed"

print_step "7. Creating configuration files..."
# Create backup configuration file
cat > config/backup.php << 'EOF'
<?php

return [
    'enabled' => env('BACKUP_ENABLED', true),
    
    'storage' => [
        'path' => storage_path('backups'),
        'max_size_gb' => env('BACKUP_MAX_SIZE_GB', 10),
        'compression' => env('BACKUP_COMPRESSION', true),
        'encryption' => env('BACKUP_ENCRYPTION', true),
    ],

    'retention' => [
        'hourly' => 24,    // Keep 24 hourly backups
        'daily' => 30,     // Keep 30 daily backups
        'weekly' => 12,    // Keep 12 weekly backups
        'monthly' => 12,   // Keep 12 monthly backups
    ],

    'alerts' => [
        'email' => [
            'enabled' => env('BACKUP_EMAIL_ALERTS', true),
            'recipients' => explode(',', env('BACKUP_ALERT_EMAILS', '')),
        ],
        'desktop' => [
            'enabled' => env('BACKUP_DESKTOP_ALERTS', true),
        ],
    ],

    'verification' => [
        'enabled' => env('BACKUP_VERIFICATION', true),
        'checksum_algorithm' => 'sha256',
        'test_restore' => env('BACKUP_TEST_RESTORE', false),
    ],

    'monitoring' => [
        'health_check_interval' => env('HEALTH_CHECK_INTERVAL', 15), // minutes
        'storage_warning_threshold' => env('STORAGE_WARNING_THRESHOLD', 80), // percentage
        'storage_critical_threshold' => env('STORAGE_CRITICAL_THRESHOLD', 90), // percentage
    ],
];
EOF

print_status "Backup configuration file created"

print_step "8. Creating logging configuration..."
# Add backup logging channel to config/logging.php
if ! grep -q "backup" config/logging.php; then
    # Create backup of original logging config
    cp config/logging.php config/logging.php.backup
    
    # Add backup logging channels
    sed -i "/channels.*=>/a\\
        'backup' => [\\
            'driver' => 'single',\\
            'path' => storage_path('logs/backup.log'),\\
            'level' => 'info',\\
        ],\\
\\
        'alerts' => [\\
            'driver' => 'single',\\
            'path' => storage_path('logs/alerts.log'),\\
            'level' => 'warning',\\
        ],\\
\\
        'critical' => [\\
            'driver' => 'single',\\
            'path' => storage_path('logs/critical.log'),\\
            'level' => 'emergency',\\
        ]," config/logging.php
    
    print_status "Logging configuration updated"
else
    print_status "Backup logging channels already configured"
fi

print_step "9. Creating initial backup..."
# Create initial test backup to verify system
php artisan backup:automated --type=test 2>/dev/null || print_warning "Initial backup creation will be available after implementing backup commands"

print_step "10. Setting up file monitoring..."
# Create .gitignore entries for backup files
if [ -f ".gitignore" ]; then
    if ! grep -q "storage/backups" .gitignore; then
        echo "" >> .gitignore
        echo "# Data Protection System" >> .gitignore
        echo "storage/backups/*" >> .gitignore
        echo "storage/logs/backup.log" >> .gitignore
        echo "storage/logs/alerts.log" >> .gitignore
        echo "storage/logs/critical.log" >> .gitignore
        echo "storage/recovery/*" >> .gitignore
        echo "storage/temp/*" >> .gitignore
        print_status "Added backup files to .gitignore"
    fi
fi

print_step "11. Creating quick access scripts..."
# Create quick backup script
cat > backup_now.sh << 'EOF'
#!/bin/bash
echo "🔄 Creating manual backup..."
php artisan backup:automated --type=manual --compress --verify
echo "✅ Manual backup completed"
EOF

chmod +x backup_now.sh

# Create health check script
cat > health_check.sh << 'EOF'
#!/bin/bash
echo "🔍 Running system health check..."
php artisan db:health-check --alert
echo "✅ Health check completed"
EOF

chmod +x health_check.sh

print_status "Quick access scripts created"

print_step "12. Verifying installation..."
# Check if all directories exist
REQUIRED_DIRS=(
    "storage/backups/hourly"
    "storage/backups/daily"
    "storage/backups/weekly"
    "storage/backups/monthly"
    "storage/backups/emergency"
    "storage/logs/alerts"
    "storage/recovery"
)

ALL_DIRS_OK=true
for dir in "${REQUIRED_DIRS[@]}"; do
    if [ ! -d "$dir" ]; then
        print_error "Directory $dir was not created"
        ALL_DIRS_OK=false
    fi
done

# Check if configuration files exist
REQUIRED_FILES=(
    "config/backup.php"
    ".env"
    "backup_now.sh"
    "health_check.sh"
)

ALL_FILES_OK=true
for file in "${REQUIRED_FILES[@]}"; do
    if [ ! -f "$file" ]; then
        print_error "File $file was not created"
        ALL_FILES_OK=false
    fi
done

echo ""
echo "=================================================="
if [ "$ALL_DIRS_OK" = true ] && [ "$ALL_FILES_OK" = true ]; then
    print_status "✅ Data Protection System installed successfully!"
    echo ""
    echo "📋 Next steps:"
    echo "1. Update BACKUP_ALERT_EMAILS in .env with your email address"
    echo "2. Implement the backup commands in your Laravel application"
    echo "3. Test backup and recovery procedures: ./backup_now.sh"
    echo "4. Set up monitoring dashboard"
    echo "5. Train team on emergency procedures"
    echo ""
    echo "🚀 Quick commands:"
    echo "   Manual backup:    ./backup_now.sh"
    echo "   Health check:     ./health_check.sh"
    echo "   View logs:        tail -f storage/logs/backup.log"
    echo ""
    echo "📚 Documentation: comprehensive_data_protection_system.md"
else
    print_error "❌ Installation completed with errors. Please check the issues above."
    exit 1
fi
