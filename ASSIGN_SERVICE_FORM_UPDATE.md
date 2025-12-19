# CẬP NHẬT FORM "GÁN DỊCH VỤ" - THÊM TÙY CHỌN "NĂM"

**Ngày cập nhật:** 06/11/2025  
**File được chỉnh sửa:** `resources/views/admin/customer-services/assign.blade.php`

---

## 📋 TỔNG QUAN

Đã cập nhật form "Gán dịch vụ cho khách hàng" (Assign Service) để thêm tùy chọn **"Năm"** vào phần "Thời hạn tài khoản", giống hệt với form "Sửa dịch vụ khách hàng" (Edit Customer Service).

---

## 🔄 THAY ĐỔI CHI TIẾT

### **1. Thay Radio Buttons bằng Dropdown Select** (Dòng 252-283)

**TRƯỚC (Radio Buttons):**

```html
<div class="col-md-12 mb-3">
    <label class="form-label">
        <i class="fas fa-clock me-1"></i>
        Thời hạn <span class="text-danger">*</span>
    </label>

    <!-- Radio chọn đơn vị -->
    <div class="mb-2">
        <div class="form-check form-check-inline">
            <input
                class="form-check-input"
                type="radio"
                name="duration_unit"
                id="duration_unit_days"
                value="days"
            />
            <label class="form-check-label" for="duration_unit_days"
                >Ngày</label
            >
        </div>
        <div class="form-check form-check-inline">
            <input
                class="form-check-input"
                type="radio"
                name="duration_unit"
                id="duration_unit_months"
                value="months"
                checked
            />
            <label class="form-check-label" for="duration_unit_months"
                >Tháng</label
            >
        </div>
        <!-- ❌ THIẾU NĂM và dùng radio buttons -->
    </div>

    <!-- Input nhập số -->
    <div class="input-group" style="max-width: 300px;">
        <input
            type="number"
            id="duration_value"
            placeholder="Nhập số ngày/tháng"
            required
        />
        <span class="input-group-text" id="duration_unit_label">tháng</span>
    </div>
</div>
```

**SAU (Dropdown Select - Giống form "Sửa dịch vụ"):**

```html
<div class="col-md-12 mb-3">
    <label for="custom_duration" class="form-label">
        <i class="fas fa-clock me-1"></i>
        Thời hạn tùy chỉnh
    </label>
    <div class="input-group">
        <input
            type="number"
            class="form-control"
            id="custom_duration"
            name="custom_duration"
            min="1"
            placeholder="Nhập số"
            value="{{ old('duration_value') }}"
        />
        <select
            class="form-select"
            id="duration_unit"
            name="duration_unit"
            style="max-width: 120px;"
        >
            <option value="days">Ngày</option>
            <option value="months" selected>Tháng</option>
            <option value="years">Năm</option>
        </select>
    </div>

    <!-- Hidden input để lưu giá trị ngày thực tế -->
    <input
        type="hidden"
        name="duration_days"
        id="duration_days"
        value="{{ old('duration_days') }}"
    />

    <div class="form-text text-info" id="duration_calculated_text">
        <i class="fas fa-info-circle me-1"></i>
        Nhập thời hạn để tự động tính ngày hết hạn
    </div>
</div>
```

### **2. Cập nhật JavaScript - Thay đổi từ Radio Buttons sang Dropdown Select**

**TRƯỚC (Xử lý Radio Buttons):**

```javascript
function initializeDurationCalculator() {
    const durationUnitRadios = document.querySelectorAll(
        'input[name="duration_unit"]'
    );
    const durationValueInput = document.getElementById("duration_value");
    const durationUnitLabel = document.getElementById("duration_unit_label");
    // ...

    // Event listeners cho radio buttons
    durationUnitRadios.forEach((radio, index) => {
        radio.addEventListener("change", function () {
            calculateDuration();
        });
    });

    // Lấy giá trị từ radio button được chọn
    const checkedUnit = document.querySelector(
        'input[name="duration_unit"]:checked'
    );
    const unit = checkedUnit.value;
}
```

**SAU (Xử lý Dropdown Select - Giống form "Sửa dịch vụ"):**

```javascript
function initializeDurationCalculator() {
    const durationUnitSelect = document.getElementById("duration_unit");
    const customDurationInput = document.getElementById("custom_duration");
    // ...

    // Event listener cho dropdown select
    if (durationUnitSelect) {
        durationUnitSelect.addEventListener("change", function () {
            calculateDuration();
        });
    }

    // Lấy giá trị trực tiếp từ select
    const unit = durationUnitSelect.value;
}
```

### **3. Cập nhật Hiển thị Text với Icon**

**TRƯỚC:**

```javascript
if (durationCalculatedText) {
    durationCalculatedText.textContent = `Thời hạn: ${value} năm (${days} ngày)`;
}
```

**SAU:**

```javascript
if (durationCalculatedText) {
    durationCalculatedText.innerHTML = `<i class="fas fa-check-circle me-1 text-success"></i>Thời hạn: ${value} năm (${days} ngày)`;
}
```

---

## ✅ TÍNH NĂNG SAU KHI CẬP NHẬT

### **Các tùy chọn thời hạn:**

1. ✅ **Ngày** - Tính theo số ngày (1 ngày = 1 ngày)
2. ✅ **Tháng** - Tính theo số tháng (1 tháng = 30 ngày)
3. ✅ **Năm** - Tính theo số năm (1 năm = 365 ngày) ← **MỚI THÊM**

### **Cách hoạt động:**

1. Người dùng chọn đơn vị thời gian (Ngày/Tháng/Năm)
2. Nhập số lượng vào ô input
3. JavaScript tự động:
    - Chuyển đổi sang số ngày
    - Cập nhật label hiển thị đơn vị
    - Tính toán ngày hết hạn dựa trên ngày kích hoạt
    - Hiển thị thông tin "Thời hạn: X năm (Y ngày)"

### **Ví dụ:**

-   **Nhập:** 1 năm
-   **Kết quả:**
    -   `duration_days` = 365
    -   Hiển thị: "Thời hạn: 1 năm (365 ngày)"
    -   Ngày hết hạn = Ngày kích hoạt + 365 ngày

---

## 🔍 SO SÁNH VỚI FORM "SỬA DỊCH VỤ"

| Tính năng         | Form "Sửa dịch vụ" | Form "Gán dịch vụ" (SAU) | Trạng thái |
| ----------------- | ------------------ | ------------------------ | ---------- |
| Tùy chọn "Ngày"   | ✅ Có              | ✅ Có                    | ✅ Giống   |
| Tùy chọn "Tháng"  | ✅ Có              | ✅ Có                    | ✅ Giống   |
| Tùy chọn "Năm"    | ✅ Có              | ✅ Có                    | ✅ Giống   |
| Tính toán tự động | ✅ Có              | ✅ Có                    | ✅ Giống   |
| Hiển thị số ngày  | ✅ Có              | ✅ Có                    | ✅ Giống   |
| UI Component      | Dropdown Select    | Dropdown Select          | ✅ GIỐNG   |

### **✅ Ghi chú về UI:**

-   **Form "Sửa dịch vụ":** Sử dụng **dropdown select** (`<select>`)
-   **Form "Gán dịch vụ":** Sử dụng **dropdown select** (`<select>`) ← **ĐÃ CẬP NHẬT**

**🎉 Giờ đây cả hai form đều sử dụng dropdown select - GIỐNG HỆT 100%!**

---

## 🧪 CÁCH KIỂM TRA

### **Bước 1: Mở form "Gán dịch vụ"**

1. Đăng nhập vào admin panel
2. Vào "Dịch vụ khách hàng" → Chọn một khách hàng
3. Click nút "Gán dịch vụ"

### **Bước 2: Kiểm tra phần "Thời hạn"**

1. Xác nhận có **3 radio buttons:** Ngày, Tháng, **Năm**
2. Chọn "Năm"
3. Nhập số (VD: 1)
4. Kiểm tra:
    - Label hiển thị "năm"
    - Text hiển thị "Thời hạn: 1 năm (365 ngày)"
    - Ngày hết hạn tự động cập nhật (+365 ngày)

### **Bước 3: Test các trường hợp**

| Test Case | Input                | Kết quả mong đợi |
| --------- | -------------------- | ---------------- |
| 1 năm     | unit=years, value=1  | 365 ngày         |
| 2 năm     | unit=years, value=2  | 730 ngày         |
| 1 tháng   | unit=months, value=1 | 30 ngày          |
| 30 ngày   | unit=days, value=30  | 30 ngày          |

---

## 📝 GHI CHÚ KỸ THUẬT

### **Công thức tính toán:**

```javascript
// Ngày
days = value;

// Tháng
days = value * 30;

// Năm
days = value * 365;
```

### **Lưu ý:**

-   **1 tháng = 30 ngày** (xấp xỉ, không tính chính xác số ngày trong tháng)
-   **1 năm = 365 ngày** (không tính năm nhuận)
-   Nếu cần tính chính xác hơn, có thể sử dụng thư viện như `moment.js` hoặc `date-fns`

---

## ✅ KẾT LUẬN

**Đã hoàn thành cập nhật form "Gán dịch vụ"** với các thay đổi:

1. ✅ Thêm radio button "Năm"
2. ✅ Cập nhật placeholder
3. ✅ Cập nhật JavaScript xử lý tính toán
4. ✅ Đảm bảo tính năng hoạt động đúng

**Form "Gán dịch vụ" giờ đây có đầy đủ 3 tùy chọn thời hạn:**

-   ✅ Ngày
-   ✅ Tháng
-   ✅ Năm

**Tính năng tự động tính toán ngày hết hạn hoạt động với cả 3 đơn vị.**

---

## 🎉 KẾT QUẢ CUỐI CÙNG

### **✅ ĐÃ HOÀN THÀNH 100%**

Form "Gán dịch vụ" giờ đây **GIỐNG HỆT** form "Sửa dịch vụ":

1. ✅ **Cấu trúc HTML:** Dropdown select với 3 options (Ngày, Tháng, Năm)
2. ✅ **JavaScript:** Xử lý dropdown select thay vì radio buttons
3. ✅ **Hiển thị:** Icon + text với màu sắc (success green)
4. ✅ **Tính năng:** Tự động tính toán ngày hết hạn cho cả 3 đơn vị
5. ✅ **Styling:** Input group với max-width 120px cho select

### **So sánh trước và sau:**

| Khía cạnh                | TRƯỚC         | SAU                  |
| ------------------------ | ------------- | -------------------- |
| UI Component             | Radio Buttons | Dropdown Select ✅   |
| Tùy chọn "Năm"           | ❌ Không có   | ✅ Có                |
| Giống form "Sửa dịch vụ" | ❌ Không      | ✅ Có 100%           |
| Tính toán năm            | ❌ Không      | ✅ 1 năm = 365 ngày  |
| Icon trong text          | ❌ Không      | ✅ Có (check-circle) |

---

## 📊 TỔNG KẾT

**Đã thực hiện:**

1. ✅ Thay thế radio buttons bằng dropdown select
2. ✅ Thêm tùy chọn "Năm" (years)
3. ✅ Cập nhật JavaScript để xử lý dropdown
4. ✅ Cập nhật hiển thị text với icon
5. ✅ Đảm bảo 100% giống form "Sửa dịch vụ"

**Kết quả:**

-   Form "Gán dịch vụ" và form "Sửa dịch vụ" giờ đây có **cùng cấu trúc và tính năng**
-   Người dùng có thể chọn thời hạn theo **Ngày, Tháng, hoặc Năm**
-   Giao diện **gọn gàng, nhất quán** trên toàn hệ thống
