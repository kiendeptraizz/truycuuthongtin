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
                                                    Family Account <span class="text-muted small">(không bắt buộc)</span>
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

                        {{-- Bảo hành (số ngày) — đồng bộ với bot bước 6 --}}
                        <div class="col-md-6 mb-3">
                            <label for="warranty_days" class="form-label">
                                <i class="fas fa-shield-alt me-1 text-info"></i>
                                Bảo hành (số ngày)
                            </label>
                            <input type="number"
                                class="form-control @error('warranty_days') is-invalid @enderror"
                                id="warranty_days"
                                name="warranty_days"
                                min="0"
                                placeholder="Vd: 30"
                                value="{{ old('warranty_days') }}">
                            @error('warranty_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Để trống nếu không bảo hành. Nhập bằng thời hạn = full thời hạn.</div>
                        </div>

                        {{-- Số tiền đơn hàng — đồng bộ với amount của PendingOrder --}}
                        <div class="col-md-6 mb-3">
                            <label for="order_amount" class="form-label">
                                <i class="fas fa-money-bill me-1 text-success"></i>
                                Số tiền đơn hàng
                            </label>
                            <div class="input-group">
                                <input type="text"
                                    class="form-control currency-input @error('order_amount') is-invalid @enderror"
                                    id="order_amount"
                                    name="order_amount"
                                    placeholder="Vd: 100.000"
                                    value="{{ old('order_amount') }}">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                            @error('order_amount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Số tiền khách trả cho đơn này.</div>
                        </div>

                        {{-- Mã nhóm-gia đình text free — đồng bộ với bot bước 5 --}}
                        <div class="col-md-12 mb-3">
                            <label for="family_code" class="form-label">
                                <i class="fas fa-users me-1 text-primary"></i>
                                Mã nhóm-gia đình (text)
                            </label>
                            <input type="text"
                                class="form-control @error('family_code') is-invalid @enderror"
                                id="family_code"
                                name="family_code"
                                maxlength="100"
                                placeholder="Vd: 2 / gd_email@gmail.com / 'gia đình A'"
                                value="{{ old('family_code') }}">
                            @error('family_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Mã ngắn để admin nhớ — KHÁC với "Family Account" ở trên (là link tới module Family Accounts).</div>
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
                                                    maxlength="20"
                                                    data-currency="VND"
                                                    data-show-currency="false">
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
    const hasFamilyMembership = @json($hasFamilyMembership ?? false);

    function formatNumberInput(number) {
        const cleanNumber = String(number).replace(/\D/g, '');
        if (!cleanNumber) return '';
        return cleanNumber.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // =====================================================
    // GLOBAL FUNCTIONS
    // =====================================================

    window.handleAccountTypeLogic = function() {
        const serviceSelect = document.getElementById('service_package_id');
        const familyWarning = document.getElementById('family-warning');
        const familySelection = document.getElementById('family-selection');
        const noFamilyWarning = document.getElementById('no-family-for-package');
        const sharedCredentialSelection = document.getElementById('shared-credential-selection');
        const noCredentialWarning = document.getElementById('no-credential-for-package');
        const submitBtn = document.querySelector('button[type="submit"]');

        if (!serviceSelect || !serviceSelect.value) {
            if (familyWarning) familyWarning.style.display = 'none';
            if (familySelection) familySelection.style.display = 'none';
            if (sharedCredentialSelection) sharedCredentialSelection.style.display = 'none';
            if (noFamilyWarning) noFamilyWarning.style.display = 'none';
            if (noCredentialWarning) noCredentialWarning.style.display = 'none';
            return;
        }

        const selectedCard = document.querySelector(`[data-package-id="${serviceSelect.value}"]`);
        if (!selectedCard) return;

        const accountType = selectedCard.getAttribute('data-account-type') || '';
        const selectedPackageId = serviceSelect.value;

        if (familySelection) familySelection.style.display = 'none';
        if (sharedCredentialSelection) sharedCredentialSelection.style.display = 'none';
        if (familyWarning) familyWarning.style.display = 'none';
        if (noFamilyWarning) noFamilyWarning.style.display = 'none';
        if (noCredentialWarning) noCredentialWarning.style.display = 'none';

        const accountTypeLower = accountType.toLowerCase();

        if (accountTypeLower.includes('add family') || accountTypeLower.includes('family')) {
            if (familySelection) {
                familySelection.style.display = 'block';
                window.filterFamilyAccountsByPackage && window.filterFamilyAccountsByPackage(selectedPackageId);
            }
        } else if (accountTypeLower.includes('dùng chung') || accountTypeLower.includes('shared')) {
            if (sharedCredentialSelection) {
                sharedCredentialSelection.style.display = 'block';
                window.filterSharedCredentialsByPackage && window.filterSharedCredentialsByPackage(selectedPackageId);
            }
        }

        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Gán dịch vụ';
            submitBtn.classList.remove('btn-danger');
            submitBtn.classList.add('btn-primary');
        }
    };

    window.handleFamilyAccountLogic = window.handleAccountTypeLogic;

    window.filterSharedCredentialsByPackage = function(packageId) {
        const sharedSelect = document.getElementById('shared_credential_id');
        const noCredentialWarning = document.getElementById('no-credential-for-package');
        if (!sharedSelect) return;

        const options = sharedSelect.querySelectorAll('option');
        let hasVisibleOptions = false;
        sharedSelect.value = '';

        options.forEach(option => {
            if (option.value === '') { option.style.display = ''; return; }
            const optionPackageId = option.getAttribute('data-service-package-id');
            if (optionPackageId === packageId) {
                option.style.display = '';
                hasVisibleOptions = true;
            } else {
                option.style.display = 'none';
            }
        });

        if (noCredentialWarning) {
            noCredentialWarning.style.display = hasVisibleOptions ? 'none' : 'block';
        }
    };

    window.filterFamilyAccountsByPackage = function(packageId) {
        const familySelect = document.getElementById('family_account_id');
        const noFamilyWarning = document.getElementById('no-family-for-package');
        if (!familySelect) return;

        const options = familySelect.querySelectorAll('option');
        let hasVisibleOptions = false;
        familySelect.value = '';

        options.forEach(option => {
            if (!option.value) { option.style.display = ''; return; }
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
        const serviceSelect = document.getElementById('service_package_id');
        const supplierSelect = document.getElementById('supplier_id');
        const supplierDetails = document.getElementById('supplier-details');
        const supplierCodeDisplay = document.getElementById('supplier-code-display');
        const supplierNameDisplay = document.getElementById('supplier-name-display');
        const supplierProductsList = document.getElementById('supplier-products-list');
        const serviceSelection = document.getElementById('service-selection');
        const supplierServiceSelect = document.getElementById('supplier_service_id');

        // =====================================================
        // SERVICE PACKAGE SELECTION
        // =====================================================
        if (serviceSelect) {
            serviceSelect.addEventListener('change', function() {
                window.handleAccountTypeLogic();
            });
            serviceSelect.addEventListener('input', function() {
                window.handleAccountTypeLogic();
            });
        }

        document.querySelectorAll('.package-card').forEach(card => {
            card.addEventListener('click', function() {
                setTimeout(function() { window.handleAccountTypeLogic(); }, 100);
            });
        });

        if (serviceSelect) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                        window.handleAccountTypeLogic();
                    }
                });
            });
            observer.observe(serviceSelect, { attributes: true });
        }

        setTimeout(function() {
            if (serviceSelect && serviceSelect.value) window.handleAccountTypeLogic();
        }, 500);

        setTimeout(function() {
            if (serviceSelect && serviceSelect.value) window.handleAccountTypeLogic();
        }, 1000);

        // =====================================================
        // SUPPLIER SELECTION
        // =====================================================
        if (supplierSelect) {
            supplierSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];

                if (selectedOption.value) {
                    if (supplierDetails) supplierDetails.style.display = 'block';
                    if (supplierCodeDisplay) supplierCodeDisplay.textContent = selectedOption.dataset.supplierCode;
                    if (supplierNameDisplay) supplierNameDisplay.textContent = selectedOption.dataset.supplierName;

                    const products = selectedOption.dataset.products;
                    if (supplierProductsList) {
                        supplierProductsList.innerHTML = '';
                        if (products) {
                            products.split('|').forEach(function(product) {
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
                    }

                    const services = selectedOption.dataset.services;
                    if (supplierServiceSelect) {
                        supplierServiceSelect.innerHTML = '<option value="">Chọn dịch vụ từ nhà cung cấp</option>';
                    }

                    if (services && serviceSelection) {
                        serviceSelection.style.display = 'block';
                        services.split('|').forEach(function(service) {
                            if (service.trim()) {
                                const parts = service.split(':');
                                if (parts.length === 3 && supplierServiceSelect) {
                                    const option = document.createElement('option');
                                    option.value = parts[0];
                                    option.textContent = parts[1] + ' - ' + parseInt(parts[2]).toLocaleString('vi-VN') + ' VND';
                                    supplierServiceSelect.appendChild(option);
                                }
                            }
                        });
                    } else if (serviceSelection) {
                        serviceSelection.style.display = 'none';
                    }
                } else {
                    if (supplierDetails) supplierDetails.style.display = 'none';
                    if (serviceSelection) serviceSelection.style.display = 'none';
                    if (supplierServiceSelect) supplierServiceSelect.innerHTML = '<option value="">Chọn dịch vụ từ nhà cung cấp</option>';
                }
            });

            if (supplierSelect.value) {
                supplierSelect.dispatchEvent(new Event('change'));
                const selectedServiceId = '{{ old("supplier_service_id") }}';
                if (selectedServiceId && supplierServiceSelect) {
                    setTimeout(function() { supplierServiceSelect.value = selectedServiceId; }, 100);
                }
            }
        }

        // =====================================================
        // FAMILY ACCOUNT SELECTION
        // =====================================================
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
                    if (familyCodeDisplay) familyCodeDisplay.textContent = selectedOption.dataset.familyCode;
                    if (familyEmailDisplay) familyEmailDisplay.textContent = selectedOption.dataset.primaryEmail;
                    if (familyServiceDisplay) familyServiceDisplay.textContent = selectedOption.dataset.serviceName;
                    if (familySlotsDisplay) familySlotsDisplay.textContent = selectedOption.dataset.usedSlots + '/' + selectedOption.dataset.maxSlots + ' (Còn: ' + selectedOption.dataset.availableSlots + ' slots)';
                    if (familyDetails) familyDetails.style.display = 'block';
                } else {
                    if (familyDetails) familyDetails.style.display = 'none';
                }
            });

            if (familyAccountSelect.value) {
                familyAccountSelect.dispatchEvent(new Event('change'));
            }
        }

        // =====================================================
        // SHARED CREDENTIAL SELECTION
        // =====================================================
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
                    const email = selectedOption.dataset.email;
                    const password = selectedOption.dataset.password;

                    if (credEmailDisplay) credEmailDisplay.textContent = email;
                    if (credSlotsDisplay) credSlotsDisplay.textContent = selectedOption.dataset.currentUsers + '/' + selectedOption.dataset.maxUsers + ' (Còn: ' + selectedOption.dataset.availableSlots + ' slots)';
                    if (loginEmailInput && email) loginEmailInput.value = email;
                    if (loginPasswordInput && password) loginPasswordInput.value = password;
                    if (sharedCredentialDetails) sharedCredentialDetails.style.display = 'block';
                } else {
                    if (sharedCredentialDetails) sharedCredentialDetails.style.display = 'none';
                    if (loginEmailInput) loginEmailInput.value = '';
                    if (loginPasswordInput) loginPasswordInput.value = '';
                }
            });

            if (sharedCredentialSelect.value) {
                sharedCredentialSelect.dispatchEvent(new Event('change'));
            }
        }

        // =====================================================
        // FORMAT PROFIT AMOUNT INPUT
        // =====================================================
        const profitAmountInput = document.getElementById('profit_amount');
        if (profitAmountInput) {
            if (profitAmountInput.value) {
                profitAmountInput.value = formatNumberInput(profitAmountInput.value);
            }

            profitAmountInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^\d]/g, '');
                e.target.value = value ? formatNumberInput(value) : '';
            });

            profitAmountInput.addEventListener('keydown', function(e) {
                if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
                    (e.keyCode === 65 && e.ctrlKey === true) ||
                    (e.keyCode === 67 && e.ctrlKey === true) ||
                    (e.keyCode === 86 && e.ctrlKey === true) ||
                    (e.keyCode === 88 && e.ctrlKey === true) ||
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                    return;
                }
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });
        }

        // =====================================================
        // DURATION CALCULATOR
        // =====================================================
        (function initializeDurationCalculator() {
            const durationUnitSelect = document.getElementById('duration_unit');
            const customDurationInput = document.getElementById('custom_duration');
            const durationDaysHidden = document.getElementById('duration_days');
            const durationCalculatedText = document.getElementById('duration_calculated_text');
            const activatedAtInput = document.getElementById('activated_at');
            const expiresAtInput = document.getElementById('expires_at');

            if (!durationUnitSelect || !customDurationInput || !durationDaysHidden) return;

            function calculateDuration() {
                const unit = durationUnitSelect.value;
                const value = parseInt(customDurationInput.value) || 0;
                let days = 0;

                if (value === 0) {
                    if (durationCalculatedText) {
                        durationCalculatedText.innerHTML = '<i class="fas fa-info-circle me-1"></i>Nhập thời hạn để tự động tính ngày hết hạn';
                    }
                    durationDaysHidden.value = '';
                    return;
                }

                if (unit === 'days') {
                    days = value;
                    if (durationCalculatedText) {
                        durationCalculatedText.innerHTML = `<i class="fas fa-check-circle me-1 text-success"></i>Thời hạn: ${value} ngày`;
                    }
                } else if (unit === 'months') {
                    days = value * 30;
                    if (durationCalculatedText) {
                        durationCalculatedText.innerHTML = `<i class="fas fa-check-circle me-1 text-success"></i>Thời hạn: ${value} tháng (${days} ngày)`;
                    }
                } else if (unit === 'years') {
                    days = value * 365;
                    if (durationCalculatedText) {
                        durationCalculatedText.innerHTML = `<i class="fas fa-check-circle me-1 text-success"></i>Thời hạn: ${value} năm (${days} ngày)`;
                    }
                }

                durationDaysHidden.value = days;
                updateExpiresDate();
            }

            function updateExpiresDate() {
                if (!activatedAtInput || !activatedAtInput.value || !durationDaysHidden.value) return;

                const activatedDate = new Date(activatedAtInput.value + 'T00:00:00');
                const days = parseInt(durationDaysHidden.value) || 0;

                if (days > 0 && expiresAtInput) {
                    const expiresDate = new Date(activatedDate);
                    expiresDate.setDate(expiresDate.getDate() + days);

                    const year = expiresDate.getFullYear();
                    const month = String(expiresDate.getMonth() + 1).padStart(2, '0');
                    const day = String(expiresDate.getDate()).padStart(2, '0');

                    expiresAtInput.value = `${year}-${month}-${day}`;
                    expiresAtInput.dispatchEvent(new Event('change'));
                }
            }

            durationUnitSelect.addEventListener('change', calculateDuration);
            customDurationInput.addEventListener('input', calculateDuration);
            customDurationInput.addEventListener('change', calculateDuration);

            if (activatedAtInput) {
                activatedAtInput.addEventListener('change', updateExpiresDate);
            }

            if (customDurationInput.value) {
                calculateDuration();
            }
        })();
    });

</script>
@endpush
@endsection