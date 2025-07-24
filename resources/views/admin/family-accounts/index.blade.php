@extends('layouts.admin')

@section('title', 'Quản lý Family Accounts')
@section('page-title', 'Quản lý Family Accounts')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Tổng Families</h6>
                            <h4 class="mb-0">{{ number_format($stats['total_families']) }}</h4>
                        </div>
                        <div class="ms-2">
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Đang Hoạt Động</h6>
                            <h4 class="mb-0">{{ number_format($stats['active_families']) }}</h4>
                        </div>
                        <div class="ms-2">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Sắp Hết Hạn</h6>
                            <h4 class="mb-0">{{ number_format($stats['expiring_soon']) }}</h4>
                        </div>
                        <div class="ms-2">
                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Tổng Thành Viên</h6>
                            <h4 class="mb-0">{{ number_format($stats['total_members']) }}</h4>
                        </div>
                        <div class="ms-2">
                            <i class="fas fa-user-friends fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-secondary text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">TB/Family</h6>
                            <h4 class="mb-0">{{ number_format($stats['avg_members_per_family'], 1) }}</h4>
                        </div>
                        <div class="ms-2">
                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-dark text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Gói Dịch Vụ</h6>
                            <h4 class="mb-0">{{ $servicePackages->count() }}</h4>
                        </div>
                        <div class="ms-2">
                            <i class="fas fa-box fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0">
                                <i class="fas fa-users me-2"></i>
                                Danh sách Family Accounts
                            </h5>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.family-accounts.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>
                                    Tạo Family Account
                                </a>
                                <a href="{{ route('admin.family-accounts.report') }}" class="btn btn-outline-info">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    Báo cáo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tìm kiếm</label>
                                <input type="text" class="form-control" name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Tên family, mã, email...">
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">Trạng thái</label>
                                <select class="form-select" name="status">
                                    <option value="">Tất cả</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Hết hạn</option>
                                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Tạm dừng</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Gói dịch vụ</label>
                                <select class="form-select" name="service_package_id">
                                    <option value="">Tất cả gói</option>
                                    @foreach($servicePackages as $package)
                                        <option value="{{ $package->id }}" 
                                                {{ request('service_package_id') == $package->id ? 'selected' : '' }}>
                                            {{ $package->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Sắp hết hạn</label>
                                <select class="form-select" name="expiring_soon">
                                    <option value="">Tất cả</option>
                                    <option value="7" {{ request('expiring_soon') === '7' ? 'selected' : '' }}>7 ngày</option>
                                    <option value="15" {{ request('expiring_soon') === '15' ? 'selected' : '' }}>15 ngày</option>
                                    <option value="30" {{ request('expiring_soon') === '30' ? 'selected' : '' }}>30 ngày</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search me-1"></i>
                                        Lọc
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Family Info</th>
                                    <th>Gói Dịch Vụ</th>
                                    <th>Thành Viên</th>
                                    <th>Trạng Thái</th>
                                    <th>Ngày Hết Hạn</th>
                                    <th>Giá Gói</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($familyAccounts as $family)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $family->family_name }}</h6>
                                                    <small class="text-muted">{{ $family->family_code }}</small><br>
                                                    <small class="text-muted">{{ $family->owner_email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div>
                                                <strong>{{ $family->servicePackage->name }}</strong><br>
                                                <small class="text-muted">{{ $family->servicePackage->category->name ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                    <div class="progress-bar" 
                                                         style="width: {{ $family->usage_percentage }}%"
                                                         role="progressbar"></div>
                                                </div>
                                                <small class="text-nowrap">
                                                    {{ $family->current_members }}/{{ $family->max_members }}
                                                </small>
                                            </div>
                                            {!! $family->usage_badge !!}
                                        </td>
                                        
                                        <td>
                                            {!! $family->status_badge !!}
                                            @if($family->is_expiring_soon)
                                                <br><small class="text-warning">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    {{ $family->days_until_expiry }} ngày
                                                </small>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <div>
                                                {{ $family->expires_at->format('d/m/Y') }}<br>
                                                <small class="text-muted">
                                                    {{ $family->expires_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div>
                                                <strong>{{ number_format($family->servicePackage->price) }}đ</strong><br>
                                                <small class="text-muted">
                                                    {{ $family->servicePackage->name }}
                                                </small>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.family-accounts.show', $family) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.family-accounts.edit', $family) }}" 
                                                   class="btn btn-sm btn-outline-secondary" 
                                                   title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($family->canAddMember())
                                                    <a href="{{ route('admin.family-accounts.add-member-form', $family) }}" 
                                                       class="btn btn-sm btn-outline-success" 
                                                       title="Thêm thành viên">
                                                        <i class="fas fa-user-plus"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-users fa-3x mb-3"></i>
                                                <p>Chưa có Family Account nào.</p>
                                                <a href="{{ route('admin.family-accounts.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus me-1"></i>
                                                    Tạo Family Account đầu tiên
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($familyAccounts->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $familyAccounts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 16px;
}

.progress {
    background-color: #e9ecef;
}

.progress-bar {
    background-color: #28a745;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
@endpush
