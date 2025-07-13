@extends('layouts.admin')

@section('title', 'Quản lý khách hàng')
@section('page-title', 'Quản lý khách hàng')

@push('styles')
<style>
    /* Optimized customer table styles */
    .customers-table {
        font-size: 0.875rem;
        min-width: 1000px;
    }

    .customers-table th,
    .customers-table td {
        padding: 0.75rem 0.5rem;
        vertical-align: middle;
    }

    .customers-table .btn-group {
        display: flex;
        gap: 0.25rem;
        justify-content: center;
    }

    .customers-table .btn-group .btn {
        padding: 0.375rem 0.5rem;
        font-size: 0.75rem;
    }

    .avatar-initial {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 600;
        font-size: 0.875rem;
    }

    @media (max-width: 1200px) {
        .customers-table th:nth-child(3),
        .customers-table td:nth-child(3) {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .customers-table th:nth-child(5),
        .customers-table td:nth-child(5) {
            display: none;
        }

        .customers-table .btn-group .btn {
            padding: 0.25rem 0.375rem;
            font-size: 0.7rem;
        }
    }
</style>
@endpush

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold text-white">Quản lý khách hàng</h5>
                        <small class="text-light opacity-75">Danh sách và quản lý thông tin khách hàng</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light btn-sm shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#quickAddModal">
                            <i class="fas fa-plus me-1"></i> Thêm nhanh
                        </button>
                        <a href="{{ route('admin.customers.create', ['page' => request('page', 1), 'search' => request('search')]) }}"
                            class="btn btn-success btn-sm shadow-sm fw-bold">
                            <i class="fas fa-user-plus me-1"></i> Thêm khách hàng
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Enhanced Filters -->
                <div class="card border-0 bg-light mb-2">
                    <div class="card-body">
                        <form method="GET" class="row g-2 align-items-end">
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-search text-primary"></i> Tìm kiếm
                                </label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Tên, email, phone, mã KH..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-box text-success"></i> Gói dịch vụ
                                </label>
                                <select name="service_package_id" class="form-select">
                                    <option value="">Tất cả gói dịch vụ</option>
                                    @foreach($servicePackages as $package)
                                    <option value="{{ $package->id }}"
                                        {{ request('service_package_id') == $package->id ? 'selected' : '' }}>
                                        {{ $package->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-toggle-on text-info"></i> Trạng thái
                                </label>
                                <select name="service_status" class="form-select">
                                    <option value="">Tất cả</option>
                                    <option value="active"
                                        {{ request('service_status') === 'active' ? 'selected' : '' }}>
                                        Hoạt động
                                    </option>
                                    <option value="expired"
                                        {{ request('service_status') === 'expired' ? 'selected' : '' }}>
                                        Hết hạn
                                    </option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <div class="d-grid gap-1">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-search"></i> Lọc
                                    </button>
                                    @if(request()->hasAny(['search', 'service_package_id', 'service_status', 'date_from', 'date_to']))
                                    <a href="{{ route('admin.customers.index') }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-times"></i> Xóa bộ lọc
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-1 mb-2">
                    <a href="{{ route('admin.customers.index', ['date_from' => now()->startOfMonth()->format('Y-m-d'), 'date_to' => now()->endOfMonth()->format('Y-m-d')]) }}"
                        class="btn btn-sm {{ request('date_from') === now()->startOfMonth()->format('Y-m-d') && request('date_to') === now()->endOfMonth()->format('Y-m-d') ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-calendar"></i> Tháng này
                    </a>
                    <a href="{{ route('admin.customers.index', ['service_status' => 'active']) }}"
                        class="btn btn-sm {{ request('service_status') === 'active' ? 'btn-success' : 'btn-outline-success' }}">
                        <i class="fas fa-check-circle"></i> DV hoạt động
                    </a>
                    <a href="{{ route('admin.customers.index', ['service_status' => 'expired']) }}"
                        class="btn btn-sm {{ request('service_status') === 'expired' ? 'btn-danger' : 'btn-outline-danger' }}">
                        <i class="fas fa-times-circle"></i> DV hết hạn
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Results Info với thông tin filter -->
<div class="row mb-2">
    <div class="col-12">
        <div class="card border-0 bg-light">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <i class="fas fa-info-circle text-primary"></i>
                        <strong class="text-dark">{{ number_format($customers->total()) }}</strong> khách hàng
                        @if($customers->total() > 0)
                        ({{ $customers->firstItem() }}-{{ $customers->lastItem() }})
                        @endif
                        
                        @if(request()->hasAny(['search', 'service_package_id', 'service_status', 'date_from', 'date_to']))
                        <div class="mt-1">
                            <small class="text-info">
                                <i class="fas fa-filter"></i> Đang lọc: 
                                @if(request('search'))
                                    <span class="badge bg-secondary">Tìm: {{ request('search') }}</span>
                                @endif
                                @if(request('service_package_id'))
                                    @php
                                        $selectedPackage = $servicePackages->find(request('service_package_id'));
                                    @endphp
                                    <span class="badge bg-success">Gói: {{ $selectedPackage ? $selectedPackage->name : 'Không xác định' }}</span>
                                @endif
                                @if(request('service_status'))
                                    <span class="badge bg-info">Trạng thái: {{ request('service_status') === 'active' ? 'Hoạt động' : 'Hết hạn' }}</span>
                                @endif
                                @if(request('date_from') || request('date_to'))
                                    <span class="badge bg-warning">
                                        Ngày: {{ request('date_from') ?? '...' }} → {{ request('date_to') ?? '...' }}
                                    </span>
                                @endif
                            </small>
                        </div>
                        @endif
                    </div>
                    @if($customers->total() > 0)
                    <small class="text-muted">
                        <i class="fas fa-clock"></i> {{ now()->format('H:i d/m/Y') }}
                    </small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customers Table -->
@if($customers->count() > 0)
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 customers-table">
                <thead>
                    <tr>
                        <th>Mã KH</th>
                        <th>Khách hàng</th>
                        <th class="d-none d-lg-table-cell">Liên hệ</th>
                        <th class="text-center">Dịch vụ</th>
                        <th class="d-none d-xl-table-cell">Ngày tạo</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr style="border-bottom: 1px solid #e9ecef;">
                        <td class="py-2 px-2">
                            <span class="badge bg-primary px-1 py-1 small">{{ $customer->customer_code }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-initial bg-primary text-white me-2">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $customer->name }}</div>
                                    <div class="d-lg-none text-muted small">
                                        @if($customer->email)
                                        <div><i class="fas fa-envelope me-1"></i>{{ Str::limit($customer->email, 20) }}</div>
                                        @endif
                                        @if($customer->phone)
                                        <div><i class="fas fa-phone me-1"></i>{{ $customer->phone }}</div>
                                        @endif
                                        <div class="d-xl-none">
                                            <span class="badge bg-light text-dark">{{ $customer->created_at->format('d/m/y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-2 px-2 d-none d-lg-table-cell">
                            <div class="small">
                                @if($customer->email)
                                <div class="mb-1 text-truncate" title="{{ $customer->email }}">
                                    <a href="mailto:{{ $customer->email }}" class="text-decoration-none text-primary">
                                        <i class="fas fa-envelope me-1"></i>{{ Str::limit($customer->email, 25) }}
                                    </a>
                                </div>
                                @endif
                                @if($customer->phone)
                                <div>
                                    <a href="tel:{{ $customer->phone }}" class="text-decoration-none text-success">
                                        <i class="fas fa-phone me-1"></i>{{ $customer->phone }}
                                    </a>
                                </div>
                                @endif
                                @if(!$customer->email && !$customer->phone)
                                <span class="text-muted small">— Chưa có</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-2 px-2 text-center">
                            @php
                            $serviceCount = $customer->customerServices->count();
                            $activeServices = $customer->customerServices->where('status', 'active')->count();
                            $expiredServices = $customer->customerServices->where('status', 'expired')->count();
                            $loginEmails = $customer->customerServices->whereNotNull('login_email')->pluck('login_email')->unique();
                            @endphp
                            <div class="d-flex flex-column align-items-center gap-1">
                                @if($serviceCount > 0)
                                <div class="d-flex gap-1">
                                    <span class="badge bg-primary px-1 py-1 small">
                                        {{ $serviceCount }}
                                    </span>
                                    @if($activeServices > 0)
                                    <span class="badge bg-success px-1 py-1 small">
                                        {{ $activeServices }}
                                    </span>
                                    @endif
                                    @if($expiredServices > 0)
                                    <span class="badge bg-danger px-1 py-1 small">
                                        {{ $expiredServices }}
                                    </span>
                                    @endif
                                </div>
                                @else
                                <span class="badge bg-secondary px-1 py-1 small">0</span>
                                @endif
                                @if($loginEmails->count() > 0)
                                <div class="mt-1">
                                    @foreach($loginEmails->take(1) as $loginEmail)
                                    <small class="text-muted" title="{{ $loginEmail }}">
                                        <i class="fas fa-envelope text-warning"></i>
                                    </small>
                                    @endforeach
                                    @if($loginEmails->count() > 1)
                                    <small class="text-muted">+{{ $loginEmails->count() - 1 }}</small>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="py-2 px-2 d-none d-xl-table-cell">
                            <div class="small text-center">
                                <div class="fw-semibold text-primary">{{ $customer->created_at->format('d/m') }}</div>
                                <div class="text-muted">{{ $customer->created_at->format('H:i') }}</div>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.customers.show', $customer) }}"
                                    class="btn btn-outline-info btn-sm" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.customers.edit', $customer) }}"
                                    class="btn btn-outline-warning btn-sm" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.customers.assign-service', $customer) }}"
                                    class="btn btn-outline-success btn-sm" title="Gán dịch vụ">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <button type="button"
                                    class="btn btn-outline-danger btn-sm delete-btn"
                                    title="Xóa khách hàng"
                                    data-customer-name="{{ $customer->name }}"
                                    data-delete-url="{{ route('admin.customers.destroy', $customer) }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 bg-light">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="fs-6 text-muted d-none d-md-block">
                        <i class="fas fa-list me-2 text-primary"></i>
                        Hiển thị {{ $customers->firstItem() ?? 0 }} - {{ $customers->lastItem() ?? 0 }} trong tổng số {{ number_format($customers->total()) }} khách hàng
                    </div>
                    <div class="ms-auto">
                        <div class="pagination-wrapper" style="font-size: 1.1rem;">
                            {{ $customers->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-users fa-5x text-muted opacity-50"></i>
                </div>
                <h4 class="text-muted mb-3">Không tìm thấy khách hàng nào</h4>
                <p class="text-muted fs-5 mb-4">
                    @if(request()->hasAny(['search', 'service_package_id', 'service_status', 'login_email', 'date_from', 'date_to']))
                    Không có khách hàng nào khớp với tiêu chí tìm kiếm của bạn.
                    @else
                    Hệ thống chưa có khách hàng nào. Hãy thêm khách hàng đầu tiên!
                    @endif
                </p>
                <div class="d-flex gap-3 justify-content-center">
                    @if(request()->hasAny(['search', 'service_package_id', 'service_status', 'login_email', 'date_from', 'date_to']))
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-refresh me-2"></i>Xem tất cả
                    </a>
                    @endif
                    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Thêm khách hàng mới
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Add Customer Modal -->
    <div class="modal fade" id="quickAddModal" tabindex="-1" aria-labelledby="quickAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-4">
                    <h4 class="modal-title fw-bold" id="quickAddModalLabel">
                        <i class="fas fa-user-plus me-3"></i>
                        Thêm khách hàng nhanh
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="quickAddForm" method="POST" action="{{ route('admin.customers.store') }}">
                    @csrf
                    <input type="hidden" name="return_page" value="{{ request('page', 1) }}">
                    <input type="hidden" name="return_search" value="{{ request('search') }}">

                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label for="quick_name" class="form-label fw-semibold fs-5">
                                <i class="fas fa-user me-2 text-primary"></i>
                                Tên khách hàng <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="quick_name" name="name" required
                                placeholder="Nhập tên đầy đủ của khách hàng">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="quick_email" class="form-label fw-semibold fs-6">
                                    <i class="fas fa-envelope me-2 text-info"></i>Email
                                </label>
                                <input type="email" class="form-control form-control-lg" id="quick_email" name="email"
                                    placeholder="email@example.com">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="quick_phone" class="form-label fw-semibold fs-6">
                                    <i class="fas fa-phone me-2 text-success"></i>Số điện thoại
                                </label>
                                <input type="text" class="form-control form-control-lg" id="quick_phone" name="phone"
                                    placeholder="0xxx xxx xxx">
                            </div>
                        </div>
                        <div class="alert alert-info border-0 bg-light-info">
                            <i class="fas fa-info-circle me-2 text-primary"></i>
                            <span class="fs-6">Mã khách hàng sẽ được tự động tạo theo định dạng KUN#####</span>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4">
                        <button type="button" class="btn btn-outline-secondary btn-lg px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>
                            Hủy
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            <i class="fas fa-save me-2"></i>
                            Lưu khách hàng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white py-4">
                    <h4 class="modal-title fw-bold" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle me-3"></i>
                        Xác nhận xóa khách hàng
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-user-times fa-5x text-danger mb-4"></i>
                        <h5 class="mb-3">Bạn có chắc chắn muốn xóa khách hàng:</h5>
                        <p class="fs-4 fw-bold text-primary mb-4" id="customerNameToDelete"></p>
                    </div>
                    <div class="alert alert-warning border-0 bg-light-warning">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                        <span class="fs-6 fw-semibold">Cảnh báo: Hành động này không thể hoàn tác!</span>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center p-4">
                    <button type="button" class="btn btn-outline-secondary btn-lg px-4 me-3" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        Hủy bỏ
                    </button>
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-lg px-4">
                            <i class="fas fa-trash me-2"></i>
                            Xóa khách hàng
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection



    @section('scripts')
    <script>
        // Confirm delete function - global scope
        function confirmDelete(customerName, deleteUrl) {
            document.getElementById('customerNameToDelete').textContent = customerName;
            document.getElementById('deleteForm').action = deleteUrl;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Handle delete buttons
            document.querySelectorAll('.delete-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const customerName = this.getAttribute('data-customer-name');
                    const deleteUrl = this.getAttribute('data-delete-url');
                    confirmDelete(customerName, deleteUrl);
                });
            });

            // Quick add form validation
            const quickAddForm = document.getElementById('quickAddForm');
            if (quickAddForm) {
                quickAddForm.addEventListener('submit', function(e) {
                    const nameInput = document.getElementById('quick_name');
                    if (!nameInput.value.trim()) {
                        e.preventDefault();
                        nameInput.focus();
                        nameInput.classList.add('is-invalid');
                    }
                });
            }

            // THÊM: Filter improvement và thông báo khi không có kết quả
            const servicePackageSelect = document.querySelector('select[name="service_package_id"]');
            const serviceStatusSelect = document.querySelector('select[name="service_status"]');
            const searchInput = document.querySelector('input[name="search"]');
            
            // Hiển thị thông báo hữu ích khi filter không có kết quả
            const customerCount = {{ $customers->total() }};
            if (customerCount === 0) {
                const hasFilters = {{ request()->hasAny(['search', 'service_package_id', 'service_status', 'date_from', 'date_to']) ? 'true' : 'false' }};
                if (hasFilters) {
                    // Tạo thông báo gợi ý
                    const emptyMessage = document.querySelector('.text-muted');
                    if (emptyMessage && emptyMessage.textContent.includes('Không có khách hàng nào')) {
                        const suggestionBox = document.createElement('div');
                        suggestionBox.className = 'alert alert-info mt-3';
                        suggestionBox.innerHTML = `
                            <h6><i class="fas fa-lightbulb"></i> Gợi ý:</h6>
                            <ul class="mb-0">
                                <li>Thử <strong>xóa bộ lọc</strong> để xem tất cả khách hàng</li>
                                <li>Kiểm tra lại <strong>tên gói dịch vụ</strong> hoặc <strong>trạng thái</strong></li>
                                <li>Thử tìm kiếm với <strong>từ khóa khác</strong></li>
                            </ul>
                            <div class="mt-2">
                                <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-refresh"></i> Xem tất cả khách hàng
                                </a>
                            </div>
                        `;
                        emptyMessage.parentNode.appendChild(suggestionBox);
                    }
                }
            }

            // Auto-submit khi thay đổi filter (optional - có thể bỏ comment nếu muốn)
            /*
            if (servicePackageSelect) {
                servicePackageSelect.addEventListener('change', function() {
                    if (this.value !== '') {
                        this.form.submit();
                    }
                });
            }
            if (serviceStatusSelect) {
                serviceStatusSelect.addEventListener('change', function() {
                    if (this.value !== '') {
                        this.form.submit();
                    }
                });
            }
            */
        });
    </script>
@endsection