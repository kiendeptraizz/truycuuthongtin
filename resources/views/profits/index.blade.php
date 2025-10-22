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
                <button type="button" class="btn btn-info btn-sm" onclick="testBasic()">
                    <i class="fas fa-check me-1"></i>Test Basic
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

<!-- Modal nhập/sửa lợi nhuận -->
<div class="modal fade" id="profitModal" tabindex="-1" aria-labelledby="profitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profitModalLabel">
                    <i class="fas fa-money-bill-wave me-2"></i>Nhập lợi nhuận
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="profitForm">
                <div class="modal-body">
                    <input type="hidden" id="customer_service_id" name="customer_service_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Thông tin đơn hàng:</label>
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <div id="order-info"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="profit_amount" class="form-label">
                            Số tiền lãi <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="profit_amount" name="profit_amount" 
                                   min="0" step="1000" placeholder="Nhập số tiền lãi" required>
                            <span class="input-group-text">VNĐ</span>
                        </div>
                        <div class="form-text">Nhập số tiền lãi thu được từ đơn hàng này</div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Nhập ghi chú về lợi nhuận (tùy chọn)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times-circle me-1"></i>Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Lưu lợi nhuận
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Global variables
let ordersData = [];

$(document).ready(function() {
    console.log('Page ready - profits management');
    
    // Test jQuery
    if (typeof $ === 'undefined') {
        console.error('jQuery not loaded!');
        alert('jQuery not loaded!');
        return;
    } else {
        console.log('jQuery loaded successfully');
    }
    
    $('#current-date').text(new Date().toLocaleDateString('vi-VN'));
    
    // Setup CSRF token
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    console.log('CSRF Token:', csrfToken);
    
    if (!csrfToken) {
        console.error('CSRF token not found!');
        alert('CSRF token not found!');
    }
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });
    
    // Setup profit form
    setupProfitForm();
    
    // Test route URLs
    console.log('Testing route URLs...');
    console.log('Orders URL: /admin/profits/today-orders');
    console.log('Stats URL: /admin/profits/today-statistics');
    
    // Auto load data after 1 second
    setTimeout(function() {
        console.log('Auto loading data...');
        loadData();
    }, 1000);
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

function testBasic() {
    alert('JavaScript works! jQuery version: ' + (typeof $ !== 'undefined' ? $.fn.jquery : 'NOT LOADED'));
    console.log('jQuery:', typeof $);
    console.log('Window location:', window.location.href);
    console.log('CSRF token:', $('meta[name="csrf-token"]').attr('content'));
}

// Add profit for an order
function addProfit(orderId) {
    const order = ordersData.find(o => o.id === orderId);
    if (!order) {
        alert('Không tìm thấy thông tin đơn hàng');
        return;
    }
    
    $('#customer_service_id').val(orderId);
    $('#profit_amount').val('');
    $('#notes').val('');
    
    $('#order-info').html(`
        <strong>ID:</strong> ${order.id}<br>
        <strong>Khách hàng:</strong> ${order.customer_display || order.customer_name}<br>
        <strong>Dịch vụ:</strong> ${order.service_name}<br>
        <strong>Giá bán:</strong> ${formatCurrency(order.price)}
    `);
    
    $('#profitModalLabel').html('<i class="fas fa-money-bill-wave me-2"></i>Nhập lợi nhuận');
    $('#profitModal').modal('show');
}

// Edit profit for an order
function editProfit(orderId) {
    const order = ordersData.find(o => o.id === orderId);
    if (!order) {
        alert('Không tìm thấy thông tin đơn hàng');
        return;
    }
    
    $('#customer_service_id').val(orderId);
    $('#profit_amount').val(order.profit_amount || '');
    $('#notes').val(''); // We need to add notes to API response if needed
    
    $('#order-info').html(`
        <strong>ID:</strong> ${order.id}<br>
        <strong>Khách hàng:</strong> ${order.customer_display || order.customer_name}<br>
        <strong>Dịch vụ:</strong> ${order.service_name}<br>
        <strong>Giá bán:</strong> ${formatCurrency(order.price)}
    `);
    
    $('#profitModalLabel').html('<i class="fas fa-edit me-2"></i>Sửa lợi nhuận');
    $('#profitModal').modal('show');
}

// Setup profit form submission
function setupProfitForm() {
    $('#profitForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            customer_service_id: $('#customer_service_id').val(),
            profit_amount: $('#profit_amount').val(),
            notes: $('#notes').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        // Disable submit button
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Đang lưu...');
        
        $.ajax({
            url: '/admin/profits/store',
            method: 'POST',
            data: formData,
            success: function(response) {
                console.log('Store profit response:', response);
                if (response.success) {
                    $('#profitModal').modal('hide');
                    alert('✅ ' + response.message);
                    loadData(); // Reload data
                } else {
                    alert('❌ ' + (response.message || 'Có lỗi xảy ra'));
                }
            },
            error: function(xhr) {
                console.error('Store profit error:', xhr);
                let errorMessage = 'Lỗi khi lưu lợi nhuận';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                alert('❌ ' + errorMessage);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
}

function loadData() {
    console.log('Loading data...');
    $('#orders-tbody').html('<tr><td colspan="7" class="text-center">Đang tải...</td></tr>');
    
    // Load statistics first
    console.log('Making AJAX request to: /admin/profits/today-statistics');
    $.ajax({
        url: '/admin/profits/today-statistics',
        method: 'GET',
        timeout: 10000,
        success: function(response) {
            console.log('Statistics success:', response);
            if (response.success) {
                $('#total-orders').text(response.data.total_orders || 0);
                $('#orders-with-profit').text(response.data.orders_with_profit || 0);
                $('#total-profit').text(response.data.total_profit || '0đ');
                $('#average-profit').text(response.data.average_profit || '0đ');
            } else {
                console.error('Statistics response not successful:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('Statistics AJAX error:', {xhr, status, error});
            console.error('Response text:', xhr.responseText);
            alert('Lỗi tải thống kê: ' + error);
        }
    });
    
    // Load orders
    console.log('Making AJAX request to: /admin/profits/today-orders');
    $.ajax({
        url: '/admin/profits/today-orders',
        method: 'GET',
        timeout: 10000,
        success: function(response) {
            console.log('Orders success:', response);
            if (response.success && response.data) {
                // Save orders data globally
                ordersData = response.data;
                
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
                                <td>${order.customer_display || order.customer_name}</td>
                                <td>${order.service_name}</td>
                                <td>${formatCurrency(order.price)}</td>
                                <td>${order.created_at}</td>
                                <td>${profitDisplay}</td>
                                <td>
                                    <button class="btn btn-sm ${order.has_profit ? 'btn-warning' : 'btn-primary'}" 
                                            onclick="${order.has_profit ? 'editProfit' : 'addProfit'}(${order.id})" 
                                            title="${order.has_profit ? 'Sửa lãi' : 'Nhập lãi'}">
                                        <i class="fas ${order.has_profit ? 'fa-edit' : 'fa-plus'}"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#orders-tbody').html(html);
            } else {
                console.error('Orders response not successful:', response);
                $('#orders-tbody').html('<tr><td colspan="7" class="text-center text-danger">Lỗi: Response không hợp lệ</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Orders AJAX error:', {xhr, status, error});
            console.error('Response text:', xhr.responseText);
            $('#orders-tbody').html('<tr><td colspan="7" class="text-center text-danger">Lỗi khi tải dữ liệu: ' + error + '</td></tr>');
        }
    });
}

function formatCurrency(amount) {
    if (!amount) return '0đ';
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

// Format profit amount input with thousand separators
$(document).ready(function() {
    // Format profit amount input
    $('#profit_amount').on('input', function() {
        let value = $(this).val().replace(/\./g, ''); // Remove existing dots
        if (value && !isNaN(value)) {
            $(this).val(parseInt(value).toLocaleString('vi-VN'));
        }
    });

    // Clean value before form submission
    $('#profitForm').on('submit', function() {
        let profitInput = $('#profit_amount');
        profitInput.val(profitInput.val().replace(/\./g, ''));
    });

    // Format existing value when editing
    $(document).on('click', '.edit-profit', function() {
        setTimeout(function() {
            let profitInput = $('#profit_amount');
            if (profitInput.val()) {
                profitInput.val(parseInt(profitInput.val()).toLocaleString('vi-VN'));
            }
        }, 100);
    });
});
</script>
@endsection
