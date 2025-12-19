# KIỂM TRA VALIDATION FORM

## Vấn đề hiện tại:
Khi nhập lợi nhuận > 1 triệu (VD: 1.000.000), không thể submit form.

## Các nguyên nhân có thể:

### 1. **Validation HTML5 của các field khác**
Có thể một field khác đang bị lỗi validation, khiến form không submit được.

### 2. **JavaScript đang chặn submit**
Có thể có event listener `submit` đang return false hoặc preventDefault().

### 3. **Hidden input `duration_days` chưa có giá trị**
Nếu `duration_days` là required nhưng chưa được set, form sẽ không submit.

## Cách kiểm tra:

### Bước 1: Mở Developer Console (F12)

### Bước 2: Điền form đầy đủ:
- Chọn gói dịch vụ
- Nhập email đăng nhập
- Chọn ngày kích hoạt
- **Nhập thời hạn: 1 tháng** (quan trọng!)
- Nhập giá vốn: 100000
- Nhập giá bán: 200000
- Nhập lợi nhuận: 1000000

### Bước 3: Trước khi click "Gán dịch vụ", kiểm tra:

**Trong Console, chạy lệnh này:**
```javascript
// Kiểm tra form validity
const form = document.querySelector('form');
console.log('Form valid:', form.checkValidity());

// Kiểm tra từng field
const inputs = form.querySelectorAll('input[required], select[required]');
inputs.forEach(input => {
    if (!input.checkValidity()) {
        console.log('Invalid field:', input.name, input.id, input.validationMessage);
    }
});

// Kiểm tra duration_days
const durationDays = document.getElementById('duration_days');
console.log('duration_days value:', durationDays.value);
```

### Bước 4: Click "Gán dịch vụ" và xem Console

Nếu có lỗi, Console sẽ hiển thị field nào đang invalid.

## Giải pháp tạm thời:

Nếu vẫn không submit được, hãy thử:

1. **Nhập lợi nhuận không có dấu chấm:**
   - Thay vì: `1.000.000`
   - Nhập: `1000000`
   - JavaScript sẽ tự động format thành `1.000.000`

2. **Kiểm tra Console có lỗi JavaScript không**

3. **Chụp màn hình Console và gửi cho tôi**

