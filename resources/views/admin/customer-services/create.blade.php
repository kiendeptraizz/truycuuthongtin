@extends('layouts.admin')

@section('title', 'Gán dịch vụ mới')
@section('page-title', 'Gán dịch vụ mới')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-plus me-2"></i>Gán Dịch Vụ Mới</h1>
            <p class="mb-0 text-muted">Kích hoạt dịch vụ cho khách hàng một cách nhanh chóng</p>
        </div>
        <div>
            <a href="{{ route('admin.customer-services.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-plus me-2"></i>
                        Thông Tin Gán Dịch Vụ
                    </h6>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.customer-services.store') }}" id="assignServiceForm">
                        @csrf
                    
                        <!-- Customer Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <x-customer-search-selector
                                    name="customer_id"
                                    id="customer_id"
                                    :customers="$customers"
                                    label="Khách hàng"
                                    placeholder="Tìm kiếm khách hàng theo tên, mã KH hoặc email..."
                                    :required="true"
                                    help-text="Tìm kiếm theo tên hoặc mã khách hàng ({{ $customers->count() }} khách hàng)"
                                />
                            </div>
                        
                        <div class="col-md-12 mb-4">
                            <label class="form-label">
                                <i class="fas fa-box me-1"></i>
                                Gói dịch vụ <span class="text-danger">*</span>
                                <small class="text-muted ms-2">(Phân loại theo danh mục và loại tài khoản)</small>
                            </label>

                            <x-service-package-grid-selector
                                :service-packages="$servicePackages"
                                :account-type-priority="$accountTypePriority"
                                name="service_package_id"
                                id="service_package_id"
                                :required="true"
                                placeholder="Chọn gói dịch vụ phù hợp..."
                            />
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="login_email" class="form-label">
                                Email đăng nhập <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('login_email') is-invalid @enderror" 
                                   id="login_email" 
                                   name="login_email" 
                                   value="{{ old('login_email') }}" 
                                   required>
                            @error('login_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="login_password" class="form-label">Mật khẩu</label>
                            <input type="text" 
                                   class="form-control @error('login_password') is-invalid @enderror" 
                                   id="login_password" 
                                   name="login_password" 
                                   value="{{ old('login_password') }}">
                            @error('login_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Để trống nếu không muốn lưu mật khẩu</div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="activated_at" class="form-label">
                                Ngày kích hoạt <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('activated_at') is-invalid @enderror" 
                                   id="activated_at" 
                                   name="activated_at" 
                                   value="{{ old('activated_at', now()->format('Y-m-d')) }}" 
                                   required>
                            @error('activated_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="expires_at" class="form-label">
                                Ngày hết hạn <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('expires_at') is-invalid @enderror" 
                                   id="expires_at" 
                                   name="expires_at" 
                                   value="{{ old('expires_at', now()->addDays(30)->format('Y-m-d')) }}" 
                                   required>
                            @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">
                                Trạng thái <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>
                                    Hoạt động
                                </option>
                                <option value="expired" {{ old('status') === 'expired' ? 'selected' : '' }}>
                                    Hết hạn
                                </option>
                                <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>
                                    Đã hủy
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="internal_notes" class="form-label">Ghi chú nội bộ</label>
                            <textarea class="form-control @error('internal_notes') is-invalid @enderror" 
                                      id="internal_notes" 
                                      name="internal_notes" 
                                      rows="3">{{ old('internal_notes') }}</textarea>
                            @error('internal_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Thông tin lợi nhuận -->
                        <div class="col-md-12 mb-4">
                            <div class="card border-success">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-success">
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        Thông tin lợi nhuận (Tùy chọn)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="profit_amount" class="form-label">
                                                <i class="fas fa-dollar-sign me-1"></i>
                                                Số tiền lãi
                                            </label>
                                            <div class="input-group">
                                                <input type="number" 
                                                       class="form-control @error('profit_amount') is-invalid @enderror" 
                                                       id="profit_amount" 
                                                       name="profit_amount" 
                                                       min="0" 
                                                       step="1000" 
                                                       placeholder="Nhập số tiền lãi"
                                                       value="{{ old('profit_amount') }}">
                                                <span class="input-group-text">VNĐ</span>
                                            </div>
                                            @error('profit_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Nhập số tiền lãi thu được từ đơn hàng này (để trống nếu chưa xác định)</div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="profit_notes" class="form-label">
                                                <i class="fas fa-sticky-note me-1"></i>
                                                Ghi chú lợi nhuận
                                            </label>
                                            <textarea class="form-control @error('profit_notes') is-invalid @enderror"
                                                      id="profit_notes"
                                                      name="profit_notes"
                                                      rows="3"
                                                      placeholder="Ghi chú về lợi nhuận...">{{ old('profit_notes') }}</textarea>
                                            @error('profit_notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.customer-services.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Quay lại
                        </a>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Gán dịch vụ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceSelect = document.getElementById('service_package_id');
    const activatedInput = document.getElementById('activated_at');
    const expiresInput = document.getElementById('expires_at');
    
    serviceSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const duration = parseInt(selectedOption.dataset.duration) || 30;
            const activatedDate = new Date(activatedInput.value);
            const expiresDate = new Date(activatedDate);
            expiresDate.setDate(expiresDate.getDate() + duration);
            
            expiresInput.value = expiresDate.toISOString().split('T')[0];
        }
    });
    
    activatedInput.addEventListener('change', function() {
        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
        if (selectedOption.value) {
            const duration = parseInt(selectedOption.dataset.duration) || 30;
            const activatedDate = new Date(this.value);
            const expiresDate = new Date(activatedDate);
            expiresDate.setDate(expiresDate.getDate() + duration);
            
            expiresInput.value = expiresDate.toISOString().split('T')[0];
        }
    });

    // Format profit amount input with thousand separators
    const profitAmountInput = document.getElementById('profit_amount');
    if (profitAmountInput) {
        // Format existing value on page load
        if (profitAmountInput.value) {
            profitAmountInput.value = formatNumberInput(profitAmountInput.value);
        }

        // Format as user types
        profitAmountInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\./g, ''); // Remove existing dots
            if (value && !isNaN(value)) {
                e.target.value = formatNumberInput(value);
            }
        });

        // Clean value before form submission (remove dots for proper validation)
        profitAmountInput.closest('form').addEventListener('submit', function() {
            profitAmountInput.value = profitAmountInput.value.replace(/\./g, '');
        });
    }
});

// Function to format number with thousand separators
function formatNumberInput(number) {
    return parseInt(number).toLocaleString('vi-VN');
}
</script>
@endpush
@endsection
