@extends('layouts.admin')

@section('title', 'Thống kê dịch vụ khách hàng')
@section('page-title', 'Thống kê dịch vụ khách hàng')

@section('content')
<div class="row">
    <!-- Header Actions -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h4 mb-0 text-primary">
                    <i class="fas fa-chart-bar me-2"></i>
                    Thống kê chi tiết dịch vụ khách hàng
                </h2>
                <p class="text-muted mb-0">Tổng quan tình hình dịch vụ và công cụ quản lý</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('admin.customer-services.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Quay lại danh sách
                </a>
                <button type="button" class="btn btn-primary" onclick="location.reload()">
                    <i class="fas fa-sync-alt me-1"></i>
                    Làm mới
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Overview Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Tổng dịch vụ
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalServices) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Đang hoạt động
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($activeServices) }}</div>
                        <div class="text-xs text-success">
                            {{ $totalServices > 0 ? round(($activeServices / $totalServices) * 100, 1) : 0 }}%
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Đã hết hạn
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($expiredByDate) }}</div>
                        <div class="text-xs text-danger">
                            {{ $totalServices > 0 ? round(($expiredByDate / $totalServices) * 100, 1) : 0 }}%
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Còn hạn sử dụng
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($validByDate) }}</div>
                        <div class="text-xs text-info">
                            {{ $totalServices > 0 ? round(($validByDate / $totalServices) * 100, 1) : 0 }}%
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Expired Services Breakdown -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="fas fa-calendar-times me-2"></i>
                    Phân loại dịch vụ hết hạn
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td><i class="fas fa-circle text-danger me-2"></i>Hôm nay</td>
                                <td class="text-end"><strong>{{ number_format($expiredCategories['today']) }}</strong></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-circle text-warning me-2"></i>Hôm qua</td>
                                <td class="text-end"><strong>{{ number_format($expiredCategories['yesterday']) }}</strong></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-circle text-info me-2"></i>Tuần qua</td>
                                <td class="text-end"><strong>{{ number_format($expiredCategories['last_week']) }}</strong></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-circle text-secondary me-2"></i>Tháng qua</td>
                                <td class="text-end"><strong>{{ number_format($expiredCategories['last_month']) }}</strong></td>
                            </tr>
                            <tr class="border-top">
                                <td><i class="fas fa-exclamation-triangle text-danger me-2"></i><strong>Quá 30 ngày</strong></td>
                                <td class="text-end text-danger"><strong>{{ number_format($expiredCategories['over_month']) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie me-2"></i>
                    Phân bố theo trạng thái
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td><i class="fas fa-circle text-success me-2"></i>Active</td>
                                <td class="text-end"><strong>{{ number_format($activeServices) }}</strong></td>
                                <td class="text-end text-success">{{ $totalServices > 0 ? round(($activeServices / $totalServices) * 100, 1) : 0 }}%</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-circle text-warning me-2"></i>Inactive</td>
                                <td class="text-end"><strong>{{ number_format($inactiveServices) }}</strong></td>
                                <td class="text-end text-warning">{{ $totalServices > 0 ? round(($inactiveServices / $totalServices) * 100, 1) : 0 }}%</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-circle text-secondary me-2"></i>Cancelled</td>
                                <td class="text-end"><strong>{{ number_format($cancelledServices) }}</strong></td>
                                <td class="text-end text-secondary">{{ $totalServices > 0 ? round(($cancelledServices / $totalServices) * 100, 1) : 0 }}%</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-circle text-danger me-2"></i>Expired</td>
                                <td class="text-end"><strong>{{ number_format($expiredByStatus) }}</strong></td>
                                <td class="text-end text-danger">{{ $totalServices > 0 ? round(($expiredByStatus / $totalServices) * 100, 1) : 0 }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Expiring Soon Services -->
@if($expiringSoon->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 bg-warning text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Dịch vụ sắp hết hạn (7 ngày tới) - {{ $expiringSoon->count() }} dịch vụ
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Khách hàng</th>
                                <th>Dịch vụ</th>
                                <th>Hết hạn</th>
                                <th>Còn lại</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expiringSoon as $service)
                            <tr>
                                <td>{{ $service->id }}</td>
                                <td>
                                    <a href="{{ route('admin.customers.show', $service->customer) }}" class="text-decoration-none">
                                        {{ $service->customer->customer_code }}
                                    </a>
                                </td>
                                <td>{{ $service->servicePackage->package_name ?? 'N/A' }}</td>
                                <td>{{ $service->expires_at ? $service->expires_at->format('d/m/Y') : 'N/A' }}</td>
                                <td>
                                    @if($service->expires_at)
                                        @php
                                            $daysLeft = now()->diffInDays($service->expires_at, false);
                                        @endphp
                                        @if($daysLeft >= 0)
                                            <span class="badge badge-warning">{{ $daysLeft }} ngày</span>
                                        @else
                                            <span class="badge badge-danger">Đã quá {{ abs($daysLeft) }} ngày</span>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $service->status === 'active' ? 'success' : ($service->status === 'expired' ? 'danger' : 'secondary') }}">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Delete Expired Services -->
@if($expiredServicesToDelete->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow border-danger">
            <div class="card-header py-3 bg-danger text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-trash-alt me-2"></i>
                    Dịch vụ hết hạn cần xóa - {{ $expiredServicesToDelete->count() }} dịch vụ
                </h6>
                <small>Các dịch vụ đã hết hạn trên 30 ngày và có trạng thái "expired" hoặc "cancelled"</small>
            </div>
            <div class="card-body">
                <!-- Delete Form -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Cảnh báo:</strong> Thao tác này sẽ xóa vĩnh viễn các dịch vụ đã hết hạn. Không thể hoàn tác!
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Lưu ý:</strong> Chỉ xóa các dịch vụ có trạng thái <code>expired</code> hoặc <code>cancelled</code>. 
                            Dịch vụ <code>active</code> dù đã hết hạn sẽ được giữ lại.
                        </div>
                    </div>
                    <div class="col-md-4">
                        <form method="POST" action="{{ route('admin.customer-services.delete-expired') }}" 
                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa các dịch vụ hết hạn? Thao tác này không thể hoàn tác!')">
                            @csrf
                            @method('DELETE')
                            
                            <div class="mb-2">
                                <label class="form-label">Xóa dịch vụ hết hạn trên:</label>
                                <select name="days" class="form-select" required>
                                    <option value="30">30 ngày</option>
                                    <option value="60">60 ngày</option>
                                    <option value="90">90 ngày</option>
                                    <option value="180">6 tháng</option>
                                    <option value="365">1 năm</option>
                                </select>
                            </div>
                            
                            <div class="mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="confirm" value="1" id="confirmDelete" required>
                                    <label class="form-check-label" for="confirmDelete">
                                        Tôi hiểu rủi ro và muốn tiếp tục
                                    </label>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt me-1"></i>
                                Xóa dịch vụ hết hạn
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Preview List -->
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-hover">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>ID</th>
                                <th>Khách hàng</th>
                                <th>Dịch vụ</th>
                                <th>Hết hạn</th>
                                <th>Số ngày</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expiredServicesToDelete as $service)
                            <tr>
                                <td>{{ $service->id }}</td>
                                <td>{{ $service->customer->customer_code ?? 'N/A' }}</td>
                                <td>{{ $service->servicePackage->package_name ?? 'N/A' }}</td>
                                <td>{{ $service->expires_at ? $service->expires_at->format('d/m/Y') : 'N/A' }}</td>
                                <td>
                                    @if($service->expires_at)
                                        <span class="badge badge-danger">
                                            {{ abs(now()->diffInDays($service->expires_at, false)) }} ngày trước
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-danger">{{ $service->status }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Expired Services List -->
@if($expiredServices->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 bg-danger text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-times-circle me-2"></i>
                    Danh sách dịch vụ đã hết hạn - {{ $expiredServices->count() }} dịch vụ (50 gần nhất)
                </h6>
                <small>Các dịch vụ đã hết hạn, sắp xếp theo thời gian hết hạn mới nhất</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>ID</th>
                                <th>Khách hàng</th>
                                <th>Dịch vụ</th>
                                <th>Hết hạn</th>
                                <th>Đã hết hạn</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expiredServices as $service)
                            <tr class="{{ $service->expires_at && $service->expires_at->lt(now()->subDays(30)) ? 'table-danger' : '' }}">
                                <td>
                                    <a href="{{ route('admin.customer-services.show', $service) }}" class="text-decoration-none">
                                        #{{ $service->id }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.customers.show', $service->customer) }}" class="text-decoration-none">
                                        <strong>{{ $service->customer->customer_code }}</strong>
                                        @if($service->customer->customer_name)
                                            <br><small class="text-muted">{{ $service->customer->customer_name }}</small>
                                        @endif
                                    </a>
                                </td>
                                <td>
                                    <strong>{{ $service->servicePackage->package_name ?? 'N/A' }}</strong>
                                    @if($service->servicePackage && $service->servicePackage->price)
                                        <br><small class="text-muted">{{ number_format($service->servicePackage->price) }} VNĐ</small>
                                    @endif
                                </td>
                                <td>
                                    @if($service->expires_at)
                                        <strong>{{ $service->expires_at->format('d/m/Y') }}</strong>
                                        <br><small class="text-muted">{{ $service->expires_at->format('H:i') }}</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($service->expires_at)
                                        @php
                                            $daysExpired = now()->diffInDays($service->expires_at, false);
                                        @endphp
                                        @if($daysExpired < 0)
                                            @php $daysExpired = abs($daysExpired); @endphp
                                            @if($daysExpired < 7)
                                                <span class="badge badge-warning">{{ $daysExpired }} ngày</span>
                                            @elseif($daysExpired < 30)
                                                <span class="badge badge-danger">{{ $daysExpired }} ngày</span>
                                            @else
                                                <span class="badge badge-dark">{{ $daysExpired }} ngày</span>
                                            @endif
                                        @else
                                            <span class="badge badge-success">Chưa hết hạn</span>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ 
                                        $service->status === 'active' ? 'success' : 
                                        ($service->status === 'expired' ? 'danger' : 
                                        ($service->status === 'cancelled' ? 'secondary' : 'warning')) 
                                    }}">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.customer-services.show', $service) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($service->status === 'active')
                                        <a href="{{ route('admin.customer-services.edit', $service) }}" 
                                           class="btn btn-sm btn-outline-warning" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Summary Footer -->
                <div class="card-footer bg-light">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <small class="text-muted">Tổng hết hạn:</small>
                            <br><strong>{{ $expiredByDate }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Hết hạn < 7 ngày:</small>
                            <br><strong>{{ $expiredServices->filter(function($s) { return $s->expires_at && now()->diffInDays($s->expires_at, false) < -7 && now()->diffInDays($s->expires_at, false) >= 0; })->count() }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Hết hạn > 30 ngày:</small>
                            <br><strong class="text-danger">{{ $expiredServices->filter(function($s) { return $s->expires_at && now()->diffInDays($s->expires_at, false) < -30; })->count() }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Vẫn Active:</small>
                            <br><strong class="text-success">{{ $expiredServices->where('status', 'active')->count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Top Services -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-star me-2"></i>
                    Top 10 gói dịch vụ phổ biến
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>Gói dịch vụ</th>
                                <th class="text-end">Số lượng</th>
                                <th class="text-end">Tỷ lệ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($servicesByPackage as $item)
                            <tr>
                                <td>{{ $item->servicePackage->package_name ?? 'Không xác định' }}</td>
                                <td class="text-end"><strong>{{ number_format($item->count) }}</strong></td>
                                <td class="text-end">{{ $totalServices > 0 ? round(($item->count / $totalServices) * 100, 1) : 0 }}%</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Không có dữ liệu</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.badge {
    font-size: 0.75rem;
}

.badge-success { background-color: #1cc88a; }
.badge-danger { background-color: #e74a3b; }
.badge-warning { background-color: #f6c23e; }
.badge-secondary { background-color: #858796; }

.card-header.bg-warning {
    background-color: #f6c23e !important;
}

.card-header.bg-danger {
    background-color: #e74a3b !important;
}

/* Expired services table */
.table-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}

.btn-group-sm .btn {
    padding: 0.2rem 0.4rem;
    font-size: 0.7rem;
}

/* Hover effects */
.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.05);
}
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto refresh every 5 minutes
    setTimeout(function() {
        location.reload();
    }, 300000);
    
    // Confirmation for delete action
    $('form[action*="delete-expired"]').on('submit', function(e) {
        const days = $(this).find('select[name="days"]').val();
        const confirmed = confirm(`Bạn có chắc chắn muốn xóa tất cả dịch vụ hết hạn trên ${days} ngày?\n\nThao tác này KHÔNG THỂ HOÀN TÁC!`);
        
        if (!confirmed) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endsection