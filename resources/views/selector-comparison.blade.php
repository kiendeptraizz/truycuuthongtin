<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>So sánh Service Package Selectors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .comparison-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .comparison-card:hover {
            border-color: #0d6efd;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .selector-demo {
            min-height: 200px;
        }
        .feature-list {
            font-size: 0.9rem;
        }
        .feature-list li {
            margin-bottom: 0.25rem;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="text-center mb-4">
                    <h2 class="display-6">
                        <i class="fas fa-layer-group me-3 text-primary"></i>
                        So sánh Service Package Selectors
                    </h2>
                    <p class="text-muted">Comparison giữa 3 phiên bản selector khác nhau</p>
                </div>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="h3 mb-1">{{ $servicePackages->count() }}</div>
                                <small>Tổng gói dịch vụ</small>
                            </div>
                            <div class="col-md-3">
                                <div class="h3 mb-1">{{ $servicePackages->groupBy('category.name')->count() }}</div>
                                <small>Danh mục</small>
                            </div>
                            <div class="col-md-3">
                                <div class="h3 mb-1">{{ $servicePackages->groupBy('account_type')->count() }}</div>
                                <small>Loại tài khoản</small>
                            </div>
                            <div class="col-md-3">
                                <div class="h3 mb-1">{{ number_format($servicePackages->min('price'), 0, ',', '.') }} - {{ number_format($servicePackages->max('price'), 0, ',', '.') }} đ</div>
                                <small>Khoảng giá</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparison Grid -->
        <div class="row">
            <!-- Category Selector (New) -->
            <div class="col-lg-4 mb-4">
                <div class="card comparison-card h-100 border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-layer-group me-2"></i>
                            Category Selector
                            <span class="badge bg-light text-success ms-2">MỚI</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="selector-demo mb-3">
                            @include('components.service-package-category-selector', [
                                'servicePackages' => $servicePackages,
                                'name' => 'category_package_id',
                                'id' => 'category_selector',
                                'required' => true,
                                'placeholder' => 'Chọn theo danh mục...'
                            ])
                        </div>
                        
                        <h6 class="text-success">✅ Ưu điểm:</h6>
                        <ul class="feature-list text-success">
                            <li>Nhóm theo danh mục dịch vụ</li>
                            <li>Sắp xếp theo giá (thấp → cao)</li>
                            <li>Hiển thị thông tin chi tiết</li>
                            <li>Icons trực quan cho categories</li>
                            <li>Hiển thị lợi nhuận</li>
                            <li>Animation mượt mà</li>
                        </ul>
                        
                        <h6 class="text-warning mt-3">⚠️ Nhược điểm:</h6>
                        <ul class="feature-list text-muted">
                            <li>Phức tạp hơn về code</li>
                            <li>Cần nhiều dữ liệu hơn</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Account Type Selector (Old) -->
            <div class="col-lg-4 mb-4">
                <div class="card comparison-card h-100 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            Account Type Selector
                            <span class="badge bg-light text-primary ms-2">CŨ</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="selector-demo mb-3">
                            @include('components.service-package-selector', [
                                'servicePackages' => $servicePackages,
                                'accountTypePriority' => $accountTypePriority,
                                'name' => 'account_package_id',
                                'id' => 'account_selector',
                                'required' => true,
                                'placeholder' => 'Chọn theo loại TK...'
                            ])
                        </div>
                        
                        <h6 class="text-success">✅ Ưu điểm:</h6>
                        <ul class="feature-list text-success">
                            <li>Đơn giản, dễ hiểu</li>
                            <li>Nhóm theo loại tài khoản</li>
                            <li>Ít phụ thuộc</li>
                            <li>Hiệu suất tốt</li>
                        </ul>
                        
                        <h6 class="text-warning mt-3">⚠️ Nhược điểm:</h6>
                        <ul class="feature-list text-muted">
                            <li>Không có thông tin chi tiết</li>
                            <li>Styling cơ bản</li>
                            <li>Khó tìm theo nhu cầu</li>
                            <li>Không hiển thị lợi nhuận</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Grid Selector (Newest) -->
            <div class="col-lg-4 mb-4">
                <div class="card comparison-card h-100 border-info">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-th-large me-2"></i>
                            Grid Selector
                            <span class="badge bg-light text-info ms-2">MỚI NHẤT</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="selector-demo mb-3">
                            <div class="text-center p-4 bg-light rounded">
                                <i class="fas fa-th-large fa-3x text-info mb-2"></i>
                                <p class="text-muted mb-0">Grid layout với cards</p>
                                <small class="text-muted">Xem demo tại trang riêng</small>
                            </div>
                        </div>
                        
                        <h6 class="text-success">✅ Ưu điểm:</h6>
                        <ul class="feature-list text-success">
                            <li>UI hiện đại với cards</li>
                            <li>Search & filter mạnh mẽ</li>
                            <li>Phân loại kép (category + type)</li>
                            <li>Interactive, responsive</li>
                            <li>Hiển thị đầy đủ thông tin</li>
                            <li>UX tốt nhất</li>
                        </ul>
                        
                        <h6 class="text-warning mt-3">⚠️ Nhược điểm:</h6>
                        <ul class="feature-list text-muted">
                            <li>Phức tạp nhất</li>
                            <li>Tốn nhiều không gian</li>
                            <li>Cần nhiều CSS/JS</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage Recommendations -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-lightbulb me-2 text-warning"></i>
                            Khuyến nghị sử dụng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                    <i class="fas fa-layer-group fa-2x text-success mb-2"></i>
                                    <h6 class="text-success">Category Selector</h6>
                                    <p class="small text-muted mb-0">
                                        <strong>Dùng khi:</strong> Cần tìm kiếm theo nhu cầu/danh mục, 
                                        muốn hiển thị thông tin chi tiết, form đơn giản
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                    <h6 class="text-primary">Account Type Selector</h6>
                                    <p class="small text-muted mb-0">
                                        <strong>Dùng khi:</strong> Cần selector đơn giản, 
                                        ưu tiên hiệu suất, form cơ bản
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                                    <i class="fas fa-th-large fa-2x text-info mb-2"></i>
                                    <h6 class="text-info">Grid Selector</h6>
                                    <p class="small text-muted mb-0">
                                        <strong>Dùng khi:</strong> Cần UX tốt nhất, 
                                        có nhiều không gian, form phức tạp
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="mb-3">
                            <i class="fas fa-external-link-alt me-2"></i>
                            Xem demo chi tiết
                        </h6>
                        <div class="btn-group" role="group">
                            <a href="/admin/demo/service-package-category" class="btn btn-success">
                                <i class="fas fa-layer-group me-1"></i>
                                Category Demo
                            </a>
                            <a href="/admin/demo/service-package-grid" class="btn btn-info">
                                <i class="fas fa-th-large me-1"></i>
                                Grid Demo
                            </a>
                            <a href="/simple-category-test" class="btn btn-outline-success">
                                <i class="fas fa-flask me-1"></i>
                                Simple Test
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Add hover effects to comparison cards
        document.querySelectorAll('.comparison-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
    </script>
</body>
</html>
