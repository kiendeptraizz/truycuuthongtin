<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Category Selector</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-layer-group me-2 text-success"></i>
                            Test Service Package Category Selector
                        </h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-box me-1"></i>
                                    Chọn gói dịch vụ <span class="text-danger">*</span>
                                </label>
                                
                                @include('components.service-package-category-selector', [
                                    'servicePackages' => $servicePackages,
                                    'name' => 'service_package_id',
                                    'id' => 'service_selector',
                                    'required' => true,
                                    'placeholder' => 'Chọn gói dịch vụ theo danh mục...'
                                ])
                            </div>
                            
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Lưu
                                </button>
                            </div>
                        </form>
                        
                        <!-- Statistics -->
                        <div class="mt-4">
                            <h6>Thống kê:</h6>
                            <div class="row">
                                @php
                                    $categoryStats = $servicePackages->groupBy('category.name');
                                @endphp
                                
                                @foreach($categoryStats as $categoryName => $packages)
                                    <div class="col-md-4 col-sm-6 mb-2">
                                        <div class="border rounded p-2 text-center">
                                            <div class="fw-bold text-primary">{{ $packages->count() }}</div>
                                            <small class="text-muted">{{ $categoryName }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
