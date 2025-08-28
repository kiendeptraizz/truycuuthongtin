{{--
    Service Package Category Selector Component
    
    Props:
    - $servicePackages: Collection of service packages
    - $name: Input name (default: 'service_package_id')
    - $id: Input id (default: 'service_package_id')
    - $required: Boolean (default: true)
    - $selected: Selected value (default: old value)
    - $placeholder: Placeholder text (default: 'Chọn gói dịch vụ')
--}}

@props([
    'servicePackages',
    'name' => 'service_package_id',
    'id' => 'service_package_id',
    'required' => true,
    'selected' => null,
    'placeholder' => 'Chọn gói dịch vụ'
])

@php
    $selected = $selected ?? old($name);
    
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

<div class="service-package-category-selector">
    <select 
        class="form-select @error($name) is-invalid @enderror" 
        id="{{ $id }}" 
        name="{{ $name }}" 
        {{ $required ? 'required' : '' }}
        data-bs-toggle="tooltip"
        data-bs-placement="top"
        title="Chọn gói dịch vụ theo danh mục">
        
        <option value="">{{ $placeholder }}</option>
        
        @foreach($sortedCategories as $categoryName => $packages)
            @php
                $config = $categoryConfig[$categoryName] ?? [
                    'icon' => '📦',
                    'color' => '#6c757d',
                    'bg_color' => '#f8f9fa'
                ];
            @endphp
            
            <optgroup 
                label="{{ $config['icon'] }} {{ $categoryName }} ({{ $packages->count() }} gói)"
                data-category="{{ $categoryName }}"
                style="background-color: {{ $config['bg_color'] }}; color: {{ $config['color'] }}; font-weight: bold;">
                
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
                        style="padding-left: 20px; color: #333; background-color: white;"
                        {{ $selected == $package->id ? 'selected' : '' }}>
                        
                        {{ $package->name }}
                        - {{ $accountTypeIcon }} {{ $package->account_type }}
                        - {{ formatPrice($package->price) }}
                        @if($durationText)
                            / {{ $durationText }}
                        @endif
                        
                        @if($package->cost_price)
                            (Lãi: {{ formatPrice($package->price - $package->cost_price) }})
                        @endif
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    </select>
    
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    
    <!-- Package Info Display -->
    <div class="package-info mt-2" id="{{ $id }}_info" style="display: none;">
        <div class="card border-info">
            <div class="card-body p-2">
                <div class="row g-2 text-sm">
                    <div class="col-md-6">
                        <strong class="text-info">Thông tin gói:</strong>
                        <div id="{{ $id }}_package_name" class="fw-bold"></div>
                        <div id="{{ $id }}_category" class="text-muted"></div>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-success">Chi tiết:</strong>
                        <div id="{{ $id }}_account_type"></div>
                        <div id="{{ $id }}_price_duration" class="text-success fw-bold"></div>
                        <div id="{{ $id }}_profit" class="text-primary" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced styling for category selector */
.service-package-category-selector .form-select {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.service-package-category-selector .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.service-package-category-selector .form-select:hover {
    border-color: #0d6efd;
}

/* Optgroup styling */
.service-package-category-selector optgroup {
    font-weight: bold;
    font-size: 0.9rem;
    padding: 8px 12px;
    margin: 4px 0;
}

/* Option styling */
.service-package-category-selector option {
    padding: 8px 16px;
    font-size: 0.9rem;
    line-height: 1.4;
}

.service-package-category-selector option:hover {
    background-color: #f8f9fa !important;
}

/* Package info card */
.service-package-category-selector .package-info .card {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.service-package-category-selector .package-info .text-sm {
    font-size: 0.875rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .service-package-category-selector .form-select {
        font-size: 0.9rem;
    }
    
    .service-package-category-selector .package-info .row {
        flex-direction: column;
    }
}

/* Animation for info display */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.service-package-category-selector .package-info {
    animation: slideDown 0.3s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selector = document.getElementById('{{ $id }}');
    const infoDiv = document.getElementById('{{ $id }}_info');
    const packageNameEl = document.getElementById('{{ $id }}_package_name');
    const categoryEl = document.getElementById('{{ $id }}_category');
    const accountTypeEl = document.getElementById('{{ $id }}_account_type');
    const priceDurationEl = document.getElementById('{{ $id }}_price_duration');
    const profitEl = document.getElementById('{{ $id }}_profit');
    
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
    
    // Initialize Bootstrap tooltips if available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        new bootstrap.Tooltip(selector);
    }
});
</script>
