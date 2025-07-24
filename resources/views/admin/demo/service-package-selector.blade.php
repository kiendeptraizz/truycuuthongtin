@extends('layouts.admin')

@section('title', 'Demo: Service Package Selector')
@section('page-title', 'Demo: Service Package Selector')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-rocket me-2"></i>
                        🎯 Demo: Giao diện chọn gói dịch vụ mới
                    </h5>
                    <small class="text-muted">
                        Phân nhóm theo loại tài khoản với ưu tiên hiển thị và styling đặc biệt
                    </small>
                </div>

                <div class="card-body">
                    <!-- Thông tin tổng quan -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Tính năng mới:</h6>
                                <ul class="mb-0">
                                    <li>✅ <strong>Phân nhóm theo loại tài khoản</strong> thay vì category</li>
                                    <li>✅ <strong>Tài khoản dùng chung</strong> được ưu tiên hiển thị đầu tiên</li>
                                    <li>✅ <strong>Styling đặc biệt</strong> với màu sắc và icon riêng biệt</li>
                                    <li>✅ <strong>Legend hiển thị</strong> các loại tài khoản có sẵn</li>
                                    <li>✅ <strong>Responsive design</strong> và accessibility</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Demo Form -->
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-star me-2"></i>
                                    Giao diện mới (Nhóm theo loại tài khoản)
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="new_selector" class="form-label">
                                        <i class="fas fa-box me-1"></i>
                                        Gói dịch vụ <span class="text-danger">*</span>
                                        <small class="text-muted ms-2">(Nhóm theo loại tài khoản)</small>
                                    </label>
                                    
                                    <x-service-package-selector 
                                        :service-packages="$servicePackages"
                                        :account-type-priority="$accountTypePriority"
                                        name="new_service_package_id"
                                        id="new_selector"
                                        :required="true"
                                        placeholder="Chọn gói dịch vụ phù hợp..."
                                    />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-secondary mb-3">
                                    <i class="fas fa-list me-2"></i>
                                    Giao diện cũ (Nhóm theo category)
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="old_selector" class="form-label">
                                        Gói dịch vụ <span class="text-danger">*</span>
                                        <small class="text-muted ms-2">(Nhóm theo category)</small>
                                    </label>
                                    
                                    <select class="form-select" id="old_selector" name="old_service_package_id">
                                        <option value="">Chọn gói dịch vụ</option>
                                        @foreach($servicePackages->groupBy('category.name') as $categoryName => $packages)
                                            <optgroup label="{{ $categoryName }}">
                                                @foreach($packages as $package)
                                                    <option value="{{ $package->id }}" 
                                                            data-price="{{ $package->price }}"
                                                            data-duration="{{ $package->default_duration_days }}">
                                                        {{ $package->name }} - {{ $package->account_type }} 
                                                        ({{ number_format($package->price) }}đ)
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin gói được chọn -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-info me-2"></i>
                                            Thông tin gói dịch vụ được chọn
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div id="new_package_info">
                                                    <strong>Giao diện mới:</strong>
                                                    <div class="text-muted">Chưa chọn gói nào</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div id="old_package_info">
                                                    <strong>Giao diện cũ:</strong>
                                                    <div class="text-muted">Chưa chọn gói nào</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Thống kê -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-chart-bar me-2"></i>
                                Thống kê gói dịch vụ theo loại tài khoản
                            </h6>
                            
                            <div class="row">
                                @php
                                    $groupedStats = $servicePackages->groupBy('account_type');
                                    $accountTypeConfig = [
                                        'Tài khoản dùng chung' => [
                                            'icon' => '👥',
                                            'color' => '#e74c3c',
                                            'bg_color' => '#fdf2f2',
                                            'description' => 'Nhiều người cùng sử dụng'
                                        ],
                                        'Tài khoản chính chủ' => [
                                            'icon' => '👤',
                                            'color' => '#3498db',
                                            'bg_color' => '#f8f9fa',
                                            'description' => 'Quyền sở hữu hoàn toàn'
                                        ],
                                        'Tài khoản add family' => [
                                            'icon' => '👨‍👩‍👧‍👦',
                                            'color' => '#f39c12',
                                            'bg_color' => '#fef9e7',
                                            'description' => 'Thêm vào gói gia đình'
                                        ],
                                        'Tài khoản cấp (dùng riêng)' => [
                                            'icon' => '🔐',
                                            'color' => '#9b59b6',
                                            'bg_color' => '#f8f4fd',
                                            'description' => 'Tài khoản phụ riêng biệt'
                                        ]
                                    ];
                                @endphp
                                
                                @foreach($accountTypePriority as $accountType => $priority)
                                    @if($groupedStats->has($accountType))
                                        @php
                                            $packages = $groupedStats->get($accountType);
                                            $config = $accountTypeConfig[$accountType];
                                        @endphp
                                        
                                        <div class="col-md-6 col-lg-3 mb-3">
                                            <div class="card h-100" style="border-left: 4px solid {{ $config['color'] }};">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="fs-4 me-2">{{ $config['icon'] }}</span>
                                                        <div>
                                                            <h6 class="mb-0" style="color: {{ $config['color'] }};">
                                                                {{ $accountType }}
                                                            </h6>
                                                            <small class="text-muted">Ưu tiên: {{ $priority }}</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-2">
                                                        <span class="badge rounded-pill" 
                                                              style="background-color: {{ $config['bg_color'] }}; color: {{ $config['color'] }};">
                                                            {{ $packages->count() }} gói
                                                        </span>
                                                    </div>
                                                    
                                                    <small class="text-muted">{{ $config['description'] }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Hướng dẫn sử dụng -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-lightbulb me-2"></i>
                                        Hướng dẫn sử dụng
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>🎯 Ưu điểm của giao diện mới:</h6>
                                            <ul>
                                                <li>Tài khoản dùng chung được ưu tiên hiển thị</li>
                                                <li>Phân nhóm rõ ràng theo loại tài khoản</li>
                                                <li>Màu sắc và icon trực quan</li>
                                                <li>Legend giúp hiểu rõ từng loại</li>
                                                <li>Responsive trên mọi thiết bị</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>📍 Vị trí áp dụng:</h6>
                                            <ul>
                                                <li><a href="{{ route('admin.customer-services.create') }}" target="_blank">Tạo dịch vụ mới</a></li>
                                                <li><a href="{{ route('admin.customers.index') }}" target="_blank">Gán dịch vụ cho khách hàng</a></li>
                                                <li>Chỉnh sửa dịch vụ hiện có</li>
                                                <li>Các form khác có dropdown gói dịch vụ</li>
                                            </ul>
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle new selector change
    const newSelector = document.getElementById('new_selector');
    const oldSelector = document.getElementById('old_selector');
    const newInfo = document.getElementById('new_package_info');
    const oldInfo = document.getElementById('old_package_info');
    
    function updatePackageInfo(selector, infoDiv, label) {
        const selectedOption = selector.options[selector.selectedIndex];
        if (selectedOption.value) {
            const price = selectedOption.dataset.price || '0';
            const duration = selectedOption.dataset.duration || 'N/A';
            const accountType = selectedOption.dataset.accountType || 'N/A';
            const category = selectedOption.dataset.category || 'N/A';
            
            infoDiv.innerHTML = `
                <strong>${label}:</strong>
                <div class="mt-1">
                    <div><strong>Tên:</strong> ${selectedOption.text.split(' - ')[0]}</div>
                    <div><strong>Loại:</strong> <span class="badge bg-primary">${accountType}</span></div>
                    <div><strong>Category:</strong> ${category}</div>
                    <div><strong>Giá:</strong> <span class="text-success">${parseInt(price).toLocaleString()}đ</span></div>
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
