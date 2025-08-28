@extends('layouts.admin')

@section('title', 'Quản lý tài khoản dùng chung')
@section('page-title', 'Quản lý tài khoản dùng chung')

@section('styles')
<style>
    .table-responsive {
        overflow-x: auto;
    }

    .table th, .table td {
        white-space: nowrap;
        vertical-align: middle;
        padding: 0.4rem 0.3rem; /* Giảm padding */
        font-size: 0.85rem; /* Giảm font size */
    }

    .table th {
        position: sticky;
        top: 0;
        background-color: var(--bs-primary);
        z-index: 10;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Sticky column styles for actions */
    .sticky-action-column {
        position: sticky !important;
        left: 0 !important;
        background: white !important;
        z-index: 15 !important;
        border-right: 2px solid #dee2e6 !important;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }

    .table thead .sticky-action-column {
        background: var(--bs-primary) !important;
        color: white !important;
        font-weight: 600;
    }

    /* Action buttons optimization */
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 1px;
        min-width: 70px; /* Giảm từ 90px */
    }

    .action-buttons .btn-group {
        width: 100%;
    }

    .action-buttons .btn {
        font-size: 0.7rem;
        padding: 0.2rem 0.3rem;
    }

    /* Email truncation */
    .email-truncate {
        max-width: 140px; /* Giảm từ 200px */
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: pointer;
    }

    .email-truncate:hover {
        overflow: visible;
        white-space: normal;
        word-break: break-all;
        background-color: #f8f9fa;
        padding: 0.2rem;
        border-radius: 0.25rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        z-index: 20;
        position: relative;
    }

    /* Compact badges */
    .badge-compact {
        font-size: 0.65rem;
        padding: 0.2rem 0.4rem;
    }

    /* Notes truncation */
    .notes-compact {
        max-width: 100px; /* Giảm từ 150px */
        font-size: 0.75rem;
    }

    /* Hover effects */
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .table tbody tr:hover .sticky-action-column {
        background-color: #f8f9fa !important;
    }

    /* Responsive breakpoints - ẩn nhiều cột hơn */
    @media (max-width: 1400px) {
        .d-none-xxl {
            display: none !important;
        }
    }

    @media (max-width: 1200px) {
        .d-none-xl {
            display: none !important;
        }
    }

    @media (max-width: 992px) {
        .d-none-lg {
            display: none !important;
        }
    }

    @media (max-width: 768px) {
        .d-none-md {
            display: none !important;
        }

        .sticky-action-column {
            min-width: 60px !important;
        }

        .action-buttons .btn {
            font-size: 0.65rem;
            padding: 0.15rem 0.25rem;
        }

        .email-truncate {
            max-width: 120px;
        }
    }

    /* Compact table styling */
    .table-compact {
        margin-bottom: 0;
    }

    .table-compact th,
    .table-compact td {
        border-width: 1px;
        line-height: 1.2;
    }

    /* Status badges optimization */
    .status-badge {
        font-size: 0.65rem;
        padding: 0.15rem 0.3rem;
        border-radius: 0.25rem;
    }
</style>
@endsection

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
                            <small>Giao diện đã được tối ưu cho màn hình nhỏ. Một số cột (Ghi chú, Hoạt động, Sắp hết, Bảo mật, Logout) được ẩn để cải thiện trải nghiệm xem.</small>
                        </div>
                        
                        <table class="table table-hover table-sm table-compact">
                            <thead class="table-primary">
                                <tr>
                                    <th class="sticky-action-column" style="min-width: 70px;">Thao tác</th>
                                    <th style="min-width: 140px;">Email</th>
                                    <th class="d-none-lg notes-compact" style="min-width: 60px;">Ghi chú</th>
                                    <th class="d-none-xl" style="min-width: 90px;">Hết hạn TK</th>
                                    <th style="min-width: 50px;">DV</th>
                                    <th style="min-width: 50px;">KH</th>
                                    <th class="d-none-xxl" style="min-width: 50px;">HĐ</th>
                                    <th style="min-width: 50px;">HH</th>
                                    <th class="d-none-lg" style="min-width: 50px;">SH</th>
                                    <th class="d-none-xl" style="min-width: 70px;">Bảo mật</th>
                                    <th class="d-none-lg" style="min-width: 80px;">Logout</th>
                                    <th style="min-width: 80px;">Trạng thái</th>
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
                                <tr id="account-{{ md5($account->login_email) }}" class="{{ $rowClass }}">
                                    <!-- Cột Thao tác - Di chuyển lên đầu và sticky -->
                                    <td class="sticky-action-column">
                                        <div class="action-buttons">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.shared-accounts.show', urlencode($account->login_email)) }}"
                                                   class="btn btn-sm btn-outline-info"
                                                   title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.shared-accounts.edit', urlencode($account->login_email)) }}?source=index"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="Chỉnh sửa thông tin">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Cột Email tài khoản - Tối ưu hóa -->
                                    <td>
                                        <div class="email-truncate" title="{{ $account->login_email }}">
                                            <strong>{{ $account->login_email }}</strong>
                                            @if($isProblematic)
                                                <br><small class="text-warning">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    Nhiều KH
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <!-- Cột Ghi chú - Format ngắn gọn -->
                                    <td class="d-none-lg notes-compact">
                                        @php
                                            // Sử dụng shared_account_notes đã được cập nhật
                                            $fullNote = $account->account_notes ?: 'Chưa có ghi chú';
                                            $shortId = $account->account_notes ?: 'TK??';
                                        @endphp
                                        <div class="text-muted" style="font-size: 0.7rem;" title="{{ $fullNote }}">
                                            <strong>{{ $shortId }}</strong>
                                        </div>
                                    </td>
                                    <!-- Cột Hết hạn TK -->
                                    <td class="d-none-xl">
                                        @if($account->latest_expiry)
                                            <div style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($account->latest_expiry)->format('d/m') }}</div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <!-- Cột Tổng DV -->
                                    <td>
                                        <span class="badge bg-primary badge-compact">{{ $account->total_services }}</span>
                                    </td>
                                    <!-- Cột Khách hàng -->
                                    <td>
                                        <span class="badge {{ $account->unique_customers > 1 ? 'bg-warning' : 'bg-info' }} badge-compact">
                                            {{ $account->unique_customers }}
                                        </span>
                                    </td>
                                    <!-- Cột Hoạt động -->
                                    <td class="d-none-xxl">
                                        <span class="badge bg-success badge-compact">{{ $account->active_count }}</span>
                                    </td>
                                    <!-- Cột Hết hạn -->
                                    <td>
                                        @if($account->expired_count > 0)
                                            <span class="badge bg-danger badge-compact">{{ $account->expired_count }}</span>
                                        @else
                                            <span class="text-muted" style="font-size: 0.75rem;">0</span>
                                        @endif
                                    </td>
                                    <!-- Cột Sắp hết -->
                                    <td class="d-none-lg">
                                        @if($account->expiring_soon_count > 0)
                                            <span class="badge bg-warning badge-compact">{{ $account->expiring_soon_count }}</span>
                                        @else
                                            <span class="text-muted" style="font-size: 0.75rem;">0</span>
                                        @endif
                                    </td>
                                    <!-- Cột Bảo mật -->
                                    <td class="d-none-xl">
                                        <div class="d-flex flex-wrap gap-1">
                                            @if(!empty($account->shared_password))
                                                <small class="badge bg-success" style="font-size: 0.6rem;">MK</small>
                                            @endif
                                            @if(!empty($account->two_factor_code))
                                                <small class="badge bg-info" style="font-size: 0.6rem;">2FA</small>
                                            @endif
                                        </div>
                                    </td>
                                    <!-- Cột Lịch sử Logout -->
                                    <td class="d-none-lg">
                                        @if($account->latest_logout_formatted)
                                            <div style="font-size: 0.7rem;" title="Logout gần nhất: {{ $account->latest_logout_formatted }}">
                                                <i class="fas fa-sign-out-alt text-muted me-1"></i>
                                                {{ $account->latest_logout_at ? \Carbon\Carbon::parse($account->latest_logout_at)->format('d/m') : '-' }}
                                            </div>
                                        @else
                                            <span class="text-muted" style="font-size: 0.7rem;" title="Chưa có lịch sử logout">
                                                <i class="fas fa-minus text-muted"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <!-- Cột Trạng thái -->
                                    <td>
                                        @if($hasExpired)
                                            <span class="badge bg-danger status-badge">Hết hạn</span>
                                        @elseif($hasExpiring)
                                            <span class="badge bg-warning status-badge">Sắp hết</span>
                                        @elseif($isProblematic)
                                            <span class="badge bg-warning status-badge">Cần chú ý</span>
                                        @else
                                            <span class="badge bg-success status-badge">OK</span>
                                        @endif
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to specific account if anchor is present in URL
    if (window.location.hash) {
        const targetElement = document.querySelector(window.location.hash);
        if (targetElement) {
            setTimeout(() => {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                // Add highlight effect
                targetElement.style.backgroundColor = '#fff3cd';
                setTimeout(() => {
                    targetElement.style.backgroundColor = '';
                }, 3000);
            }, 100);
        }
    }
});
</script>
@endsection
