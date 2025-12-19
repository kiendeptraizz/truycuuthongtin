@extends('layouts.admin')

@section('title', 'Tài nguyên: ' . $resource->name)

@section('page-title', $resource->name)

@section('styles')
<style>
    .password-cell {
        font-family: 'Courier New', monospace;
        position: relative;
    }

    .password-hidden {
        filter: blur(4px);
        user-select: none;
    }

    .password-visible {
        filter: none;
    }

    .copy-btn {
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s;
    }

    .copy-btn:hover {
        opacity: 1;
    }

    .account-row:hover {
        background-color: #f8f9fa !important;
    }

    .days-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .subcategory-badge {
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 4px;
    }

    .subcategory-item {
        cursor: pointer;
        transition: all 0.2s;
    }

    .subcategory-item:hover {
        background-color: #f8f9fa;
    }

    .subcategory-item.active {
        background-color: #e7f1ff;
        border-left: 3px solid #0d6efd;
    }
    .bulk-actions-bar {
        border: 1px solid #dee2e6;
        animation: slideDown 0.2s ease-out;
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .account-checkbox {
        cursor: pointer;
        transform: scale(1.1);
    }
    #selectAll {
        cursor: pointer;
        transform: scale(1.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Import Errors -->
    @if(session('import_errors'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Một số dòng không thể nhập:</h6>
        <ul class="mb-0 small">
            @foreach(session('import_errors') as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                @if($resource->icon)
                <i class="{{ $resource->icon }} me-2 text-{{ $resource->color ?? 'primary' }}"></i>
                @else
                <i class="fas fa-folder me-2 text-{{ $resource->color ?? 'primary' }}"></i>
                @endif
                {{ $resource->name }}
            </h4>
            @if($resource->description)
            <p class="text-muted mb-0">{{ $resource->description }}</p>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.resources.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
            <a href="{{ route('admin.resources.accounts.bulk-import-form', $resource) }}" class="btn btn-success">
                <i class="fas fa-upload me-1"></i> Nhập hàng loạt
            </a>
            <a href="{{ route('admin.resources.accounts.create', $resource) }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Thêm tài khoản
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-2 col-sm-4 col-6 mb-3">
            <div class="card bg-light h-100">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0">{{ $categoryStats['total'] }}</h4>
                    <small class="text-muted">Tổng</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0">{{ $categoryStats['available'] }}</h4>
                    <small>Khả dụng</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0">{{ $categoryStats['active'] }}</h4>
                    <small>Đang hoạt động</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0">{{ $categoryStats['sold'] }}</h4>
                    <small>Đã bán</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6 mb-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0">{{ $categoryStats['expired'] }}</h4>
                    <small>Hết hạn</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0">{{ $categoryStats['expiring_soon'] }}</h4>
                    <small>Sắp hết hạn</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Subcategories Card -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-tags me-2"></i>Danh mục con</h6>
            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#subcategoryModal">
                <i class="fas fa-plus me-1"></i> Thêm
            </button>
        </div>
        <div class="card-body py-2">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.resources.show', $resource) }}"
                    class="btn btn-sm {{ !request('subcategory') ? 'btn-primary' : 'btn-outline-secondary' }}">
                    Tất cả ({{ $categoryStats['total'] }})
                </a>
                <a href="{{ route('admin.resources.show', ['resource' => $resource, 'subcategory' => 'none']) }}"
                    class="btn btn-sm {{ request('subcategory') === 'none' ? 'btn-warning' : 'btn-outline-warning' }}">
                    Chưa phân loại
                </a>
                @foreach($subcategories as $sub)
                <a href="{{ route('admin.resources.show', ['resource' => $resource, 'subcategory' => $sub->id]) }}"
                    class="btn btn-sm {{ request('subcategory') == $sub->id ? 'btn-'.$sub->color : 'btn-outline-'.$sub->color }}">
                    {{ $sub->name }}
                    <span class="badge bg-light text-dark ms-1">{{ $sub->accounts_count ?? $sub->accounts()->count() }}</span>
                    <i class="fas fa-edit ms-1 edit-subcategory" data-id="{{ $sub->id }}" data-name="{{ $sub->name }}"
                        data-color="{{ $sub->color }}" data-description="{{ $sub->description }}"
                        onclick="event.preventDefault(); editSubcategory(this);" title="Sửa"></i>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-key me-2"></i>Danh sách tài khoản
            </h5>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control"
                            placeholder="Tìm email, username, ghi chú..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="subcategory" class="form-select">
                            <option value="">-- Danh mục con --</option>
                            <option value="none" {{ request('subcategory') === 'none' ? 'selected' : '' }}>Chưa phân loại</option>
                            @foreach($subcategories as $sub)
                            <option value="{{ $sub->id }}" {{ request('subcategory') == $sub->id ? 'selected' : '' }}>
                                {{ $sub->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">-- Trạng thái --</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Hết hạn</option>
                            <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Đã bán</option>
                            <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Đã đặt trước</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Tạm ngưng</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="available" class="form-select">
                            <option value="">-- Khả dụng --</option>
                            <option value="1" {{ request('available') === '1' ? 'selected' : '' }}>Còn khả dụng</option>
                            <option value="0" {{ request('available') === '0' ? 'selected' : '' }}>Không khả dụng</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-filter me-1"></i> Lọc
                        </button>
                        @if(request()->hasAny(['search', 'status', 'available', 'subcategory']))
                        <a href="{{ route('admin.resources.show', $resource) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Xóa lọc
                        </a>
                        @endif
                        <button type="button" class="btn btn-outline-info ms-2" onclick="toggleAllPasswords()">
                            <i class="fas fa-eye me-1"></i>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Bulk Actions Bar -->
            <div class="bulk-actions-bar bg-light p-3 rounded mb-3" id="bulkActionsBar" style="display: none;">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <span class="fw-bold"><span id="selectedCount">0</span> tài khoản đã chọn</span>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        @if($subcategories->count() > 0)
                        <div class="input-group input-group-sm" style="width: auto;">
                            <select class="form-select form-select-sm" id="bulkSubcategory" style="width: 150px;">
                                <option value="">-- Chuyển danh mục --</option>
                                <option value="">Chưa phân loại</option>
                                @foreach($subcategories as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="bulkChangeSubcategory()">
                                <i class="fas fa-tag"></i>
                            </button>
                        </div>
                        @endif
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="bulkAction('mark_sold')">
                            <i class="fas fa-shopping-cart me-1"></i> Đánh dấu đã bán
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="bulkAction('mark_available')">
                            <i class="fas fa-check me-1"></i> Khả dụng
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="bulkAction('mark_unavailable')">
                            <i class="fas fa-times me-1"></i> Không khả dụng
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete()">
                            <i class="fas fa-trash me-1"></i> Xóa đã chọn
                        </button>
                    </div>
                </div>
            </div>

            <!-- Accounts Table -->
            @if($accounts->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="selectAll" onclick="toggleSelectAll()">
                            </th>
                            <th style="width: 50px;">#</th>
                            <th>Tài khoản</th>
                            <th>Mật khẩu</th>
                            <th>2FA</th>
                            <th>Phân loại</th>
                            <th>Thời hạn</th>
                            <th>Trạng thái</th>
                            <th style="width: 150px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $index => $account)
                        <tr class="account-row">
                            <td>
                                <input type="checkbox" class="form-check-input account-checkbox" 
                                       value="{{ $account->id }}" onchange="updateSelectedCount()">
                            </td>
                            <td class="text-muted">{{ $accounts->firstItem() + $index }}</td>
                            <td>
                                <div>
                                    @if($account->name)
                                    <strong>{{ $account->name }}</strong><br>
                                    @endif
                                    @if($account->email)
                                    <span class="text-primary">
                                        <i class="fas fa-envelope me-1"></i>{{ $account->email }}
                                        <i class="fas fa-copy copy-btn ms-1" onclick="copyToClipboard('{{ $account->email }}')" title="Copy"></i>
                                    </span><br>
                                    @endif
                                    @if($account->username)
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>{{ $account->username }}
                                        <i class="fas fa-copy copy-btn ms-1" onclick="copyToClipboard('{{ $account->username }}')" title="Copy"></i>
                                    </small>
                                    @endif
                                </div>
                            </td>
                            <td class="password-cell">
                                @if($account->password)
                                <span class="password-text password-hidden" data-password="{{ $account->password }}">{{ $account->password }}</span>
                                <i class="fas fa-copy copy-btn ms-1" onclick="copyToClipboard('{{ $account->password }}')" title="Copy"></i>
                                <i class="fas fa-eye copy-btn ms-1 toggle-password" onclick="togglePassword(this)" title="Hiện/Ẩn"></i>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="password-cell">
                                @if($account->two_factor_secret)
                                <span class="password-text password-hidden" data-password="{{ $account->two_factor_secret }}">{{ Str::limit($account->two_factor_secret, 20) }}</span>
                                <i class="fas fa-copy copy-btn ms-1" onclick="copyToClipboard('{{ $account->two_factor_secret }}')" title="Copy"></i>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($account->subcategory)
                                <span class="badge bg-{{ $account->subcategory->color ?? 'secondary' }} subcategory-badge">
                                    {{ $account->subcategory->name }}
                                </span>
                                @else
                                <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($account->start_date || $account->end_date)
                                <small>
                                    @if($account->start_date)
                                    <i class="fas fa-play text-success me-1"></i>{{ $account->start_date->format('d/m/Y') }}<br>
                                    @endif
                                    @if($account->end_date)
                                    <i class="fas fa-stop text-danger me-1"></i>{{ $account->end_date->format('d/m/Y') }}
                                    @if($account->days_remaining !== null)
                                    @if($account->days_remaining <= 0)
                                        <span class="badge bg-danger days-badge ms-1">Hết hạn</span>
                                        @elseif($account->days_remaining <= 7)
                                            <span class="badge bg-warning days-badge ms-1">{{ $account->days_remaining }} ngày</span>
                                            @else
                                            <span class="badge bg-success days-badge ms-1">{{ $account->days_remaining }} ngày</span>
                                            @endif
                                            @endif
                                            @endif
                                </small>
                                @else
                                <span class="text-muted">Không giới hạn</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $account->status_badge_class }}">
                                    {{ $account->status_label }}
                                </span>
                                @if($account->is_available)
                                <span class="badge bg-success-subtle text-success ms-1">
                                    <i class="fas fa-check"></i>
                                </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.resources.accounts.edit', [$resource, $account]) }}"
                                        class="btn btn-outline-primary" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if($account->status !== 'sold')
                                    <form method="POST"
                                        action="{{ route('admin.resources.accounts.mark-sold', [$resource, $account]) }}"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-info" title="Đánh dấu đã bán"
                                            onclick="return confirm('Đánh dấu tài khoản này là đã bán?')">
                                            <i class="fas fa-shopping-cart"></i>
                                        </button>
                                    </form>
                                    @endif

                                    <form method="POST"
                                        action="{{ route('admin.resources.accounts.toggle', [$resource, $account]) }}"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-{{ $account->is_available ? 'warning' : 'success' }}"
                                            title="{{ $account->is_available ? 'Đánh dấu không khả dụng' : 'Đánh dấu khả dụng' }}">
                                            <i class="fas fa-{{ $account->is_available ? 'times' : 'check' }}"></i>
                                        </button>
                                    </form>

                                    <form method="POST"
                                        action="{{ route('admin.resources.accounts.destroy', [$resource, $account]) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @if($account->notes)
                        <tr class="account-row">
                            <td></td>
                            <td></td>
                            <td colspan="7">
                                <small class="text-muted">
                                    <i class="fas fa-sticky-note me-1"></i>{{ $account->notes }}
                                </small>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($accounts->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $accounts->appends(request()->query())->links() }}
            </div>
            @endif
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Chưa có tài khoản nào</h5>
                <p class="text-muted">Thêm tài khoản đầu tiên vào danh mục này</p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('admin.resources.accounts.bulk-import-form', $resource) }}" class="btn btn-success">
                        <i class="fas fa-upload me-1"></i> Nhập hàng loạt
                    </a>
                    <a href="{{ route('admin.resources.accounts.create', $resource) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Thêm tài khoản
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Copy to clipboard
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            // Show toast notification
            const toast = document.createElement('div');
            toast.className = 'position-fixed bottom-0 end-0 p-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
            <div class="toast show bg-success text-white" role="alert">
                <div class="toast-body">
                    <i class="fas fa-check me-2"></i>Đã copy!
                </div>
            </div>
        `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        });
    }

    // Toggle single password visibility
    function togglePassword(btn) {
        const passwordSpan = btn.parentElement.querySelector('.password-text');
        if (passwordSpan.classList.contains('password-hidden')) {
            passwordSpan.classList.remove('password-hidden');
            passwordSpan.classList.add('password-visible');
            btn.classList.remove('fa-eye');
            btn.classList.add('fa-eye-slash');
        } else {
            passwordSpan.classList.add('password-hidden');
            passwordSpan.classList.remove('password-visible');
            btn.classList.add('fa-eye');
            btn.classList.remove('fa-eye-slash');
        }
    }

    // Toggle all passwords
    let allPasswordsVisible = false;

    function toggleAllPasswords() {
        allPasswordsVisible = !allPasswordsVisible;
        document.querySelectorAll('.password-text').forEach(span => {
            if (allPasswordsVisible) {
                span.classList.remove('password-hidden');
                span.classList.add('password-visible');
            } else {
                span.classList.add('password-hidden');
                span.classList.remove('password-visible');
            }
        });

        document.querySelectorAll('.toggle-password').forEach(btn => {
            if (allPasswordsVisible) {
                btn.classList.remove('fa-eye');
                btn.classList.add('fa-eye-slash');
            } else {
                btn.classList.add('fa-eye');
                btn.classList.remove('fa-eye-slash');
            }
        });
    }

    // =========== BULK ACTIONS ===========
    function getSelectedIds() {
        const checkboxes = document.querySelectorAll('.account-checkbox:checked');
        return Array.from(checkboxes).map(cb => cb.value);
    }

    function updateSelectedCount() {
        const count = getSelectedIds().length;
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('bulkActionsBar').style.display = count > 0 ? 'block' : 'none';
        
        // Update select all checkbox state
        const allCheckboxes = document.querySelectorAll('.account-checkbox');
        const selectAll = document.getElementById('selectAll');
        if (allCheckboxes.length > 0) {
            selectAll.checked = count === allCheckboxes.length;
            selectAll.indeterminate = count > 0 && count < allCheckboxes.length;
        }
    }

    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.account-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateSelectedCount();
    }

    function bulkDelete() {
        const ids = getSelectedIds();
        if (ids.length === 0) {
            alert('Vui lòng chọn ít nhất một tài khoản!');
            return;
        }
        
        if (!confirm(`Bạn có chắc chắn muốn xóa ${ids.length} tài khoản đã chọn?\n\nHành động này không thể hoàn tác!`)) {
            return;
        }

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.resources.accounts.bulk-delete", $resource) }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'account_ids[]';
            input.value = id;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }

    function bulkAction(action) {
        const ids = getSelectedIds();
        if (ids.length === 0) {
            alert('Vui lòng chọn ít nhất một tài khoản!');
            return;
        }

        const actionTexts = {
            'mark_sold': 'đánh dấu đã bán',
            'mark_available': 'đánh dấu khả dụng',
            'mark_unavailable': 'đánh dấu không khả dụng'
        };

        if (!confirm(`Bạn có chắc chắn muốn ${actionTexts[action]} ${ids.length} tài khoản đã chọn?`)) {
            return;
        }

        submitBulkUpdate(ids, action);
    }

    function bulkChangeSubcategory() {
        const ids = getSelectedIds();
        if (ids.length === 0) {
            alert('Vui lòng chọn ít nhất một tài khoản!');
            return;
        }

        const subcategoryId = document.getElementById('bulkSubcategory').value;
        const subcategoryText = document.getElementById('bulkSubcategory').selectedOptions[0].text;

        if (!confirm(`Chuyển ${ids.length} tài khoản sang "${subcategoryText}"?`)) {
            return;
        }

        submitBulkUpdate(ids, 'change_subcategory', subcategoryId);
    }

    function submitBulkUpdate(ids, action, subcategoryId = null) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.resources.accounts.bulk-update", $resource) }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        form.appendChild(actionInput);

        if (subcategoryId !== null) {
            const subInput = document.createElement('input');
            subInput.type = 'hidden';
            subInput.name = 'resource_subcategory_id';
            subInput.value = subcategoryId;
            form.appendChild(subInput);
        }

        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'account_ids[]';
            input.value = id;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }

    // Subcategory management
    function editSubcategory(el) {
        document.getElementById('subcategory_id').value = el.dataset.id;
        document.getElementById('subcategory_name').value = el.dataset.name;
        document.getElementById('subcategory_color').value = el.dataset.color || 'secondary';
        document.getElementById('subcategory_description').value = el.dataset.description || '';
        document.getElementById('subcategoryModalLabel').textContent = 'Sửa danh mục con';
        document.getElementById('deleteSubcategoryBtn').style.display = 'inline-block';
        new bootstrap.Modal(document.getElementById('subcategoryModal')).show();
    }

    function saveSubcategory() {
        const id = document.getElementById('subcategory_id').value;
        const name = document.getElementById('subcategory_name').value;
        const color = document.getElementById('subcategory_color').value;
        const description = document.getElementById('subcategory_description').value;

        if (!name.trim()) {
            alert('Vui lòng nhập tên danh mục con!');
            return;
        }

        const url = id ?
            `{{ route('admin.resources.subcategories.store', $resource) }}/${id}`.replace('/subcategories/', '/subcategories/') :
            `{{ route('admin.resources.subcategories.store', $resource) }}`;

        const method = id ? 'PUT' : 'POST';

        fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name,
                    color,
                    description
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra!');
            });
    }

    function deleteSubcategory() {
        const id = document.getElementById('subcategory_id').value;
        if (!id) return;

        if (!confirm('Xóa danh mục con này? Các tài khoản sẽ được chuyển về "Chưa phân loại".')) {
            return;
        }

        fetch(`{{ route('admin.resources.subcategories.store', $resource) }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra!');
            });
    }

    // Reset modal khi mở để thêm mới
    document.getElementById('subcategoryModal').addEventListener('show.bs.modal', function(event) {
        if (!event.relatedTarget || !event.relatedTarget.classList.contains('edit-subcategory')) {
            document.getElementById('subcategory_id').value = '';
            document.getElementById('subcategory_name').value = '';
            document.getElementById('subcategory_color').value = 'secondary';
            document.getElementById('subcategory_description').value = '';
            document.getElementById('subcategoryModalLabel').textContent = 'Thêm danh mục con';
            document.getElementById('deleteSubcategoryBtn').style.display = 'none';
        }
    });
</script>

<!-- Subcategory Modal -->
<div class="modal fade" id="subcategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subcategoryModalLabel">Thêm danh mục con</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="subcategory_id">
                <div class="mb-3">
                    <label class="form-label">Tên danh mục con <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="subcategory_name" placeholder="VD: Tài khoản dùng chung, Code...">
                </div>
                <div class="mb-3">
                    <label class="form-label">Màu sắc</label>
                    <select class="form-select" id="subcategory_color">
                        <option value="primary">🔵 Primary</option>
                        <option value="secondary" selected>⚫ Secondary</option>
                        <option value="success">🟢 Success</option>
                        <option value="danger">🔴 Danger</option>
                        <option value="warning">🟡 Warning</option>
                        <option value="info">🔷 Info</option>
                        <option value="dark">⬛ Dark</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea class="form-control" id="subcategory_description" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" id="deleteSubcategoryBtn" onclick="deleteSubcategory()" style="display: none;">
                    <i class="fas fa-trash me-1"></i> Xóa
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="saveSubcategory()">
                    <i class="fas fa-save me-1"></i> Lưu
                </button>
            </div>
        </div>
    </div>
</div>
@endsection