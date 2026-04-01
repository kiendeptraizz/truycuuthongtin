@extends('layouts.admin')

@section('title', 'Báo Cáo Backup')
@section('page-title', 'Báo Cáo Backup')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">📊 Báo Cáo Backup Chi Tiết</h1>
            <p class="mb-0 text-muted">Phân tích và thống kê hệ thống backup</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" id="refreshReportBtn">
                <i class="fas fa-sync-alt"></i> Làm Mới Báo Cáo
            </button>
            <a href="{{ route('admin.backup.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Quay Lại Dashboard
            </a>
        </div>
    </div>

    <!-- Report Output -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">📋 Báo Cáo Hệ Thống</h6>
        </div>
        <div class="card-body">
            <div id="reportContent">
                @if(isset($reportOutput))
                    <pre class="bg-light p-3 rounded" style="white-space: pre-wrap; font-family: 'Courier New', monospace; font-size: 14px;">{{ $reportOutput }}</pre>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-line fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">Đang tải báo cáo...</p>
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Charts and Analytics -->
    <div class="row">
        <!-- Backup Trends Chart -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📈 Xu Hướng Backup</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="backupTrendsChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Storage Usage -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">💾 Sử Dụng Dung Lượng</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="storageUsageChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> JSON Files
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> ZIP Files
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> SQL Files
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics -->
    <div class="row">
        <!-- Backup Types Distribution -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📊 Phân Bố Loại Backup</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Loại Backup</th>
                                    <th>Số Lượng</th>
                                    <th>Dung Lượng</th>
                                    <th>Tỷ Lệ</th>
                                </tr>
                            </thead>
                            <tbody id="backupTypesTable">
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">🕒 Hoạt Động Gần Đây</h6>
                </div>
                <div class="card-body">
                    <div class="timeline" id="recentActivity">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">⚡ Chỉ Số Hiệu Suất</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="h4 mb-0 font-weight-bold text-primary" id="avgBackupSize">-</div>
                        <div class="text-xs font-weight-bold text-uppercase text-muted">
                            Kích Thước TB
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="h4 mb-0 font-weight-bold text-success" id="successRate">-</div>
                        <div class="text-xs font-weight-bold text-uppercase text-muted">
                            Tỷ Lệ Thành Công
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="h4 mb-0 font-weight-bold text-info" id="avgFrequency">-</div>
                        <div class="text-xs font-weight-bold text-uppercase text-muted">
                            Tần Suất TB/Ngày
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="h4 mb-0 font-weight-bold text-warning" id="retentionDays">-</div>
                        <div class="text-xs font-weight-bold text-uppercase text-muted">
                            Lưu Trữ (Ngày)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">📤 Xuất Báo Cáo</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted">Xuất báo cáo dưới các định dạng khác nhau:</p>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary" id="exportPdfBtn">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <button type="button" class="btn btn-outline-success" id="exportExcelBtn">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                        <button type="button" class="btn btn-outline-info" id="exportCsvBtn">
                            <i class="fas fa-file-csv"></i> CSV
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <p class="text-muted">Lên lịch báo cáo tự động:</p>
                    <div class="form-group">
                        <select class="form-control" id="scheduleReport">
                            <option value="">Chọn tần suất...</option>
                            <option value="daily">Hàng ngày</option>
                            <option value="weekly">Hàng tuần</option>
                            <option value="monthly">Hàng tháng</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-warning" id="scheduleBtn">
                        <i class="fas fa-calendar-plus"></i> Lên Lịch
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    loadReportData();
    initializeCharts();
    
    // Refresh report
    $('#refreshReportBtn').click(function() {
        loadReportData();
    });
    
    // Export buttons
    $('#exportPdfBtn').click(() => exportReport('pdf'));
    $('#exportExcelBtn').click(() => exportReport('excel'));
    $('#exportCsvBtn').click(() => exportReport('csv'));
    
    // Schedule report
    $('#scheduleBtn').click(function() {
        const frequency = $('#scheduleReport').val();
        if (frequency) {
            scheduleReport(frequency);
        } else {
            alert('Vui lòng chọn tần suất báo cáo');
        }
    });
});

function loadReportData() {
    // Simulate loading report data
    setTimeout(() => {
        updatePerformanceMetrics();
        updateBackupTypesTable();
        updateRecentActivity();
        updateCharts();
    }, 1000);
}

function updatePerformanceMetrics() {
    $('#avgBackupSize').text('2.5 MB');
    $('#successRate').text('98.5%');
    $('#avgFrequency').text('4.2');
    $('#retentionDays').text('30');
}

function updateBackupTypesTable() {
    const data = [
        { type: 'Daily', count: 15, size: '28.5 MB', percentage: '45%' },
        { type: 'Weekly', count: 4, size: '8.2 MB', percentage: '13%' },
        { type: 'Quick', count: 20, size: '18.7 MB', percentage: '30%' },
        { type: 'Manual', count: 8, size: '7.6 MB', percentage: '12%' }
    ];
    
    let html = '';
    data.forEach(item => {
        html += `
            <tr>
                <td><span class="badge badge-primary">${item.type}</span></td>
                <td>${item.count}</td>
                <td>${item.size}</td>
                <td>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar" style="width: ${item.percentage}">${item.percentage}</div>
                    </div>
                </td>
            </tr>
        `;
    });
    
    $('#backupTypesTable').html(html);
}

function updateRecentActivity() {
    const activities = [
        { time: '2 phút trước', action: 'Backup daily hoàn thành', status: 'success' },
        { time: '1 giờ trước', action: 'Backup quick được tạo', status: 'info' },
        { time: '3 giờ trước', action: 'Xóa backup cũ', status: 'warning' },
        { time: '6 giờ trước', action: 'Backup weekly hoàn thành', status: 'success' }
    ];
    
    let html = '';
    activities.forEach(activity => {
        const iconClass = activity.status === 'success' ? 'fa-check-circle text-success' : 
                         activity.status === 'info' ? 'fa-info-circle text-info' : 
                         'fa-exclamation-triangle text-warning';
        
        html += `
            <div class="d-flex align-items-center mb-3">
                <div class="mr-3">
                    <i class="fas ${iconClass}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="small font-weight-bold">${activity.action}</div>
                    <div class="text-muted small">${activity.time}</div>
                </div>
            </div>
        `;
    });
    
    $('#recentActivity').html(html);
}

function initializeCharts() {
    // Backup Trends Chart
    const trendsCtx = document.getElementById('backupTrendsChart').getContext('2d');
    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Backup Count',
                data: [3, 4, 2, 5, 3, 4, 6],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Storage Usage Chart
    const storageCtx = document.getElementById('storageUsageChart').getContext('2d');
    new Chart(storageCtx, {
        type: 'doughnut',
        data: {
            labels: ['JSON Files', 'ZIP Files', 'SQL Files'],
            datasets: [{
                data: [60, 30, 10],
                backgroundColor: [
                    'rgb(54, 162, 235)',
                    'rgb(75, 192, 192)',
                    'rgb(255, 205, 86)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function updateCharts() {
    // Charts are already initialized with sample data
}

function exportReport(format) {
    // TODO: Implement report export
    showAlert('info', `Đang xuất báo cáo định dạng ${format.toUpperCase()}...`);
}

function scheduleReport(frequency) {
    // TODO: Implement report scheduling
    showAlert('success', `Đã lên lịch báo cáo ${frequency}`);
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;
    
    $('.alert').remove();
    $('.container-fluid').prepend(alertHtml);
    
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
@endsection
