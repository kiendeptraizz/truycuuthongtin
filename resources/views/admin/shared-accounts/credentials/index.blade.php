@extends('layouts.admin')

@section('title', 'Quản lý tài khoản dùng chung')

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
    }
    .package-card.active {
        border-color: #667eea;
        background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
    }
    .password-cell {
        font-family: 'Courier New', monospace;
    }
    .password-hidden {
        filter: blur(4px);
        user-select: none;
    }
    .copy-btn {
        cursor: pointer;
        opacity: 0.7;
    }
    .copy-btn:hover {
        opacity: 1;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-key me-2"></i>Quản lý tài khoản dùng chung
            </h1>
            <p class="text-muted mb-0">Thêm và quản lý các tài khoản để gán cho khách hàng</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.shared-accounts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-chart-bar me-1"></i> Thống kê sử dụng
            </a>
            @if($selectedPackage)
            <a href="{{ route('admin.shared-accounts.credentials.create', ['service_package_id' => $selectedPackage->id]) }}" 
               class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Thêm tài khoản
            </a>
            @endif
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Tổng tài khoản</h6>
                            <h3 class="mb-0">{{ $stats['total_credentials'] }}</h3>
                        </div>
                        <i class="fas fa-key fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Đang hoạt động</h6>
                            <h3 class="mb-0">{{ $stats['active_credentials'] }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Tổng người dùng</h6>
                            <h3 class="mb-0">{{ $stats['total_users'] }}</h3>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Packages Grid -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-cubes me-2"></i>Chọn gói dịch vụ
            </h5>
        </div>
        <div class="card-body">
            @if($servicePackages->count() > 0)
                <div class="row">
                    @foreach($servicePackages as $package)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('admin.shared-accounts.credentials', ['service_package_id' => $package->id]) }}" 
                               class="text-decoration-none">
                                <div class="card package-card h-100 {{ $selectedPackage && $selectedPackage->id == $package->id ? 'active' : '' }}">
                                    <div class="card-body text-center">
                                        <i class="fas fa-box fa-2x text-primary mb-2"></i>
                                        <h6 class="mb-1 text-dark">{{ $package->name }}</h6>
                                        <span class="badge bg-primary">{{ $package->shared_credentials_count }} tài khoản</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-box-open fa-3x mb-3"></i>
                    <p>Chưa có gói dịch vụ "Tài khoản dùng chung" nào</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Credentials Table -->
    @if($selectedPackage)
    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>{{ $selectedPackage->name }}
                    <span class="badge bg-light text-primary ms-2">{{ $credentials->total() ?? 0 }} tài khoản</span>
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#bulkImportModal">
                        <i class="fas fa-upload me-1"></i> Nhập hàng loạt
                    </button>
                    <a href="{{ route('admin.shared-accounts.credentials') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-times me-1"></i> Đóng
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Search -->
            <form method="GET" class="mb-4">
                <input type="hidden" name="service_package_id" value="{{ $selectedPackage->id }}">
                <div class="row g-3">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="search" 
                               value="{{ request('search') }}" placeholder="Tìm email...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="status">
                            <option value="">Tất cả trạng thái</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Hết hạn</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Tạm ngưng</option>
                            <option value="full" {{ request('status') === 'full' ? 'selected' : '' }}>Đã đầy</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Lọc
                        </button>
                        <a href="{{ route('admin.shared-accounts.credentials', ['service_package_id' => $selectedPackage->id]) }}" 
                           class="btn btn-outline-secondary">Reset</a>
                        <button type="button" class="btn btn-outline-info" onclick="toggleAllPasswords()">
                            <i class="fas fa-eye me-1"></i>
                        </button>
                    </div>
                </div>
            </form>

            @if($credentials->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Email</th>
                                <th>Mật khẩu</th>
                                <th>2FA</th>
                                <th>Người dùng</th>
                                <th>Thời hạn</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($credentials as $index => $cred)
                            <tr>
                                <td>{{ $credentials->firstItem() + $index }}</td>
                                <td>
                                    <span class="text-primary">{{ $cred->email }}</span>
                                    <i class="fas fa-copy copy-btn ms-1" onclick="copyToClipboard('{{ $cred->email }}')" title="Copy"></i>
                                </td>
                                <td class="password-cell">
                                    @if($cred->password)
                                        <span class="password-text password-hidden">{{ $cred->password }}</span>
                                        <i class="fas fa-copy copy-btn ms-1" onclick="copyToClipboard('{{ $cred->password }}')" title="Copy"></i>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="password-cell">
                                    @if($cred->two_factor_secret)
                                        <span class="password-text password-hidden">{{ Str::limit($cred->two_factor_secret, 15) }}</span>
                                        <i class="fas fa-copy copy-btn ms-1" onclick="copyToClipboard('{{ $cred->two_factor_secret }}')" title="Copy"></i>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $currentUsers = $cred->customerServices->where('status', 'active')->count();
                                    @endphp
                                    <span class="badge {{ $currentUsers >= $cred->max_users ? 'bg-danger' : 'bg-success' }}">
                                        {{ $currentUsers }}/{{ $cred->max_users }}
                                    </span>
                                </td>
                                <td>
                                    @if($cred->end_date)
                                        <small>
                                            {{ $cred->end_date->format('d/m/Y') }}
                                            @if($cred->days_remaining !== null && $cred->days_remaining <= 7)
                                                <span class="badge bg-warning">{{ $cred->days_remaining }}d</span>
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $cred->status_badge_class }}">{{ $cred->status_label }}</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.shared-accounts.credentials.edit', $cred) }}" 
                                           class="btn btn-outline-primary" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($currentUsers == 0)
                                        <form method="POST" action="{{ route('admin.shared-accounts.credentials.destroy', $cred) }}" 
                                              class="d-inline" onsubmit="return confirm('Xóa tài khoản này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @if($cred->notes)
                            <tr>
                                <td></td>
                                <td colspan="7">
                                    <small class="text-muted"><i class="fas fa-sticky-note me-1"></i>{{ $cred->notes }}</small>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($credentials->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $credentials->links() }}
                </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có tài khoản nào</h5>
                    <a href="{{ route('admin.shared-accounts.credentials.create', ['service_package_id' => $selectedPackage->id]) }}" 
                       class="btn btn-primary mt-2">
                        <i class="fas fa-plus me-1"></i> Thêm tài khoản
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Bulk Import Modal -->
    <div class="modal fade" id="bulkImportModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.shared-accounts.credentials.bulk-import') }}">
                    @csrf
                    <input type="hidden" name="service_package_id" value="{{ $selectedPackage->id }}">
                    
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Nhập hàng loạt tài khoản</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Danh sách tài khoản <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="accounts_data" rows="8" 
                                      placeholder="email|password|2fa&#10;email2|password2|2fa2" required></textarea>
                            <small class="text-muted">
                                Mỗi dòng một tài khoản. Định dạng: <code>email|password|2fa</code>
                            </small>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Max users <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="max_users" value="10" min="1" max="100" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ngày bắt đầu</label>
                                <input type="date" class="form-control" name="start_date" id="bulk_start_date" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ngày hết hạn</label>
                                <input type="date" class="form-control" name="end_date" id="bulk_end_date">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Thời hạn</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="bulk_duration" min="1" placeholder="Số">
                                    <select class="form-select" id="bulk_duration_unit" style="max-width: 70px;">
                                        <option value="days">D</option>
                                        <option value="months" selected>M</option>
                                        <option value="years">Y</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-text text-info mt-2" id="bulk_duration_info">
                            <i class="fas fa-info-circle me-1"></i>
                            Nhập thời hạn để tự động tính ngày hết hạn
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i> Nhập tài khoản
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Click vào một gói dịch vụ ở trên để xem và quản lý tài khoản dùng chung.
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 p-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `<div class="toast show bg-success text-white"><div class="toast-body"><i class="fas fa-check me-2"></i>Đã copy!</div></div>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    });
}

let allPasswordsVisible = false;
function toggleAllPasswords() {
    allPasswordsVisible = !allPasswordsVisible;
    document.querySelectorAll('.password-text').forEach(span => {
        span.classList.toggle('password-hidden', !allPasswordsVisible);
    });
}

// Bulk import duration calculator
document.addEventListener('DOMContentLoaded', function() {
    const bulkStartDate = document.getElementById('bulk_start_date');
    const bulkEndDate = document.getElementById('bulk_end_date');
    const bulkDuration = document.getElementById('bulk_duration');
    const bulkDurationUnit = document.getElementById('bulk_duration_unit');
    const bulkDurationInfo = document.getElementById('bulk_duration_info');

    if (bulkStartDate && bulkDuration && bulkDurationUnit) {
        function calculateBulkEndDate() {
            const startDate = bulkStartDate.value;
            const duration = parseInt(bulkDuration.value) || 0;
            const unit = bulkDurationUnit.value;

            if (!startDate || duration <= 0) {
                bulkDurationInfo.innerHTML = '<i class="fas fa-info-circle me-1"></i>Nhập thời hạn để tự động tính ngày hết hạn';
                return;
            }

            const start = new Date(startDate);
            let end = new Date(start);
            let daysText = '';

            if (unit === 'days') {
                end.setDate(start.getDate() + duration);
                daysText = `${duration} ngày`;
            } else if (unit === 'months') {
                end.setMonth(start.getMonth() + duration);
                const days = Math.round((end - start) / (1000 * 60 * 60 * 24));
                daysText = `${duration} tháng (~${days} ngày)`;
            } else if (unit === 'years') {
                end.setFullYear(start.getFullYear() + duration);
                const days = Math.round((end - start) / (1000 * 60 * 60 * 24));
                daysText = `${duration} năm (~${days} ngày)`;
            }

            const formattedDate = end.toISOString().split('T')[0];
            bulkEndDate.value = formattedDate;

            const formattedDisplay = end.toLocaleDateString('vi-VN', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            bulkDurationInfo.innerHTML = `<i class="fas fa-check-circle me-1 text-success"></i>Thời hạn: ${daysText} → Hết hạn: <strong>${formattedDisplay}</strong>`;
        }

        bulkDuration.addEventListener('input', calculateBulkEndDate);
        bulkDurationUnit.addEventListener('change', calculateBulkEndDate);
        bulkStartDate.addEventListener('change', calculateBulkEndDate);
    }
});
</script>
@endsection

