# 🚀 Production Deployment Checklist

## Tổng quan
Checklist này giúp bạn deploy dự án Laravel "Truy Cứu Thông Tin" lên môi trường production một cách an toàn và tối ưu.

---

## 🔐 Thông tin đăng nhập Admin mặc định

| Thông tin | Giá trị |
|-----------|---------|
| **URL** | `/login` |
| **Email** | `admin@truycuuthongtin.com` |
| **Mật khẩu** | `admin123456` |

⚠️ **QUAN TRỌNG**: Đổi mật khẩu ngay sau khi đăng nhập lần đầu tại `/change-password`

---

## 1. ⚙️ Cấu hình Environment (.env)

### Bắt buộc thay đổi:
```env
# Chuyển sang production mode
APP_ENV=production
APP_DEBUG=false

# Tạo key mới cho production
APP_KEY=base64:... (chạy: php artisan key:generate)

# URL production
APP_URL=https://yourdomain.com

# Database production
DB_CONNECTION=mysql
DB_HOST=your_production_db_host
DB_PORT=3306
DB_DATABASE=your_production_db
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Mail configuration (nếu cần gửi email)
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Session & Cache (khuyến nghị dùng redis cho production)
SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=database
```

---

## 2. 🔒 Bảo mật

### ✅ Checklist bảo mật:
- [ ] **APP_DEBUG=false** - Tắt debug mode
- [ ] **Đổi mật khẩu database** - Không dùng mật khẩu mặc định
- [ ] **HTTPS** - Cài đặt SSL certificate
- [ ] **Xóa các file test** - Xóa các file .php không cần thiết trong root
- [ ] **Bảo vệ thư mục storage** - Đảm bảo không public
- [ ] **CSRF Protection** - Đã có sẵn trong Laravel
- [ ] **Rate Limiting** - Cân nhắc thêm cho API

### File cần xóa trước khi deploy:
```bash
# Xóa các file test/debug không cần thiết
rm -f check_*.php
rm -f test_*.php
rm -f fix_*.php
rm -f sync_*.php
rm -f verify_*.php
rm -f create_*.php
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
```

---

## 3. 🗄️ Database

### Trước khi deploy:
```bash
# Export database từ local
mysqldump -u root -p truycuuthongtin > backup_production.sql

# Import vào production server
mysql -u username -p production_db < backup_production.sql
```

### Sau khi deploy:
```bash
# Chạy migrations
php artisan migrate --force

# Nếu cần seed data
php artisan db:seed --force
```

---

## 4. 🔧 Optimization Commands

### Chạy trên production server:
```bash
# 1. Cài đặt dependencies (không có dev packages)
composer install --optimize-autoloader --no-dev

# 2. Tối ưu hóa Laravel
php artisan config:cache      # Cache config
php artisan route:cache       # Cache routes
php artisan view:cache        # Cache views
php artisan event:cache       # Cache events

# 3. Tối ưu autoloader
composer dump-autoload --optimize

# 4. Tạo storage link
php artisan storage:link
```

---

## 5. 📁 File Permissions (Linux)

```bash
# Ownership
sudo chown -R www-data:www-data /path/to/project

# Directories: 755
sudo find /path/to/project -type d -exec chmod 755 {} \;

# Files: 644
sudo find /path/to/project -type f -exec chmod 644 {} \;

# Storage & cache: writable
sudo chmod -R 775 /path/to/project/storage
sudo chmod -R 775 /path/to/project/bootstrap/cache
```

---

## 6. ⏰ Cron Jobs (Scheduled Tasks)

### Thêm vào crontab:
```bash
# Mở crontab
crontab -e

# Thêm dòng này (thay đường dẫn phù hợp)
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### Các task đã được lên lịch:
- **02:00 AM**: Backup database hàng ngày
- **01:00 AM Chủ nhật**: Backup toàn bộ hệ thống hàng tuần
- **03:00 AM**: Backup toàn bộ hàng ngày
- **00:05 AM**: Cập nhật status dịch vụ hết hạn
- **Mỗi 15 phút**: Kiểm tra content reminders

---

## 7. 🌐 Web Server Configuration

### Nginx (khuyến nghị):
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com;
    root /path/to/project/public;

    # SSL Configuration
    ssl_certificate /path/to/ssl/certificate.crt;
    ssl_certificate_key /path/to/ssl/private.key;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml application/javascript;

    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

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
}
```

### Apache (.htaccess đã có sẵn trong public/)

---

## 8. 📊 Monitoring & Logging

### Cấu hình logging cho production:
```env
LOG_CHANNEL=daily
LOG_LEVEL=error
```

### Khuyến nghị:
- [ ] Cài đặt Laravel Telescope (development) hoặc Laravel Horizon (queues)
- [ ] Monitoring với services như: Sentry, Bugsnag, hoặc New Relic
- [ ] Server monitoring: Uptime Robot, Pingdom

---

## 9. 🔄 Backup Strategy

### Đã có sẵn trong project:
- Backup database hàng ngày (02:00 AM)
- Backup toàn bộ hàng tuần (Chủ nhật 01:00 AM)
- Backup toàn bộ hàng ngày (03:00 AM)

### Khuyến nghị thêm:
- [ ] Backup lên cloud storage (AWS S3, Google Cloud, etc.)
- [ ] Test restore backup định kỳ
- [ ] Giữ ít nhất 7 ngày backup

---

## 10. 🚀 Deployment Steps

### Bước 1: Chuẩn bị server
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2+, MySQL, Nginx
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip nginx mysql-server -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Bước 2: Upload code
```bash
# Clone từ git hoặc upload qua SFTP
git clone your-repo-url /var/www/yourdomain

# Hoặc sử dụng rsync
rsync -avz --exclude 'node_modules' --exclude 'vendor' --exclude '.env' ./ user@server:/var/www/yourdomain/
```

### Bước 3: Cài đặt dependencies
```bash
cd /var/www/yourdomain
composer install --optimize-autoloader --no-dev
```

### Bước 4: Cấu hình environment
```bash
cp .env.example .env
nano .env  # Chỉnh sửa các giá trị production
php artisan key:generate
```

### Bước 5: Setup database
```bash
php artisan migrate --force
```

### Bước 6: Optimize
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

### Bước 7: Set permissions
```bash
sudo chown -R www-data:www-data /var/www/yourdomain
sudo chmod -R 755 /var/www/yourdomain
sudo chmod -R 775 /var/www/yourdomain/storage
sudo chmod -R 775 /var/www/yourdomain/bootstrap/cache
```

### Bước 8: Cấu hình web server & SSL
```bash
# Cài SSL với Let's Encrypt
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d yourdomain.com
```

### Bước 9: Setup cron job
```bash
crontab -e
# Thêm: * * * * * cd /var/www/yourdomain && php artisan schedule:run >> /dev/null 2>&1
```

---

## 11. ✅ Final Checklist

### Trước khi go-live:
- [ ] APP_DEBUG=false
- [ ] APP_ENV=production
- [ ] Database đã migrate
- [ ] SSL đã cài đặt
- [ ] Cron jobs đã setup
- [ ] File permissions đúng
- [ ] Xóa các file test không cần thiết
- [ ] Test tất cả các chức năng chính
- [ ] Backup đã hoạt động
- [ ] Error logging hoạt động

### Sau khi go-live:
- [ ] Monitor errors trong 24h đầu
- [ ] Check performance
- [ ] Verify backups đang chạy
- [ ] Test email (nếu có)

---

## 12. 🆘 Troubleshooting

### Lỗi 500 Internal Server Error:
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check permissions
sudo chmod -R 775 storage bootstrap/cache
```

### Lỗi "Class not found":
```bash
composer dump-autoload
php artisan clear-compiled
```

### Lỗi cache:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 📞 Support

Nếu gặp vấn đề khi deploy, hãy kiểm tra:
1. Laravel logs: `storage/logs/laravel.log`
2. PHP error logs: `/var/log/php8.2-fpm.log`
3. Nginx error logs: `/var/log/nginx/error.log`

---

*Cập nhật lần cuối: {{ date('d/m/Y') }}*

