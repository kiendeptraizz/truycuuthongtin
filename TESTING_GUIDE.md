# HƯỚNG DẪN KIỂM TRA SỬA LỖI CỘT THAO TÁC

## 🧪 CÁCH KIỂM TRA

### 1. Truy cập trang quản lý khách hàng

```
URL: http://localhost/admin/customers
Hoặc: http://your-domain.com/admin/customers
```

### 2. Kiểm tra trên desktop (1920x1080)

-   [x] Mở browser ở full screen
-   [x] Zoom: 100%
-   [x] Kiểm tra có thể thấy đầy đủ 4 nút trong cột "Thao tác"
-   [x] Các nút: Xem (👁️), Chỉnh sửa (✏️), Gán dịch vụ (➕), Xóa (🗑️)

### 3. Kiểm tra responsive

-   [x] Resize browser xuống 1600px → Kiểm tra nút vẫn hiển thị
-   [x] Resize xuống 1200px → Cột liên hệ ẩn, nút vẫn OK
-   [x] Resize xuống 768px → Mobile view, nút nhỏ nhưng vẫn dùng được

### 4. Kiểm tra chức năng

-   [x] Click nút "Xem" → Mở trang chi tiết khách hàng
-   [x] Click nút "Chỉnh sửa" → Mở form chỉnh sửa
-   [x] Click nút "Gán dịch vụ" → Mở form gán dịch vụ
-   [x] Click nút "Xóa" → Hiện modal xác nhận xóa

## ⚠️ NẾU VẪN CÓ VẤN ĐỀ

### Clear cache browser

```bash
Ctrl + F5 (Windows)
Cmd + Shift + R (Mac)
```

### Kiểm tra console browser

```
F12 → Console tab → Xem có lỗi CSS/JS không
```

### Kiểm tra CSS load đúng

```
F12 → Network tab → Refresh →
Tìm file: customer-table-fix.css (status 200)
Tìm file: table-fix-override.css (status 200)
```

## 📞 BÁO CÁO VẤN ĐỀ

Nếu vẫn còn vấn đề, hãy báo cáo kèm thông tin:

-   Kích thước màn hình
-   Trình duyệt + version
-   Screenshot cột thao tác
-   Console errors (nếu có)

## ✅ KẾT QUẢ MONG ĐỢI

Sau khi sửa, bạn sẽ thấy:

-   Cột "Thao tác" luôn hiển thị ở góc phải bảng
-   4 nút hành động rõ ràng và có thể click được
-   Không cần cuộn ngang để truy cập
-   Tooltip hiện khi hover lên nút
-   Responsive tốt trên mọi kích thước màn hình
