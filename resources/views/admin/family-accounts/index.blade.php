@extends('layouts.admin')

@section('title', 'Quản lý Family Accounts')

@section('styles')
<style>
    .package-card {
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }

    .package-card:hover {
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .package-card.active {
        border-color: #667eea;
        background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
    }

    .package-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.5rem;
    }

    .slots-info {
        font-size: 0.8rem;
    }

    .family-table th {
        font-size: 0.85rem;
        white-space: nowrap;
    }
</style>
@endsection

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
                    <p class="text-muted mb-0">Quản lý tài khoản gia đình theo gói dịch vụ</p>
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

    <!-- Global Email Search -->
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-search me-2"></i>
                Tìm kiếm Email trong Family
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.family-accounts.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label">Nhập email hoặc tên khách hàng cần tìm</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="text"
                                class="form-control"
                                name="email_search"
                                value="{{ $emailSearchQuery ?? '' }}"
                                placeholder="Nhập email hoặc tên khách hàng...">
                        </div>
                        <small class="text-muted">Tìm theo email đăng nhập, email khách hàng, email chủ family, hoặc tên khách hàng</small>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="fas fa-search me-1"></i> Tìm kiếm
                        </button>
                        @if($emailSearchQuery)
                        <a href="{{ route('admin.family-accounts.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-1"></i> Xóa
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Email Search Results -->
    @if($emailSearchQuery)
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Kết quả tìm kiếm: "{{ $emailSearchQuery }}"
                    <span class="badge bg-light text-info ms-2">{{ $emailSearchResults->count() }} kết quả</span>
                </h5>
            </div>
        </div>
        <div class="card-body">
            @if($emailSearchResults->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th style="position: sticky; left: 0; background: #212529; z-index: 10; min-width: 100px; border-right: 2px solid #495057;">Hành động</th>
                            <th>Email</th>
                            <th>Khách hàng</th>
                            <th>Mã KH</th>
                            <th>Family</th>
                            <th>Gói dịch vụ</th>
                            <th>Nguồn</th>
                            <th>Trạng thái</th>
                            <th>Hết hạn</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($emailSearchResults as $result)
                        <tr>
                            <td style="position: sticky; left: 0; background: white; z-index: 5; border-right: 2px solid #dee2e6;">
                                <div class="btn-group btn-group-sm">
                                    @if($result['family_id'])
                                    <a href="{{ route('admin.family-accounts.show', $result['family_id']) }}"
                                        class="btn btn-outline-primary" title="Xem Family">
                                        <i class="fas fa-home"></i>
                                    </a>
                                    @endif
                                    @if($result['customer_id'])
                                    <a href="{{ route('admin.customers.show', $result['customer_id']) }}"
                                        class="btn btn-outline-info" title="Xem khách hàng">
                                        <i class="fas fa-user"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <code class="bg-light px-2 py-1 rounded small">{{ $result['email'] }}</code>
                            </td>
                            <td>
                                <strong>{{ $result['customer_name'] }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $result['customer_code'] }}</span>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $result['family_name'] }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $result['family_code'] }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $result['service_package'] }}</span>
                            </td>
                            <td>
                                @php
                                $sourceColors = [
                                'Dịch vụ khách hàng' => 'primary',
                                'Chủ sở hữu Family' => 'success',
                                'Thành viên Family' => 'warning',
                                ];
                                @endphp
                                <span class="badge bg-{{ $sourceColors[$result['source']] ?? 'secondary' }}">
                                    {{ $result['source'] }}
                                </span>
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
                                <span class="badge bg-{{ $statusColors[$result['status']] ?? 'secondary' }}">
                                    {{ $statusLabels[$result['status']] ?? $result['status'] }}
                                </span>
                            </td>
                            <td>
                                @if($result['expires_at'])
                                <small>{{ \Carbon\Carbon::parse($result['expires_at'])->format('d/m/Y') }}</small>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Không tìm thấy kết quả nào cho "{{ $emailSearchQuery }}"</h5>
                <p class="text-muted">Thử tìm với từ khóa khác hoặc kiểm tra lại email</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-primary text-white h-100">
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
            <div class="card bg-success text-white h-100">
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
            <div class="card bg-warning text-white h-100">
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
            <div class="card bg-danger text-white h-100">
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

    <!-- Service Packages Grid -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-cubes me-2"></i>Chọn gói dịch vụ để xem Family Accounts
            </h5>
        </div>
        <div class="card-body">
            @if($servicePackages->count() > 0)
            <div class="row">
                @foreach($servicePackages as $package)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                    <a href="{{ route('admin.family-accounts.index', ['service_package_id' => $package->id]) }}"
                        class="text-decoration-none">
                        <div class="card package-card h-100 {{ $selectedPackage && $selectedPackage->id == $package->id ? 'active' : '' }}">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="package-icon bg-primary-subtle text-primary me-3">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 text-dark">{{ $package->name }}</h6>
                                        <small class="text-muted">{{ $package->category->name ?? 'N/A' }}</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <span class="badge bg-primary">{{ $package->family_accounts_count }} Family</span>
                                    </div>
                                    <div>
                                        <span class="badge bg-success">{{ $package->active_families_count }} Active</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-4">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">Chưa có gói dịch vụ Family nào</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Family Accounts Table (chỉ hiện khi chọn package) -->
    @if($selectedPackage)
    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>
                    Family Accounts - {{ $selectedPackage->name }}
                    <span class="badge bg-light text-primary ms-2">{{ $familyAccounts->total() }}</span>
                </h5>
                <a href="{{ route('admin.family-accounts.index') }}" class="btn btn-sm btn-light">
                    <i class="fas fa-times me-1"></i> Đóng
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Search & Filter -->
            <form method="GET" class="mb-4">
                <input type="hidden" name="service_package_id" value="{{ $selectedPackage->id }}">
                <div class="row g-3">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="search"
                            value="{{ request('search') }}"
                            placeholder="Tìm tên family, mã, email chủ...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="status">
                            <option value="">Tất cả trạng thái</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Hết hạn</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Tạm ngưng</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Lọc
                        </button>
                        <a href="{{ route('admin.family-accounts.index', ['service_package_id' => $selectedPackage->id]) }}"
                            class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            @if($familyAccounts->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover family-table">
                    <thead class="table-dark">
                        <tr>
                            <th style="position: sticky; left: 0; background: #212529; z-index: 10; min-width: 120px; border-right: 2px solid #495057;">Hành động</th>
                            <th>ID</th>
                            <th>Tên Family</th>
                            <th>Mã</th>
                            <th>Email chủ</th>
                            <th>Slots</th>
                            <th>Trạng thái</th>
                            <th>Hết hạn</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($familyAccounts as $account)
                        <tr>
                            <td style="position: sticky; left: 0; background: white; z-index: 5; border-right: 2px solid #dee2e6;">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.family-accounts.show', $account) }}"
                                        class="btn btn-outline-primary" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.family-accounts.edit', $account) }}"
                                        class="btn btn-outline-secondary" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($account->current_members < $account->max_members)
                                        <a href="{{ route('admin.family-accounts.add-member-form', $account) }}"
                                            class="btn btn-outline-success" title="Thêm thành viên">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                        @endif
                                        <button type="button"
                                            class="btn btn-outline-danger"
                                            title="Xóa Family"
                                            onclick="confirmDelete({{ $account->id }}, '{{ addslashes($account->family_name) }}', {{ $account->current_members }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                </div>
                                <form id="delete-form-{{ $account->id }}"
                                    action="{{ route('admin.family-accounts.destroy', $account) }}"
                                    method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                            <td><span class="fw-bold">#{{ $account->id }}</span></td>
                            <td>
                                <div>
                                    <strong>{{ $account->family_name }}</strong>
                                    @if($account->owner_name)
                                    <br><small class="text-muted">{{ $account->owner_name }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <code class="bg-light px-2 py-1 rounded small">{{ $account->family_code }}</code>
                            </td>
                            <td>
                                <a href="mailto:{{ $account->owner_email }}" class="text-decoration-none small">
                                    {{ $account->owner_email }}
                                </a>
                            </td>
                            <td>
                                <span class="badge {{ $account->current_members >= $account->max_members ? 'bg-danger' : 'bg-success' }}">
                                    {{ $account->current_members }}/{{ $account->max_members }}
                                </span>
                                @if($account->current_members >= $account->max_members)
                                <i class="fas fa-exclamation-triangle text-warning ms-1" title="Đã hết slot"></i>
                                @endif
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
                                @php
                                $daysRemaining = $account->getDaysRemaining();
                                $isExpired = $account->isExpired();
                                $isExpiringSoon = $account->isExpiringSoon(7);
                                @endphp
                                <div class="small">
                                    {{ $account->expires_at->format('d/m/Y') }}
                                    @if($isExpired)
                                    <i class="fas fa-exclamation-triangle text-danger ms-1"></i>
                                    @elseif($isExpiringSoon)
                                    <i class="fas fa-exclamation-triangle text-warning ms-1"></i>
                                    @endif
                                    <br>
                                    <span class="text-muted">
                                        @if($isExpired)
                                        Đã hết hạn
                                        @elseif($daysRemaining == 0)
                                        Hết hạn hôm nay
                                        @else
                                        Còn {{ $daysRemaining }} ngày
                                        @endif
                                    </span>
                                </div>
                                @else
                                <span class="text-muted small">Chưa set</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($familyAccounts->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <p class="text-muted mb-0 small">
                        Hiển thị {{ $familyAccounts->firstItem() }} - {{ $familyAccounts->lastItem() }}
                        trong tổng số {{ $familyAccounts->total() }}
                    </p>
                </div>
                <div>
                    {{ $familyAccounts->links() }}
                </div>
            </div>
            @endif
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Chưa có Family Account nào trong gói này</h5>
                <a href="{{ route('admin.family-accounts.create') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-1"></i> Tạo Family Account
                </a>
            </div>
            @endif
        </div>
    </div>
    @else
    <!-- Hướng dẫn khi chưa chọn package -->
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Hướng dẫn:</strong> Click vào một gói dịch vụ ở trên để xem danh sách Family Accounts trong gói đó.
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Auto submit form on filter change
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.querySelector('select[name="status"]');
        if (statusSelect) {
            statusSelect.addEventListener('change', function() {
                this.form.submit();
            });
        }
    });

    // Confirm delete family account
    function confirmDelete(accountId, familyName, memberCount) {
        let message = `Bạn có chắc chắn muốn xóa Family Account "${familyName}"?`;

        if (memberCount > 0) {
            message += `\n\n⚠️ CẢNH BÁO: Family này đang có ${memberCount} dịch vụ khách hàng đang sử dụng!\nXóa sẽ gỡ bỏ liên kết các dịch vụ này khỏi Family.`;
        }

        if (confirm(message)) {
            document.getElementById('delete-form-' + accountId).submit();
        }
    }
</script>
@endpush
@endsection