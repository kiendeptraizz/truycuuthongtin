# 🔧 Tóm tắt Fix Lỗi Encoding Tiếng Việt

## ⚠️ Vấn đề

Khi backup và restore database, tên khách hàng tiếng Việt bị lỗi hiển thị:

-   **Tên gốc:** Nguyễn Văn Thành
-   **Sau backup/restore:** Nguy???n V??n Th??nh

## ✅ Nguyên nhân

1. **Laravel connection**: Không set charset khi kết nối MySQL

    - Windows dùng CP1258 (Vietnamese charset)
    - MySQL nhận CP1258 thay vì UTF8MB4
    - Dữ liệu lưu vào database bị encode sai

2. **mysqldump**: Không chỉ định charset khi backup

    - Đọc dữ liệu với charset mặc định
    - File SQL được tạo ra có encoding sai

3. **mysql restore**: Không chỉ định charset khi restore
    - Đọc file SQL với charset sai
    - Dữ liệu import vào lại bị lỗi

## 🔧 Đã fix

### 1. config/database.php (Dòng 60-63)

**TRƯỚC:**

```php
'options' => extension_loaded('pdo_mysql') ? array_filter([
    PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
]) : [],
```

**SAU:**

```php
'options' => extension_loaded('pdo_mysql') ? array_filter([
    PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
]) : [],
```

### 2. app/Console/Commands/AutoBackupCommand.php (Dòng 80-89)

**TRƯỚC:**

```php
$command = sprintf(
    '"%s" ... --single-transaction ... %s > "%s"',
    ...
);
```

**SAU:**

```php
$command = sprintf(
    '"%s" ... --default-character-set=utf8mb4 --single-transaction ... --result-file="%s" %s',
    ...
);
```

**Thay đổi:**

-   ✅ Thêm `--default-character-set=utf8mb4`
-   ✅ Dùng `--result-file` thay vì redirect `>`

### 3. app/Console/Commands/CompleteBackupCommand.php (Dòng 125-134)

**Tương tự AutoBackupCommand:**

-   ✅ Thêm `--default-character-set=utf8mb4`
-   ✅ Dùng `--result-file` thay vì redirect `>`

### 4. app/Http/Controllers/Admin/BackupController.php (Dòng 148-156)

**TRƯỚC:**

```php
$command = sprintf(
    'mysql --host=%s ... %s < %s',
    ...
);
```

**SAU:**

```php
$command = sprintf(
    'mysql --host=%s ... --default-character-set=utf8mb4 %s < %s',
    ...
);
```

### 5. app/Console/Commands/RestoreBackupCommand.php (Dòng 240-248)

**Tương tự BackupController:**

-   ✅ Thêm `--default-character-set=utf8mb4` vào mysql restore command

## 📊 Kết quả

### ✅ THÀNH CÔNG:

**Khách hàng mới (thêm sau khi fix):**

```
Nguyễn Văn Đạt - Test Final  ← 100% ĐÚNG!
```

**Backup mới:**

```sql
('TEST_FINAL_1761157728','Nguyễn Văn Đạt - Test Final',...)  ← UTF8 ĐÚNG!
```

**Restore từ backup mới:**

```
Nguyễn Văn Đạt - Test Final  ← GIỮ NGUYÊN 100%!
```

### ⚠️ Lưu ý:

**371 khách hàng cũ** vẫn bị lỗi encoding:

-   Do được thêm vào trước khi fix
-   Vẫn tra cứu được bằng: Mã KH, Email, SĐT
-   Có thể sửa bằng cách: Edit → Save lại

## 🎯 Từ giờ trở đi

✅ **Khách hàng mới** thêm vào → Tiếng Việt ĐÚNG  
✅ **Backup mới** tạo ra → Giữ tiếng Việt ĐÚNG  
✅ **Restore** từ backup mới → Tiếng Việt ĐÚNG  
✅ **KHÔNG CÒN BỊ LỖI ENCODING NỮA!**

## 📝 Backup mới nhất

**File:** `DB_BACKUP_manual_2025-10-23_01-29-43.sql`  
**Ngày:** 23/10/2025 01:29  
**Trạng thái:** ✅ Tiếng Việt ĐÚNG  
**Dữ liệu:** 371 Customers + 501 Services + Full data

---

**Fix date:** 23/10/2025  
**Fixed by:** AI Assistant  
**Status:** ✅ HOÀN TẤT
