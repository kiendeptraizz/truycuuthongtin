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
                            
                            <!-- DEBUG: Show service packages with shared account type -->
                            @if(config('app.debug'))
                            <div class="alert alert-secondary mt-2 small" style="max-height: 150px; overflow-y: auto;">
                                <strong>🔍 Debug - Gói có account_type "dùng chung":</strong><br>
                                @php
                                    $sharedPackages = $servicePackages->filter(function($pkg) {
                                        return str_contains(strtolower($pkg->account_type ?? ''), 'dùng chung');
                                    });
                                @endphp
                                @if($sharedPackages->count() > 0)
                                    @foreach($sharedPackages as $pkg)
                                        - <strong>ID: {{ $pkg->id }}</strong>, Name: {{ $pkg->name }}, Account Type: "{{ $pkg->account_type }}"<br>
                                    @endforeach
                                @else
                                    <span class="text-warning">Không có gói nào có account_type chứa "dùng chung"!</span><br>
                                    <strong>Các account_type hiện có:</strong><br>
                                    @foreach($servicePackages->pluck('account_type')->unique() as $type)
                                        - "{{ $type }}"<br>
                                    @endforeach
                                @endif
                            </div>
                            @endif

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
                                                        data-service-package-id="{{ $family->service_package_id }}"
                                                        data-service-name="{{ $family->servicePackage->name ?? 'N/A' }}"
                                                        data-used-slots="{{ $family->used_slots }}"
                                                        data-available-slots="{{ $family->available_slots }}"
                                                        data-max-slots="{{ $family->max_members }}"
                                                        {{ old('family_account_id') == $family->id ? 'selected' : '' }}>
                                                        {{ $family->family_code }} - {{ $family->family_name }}
                                                        ({{ $family->used_slots }}/{{ $family->max_members }} slots - Còn: {{ $family->available_slots }})
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <div id="no-family-for-package" class="alert alert-warning mt-2" style="display: none;">
                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                    Không có Family Account nào cho gói dịch vụ này.
                                                    <a href="{{ route('admin.family-accounts.create') }}" target="_blank">Tạo mới</a>
                                                </div>
                                                @error('family_account_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">Mỗi dịch vụ được gán = 1 slot</div>
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
                                                        <strong>Slots:</strong> <span id="family-slots-display"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DEBUG: Show available shared credentials info -->
                            @if(config('app.debug'))
                            <div class="alert alert-info mt-3 small">
                                <strong>🔍 Debug Info - Shared Credentials:</strong><br>
                                @if(isset($sharedCredentials))
                                    Tổng số tài khoản có sẵn: <strong>{{ $sharedCredentials->count() }}</strong><br>
                                    @if($sharedCredentials->count() > 0)
                                        @foreach($sharedCredentials as $cred)
                                            - ID: {{ $cred->id }}, Email: {{ $cred->email }}, <strong>Package ID: {{ $cred->service_package_id }}</strong>, 
                                            Package: {{ $cred->servicePackage->name ?? 'N/A' }}, 
                                            Slots: {{ $cred->current_users }}/{{ $cred->max_users }}<br>
                                        @endforeach
                                    @else
                                        <span class="text-warning">⚠️ Không có tài khoản dùng chung nào còn slot trống hoặc đang active!</span>
                                    @endif
                                @else
                                    <span class="text-danger">❌ Biến $sharedCredentials không tồn tại!</span>
                                @endif
                                <hr class="my-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.handleFamilyAccountLogic && window.handleFamilyAccountLogic()">
                                    🔄 Force Trigger Logic
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="document.getElementById('shared-credential-selection').style.display='block'">
                                    👁️ Force Show Shared Selection
                                </button>
                            </div>
                            @endif

                            <!-- Shared Credential Selection for Shared Account Services -->
                            <div id="shared-credential-selection" class="mt-3" style="display: none;">
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
                                                        {{ old('shared_credential_id') == $cred->id ? 'selected' : '' }}>
                                                        {{ $cred->email }} ({{ $cred->current_users }}/{{ $cred->max_users }} slots - Còn: {{ $cred->available_slots }}) [Gói ID: {{ $cred->service_package_id }}]
                                                    </option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                                @if(isset($sharedCredentials))
                                                <small class="text-muted d-block mt-1">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Tổng: {{ $sharedCredentials->count() }} tài khoản dùng chung trong hệ thống
                                                </small>
                                                @else
                                                <small class="text-danger d-block mt-1">
                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                    Không có dữ liệu tài khoản dùng chung!
                                                </small>
                                                @endif
                                                <div id="no-credential-for-package" class="alert alert-warning mt-2" style="display: none;">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    <strong>Không có tài khoản dùng chung nào</strong> cho gói dịch vụ này.
                                                    <hr class="my-2">
                                                    <small>Nguyên nhân có thể:</small>
                                                    <ul class="mb-2 small">
                                                        <li>Chưa tạo tài khoản dùng chung cho gói này</li>
                                                        <li>Tất cả tài khoản đã hết slots (đầy người dùng)</li>
                                                        <li>Tài khoản không hoạt động (inactive)</li>
                                                    </ul>
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

                                        <!-- Shared Credential Details Display -->
                                        <div id="shared-credential-details" style="display: none;">
                                            <div class="alert alert-success">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Email:</strong> <span id="cred-email-display"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Slots:</strong> <span id="cred-slots-display"></span>
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
                            <label for="custom_duration" class="form-label">
                                <i class="fas fa-clock me-1"></i>
                                Thời hạn tùy chỉnh
                            </label>
                            <div class="input-group">
                                <input type="number"
                                    class="form-control @error('duration_days') is-invalid @enderror"
                                    id="custom_duration"
                                    name="custom_duration"
                                    min="1"
                                    placeholder="Nhập số"
                                    value="{{ old('custom_duration') }}">
                                <select class="form-select" id="duration_unit" name="duration_unit" style="max-width: 120px;">
                                    <option value="days">Ngày</option>
                                    <option value="months" selected>Tháng</option>
                                    <option value="years">Năm</option>
                                </select>
                            </div>

                            <!-- Hidden input để lưu giá trị ngày thực tế -->
                            <input type="hidden" name="duration_days" id="duration_days" value="{{ old('duration_days') }}">

                            @error('duration_days')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-info" id="duration_calculated_text">
                                <i class="fas fa-info-circle me-1"></i>
                                Nhập thời hạn để tự động tính ngày hết hạn
                            </div>
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
                                                    placeholder="Nhập số tiền lãi (VD: 70.000)"
                                                    value="{{ old('profit_amount') }}"
                                                    inputmode="numeric"
                                                    maxlength="20">
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
    // Define PHP variables for JavaScript
    const hasFamilyMembership = @json($hasFamilyMembership ?? false);

    // Function to format number with thousand separators (Vietnamese format: 70.000)
    function formatNumberInput(number) {
        // Remove non-digits first
        const cleanNumber = String(number).replace(/\D/g, '');
        if (!cleanNumber) return '';
        
        // Format with dots as thousand separators
        return cleanNumber.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // =====================================================
    // GLOBAL FUNCTIONS - Defined outside DOMContentLoaded
    // =====================================================
    
    // Main function to handle account type logic
    window.handleAccountTypeLogic = function() {
        const serviceSelect = document.getElementById('service_package_id');
        const familyWarning = document.getElementById('family-warning');
        const familySelection = document.getElementById('family-selection');
        const noFamilyWarning = document.getElementById('no-family-for-package');
        const sharedCredentialSelection = document.getElementById('shared-credential-selection');
        const noCredentialWarning = document.getElementById('no-credential-for-package');
        const submitBtn = document.querySelector('button[type="submit"]');

        console.log('🔄 handleAccountTypeLogic called');
        console.log('📦 Service select value:', serviceSelect?.value);

        if (!serviceSelect || !serviceSelect.value) {
            console.log('⚠️ No service selected');
            if (familyWarning) familyWarning.style.display = 'none';
            if (familySelection) familySelection.style.display = 'none';
            if (sharedCredentialSelection) sharedCredentialSelection.style.display = 'none';
            if (noFamilyWarning) noFamilyWarning.style.display = 'none';
            if (noCredentialWarning) noCredentialWarning.style.display = 'none';
            return;
        }

        const selectedCard = document.querySelector(`[data-package-id="${serviceSelect.value}"]`);
        console.log('🎴 Selected card:', selectedCard);
        
        if (!selectedCard) {
            console.log('❌ No card found for package ID:', serviceSelect.value);
            return;
        }

        const accountType = selectedCard.getAttribute('data-account-type') || '';
        const selectedPackageId = serviceSelect.value;

        console.log('📝 Account type from card:', `"${accountType}"`);
        console.log('🔢 Selected package ID:', selectedPackageId);

        // Hide all by default
        if (familySelection) familySelection.style.display = 'none';
        if (sharedCredentialSelection) sharedCredentialSelection.style.display = 'none';
        if (familyWarning) familyWarning.style.display = 'none';
        if (noFamilyWarning) noFamilyWarning.style.display = 'none';
        if (noCredentialWarning) noCredentialWarning.style.display = 'none';

        // Check for different account types (case-insensitive and flexible matching)
        const accountTypeLower = accountType.toLowerCase();
        console.log('🔍 Account type lowercase:', `"${accountTypeLower}"`);
        
        // Check for family/add family type
        if (accountTypeLower.includes('add family') || accountTypeLower.includes('family')) {
            console.log('✅ Detected: Family account type');
            if (familySelection) {
                familySelection.style.display = 'block';
                window.filterFamilyAccountsByPackage && window.filterFamilyAccountsByPackage(selectedPackageId);
            }
        } 
        // Check for shared account type
        else if (accountTypeLower.includes('dùng chung') || accountTypeLower.includes('shared')) {
            console.log('✅ Detected: Shared account type - showing credential selection');
            if (sharedCredentialSelection) {
                sharedCredentialSelection.style.display = 'block';
                console.log('✅ Set sharedCredentialSelection display to block');
                window.filterSharedCredentialsByPackage && window.filterSharedCredentialsByPackage(selectedPackageId);
            } else {
                console.log('❌ sharedCredentialSelection element NOT FOUND!');
            }
        } else {
            console.log('ℹ️ Other account type:', accountType);
        }

        // Reset submit button
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Gán dịch vụ';
            submitBtn.classList.remove('btn-danger');
            submitBtn.classList.add('btn-primary');
        }
    };

    // Alias for backward compatibility
    window.handleFamilyAccountLogic = window.handleAccountTypeLogic;

    // Filter shared credentials function
    window.filterSharedCredentialsByPackage = function(packageId) {
        const sharedSelect = document.getElementById('shared_credential_id');
        const noCredentialWarning = document.getElementById('no-credential-for-package');
        
        if (!sharedSelect) {
            console.log('❌ shared_credential_id select not found');
            return;
        }
        
        const options = sharedSelect.querySelectorAll('option');
        let hasVisibleOptions = false;

        console.log('🔍 Filtering shared credentials for package ID:', packageId);
        console.log('📋 Total options:', options.length - 1);

        // Reset selection
        sharedSelect.value = '';

        options.forEach(option => {
            if (option.value === '') {
                option.style.display = '';
                return;
            }

            const optionPackageId = option.getAttribute('data-service-package-id');
            console.log(`  Option: ${option.textContent.substring(0, 40)}... | Package ID: ${optionPackageId} | Match: ${optionPackageId === packageId}`);
            
            if (optionPackageId === packageId) {
                option.style.display = '';
                hasVisibleOptions = true;
            } else {
                option.style.display = 'none';
            }
        });

        console.log('✅ Has matching options:', hasVisibleOptions);

        if (noCredentialWarning) {
            noCredentialWarning.style.display = hasVisibleOptions ? 'none' : 'block';
        }
    };

    // Filter family accounts function
    window.filterFamilyAccountsByPackage = function(packageId) {
        const familySelect = document.getElementById('family_account_id');
        const noFamilyWarning = document.getElementById('no-family-for-package');
        
        if (!familySelect) return;
        
        const options = familySelect.querySelectorAll('option');
        let hasVisibleOptions = false;

        familySelect.value = '';

        options.forEach(option => {
            if (!option.value) {
                option.style.display = '';
                return;
            }

            const familyPackageId = option.getAttribute('data-service-package-id');
            if (familyPackageId === packageId) {
                option.style.display = '';
                hasVisibleOptions = true;
            } else {
                option.style.display = 'none';
            }
        });

        if (noFamilyWarning) {
            noFamilyWarning.style.display = hasVisibleOptions ? 'none' : 'block';
        }

        const familyDetails = document.getElementById('family-details');
        if (familyDetails) familyDetails.style.display = 'none';
    };

    // =====================================================
    // DOMContentLoaded
    // =====================================================
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
            serviceSelect: !!serviceSelect,
            sharedCredentialSelection: !!document.getElementById('shared-credential-selection'),
            shared_credential_id: !!document.getElementById('shared_credential_id')
        });

        // Listen for changes on service select
        if (serviceSelect) {
            serviceSelect.addEventListener('change', function() {
                console.log('📢 Service select CHANGE event fired, value:', this.value);
                window.handleAccountTypeLogic();
            });
            
            // Also listen for input event
            serviceSelect.addEventListener('input', function() {
                console.log('📢 Service select INPUT event fired, value:', this.value);
                window.handleAccountTypeLogic();
            });
        }

        // Add click listeners to all package cards
        document.querySelectorAll('.package-card').forEach(card => {
            card.addEventListener('click', function() {
                console.log('📢 Package card CLICKED, package ID:', this.dataset.packageId);
                // Wait a bit for the grid selector to update the hidden input
                setTimeout(function() {
                    window.handleAccountTypeLogic();
                }, 100);
            });
        });

        // MutationObserver to watch for changes in the hidden input value
        if (serviceSelect) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                        console.log('📢 Service select VALUE attribute changed');
                        window.handleAccountTypeLogic();
                    }
                });
            });
            observer.observe(serviceSelect, { attributes: true });
        }

        // Initial check after a delay
        setTimeout(function() {
            console.log('⏰ Initial check - service value:', serviceSelect?.value);
            if (serviceSelect && serviceSelect.value) {
                window.handleAccountTypeLogic();
            }
        }, 500);

        // Additional check after grid might be fully loaded
        setTimeout(function() {
            console.log('⏰ Secondary check - service value:', serviceSelect?.value);
            if (serviceSelect && serviceSelect.value) {
                window.handleAccountTypeLogic();
            }
        }, 1000);

        // =====================================================
        // FORMAT PROFIT AMOUNT INPUT
        // =====================================================
        const profitAmountInput = document.getElementById('profit_amount');
        if (profitAmountInput) {
            console.log('💰 Initializing profit amount formatter');
            
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
        }

        function initializeServicePackageHandlers() {
            // Main logic moved to global functions and DOMContentLoaded
            console.log('✅ Service package handlers initialized');

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
            const familySlotsDisplay = document.getElementById('family-slots-display');

            if (familyAccountSelect) {
                familyAccountSelect.addEventListener('change', function() {
                    if (this.value) {
                        const selectedOption = this.options[this.selectedIndex];

                        // Fill family information
                        familyCodeDisplay.textContent = selectedOption.dataset.familyCode;
                        familyEmailDisplay.textContent = selectedOption.dataset.primaryEmail;
                        familyServiceDisplay.textContent = selectedOption.dataset.serviceName;
                        familySlotsDisplay.textContent = selectedOption.dataset.usedSlots + '/' + selectedOption.dataset.maxSlots + ' (Còn: ' + selectedOption.dataset.availableSlots + ' slots)';

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

            // Handle shared credential selection
            const sharedCredentialSelect = document.getElementById('shared_credential_id');
            const sharedCredentialDetails = document.getElementById('shared-credential-details');
            const credEmailDisplay = document.getElementById('cred-email-display');
            const credSlotsDisplay = document.getElementById('cred-slots-display');
            const loginEmailInput = document.getElementById('login_email');
            const loginPasswordInput = document.getElementById('login_password');

            if (sharedCredentialSelect) {
                sharedCredentialSelect.addEventListener('change', function() {
                    if (this.value) {
                        const selectedOption = this.options[this.selectedIndex];

                        // Fill credential information
                        const email = selectedOption.dataset.email;
                        const password = selectedOption.dataset.password;
                        const currentUsers = selectedOption.dataset.currentUsers;
                        const maxUsers = selectedOption.dataset.maxUsers;
                        const availableSlots = selectedOption.dataset.availableSlots;

                        credEmailDisplay.textContent = email;
                        credSlotsDisplay.textContent = currentUsers + '/' + maxUsers + ' (Còn: ' + availableSlots + ' slots)';

                        // Auto-fill login email and password
                        if (loginEmailInput && email) {
                            loginEmailInput.value = email;
                        }
                        if (loginPasswordInput && password) {
                            loginPasswordInput.value = password;
                        }

                        sharedCredentialDetails.style.display = 'block';
                    } else {
                        sharedCredentialDetails.style.display = 'none';
                        // Clear login fields when no credential selected
                        if (loginEmailInput) loginEmailInput.value = '';
                        if (loginPasswordInput) loginPasswordInput.value = '';
                    }
                });

                // Show credential details if already selected
                if (sharedCredentialSelect.value) {
                    sharedCredentialSelect.dispatchEvent(new Event('change'));
                }
            }

            // Clean currency values before form submission (remove dots for proper validation)
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    console.log('🚀 Form submit event triggered');

                    // Check form validity
                    if (!form.checkValidity()) {
                        console.log('❌ Form is INVALID');

                        // Find invalid fields
                        const invalidFields = [];
                        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
                        inputs.forEach(input => {
                            if (!input.checkValidity()) {
                                invalidFields.push({
                                    name: input.name,
                                    id: input.id,
                                    message: input.validationMessage,
                                    value: input.value
                                });
                            }
                        });

                        console.log('❌ Invalid fields:', invalidFields);

                        // Don't prevent default - let browser show validation messages
                        return;
                    }

                    console.log('✅ Form is VALID - proceeding with submit');

                    // Backend sẽ tự động parse currency values, không cần clean ở đây

                    // Log all form data
                    const formData = new FormData(form);
                    console.log('📋 Form data being submitted:');
                    for (let [key, value] of formData.entries()) {
                        console.log(`  ${key}: ${value}`);
                    }
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
            const durationUnitSelect = document.getElementById('duration_unit');
            const customDurationInput = document.getElementById('custom_duration');
            const durationDaysHidden = document.getElementById('duration_days');
            const durationCalculatedText = document.getElementById('duration_calculated_text');
            const activatedAtInput = document.getElementById('activated_at');
            const expiresAtInput = document.getElementById('expires_at');

            console.log('📊 Duration Calculator Elements Found:', {
                'durationUnitSelect': durationUnitSelect ? '✅ Found' : '❌ Not found',
                'customDurationInput': customDurationInput ? '✅ Found' : '❌ Not found',
                'durationDaysHidden': durationDaysHidden ? '✅ Found' : '❌ Not found',
                'durationCalculatedText': durationCalculatedText ? '✅ Found' : '❌ Not found',
                'activatedAtInput': activatedAtInput ? '✅ Found' : '❌ Not found',
                'expiresAtInput': expiresAtInput ? '✅ Found' : '❌ Not found'
            });

            if (durationUnitSelect && customDurationInput && durationDaysHidden) {
                console.log('Duration calculator initialized successfully');

                function calculateDuration() {
                    console.log('🔄 calculateDuration() called');

                    const unit = durationUnitSelect.value;
                    const value = parseInt(customDurationInput.value) || 0;
                    let days = 0;

                    console.log('📊 Duration calculation - unit:', unit, 'value:', value);

                    if (value === 0) {
                        // No value entered yet
                        if (durationCalculatedText) {
                            durationCalculatedText.innerHTML = '<i class="fas fa-info-circle me-1"></i>Nhập thời hạn để tự động tính ngày hết hạn';
                        }
                        if (durationDaysHidden) {
                            durationDaysHidden.value = '';
                        }
                        console.log('⚠️ No duration value entered');
                        return;
                    }

                    if (unit === 'days') {
                        days = value;
                        if (durationCalculatedText) {
                            durationCalculatedText.innerHTML = `<i class="fas fa-check-circle me-1 text-success"></i>Thời hạn: ${value} ngày`;
                        }
                    } else if (unit === 'months') {
                        days = value * 30; // 1 tháng = 30 ngày
                        if (durationCalculatedText) {
                            durationCalculatedText.innerHTML = `<i class="fas fa-check-circle me-1 text-success"></i>Thời hạn: ${value} tháng (${days} ngày)`;
                        }
                    } else if (unit === 'years') {
                        days = value * 365; // 1 năm = 365 ngày
                        if (durationCalculatedText) {
                            durationCalculatedText.innerHTML = `<i class="fas fa-check-circle me-1 text-success"></i>Thời hạn: ${value} năm (${days} ngày)`;
                        }
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

                // Listen to dropdown select changes
                if (durationUnitSelect) {
                    durationUnitSelect.addEventListener('change', function() {
                        console.log(`📋 Duration unit changed to: ${this.value}`);
                        calculateDuration();
                    });
                    console.log('✅ Added change listener to duration unit select');
                }

                // Listen to custom duration input changes
                if (customDurationInput) {
                    customDurationInput.addEventListener('input', function(e) {
                        console.log('📝 Duration value INPUT event:', e.target.value);
                        calculateDuration();
                    });

                    customDurationInput.addEventListener('keyup', function(e) {
                        console.log('⌨️ Duration value KEYUP event:', e.target.value);
                        calculateDuration();
                    });

                    customDurationInput.addEventListener('change', function(e) {
                        console.log('🔄 Duration value CHANGE event:', e.target.value);
                        calculateDuration();
                    });

                    console.log('✅ Added multiple listeners to custom duration input');
                } else {
                    console.log('❌ customDurationInput not found, cannot add listeners');
                }

                // Khi thay đổi ngày kích hoạt, tính lại ngày hết hạn
                if (activatedAtInput) {
                    activatedAtInput.addEventListener('change', function(e) {
                        console.log('Activated date changed to:', e.target.value);
                        updateExpiresDate();
                    });
                    console.log('✅ Added change listener to activated date input');
                }

                // Initial calculation only if there are values (from validation errors)
                if (customDurationInput.value) {
                    console.log('Running initial calculation with existing value...');
                    calculateDuration();
                } else {
                    console.log('No initial value, waiting for user input...');
                }

                // Test indicator để confirm JS đang hoạt động
                if (customDurationInput) {
                    console.log('🎯 Duration calculator fully initialized!');

                    // Thêm visual indicator
                    const indicator = document.createElement('div');
                    indicator.className = 'alert alert-success mt-2';
                    indicator.innerHTML = '<i class="fas fa-check-circle"></i> Tính năng tự động tính ngày đã sẵn sàng!';
                    indicator.style.fontSize = '12px';
                    indicator.style.padding = '5px 10px';

                    // Thêm indicator sau duration input group
                    customDurationInput.parentElement.insertAdjacentElement('afterend', indicator);

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

</script>
@endpush
@endsection