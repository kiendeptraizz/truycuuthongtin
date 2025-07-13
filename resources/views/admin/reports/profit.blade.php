@extends('layouts.admin')

@section('title', 'Báo cáo lợi nhuận')
@section('page-title', 'Báo cáo lợi nhuận')

@section('content')
<!-- Header Card -->
<div class="glass-card page-header-card ">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="icon-circle bg-gradient-success me-3">
                    <i class="fas fa-chart-line text-white"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1">Báo cáo lợi nhuận</h4>
                    <p class="text-muted mb-0">Theo dõi doanh thu, chi phí và lợi nhuận theo thời gian</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button onclick="refreshData()" class="btn btn-outline-secondary btn-sm" title="Làm mới dữ liệu">
                    <i class="fas fa-sync-alt me-1"></i>
                    Làm mới
                </button>
                <button onclick="exportReport()" class="btn btn-outline-success btn-sm" title="Xuất báo cáo">
                    <i class="fas fa-download me-1"></i>
                    Xuất Excel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Date Filter Card -->
<div class="filter-card glass-card border-0 mb-4 ">
    <div class="card-body">
        <form method="GET" class="row align-items-end g-3">
            <div class="col-md-3">
                <label for="start_date" class="form-label small text-muted fw-semibold">
                    <i class="fas fa-calendar-alt me-1"></i>Từ ngày
                </label>
                <input type="date" 
                       class="form-control form-control-modern" 
                       id="start_date" 
                       name="start_date" 
                       value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label small text-muted fw-semibold">
                    <i class="fas fa-calendar-check me-1"></i>Đến ngày
                </label>
                <input type="date" 
                       class="form-control form-control-modern" 
                       id="end_date" 
                       name="end_date" 
                       value="{{ $endDate }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-gradient-primary btn-hover-lift">
                    <i class="fas fa-search me-1"></i>
                    Xem báo cáo
                </button>
            </div>
            <div class="col-md-3">
                <div class="date-range-info">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Từ {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} 
                        đến {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                    </small>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Stats Overview -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stats-card glass-card border-0 ">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-gradient-primary">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-number">{{ $soldServices->count() }}</div>
                        <div class="stats-label">Dịch vụ đã bán</div>
                    </div>
                </div>
                <div class="stats-progress mt-2">
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stats-card glass-card border-0 ">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-gradient-success">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-number">{{ number_format($totalRevenue, 0, ',', '.') }}</div>
                        <div class="stats-label">Tổng doanh thu</div>
                        <div class="stats-unit">VNĐ</div>
                    </div>
                </div>
                <div class="stats-progress mt-2">
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-success" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stats-card glass-card border-0 ">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-gradient-warning">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-number">{{ number_format($totalCost, 0, ',', '.') }}</div>
                        <div class="stats-label">Tổng chi phí</div>
                        <div class="stats-unit">VNĐ</div>
                    </div>
                </div>
                <div class="stats-progress mt-2">
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-warning" 
                             style="width: {{ $totalRevenue > 0 ? ($totalCost / $totalRevenue) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stats-card glass-card border-0 ">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-gradient-info">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-number">{{ number_format($totalProfit, 0, ',', '.') }}</div>
                        <div class="stats-label">Lợi nhuận</div>
                        <div class="stats-unit">VNĐ</div>
                        @if($totalRevenue > 0)
                            <div class="stats-percentage">
                                {{ round(($totalProfit / $totalRevenue) * 100, 1) }}% margin
                            </div>
                        @endif
                    </div>
                </div>
                <div class="stats-progress mt-2">
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-info" 
                             style="width: {{ $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Reports Section -->
<div class="row">
    <!-- Package Profit Analysis -->
    <div class="col-lg-8">
        <div class="glass-card main-content-card ">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-box me-2 text-primary"></i>
                    Lợi nhuận theo gói dịch vụ
                </h5>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary" onclick="toggleChart()" title="Xem biểu đồ">
                        <i class="fas fa-chart-bar"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="exportPackageData()" title="Xuất dữ liệu">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($packageStats->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-modern table-hover">
                            <thead class="table-header-modern">
                                <tr>
                                    <th class="sortable" data-sort="package">
                                        <i class="fas fa-box me-1"></i>Gói dịch vụ
                                        <i class="fas fa-sort ms-1"></i>
                                    </th>
                                    <th class="sortable text-center" data-sort="count">
                                        <i class="fas fa-shopping-cart me-1"></i>Số lượng
                                        <i class="fas fa-sort ms-1"></i>
                                    </th>
                                    <th class="sortable text-end" data-sort="revenue">
                                        <i class="fas fa-dollar-sign me-1"></i>Doanh thu
                                        <i class="fas fa-sort ms-1"></i>
                                    </th>
                                    <th class="sortable text-end" data-sort="cost">
                                        <i class="fas fa-credit-card me-1"></i>Chi phí
                                        <i class="fas fa-sort ms-1"></i>
                                    </th>
                                    <th class="sortable text-end" data-sort="profit">
                                        <i class="fas fa-chart-line me-1"></i>Lợi nhuận
                                        <i class="fas fa-sort ms-1"></i>
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-percentage me-1"></i>Tỷ lệ LN
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packageStats as $stat)
                                    <tr class="table-row-modern ">
                                        <td>
                                            <div class="package-info">
                                                <div class="package-name">
                                                    <i class="fas fa-box me-1 text-primary"></i>
                                                    {{ $stat['package']->name }}
                                                </div>
                                                <div class="package-category">
                                                    <span class="category-badge">
                                                        <i class="fas fa-tags me-1"></i>
                                                        {{ $stat['package']->category->name }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="quantity-badge">
                                                {{ $stat['count'] }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="amount-info">
                                                <div class="amount-value text-success">
                                                    {{ number_format($stat['revenue'], 0, ',', '.') }}
                                                </div>
                                                <div class="amount-currency">VNĐ</div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="amount-info">
                                                <div class="amount-value text-danger">
                                                    {{ number_format($stat['cost'], 0, ',', '.') }}
                                                </div>
                                                <div class="amount-currency">VNĐ</div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="amount-info">
                                                <div class="amount-value text-info">
                                                    {{ number_format($stat['profit'], 0, ',', '.') }}
                                                </div>
                                                <div class="amount-currency">VNĐ</div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($stat['profit_margin'] > 0)
                                                <span class="profit-margin-badge profit-positive">
                                                    {{ $stat['profit_margin'] }}%
                                                </span>
                                            @elseif($stat['profit_margin'] < 0)
                                                <span class="profit-margin-badge profit-negative">
                                                    {{ $stat['profit_margin'] }}%
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h5 class="empty-state-title">Không có dữ liệu</h5>
                        <p class="empty-state-text">Không có dữ liệu trong khoảng thời gian này</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Monthly Statistics -->
    <div class="col-lg-4">
        <div class="glass-card ">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar me-2 text-info"></i>
                    Thống kê 6 tháng gần nhất
                </h5>
            </div>
            <div class="card-body">
                @foreach($monthlyStats as $month)
                    <div class="monthly-stat-item ">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="monthly-info">
                                <div class="monthly-month">{{ $month['month'] }}</div>
                                <div class="monthly-count">
                                    <i class="fas fa-shopping-bag me-1"></i>
                                    {{ $month['count'] }} dịch vụ
                                </div>
                            </div>
                            <div class="monthly-amounts">
                                <div class="monthly-revenue">
                                    <i class="fas fa-arrow-up me-1 text-success"></i>
                                    {{ number_format($month['revenue'], 0, ',', '.') }}đ
                                </div>
                                <div class="monthly-profit">
                                    <i class="fas fa-plus me-1 text-info"></i>
                                    {{ number_format($month['profit'], 0, ',', '.') }}đ
                                </div>
                            </div>
                        </div>
                        <div class="monthly-progress">
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-gradient-info" 
                                     style="width: {{ $month['revenue'] > 0 ? ($month['profit'] / $month['revenue']) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Service Details Section -->
@if($soldServices->count() > 0)
<div class="glass-card main-content-card ">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-list me-2 text-primary"></i>
            Chi tiết dịch vụ đã bán
            <span class="service-count-badge">{{ $soldServices->count() }}</span>
        </h5>
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-outline-secondary" onclick="toggleServiceView()" title="Chuyển đổi view">
                <i class="fas fa-table"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="exportServiceData()" title="Xuất dữ liệu">
                <i class="fas fa-download"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="printServiceReport()" title="In báo cáo">
                <i class="fas fa-print"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-modern table-hover" id="serviceTable">
                <thead class="table-header-modern">
                    <tr>
                        <th class="sortable" data-sort="date">
                            <i class="fas fa-calendar me-1"></i>Ngày bán
                            <i class="fas fa-sort ms-1"></i>
                        </th>
                        <th class="sortable" data-sort="customer">
                            <i class="fas fa-user me-1"></i>Khách hàng
                            <i class="fas fa-sort ms-1"></i>
                        </th>
                        <th class="sortable" data-sort="package">
                            <i class="fas fa-box me-1"></i>Gói dịch vụ
                            <i class="fas fa-sort ms-1"></i>
                        </th>
                        <th class="sortable text-end" data-sort="price">
                            <i class="fas fa-dollar-sign me-1"></i>Giá bán
                            <i class="fas fa-sort ms-1"></i>
                        </th>
                        <th class="sortable text-end" data-sort="cost">
                            <i class="fas fa-credit-card me-1"></i>Giá nhập
                            <i class="fas fa-sort ms-1"></i>
                        </th>
                        <th class="sortable text-end" data-sort="profit">
                            <i class="fas fa-chart-line me-1"></i>Lợi nhuận
                            <i class="fas fa-sort ms-1"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($soldServices as $service)
                        <tr class="table-row-modern ">
                            <td>
                                <div class="date-info">
                                    <div class="date-value">
                                        <i class="fas fa-calendar me-1 text-primary"></i>
                                        {{ $service->activated_at->format('d/m/Y') }}
                                    </div>
                                    <div class="date-relative">
                                        {{ $service->activated_at->diffForHumans() }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="customer-info">
                                    <div class="customer-avatar">
                                        <div class="avatar-circle">
                                            {{ substr($service->customer->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="customer-details">
                                        <div class="customer-name">
                                            {{ $service->customer->name }}
                                        </div>
                                        <div class="customer-code">
                                            <i class="fas fa-tag me-1"></i>{{ $service->customer->customer_code }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="service-info">
                                    <div class="service-name">
                                        <i class="fas fa-box me-1 text-primary"></i>
                                        {{ $service->servicePackage->name }}
                                    </div>
                                    <div class="service-category">
                                        <span class="category-badge">
                                            {{ $service->servicePackage->category->name }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="amount-info">
                                    <div class="amount-value text-success">
                                        {{ number_format($service->servicePackage->price, 0, ',', '.') }}
                                    </div>
                                    <div class="amount-currency">VNĐ</div>
                                </div>
                            </td>
                            <td class="text-end">
                                @if($service->servicePackage->cost_price)
                                    <div class="amount-info">
                                        <div class="amount-value text-danger">
                                            {{ number_format($service->servicePackage->cost_price, 0, ',', '.') }}
                                        </div>
                                        <div class="amount-currency">VNĐ</div>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($service->servicePackage->cost_price)
                                    <div class="profit-info">
                                        <div class="profit-value {{ $service->servicePackage->getProfit() > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($service->servicePackage->getProfit(), 0, ',', '.') }}
                                        </div>
                                        <div class="profit-currency">VNĐ</div>
                                        <div class="profit-percentage">
                                            {{ round(($service->servicePackage->getProfit() / $service->servicePackage->price) * 100, 1) }}%
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date validation
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
            if (endDateInput.value && this.value > endDateInput.value) {
                endDateInput.value = this.value;
            }
        });
        
        endDateInput.addEventListener('change', function() {
            startDateInput.max = this.value;
            if (startDateInput.value && this.value < startDateInput.value) {
                startDateInput.value = this.value;
            }
        });
    }
    
    // Table sorting
    document.querySelectorAll('.sortable').forEach(header => {
        header.addEventListener('click', function() {
            const sortBy = this.dataset.sort;
            toggleSort(sortBy);
        });
    });
    
    // Animate stats cards
    animateNumbers();
    
    // Auto-refresh every 5 minutes
    setInterval(() => {
        if (document.hasFocus()) {
            const currentTime = new Date();
            const lastRefresh = localStorage.getItem('reportsLastRefresh');
            
            if (!lastRefresh || (currentTime - new Date(lastRefresh)) > 300000) { // 5 minutes
                localStorage.setItem('reportsLastRefresh', currentTime.toISOString());
                refreshData();
            }
        }
    }, 300000);
});

// Animate numbers on page load
function animateNumbers() {
    document.querySelectorAll('.stats-number').forEach(element => {
        const finalNumber = element.textContent.replace(/[^\d]/g, '');
        if (finalNumber) {
            animateValue(element, 0, parseInt(finalNumber), 1000);
        }
    });
}

function animateValue(element, start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const current = Math.floor(progress * (end - start) + start);
        
        if (element.textContent.includes(',')) {
            element.textContent = current.toLocaleString();
        } else {
            element.textContent = current;
        }
        
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

// Chart toggle functionality
function toggleChart() {
    showToast('Tính năng biểu đồ sẽ được phát triển trong phiên bản tới', 'info');
}

// Export functions
function exportReport() {
    showToast('Đang chuẩn bị xuất báo cáo...', 'info');
    // Implementation for export
}

function exportPackageData() {
    showToast('Đang xuất dữ liệu gói dịch vụ...', 'info');
    // Implementation for package data export
}

function exportServiceData() {
    showToast('Đang xuất dữ liệu chi tiết dịch vụ...', 'info');
    // Implementation for service data export
}

// Print functions
function printServiceReport() {
    window.print();
}

// View toggle
function toggleServiceView() {
    const table = document.getElementById('serviceTable');
    table.classList.toggle('table-condensed');
    showToast('Đã chuyển đổi chế độ xem', 'success');
}

// Refresh data
function refreshData() {
    showLoadingState();
    window.location.reload();
}

// Sort toggle
function toggleSort(sortBy) {
    const url = new URL(window.location);
    const currentSort = url.searchParams.get('sort');
    const currentDir = url.searchParams.get('dir') || 'asc';
    
    if (currentSort === sortBy) {
        url.searchParams.set('dir', currentDir === 'asc' ? 'desc' : 'asc');
    } else {
        url.searchParams.set('sort', sortBy);
        url.searchParams.set('dir', 'asc');
    }
    
    showLoadingState();
    window.location.href = url.toString();
}

// Loading state
function showLoadingState() {
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = `
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Đang tải...</span>
            </div>
            <div class="mt-2">Đang tải báo cáo...</div>
        </div>
    `;
    document.body.appendChild(loadingOverlay);
}

// Show profit summary notification
document.addEventListener('DOMContentLoaded', function() {
    const totalProfit = {{ $totalProfit }};
    const totalRevenue = {{ $totalRevenue }};
    
    if (totalRevenue > 0) {
        const profitMargin = ((totalProfit / totalRevenue) * 100).toFixed(1);
        
        if (profitMargin > 20) {
            showToast(`Lợi nhuận tốt! Tỷ lệ lợi nhuận: ${profitMargin}%`, 'success', 5000);
        } else if (profitMargin < 5) {
            showToast(`Tỷ lệ lợi nhuận thấp: ${profitMargin}%. Cần xem xét lại giá cả.`, 'warning', 5000);
        }
    }
});
</script>
@endsection
