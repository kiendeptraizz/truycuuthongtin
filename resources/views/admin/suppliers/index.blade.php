@extends('layouts.admin')

@section('title', 'Quản lý nhà cung cấp')
@section('page-title', 'Quản lý nhà cung cấp')

@section('styles')
<style>
    .stats-card {
        border-radius: 15px;
        border: none;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .icon-wrapper {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .table th {
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }

    .table td {
        border-color: #f1f3f4;
        vertical-align: middle;
    }

    .btn-group .btn {
        border-radius: 8px;
        margin: 0 2px;
    }

    .search-box {
        border-radius: 12px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .search-box:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.15);
    }
</style>
@endsection

@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-12 mb-4">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card stats-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper me-3"
                                style="width: 48px; height: 48px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-truck text-white" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold text-primary">{{ number_format($stats['total']) }}</h4>
                                <p class="mb-0 text-muted">Tổng nhà cung cấp</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card stats-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper me-3"
                                style="width: 48px; height: 48px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-money-bill-wave text-white" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold text-success">{{ number_format($stats['total_value']) }} VND</h4>
                                <p class="mb-0 text-muted">Tổng giá trị</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card stats-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper me-3"
                                style="width: 48px; height: 48px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-laptop text-white" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold text-info">{{ $suppliers->count() }}</h4>
                                <p class="mb-0 text-muted">Đang hiển thị</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary text-white py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-4"
                            style="width: 64px; height: 64px; background: rgba(255,255,255,0.2); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-truck text-white" style="font-size: 1.8rem;"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bold text-white">Quản lý nhà cung cấp</h4>
                            <p class="mb-0 text-light opacity-75 fs-5">Danh sách và quản lý nhà cung cấp hàng hóa</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-warning btn-lg shadow-sm">
                            <i class="fas fa-plus me-2"></i>
                            Thêm nhà cung cấp
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Search Filter -->
                <div class="card border-0 bg-light mb-4">
                    <div class="card-body py-4">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold fs-6">
                                    <i class="fas fa-search me-2 text-primary"></i>Tìm kiếm
                                </label>
                                <input type="text" name="search" class="form-control search-box"
                                    placeholder="Tên nhà cung cấp, mã NCC..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold fs-6">
                                    <i class="fas fa-filter me-2 text-primary"></i>Lọc theo dịch vụ
                                </label>
                                <input type="text" name="service_filter" class="form-control search-box"
                                    placeholder="Tên dịch vụ..."
                                    value="{{ request('service_filter') }}">
                            </div>
                            <div class="col-md-2">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-2"></i>Tìm kiếm
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                @if(request('search') || request('service_filter'))
                                <div class="d-grid">
                                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Xóa lọc
                                    </a>
                                </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Results Info -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="fs-5 text-muted">
                                        <i class="fas fa-info-circle me-2 text-primary"></i>
                                        <strong class="text-dark">{{ number_format($suppliers->total()) }}</strong> nhà cung cấp
                                        @if($suppliers->total() > 0)
                                        <span class="ms-2">(Hiển thị {{ $suppliers->firstItem() }}-{{ $suppliers->lastItem() }})</span>
                                        @endif
                                    </div>
                                    @if($suppliers->total() > 0)
                                    <small class="text-muted fs-6">
                                        <i class="fas fa-clock me-1"></i>
                                        Cập nhật {{ now()->format('H:i d/m/Y') }}
                                    </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions -->
                @if($suppliers->count() > 0)
                <div class="card border-0 bg-light mb-3" id="bulkActionsCard" style="display: none;">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold text-primary">
                                    <i class="fas fa-check-square me-2"></i>
                                    Đã chọn <span id="selectedCount">0</span> nhà cung cấp
                                </span>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-danger" id="bulkDeleteBtn">
                                    <i class="fas fa-trash me-2"></i>Xóa đã chọn
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-2" id="deselectAllBtn">
                                    <i class="fas fa-times me-2"></i>Bỏ chọn tất cả
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Suppliers Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
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
                                <th class="py-3">Số dịch vụ</th>
                                <th class="py-3">Tổng giá trị</th>
                                <th class="py-3">Ngày tạo</th>
                                <th class="py-3 text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $supplier)
                            <tr>
                                <td class="py-3">
                                    <div class="form-check">
                                        <input class="form-check-input supplier-checkbox" type="checkbox"
                                            value="{{ $supplier->id }}"
                                            data-supplier-name="{{ $supplier->supplier_name }}">
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-primary fs-6">{{ $supplier->supplier_code }}</span>
                                </td>
                                <td class="py-3">
                                    <div class="fw-bold">{{ $supplier->supplier_name }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-laptop me-2 text-info"></i>
                                        <span class="fw-bold">{{ $supplier->product_count }}</span>
                                        <small class="text-muted ms-1">dịch vụ</small>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="fw-bold text-success">
                                        {{ number_format($supplier->total_value, 0, '.', ',') }} VND
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="text-muted">{{ $supplier->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="py-3 text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.suppliers.show', $supplier) }}"
                                            class="btn btn-outline-info" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.suppliers.edit', $supplier) }}"
                                            class="btn btn-outline-warning" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button"
                                            class="btn btn-outline-danger delete-btn"
                                            title="Xóa"
                                            data-supplier-name="{{ $supplier->supplier_name }}"
                                            data-delete-url="{{ route('admin.suppliers.destroy', $supplier) }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($suppliers->hasPages())
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            {{ $suppliers->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
                @endif

                @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-truck fa-5x text-muted opacity-50"></i>
                    </div>
                    <h4 class="text-muted mb-3">Không tìm thấy nhà cung cấp dịch vụ nào</h4>
                    <p class="text-muted fs-5 mb-4">
                        @if(request('search'))
                        Không có nhà cung cấp dịch vụ nào khớp với từ khóa "{{ request('search') }}".
                        @else
                        Hệ thống chưa có nhà cung cấp dịch vụ nào. Hãy thêm nhà cung cấp đầu tiên!
                        @endif
                    </p>
                    <div class="d-flex gap-3 justify-content-center">
                        @if(request('search'))
                        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-refresh me-2"></i>Xem tất cả
                        </a>
                        @endif
                        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>Thêm nhà cung cấp mới
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white py-4">
                <h5 class="modal-title fw-bold" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Xác nhận xóa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="fas fa-truck fa-3x text-danger mb-3"></i>
                    <h6>Bạn có chắc chắn muốn xóa nhà cung cấp:</h6>
                    <strong id="supplierNameToDelete" class="text-primary"></strong>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Hành động này không thể hoàn tác!
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Hủy
                </button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Xóa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Confirmation Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white py-4">
                <h5 class="modal-title fw-bold" id="bulkDeleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Xác nhận xóa nhiều nhà cung cấp
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="fas fa-truck fa-3x text-danger mb-3"></i>
                    <h6>Bạn có chắc chắn muốn xóa <span id="bulkDeleteCount" class="fw-bold text-primary"></span> nhà cung cấp đã chọn?</h6>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Hành động này sẽ xóa tất cả nhà cung cấp đã chọn và không thể hoàn tác!
                </div>
                <div class="border p-3 rounded bg-light" id="selectedSuppliersList">
                    <!-- Danh sách nhà cung cấp sẽ được hiển thị ở đây -->
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Hủy
                </button>
                <form id="bulkDeleteForm" method="POST" action="{{ route('admin.suppliers.bulk-delete') }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="supplier_ids" id="supplierIdsInput">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Xóa tất cả
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete confirmation
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const deleteForm = document.getElementById('deleteForm');
        const supplierNameElement = document.getElementById('supplierNameToDelete');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const supplierName = this.getAttribute('data-supplier-name');
                const deleteUrl = this.getAttribute('data-delete-url');

                supplierNameElement.textContent = supplierName;
                deleteForm.action = deleteUrl;
                deleteModal.show();
            });
        });

        // Bulk selection functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const supplierCheckboxes = document.querySelectorAll('.supplier-checkbox');
        const bulkActionsCard = document.getElementById('bulkActionsCard');
        const selectedCountElement = document.getElementById('selectedCount');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const deselectAllBtn = document.getElementById('deselectAllBtn');
        const bulkDeleteModal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));

        // Function to update bulk actions visibility and count
        function updateBulkActions() {
            const selectedCheckboxes = document.querySelectorAll('.supplier-checkbox:checked');
            const selectedCount = selectedCheckboxes.length;

            if (selectedCount > 0) {
                bulkActionsCard.style.display = 'block';
                selectedCountElement.textContent = selectedCount;
            } else {
                bulkActionsCard.style.display = 'none';
            }

            // Update select all checkbox state
            if (selectedCount === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (selectedCount === supplierCheckboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }

        // Select all functionality
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            supplierCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateBulkActions();
        });

        // Individual checkbox change
        supplierCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActions);
        });

        // Deselect all button
        deselectAllBtn.addEventListener('click', function() {
            supplierCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            selectAllCheckbox.checked = false;
            updateBulkActions();
        });

        // Bulk delete functionality
        bulkDeleteBtn.addEventListener('click', function() {
            const selectedCheckboxes = document.querySelectorAll('.supplier-checkbox:checked');

            if (selectedCheckboxes.length === 0) {
                alert('Vui lòng chọn ít nhất một nhà cung cấp để xóa!');
                return;
            }

            // Collect selected supplier data
            const selectedSuppliers = [];
            const selectedIds = [];

            selectedCheckboxes.forEach(checkbox => {
                selectedIds.push(checkbox.value);
                selectedSuppliers.push({
                    id: checkbox.value,
                    name: checkbox.getAttribute('data-supplier-name')
                });
            });

            // Update modal content
            document.getElementById('bulkDeleteCount').textContent = selectedSuppliers.length;
            document.getElementById('supplierIdsInput').value = selectedIds.join(',');

            // Create supplier list for modal
            const suppliersList = document.getElementById('selectedSuppliersList');
            suppliersList.innerHTML = selectedSuppliers.map(supplier =>
                `<div class="text-start mb-1">
                    <i class="fas fa-truck me-2 text-primary"></i>
                    <strong>${supplier.name}</strong>
                </div>`
            ).join('');

            // Show modal
            bulkDeleteModal.show();
        });

        // Auto-submit search form
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    this.form.submit();
                }
            });
        }

        // Initialize bulk actions state
        updateBulkActions();
    });
</script>
@endsection