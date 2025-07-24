@extends('layouts.admin')

@section('title', 'Lịch Sử Backup')
@section('page-title', 'Lịch Sử Backup')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">📜 Lịch Sử Backup</h1>
            <p class="mb-0 text-muted">Theo dõi tất cả hoạt động backup trong hệ thống</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" id="refreshHistoryBtn">
                <i class="fas fa-sync-alt"></i> Làm Mới
            </button>
            <a href="{{ route('admin.backup.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Quay Lại Dashboard
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">🔍 Bộ Lọc Lịch Sử</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="filterDate">Ngày:</label>
                    <input type="date" class="form-control" id="filterDate" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="filterAction">Hành Động:</label>
                    <select class="form-control" id="filterAction">
                        <option value="">Tất cả</option>
                        <option value="backup_created">Tạo Backup</option>
                        <option value="backup_deleted">Xóa Backup</option>
                        <option value="backup_restored">Khôi Phục</option>
                        <option value="backup_failed">Backup Thất Bại</option>
                        <option value="cleanup">Dọn Dẹp</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterStatus">Trạng Thái:</label>
                    <select class="form-control" id="filterStatus">
                        <option value="">Tất cả</option>
                        <option value="success">Thành Công</option>
                        <option value="error">Lỗi</option>
                        <option value="warning">Cảnh Báo</option>
                        <option value="info">Thông Tin</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <div>
                        <button type="button" class="btn btn-info btn-block" id="applyFilters">
                            <i class="fas fa-filter"></i> Áp Dụng Bộ Lọc
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Timeline -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">⏰ Timeline Hoạt Động</h6>
            <div>
                <span class="badge badge-info" id="totalEvents">0 sự kiện</span>
                <button type="button" class="btn btn-sm btn-outline-primary ml-2" id="exportHistoryBtn">
                    <i class="fas fa-download"></i> Xuất Lịch Sử
                </button>
            </div>
        </div>
        <div class="card-body">
            <div id="historyTimeline">
                <!-- Timeline will be loaded here -->
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Đang tải...</span>
                    </div>
                    <p class="mt-2 text-muted">Đang tải lịch sử hoạt động...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📊 Thống Kê Hoạt Động</h6>
                </div>
                <div class="card-body">
                    <canvas id="activityChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📈 Tóm Tắt Hôm Nay</h6>
                </div>
                <div class="card-body">
                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Backup Thành Công
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="todaySuccessCount">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    
                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Backup Thất Bại
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="todayFailureCount">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    
                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tổng Dung Lượng
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="todayTotalSize">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hdd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Thời Gian TB
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="todayAvgTime">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
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
    loadHistoryData();
    initializeChart();
    
    // Refresh history
    $('#refreshHistoryBtn').click(function() {
        loadHistoryData();
    });
    
    // Apply filters
    $('#applyFilters').click(function() {
        applyFilters();
    });
    
    // Export history
    $('#exportHistoryBtn').click(function() {
        exportHistory();
    });
});

function loadHistoryData() {
    // Simulate loading history data
    setTimeout(() => {
        generateSampleHistory();
        updateTodayStats();
        updateActivityChart();
    }, 1000);
}

function generateSampleHistory() {
    const events = [
        {
            time: '2 phút trước',
            action: 'Tạo Backup',
            details: 'AUTO_BACKUP_daily_2025-07-21_18-17-15.json',
            status: 'success',
            icon: 'fa-plus-circle',
            color: 'success'
        },
        {
            time: '1 giờ trước',
            action: 'Backup Nhanh',
            details: 'AUTO_BACKUP_quick_2025-07-21_17-18-25.json (2.5 MB)',
            status: 'success',
            icon: 'fa-bolt',
            color: 'info'
        },
        {
            time: '3 giờ trước',
            action: 'Dọn Dẹp',
            details: 'Đã xóa 3 backup cũ để tiết kiệm dung lượng',
            status: 'info',
            icon: 'fa-broom',
            color: 'warning'
        },
        {
            time: '6 giờ trước',
            action: 'Backup Hàng Tuần',
            details: 'AUTO_BACKUP_weekly_2025-07-21_12-00-00.json (3.1 MB)',
            status: 'success',
            icon: 'fa-calendar-week',
            color: 'success'
        },
        {
            time: '8 giờ trước',
            action: 'Khôi Phục',
            details: 'Khôi phục từ backup_2025-07-20.json',
            status: 'warning',
            icon: 'fa-undo',
            color: 'warning'
        },
        {
            time: '12 giờ trước',
            action: 'Backup Thất Bại',
            details: 'Lỗi: Không đủ dung lượng ổ cứng',
            status: 'error',
            icon: 'fa-exclamation-triangle',
            color: 'danger'
        }
    ];
    
    let timelineHtml = '';
    events.forEach((event, index) => {
        timelineHtml += `
            <div class="timeline-item">
                <div class="timeline-marker bg-${event.color}">
                    <i class="fas ${event.icon}"></i>
                </div>
                <div class="timeline-content">
                    <div class="timeline-header">
                        <h6 class="timeline-title">${event.action}</h6>
                        <small class="text-muted">${event.time}</small>
                    </div>
                    <p class="timeline-body">${event.details}</p>
                    <span class="badge badge-${event.color}">${event.status.toUpperCase()}</span>
                </div>
            </div>
        `;
    });
    
    $('#historyTimeline').html(`
        <div class="timeline">
            ${timelineHtml}
        </div>
    `);
    
    $('#totalEvents').text(`${events.length} sự kiện`);
}

function updateTodayStats() {
    $('#todaySuccessCount').text('12');
    $('#todayFailureCount').text('1');
    $('#todayTotalSize').text('28.5 MB');
    $('#todayAvgTime').text('45s');
}

function initializeChart() {
    const ctx = document.getElementById('activityChart').getContext('2d');
    
    window.activityChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'CN'],
            datasets: [
                {
                    label: 'Thành Công',
                    data: [12, 15, 8, 10, 14, 9, 6],
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Thất Bại',
                    data: [1, 0, 2, 1, 0, 1, 0],
                    backgroundColor: 'rgba(220, 53, 69, 0.8)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Hoạt Động Backup 7 Ngày Qua'
                }
            }
        }
    });
}

function updateActivityChart() {
    if (window.activityChart) {
        // Update chart with new data
        console.log('Activity chart updated');
    }
}

function applyFilters() {
    const date = $('#filterDate').val();
    const action = $('#filterAction').val();
    const status = $('#filterStatus').val();
    
    // TODO: Implement filtering logic
    showAlert('info', `Áp dụng bộ lọc: Ngày=${date}, Hành động=${action || 'Tất cả'}, Trạng thái=${status || 'Tất cả'}`);
}

function exportHistory() {
    // TODO: Implement history export
    showAlert('info', 'Đang xuất lịch sử backup...');
    
    setTimeout(() => {
        showAlert('success', 'Đã xuất lịch sử thành công!');
    }, 2000);
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

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e3e6f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.timeline-content {
    background: #f8f9fc;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    padding: 15px;
    margin-left: 20px;
}

.timeline-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 10px;
}

.timeline-title {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.timeline-body {
    margin: 0 0 10px 0;
    color: #6c757d;
}
</style>
@endsection
