# Thay Đổi Hệ Thống Gói Dịch Vụ

**Ngày thực hiện:** 26/10/2025  
**Mục đích:** Linh hoạt hóa quản lý gói dịch vụ - cho phép một gói có nhiều thời hạn và giá khác nhau

---

## 📋 Tổng quan thay đổi

### ❌ Bỏ ở **Gói Dịch Vụ**:

-   Thời hạn mặc định (ngày)
-   Giá bán
-   Giá nhập

### ✅ Thêm ở **Gán Dịch Vụ cho Khách Hàng**:

-   **Thời hạn (ngày)** - Bắt buộc
-   **Giá nhập** - Bắt buộc
-   **Giá bán** - Bắt buộc
-   **Lợi nhuận** - Tùy chọn (đã có sẵn)

---

## 🗂️ Files đã thay đổi

### 1. **Views - Form Gói Dịch Vụ**

#### `resources/views/admin/service-packages/create.blade.php`

-   ❌ Xóa trường: `default_duration_days`
-   ❌ Xóa trường: `price`
-   ❌ Xóa trường: `cost_price`

#### `resources/views/admin/service-packages/edit.blade.php`

-   ❌ Xóa trường: `default_duration_days`
-   ❌ Xóa trường: `price`
-   ❌ Xóa trường: `cost_price`

### 2. **Views - Form Gán Dịch Vụ**

#### `resources/views/admin/customer-services/create.blade.php`

-   ✅ Thêm trường: `duration_days` (số ngày, required)
-   ✅ Thêm trường: `cost_price` (giá nhập, required, format VNĐ)
-   ✅ Thêm trường: `price` (giá bán, required, format VNĐ)

#### `resources/views/admin/customer-services/assign.blade.php`

-   ✅ Thêm trường: `duration_days` (số ngày, required)
-   ✅ Thêm trường: `cost_price` (giá nhập, required, format VNĐ)
-   ✅ Thêm trường: `price` (giá bán, required, format VNĐ)

### 3. **Controllers**

#### `app/Http/Controllers/Admin/ServicePackageController.php`

**Method `store()`:**

-   ❌ Bỏ validation: `default_duration_days`
-   ❌ Bỏ validation: `price`
-   ❌ Bỏ validation: `cost_price`
-   ❌ Bỏ parse currency logic

**Method `update()`:**

-   ❌ Bỏ validation: `default_duration_days`
-   ❌ Bỏ validation: `price`
-   ❌ Bỏ validation: `cost_price`
-   ❌ Bỏ parse currency logic

#### `app/Http/Controllers/Admin/CustomerServiceController.php`

**Method `store()`:**

-   ✅ Thêm validation: `duration_days` (required, integer, min:1)
-   ✅ Thêm validation: `cost_price` (required, string)
-   ✅ Thêm validation: `price` (required, string)
-   ✅ Thêm parse currency cho cost_price và price
-   ✅ Lưu 3 trường mới vào database

**Method `assignService()`:**

-   ✅ Thêm validation: `duration_days` (required, integer, min:1)
-   ✅ Thêm validation: `cost_price` (required, string)
-   ✅ Thêm validation: `price` (required, string)
-   ✅ Thêm parse currency cho cost_price và price
-   ✅ Lưu 3 trường mới vào database

### 4. **Models**

#### `app/Models/CustomerService.php`

-   ✅ Thêm vào fillable: `duration_days`
-   ✅ Thêm vào fillable: `cost_price`
-   ✅ Thêm vào fillable: `price`

### 5. **Database Migration**

#### `database/migrations/2025_10_26_005115_add_pricing_fields_to_customer_services_table.php`

**Table `customer_services`:**

-   ✅ Thêm cột: `duration_days` (int, nullable)
-   ✅ Thêm cột: `cost_price` (decimal 10,2, nullable)
-   ✅ Thêm cột: `price` (decimal 10,2, nullable)

**Table `service_packages`:**

-   ✅ Chuyển sang nullable: `default_duration_days`
-   ✅ Chuyển sang nullable: `price`
-   ✅ Chuyển sang nullable: `cost_price`

---

## 💡 Lợi ích của thay đổi

### ✅ Trước đây:

-   1 gói "ChatGPT Plus" cần tạo nhiều gói con:
    -   ChatGPT Plus 30 ngày - 99k
    -   ChatGPT Plus 60 ngày - 189k
    -   ChatGPT Plus 90 ngày - 269k
    -   ...

### 🎉 Bây giờ:

-   Chỉ cần **1 gói** "ChatGPT Plus"
-   Khi gán cho khách hàng, nhập:
    -   Thời hạn: 30/60/90 ngày
    -   Giá nhập: 50k/95k/135k
    -   Giá bán: 99k/189k/269k
    -   Lợi nhuận tự động tính

---

## 📊 Workflow mới

### Tạo Gói Dịch Vụ:

1. Chọn danh mục
2. Nhập tên gói (vd: "ChatGPT Plus")
3. Chọn loại tài khoản
4. Nhập mô tả
5. **XONG!** (không cần giá hay thời hạn)

### Gán Dịch Vụ cho Khách Hàng:

1. Chọn khách hàng
2. Chọn gói dịch vụ
3. Nhập email đăng nhập
4. Chọn ngày kích hoạt
5. Chọn ngày hết hạn
6. **NHẬP:** Thời hạn (ngày)
7. **NHẬP:** Giá nhập (VNĐ)
8. **NHẬP:** Giá bán (VNĐ)
9. (Tùy chọn) Nhập lợi nhuận
10. Ghi chú

---

## ✅ Kiểm tra đã thực hiện

-   ✅ Database migration thành công
-   ✅ Cột mới đã được tạo trong `customer_services`
-   ✅ Cột cũ đã nullable trong `service_packages`
-   ✅ Model đã cập nhật fillable
-   ✅ Controllers đã cập nhật validation
-   ✅ Forms đã hiển thị đúng trường
-   ✅ Cache đã được clear

---

## 🚀 Kết quả

**Hệ thống bây giờ:**

-   ✅ Linh hoạt hơn trong quản lý giá và thời hạn
-   ✅ Giảm số lượng gói dịch vụ cần tạo
-   ✅ Tự động tính lợi nhuận từ giá bán - giá nhập
-   ✅ Dễ dàng theo dõi giá vốn và doanh thu

**Sử dụng:**

1. Tạo gói dịch vụ (chỉ cần tên + loại)
2. Khi gán cho khách, nhập cụ thể: thời hạn, giá nhập, giá bán
3. Hệ thống tự động tính và theo dõi lợi nhuận

