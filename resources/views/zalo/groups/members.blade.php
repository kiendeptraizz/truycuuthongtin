@extends('layouts.admin')

@section('title', 'Thành viên Nhóm')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Thành viên: {{ $group->group_name }}</h2>
            <p class="text-muted mb-0">Tổng {{ number_format($members->total()) }} thành viên</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.zalo.groups.show', $group) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Filter & Actions -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" id="searchMembers" placeholder="Tìm theo tên hoặc Zalo ID...">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterStatus">
                        <option value="">Tất cả trạng thái</option>
                        <option value="new">Mới</option>
                        <option value="contacted">Đã liên hệ</option>
                        <option value="converted">Đã chuyển đổi</option>
                        <option value="failed">Thất bại</option>
                        <option value="blocked">Bị chặn</option>
                    </select>
                </div>
                <div class="col-md-5 text-end">
                    <button class="btn btn-outline-primary">
                        <i class="fas fa-file-import"></i> Import thành viên
                    </button>
                    <button class="btn btn-outline-success">
                        <i class="fas fa-file-export"></i> Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Mới</h6>
                    <h3 class="mb-0 text-info">{{ number_format($group->newMembers()->count()) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Đã liên hệ</h6>
                    <h3 class="mb-0 text-warning">{{ number_format($group->contactedMembers()->count()) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Đã chuyển đổi</h6>
                    <h3 class="mb-0 text-success">{{ number_format($group->convertedMembers()->count()) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Conversion Rate</h6>
                    @php
                    $contacted = $group->contactedMembers()->count() + $group->convertedMembers()->count();
                    $converted = $group->convertedMembers()->count();
                    $rate = $contacted > 0 ? round(($converted / $contacted) * 100, 2) : 0;
                    @endphp
                    <h3 class="mb-0">{{ $rate }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Members Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="membersTable">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Tên hiển thị</th>
                            <th>Zalo ID</th>
                            <th>SĐT</th>
                            <th>Trạng thái</th>
                            <th>Ngày tham gia</th>
                            <th>Lần liên hệ cuối</th>
                            <th>Số lần liên hệ</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                        <tr data-status="{{ $member->status }}">
                            <td>
                                <input type="checkbox" class="form-check-input member-checkbox" value="{{ $member->id }}">
                            </td>
                            <td>
                                <strong>{{ $member->display_name ?? 'N/A' }}</strong>
                            </td>
                            <td><code>{{ $member->zalo_id }}</code></td>
                            <td>{{ $member->phone_number ?? '-' }}</td>
                            <td>
                                @php
                                $statusColors = [
                                'new' => 'info',
                                'contacted' => 'warning',
                                'converted' => 'success',
                                'failed' => 'danger',
                                'blocked' => 'dark'
                                ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$member->status] ?? 'secondary' }}">
                                    {{ ucfirst($member->status) }}
                                </span>
                            </td>
                            <td>{{ $member->joined_at ? $member->joined_at->format('d/m/Y') : '-' }}</td>
                            <td>
                                @if($member->last_contacted_at)
                                {{ $member->last_contacted_at->diffForHumans() }}
                                @else
                                <span class="text-muted">Chưa liên hệ</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $member->contact_count }}</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($member->status === 'new')
                                    <button class="btn btn-outline-primary btn-sm" title="Gửi tin nhắn">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                    @endif
                                    @if($member->messageLogs->count() > 0)
                                    <button class="btn btn-outline-info btn-sm" title="Lịch sử"
                                        data-bs-toggle="modal" data-bs-target="#historyModal{{ $member->id }}">
                                        <i class="fas fa-history"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                Chưa có thành viên nào. Import thành viên để bắt đầu.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($members->hasPages())
            <div class="mt-3">
                {{ $members->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card border-0 shadow-sm mt-3 bg-light">
        <div class="card-body">
            <div class="d-flex gap-2 align-items-center">
                <span class="text-muted">Với các mục đã chọn:</span>
                <button class="btn btn-sm btn-primary" id="bulkMessage">
                    <i class="fas fa-envelope"></i> Gửi tin nhắn hàng loạt
                </button>
                <button class="btn btn-sm btn-success" id="bulkExport">
                    <i class="fas fa-file-export"></i> Export
                </button>
                <button class="btn btn-sm btn-danger" id="bulkDelete">
                    <i class="fas fa-trash"></i> Xóa
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Select all checkbox
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.member-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Filter by status
    document.getElementById('filterStatus')?.addEventListener('change', function() {
        const status = this.value;
        const rows = document.querySelectorAll('#membersTable tbody tr');

        rows.forEach(row => {
            if (status === '' || row.dataset.status === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Search members
    document.getElementById('searchMembers')?.addEventListener('input', function() {
        const search = this.value.toLowerCase();
        const rows = document.querySelectorAll('#membersTable tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(search) ? '' : 'none';
        });
    });
</script>
@endpush
@endsection