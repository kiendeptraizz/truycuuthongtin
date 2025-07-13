@extends('layouts.admin')

@section('title', 'Quản lý Lead')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user-plus text-primary me-2"></i>
                        Quản lý Lead
                    </h1>
                    <p class="text-muted mb-0">Chăm sóc khách hàng tiềm năng</p>
                </div>
                <a href="{{ route('admin.leads.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Thêm Lead
                </a>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Tổng số Lead</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Lead mới</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['new'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Cần theo dõi hôm nay</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['follow_up_today'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Quá hạn</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['overdue'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-filter me-2"></i>Bộ lọc
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.leads.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tìm kiếm</label>
                            <input type="text" name="search" class="form-control" placeholder="Tên, SĐT, Email..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>Mới</option>
                                <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Đã liên hệ</option>
                                <option value="interested" {{ request('status') == 'interested' ? 'selected' : '' }}>Quan tâm</option>
                                <option value="quoted" {{ request('status') == 'quoted' ? 'selected' : '' }}>Đã báo giá</option>
                                <option value="won" {{ request('status') == 'won' ? 'selected' : '' }}>Thành công</option>
                                <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Thất bại</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Độ ưu tiên</label>
                            <select name="priority" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Thấp</option>
                                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Cao</option>
                                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Khẩn cấp</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Người phụ trách</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Tất cả</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Lọc
                                </button>
                                <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-undo me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Leads Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách Lead</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-success" onclick="bulkAction('assign')">
                            <i class="fas fa-user-check me-1"></i>Gán hàng loạt
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="bulkAction('status')">
                            <i class="fas fa-edit me-1"></i>Đổi trạng thái
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                            <i class="fas fa-trash me-1"></i>Xóa hàng loạt
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($leads->count() > 0)
                        <div class="table-responsive-enhanced horizontal-scroll-container">
                            <table class="table table-bordered table-fixed-layout table-min-width-extra-large" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="checkAll">
                                        </th>
                                        <th>Thông tin</th>
                                        <th>Gói dịch vụ</th>
                                        <th>Trạng thái</th>
                                        <th>Độ ưu tiên</th>
                                        <th>Người phụ trách</th>
                                        <th>Theo dõi tiếp theo</th>
                                        <th class="table-action-column">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leads as $lead)
                                        <tr class="{{ $lead->isOverdue() ? 'table-danger' : '' }}">
                                            <td>
                                                <input type="checkbox" class="lead-checkbox" value="{{ $lead->id }}">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <div class="avatar-initial bg-primary rounded-circle">
                                                            {{ substr($lead->name, 0, 1) }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $lead->name }}</h6>
                                                        <small class="text-muted">
                                                            <i class="fas fa-phone me-1"></i>{{ $lead->phone }}
                                                        </small>
                                                        @if($lead->email)
                                                            <br><small class="text-muted">
                                                                <i class="fas fa-envelope me-1"></i>{{ $lead->email }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($lead->servicePackage)
                                                    <span class="badge bg-info">{{ $lead->servicePackage->name }}</span>
                                                    @if($lead->estimated_value)
                                                        <br><small class="text-success">
                                                            {{ number_format($lead->estimated_value) }} VND
                                                        </small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Chưa xác định</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $lead->getStatusBadgeClass() }}">
                                                    {{ $lead->getStatusName() }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $lead->getPriorityBadgeClass() }}">
                                                    {{ $lead->getPriorityName() }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($lead->assignedUser)
                                                    <span class="text-primary">{{ $lead->assignedUser->name }}</span>
                                                @else
                                                    <span class="text-muted">Chưa gán</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($lead->next_follow_up_at)
                                                    <div class="text-sm">
                                                        {{ $lead->next_follow_up_at->format('d/m/Y H:i') }}
                                                        @if($lead->isOverdue())
                                                            <br><span class="text-danger">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                                Quá hạn {{ $lead->getDaysOverdue() }} ngày
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">Chưa đặt lịch</span>
                                                @endif
                                            </td>
                                            <td class="table-action-column">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                                            data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.leads.show', $lead) }}">
                                                                <i class="fas fa-eye me-2"></i>Xem chi tiết
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.leads.edit', $lead) }}">
                                                                <i class="fas fa-edit me-2"></i>Chỉnh sửa
                                                            </a>
                                                        </li>
                                                        @if($lead->status !== 'won')
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item text-success" href="#" 
                                                                   onclick="showConvertModal({{ $lead->id }})">
                                                                    <i class="fas fa-user-check me-2"></i>Chuyển đổi
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if($lead->status !== 'lost')
                                                            <li>
                                                                <a class="dropdown-item text-warning" href="#" 
                                                                   onclick="showLostModal({{ $lead->id }})">
                                                                    <i class="fas fa-times me-2"></i>Đánh dấu mất
                                                                </a>
                                                            </li>
                                                        @endif
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#" 
                                                               onclick="confirmDelete({{ $lead->id }})">
                                                                <i class="fas fa-trash me-2"></i>Xóa
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Hiển thị {{ $leads->firstItem() }} - {{ $leads->lastItem() }} 
                                trong tổng số {{ $leads->total() }} kết quả
                            </div>
                            {{ $leads->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-user-plus fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có lead nào</h5>
                            <p class="text-muted">Bắt đầu bằng cách thêm lead đầu tiên của bạn.</p>
                            <a href="{{ route('admin.leads.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Thêm Lead
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Convert Modal -->
<div class="modal fade" id="convertModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chuyển đổi Lead thành Khách hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="convertForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên khách hàng</label>
                        <input type="text" name="customer_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="customer_email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="customer_phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <textarea name="customer_address" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">Chuyển đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lost Modal -->
<div class="modal fade" id="lostModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Đánh dấu Lead bị mất</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="lostForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Lý do mất lead</label>
                        <textarea name="reason" class="form-control" rows="3" 
                                  placeholder="Mô tả lý do tại sao lead này bị mất..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning">Đánh dấu mất</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Checkbox tất cả
document.getElementById('checkAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.lead-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Hiển thị modal chuyển đổi
function showConvertModal(leadId) {
    const form = document.getElementById('convertForm');
    form.action = `/admin/leads/${leadId}/convert`;
    new bootstrap.Modal(document.getElementById('convertModal')).show();
}

// Hiển thị modal mất lead
function showLostModal(leadId) {
    const form = document.getElementById('lostForm');
    form.action = `/admin/leads/${leadId}/mark-lost`;
    new bootstrap.Modal(document.getElementById('lostModal')).show();
}

// Xác nhận xóa
function confirmDelete(leadId) {
    if (confirm('Bạn có chắc chắn muốn xóa lead này?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/leads/${leadId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Hành động hàng loạt
function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.lead-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Vui lòng chọn ít nhất một lead');
        return;
    }

    const leadIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    let value = '';
    if (action === 'assign') {
        value = prompt('Nhập ID người dùng để gán:');
        if (!value) return;
    } else if (action === 'status') {
        value = prompt('Nhập trạng thái mới (new, contacted, interested, quoted, won, lost):');
        if (!value) return;
    } else if (action === 'delete') {
        if (!confirm('Bạn có chắc chắn muốn xóa các lead đã chọn?')) return;
    }

    fetch('/admin/leads/bulk-action', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            action: action,
            lead_ids: leadIds,
            value: value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Có lỗi xảy ra');
        }
    });
}
</script>
@endsection
