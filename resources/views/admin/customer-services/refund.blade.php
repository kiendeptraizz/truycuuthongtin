@extends('layouts.admin')

@section('title', 'Tính tiền hoàn — ' . ($cs->order_code ?? '#' . $cs->id))
@section('page-title', 'Tính tiền hoàn')

@section('content')
<div class="container-fluid py-3" style="max-width: 880px;">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-undo-alt text-warning me-2"></i>
                Tính tiền hoàn cho đơn
            </h4>
            <div class="text-muted small">
                @if($cs->order_code)<code class="text-success">{{ $cs->order_code }}</code> · @endif
                {{ $cs->servicePackage->name ?? '?' }} ·
                KH: {{ $cs->customer->name ?? '?' }}
            </div>
        </div>
        <a href="{{ route('admin.customer-services.show', $cs) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Quay lại
        </a>
    </div>

    {{-- Trường hợp không thể hoàn --}}
    @if(!$calc['ok'])
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Không thể hoàn tiền cho đơn này:</strong>
            @switch($calc['reason'] ?? '')
                @case('already_refunded')
                    Đơn đã được ghi nhận hoàn tiền trước đó
                    @if($cs->refunded_at)
                        ({{ number_format((int) $cs->refund_amount, 0, ',', '.') }}đ ·
                        {{ $cs->refunded_at->format('H:i d/m/Y') }}).
                    @endif
                    @break
                @case('already_cancelled')
                    Đơn đã ở trạng thái "Đã huỷ" — không có gì để hoàn.
                    @break
                @case('no_order_amount')
                    Đơn chưa có số tiền đơn (<code>order_amount</code>). Hãy điền vào trang chỉnh sửa trước.
                    @break
                @case('no_expires_at')
                    Đơn không có ngày hết hạn — không thể tính thời gian còn lại.
                    @break
                @default
                    {{ $calc['reason'] ?? 'Lỗi không xác định' }}.
            @endswitch
        </div>
    @else
        {{-- Trường hợp hoàn được --}}
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Bảng tính</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Số tiền đơn gốc</div>
                        <div class="h5 mb-0 text-success">
                            {{ number_format((int) ($calc['order_amount'] ?? 0), 0, ',', '.') }}đ
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Trạng thái hoàn</div>
                        <div>
                            @switch($calc['mode'])
                                @case('full')
                                    <span class="badge bg-success">Hoàn toàn bộ</span>
                                    @break
                                @case('partial')
                                    <span class="badge bg-warning text-dark">Hoàn 1 phần</span>
                                    @break
                                @case('expired')
                                    <span class="badge bg-secondary">Đã hết hạn</span>
                                    @break
                            @endswitch
                        </div>
                    </div>

                    @if($calc['mode'] !== 'full')
                        <div class="col-md-4">
                            <div class="text-muted small">Tổng thời hạn</div>
                            <div><strong>{{ $calc['total_days'] ?? 0 }}</strong> ngày</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Đã dùng</div>
                            <div><strong>{{ $calc['days_used'] ?? 0 }}</strong> ngày</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Còn lại</div>
                            <div>
                                <strong class="{{ ($calc['days_remaining'] ?? 0) > 0 ? 'text-primary' : 'text-muted' }}">
                                    {{ $calc['days_remaining'] ?? 0 }}
                                </strong> ngày
                                ({{ $calc['percent_remaining'] ?? 0 }}%)
                            </div>
                        </div>

                        {{-- Progress bar phần trăm còn lại --}}
                        <div class="col-12">
                            <div class="progress" style="height: 16px;">
                                <div class="progress-bar bg-secondary"
                                    style="width: {{ 100 - ($calc['percent_remaining'] ?? 0) }}%;"
                                    title="Đã dùng {{ 100 - ($calc['percent_remaining'] ?? 0) }}%">
                                    Đã dùng
                                </div>
                                <div class="progress-bar bg-primary"
                                    style="width: {{ $calc['percent_remaining'] ?? 0 }}%;"
                                    title="Còn {{ $calc['percent_remaining'] ?? 0 }}%">
                                    Còn lại
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="col-12">
                        <hr>
                        <div class="text-muted small">{{ $calc['reason_label'] ?? '' }}</div>
                    </div>

                    <div class="col-12">
                        <div class="alert alert-info mb-0 d-flex align-items-center">
                            <i class="fas fa-coins fa-2x me-3"></i>
                            <div>
                                <div class="small text-muted">Số tiền hoàn đề xuất</div>
                                <div class="h3 mb-0 text-primary">
                                    {{ number_format((int) ($calc['refund_amount'] ?? 0), 0, ',', '.') }}đ
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form xác nhận --}}
        <div class="card shadow-sm border-warning">
            <div class="card-header bg-warning bg-opacity-25">
                <h6 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Xác nhận hoàn tiền (admin trả thủ công ngoài hệ thống)
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.customer-services.refund.confirm', $cs) }}">
                    @csrf

                    <div class="mb-3">
                        <label for="refund_amount" class="form-label">
                            <i class="fas fa-money-bill-wave me-1 text-success"></i>
                            Số tiền sẽ hoàn (admin có thể chỉnh)
                        </label>
                        <div class="input-group">
                            <input type="number"
                                class="form-control form-control-lg @error('refund_amount') is-invalid @enderror"
                                id="refund_amount"
                                name="refund_amount"
                                min="0"
                                max="{{ (int) ($calc['order_amount'] ?? 0) }}"
                                value="{{ old('refund_amount', $calc['refund_amount'] ?? 0) }}"
                                required>
                            <span class="input-group-text">VNĐ</span>
                        </div>
                        @error('refund_amount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Mặc định = số tiền đề xuất (theo % thời gian còn lại). Có thể giảm/tăng nhưng không vượt {{ number_format((int) ($calc['order_amount'] ?? 0), 0, ',', '.') }}đ.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="refund_reason" class="form-label">
                            <i class="fas fa-comment-dots me-1 text-info"></i>
                            Lý do hoàn tiền <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('refund_reason') is-invalid @enderror"
                            id="refund_reason"
                            name="refund_reason"
                            rows="3"
                            placeholder="VD: TK lỗi không đăng nhập được, đối tác hết hàng, khách yêu cầu huỷ, ..."
                            required>{{ old('refund_reason') }}</textarea>
                        @error('refund_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-warning small">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Bấm xác nhận sẽ:
                        <ul class="mb-0">
                            <li>Lưu số tiền đã hoàn + thời điểm + lý do vào DB.</li>
                            <li>Đơn chuyển sang trạng thái <strong>"Đã huỷ"</strong>.</li>
                            <li><strong>KHÔNG</strong> tự chuyển khoản — bạn phải trả thủ công cho khách.</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.customer-services.show', $cs) }}" class="btn btn-outline-secondary">
                            Huỷ thao tác
                        </a>
                        <button type="submit" class="btn btn-warning"
                            onclick="return confirm('Xác nhận hoàn ' + document.getElementById('refund_amount').value.toLocaleString('vi-VN') + 'đ và huỷ đơn?\n\nNhớ chuyển khoản thủ công cho khách sau khi bấm OK.')">
                            <i class="fas fa-check me-1"></i>Xác nhận hoàn tiền + huỷ đơn
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Show refund history nếu đã refunded --}}
    @if($cs->refunded_at)
        <div class="card mt-3 border-secondary">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i>Đã hoàn tiền</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3">Số tiền hoàn:</dt>
                    <dd class="col-sm-9">
                        <strong class="text-warning">{{ number_format((int) $cs->refund_amount, 0, ',', '.') }}đ</strong>
                    </dd>

                    <dt class="col-sm-3">Thời điểm:</dt>
                    <dd class="col-sm-9">{{ $cs->refunded_at->format('H:i:s d/m/Y') }}</dd>

                    <dt class="col-sm-3">Lý do:</dt>
                    <dd class="col-sm-9">{{ $cs->refund_reason ?: '—' }}</dd>
                </dl>
            </div>
        </div>
    @endif
</div>
@endsection
