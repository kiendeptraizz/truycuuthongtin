@extends('layouts.admin')

@section('title', 'Pending Orders - Đơn chờ fill')

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }
    .order-code {
        font-family: 'Courier New', monospace;
        font-weight: 700;
        color: #1a1a2e;
    }
    .amount-badge {
        font-weight: 700;
        color: #16a34a;
    }
    .qr-thumb {
        width: 48px;
        height: 48px;
        cursor: pointer;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 4px;
        background: white;
    }
    .qr-thumb:hover { border-color: #667eea; transform: scale(1.05); }
    #qrModalImg { max-width: 100%; height: auto; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-receipt text-primary me-2"></i>Pending Orders</h2>
            <p class="text-muted mb-0">Đơn được tạo nhanh — fill thông tin để biến thành dịch vụ khách hàng.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newOrderModal">
            <i class="fas fa-plus me-1"></i>Tạo nhanh
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card border-start border-4 border-warning">
                <div class="text-muted small">Đang chờ fill</div>
                <div class="h2 mb-0" data-stats-key="pending">{{ number_format($stats['pending']) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card border-start border-4 border-info">
                <div class="text-muted small">Đơn hôm nay</div>
                <div class="h2 mb-0" data-stats-key="today">{{ number_format($stats['today']) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card border-start border-4 border-success">
                <div class="text-muted small">Tổng tiền hôm nay</div>
                <div class="h2 mb-0" title="{{ number_format($stats['today_amount'], 0, ',', '.') }}đ">{{ formatShortAmount($stats['today_amount']) }}</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small">Trạng thái</label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Đang chờ fill</option>
                        <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Đã huỷ</option>
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tất cả</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Ngày tạo</label>
                    <input type="date" name="date" value="{{ request('date') }}" class="form-control form-control-sm" onchange="this.form.submit()">
                </div>
                @if(request('date') || $status !== 'pending')
                    <div class="col-auto">
                        <a href="{{ route('admin.pending-orders.index') }}" class="btn btn-sm btn-outline-secondary">Xoá lọc</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body p-0">
            @if($orders->count() === 0)
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Không có đơn nào</h5>
                    <p class="text-muted">Tạo đơn nhanh từ bot Telegram hoặc click "Tạo nhanh" ở trên.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã đơn</th>
                                <th>Số tiền</th>
                                <th>Ghi chú</th>
                                <th>Nguồn</th>
                                <th>Tạo lúc</th>
                                <th>Trạng thái</th>
                                <th>Thanh toán</th>
                                <th class="text-center">QR</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $order)
                            <tr data-order-row="{{ $order->id }}">
                                <td><span class="order-code">{{ $order->order_code }}</span></td>
                                <td>
                                    <span class="amount-badge" title="{{ number_format($order->amount, 0, ',', '.') }}đ">
                                        {{ formatShortAmount($order->amount) }}
                                    </span>
                                </td>
                                <td>
                                    @if($order->note)
                                        <small>{{ $order->note }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->created_via === 'telegram')
                                        <span class="badge bg-info"><i class="fab fa-telegram me-1"></i>Telegram</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="fas fa-globe me-1"></i>Web</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $order->created_at->format('H:i') }}
                                    <br><small class="text-muted">{{ $order->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    @if($order->status === 'pending')
                                        <span class="badge bg-warning text-dark">Chờ fill</span>
                                    @elseif($order->status === 'completed')
                                        <span class="badge bg-success">Đã fill</span>
                                        @if($order->customer_service_id)
                                            <br><small><a href="{{ route('admin.customer-services.show', $order->customer_service_id) }}">→ DV #{{ $order->customer_service_id }}</a></small>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Huỷ</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->paid_at)
                                        <span class="badge bg-success" title="{{ $order->paid_at->format('d/m/Y H:i:s') }}">
                                            <i class="fas fa-check-circle me-1"></i>Đã trả
                                        </span>
                                        @if($order->paid_amount && $order->paid_amount != $order->amount)
                                            <br><small class="text-danger">
                                                {{ formatShortAmount($order->paid_amount) }}
                                                ({{ $order->paid_amount > $order->amount ? '+' : '' }}{{ formatShortAmount($order->paid_amount - $order->amount) }})
                                            </small>
                                        @endif
                                    @else
                                        <span class="badge bg-light text-dark">
                                            <i class="far fa-clock me-1"></i>Chưa trả
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <img src="{{ $order->qrCodeUrl() }}"
                                         alt="QR"
                                         class="qr-thumb"
                                         onclick="showQrModal('{{ $order->qrCodeUrl() }}', '{{ $order->order_code }}', '{{ formatShortAmount($order->amount) }}')"
                                         loading="lazy">
                                </td>
                                <td class="text-end">
                                    @if($order->status === 'pending')
                                        <a href="{{ route('admin.pending-orders.fill-form', $order) }}"
                                           class="btn btn-sm btn-success" title="Fill thông tin">
                                            <i class="fas fa-edit me-1"></i>Fill
                                        </a>
                                        <form action="{{ route('admin.pending-orders.destroy', $order) }}"
                                              method="POST" class="d-inline"
                                              data-ajax-action data-row-target="closest:tr">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Huỷ"
                                                    data-confirm="Huỷ đơn {{ $order->order_code }}?">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-3 py-2 d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        {{ $orders->firstItem() }}–{{ $orders->lastItem() }} / {{ $orders->total() }}
                    </small>
                    {{ $orders->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal: Tạo nhanh --}}
<div class="modal fade" id="newOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.pending-orders.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tạo đơn nhanh</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Số tiền *</label>
                        <input type="text" name="amount_input" id="quickAmountInput"
                               class="form-control form-control-lg"
                               placeholder="100k, 200k, 1.5tr..." required autofocus
                               autocomplete="off" inputmode="decimal">
                        <input type="hidden" name="amount" id="quickAmountHidden">
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small class="text-muted">Hỗ trợ: <code>100k</code>, <code>200k</code>, <code>1.5tr</code>, <code>500000</code></small>
                            <small id="quickAmountPreview" class="fw-bold text-success"></small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú nhanh (tuỳ chọn)</label>
                        <input type="text" name="note" class="form-control"
                               placeholder="VD: chatgpt cho Thu Hà, gemini Tâm 1 tháng,..." maxlength="255">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Tạo đơn
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal: QR full size --}}
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-qrcode me-2"></i>
                    <span id="qrModalCode"></span> · <span id="qrModalAmount"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="qrModalImg" src="" alt="QR Code">
                <div class="alert alert-info text-start mt-3 mb-0">
                    <strong>{{ config('payment.bank_short_name') }}</strong> · STK: <code>{{ config('payment.account_number') }}</code><br>
                    Tên: <strong>{{ config('payment.account_name') }}</strong>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" onclick="copyQrUrl()">
                    <i class="fas fa-copy me-1"></i>Copy link QR
                </button>
                <a id="qrModalDownload" href="" download class="btn btn-primary">
                    <i class="fas fa-download me-1"></i>Tải ảnh
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ====== Parse + format số tiền dạng ngắn ======
function parseShortAmount(input) {
    if (!input) return 0;
    const s = String(input).trim().toLowerCase();
    const m = s.match(/^([\d.,]+)\s*(k|nghìn|nghin|tr|triệu|trieu|m)?\s*$/);
    if (!m) return 0;
    const unit = m[2] || '';
    let num;
    if (unit === '') {
        // Không có đơn vị → bỏ hết dấu chấm/phẩy (đều là thousand separator vì VND không có decimal)
        num = parseFloat(m[1].replace(/[.,]/g, ''));
    } else {
        // Có đơn vị → cho phép thập phân (vd "1.5tr", "1,5tr")
        num = parseFloat(m[1].replace(',', '.'));
    }
    if (isNaN(num)) return 0;
    if (unit === 'k' || unit === 'nghìn' || unit === 'nghin') return Math.round(num * 1000);
    if (unit === 'tr' || unit === 'triệu' || unit === 'trieu' || unit === 'm') return Math.round(num * 1000000);
    return Math.round(num);
}
function formatShortAmount(a) {
    a = Math.round(Number(a) || 0);
    if (a === 0) return '0đ';
    const abs = Math.abs(a);
    const sign = a < 0 ? '-' : '';
    if (abs >= 1000000 && abs % 100000 === 0) {
        const v = abs / 1000000;
        return sign + (v % 1 === 0 ? v.toFixed(0) : v.toFixed(1).replace(/\.?0+$/, '')) + 'tr';
    }
    if (abs >= 1000 && abs % 1000 === 0) {
        return sign + (abs / 1000) + 'k';
    }
    return sign + abs.toLocaleString('vi-VN') + 'đ';
}

// ====== Quick order form: live preview + sync hidden field ======
(function() {
    const inp = document.getElementById('quickAmountInput');
    const hidden = document.getElementById('quickAmountHidden');
    const preview = document.getElementById('quickAmountPreview');
    if (!inp) return;

    function update() {
        const parsed = parseShortAmount(inp.value);
        hidden.value = parsed;
        preview.textContent = parsed > 0 ? '= ' + formatShortAmount(parsed) + ' (' + parsed.toLocaleString('vi-VN') + 'đ)' : '';
    }
    inp.addEventListener('input', update);
    document.getElementById('newOrderModal')?.addEventListener('shown.bs.modal', () => {
        inp.value = '';
        update();
        inp.focus();
    });
})();

// ====== QR modal ======
function showQrModal(url, code, amount) {
    document.getElementById('qrModalImg').src = url;
    document.getElementById('qrModalDownload').href = url;
    document.getElementById('qrModalCode').textContent = code;
    document.getElementById('qrModalAmount').textContent = amount;
    new bootstrap.Modal(document.getElementById('qrModal')).show();
}
function copyQrUrl() {
    const url = document.getElementById('qrModalImg').src;
    navigator.clipboard.writeText(url).then(() => {
        alert('Đã copy link QR vào clipboard.');
    });
}
</script>
@endpush
