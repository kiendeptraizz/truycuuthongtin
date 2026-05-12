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
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px">
                <div class="card-body">
                    <h6 class="text-muted small">Mã đơn</h6>
                    <h4 class="mb-3" style="font-family: 'Courier New', monospace">{{ $pendingOrder->order_code }}</h4>

                    <h6 class="text-muted small">Số tiền</h6>
                    <h3 class="mb-3 text-success" title="{{ number_format($pendingOrder->amount, 0, ',', '.') }}đ">
                        {{ formatShortAmount($pendingOrder->amount) }}
                        <small class="text-muted fs-6">({{ number_format($pendingOrder->amount, 0, ',', '.') }}đ)</small>
                    </h3>

                    @if($pendingOrder->note)
                        <h6 class="text-muted small">Ghi chú</h6>
                        <p class="mb-3">{{ $pendingOrder->note }}</p>
                    @endif

                    <h6 class="text-muted small">Tạo lúc</h6>
                    <p class="mb-3">{{ $pendingOrder->created_at->format('H:i d/m/Y') }}</p>

                    <h6 class="text-muted small mb-2">QR thanh toán</h6>
                    <img src="{{ $pendingOrder->qrCodeUrl() }}" class="img-fluid border rounded" alt="QR">
                </div>
            </div>
        </div>

        <div class="col-lg-8">
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
                                $preDurationDays = old('duration_days', $pendingOrder->duration_days ?? 30);
                                $preProfitAmount = old('profit_amount', $pendingOrder->profit_amount ?? $pendingOrder->amount);
                                $preFamilyCode = old('family_code', $pendingOrder->family_code);
                                $preWarrantyDays = old('warranty_days', $pendingOrder->warranty_days);
                                $preActivatedAt = old('activated_at', now()->format('Y-m-d'));
                                $preExpiresAt = old('expires_at', now()->addDays((int) $preDurationDays)->format('Y-m-d'));
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
                                <label class="form-label">Gói dịch vụ *</label>
                                <select name="service_package_id" class="form-select" required>
                                    <option value="">— Chọn gói —</option>
                                    @foreach($servicePackages as $p)
                                        <option value="{{ $p->id }}" {{ $prePackageId == $p->id ? 'selected' : '' }}>
                                            {{ $p->name }} ({{ $p->category?->name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @if($pendingOrder->service_package_id && $pendingOrder->servicePackage)
                                    <small class="text-success"><i class="fas fa-check-circle me-1"></i>Đã pre-fill: {{ $pendingOrder->servicePackage->name }}</small>
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
                            <div class="col-md-4">
                                <label class="form-label">Ngày kích hoạt *</label>
                                <input type="date" name="activated_at" class="form-control" required
                                       value="{{ $preActivatedAt }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Ngày hết hạn *</label>
                                <input type="date" name="expires_at" class="form-control" required
                                       value="{{ $preExpiresAt }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Số ngày *</label>
                                <input type="number" name="duration_days" class="form-control" required
                                       value="{{ $preDurationDays }}" min="1">
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
                                <label class="form-label">Lợi nhuận (đ)</label>
                                <input type="number" name="profit_amount" class="form-control"
                                       value="{{ $preProfitAmount }}" min="0" step="1000">
                                <small class="text-muted">
                                    @if($pendingOrder->profit_amount)
                                        <i class="fas fa-check-circle text-success me-1"></i>Đã pre-fill từ bot: {{ formatShortAmount($pendingOrder->profit_amount) }}
                                    @else
                                        Mặc định = số tiền đơn ({{ formatShortAmount($pendingOrder->amount) }}). Sửa nếu khác.
                                    @endif
                                </small>
                            </div>
                            <div class="col-md-6">
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
    // Auto-calc expires_at khi đổi activated_at hoặc duration_days
    function recalc() {
        const start = $('input[name=activated_at]').val();
        const days = parseInt($('input[name=duration_days]').val());
        if (start && days > 0) {
            const d = new Date(start);
            d.setDate(d.getDate() + days);
            $('input[name=expires_at]').val(d.toISOString().slice(0, 10));
        }
    }
    $('input[name=activated_at], input[name=duration_days]').on('change', recalc);
});
</script>
@endpush
