{{--
    Service Package Selector — REWRITE từ native select sang search dropdown.

    Props (giữ backward compat):
    - $servicePackages: Collection of service packages (with category relation)
    - $accountTypePriority: Array of account type → priority (chỉ dùng để sort)
    - $name: Input name (default: 'service_package_id')
    - $id: Input id (default: 'service_package_id')
    - $required: Boolean (default: true)
    - $selected: Selected value (default: old value)
    - $placeholder: Placeholder text
--}}

@props([
    'servicePackages',
    'accountTypePriority' => [],
    'name' => 'service_package_id',
    'id' => 'service_package_id',
    'required' => true,
    'selected' => null,
    'placeholder' => 'Gõ tên gói / danh mục để tìm...',
])

@php
    $searchId = $id . '_search';
    $dropdownId = $id . '_dropdown';
    $resultsId = $id . '_results';
    $selectedValue = $selected ?? old($name);
    $selectedPackage = $selectedValue ? $servicePackages->firstWhere('id', $selectedValue) : null;

    // Sort by account_type priority → name
    $sortedPackages = $servicePackages->sortBy(function ($p) use ($accountTypePriority) {
        $priority = $accountTypePriority[$p->account_type] ?? 999;
        return [$priority, $p->name];
    });

    $accountTypeIcons = [
        'Tài khoản dùng chung' => '👥',
        'Tài khoản chính chủ' => '👤',
        'Tài khoản add family' => '👨‍👩‍👧‍👦',
        'Tài khoản cấp (dùng riêng)' => '🔐',
    ];
@endphp

<div class="package-search-selector" data-component-id="{{ $id }}">
    {{-- Search input wrapper --}}
    <div class="position-relative">
        <input type="text"
               class="form-control @error($name) is-invalid @enderror"
               id="{{ $searchId }}"
               placeholder="{{ $placeholder }}"
               autocomplete="off"
               value="{{ $selectedPackage ? $selectedPackage->name . ' (' . ($selectedPackage->category?->name ?? 'N/A') . ')' : '' }}">
        <div class="position-absolute top-50 end-0 translate-middle-y me-3" style="pointer-events: none;">
            <i class="fas fa-search text-muted"></i>
        </div>

        {{-- Results panel — block-level (position:static safer) --}}
        <div class="package-results-panel" id="{{ $dropdownId }}" style="display: none;">
            <div class="package-results-header">
                <i class="fas fa-info-circle me-1"></i>
                <strong>{{ $sortedPackages->count() }}</strong> gói — gõ để lọc theo tên / danh mục / loại TK
            </div>
            <div id="{{ $resultsId }}" class="package-results-list">
                @foreach($sortedPackages as $p)
                    @php
                        $catName = $p->category?->name ?? '';
                        $accountType = $p->account_type ?? '';
                        $icon = $accountTypeIcons[$accountType] ?? '📦';
                        // Search key: name + category + account_type, lowercase + bỏ dấu cơ bản
                        $searchKey = strtolower($p->name . ' ' . $catName . ' ' . $accountType);
                    @endphp
                    <a class="package-result-item"
                       href="#"
                       data-id="{{ $p->id }}"
                       data-name="{{ $p->name }}"
                       data-cat="{{ $catName }}"
                       data-search="{{ $searchKey }}">
                        <div class="d-flex align-items-center">
                            <div class="package-icon">{{ $icon }}</div>
                            <div class="flex-grow-1 ms-2">
                                <div class="fw-bold">{{ $p->name }}</div>
                                <small class="text-muted">
                                    @if($catName) {{ $catName }} @endif
                                    @if($accountType) · {{ $accountType }} @endif
                                </small>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Hidden select để form submit --}}
    <select class="d-none @error($name) is-invalid @enderror"
            id="{{ $id }}"
            name="{{ $name }}"
            {{ $required ? 'required' : '' }}>
        <option value="">— Chọn gói —</option>
        @foreach($sortedPackages as $p)
            <option value="{{ $p->id }}"
                    data-price="{{ $p->price ?? 0 }}"
                    data-duration="{{ $p->default_duration_days ?? '' }}"
                    {{ $selectedValue == $p->id ? 'selected' : '' }}>
                {{ $p->name }} ({{ $p->category?->name ?? 'N/A' }})
            </option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

@push('styles')
<style>
.package-search-selector { position: relative; }
.package-search-selector .package-results-panel {
    margin-top: 0.5rem;
    width: 100%;
    max-height: 380px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    background-color: #fff;
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
}
.package-search-selector .package-results-header {
    padding: 0.6rem 1rem;
    color: #495057;
    font-size: 0.85rem;
    border-bottom: 1px solid #e9ecef;
    background: #f8f9fa;
    position: sticky;
    top: 0;
    z-index: 1;
}
.package-search-selector .package-results-empty {
    padding: 1.25rem;
    color: #6c757d;
    text-align: center;
    font-size: 0.9rem;
}
.package-search-selector .package-result-item {
    display: block;
    padding: 0.55rem 1rem;
    border-bottom: 1px solid #f1f3f5;
    cursor: pointer;
    transition: background 0.15s ease;
    color: inherit;
    text-decoration: none;
}
.package-search-selector .package-result-item:last-child { border-bottom: none; }
.package-search-selector .package-result-item:hover,
.package-search-selector .package-result-item.active {
    background-color: #eef2ff;
    color: #1f2937;
    text-decoration: none;
}
.package-search-selector .package-result-item.d-none { display: none !important; }
.package-search-selector .package-icon {
    width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    background: #f8f9fa;
    border-radius: 8px;
    flex-shrink: 0;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initPackageSearchSelector('{{ $id }}');
});

function initPackageSearchSelector(baseId) {
    const searchInput = document.getElementById(baseId + '_search');
    const hiddenSelect = document.getElementById(baseId);
    const dropdown = document.getElementById(baseId + '_dropdown');
    const resultsContainer = document.getElementById(baseId + '_results');

    if (!searchInput || !hiddenSelect || !dropdown || !resultsContainer) return;

    const items = Array.from(resultsContainer.querySelectorAll('.package-result-item'));

    searchInput.addEventListener('focus', function() {
        dropdown.style.display = 'block';
        if (!this.value.trim()) applyFilter('');
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    searchInput.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        applyFilter(q);
        dropdown.style.display = 'block';
        // Nếu user xoá hết → clear hidden select
        if (!q && hiddenSelect.value) {
            // Don't auto-clear — user might just be re-searching
        }
    });

    function applyFilter(q) {
        let visibleCount = 0;
        items.forEach(item => {
            const search = item.dataset.search || '';
            if (q === '' || search.includes(q)) {
                item.classList.remove('d-none');
                visibleCount++;
            } else {
                item.classList.add('d-none');
            }
        });

        let emptyMsg = resultsContainer.querySelector('.package-results-empty');
        if (visibleCount === 0) {
            if (!emptyMsg) {
                emptyMsg = document.createElement('div');
                emptyMsg.className = 'package-results-empty';
                resultsContainer.appendChild(emptyMsg);
            }
            emptyMsg.textContent = 'Không tìm thấy gói khớp "' + q + '"';
        } else if (emptyMsg) {
            emptyMsg.remove();
        }
    }

    resultsContainer.addEventListener('click', function(e) {
        e.preventDefault();
        const item = e.target.closest('.package-result-item');
        if (!item) return;

        const id = item.dataset.id;
        const name = item.dataset.name;
        const cat = item.dataset.cat;

        hiddenSelect.value = id;
        searchInput.value = name + (cat ? ' (' + cat + ')' : '');
        dropdown.style.display = 'none';
        hiddenSelect.dispatchEvent(new Event('change', { bubbles: true }));
    });

    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const firstVisible = items.find(item => !item.classList.contains('d-none'));
            if (firstVisible) firstVisible.click();
        } else if (e.key === 'Escape') {
            dropdown.style.display = 'none';
        }
    });
}
</script>
@endpush
