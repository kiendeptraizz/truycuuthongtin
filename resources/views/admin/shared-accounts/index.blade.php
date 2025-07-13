@extends('layouts.admin')

@section('title', 'Quản lý tài khoản dùng chung')
@section('page-title', 'Quản lý tài khoản dùng chung')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Quản lý tài khoản dùng chung</h5>
                        <small class="text-muted">Theo dõi gói dịch vụ "TEAM DÙNG CHUNG"</small>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.shared-accounts.report') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-pie"></i> Báo cáo
                        </a>
                        <a href="{{ route('admin.customer-services.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Thống kê tổng quan -->
                <div class="row mb-3">
                    <div class="col-6 col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center py-2">
                                <h4 class="mb-1">{{ $stats['total_shared_accounts'] }}</h4>
                                <small>Tài khoản chung</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center py-2">
                                <h4 class="mb-1">{{ $stats['total_users_in_shared'] }}</h4>
                                <small>Tổng dịch vụ</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center py-2">
                                <h4 class="mb-1">{{ $stats['problematic_accounts'] }}</h4>
                                <small>Có vấn đề</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center py-2">
                                <h4 class="mb-1">{{ $stats['expiring_shared_services'] }}</h4>
                                <small>Sắp hết hạn</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bộ lọc -->
                <div class="row mb-3">
                    <div class="col-12">
                        <form method="GET" class="row g-2">
                            <div class="col-md-3">
                                <select name="filter_type" class="form-select form-select-sm">
                                    <option value="">Tất cả tài khoản</option>
                                    <option value="problematic" {{ request('filter_type') == 'problematic' ? 'selected' : '' }}>
                                        Tài khoản có vấn đề
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="min_customers" class="form-select form-select-sm">
                                    <option value="">Số khách hàng</option>
                                    <option value="2" {{ request('min_customers') == 2 ? 'selected' : '' }}>Từ 2 khách hàng</option>
                                    <option value="3" {{ request('min_customers') == 3 ? 'selected' : '' }}>Từ 3 khách hàng</option>
                                    <option value="5" {{ request('min_customers') == 5 ? 'selected' : '' }}>Từ 5 khách hàng</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="sort_by" class="form-select">
                                    <option value="total_services" {{ request('sort_by', 'total_services') == 'total_services' ? 'selected' : '' }}>
                                        Số dịch vụ
                                    </option>
                                    <option value="unique_customers" {{ request('sort_by') == 'unique_customers' ? 'selected' : '' }}>
                                        Số khách hàng
                                    </option>
                                    <option value="expired_count" {{ request('sort_by') == 'expired_count' ? 'selected' : '' }}>
                                        Đã hết hạn
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Lọc</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.shared-accounts.index') }}" class="btn btn-outline-secondary w-100">
                                    Xóa bộ lọc
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Danh sách tài khoản dùng chung -->
                @if($sharedAccounts->count() > 0)
                    <div class="table-responsive">
                        <!-- Responsive info alert -->
                        <div class="alert alert-info alert-sm d-lg-none mb-2">
                            <i class="fas fa-info-circle me-1"></i>
                            <small>Một số cột được ẩn trên màn hình nhỏ. Xem trên màn hình lớn hơn để thấy đầy đủ thông tin.</small>
                        </div>
                        
                        <table class="table table-hover table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th style="min-width: 200px;">Email tài khoản</th>
                                    <th class="d-none-md" style="min-width: 100px;">Loại</th>
                                    <th style="min-width: 80px;">Tổng DV</th>
                                    <th style="min-width: 80px;">Khách hàng</th>
                                    <th class="d-none-lg" style="min-width: 80px;">Hoạt động</th>
                                    <th style="min-width: 80px;">Hết hạn</th>
                                    <th class="d-none-xl" style="min-width: 80px;">Sắp hết</th>
                                    <th class="d-none-lg" style="min-width: 100px;">Bảo mật</th>
                                    <th style="min-width: 100px;">Trạng thái</th>
                                    <th style="min-width: 80px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sharedAccounts as $account)
                                @php
                                    $isProblematic = $account->unique_customers > 1;
                                    $hasExpired = $account->expired_count > 0;
                                    $hasExpiring = $account->expiring_soon_count > 0;
                                    $rowClass = $isProblematic ? 'table-warning' : ($hasExpired ? 'table-danger' : ($hasExpiring ? 'table-warning' : ''));
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <strong>{{ $account->login_email }}</strong>
                                                @if($isProblematic)
                                                    <br><small class="text-warning">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        Nhiều khách hàng
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="d-none-md">
                                        <span class="badge bg-secondary">TEAM SPAN</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $account->total_services }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $account->unique_customers > 1 ? 'bg-warning' : 'bg-info' }}">
                                            {{ $account->unique_customers }}
                                        </span>
                                    </td>
                                    <td class="d-none-lg">
                                        <span class="badge bg-success">{{ $account->active_count }}</span>
                                    </td>
                                    <td>
                                        @if($account->expired_count > 0)
                                            <span class="badge bg-danger">{{ $account->expired_count }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td class="d-none-xl">
                                        @if($account->expiring_soon_count > 0)
                                            <span class="badge bg-warning">{{ $account->expiring_soon_count }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td class="d-none-lg">
                                        <div class="d-flex flex-column gap-1">
                                            @if(!empty($account->shared_password))
                                                <small class="badge bg-success">Mật khẩu</small>
                                            @endif
                                            @if(!empty($account->two_factor_code))
                                                <small class="badge bg-info">2FA</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($hasExpired)
                                            <span class="badge bg-danger">Có hết hạn</span>
                                        @elseif($hasExpiring)
                                            <span class="badge bg-warning">Sắp hết hạn</span>
                                        @elseif($isProblematic)
                                            <span class="badge bg-warning">Cần chú ý</span>
                                        @else
                                            <span class="badge bg-success">Bình thường</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.shared-accounts.show', urlencode($account->login_email)) }}" 
                                               class="btn btn-sm btn-outline-info"
                                               title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.shared-accounts.edit', urlencode($account->login_email)) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Chỉnh sửa thông tin">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Phân trang -->
                    <div class="d-flex justify-content-center">
                        {{ $sharedAccounts->appends(request()->query())->links() }}
                    </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Không có tài khoản dùng chung</h5>
                    <p class="text-muted">Không tìm thấy tài khoản nào có nhiều hơn 1 dịch vụ hoặc thỏa mãn điều kiện lọc.</p>
                    <a href="{{ route('admin.shared-accounts.index') }}" class="btn btn-primary">
                        <i class="fas fa-refresh me-1"></i>
                        Xem tất cả
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
