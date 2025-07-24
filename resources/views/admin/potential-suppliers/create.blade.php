@extends('layouts.admin')

@section('title', 'Thêm nhà cung cấp tiềm năng')

@section('styles')
<style>
    .form-section {
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-section h5 {
        color: #5a5c69;
        border-bottom: 2px solid #e3e6f0;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }

    .service-item {
        background: white;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        position: relative;
    }

    .service-item h6 {
        color: #5a5c69;
        margin-bottom: 1rem;
    }

    .btn-remove-service {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .btn-add-service {
        border: 2px dashed #4e73df;
        color: #4e73df;
        background: transparent;
        transition: all 0.3s ease;
    }

    .btn-add-service:hover {
        background: #4e73df;
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800 fw-bold">
                <i class="fas fa-plus-circle me-2 text-primary"></i>
                Thêm nhà cung cấp tiềm năng
            </h1>
            <p class="mb-0 text-muted">Thêm thông tin nhà cung cấp tiềm năng và các dịch vụ của họ</p>
        </div>
        <a href="{{ route('admin.potential-suppliers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header bg-gradient-primary text-white py-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-4"
                            style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user-plus text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bold text-white">Thông tin nhà cung cấp tiềm năng</h4>
                            <p class="mb-0 text-light opacity-75">Điền đầy đủ thông tin để tạo nhà cung cấp tiềm năng mới</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.potential-suppliers.store') }}" id="supplier-form">
                        @csrf

                        <!-- Thông tin cơ bản -->
                        <div class="form-section">
                            <h5>
                                <i class="fas fa-info-circle me-2"></i>
                                Thông tin cơ bản
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="supplier_name" class="form-label fw-semibold">
                                        <i class="fas fa-building me-2 text-secondary"></i>
                                        Tên nhà cung cấp <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control @error('supplier_name') is-invalid @enderror"
                                        id="supplier_name"
                                        name="supplier_name"
                                        value="{{ old('supplier_name') }}"
                                        required>
                                    @error('supplier_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="contact_person" class="form-label fw-semibold">
                                        <i class="fas fa-user me-2 text-secondary"></i>
                                        Người liên hệ
                                    </label>
                                    <input type="text"
                                        class="form-control @error('contact_person') is-invalid @enderror"
                                        id="contact_person"
                                        name="contact_person"
                                        value="{{ old('contact_person') }}">
                                    @error('contact_person')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-semibold">
                                        <i class="fas fa-phone me-2 text-secondary"></i>
                                        Số điện thoại
                                    </label>
                                    <input type="text"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        id="phone"
                                        name="phone"
                                        value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold">
                                        <i class="fas fa-envelope me-2 text-secondary"></i>
                                        Email
                                    </label>
                                    <input type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        id="email"
                                        name="email"
                                        value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="website" class="form-label fw-semibold">
                                        <i class="fas fa-globe me-2 text-secondary"></i>
                                        Website
                                    </label>
                                    <input type="url"
                                        class="form-control @error('website') is-invalid @enderror"
                                        id="website"
                                        name="website"
                                        value="{{ old('website') }}"
                                        placeholder="https://example.com">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="priority" class="form-label fw-semibold">
                                        <i class="fas fa-star me-2 text-secondary"></i>
                                        Mức độ ưu tiên <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('priority') is-invalid @enderror"
                                        id="priority"
                                        name="priority"
                                        required>
                                        <option value="">Chọn mức độ ưu tiên</option>
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Thấp</option>
                                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Cao</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="expected_cooperation_date" class="form-label fw-semibold">
                                        <i class="fas fa-calendar me-2 text-secondary"></i>
                                        Ngày dự kiến hợp tác
                                    </label>
                                    <input type="date"
                                        class="form-control @error('expected_cooperation_date') is-invalid @enderror"
                                        id="expected_cooperation_date"
                                        name="expected_cooperation_date"
                                        value="{{ old('expected_cooperation_date') }}">
                                    @error('expected_cooperation_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="address" class="form-label fw-semibold">
                                        <i class="fas fa-map-marker-alt me-2 text-secondary"></i>
                                        Địa chỉ
                                    </label>
                                    <textarea class="form-control @error('address') is-invalid @enderror"
                                        id="address"
                                        name="address"
                                        rows="2">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="reason_potential" class="form-label fw-semibold">
                                        <i class="fas fa-lightbulb me-2 text-secondary"></i>
                                        Lý do được coi là tiềm năng
                                    </label>
                                    <textarea class="form-control @error('reason_potential') is-invalid @enderror"
                                        id="reason_potential"
                                        name="reason_potential"
                                        rows="2"
                                        placeholder="Ví dụ: Giá cả cạnh tranh, dịch vụ tốt, đánh giá cao...">{{ old('reason_potential') }}</textarea>
                                    @error('reason_potential')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="notes" class="form-label fw-semibold">
                                        <i class="fas fa-sticky-note me-2 text-secondary"></i>
                                        Ghi chú
                                    </label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror"
                                        id="notes"
                                        name="notes"
                                        rows="2">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Danh sách dịch vụ -->
                        <div class="form-section">
                            <h5>
                                <i class="fas fa-laptop me-2"></i>
                                Danh sách dịch vụ/tài khoản
                            </h5>
                            <div id="services-container">
                                <!-- Service items will be added here -->
                            </div>
                            <button type="button" class="btn btn-add-service w-100 py-3" id="add-service-btn">
                                <i class="fas fa-plus me-2"></i>
                                Thêm dịch vụ
                            </button>
                        </div>

                        <!-- Submit buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.potential-suppliers.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Lưu nhà cung cấp tiềm năng
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Service Template -->
<template id="service-template">
    <div class="service-item" data-index="0">
        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-service">
            <i class="fas fa-times"></i>
        </button>
        <h6 class="text-secondary">
            <i class="fas fa-laptop me-2"></i>Dịch vụ #<span class="service-number">1</span>
        </h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">
                    Tên dịch vụ <span class="text-danger">*</span>
                </label>
                <input type="text"
                    class="form-control"
                    name="services[0][service_name]"
                    required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">
                    Giá ước tính (VND) <span class="text-danger">*</span>
                </label>
                <input type="number"
                    class="form-control"
                    name="services[0][estimated_price]"
                    min="0"
                    step="1000"
                    required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">
                    Đơn vị
                </label>
                <input type="text"
                    class="form-control"
                    name="services[0][unit]"
                    placeholder="cái, chiếc, tháng...">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">
                    Bảo hành (ngày)
                </label>
                <input type="number"
                    class="form-control"
                    name="services[0][warranty_days]"
                    min="0"
                    max="3650">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label fw-semibold">
                    Mô tả dịch vụ
                </label>
                <textarea class="form-control"
                    name="services[0][description]"
                    rows="2"></textarea>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label fw-semibold">
                    Ghi chú
                </label>
                <textarea class="form-control"
                    name="services[0][notes]"
                    rows="2"></textarea>
            </div>
        </div>
    </div>
</template>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let serviceIndex = 0;
    const servicesContainer = document.getElementById('services-container');
    const addServiceBtn = document.getElementById('add-service-btn');
    const serviceTemplate = document.getElementById('service-template');

    // Add first service by default
    addService();

    addServiceBtn.addEventListener('click', addService);

    function addService() {
        const template = serviceTemplate.content.cloneNode(true);
        const serviceItem = template.querySelector('.service-item');
        
        // Update data-index
        serviceItem.setAttribute('data-index', serviceIndex);
        
        // Update service number
        const serviceNumber = serviceItem.querySelector('.service-number');
        serviceNumber.textContent = serviceIndex + 1;
        
        // Update input names
        const inputs = serviceItem.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace('[0]', `[${serviceIndex}]`));
            }
        });
        
        // Add remove functionality
        const removeBtn = serviceItem.querySelector('.btn-remove-service');
        removeBtn.addEventListener('click', function() {
            removeService(serviceItem);
        });
        
        // Show/hide remove button
        updateRemoveButtons();
        
        servicesContainer.appendChild(serviceItem);
        serviceIndex++;
        
        updateRemoveButtons();
    }

    function removeService(serviceItem) {
        serviceItem.remove();
        updateServiceNumbers();
        updateRemoveButtons();
    }

    function updateServiceNumbers() {
        const serviceItems = servicesContainer.querySelectorAll('.service-item');
        serviceItems.forEach((item, index) => {
            const serviceNumber = item.querySelector('.service-number');
            serviceNumber.textContent = index + 1;
        });
    }

    function updateRemoveButtons() {
        const serviceItems = servicesContainer.querySelectorAll('.service-item');
        const removeButtons = servicesContainer.querySelectorAll('.btn-remove-service');
        
        removeButtons.forEach(btn => {
            btn.style.display = serviceItems.length > 1 ? 'flex' : 'none';
        });
    }
});
</script>
@endsection
