<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Customer Search Selector</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-search me-2"></i>
                            Test Customer Search Selector Component
                        </h5>
                    </div>
                    <div class="card-body">
                        <form>
                            @csrf
                            
                            <!-- Test 1: Basic Usage -->
                            <div class="mb-4">
                                <h6 class="text-primary">Test 1: Basic Usage</h6>
                                <x-customer-search-selector
                                    name="customer_id_1"
                                    id="customer_id_1"
                                    :customers="collect([
                                        (object)['id' => 1, 'name' => 'Nguyễn Văn A', 'customer_code' => 'KUN12345', 'email' => 'nguyenvana@email.com'],
                                        (object)['id' => 2, 'name' => 'Trần Thị B', 'customer_code' => 'KUN12346', 'email' => 'tranthib@email.com'],
                                        (object)['id' => 3, 'name' => 'Lê Văn C', 'customer_code' => 'KUN12347', 'email' => 'levanc@email.com'],
                                        (object)['id' => 4, 'name' => 'Phạm Thị D', 'customer_code' => 'KUN12348', 'email' => 'phamthid@email.com'],
                                        (object)['id' => 5, 'name' => 'Hoàng Văn E', 'customer_code' => 'KUN12349', 'email' => 'hoangvane@email.com']
                                    ])"
                                    label="Chọn Khách Hàng (Basic)"
                                    :required="true"
                                />
                            </div>
                            
                            <hr>
                            
                            <!-- Test 2: With Auto-fill Email -->
                            <div class="mb-4">
                                <h6 class="text-primary">Test 2: With Auto-fill Email</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-customer-search-selector
                                            name="customer_id_2"
                                            id="customer_id_2"
                                            :customers="collect([
                                                (object)['id' => 1, 'name' => 'Nguyễn Văn A', 'customer_code' => 'KUN12345', 'email' => 'nguyenvana@email.com'],
                                                (object)['id' => 2, 'name' => 'Trần Thị B', 'customer_code' => 'KUN12346', 'email' => 'tranthib@email.com'],
                                                (object)['id' => 3, 'name' => 'Lê Văn C', 'customer_code' => 'KUN12347', 'email' => 'levanc@email.com']
                                            ])"
                                            label="Chọn Khách Hàng (Auto-fill)"
                                            :auto-fill-email="true"
                                            email-field-id="test_email"
                                            :required="true"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <label for="test_email" class="form-label">
                                            <i class="fas fa-envelope me-1"></i>
                                            Email (Auto-filled)
                                        </label>
                                        <input type="email" class="form-control" id="test_email" name="test_email">
                                        <small class="text-muted">Email sẽ được tự động điền khi chọn khách hàng</small>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <!-- Test 3: With Pre-selected Value -->
                            <div class="mb-4">
                                <h6 class="text-primary">Test 3: With Pre-selected Value</h6>
                                <x-customer-search-selector
                                    name="customer_id_3"
                                    id="customer_id_3"
                                    :customers="collect([
                                        (object)['id' => 1, 'name' => 'Nguyễn Văn A', 'customer_code' => 'KUN12345', 'email' => 'nguyenvana@email.com'],
                                        (object)['id' => 2, 'name' => 'Trần Thị B', 'customer_code' => 'KUN12346', 'email' => 'tranthib@email.com'],
                                        (object)['id' => 3, 'name' => 'Lê Văn C', 'customer_code' => 'KUN12347', 'email' => 'levanc@email.com']
                                    ])"
                                    label="Chọn Khách Hàng (Pre-selected)"
                                    :value="2"
                                    help-text="Khách hàng 'Trần Thị B' đã được chọn sẵn"
                                />
                            </div>
                            
                            <hr>
                            
                            <!-- Test 4: Manual Entry + Date Fields -->
                            <div class="mb-4">
                                <h6 class="text-primary">Test 4: Manual Entry + Date Fields</h6>
                                <x-customer-search-selector
                                    name="customer_id_4"
                                    id="customer_id_4"
                                    :customers="collect([
                                        (object)['id' => 1, 'name' => 'Nguyễn Văn A', 'customer_code' => 'KUN12345', 'email' => 'nguyenvana@email.com'],
                                        (object)['id' => 2, 'name' => 'Trần Thị B', 'customer_code' => 'KUN12346', 'email' => 'tranthib@email.com']
                                    ])"
                                    label="Chọn Khách Hàng (Manual Entry + Dates)"
                                    :allow-manual-entry="true"
                                    manual-entry-placeholder="Nhập email khách hàng mới..."
                                    :show-date-fields="true"
                                    start-date-name="start_date_4"
                                    end-date-name="end_date_4"
                                    start-date-label="Ngày bắt đầu"
                                    end-date-label="Ngày kết thúc"
                                    :date-fields-required="true"
                                    help-text="Test manual entry và date fields"
                                />
                            </div>

                            <hr>

                            <!-- Test 5: Large Dataset -->
                            <div class="mb-4">
                                <h6 class="text-primary">Test 5: Large Dataset (Performance Test)</h6>
                                <x-customer-search-selector
                                    name="customer_id_5"
                                    id="customer_id_5"
                                    :customers="collect(range(1, 50))->map(function($i) {
                                        return (object)[
                                            'id' => $i,
                                            'name' => 'Khách hàng ' . $i,
                                            'customer_code' => 'KUN' . str_pad($i, 5, '0', STR_PAD_LEFT),
                                            'email' => 'customer' . $i . '@email.com'
                                        ];
                                    })"
                                    label="Chọn Khách Hàng (50 khách hàng)"
                                    placeholder="Tìm kiếm trong 50 khách hàng..."
                                    help-text="Test hiệu suất với 50 khách hàng"
                                />
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="showFormData()">
                                    <i class="fas fa-eye me-1"></i>
                                    Xem dữ liệu form
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    Submit Test
                                </button>
                            </div>
                        </form>
                        
                        <!-- Form Data Display -->
                        <div class="mt-4 d-none" id="form-data-display">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Form Data:</h6>
                                <pre id="form-data-content"></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function showFormData() {
            const form = document.querySelector('form');
            const formData = new FormData(form);
            const data = {};
            
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            document.getElementById('form-data-content').textContent = JSON.stringify(data, null, 2);
            document.getElementById('form-data-display').classList.remove('d-none');
        }
        
        // Test keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'd') {
                e.preventDefault();
                showFormData();
            }
        });
        
        console.log('Customer Search Selector Test Page Loaded');
        console.log('Press Ctrl+D to show form data');
    </script>
</body>
</html>
