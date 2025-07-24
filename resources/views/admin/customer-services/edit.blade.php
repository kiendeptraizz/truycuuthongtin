@extends('layouts.admin')

@section('title', 'Chỉnh sửa dịch vụ khách hàng')
@section('page-title', 'Chỉnh sửa dịch vụ khách hàng')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Chỉnh sửa dịch vụ: {{ $customerService->servicePackage->name }}
                </h5>
                <small class="text-muted">Khách hàng: {{ $customerService->customer->name }}</small>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('admin.customer-services.update', $customerService) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_id" class="form-label">
                                Khách hàng <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('customer_id') is-invalid @enderror"
                                id="customer_id"
                                name="customer_id"
                                required>
                                <option value="">Chọn khách hàng</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ old('customer_id', $customerService->customer_id) == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->customer_code }})
                                </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="service_package_id" class="form-label">
                                <i class="fas fa-box me-1"></i>
                                Gói dịch vụ <span class="text-danger">*</span>
                                <small class="text-muted ms-2">(Nhóm theo loại tài khoản)</small>
                            </label>

                            <x-service-package-selector
                                :service-packages="$servicePackages"
                                :account-type-priority="$accountTypePriority"
                                name="service_package_id"
                                id="service_package_id"
                                :required="true"
                                :selected="old('service_package_id', $customerService->service_package_id)"
                                placeholder="Chọn gói dịch vụ..."
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
                                value="{{ old('login_email', $customerService->login_email) }}"
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
                                value="{{ old('login_password', $customerService->login_password) }}">
                            @error('login_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Để trống nếu không muốn thay đổi mật khẩu</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="activated_at" class="form-label">
                                Ngày kích hoạt <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                class="form-control @error('activated_at') is-invalid @enderror"
                                id="activated_at"
                                name="activated_at"
                                value="{{ old('activated_at', $customerService->activated_at->format('Y-m-d')) }}"
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
                                value="{{ old('expires_at', $customerService->expires_at->format('Y-m-d')) }}"
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
                                <option value="active" {{ old('status', $customerService->status) === 'active' ? 'selected' : '' }}>
                                    Hoạt động
                                </option>
                                <option value="expired" {{ old('status', $customerService->status) === 'expired' ? 'selected' : '' }}>
                                    Hết hạn
                                </option>
                                <option value="cancelled" {{ old('status', $customerService->status) === 'cancelled' ? 'selected' : '' }}>
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
                                rows="3">{{ old('internal_notes', $customerService->internal_notes) }}</textarea>
                            @error('internal_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="supplier_id" class="form-label">
                                <i class="fas fa-truck me-2 text-warning"></i>
                                Nhà cung cấp
                            </label>
                            <select class="form-select @error('supplier_id') is-invalid @enderror"
                                id="supplier_id"
                                name="supplier_id">
                                <option value="">Chọn nhà cung cấp (tùy chọn)</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    data-supplier-name="{{ $supplier->supplier_name }}"
                                    data-supplier-code="{{ $supplier->supplier_code }}"
                                    data-products="{{ $supplier->products->map(function($p) { return $p->product_name . ' - ' . number_format($p->price) . ' VND'; })->implode('|') }}"
                                    data-services="{{ $supplier->products->map(function($p) { return $p->id . ':' . $p->product_name . ':' . $p->price; })->implode('|') }}"
                                    {{ old('supplier_id', $customerService->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->supplier_code }} - {{ $supplier->supplier_name }}
                                    @if($supplier->products->count() > 0)
                                    ({{ $supplier->products->count() }} dịch vụ)
                                    @else
                                    (Chưa có dịch vụ)
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Nhà cung cấp tài khoản dịch vụ này</div>
                        </div>

                        <!-- Thông tin chi tiết nhà cung cấp -->
                        <div class="col-md-12 mb-3" id="supplier-details" style="display: none;">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Thông tin nhà cung cấp
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Mã NCC:</strong> <span id="supplier-code-display"></span><br>
                                            <strong>Tên:</strong> <span id="supplier-name-display"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Danh sách dịch vụ:</strong>
                                            <ul id="supplier-products-list" class="mb-0 mt-1"></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chọn dịch vụ cụ thể -->
                        <div class="col-md-12 mb-3" id="service-selection" style="display: none;">
                            <label for="supplier_service_id" class="form-label">
                                <i class="fas fa-laptop me-2 text-success"></i>
                                Chọn dịch vụ cụ thể
                            </label>
                            <select class="form-select @error('supplier_service_id') is-invalid @enderror"
                                id="supplier_service_id"
                                name="supplier_service_id">
                                <option value="">Chọn dịch vụ từ nhà cung cấp</option>
                            </select>
                            @error('supplier_service_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Chọn dịch vụ cụ thể mà nhà cung cấp sẽ cung cấp cho khách hàng này</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.customer-services.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Quay lại
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Cập nhật dịch vụ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const supplierSelect = document.getElementById('supplier_id');
        const supplierDetails = document.getElementById('supplier-details');
        const supplierCodeDisplay = document.getElementById('supplier-code-display');
        const supplierNameDisplay = document.getElementById('supplier-name-display');
        const supplierProductsList = document.getElementById('supplier-products-list');
        const serviceSelection = document.getElementById('service-selection');
        const supplierServiceSelect = document.getElementById('supplier_service_id');

        // Handle supplier selection
        supplierSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];

            if (selectedOption.value) {
                // Show supplier details
                supplierDetails.style.display = 'block';

                // Fill supplier information
                supplierCodeDisplay.textContent = selectedOption.dataset.supplierCode;
                supplierNameDisplay.textContent = selectedOption.dataset.supplierName;

                // Fill products list
                const products = selectedOption.dataset.products;
                supplierProductsList.innerHTML = '';

                if (products) {
                    const productArray = products.split('|');
                    productArray.forEach(function(product) {
                        if (product.trim()) {
                            const li = document.createElement('li');
                            li.textContent = product;
                            li.className = 'text-muted';
                            supplierProductsList.appendChild(li);
                        }
                    });
                } else {
                    const li = document.createElement('li');
                    li.textContent = 'Chưa có dịch vụ nào';
                    li.className = 'text-muted fst-italic';
                    supplierProductsList.appendChild(li);
                }

                // Handle service selection dropdown
                const services = selectedOption.dataset.services;
                supplierServiceSelect.innerHTML = '<option value="">Chọn dịch vụ từ nhà cung cấp</option>';

                if (services) {
                    serviceSelection.style.display = 'block';
                    const serviceArray = services.split('|');
                    serviceArray.forEach(function(service) {
                        if (service.trim()) {
                            const parts = service.split(':');
                            if (parts.length === 3) {
                                const serviceId = parts[0];
                                const serviceName = parts[1];
                                const servicePrice = parseInt(parts[2]);

                                const option = document.createElement('option');
                                option.value = serviceId;
                                option.textContent = serviceName + ' - ' + servicePrice.toLocaleString('vi-VN') + ' VND';
                                supplierServiceSelect.appendChild(option);
                            }
                        }
                    });
                } else {
                    serviceSelection.style.display = 'none';
                }
            } else {
                // Hide supplier details and service selection
                supplierDetails.style.display = 'none';
                serviceSelection.style.display = 'none';
                supplierServiceSelect.innerHTML = '<option value="">Chọn dịch vụ từ nhà cung cấp</option>';
            }
        });

        // Show supplier details if already selected (for existing data or validation errors)
        if (supplierSelect.value) {
            supplierSelect.dispatchEvent(new Event('change'));

            // Restore selected service if any
            const selectedServiceId = '{{ old("supplier_service_id", $customerService->supplier_service_id) }}';
            if (selectedServiceId) {
                setTimeout(function() {
                    supplierServiceSelect.value = selectedServiceId;
                }, 100);
            }
        }
    });
</script>
@endpush