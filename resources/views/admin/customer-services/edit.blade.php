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
                    
                    <!-- Hidden inputs to preserve redirect info -->
                    @if(request()->has('source'))
                    <input type="hidden" name="redirect_source" value="{{ request()->get('source') }}">
                    @endif
                    @if(request()->has('customer_id'))
                    <input type="hidden" name="redirect_customer_id" value="{{ request()->get('customer_id') }}">
                    @endif

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
                            </label>

                            <!-- Search Box with Autocomplete -->
                            <div class="position-relative">
                                <input type="text" 
                                       class="form-control" 
                                       id="servicePackageSearch" 
                                       placeholder="🔍 Gõ để tìm kiếm gói dịch vụ..."
                                       autocomplete="off">
                                
                                <!-- Hidden input to store actual value -->
                                <input type="hidden" 
                                       name="service_package_id" 
                                       id="service_package_id" 
                                       value="{{ old('service_package_id', $customerService->service_package_id) }}"
                                       required>
                                
                                <!-- Dropdown Results -->
                                <div id="servicePackageResults" 
                                     class="position-absolute w-100 bg-white border rounded shadow-sm"
                                     style="display: none; max-height: 300px; overflow-y: auto; z-index: 1000; top: 100%; margin-top: 2px;">
                                </div>
                            </div>
                            
                            <!-- Display selected package -->
                            <div id="selectedPackageDisplay" class="mt-2" style="display: {{ $customerService->servicePackage ? 'block' : 'none' }};">
                                <div class="alert alert-success mb-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><i class="fas fa-check-circle me-1"></i>Đã chọn:</strong> 
                                            <span id="selectedPackageName">{{ $customerService->servicePackage->name ?? '' }}</span>
                                            <span id="selectedPackageType" class="badge bg-secondary ms-2">{{ $customerService->servicePackage->account_type ?? '' }}</span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger py-0" id="clearSelection">
                                            <i class="fas fa-times"></i> Đổi gói
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            @error('service_package_id')
                            <div class="text-danger mt-1"><small>{{ $message }}</small></div>
                            @enderror
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
                                            data-used-slots="{{ $family->used_slots }}"
                                            data-available-slots="{{ $family->available_slots }}"
                                            data-max-slots="{{ $family->max_members }}"
                                            {{ old('family_account_id', $currentFamilyMembership?->family_account_id) == $family->id ? 'selected' : '' }}>
                                            {{ $family->family_name }} 
                                            ({{ $family->used_slots }}/{{ $family->max_members }} slots - Còn: {{ $family->available_slots }})
                                            - {{ $family->owner_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('family_account_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Mỗi dịch vụ được gán = 1 slot</div>
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

                        <!-- Shared Credential Selection for Shared Account Services -->
                        <div id="shared-credential-selection" class="col-md-12 mb-3" 
                             style="display: {{ str_contains(strtolower($customerService->servicePackage->account_type ?? ''), 'dùng chung') ? 'block' : 'none' }};">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="fas fa-key me-2"></i>
                                        Chọn tài khoản dùng chung
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="shared_credential_id" class="form-label">
                                                <i class="fas fa-user-shield me-2 text-warning"></i>
                                                Tài khoản <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select @error('shared_credential_id') is-invalid @enderror"
                                                id="shared_credential_id"
                                                name="shared_credential_id">
                                                <option value="">Chọn tài khoản</option>
                                                @if(isset($sharedCredentials) && $sharedCredentials->count() > 0)
                                                @foreach($sharedCredentials as $cred)
                                                <option value="{{ $cred->id }}"
                                                    data-email="{{ $cred->email }}"
                                                    data-password="{{ $cred->password }}"
                                                    data-service-package-id="{{ $cred->service_package_id }}"
                                                    data-current-users="{{ $cred->current_users }}"
                                                    data-max-users="{{ $cred->max_users }}"
                                                    data-available-slots="{{ $cred->available_slots }}"
                                                    {{ $customerService->login_email == $cred->email ? 'selected' : '' }}>
                                                    {{ $cred->email }} ({{ $cred->current_users }}/{{ $cred->max_users }} slots - Còn: {{ $cred->available_slots }})
                                                </option>
                                                @endforeach
                                                @endif
                                            </select>
                                            @if(isset($sharedCredentials))
                                            <small class="text-muted d-block mt-1">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Tổng: {{ $sharedCredentials->count() }} tài khoản dùng chung còn slot
                                            </small>
                                            @endif
                                            <div id="no-credential-for-package" class="alert alert-warning mt-2" style="display: none;">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Không có tài khoản dùng chung nào</strong> cho gói dịch vụ này.
                                                <hr class="my-2">
                                                <a href="{{ route('admin.shared-accounts.credentials') }}" target="_blank" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-plus me-1"></i>Quản lý tài khoản dùng chung
                                                </a>
                                            </div>
                                            @error('shared_credential_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Chọn tài khoản để tự động điền thông tin đăng nhập</div>
                                        </div>
                                    </div>
                                </div>
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
                    </div>

                    <!-- Custom Duration Row -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="custom_duration" class="form-label">
                                <i class="fas fa-clock me-1"></i>
                                Thời hạn tùy chỉnh
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control" 
                                       id="custom_duration" 
                                       name="custom_duration" 
                                       min="1" 
                                       placeholder="Nhập số"
                                       value="">
                                <select class="form-select" id="duration_unit" name="duration_unit" style="max-width: 120px;">
                                    <option value="days">Ngày</option>
                                    <option value="months" selected>Tháng</option>
                                    <option value="years">Năm</option>
                                </select>
                            </div>
                            <div class="form-text text-info">
                                <i class="fas fa-info-circle me-1"></i>
                                Để trống để sử dụng thời hạn mặc định của gói dịch vụ (<strong>{{ $customerService->servicePackage->default_duration_days ?? 30 }}</strong> ngày)
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-calculator me-1"></i>
                                Tính toán tự động
                            </label>
                            <div class="alert alert-light border p-2 mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-magic text-primary me-2"></i>
                                    <small class="text-muted mb-0">
                                        Ngày hết hạn sẽ được tự động cập nhật khi bạn thay đổi ngày kích hoạt hoặc thời hạn
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
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
        const customDurationInput = document.getElementById('custom_duration');
        const durationUnitSelect = document.getElementById('duration_unit');

        // Function to calculate duration in days
        function calculateDuration() {
            const duration = parseInt(customDurationInput?.value) || 0;
            const unit = durationUnitSelect?.value || 'days';
            
            if (duration <= 0) {
                return currentPackageDuration; // Use package default
            }
            
            switch (unit) {
                case 'days':
                    return duration;
                case 'months':
                    return duration * 30; // Approximate
                case 'years':
                    return duration * 365; // Approximate
                default:
                    return currentPackageDuration;
            }
        }

        // Function to update expires date based on start date and duration
        function updateExpiresDate() {
            if (!startDateInput?.value || !endDateInput) return;
            
            const startDate = new Date(startDateInput.value);
            if (isNaN(startDate.getTime())) return;
            
            const durationInDays = calculateDuration();
            const endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + durationInDays);
            
            const formattedEndDate = endDate.toISOString().split('T')[0];
            endDateInput.value = formattedEndDate;
            
            console.log(`Updated end date: ${formattedEndDate} (${durationInDays} days from ${startDateInput.value})`);
            showAutoCalculationNotice(durationInDays);
        }

        // Add event listeners for auto-calculation
        if (startDateInput) {
            startDateInput.addEventListener('change', updateExpiresDate);
            startDateInput.addEventListener('input', updateExpiresDate);
        }
        
        if (customDurationInput) {
            customDurationInput.addEventListener('change', updateExpiresDate);
            customDurationInput.addEventListener('input', updateExpiresDate);
            customDurationInput.addEventListener('keyup', updateExpiresDate);
        }
        
        if (durationUnitSelect) {
            durationUnitSelect.addEventListener('change', updateExpiresDate);
        }

        // Function to update expiry date when service package changes
        function updateExpiryDateForSelectedService() {
            const selectedServiceCard = document.querySelector('.service-package-card.selected');
            
            if (selectedServiceCard) {
                const newDuration = parseInt(selectedServiceCard.dataset.duration) || 365;
                currentPackageDuration = newDuration;
                
                console.log('Service package changed, new duration:', newDuration);
                
                // Clear custom duration when package changes (optional)
                if (customDurationInput) {
                    customDurationInput.value = '';
                }
                
                // Recalculate dates using the new package duration
                updateExpiresDate();
            }
        }

        // Show auto-calculation notice
        function showAutoCalculationNotice(days) {
            // Remove old notice if exists
            const oldNotice = document.querySelector('.auto-calculation-notice');
            if (oldNotice) {
                oldNotice.remove();
            }

            // Determine source of duration
            const isCustomDuration = customDurationInput && customDurationInput.value && parseInt(customDurationInput.value) > 0;
            const durationSource = isCustomDuration ? 'thời hạn tùy chỉnh' : 'gói dịch vụ';

            // Create new notice
            const notice = document.createElement('div');
            notice.className = 'alert alert-success alert-dismissible fade show auto-calculation-notice mt-3';
            notice.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                <strong>Tự động tính toán:</strong> Ngày hết hạn đã được cập nhật (+${days} ngày từ ${durationSource})
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            // Add notice after the custom duration row
            const customDurationRow = customDurationInput ? customDurationInput.closest('.row') : null;
            const targetRow = customDurationRow || (startDateInput ? startDateInput.closest('.row') : null);
            
            if (targetRow) {
                targetRow.insertAdjacentElement('afterend', notice);

                // Auto-hide after 4 seconds
                setTimeout(() => {
                    if (notice && notice.parentNode) {
                        notice.remove();
                    }
                }, 4000);
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

        // Format profit amount input with thousand separators
        const profitAmountInput = document.getElementById('profit_amount');
        if (profitAmountInput) {
            // Format existing value on page load
            if (profitAmountInput.value) {
                // Remove all non-numeric characters first, then format
                let cleanValue = profitAmountInput.value.replace(/[^0-9]/g, '');
                if (cleanValue) {
                    profitAmountInput.value = formatNumberInput(cleanValue);
                }
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

        // Service Package Autocomplete Search
        const servicePackageSearch = document.getElementById('servicePackageSearch');
        const servicePackageInput = document.getElementById('service_package_id');
        const servicePackageResults = document.getElementById('servicePackageResults');
        const selectedPackageDisplay = document.getElementById('selectedPackageDisplay');
        const selectedPackageName = document.getElementById('selectedPackageName');
        const clearSelectionBtn = document.getElementById('clearSelection');
        
        // Get all service packages data
        const allServicePackages = {!! json_encode($servicePackages->map(function($pkg) {
            return [
                'id' => $pkg->id,
                'name' => $pkg->name,
                'account_type' => $pkg->account_type,
                'category' => $pkg->category ? $pkg->category->name : 'Chưa phân loại'
            ];
        })->values()) !!};
        
        // Show selected package on load if exists
        const currentPackageId = '{{ old('service_package_id', $customerService->service_package_id) }}';
        if (currentPackageId) {
            const currentPackage = allServicePackages.find(pkg => pkg.id == currentPackageId);
            if (currentPackage) {
                selectedPackageName.textContent = currentPackage.name;
                const selectedPackageType = document.getElementById('selectedPackageType');
                if (selectedPackageType) {
                    selectedPackageType.textContent = currentPackage.account_type;
                }
                selectedPackageDisplay.style.display = 'block';
                servicePackageSearch.value = '';
                servicePackageSearch.placeholder = '🔍 Tìm để đổi gói dịch vụ...';
                
                // Initialize account type sections visibility
                updateAccountTypeSections(currentPackage.account_type, currentPackage.id);
            }
        }
        
        // Search input handler
        servicePackageSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            if (searchTerm.length < 2) {
                servicePackageResults.style.display = 'none';
                return;
            }
            
            // Filter packages
            const filtered = allServicePackages.filter(pkg => {
                return pkg.name.toLowerCase().includes(searchTerm) ||
                       pkg.account_type.toLowerCase().includes(searchTerm) ||
                       pkg.category.toLowerCase().includes(searchTerm);
            });
            
            // Helper function to escape HTML attributes
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
            
            // Store filtered results globally for click handler
            window.filteredServicePackages = filtered;
            
            // Display results
            if (filtered.length > 0) {
                servicePackageResults.innerHTML = filtered.map((pkg, index) => `
                    <div class="service-package-item px-3 py-2 border-bottom" 
                         style="cursor: pointer;"
                         onclick="selectServicePackage(${index})"
                         onmouseover="this.style.backgroundColor='#f8f9fa'"
                         onmouseout="this.style.backgroundColor=''">
                        <div style="pointer-events: none;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>${escapeHtml(pkg.name)}</strong>
                                    <br>
                                    <small class="text-muted">${escapeHtml(pkg.category)}</small>
                                </div>
                                <span class="badge bg-secondary">${escapeHtml(pkg.account_type)}</span>
                            </div>
                        </div>
                    </div>
                `).join('');
                servicePackageResults.style.display = 'block';
            } else {
                servicePackageResults.innerHTML = '<div class="px-3 py-2 text-muted">❌ Không tìm thấy gói dịch vụ phù hợp</div>';
                servicePackageResults.style.display = 'block';
            }
        });
        
        // Clear selection
        clearSelectionBtn.addEventListener('click', function() {
            servicePackageInput.value = '';
            selectedPackageDisplay.style.display = 'none';
            servicePackageSearch.value = '';
            servicePackageSearch.placeholder = '🔍 Gõ để tìm kiếm gói dịch vụ...';
            servicePackageSearch.focus();
            
            // Hide all account type sections
            const familySelection = document.getElementById('family-selection');
            const sharedCredentialSelection = document.getElementById('shared-credential-selection');
            if (familySelection) {
                familySelection.style.display = 'none';
            }
            if (sharedCredentialSelection) {
                sharedCredentialSelection.style.display = 'none';
            }
        });
        
        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!servicePackageSearch.contains(e.target) && !servicePackageResults.contains(e.target)) {
                servicePackageResults.style.display = 'none';
            }
        });
        
        // Focus search when clicking on results area
        servicePackageSearch.addEventListener('focus', function() {
            if (this.value.length >= 2) {
                const event = new Event('input');
                this.dispatchEvent(event);
            }
        });
    });

    // Function to format number with thousand separators
    function formatNumberInput(number) {
        // Ensure number is a string and remove any non-numeric characters
        let cleanNumber = String(number).replace(/[^0-9]/g, '');
        if (!cleanNumber) return '';
        return parseInt(cleanNumber).toLocaleString('vi-VN');
    }
    
    // Global function to select service package from dropdown
    function selectServicePackage(index) {
        const pkg = window.filteredServicePackages ? window.filteredServicePackages[index] : null;
        
        if (!pkg) {
            console.log('No package found at index:', index);
            return;
        }
        
        console.log('Selected package:', pkg.name, pkg.id, pkg.account_type);
        
        // Get DOM elements
        const servicePackageInput = document.getElementById('service_package_id');
        const selectedPackageName = document.getElementById('selectedPackageName');
        const selectedPackageType = document.getElementById('selectedPackageType');
        const selectedPackageDisplay = document.getElementById('selectedPackageDisplay');
        const servicePackageSearch = document.getElementById('servicePackageSearch');
        const servicePackageResults = document.getElementById('servicePackageResults');
        
        // Set values
        servicePackageInput.value = pkg.id;
        selectedPackageName.textContent = pkg.name;
        if (selectedPackageType) {
            selectedPackageType.textContent = pkg.account_type;
        }
        selectedPackageDisplay.style.display = 'block';
        
        // Clear search and hide results
        servicePackageSearch.value = '';
        servicePackageResults.style.display = 'none';
        
        // Update account type sections based on selected package
        updateAccountTypeSections(pkg.account_type, pkg.id);
    }
    
    // Function to update visibility of account type sections
    function updateAccountTypeSections(accountType, packageId) {
        const accountTypeLower = (accountType || '').toLowerCase();
        
        const familySelection = document.getElementById('family-selection');
        const sharedCredentialSelection = document.getElementById('shared-credential-selection');
        
        // Hide all sections first
        if (familySelection) familySelection.style.display = 'none';
        if (sharedCredentialSelection) sharedCredentialSelection.style.display = 'none';
        
        // Show appropriate section based on account type
        if (accountTypeLower.includes('add family') || accountTypeLower.includes('add team')) {
            if (familySelection) {
                familySelection.style.display = 'block';
            }
        } else if (accountTypeLower.includes('dùng chung') || accountTypeLower.includes('shared')) {
            if (sharedCredentialSelection) {
                sharedCredentialSelection.style.display = 'block';
                // Filter shared credentials by selected package
                filterSharedCredentialsByPackage(packageId);
            }
        }
        // For "chính chủ" or "cá nhân" types, both sections stay hidden
    }
    
    // Function to filter shared credentials dropdown by selected package
    function filterSharedCredentialsByPackage(packageId) {
        const select = document.getElementById('shared_credential_id');
        if (!select) return;
        
        const options = select.querySelectorAll('option');
        let hasVisibleOptions = false;
        
        options.forEach(option => {
            if (option.value === '') {
                // Keep the placeholder option visible
                option.style.display = '';
                return;
            }
            
            const optionPackageId = option.getAttribute('data-service-package-id');
            if (optionPackageId == packageId) {
                option.style.display = '';
                hasVisibleOptions = true;
            } else {
                option.style.display = 'none';
                if (option.selected) {
                    option.selected = false;
                    select.value = '';
                }
            }
        });
        
        // Show warning if no credentials for this package
        const noCredWarning = document.getElementById('no-credential-for-package');
        if (noCredWarning) {
            noCredWarning.style.display = hasVisibleOptions ? 'none' : 'block';
        }
    }
    
    // Handle shared credential selection to auto-fill email/password
    document.addEventListener('DOMContentLoaded', function() {
        const sharedCredSelect = document.getElementById('shared_credential_id');
        if (sharedCredSelect) {
            sharedCredSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    const email = selectedOption.getAttribute('data-email');
                    const password = selectedOption.getAttribute('data-password');
                    
                    const emailInput = document.getElementById('login_email');
                    const passwordInput = document.getElementById('login_password');
                    
                    if (emailInput && email) emailInput.value = email;
                    if (passwordInput && password) passwordInput.value = password;
                }
            });
        }
    });
</script>
@endpush