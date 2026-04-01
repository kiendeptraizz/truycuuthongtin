@extends('layouts.admin')

@section('titl @media (max-width: 768px) {
.customers-table th:nth-child(5),
.customers-table td:nth-child(5) {
display: none;
}
}

/* Icon fallback styles */
.btn .fas, .btn .fa {
display: inline-block;
width: 1em;
text-align: center;
font-size: 14px;
}

/* Khi Font Awesome load được, ẩn emoji */
.fas:before, .fa:before {
font-family: "Font Awesome 6 Free";
font-weight: 900;
}

/* Fallback: hiện emoji khi Font Awesome không load */
.fas, .fa {
font-family: "Font Awesome 6 Free", "Apple Color Emoji", "Segoe UI Emoji", sans-serif;
}

/* Đảm bảo icons hiển thị chính xác */
.fas.fa-eye:before { content: "\f06e"; }
.fas.fa-edit:before { content: "\f044"; }
.fas.fa-plus:before { content: "\f067"; }
.fas.fa-trash:before { content: "\f2ed"; }

/* Ensure icons are visible */
.btn-group .btn {
min-width: 32px;
display: inline-flex;
align-items: center;
justify-content: center;
}uản lý khách hàng')
@section('page-title', 'Quản lý khách hàng')

@section('styles')
<style>
    /* Searchable Dropdown Portal - Fixed position outside all containers */
    .service-dropdown-portal {
        display: none;
        position: fixed;
        z-index: 999999;
        background: #fff;
        border: 1px solid rgba(102, 126, 234, 0.3);
        border-radius: 12px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        max-height: 400px;
        overflow-y: auto;
        padding: 10px 0;
    }

    .service-dropdown-portal .dropdown-item {
        padding: 12px 20px;
        cursor: pointer;
        display: block;
        color: #333;
        text-decoration: none;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .service-dropdown-portal .dropdown-item:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        color: #667eea;
        border-left-color: #667eea;
    }

    .service-dropdown-portal .dropdown-item.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-left-color: white;
    }

    .service-dropdown-portal .dropdown-item.active i {
        color: white !important;
    }

    .service-dropdown-portal .dropdown-item.highlighted {
        background: rgba(102, 126, 234, 0.1);
    }

    .service-dropdown-portal .dropdown-divider {
        margin: 8px 15px;
        border-color: #eee;
    }

    #servicePackageSearch {
        border-radius: 10px;
        border: 2px solid #e0e0e0;
        padding-left: 15px;
        transition: all 0.3s ease;
        background: white;
    }

    #servicePackageSearch:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
        background: white;
    }

    /* Optimized customer table styles */
    .customers-table {
        font-size: 0.875rem;
        min-width: 800px;
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
        flex-wrap: nowrap;
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

    /* Sticky action column - luôn hiển thị cột thao tác */
    .customers-table th:last-child,
    .customers-table td:last-child {
        position: sticky;
        right: 0;
        background: #fff;
        box-shadow: -3px 0 5px rgba(0,0,0,0.05);
        z-index: 1;
        min-width: 140px;
    }

    .customers-table thead th:last-child {
        background: #f8f9fa;
        z-index: 2;
    }

    .customers-table tbody tr:hover td:last-child {
        background: #f8f9fa;
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

        .customers-table th:last-child,
        .customers-table td:last-child {
            min-width: 120px;
        }
    }

    @media (max-width: 576px) {
        .customers-table {
            min-width: 600px;
        }

        .customers-table .btn-group {
            flex-direction: column;
            gap: 0.15rem;
        }

        .customers-table .btn-group .btn {
            padding: 0.2rem 0.3rem;
            font-size: 0.65rem;
        }

        .customers-table th:last-child,
        .customers-table td:last-child {
            min-width: 45px;
        }
    }

    /* Scroll indicator cho mobile */
    .table-scroll-hint {
        display: none;
        text-align: center;
        padding: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-size: 0.8rem;
        border-radius: 8px 8px 0 0;
        animation: pulse-hint 2s infinite;
    }

    @keyframes pulse-hint {
        0%, 100% { opacity: 0.9; }
        50% { opacity: 1; }
    }

    @media (max-width: 992px) {
        .table-scroll-hint {
            display: block;
        }
    }

    /* Action cell styling để nổi bật hơn */
    .action-cell {
        background: #fff !important;
    }

    .action-buttons {
        white-space: nowrap;
    }
</style>
@endsection

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
                            class="btn btn-outline-secondary btn-sm shadow-sm">
                            <i class="fas fa-plus me-1"></i> Thêm đầy đủ
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
                                <div class="service-package-wrapper position-relative">
                                    <input type="text"
                                        id="servicePackageSearch"
                                        name="service_package_search"
                                        class="form-control"
                                        placeholder="Tìm gói dịch vụ..."
                                        value="{{ request('service_package_search') ?? (request('service_package_id') ? $servicePackages->find(request('service_package_id'))?->name : '') }}"
                                        autocomplete="off">
                                    <input type="hidden" name="service_package_id" id="servicePackageId" value="{{ request('service_package_id') }}">
                                </div>
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
                                    @if(request()->hasAny(['search', 'service_package_id', 'service_package_search', 'service_status', 'date_from', 'date_to']))
                                    <a href="{{ route('admin.customers.index') }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-times-circle"></i> Xóa bộ lọc
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

                        @if(request()->hasAny(['search', 'service_package_id', 'service_package_search', 'service_status', 'date_from', 'date_to']))
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
                                @elseif(request('service_package_search'))
                                <span class="badge bg-success">Gói: {{ request('service_package_search') }}</span>
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

<!-- Enhanced Search và Actions -->
<div class="row mb-3">
    <div class="col-md-4">
        <input type="text" id="customerSearch" data-table-search="customersTable"
            class="form-control" placeholder="Tìm kiếm nhanh...">
    </div>
    <div class="col-md-4">
        <div id="bulkActions" data-bulk-actions="customersTable" style="display: none;">
            <div class="btn-group">
                <button class="btn btn-outline-danger btn-sm" onclick="bulkDelete()">
                    <i class="fas fa-trash-alt me-1"></i>
                    Xóa (<span data-selected-count>0</span>)
                </button>
                <button class="btn btn-outline-info btn-sm" onclick="bulkExport()">
                    <i class="fas fa-download me-1"></i>
                    Xuất Excel
                </button>
            </div>
        </div>
    </div>
    <div class="col-md-4 text-end">
        <div class="btn-group">
            <button class="btn btn-outline-secondary btn-sm" data-export-csv="customersTable">
                <i class="fas fa-file-csv me-1"></i>
                Xuất CSV
            </button>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#quickAddModal">
                <i class="fas fa-plus me-2"></i>
                Thêm nhanh
            </button>
        </div>
    </div>
</div>

<!-- Table Info -->
<div class="row mb-2">
    <div class="col-md-6">
        <small class="text-muted" data-table-count="customersTable">
            Hiển thị {{ $customers->count() }} / {{ $customers->count() }} bản ghi
        </small>
    </div>
    <div class="col-md-6 text-end">
        @if(request('search'))
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-times-circle me-1"></i>
            Xóa bộ lọc
        </a>
        @endif
    </div>
</div>

<!-- Customers Table -->
@if($customers->count() > 0)
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-scroll-hint">
            <i class="fas fa-arrows-alt-h me-2"></i>
            Vuốt sang phải để xem thêm • Cột thao tác cố định bên phải
        </div>
        <div class="table-responsive">
            <table id="customersTable" class="table table-hover mb-0 customers-table enhanced-table">
                <thead>
                    <tr>
                        <th data-sort="customer_code">Mã KH</th>
                        <th data-sort="name">Khách hàng</th>
                        <th class="d-none d-lg-table-cell" data-sort="email">Liên hệ</th>
                        <th class="text-center" data-sort="services">Dịch vụ</th>
                        <th class="d-none d-xl-table-cell" data-sort="created_at">Ngày tạo</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr id="customer-{{ $customer->id }}" style="border-bottom: 1px solid #e9ecef;">
                        <td class="py-2 px-2">
                            <div class="d-flex flex-column gap-1">
                                <span class="badge bg-primary px-1 py-1 small">{{ $customer->customer_code }}</span>
                                @if($customer->is_collaborator)
                                <span class="badge bg-success px-1 py-1 small">
                                    <i class="fas fa-handshake me-1"></i>CTV
                                </span>
                                @endif
                            </div>
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
                            $serviceCount = $customer->service_count;
                            $activeServices = $customer->active_services;
                            $expiredServices = $customer->expired_services;
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
                        <td class="text-center action-cell">
                            <div class="btn-group action-buttons" role="group">
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
                                    <i class="fas fa-trash-alt"></i>
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
                    @if(request()->hasAny(['search', 'service_package_id', 'service_package_search', 'service_status', 'login_email', 'date_from', 'date_to']))
                    Không có khách hàng nào khớp với tiêu chí tìm kiếm của bạn.
                    @else
                    Hệ thống chưa có khách hàng nào. Hãy thêm nhanh khách hàng đầu tiên!
                    @endif
                </p>
                <div class="d-flex gap-3 justify-content-center">
                    @if(request()->hasAny(['search', 'service_package_id', 'service_package_search', 'service_status', 'login_email', 'date_from', 'date_to']))
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-sync-alt me-2"></i>Xem tất cả
                    </a>
                    @endif
                    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#quickAddModal">
                        <i class="fas fa-plus me-2"></i>Thêm nhanh
                    </button>
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
                        <i class="fas fa-plus me-3"></i>
                        Thêm nhanh
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
                            <i class="fas fa-times-circle me-2"></i>
                            Hủy
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg px-4" name="action" value="save">
                            <i class="fas fa-save me-2"></i>
                            Lưu khách hàng
                        </button>
                        <button type="submit" class="btn btn-success btn-lg px-4" name="action" value="save_and_assign">
                            <i class="fas fa-plus me-2"></i>
                            Lưu & Gán dịch vụ
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
                        <i class="fas fa-times-circle me-2"></i>
                        Hủy bỏ
                    </button>
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-lg px-4">
                            <i class="fas fa-trash-alt me-2"></i>
                            Xóa khách hàng
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Package Dropdown Portal - Fixed position with inline styles for reliability -->
    <div id="servicePackageDropdown" style="display: none; position: fixed; z-index: 999999; background: #fff; border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-height: 400px; overflow-y: auto; padding: 10px 0;">
        <a class="dropdown-item" href="#" data-id="" style="padding: 12px 20px; cursor: pointer; display: block; color: #333; text-decoration: none; border-left: 3px solid transparent;">
            <i class="fas fa-list me-2 text-muted"></i>Tất cả gói dịch vụ
        </a>
        <hr style="margin: 8px 15px; border-color: #eee;">
        @foreach($servicePackages as $package)
        <a class="dropdown-item" href="#" data-id="{{ $package->id }}" style="padding: 12px 20px; cursor: pointer; display: block; color: #333; text-decoration: none; border-left: 3px solid transparent;">
            <i class="fas fa-cube me-2 text-primary"></i>{{ $package->name }}
        </a>
        @endforeach
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
            // ============================================
            // SEARCHABLE SERVICE PACKAGE DROPDOWN
            // ============================================
            const servicePackageSearch = document.getElementById('servicePackageSearch');
            const servicePackageId = document.getElementById('servicePackageId');
            const servicePackageDropdown = document.getElementById('servicePackageDropdown');

            if (servicePackageSearch && servicePackageDropdown) {
                console.log('✅ Service Package Search initialized');

                // IMPORTANT: Move dropdown to body to avoid overflow/stacking context issues
                document.body.appendChild(servicePackageDropdown);
                console.log('📦 Dropdown moved to body');

                const allItems = servicePackageDropdown.querySelectorAll('.dropdown-item');
                console.log('Items found:', allItems.length);

                // Function to position and show dropdown
                function showDropdown() {
                    const rect = servicePackageSearch.getBoundingClientRect();

                    // Position dropdown below input field
                    servicePackageDropdown.style.position = 'fixed';
                    servicePackageDropdown.style.top = (rect.bottom + 5) + 'px';
                    servicePackageDropdown.style.left = rect.left + 'px';
                    servicePackageDropdown.style.width = Math.max(rect.width, 300) + 'px';
                    servicePackageDropdown.style.display = 'block';
                    servicePackageDropdown.style.zIndex = '999999';

                    console.log('📦 Dropdown shown');
                }

                // Function to hide dropdown
                function hideDropdown() {
                    servicePackageDropdown.style.display = 'none';
                    console.log('🔒 Dropdown hidden');
                }

                // Update position on scroll
                window.addEventListener('scroll', function() {
                    if (servicePackageDropdown.style.display === 'block') {
                        showDropdown();
                    }
                });

                // Resize handler
                window.addEventListener('resize', function() {
                    if (servicePackageDropdown.style.display === 'block') {
                        showDropdown();
                    }
                });

                // Show dropdown when input is focused
                servicePackageSearch.addEventListener('focus', function() {
                    console.log('🎯 Input focused');
                    showDropdown();
                    filterDropdownItems('');
                });

                // Show dropdown when clicking on input
                servicePackageSearch.addEventListener('click', function(e) {
                    e.stopPropagation();
                    console.log('👆 Input clicked');
                    showDropdown();
                });

                // Filter items as user types
                servicePackageSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    console.log('⌨️ Typing:', searchTerm);
                    // Clear the hidden ID field when user types - allow text search
                    servicePackageId.value = '';
                    filterDropdownItems(searchTerm);
                    showDropdown();
                });

                // Filter function
                function filterDropdownItems(searchTerm) {
                    let visibleCount = 0;
                    allItems.forEach(item => {
                        const text = item.textContent.toLowerCase();
                        const isAllOption = item.dataset.id === '';

                        if (searchTerm === '' || text.includes(searchTerm) || isAllOption) {
                            item.style.display = 'block';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    // Update divider visibility
                    const divider = servicePackageDropdown.querySelector('.dropdown-divider');
                    if (divider) {
                        divider.style.display = visibleCount > 1 ? 'block' : 'none';
                    }

                    // Show "no results" message if needed
                    let noResultsMsg = servicePackageDropdown.querySelector('.no-results-message');
                    if (visibleCount <= 1 && searchTerm !== '') {
                        if (!noResultsMsg) {
                            noResultsMsg = document.createElement('div');
                            noResultsMsg.className = 'no-results-message text-muted text-center py-3';
                            noResultsMsg.innerHTML = '<i class="fas fa-search me-2"></i>Không tìm thấy gói dịch vụ phù hợp';
                            servicePackageDropdown.appendChild(noResultsMsg);
                        }
                        noResultsMsg.style.display = 'block';
                    } else if (noResultsMsg) {
                        noResultsMsg.style.display = 'none';
                    }
                }

                // Handle item selection
                allItems.forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const id = this.dataset.id;
                        const name = id === '' ? '' : this.textContent.trim().replace(/^\s*[\uf0c8\uf03a]\s*/, ''); // Remove icon text

                        servicePackageId.value = id;
                        servicePackageSearch.value = name;
                        hideDropdown();

                        // Add selected styling
                        allItems.forEach(i => i.classList.remove('active'));
                        if (id !== '') {
                            this.classList.add('active');
                        }
                    });
                });

                // Hide dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!servicePackageSearch.contains(e.target) && !servicePackageDropdown.contains(e.target)) {
                        hideDropdown();
                    }
                });

                // Prevent dropdown from closing when clicking inside it
                servicePackageDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });

                // Keyboard navigation
                servicePackageSearch.addEventListener('keydown', function(e) {
                    const visibleItems = Array.from(allItems).filter(item => item.style.display !== 'none');
                    const currentIndex = visibleItems.findIndex(item => item.classList.contains('highlighted'));

                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        showDropdown();
                        const nextIndex = currentIndex < visibleItems.length - 1 ? currentIndex + 1 : 0;
                        highlightItem(visibleItems, nextIndex);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        showDropdown();
                        const prevIndex = currentIndex > 0 ? currentIndex - 1 : visibleItems.length - 1;
                        highlightItem(visibleItems, prevIndex);
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        const highlightedItem = visibleItems.find(item => item.classList.contains('highlighted'));
                        if (highlightedItem) {
                            highlightedItem.click();
                        } else if (visibleItems.length > 0) {
                            visibleItems[0].click();
                        }
                    } else if (e.key === 'Escape') {
                        hideDropdown();
                        servicePackageSearch.blur();
                    }
                });

                function highlightItem(items, index) {
                    items.forEach(item => {
                        item.classList.remove('highlighted');
                        item.style.backgroundColor = '';
                    });
                    if (items[index]) {
                        items[index].classList.add('highlighted');
                        items[index].style.backgroundColor = '#e9ecef';
                        items[index].scrollIntoView({
                            block: 'nearest'
                        });
                    }
                }

                // Add hover effect
                allItems.forEach(item => {
                    item.addEventListener('mouseenter', function() {
                        allItems.forEach(i => {
                            i.classList.remove('highlighted');
                            if (!i.classList.contains('active')) {
                                i.style.backgroundColor = '';
                            }
                        });
                        if (!this.classList.contains('active')) {
                            this.style.backgroundColor = '#e9ecef';
                        }
                    });
                    item.addEventListener('mouseleave', function() {
                        if (!this.classList.contains('active')) {
                            this.style.backgroundColor = '';
                        }
                    });
                });
            }
            // ============================================
            // END SEARCHABLE SERVICE PACKAGE DROPDOWN
            // ============================================

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

            // Quick add form validation - REMOVED (duplicate)

            // THÊM: Filter improvement và thông báo khi không có kết quả
            const servicePackageSelect = document.querySelector('select[name="service_package_id"]');
            const serviceStatusSelect = document.querySelector('select[name="service_status"]');
            const searchInput = document.querySelector('input[name="search"]');

            // Hiển thị thông báo hữu ích khi filter không có kết quả
            const customerCount = {
                {
                    $customers - > total()
                }
            };
            if (customerCount === 0) {
                const hasFilters = {
                    {
                        request() - > hasAny(['search', 'service_package_id', 'service_package_search', 'service_status', 'date_from', 'date_to']) ? 'true' : 'false'
                    }
                };
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
                                    <i class="fas fa-sync-alt"></i> Xem tất cả khách hàng
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

        // Bulk actions functions
        window.bulkDelete = function() {
            const checkedBoxes = document.querySelectorAll('#customersTable tbody input[type="checkbox"]:checked');
            if (checkedBoxes.length === 0) {
                alert('Vui lòng chọn ít nhất một khách hàng để xóa.');
                return;
            }

            if (confirm(`Bạn có chắc chắn muốn xóa ${checkedBoxes.length} khách hàng đã chọn?`)) {
                // Simple approach: delete one by one
                checkedBoxes.forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    const deleteBtn = row.querySelector('.delete-btn');
                    if (deleteBtn) {
                        deleteBtn.click();
                    }
                });
            }
        };

        window.bulkExport = function() {
            const checkedBoxes = document.querySelectorAll('#customersTable tbody input[type="checkbox"]:checked');
            if (checkedBoxes.length === 0) {
                alert('Vui lòng chọn ít nhất một khách hàng để xuất.');
                return;
            }

            // Simple CSV export of selected rows
            const table = document.getElementById('customersTable');
            const headers = Array.from(table.querySelectorAll('th')).map(th => th.textContent.trim());

            let csv = headers.join(',') + '\n';

            checkedBoxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const cells = Array.from(row.cells).map(cell => {
                    return '"' + cell.textContent.trim().replace(/"/g, '""') + '"';
                });
                csv += cells.join(',') + '\n';
            });

            const blob = new Blob([csv], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `customers_selected_${new Date().toISOString().split('T')[0]}.csv`;
            link.click();
        };

        // Quick Add Form handling
        const quickAddForm = document.getElementById('quickAddForm');
        const quickAddModal = document.getElementById('quickAddModal');

        if (quickAddForm && quickAddModal) {
            console.log('✅ Quick Add Form and Modal found');

            // Reset form when modal is shown
            quickAddModal.addEventListener('show.bs.modal', function() {
                console.log('📂 Modal opening, resetting form');
                quickAddForm.reset();
                // Clear any previous error states
                quickAddForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                // Focus on name field
                setTimeout(() => {
                    document.getElementById('quick_name')?.focus();
                }, 300);
            });

            // Add error handling
            quickAddForm.addEventListener('error', function(e) {
                console.error('❌ Form error:', e);
            });

            // Form validation
            quickAddForm.addEventListener('submit', function(e) {
                console.log('🚀 Quick Add Form submitted!');

                const nameField = document.getElementById('quick_name');
                const emailField = document.getElementById('quick_email');
                const phoneField = document.getElementById('quick_phone');

                const name = nameField?.value.trim();
                const email = emailField?.value.trim();
                const phone = phoneField?.value.trim();

                console.log('📝 Form data:', {
                    name,
                    email,
                    phone
                });

                // Check CSRF token
                const csrfToken = quickAddForm.querySelector('input[name="_token"]');
                console.log('🔐 CSRF Token:', csrfToken ? csrfToken.value : 'NOT FOUND');

                // Check form action
                console.log('🎯 Form action:', quickAddForm.action);

                if (!name || name.length < 2) {
                    e.preventDefault();
                    nameField?.focus();
                    alert('Vui lòng nhập tên khách hàng (ít nhất 2 ký tự)');
                    console.log('❌ Validation failed: Name too short');
                    return false;
                }

                console.log('✅ Validation passed, submitting form...');

                // Show loading state
                const submitButtons = quickAddForm.querySelectorAll('button[type="submit"]');
                submitButtons.forEach(btn => {
                    btn.disabled = true;
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';

                    // Restore button after 10 seconds (fallback)
                    setTimeout(() => {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                        console.log('🔄 Button restored after timeout');
                    }, 10000);
                });
            });
        }

        // Scroll to specific customer if anchor is present in URL
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
    </script>
    @endsection