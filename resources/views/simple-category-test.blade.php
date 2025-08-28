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
                                
                                @php
                                    $selected = old('service_package_id');
                                    
                                    // Define category icons and colors for visual enhancement
                                    $categoryConfig = [
                                        'AI & Trí tuệ nhân tạo' => [
                                            'icon' => '🤖',
                                            'color' => '#2ecc71',
                                            'bg_color' => '#e8f8f5'
                                        ],
                                        'Giải trí' => [
                                            'icon' => '🎬',
                                            'color' => '#e74c3c',
                                            'bg_color' => '#fdf2f2'
                                        ],
                                        'Thiết kế & Sáng tạo' => [
                                            'icon' => '🎨',
                                            'color' => '#9b59b6',
                                            'bg_color' => '#f8f4fd'
                                        ],
                                        'Công cụ làm việc' => [
                                            'icon' => '💼',
                                            'color' => '#34495e',
                                            'bg_color' => '#f8f9fa'
                                        ],
                                        'Lưu trữ & Đám mây' => [
                                            'icon' => '☁️',
                                            'color' => '#3498db',
                                            'bg_color' => '#ebf3fd'
                                        ],
                                        'Học tập & Giáo dục' => [
                                            'icon' => '📚',
                                            'color' => '#f39c12',
                                            'bg_color' => '#fef9e7'
                                        ]
                                    ];

                                    // Define account type icons for better visual distinction
                                    $accountTypeIcons = [
                                        'Tài khoản dùng chung' => '👥',
                                        'Tài khoản chính chủ' => '👤',
                                        'Tài khoản add family' => '👨‍👩‍👧‍👦',
                                        'Tài khoản cấp (dùng riêng)' => '🔐'
                                    ];

                                    // Group packages by category
                                    $groupedPackages = $servicePackages->groupBy('category.name');
                                    
                                    // Sort categories alphabetically
                                    $sortedCategories = $groupedPackages->sortKeys();
                                    
                                    // Sort packages within each category by price (low to high)
                                    $sortedCategories = $sortedCategories->map(function ($packages) {
                                        return $packages->sortBy('price');
                                    });
                                @endphp

                                <select class="form-select" id="service_selector" name="service_package_id" required>
                                    <option value="">Chọn gói dịch vụ theo danh mục...</option>
                                    
                                    @foreach($sortedCategories as $categoryName => $packages)
                                        @php
                                            $config = $categoryConfig[$categoryName] ?? [
                                                'icon' => '📦',
                                                'color' => '#6c757d',
                                                'bg_color' => '#f8f9fa'
                                            ];
                                        @endphp
                                        
                                        <optgroup label="{{ $config['icon'] }} {{ $categoryName }} ({{ $packages->count() }} gói)">
                                            @foreach($packages as $package)
                                                @php
                                                    $accountTypeIcon = $accountTypeIcons[$package->account_type] ?? '📦';
                                                    $durationText = $package->custom_duration ?: ($package->default_duration_days . ' ngày');
                                                @endphp
                                                
                                                <option 
                                                    value="{{ $package->id }}" 
                                                    data-price="{{ $package->price }}"
                                                    data-duration="{{ $package->default_duration_days }}"
                                                    data-account-type="{{ $package->account_type }}"
                                                    data-category="{{ $categoryName }}"
                                                    data-cost-price="{{ $package->cost_price ?? 0 }}"
                                                    {{ $selected == $package->id ? 'selected' : '' }}>
                                                    
                                                    {{ $package->name }}
                                                    - {{ $accountTypeIcon }} {{ $package->account_type }}
                                                    - {{ number_format($package->price, 0, ',', '.') }} đ
                                                    @if($durationText)
                                                        / {{ $durationText }}
                                                    @endif
                                                    
                                                    @if($package->cost_price)
                                                        (Lãi: {{ number_format($package->price - $package->cost_price, 0, ',', '.') }} đ)
                                                    @endif
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Package Info Display -->
                            <div class="package-info mt-2" id="service_selector_info" style="display: none;">
                                <div class="card border-info">
                                    <div class="card-body p-2">
                                        <div class="row g-2 text-sm">
                                            <div class="col-md-6">
                                                <strong class="text-info">Thông tin gói:</strong>
                                                <div id="service_selector_package_name" class="fw-bold"></div>
                                                <div id="service_selector_category" class="text-muted"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <strong class="text-success">Chi tiết:</strong>
                                                <div id="service_selector_account_type"></div>
                                                <div id="service_selector_price_duration" class="text-success fw-bold"></div>
                                                <div id="service_selector_profit" class="text-primary" style="display: none;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const selector = document.getElementById('service_selector');
        const infoDiv = document.getElementById('service_selector_info');
        const packageNameEl = document.getElementById('service_selector_package_name');
        const categoryEl = document.getElementById('service_selector_category');
        const accountTypeEl = document.getElementById('service_selector_account_type');
        const priceDurationEl = document.getElementById('service_selector_price_duration');
        const profitEl = document.getElementById('service_selector_profit');
        
        if (selector && infoDiv) {
            // Show info on selection change
            selector.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                
                if (selectedOption.value) {
                    // Extract data from selected option
                    const packageName = selectedOption.text.split(' - ')[0];
                    const category = selectedOption.dataset.category;
                    const accountType = selectedOption.dataset.accountType;
                    const price = parseFloat(selectedOption.dataset.price);
                    const costPrice = parseFloat(selectedOption.dataset.costPrice) || 0;
                    const duration = selectedOption.dataset.duration;
                    
                    // Format price
                    const formattedPrice = new Intl.NumberFormat('vi-VN').format(price) + ' đ';
                    
                    // Update info display
                    if (packageNameEl) packageNameEl.textContent = packageName;
                    if (categoryEl) categoryEl.textContent = `📂 ${category}`;
                    if (accountTypeEl) accountTypeEl.textContent = `👤 ${accountType}`;
                    if (priceDurationEl) priceDurationEl.textContent = `💰 ${formattedPrice}${duration ? ' / ' + duration + ' ngày' : ''}`;
                    
                    // Show profit if available
                    if (profitEl && costPrice > 0) {
                        const profit = price - costPrice;
                        const profitFormatted = new Intl.NumberFormat('vi-VN').format(profit) + ' đ';
                        profitEl.textContent = `📈 Lợi nhuận: ${profitFormatted}`;
                        profitEl.style.display = 'block';
                    } else if (profitEl) {
                        profitEl.style.display = 'none';
                    }
                    
                    // Show info div with animation
                    infoDiv.style.display = 'block';
                } else {
                    // Hide info div when no selection
                    infoDiv.style.display = 'none';
                }
            });
            
            // Initialize if there's already a selected value
            if (selector.value) {
                selector.dispatchEvent(new Event('change'));
            }
        }
    });
    </script>
</body>
</html>
