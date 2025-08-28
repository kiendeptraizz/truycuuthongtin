{{--
    Service Package Grid Selector Component
    
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
            'border_color' => '#e74c3c',
            'description' => 'Nhiều người cùng sử dụng'
        ],
        'Tài khoản chính chủ' => [
            'icon' => '👤',
            'color' => '#3498db',
            'bg_color' => '#f8f9fa',
            'border_color' => '#3498db',
            'description' => 'Quyền sở hữu hoàn toàn'
        ],
        'Tài khoản add family' => [
            'icon' => '👨‍👩‍👧‍👦',
            'color' => '#f39c12',
            'bg_color' => '#fef9e7',
            'border_color' => '#f39c12',
            'description' => 'Thêm vào gói gia đình'
        ],
        'Tài khoản cấp (dùng riêng)' => [
            'icon' => '🔐',
            'color' => '#9b59b6',
            'bg_color' => '#f8f4fd',
            'border_color' => '#9b59b6',
            'description' => 'Tài khoản phụ riêng biệt'
        ]
    ];

    // Define category icons and colors
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
        ]
    ];

    // Group packages by category first, then by account type
    $groupedPackages = $servicePackages->groupBy('category.name');
    
    // Sort categories
    $sortedCategories = $groupedPackages->sortKeys();
@endphp

<div class="service-package-grid-selector" id="{{ $id }}_container">
    <!-- Hidden input to store selected value -->
    <input type="hidden" 
           name="{{ $name }}" 
           id="{{ $id }}" 
           value="{{ $selected }}"
           {{ $required ? 'required' : '' }}>
    
    <!-- Search and Filter Controls -->
    <div class="selector-controls mb-4">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" 
                           class="form-control search-input" 
                           placeholder="Tìm kiếm gói dịch vụ..."
                           id="{{ $id }}_search">
                </div>
            </div>
            <div class="col-md-6">
                <div class="filter-buttons">
                    <button type="button" class="btn btn-outline-primary btn-sm filter-btn active" data-filter="all">
                        <i class="fas fa-th-large me-1"></i>Tất cả
                    </button>
                    @foreach($accountTypeConfig as $accountType => $config)
                        <button type="button" 
                                class="btn btn-outline-secondary btn-sm filter-btn" 
                                data-filter="{{ $accountType }}"
                                style="border-color: {{ $config['color'] }}; color: {{ $config['color'] }};">
                            {{ $config['icon'] }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Selected Package Display -->
    <div class="selected-package-display mb-3" id="{{ $id }}_selected_display" style="display: none;">
        <div class="alert alert-success d-flex align-items-center">
            <i class="fas fa-check-circle me-2"></i>
            <div class="selected-info flex-grow-1">
                <strong>Đã chọn:</strong> <span class="selected-name"></span>
                <small class="d-block text-muted selected-details"></small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger clear-selection">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Service Packages Grid -->
    <div class="packages-grid">
        @foreach($sortedCategories as $categoryName => $packages)
            @php
                $catConfig = $categoryConfig[$categoryName] ?? [
                    'icon' => '📦',
                    'color' => '#6c757d',
                    'bg_color' => '#f8f9fa'
                ];
                
                // Group packages in this category by account type
                $categoryPackagesByType = $packages->groupBy('account_type');
                
                // Sort by account type priority
                $sortedCategoryPackages = $categoryPackagesByType->sortBy(function ($packages, $accountType) use ($accountTypePriority) {
                    return $accountTypePriority[$accountType] ?? 999;
                });
            @endphp
            
            <div class="category-section" data-category="{{ $categoryName }}">
                <div class="category-header">
                    <h5 class="category-title">
                        <span class="category-icon">{{ $catConfig['icon'] }}</span>
                        {{ $categoryName }}
                        <span class="badge bg-secondary ms-2">{{ $packages->count() }} gói</span>
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-secondary toggle-category">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                
                <div class="category-content">
                    @foreach($sortedCategoryPackages as $accountType => $typePackages)
                        @php
                            $typeConfig = $accountTypeConfig[$accountType] ?? [
                                'icon' => '📦',
                                'color' => '#6c757d',
                                'bg_color' => '#f8f9fa',
                                'border_color' => '#6c757d',
                                'description' => ''
                            ];
                        @endphp
                        
                        <div class="account-type-group" data-account-type="{{ $accountType }}">
                            <div class="account-type-header">
                                <span class="account-type-badge" 
                                      style="background-color: {{ $typeConfig['bg_color'] }}; 
                                             color: {{ $typeConfig['color'] }}; 
                                             border: 1px solid {{ $typeConfig['border_color'] }};">
                                    {{ $typeConfig['icon'] }} {{ $accountType }}
                                </span>
                                <small class="text-muted ms-2">{{ $typeConfig['description'] }}</small>
                            </div>
                            
                            <div class="packages-row">
                                @foreach($typePackages as $package)
                                    <div class="package-card" 
                                         data-package-id="{{ $package->id }}"
                                         data-category="{{ $categoryName }}"
                                         data-account-type="{{ $accountType }}"
                                         data-search-text="{{ strtolower($package->name . ' ' . $categoryName . ' ' . $accountType) }}">
                                        
                                        <div class="package-card-inner">
                                            <div class="package-header">
                                                <div class="package-name">{{ $package->name }}</div>
                                                <div class="package-type-indicator" 
                                                     style="background-color: {{ $typeConfig['color'] }};">
                                                    {{ $typeConfig['icon'] }}
                                                </div>
                                            </div>
                                            
                                            <div class="package-details">
                                                <div class="package-price">
                                                    <strong>{{ formatPrice($package->price) }}</strong>
                                                    @if($package->default_duration_days)
                                                        <small class="text-muted">/ {{ $package->default_duration_days }} ngày</small>
                                                    @endif
                                                </div>
                                                
                                                @if($package->description)
                                                    <div class="package-description">
                                                        {{ Str::limit($package->description, 80) }}
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="package-footer">
                                                <small class="text-muted">
                                                    <i class="fas fa-tag me-1"></i>{{ $categoryName }}
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="package-selected-overlay">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <!-- No Results Message -->
    <div class="no-results" style="display: none;">
        <div class="text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Không tìm thấy gói dịch vụ phù hợp</h5>
            <p class="text-muted">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc</p>
        </div>
    </div>

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<style>
/* Service Package Grid Selector Styles */
.service-package-grid-selector {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Search and Filter Controls */
.selector-controls {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.search-box {
    position: relative;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    z-index: 2;
}

.search-input {
    padding-left: 40px;
    border-radius: 6px;
    border: 1px solid #ced4da;
    transition: all 0.2s ease;
}

.search-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.filter-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.filter-btn {
    border-radius: 20px;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.filter-btn.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.filter-btn:hover:not(.active) {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Selected Package Display */
.selected-package-display .alert {
    border-radius: 8px;
    border: none;
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.clear-selection {
    border-radius: 50%;
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Category Sections */
.category-section {
    margin-bottom: 2rem;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    overflow: hidden;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.category-section:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.category-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.category-header:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
}

.category-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
    display: flex;
    align-items: center;
}

.category-icon {
    font-size: 1.2rem;
    margin-right: 0.5rem;
}

.toggle-category {
    border: none;
    background: transparent;
    color: #6c757d;
    transition: transform 0.2s ease;
}

.category-section.collapsed .toggle-category {
    transform: rotate(-90deg);
}

.category-content {
    padding: 1.5rem;
    transition: all 0.3s ease;
}

.category-section.collapsed .category-content {
    display: none;
}

/* Account Type Groups */
.account-type-group {
    margin-bottom: 1.5rem;
}

.account-type-group:last-child {
    margin-bottom: 0;
}

.account-type-header {
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.account-type-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

/* Packages Grid */
.packages-row {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1rem;
}

@media (max-width: 768px) {
    .packages-row {
        grid-template-columns: 1fr;
    }
}

/* Package Cards */
.package-card {
    position: relative;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    overflow: hidden;
}

.package-card:hover {
    border-color: #0d6efd;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(13, 110, 253, 0.15);
}

.package-card.selected {
    border-color: #198754;
    background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%);
    box-shadow: 0 4px 12px rgba(25, 135, 84, 0.2);
}

.package-card-inner {
    padding: 1.25rem;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.package-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.package-name {
    font-weight: 600;
    font-size: 1rem;
    color: #212529;
    line-height: 1.3;
    flex: 1;
    margin-right: 0.5rem;
}

.package-type-indicator {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.package-details {
    flex: 1;
    margin-bottom: 1rem;
}

.package-price {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.package-price strong {
    color: #198754;
}

.package-description {
    font-size: 0.875rem;
    color: #6c757d;
    line-height: 1.4;
}

.package-footer {
    border-top: 1px solid #e9ecef;
    padding-top: 0.75rem;
    margin-top: auto;
}

/* Selected Overlay */
.package-selected-overlay {
    position: absolute;
    top: 0;
    right: 0;
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #198754 0%, #20c997 100%);
    border-radius: 0 12px 0 40px;
    display: none;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.package-card.selected .package-selected-overlay {
    display: flex;
}

/* No Results */
.no-results {
    text-align: center;
    padding: 3rem 1rem;
}

/* Responsive Design */
@media (max-width: 576px) {
    .selector-controls .row {
        flex-direction: column;
    }

    .filter-buttons {
        justify-content: center;
        margin-top: 1rem;
    }

    .category-header {
        padding: 0.75rem 1rem;
    }

    .category-content {
        padding: 1rem;
    }

    .package-card-inner {
        padding: 1rem;
    }
}

/* Animation for category collapse */
@keyframes slideDown {
    from {
        opacity: 0;
        max-height: 0;
    }
    to {
        opacity: 1;
        max-height: 1000px;
    }
}

@keyframes slideUp {
    from {
        opacity: 1;
        max-height: 1000px;
    }
    to {
        opacity: 0;
        max-height: 0;
    }
}

.category-content {
    animation: slideDown 0.3s ease;
}

.category-section.collapsed .category-content {
    animation: slideUp 0.3s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('{{ $id }}_container');
    if (!container) return;

    const hiddenInput = document.getElementById('{{ $id }}');
    const searchInput = document.getElementById('{{ $id }}_search');
    const selectedDisplay = document.getElementById('{{ $id }}_selected_display');
    const filterButtons = container.querySelectorAll('.filter-btn');
    const packageCards = container.querySelectorAll('.package-card');
    const categoryHeaders = container.querySelectorAll('.category-header');
    const noResults = container.querySelector('.no-results');
    const clearSelectionBtn = container.querySelector('.clear-selection');

    let selectedPackageId = '{{ $selected }}';
    let currentFilter = 'all';
    let currentSearch = '';

    // Initialize
    init();

    function init() {
        // Set initial selection if any
        if (selectedPackageId) {
            selectPackage(selectedPackageId);
        }

        // Add event listeners
        addEventListeners();

        // Initial filter
        applyFilters();
    }

    function addEventListeners() {
        // Package card clicks
        packageCards.forEach(card => {
            card.addEventListener('click', function() {
                const packageId = this.dataset.packageId;
                selectPackage(packageId);
            });
        });

        // Search input
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                currentSearch = this.value.toLowerCase().trim();
                applyFilters();
            });
        }

        // Filter buttons
        filterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(b => b.classList.remove('active'));

                // Add active class to clicked button
                this.classList.add('active');

                // Update current filter
                currentFilter = this.dataset.filter;

                // Apply filters
                applyFilters();
            });
        });

        // Category toggle buttons
        categoryHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const categorySection = this.closest('.category-section');
                categorySection.classList.toggle('collapsed');
            });
        });

        // Clear selection button
        if (clearSelectionBtn) {
            clearSelectionBtn.addEventListener('click', function() {
                clearSelection();
            });
        }
    }

    function selectPackage(packageId) {
        // Clear previous selection
        packageCards.forEach(card => {
            card.classList.remove('selected');
        });

        // Find and select the new package
        const selectedCard = container.querySelector(`[data-package-id="${packageId}"]`);
        if (selectedCard) {
            selectedCard.classList.add('selected');

            // Update hidden input
            hiddenInput.value = packageId;
            selectedPackageId = packageId;

            // Update selected display
            updateSelectedDisplay(selectedCard);

            // Scroll to selected card if not visible
            selectedCard.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });

            // Trigger change event for form validation
            hiddenInput.dispatchEvent(new Event('change'));
        }
    }

    function updateSelectedDisplay(selectedCard) {
        if (!selectedDisplay) return;

        const packageName = selectedCard.querySelector('.package-name').textContent;
        const packagePrice = selectedCard.querySelector('.package-price strong').textContent;
        const packageCategory = selectedCard.dataset.category;
        const packageAccountType = selectedCard.dataset.accountType;

        const selectedNameEl = selectedDisplay.querySelector('.selected-name');
        const selectedDetailsEl = selectedDisplay.querySelector('.selected-details');

        if (selectedNameEl) {
            selectedNameEl.textContent = packageName;
        }

        if (selectedDetailsEl) {
            selectedDetailsEl.textContent = `${packageCategory} - ${packageAccountType} - ${packagePrice}`;
        }

        selectedDisplay.style.display = 'block';
    }

    function clearSelection() {
        // Clear all selections
        packageCards.forEach(card => {
            card.classList.remove('selected');
        });

        // Clear hidden input
        hiddenInput.value = '';
        selectedPackageId = '';

        // Hide selected display
        if (selectedDisplay) {
            selectedDisplay.style.display = 'none';
        }

        // Trigger change event
        hiddenInput.dispatchEvent(new Event('change'));
    }

    function applyFilters() {
        let visibleCount = 0;
        let visibleCategories = new Set();

        packageCards.forEach(card => {
            const matchesSearch = !currentSearch ||
                card.dataset.searchText.includes(currentSearch);

            const matchesFilter = currentFilter === 'all' ||
                card.dataset.accountType === currentFilter;

            const isVisible = matchesSearch && matchesFilter;

            // Show/hide card
            card.style.display = isVisible ? 'block' : 'none';

            if (isVisible) {
                visibleCount++;
                visibleCategories.add(card.dataset.category);
            }
        });

        // Show/hide categories based on visible packages
        const categorySection = container.querySelectorAll('.category-section');
        categorySection.forEach(section => {
            const categoryName = section.dataset.category;
            const hasVisiblePackages = visibleCategories.has(categoryName);

            section.style.display = hasVisiblePackages ? 'block' : 'none';
        });

        // Show/hide account type groups
        const accountTypeGroups = container.querySelectorAll('.account-type-group');
        accountTypeGroups.forEach(group => {
            const hasVisibleCards = Array.from(group.querySelectorAll('.package-card'))
                .some(card => card.style.display !== 'none');

            group.style.display = hasVisibleCards ? 'block' : 'none';
        });

        // Show/hide no results message
        if (noResults) {
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        // Update filter button badges with counts
        updateFilterCounts();
    }

    function updateFilterCounts() {
        filterButtons.forEach(btn => {
            const filter = btn.dataset.filter;
            let count = 0;

            if (filter === 'all') {
                count = packageCards.length;
            } else {
                packageCards.forEach(card => {
                    if (card.dataset.accountType === filter) {
                        count++;
                    }
                });
            }

            // Update button text with count (if not already there)
            const currentText = btn.textContent.trim();
            const baseText = currentText.split('(')[0].trim();

            if (filter === 'all') {
                btn.innerHTML = `<i class="fas fa-th-large me-1"></i>${baseText}`;
            } else {
                // Keep only the emoji for non-all filters
                const emoji = btn.textContent.trim().match(/[\u{1F600}-\u{1F64F}]|[\u{1F300}-\u{1F5FF}]|[\u{1F680}-\u{1F6FF}]|[\u{1F1E0}-\u{1F1FF}]|[\u{2600}-\u{26FF}]|[\u{2700}-\u{27BF}]/gu);
                btn.textContent = emoji ? emoji[0] : '📦';
            }
        });
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (!container.contains(document.activeElement)) return;

        // Don't interfere with typing in search input
        if (e.target === searchInput) return;

        const visibleCards = Array.from(packageCards).filter(card =>
            card.style.display !== 'none'
        );

        if (visibleCards.length === 0) return;

        const currentIndex = visibleCards.findIndex(card =>
            card.classList.contains('selected')
        );

        let newIndex = currentIndex;

        switch(e.key) {
            case 'ArrowRight':
            case 'ArrowDown':
                e.preventDefault();
                newIndex = (currentIndex + 1) % visibleCards.length;
                break;
            case 'ArrowLeft':
            case 'ArrowUp':
                e.preventDefault();
                newIndex = currentIndex <= 0 ? visibleCards.length - 1 : currentIndex - 1;
                break;
            case 'Enter':
            case ' ':
                e.preventDefault();
                if (currentIndex >= 0) {
                    const packageId = visibleCards[currentIndex].dataset.packageId;
                    selectPackage(packageId);
                }
                return;
            case 'Escape':
                e.preventDefault();
                clearSelection();
                return;
        }

        if (newIndex !== currentIndex && newIndex >= 0) {
            const packageId = visibleCards[newIndex].dataset.packageId;
            selectPackage(packageId);
        }
    });

    // Auto-collapse categories with no visible packages
    function autoCollapseEmptyCategories() {
        const categorySection = container.querySelectorAll('.category-section');
        categorySection.forEach(section => {
            const visiblePackages = section.querySelectorAll('.package-card:not([style*="display: none"])');

            if (visiblePackages.length === 0) {
                section.classList.add('collapsed');
            } else {
                section.classList.remove('collapsed');
            }
        });
    }

    // Debounced search
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentSearch = this.value.toLowerCase().trim();
                applyFilters();
                autoCollapseEmptyCategories();
            }, 300);
        });
    }
});
</script>
