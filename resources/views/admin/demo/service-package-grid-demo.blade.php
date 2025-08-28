@extends('layouts.admin')

@section('title', 'Demo - Giao diện lựa chọn gói dịch vụ mới')
@section('page-title', 'Demo - Giao diện lựa chọn gói dịch vụ mới')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-rocket me-2 text-primary"></i>
                        Giao diện lựa chọn gói dịch vụ mới
                    </h5>
                    <p class="text-muted mb-0">
                        Giao diện được cải tiến với phân loại theo danh mục, tìm kiếm và lọc trực quan
                    </p>
                </div>

                <div class="card-body">
                    <!-- Comparison Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">
                                    <i class="fas fa-star me-2"></i>
                                    Giao diện mới (Grid Layout)
                                </h6>
                                <ul class="mb-0">
                                    <li>Phân loại theo danh mục và loại tài khoản</li>
                                    <li>Tìm kiếm và lọc trực quan</li>
                                    <li>Hiển thị thông tin chi tiết</li>
                                    <li>Responsive design</li>
                                    <li>Keyboard navigation</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-secondary">
                                <h6 class="alert-heading">
                                    <i class="fas fa-list me-2"></i>
                                    Giao diện cũ (Dropdown)
                                </h6>
                                <ul class="mb-0">
                                    <li>Chỉ nhóm theo loại tài khoản</li>
                                    <li>Khó duyệt khi có nhiều gói</li>
                                    <li>Thông tin hạn chế</li>
                                    <li>Không có tìm kiếm</li>
                                    <li>Chỉ có thể scroll</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Demo Form -->
                    <form>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-star me-2"></i>
                                    Giao diện mới (Grid Layout với phân loại kép)
                                </h6>
                                
                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-box me-1"></i>
                                        Gói dịch vụ <span class="text-danger">*</span>
                                        <small class="text-muted ms-2">(Phân loại theo danh mục và loại tài khoản)</small>
                                    </label>
                                    
                                    <x-service-package-grid-selector 
                                        :service-packages="$servicePackages"
                                        :account-type-priority="$accountTypePriority"
                                        name="new_service_package_id"
                                        id="new_selector"
                                        :required="true"
                                        placeholder="Chọn gói dịch vụ phù hợp..."
                                    />
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-secondary mb-3">
                                    <i class="fas fa-list me-2"></i>
                                    Giao diện cũ (Dropdown với nhóm theo loại tài khoản)
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="old_selector" class="form-label">
                                        Gói dịch vụ <span class="text-danger">*</span>
                                        <small class="text-muted ms-2">(Nhóm theo loại tài khoản)</small>
                                    </label>
                                    
                                    <x-service-package-selector 
                                        :service-packages="$servicePackages"
                                        :account-type-priority="$accountTypePriority"
                                        name="old_service_package_id"
                                        id="old_selector"
                                        :required="true"
                                        placeholder="Chọn gói dịch vụ..."
                                    />
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Selection Info -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Thông tin gói đã chọn (Giao diện mới)</h6>
                                </div>
                                <div class="card-body">
                                    <div id="new-selection-info">
                                        <strong>Giao diện mới:</strong>
                                        <div class="text-muted">Chưa chọn gói nào</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-secondary">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0">Thông tin gói đã chọn (Giao diện cũ)</h6>
                                </div>
                                <div class="card-body">
                                    <div id="old-selection-info">
                                        <strong>Giao diện cũ:</strong>
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
                                        <i class="fas fa-chart-bar me-2"></i>
                                        Thống kê gói dịch vụ
                                    </h6>
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="border-end">
                                                <h4 class="text-primary mb-0">{{ $servicePackages->count() }}</h4>
                                                <small class="text-muted">Tổng số gói</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border-end">
                                                <h4 class="text-success mb-0">{{ $servicePackages->groupBy('category.name')->count() }}</h4>
                                                <small class="text-muted">Danh mục</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border-end">
                                                <h4 class="text-warning mb-0">{{ $servicePackages->groupBy('account_type')->count() }}</h4>
                                                <small class="text-muted">Loại tài khoản</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <h4 class="text-info mb-0">{{ $servicePackages->where('is_active', true)->count() }}</h4>
                                            <small class="text-muted">Đang hoạt động</small>
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
    const newSelector = document.getElementById('new_selector');
    const oldSelector = document.getElementById('old_selector');
    const newInfo = document.getElementById('new-selection-info');
    const oldInfo = document.getElementById('old-selection-info');
    
    function updatePackageInfo(selector, infoDiv, label) {
        const selectedValue = selector.value;
        
        if (selectedValue) {
            let selectedOption, packageName, accountType, category, price, duration;
            
            if (selector.tagName === 'SELECT') {
                // Old selector (dropdown)
                selectedOption = selector.options[selector.selectedIndex];
                packageName = selectedOption.text.split(' - ')[0];
                accountType = selectedOption.dataset.accountType || 'N/A';
                category = selectedOption.dataset.category || 'N/A';
                price = selectedOption.dataset.price || '0';
                duration = selectedOption.dataset.duration || '30';
            } else {
                // New selector (grid) - get info from selected card
                const selectedCard = document.querySelector(`[data-package-id="${selectedValue}"]`);
                if (selectedCard) {
                    packageName = selectedCard.querySelector('.package-name').textContent;
                    accountType = selectedCard.dataset.accountType;
                    category = selectedCard.dataset.category;
                    price = selectedCard.querySelector('.package-price strong').textContent;
                    const priceText = selectedCard.querySelector('.package-price').textContent;
                    const durationMatch = priceText.match(/(\d+)\s*ngày/);
                    duration = durationMatch ? durationMatch[1] : '30';
                }
            }
            
            infoDiv.innerHTML = `
                <strong>${label}:</strong>
                <div class="mt-1">
                    <div><strong>Tên:</strong> ${packageName}</div>
                    <div><strong>Loại:</strong> <span class="badge bg-primary">${accountType}</span></div>
                    <div><strong>Category:</strong> ${category}</div>
                    <div><strong>Giá:</strong> <span class="text-success">${price}</span></div>
                    <div><strong>Thời hạn:</strong> ${duration} ngày</div>
                </div>
            `;
        } else {
            infoDiv.innerHTML = `<strong>${label}:</strong><div class="text-muted">Chưa chọn gói nào</div>`;
        }
    }
    
    newSelector.addEventListener('change', function() {
        updatePackageInfo(this, newInfo, 'Giao diện mới');
    });
    
    oldSelector.addEventListener('change', function() {
        updatePackageInfo(this, oldInfo, 'Giao diện cũ');
    });
});
</script>
@endpush
@endsection
