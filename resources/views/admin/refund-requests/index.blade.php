@extends('layouts.admin')

@section('title', 'Yêu cầu hoàn tiền')
@section('page-title', 'Yêu cầu hoàn tiền')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <h4 class="mb-1"><i class="fas fa-hand-holding-usd text-primary me-2"></i>Yêu cầu hoàn tiền</h4>
            <small class="text-muted">Khách gửi từ trang công khai <code>/refund</code>. Đang chờ xử lý: <strong>{{ $pendingCount }}</strong></small>
        </div>
        <a href="{{ route('refund.create') }}" target="_blank" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-external-link-alt me-1"></i>Mở trang khách gửi
        </a>
    </div>

    {{-- Bộ lọc --}}
    <form method="GET" class="card card-body shadow-sm mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small mb-1">Tìm kiếm</label>
                <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm"
                       placeholder="Tên KH, mã đơn, mã theo dõi, số tài khoản...">
            </div>
            <div class="col-md-4">
                <label class="form-label small mb-1">Trạng thái</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Tất cả --</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="done" {{ $status === 'done' ? 'selected' : '' }}>Đã hoàn</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary btn-sm flex-fill"><i class="fas fa-filter me-1"></i>Lọc</button>
                <a href="{{ route('admin.refund-requests.index') }}" class="btn btn-outline-secondary btn-sm">Xóa lọc</a>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã theo dõi</th>
                        <th>Khách hàng</th>
                        <th>Mã đơn</th>
                        <th>Số tài khoản</th>
                        <th class="text-center">QR</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($refundRequests as $req)
                        <tr>
                            <td>
                                <code class="text-primary fw-bold">{{ $req->code }}</code>
                                <br><small class="text-muted">{{ $req->created_at->format('H:i d/m/Y') }}</small>
                            </td>
                            <td><strong>{{ $req->customer_name }}</strong></td>
                            <td><code class="text-success">{{ $req->order_code }}</code></td>
                            <td>
                                <div class="fw-semibold">{{ $req->bank_account }}</div>
                                <small class="text-muted">
                                    {{ $req->account_holder ?? '—' }}@if($req->bank_name) · {{ $req->bank_name }}@endif
                                </small>
                            </td>
                            <td class="text-center">
                                @if($req->qr_url)
                                    <a href="{{ $req->qr_url }}" target="_blank" title="Xem ảnh QR">
                                        <img src="{{ $req->qr_url }}" alt="QR" style="width:46px;height:46px;object-fit:cover;border-radius:8px;border:1px solid #dee2e6;">
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($req->status === 'done')
                                    <span class="badge bg-success">Đã hoàn</span>
                                @elseif($req->status === 'rejected')
                                    <span class="badge bg-secondary">Từ chối</span>
                                @else
                                    <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                @endif
                                @if($req->admin_note)
                                    <br><small class="text-muted" title="{{ $req->admin_note }}"><i class="fas fa-comment-dots"></i> có ghi chú</small>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary btn-process"
                                    data-update-url="{{ route('admin.refund-requests.update', $req) }}"
                                    data-code="{{ $req->code }}"
                                    data-customer="{{ $req->customer_name }}"
                                    data-order="{{ $req->order_code }}"
                                    data-bank="{{ $req->bank_account }}"
                                    data-holder="{{ $req->account_holder }}"
                                    data-bankname="{{ $req->bank_name }}"
                                    data-qr="{{ $req->qr_url }}"
                                    data-status="{{ $req->status }}"
                                    data-note="{{ $req->admin_note }}"
                                    title="Xử lý">
                                    <i class="fas fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('admin.refund-requests.destroy', $req) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Xóa yêu cầu hoàn tiền {{ $req->code }}? Ảnh QR cũng sẽ bị xóa.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                Chưa có yêu cầu hoàn tiền nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($refundRequests->hasPages())
            <div class="card-footer bg-white">{{ $refundRequests->links() }}</div>
        @endif
    </div>
</div>

{{-- Modal xử lý --}}
<div class="modal fade" id="processModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="processForm" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-hand-holding-usd me-2"></i>Xử lý hoàn tiền <span id="pmCode" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-6"><small class="text-muted d-block">Khách hàng</small><strong id="pmCustomer"></strong></div>
                    <div class="col-6"><small class="text-muted d-block">Mã đơn</small><code id="pmOrder"></code></div>
                    <div class="col-6"><small class="text-muted d-block">Ngân hàng</small><strong id="pmBankName"></strong></div>
                    <div class="col-6"><small class="text-muted d-block">Chủ tài khoản</small><strong id="pmHolder"></strong></div>
                    <div class="col-12"><small class="text-muted d-block">Số tài khoản</small><strong id="pmBank" style="font-size:1.1rem;letter-spacing:.5px;"></strong></div>
                </div>
                <div class="text-center mb-3" id="pmQrWrap">
                    <small class="text-muted d-block mb-1">Ảnh QR nhận tiền</small>
                    <a id="pmQrLink" href="#" target="_blank">
                        <img id="pmQr" src="" alt="QR" style="max-width:200px;max-height:200px;border-radius:12px;border:1px solid #dee2e6;">
                    </a>
                </div>
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" id="pmStatus" class="form-select">
                        <option value="pending">Chờ xử lý</option>
                        <option value="done">Đã hoàn tiền</option>
                        <option value="rejected">Từ chối</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Ghi chú (nội bộ)</label>
                    <textarea name="admin_note" id="pmNote" class="form-control" rows="2" placeholder="VD: đã chuyển 200k lúc 10:30..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.btn-process').forEach(btn => {
        btn.addEventListener('click', function () {
            const d = this.dataset;
            document.getElementById('processForm').action = d.updateUrl;
            document.getElementById('pmCode').textContent = d.code;
            document.getElementById('pmCustomer').textContent = d.customer;
            document.getElementById('pmOrder').textContent = d.order;
            document.getElementById('pmBank').textContent = d.bank;
            document.getElementById('pmBankName').textContent = d.bankname || '—';
            document.getElementById('pmHolder').textContent = d.holder || '—';
            document.getElementById('pmStatus').value = d.status;
            document.getElementById('pmNote').value = d.note || '';

            const qrWrap = document.getElementById('pmQrWrap');
            if (d.qr) {
                document.getElementById('pmQr').src = d.qr;
                document.getElementById('pmQrLink').href = d.qr;
                qrWrap.style.display = 'block';
            } else {
                qrWrap.style.display = 'none';
            }

            new bootstrap.Modal(document.getElementById('processModal')).show();
        });
    });
</script>
@endsection
