# 🔄 Tự Động Cập Nhật Status Dịch Vụ Hết Hạn

## 📋 Vấn đề đã được giải quyết

Trước đây, hệ thống có vấn đề sau:

-   Các dịch vụ đã hết hạn (theo `expires_at`) vẫn giữ `status = 'active'`
-   Filter "Đã hết hạn" hiển thị TẤT CẢ dịch vụ có `expires_at` đã qua (bao gồm cả cancelled)
-   Dẫn đến việc quản lý dịch vụ hết hạn không chính xác

### Ví dụ trước khi sửa:

```
- 138 dịch vụ có expires_at đã qua
  - 118 dịch vụ: status = 'active' (SAI!)
  - 19 dịch vụ: status = 'cancelled'
  - 1 dịch vụ: status = 'expired'
```

## ✅ Giải pháp đã triển khai

### 1. **Command tự động cập nhật status**

**File:** `app/Console/Commands/UpdateExpiredServices.php`

Command này sẽ:

-   Tìm tất cả dịch vụ có `status = 'active'` nhưng `expires_at` đã qua
-   Tự động cập nhật `status` từ `'active'` sang `'expired'`
-   Hiển thị progress bar và thống kê kết quả

**Cách chạy thủ công:**

```bash
php artisan services:update-expired
```

### 2. **Scheduled Task - Chạy tự động hàng ngày**

**File:** `routes/console.php`

Command sẽ tự động chạy mỗi ngày vào lúc **00:05 AM** (5 phút sau nửa đêm):

```php
Schedule::command('services:update-expired')
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->runInBackground();
```

**Lưu ý:** Để scheduled task hoạt động, bạn cần:

1. Thêm cron job trên server:
    ```bash
    * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
    ```
2. Hoặc chạy trong development:
    ```bash
    php artisan schedule:work
    ```

### 3. **Cập nhật Scope trong Model**

**File:** `app/Models/CustomerService.php`

#### Scope `expired()` - Đã được cập nhật

Bây giờ chỉ lọc theo `status = 'expired'`:

```php
public function scopeExpired($query)
{
    return $query->where('status', 'expired');
}
```

#### Scope mới: `expiredByDate()`

Lọc theo thời gian hết hạn (bất kể status):

```php
public function scopeExpiredByDate($query)
{
    $yesterday = now()->subDay()->endOfDay();
    return $query->where('expires_at', '<=', $yesterday);
}
```

## 📊 Kết quả sau khi triển khai

### Trước khi chạy command:

```
Status trong database:
- active: 481 dịch vụ (trong đó 118 đã hết hạn!)
- expired: 1 dịch vụ
- cancelled: 19 dịch vụ
```

### Sau khi chạy command:

```
Status trong database:
- active: 363 dịch vụ (chỉ còn dịch vụ còn hạn)
- expired: 119 dịch vụ ✓
- cancelled: 19 dịch vụ
```

### Filter "Đã hết hạn" trên UI:

-   **Trước:** Hiển thị 138 dịch vụ (bao gồm cả cancelled)
-   **Sau:** Hiển thị 119 dịch vụ (chỉ dịch vụ có status = expired)

## 🔍 Cách sử dụng

### 1. Xem dịch vụ đã hết hạn (theo status)

```php
$expiredServices = CustomerService::expired()->get();
```

### 2. Xem dịch vụ đã hết hạn theo thời gian (bất kể status)

```php
$expiredByDate = CustomerService::expiredByDate()->get();
```

### 3. Chạy command cập nhật thủ công

```bash
php artisan services:update-expired
```

### 4. Kiểm tra scheduled tasks

```bash
php artisan schedule:list
```

## 🚀 Lợi ích

1. ✅ **Tự động hóa:** Không cần cập nhật status thủ công
2. ✅ **Chính xác:** Status luôn phản ánh đúng trạng thái dịch vụ
3. ✅ **Dễ quản lý:** Filter "Đã hết hạn" chỉ hiển thị dịch vụ thực sự expired
4. ✅ **Tách biệt:** Dịch vụ cancelled không bị lẫn với dịch vụ expired
5. ✅ **Hiệu suất:** Command chạy nhanh với progress bar

## 📝 Maintenance

### Kiểm tra xem có dịch vụ nào cần cập nhật không:

```bash
php artisan tinker
```

```php
CustomerService::where('status', 'active')
    ->where('expires_at', '<=', now()->subDay()->endOfDay())
    ->count();
```

### Xem lịch sử scheduled tasks:

```bash
tail -f storage/logs/laravel.log
```

## ⚠️ Lưu ý quan trọng

1. **Cron job:** Đảm bảo đã setup cron job trên production server
2. **Timezone:** Command sử dụng timezone trong `config/app.php`
3. **Status cancelled:** Dịch vụ có status 'cancelled' sẽ KHÔNG bị tự động cập nhật sang 'expired'
4. **Backup:** Command này chỉ cập nhật status, không xóa dữ liệu

## 🔗 Files liên quan

-   `app/Console/Commands/UpdateExpiredServices.php` - Command chính
-   `routes/console.php` - Schedule configuration
-   `app/Models/CustomerService.php` - Model với scopes
-   `app/Http/Controllers/Admin/CustomerServiceController.php` - Controller

---

**Ngày tạo:** 29/10/2025  
**Version:** 1.0  
**Status:** ✅ Active
