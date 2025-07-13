@extends('layouts.admin')

@section('title', 'Thêm cộng tác viên mới')
@section('page-title', 'Thêm cộng tác viên mới')

@section('styles')
<style>
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.15);
    }

    .card {
        border-radius: 15px;
        overflow: hidden;
    }

    .form-control-lg {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .form-control-lg:focus {
        transform: translateY(-1px);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.2s ease;
    }

    .service-item {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }

    .service-item:hover {
        border-color: #667eea;
        background: #fff;
    }

    .btn-remove-service {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary text-white py-4">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper me-4"
                        style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-plus text-white" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-white">Thêm cộng tác viên mới</h4>
                        <p class="mb-0 text-light opacity-75">Nhập thông tin cơ bản và dịch vụ cung cấp</p>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.collaborators.store') }}" id="collaborator-form">
                    @csrf

                    <div class="row">
                        <!-- Thông tin cơ bản -->
                        <div class="col-12 mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Thông tin cộng tác viên
                            </h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label fw-semibold">
                                <i class="fas fa-user me-2 text-primary"></i>
                                Tên cộng tác viên <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                class="form-control form-control-lg @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                placeholder="VD: Nguyễn Văn A">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label fw-semibold">
                                <i class="fas fa-toggle-on me-2 text-success"></i>
                                Trạng thái <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-control-lg @error('status') is-invalid @enderror"
                                id="status" name="status" required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="fas fa-envelope me-2 text-info"></i>
                                Email liên hệ
                            </label>
                            <input type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="example@domain.com">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label fw-semibold">
                                <i class="fas fa-phone me-2 text-warning"></i>
                                Số điện thoại
                            </label>
                            <input type="text"
                                class="form-control @error('phone') is-invalid @enderror"
                                id="phone"
                                name="phone"
                                value="{{ old('phone') }}"
                                placeholder="0123456789">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="address" class="form-label fw-semibold">
                                <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                                Địa chỉ
                            </label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                id="address"
                                name="address"
                                rows="2"
                                placeholder="Nhập địa chỉ cộng tác viên">{{ old('address') }}</textarea>
                            @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-4">
                            <label for="notes" class="form-label fw-semibold">
                                <i class="fas fa-sticky-note me-2 text-secondary"></i>
                                Ghi chú
                            </label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                id="notes"
                                name="notes"
                                rows="3"
                                placeholder="Ghi chú về cộng tác viên...">{{ old('notes') }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Danh sách dịch vụ -->
                        <div class="col-12 mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-concierge-bell me-2"></i>
                                Dịch vụ cung cấp
                            </h5>
                        </div>

                        <div class="col-12 mb-4">
                            <div id="services-container">
                                <div class="service-item p-3 mb-3" data-index="0">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 text-secondary">
                                            <i class="fas fa-concierge-bell me-2"></i>Dịch vụ #1
                                        </h6>
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-service" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">
                                                Tên dịch vụ <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control"
                                                name="services[0][service_name]"
                                                required
                                                placeholder="VD: Netflix Premium, ChatGPT Plus...">
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label fw-semibold">
                                                Giá <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control"
                                                    name="services[0][price]"
                                                    required
                                                    min="0"
                                                    step="1000"
                                                    placeholder="0">
                                                <span class="input-group-text">VND</span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label fw-semibold">
                                                Số lượng <span class="text-danger">*</span>
                                            </label>
                                            <input type="number"
                                                class="form-control"
                                                name="services[0][quantity]"
                                                required
                                                min="1"
                                                value="1"
                                                placeholder="1">
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label class="form-label fw-semibold">
                                                Bảo hành (ngày)
                                            </label>
                                            <input type="number"
                                                class="form-control"
                                                name="services[0][warranty_period]"
                                                min="0"
                                                value="0"
                                                placeholder="0">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-semibold">
                                                Mô tả dịch vụ
                                            </label>
                                            <textarea class="form-control"
                                                name="services[0][description]"
                                                rows="2"
                                                placeholder="Mô tả chi tiết về dịch vụ..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn-outline-primary" id="add-service-btn">
                                    <i class="fas fa-plus me-2"></i>Thêm dịch vụ
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.collaborators.index') }}" class="btn btn-light px-4">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                                </a>

                                <div>
                                    <button type="reset" class="btn btn-outline-secondary me-2" id="reset-btn">
                                        <i class="fas fa-undo me-2"></i>Đặt lại
                                    </button>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i>Lưu cộng tác viên
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let serviceIndex = 1;

        const servicesContainer = document.getElementById('services-container');
        const addServiceBtn = document.getElementById('add-service-btn');
        const resetBtn = document.getElementById('reset-btn');

        // Add service function
        if (addServiceBtn) {
            addServiceBtn.addEventListener('click', function() {
                const serviceHtml = `
            <div class="service-item p-3 mb-3" data-index="${serviceIndex}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 text-secondary">
                        <i class="fas fa-concierge-bell me-2"></i>Dịch vụ #${serviceIndex + 1}
                    </h6>
                    <button type="button" class="btn btn-outline-danger btn-sm btn-remove-service">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Tên dịch vụ <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               name="services[${serviceIndex}][service_name]" 
                               required
                               placeholder="VD: Netflix Premium, ChatGPT Plus...">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-semibold">
                            Giá <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" 
                                   class="form-control" 
                                   name="services[${serviceIndex}][price]" 
                                   required
                                   min="0"
                                   step="1000"
                                   placeholder="0">
                            <span class="input-group-text">VND</span>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-semibold">
                            Số lượng <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               class="form-control" 
                               name="services[${serviceIndex}][quantity]" 
                               required
                               min="1"
                               value="1"
                               placeholder="1">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-semibold">
                            Bảo hành (ngày)
                        </label>
                        <input type="number" 
                               class="form-control" 
                               name="services[${serviceIndex}][warranty_period]" 
                               min="0"
                               value="0"
                               placeholder="0">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">
                            Mô tả dịch vụ
                        </label>
                        <textarea class="form-control"
                                  name="services[${serviceIndex}][description]"
                                  rows="2"
                                  placeholder="Mô tả chi tiết về dịch vụ..."></textarea>
                    </div>
                </div>
            </div>
        `;

                servicesContainer.insertAdjacentHTML('beforeend', serviceHtml);
                serviceIndex++;
                updateRemoveButtons();
            });
        }

        // Remove service function
        if (servicesContainer) {
            servicesContainer.addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove-service')) {
                    const serviceItem = e.target.closest('.service-item');
                    serviceItem.remove();
                    updateServiceNumbers();
                    updateRemoveButtons();
                }
            });
        }

        // Update service numbers
        function updateServiceNumbers() {
            const serviceItems = servicesContainer.querySelectorAll('.service-item');
            serviceItems.forEach((item, index) => {
                const title = item.querySelector('h6');
                title.innerHTML = `<i class="fas fa-concierge-bell me-2"></i>Dịch vụ #${index + 1}`;
            });
        }

        // Update remove buttons visibility
        function updateRemoveButtons() {
            const serviceItems = servicesContainer.querySelectorAll('.service-item');
            const removeButtons = servicesContainer.querySelectorAll('.btn-remove-service');

            removeButtons.forEach(btn => {
                btn.style.display = serviceItems.length > 1 ? 'flex' : 'none';
            });
        }

        // Reset form
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                // Keep only first service
                const firstService = servicesContainer.querySelector('.service-item');
                servicesContainer.innerHTML = '';
                servicesContainer.appendChild(firstService);

                // Reset first service form
                firstService.querySelectorAll('input, textarea').forEach(input => {
                    if (input.name.includes('quantity') || input.name.includes('warranty_period')) {
                        input.value = input.name.includes('quantity') ? '1' : '0';
                    } else {
                        input.value = '';
                    }
                });

                serviceIndex = 1;
                updateRemoveButtons();
            });
        }

        // Auto focus first input
        document.getElementById('name').focus();

        // Initialize remove buttons
        updateRemoveButtons();
    });
</script>
@endsection