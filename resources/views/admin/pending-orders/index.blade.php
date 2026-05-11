@extends('layouts.admin')

@section('title', 'Pending Orders - Đơn chờ fill')

@push('styles')
<style>
    /* ====== STATS CARDS ====== */
    .po-stat-card {
        background: #fff;
        border-radius: 14px;
        padding: 1.1rem 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        border: 1px solid #f1f3f5;
        position: relative;
        overflow: hidden;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .po-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .po-stat-card .icon-wrap {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 42px; height: 42px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.15rem;
        opacity: 0.9;
    }
    .po-stat-card .stat-label {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 0.4rem;
    }
    .po-stat-card .stat-value {
        font-size: 1.85rem;
        font-weight: 700;
        line-height: 1.1;
        color: #1a1a2e;
    }
    .po-stat-card .stat-sub {
        font-size: 0.78rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }
    .po-stat-card.variant-warning { border-top: 3px solid #f59e0b; }
    .po-stat-card.variant-warning .icon-wrap { background: #fef3c7; color: #d97706; }
    .po-stat-card.variant-danger { border-top: 3px solid #ef4444; }
    .po-stat-card.variant-danger .icon-wrap { background: #fee2e2; color: #dc2626; }
    .po-stat-card.variant-info { border-top: 3px solid #3b82f6; }
    .po-stat-card.variant-info .icon-wrap { background: #dbeafe; color: #2563eb; }
    .po-stat-card.variant-success { border-top: 3px solid #10b981; }
    .po-stat-card.variant-success .icon-wrap { background: #d1fae5; color: #059669; }

    /* Pulse animation cho card urgent */
    .po-stat-card.variant-danger.has-urgent .icon-wrap {
        animation: pulse-danger 2s ease-in-out infinite;
    }
    @keyframes pulse-danger {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        50%      { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
    }

    /* ====== FILTER CARD ====== */
    .filter-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #f1f3f5;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .filter-card .filter-header {
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid #f1f3f5;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .filter-card .filter-header .title {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #495057;
    }
    .filter-card .active-count {
        background: #2563eb;
        color: white;
        font-size: 0.7rem;
        padding: 0.15rem 0.5rem;
        border-radius: 999px;
        font-weight: 700;
        margin-left: 0.4rem;
    }

    /* ====== TABLE ====== */
    .po-table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .po-table thead th {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6c757d;
        font-weight: 700;
        background: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
        padding: 0.65rem 0.75rem;
    }
    .po-table tbody td {
        padding: 0.875rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f5;
    }
    .po-table tbody tr {
        transition: background-color 0.15s ease, box-shadow 0.15s ease;
    }
    .po-table tbody tr:hover {
        background-color: #f8fafc;
    }
    /* Status strip indicator bên trái mỗi row */
    .po-table tbody tr td:first-child {
        position: relative;
        border-left: 4px solid transparent;
    }
    .po-table tbody tr.row-status-pending td:first-child { border-left-color: #f59e0b; }
    .po-table tbody tr.row-status-completed td:first-child { border-left-color: #10b981; }
    .po-table tbody tr.row-status-cancelled td:first-child { border-left-color: #9ca3af; }
    .po-table tbody tr.row-status-urgent td:first-child {
        border-left-color: #ef4444;
        animation: pulse-strip 2s ease-in-out infinite;
    }
    @keyframes pulse-strip {
        0%, 100% { border-left-color: #ef4444; }
        50%      { border-left-color: #fca5a5; }
    }
    /* Urgent row background tint */
    .po-table tbody tr.row-status-urgent {
        background-color: #fef2f2;
    }
    .po-table tbody tr.row-status-urgent:hover {
        background-color: #fee2e2;
    }

    /* Order code badge — monospace, click to copy */
    .order-code {
        font-family: 'JetBrains Mono', 'Courier New', monospace;
        font-weight: 700;
        color: #1a1a2e;
        background: #f1f5f9;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-size: 0.85rem;
        letter-spacing: -0.02em;
        cursor: pointer;
        transition: background 0.15s;
    }
    .order-code:hover {
        background: #e0e7ff;
        color: #4338ca;
    }
    .amount-badge {
        font-weight: 700;
        color: #16a34a;
        font-size: 0.95rem;
    }

    /* Status badges với icon */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.35rem 0.7rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }
    .status-badge.pending  { background: #fef3c7; color: #92400e; }
    .status-badge.urgent   { background: #fee2e2; color: #b91c1c; animation: blink 1.4s ease-in-out infinite; }
    .status-badge.completed { background: #d1fae5; color: #065f46; }
    .status-badge.cancelled { background: #e5e7eb; color: #374151; }
    .status-badge.paid      { background: #d1fae5; color: #065f46; }
    .status-badge.unpaid    { background: #f3f4f6; color: #6b7280; }
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    /* Customer cell */
    .customer-cell .code {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.78rem;
        color: #4338ca;
        font-weight: 600;
    }
    .customer-cell .name {
        font-size: 0.85rem;
        color: #374151;
    }

    /* Sticky cột Thao tác */
    .table-responsive { position: relative; }
    .action-cell-sticky {
        position: sticky;
        right: 0;
        background: #ffffff;
        z-index: 2;
        box-shadow: -6px 0 10px -6px rgba(0,0,0,0.08);
        white-space: nowrap;
    }
    .po-table thead .action-cell-sticky {
        background: #f8f9fa;
    }
    .po-table tbody tr.row-status-urgent .action-cell-sticky {
        background: #fef2f2;
    }
    .po-table tbody tr:hover .action-cell-sticky {
        background: #f8fafc;
    }
    .po-table tbody tr.row-status-urgent:hover .action-cell-sticky {
        background: #fee2e2;
    }
    .action-cell-sticky .btn {
        white-space: nowrap;
    }

    /* QR modal */
    .qr-thumb {
        width: 48px; height: 48px;
        cursor: pointer;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 4px;
        background: white;
        transition: transform 0.15s ease;
    }
    .qr-thumb:hover { border-color: #6366f1; transform: scale(1.08); }
    #qrModalImg { max-width: 100%; height: auto; }

    /* Empty state */
    .po-empty {
        padding: 3.5rem 1rem;
        text-align: center;
    }
    .po-empty .empty-icon {
        width: 80px; height: 80px;
        margin: 0 auto 1rem;
        border-radius: 50%;
        background: #f1f5f9;
        display: flex; align-items: center; justify-content: center;
        color: #94a3b8;
        font-size: 2rem;
    }
    .po-empty h5 { color: #1e293b; margin-bottom: 0.4rem; }
    .po-empty p { color: #64748b; max-width: 400px; margin: 0 auto 1rem; }
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

    {{-- Stats — 4 cards with accent color + icon --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="po-stat-card variant-warning">
                <div class="icon-wrap"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-label">Đang chờ fill</div>
                <div class="stat-value" data-stats-key="pending">{{ number_format($stats['pending']) }}</div>
                <div class="stat-sub">Tổng đơn pending</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="po-stat-card variant-danger {{ ($stats['needs_fill_urgent'] ?? 0) > 0 ? 'has-urgent' : '' }}">
                <div class="icon-wrap"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-label">Cần fill gấp</div>
                <div class="stat-value">{{ number_format($stats['needs_fill_urgent'] ?? 0) }}</div>
                <div class="stat-sub">Đã trả mà chưa fill xong</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="po-stat-card variant-info">
                <div class="icon-wrap"><i class="fas fa-calendar-day"></i></div>
                <div class="stat-label">Đơn hôm nay</div>
                <div class="stat-value" data-stats-key="today">{{ number_format($stats['today']) }}</div>
                <div class="stat-sub">{{ now()->format('d/m/Y') }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="po-stat-card variant-success">
                <div class="icon-wrap"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-label">Tổng tiền hôm nay</div>
                <div class="stat-value" title="{{ number_format($stats['today_amount'], 0, ',', '.') }}đ">{{ formatShortAmount($stats['today_amount']) }}</div>
                <div class="stat-sub">Số tiền đã đặt đơn</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    @php
        $activeFilters = collect([
            $status !== 'pending' ? 'status' : null,
            ($paidFilter ?? 'all') !== 'all' ? 'paid' : null,
            ($source ?? 'all') !== 'all' ? 'source' : null,
            request('date') ? 'date' : null,
            !empty($code) ? 'code' : null,
            !empty($customerSearch) ? 'customer' : null,
        ])->filter()->count();
    @endphp
    <div class="filter-card mb-3">
        <div class="filter-header">
            <div class="title">
                <i class="fas fa-filter me-1 text-primary"></i>Bộ lọc
                @if($activeFilters > 0)
                    <span class="active-count">{{ $activeFilters }} điều kiện</span>
                @endif
            </div>
            <div class="d-flex gap-2 align-items-center">
                @if($activeFilters > 0)
                    <span class="small text-muted">
                        <i class="fas fa-check-circle text-success me-1"></i>
                        Tìm thấy <strong>{{ $orders->total() }}</strong> đơn
                    </span>
                    <a href="{{ route('admin.pending-orders.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Xoá lọc
                    </a>
                @endif
            </div>
        </div>
        <div class="p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold">Trạng thái</label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Đang chờ fill</option>
                        <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Đã huỷ</option>
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tất cả</option>
                    </select>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold">Thanh toán</label>
                    <select name="paid" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ ($paidFilter ?? 'all') === 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="paid" {{ ($paidFilter ?? '') === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="unpaid" {{ ($paidFilter ?? '') === 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                    </select>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold">Nguồn</label>
                    <select name="source" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ ($source ?? 'all') === 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="telegram" {{ ($source ?? '') === 'telegram' ? 'selected' : '' }}>Telegram</option>
                        <option value="web" {{ ($source ?? '') === 'web' ? 'selected' : '' }}>Web</option>
                    </select>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold">Ngày tạo</label>
                    <input type="date" name="date" value="{{ request('date') }}" class="form-control form-control-sm" onchange="this.form.submit()">
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold">Mã đơn</label>
                    <input type="text" name="code" value="{{ $code ?? '' }}" class="form-control form-control-sm" placeholder="DH-260506-001">
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold">
                        <i class="fas fa-user me-1"></i>Khách hàng
                    </label>
                    <input type="text" name="customer" value="{{ $customerSearch ?? '' }}" class="form-control form-control-sm" placeholder="KUN/CTV hoặc tên...">
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm" style="border-radius: 14px;">
        <div class="card-body p-0">
            @if($orders->count() === 0)
                <div class="po-empty">
                    <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                    <h5>Không có đơn nào khớp</h5>
                    @if($activeFilters > 0)
                        <p>Thử xoá bớt filter, hoặc kiểm tra mã đơn / mã KH đã gõ đúng chưa.</p>
                        <a href="{{ route('admin.pending-orders.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-times me-1"></i>Xoá tất cả filter
                        </a>
                    @else
                        <p>Tạo đơn nhanh từ bot Telegram hoặc bấm <strong>"Tạo nhanh"</strong> ở góc phải.</p>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newOrderModal">
                            <i class="fas fa-plus me-1"></i>Tạo đơn mới
                        </button>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="po-table table mb-0">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Số tiền</th>
                                <th class="d-none d-md-table-cell">Khách hàng</th>
                                <th class="d-none d-xl-table-cell">Ghi chú</th>
                                <th class="d-none d-lg-table-cell">Nguồn</th>
                                <th class="d-none d-md-table-cell">Tạo lúc</th>
                                <th>Trạng thái</th>
                                <th>Thanh toán</th>
                                <th class="text-end action-cell-sticky">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $order)
                            @php
                                // Đơn pending + đã paid + chưa link CS → cần fill GẤP (cảnh báo)
                                $needsFillUrgent = $order->status === 'pending' && $order->paid_at && !$order->customer_service_id;
                                $rowClass = $needsFillUrgent ? 'row-status-urgent' : "row-status-{$order->status}";
                            @endphp
                            <tr data-order-row="{{ $order->id }}" class="{{ $rowClass }}">
                                <td>
                                    <span class="order-code" title="Click để copy" onclick="navigator.clipboard?.writeText('{{ $order->order_code }}')">
                                        {{ $order->order_code }}
                                    </span>
                                </td>
                                <td>
                                    <span class="amount-badge" title="{{ number_format($order->amount, 0, ',', '.') }}đ">
                                        {{ formatShortAmount($order->amount) }}
                                    </span>
                                </td>
                                {{-- Cột Khách hàng — direct customer_id ưu tiên, fallback customerService.customer --}}
                                <td class="d-none d-md-table-cell customer-cell">
                                    @php
                                        $kh = $order->customer ?? $order->customerService?->customer;
                                    @endphp
                                    @if($kh)
                                        <div class="code">{{ $kh->customer_code }}</div>
                                        <div class="name">{{ $kh->name }}</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="d-none d-xl-table-cell">
                                    @if($order->note)
                                        <small>{{ $order->note }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    @if($order->created_via === 'telegram')
                                        <span class="badge bg-info"><i class="fab fa-telegram me-1"></i>Telegram</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="fas fa-globe me-1"></i>Web</span>
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell">
                                    {{ $order->created_at->format('H:i') }}
                                    <br><small class="text-muted">{{ $order->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    @if($order->status === 'pending')
                                        @if($needsFillUrgent)
                                            <span class="status-badge urgent" title="Đã thanh toán nhưng chưa fill — cần xử lý gấp!">
                                                <i class="fas fa-exclamation-triangle"></i>Cần fill gấp
                                            </span>
                                        @else
                                            <span class="status-badge pending">
                                                <i class="fas fa-hourglass-half"></i>Chờ fill
                                            </span>
                                        @endif
                                    @elseif($order->status === 'completed')
                                        <span class="status-badge completed">
                                            <i class="fas fa-check-circle"></i>Đã fill
                                        </span>
                                        @if($order->customer_service_id)
                                            <br><a href="{{ route('admin.customer-services.show', $order->customer_service_id) }}" class="small text-decoration-none mt-1 d-inline-block">
                                                <i class="fas fa-arrow-right me-1"></i>DV #{{ $order->customer_service_id }}
                                            </a>
                                        @endif
                                    @else
                                        <span class="status-badge cancelled">
                                            <i class="fas fa-times-circle"></i>Đã huỷ
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->paid_at)
                                        <span class="status-badge paid" title="{{ $order->paid_at->format('d/m/Y H:i:s') }}">
                                            <i class="fas fa-check-circle"></i>Đã trả
                                        </span>
                                        @if($order->paid_amount && $order->paid_amount != $order->amount)
                                            <br><small class="text-danger fw-semibold mt-1 d-inline-block">
                                                {{ formatShortAmount($order->paid_amount) }}
                                                ({{ $order->paid_amount > $order->amount ? '+' : '' }}{{ formatShortAmount($order->paid_amount - $order->amount) }})
                                            </small>
                                        @endif
                                    @else
                                        <span class="status-badge unpaid">
                                            <i class="far fa-clock"></i>Chưa trả
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end action-cell-sticky">
                                    {{-- Nút "Xem QR" — luôn hiện cho mọi đơn (kể cả completed/cancelled) --}}
                                    <button type="button"
                                            class="btn btn-sm btn-outline-info"
                                            title="Xem QR thanh toán"
                                            onclick="showQrModal('{{ $order->qrCodeUrl() }}', '{{ $order->order_code }}', '{{ formatShortAmount($order->amount) }}')">
                                        <i class="fas fa-qrcode"></i>
                                        <span class="d-none d-xl-inline ms-1">QR</span>
                                    </button>
                                    @if($order->status === 'pending')
                                        @if(!$order->paid_at)
                                            <form action="{{ route('admin.pending-orders.mark-paid', $order) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Đánh dấu đã thanh toán (khi Pay2S không nhận được)"
                                                        data-confirm="Xác nhận đơn {{ $order->order_code }} ({{ formatShortAmount($order->amount) }}) đã được khách thanh toán?">
                                                    <i class="fas fa-check-circle me-1"></i>Đã trả
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.pending-orders.fill-form', $order) }}"
                                           class="btn btn-sm btn-outline-primary" title="Fill thông tin">
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
