@props([
    'name' => 'customer_id',
    'id' => 'customer_id',
    'customers' => collect(),
    'placeholder' => 'Tìm kiếm khách hàng theo tên, mã KH hoặc email...',
    'required' => false,
    'value' => null,
    'label' => 'Chọn Khách Hàng',
    'helpText' => null,
    'showIcon' => true,
    'allowClear' => true,
    'autoFillEmail' => false,
    'emailFieldId' => 'member_email',
    // New props for manual customer entry
    'allowManualEntry' => false,
    'manualEntryPlaceholder' => 'Nhập email khách hàng mới...',
    'manualEntryLabel' => 'Email khách hàng mới',
    // New props for date fields
    'showDateFields' => false,
    'startDateName' => 'start_date',
    'endDateName' => 'end_date',
    'startDateLabel' => 'Ngày bắt đầu',
    'endDateLabel' => 'Ngày kết thúc',
    'startDateValue' => null,
    'endDateValue' => null,
    'dateFieldsRequired' => false
])

@php
    $searchId = $id . '_search';
    $dropdownId = $id . '_dropdown';
    $resultsId = $id . '_results';
    $displayId = $id . '_display';
    $manualEntryId = $id . '_manual_entry';
    $modeToggleId = $id . '_mode_toggle';
    $startDateId = $startDateName . '_field';
    $endDateId = $endDateName . '_field';
    $selectedValue = old($name, $value);
    $selectedStartDate = old($startDateName, $startDateValue);
    $selectedEndDate = old($endDateName, $endDateValue);
@endphp

<div class="customer-search-selector" data-component-id="{{ $id }}">
    @if($label)
        <label for="{{ $searchId }}" class="form-label">
            @if($showIcon)
                <i class="fas fa-user me-1"></i>
            @endif
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    @if($allowManualEntry)
        <!-- Mode Toggle -->
        <div class="mb-2">
            <div class="btn-group btn-group-sm w-100" role="group" id="{{ $modeToggleId }}">
                <input type="radio" class="btn-check" name="{{ $id }}_mode" id="{{ $id }}_mode_existing" value="existing" checked>
                <label class="btn btn-outline-primary" for="{{ $id }}_mode_existing">
                    <i class="fas fa-search me-1"></i>
                    Chọn khách hàng có sẵn
                </label>

                <input type="radio" class="btn-check" name="{{ $id }}_mode" id="{{ $id }}_mode_manual" value="manual">
                <label class="btn btn-outline-success" for="{{ $id }}_mode_manual">
                    <i class="fas fa-user-plus me-1"></i>
                    Thêm nhanh
                </label>
            </div>
        </div>
    @endif

    <!-- Existing Customer Selection Mode -->
    <div class="existing-customer-mode" id="{{ $id }}_existing_mode">
        <!-- Search Input + Dropdown Wrapper (position-relative để dropdown absolute anchor đúng) -->
        <div class="position-relative mb-2">
            <input type="text"
                   class="form-control @error($name) is-invalid @enderror"
                   id="{{ $searchId }}"
                   placeholder="{{ $placeholder }}"
                   autocomplete="off">
            <div class="position-absolute top-50 end-0 translate-middle-y me-3" style="pointer-events: none;">
                <i class="fas fa-search text-muted"></i>
            </div>

        </div>

        {{-- Results panel custom (KHÔNG dùng class dropdown-menu để tránh Bootstrap JS auto-dispose) --}}
        <div class="customer-results-panel"
             id="{{ $dropdownId }}"
             style="display: none;">
            <div class="customer-results-header">
                <i class="fas fa-info-circle me-1"></i>
                Nhập để tìm kiếm hoặc chọn từ danh sách bên dưới
            </div>
            {{-- AJAX search results — render bởi JS từ /admin/customers-search-api --}}
            <div id="{{ $resultsId }}" class="customer-results-list">
                <div class="customer-results-empty">
                    <i class="fas fa-spinner fa-spin me-1"></i> Đang tải khách hàng...
                </div>
            </div>
        </div>
    </div>

    @if($allowManualEntry)
        <!-- Manual Entry Mode -->
        <div class="manual-entry-mode d-none" id="{{ $id }}_manual_mode">
            <div class="position-relative mb-2">
                <input type="email"
                       class="form-control @error($name . '_manual_email') is-invalid @enderror"
                       id="{{ $manualEntryId }}"
                       name="{{ $name }}_manual_email"
                       placeholder="{{ $manualEntryPlaceholder }}"
                       autocomplete="off">
                <div class="position-absolute top-50 end-0 translate-middle-y me-3">
                    <i class="fas fa-envelope text-muted"></i>
                </div>
            </div>

            <!-- Manual Entry Display -->
            <div class="mt-2 d-none" id="{{ $id }}_manual_display">
                <div class="alert alert-info d-flex align-items-center">
                    <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="flex-grow-1">
                        <strong>Khách hàng mới</strong><br>
                        <small class="text-muted">
                            Email: <span id="{{ $id }}_manual_email_display"></span>
                        </small>
                    </div>
                    @if($allowClear)
                        <button type="button" class="btn btn-sm btn-outline-secondary clear-manual-entry">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>
            </div>

            @error($name . '_manual_email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    @endif
    
    <!-- Hidden Select for Form Submission -->
    <select class="form-select d-none @error($name) is-invalid @enderror" 
            id="{{ $id }}" 
            name="{{ $name }}" 
            {{ $required ? 'required' : '' }}>
        <option value="">Chọn khách hàng...</option>
        @foreach($customers as $customer)
            <option value="{{ $customer->id }}" 
                    data-name="{{ $customer->name }}"
                    data-email="{{ $customer->email ?? '' }}"
                    data-code="{{ $customer->customer_code }}"
                    data-search="{{ strtolower($customer->name . ' ' . $customer->customer_code . ' ' . ($customer->email ?? '')) }}"
                    {{ $selectedValue == $customer->id ? 'selected' : '' }}>
                {{ $customer->name }} ({{ $customer->customer_code }})
                @if($customer->email)
                    - {{ $customer->email }}
                @endif
            </option>
        @endforeach
    </select>
    
    {{-- Old dropdown (sibling) đã được move vào trong existing-customer-mode wrapper
         để position:absolute anchor đúng dưới input. --}}

    <!-- Selected Customer Display -->
    <div class="mt-2 d-none" id="{{ $displayId }}">
        <div class="alert alert-success d-flex align-items-center">
            <div class="avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                <i class="fas fa-check"></i>
            </div>
            <div class="flex-grow-1">
                <strong class="selected-customer-name"></strong><br>
                <small class="text-muted">
                    <span class="selected-customer-code"></span>
                    <span class="selected-customer-email"></span>
                </small>
            </div>
            @if($allowClear)
                <button type="button" class="btn btn-sm btn-outline-secondary clear-selection">
                    <i class="fas fa-times"></i>
                </button>
            @endif
        </div>
    </div>
    
    @if($showDateFields)
        <!-- Date Fields -->
        <div class="row mt-3" id="{{ $id }}_date_fields">
            <div class="col-md-6 mb-3">
                <label for="{{ $startDateId }}" class="form-label">
                    <i class="fas fa-calendar-alt me-1"></i>
                    {{ $startDateLabel }}
                    @if($dateFieldsRequired)
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <input type="date"
                       class="form-control @error($startDateName) is-invalid @enderror"
                       id="{{ $startDateId }}"
                       name="{{ $startDateName }}"
                       value="{{ $selectedStartDate }}"
                       {{ $dateFieldsRequired ? 'required' : '' }}>
                @error($startDateName)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="{{ $endDateId }}" class="form-label">
                    <i class="fas fa-calendar-check me-1"></i>
                    {{ $endDateLabel }}
                    @if($dateFieldsRequired)
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <input type="date"
                       class="form-control @error($endDateName) is-invalid @enderror"
                       id="{{ $endDateId }}"
                       name="{{ $endDateName }}"
                       value="{{ $selectedEndDate }}"
                       {{ $dateFieldsRequired ? 'required' : '' }}>
                @error($endDateName)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    Ngày kết thúc phải sau ngày bắt đầu
                </small>
            </div>
        </div>
    @endif

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror

    @if($helpText)
        <small class="form-text text-muted">
            <i class="fas fa-info-circle me-1"></i>
            {{ $helpText }}
        </small>
    @endif
</div>

@push('styles')
<style>
/* Customer Search Selector — custom classes (KHÔNG dùng Bootstrap dropdown-*
   để tránh Bootstrap JS auto-init/dispose can thiệp). */

/* Results panel — block-level, đẩy form xuống dưới khi mở */
.customer-search-selector .customer-results-panel {
    margin-top: 0.5rem;
    width: 100%;
    max-height: 350px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    background-color: #fff;
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
}

.customer-search-selector .customer-results-header {
    padding: 0.6rem 1rem;
    color: #6c757d;
    font-size: 0.85rem;
    border-bottom: 1px solid #e9ecef;
    background: #f8f9fa;
}

.customer-search-selector .customer-results-empty {
    padding: 1.25rem;
    color: #6c757d;
    text-align: center;
    font-size: 0.9rem;
}

.customer-search-selector .customer-result-item {
    display: block;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f1f3f5;
    cursor: pointer;
    transition: background 0.15s ease;
    color: inherit;
    text-decoration: none;
}

.customer-search-selector .customer-result-item:last-child {
    border-bottom: none;
}

.customer-search-selector .customer-result-item:hover,
.customer-search-selector .customer-result-item.active {
    background-color: #eef2ff;
    color: #1f2937;
    text-decoration: none;
}

.customer-search-selector .customer-result-item .avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 16px;
}

.customer-search-selector .form-control {
    transition: all 0.2s ease;
}

.customer-search-selector .form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.customer-search-selector .alert {
    margin-bottom: 0;
    padding: 0.75rem;
}

.customer-search-selector .alert .avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 16px;
}

/* Highlight search matches */
.customer-search-selector mark {
    background-color: #fff3cd;
    color: #856404;
    padding: 0.1em 0.2em;
    border-radius: 0.2em;
}

/* Mode toggle styles */
.customer-search-selector .btn-group .btn-check:checked + .btn {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}

.customer-search-selector .btn-group .btn-outline-success:checked,
.customer-search-selector .btn-group .btn-check:checked + .btn-outline-success {
    background-color: var(--bs-success);
    border-color: var(--bs-success);
    color: white;
}

/* Manual entry styles */
.customer-search-selector .manual-entry-mode .alert-info {
    border-left: 4px solid #0dcaf0;
}

.customer-search-selector .manual-entry-mode .avatar-sm {
    background-color: #0dcaf0 !important;
}

/* Date fields styles */
.customer-search-selector input[type="date"] {
    transition: all 0.2s ease;
}

.customer-search-selector input[type="date"]:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.customer-search-selector input[type="date"].is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .customer-search-selector .customer-results-panel {
        max-height: 280px;
    }
    .customer-search-selector .customer-result-item {
        padding: 0.5rem 0.75rem;
    }
    .customer-search-selector .customer-result-item .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initCustomerSearchSelector('{{ $id }}', {
        autoFillEmail: {{ $autoFillEmail ? 'true' : 'false' }},
        emailFieldId: '{{ $emailFieldId }}',
        allowClear: {{ $allowClear ? 'true' : 'false' }},
        allowManualEntry: {{ $allowManualEntry ? 'true' : 'false' }},
        showDateFields: {{ $showDateFields ? 'true' : 'false' }},
        startDateName: '{{ $startDateName }}',
        endDateName: '{{ $endDateName }}'
    });
});

function initCustomerSearchSelector(baseId, options = {}) {
    const searchId = baseId + '_search';
    const dropdownId = baseId + '_dropdown';
    const resultsId = baseId + '_results';
    const displayId = baseId + '_display';
    const manualEntryId = baseId + '_manual_entry';
    const modeToggleId = baseId + '_mode_toggle';
    const existingModeId = baseId + '_existing_mode';
    const manualModeId = baseId + '_manual_mode';
    const manualDisplayId = baseId + '_manual_display';
    const manualEmailDisplayId = baseId + '_manual_email_display';

    const customerSearch = document.getElementById(searchId);
    const customerSelect = document.getElementById(baseId);
    const customerDropdown = document.getElementById(dropdownId);
    const customerResults = document.getElementById(resultsId);
    const selectedCustomerDisplay = document.getElementById(displayId);
    const clearSelectionBtn = selectedCustomerDisplay?.querySelector('.clear-selection');

    // Manual entry elements
    const manualEntryInput = document.getElementById(manualEntryId);
    const modeToggle = document.getElementById(modeToggleId);
    const existingModeDiv = document.getElementById(existingModeId);
    const manualModeDiv = document.getElementById(manualModeId);
    const manualDisplay = document.getElementById(manualDisplayId);
    const manualEmailDisplay = document.getElementById(manualEmailDisplayId);
    const clearManualBtn = manualDisplay?.querySelector('.clear-manual-entry');

    // Date field elements
    const startDateField = options.showDateFields ? document.getElementById(options.startDateName + '_field') : null;
    const endDateField = options.showDateFields ? document.getElementById(options.endDateName + '_field') : null;

    if (!customerSearch || !customerSelect) return;

    let currentMode = 'existing'; // 'existing' or 'manual'
    
    let allCustomers = [];

    // Initialize customer data from select options
    Array.from(customerSelect.options).forEach(option => {
        if (option.value) {
            allCustomers.push({
                id: option.value,
                name: option.dataset.name,
                code: option.dataset.code,
                email: option.dataset.email || '',
                search: option.dataset.search,
                text: option.textContent
            });
        }
    });

    // Mode toggle functionality
    if (options.allowManualEntry && modeToggle) {
        modeToggle.addEventListener('change', function(e) {
            if (e.target.name === baseId + '_mode') {
                currentMode = e.target.value;
                toggleMode(currentMode);
            }
        });
    }

    // Manual entry input handling
    if (manualEntryInput) {
        manualEntryInput.addEventListener('input', function() {
            const email = this.value.trim();
            if (email && isValidEmail(email)) {
                showManualEntryDisplay(email);
                updateHiddenSelect('manual:' + email);
            } else {
                hideManualEntryDisplay();
                updateHiddenSelect('');
            }
        });
    }

    // Clear manual entry
    if (clearManualBtn) {
        clearManualBtn.addEventListener('click', function() {
            clearManualEntry();
        });
    }
    
    // Show dropdown when search input is focused
    customerSearch.addEventListener('focus', function() {
        showDropdown();
        if (!this.value) {
            showAllCustomers();
        }
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!customerSearch.contains(e.target) && !customerDropdown.contains(e.target)) {
            hideDropdown();
        }
    });
    
    // Search functionality
    customerSearch.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        
        if (query === '') {
            showAllCustomers();
        } else {
            searchCustomers(query);
        }
        
        showDropdown();
    });
    
    // Handle customer selection
    if (customerResults) {
        customerResults.addEventListener('click', function(e) {
            e.preventDefault();
            const customerOption = e.target.closest('.customer-result-item');
            if (customerOption) {
                selectCustomer(customerOption);
            }
        });
    }
    
    // Clear selection
    if (clearSelectionBtn && options.allowClear) {
        clearSelectionBtn.addEventListener('click', function() {
            clearSelection();
        });
    }
    
    // Keyboard navigation
    customerSearch.addEventListener('keydown', function(e) {
        if (!customerResults) return;
        
        const visibleOptions = customerResults.querySelectorAll('.customer-result-item:not(.d-none)');
        const activeOption = customerResults.querySelector('.customer-result-item.active');
        let activeIndex = Array.from(visibleOptions).indexOf(activeOption);
        
        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                if (activeIndex < visibleOptions.length - 1) {
                    setActiveOption(visibleOptions[activeIndex + 1]);
                } else {
                    setActiveOption(visibleOptions[0]);
                }
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                if (activeIndex > 0) {
                    setActiveOption(visibleOptions[activeIndex - 1]);
                } else {
                    setActiveOption(visibleOptions[visibleOptions.length - 1]);
                }
                break;
                
            case 'Enter':
                e.preventDefault();
                if (activeOption) {
                    selectCustomer(activeOption);
                }
                break;
                
            case 'Escape':
                hideDropdown();
                break;
        }
    });
    
    function showDropdown() {
        if (customerDropdown) {
            customerDropdown.style.display = 'block';
            customerDropdown.classList.add('show');
        }
    }
    
    function hideDropdown() {
        if (customerDropdown) {
            customerDropdown.style.display = 'none';
            customerDropdown.classList.remove('show');
        }
    }
    
    // ====== AJAX search ======
    const SEARCH_API_URL = "{{ route('admin.customers.search-api') }}";
    let searchAbortController = null;
    let searchDebounceTimer = null;
    let lastResults = [];

    function fetchCustomers(query) {
        if (searchAbortController) {
            try { searchAbortController.abort(); } catch (e) {}
        }
        searchAbortController = new AbortController();
        if (customerResults) {
            customerResults.innerHTML = '<div class="px-3 py-3 text-muted text-center small"><i class="fas fa-spinner fa-spin me-1"></i> Đang tìm kiếm...</div>';
        }
        const url = SEARCH_API_URL + '?q=' + encodeURIComponent(query);
        fetch(url, { signal: searchAbortController.signal, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.ok ? r.json() : Promise.reject(r.status))
            .then(json => {
                lastResults = json.data || [];
                renderResults(lastResults, query);
            })
            .catch(err => {
                if (err && err.name === 'AbortError') return;
                console.error('Customer search failed:', err);
                if (customerResults) {
                    customerResults.innerHTML = '<div class="px-3 py-3 text-danger text-center small">Lỗi tải khách hàng. Vui lòng thử lại.</div>';
                }
            });
    }

    function renderResults(customers, query) {
        if (!customerResults) {
            console.error('[customer-search] customerResults element null — id:', resultsId);
            return;
        }
        if (!customers || customers.length === 0) {
            customerResults.innerHTML = '<div class="customer-results-empty">Không tìm thấy khách hàng nào' + (query ? ' khớp với "' + escapeHtml(query) + '"' : '') + '</div>';
            return;
        }
        customerResults.innerHTML = customers.map(c => {
            const name = escapeHtml(c.name || '');
            const code = escapeHtml(c.customer_code || '');
            const email = escapeHtml(c.email || '');
            const phone = escapeHtml(c.phone || '');
            const meta = [code, email, phone].filter(Boolean).join(' • ');
            return '<a class="customer-result-item" href="#" '
                + 'data-customer-id="' + c.id + '" '
                + 'data-customer-name="' + name + '" '
                + 'data-customer-code="' + code + '" '
                + 'data-customer-email="' + email + '">'
                + '<div class="d-flex align-items-center">'
                + '<div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"><i class="fas fa-user"></i></div>'
                + '<div><div class="fw-bold">' + name + '</div>'
                + '<small class="text-muted">' + meta + '</small></div></div></a>';
        }).join('');
    }

    function escapeHtml(s) {
        if (s === null || s === undefined) return '';
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function showAllCustomers() {
        clearTimeout(searchDebounceTimer);
        fetchCustomers('');
    }

    function searchCustomers(query) {
        clearTimeout(searchDebounceTimer);
        // Debounce 300ms — tránh spam request mỗi keystroke
        searchDebounceTimer = setTimeout(() => fetchCustomers(query), 300);
    }

    function _legacyShowAllClientSide() {
        if (customerResults) {
            customerResults.querySelectorAll('.customer-result-item').forEach(option => {
                option.classList.remove('d-none');
            });
        }
    }
    
    function _legacySearchClientSide(query) {
        if (customerResults) {
            customerResults.querySelectorAll('.customer-result-item').forEach(option => {
                const searchText = option.dataset.search;
                if (searchText.includes(query)) {
                    option.classList.remove('d-none');
                    highlightMatch(option, query);
                } else {
                    option.classList.add('d-none');
                }
            });
        }
    }
    
    function highlightMatch(option, query) {
        const nameElement = option.querySelector('.fw-bold');
        if (nameElement) {
            const originalText = nameElement.textContent;
            const regex = new RegExp(`(${query})`, 'gi');
            nameElement.innerHTML = originalText.replace(regex, '<mark>$1</mark>');
        }
    }
    
    function setActiveOption(option) {
        if (customerResults) {
            customerResults.querySelectorAll('.customer-result-item').forEach(opt => {
                opt.classList.remove('active', 'bg-light');
            });
            option.classList.add('active', 'bg-light');
            option.scrollIntoView({ block: 'nearest' });
        }
    }
    
    function selectCustomer(customerOption) {
        const customerId = customerOption.dataset.customerId;
        const customerName = customerOption.dataset.customerName;
        const customerCode = customerOption.dataset.customerCode;
        const customerEmail = customerOption.dataset.customerEmail;
        
        // Update hidden select
        customerSelect.value = customerId;
        
        // Update search input
        customerSearch.value = `${customerName} (${customerCode})`;
        
        // Show selected customer display
        if (selectedCustomerDisplay) {
            const nameElement = selectedCustomerDisplay.querySelector('.selected-customer-name');
            const codeElement = selectedCustomerDisplay.querySelector('.selected-customer-code');
            const emailElement = selectedCustomerDisplay.querySelector('.selected-customer-email');
            
            if (nameElement) nameElement.textContent = customerName;
            if (codeElement) codeElement.textContent = customerCode;
            if (emailElement) emailElement.textContent = customerEmail ? ` • ${customerEmail}` : '';
            
            selectedCustomerDisplay.classList.remove('d-none');
        }
        
        // Auto-fill member email if enabled
        if (options.autoFillEmail && options.emailFieldId && customerEmail) {
            const emailInput = document.getElementById(options.emailFieldId);
            if (emailInput && !emailInput.value) {
                emailInput.value = customerEmail;
            }
        }
        
        hideDropdown();
        
        // Trigger change event for validation
        customerSelect.dispatchEvent(new Event('change'));
    }
    
    // Helper functions for new features
    function toggleMode(mode) {
        if (mode === 'manual') {
            existingModeDiv?.classList.add('d-none');
            manualModeDiv?.classList.remove('d-none');
            hideDropdown();
            if (selectedCustomerDisplay) {
                selectedCustomerDisplay.classList.add('d-none');
            }
            manualEntryInput?.focus();
        } else {
            existingModeDiv?.classList.remove('d-none');
            manualModeDiv?.classList.add('d-none');
            hideManualEntryDisplay();
            customerSearch?.focus();
        }
        updateHiddenSelect('');
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function showManualEntryDisplay(email) {
        if (manualDisplay && manualEmailDisplay) {
            manualEmailDisplay.textContent = email;
            manualDisplay.classList.remove('d-none');
        }
    }

    function hideManualEntryDisplay() {
        if (manualDisplay) {
            manualDisplay.classList.add('d-none');
        }
    }

    function clearManualEntry() {
        if (manualEntryInput) {
            manualEntryInput.value = '';
        }
        hideManualEntryDisplay();
        updateHiddenSelect('');
        manualEntryInput?.focus();
    }

    function updateHiddenSelect(value) {
        customerSelect.value = value;
        customerSelect.dispatchEvent(new Event('change'));
    }

    function clearSelection() {
        customerSelect.value = '';
        customerSearch.value = '';
        if (selectedCustomerDisplay) {
            selectedCustomerDisplay.classList.add('d-none');
        }

        // Clear email if auto-fill is enabled
        if (options.autoFillEmail && options.emailFieldId) {
            const emailInput = document.getElementById(options.emailFieldId);
            if (emailInput) {
                emailInput.value = '';
            }
        }

        customerSearch.focus();
        showAllCustomers();
    }
    
    // Date validation
    if (options.showDateFields && startDateField && endDateField) {
        function validateDates() {
            const startDate = new Date(startDateField.value);
            const endDate = new Date(endDateField.value);

            if (startDateField.value && endDateField.value) {
                if (startDate >= endDate) {
                    endDateField.setCustomValidity('Ngày kết thúc phải sau ngày bắt đầu');
                    endDateField.classList.add('is-invalid');
                } else {
                    endDateField.setCustomValidity('');
                    endDateField.classList.remove('is-invalid');
                }
            }
        }

        startDateField.addEventListener('change', validateDates);
        endDateField.addEventListener('change', validateDates);

        // Set minimum date for end date when start date changes
        startDateField.addEventListener('change', function() {
            if (this.value) {
                const nextDay = new Date(this.value);
                nextDay.setDate(nextDay.getDate() + 1);
                endDateField.min = nextDay.toISOString().split('T')[0];
            }
        });
    }

    // Initialize with selected value if exists
    if (customerSelect.value) {
        const selectedOption = customerSelect.options[customerSelect.selectedIndex];
        if (selectedOption && selectedCustomerDisplay) {
            customerSearch.value = selectedOption.textContent;

            const nameElement = selectedCustomerDisplay.querySelector('.selected-customer-name');
            const codeElement = selectedCustomerDisplay.querySelector('.selected-customer-code');
            const emailElement = selectedCustomerDisplay.querySelector('.selected-customer-email');

            if (nameElement) nameElement.textContent = selectedOption.dataset.name;
            if (codeElement) codeElement.textContent = selectedOption.dataset.code;
            if (emailElement) emailElement.textContent = selectedOption.dataset.email ? ` • ${selectedOption.dataset.email}` : '';

            selectedCustomerDisplay.classList.remove('d-none');
        }
    }
}
</script>
@endpush
