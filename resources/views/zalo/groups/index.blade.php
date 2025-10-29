@extends('layouts.admin')

@section('title', 'Quản lý Nhóm Mục tiêu')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Quản lý Nhóm Mục tiêu</h2>
            <p class="text-muted mb-0">Quản lý các nhóm Zalo để quét thành viên và gửi tin nhắn</p>
        </div>
        <a href="{{ route('admin.zalo.groups.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm nhóm
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="group_type" class="form-select">
                        <option value="">Tất cả loại nhóm</option>
                        <option value="competitor" {{ request('group_type') === 'competitor' ? 'selected' : '' }}>Đối thủ</option>
                        <option value="own" {{ request('group_type') === 'own' ? 'selected' : '' }}>Nhóm của tôi</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Lọc</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tên nhóm</th>
                            <th>Loại</th>
                            <th>Chủ đề</th>
                            <th>Số thành viên</th>
                            <th>Ngày khai giảng</th>
                            <th>Lần quét cuối</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $group)
                        <tr>
                            <td>
                                <strong>{{ $group->group_name }}</strong>
                                <br>
                                <small class="text-muted">{{ Str::limit($group->group_link, 50) }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $group->group_type === 'own' ? 'success' : 'info' }}">
                                    {{ $group->group_type === 'own' ? 'Của tôi' : 'Đối thủ' }}
                                </span>
                            </td>
                            <td>{{ $group->topic ?? '-' }}</td>
                            <td>
                                <strong>{{ number_format($group->total_members) }}</strong>
                            </td>
                            <td>{{ $group->opening_date ? $group->opening_date->format('d/m/Y') : '-' }}</td>
                            <td>{{ $group->last_scanned_at ? $group->last_scanned_at->diffForHumans() : 'Chưa quét' }}</td>
                            <td>
                                @php
                                $statusColors = [
                                'active' => 'success',
                                'inactive' => 'secondary',
                                'completed' => 'primary'
                                ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$group->status] ?? 'secondary' }}">
                                    {{ ucfirst($group->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.zalo.groups.show', $group) }}" class="btn btn-outline-info" title="Chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.zalo.groups.members', $group) }}" class="btn btn-outline-success" title="Thành viên">
                                        <i class="fas fa-users"></i>
                                    </a>
                                    <a href="{{ route('admin.zalo.groups.edit', $group) }}" class="btn btn-outline-primary" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.zalo.groups.destroy', $group) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa nhóm này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Chưa có nhóm nào. <a href="{{ route('admin.zalo.groups.create') }}">Thêm nhóm đầu tiên</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($groups->hasPages())
            <div class="mt-3">
                {{ $groups->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection