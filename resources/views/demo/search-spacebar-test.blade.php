<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Search Spacebar - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .test-section {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .test-result {
            background: #f8f9fa;
            border-radius: 4px;
            padding: 10px;
            margin-top: 10px;
            font-family: monospace;
        }
        .success { color: #198754; }
        .error { color: #dc3545; }

        /* Search box styling like in grid selector */
        .search-box {
            position: relative;
        }
        .search-box .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 2;
        }
        .search-box .search-input {
            padding-left: 40px;
        }

        /* Highlight inputs when they have focus */
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-keyboard me-2 text-primary"></i>
                        Test Search Spacebar Functionality
                    </h1>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Quay lại Dashboard
                    </a>
                </div>

                <!-- Test Instructions -->
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle me-2"></i>Hướng dẫn test:</h5>
                    <ul class="mb-0">
                        <li>Thử nhập các cụm từ có dấu cách như: "ChatGPT Plus", "Google Drive", "Microsoft Office"</li>
                        <li>Kiểm tra xem có thể nhập dấu cách bình thường không</li>
                        <li>Xem kết quả hiển thị trong phần "Test Result" bên dưới mỗi input</li>
                    </ul>
                </div>

                <!-- Test 1: Simple Search Input -->
                <div class="test-section">
                    <h4><i class="fas fa-search me-2 text-primary"></i>Test 1: Search Input với Grid Selector Style</h4>
                    <p class="text-muted">Test input tìm kiếm với style giống Service Package Grid Selector</p>

                    <div class="search-box mb-3">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text"
                               class="form-control search-input"
                               placeholder="Tìm kiếm gói dịch vụ (thử nhập: ChatGPT Plus)..."
                               id="grid_style_search">
                    </div>

                    <div class="test-result">
                        <strong>Input Value:</strong> <span id="grid_result">Chưa nhập</span>
                    </div>
                </div>

                <!-- Test 2: Dropdown Search -->
                <div class="test-section">
                    <h4><i class="fas fa-list me-2 text-success"></i>Test 2: Dropdown với Search</h4>
                    <p class="text-muted">Test dropdown selector với tìm kiếm</p>

                    <select class="form-select" id="dropdown_search">
                        <option value="">Chọn gói dịch vụ...</option>
                        <option value="1">ChatGPT Plus</option>
                        <option value="2">Google Drive Premium</option>
                        <option value="3">Microsoft Office 365</option>
                        <option value="4">Adobe Creative Cloud</option>
                    </select>

                    <div class="test-result">
                        <strong>Selected Value:</strong> <span id="dropdown_result">Chưa chọn</span>
                    </div>
                </div>

                <!-- Test 3: Customer Search Style -->
                <div class="test-section">
                    <h4><i class="fas fa-users me-2 text-warning"></i>Test 3: Customer Search Style</h4>
                    <p class="text-muted">Test input với style giống Customer Search</p>

                    <div class="mb-3">
                        <label for="customer_search" class="form-label">Tìm kiếm khách hàng</label>
                        <input type="text"
                               class="form-control customer-search-input"
                               id="customer_search"
                               placeholder="Tìm kiếm khách hàng (thử nhập: Nguyễn Văn A)...">
                    </div>

                    <div class="test-result">
                        <strong>Input Value:</strong> <span id="customer_result">Chưa nhập</span>
                    </div>
                </div>

                <!-- Test 4: Regular Search Input -->
                <div class="test-section">
                    <h4><i class="fas fa-search me-2 text-info"></i>Test 4: Regular Search Input</h4>
                    <p class="text-muted">Input tìm kiếm thông thường để so sánh</p>
                    
                    <div class="mb-3">
                        <label for="regular_search" class="form-label">Tìm kiếm thông thường</label>
                        <input type="text" 
                               class="form-control" 
                               id="regular_search" 
                               placeholder="Nhập từ khóa tìm kiếm có dấu cách...">
                    </div>
                    
                    <div class="test-result">
                        <strong>Input Value:</strong> <span id="regular_result">Chưa nhập</span>
                    </div>
                </div>

                <!-- Test Results Summary -->
                <div class="alert alert-light">
                    <h5><i class="fas fa-clipboard-check me-2"></i>Kết quả test:</h5>
                    <div id="test_summary">
                        <p class="mb-0">Hãy thử nhập các cụm từ có dấu cách vào các input trên để kiểm tra.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Monitor input changes
        document.getElementById('grid_style_search').addEventListener('input', function() {
            document.getElementById('grid_result').textContent = this.value || 'Chưa nhập';
        });

        document.getElementById('dropdown_search').addEventListener('change', function() {
            const selectedText = this.options[this.selectedIndex].text;
            document.getElementById('dropdown_result').textContent = this.value ? selectedText : 'Chưa chọn';
        });

        document.getElementById('customer_search').addEventListener('input', function() {
            document.getElementById('customer_result').textContent = this.value || 'Chưa nhập';
        });

        document.getElementById('regular_search').addEventListener('input', function() {
            document.getElementById('regular_result').textContent = this.value || 'Chưa nhập';
        });

        // Monitor all search inputs for spacebar functionality
        let spacebarTests = {
            grid: false,
            dropdown: false,
            customer: false,
            regular: false
        };

        function updateTestSummary() {
            const summary = document.getElementById('test_summary');
            const results = [];

            Object.keys(spacebarTests).forEach(key => {
                const status = spacebarTests[key] ?
                    '<span class="success"><i class="fas fa-check"></i> PASS</span>' :
                    '<span class="error"><i class="fas fa-times"></i> CHƯA TEST</span>';
                results.push(`${key.toUpperCase()}: ${status}`);
            });

            summary.innerHTML = '<p class="mb-0">' + results.join(' | ') + '</p>';
        }

        // Test spacebar in search inputs
        document.addEventListener('keydown', function(e) {
            if (e.key === ' ') {
                const target = e.target;
                if (target.id === 'grid_style_search') {
                    spacebarTests.grid = true;
                } else if (target.id === 'dropdown_search') {
                    spacebarTests.dropdown = true;
                } else if (target.id === 'customer_search') {
                    spacebarTests.customer = true;
                } else if (target.id === 'regular_search') {
                    spacebarTests.regular = true;
                }
                updateTestSummary();
            }
        });

        updateTestSummary();
        console.log('Search Spacebar Test Page Loaded');
    </script>
</body>
</html>
