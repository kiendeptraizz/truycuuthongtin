@extends('layouts.admin')

@section('title', 'Fill đơn ' . $pendingOrder->order_code)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-edit text-success me-2"></i>Fill thông tin đơn</h2>
            <p class="text-muted mb-0">Hoàn thiện đơn pending → tạo dịch vụ khách hàng.</p>
        </div>
        <a href="{{ route('admin.pending-orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Quay lại
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
        </div>
    @endif

    <div class="row g-3">
        {{-- Đổi col-lg → col-xl để stack vertical khi <1200px (thấy đủ form trên màn hình hẹp) --}}
        <div class="col-xl-4">
            <div class="card sticky-top" style="top: 20px">
                <div class="card-body">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-12 col-sm-4">
                            <h6 class="text-muted small mb-1">Mã đơn</h6>
                            <h5 class="mb-0" style="font-family: 'Courier New', monospace">{{ $pendingOrder->order_code }}</h5>
                        </div>
                        <div class="col-md-12 col-sm-4">
                            <h6 class="text-muted small mb-1">Số tiền</h6>
                            <h4 class="mb-0 text-success" title="{{ number_format($pendingOrder->amount, 0, ',', '.') }}đ">
                                {{ formatShortAmount($pendingOrder->amount) }}
                            </h4>
                            <small class="text-muted">({{ number_format($pendingOrder->amount, 0, ',', '.') }}đ)</small>
                        </div>
                        <div class="col-md-12 col-sm-4">
                            <h6 class="text-muted small mb-1">Tạo lúc</h6>
                            <p class="mb-0 small">{{ $pendingOrder->created_at->format('H:i d/m/Y') }}</p>
                        </div>
                    </div>

                    @if($pendingOrder->note)
                        <hr class="my-3">
                        <h6 class="text-muted small mb-1">Ghi chú</h6>
                        <p class="mb-0 small">{{ $pendingOrder->note }}</p>
                    @endif

                    <hr class="my-3">
                    <h6 class="text-muted small mb-2 d-flex justify-content-between align-items-center">
                        <span>QR thanh toán</span>
                        <button type="button" class="btn btn-sm btn-link p-0" data-bs-toggle="collapse" data-bs-target="#qrCollapse">
                            <i class="fas fa-eye"></i> ẩn/hiện
                        </button>
                    </h6>
                    <div class="collapse show" id="qrCollapse">
                        <img src="{{ $pendingOrder->qrCodeUrl() }}" class="img-fluid border rounded" alt="QR" style="max-width: 220px">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <form method="POST" action="{{ route('admin.pending-orders.fill', $pendingOrder) }}">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Thông tin dịch vụ</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @php
                                // Pre-fill từ data bot đã gửi (nếu có). Bot lưu vào PendingOrder
                                // các field: customer_id, service_package_id, account_email,
                                // family_code, duration_days, warranty_days, profit_amount.
                                $preCustomerId = old('customer_id', $pendingOrder->customer_id);
                                $prePackageId = old('service_package_id', $pendingOrder->service_package_id);
                                $preEmail = old('login_email', $pendingOrder->account_email);
                                $preDurationDays = (int) old('duration_days', $pendingOrder->duration_days ?? 30);
                                // Lợi nhuận: KHÔNG fallback sang amount nữa — để trống nếu bot chưa nhập
                                $preProfitAmount = old('profit_amount', $pendingOrder->profit_amount);
                                // Số tiền đơn: pre-fill từ pending_order.amount, sửa được
                                $preOrderAmount = old('order_amount', $pendingOrder->amount);
                                $preFamilyCode = old('family_code', $pendingOrder->family_code);
                                $preWarrantyDays = old('warranty_days', $pendingOrder->warranty_days);
                                // Ngày kích hoạt mặc định = ngày tạo đơn (không phải hôm nay)
                                $preActivatedAt = old('activated_at', $pendingOrder->created_at->format('Y-m-d'));
                                $preExpiresAt = old('expires_at', $pendingOrder->created_at->copy()->addDays($preDurationDays)->format('Y-m-d'));

                                // Infer unit cho dropdown duration: 30, 60, 90, ... → months; 365, 730 → years; else days
                                if ($preDurationDays > 0 && $preDurationDays % 365 === 0) {
                                    $preDurationUnit = 'years';
                                    $preDurationValue = (int) ($preDurationDays / 365);
                                } elseif ($preDurationDays > 0 && $preDurationDays % 30 === 0) {
                                    $preDurationUnit = 'months';
                                    $preDurationValue = (int) ($preDurationDays / 30);
                                } else {
                                    $preDurationUnit = 'days';
                                    $preDurationValue = $preDurationDays;
                                }
                            @endphp
                            <div class="col-md-6">
                                <x-customer-search-selector
                                    name="customer_id"
                                    :customers="$customers"
                                    :value="$preCustomerId"
                                    :required="true"
                                    label="Khách hàng"
                                    placeholder="Gõ tên / mã KH / SĐT để tìm..."
                                    :help-text="$pendingOrder->customer_id && $pendingOrder->customer
                                        ? 'Đã pre-fill từ bot: ' . $pendingOrder->customer->customer_code . ' — ' . $pendingOrder->customer->name
                                        : 'Gõ để tìm khách (hỗ trợ tên/mã KUN/CTV/SĐT/email). Hoặc bấm + để thêm KH mới chỉ với tên.'"
                                />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-box me-1 text-primary"></i>
                                    Gói dịch vụ <span class="text-danger">*</span>
                                </label>
                                <x-service-package-selector
                                    name="service_package_id"
                                    :service-packages="$servicePackages"
                                    :selected="$prePackageId"
                                    :required="true"
                                    placeholder="Gõ tên gói (vd: Claude, ChatGPT, Canva...)..."
                                />
                                @if($pendingOrder->service_package_id && $pendingOrder->servicePackage)
                                    <small class="text-success d-block mt-1"><i class="fas fa-check-circle me-1"></i>Đã pre-fill: {{ $pendingOrder->servicePackage->name }}</small>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email đăng nhập *</label>
                                <input type="email" name="login_email" class="form-control" required value="{{ $preEmail }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mật khẩu (tuỳ chọn)</label>
                                <input type="text" name="login_password" class="form-control" value="{{ old('login_password') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ngày kích hoạt *</label>
                                <input type="date" name="activated_at" id="activated_at" class="form-control" required
                                       value="{{ $preActivatedAt }}">
                                <small class="text-muted">Mặc định = ngày tạo đơn ({{ $pendingOrder->created_at->format('d/m/Y') }}).</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ngày hết hạn *</label>
                                <input type="date" name="expires_at" id="expires_at" class="form-control" required
                                       value="{{ $preExpiresAt }}">
                                <small class="text-muted">Tự tính từ "Thời hạn" bên dưới.</small>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">
                                    <i class="fas fa-clock me-1"></i>
                                    Thời hạn *
                                </label>
                                <div class="input-group">
                                    <input type="number" id="custom_duration" class="form-control"
                                           min="1" placeholder="Nhập số" value="{{ $preDurationValue }}">
                                    <select id="duration_unit" class="form-select" style="max-width: 120px;">
                                        <option value="days" {{ $preDurationUnit === 'days' ? 'selected' : '' }}>Ngày</option>
                                        <option value="months" {{ $preDurationUnit === 'months' ? 'selected' : '' }}>Tháng</option>
                                        <option value="years" {{ $preDurationUnit === 'years' ? 'selected' : '' }}>Năm</option>
                                    </select>
                                </div>
                                <input type="hidden" name="duration_days" id="duration_days" value="{{ $preDurationDays }}">
                                <small id="duration_calculated_text" class="form-text text-info">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Nhập thời hạn để tự động tính ngày hết hạn
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mã nhóm - gia đình</label>
                                <input type="text" name="family_code" class="form-control" value="{{ $preFamilyCode }}" placeholder="Vd: gd_abc@gmail.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bảo hành (số ngày)</label>
                                <input type="number" name="warranty_days" class="form-control" value="{{ $preWarrantyDays }}" min="0" placeholder="Để trống = không bảo hành">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-money-bill me-1 text-success"></i>
                                    Số tiền đơn hàng (đ)
                                </label>
                                <input type="number" name="order_amount" class="form-control"
                                       value="{{ $preOrderAmount }}" min="0" step="1000" placeholder="Vd: 395000">
                                <small class="text-muted">
                                    Mặc định = số tiền pending order ({{ formatShortAmount($pendingOrder->amount) }}). Sửa nếu khách trả khác.
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-coins me-1 text-warning"></i>
                                    Lợi nhuận (đ)
                                </label>
                                <input type="number" name="profit_amount" class="form-control"
                                       value="{{ $preProfitAmount }}" min="0" step="1000" placeholder="Vd: 100000">
                                <small class="text-muted">
                                    @if($pendingOrder->profit_amount)
                                        <i class="fas fa-check-circle text-success me-1"></i>Đã pre-fill từ bot: {{ formatShortAmount($pendingOrder->profit_amount) }}
                                    @else
                                        Nhập lãi thực, hoặc để trống nếu chưa biết.
                                    @endif
                                </small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Ghi chú lợi nhuận</label>
                                <input type="text" name="profit_notes" class="form-control" value="{{ old('profit_notes') }}" maxlength="1000">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Ghi chú nội bộ</label>
                                <textarea name="internal_notes" class="form-control" rows="2">{{ old('internal_notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('admin.pending-orders.index') }}" class="btn btn-outline-secondary">Huỷ</a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-1"></i>Hoàn tất tạo dịch vụ
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    const $custom = $('#custom_duration');
    const $unit = $('#duration_unit');
    const $durationDays = $('#duration_days');
    const $text = $('#duration_calculated_text');
    const $activated = $('#activated_at');
    const $expires = $('#expires_at');

    function calcDays() {
        const value = parseInt($custom.val()) || 0;
        const unit = $unit.val();
        let days = 0;
        let label = '';

        if (value <= 0) {
            $durationDays.val('');
            $text.html('<i class="fas fa-info-circle me-1"></i>Nhập thời hạn để tự động tính ngày hết hạn');
            return;
        }

        if (unit === 'days') {
            days = value;
            label = `${value} ngày`;
        } else if (unit === 'months') {
            days = value * 30;
            label = `${value} tháng (${days} ngày)`;
        } else if (unit === 'years') {
            days = value * 365;
            label = `${value} năm (${days} ngày)`;
        }

        $durationDays.val(days);
        $text.html(`<i class="fas fa-check-circle me-1 text-success"></i>Thời hạn: ${label}`);
        updateExpires();
    }

    function updateExpires() {
        const start = $activated.val();
        const days = parseInt($durationDays.val()) || 0;
        if (start && days > 0) {
            const d = new Date(start + 'T00:00:00');
            d.setDate(d.getDate() + days);
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            $expires.val(`${y}-${m}-${dd}`);
        }
    }

    // Dùng 'input' event để fire tức thì khi gõ phím / bấm arrow spinner
    $custom.on('input change', calcDays);
    $unit.on('change', calcDays);
    $activated.on('input change', updateExpires);

    // Init label nếu đã có giá trị pre-fill
    if (parseInt($custom.val()) > 0) {
        calcDays();
    }
});
</script>
@endpush
