{{--
    Service Package Selector Component
    
    Props:
    - $servicePackages: Collection of service packages
    - $accountTypePriority: Array of account type priorities
    - $name: Input name (default: 'service_package_id')
    - $id: Input id (default: 'service_package_id')
    - $required: Boolean (default: true)
    - $selected: Selected value (default: old value)
    - $placeholder: Placeholder text (default: 'Chọn gói dịch vụ')
--}}

@props([
    'servicePackages',
    'accountTypePriority' => [],
    'name' => 'service_package_id',
    'id' => 'service_package_id',
    'required' => true,
    'selected' => null,
    'placeholder' => 'Chọn gói dịch vụ'
])

@php
    $selected = $selected ?? old($name);
    
    // Define account type icons and colors
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
    
    // Group packages by account_type
    $groupedPackages = $servicePackages->groupBy('account_type');
    
    // Sort groups by priority
    $sortedGroups = collect();
    foreach ($accountTypePriority as $accountType => $priority) {
        if ($groupedPackages->has($accountType)) {
            $sortedGroups->put($accountType, $groupedPackages->get($accountType));
        }
    }
    
    // Add any remaining groups not in priority list
    foreach ($groupedPackages as $accountType => $packages) {
        if (!$sortedGroups->has($accountType)) {
            $sortedGroups->put($accountType, $packages);
        }
    }
@endphp

<div class="service-package-selector">
    <select 
        class="form-select @error($name) is-invalid @enderror" 
        id="{{ $id }}" 
        name="{{ $name }}" 
        {{ $required ? 'required' : '' }}
        data-bs-toggle="tooltip"
        data-bs-placement="top"
        title="Chọn gói dịch vụ phù hợp với nhu cầu">
        
        <option value="">{{ $placeholder }}</option>
        
        @foreach($sortedGroups as $accountType => $packages)
            @php
                $config = $accountTypeConfig[$accountType] ?? [
                    'icon' => '📦',
                    'color' => '#6c757d',
                    'bg_color' => '#f8f9fa',
                    'description' => ''
                ];
            @endphp
            
            <optgroup 
                label="{{ $config['icon'] }} {{ $accountType }} ({{ $packages->count() }} gói)"
                data-account-type="{{ $accountType }}"
                style="background-color: {{ $config['bg_color'] }}; color: {{ $config['color'] }}; font-weight: bold;">
                
                @foreach($packages as $package)
                    <option 
                        value="{{ $package->id }}" 
                        data-price="{{ $package->price }}"
                        data-duration="{{ $package->default_duration_days }}"
                        data-account-type="{{ $package->account_type }}"
                        data-category="{{ $package->category->name ?? '' }}"
                        style="padding-left: 20px; color: #333;"
                        {{ $selected == $package->id ? 'selected' : '' }}>
                        
                        {{ $package->name }}
                        @if($package->category)
                            <small>({{ $package->category->name }})</small>
                        @endif
                        - {{ number_format($package->price) }}đ
                        @if($package->default_duration_days)
                            / {{ $package->default_duration_days }} ngày
                        @endif
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    </select>
    
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    
    {{-- Account Type Legend --}}
    <div class="account-type-legend mt-2">
        <small class="text-muted d-block mb-1">
            <i class="fas fa-info-circle me-1"></i>
            Loại tài khoản:
        </small>
        <div class="row g-1">
            @foreach($accountTypeConfig as $accountType => $config)
                @if($sortedGroups->has($accountType))
                    <div class="col-6 col-md-3">
                        <span class="badge rounded-pill d-inline-flex align-items-center" 
                              style="background-color: {{ $config['bg_color'] }}; color: {{ $config['color'] }}; border: 1px solid {{ $config['color'] }}20;">
                            <span class="me-1">{{ $config['icon'] }}</span>
                            <small>{{ Str::limit($accountType, 15) }}</small>
                        </span>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

<style>
.service-package-selector {
    position: relative;
}

.service-package-selector select {
    font-size: 14px;
}

.service-package-selector optgroup {
    font-weight: bold;
    font-size: 13px;
    padding: 8px 12px;
    margin: 2px 0;
    border-radius: 4px;
}

.service-package-selector optgroup[data-account-type="Tài khoản dùng chung"] {
    background: linear-gradient(135deg, #fdf2f2 0%, #fce4e4 100%) !important;
    border: 2px solid #e74c3c20;
    font-weight: 900;
}

.service-package-selector optgroup[data-account-type="Tài khoản dùng chung"] option {
    background-color: #fff5f5 !important;
    border-left: 3px solid #e74c3c;
    font-weight: 600;
}

.service-package-selector option {
    padding: 8px 12px;
    font-size: 13px;
    line-height: 1.4;
}

.service-package-selector option:hover {
    background-color: #f8f9fa !important;
}

.account-type-legend .badge {
    font-size: 10px;
    padding: 4px 8px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .account-type-legend .col-6 {
        margin-bottom: 4px;
    }
    
    .service-package-selector select {
        font-size: 13px;
    }
    
    .service-package-selector optgroup {
        font-size: 12px;
    }
    
    .service-package-selector option {
        font-size: 12px;
        padding: 6px 10px;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .service-package-selector optgroup {
        color: #fff !important;
    }
    
    .service-package-selector option {
        background-color: #2d3748 !important;
        color: #e2e8f0 !important;
    }
    
    .account-type-legend .badge {
        background-color: #4a5568 !important;
        color: #e2e8f0 !important;
        border-color: #718096 !important;
    }
}

/* Focus states for accessibility */
.service-package-selector select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Loading state */
.service-package-selector.loading select {
    background-image: url("data:image/svg+xml,%3csvg width='16' height='16' viewBox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M8 1V3M8 13V15M3 8H1M15 8H13M4.22 4.22L2.81 2.81M13.19 2.81L11.78 4.22M4.22 11.78L2.81 13.19M13.19 13.19L11.78 11.78' stroke='%236c757d' stroke-width='2' stroke-linecap='round'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 12px center;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Enhanced select functionality
    const selector = document.getElementById('{{ $id }}');
    if (selector) {
        // Add search functionality (optional enhancement)
        selector.addEventListener('keydown', function(e) {
            if (e.key.length === 1) {
                const searchTerm = e.key.toLowerCase();
                const options = Array.from(this.options);
                const matchingOption = options.find(option => 
                    option.text.toLowerCase().includes(searchTerm) && option.value
                );
                
                if (matchingOption) {
                    this.value = matchingOption.value;
                    this.dispatchEvent(new Event('change'));
                }
            }
        });
        
        // Highlight shared accounts
        selector.addEventListener('focus', function() {
            const sharedAccountOptions = this.querySelectorAll('optgroup[data-account-type="Tài khoản dùng chung"] option');
            sharedAccountOptions.forEach(option => {
                option.style.animation = 'pulse 2s infinite';
            });
        });
        
        selector.addEventListener('blur', function() {
            const sharedAccountOptions = this.querySelectorAll('optgroup[data-account-type="Tài khoản dùng chung"] option');
            sharedAccountOptions.forEach(option => {
                option.style.animation = '';
            });
        });
    }
});
</script>
