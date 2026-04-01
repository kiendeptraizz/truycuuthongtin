#!/bin/bash

# ============================================
# Production Cleanup Script
# Xóa các file test/debug không cần thiết trước khi deploy
# ============================================

echo "🧹 Bắt đầu dọn dẹp các file không cần thiết..."

# Các file check_*.php
echo "Đang xóa các file check_*.php..."
rm -f check_*.php

# Các file test_*.php
echo "Đang xóa các file test_*.php..."
rm -f test_*.php

# Các file fix_*.php
echo "Đang xóa các file fix_*.php..."
rm -f fix_*.php

# Các file sync_*.php
echo "Đang xóa các file sync_*.php..."
rm -f sync_*.php

# Các file verify_*.php
echo "Đang xóa các file verify_*.php..."
rm -f verify_*.php

# Các file create_*.php (trừ artisan commands)
echo "Đang xóa các file create_*.php trong root..."
rm -f create_*.php

# Các file khác
echo "Đang xóa các file khác..."
rm -f list_*.php
rm -f compare_*.php
rm -f clean_*.php
rm -f detailed_*.php
rm -f smart_*.php
rm -f statistics_*.php
rm -f quick_restore.php
rm -f restore_database.php
rm -f add_family_groups.php
rm -f final_test.php

# Xóa các file .md không cần thiết (giữ README.md)
echo "Đang xóa các file .md không cần thiết..."
rm -f ASSIGN_SERVICE_FORM_UPDATE.md
rm -f AUTO_UPDATE_EXPIRED_SERVICES.md
rm -f DATABASE_RESTORE_REPORT.md
rm -f ENCODING_FIX_SUMMARY.md
rm -f SERVICE_PACKAGE_CHANGES.md
rm -f ZALO_MARKETING_README.md
rm -f check_form_validation.md

# Xóa file .bat
rm -f create_desktop_shortcut.bat

# Xóa thư mục backup (nếu muốn)
# rm -rf backup/

echo ""
echo "✅ Hoàn thành dọn dẹp!"
echo ""
echo "📋 Các bước tiếp theo:"
echo "1. composer install --optimize-autoloader --no-dev"
echo "2. php artisan config:cache"
echo "3. php artisan route:cache"
echo "4. php artisan view:cache"
echo "5. Xem file PRODUCTION_DEPLOYMENT_CHECKLIST.md để biết thêm chi tiết"

