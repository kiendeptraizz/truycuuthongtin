# Hướng dẫn Backup hệ thống

## TL;DR — bạn cần làm 1 lần

1. Mở `.env`, thêm dòng `BACKUP_MIRROR_PATH=D:\backups-truycuuthongtin` (đường dẫn tới ổ khác)
2. Click chuột phải file `scripts/install-task-scheduler.bat` → **Run as administrator**
3. Đợi 2 phút, chạy `php artisan schedule:list` xem cột "Last Ran At" có cập nhật chưa
4. Test 1 lần: `php artisan backup:verify --restore-test` để chắc chắn backup phục hồi được

Xong — backup tự chạy hằng ngày 02:00, mirror sang ổ phụ, giữ 30 ngày.

---

## 1. Hệ thống backup hiện tại

### Lịch chạy
| Schedule | Command | Thời gian |
|---|---|---|
| Daily | `backup:run --type=daily` | **02:00** mỗi ngày |
| Daily | `backup:complete --type=daily` | **03:00** (full system: db + files + config) |
| Weekly | `backup:complete --type=weekly` | **01:00 Chủ nhật** |

### File backup
- Lưu tại `storage/app/backups/`
- Tên: `DB_BACKUP_<type>_<YYYY-MM-DD_HH-MM-SS>.sql.gz`
- Đi kèm file `.sha256` để verify checksum
- Nén gzip → giảm ~85% kích thước
- Daily backup tự xoá sau **30 ngày** (config qua `BACKUP_RETENTION_DAYS`)
- Manual backup **không bị xoá** tự động

---

## 2. Cấu hình `.env`

```env
# Đường dẫn lưu backup chính (mặc định: storage/app/backups)
# BACKUP_PATH=

# Số ngày giữ backup daily
BACKUP_RETENTION_DAYS=30

# RẤT QUAN TRỌNG: Mirror sang ổ/folder khác để không mất khi ổ chính hỏng
# Ví dụ: D:\backups-truycuuthongtin  hoặc  \\nas\share\backups
BACKUP_MIRROR_PATH=D:\backups-truycuuthongtin

# Path tới mysqldump (để trống → tự dò trong Laragon/XAMPP)
# MYSQLDUMP_PATH=
```

> **Cảnh báo**: Backup chỉ nằm trên 1 ổ là KHÔNG AN TOÀN. Đặt mirror ở:
> - Ổ vật lý khác (D:, E:)
> - NAS / network share (`\\server\backup`)
> - USB ngoài cắm thường xuyên
> - Cloud sync (OneDrive/Dropbox folder)

---

## 3. Setup Task Scheduler (BẮT BUỘC)

Tất cả schedule (kể cả backup) chỉ chạy khi Windows trigger `php artisan schedule:run` mỗi phút.

### Cách 1: dùng script có sẵn (khuyến nghị)

```cmd
# Chuột phải -> Run as administrator
scripts\install-task-scheduler.bat
```

Script sẽ:
- Tự dò PHP của Laragon
- Tạo task `TruycuuthongtinScheduler` chạy mỗi phút
- Chạy với quyền SYSTEM (chạy ngay cả khi user logout)

### Cách 2: tạo tay bằng `schtasks`

```cmd
schtasks /Create /TN "TruycuuthongtinScheduler" ^
    /TR "C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe C:\laragon\www\truycuuthongtin\artisan schedule:run" ^
    /SC MINUTE /MO 1 /RU "SYSTEM" /RL HIGHEST /F
```

### Verify đã chạy

```cmd
# Xem task đã tạo
schtasks /Query /TN "TruycuuthongtinScheduler"

# Sau 2-3 phút
cd C:\laragon\www\truycuuthongtin
php artisan schedule:list
# Cột "Last Ran At" phải có giá trị
```

### Gỡ bỏ
```cmd
schtasks /Delete /TN "TruycuuthongtinScheduler" /F
```

---

## 4. Lệnh thường dùng

### Tạo backup tay
```bash
php artisan backup:run --type=manual
```

### Liệt kê + kiểm tra sức khoẻ backup
```bash
php artisan backup:monitor              # Tóm tắt nhanh
php artisan backup:monitor --report     # Báo cáo chi tiết
php artisan backup:monitor --check      # Health check
```

### Verify backup file (KHÔNG đụng DB hiện tại)
```bash
# Verify file mới nhất
php artisan backup:verify

# Verify file cụ thể
php artisan backup:verify --file=DB_BACKUP_daily_2026-04-30_02-00-00.sql.gz

# Restore vào DB tạm để chắc chắn phục hồi được (xoá DB tạm sau khi xong)
php artisan backup:verify --restore-test
```

### Restore khi cần
```bash
# CẢNH BÁO: lệnh này GHI ĐÈ DB hiện tại
php artisan backup:restore <tên-file.sql.gz>
```

---

## 5. Bảo mật

### Đã có:
- ✅ Mật khẩu DB **không lộ** trên command line — truyền qua file `.cnf` tạm với perm `0600`, xoá ngay sau dump
- ✅ File credential tạm đặt trong project storage (tránh path Windows có space)
- ✅ Checksum SHA256 — phát hiện file bị hỏng/sửa đổi
- ✅ Mirror sang folder phụ — chống mất ổ chính

### Nên làm thêm:
- 🟡 **Encrypt backup files** — dùng `gpg` hoặc 7zip với password trước khi mirror lên cloud
- 🟡 **Cloud backup** — `backup:cloud` đã có khung, cần implement Google Drive / Dropbox API key
- 🟡 **Alert qua email/Zalo khi backup fail** — hiện chỉ log
- 🟡 **Restrict access** thư mục `storage/app/backups/` (web không serve trực tiếp)

---

## 6. Khi backup fail

Mở `storage/logs/laravel.log` tìm `Backup failure` để xem error.

### Lỗi thường gặp

**`mysqldump: command not found`**
→ Cài Laragon hoặc set `MYSQLDUMP_PATH` trong `.env`

**`Failed to open required defaults file`**
→ Path có ký tự Unicode/space — đảm bảo `BACKUP_PATH` đặt trong path không dấu (đã fix mặc định)

**Schedule không chạy**
→ Task Scheduler chưa setup (xem mục 3) HOẶC máy tắt vào giờ chạy backup

---

## 7. Disaster recovery checklist

Khi DB hỏng / mất:

1. Xác định backup mới nhất:
   ```bash
   ls -lt storage/app/backups/DB_BACKUP_*.sql.gz | head -3
   ```

2. Verify nó còn dùng được:
   ```bash
   php artisan backup:verify --restore-test
   ```

3. Tạo backup hiện trạng (nếu DB còn 1 phần):
   ```bash
   php artisan backup:run --type=manual
   ```

4. Restore:
   ```bash
   php artisan backup:restore DB_BACKUP_daily_2026-04-30_02-00-00.sql.gz
   ```

5. Verify ứng dụng chạy được:
   ```bash
   curl http://localhost:8080/login
   ```
