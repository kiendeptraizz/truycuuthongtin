# 🐳 Docker Setup cho Laravel TruyCuuThongTin

Hướng dẫn chi tiết thiết lập và sử dụng Docker cho project Laravel TruyCuuThongTin.

## 📋 Yêu cầu hệ thống

-   Docker Desktop (Windows/Mac) hoặc Docker Engine (Linux)
-   Docker Compose v2.0+
-   Git Bash (Windows) hoặc Terminal (Mac/Linux)

## 🚀 Cài đặt lần đầu

### Bước 1: Clone project và chuyển vào thư mục

```bash
git clone <repository-url>
cd truycuuthongtin
```

### Bước 2: Thiết lập quyền cho các script

```bash
chmod +x setup-docker.sh
chmod +x docker-commands.sh
```

### Bước 3: Chạy setup tự động

```bash
./setup-docker.sh
```

Script này sẽ:

-   ✅ Tạo file `.env` với cấu hình Docker
-   🏗️ Build Docker containers
-   📦 Cài đặt Composer dependencies
-   🔑 Generate Laravel application key
-   🏃‍♂️ Chạy database migrations
-   🌱 Chạy database seeders

## 🌐 Truy cập ứng dụng

Sau khi setup hoàn tất:

-   **Laravel App**: http://localhost:8000
-   **phpMyAdmin**: http://localhost:8081
    -   Server: `mysql`
    -   Username: `laravel`
    -   Password: `laravel`
    -   Database: `truycuuthongtin`

## 🛠️ Quản lý containers

### Sử dụng script tiện lợi

```bash
# Xem tất cả commands
./docker-commands.sh

# Khởi động containers
./docker-commands.sh start

# Dừng containers
./docker-commands.sh stop

# Restart containers
./docker-commands.sh restart

# Xem logs
./docker-commands.sh logs

# Truy cập shell của app container
./docker-commands.sh shell

# Truy cập MySQL shell
./docker-commands.sh mysql
```

### Commands thường dùng

```bash
# Chạy artisan commands
./docker-commands.sh artisan migrate
./docker-commands.sh artisan make:controller TestController
./docker-commands.sh artisan queue:work

# Chạy composer commands
./docker-commands.sh composer install
./docker-commands.sh composer require package-name

# Database operations
./docker-commands.sh migrate
./docker-commands.sh seed
./docker-commands.sh fresh  # migrate:fresh --seed

# Laravel Tinker
./docker-commands.sh tinker

# Chạy tests
./docker-commands.sh test

# Clear caches
./docker-commands.sh clear
```

### Sử dụng Docker Compose trực tiếp

```bash
# Khởi động containers
docker compose up -d

# Dừng containers
docker compose down

# Xem logs
docker compose logs -f

# Rebuild containers
docker compose up -d --build
```

## 🔧 Aliases hữu ích

Thêm vào file `~/.bashrc` hoặc `~/.zshrc`:

```bash
# Laravel Docker Aliases
alias art='docker exec -it truycuuthongtin_app php artisan'
alias composer='docker exec -it truycuuthongtin_app composer'
alias tinker='docker exec -it truycuuthongtin_app php artisan tinker'
alias test='docker exec -it truycuuthongtin_app php artisan test'
alias shell='docker exec -it truycuuthongtin_app bash'
alias mysql='docker exec -it truycuuthongtin_db mysql -u laravel -p truycuuthongtin'
```

Sau đó reload terminal:

```bash
source ~/.bashrc  # hoặc source ~/.zshrc
```

## 📁 Cấu trúc file Docker

```
truycuuthongtin/
├── docker-compose.yml      # Cấu hình Docker services
├── Dockerfile             # Build PHP-Apache container
├── .dockerignore          # Loại trừ files khi build
├── setup-docker.sh        # Script setup tự động
├── docker-commands.sh     # Script quản lý containers
└── DOCKER_README.md       # Hướng dẫn này
```

## 🗄️ Database

### Thông tin kết nối

-   **Host**: `mysql` (trong container) / `localhost` (từ host)
-   **Port**: `3306`
-   **Database**: `truycuuthongtin`
-   **Username**: `laravel`
-   **Password**: `laravel`
-   **Root Password**: `root`

### Backup & Restore Database

```bash
# Backup database
docker exec truycuuthongtin_db mysqldump -u laravel -p truycuuthongtin > backup.sql

# Restore database
docker exec -i truycuuthongtin_db mysql -u laravel -p truycuuthongtin < backup.sql
```

## 🚨 Xử lý sự cố

### Container không khởi động

```bash
# Xem logs để debug
./docker-commands.sh logs

# Rebuild containers
./docker-commands.sh build
```

### Database connection error

```bash
# Kiểm tra container status
./docker-commands.sh status

# Restart database container
docker compose restart mysql
```

### Permission errors

```bash
# Fix permissions cho storage và bootstrap/cache
./docker-commands.sh shell
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Xóa và setup lại từ đầu

```bash
# Dừng và xóa tất cả containers + volumes
./docker-commands.sh clean

# Setup lại từ đầu
./setup-docker.sh
```

## 🔄 Workflow phát triển

### 1. Khởi động môi trường

```bash
./docker-commands.sh start
```

### 2. Phát triển code

-   Edit code như bình thường
-   File sẽ sync real-time với container

### 3. Database changes

```bash
# Tạo migration
./docker-commands.sh artisan make:migration create_something_table

# Chạy migration
./docker-commands.sh migrate
```

### 4. Test ứng dụng

```bash
# Chạy tests
./docker-commands.sh test

# Hoặc test cụ thể
./docker-commands.sh artisan test --filter TestName
```

### 5. Dừng môi trường

```bash
./docker-commands.sh stop
```

## 📝 Ghi chú

-   Containers sử dụng volume để lưu trữ database, data sẽ không mất khi restart
-   Source code được mount vào container, thay đổi code sẽ reflect ngay lập tức
-   Port 8000 và 8081 cần available trên host machine
-   File `.env` sẽ được tạo tự động với cấu hình Docker

## 🆘 Hỗ trợ

Nếu gặp vấn đề, hãy:

1. Kiểm tra logs: `./docker-commands.sh logs`
2. Kiểm tra status: `./docker-commands.sh status`
3. Rebuild containers: `./docker-commands.sh build`
4. Clean và setup lại: `./docker-commands.sh clean` + `./setup-docker.sh`
