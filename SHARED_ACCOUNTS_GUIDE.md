# Hướng dẫn Quản lý Tài khoản Dùng Chung

## Tổng quan

Hệ thống quản lý tài khoản dùng chung giúp theo dõi và kiểm soát các gói dịch vụ có loại **"TEAM DÙNG CHUNG"** được sử dụng bởi nhiều khách hàng. Điều này khác với việc chỉ dựa vào email trùng nhau - chỉ những gói dịch vụ được thiết kế đặc biệt cho việc chia sẻ mới được quản lý ở đây.

**Lưu ý quan trọng:** Chỉ những gói dịch vụ có `account_type = "TEAM DÙNG CHUNG"` mới được hiển thị trong hệ thống này, không phải tất cả dịch vụ có cùng email đăng nhập.

## Các tính năng chính

### 1. Danh sách tài khoản dùng chung (`/admin/shared-accounts`)

**Truy cập:** Từ trang quản lý dịch vụ khách hàng → "Tài khoản dùng chung"

**Tính năng:**

-   Hiển thị danh sách email được sử dụng bởi nhiều dịch vụ
-   Thống kê tổng quan: Tổng tài khoản, dịch vụ, tài khoản có vấn đề, dịch vụ sắp hết hạn
-   Bộ lọc nâng cao:
    -   Lọc theo tài khoản có vấn đề
    -   Lọc theo số lượng khách hàng tối thiểu
    -   Sắp xếp theo nhiều tiêu chí
-   Hiển thị cảnh báo trực quan cho:
    -   Tài khoản có nhiều khách hàng (màu vàng)
    -   Tài khoản có dịch vụ hết hạn (màu đỏ)
    -   Tài khoản có dịch vụ sắp hết hạn (màu vàng)

**Cách sử dụng:**

1. Truy cập `/admin/shared-accounts`
2. Sử dụng bộ lọc để tìm tài khoản cần quan tâm
3. Click "Chi tiết" để xem thông tin chi tiết từng tài khoản

### 2. Chi tiết tài khoản dùng chung (`/admin/shared-accounts/{email}`)

**Tính năng:**

-   Thống kê chi tiết cho từng email:
    -   Tổng số dịch vụ
    -   Số khách hàng sử dụng
    -   Số dịch vụ đang hoạt động/hết hạn/sắp hết hạn
-   Danh sách đầy đủ các dịch vụ sử dụng email này
-   Hiển thị/ẩn mật khẩu tài khoản
-   Cảnh báo nếu có nhiều khách hàng dùng chung
-   Thông tin liên hệ khách hàng
-   Thao tác nhanh: gửi email, đánh dấu nhắc nhở

**Thông tin hiển thị:**

-   Thông tin khách hàng và gói dịch vụ
-   Mật khẩu tài khoản (có thể ẩn/hiện)
-   Ngày kích hoạt và hết hạn
-   Trạng thái dịch vụ
-   Tình trạng nhắc nhở
-   Người phân công dịch vụ

### 3. Báo cáo tài khoản dùng chung (`/admin/shared-accounts/report`)

**Tính năng:**

-   **Thống kê tổng quan:**

    -   Tổng dịch vụ có email
    -   Số email duy nhất
    -   Số email dùng chung
    -   Số email có nhiều khách hàng

-   **Top 10 tài khoản dùng chung nhiều nhất:**

    -   Xếp hạng theo số lượng dịch vụ
    -   Hiển thị số khách hàng khác nhau
    -   Số dịch vụ đang hoạt động/đã hết hạn

-   **Tài khoản có vấn đề:**

    -   Danh sách email được sử dụng bởi nhiều khách hàng
    -   Đánh giá mức độ rủi ro
    -   Tên các khách hàng sử dụng
    -   Khuyến nghị xử lý

-   **Biểu đồ thống kê:**
    -   Phân bố dịch vụ theo email
    -   Tỷ lệ tài khoản có rủi ro

## Commands hỗ trợ

### 1. Tạo dữ liệu test

```bash
php artisan test:create-shared-accounts
```

Tạo dữ liệu test với các tài khoản dùng chung mẫu.

### 2. Các command hiện có có thể áp dụng

```bash
# Gửi nhắc nhở cho dịch vụ sắp hết hạn (bao gồm tài khoản dùng chung)
php artisan reminders:send

# Báo cáo nhắc nhở
php artisan reminders:report

# Backup dữ liệu
php artisan backup:customers
```

## Quy trình xử lý tài khoản dùng chung

### 1. Phát hiện tài khoản có vấn đề

1. Truy cập `/admin/shared-accounts`
2. Sử dụng filter "Tài khoản có vấn đề"
3. Kiểm tra danh sách tài khoản có nhiều khách hàng

### 2. Phân tích chi tiết

1. Click "Chi tiết" để xem thông tin cụ thể
2. Kiểm tra:
    - Số khách hàng đang sử dụng
    - Trạng thái các dịch vụ
    - Thông tin liên hệ khách hàng

### 3. Xử lý

**Tùy chọn 1: Tách riêng tài khoản**

1. Liên hệ với khách hàng để tạo tài khoản riêng
2. Cập nhật thông tin `login_email` và `login_password` cho từng dịch vụ
3. Kiểm tra lại để đảm bảo không còn xung đột

**Tùy chọn 2: Quản lý chung có kiểm soát**

1. Thông báo cho khách hàng về việc dùng chung
2. Thiết lập quy tắc sử dụng
3. Theo dõi thường xuyên

### 4. Báo cáo và theo dõi

1. Sử dụng `/admin/shared-accounts/report` để theo dõi xu hướng
2. Báo cáo định kỳ cho quản lý
3. Cập nhật quy trình xử lý

## Mức độ ưu tiên xử lý

### 🔴 Ưu tiên cao (Xử lý ngay)

-   Tài khoản có ≥5 khách hàng dùng chung
-   Tài khoản có dịch vụ đã hết hạn
-   Tài khoản có khiếu nại từ khách hàng

### 🟡 Ưu tiên trung bình (Xử lý trong tuần)

-   Tài khoản có 3-4 khách hàng dùng chung
-   Tài khoản có dịch vụ sắp hết hạn
-   Tài khoản chưa có quy tắc sử dụng rõ ràng

### 🟢 Ưu tiên thấp (Theo dõi)

-   Tài khoản có 2 khách hàng dùng chung
-   Tài khoản đang hoạt động bình thường
-   Có thỏa thuận rõ ràng về việc dùng chung

## Lưu ý quan trọng

### Bảo mật

-   Chỉ hiển thị mật khẩu khi cần thiết
-   Đảm bảo chỉ admin có quyền truy cập thông tin này
-   Log mọi thao tác liên quan đến tài khoản dùng chung

### Pháp lý

-   Đảm bảo việc dùng chung tuân thủ điều khoản dịch vụ
-   Thông báo rõ ràng cho khách hàng về rủi ro
-   Có thỏa thuận bằng văn bản khi cần thiết

### Kỹ thuật

-   Định kỳ kiểm tra và cập nhật mật khẩu
-   Theo dõi số lượng đăng nhập đồng thời
-   Sẵn sàng xử lý xung đột khi có

## Troubleshooting

### Không hiển thị tài khoản dùng chung

**Nguyên nhân có thể:**

-   Không có dữ liệu (chưa có tài khoản nào dùng chung)
-   Lỗi query do SQL mode strict
-   Lỗi quyền truy cập database

**Cách xử lý:**

1. Kiểm tra dữ liệu: `SELECT login_email, COUNT(*) FROM customer_services WHERE login_email IS NOT NULL GROUP BY login_email HAVING COUNT(*) > 1`
2. Chạy test data: `php artisan test:create-shared-accounts`
3. Kiểm tra error logs

### Lỗi hiển thị chi tiết tài khoản

**Nguyên nhân có thể:**

-   Email chứa ký tự đặc biệt không được encode
-   Không tìm thấy dịch vụ với email đó

**Cách xử lý:**

1. Kiểm tra URL encoding
2. Kiểm tra dữ liệu trong database
3. Sử dụng urlencode() trong route

### Lỗi báo cáo

**Nguyên nhân có thể:**

-   Lỗi GROUP BY với MySQL strict mode
-   Dữ liệu bị thiếu hoặc corrupt

**Cách xử lý:**

1. Kiểm tra MySQL configuration
2. Sử dụng raw query an toàn
3. Kiểm tra và clean dữ liệu

---

**Lưu ý:** Tài liệu này được cập nhật theo phiên bản hiện tại của hệ thống. Vui lòng kiểm tra và cập nhật thường xuyên khi có thay đổi.
