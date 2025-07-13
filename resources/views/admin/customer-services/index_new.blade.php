@extends('layouts.admin')

@section('title', 'Quản lý dịch vụ khách hàng')
@section('page-title', 'Quản lý dịch vụ khách hàng')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Quản lý dịch vụ khách hàng</h5>
                        <small class="text-muted">Theo dõi và quản lý các dịch vụ đã gán cho khách hàng</small>
                    </div>
                    <a href="{{ route('admin.customer-services.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Gán dịch vụ mới
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Simple Filter -->
                <div class="row mb-4">
                    <div class="col-12">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="Tìm khách hàng hoặc dịch vụ..."
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="filter" class="form-select">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="active" {{ request('filter') === 'active' ? 'selected' : '' }}>
                                        Đang hoạt động
                                    </option>
                                    <option value="expiring" {{ request('filter') === 'expiring' ? 'selected' : '' }}>
                                        Sắp hết hạn
                                    </option>
                                    <option value="expired" {{ request('filter') === 'expired' ? 'selected' : '' }}>
                                        Đã hết hạn
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
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
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Lọc
                                </button>
                                @if(request()->hasAny(['search', 'filter', 'service_package_id']))
                                    <a href="{{ route('admin.customer-services.index') }}" 
                                       class="btn btn-secondary w-100 mt-1">
                                        <i class="fas fa-times me-1"></i>Xóa
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Results Info -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">
                        Hiển thị {{ $customerServices->firstItem() ?? 0 }} - {{ $customerServices->lastItem() ?? 0 }} 
                        trong tổng số <strong>{{ $customerServices->total() }}</strong> dịch vụ
                    </span>
                    <small class="text-muted">
                        Cập nhật lúc {{ now()->format('H:i') }}
                    </small>
                </div>

                <!-- Customer Services Table -->
                @if($customerServices->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Khách hàng</th>
                                    <th>Dịch vụ</th>
                                    <th>Email đăng nhập</th>
                                    <th>Kích hoạt</th>
                                    <th>Hết hạn</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customerServices as $service)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $service->customer->name }}</strong>
                                                <br><small class="text-muted">{{ $service->customer->customer_code }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $service->servicePackage->name }}</strong>
                                                <br><small class="text-muted">{{ $service->servicePackage->category->name ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $service->login_email ?? 'Chưa có' }}</td>
                                        <td>{{ $service->activated_at ? $service->activated_at->format('d/m/Y') : 'Chưa kích hoạt' }}</td>
                                        <td>{{ $service->expires_at ? $service->expires_at->format('d/m/Y') : 'Không giới hạn' }}</td>
                                        <td>
                                            @php
                                                $status = $service->getStatus();
                                            @endphp
                                            @if($status === 'active')
                                                <span class="badge bg-success">Đang hoạt động</span>
                                            @elseif($status === 'expiring')
                                                <span class="badge bg-warning">Sắp hết hạn</span>
                                            @elseif($status === 'expired')
                                                <span class="badge bg-danger">Đã hết hạn</span>
                                            @else
                                                <span class="badge bg-secondary">Chưa kích hoạt</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.customer-services.show', $service) }}" 
                                                   class="btn btn-sm btn-outline-info"
                                                   title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.customer-services.edit', $service) }}" 
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="confirmDelete('{{ $service->customer->name }} - {{ $service->servicePackage->name }}', '{{ route('admin.customer-services.destroy', $service) }}')"
                                                        title="Xóa">
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
                    <div class="d-flex justify-content-center">
                        {{ $customerServices->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Không tìm thấy dịch vụ nào</h5>
                        @if(request()->hasAny(['search', 'filter', 'service_package_id']))
                            <p class="text-muted">Thử thay đổi bộ lọc hoặc <a href="{{ route('admin.customer-services.index') }}">xóa bộ lọc</a></p>
                        @else
                            <p class="text-muted">Hãy <a href="{{ route('admin.customer-services.create') }}">gán dịch vụ đầu tiên</a></p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa dịch vụ này?</p>
                <p class="text-muted" id="serviceToDelete"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmDelete(serviceName, deleteUrl) {
    document.getElementById('serviceToDelete').textContent = serviceName;
    document.getElementById('deleteForm').action = deleteUrl;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection
