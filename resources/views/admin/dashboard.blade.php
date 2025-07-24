@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Tổng Quan')

@section('content')
<!-- Header Welcome -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">🏠 Dashboard Tổng Quan</h1>
        <p class="mb-0 text-muted">Chào mừng trở lại! Đây là tổng quan hệ thống của bạn.</p>
    </div>
    <div>
        <span class="badge badge-success">Hệ thống hoạt động tốt</span>
        <small class="text-muted ml-2">{{ now()->format('d/m/Y H:i') }}</small>
    </div>
</div>

<!-- Thống kê tổng quan -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Tổng Khách Hàng
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalCustomers) }}</div>
                        <small class="text-success">
                            <i class="fas fa-arrow-up"></i> Tăng trưởng ổn định
                        </small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Dịch Vụ Hoạt Động
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalActiveServices) }}</div>
                        <small class="text-info">
                            <i class="fas fa-sync-alt"></i> Đang vận hành
                        </small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Gói Dịch Vụ
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalServicePackages) }}</div>
                        <small class="text-primary">
                            <i class="fas fa-box"></i> Đa dạng sản phẩm
                        </small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cube fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Sắp Hết Hạn
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($expiringSoonServices) }}</div>
                        <small class="text-danger">
                            <i class="fas fa-exclamation-triangle"></i> Cần chú ý
                        </small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">⚡ Hành Động Nhanh</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 col-sm-4 col-6 mb-3">
                        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-user-plus d-block mb-1"></i>
                            <small>Thêm KH</small>
                        </a>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-3">
                        <a href="{{ route('admin.customer-services.create') }}" class="btn btn-success btn-block">
                            <i class="fas fa-link d-block mb-1"></i>
                            <small>Gán Dịch Vụ</small>
                        </a>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-3">
                        <a href="{{ route('admin.service-packages.create') }}" class="btn btn-info btn-block">
                            <i class="fas fa-box-open d-block mb-1"></i>
                            <small>Thêm Gói</small>
                        </a>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-3">
                        <a href="{{ route('admin.backup.index') }}" class="btn btn-warning btn-block">
                            <i class="fas fa-shield-alt d-block mb-1"></i>
                            <small>Backup</small>
                        </a>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-3">
                        <a href="{{ route('admin.reports.profit') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-chart-line d-block mb-1"></i>
                            <small>Báo Cáo</small>
                        </a>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-3">
                        <a href="{{ route('lookup.index') }}" target="_blank" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-search d-block mb-1"></i>
                            <small>Tra Cứu</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <!-- Recent Activities -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">📋 Hoạt Động Gần Đây</h6>
                <a href="{{ route('admin.customer-services.index') }}" class="btn btn-sm btn-outline-primary">
                    Xem Tất Cả
                </a>
            </div>
            <div class="card-body">
                @if($recentAssignments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%">
                            <thead>
                                <tr>
                                    <th>Khách Hàng</th>
                                    <th>Dịch Vụ</th>
                                    <th>Ngày Gán</th>
                                    <th>Trạng Thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAssignments as $assignment)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    <div class="icon-circle bg-primary">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="small font-weight-bold">{{ $assignment->customer->name }}</div>
                                                    <div class="small text-gray-500">{{ $assignment->customer->customer_code }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold">{{ $assignment->servicePackage->name }}</div>
                                            <div class="small text-gray-500">{{ number_format($assignment->servicePackage->price) }}đ</div>
                                        </td>
                                        <td>{{ $assignment->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            @if($assignment->status === 'active')
                                                <span class="badge badge-success">Hoạt động</span>
                                            @elseif($assignment->status === 'pending')
                                                <span class="badge badge-warning">Chờ kích hoạt</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($assignment->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">Chưa có hoạt động nào gần đây</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <!-- Expiring Services -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">⏰ Sắp Hết Hạn</h6>
            </div>
            <div class="card-body">
                @if($expiringSoon->count() > 0)
                    @foreach($expiringSoon->take(5) as $service)
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                <div class="icon-circle bg-warning">
                                    <i class="fas fa-clock text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small font-weight-bold">{{ $service->customer->name }}</div>
                                <div class="small text-gray-500">{{ $service->servicePackage->name }}</div>
                                <div class="small text-danger">
                                    Hết hạn: {{ $service->expires_at->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($expiringSoon->count() > 5)
                        <div class="text-center">
                            <a href="{{ route('admin.customer-services.index', ['filter' => 'expiring']) }}" class="btn btn-sm btn-outline-warning">
                                Xem thêm {{ $expiringSoon->count() - 5 }} dịch vụ
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted small">Không có dịch vụ nào sắp hết hạn</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- System Status -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">🔧 Trạng Thái Hệ Thống</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="mr-3">
                        <i class="fas fa-server text-success"></i>
                    </div>
                    <div>
                        <div class="small font-weight-bold">Database</div>
                        <div class="small text-success">Hoạt động bình thường</div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="mr-3">
                        <i class="fas fa-shield-alt text-success"></i>
                    </div>
                    <div>
                        <div class="small font-weight-bold">Backup System</div>
                        <div class="small text-success">Tự động hàng ngày</div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <i class="fas fa-chart-line text-info"></i>
                    </div>
                    <div>
                        <div class="small font-weight-bold">Performance</div>
                        <div class="small text-info">Tối ưu</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto refresh dashboard every 5 minutes
    setInterval(function() {
        location.reload();
    }, 300000);
});
</script>
@endsection
