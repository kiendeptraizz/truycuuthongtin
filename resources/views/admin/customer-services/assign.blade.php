@extends('layouts.admin')

@section('title', 'Gán dịch vụ cho khách hàng')
@section('page-title', 'Gán dịch vụ cho khách hàng')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-link me-2"></i>
                    Gán dịch vụ cho: <strong>{{ $customer->name }}</strong>
                </h5>
                <small class="text-muted">Mã khách hàng: {{ $customer->customer_code }}</small>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('admin.customers.store-service', $customer) }}">
                    @csrf

                    <div class="row">
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
                                placeholder="Chọn gói dịch vụ cho khách hàng..." />

                            <!-- Family Account Warning -->
                            <div id="family-warning" class="alert alert-warning mt-3" style="display: none;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                    <div>
                                        <strong>Yêu cầu Family Account!</strong><br>
                                        <small>Gói dịch vụ "Add Family" yêu cầu khách hàng phải có Family Account trước.
                                            @if(!$hasFamilyMembership)
                                            Khách hàng này chưa có Family Account.
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                @if(!$hasFamilyMembership)
                                <div class="mt-2">
                                    <a href="{{ route('admin.family-accounts.create', ['customer_id' => $customer->id]) }}"
                                        class="btn btn-sm btn-warning">
                                        <i class="fas fa-plus me-1"></i>Tạo Family Account trước
                                    </a>
                                </div>
                                @endif
                            </div>

                            <!-- Family Selection for Add Team Services -->
                            <div id="family-selection" class="mt-3" style="display: none;">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-users me-2"></i>
                                            Chọn Family Account để thêm vào
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="family_account_id" class="form-label">
                                                    <i class="fas fa-home me-2 text-primary"></i>
                                                    Family Account <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select @error('family_account_id') is-invalid @enderror"
                                                    id="family_account_id"
                                                    name="family_account_id">
                                                    <option value="">Chọn Family Account</option>
                                                    @foreach($availableFamilyAccounts as $family)
                                                    <option value="{{ $family->id }}"
                                                        data-family-code="{{ $family->family_code }}"
                                                        data-primary-email="{{ $family->owner_email }}"
                                                        data-service-name="{{ $family->servicePackage->name ?? 'N/A' }}"
                                                        data-members-count="{{ $family->family_members_count }}"
                                                        data-members-limit="{{ $family->max_members }}"
                                                        {{ old('family_account_id') == $family->id ? 'selected' : '' }}>
                                                        {{ $family->family_code }} - {{ $family->family_name }}
                                                        ({{ $family->family_members_count }}/{{ $family->max_members }} thành viên)
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('family_account_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">Chọn Family Account để thêm khách hàng này vào</div>
                                            </div>
                                        </div>

                                        <!-- Family Details Display -->
                                        <div id="family-details" style="display: none;">
                                            <div class="alert alert-info">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Mã Family:</strong> <span id="family-code-display"></span><br>
                                                        <strong>Email chính:</strong> <span id="family-email-display"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Dịch vụ:</strong> <span id="family-service-display"></span><br>
                                                        <strong>Thành viên:</strong> <span id="family-members-display"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                    data-products="{{ $supplier->products->map(function($p) { return $p->product_name . ' - ' . number_format($p->price, 0, ',', '.') . ' VND'; })->implode('|') }}"
                                    data-services="{{ $supplier->products->map(function($p) { return $p->id . ':' . $p->product_name . ':' . $p->price; })->implode('|') }}"
                                    {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
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
                                Chọn dịch vụ cụ thể <span class="text-danger">*</span>
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

                        <div class="col-md-6 mb-3">
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

                        <div class="col-md-6 mb-3">
                            <label for="expires_at" class="form-label">
                                Ngày hết hạn <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                class="form-control @error('expires_at') is-invalid @enderror"
                                id="expires_at"
                                name="expires_at"
                                value="{{ old('expires_at') }}"
                                required>
                            @error('expires_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Thời hạn -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                <i class="fas fa-clock me-1"></i>
                                Thời hạn <span class="text-danger">*</span>
                            </label>

                            <!-- Radio chọn đơn vị -->
                            <div class="mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="duration_unit" id="duration_unit_days" value="days" checked>
                                    <label class="form-check-label" for="duration_unit_days">
                                        Ngày
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="duration_unit" id="duration_unit_months" value="months">
                                    <label class="form-check-label" for="duration_unit_months">
                                        Tháng
                                    </label>
                                </div>
                            </div>

                            <!-- Input nhập số -->
                            <div class="input-group" style="max-width: 300px;">
                                <input type="number"
                                    class="form-control @error('duration_days') is-invalid @enderror"
                                    id="duration_value"
                                    value="{{ old('duration_value') }}"
                                    min="1"
                                    placeholder="Nhập số ngày/tháng"
                                    required>
                                <span class="input-group-text" id="duration_unit_label">ngày</span>
                            </div>

                            <!-- Hidden input để lưu giá trị ngày thực tế -->
                            <input type="hidden" name="duration_days" id="duration_days" value="{{ old('duration_days') }}">

                            @error('duration_days')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text" id="duration_calculated_text">Nhập thời hạn để tự động tính ngày hết hạn</div>
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
                                                <input type="text"
                                                    class="form-control @error('profit_amount') is-invalid @enderror"
                                                    id="profit_amount"
                                                    name="profit_amount"
                                                    placeholder="Nhập số tiền lãi (VD: 1000000)"
                                                    value="{{ old('profit_amount') }}"
                                                    inputmode="numeric"
                                                    maxlength="15">
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
                        <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-secondary">
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
        console.log('🚀 DOM Content Loaded - Assign Service Form');

        const serviceSelect = document.getElementById('service_package_id');
        const activatedInput = document.getElementById('activated_at');
        const expiresInput = document.getElementById('expires_at');
        const supplierSelect = document.getElementById('supplier_id');
        const supplierDetails = document.getElementById('supplier-details');
        const supplierCodeDisplay = document.getElementById('supplier-code-display');
        const supplierNameDisplay = document.getElementById('supplier-name-display');
        const supplierProductsList = document.getElementById('supplier-products-list');
        const serviceSelection = document.getElementById('service-selection');
        const supplierServiceSelect = document.getElementById('supplier_service_id');

        console.log('📋 Main elements check:', {
            activatedInput: !!activatedInput,
            expiresInput: !!expiresInput
        });

        // Wait a bit for grid component to initialize
        setTimeout(function() {
            console.log('Initializing service package handlers...');
            initializeServicePackageHandlers();
        }, 100);

        function initializeServicePackageHandlers() {

            // Handle service package selection (for grid selector)
            serviceSelect.addEventListener('change', function() {
                console.log('Service changed to:', this.value);
                // REMOVED: updateExpiryDateForSelectedService();
                // Không tự động điền thời hạn từ gói dịch vụ nữa
                handleFamilyAccountLogic();
            });

            // DISABLED: Function to update expiry date based on selected service
            // Đã tắt để không tự động điền thời hạn từ gói dịch vụ
            /*
            function updateExpiryDateForSelectedService() {
                if (serviceSelect.value) {
                    const selectedCard = document.querySelector(`[data-package-id="${serviceSelect.value}"]`);
                    if (selectedCard) {
                        const duration = parseInt(selectedCard.getAttribute('data-duration')) || 365;
                        console.log('Selected service duration:', duration, 'days');

                        const activatedDate = new Date(activatedInput.value);
                        const expiresDate = new Date(activatedDate);
                        expiresDate.setDate(expiresDate.getDate() + duration);

                        expiresInput.value = expiresDate.toISOString().split('T')[0];
                        console.log('Updated expiry date to:', expiresInput.value);
                    } else {
                        console.log('No card found for package ID:', serviceSelect.value);
                    }
                }
            }
            */

            // Function to handle family account logic
            function handleFamilyAccountLogic() {
                const familyWarning = document.getElementById('family-warning');
                const familySelection = document.getElementById('family-selection');
                const submitBtn = document.querySelector('button[type="submit"]');
                const hasFamilyMembership = @json($hasFamilyMembership ?? false);

                if (serviceSelect.value) {
                    const selectedCard = document.querySelector(`[data-package-id="${serviceSelect.value}"]`);
                    if (selectedCard) {
                        const accountType = selectedCard.getAttribute('data-account-type') || '';

                        if (accountType.includes('add family')) {
                            familySelection.style.display = 'block';
                            familyWarning.style.display = 'none';
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Gán dịch vụ';
                            submitBtn.classList.remove('btn-danger');
                            submitBtn.classList.add('btn-primary');
                        } else {
                            familySelection.style.display = 'none';
                            familyWarning.style.display = 'none';
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Gán dịch vụ';
                            submitBtn.classList.remove('btn-danger');
                            submitBtn.classList.add('btn-primary');
                        }
                    }
                } else {
                    familyWarning.style.display = 'none';
                    familySelection.style.display = 'none';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Gán dịch vụ';
                    submitBtn.classList.remove('btn-danger');
                    submitBtn.classList.add('btn-primary');
                }
            }

            // Removed - this was conflicting with duration calculator
            // activatedInput.addEventListener('change', function() {
            //     console.log('Activation date changed to:', this.value);
            //     updateExpiryDateForSelectedService();
            // });

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

            // Show supplier details if already selected (for form validation errors)
            if (supplierSelect.value) {
                supplierSelect.dispatchEvent(new Event('change'));

                // Restore selected service if any
                const selectedServiceId = '{{ old("supplier_service_id") }}';
                if (selectedServiceId) {
                    setTimeout(function() {
                        supplierServiceSelect.value = selectedServiceId;
                    }, 100);
                }
            }

            // Handle family account selection
            const familyAccountSelect = document.getElementById('family_account_id');
            const familyDetails = document.getElementById('family-details');
            const familyCodeDisplay = document.getElementById('family-code-display');
            const familyEmailDisplay = document.getElementById('family-email-display');
            const familyServiceDisplay = document.getElementById('family-service-display');
            const familyMembersDisplay = document.getElementById('family-members-display');

            if (familyAccountSelect) {
                familyAccountSelect.addEventListener('change', function() {
                    if (this.value) {
                        const selectedOption = this.options[this.selectedIndex];

                        // Fill family information
                        familyCodeDisplay.textContent = selectedOption.dataset.familyCode;
                        familyEmailDisplay.textContent = selectedOption.dataset.primaryEmail;
                        familyServiceDisplay.textContent = selectedOption.dataset.serviceName;
                        familyMembersDisplay.textContent = selectedOption.dataset.membersCount + '/' + selectedOption.dataset.membersLimit + ' thành viên';

                        familyDetails.style.display = 'block';
                    } else {
                        familyDetails.style.display = 'none';
                    }
                });

                // Show family details if already selected
                if (familyAccountSelect.value) {
                    familyAccountSelect.dispatchEvent(new Event('change'));
                }
            }

            // Format profit amount input with thousand separators
            const profitAmountInput = document.getElementById('profit_amount');
            if (profitAmountInput) {
                // Format existing value on page load
                if (profitAmountInput.value) {
                    profitAmountInput.value = formatNumberInput(profitAmountInput.value);
                }

                // Format as user types
                profitAmountInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/[^\d]/g, ''); // Chỉ giữ lại số
                    if (value) {
                        e.target.value = formatNumberInput(value);
                    } else {
                        e.target.value = '';
                    }
                });

                // Prevent non-numeric characters except backspace, delete, arrow keys
                profitAmountInput.addEventListener('keydown', function(e) {
                    // Allow: backspace, delete, tab, escape, enter
                    if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
                        // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                        (e.keyCode === 65 && e.ctrlKey === true) ||
                        (e.keyCode === 67 && e.ctrlKey === true) ||
                        (e.keyCode === 86 && e.ctrlKey === true) ||
                        (e.keyCode === 88 && e.ctrlKey === true) ||
                        // Allow: home, end, left, right, down, up
                        (e.keyCode >= 35 && e.keyCode <= 40)) {
                        return;
                    }
                    // Ensure that it is a number and stop the keypress
                    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                        e.preventDefault();
                    }
                });

                // Clean value before form submission (remove dots for proper validation)
                profitAmountInput.closest('form').addEventListener('submit', function() {
                    const originalValue = profitAmountInput.value;
                    const cleanValue = profitAmountInput.value.replace(/\./g, '');
                    profitAmountInput.value = cleanValue;
                    console.log('Profit amount: Original =', originalValue, ', Clean =', cleanValue);
                });
            }

        } // End of initializeServicePackageHandlers()

        // DISABLED: Observer for grid component changes
        // Đã tắt để không tự động điền thời hạn từ gói dịch vụ
        /*
        const gridContainer = document.querySelector('.service-package-grid-container');
        if (gridContainer) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' || mutation.type === 'attributes') {
                        // Check if a new card was selected
                        const selectedCard = gridContainer.querySelector('.package-card.selected');
                        if (selectedCard && serviceSelect.value) {
                            console.log('Grid selection detected via observer');
                            updateExpiryDateForSelectedService();
                        }
                    }
                });
            });

            observer.observe(gridContainer, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['class']
            });
        }
        */

        // ===== Duration Calculator Logic =====
        setTimeout(function() {
            console.log('⏰ Initializing Duration Calculator (delayed)...');
            initializeDurationCalculator();
        }, 200);

        function initializeDurationCalculator() {
            const durationUnitRadios = document.querySelectorAll('input[name="duration_unit"]');
            const durationValueInput = document.getElementById('duration_value');
            const durationDaysHidden = document.getElementById('duration_days');
            const durationUnitLabel = document.getElementById('duration_unit_label');
            const durationCalculatedText = document.getElementById('duration_calculated_text');
            const activatedAtInput = document.getElementById('activated_at');
            const expiresAtInput = document.getElementById('expires_at');

            console.log('📊 Duration Calculator Elements Found:', {
                'durationUnitRadios': durationUnitRadios.length + ' radios',
                'durationValueInput': durationValueInput ? '✅ Found' : '❌ Not found',
                'durationDaysHidden': durationDaysHidden ? '✅ Found' : '❌ Not found',
                'durationUnitLabel': durationUnitLabel ? '✅ Found' : '❌ Not found',
                'durationCalculatedText': durationCalculatedText ? '✅ Found' : '❌ Not found',
                'activatedAtInput': activatedAtInput ? '✅ Found' : '❌ Not found',
                'expiresAtInput': expiresAtInput ? '✅ Found' : '❌ Not found'
            });

            // Log các radio buttons
            if (durationUnitRadios.length > 0) {
                console.log('🔘 Available duration unit radios:');
                durationUnitRadios.forEach((radio, index) => {
                    console.log(`  ${index + 1}. ${radio.value} - checked: ${radio.checked}`);
                });
            }

            if (durationUnitRadios.length > 0 && durationValueInput && durationDaysHidden) {
                console.log('Duration calculator initialized successfully');

                function calculateDuration() {
                    console.log('🔄 calculateDuration() called');

                    const checkedUnit = document.querySelector('input[name="duration_unit"]:checked');
                    if (!checkedUnit) {
                        console.log('❌ No duration unit selected');
                        return;
                    }

                    const unit = checkedUnit.value;
                    const value = parseInt(durationValueInput.value) || 0;
                    let days = 0;

                    console.log('📊 Duration calculation - unit:', unit, 'value:', value);

                    if (value === 0) {
                        // No value entered yet
                        if (durationCalculatedText) {
                            durationCalculatedText.textContent = 'Nhập thời hạn để tự động tính ngày hết hạn';
                        }
                        if (durationDaysHidden) {
                            durationDaysHidden.value = '';
                        }
                        console.log('⚠️ No duration value entered');
                        return;
                    }

                    if (unit === 'days') {
                        days = value;
                        if (durationUnitLabel) durationUnitLabel.textContent = 'ngày';
                        if (durationCalculatedText) durationCalculatedText.textContent = `Thời hạn: ${value} ngày`;
                    } else if (unit === 'months') {
                        days = value * 30; // 1 tháng = 30 ngày
                        if (durationUnitLabel) durationUnitLabel.textContent = 'tháng';
                        if (durationCalculatedText) durationCalculatedText.textContent = `Thời hạn: ${value} tháng (${days} ngày)`;
                    }

                    if (durationDaysHidden) {
                        durationDaysHidden.value = days;
                        console.log('✅ Duration in days updated to:', days);
                    } else {
                        console.log('❌ durationDaysHidden element not found');
                    }

                    // Tự động tính ngày hết hạn
                    console.log('🔄 Calling updateExpiresDate...');
                    updateExpiresDate();
                }

                function updateExpiresDate() {
                    console.log('🔄 updateExpiresDate() called');
                    console.log('📅 Current values:', {
                        'activated': activatedAtInput?.value,
                        'duration_days': durationDaysHidden?.value,
                        'activatedAtInput exists': !!activatedAtInput,
                        'expiresAtInput exists': !!expiresAtInput,
                        'durationDaysHidden exists': !!durationDaysHidden
                    });

                    if (activatedAtInput && activatedAtInput.value && durationDaysHidden && durationDaysHidden.value) {
                        const activatedDate = new Date(activatedAtInput.value + 'T00:00:00');
                        const days = parseInt(durationDaysHidden.value) || 0;

                        console.log('📊 Date calculation:', {
                            'activated_date': activatedDate.toDateString(),
                            'days_to_add': days
                        });

                        if (days > 0 && expiresAtInput) {
                            const expiresDate = new Date(activatedDate);
                            expiresDate.setDate(expiresDate.getDate() + days);

                            // Format date to YYYY-MM-DD
                            const year = expiresDate.getFullYear();
                            const month = String(expiresDate.getMonth() + 1).padStart(2, '0');
                            const day = String(expiresDate.getDate()).padStart(2, '0');

                            const newExpiresDate = `${year}-${month}-${day}`;
                            expiresAtInput.value = newExpiresDate;

                            console.log('✅ Successfully updated expires date:', newExpiresDate, `(+${days} days from ${activatedAtInput.value})`);

                            // Trigger change event để notify other listeners
                            expiresAtInput.dispatchEvent(new Event('change'));
                        } else {
                            console.log('❌ Days is 0 or expiresAtInput not found');
                        }
                    } else {
                        console.log('❌ Missing required fields:', {
                            'activatedAtInput': !!activatedAtInput,
                            'activatedAtInput.value': activatedAtInput?.value,
                            'durationDaysHidden': !!durationDaysHidden,
                            'durationDaysHidden.value': durationDaysHidden?.value
                        });
                    }
                }

                // Event listeners
                console.log('🎧 Adding event listeners...');

                durationUnitRadios.forEach((radio, index) => {
                    radio.addEventListener('change', function() {
                        console.log(`🔘 Duration unit changed to: ${this.value} (radio ${index + 1})`);
                        calculateDuration();
                    });
                });
                console.log(`✅ Added change listeners to ${durationUnitRadios.length} radio buttons`);

                if (durationValueInput) {
                    durationValueInput.addEventListener('input', function(e) {
                        console.log('📝 Duration value INPUT event:', e.target.value);
                        calculateDuration();
                    });

                    // Thêm event cho keyup và change để bắt tất cả thay đổi
                    durationValueInput.addEventListener('keyup', function(e) {
                        console.log('⌨️ Duration value KEYUP event:', e.target.value);
                        calculateDuration();
                    });

                    durationValueInput.addEventListener('change', function(e) {
                        console.log('🔄 Duration value CHANGE event:', e.target.value);
                        calculateDuration();
                    });

                    console.log('✅ Added multiple listeners to duration value input');
                } else {
                    console.log('❌ durationValueInput not found, cannot add listeners');
                }

                // Khi thay đổi ngày kích hoạt, tính lại ngày hết hạn
                if (activatedAtInput) {
                    activatedAtInput.addEventListener('change', function(e) {
                        console.log('Activated date changed to:', e.target.value);
                        updateExpiresDate();
                    });
                }
                console.log('Added change listener to activated date input');

                // Initial calculation only if there are values (from validation errors)
                if (durationValueInput.value) {
                    console.log('Running initial calculation with existing value...');
                    calculateDuration();
                } else {
                    console.log('No initial value, waiting for user input...');
                }

                // Test indicator để confirm JS đang hoạt động
                if (durationValueInput) {
                    console.log('🎯 Duration calculator fully initialized!');

                    // Thêm visual indicator
                    const indicator = document.createElement('div');
                    indicator.className = 'alert alert-success mt-2';
                    indicator.innerHTML = '<i class="fas fa-check-circle"></i> Tính năng tự động tính ngày đã sẵn sàng!';
                    indicator.style.fontSize = '12px';
                    indicator.style.padding = '5px 10px';

                    // Thêm indicator sau duration value input
                    durationValueInput.parentElement.insertAdjacentElement('afterend', indicator);

                    // Tự động ẩn sau 3 giây
                    setTimeout(() => {
                        if (indicator.parentElement) {
                            indicator.remove();
                        }
                    }, 3000);
                }
            } else {
                console.log('❌ Duration calculator NOT initialized - missing required elements');
            }
        } // End initializeDurationCalculator
    });

    // Function to format number with thousand separators
    function formatNumberInput(number) {
        return parseInt(number).toLocaleString('vi-VN');
    }
</script>
@endpush
@endsection