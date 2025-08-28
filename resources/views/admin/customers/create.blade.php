@extends('layouts.admin')

@section('title', 'Thêm đầy đủ thông tin khách hàng')
@section('page-title', 'Thêm đầy đủ thông tin khách hàng')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-10">
        <div class="form-container">
            <div class="form-header">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper me-3" style="width: 50px; height: 50px; background: var(--primary-gradient); border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-plus fa-lg text-white"></i>
                    </div>
                    <div>
                        <h1 class="form-title">Thêm đầy đủ thông tin khách hàng</h1>
                        <p class="form-subtitle">Nhập thông tin chi tiết để tạo khách hàng mới trong hệ thống</p>
                    </div>
                </div>
            </div>
            
            <form method="POST" action="{{ route('admin.customers.store') }}"
                  class="enhanced-form" data-auto-save="true" id="customerCreateForm">
                @csrf

                <!-- Hidden fields để giữ thông tin trang hiện tại -->
                @if(isset($returnPage))
                    <input type="hidden" name="return_page" value="{{ $returnPage }}">
                @endif
                @if(isset($returnSearch))
                    <input type="hidden" name="return_search" value="{{ $returnSearch }}">
                @endif
                
                <div class="form-body">
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-user text-primary"></i>
                            Thông tin cơ bản
                        </h3>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">
                                        Tên khách hàng <span class="required">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           placeholder="Nhập tên khách hàng"
                                           required
                                           minlength="2"
                                           data-capitalize="true">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_code" class="form-label">
                                        Mã khách hàng <span class="text-muted">(tùy chọn)</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-barcode"></i>
                                        </span>
                                        <input type="text"
                                               class="form-control @error('customer_code') is-invalid @enderror"
                                               id="customer_code"
                                               name="customer_code"
                                               value="{{ old('customer_code') }}"
                                               placeholder="KUN12345"
                                               pattern="^KUN\d{5}$"
                                               maxlength="8"
                                               style="text-transform: uppercase;">
                                    </div>
                                    @error('customer_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-info-circle text-info"></i>
                                        Để trống để tự động tạo mã theo định dạng KUN#####
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-address-book text-info"></i>
                            Thông tin liên hệ
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email') }}"
                                               placeholder="example@email.com">
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-phone"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" 
                                               name="phone" 
                                               value="{{ old('phone') }}"
                                               placeholder="0123456789">
                                    </div>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-sticky-note text-warning"></i>
                            Ghi chú
                        </h3>
                        
                        <div class="form-group">
                            <label for="notes" class="form-label">Ghi chú</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="4"
                                      placeholder="Nhập ghi chú về khách hàng (tùy chọn)">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Bạn có thể thêm thông tin bổ sung về khách hàng</div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.customers.index', ['page' => $returnPage ?? 1, 'search' => $returnSearch ?? '']) }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary" name="action" value="save">
                        <i class="fas fa-save me-1"></i>
                        Lưu khách hàng
                    </button>
                    <button type="submit" class="btn btn-success" name="action" value="save_and_assign">
                        <i class="fas fa-plus me-1"></i>
                        Lưu & Gán dịch vụ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerCodeInput = document.getElementById('customer_code');
    const form = document.getElementById('customerCreateForm');

    // Auto-format customer code input
    if (customerCodeInput) {
        customerCodeInput.addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');

            // Auto-add KUN prefix if user starts typing numbers
            if (value && !value.startsWith('KUN') && /^\d/.test(value)) {
                value = 'KUN' + value;
            }

            // Limit to KUN + 5 digits
            if (value.startsWith('KUN')) {
                const numbers = value.substring(3).replace(/\D/g, '');
                value = 'KUN' + numbers.substring(0, 5);
            }

            e.target.value = value;

            // Validate format
            const isValid = value === '' || /^KUN\d{5}$/.test(value);
            e.target.classList.toggle('is-invalid', !isValid && value !== '');
            e.target.classList.toggle('is-valid', isValid && value !== '');
        });

        // Real-time uniqueness check
        let checkTimeout;
        customerCodeInput.addEventListener('input', function(e) {
            const value = e.target.value;

            if (value && /^KUN\d{5}$/.test(value)) {
                clearTimeout(checkTimeout);
                checkTimeout = setTimeout(() => {
                    checkCustomerCodeUniqueness(value);
                }, 500);
            }
        });
    }

    // Form validation
    if (form) {
        form.addEventListener('submit', function(e) {
            const customerCode = customerCodeInput.value.trim();

            if (customerCode && !/^KUN\d{5}$/.test(customerCode)) {
                e.preventDefault();
                customerCodeInput.classList.add('is-invalid');

                // Show error message
                let errorDiv = customerCodeInput.parentNode.parentNode.querySelector('.invalid-feedback');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    customerCodeInput.parentNode.parentNode.appendChild(errorDiv);
                }
                errorDiv.textContent = 'Mã khách hàng phải theo định dạng KUN##### (ví dụ: KUN12345)';

                return false;
            }
        });
    }

    // Check customer code uniqueness
    function checkCustomerCodeUniqueness(code) {
        fetch(`/admin/customers/check-code/${code}`)
            .then(response => response.json())
            .then(data => {
                const input = document.getElementById('customer_code');
                const feedback = input.parentNode.parentNode.querySelector('.code-feedback') ||
                               createFeedbackElement(input.parentNode.parentNode);

                if (data.exists) {
                    input.classList.remove('is-valid');
                    input.classList.add('is-invalid');
                    feedback.className = 'invalid-feedback code-feedback';
                    feedback.textContent = 'Mã khách hàng này đã tồn tại';
                    feedback.style.display = 'block';
                } else {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                    feedback.className = 'valid-feedback code-feedback';
                    feedback.textContent = 'Mã khách hàng có thể sử dụng';
                    feedback.style.display = 'block';
                }
            })
            .catch(error => {
                console.log('Error checking customer code:', error);
            });
    }

    function createFeedbackElement(parent) {
        const feedback = document.createElement('div');
        feedback.className = 'code-feedback';
        parent.appendChild(feedback);
        return feedback;
    }
});
</script>
@endpush

@endsection
