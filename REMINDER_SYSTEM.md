# Hệ Thống Quản Lý Nhắc Nhở và Tài Khoản Dùng Chung

## Tổng Quan

Hệ thống này bao gồm hai phần chính:

1. **Quản lý nhắc nhở khách hàng sắp hết hạn**: Theo dõi và quản lý việc nhắc nhở khách hàng có dịch vụ sắp hết hạn
2. **Quản lý tài khoản dùng chung**: Theo dõi và kiểm soát các tài khoản dịch vụ được sử dụng bởi nhiều khách hàng

### Các tính năng chính:

**Phần Nhắc Nhở:**

-   **Đánh dấu trạng thái nhắc nhở**: Theo dõi ai đã được nhắc và ai chưa
-   **Giao diện web trực quan**: Quản lý trực tiếp từ trang admin
-   **Thống kê chi tiết**: Báo cáo tổng quan về tình trạng nhắc nhở
-   **Quản lý linh hoạt**: Có thể reset, backup và restore dữ liệu
-   **Ghi chú nhắc nhở**: Lưu lại lịch sử và ghi chú cho mỗi lần nhắc

**Phần Tài Khoản Dùng Chung:**

-   **Theo dõi tài khoản đa người dùng**: Phát hiện email được sử dụng bởi nhiều khách hàng
-   **Cảnh báo xung đột**: Cảnh báo khi có rủi ro xung đột tài khoản
-   **Báo cáo chi tiết**: Thống kê top tài khoản dùng chung và mức độ rủi ro
-   **Quản lý mật khẩu**: Hiển thị/ẩn mật khẩu an toàn
-   **Phân tích xu hướng**: Biểu đồ và thống kê trực quan

## Giao Diện Web

### 1. Trang Quản Lý Dịch Vụ Khách Hàng

**Đường dẫn:** `/admin/customer-services`

**Tính năng mới:**

-   **Sắp xếp thông minh**:

    -   Dịch vụ sắp hết hạn được ưu tiên lên đầu
    -   Trong cùng nhóm sắp hết hạn: gần hết hạn nhất lên trước
    -   Chưa được nhắc nhở sẽ lên trước đã nhắc

-   **Cảnh báo trực quan**:

    -   🚨 **Dòng đỏ**: Dịch vụ hết hạn trong 0-1 ngày (CẤP BÁC!)
    -   ⚠️ **Dòng vàng**: Dịch vụ hết hạn trong 2 ngày
    -   📋 **Thông báo đầu trang**: Hiển thị số lượng dịch vụ cấp bách

-   **Cột "Nhắc nhở"**: Hiển thị trạng thái nhắc nhở cho từng dịch vụ

    -   ✅ **Đã nhắc**: Số lần nhắc + thời gian nhắc gần nhất
    -   ⚠️ **Cần nhắc lại**: Hiển thị khi đã nhắc nhưng cần nhắc lại sau 24h
    -   ❌ **Chưa nhắc**: Đối với dịch vụ sắp hết hạn chưa được nhắc

-   **Nút thao tác mới:**
    -   🔔 **Đánh dấu nhắc nhở**: Cho dịch vụ sắp hết hạn chưa được nhắc
    -   ↩️ **Reset nhắc nhở**: Reset trạng thái nhắc nhở

### 2. Trang Báo Cáo Nhắc Nhở

**Đường dẫn:** `/admin/customer-services-reminder-report`

**Tính năng:**

-   **Thống kê tổng quan**:

    -   Tổng số dịch vụ sắp hết hạn
    -   Số lượng đã/chưa nhắc nhở
    -   Tỷ lệ % đã nhắc nhở

-   **Bộ lọc thời gian**: 3, 5, 7, 10 ngày tới

-   **Danh sách chi tiết:**

    -   **Cần nhắc nhở**: Danh sách dịch vụ chưa được nhắc
    -   **Đã nhắc nhở**: Danh sách dịch vụ đã được nhắc với trạng thái

-   **Thao tác hàng loạt:**
    -   Chọn nhiều dịch vụ và đánh dấu cùng lúc
    -   Đánh dấu tất cả chưa được nhắc

### 3. Báo Cáo Thống Kê Hàng Ngày

**Đường dẫn:** `/admin/customer-services-daily-report`

**Tính năng:**

-   **Thống kê tổng quan theo ngày:**

    -   Số dịch vụ kích hoạt + doanh thu ước tính
    -   Số dịch vụ hết hạn (cần gia hạn)
    -   Số dịch vụ sắp hết hạn trong 5 ngày

-   **Chi tiết dịch vụ kích hoạt:**

    -   Danh sách khách hàng kích hoạt dịch vụ
    -   Top gói dịch vụ phổ biến
    -   Doanh thu theo từng gói

-   **Chi tiết dịch vụ hết hạn:**

    -   Danh sách khách hàng cần liên hệ gia hạn
    -   Thông tin liên hệ và thao tác nhanh

-   **Bộ lọc ngày:** Có thể xem báo cáo cho bất kỳ ngày nào

### 4. Nút Truy Cập Nhanh

-   **Từ Dashboard**: Truy cập nhanh tới dịch vụ sắp hết hạn
-   **Từ menu**: Menu "Dịch vụ khách hàng"
    -   "Báo cáo hàng ngày" - Xem thống kê tổng quan
    -   "Báo cáo nhắc nhở" - Quản lý nhắc nhở

### 5. Bộ Lọc Nâng Cao

**Trạng thái dịch vụ:**

-   Đang hoạt động, Sắp hết hạn, Đã hết hạn

**Nhắc nhở:**

-   Sắp hết hạn - Chưa nhắc
-   Đã được nhắc nhở

**Ngày kích hoạt:**

-   🎯 **Kích hoạt hôm nay** - Xem khách hàng mới + thống kê
-   Kích hoạt hôm qua
-   Kích hoạt tuần này
-   Kích hoạt tháng này

## Quy Trình Sử Dụng Hàng Ngày

### 1. Kiểm Tra Doanh Số Hôm Nay

```
1. Vào "Dịch vụ khách hàng" → Filter "🎯 Kích hoạt hôm nay"
2. Xem thống kê: Tổng dịch vụ, khách hàng, doanh thu
3. Hoặc vào "Báo cáo hàng ngày" để xem chi tiết đầy đủ
```

### 2. Quản Lý Nhắc Nhở

```
1. Vào "Báo cáo nhắc nhở"
2. Xem danh sách chưa được nhắc
3. Đánh dấu hàng loạt hoặc từng khách
```

### 3. Theo Dõi Hết Hạn

```
1. Vào "Báo cáo hàng ngày"
2. Xem mục "Dịch vụ hết hạn hôm nay"
3. Liên hệ khách hàng gia hạn
```

## Tổng Quan

Hệ thống này cho phép bạn theo dõi và quản lý việc nhắc nhở khách hàng có dịch vụ sắp hết hạn. Bao gồm:

-   **Đánh dấu trạng thái nhắc nhở**: Theo dõi ai đã được nhắc và ai chưa
-   **Thống kê chi tiết**: Báo cáo tổng quan về tình trạng nhắc nhở
-   **Quản lý linh hoạt**: Có thể reset, backup và restore dữ liệu
-   **Ghi chú nhắc nhở**: Lưu lại lịch sử và ghi chú cho mỗi lần nhắc

## Các Trường Dữ Liệu Mới

Đã thêm vào bảng `customer_services`:

-   `reminder_sent` (boolean): Đã gửi nhắc nhở hay chưa
-   `reminder_sent_at` (timestamp): Thời gian gửi nhắc nhở gần nhất
-   `reminder_count` (integer): Số lần đã nhắc nhở
-   `reminder_notes` (text): Ghi chú về các lần nhắc nhở

## Các Lệnh Quản Lý

### 1. Xem Báo Cáo Trạng Thái

```bash
# Xem báo cáo dịch vụ sắp hết hạn trong 5 ngày (mặc định)
php artisan reminder:report

# Xem báo cáo với số ngày tùy chỉnh
php artisan reminder:report --days=3

# Reset tất cả trạng thái nhắc nhở
php artisan reminder:report --reset
```

**Báo cáo bao gồm:**

-   Tổng quan số lượng
-   Danh sách chi tiết những ai chưa được nhắc
-   Danh sách chi tiết những ai đã được nhắc
-   Thống kê theo số ngày còn lại

### 2. Gửi Nhắc Nhở

```bash
# Gửi nhắc nhở cho dịch vụ sắp hết hạn trong 5 ngày (mặc định)
php artisan reminder:send-expiration

# Tùy chỉnh số ngày
php artisan reminder:send-expiration --days=3

# Chỉ đánh dấu, không gửi thực tế (để test)
php artisan reminder:send-expiration --mark-only

# Gửi cho tất cả, kể cả đã gửi trước đó
php artisan reminder:send-expiration --force
```

**Lưu ý:** Hiện tại lệnh chỉ đánh dấu trạng thái. Để gửi email/SMS thực tế, bạn cần tích hợp thêm logic gửi.

### 3. Backup Dữ Liệu

```bash
# Backup với tên mặc định (timestamp)
php artisan backup:customers

# Backup với tên tùy chỉnh
php artisan backup:customers --name="truoc-khi-cap-nhat"
```

### 4. Restore Dữ Liệu

```bash
# Restore từ backup mới nhất
php artisan db:seed --class=RestoreCustomersSeeder
```

## Ví Dụ Sử Dụng

### Quy Trình Hàng Ngày

1. **Kiểm tra tình hình:**

    ```bash
    php artisan reminder:report
    ```

2. **Gửi nhắc nhở cho những ai chưa được nhắc:**

    ```bash
    php artisan reminder:send-expiration --mark-only
    ```

3. **Backup định kỳ:**
    ```bash
    php artisan backup:customers --name="daily-$(date +%Y%m%d)"
    ```

### Quy Trình Test

1. **Reset để test:**

    ```bash
    php artisan reminder:report --reset
    ```

2. **Gửi nhắc nhở cho một nhóm nhỏ:**

    ```bash
    php artisan reminder:send-expiration --days=2 --mark-only
    ```

3. **Kiểm tra kết quả:**
    ```bash
    php artisan reminder:report
    ```

## Tích Hợp Gửi Email/SMS

Để gửi nhắc nhở thực tế, bạn có thể:

1. **Tạo Mailable class:**

    ```bash
    php artisan make:mail ExpirationReminder
    ```

2. **Sửa file `SendExpirationReminders.php`:**

    ```php
    // Thay dòng này:
    $service->markAsReminded('Gửi nhắc nhở qua command');

    // Bằng:
    Mail::to($service->customer->email)->send(new ExpirationReminder($service));
    $service->markAsReminded('Gửi email nhắc nhở');
    ```

## Phương Thức Hữu Ích Trong Model

```php
// Kiểm tra cần nhắc lại không (sau 24h)
$service->needsReminderAgain();

// Đánh dấu đã nhắc với ghi chú
$service->markAsReminded('Đã gọi điện nhắc nhở');

// Reset trạng thái nhắc nhở
$service->resetReminderStatus();

// Lấy dịch vụ sắp hết hạn chưa được nhắc
CustomerService::expiringSoonNotReminded(5)->get();

// Lấy dịch vụ đã được nhắc nhở
CustomerService::reminded()->get();
```

## Ghi Chú Quan Trọng

-   Hệ thống tự động tính toán khách hàng cần nhắc lại sau 24h nếu vẫn sắp hết hạn
-   Backup bao gồm tất cả thông tin nhắc nhở và thống kê
-   Restore sẽ khôi phục cả trạng thái nhắc nhở nếu có trong backup
-   Có thể tùy chỉnh số ngày "sắp hết hạn" cho từng tình huống khác nhau

## Quản Lý Tài Khoản Dùng Chung

### 1. Trang Danh Sách Tài Khoản Dùng Chung

**Đường dẫn:** `/admin/shared-accounts`

**Cách truy cập:** Từ trang quản lý dịch vụ khách hàng → Nút "Tài khoản dùng chung"

**Tính năng:**

-   **Thống kê tổng quan**:

    -   Số lượng tài khoản dùng chung
    -   Tổng số dịch vụ trong tài khoản dùng chung
    -   Số tài khoản có vấn đề (nhiều khách hàng)
    -   Số dịch vụ sắp hết hạn trong tài khoản dùng chung

-   **Bộ lọc nâng cao**:

    -   Lọc tài khoản có vấn đề
    -   Lọc theo số lượng khách hàng tối thiểu
    -   Sắp xếp theo số dịch vụ, số khách hàng, số dịch vụ hết hạn

-   **Cảnh báo trực quan**:

    -   🟡 **Dòng vàng**: Tài khoản có nhiều khách hàng dùng chung
    -   🔴 **Dòng đỏ**: Tài khoản có dịch vụ đã hết hạn
    -   ⚠️ **Badge cảnh báo**: Hiển thị "Nhiều khách hàng" khi có > 1 người dùng

-   **Thông tin hiển thị**:
    -   Email tài khoản
    -   Tổng số dịch vụ sử dụng email này
    -   Số khách hàng khác nhau
    -   Số dịch vụ đang hoạt động/hết hạn/sắp hết hạn
    -   Trạng thái tổng thể của tài khoản

### 2. Trang Chi Tiết Tài Khoản Dùng Chung

**Đường dẫn:** `/admin/shared-accounts/{email}`

**Tính năng:**

-   **Thống kê chi tiết cho email**:

    -   Tổng số dịch vụ
    -   Số khách hàng sử dụng
    -   Phân loại theo trạng thái (hoạt động/hết hạn/sắp hết hạn)

-   **Danh sách dịch vụ chi tiết**:

    -   Thông tin khách hàng và liên hệ
    -   Gói dịch vụ đang sử dụng
    -   **Mật khẩu có thể ẩn/hiện**: Click nút mắt để xem mật khẩu
    -   Ngày kích hoạt và hết hạn
    -   Trạng thái nhắc nhở
    -   Người phân công dịch vụ

-   **Cảnh báo đặc biệt**:

    -   Hiển thị cảnh báo nổi bật khi có nhiều khách hàng dùng chung
    -   Highlight các dịch vụ hết hạn/sắp hết hạn

-   **Thao tác nhanh**:

    -   Xem thông tin khách hàng
    -   Đánh dấu nhắc nhở cho dịch vụ sắp hết hạn
    -   Gửi email trực tiếp cho khách hàng

-   **Thông tin hỗ trợ**:
    -   Ghi chú quan trọng về rủi ro dùng chung
    -   Danh sách liên hệ tất cả khách hàng sử dụng email này

### 3. Trang Báo Cáo Tài Khoản Dùng Chung

**Đường dẫn:** `/admin/shared-accounts/report`

**Tính năng:**

-   **Thống kê tổng quan toàn hệ thống**:

    -   Tổng số dịch vụ có email đăng nhập
    -   Số email duy nhất trong hệ thống
    -   Số email được dùng chung (>1 dịch vụ)
    -   Số email có vấn đề (>1 khách hàng)

-   **Top 10 tài khoản dùng chung nhiều nhất**:

    -   Xếp hạng với icon 🥇🥈🥉
    -   Hiển thị số dịch vụ, số khách hàng
    -   Phân loại hoạt động/hết hạn

-   **Danh sách tài khoản có vấn đề**:

    -   Email được sử dụng bởi nhiều khách hàng khác nhau
    -   Đánh giá mức độ rủi ro (Cao/Trung bình/Thấp)
    -   Tên tất cả khách hàng đang sử dụng
    -   Khuyến nghị xử lý cụ thể

-   **Biểu đồ thống kê trực quan**:

    -   Biểu đồ tròn: Phân bố email riêng vs dùng chung
    -   Biểu đồ tỷ lệ: Mức độ rủi ro trong hệ thống

-   **Tính năng in báo cáo**: Hỗ trợ in báo cáo với định dạng chuyên nghiệp

## Commands Hỗ Trợ

### Commands hiện có:

```bash
# Gửi nhắc nhở dịch vụ sắp hết hạn
php artisan reminders:send

# Tạo báo cáo nhắc nhở
php artisan reminders:report

# Backup dữ liệu khách hàng
php artisan backup:customers

# Restore dữ liệu từ backup
php artisan db:seed --class=RestoreCustomersSeeder

# Tạo dữ liệu test cho dịch vụ kích hoạt hôm nay
php artisan test:create-today-data

# Tạo dữ liệu test cho tài khoản dùng chung
php artisan test:create-shared-accounts
```

### Quy trình xử lý tài khoản dùng chung:

1. **Phát hiện vấn đề**:

    - Truy cập `/admin/shared-accounts`
    - Sử dụng filter "Tài khoản có vấn đề"

2. **Phân tích chi tiết**:

    - Click "Chi tiết" để xem thông tin cụ thể
    - Kiểm tra số khách hàng và trạng thái dịch vụ

3. **Xử lý**:

    - **Tách riêng**: Liên hệ khách hàng tạo tài khoản riêng
    - **Quản lý chung**: Thiết lập quy tắc sử dụng rõ ràng

4. **Theo dõi**: Sử dụng báo cáo để theo dõi xu hướng

### Mức độ ưu tiên xử lý tài khoản dùng chung:

🔴 **Ưu tiên cao** (Xử lý ngay):

-   Tài khoản có ≥5 khách hàng
-   Có dịch vụ đã hết hạn
-   Có khiếu nại từ khách hàng

🟡 **Ưu tiên trung bình** (Xử lý trong tuần):

-   Tài khoản có 3-4 khách hàng
-   Có dịch vụ sắp hết hạn
-   Chưa có quy tắc rõ ràng

🟢 **Ưu tiên thấp** (Theo dõi):

-   Tài khoản có 2 khách hàng
-   Đang hoạt động bình thường
-   Có thỏa thuận rõ ràng
