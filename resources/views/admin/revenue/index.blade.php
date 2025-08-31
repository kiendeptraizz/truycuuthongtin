@extends('layouts.admin')

@section('title', 'Thống kê doanh thu')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-line text-primary me-2"></i>
                Thống kê doanh thu
            </h1>
            <p class="text-muted mb-0">Quản lý và theo dõi doanh thu từ các đơn hàng</p>
        </div>
        <div class="text-muted">
            <i class="fas fa-calendar-alt me-1"></i>
            Ngày hiện tại: {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Doanh thu hôm nay
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ format_currency($stats['today_revenue']) }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ $stats['today_orders'] }} đơn hàng
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                Doanh thu tháng này
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ format_currency($stats['month_revenue']) }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ $stats['month_orders'] }} đơn hàng
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                                Lợi nhuận tháng này
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ format_currency($stats['month_profit']) }}
                            </div>
                            <div class="text-xs text-muted">
                                @if($stats['month_revenue'] > 0)
                                    {{ round(($stats['month_profit'] / $stats['month_revenue']) * 100, 1) }}% margin
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
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
                                Doanh thu năm này
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ format_currency($stats['year_revenue']) }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ $stats['year_orders'] }} đơn hàng
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-area fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>
                Bộ lọc thống kê
            </h6>
        </div>
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Từ ngày</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="{{ $today->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Đến ngày</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="{{ $today->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="group_by" class="form-label">Nhóm theo</label>
                    <select class="form-select" id="group_by" name="group_by">
                        <option value="day">Theo ngày</option>
                        <option value="month">Theo tháng</option>
                        <option value="year">Theo năm</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>
                            Lọc dữ liệu
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="exportBtn">
                            <i class="fas fa-download me-1"></i>
                            Xuất báo cáo
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area me-2"></i>
                        Biểu đồ doanh thu theo thời gian
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>
                        Tóm tắt kết quả
                    </h6>
                </div>
                <div class="card-body">
                    <div id="summaryStats">
                        <!-- Will be populated by JavaScript -->
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p class="mt-2">Đang tải dữ liệu...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Performance -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-medal me-2"></i>
                        Top dịch vụ bán chạy
                    </h6>
                </div>
                <div class="card-body">
                    <div id="serviceStats">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p class="mt-2">Đang tải dữ liệu...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow mb-4">
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

<!-- Modal Chỉnh sửa lợi nhuận -->
<div class="modal fade" id="editProfitModal" tabindex="-1" aria-labelledby="editProfitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfitModalLabel">
                    <i class="fas fa-edit me-2"></i>
                    Chỉnh sửa lợi nhuận
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editProfitForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_customer_service_id" name="customer_service_id">
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Thông tin đơn hàng:</strong></label>
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <div id="order_info_display"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_profit_amount" class="form-label">
                            <i class="fas fa-dollar-sign me-1"></i>
                            Số tiền lợi nhuận
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="edit_profit_amount" name="profit_amount" 
                                   placeholder="Nhập số tiền lợi nhuận" required>
                            <span class="input-group-text">VNĐ</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_profit_notes" class="form-label">
                            <i class="fas fa-sticky-note me-1"></i>
                            Ghi chú
                        </label>
                        <textarea class="form-control" id="edit_profit_notes" name="profit_notes" 
                                  rows="3" placeholder="Ghi chú về lợi nhuận..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let revenueChart;

$(document).ready(function() {
    // Load initial data
    loadRevenueData();

    // Handle filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadRevenueData();
    });

    // Handle export button
    $('#exportBtn').on('click', function() {
        // TODO: Implement export functionality
        alert('Tính năng xuất báo cáo đang được phát triển');
    });
});

function loadRevenueData() {
    const formData = {
        start_date: $('#start_date').val(),
        end_date: $('#end_date').val(),
        group_by: $('#group_by').val()
    };

    // Show loading states
    showLoadingState();

    // Load main revenue data
    $.ajax({
        url: '{{ route("admin.revenue.data") }}',
        method: 'GET',
        data: formData,
        success: function(response) {
            updateChart(response.chart_data);
            updateSummaryStats(response.summary);
            updateOrdersTable(response.orders);
        },
        error: function(xhr, status, error) {
            console.error('Error loading revenue data:', error);
            showErrorState();
        }
    });

    // Load service stats
    $.ajax({
        url: '{{ route("admin.revenue.service-stats") }}',
        method: 'GET',
        data: formData,
        success: function(response) {
            updateServiceStats(response);
        },
        error: function(xhr, status, error) {
            console.error('Error loading service stats:', error);
        }
    });
}

function showLoadingState() {
    $('#summaryStats').html(`
        <div class="text-center text-muted py-4">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">Đang tải dữ liệu...</p>
        </div>
    `);
    $('#serviceStats').html(`
        <div class="text-center text-muted py-4">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">Đang tải dữ liệu...</p>
        </div>
    `);
    $('#ordersTableBody').html(`
        <tr>
            <td colspan="8" class="text-center text-muted py-4">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p class="mt-2">Đang tải dữ liệu...</p>
            </td>
        </tr>
    `);
}

function showErrorState() {
    $('#summaryStats').html(`
        <div class="text-center text-danger py-4">
            <i class="fas fa-exclamation-triangle fa-2x"></i>
            <p class="mt-2">Lỗi khi tải dữ liệu</p>
        </div>
    `);
}

function updateChart(chartData) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    if (revenueChart) {
        revenueChart.destroy();
    }

    revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(item => item.period),
            datasets: [{
                label: 'Doanh thu',
                data: chartData.map(item => item.revenue),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                yAxisID: 'y'
            }, {
                label: 'Lợi nhuận',
                data: chartData.map(item => item.profit),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1,
                yAxisID: 'y'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Thời gian'
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Số tiền (VNĐ)'
                    },
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN').format(value) + ' ₫';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(context.parsed.y);
                            return label;
                        }
                    }
                }
            }
        }
    });
}

function updateSummaryStats(summary) {
    $('#summaryStats').html(`
        <div class="row text-center">
            <div class="col-6 border-right">
                <div class="h5 font-weight-bold text-primary">${summary.total_orders}</div>
                <div class="text-xs text-uppercase text-muted">Tổng đơn hàng</div>
            </div>
            <div class="col-6">
                <div class="h5 font-weight-bold text-success">${formatCurrency(summary.total_revenue)}</div>
                <div class="text-xs text-uppercase text-muted">Tổng doanh thu</div>
            </div>
        </div>
        <hr>
        <div class="row text-center">
            <div class="col-6 border-right">
                <div class="h5 font-weight-bold text-info">${formatCurrency(summary.total_profit)}</div>
                <div class="text-xs text-uppercase text-muted">Tổng lợi nhuận</div>
            </div>
            <div class="col-6">
                <div class="h5 font-weight-bold text-warning">${summary.profit_margin}%</div>
                <div class="text-xs text-uppercase text-muted">Tỷ lệ lợi nhuận</div>
            </div>
        </div>
        <hr>
        <div class="text-center">
            <div class="h5 font-weight-bold text-dark">${formatCurrency(summary.average_order_value)}</div>
            <div class="text-xs text-uppercase text-muted">Giá trị đơn hàng TB</div>
        </div>
    `);
}

function updateServiceStats(services) {
    if (services.length === 0) {
        $('#serviceStats').html('<p class="text-muted text-center">Không có dữ liệu</p>');
        return;
    }

    let html = '';
    services.slice(0, 5).forEach((service, index) => {
        const percentage = services[0].total_revenue > 0 ? 
            Math.round((service.total_revenue / services[0].total_revenue) * 100) : 0;
        
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
            </div>
        `;
    });
    
    $('#serviceStats').html(html);
}

function updateOrdersTable(orders) {
    if (orders.length === 0) {
        $('#ordersTableBody').html(`
            <tr>
                <td colspan="9" class="text-center text-muted py-4">
                    Không có đơn hàng nào trong khoảng thời gian này
                </td>
            </tr>
        `);
        return;
    }

    let html = '';
    orders.forEach(order => {
        const statusBadge = getStatusBadge(order.status);
        const actionButtons = getActionButtons(order);
        html += `
            <tr>
                <td>#${order.id}</td>
                <td>${order.customer_display}</td>
                <td>${order.service_name}</td>
                <td class="text-right font-weight-bold text-primary">${formatCurrency(order.revenue)}</td>
                <td class="text-right font-weight-bold text-success">${formatCurrency(order.profit)}</td>
                <td class="text-right">${order.profit_margin}%</td>
                <td>${order.created_at}</td>
                <td>${statusBadge}</td>
                <td class="text-center">${actionButtons}</td>
            </tr>
        `;
    });
    
    $('#ordersTableBody').html(html);
}

function getStatusBadge(status) {
    const badges = {
        'active': '<span class="badge badge-success">Hoạt động</span>',
        'expired': '<span class="badge badge-warning">Hết hạn</span>',
        'cancelled': '<span class="badge badge-danger">Đã hủy</span>'
    };
    return badges[status] || '<span class="badge badge-secondary">Không xác định</span>';
}

function getActionButtons(order) {
    return `
        <button class="btn btn-sm btn-primary me-1" onclick="editProfit(${order.id}, '${order.customer_display}', '${order.service_name}', ${order.profit}, '${order.profit_notes || ''}')">
            <i class="fas fa-edit"></i>
        </button>
        ${order.profit > 0 ? `
        <button class="btn btn-sm btn-danger" onclick="deleteProfit(${order.id})">
            <i class="fas fa-trash"></i>
        </button>
        ` : ''}
    `;
}

function editProfit(orderId, customerName, serviceName, currentProfit, currentNotes) {
    $('#edit_customer_service_id').val(orderId);
    $('#edit_profit_amount').val(currentProfit || '');
    $('#edit_profit_notes').val(currentNotes || '');
    
    $('#order_info_display').html(`
        <small>
            <strong>Đơn hàng:</strong> #${orderId}<br>
            <strong>Khách hàng:</strong> ${customerName}<br>
            <strong>Dịch vụ:</strong> ${serviceName}
        </small>
    `);
    
    // Format số tiền trong input
    if (currentProfit) {
        $('#edit_profit_amount').val(parseInt(currentProfit).toLocaleString('vi-VN'));
    }
    
    $('#editProfitModal').modal('show');
}

function deleteProfit(orderId) {
    if (confirm('Bạn có chắc chắn muốn xóa lợi nhuận của đơn hàng này không?')) {
        $.ajax({
            url: '{{ route("admin.revenue.delete-profit") }}',
            method: 'DELETE',
            data: {
                customer_service_id: orderId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    loadRevenueData(); // Reload data
                } else {
                    alert('Lỗi: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Có lỗi xảy ra khi xóa lợi nhuận!');
                console.error(xhr);
            }
        });
    }
}

// Handle edit profit form submission
$(document).ready(function() {
    // Format profit amount input in modal
    $('#edit_profit_amount').on('input', function() {
        let value = $(this).val().replace(/[^\d]/g, ''); // Only allow digits
        if (value) {
            $(this).val(parseInt(value).toLocaleString('vi-VN'));
        }
    });

    // Prevent non-numeric input
    $('#edit_profit_amount').on('keypress', function(e) {
        // Allow backspace, delete, tab, escape, enter
        if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
            // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            (e.keyCode === 65 && e.ctrlKey === true) ||
            (e.keyCode === 67 && e.ctrlKey === true) ||
            (e.keyCode === 86 && e.ctrlKey === true) ||
            (e.keyCode === 88 && e.ctrlKey === true)) {
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    $('#editProfitForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate and remove formatting before sending
        let profitAmount = $('#edit_profit_amount').val().replace(/\./g, '');
        
        if (!profitAmount || isNaN(profitAmount) || parseInt(profitAmount) < 0) {
            alert('Vui lòng nhập số tiền lợi nhuận hợp lệ (≥ 0)');
            return;
        }
        
        $.ajax({
            url: '{{ route("admin.revenue.update-profit") }}',
            method: 'POST',
            data: {
                customer_service_id: $('#edit_customer_service_id').val(),
                profit_amount: profitAmount,
                profit_notes: $('#edit_profit_notes').val(),
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#editProfitModal').modal('hide');
                    loadRevenueData(); // Reload data
                } else {
                    alert('Lỗi: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Có lỗi xảy ra khi cập nhật lợi nhuận!');
                console.error(xhr);
            }
        });
    });
});

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount || 0);
}
</script>
@endpush
