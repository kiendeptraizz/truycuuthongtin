@extends('layouts.admin')

@section('title', 'Chi tiết gói dịch vụ')
@section('page-title', 'Chi tiết gói dịch vụ')

@section('content')
<div class="row">
    <!-- Thông tin gói dịch vụ -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-box me-2"></i>
                    Thông tin gói dịch vụ
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px; font-size: 2rem;">
                        <i class="fas fa-box"></i>
                    </div>
                </div>
                
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Tên gói:</strong></td>
                        <td>{{ $servicePackage->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Danh mục:</strong></td>
                        <td>
                            @if($servicePackage->category)
                                <span class="badge bg-info">{{ $servicePackage->category->name }}</span>
                            @else
                                <span class="badge bg-secondary">Chưa phân loại</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Loại TK:</strong></td>
                        <td><span class="badge bg-secondary">{{ $servicePackage->account_type }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Thời hạn:</strong></td>
                        <td>{{ $servicePackage->default_duration_days }} ngày</td>
                    </tr>
                    <tr>
                        <td><strong>Giá bán:</strong></td>
                        <td><strong class="text-success">{{ formatPrice($servicePackage->price) }}</strong></td>
                    </tr>
                    @if($servicePackage->cost_price)
                    <tr>
                        <td><strong>Giá nhập:</strong></td>
                        <td><strong class="text-danger">{{ formatPrice($servicePackage->cost_price) }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Lợi nhuận:</strong></td>
                        <td><strong class="text-primary">{{ formatPrice($servicePackage->price - $servicePackage->cost_price) }}</strong></td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Trạng thái:</strong></td>
                        <td>
                            @if($servicePackage->is_active)
                                <span class="badge bg-success">Hoạt động</span>
                            @else
                                <span class="badge bg-danger">Tạm dừng</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Ngày tạo:</strong></td>
                        <td>{{ $servicePackage->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
                
                @if($servicePackage->description)
                <div class="mt-3">
                    <strong>Mô tả:</strong>
                    <p class="text-muted mt-2">{{ $servicePackage->description }}</p>
                </div>
                @endif
                
                <div class="d-grid gap-2 mt-4">
                    <a href="{{ route('admin.service-packages.edit', $servicePackage) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>
                        Chỉnh sửa
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Danh sách khách hàng sử dụng -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    Khách hàng đang sử dụng ({{ $servicePackage->customerServices->count() }})
                </h5>
            </div>
            <div class="card-body">
                @if($servicePackage->customerServices->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Khách hàng</th>
                                    <th>Email đăng nhập</th>
                                    <th>Kích hoạt</th>
                                    <th>Hết hạn</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($servicePackage->customerServices as $service)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $service->customer->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $service->customer->customer_code }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <code>{{ $service->login_email }}</code>
                                        </td>
                                        <td>
                                            <small>{{ $service->activated_at->format('d/m/Y') }}</small>
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
                                                        Còn {{ $service->getDaysRemaining() }} ngày
                                                    @endif
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($service->status === 'active')
                                                @if($service->isExpired())
                                                    <span class="badge bg-danger">Hết hạn</span>
                                                @elseif($service->isExpiringSoon())
                                                    <span class="badge bg-warning">Sắp hết hạn</span>
                                                @else
                                                    <span class="badge bg-success">Hoạt động</span>
                                                @endif
                                            @elseif($service->status === 'expired')
                                                <span class="badge bg-danger">Hết hạn</span>
                                            @else
                                                <span class="badge bg-secondary">Đã hủy</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.customers.show', $service->customer) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="Xem khách hàng">
                                                    <i class="fas fa-user"></i>
                                                </a>
                                                <a href="{{ route('admin.customer-services.edit', $service) }}" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Chưa có khách hàng nào sử dụng gói dịch vụ này</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('admin.service-packages.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>
        Quay lại danh sách
    </a>
</div>
@endsection
