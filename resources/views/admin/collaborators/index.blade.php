@extends('layouts.admin')

@section('title', 'Quản lý cộng tác viên')
@section('page-title', 'Quản lý cộng tác viên')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">{{ $stats['total'] }}</h5>
                            <p class="card-text">Tổng cộng tác viên</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">{{ $stats['active'] }}</h5>
                            <p class="card-text">Đang hoạt động</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-concierge-bell fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">{{ $stats['total_services'] }}</h5>
                            <p class="card-text">Tổng dịch vụ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-key fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">{{ $stats['total_accounts'] }}</h5>
                            <p class="card-text">Tổng tài khoản</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>
                        Danh sách cộng tác viên
                    </h5>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.collaborators.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Thêm cộng tác viên
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Search and Filter -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text"
                        class="form-control"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Tìm kiếm tên, mã, email, SĐT...">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sort_by" class="form-select">
                        <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                        <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Tên</option>
                        <option value="collaborator_code" {{ request('sort_by') === 'collaborator_code' ? 'selected' : '' }}>Mã</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sort_direction" class="form-select">
                        <option value="desc" {{ request('sort_direction') === 'desc' ? 'selected' : '' }}>Giảm dần</option>
                        <option value="asc" {{ request('sort_direction') === 'asc' ? 'selected' : '' }}>Tăng dần</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Mã CTV</th>
                            <th>Tên cộng tác viên</th>
                            <th>Liên hệ</th>
                            <th>Dịch vụ</th>
                            <th>Tổng giá trị</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($collaborators as $collaborator)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $collaborator->collaborator_code }}</span>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $collaborator->name }}</strong>
                                    @if($collaborator->notes)
                                    <br><small class="text-muted">{{ Str::limit($collaborator->notes, 30) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($collaborator->email)
                                <div><i class="fas fa-envelope me-1"></i> {{ $collaborator->email }}</div>
                                @endif
                                @if($collaborator->phone)
                                <div><i class="fas fa-phone me-1"></i> {{ $collaborator->phone }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $collaborator->active_services_count }} dịch vụ</span>
                                <br>
                                <small class="text-muted">{{ $collaborator->total_accounts }} tài khoản</small>
                            </td>
                            <td>
                                <strong class="text-success">{{ number_format($collaborator->total_value, 0, ',', '.') }} VND</strong>
                            </td>
                            <td>
                                @if($collaborator->status === 'active')
                                <span class="badge bg-success">Hoạt động</span>
                                @else
                                <span class="badge bg-danger">Không hoạt động</span>
                                @endif
                            </td>
                            <td>{{ $collaborator->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.collaborators.show', $collaborator) }}"
                                        class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.collaborators.edit', $collaborator) }}"
                                        class="btn btn-sm btn-outline-warning" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.collaborators.destroy', $collaborator) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa cộng tác viên này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Chưa có cộng tác viên nào</p>
                                <a href="{{ route('admin.collaborators.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Thêm cộng tác viên đầu tiên
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($collaborators->hasPages())
            <div class="d-flex justify-content-center">
                {{ $collaborators->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection