@extends('layouts.admin')

@section('title', 'Thống kê doanh thu chi tiết')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
<link href="{{ asset('css/revenue-dashboard.css') }}" rel="stylesheet">
<style>
.growth-positive {
    color: #28a745 !important;
}
.growth-negative {
    color: #dc3545 !important;
}
.growth-neutral {
    color: #6c757d !important;
}
.stats-card {
    transition: all 0.3s ease;
    cursor: pointer;
}
.stats-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
}
.border-left-success {
    border-left: 0.25rem solid #28a745 !important;
}
.border-left-info {
    border-left: 0.25rem solid #17a2b8 !important;
}
.border-left-warning {
    border-left: 0.25rem solid #ffc107 !important;
}
.border-left-danger {
    border-left: 0.25rem solid #dc3545 !important;
}
.profit-highlight {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.profit-highlight .text-gray-800 {
    color: white !important;
}
.profit-highlight .text-muted {
    color: rgba(255,255,255,0.8) !important;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header với thời gian thực -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-line text-primary me-2"></i>
                Thống kê doanh thu chi tiết
            </h1>
            <p class="text-muted mb-0">Dashboard phân tích doanh thu, lợi nhuận và hiệu suất kinh doanh</p>
        </div>
        <div class="text-muted">
            <i class="fas fa-clock me-1"></i>
            <span id="current-time"></span>
        </div>
    </div>

    <!-- Enhanced Quick Stats với so sánh -->
    <div class="row mb-4">
        <!-- Doanh thu hôm nay -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Doanh thu hôm nay
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ format_currency($stats['today_revenue']) }}
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">{{ $stats['today_orders'] }} đơn hàng</small>
                                <small id="today-vs-yesterday" class="growth-neutral">
                                    <i class="fas fa-arrows-alt-h"></i> So sánh...
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tuần này -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Doanh thu tuần này
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ format_currency($stats['week_revenue']) }}
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">{{ $stats['week_orders'] }} đơn hàng</small>
                                <small id="week-vs-last-week" class="growth-neutral">
                                    <i class="fas fa-arrows-alt-h"></i> So sánh...
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tháng này -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Doanh thu tháng này
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ format_currency($stats['month_revenue']) }}
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">{{ $stats['month_orders'] }} đơn hàng</small>
                                <small id="month-vs-last-month" class="growth-neutral">
                                    <i class="fas fa-arrows-alt-h"></i> So sánh...
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khách hàng mới -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Khách hàng mới tháng này
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['new_customers_month']) }}
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Hôm nay: {{ $stats['new_customers_today'] }}</small>
                                <small class="text-info">
                                    <i class="fas fa-chart-line"></i> {{ $stats['conversion_rate_month'] }}% convert
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profit Stats Row -->
    <div class="row mb-4">
        <!-- Lợi nhuận hôm nay -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Lợi nhuận hôm nay
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ format_currency($stats['today_profit'] ?? 0) }}
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">
                                    Margin: {{ $stats['today_revenue'] > 0 ? round(($stats['today_profit'] ?? 0) / $stats['today_revenue'] * 100, 1) : 0 }}%
                                </small>
                                <small id="today-profit-vs-yesterday" class="growth-neutral">
                                    <i class="fas fa-arrows-alt-h"></i> So sánh...
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lợi nhuận tuần này -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Lợi nhuận tuần này
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ format_currency($stats['week_profit'] ?? 0) }}
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">
                                    Margin: {{ $stats['week_revenue'] > 0 ? round(($stats['week_profit'] ?? 0) / $stats['week_revenue'] * 100, 1) : 0 }}%
                                </small>
                                <small id="week-profit-vs-last-week" class="growth-neutral">
                                    <i class="fas fa-arrows-alt-h"></i> So sánh...
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trending-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lợi nhuận tháng này -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Lợi nhuận tháng này
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ format_currency($stats['month_profit'] ?? 0) }}
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">
                                    Margin: {{ $stats['month_revenue'] > 0 ? round(($stats['month_profit'] ?? 0) / $stats['month_revenue'] * 100, 1) : 0 }}%
                                </small>
                                <small id="month-profit-vs-last-month" class="growth-neutral">
                                    <i class="fas fa-arrows-alt-h"></i> So sánh...
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROI Summary -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Tỷ suất lợi nhuận tháng
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['month_revenue'] > 0 ? round(($stats['month_profit'] ?? 0) / $stats['month_revenue'] * 100, 1) : 0 }}%
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">ROI Performance</small>
                                <small class="text-{{ ($stats['month_revenue'] > 0 ? round(($stats['month_profit'] ?? 0) / $stats['month_revenue'] * 100, 1) : 0) >= 20 ? 'success' : (($stats['month_revenue'] > 0 ? round(($stats['month_profit'] ?? 0) / $stats['month_revenue'] * 100, 1) : 0) >= 10 ? 'warning' : 'danger') }}">
                                    @if(($stats['month_revenue'] > 0 ? round(($stats['month_profit'] ?? 0) / $stats['month_revenue'] * 100, 1) : 0) >= 20)
                                        <i class="fas fa-thumbs-up"></i> Tốt
                                    @elseif(($stats['month_revenue'] > 0 ? round(($stats['month_profit'] ?? 0) / $stats['month_revenue'] * 100, 1) : 0) >= 10)
                                        <i class="fas fa-minus-circle"></i> Trung bình
                                    @else
                                        <i class="fas fa-exclamation-triangle"></i> Thấp
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-primary shadow profit-highlight">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-2">
                                <h4 class="text-white">{{ format_currency($stats['month_revenue']) }}</h4>
                                <small class="text-white-50">Doanh thu tháng này</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2">
                                <h4 class="text-white">{{ format_currency($stats['month_profit'] ?? 0) }}</h4>
                                <small class="text-white-50">Lợi nhuận tháng này</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2">
                                <h4 class="text-white">{{ $stats['month_orders'] }}</h4>
                                <small class="text-white-50">Đơn hàng tháng này</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2">
                                <h4 class="text-white">
                                    {{ $stats['month_revenue'] > 0 ? round(($stats['month_profit'] ?? 0) / $stats['month_revenue'] * 100, 1) : 0 }}%
                                </h4>
                                <small class="text-white-50">Tỷ suất lợi nhuận</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters với nhiều tùy chọn hơn -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter me-2"></i>
                    Bộ lọc thống kê nâng cao
                </h6>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-primary" onclick="refreshAllData()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="exportData()">
                        <i class="fas fa-file-excel"></i> Export
                    </button>
                    <button type="button" class="btn btn-outline-warning" onclick="debugAjax()">
                        <i class="fas fa-bug"></i> Debug
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-2">
                    <label for="start_date" class="form-label">Từ ngày</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="{{ $today->format('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <label for="end_date" class="form-label">Đến ngày</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="{{ $today->format('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <label for="group_by" class="form-label">Nhóm theo</label>
                    <select class="form-select" id="group_by" name="group_by">
                        <option value="day">Theo ngày</option>
                        <option value="week">Theo tuần</option>
                        <option value="month">Theo tháng</option>
                        <option value="year">Theo năm</option>
                        <option value="hour">Theo giờ</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="chart_type" class="form-label">Loại biểu đồ</label>
                    <select class="form-select" id="chart_type" name="chart_type">
                        <option value="line">Đường</option>
                        <option value="bar">Cột</option>
                        <option value="area">Vùng</option>
                        <option value="pie">Tròn</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="comparison_period" class="form-label">So sánh với</label>
                    <select class="form-select" id="comparison_period" name="comparison_period">
                        <option value="none">Không so sánh</option>
                        <option value="previous">Kỳ trước</option>
                        <option value="last_year">Cùng kỳ năm trước</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>
                            Áp dụng
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- Quick Date Filters -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="setDateRange('today')">Hôm nay</button>
                        <button type="button" class="btn btn-outline-primary" onclick="setDateRange('yesterday')">Hôm qua</button>
                        <button type="button" class="btn btn-outline-primary" onclick="setDateRange('this_week')" title="Từ thứ 2 đến hôm nay">Tuần này</button>
                        <button type="button" class="btn btn-outline-primary" onclick="setDateRange('this_month')">Tháng này</button>
                        <button type="button" class="btn btn-outline-primary" onclick="setDateRange('last_month')">Tháng trước</button>
                        <button type="button" class="btn btn-outline-primary" onclick="setDateRange('this_year')">Năm nay</button>
                        <button type="button" class="btn btn-outline-primary" onclick="setDateRange('last_30_days')">30 ngày qua</button>
                        <button type="button" class="btn btn-outline-primary" onclick="setDateRange('last_90_days')">90 ngày qua</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Tabs -->
    <div class="card">
        <div class="card-header p-0">
            <ul class="nav nav-pills nav-fill" id="dashboard-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab">
                        <i class="fas fa-tachometer-alt me-1"></i> Tổng quan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="performance-tab" data-bs-toggle="pill" data-bs-target="#performance" type="button" role="tab">
                        <i class="fas fa-chart-line me-1"></i> Hiệu suất
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="customers-tab" data-bs-toggle="pill" data-bs-target="#customers" type="button" role="tab">
                        <i class="fas fa-users me-1"></i> Khách hàng
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="services-tab" data-bs-toggle="pill" data-bs-target="#services" type="button" role="tab">
                        <i class="fas fa-cogs me-1"></i> Dịch vụ
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="forecast-tab" data-bs-toggle="pill" data-bs-target="#forecast" type="button" role="tab">
                        <i class="fas fa-crystal-ball me-1"></i> Dự báo
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="detailed-tab" data-bs-toggle="pill" data-bs-target="#detailed" type="button" role="tab">
                        <i class="fas fa-table me-1"></i> Chi tiết
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="dashboard-content">
                <!-- Tab Tổng quan -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div class="row">
                        <!-- Main Chart -->
                        <div class="col-lg-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-chart-area me-2"></i>
                                        Biểu đồ doanh thu & lợi nhuận
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="mainChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Stats -->
                        <div class="col-lg-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-calculator me-2"></i>
                                        Tóm tắt thống kê
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="summaryStats">
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                                            <p class="mt-2">Đang tải...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Top Services -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-medal me-2"></i>
                                        Top 5 dịch vụ bán chạy
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="serviceStats">
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                                            <p class="mt-2">Đang tải...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Growth Comparison -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-trending-up text-success me-2"></i>
                                        So sánh tăng trưởng
                                    </h5>
                                    <div id="growthComparison" class="row">
                                        <!-- Will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-list-alt me-2"></i>
                                        Danh sách đơn hàng gần đây
                                    </h6>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" onclick="refreshOrdersList()">
                                            <i class="fas fa-sync-alt"></i> Làm mới
                                        </button>
                                        <button type="button" class="btn btn-outline-success" onclick="exportOrdersList()">
                                            <i class="fas fa-file-excel"></i> Xuất Excel
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="overviewOrdersTable" width="100%" cellspacing="0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Khách hàng</th>
                                                    <th>Dịch vụ</th>
                                                    <th>Doanh thu</th>
                                                    <th>Lợi nhuận</th>
                                                    <th>Margin (%)</th>
                                                    <th>Ngày tạo</th>
                                                    <th>Trạng thái</th>
                                                    <th>Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody id="overviewOrdersTableBody">
                                                <tr>
                                                    <td colspan="9" class="text-center text-muted py-4">
                                                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                                                        <p class="mt-2">Đang tải danh sách đơn hàng...</p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Pagination and Info -->
                                    <div class="row mt-3">
                                        <div class="col-sm-12 col-md-5">
                                            <div class="dataTables_info" id="overviewOrdersInfo">
                                                Hiển thị <span id="ordersStart">0</span> đến <span id="ordersEnd">0</span> 
                                                của <span id="ordersTotal">0</span> đơn hàng
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-7">
                                            <div class="dataTables_paginate paging_simple_numbers" id="overviewOrdersPagination">
                                                <ul class="pagination justify-content-end">
                                                    <li class="paginate_button page-item previous disabled">
                                                        <a href="#" class="page-link">Trước</a>
                                                    </li>
                                                    <li class="paginate_button page-item active">
                                                        <a href="#" class="page-link">1</a>
                                                    </li>
                                                    <li class="paginate_button page-item next disabled">
                                                        <a href="#" class="page-link">Sau</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Hiệu suất -->
                <div class="tab-pane fade" id="performance" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-clock me-2"></i>
                                        Hiệu suất theo giờ (hôm nay)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="hourlyChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-percentage me-2"></i>
                                        Margin lợi nhuận theo thời gian
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="marginChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Metrics -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-gauge-high text-info me-2"></i>
                                        Các chỉ số hiệu suất
                                    </h5>
                                    <div id="performanceMetrics" class="row">
                                        <!-- Will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Khách hàng -->
                <div class="tab-pane fade" id="customers" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-crown me-2"></i>
                                        Top khách hàng VIP
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="topCustomers">
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                                            <p class="mt-2">Đang tải...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-chart-pie me-2"></i>
                                        Phân bố khách hàng
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="height: 300px;">
                                        <canvas id="customerChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Dịch vụ -->
                <div class="tab-pane fade" id="services" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-list-alt me-2"></i>
                                        Thống kê theo danh mục dịch vụ
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="categoryStats">
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                                            <p class="mt-2">Đang tải...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card shadow">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-chart-donut me-2"></i>
                                        Tỷ lệ doanh thu theo danh mục
                                    </h6>
                                    <button class="btn btn-sm btn-outline-primary" onclick="loadCategoryStats()" title="Refresh biểu đồ">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="height: 300px;">
                                        <canvas id="categoryChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Dự báo -->
                <div class="tab-pane fade" id="forecast" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-chart-line me-2"></i>
                                        Dự báo doanh thu 7 ngày tới
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="forecastChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-magic me-2"></i>
                                        Thông tin dự báo
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="forecastInfo">
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                                            <p class="mt-2">Đang tính toán...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Chi tiết -->
                <div class="tab-pane fade" id="detailed" role="tabpanel">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-table me-2"></i>
                                Chi tiết đơn hàng
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Khách hàng</th>
                                            <th>Dịch vụ</th>
                                            <th>Doanh thu</th>
                                            <th>Lợi nhuận</th>
                                            <th>Margin (%)</th>
                                            <th>Ngày tạo</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ordersTableBody">
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">
                                                <i class="fas fa-spinner fa-spin fa-2x"></i>
                                                <p class="mt-2">Đang tải dữ liệu...</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@include('admin.revenue.modals')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/date-fns@2.29.3/index.min.js"></script>

<script>
// Global variables
let mainChart, hourlyChart, marginChart, customerChart, categoryChart, forecastChart;
let currentFilters = {};

$(document).ready(function() {
    // Setup CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        timeout: 30000 // 30 seconds timeout
    });
    
    console.log('Document ready, starting initialization...');
    
    // Initialize
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);
    
    // Đảm bảo ngày mặc định trong input sử dụng local timezone
    ensureLocalDateInputs();
    
    loadInitialData();
    
    // Event listeners
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadAllData();
    });
    
    $('#start_date, #end_date, #group_by, #chart_type').on('change', function() {
        console.log('Filter changed, reloading all data');
        
        // Force reload current tab data immediately
        const activeTab = $('.nav-pills .nav-link.active').attr('data-bs-target');
        console.log('Current active tab on change:', activeTab);
        
        loadAllData();
    });
    
    // Tab change events
    $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('data-bs-target');
        handleTabChange(target);
    });
});

function updateCurrentTime() {
    const now = new Date();
    $('#current-time').text(now.toLocaleString('vi-VN'));
}

// Helper function để lấy ngày local (không bị ảnh hưởng timezone)
function getLocalDateString(date) {
    const year = date.getFullYear();
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const day = date.getDate().toString().padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function ensureLocalDateInputs() {
    const today = new Date();
    const todayLocal = getLocalDateString(today);
    
    // Đảm bảo input date hiển thị đúng ngày local
    if (!$('#start_date').val()) {
        $('#start_date').val(todayLocal);
    }
    if (!$('#end_date').val()) {
        $('#end_date').val(todayLocal);
    }
    
    console.log('Date inputs ensured with local date:', todayLocal);
}

function loadInitialData() {
    console.log('loadInitialData called');
    // Load growth comparisons first
    console.log('Loading growth stats...');
    loadGrowthStats().then(function(data) {
        console.log('Growth stats loaded successfully:', data);
    }).catch(function(xhr) {
        console.error('Growth stats failed:', xhr);
    });
    // Then load main data
    loadAllData();
}

function loadAllData() {
    console.log('loadAllData called');
    console.log('jQuery available:', typeof $ !== 'undefined');
    console.log('Start date input exists:', $('#start_date').length > 0);
    console.log('End date input exists:', $('#end_date').length > 0);
    
    currentFilters = {
        start_date: $('#start_date').val() || getLocalDateString(new Date()),
        end_date: $('#end_date').val() || getLocalDateString(new Date()),
        group_by: $('#group_by').val() || 'day',
        chart_type: $('#chart_type').val() || 'line'
    };
    
    console.log('Loading data with filters:', currentFilters);
    
    // Show loading states
    showLoadingStates();
    
    // Load different datasets
    Promise.all([
        loadRevenueData(),
        loadServiceStats(),
        loadGrowthStats()
    ]).then(() => {
        console.log('All data loaded successfully');
        
        // Reload data for current active tab (especially important for services tab)
        const activeTab = $('.nav-pills .nav-link.active').attr('data-bs-target');
        console.log('Current active tab:', activeTab);
        if (activeTab && activeTab !== '#overview') {
            console.log('Reloading data for active tab:', activeTab);
            handleTabChange(activeTab);
        }
    }).catch(error => {
        console.error('Error loading data:', error);
        
        // Show error states instead of loading forever
        $('#summaryStats').html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Lỗi khi tải dữ liệu thống kê. 
                <button class="btn btn-sm btn-outline-danger ms-2" onclick="loadAllData()">
                    <i class="fas fa-redo"></i> Thử lại
                </button>
            </div>
        `);
        
        $('#serviceStats').html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Lỗi khi tải dữ liệu dịch vụ.
                <button class="btn btn-sm btn-outline-danger ms-2" onclick="loadServiceStats()">
                    <i class="fas fa-redo"></i> Thử lại
                </button>
            </div>
        `);
    });
}

function showLoadingStates() {
    const loadingHtml = `
        <div class="text-center text-muted py-4">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">Đang tải dữ liệu...</p>
        </div>
    `;
    
    $('#summaryStats').html(loadingHtml);
    $('#serviceStats').html(loadingHtml);
    $('#categoryStats').html(loadingHtml); // Add loading state for category stats
    
    const ordersLoadingHtml = `
        <tr><td colspan="9" class="text-center text-muted py-4">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">Đang tải dữ liệu...</p>
        </td></tr>
    `;
    $('#ordersTableBody').html(ordersLoadingHtml);
    $('#overviewOrdersTableBody').html(ordersLoadingHtml);
}

function loadRevenueData() {
    return $.ajax({
        url: '{{ route("admin.revenue.data") }}',
        method: 'GET',
        data: currentFilters,
        success: function(response) {
            console.log('Revenue data loaded:', response);
            
            // Store orders data globally for export
            window.currentOrdersData = response.orders;
            
            updateMainChart(response.chart_data);
            updateSummaryStats(response.summary);
            updateOrdersTable(response.orders);
        },
        error: function(xhr, status, error) {
            console.error('Error loading revenue data:', error);
            handleDataError('revenue');
        }
    });
}

function loadServiceStats() {
    return $.ajax({
        url: '{{ route("admin.revenue.service-stats") }}',
        method: 'GET',
        data: currentFilters,
        success: function(response) {
            updateServiceStats(response);
        },
        error: function(xhr, status, error) {
            console.error('Error loading service stats:', error);
            handleDataError('services');
        }
    });
}

function loadGrowthStats() {
    // Load growth stats for different periods
    const promises = [
        loadGrowthStatsForPeriod('day'),
        loadGrowthStatsForPeriod('week'), 
        loadGrowthStatsForPeriod('month')
    ];
    
    return Promise.all(promises).then(function(responses) {
        console.log('All growth stats loaded:', responses);
        
        // Update comparison texts for each period
        if (responses[0]) { // day
            updateComparisonText('today-vs-yesterday', responses[0].growth.revenue_growth);
            updateComparisonText('today-profit-vs-yesterday', responses[0].growth.profit_growth);
        }
        
        if (responses[1]) { // week
            updateComparisonText('week-vs-last-week', responses[1].growth.revenue_growth);
            updateComparisonText('week-profit-vs-last-week', responses[1].growth.profit_growth);
        }
        
        if (responses[2]) { // month
            updateComparisonText('month-vs-last-month', responses[2].growth.revenue_growth);
            updateComparisonText('month-profit-vs-last-month', responses[2].growth.profit_growth);
        }
        
        // Use day stats for the main growth comparison display
        if (responses[0]) {
            updateGrowthComparison(responses[0]);
        }
    }).catch(function(error) {
        console.error('Error loading growth stats:', error);
        showGrowthError();
    });
}

function loadGrowthStatsForPeriod(period) {
    return $.ajax({
        url: '{{ route("admin.revenue.growth-stats") }}',
        method: 'GET',
        data: { period: period },
        success: function(response) {
            console.log(`Growth stats for ${period} loaded:`, response);
            return response;
        },
        error: function(xhr, status, error) {
            console.error(`Error loading growth stats for ${period}:`, error);
            return null;
        }
    });
}

function showGrowthError() {
    $('#growthComparison').html(`
        <div class="col-12">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Không thể tải dữ liệu so sánh tăng trưởng. 
                <button class="btn btn-sm btn-outline-primary ms-2" onclick="loadGrowthStats()">
                    <i class="fas fa-redo"></i> Thử lại
                </button>
            </div>
        </div>
    `);
}

function loadCustomerStats() {
    return $.ajax({
        url: '{{ route("admin.revenue.customer-stats") }}',
        method: 'GET',
        data: currentFilters,
        success: function(response) {
            updateCustomerStats(response);
        },
        error: function(xhr, status, error) {
            console.error('Error loading customer stats:', error);
        }
    });
}

function loadCategoryStats() {
    console.log('Loading category stats with filters:', currentFilters);
    return $.ajax({
        url: '{{ route("admin.revenue.category-stats") }}',
        method: 'GET',
        data: currentFilters,
        success: function(response) {
            console.log('Category stats response:', response);
            updateCategoryStats(response);
        },
        error: function(xhr, status, error) {
            console.error('Error loading category stats:', error, xhr.responseJSON);
            // Show error message
            $('#categoryStats').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Lỗi khi tải dữ liệu danh mục: ${error}
                    <button class="btn btn-sm btn-outline-danger ms-2" onclick="loadCategoryStats()">
                        <i class="fas fa-redo"></i> Thử lại
                    </button>
                </div>
            `);
        }
    });
}

function loadHourlyStats() {
    return $.ajax({
        url: '{{ route("admin.revenue.hourly-stats") }}',
        method: 'GET',
        data: { date: $('#start_date').val() },
        success: function(response) {
            updateHourlyChart(response);
        },
        error: function(xhr, status, error) {
            console.error('Error loading hourly stats:', error);
        }
    });
}

function loadForecastStats() {
    return $.ajax({
        url: '{{ route("admin.revenue.forecast-stats") }}',
        method: 'GET',
        data: { days: 7, base_days: 30 },
        success: function(response) {
            updateForecastChart(response);
            updateForecastInfo(response.metrics);
        },
        error: function(xhr, status, error) {
            console.error('Error loading forecast stats:', error);
        }
    });
}

function loadPerformanceStats() {
    return $.ajax({
        url: '{{ route("admin.revenue.performance-stats") }}',
        method: 'GET',
        data: currentFilters,
        success: function(response) {
            updatePerformanceMetrics(response);
        },
        error: function(xhr, status, error) {
            console.error('Error loading performance stats:', error);
        }
    });
}

function updateMainChart(data) {
    const ctx = document.getElementById('mainChart').getContext('2d');
    
    if (mainChart) {
        mainChart.destroy();
    }
    
    const chartType = $('#chart_type').val() || 'line';
    
    mainChart = new Chart(ctx, {
        type: chartType,
        data: {
            labels: data.map(item => item.period),
            datasets: [
                {
                    label: 'Doanh thu',
                    data: data.map(item => item.revenue),
                    borderColor: '#4e73df',
                    backgroundColor: chartType === 'line' ? 'rgba(78, 115, 223, 0.1)' : '#4e73df',
                    tension: 0.3,
                    fill: chartType === 'area'
                },
                {
                    label: 'Lợi nhuận',
                    data: data.map(item => item.profit),
                    borderColor: '#1cc88a',
                    backgroundColor: chartType === 'line' ? 'rgba(28, 200, 138, 0.1)' : '#1cc88a',
                    tension: 0.3,
                    fill: chartType === 'area'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + formatCurrency(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return formatCurrency(value);
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
}

function updateSummaryStats(summary) {
    const html = `
        <div class="metric-item">
            <div class="d-flex justify-content-between">
                <strong>Tổng đơn hàng:</strong>
                <span class="text-primary">${summary.total_orders.toLocaleString()}</span>
            </div>
        </div>
        <div class="metric-item">
            <div class="d-flex justify-content-between">
                <strong>Tổng doanh thu:</strong>
                <span class="text-success">${formatCurrency(summary.total_revenue)}</span>
            </div>
        </div>
        <div class="metric-item">
            <div class="d-flex justify-content-between">
                <strong>Tổng lợi nhuận:</strong>
                <span class="text-info">${formatCurrency(summary.total_profit)}</span>
            </div>
        </div>
        <div class="metric-item">
            <div class="d-flex justify-content-between">
                <strong>Giá trị đơn TB:</strong>
                <span class="text-warning">${formatCurrency(summary.average_order_value)}</span>
            </div>
        </div>
        <div class="metric-item">
            <div class="d-flex justify-content-between">
                <strong>Margin lợi nhuận:</strong>
                <span class="text-${summary.profit_margin >= 20 ? 'success' : summary.profit_margin >= 10 ? 'warning' : 'danger'}">${summary.profit_margin}%</span>
            </div>
        </div>
    `;
    
    $('#summaryStats').html(html);
}

function updateServiceStats(services) {
    if (services.length === 0) {
        $('#serviceStats').html('<p class="text-muted text-center">Không có dữ liệu</p>');
        return;
    }

    const totalRevenue = services.reduce((sum, service) => sum + parseFloat(service.total_revenue || 0), 0);

    let html = '';
    services.slice(0, 5).forEach((service, index) => {
        const percentage = totalRevenue > 0 ? 
            Math.round((parseFloat(service.total_revenue || 0) / totalRevenue) * 100) : 0;
        
        html += `
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-sm font-weight-bold">${service.name}</span>
                    <span class="text-sm text-muted">${service.orders_count} đơn</span>
                </div>
                <div class="progress mb-1" style="height: 6px;">
                    <div class="progress-bar bg-primary" role="progressbar" 
                         style="width: ${percentage}%" aria-valuenow="${percentage}" 
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">${formatCurrency(service.total_revenue)}</small>
                    <small class="text-success">+${formatCurrency(service.total_profit)}</small>
                </div>
                <div class="text-right">
                    <small class="text-info">${percentage}% tổng doanh thu</small>
                </div>
            </div>
        `;
    });
    
    $('#serviceStats').html(html);
}

function updateGrowthComparison(growth) {
    console.log('updateGrowthComparison called with:', growth);
    
    if (!growth || !growth.current_period || !growth.previous_period || !growth.growth) {
        console.error('Invalid growth data structure:', growth);
        $('#growthComparison').html(`
            <div class="col-12">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Dữ liệu so sánh tăng trưởng không hợp lệ
                </div>
            </div>
        `);
        return;
    }
    
    const html = `
        <div class="col-md-3">
            <div class="text-center">
                <h5 class="text-primary">Doanh thu</h5>
                <div class="h4 mb-1">${formatCurrency(growth.current_period.revenue)}</div>
                <div class="small ${getGrowthClass(growth.growth.revenue_growth)}">
                    <i class="fas fa-${getGrowthIcon(growth.growth.revenue_growth)} me-1"></i>
                    ${growth.growth.revenue_growth}%
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <h5 class="text-success">Đơn hàng</h5>
                <div class="h4 mb-1">${growth.current_period.orders.toLocaleString()}</div>
                <div class="small ${getGrowthClass(growth.growth.orders_growth)}">
                    <i class="fas fa-${getGrowthIcon(growth.growth.orders_growth)} me-1"></i>
                    ${growth.growth.orders_growth}%
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <h5 class="text-info">Lợi nhuận</h5>
                <div class="h4 mb-1">${formatCurrency(growth.current_period.profit)}</div>
                <div class="small ${getGrowthClass(growth.growth.profit_growth)}">
                    <i class="fas fa-${getGrowthIcon(growth.growth.profit_growth)} me-1"></i>
                    ${growth.growth.profit_growth}%
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <h5 class="text-warning">Khách hàng</h5>
                <div class="h4 mb-1">${growth.current_period.customers.toLocaleString()}</div>
                <div class="small ${getGrowthClass(growth.growth.customers_growth)}">
                    <i class="fas fa-${getGrowthIcon(growth.growth.customers_growth)} me-1"></i>
                    ${growth.growth.customers_growth}%
                </div>
            </div>
        </div>
    `;
    
    $('#growthComparison').html(html);
    
    // Note: Individual comparison texts are now handled in loadGrowthStats()
}

function updateComparisonText(elementId, growthPercent) {
    const element = document.getElementById(elementId);
    if (element) {
        element.className = `small ${getGrowthClass(growthPercent)}`;
        element.innerHTML = `<i class="fas fa-${getGrowthIcon(growthPercent)} me-1"></i>${growthPercent}%`;
    }
}

function getGrowthClass(percent) {
    if (percent > 0) return 'growth-positive';
    if (percent < 0) return 'growth-negative';
    return 'growth-neutral';
}

function getGrowthIcon(percent) {
    if (percent > 0) return 'arrow-up';
    if (percent < 0) return 'arrow-down';
    return 'minus';
}

function updateOrdersTable(orders) {
    if (orders.length === 0) {
        const emptyRow = `
            <tr>
                <td colspan="9" class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">Không có đơn hàng nào trong khoảng thời gian này</p>
                </td>
            </tr>
        `;
        $('#ordersTableBody').html(emptyRow);
        $('#overviewOrdersTableBody').html(emptyRow);
        updateOrdersInfo(0, 0, 0);
        return;
    }

    let html = '';
    const displayOrders = orders.slice(0, 25); // Hiển thị tối đa 25 đơn trong overview
    
    displayOrders.forEach((order, index) => {
        const statusBadge = getStatusBadge(order.status);
        const actionButtons = getActionButtons(order);
        const rowClass = index % 2 === 0 ? '' : 'table-row-alt';
        
        html += `
            <tr class="${rowClass}">
                <td class="fw-bold">#${order.id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm me-2">
                            <div class="avatar-title bg-primary text-white rounded-circle">
                                ${order.customer_display.charAt(0).toUpperCase()}
                            </div>
                        </div>
                        <div>
                            <div class="fw-medium">${order.customer_display}</div>
                            <small class="text-muted">${order.customer_code || 'N/A'}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-info bg-gradient text-wrap" style="max-width: 200px;">
                        ${order.service_name}
                    </span>
                </td>
                <td class="text-end">
                    <span class="fw-bold text-primary">${formatCurrency(order.revenue)}</span>
                </td>
                <td class="text-end">
                    <span class="fw-bold text-success">${formatCurrency(order.profit)}</span>
                </td>
                <td class="text-end">
                    <span class="badge bg-${getMarginBadgeClass(order.profit_margin)} bg-gradient">
                        ${order.profit_margin}%
                    </span>
                </td>
                <td>
                    <small class="text-muted">
                        <i class="fas fa-calendar-alt me-1"></i>
                        ${order.created_at}
                    </small>
                </td>
                <td>${statusBadge}</td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        ${actionButtons}
                    </div>
                </td>
            </tr>
        `;
    });
    
    // Update both tables
    $('#ordersTableBody').html(html);
    $('#overviewOrdersTableBody').html(html);
    
    // Update pagination info
    updateOrdersInfo(1, Math.min(25, orders.length), orders.length);
}

function getMarginBadgeClass(margin) {
    if (margin >= 30) return 'success';
    if (margin >= 20) return 'info';
    if (margin >= 10) return 'warning';
    if (margin > 0) return 'secondary';
    return 'danger';
}

function updateOrdersInfo(start, end, total) {
    $('#ordersStart').text(start);
    $('#ordersEnd').text(end);
    $('#ordersTotal').text(total);
}

function refreshOrdersList() {
    console.log('Refreshing orders list...');
    loadAllData();
}

function exportOrdersList() {
    // Tạo data để export
    const currentOrders = window.currentOrdersData || [];
    if (currentOrders.length === 0) {
        alert('Không có dữ liệu để xuất');
        return;
    }
    
    // Tạo CSV content
    const headers = ['ID', 'Khách hàng', 'Dịch vụ', 'Doanh thu', 'Lợi nhuận', 'Margin %', 'Ngày tạo', 'Trạng thái'];
    let csvContent = headers.join(',') + '\\n';
    
    currentOrders.forEach(order => {
        const row = [
            order.id,
            `"${order.customer_display}"`,
            `"${order.service_name}"`,
            order.revenue,
            order.profit,
            order.profit_margin,
            `"${order.created_at}"`,
            `"${order.status}"`
        ];
        csvContent += row.join(',') + '\\n';
    });
    
    // Download file
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `danh-sach-don-hang-${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function handleTabChange(target) {
    console.log('Tab changed to:', target);
    switch(target) {
        case '#performance':
            loadHourlyStats();
            loadPerformanceStats();
            break;
        case '#customers':
            loadCustomerStats();
            break;
        case '#services':
            console.log('Loading category stats with current filters:', currentFilters);
            loadCategoryStats();
            break;
        case '#forecast':
            loadForecastStats();
            break;
        case '#overview':
            // Overview data is already loaded in loadAllData()
            break;
    }
}

function updateHourlyChart(data) {
    const ctx = document.getElementById('hourlyChart').getContext('2d');
    
    if (hourlyChart) {
        hourlyChart.destroy();
    }
    
    hourlyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => `${item.hour}:00`),
            datasets: [{
                label: 'Đơn hàng',
                data: data.map(item => item.orders_count),
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                borderColor: '#4e73df',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

function updateForecastChart(data) {
    const ctx = document.getElementById('forecastChart').getContext('2d');
    
    if (forecastChart) {
        forecastChart.destroy();
    }
    
    const historicalLabels = data.historical_data.map(item => item.date);
    const forecastLabels = data.forecast.map(item => item.date);
    const allLabels = [...historicalLabels, ...forecastLabels];
    
    forecastChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: allLabels,
            datasets: [
                {
                    label: 'Doanh thu thực tế',
                    data: [...data.historical_data.map(item => item.revenue), ...Array(data.forecast.length).fill(null)],
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    tension: 0.3
                },
                {
                    label: 'Dự báo',
                    data: [...Array(data.historical_data.length).fill(null), ...data.forecast.map(item => item.projected_revenue)],
                    borderColor: '#e74a3b',
                    backgroundColor: 'rgba(231, 74, 59, 0.1)',
                    borderDash: [5, 5],
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return formatCurrency(value);
                        }
                    }
                }
            }
        }
    });
}

function updateCustomerStats(customers) {
    if (!customers || customers.length === 0) {
        $('#topCustomers').html('<p class="text-muted text-center">Không có dữ liệu khách hàng</p>');
        return;
    }

    let html = '<div class="table-responsive">';
    html += '<table class="table table-hover">';
    html += '<thead><tr><th>Khách hàng</th><th>Tổng đơn</th><th>Doanh thu</th><th>Lợi nhuận</th><th>GT TB/đơn</th></tr></thead>';
    html += '<tbody>';
    
    customers.forEach((customer, index) => {
        const rankClass = index < 3 ? ['text-warning', 'text-secondary', 'text-warning'][index] : 'text-muted';
        const rankIcon = index < 3 ? ['fa-crown', 'fa-medal', 'fa-award'][index] : 'fa-user';
        
        html += `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="fas ${rankIcon} ${rankClass} me-2"></i>
                        <div>
                            <div class="fw-bold">${customer.name}</div>
                            <small class="text-muted">${customer.customer_code || ''}</small>
                            <br><small class="text-muted">${customer.phone || ''}</small>
                        </div>
                    </div>
                </td>
                <td><span class="badge bg-primary">${customer.total_orders}</span></td>
                <td class="fw-bold text-success">${formatCurrency(customer.total_revenue)}</td>
                <td class="fw-bold text-info">${formatCurrency(customer.total_profit)}</td>
                <td class="text-muted">${formatCurrency(customer.avg_order_value)}</td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    $('#topCustomers').html(html);
    
    // Update customer chart
    updateCustomerChart(customers);
}

function updateCustomerChart(customers) {
    const ctx = document.getElementById('customerChart').getContext('2d');
    
    if (customerChart) {
        customerChart.destroy();
    }
    
    const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'];
    
    customerChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: customers.slice(0, 6).map(c => c.name),
            datasets: [{
                data: customers.slice(0, 6).map(c => c.total_revenue),
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 10,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + formatCurrency(context.raw);
                        }
                    }
                }
            }
        }
    });
}

function updateCategoryStats(categories) {
    if (!categories || categories.length === 0) {
        $('#categoryStats').html('<p class="text-muted text-center">Không có dữ liệu danh mục</p>');
        return;
    }

    let html = '<div class="row">';
    
    categories.forEach((category, index) => {
        const marginPercent = category.total_revenue > 0 ? 
            Math.round((category.total_profit / category.total_revenue) * 100) : 0;
        const marginClass = marginPercent >= 20 ? 'success' : marginPercent >= 10 ? 'warning' : 'danger';
        
        html += `
            <div class="col-md-6 mb-3">
                <div class="card border-left-primary h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="font-weight-bold text-primary mb-0">${category.name}</h6>
                            <span class="badge bg-${marginClass}">${marginPercent}% margin</span>
                        </div>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="text-xs text-muted mb-1">Đơn hàng</div>
                                <div class="h6 mb-0">${category.total_orders}</div>
                            </div>
                            <div class="col-4">
                                <div class="text-xs text-muted mb-1">Doanh thu</div>
                                <div class="h6 mb-0 text-success">${formatCurrency(category.total_revenue)}</div>
                            </div>
                            <div class="col-4">
                                <div class="text-xs text-muted mb-1">Lợi nhuận</div>
                                <div class="h6 mb-0 text-info">${formatCurrency(category.total_profit)}</div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-cogs me-1"></i>
                                ${category.services_count} dịch vụ | 
                                TB: ${formatCurrency(category.avg_order_value)}/đơn
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    $('#categoryStats').html(html);
    
    // Update category chart
    updateCategoryChart(categories);
}

function updateCategoryChart(categories) {
    console.log('Updating category chart with data:', categories);
    const ctx = document.getElementById('categoryChart').getContext('2d');
    
    if (categoryChart) {
        console.log('Destroying existing category chart');
        categoryChart.destroy();
        categoryChart = null;
    }
    
    const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'];
    
    console.log('Creating new category chart');
    categoryChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: categories.map(c => c.name),
            datasets: [{
                data: categories.map(c => c.total_revenue),
                backgroundColor: colors.slice(0, categories.length),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 8,
                        usePointStyle: true,
                        font: {
                            size: 11
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percent = Math.round((context.raw / total) * 100);
                            return context.label + ': ' + formatCurrency(context.raw) + ' (' + percent + '%)';
                        }
                    }
                }
            }
        }
    });
}

function updatePerformanceMetrics(performance) {
    if (!performance || performance.length === 0) {
        $('#performanceMetrics').html('<p class="text-muted text-center">Không có dữ liệu hiệu suất</p>');
        return;
    }

    // Tính toán các chỉ số tổng hợp
    const totalRevenue = performance.reduce((sum, p) => sum + parseFloat(p.total_revenue || 0), 0);
    const totalOrders = performance.reduce((sum, p) => sum + parseInt(p.orders_count || 0), 0);
    const totalCustomers = performance.reduce((sum, p) => sum + parseInt(p.unique_customers || 0), 0);
    const avgMargin = performance.reduce((sum, p) => sum + parseFloat(p.profit_margin || 0), 0) / performance.length;
    
    const avgOrderValue = totalOrders > 0 ? totalRevenue / totalOrders : 0;
    const avgRevenuePerCustomer = totalCustomers > 0 ? totalRevenue / totalCustomers : 0;
    
    const html = `
        <div class="col-md-3">
            <div class="metric-item text-center">
                <div class="h4 text-primary mb-1">${formatCurrency(avgOrderValue)}</div>
                <div class="text-xs text-muted">Giá trị đơn hàng TB</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-item text-center">
                <div class="h4 text-success mb-1">${formatCurrency(avgRevenuePerCustomer)}</div>
                <div class="text-xs text-muted">Doanh thu/Khách hàng</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-item text-center">
                <div class="h4 text-info mb-1">${Math.round(avgMargin * 10) / 10}%</div>
                <div class="text-xs text-muted">Margin TB</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-item text-center">
                <div class="h4 text-warning mb-1">${Math.round((totalOrders / performance.length) * 10) / 10}</div>
                <div class="text-xs text-muted">Đơn hàng/Kỳ</div>
            </div>
        </div>
    `;
    
    $('#performanceMetrics').html(html);
}

function updateForecastInfo(metrics) {
    const html = `
        <div class="forecast-item">
            <h6 class="text-white mb-2">
                <i class="fas fa-chart-line me-2"></i>
                Dự báo 7 ngày tới
            </h6>
            <div class="row text-center">
                <div class="col-6">
                    <div class="h5 text-white mb-1">${formatCurrency(metrics.total_forecast_revenue)}</div>
                    <small class="text-white-50">Doanh thu dự kiến</small>
                </div>
                <div class="col-6">
                    <div class="h5 text-white mb-1">${Math.round(metrics.total_forecast_orders)}</div>
                    <small class="text-white-50">Đơn hàng dự kiến</small>
                </div>
            </div>
        </div>
        
        <div class="metric-item">
            <div class="d-flex justify-content-between">
                <strong>Doanh thu TB/ngày:</strong>
                <span>${formatCurrency(metrics.avg_daily_revenue)}</span>
            </div>
        </div>
        
        <div class="metric-item">
            <div class="d-flex justify-content-between">
                <strong>Đơn hàng TB/ngày:</strong>
                <span>${Math.round(metrics.avg_daily_orders * 10) / 10}</span>
            </div>
        </div>
        
        <div class="metric-item">
            <div class="d-flex justify-content-between">
                <strong>Tốc độ tăng trưởng:</strong>
                <span class="${getGrowthClass(metrics.growth_rate)}">${metrics.growth_rate}%</span>
            </div>
        </div>
        
        <div class="alert alert-info mt-3">
            <small>
                <i class="fas fa-info-circle me-1"></i>
                Dự báo dựa trên dữ liệu 30 ngày gần nhất với độ tin cậy giảm dần theo thời gian.
            </small>
        </div>
    `;
    
    $('#forecastInfo').html(html);
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount || 0);
}

function getStatusBadge(status) {
    const badges = {
        'completed': '<span class="badge badge-success">Hoàn thành</span>',
        'pending': '<span class="badge badge-warning">Đang xử lý</span>',
        'cancelled': '<span class="badge badge-danger">Đã hủy</span>'
    };
    return badges[status] || '<span class="badge badge-secondary">Không xác định</span>';
}

function getActionButtons(order) {
    return `
        <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-primary btn-sm" onclick="editProfit(${order.id})" title="Sửa lợi nhuận">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-outline-info btn-sm" onclick="viewOrder(${order.id})" title="Xem chi tiết">
                <i class="fas fa-eye"></i>
            </button>
        </div>
    `;
}

function handleDataError(section) {
    const errorHtml = `
        <div class="text-center text-danger py-4">
            <i class="fas fa-exclamation-triangle fa-2x"></i>
            <p class="mt-2">Có lỗi xảy ra khi tải dữ liệu</p>
            <button class="btn btn-sm btn-outline-primary" onclick="loadAllData()">
                <i class="fas fa-redo"></i> Thử lại
            </button>
        </div>
    `;
    
    switch(section) {
        case 'revenue':
            $('#summaryStats').html(errorHtml);
            break;
        case 'services':
            $('#serviceStats').html(errorHtml);
            break;
    }
}

// Quick date range functions
function setDateRange(period) {
    const today = new Date();
    let startDate, endDate;
    
    switch(period) {
        case 'today':
            startDate = endDate = getLocalDateString(today);
            console.log('Today filter: Using local date', startDate, 'instead of UTC', today.toISOString().split('T')[0]);
            break;
        case 'yesterday':
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            startDate = endDate = getLocalDateString(yesterday);
            break;
        case 'this_week':
            const startOfWeek = new Date(today);
            // Tính tuần bắt đầu từ thứ 2 (Monday = 1, Sunday = 0)
            const dayOfWeek = today.getDay();
            const daysToSubtract = dayOfWeek === 0 ? 6 : dayOfWeek - 1; // Nếu Chủ nhật thì lùi 6 ngày, còn lại lùi (dayOfWeek - 1) ngày
            startOfWeek.setDate(today.getDate() - daysToSubtract);
            startDate = getLocalDateString(startOfWeek);
            endDate = getLocalDateString(today);
            console.log('This week calculation: dayOfWeek =', dayOfWeek, ', daysToSubtract =', daysToSubtract);
            break;
        case 'this_month':
            startDate = getLocalDateString(new Date(today.getFullYear(), today.getMonth(), 1));
            endDate = getLocalDateString(today);
            break;
        case 'last_month':
            const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const lastDayOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
            startDate = getLocalDateString(lastMonth);
            endDate = getLocalDateString(lastDayOfLastMonth);
            break;
        case 'this_year':
            startDate = getLocalDateString(new Date(today.getFullYear(), 0, 1));
            endDate = getLocalDateString(today);
            break;
        case 'last_30_days':
            const thirtyDaysAgo = new Date(today);
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
            startDate = getLocalDateString(thirtyDaysAgo);
            endDate = getLocalDateString(today);
            break;
        case 'last_90_days':
            const ninetyDaysAgo = new Date(today);
            ninetyDaysAgo.setDate(ninetyDaysAgo.getDate() - 90);
            startDate = getLocalDateString(ninetyDaysAgo);
            endDate = getLocalDateString(today);
            break;
    }
    
    $('#start_date').val(startDate);
    $('#end_date').val(endDate);
    
    console.log('Date range changed to:', period, 'from', startDate, 'to', endDate);
    console.log('Today is:', getLocalDateString(today), '- Day of week:', today.getDay());
    console.log('Today UTC vs Local: UTC=', today.toISOString().split('T')[0], ', Local=', getLocalDateString(today));
    loadAllData();
}

function refreshAllData() {
    console.log('Refreshing all data including charts');
    
    // Destroy all existing charts to force recreation
    if (mainChart) {
        mainChart.destroy();
        mainChart = null;
    }
    if (categoryChart) {
        categoryChart.destroy();
        categoryChart = null;
    }
    if (customerChart) {
        customerChart.destroy();
        customerChart = null;
    }
    if (hourlyChart) {
        hourlyChart.destroy();
        hourlyChart = null;
    }
    if (marginChart) {
        marginChart.destroy();
        marginChart = null;
    }
    if (forecastChart) {
        forecastChart.destroy();
        forecastChart = null;
    }
    
    loadAllData();
}

function exportData() {
    // TODO: Implement export functionality
    alert('Tính năng xuất dữ liệu đang được phát triển');
}

function debugAjax() {
    console.log('=== DEBUG AJAX ===');
    console.log('Current filters:', currentFilters);
    console.log('Testing revenue data API...');
    
    const testFilters = {
        start_date: getLocalDateString(new Date()),
        end_date: getLocalDateString(new Date()),
        group_by: 'day',
        chart_type: 'line'
    };
    
    $.ajax({
        url: '{{ route("admin.revenue.data") }}',
        method: 'GET',
        data: testFilters,
        success: function(response) {
            console.log('✅ Revenue API Success:', response);
            alert('Revenue API hoạt động! Kiểm tra console để xem chi tiết.');
        },
        error: function(xhr, status, error) {
            console.error('❌ Revenue API Error:', xhr, status, error);
            alert('Revenue API lỗi: ' + error + '. Kiểm tra console để xem chi tiết.');
        }
    });
    
    console.log('Testing service stats API...');
    $.ajax({
        url: '{{ route("admin.revenue.service-stats") }}',
        method: 'GET', 
        data: testFilters,
        success: function(response) {
            console.log('✅ Service Stats API Success:', response);
        },
        error: function(xhr, status, error) {
            console.error('❌ Service Stats API Error:', xhr, status, error);
        }
    });
}

// Placeholder functions for order actions
function editProfit(orderId) {
    // TODO: Implement edit profit modal
    console.log('Edit profit for order:', orderId);
}

function viewOrder(orderId) {
    // TODO: Implement view order details
    console.log('View order:', orderId);
}
</script>
@endpush