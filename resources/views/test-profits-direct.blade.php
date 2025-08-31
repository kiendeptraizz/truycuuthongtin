<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Profits Direct</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Test Profits Direct Access</h1>
        
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <h5>Tổng đơn hàng</h5>
                        <h3 id="total-orders">Loading...</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h5>Đã nhập lãi</h5>
                        <h3 id="orders-with-profit">Loading...</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <h5>Tổng lợi nhuận</h5>
                        <h3 id="total-profit">Loading...</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <h5>Lợi nhuận TB</h5>
                        <h3 id="average-profit">Loading...</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>Danh sách đơn hàng</h5>
                <button onclick="loadData()" class="btn btn-primary btn-sm">Reload</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Khách hàng</th>
                                <th>Dịch vụ</th>
                                <th>Giá bán</th>
                                <th>Ngày tạo</th>
                                <th>Lợi nhuận</th>
                            </tr>
                        </thead>
                        <tbody id="orders-tbody">
                            <tr>
                                <td colspan="6" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            console.log('Direct test page ready');
            console.log('jQuery version:', $.fn.jquery);
            
            // Setup CSRF
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            loadData();
        });

        function loadData() {
            console.log('Loading data directly...');
            
            // Load statistics
            $.ajax({
                url: '/admin/profits/today-statistics',
                method: 'GET',
                success: function(response) {
                    console.log('Statistics response:', response);
                    if (response.success) {
                        $('#total-orders').text(response.data.total_orders || 0);
                        $('#orders-with-profit').text(response.data.orders_with_profit || 0);
                        $('#total-profit').text(response.data.total_profit || '0đ');
                        $('#average-profit').text(response.data.average_profit || '0đ');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Statistics error:', {xhr, status, error});
                    $('#total-orders').text('Error');
                    $('#orders-with-profit').text('Error');
                    $('#total-profit').text('Error');
                    $('#average-profit').text('Error');
                }
            });
            
            // Load orders
            $.ajax({
                url: '/admin/profits/today-orders',
                method: 'GET',
                success: function(response) {
                    console.log('Orders response:', response);
                    if (response.success && response.data) {
                        let html = '';
                        if (response.data.length === 0) {
                            html = '<tr><td colspan="6" class="text-center">Không có đơn hàng nào</td></tr>';
                        } else {
                            response.data.forEach(function(order) {
                                html += `
                                    <tr>
                                        <td>${order.id}</td>
                                        <td>${order.customer_name}</td>
                                        <td>${order.service_name}</td>
                                        <td>${formatMoney(order.price)}</td>
                                        <td>${order.created_at}</td>
                                        <td>${order.has_profit ? formatMoney(order.profit_amount) : 'Chưa nhập'}</td>
                                    </tr>
                                `;
                            });
                        }
                        $('#orders-tbody').html(html);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Orders error:', {xhr, status, error});
                    $('#orders-tbody').html('<tr><td colspan="6" class="text-center text-danger">Lỗi: ' + error + '</td></tr>');
                }
            });
        }
        
        function formatMoney(amount) {
            if (!amount) return '0đ';
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(amount);
        }
    </script>
</body>
</html>
