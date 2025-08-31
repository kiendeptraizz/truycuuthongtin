@extends('layouts.admin')

@section('title', 'Quản lý lợi nhuận')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-chart-line me-2"></i>Quản lý lợi nhuận
                    </h1>
                    <p class="text-muted mb-0">Quản lý và thống kê lợi nhuận từ các đơn hàng trong ngày</p>
                </div>
                <div class="text-end">
                    <small class="text-muted">Ngày hiện tại: <span id="current-date"></span></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4" id="statistics-cards">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng đơn hàng trong ngày
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-orders">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
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
                                Đã nhập lãi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="orders-with-profit">0</div>
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
                                Tổng lợi nhuận
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-profit">0đ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                Lợi nhuận trung bình
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="average-profit">0đ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Danh sách đơn hàng trong ngày
            </h6>
            <div>
                <button type="button" class="btn btn-warning btn-sm" onclick="testData()">
                    <i class="fas fa-bug me-1"></i>Test
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="loadData()">
                    <i class="fas fa-sync-alt me-1"></i>Tải dữ liệu
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="orders-table">
                    <thead class="table-light">
                        <tr>
                            <th width="8%">ID</th>
                            <th width="20%">Khách hàng</th>
                            <th width="25%">Dịch vụ</th>
                            <th width="12%">Giá bán</th>
                            <th width="15%">Ngày tạo</th>
                            <th width="12%">Lợi nhuận</th>
                            <th width="8%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="orders-tbody">
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="mt-2">Nhấn "Tải dữ liệu" để xem đơn hàng trong ngày</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    console.log('Page ready');
    $('#current-date').text(new Date().toLocaleDateString('vi-VN'));
    
    // Setup CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Auto load data
    loadData();
});

function testData() {
    console.log('Testing data...');
    alert('Check console for test results');
    
    // Test basic fetch
    fetch('/admin/profits/today-orders')
        .then(response => response.json())
        .then(data => {
            console.log('Fetch test result:', data);
            alert('Fetch test completed. Count: ' + (data.data ? data.data.length : 0));
        })
        .catch(error => {
            console.error('Fetch test error:', error);
            alert('Fetch test failed: ' + error.message);
        });
}

function loadData() {
    console.log('Loading data...');
    $('#orders-tbody').html('<tr><td colspan="7" class="text-center">Đang tải...</td></tr>');
    
    // Load statistics
    $.get('/admin/profits/today-statistics')
        .done(function(response) {
            console.log('Statistics loaded:', response);
            if (response.success) {
                $('#total-orders').text(response.data.total_orders || 0);
                $('#orders-with-profit').text(response.data.orders_with_profit || 0);
                $('#total-profit').text(response.data.total_profit || '0đ');
                $('#average-profit').text(response.data.average_profit || '0đ');
            }
        })
        .fail(function(xhr) {
            console.error('Failed to load statistics:', xhr);
        });
    
    // Load orders
    $.get('/admin/profits/today-orders')
        .done(function(response) {
            console.log('Orders loaded:', response);
            if (response.success && response.data) {
                let html = '';
                if (response.data.length === 0) {
                    html = '<tr><td colspan="7" class="text-center">Không có đơn hàng nào trong ngày hôm nay</td></tr>';
                } else {
                    response.data.forEach(function(order) {
                        const profitDisplay = order.has_profit 
                            ? `<span class="badge bg-success">${formatCurrency(order.profit_amount)}</span>`
                            : `<span class="badge bg-secondary">Chưa nhập</span>`;
                            
                        html += `
                            <tr>
                                <td>${order.id}</td>
                                <td>${order.customer_name}</td>
                                <td>${order.service_name}</td>
                                <td>${formatCurrency(order.price)}</td>
                                <td>${order.created_at}</td>
                                <td>${profitDisplay}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="alert('Chức năng nhập lãi đang phát triển')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#orders-tbody').html(html);
            }
        })
        .fail(function(xhr) {
            console.error('Failed to load orders:', xhr);
            $('#orders-tbody').html('<tr><td colspan="7" class="text-center text-danger">Lỗi khi tải dữ liệu</td></tr>');
        });
}

function formatCurrency(amount) {
    if (!amount) return '0đ';
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}
</script>
@endsection
