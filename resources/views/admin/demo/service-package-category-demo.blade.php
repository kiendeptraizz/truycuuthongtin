@extends('layouts.admin')

@section('title', 'Demo - Service Package Category Selector')
@section('page-title', 'Demo - Service Package Category Selector')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-layer-group me-2 text-success"></i>
                        Service Package Category Selector Demo
                    </h5>
                    <p class="text-muted mb-0">
                        Component selector nhóm theo danh mục dịch vụ thay vì loại tài khoản
                    </p>
                </div>

                <div class="card-body">
                    <!-- Comparison Section -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="alert alert-success">
                                <h6 class="alert-heading">
                                    <i class="fas fa-layer-group me-2"></i>
                                    Category Selector (Mới)
                                </h6>
                                <ul class="mb-0 small">
                                    <li>Nhóm theo danh mục dịch vụ</li>
                                    <li>Sắp xếp theo giá (thấp → cao)</li>
                                    <li>Hiển thị loại tài khoản trong option</li>
                                    <li>Thông tin chi tiết khi chọn</li>
                                    <li>Icons cho categories</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-primary">
                                <h6 class="alert-heading">
                                    <i class="fas fa-users me-2"></i>
                                    Account Type Selector (Cũ)
                                </h6>
                                <ul class="mb-0 small">
                                    <li>Nhóm theo loại tài khoản</li>
                                    <li>Sắp xếp theo priority</li>
                                    <li>Format cơ bản</li>
                                    <li>Không có thông tin chi tiết</li>
                                    <li>Styling đơn giản</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">
                                    <i class="fas fa-th-large me-2"></i>
                                    Grid Selector (Mới nhất)
                                </h6>
                                <ul class="mb-0 small">
                                    <li>Grid layout với cards</li>
                                    <li>Search & filter</li>
                                    <li>Phân loại kép</li>
                                    <li>Interactive UI</li>
                                    <li>Responsive design</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Demo Forms -->
                    <form>
                        <div class="row">
                            <!-- New Category Selector -->
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-layer-group me-2"></i>
                                            Category Selector (Mới)
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-box me-1"></i>
                                                Gói dịch vụ <span class="text-danger">*</span>
                                                <small class="text-muted ms-2">(Nhóm theo danh mục)</small>
                                            </label>
                                            
                                            <x-service-package-category-selector 
                                                :service-packages="$servicePackages"
                                                name="category_service_package_id"
                                                id="category_selector"
                                                :required="true"
                                                placeholder="Chọn gói dịch vụ theo danh mục..."
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Old Account Type Selector -->
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-users me-2"></i>
                                            Account Type Selector (Cũ)
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Gói dịch vụ <span class="text-danger">*</span>
                                                <small class="text-muted ms-2">(Nhóm theo loại tài khoản)</small>
                                            </label>
                                            
                                            <x-service-package-selector 
                                                :service-packages="$servicePackages"
                                                :account-type-priority="$accountTypePriority"
                                                name="account_service_package_id"
                                                id="account_selector"
                                                :required="true"
                                                placeholder="Chọn gói dịch vụ..."
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Selection Comparison -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-success">Thông tin gói đã chọn (Category Selector)</h6>
                                </div>
                                <div class="card-body">
                                    <div id="category-selection-info">
                                        <div class="text-muted">Chưa chọn gói nào</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">Thông tin gói đã chọn (Account Type Selector)</h6>
                                </div>
                                <div class="card-body">
                                    <div id="account-selection-info">
                                        <div class="text-muted">Chưa chọn gói nào</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-chart-pie me-2"></i>
                                        Thống kê gói dịch vụ theo danh mục
                                    </h6>
                                    <div class="row text-center">
                                        @php
                                            $categoryStats = $servicePackages->groupBy('category.name');
                                        @endphp
                                        
                                        @foreach($categoryStats as $categoryName => $packages)
                                            <div class="col-md-2 col-sm-4 col-6 mb-3">
                                                <div class="border rounded p-2">
                                                    <div class="h4 mb-1 text-primary">{{ $packages->count() }}</div>
                                                    <small class="text-muted">{{ $categoryName }}</small>
                                                    <div class="mt-1">
                                                        <small class="text-success">
                                                            {{ formatPrice($packages->min('price')) }} - {{ formatPrice($packages->max('price')) }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                                            <div class="border rounded p-2 bg-primary text-white">
                                                <div class="h4 mb-1">{{ $servicePackages->count() }}</div>
                                                <small>Tổng cộng</small>
                                                <div class="mt-1">
                                                    <small>
                                                        {{ formatPrice($servicePackages->min('price')) }} - {{ formatPrice($servicePackages->max('price')) }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Usage Examples -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-code me-2"></i>
                                        Cách sử dụng Component
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-success">Category Selector (Mới)</h6>
                                            <pre class="bg-light p-3 rounded"><code>&lt;x-service-package-category-selector 
    :service-packages="$servicePackages"
    name="service_package_id"
    id="service_selector"
    :required="true"
    placeholder="Chọn gói dịch vụ..."
/&gt;</code></pre>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Account Type Selector (Cũ)</h6>
                                            <pre class="bg-light p-3 rounded"><code>&lt;x-service-package-selector 
    :service-packages="$servicePackages"
    :account-type-priority="$accountTypePriority"
    name="service_package_id"
    id="service_selector"
    :required="true"
    placeholder="Chọn gói dịch vụ..."
/&gt;</code></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelector = document.getElementById('category_selector');
    const accountSelector = document.getElementById('account_selector');
    const categoryInfo = document.getElementById('category-selection-info');
    const accountInfo = document.getElementById('account-selection-info');
    
    function updatePackageInfo(selector, infoDiv, label) {
        const selectedValue = selector.value;
        
        if (selectedValue) {
            const selectedOption = selector.options[selector.selectedIndex];
            const packageName = selectedOption.text.split(' - ')[0];
            const accountType = selectedOption.dataset.accountType || 'N/A';
            const category = selectedOption.dataset.category || 'N/A';
            const price = selectedOption.dataset.price || '0';
            const duration = selectedOption.dataset.duration || '30';
            const costPrice = selectedOption.dataset.costPrice || '0';
            
            const profit = parseFloat(price) - parseFloat(costPrice);
            const profitText = profit > 0 ? formatCurrency(profit) + ' đ' : 'N/A';
            
            infoDiv.innerHTML = `
                <div class="mb-2"><strong>${label}:</strong></div>
                <div class="row g-2 small">
                    <div class="col-6"><strong>Tên:</strong> ${packageName}</div>
                    <div class="col-6"><strong>Danh mục:</strong> ${category}</div>
                    <div class="col-6"><strong>Loại TK:</strong> <span class="badge bg-secondary">${accountType}</span></div>
                    <div class="col-6"><strong>Thời hạn:</strong> ${duration} ngày</div>
                    <div class="col-6"><strong>Giá:</strong> <span class="text-success fw-bold">${formatCurrency(price)} đ</span></div>
                    <div class="col-6"><strong>Lợi nhuận:</strong> <span class="text-primary">${profitText}</span></div>
                </div>
            `;
        } else {
            infoDiv.innerHTML = `<div class="text-muted">Chưa chọn gói nào</div>`;
        }
    }
    
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(parseFloat(amount) || 0);
    }
    
    if (categorySelector && categoryInfo) {
        categorySelector.addEventListener('change', function() {
            updatePackageInfo(this, categoryInfo, 'Category Selector');
        });
    }
    
    if (accountSelector && accountInfo) {
        accountSelector.addEventListener('change', function() {
            updatePackageInfo(this, accountInfo, 'Account Type Selector');
        });
    }
});
</script>
@endpush
@endsection
