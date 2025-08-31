@extends('layouts.admin')

@section('title', 'Quản lý Family Accounts')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-home me-2"></i>
                        Quản lý Family Accounts
                    </h1>
                    <p class="text-muted mb-0">Quản lý tài khoản gia đình và thành viên</p>
                </div>
                <div>
                    <a href="{{ route('admin.family-accounts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Tạo Family Account
                    </a>
                    <a href="{{ route('admin.family-accounts.report') }}" class="btn btn-info">
                        <i class="fas fa-chart-bar me-1"></i>
                        Báo cáo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Tổng số Family</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-home fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Đang hoạt động</h6>
                            <h3 class="mb-0">{{ $stats['active'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Hết hạn</h6>
                            <h3 class="mb-0">{{ $stats['expired'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Tạm ngưng</h6>
                            <h3 class="mb-0">{{ $stats['suspended'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-pause-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.family-accounts.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text"
                                class="form-control"
                                id="search"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Tên family, mã, email chủ...">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Hết hạn</option>
                                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Tạm ngưng</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="service_package_id" class="form-label">Gói dịch vụ</label>
                            <select class="form-select" id="service_package_id" name="service_package_id">
                                <option value="">Tất cả gói</option>
                                @foreach($servicePackages as $package)
                                <option value="{{ $package->id }}" {{ request('service_package_id') == $package->id ? 'selected' : '' }}>
                                    {{ $package->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>
                                    Lọc
                                </button>
                                <a href="{{ route('admin.family-accounts.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Family Accounts Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        Danh sách Family Accounts
                        <span class="badge bg-secondary ms-2">{{ $familyAccounts->total() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($familyAccounts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Tên Family</th>
                                    <th>Mã Family</th>
                                    <th>Gói dịch vụ</th>
                                    <th>Email chủ</th>
                                    <th>Thành viên</th>
                                    <th>Trạng thái</th>
                                    <th>Hết hạn</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($familyAccounts as $account)
                                <tr>
                                    <td>
                                        <span class="fw-bold">#{{ $account->id }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $account->family_name }}</strong>
                                            @if($account->owner_name)
                                            <br><small class="text-muted">{{ $account->owner_name }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <code class="bg-light px-2 py-1 rounded">{{ $account->family_code }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $account->servicePackage->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $account->owner_email }}" class="text-decoration-none">
                                            {{ $account->owner_email }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge {{ $account->current_members >= $account->max_members ? 'bg-danger' : 'bg-success' }} me-2">
                                                {{ $account->current_members }}/{{ $account->max_members }}
                                            </span>
                                            @if($account->current_members >= $account->max_members)
                                            <i class="fas fa-exclamation-triangle text-warning" title="Family đã đầy"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                        $statusColors = [
                                        'active' => 'success',
                                        'expired' => 'warning',
                                        'suspended' => 'danger',
                                        'cancelled' => 'secondary',
                                        ];
                                        $statusLabels = [
                                        'active' => 'Hoạt động',
                                        'expired' => 'Hết hạn',
                                        'suspended' => 'Tạm ngưng',
                                        'cancelled' => 'Đã hủy',
                                        ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$account->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$account->status] ?? $account->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($account->expires_at)
                                        <div>
                                            {{ $account->expires_at->format('d/m/Y') }}
                                            @if($account->expires_at->isPast())
                                            <i class="fas fa-exclamation-circle text-danger ms-1" title="Đã hết hạn"></i>
                                            @elseif($account->expires_at->diffInDays() <= 7)
                                                <i class="fas fa-clock text-warning ms-1" title="Sắp hết hạn"></i>
                                                @endif
                                        </div>
                                        <small class="text-muted">
                                            {{ $account->expires_at->diffForHumans() }}
                                        </small>
                                        @else
                                        <span class="text-muted">Chưa set</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.family-accounts.show', $account) }}"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Xem chi tiết">
                                                <i class="fas fa-eye">👁️</i>
                                            </a>
                                            <a href="{{ route('admin.family-accounts.edit', $account) }}"
                                                class="btn btn-sm btn-outline-secondary"
                                                title="Chỉnh sửa">
                                                <i class="fas fa-edit">✏️</i>
                                            </a>
                                            @if($account->current_members < $account->max_members)
                                                <a href="{{ route('admin.family-accounts.add-member-form', $account) }}"
                                                    class="btn btn-sm btn-outline-success"
                                                    title="Thêm thành viên">
                                                    <i class="fas fa-user-plus">➕</i>
                                                </a>
                                                @endif
                                                <form method="POST"
                                                    action="{{ route('admin.family-accounts.destroy', $account) }}"
                                                    class="d-inline delete-form"
                                                    onsubmit="return confirmDelete(event, '{{ $account->family_name }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa Family Account">
                                                        <i class="fas fa-trash">🗑️</i>
                                                    </button>
                                                </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="text-muted mb-0">
                                Hiển thị {{ $familyAccounts->firstItem() }} - {{ $familyAccounts->lastItem() }}
                                trong tổng số {{ $familyAccounts->total() }} kết quả
                            </p>
                        </div>
                        <div>
                            {{ $familyAccounts->links() }}
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-home fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Chưa có Family Account nào</h5>
                        <p class="text-muted mb-4">Bắt đầu tạo Family Account đầu tiên của bạn</p>
                        <a href="{{ route('admin.family-accounts.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            Tạo Family Account
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto submit form on filter change
    document.addEventListener('DOMContentLoaded', function() {
        const filters = ['status', 'service_package_id'];

        filters.forEach(filterId => {
            const element = document.getElementById(filterId);
            if (element) {
                element.addEventListener('change', function() {
                    this.form.submit();
                });
            }
        });
    });

    // Delete confirmation with debug
    function confirmDelete(event, familyName) {
        console.log('Delete button clicked for:', familyName);
        console.log('Form:', event.target);
        console.log('Form action:', event.target.action);
        
        const confirmed = confirm(`Bạn có chắc muốn xóa Family Account "${familyName}"? Hành động này không thể hoàn tác!`);
        console.log('User confirmed:', confirmed);
        
        if (confirmed) {
            console.log('Submitting form...');
            return true;
        } else {
            console.log('User cancelled');
            return false;
        }
    }
</script>
@endpush
@endsection