# HƯỚNG DẪN QUẢN LÝ THÔNG TIN TÀI KHOẢN DÙNG CHUNG

**Ngày cập nhật:** 11/07/2025  
**Phiên bản:** 2.0

## 🔐 CÁC TRƯỜNG THÔNG TIN MỚI

### 1. Thông tin đăng nhập

-   **`login_password`** (đã có): Mật khẩu tài khoản
-   **`password_expires_at`** (mới): Ngày hết hạn mật khẩu
-   **`is_password_shared`** (mới): Đánh dấu đã chia sẻ mật khẩu với khách hàng

### 2. Xác thực 2 yếu tố (2FA)

-   **`two_factor_code`** (mới): Mã 2FA hoặc Secret Key
-   **`two_factor_updated_at`** (mới): Thời gian cập nhật 2FA gần nhất
-   **`recovery_codes`** (mới): Danh sách mã khôi phục (lưu dạng JSON array)

### 3. Ghi chú và hướng dẫn

-   **`shared_account_notes`** (mới): Ghi chú nội bộ về tài khoản
-   **`customer_instructions`** (mới): Hướng dẫn/ghi chú gửi cho khách hàng
-   **`shared_with_customers`** (mới): Danh sách khách hàng đã chia sẻ (JSON array)

## 📝 CÁCH SỬ DỤNG

### Thêm/Chỉnh sửa thông tin

1. Vào **Admin Panel > Quản lý tài khoản dùng chung**
2. Chọn tài khoản cần chỉnh sửa
3. Click nút **"Chỉnh sửa thông tin"**
4. Cập nhật các trường cần thiết
5. Lưu thay đổi

### Xem thông tin

-   **Danh sách tổng quan:** Hiển thị thông tin bảo mật tóm tắt
-   **Chi tiết tài khoản:** Hiển thị đầy đủ thông tin (có thể ẩn/hiện password)
-   **Ghi chú:** Phân biệt rõ ghi chú nội bộ và hướng dẫn khách hàng

## 🔒 BẢO MẬT VÀ AN TOÀN

### Nguyên tắc bảo mật

1. **Không tự động thêm dữ liệu:** Hệ thống chỉ cho phép chỉnh sửa thủ công
2. **Mã hóa hiển thị:** Mật khẩu và mã 2FA được ẩn mặc định
3. **Kiểm soát truy cập:** Chỉ admin mới có quyền xem/sửa
4. **Ghi log:** Mọi thay đổi đều được theo dõi qua `updated_at`

### Quy trình chia sẻ với khách hàng

1. Cập nhật đầy đủ thông tin tài khoản
2. Tạo hướng dẫn chi tiết trong `customer_instructions`
3. Đánh dấu `is_password_shared = true`
4. Gửi thông tin qua kênh bảo mật (không email thường)
5. Cập nhật danh sách khách hàng đã chia sẻ

## 📊 HIỂN THỊ THÔNG TIN

### Trong danh sách tổng quan

-   **Cột "Bảo mật":** Hiển thị các badge:
    -   🟢 **Có mật khẩu:** Đã cập nhật mật khẩu
    -   🔵 **2FA:** Đã cấu hình xác thực 2 yếu tố
    -   🟡 **Đã chia sẻ:** Đã chia sẻ thông tin với khách hàng

### Trong chi tiết tài khoản

-   **Thông tin đăng nhập:** Email, mật khẩu, ngày hết hạn
-   **Xác thực 2FA:** Mã 2FA, thời gian cập nhật, mã khôi phục
-   **Ghi chú:** Phân biệt nội bộ và khách hàng

## 🔧 CẤU TRÚC DATABASE

### Các trường đã thêm vào bảng `customer_services`:

```sql
-- Thông tin 2FA
two_factor_code VARCHAR(100) NULL COMMENT 'Mã 2FA của tài khoản dùng chung'
two_factor_updated_at DATETIME NULL COMMENT 'Ngày cập nhật 2FA gần nhất'
recovery_codes TEXT NULL COMMENT 'Danh sách mã khôi phục (JSON format)'

-- Thông tin mật khẩu
password_expires_at DATETIME NULL COMMENT 'Ngày hết hạn mật khẩu'
is_password_shared TINYINT(1) DEFAULT 0 COMMENT 'Có phải mật khẩu được chia sẻ không'

-- Ghi chú
shared_account_notes TEXT NULL COMMENT 'Ghi chú riêng cho tài khoản dùng chung'
customer_instructions TEXT NULL COMMENT 'Hướng dẫn/ghi chú gửi cho khách hàng'
shared_with_customers JSON NULL COMMENT 'Danh sách khách hàng đã chia sẻ thông tin'
```

## 🚨 LƯU Ý QUAN TRỌNG

### Về dữ liệu

-   **KHÔNG tự động thêm dữ liệu:** Tất cả đều phải nhập thủ công
-   **Đồng bộ hóa:** Khi cập nhật, tất cả services cùng email đều được cập nhật
-   **Backup:** Luôn tạo backup trước khi thực hiện thay đổi lớn

### Về bảo mật

-   **Mã 2FA và recovery codes:** Cần lưu trữ an toàn
-   **Chia sẻ thông tin:** Sử dụng kênh bảo mật
-   **Kiểm tra định kỳ:** Rà soát thông tin tài khoản thường xuyên

### Về sử dụng

-   **Cập nhật thường xuyên:** Đặc biệt là ngày hết hạn mật khẩu
-   **Ghi chú chi tiết:** Để dễ quản lý và hỗ trợ khách hàng
-   **Kiểm tra trước khi chia sẻ:** Đảm bảo thông tin chính xác

## 📞 HỖ TRỢ

Nếu gặp vấn đề khi sử dụng chức năng mới:

1. Kiểm tra log trong `storage/logs/laravel.log`
2. Đảm bảo đã chạy migration thành công
3. Xác nhận quyền truy cập admin
4. Liên hệ IT support nếu cần thiết

---

_Tài liệu này sẽ được cập nhật khi có thay đổi về chức năng._
