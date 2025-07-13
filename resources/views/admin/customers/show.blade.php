@extends('layouts.admin')

@section('title', 'Chi tiết khách hàng')
@section('page-title', 'Chi tiết khách hàng')

@section('content')
<!-- Header Actions -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.customers.index') }}">
                    <i class="fas fa-users"></i> Khách hàng
                </a>
            </li>
            <li class="breadcrumb-item active">{{ $customer->name }}</li>
        </ol>
    </nav>
    <div class="btn-group" role="group">
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Quay lại
        </a>
        <a href="{{ route('admin.customers.create') }}" class="btn btn-success">
            <i class="fas fa-user-plus me-1"></i>
            Thêm khách hàng mới
        </a>
    </div>
</div>

<div class="row">
    <!-- Thông tin khách hàng -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    Thông tin khách hàng
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width: 80px; height: 80px; font-size: 2rem;">
                        <i class="fas fa-user"></i>
                    </div>
                </div>

                <table class="table table-borderless">
                    <tr>
                        <td><strong>Mã KH:</strong></td>
                        <td><span class="badge bg-primary">{{ $customer->customer_code }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Tên:</strong></td>
                        <td>{{ $customer->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>
                            @if($customer->email)
                            <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                            @else
                            <span class="text-muted">Chưa có</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>SĐT:</strong></td>
                        <td>
                            @if($customer->phone)
                            <a href="tel:{{ $customer->phone }}">{{ $customer->phone }}</a>
                            @else
                            <span class="text-muted">Chưa có</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Ngày tạo:</strong></td>
                        <td>{{ $customer->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>

                <div class="d-grid gap-2">
                    <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>
                        Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.customers.create') }}" class="btn btn-success">
                        <i class="fas fa-user-plus me-1"></i>
                        Thêm khách hàng mới
                    </a>
                    <a href="{{ route('admin.customers.assign-service', $customer) }}" class="btn btn-info">
                        <i class="fas fa-plus me-1"></i>
                        Gán dịch vụ mới
                    </a>

                    <a href="{{ route('lookup.index', ['code' => $customer->customer_code]) }}"
                        class="btn btn-secondary" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i>
                        Xem trang tra cứu
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách dịch vụ -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Dịch vụ đang sử dụng ({{ $customer->customerServices->count() }})
                </h5>
                <a href="{{ route('admin.customers.assign-service', $customer) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Thêm dịch vụ
                </a>
            </div>
            <div class="card-body">
                @if($customer->customerServices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Dịch vụ</th>
                                <th>Loại TK</th>
                                <th>Email đăng nhập</th>
                                <th>Hết hạn</th>
                                <th>Trạng thái</th>
                                <th>Người nhập hàng</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->customerServices as $service)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $service->servicePackage->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $service->servicePackage->category->name }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $service->servicePackage->account_type }}
                                    </span>
                                </td>
                                <td>
                                    <code>{{ $service->login_email }}</code>
                                </td>
                                <td>
                                    <div>
                                        {{ $service->expires_at->format('d/m/Y') }}
                                        <br>
                                        <small class="text-muted">
                                            @if($service->isExpired())
                                            <span class="text-danger">Đã hết hạn</span>
                                            @elseif($service->isExpiringSoon())
                                            <span class="text-warning">Sắp hết hạn</span>
                                            @else
                                            Còn {{ $service->expires_at->diffInDays(now()) }} ngày
                                            @endif
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    @if($service->status === 'active')
                                    <span class="badge bg-success">Hoạt động</span>
                                    @elseif($service->status === 'expired')
                                    <span class="badge bg-danger">Hết hạn</span>
                                    @else
                                    <span class="badge bg-secondary">Đã hủy</span>
                                    @endif
                                </td>
                                <td>
                                    @if($service->assignedBy)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2"
                                            style="width: 32px; height: 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.8rem;">
                                            {{ strtoupper(substr($service->assignedBy->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold" style="font-size: 0.9rem;">{{ $service->assignedBy->name }}</div>
                                            <small class="text-muted">{{ $service->assignedBy->email }}</small>
                                        </div>
                                    </div>
                                    @else
                                    <span class="text-muted">
                                        <i class="fas fa-user-slash me-1"></i>
                                        Chưa xác định
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.customer-services.edit', $service) }}"
                                            class="btn btn-sm btn-outline-warning"
                                            title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.customer-services.destroy', $service) }}"
                                            class="d-inline"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa dịch vụ này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Khách hàng chưa có dịch vụ nào</h6>
                    <a href="{{ route('admin.customers.assign-service', $customer) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Gán dịch vụ đầu tiên
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>
        Quay lại danh sách
    </a>
</div>
@endsection