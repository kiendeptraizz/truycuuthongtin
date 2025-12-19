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
                <div class="row g-3">
                    @foreach($customer->customerServices as $service)
                    <div class="col-12">
                        <div class="card border {{ $service->status === 'active' ? 'border-success' : ($service->status === 'expired' ? 'border-danger' : 'border-secondary') }}">
                            <div class="card-body p-3">
                                <div class="row align-items-center">
                                    <!-- Thao tác -->
                                    <div class="col-auto">
                                        <div class="btn-group-vertical btn-group-sm">
                                            <a href="{{ route('admin.customer-services.show', $service) }}"
                                                class="btn btn-outline-info" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.customer-services.edit', $service) }}?source=customer&customer_id={{ $customer->id }}"
                                                class="btn btn-outline-warning" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.customer-services.destroy', $service) }}"
                                                class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa dịch vụ này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Xóa">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Thông tin dịch vụ -->
                                    <div class="col">
                                        <div class="row">
                                            <!-- Tên dịch vụ & Family -->
                                            <div class="col-md-4 mb-2 mb-md-0">
                                                <strong>{{ $service->servicePackage->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $service->servicePackage->category->name ?? 'Chưa phân loại' }}</small>

                                                @if($service->family_account_id && $service->familyAccount)
                                                <br>
                                                <a href="{{ route('admin.family-accounts.show', $service->familyAccount) }}"
                                                    class="badge bg-primary text-decoration-none mt-1">
                                                    <i class="fas fa-home me-1"></i>
                                                    {{ Str::limit($service->familyAccount->family_name, 20) }}
                                                </a>
                                                @elseif(str_contains(strtolower($service->servicePackage->account_type ?? ''), 'family'))
                                                <br>
                                                <span class="badge bg-warning text-dark mt-1">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Chưa gán Family
                                                </span>
                                                @endif
                                            </div>

                                            <!-- Email đăng nhập -->
                                            <div class="col-md-4 mb-2 mb-md-0">
                                                <small class="text-muted d-block">Email đăng nhập:</small>
                                                <code class="small">{{ $service->login_email ?: 'Chưa có' }}</code>
                                            </div>

                                            <!-- Hết hạn & Trạng thái -->
                                            <div class="col-md-4 text-md-end">
                                                @if($service->status === 'active')
                                                <span class="badge bg-success">Hoạt động</span>
                                                @elseif($service->status === 'expired')
                                                <span class="badge bg-danger">Hết hạn</span>
                                                @else
                                                <span class="badge bg-secondary">Đã hủy</span>
                                                @endif
                                                <br>
                                                <small class="text-muted">
                                                    {{ $service->expires_at ? $service->expires_at->format('d/m/Y') : 'Không giới hạn' }}
                                                    @if($service->expires_at)
                                                    @if($service->isExpired())
                                                    <span class="text-danger">(Đã hết hạn)</span>
                                                    @elseif($service->isExpiringSoon())
                                                    <span class="text-warning">(Còn {{ $service->getDaysRemaining() }} ngày)</span>
                                                    @else
                                                    (Còn {{ $service->getDaysRemaining() }} ngày)
                                                    @endif
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
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