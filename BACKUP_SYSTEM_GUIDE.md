# 🛡️ HỆ THỐNG BACKUP TỰ ĐỘNG TOÀN DIỆN

## 📋 TỔNG QUAN

Hệ thống backup tự động này được thiết kế để bảo vệ dữ liệu kinh doanh quan trọng của bạn với các tính năng:

- ✅ **Backup tự động theo lịch** (hàng ngày, hàng tuần, nhanh)
- ✅ **Nhiều định dạng** (JSON, SQL, ZIP)
- ✅ **Quản lý phiên bản** (giữ 30 backup gần nhất)
- ✅ **Xác minh tính toàn vẹn** tự động
- ✅ **Thông báo và giám sát**
- ✅ **Khôi phục nhanh** và khôi phục khẩn cấp
- ✅ **Lưu trữ đa vị trí** (local + cloud)

## 🚀 CÁC LỆNH BACKUP

### 1. Backup Tự Động
```bash
# Backup hàng ngày (JSON + SQL)
php artisan backup:auto --type=daily --format=both

# Backup hàng tuần (JSON + SQL)
php artisan backup:auto --type=weekly --format=both

# Backup nhanh (chỉ JSON)
php artisan backup:auto --type=quick --format=json
```

### 2. Khôi Phục Dữ Liệu
```bash
# Liệt kê backup khả dụng
php artisan backup:restore --list

# Khôi phục từ file cụ thể
php artisan backup:restore backup_file.zip

# Khôi phục tự động (chọn backup mới nhất)
php artisan backup:restore --confirm
```

### 3. Giám Sát Backup
```bash
# Kiểm tra tình trạng nhanh
php artisan backup:monitor

# Báo cáo chi tiết
php artisan backup:monitor --report

# Kiểm tra sức khỏe hệ thống
php artisan backup:monitor --check
```

### 4. Cloud Backup
```bash
# Upload backup lên cloud
php artisan backup:cloud --provider=local

# Upload file cụ thể
php artisan backup:cloud backup_file.zip --provider=gdrive
```

## ⏰ LỊCH TRÌNH TỰ ĐỘNG

Hệ thống tự động chạy các tác vụ sau:

| Thời gian | Tác vụ | Mô tả |
|-----------|--------|-------|
| 01:00 Chủ nhật | Weekly Backup | Backup hàng tuần (JSON + SQL) |
| 02:00 hàng ngày | Daily Backup | Backup hàng ngày (JSON + SQL) |
| 02:30 hàng ngày | Cloud Upload | Upload backup lên cloud |
| Mỗi 6 giờ | Quick Backup | Backup nhanh (JSON) |
| 08:00 hàng ngày | Health Check | Kiểm tra sức khỏe backup |
| 09:00 thứ 2 | Weekly Report | Báo cáo backup hàng tuần |

## 📁 CẤU TRÚC FILE BACKUP

```
storage/app/backups/
├── AUTO_BACKUP_daily_2025-07-21_02-00-00.zip
├── AUTO_BACKUP_weekly_2025-07-21_01-00-00.zip
├── AUTO_BACKUP_quick_2025-07-21_08-00-00.zip
└── ...

storage/app/cloud_backups/
├── [Bản sao backup để bảo vệ]
└── ...
```

### Nội dung file ZIP backup:
```
backup_file.zip
├── backup_data.json      # Dữ liệu chính
├── backup_data.sql       # SQL dump
└── backup_info.json      # Metadata
```

## 🚨 KHÔI PHỤC KHẨN CẤP

Khi hệ thống gặp sự cố nghiêm trọng, sử dụng script khôi phục khẩn cấp:

```bash
# Khôi phục từ backup mới nhất
php emergency_restore.php

# Khôi phục từ file cụ thể
php emergency_restore.php backup_file.zip
```

**Lưu ý:** Script này chạy độc lập, không cần Laravel framework.

## 📊 GIÁM SÁT VÀ BÁO CÁO

### Kiểm tra nhanh:
```bash
php artisan backup:monitor
```

### Báo cáo chi tiết:
```bash
php artisan backup:monitor --report
```

### Kiểm tra sức khỏe:
```bash
php artisan backup:monitor --check
```

## ⚙️ CẤU HÌNH

### 1. Cấu hình Database (emergency_restore.php)
```php
$dbConfig = [
    'host' => 'localhost',
    'port' => 3306,
    'database' => 'truycuuthongtin',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];
```

### 2. Cấu hình Cloud Storage
Để sử dụng Google Drive hoặc Dropbox:

1. **Google Drive:**
   - Cài đặt: `composer require google/apiclient`
   - Tạo service account credentials
   - Cập nhật `CloudBackupCommand.php`

2. **Dropbox:**
   - Cài đặt: `composer require spatie/dropbox-api`
   - Tạo Dropbox app và lấy access token
   - Cập nhật `CloudBackupCommand.php`

## 🔧 TROUBLESHOOTING

### Lỗi thường gặp:

1. **"Backup quá cũ"**
   - Kiểm tra cron job có chạy không
   - Xem log: `storage/logs/laravel.log`

2. **"File ZIP bị lỗi"**
   - Kiểm tra dung lượng ổ cứng
   - Kiểm tra quyền ghi thư mục backup

3. **"Khôi phục thất bại"**
   - Kiểm tra kết nối database
   - Đảm bảo file backup không bị lỗi

### Kiểm tra log:
```bash
tail -f storage/logs/laravel.log | grep -i backup
```

## 📈 BEST PRACTICES

1. **Kiểm tra backup định kỳ:**
   - Chạy `backup:monitor --check` hàng tuần
   - Thử khôi phục backup cũ thỉnh thoảng

2. **Quản lý dung lượng:**
   - Hệ thống tự động xóa backup cũ (giữ 30 bản)
   - Theo dõi dung lượng thư mục backup

3. **Bảo mật:**
   - Backup chứa dữ liệu nhạy cảm
   - Bảo vệ thư mục backup
   - Sử dụng cloud storage có mã hóa

4. **Testing:**
   - Test khôi phục trên môi trường dev
   - Xác minh tính toàn vẹn dữ liệu sau khôi phục

## 📞 HỖ TRỢ

Nếu gặp vấn đề:

1. Kiểm tra log hệ thống
2. Chạy `backup:monitor --check`
3. Sử dụng `emergency_restore.php` nếu cần thiết
4. Liên hệ admin hệ thống

---

**🛡️ Hệ thống backup này đảm bảo dữ liệu kinh doanh của bạn luôn được bảo vệ!**
