# BẢO VỆ DỮ LIỆU THẬT - HƯỚNG DẪN AN TOÀN

## ⚠️ CẢNH BÁO QUAN TRỌNG

Hệ thống hiện tại có một số command có thể **THAY ĐỔI HOẶC XÓA DỮ LIỆU THẬT** của bạn. Để bảo vệ dữ liệu, tất cả các command nguy hiểm đã được cập nhật với các biện pháp bảo vệ.

## 🛡️ CÁC COMMAND ĐÃ ĐƯỢC BẢO VỆ

### 1. Command tạo dữ liệu test (NGUY HIỂM - ĐÃ ĐƯỢC BẢO VỆ)

```bash
# CŨ (không an toàn):
php artisan test:create-shared-accounts
php artisan test:create-today-data

# MỚI (an toàn - yêu cầu xác nhận):
php artisan test:create-shared-accounts --force
php artisan test:create-today-data --force
```

### 2. Command xóa toàn bộ khách hàng (CỰC KỲ NGUY HIỂM - ĐÃ ĐƯỢC BẢO VỆ)

```bash
# CŨ (không an toàn):
php artisan customers:delete-all --force

# MỚI (an toàn - yêu cầu xác nhận rõ ràng):
php artisan customers:delete-all --i-understand-this-will-delete-real-data --force
```

## ✅ CÁC COMMAND AN TOÀN (CHỈ ĐỌC DỮ LIỆU)

### Xem dữ liệu tài khoản dùng chung

```bash
# Xem tất cả tài khoản dùng chung
php artisan shared:view-data

# Lọc theo email cụ thể
php artisan shared:view-data --email="chatgpt.shared@company.com"
```

### Xem dữ liệu kích hoạt theo ngày

```bash
# Xem dịch vụ kích hoạt hôm nay
php artisan today:view-data

# Xem dịch vụ kích hoạt ngày cụ thể
php artisan today:view-data --date="2025-07-10"
```

### Các command báo cáo (an toàn)

```bash
# Báo cáo nhắc nhở
php artisan reminder:report

# Backup dữ liệu
php artisan backup:customers

# Gửi nhắc nhở (chỉ cập nhật trường nhắc nhở)
php artisan reminder:send-expiration --mark-only
```

## 🔍 PHÁT HIỆN VẤN ĐỀ ĐÃ SỬA

Qua kiểm tra, tôi phát hiện:

1. **Command `test:create-shared-accounts`** đã tạo dữ liệu test với các email:

    - `chatgpt.shared@company.com`
    - `netflix.family@company.com`
    - `adobe.team@company.com`

2. **Dữ liệu thật của bạn vẫn còn nguyên vẹn**, chỉ bị trộn lẫn với dữ liệu test.

3. **Các ngày hết hạn và email đăng nhập KHÔNG bị thay đổi** ngoài ý muốn.

## 📋 KHUYẾN NGHỊ

### 1. Chỉ sử dụng command an toàn

-   Luôn sử dụng `shared:view-data` và `today:view-data` để xem dữ liệu
-   Tránh chạy command có `--force` nếu không chắc chắn

### 2. Backup định kỳ

```bash
php artisan backup:customers
```

### 3. Kiểm tra trước khi chạy command

-   Đọc kỹ description của command
-   Chú ý các từ khóa: "test", "delete", "create", "force"

## 🚨 NẾU CẦN XÓA DỮ LIỆU TEST

Nếu bạn muốn xóa dữ liệu test (các email `*.shared@company.com`, `netflix.family@company.com`, etc):

```bash
# 1. Backup trước
php artisan backup:customers

# 2. Xóa từng email cụ thể (an toàn hơn)
# Sử dụng giao diện web hoặc tinker
```

## 🔧 CÁC THAY ĐỔI ĐÃ THỰC HIỆN

1. ✅ Thêm `--force` flag cho command tạo dữ liệu test
2. ✅ Thêm confirmation prompt bắt buộc
3. ✅ Tạo command mới chỉ đọc dữ liệu (`shared:view-data`, `today:view-data`)
4. ✅ Bảo vệ command xóa dữ liệu với flag phức tạp
5. ✅ Cập nhật description để cảnh báo rõ ràng

## 📞 HỖ TRỢ

Nếu bạn cần:

-   Xóa dữ liệu test cụ thể
-   Phục hồi dữ liệu từ backup
-   Kiểm tra thêm về tính toàn vẹn dữ liệu

Hãy cho tôi biết và tôi sẽ hướng dẫn chi tiết!
