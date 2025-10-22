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
                <form method="POST" action="{{ route('admin.customer-services.update', $customerService) }}{{ request()->has('source') ? '?' . http_build_query(request()->only(['source', 'customer_id'])) : '' }}">
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

                        <!-- Family Account Selection -->
                        <div id="family-selection" class="col-md-12 mb-3" 
                             style="display: {{ $customerService->servicePackage->account_type === 'Tài khoản add family' ? 'block' : 'none' }};">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Chọn Family Account để thêm khách hàng này vào</strong>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="family_account_id" class="form-label">
                                        <i class="fas fa-users me-1"></i>
                                        Family Account <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('family_account_id') is-invalid @enderror"
                                        id="family_account_id"
                                        name="family_account_id">
                                        <option value="">Chọn Family Account</option>
                                        @foreach($availableFamilyAccounts as $family)
                                        <option value="{{ $family->id }}"
                                            data-current-members="{{ $family->family_members_count }}"
                                            data-max-members="{{ $family->max_members }}"
                                            {{ old('family_account_id', $currentFamilyMembership?->family_account_id) == $family->id ? 'selected' : '' }}>
                                            {{ $family->family_name }} 
                                            ({{ $family->family_members_count }}/{{ $family->max_members }} members)
                                            - {{ $family->owner_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('family_account_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Chọn Family Account để thêm khách hàng này vào</div>
                                </div>
                                @if($currentFamilyMembership)
                                <div class="col-md-6">
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <strong>Đã có Family Account:</strong><br>
                                        {{ $currentFamilyMembership->familyAccount->family_name }}
                                        <br><small>Owner: {{ $currentFamilyMembership->familyAccount->owner_name }}</small>
                                    </div>
                                </div>
                                @endif
                            </div>
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
                                class="form-control start-date-input @error('activated_at') is-invalid @enderror"
                                id="activated_at"
                                name="activated_at"
                                value="{{ old('activated_at', $customerService->activated_at->format('Y-m-d')) }}"
                                data-package-duration="{{ $customerService->servicePackage->default_duration_days ?? 30 }}"
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
                                class="form-control end-date-input @error('expires_at') is-invalid @enderror"
                                id="expires_at"
                                name="expires_at"
                                value="{{ old('expires_at', $customerService->expires_at->format('Y-m-d')) }}"
                                required>
                            @error('expires_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Tự động tính từ ngày kích hoạt + {{ $customerService->servicePackage->default_duration_days ?? 30 }} ngày</small>
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
                                    data-products="{{ $supplier->products->map(function($p) { return $p->product_name . ' - ' . number_format($p->price, 0, ',', '.') . ' VND'; })->implode('|') }}"
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

                        <!-- Thông tin lợi nhuận -->
                        <div class="col-md-12 mb-4">
                            <div class="card border-success">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-success">
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        Thông tin lợi nhuận
                                        @if($customerService->profit)
                                            <span class="badge bg-success ms-2">Đã có lợi nhuận</span>
                                        @else
                                            <span class="badge bg-secondary ms-2">Chưa nhập lãi</span>
                                        @endif
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
                                                       placeholder="Nhập số tiền lãi (VD: 100000 hoặc 100,000)"
                                                       value="{{ old('profit_amount', $customerService->profit->profit_amount ?? '') }}">
                                                <span class="input-group-text">VNĐ</span>
                                            </div>
                                            @error('profit_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">
                                                @if($customerService->profit)
                                                    Lợi nhuận hiện tại: <strong>{{ format_currency($customerService->profit->profit_amount) }}</strong>
                                                @else
                                                    Nhập số tiền lãi thu được từ đơn hàng này
                                                @endif
                                            </div>
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
                                                      placeholder="Ghi chú về lợi nhuận...">{{ old('profit_notes', $customerService->profit->notes ?? '') }}</textarea>
                                            @error('profit_notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if($customerService->profit)
                                                <div class="form-text">
                                                    <small class="text-muted">
                                                        Lần cập nhật cuối: {{ $customerService->profit->updated_at->format('d/m/Y H:i') }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        @if(request('source') === 'customer' && request('customer_id'))
                            <a href="{{ route('admin.customers.show', request('customer_id')) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Quay lại khách hàng
                            </a>
                        @else
                            <a href="{{ route('admin.customer-services.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Quay lại danh sách
                            </a>
                        @endif

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
        console.log('Edit form JavaScript loaded');
        
        // Service package duration tracking
        let currentPackageDuration = {{ $customerService->servicePackage->default_duration_days ?? 30 }};
        
        // Auto-calculation for date fields
        const startDateInput = document.querySelector('.start-date-input');
        const endDateInput = document.querySelector('.end-date-input');

        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', function() {
                if (this.value) {
                    // Calculate end date = start date + current package duration
                    const startDate = new Date(this.value);
                    const endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + currentPackageDuration);

                    // Format date for input
                    const formattedEndDate = endDate.toISOString().split('T')[0];
                    endDateInput.value = formattedEndDate;

                    // Show notification
                    showAutoCalculationNotice(currentPackageDuration);
                }
            });
        }

        // Function to update expiry date when service package changes
        function updateExpiryDateForSelectedService() {
            const selectedServiceCard = document.querySelector('.service-package-card.selected');
            
            if (selectedServiceCard && startDateInput && endDateInput) {
                const newDuration = parseInt(selectedServiceCard.dataset.duration) || 365;
                currentPackageDuration = newDuration;
                
                console.log('Service package changed, new duration:', newDuration);
                
                // If start date is filled, recalculate end date
                if (startDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    const endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + newDuration);
                    
                    const formattedEndDate = endDate.toISOString().split('T')[0];
                    endDateInput.value = formattedEndDate;
                    
                    console.log('Updated expiry date to:', formattedEndDate);
                    showAutoCalculationNotice(newDuration);
                }
            }
        }

        // Show auto-calculation notice
        function showAutoCalculationNotice(days) {
            // Remove old notice if exists
            const oldNotice = document.querySelector('.auto-calculation-notice');
            if (oldNotice) {
                oldNotice.remove();
            }

            // Create new notice
            const notice = document.createElement('div');
            notice.className = 'alert alert-info alert-dismissible fade show auto-calculation-notice mt-3';
            notice.innerHTML = `
                <i class="fas fa-info-circle me-2"></i>
                <strong>Tự động tính toán:</strong> Ngày hết hạn đã được cập nhật (+${days} ngày từ ngày kích hoạt)
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            // Add notice after the date fields row
            const dateFieldsRow = startDateInput.closest('.row');
            if (dateFieldsRow) {
                dateFieldsRow.insertAdjacentElement('afterend', notice);

                // Auto-hide after 5 seconds
                setTimeout(() => {
                    if (notice && notice.parentNode) {
                        notice.remove();
                    }
                }, 5000);
            }
        }

        // Family account handling for service packages
        const familySection = document.getElementById('family-selection');
        console.log('Family section element:', familySection);
        
        function updateFamilyAccountVisibility() {
            const selectedServiceCard = document.querySelector('.service-package-card.selected');
            console.log('Selected service card:', selectedServiceCard);
            
            if (selectedServiceCard) {
                const accountType = selectedServiceCard.dataset.accountType;
                console.log('Current account type:', accountType);
                
                if (accountType === 'Tài khoản add family') {
                    if (familySection) {
                        familySection.style.display = 'block';
                        console.log('Family section shown for add family account type');
                    }
                } else {
                    if (familySection) {
                        familySection.style.display = 'none';
                        // Clear family selection when hidden
                        const familySelect = document.getElementById('family_account_id');
                        if (familySelect) {
                            familySelect.value = '';
                        }
                        console.log('Family section hidden for non-family account type');
                    }
                }
            } else {
                console.log('No selected service card found');
            }
        }

        // Update family visibility on page load
        updateFamilyAccountVisibility();
        
        // Also check if current service package is add family type on page load
        const currentServicePackage = @json($customerService->servicePackage);
        if (currentServicePackage && currentServicePackage.account_type === 'Tài khoản add family') {
            if (familySection) {
                familySection.style.display = 'block';
                console.log('Family section shown on page load for current add family service');
            }
        }

        // Listen for service package card selection changes
        document.addEventListener('click', function(e) {
            if (e.target.closest('.service-package-card')) {
                // Add a small delay to ensure the selected class is updated
                setTimeout(function() {
                    updateFamilyAccountVisibility();
                    updateExpiryDateForSelectedService();
                }, 50);
            }
        });

        // Also listen for any programmatic changes to selected cards
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    const target = mutation.target;
                    if (target.classList.contains('service-package-card')) {
                        updateFamilyAccountVisibility();
                        updateExpiryDateForSelectedService();
                    }
                }
            });
        });

        // Observe all service package cards for class changes
        const serviceCards = document.querySelectorAll('.service-package-card');
        serviceCards.forEach(card => {
            observer.observe(card, { attributes: true, attributeFilter: ['class'] });
        });

        // Supplier handling code
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

        // Format profit amount input with thousand separators
        const profitAmountInput = document.getElementById('profit_amount');
        if (profitAmountInput) {
            // Format existing value on page load
            if (profitAmountInput.value) {
                profitAmountInput.value = formatNumberInput(profitAmountInput.value);
            }

            // Format as user types
            profitAmountInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\./g, '').replace(/[^0-9]/g, ''); // Remove dots and non-numeric characters
                if (value) {
                    e.target.value = formatNumberInput(value);
                } else {
                    e.target.value = '';
                }
            });

            // Clean value before form submission (remove dots for proper validation)
            profitAmountInput.closest('form').addEventListener('submit', function() {
                let cleanValue = profitAmountInput.value.replace(/\./g, '').replace(/[^0-9]/g, '');
                profitAmountInput.value = cleanValue;
            });
        }
    });

    // Function to format number with thousand separators
    function formatNumberInput(number) {
        return parseInt(number).toLocaleString('vi-VN');
    }
</script>
@endpush