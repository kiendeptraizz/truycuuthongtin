@extends('layouts.admin')

@section('title', 'Thêm khách hàng mới')
@section('page-title', 'Thêm khách hàng mới')

@push('styles')
<style>
    .form-wrapper {
        max-width: 800px;
        margin: 0 auto;
    }
    .form-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .form-card-header {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        padding: 24px 32px;
        color: #fff;
    }
    .form-card-header h2 {
        margin: 0 0 4px 0;
        font-size: 1.5rem;
        font-weight: 600;
    }
    .form-card-header p {
        margin: 0;
        opacity: 0.85;
        font-size: 0.95rem;
    }
    .form-card-body {
        padding: 32px;
    }
    .form-section {
        margin-bottom: 32px;
    }
    .form-section:last-child {
        margin-bottom: 0;
    }
    .form-section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-section-title i {
        color: #6366f1;
    }
    .form-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #475569;
        margin-bottom: 6px;
    }
    .form-label .text-danger {
        color: #ef4444;
    }
    .form-control {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.95rem;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }
    .form-control::placeholder {
        color: #94a3b8;
    }
    .form-text {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 4px;
    }
    .collaborator-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px;
        margin-top: 16px;
    }
    .collaborator-box .form-check {
        margin: 0;
    }
    .collaborator-box .form-check-input {
        width: 18px;
        height: 18px;
        margin-top: 2px;
    }
    .collaborator-box .form-check-label {
        font-weight: 500;
        color: #334155;
        margin-left: 4px;
    }
    .collaborator-box .form-text {
        margin-left: 26px;
        margin-top: 4px;
    }
    .form-card-footer {
        background: #f8fafc;
        padding: 20px 32px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .btn-group-actions {
        display: flex;
        gap: 10px;
    }
    .btn {
        padding: 10px 20px;
        font-size: 0.9rem;
        font-weight: 500;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-secondary {
        background: #fff;
        border: 1px solid #cbd5e1;
        color: #475569;
    }
    .btn-secondary:hover {
        background: #f1f5f9;
        color: #334155;
    }
    .btn-primary {
        background: #6366f1;
        border: none;
        color: #fff;
    }
    .btn-primary:hover {
        background: #4f46e5;
    }
    .btn-success {
        background: #10b981;
        border: none;
        color: #fff;
    }
    .btn-success:hover {
        background: #059669;
    }
    .input-group-text {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-right: none;
        color: #64748b;
    }
    .input-group .form-control {
        border-left: none;
    }
    .input-group:focus-within .input-group-text {
        border-color: #6366f1;
    }
    @media (max-width: 576px) {
        .form-card-body {
            padding: 20px;
        }
        .form-card-header {
            padding: 20px;
        }
        .form-card-footer {
            flex-direction: column;
            padding: 16px 20px;
        }
        .btn-group-actions {
            width: 100%;
            flex-direction: column;
        }
        .btn-group-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-card-header">
            <h2><i class="fas fa-user-plus me-2"></i>Thêm khách hàng mới</h2>
            <p>Nhập thông tin để tạo khách hàng trong hệ thống</p>
        </div>
        
        <form method="POST" action="{{ route('admin.customers.store') }}" id="customerCreateForm">
            @csrf
            @if(isset($returnPage))
                <input type="hidden" name="return_page" value="{{ $returnPage }}">
            @endif
            @if(isset($returnSearch))
                <input type="hidden" name="return_search" value="{{ $returnSearch }}">
            @endif
            
            <div class="form-card-body">
                <!-- Thông tin cơ bản -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-user"></i>
                        Thông tin cơ bản
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label">
                                Tên khách hàng <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="Nguyễn Văn A"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Tên sẽ tự động được định dạng đúng chuẩn</div>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">
                                Mã khách hàng <span class="text-muted">(tùy chọn)</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('customer_code') is-invalid @enderror"
                                   id="customer_code"
                                   name="customer_code"
                                   value="{{ old('customer_code') }}"
                                   placeholder="KUN12345"
                                   maxlength="8"
                                   style="text-transform: uppercase;">
                            @error('customer_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Để trống để tự động tạo mã</div>
                        </div>
                    </div>
                    
                    <div class="collaborator-box">
                        <div class="form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="is_collaborator" 
                                   name="is_collaborator"
                                   value="1"
                                   {{ old('is_collaborator') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_collaborator">
                                <i class="fas fa-handshake text-success me-1"></i>
                                Đây là cộng tác viên
                            </label>
                        </div>
                        <div class="form-text">Nếu tích, mã khách hàng sẽ có định dạng CTV#####</div>
                    </div>
                </div>

                <!-- Thông tin liên hệ -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-address-book"></i>
                        Thông tin liên hệ
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   placeholder="email@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone') }}"
                                   placeholder="0912 345 678">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Ghi chú -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-sticky-note"></i>
                        Ghi chú
                    </div>
                    
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                              id="notes" 
                              name="notes" 
                              rows="3"
                              placeholder="Thêm ghi chú về khách hàng (tùy chọn)...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-card-footer">
                <a href="{{ route('admin.customers.index', ['page' => $returnPage ?? 1, 'search' => $returnSearch ?? '']) }}" 
                   class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại
                </a>
                <div class="btn-group-actions">
                    <button type="submit" class="btn btn-primary" name="action" value="save">
                        <i class="fas fa-save"></i>
                        Lưu khách hàng
                    </button>
                    <button type="submit" class="btn btn-success" name="action" value="save_and_assign">
                        <i class="fas fa-plus"></i>
                        Lưu & Gán dịch vụ
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerCodeInput = document.getElementById('customer_code');
    const collaboratorCheckbox = document.getElementById('is_collaborator');

    // Update pattern based on collaborator status
    function updatePattern() {
        const isCollaborator = collaboratorCheckbox.checked;
        customerCodeInput.placeholder = isCollaborator ? 'CTV12345' : 'KUN12345';
    }

    collaboratorCheckbox.addEventListener('change', function() {
        updatePattern();
        customerCodeInput.value = '';
    });

    // Auto-format customer code
    customerCodeInput.addEventListener('input', function(e) {
        const isCollaborator = collaboratorCheckbox.checked;
        const prefix = isCollaborator ? 'CTV' : 'KUN';
        let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');

        if (value && !value.startsWith(prefix) && /^\d/.test(value)) {
            value = prefix + value;
        }
        if (value.startsWith(prefix)) {
            const numbers = value.substring(3).replace(/\D/g, '');
            value = prefix + numbers.substring(0, 5);
        }
        e.target.value = value;
    });

    // Check uniqueness
    let timeout;
    customerCodeInput.addEventListener('input', function() {
        const value = this.value;
        const isCollaborator = collaboratorCheckbox.checked;
        const pattern = isCollaborator ? /^CTV\d{5}$/ : /^KUN\d{5}$/;
        
        if (pattern.test(value)) {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                fetch(`/admin/customers/check-code/${value}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.exists) {
                            customerCodeInput.classList.add('is-invalid');
                            customerCodeInput.classList.remove('is-valid');
                        } else {
                            customerCodeInput.classList.add('is-valid');
                            customerCodeInput.classList.remove('is-invalid');
                        }
                    });
            }, 500);
        }
    });

    updatePattern();
});
</script>
@endpush
@endsection
