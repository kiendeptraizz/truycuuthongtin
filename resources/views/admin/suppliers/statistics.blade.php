@extends('layouts.admin')

@section('title', 'Thống kê nhà cung cấp')

@section('styles')
<style>
    .stats-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .priority-badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
        border-radius: 50px;
    }

    .service-item {
        padding: 0.75rem;
        border-radius: 10px;
        background: #f8f9fa;
        margin-bottom: 0.5rem;
        border-left: 4px solid #007bff;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800 fw-bold">
                <i class="fas fa-chart-bar me-2 text-primary"></i>
                Thống kê nhà cung cấp
            </h1>
            <p class="mb-0 text-muted">Báo cáo và phân tích tổng quan về nhà cung cấp</p>
        </div>
    </div>

    <!-- Current Suppliers Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="fw-bold text-primary mb-3">
                <i class="fas fa-building me-2"></i>
                Nhà cung cấp hiện tại
            </h5>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng số nhà cung cấp
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $currentStats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="stats-icon bg-primary">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tổng giá trị
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ formatCurrency($currentStats['total_value']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="stats-icon bg-success">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Trung bình sản phẩm/NCC
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $currentStats['avg_products_per_supplier'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="stats-icon bg-info">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Potential Suppliers Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="fw-bold text-primary mb-3">
                <i class="fas fa-users me-2"></i>
                Nhà cung cấp tiềm năng
            </h5>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng số tiềm năng
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $potentialStats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="stats-icon bg-primary">
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
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Ưu tiên cao
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $potentialStats['high_priority'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="stats-icon bg-danger">
                                <i class="fas fa-exclamation"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $potentialStats['medium_priority'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="stats-icon bg-warning">
                                <i class="fas fa-minus"></i>
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
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Ưu tiên thấp
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $potentialStats['low_priority'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="stats-icon bg-secondary">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Distribution -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>
                        Phân bố dịch vụ hiện tại
                    </h6>
                </div>
                <div class="card-body">
                    @if($serviceDistribution->count() > 0)
                        @foreach($serviceDistribution as $service)
                            <div class="service-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">{{ $service->product_name }}</span>
                                    <span class="badge bg-primary">{{ $service->count }} NCC</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có dữ liệu dịch vụ</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>
                        Dịch vụ tiềm năng
                    </h6>
                </div>
                <div class="card-body">
                    @if($potentialServiceDistribution->count() > 0)
                        @foreach($potentialServiceDistribution as $service)
                            <div class="service-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">{{ $service->service_name }}</span>
                                    <span class="badge bg-success">{{ $service->count }} NCC</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có dữ liệu dịch vụ tiềm năng</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        Giá trị ước tính tiềm năng
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="h2 mb-3 font-weight-bold text-success">
                            {{ formatCurrency($potentialStats['total_estimated_value']) }}
                        </div>
                        <p class="text-muted">Tổng giá trị ước tính từ các nhà cung cấp tiềm năng</p>
                        <div class="mt-3">
                            <small class="text-muted">
                                Trung bình {{ $potentialStats['avg_services_per_supplier'] }} dịch vụ/NCC
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>
                        Tỷ lệ ưu tiên
                    </h6>
                </div>
                <div class="card-body">
                    @if($potentialStats['total'] > 0)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-danger fw-bold">Cao</span>
                                <span>{{ round(($potentialStats['high_priority'] / $potentialStats['total']) * 100, 1) }}%</span>
                            </div>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-danger" style="width: {{ ($potentialStats['high_priority'] / $potentialStats['total']) * 100 }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-warning fw-bold">Trung bình</span>
                                <span>{{ round(($potentialStats['medium_priority'] / $potentialStats['total']) * 100, 1) }}%</span>
                            </div>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-warning" style="width: {{ ($potentialStats['medium_priority'] / $potentialStats['total']) * 100 }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-secondary fw-bold">Thấp</span>
                                <span>{{ round(($potentialStats['low_priority'] / $potentialStats['total']) * 100, 1) }}%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-secondary" style="width: {{ ($potentialStats['low_priority'] / $potentialStats['total']) * 100 }}%"></div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có dữ liệu để hiển thị</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
