@extends('layouts.admin')

@section('title', 'Thùng rác - Dịch vụ khách hàng')

@push('styles')
<style>
    .trash-stats-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }
    .trash-row td { vertical-align: middle; }
    .deleted-badge {
        background: #fef2f2;
        color: #dc2626;
        padding: 2px 8px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-trash-alt text-danger me-2"></i>Thùng rác</h2>
            <p class="text-muted mb-0">Các dịch vụ đã bị xoá. Có thể khôi phục hoặc xoá vĩnh viễn.</p>
        </div>
        <a href="{{ route('admin.customer-services.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Quay lại danh sách
        </a>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Thống kê — JS cập nhật real-time qua data-stats-key --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="trash-stats-card border-start border-4 border-danger">
                <div class="text-muted small">Tổng trong thùng rác</div>
                <div class="h3 mb-0" data-stats-key="total">{{ number_format($stats['total']) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="trash-stats-card border-start border-4 border-warning">
                <div class="text-muted small">Xoá trong 7 ngày qua</div>
                <div class="h3 mb-0" data-stats-key="last_7_days">{{ number_format($stats['last_7_days']) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="trash-stats-card border-start border-4 border-info">
                <div class="text-muted small">Xoá trong 30 ngày qua</div>
                <div class="h3 mb-0" data-stats-key="last_30_days">{{ number_format($stats['last_30_days']) }}</div>
            </div>
        </div>
    </div>

    {{-- Thanh công cụ --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('admin.customer-services.trash') }}">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   class="form-control" placeholder="Tìm theo tên, SĐT, mã KH, email, gói dịch vụ...">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                            @if(request('search'))
                                <a href="{{ route('admin.customer-services.trash') }}" class="btn btn-outline-secondary">Xoá lọc</a>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-md-end">
                    @if($stats['total'] > 0)
                        <button type="button" class="btn btn-outline-success" id="bulkRestoreBtn"
                                data-trigger="bulkForm"
                                data-action="{{ route('admin.customer-services.trash.bulk-restore') }}"
                                data-method="POST"
                                data-confirm="Khôi phục {n} dịch vụ?"
                                disabled>
                            <i class="fas fa-undo me-1"></i>Khôi phục đã chọn
                        </button>
                        <button type="button" class="btn btn-outline-danger" id="bulkForceDeleteBtn"
                                data-trigger="bulkForm"
                                data-action="{{ route('admin.customer-services.trash.bulk-force-delete') }}"
                                data-method="DELETE"
                                data-confirm="XOÁ VĨNH VIỄN {n} dịch vụ?\n\nKhông thể khôi phục."
                                disabled>
                            <i class="fas fa-times me-1"></i>Xoá vĩnh viễn đã chọn
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#emptyTrashModal">
                            <i class="fas fa-trash me-1"></i>Làm rỗng thùng rác
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Bảng dịch vụ --}}
    <div class="card">
        <div class="card-body p-0">
            @if($deletedServices->count() === 0)
                <div class="text-center py-5">
                    <i class="fas fa-trash-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Thùng rác trống</h5>
                    <p class="text-muted">Các dịch vụ đã bị xoá sẽ hiển thị ở đây.</p>
                </div>
            @else
                <form id="bulkForm" method="POST" data-ajax-bulk data-row-target="closest:tr" data-checkbox=".row-check">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="trashTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>ID</th>
                                    <th>Khách hàng</th>
                                    <th>Gói dịch vụ</th>
                                    <th>Email đăng nhập</th>
                                    <th>Hết hạn</th>
                                    <th>Đã xoá lúc</th>
                                    <th width="180">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($deletedServices as $service)
                                <tr class="trash-row">
                                    <td>
                                        <input type="checkbox" name="ids[]" value="{{ $service->id }}" class="form-check-input row-check">
                                    </td>
                                    <td><strong>#{{ $service->id }}</strong></td>
                                    <td>
                                        @if($service->customer)
                                            <div>{{ $service->customer->name }}</div>
                                            <small class="text-muted">{{ $service->customer->customer_code }} · {{ $service->customer->phone }}</small>
                                        @else
                                            <span class="text-muted">[Khách hàng đã bị xoá]</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $service->servicePackage?->name ?? '[Gói đã bị xoá]' }}
                                        @if($service->servicePackage?->category)
                                            <br><small class="text-muted">{{ $service->servicePackage->category->name }}</small>
                                        @endif
                                    </td>
                                    <td><small>{{ $service->login_email }}</small></td>
                                    <td>
                                        @if($service->expires_at)
                                            {{ $service->expires_at->format('d/m/Y') }}
                                            <br><small class="text-muted">{{ $service->expires_at->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="deleted-badge">
                                            {{ $service->deleted_at->format('d/m/Y H:i') }}
                                        </span>
                                        <br><small class="text-muted">{{ $service->deleted_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.customer-services.trash.restore', $service->id) }}"
                                              method="POST" class="d-inline"
                                              data-ajax-action data-row-target="closest:tr">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Khôi phục">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.customer-services.trash.force-delete', $service->id) }}"
                                              method="POST" class="d-inline"
                                              data-ajax-action data-row-target="closest:tr">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xoá vĩnh viễn"
                                                    data-confirm="XOÁ VĨNH VIỄN dịch vụ #{{ $service->id }}?&#10;&#10;Không thể khôi phục.">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>

                <div class="px-3 py-2 d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Hiển thị {{ $deletedServices->firstItem() }}–{{ $deletedServices->lastItem() }}
                        trong tổng số {{ $deletedServices->total() }}
                    </small>
                    {{ $deletedServices->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal: Làm rỗng thùng rác --}}
<div class="modal fade" id="emptyTrashModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Làm rỗng thùng rác</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.customer-services.trash.empty') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <strong>CẢNH BÁO!</strong> Hành động này sẽ xoá vĩnh viễn <strong>{{ $stats['total'] }}</strong>
                        dịch vụ trong thùng rác và <strong>không thể hoàn tác</strong>.
                    </div>
                    <p>Để xác nhận, gõ chính xác <code class="bg-light px-2">XÓA VĨNH VIỄN</code> vào ô bên dưới:</p>
                    <input type="text" name="confirm_text" class="form-control" required
                           placeholder="XÓA VĨNH VIỄN" autocomplete="off">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Xác nhận xoá vĩnh viễn
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Chỉ xử lý chọn tất cả + enable/disable button. AJAX submit do admin-ajax-actions.js xử lý.
$(function() {
    const $selectAll = $('#selectAll');
    const $rowCheck = $(document).on('change', '.row-check', updateButtons);
    const $bulkBtns = $('#bulkRestoreBtn, #bulkForceDeleteBtn');

    function updateButtons() {
        const checked = $('.row-check:checked').length;
        $bulkBtns.prop('disabled', checked === 0);
    }

    $selectAll.on('change', function() {
        $('.row-check').prop('checked', this.checked);
        updateButtons();
    });
});
</script>
@endpush
