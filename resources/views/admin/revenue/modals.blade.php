<!-- Edit Profit Modal -->
<div class="modal fade" id="editProfitModal" tabindex="-1" aria-labelledby="editProfitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfitModalLabel">
                    <i class="fas fa-edit me-2"></i>
                    Cập nhật lợi nhuận
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editProfitForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_customer_service_id" name="customer_service_id">
                    
                    <div class="mb-3">
                        <label for="edit_profit_amount" class="form-label">Số tiền lợi nhuận (VNĐ)</label>
                        <input type="text" class="form-control" id="edit_profit_amount" name="profit_amount" 
                               placeholder="Nhập số tiền lợi nhuận" required>
                        <div class="form-text">Nhập số tiền lợi nhuận thực tế từ đơn hàng này</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_profit_notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="edit_profit_notes" name="profit_notes" 
                                  rows="3" placeholder="Ghi chú về lợi nhuận (tùy chọn)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Order Details Modal -->
<div class="modal fade" id="viewOrderModal" tabindex="-1" aria-labelledby="viewOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewOrderModalLabel">
                    <i class="fas fa-eye me-2"></i>
                    Chi tiết đơn hàng
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="orderDetails">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Đang tải thông tin...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="printOrder()">
                    <i class="fas fa-print me-1"></i>
                    In hóa đơn
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Export Options Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">
                    <i class="fas fa-download me-2"></i>
                    Xuất báo cáo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <div class="mb-3">
                        <label class="form-label">Định dạng file</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="format" id="format_excel" value="excel" checked>
                            <label class="form-check-label" for="format_excel">
                                <i class="fas fa-file-excel text-success me-1"></i>
                                Excel (.xlsx)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="format" id="format_pdf" value="pdf">
                            <label class="form-check-label" for="format_pdf">
                                <i class="fas fa-file-pdf text-danger me-1"></i>
                                PDF
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="format" id="format_csv" value="csv">
                            <label class="form-check-label" for="format_csv">
                                <i class="fas fa-file-csv text-info me-1"></i>
                                CSV
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Dữ liệu cần xuất</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="export_orders" checked>
                            <label class="form-check-label" for="export_orders">
                                Chi tiết đơn hàng
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="export_summary" checked>
                            <label class="form-check-label" for="export_summary">
                                Tóm tắt thống kê
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="export_services">
                            <label class="form-check-label" for="export_services">
                                Thống kê dịch vụ
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="export_customers">
                            <label class="form-check-label" for="export_customers">
                                Thống kê khách hàng
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="include_charts">
                            <label class="form-check-label" for="include_charts">
                                Bao gồm biểu đồ (chỉ PDF)
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-success" onclick="processExport()">
                    <i class="fas fa-download me-1"></i>
                    Tải xuống
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="settingsModalLabel">
                    <i class="fas fa-cog me-2"></i>
                    Cài đặt dashboard
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="dashboardSettingsForm">
                    <div class="mb-3">
                        <label class="form-label">Tự động làm mới dữ liệu</label>
                        <select class="form-select" id="auto_refresh">
                            <option value="0">Không tự động</option>
                            <option value="30">Mỗi 30 giây</option>
                            <option value="60">Mỗi 1 phút</option>
                            <option value="300">Mỗi 5 phút</option>
                            <option value="600">Mỗi 10 phút</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Số lượng đơn hàng hiển thị</label>
                        <select class="form-select" id="orders_per_page">
                            <option value="10">10 đơn</option>
                            <option value="25" selected>25 đơn</option>
                            <option value="50">50 đơn</option>
                            <option value="100">100 đơn</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Màu sắc biểu đồ</label>
                        <select class="form-select" id="chart_theme">
                            <option value="default">Mặc định</option>
                            <option value="dark">Tối</option>
                            <option value="colorful">Đầy màu sắc</option>
                            <option value="minimal">Tối giản</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="show_animations" checked>
                            <label class="form-check-label" for="show_animations">
                                Hiển thị animation trên biểu đồ
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="enable_notifications" checked>
                            <label class="form-check-label" for="enable_notifications">
                                Thông báo khi có đơn hàng mới
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="saveDashboardSettings()">
                    <i class="fas fa-save me-1"></i>
                    Lưu cài đặt
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Modal handling functions
function editProfit(orderId) {
    // TODO: Load current profit data and show modal
    $('#edit_customer_service_id').val(orderId);
    $('#editProfitModal').modal('show');
}

function viewOrder(orderId) {
    $('#viewOrderModal').modal('show');
    loadOrderDetails(orderId);
}

function loadOrderDetails(orderId) {
    // TODO: Load order details via AJAX
    $('#orderDetails').html(`
        <div class="row">
            <div class="col-md-6">
                <h6>Thông tin đơn hàng</h6>
                <table class="table table-sm">
                    <tr><td>Mã đơn hàng:</td><td>#${orderId}</td></tr>
                    <tr><td>Ngày tạo:</td><td>Loading...</td></tr>
                    <tr><td>Trạng thái:</td><td>Loading...</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Thông tin khách hàng</h6>
                <table class="table table-sm">
                    <tr><td>Tên:</td><td>Loading...</td></tr>
                    <tr><td>SĐT:</td><td>Loading...</td></tr>
                    <tr><td>Email:</td><td>Loading...</td></tr>
                </table>
            </div>
        </div>
    `);
}

function exportData() {
    $('#exportModal').modal('show');
}

function processExport() {
    const format = $('input[name="format"]:checked').val();
    const options = {
        format: format,
        include_orders: $('#export_orders').is(':checked'),
        include_summary: $('#export_summary').is(':checked'),
        include_services: $('#export_services').is(':checked'),
        include_customers: $('#export_customers').is(':checked'),
        include_charts: $('#include_charts').is(':checked')
    };
    
    console.log('Export with options:', options);
    // TODO: Implement actual export
    $('#exportModal').modal('hide');
    alert('Tính năng xuất báo cáo đang được phát triển');
}

function printOrder() {
    window.print();
}

function saveDashboardSettings() {
    const settings = {
        auto_refresh: $('#auto_refresh').val(),
        orders_per_page: $('#orders_per_page').val(),
        chart_theme: $('#chart_theme').val(),
        show_animations: $('#show_animations').is(':checked'),
        enable_notifications: $('#enable_notifications').is(':checked')
    };
    
    // Save to localStorage
    localStorage.setItem('dashboard_settings', JSON.stringify(settings));
    
    $('#settingsModal').modal('hide');
    alert('Cài đặt đã được lưu!');
    
    // Apply settings
    applyDashboardSettings(settings);
}

function loadDashboardSettings() {
    const settings = JSON.parse(localStorage.getItem('dashboard_settings') || '{}');
    
    if (settings.auto_refresh) $('#auto_refresh').val(settings.auto_refresh);
    if (settings.orders_per_page) $('#orders_per_page').val(settings.orders_per_page);
    if (settings.chart_theme) $('#chart_theme').val(settings.chart_theme);
    if (settings.show_animations !== undefined) $('#show_animations').prop('checked', settings.show_animations);
    if (settings.enable_notifications !== undefined) $('#enable_notifications').prop('checked', settings.enable_notifications);
    
    return settings;
}

function applyDashboardSettings(settings) {
    // Auto refresh
    if (settings.auto_refresh && settings.auto_refresh > 0) {
        setInterval(refreshAllData, settings.auto_refresh * 1000);
    }
    
    // Chart theme
    if (settings.chart_theme) {
        // TODO: Apply chart theme
    }
}

// Initialize settings on page load
$(document).ready(function() {
    const settings = loadDashboardSettings();
    applyDashboardSettings(settings);
});

// Handle profit form submission
$('#editProfitForm').on('submit', function(e) {
    e.preventDefault();
    
    const profitAmount = $('#edit_profit_amount').val().replace(/\./g, '');
    
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
                loadAllData(); // Reload data
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

// Format profit amount input
$('#edit_profit_amount').on('input', function() {
    let value = $(this).val().replace(/[^\d]/g, '');
    if (value) {
        $(this).val(parseInt(value).toLocaleString('vi-VN'));
    }
});
</script>