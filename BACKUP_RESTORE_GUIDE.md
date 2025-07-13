# 🔄 Hướng dẫn Backup & Restore dữ liệu khách hàng

## ⚠️ **LƯU Ý QUAN TRỌNG**

Luôn luôn tạo backup trước khi:

-   Chạy `migrate:fresh`
-   Cập nhật database structure
-   Xóa dữ liệu lớn
-   Deploy production

## 📥 **Tạo Backup**

### 1. Backup thủ công

```bash
php artisan app:backup-customer-data
```

### 2. Backup tự động (không cần xác nhận)

```bash
php artisan app:backup-customer-data --auto
```

### 3. Backup đã được lên lịch tự động:

-   **Hàng ngày lúc 2:00 AM**
-   **Mỗi 6 tiếng một lần** (để an toàn)

## 📤 **Khôi phục dữ liệu**

### 1. Xem danh sách backup có sẵn

```bash
php artisan app:restore-customer-data
```

### 2. Khôi phục từ file cụ thể

```bash
php artisan app:restore-customer-data customer_backup_2025_06_30_18_30_45.json
```

### 3. Khôi phục từ Seeder (file backup mới nhất)

```bash
php artisan db:seed --class=RestoreCustomersSeeder
```

## 📂 **Vị trí file backup**

```
storage/app/backups/
├── customer_backup_2025_06_30_18_30_45.json
├── customer_backup_2025_06_30_12_00_00.json
└── customer_backup_2025_06_29_18_30_45.json
```

## 🔧 **Quy trình an toàn khi làm việc với database**

### 1. Trước khi migrate:fresh

```bash
# Tạo backup
php artisan app:backup-customer-data

# Chạy migrate:fresh
php artisan migrate:fresh --seed

# Nếu có vấn đề, khôi phục ngay
php artisan app:restore-customer-data
```

### 2. Trước khi deploy production

```bash
# Backup production data
php artisan app:backup-customer-data --auto

# Deploy code
git pull origin main
composer install --no-dev

# Migrate (nếu cần)
php artisan migrate --force

# Test xem có lỗi không
php artisan tinker --execute="echo App\Models\Customer::count() . ' customers'"
```

## 📊 **Cấu trúc file backup**

```json
{
    "backup_date": "2025-06-30 18:50:45",
    "customers_count": 15,
    "services_count": 23,
    "customers": [
        {
            "id": 1,
            "customer_code": "KUN12345",
            "name": "Nguyễn Văn A",
            "email": "example@gmail.com",
            "phone": "0123456789"
        }
    ],
    "customer_services": [
        {
            "id": 1,
            "customer_id": 1,
            "service_package_id": 2,
            "login_email": "account@service.com",
            "activated_at": "2025-06-01",
            "expires_at": "2025-07-01",
            "status": "active"
        }
    ]
}
```

## 🚨 **Khôi phục khẩn cấp**

Nếu mất dữ liệu và cần khôi phục ngay:

```bash
# 1. Kiểm tra backup có sẵn
ls -la storage/app/backups/

# 2. Khôi phục file mới nhất
php artisan app:restore-customer-data

# 3. Kiểm tra dữ liệu đã về
php artisan tinker --execute="
    echo 'Customers: ' . App\Models\Customer::count() . PHP_EOL;
    echo 'Services: ' . App\Models\CustomerService::count() . PHP_EOL;
"
```

## 🔄 **Backup tự động**

Hệ thống sẽ tự động:

-   Tạo backup mỗi 6 tiếng
-   Giữ lại **10 file backup gần nhất**
-   Xóa tự động các file cũ hơn

## ⚡ **Lệnh nhanh**

```bash
# Backup ngay
php artisan app:backup-customer-data --auto

# Restore nhanh (file mới nhất)
php artisan db:seed --class=RestoreCustomersSeeder

# Xem thống kê nhanh
php artisan tinker --execute="
    echo '📊 Thống kê hiện tại:' . PHP_EOL;
    echo 'Khách hàng: ' . App\Models\Customer::count() . PHP_EOL;
    echo 'Dịch vụ: ' . App\Models\CustomerService::count() . PHP_EOL;
    echo 'Dịch vụ hoạt động: ' . App\Models\CustomerService::where('status', 'active')->count() . PHP_EOL;
"
```

---

**Ghi nhớ:** Backup là bảo hiểm, không backup là liều mạng! 🛡️
