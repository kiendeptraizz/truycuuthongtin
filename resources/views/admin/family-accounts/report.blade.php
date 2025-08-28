@extends('layouts.admin')

@section('title', 'Báo cáo Family Accounts')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Báo cáo Family Accounts
                    </h1>
                    <p class="text-muted mb-0">Thống kê và phân tích family accounts</p>
                </div>
                <div>
                    <a href="{{ route('admin.family-accounts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-home fa-2x mb-2"></i>
                    <h3 class="mb-1">{{ $stats['total_families'] }}</h3>
                    <small>Tổng Family</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <h3 class="mb-1">{{ $stats['active_families'] }}</h3>
                    <small>Đang hoạt động</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h3 class="mb-1">{{ $stats['total_members'] }}</h3>
                    <small>Tổng thành viên</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <h3 class="mb-1">{{ $stats['expired_families'] }}</h3>
                    <small>Hết hạn</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <h3 class="mb-1">{{ $stats['expiring_soon'] }}</h3>
                    <small>Sắp hết hạn</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-percentage fa-2x mb-2"></i>
                    <h3 class="mb-1">{{ $stats['total_families'] > 0 ? round(($stats['active_families'] / $stats['total_families']) * 100, 1) : 0 }}%</h3>
                    <small>Tỷ lệ hoạt động</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analysis -->
    <div class="row mb-4">
        <!-- Package Distribution -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-pie-chart me-2"></i>
                        Phân bổ theo gói dịch vụ
                    </h6>
                </div>
                <div class="card-body">
                    @if($packageStats->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Gói dịch vụ</th>
                                        <th class="text-end">Số lượng</th>
                                        <th class="text-end">Tỷ lệ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($packageStats as $package)
                                        @php
                                            $percentage = $stats['total_families'] > 0 ? round(($package->count / $stats['total_families']) * 100, 1) : 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $package->name }}</td>
                                            <td class="text-end">
                                                <span class="badge bg-primary">{{ $package->count }}</span>
                                            </td>
                                            <td class="text-end">{{ $percentage }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">Chưa có dữ liệu</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Family accounts gần đây
                    </h6>
                </div>
                <div class="card-body">
                    @if($recentFamilies->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentFamilies as $family)
                                <div class="list-group-item d-flex justify-content-between align-items-center p-2">
                                    <div>
                                        <h6 class="mb-1">{{ $family->family_name }}</h6>
                                        <small class="text-muted">
                                            {{ $family->servicePackage->name ?? 'N/A' }} • 
                                            {{ $family->current_members }}/{{ $family->max_members }} thành viên
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-{{ $family->status === 'active' ? 'success' : 'warning' }}">
                                            {{ ucfirst($family->status) }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $family->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">Chưa có family account nào</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analysis -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-analytics me-2"></i>
                        Phân tích chi tiết
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Thống kê thành viên</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-users text-primary me-2"></i>
                                    <strong>Tổng thành viên:</strong> {{ $stats['total_members'] }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-calculator text-info me-2"></i>
                                    <strong>Trung bình/Family:</strong> 
                                    {{ $stats['total_families'] > 0 ? round($stats['total_members'] / $stats['total_families'], 1) : 0 }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-chart-line text-success me-2"></i>
                                    <strong>Tỷ lệ lấp đầy:</strong>
                                    @php
                                        $totalCapacity = \App\Models\FamilyAccount::sum('max_members');
                                        $utilizationRate = $totalCapacity > 0 ? round(($stats['total_members'] / $totalCapacity) * 100, 1) : 0;
                                    @endphp
                                    {{ $utilizationRate }}%
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-4">
                            <h6>Trạng thái hoạt động</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <strong>Hoạt động:</strong> {{ $stats['active_families'] }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-clock text-warning me-2"></i>
                                    <strong>Hết hạn:</strong> {{ $stats['expired_families'] }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                    <strong>Sắp hết hạn (7 ngày):</strong> {{ $stats['expiring_soon'] }}
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-4">
                            <h6>Hành động khuyến nghị</h6>
                            <ul class="list-unstyled">
                                @if($stats['expiring_soon'] > 0)
                                    <li class="mb-2">
                                        <i class="fas fa-bell text-warning me-2"></i>
                                        Nhắc nhở {{ $stats['expiring_soon'] }} family sắp hết hạn
                                    </li>
                                @endif
                                @if($stats['expired_families'] > 0)
                                    <li class="mb-2">
                                        <i class="fas fa-refresh text-info me-2"></i>
                                        Gia hạn {{ $stats['expired_families'] }} family đã hết hạn
                                    </li>
                                @endif
                                @if($utilizationRate < 70)
                                    <li class="mb-2">
                                        <i class="fas fa-users-plus text-primary me-2"></i>
                                        Tỷ lệ lấp đầy thấp - có thể tăng marketing
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto refresh every 5 minutes
setTimeout(function() {
    location.reload();
}, 300000);

// Add some basic charts if Chart.js is available
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart !== 'undefined') {
        // Package distribution pie chart
        const ctx = document.getElementById('packageChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($packageStats->pluck('name')) !!},
                    datasets: [{
                        data: {!! json_encode($packageStats->pluck('count')) !!},
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF',
                            '#FF9F40'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    }
});
</script>
@endpush
@endsection
