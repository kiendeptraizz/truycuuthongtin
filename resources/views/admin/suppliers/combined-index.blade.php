@extends('layouts.admin')

@section('title', 'Quản lý nhà cung cấp')

@section('styles')
<style>
    .nav-tabs .nav-link {
        border: none;
        border-radius: 10px 10px 0 0;
        color: #6c757d;
        font-weight: 600;
        padding: 1rem 1.5rem;
        margin-right: 0.5rem;
        background: #f8f9fc;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link:hover {
        background: #e3e6f0;
        color: #495057;
    }

    .nav-tabs .nav-link.active {
        background: #4e73df;
        color: white;
        border-color: #4e73df;
    }

    .tab-content {
        background: white;
        border-radius: 0 15px 15px 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        min-height: 500px;
    }

    .stats-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out;
    }

    .stats-card:hover {
        transform: translateY(-2px);
    }

    .icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }

    .table td {
        vertical-align: middle;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }

    .btn-action {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 6px;
    }

    .search-box {
        border-radius: 10px;
        border: 1px solid #e3e6f0;
        padding: 0.75rem 1rem;
    }

    .search-box:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .filter-card {
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: 10px;
    }

    .tab-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e3e6f0;
        background: #f8f9fc;
        border-radius: 15px 15px 0 0;
    }

    .tab-body {
        padding: 1.5rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800 fw-bold">
                <i class="fas fa-users-cog me-2 text-primary"></i>
                Quản lý nhà cung cấp
            </h1>
            <p class="mb-0 text-muted">Quản lý danh sách nhà cung cấp hiện tại và tiềm năng</p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-0" id="supplierTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="current-tab" data-bs-toggle="tab" data-bs-target="#current" 
                    type="button" role="tab" aria-controls="current" aria-selected="true">
                <i class="fas fa-building me-2"></i>
                Nhà cung cấp hiện tại
                <span class="badge bg-light text-dark ms-2">{{ $currentSuppliersCount ?? 0 }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="potential-tab" data-bs-toggle="tab" data-bs-target="#potential"
                    type="button" role="tab" aria-controls="potential" aria-selected="false">
                <i class="fas fa-users me-2"></i>
                Nhà cung cấp tiềm năng
                <span class="badge bg-light text-dark ms-2">{{ $potentialSuppliersCount ?? 0 }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="statistics-tab" data-bs-toggle="tab" data-bs-target="#statistics"
                    type="button" role="tab" aria-controls="statistics" aria-selected="false">
                <i class="fas fa-chart-bar me-2"></i>
                Thống kê
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="supplierTabContent">
        <!-- Current Suppliers Tab -->
        <div class="tab-pane fade show active" id="current" role="tabpanel" aria-labelledby="current-tab">
            <div class="tab-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 fw-bold text-primary">
                            <i class="fas fa-building me-2"></i>
                            Nhà cung cấp hiện tại
                        </h5>
                        <p class="mb-0 text-muted">Danh sách các nhà cung cấp đang hợp tác</p>
                    </div>
                    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Thêm nhà cung cấp
                    </a>
                </div>
            </div>
            <div class="tab-body">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="mt-3 text-muted">Đang tải danh sách nhà cung cấp hiện tại...</p>
                </div>
            </div>
        </div>

        <!-- Potential Suppliers Tab -->
        <div class="tab-pane fade" id="potential" role="tabpanel" aria-labelledby="potential-tab">
            <div class="tab-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 fw-bold text-primary">
                            <i class="fas fa-users me-2"></i>
                            Nhà cung cấp tiềm năng
                        </h5>
                        <p class="mb-0 text-muted">Danh sách các nhà cung cấp có tiềm năng hợp tác</p>
                    </div>
                    <a href="{{ route('admin.potential-suppliers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Thêm nhà cung cấp tiềm năng
                    </a>
                </div>
            </div>
            <div class="tab-body">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="mt-3 text-muted">Đang tải danh sách nhà cung cấp tiềm năng...</p>
                </div>
            </div>
        </div>

        <!-- Statistics Tab -->
        <div class="tab-pane fade" id="statistics" role="tabpanel" aria-labelledby="statistics-tab">
            <div class="tab-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 fw-bold text-primary">
                            <i class="fas fa-chart-bar me-2"></i>
                            Thống kê nhà cung cấp
                        </h5>
                        <p class="mb-0 text-muted">Báo cáo và thống kê tổng quan về nhà cung cấp</p>
                    </div>
                </div>
            </div>
            <div class="tab-body">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="mt-3 text-muted">Đang tải thống kê...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Setup CSRF token for all AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const currentTab = document.getElementById('current-tab');
    const potentialTab = document.getElementById('potential-tab');
    const statisticsTab = document.getElementById('statistics-tab');
    const currentContent = document.getElementById('current');
    const potentialContent = document.getElementById('potential');
    const statisticsContent = document.getElementById('statistics');

    // Load current suppliers when tab is activated
    currentTab.addEventListener('shown.bs.tab', function() {
        if (!currentContent.dataset.loaded) {
            loadCurrentSuppliers();
            currentContent.dataset.loaded = 'true';
        }
    });

    // Load potential suppliers when tab is activated
    potentialTab.addEventListener('shown.bs.tab', function() {
        if (!potentialContent.dataset.loaded) {
            loadPotentialSuppliers();
            potentialContent.dataset.loaded = 'true';
        }
    });

    // Load statistics when tab is activated
    statisticsTab.addEventListener('shown.bs.tab', function() {
        if (!statisticsContent.dataset.loaded) {
            loadStatistics();
            statisticsContent.dataset.loaded = 'true';
        }
    });

    // Load current suppliers by default
    loadCurrentSuppliers();
    currentContent.dataset.loaded = 'true';

    function loadCurrentSuppliers() {
        console.log('Loading current suppliers...');
        $.ajax({
            url: '{{ route("admin.suppliers.api.current") }}',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log('Current suppliers loaded successfully:', data);
                renderCurrentSuppliers(data);
            },
            error: function(xhr, status, error) {
                console.error('Error loading current suppliers:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                currentContent.querySelector('.tab-body').innerHTML = '<div class="text-center py-5"><p class="text-danger">Lỗi khi tải dữ liệu nhà cung cấp hiện tại</p></div>';
            }
        });
    }

    function loadPotentialSuppliers() {
        console.log('Loading potential suppliers...');
        $.ajax({
            url: '{{ route("admin.potential-suppliers.api.list") }}',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log('Potential suppliers loaded successfully:', data);
                renderPotentialSuppliers(data);
            },
            error: function(xhr, status, error) {
                console.error('Error loading potential suppliers:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                potentialContent.querySelector('.tab-body').innerHTML = '<div class="text-center py-5"><p class="text-danger">Lỗi khi tải dữ liệu nhà cung cấp tiềm năng</p></div>';
            }
        });
    }

    function loadStatistics() {
        fetch('{{ route("admin.suppliers.statistics") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Extract the content we need from the response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Get the main content (everything after the header)
            const content = doc.querySelector('.container-fluid');
            if (content) {
                // Remove the header part and keep only the content
                const header = content.querySelector('.d-sm-flex.align-items-center');
                if (header) {
                    header.remove();
                }

                statisticsContent.querySelector('.tab-body').innerHTML = content.innerHTML || '<div class="text-center py-5"><p class="text-muted">Không thể tải dữ liệu thống kê</p></div>';
            }
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
            statisticsContent.querySelector('.tab-body').innerHTML = '<div class="text-center py-5"><p class="text-danger">Lỗi khi tải thống kê</p></div>';
        });
    }

    function renderCurrentSuppliers(data) {
        const suppliers = data.suppliers.data;
        const stats = data.stats;

        let html = `
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Tổng số nhà cung cấp</h6>
                                    <h3 class="mb-0">${stats.total}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-building fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Tổng giá trị</h6>
                                    <h3 class="mb-0">${new Intl.NumberFormat('vi-VN').format(stats.total_value || 0)} VNĐ</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Suppliers Table -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách nhà cung cấp hiện tại</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Mã NCC</th>
                                    <th>Tên nhà cung cấp</th>
                                    <th>Số sản phẩm</th>
                                    <th>Tổng giá trị</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>`;

        if (suppliers.length > 0) {
            suppliers.forEach(supplier => {
                html += `
                    <tr>
                        <td>${supplier.supplier_code || 'N/A'}</td>
                        <td>${supplier.supplier_name}</td>
                        <td>${supplier.product_count || 0}</td>
                        <td>${new Intl.NumberFormat('vi-VN').format(supplier.total_value || 0)} VNĐ</td>
                        <td>${new Date(supplier.created_at).toLocaleDateString('vi-VN')}</td>
                        <td>
                            <a href="/admin/suppliers/${supplier.id}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/admin/suppliers/${supplier.id}/edit" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>`;
            });
        } else {
            html += `
                <tr>
                    <td colspan="6" class="text-center">Không có dữ liệu nhà cung cấp hiện tại</td>
                </tr>`;
        }

        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>`;

        currentContent.querySelector('.tab-body').innerHTML = html;
    }

    function renderPotentialSuppliers(data) {
        const suppliers = data.potentialSuppliers.data;
        const stats = data.stats;

        let html = `
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Tổng số tiềm năng</h6>
                                    <h3 class="mb-0">${stats.total}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Ưu tiên cao</h6>
                                    <h3 class="mb-0">${stats.high_priority}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-star fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Tổng giá trị ước tính</h6>
                                    <h3 class="mb-0">${new Intl.NumberFormat('vi-VN').format(stats.total_estimated_value || 0)} VNĐ</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Potential Suppliers Table -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách nhà cung cấp tiềm năng</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Mã NCC</th>
                                    <th>Tên nhà cung cấp</th>
                                    <th>Người liên hệ</th>
                                    <th>Ưu tiên</th>
                                    <th>Số dịch vụ</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>`;

        if (suppliers.length > 0) {
            suppliers.forEach(supplier => {
                const priorityBadge = supplier.priority === 'high' ? 'badge-danger' :
                                    supplier.priority === 'medium' ? 'badge-warning' : 'badge-secondary';
                const priorityText = supplier.priority === 'high' ? 'Cao' :
                                   supplier.priority === 'medium' ? 'Trung bình' : 'Thấp';

                html += `
                    <tr>
                        <td>${supplier.supplier_code || 'N/A'}</td>
                        <td>${supplier.supplier_name}</td>
                        <td>${supplier.contact_person || 'N/A'}</td>
                        <td><span class="badge ${priorityBadge}">${priorityText}</span></td>
                        <td>${supplier.services ? supplier.services.length : 0}</td>
                        <td>${new Date(supplier.created_at).toLocaleDateString('vi-VN')}</td>
                        <td>
                            <a href="/admin/potential-suppliers/${supplier.id}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/admin/potential-suppliers/${supplier.id}/edit" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>`;
            });
        } else {
            html += `
                <tr>
                    <td colspan="7" class="text-center">Không có dữ liệu nhà cung cấp tiềm năng</td>
                </tr>`;
        }

        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>`;

        potentialContent.querySelector('.tab-body').innerHTML = html;
    }
});
</script>
@endsection
