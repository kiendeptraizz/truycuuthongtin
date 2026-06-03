{{--
    Partial 1 dòng đơn pending — tách ra để controller (markPaid / markCompleted /
    destroy) render lại sau AJAX action và trả về JSON `row_html`. JS thay outerHTML
    của <tr> tại chỗ + flash highlight, KHÔNG reload trang (giữ scroll position).

    Cần biến: $order (đã eager load customer, customerService.customer, creator).
--}}
@php
    // Đơn pending + đã paid + chưa link CS → cần fill GẤP (cảnh báo)
    $needsFillUrgent = $order->status === 'pending' && $order->paid_at && !$order->customer_service_id;
    $rowClass = $needsFillUrgent ? 'row-status-urgent' : "row-status-{$order->status}";
    // Inline style fallback — bypass mọi CSS cache để guarantee strip hiện
    $stripColor = match (true) {
        $needsFillUrgent => '#ef4444',
        $order->status === 'pending' => '#f59e0b',
        $order->status === 'completed' => '#10b981',
        $order->status === 'cancelled' => '#9ca3af',
        default => 'transparent',
    };
    $rowBg = match (true) {
        $needsFillUrgent => '#fee2e2',
        $order->status === 'pending' => '#fefce8',
        $order->status === 'completed' => '#f0fdf4',
        $order->status === 'cancelled' => '#f9fafb',
        default => 'transparent',
    };
@endphp
<tr id="order-{{ $order->id }}" data-order-row="{{ $order->id }}" class="{{ $rowClass }}" style="background-color: {{ $rowBg }};">
    <td style="border-left: 6px solid {{ $stripColor }}; padding-left: 16px; background-color: {{ $rowBg }};">
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
                      method="POST" class="d-inline" data-ajax-action data-row-update>
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
        @endif
        {{-- Nút Hoàn thành: hiện cho pending HOẶC completed-chưa-có-CS (backfill).
             Đơn nhanh CTV / không cần fill chi tiết → 1 click đóng đơn,
             tự set paid_at=now() nếu chưa paid + status='completed' + tạo CS placeholder. --}}
        @if($order->status === 'pending' || ($order->status === 'completed' && !$order->customer_service_id))
            <form action="{{ route('admin.pending-orders.mark-completed', $order) }}"
                  method="POST" class="d-inline" data-ajax-action data-row-update>
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-dark"
                        title="Hoàn thành đơn (tạo CS placeholder cho đơn CTV không fill chi tiết)"
                        data-confirm="Đánh dấu đơn {{ $order->order_code }} ({{ formatShortAmount($order->amount) }}) là HOÀN THÀNH?&#10;&#10;&bull; Nếu chưa CK → tự đánh dấu đã CK lúc bây giờ&#10;&bull; Tạo dịch vụ khách hàng (placeholder) ngay&#10;&bull; Đơn vào doanh thu/lợi nhuận&#10;&bull; Không cần fill gói/email/duration nữa&#10;&#10;Dùng cho đơn nhanh CTV không cần xử lý chi tiết.">
                    <i class="fas fa-flag-checkered me-1"></i>Hoàn thành
                </button>
            </form>
        @endif
        @if($order->status === 'pending')
            <form action="{{ route('admin.pending-orders.destroy', $order) }}"
                  method="POST" class="d-inline" data-ajax-action data-row-update>
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Huỷ"
                        data-confirm="Huỷ đơn {{ $order->order_code }}?">
                    <i class="fas fa-times"></i>
                </button>
            </form>
        @endif
    </td>
</tr>
