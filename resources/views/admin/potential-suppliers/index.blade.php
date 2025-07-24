@extends('layouts.admin')

@section('title', 'Quản lý nhà cung cấp tiềm năng')

@section('styles')
<style>
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
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800 fw-bold">
                <i class="fas fa-users-cog me-2 text-primary"></i>
                Quản lý nhà cung cấp tiềm năng
            </h1>
            <p class="mb-0 text-muted">Quản lý danh sách các nhà cung cấp tiềm năng và dịch vụ của họ</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Nhà cung cấp hiện tại
            </a>
            <a href="{{ route('admin.potential-suppliers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Thêm nhà cung cấp tiềm năng
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng nhà cung cấp tiềm năng
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-wrapper" style="background: linear-gradient(45deg, #4e73df, #224abe);">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tổng giá trị ước tính
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_estimated_value'], 0, '.', ',') }} VND
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-wrapper" style="background: linear-gradient(45deg, #1cc88a, #13855c);">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Ưu tiên cao
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['high_priority'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-wrapper" style="background: linear-gradient(45deg, #e74a3b, #c0392b);">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Ưu tiên trung bình
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['medium_priority'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-wrapper" style="background: linear-gradient(45deg, #f6c23e, #dda20a);">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.potential-suppliers.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label fw-semibold">Tìm kiếm</label>
                    <input type="text" 
                           class="form-control search-box" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Tìm theo tên, mã NCC, người liên hệ, dịch vụ...">
                </div>
                <div class="col-md-3">
                    <label for="service_filter" class="form-label fw-semibold">Lọc theo dịch vụ</label>
                    <input type="text" 
                           class="form-control search-box" 
                           id="service_filter" 
                           name="service_filter" 
                           value="{{ request('service_filter') }}"
                           placeholder="Tên dịch vụ...">
                </div>
                <div class="col-md-3">
                    <label for="priority_filter" class="form-label fw-semibold">Mức độ ưu tiên</label>
                    <select class="form-select search-box" id="priority_filter" name="priority_filter">
                        <option value="">Tất cả</option>
                        <option value="high" {{ request('priority_filter') == 'high' ? 'selected' : '' }}>Cao</option>
                        <option value="medium" {{ request('priority_filter') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                        <option value="low" {{ request('priority_filter') == 'low' ? 'selected' : '' }}>Thấp</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Tìm kiếm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Suppliers Table -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>
                Danh sách nhà cung cấp tiềm năng ({{ $potentialSuppliers->total() }} kết quả)
            </h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-danger btn-sm" id="bulk-delete-btn" style="display: none;">
                    <i class="fas fa-trash me-2"></i>Xóa đã chọn
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3" style="width: 50px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                    <label class="form-check-label" for="selectAll"></label>
                                </div>
                            </th>
                            <th class="py-3">Mã NCC</th>
                            <th class="py-3">Tên nhà cung cấp</th>
                            <th class="py-3">Người liên hệ</th>
                            <th class="py-3">Số dịch vụ</th>
                            <th class="py-3">Giá trị ước tính</th>
                            <th class="py-3">Ưu tiên</th>
                            <th class="py-3">Ngày tạo</th>
                            <th class="py-3 text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($potentialSuppliers as $supplier)
                        <tr>
                            <td class="py-3">
                                <div class="form-check">
                                    <input class="form-check-input supplier-checkbox" type="checkbox"
                                        value="{{ $supplier->id }}"
                                        id="supplier-{{ $supplier->id }}">
                                    <label class="form-check-label" for="supplier-{{ $supplier->id }}"></label>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="fw-bold text-primary">{{ $supplier->supplier_code }}</span>
                            </td>
                            <td class="py-3">
                                <div>
                                    <div class="fw-semibold">{{ $supplier->supplier_name }}</div>
                                    @if($supplier->phone)
                                        <small class="text-muted">
                                            <i class="fas fa-phone me-1"></i>{{ $supplier->phone }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3">
                                @if($supplier->contact_person)
                                    <div>{{ $supplier->contact_person }}</div>
                                    @if($supplier->email)
                                        <small class="text-muted">{{ $supplier->email }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">Chưa có</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="badge bg-info">{{ $supplier->services->count() }} dịch vụ</span>
                            </td>
                            <td class="py-3">
                                <span class="fw-bold text-success">
                                    {{ number_format($supplier->total_estimated_value, 0, '.', ',') }} VND
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-{{ $supplier->priority == 'high' ? 'danger' : ($supplier->priority == 'medium' ? 'warning' : 'secondary') }}">
                                    {{ $supplier->priority_label }}
                                </span>
                            </td>
                            <td class="py-3">
                                <small class="text-muted">{{ $supplier->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td class="py-3 text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.potential-suppliers.show', $supplier) }}" 
                                       class="btn btn-outline-info btn-action" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.potential-suppliers.edit', $supplier) }}" 
                                       class="btn btn-outline-primary btn-action" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.potential-suppliers.convert', $supplier) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Bạn có chắc chắn muốn chuyển đổi nhà cung cấp này thành nhà cung cấp chính thức?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-action" title="Chuyển đổi">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.potential-suppliers.destroy', $supplier) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhà cung cấp tiềm năng này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-action" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-0">Không có nhà cung cấp tiềm năng nào</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($potentialSuppliers->hasPages())
        <div class="card-footer">
            {{ $potentialSuppliers->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Bulk Delete Form -->
<form id="bulk-delete-form" action="{{ route('admin.potential-suppliers.bulk-delete') }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="ids" id="bulk-delete-ids">
</form>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const supplierCheckboxes = document.querySelectorAll('.supplier-checkbox');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    const bulkDeleteForm = document.getElementById('bulk-delete-form');
    const bulkDeleteIds = document.getElementById('bulk-delete-ids');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        supplierCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkDeleteButton();
    });

    // Individual checkbox change
    supplierCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllCheckbox();
            updateBulkDeleteButton();
        });
    });

    function updateSelectAllCheckbox() {
        const checkedCount = document.querySelectorAll('.supplier-checkbox:checked').length;
        selectAllCheckbox.checked = checkedCount === supplierCheckboxes.length;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < supplierCheckboxes.length;
    }

    function updateBulkDeleteButton() {
        const checkedBoxes = document.querySelectorAll('.supplier-checkbox:checked');
        if (checkedBoxes.length > 0) {
            bulkDeleteBtn.style.display = 'inline-block';
        } else {
            bulkDeleteBtn.style.display = 'none';
        }
    }

    // Bulk delete
    bulkDeleteBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.supplier-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Vui lòng chọn ít nhất một nhà cung cấp để xóa.');
            return;
        }

        if (confirm(`Bạn có chắc chắn muốn xóa ${checkedBoxes.length} nhà cung cấp tiềm năng đã chọn?`)) {
            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            bulkDeleteIds.value = JSON.stringify(ids);
            bulkDeleteForm.submit();
        }
    });
});
</script>
@endsection
