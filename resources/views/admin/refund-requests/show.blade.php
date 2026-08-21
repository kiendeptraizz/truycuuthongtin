@extends('layouts.admin')

@section('title', 'Chi tiết hoàn tiền ' . $refundRequest->code)
@section('page-title', 'Chi tiết yêu cầu hoàn tiền')

@section('content')
@php
    // Format tiền VND
    $money = fn ($v) => number_format((int) $v, 0, ',', '.') . 'đ';
@endphp
<div class="container-fluid">
    <div class="mb-3">
        <a href="{{ route('admin.refund-requests.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Về danh sách
        </a>
    </div>

    <div class="row g-3">
        {{-- CỘT TRÁI: thông tin khách gửi --}}
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-user me-2 text-primary"></i>Thông tin khách gửi</span>
                    @if($refundRequest->status === 'done')
                        <span class="badge bg-success">Đã hoàn</span>
                    @elseif($refundRequest->status === 'rejected')
                        <span class="badge bg-secondary">Từ chối</span>
                    @else
                        <span class="badge bg-warning text-dark">Chờ xử lý</span>
                    @endif
                </div>
                <div class="card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-5 text-muted">Mã theo dõi</dt>
                        <dd class="col-7"><code class="text-primary fw-bold">{{ $refundRequest->code }}</code></dd>

                        <dt class="col-5 text-muted">Ngày gửi</dt>
                        <dd class="col-7">{{ $refundRequest->created_at->format('H:i d/m/Y') }}</dd>

                        <dt class="col-5 text-muted">Tên khách hàng</dt>
                        <dd class="col-7 fw-semibold">{{ $refundRequest->customer_name }}</dd>

                        <dt class="col-5 text-muted">Mã đơn (khách nhập)</dt>
                        <dd class="col-7"><code class="text-success">{{ $refundRequest->order_code }}</code></dd>

                        <dt class="col-5 text-muted">Ngân hàng</dt>
                        <dd class="col-7">{{ $refundRequest->bank_name ?? '—' }}</dd>

                        <dt class="col-5 text-muted">Số tài khoản</dt>
                        <dd class="col-7 fw-semibold" style="letter-spacing:.5px;">{{ $refundRequest->bank_account }}</dd>

                        <dt class="col-5 text-muted">Chủ tài khoản</dt>
                        <dd class="col-7">{{ $refundRequest->account_holder ?? '—' }}</dd>
                    </dl>

                    @if($refundRequest->qr_url)
                        <div class="text-center mt-3">
                            <small class="text-muted d-block mb-1">Ảnh QR nhận tiền</small>
                            <a href="{{ $refundRequest->qr_url }}" target="_blank">
                                <img src="{{ $refundRequest->qr_url }}" alt="QR"
                                     style="max-width:180px;max-height:180px;border-radius:12px;border:1px solid #dee2e6;">
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI: khớp đơn + tính hoàn --}}
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <i class="fas fa-calculator me-2 text-primary"></i>Đơn khớp &amp; tính tiền hoàn
                </div>
                <div class="card-body">
                    @if(!$service)
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Không tìm thấy đơn dịch vụ khớp mã <code>{{ $refundRequest->order_code }}</code>.
                            Vui lòng kiểm tra lại mã đơn khách nhập, hoặc xử lý hoàn tiền thủ công.
                        </div>
                    @else
                        {{-- Thông tin đơn khớp --}}
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong>{{ $service->servicePackage->name ?? 'Đơn nhanh' }}</strong>
                                <div class="text-muted small">
                                    Đơn: <code>{{ $service->order_code }}</code>
                                    · KH: {{ $service->customer->name ?? '—' }}
                                </div>
                            </div>
                            <a href="{{ route('admin.customer-services.show', $service) }}" target="_blank"
                               class="btn btn-sm btn-outline-info" title="Xem đơn gốc">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="border rounded p-2 text-center">
                                    <small class="text-muted d-block">Giá đơn</small>
                                    <strong>{{ $service->order_amount ? $money($service->order_amount) : '—' }}</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 text-center">
                                    <small class="text-muted d-block">Kích hoạt</small>
                                    <strong>{{ $service->activated_at ? $service->activated_at->format('d/m/Y') : 'Chưa' }}</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 text-center">
                                    <small class="text-muted d-block">Hết hạn</small>
                                    <strong>{{ $service->expires_at ? $service->expires_at->format('d/m/Y') : '—' }}</strong>
                                </div>
                            </div>
                        </div>

                        {{-- Chọn ngày lỗi (GET preview — tự submit khi đổi) --}}
                        <form method="GET" action="{{ route('admin.refund-requests.show', $refundRequest) }}" class="mb-3">
                            <label class="form-label fw-semibold mb-1">
                                <i class="fas fa-calendar-times me-1 text-danger"></i>Ngày tài khoản lỗi
                            </label>
                            <div class="input-group">
                                <input type="date" name="error_date" class="form-control"
                                       value="{{ $errorDate->format('Y-m-d') }}"
                                       onchange="this.form.submit()">
                                <button class="btn btn-outline-primary" type="submit"><i class="fas fa-sync-alt me-1"></i>Tính lại</button>
                            </div>
                            <div class="form-text">
                                Hệ thống tính hoàn theo số ngày còn lại <strong>từ ngày lỗi</strong> đến ngày hết hạn (không tính từ hôm nay).
                            </div>
                        </form>

                        {{-- Kết quả tính --}}
                        @if($calc && ($calc['ok'] ?? false))
                            @if(($calc['mode'] ?? '') === 'expired')
                                <div class="alert alert-secondary mb-0">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Tính đến ngày lỗi <strong>{{ $errorDate->format('d/m/Y') }}</strong> thì đơn đã hết hạn → <strong>hoàn 0đ</strong>.
                                </div>
                            @else
                                <div class="p-3 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Số tiền hoàn đề xuất</span>
                                        <span class="fs-3 fw-bold text-success">{{ $money($calc['refund_amount'] ?? 0) }}</span>
                                    </div>
                                    <div class="row text-center g-2 small">
                                        <div class="col">
                                            <div class="text-muted">Tổng ngày</div>
                                            <strong>{{ $calc['total_days'] ?? '—' }}</strong>
                                        </div>
                                        <div class="col">
                                            <div class="text-muted">Đã dùng (đến ngày lỗi)</div>
                                            <strong>{{ $calc['days_used'] ?? '—' }}</strong>
                                        </div>
                                        <div class="col">
                                            <div class="text-muted">Còn lại</div>
                                            <strong class="text-success">{{ $calc['days_remaining'] ?? '—' }}</strong>
                                        </div>
                                        <div class="col">
                                            <div class="text-muted">% hoàn</div>
                                            <strong>{{ $calc['percent_remaining'] ?? '—' }}%</strong>
                                        </div>
                                    </div>
                                    <div class="text-muted small mt-2">
                                        <i class="fas fa-circle-info me-1"></i>{{ $calc['reason_label'] ?? '' }}
                                    </div>
                                </div>
                            @endif
                        @elseif($calc)
                            @php
                                $reasonMsg = match($calc['reason'] ?? '') {
                                    'already_refunded' => 'Đơn này đã được hoàn tiền trước đó.',
                                    'already_cancelled' => 'Đơn đã huỷ — không tính hoàn lại.',
                                    'no_order_amount' => 'Đơn không có số tiền (order_amount) → không tự tính được, cần nhập thủ công.',
                                    'no_expires_at' => 'Đơn thiếu ngày hết hạn → không tính được tỉ lệ.',
                                    default => 'Không tính được tiền hoàn cho đơn này.',
                                };
                            @endphp
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-1"></i>{{ $reasonMsg }}
                            </div>
                        @endif
                    @endif

                    {{-- Form lưu quyết định --}}
                    <hr class="my-3">
                    <form method="POST" action="{{ route('admin.refund-requests.update', $refundRequest) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="error_date" value="{{ $errorDate->format('Y-m-d') }}">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <label class="form-label small mb-1">Trạng thái</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="pending" {{ $refundRequest->status === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                    <option value="done" {{ $refundRequest->status === 'done' ? 'selected' : '' }}>Đã hoàn tiền</option>
                                    <option value="rejected" {{ $refundRequest->status === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label small mb-1">Ghi chú (nội bộ)</label>
                                <input type="text" name="admin_note" class="form-control form-control-sm"
                                       value="{{ $refundRequest->admin_note }}" placeholder="VD: đã CK 150k lúc 10:30">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">
                                @if($refundRequest->computed_refund !== null)
                                    Đã lưu tiền hoàn: <strong class="text-success">{{ $money($refundRequest->computed_refund) }}</strong>
                                    @if($refundRequest->error_date)
                                        (ngày lỗi {{ $refundRequest->error_date->format('d/m/Y') }})
                                    @endif
                                @endif
                            </small>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save me-1"></i>Lưu (ngày lỗi + số tiền hoàn)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
