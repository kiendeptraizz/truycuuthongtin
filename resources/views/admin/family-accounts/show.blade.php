@extends('layouts.admin')

@section('title', 'Chi tiết Family Account')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-home me-2"></i>
                        {{ $familyAccount->family_name }}
                    </h1>
                    <p class="text-muted mb-0">
                        <code class="bg-light px-2 py-1 rounded">{{ $familyAccount->family_code }}</code>
                        •
                        <span class="badge bg-{{ $familyAccount->status === 'active' ? 'success' : 'warning' }} ms-2">
                            {{ ucfirst($familyAccount->status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.family-accounts.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>
                        Quay lại danh sách
                    </a>
                    <a href="{{ route('admin.family-accounts.edit', $familyAccount) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-1"></i>
                        Chỉnh sửa
                    </a>
                    <button type="button" class="btn btn-danger" onclick="confirmDeleteFamily()">
                        <i class="fas fa-trash me-1"></i>
                        Xóa Family
                    </button>
                    <form id="delete-family-form"
                        action="{{ route('admin.family-accounts.destroy', $familyAccount) }}"
                        method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Family Info Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Thông tin cơ bản
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Tên Family:</small><br>
                            <strong>{{ $familyAccount->family_name }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Mã Family:</small><br>
                            <code class="bg-light px-2 py-1 rounded">{{ $familyAccount->family_code }}</code>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Email chủ:</small><br>
                            <a href="mailto:{{ $familyAccount->owner_email }}">{{ $familyAccount->owner_email }}</a>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Tên chủ gia đình:</small><br>
                            <strong>{{ $familyAccount->owner_name ?: 'Chưa cập nhật' }}</strong>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Gói dịch vụ:</small><br>
                            <span class="badge bg-info">{{ $familyAccount->servicePackage->name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Trạng thái:</small><br>
                            @php
                            $statusColors = [
                            'active' => 'success',
                            'expired' => 'warning',
                            'suspended' => 'danger',
                            'cancelled' => 'secondary',
                            ];
                            $statusLabels = [
                            'active' => 'Hoạt động',
                            'expired' => 'Hết hạn',
                            'suspended' => 'Tạm ngưng',
                            'cancelled' => 'Đã hủy',
                            ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$familyAccount->status] ?? 'secondary' }}">
                                {{ $statusLabels[$familyAccount->status] ?? $familyAccount->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar me-2"></i>
                        Thông tin thời gian
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Ngày tạo:</small><br>
                            <strong>{{ $familyAccount->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Cập nhật cuối:</small><br>
                            <strong>{{ $familyAccount->updated_at->format('d/m/Y H:i') }}</strong>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Ngày hết hạn:</small><br>
                            @if($familyAccount->expires_at)
                            @php
                            $daysRemaining = $familyAccount->getDaysRemaining();
                            $isExpired = $familyAccount->isExpired();
                            $isExpiringSoon = $familyAccount->isExpiringSoon(7);
                            @endphp
                            <strong class="{{ $isExpired ? 'text-danger' : ($isExpiringSoon ? 'text-warning' : 'text-success') }}">
                                {{ $familyAccount->expires_at->format('d/m/Y') }}
                            </strong>
                            <br>
                            <small class="text-muted">
                                @if($isExpired)
                                Đã hết hạn
                                @elseif($daysRemaining == 0)
                                Hết hạn hôm nay
                                @elseif($daysRemaining == 1)
                                Còn 1 ngày
                                @else
                                Còn {{ $daysRemaining }} ngày
                                @endif
                            </small>
                            @else
                            <span class="text-muted">Chưa thiết lập</span>
                            @endif
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Slots đang sử dụng:</small><br>
                            <span class="badge {{ $activeServices->count() >= $familyAccount->max_members ? 'bg-danger' : 'bg-success' }} fs-6">
                                {{ $activeServices->count() }}/{{ $familyAccount->max_members }}
                            </span>
                            <br>
                            <small class="text-success mt-1 d-block">
                                <i class="fas fa-check-circle me-1"></i>
                                Còn lại: <strong>{{ $familyAccount->max_members - $activeServices->count() }} slots</strong>
                            </small>
                            <small class="text-muted mt-1 d-block">
                                <i class="fas fa-info-circle me-1"></i>
                                Mỗi dịch vụ = 1 slot
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Using This Family Section -->
    @if($totalServices > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-box-open me-2"></i>
                        Dịch vụ đang sử dụng Family này (Mỗi dịch vụ = 1 slot)
                        <span class="badge bg-primary">{{ $activeServices->count() }}</span>
                        @if($totalServices > $activeServices->count())
                        <span class="badge bg-secondary ms-2">{{ $totalServices - $activeServices->count() }} không hoạt động</span>
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Search Box -->
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text"
                                        class="form-control"
                                        id="serviceSearch"
                                        placeholder="Tìm kiếm theo tên, mã khách hàng, email, gói dịch vụ...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="statusFilter">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="active">Hoạt động</option>
                                    <option value="expired">Hết hạn</option>
                                    <option value="cancelled">Đã hủy</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-outline-secondary" id="clearFilters">
                                    <i class="fas fa-times me-1"></i>
                                    Xóa bộ lọc
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="servicesListContainer">
                        <div class="row g-2" id="servicesTableBody">
                            @foreach($familyAccount->customerServices->sortByDesc('status') as $service)
                            @php
                            $statusColors = ['active' => 'success', 'expired' => 'warning', 'cancelled' => 'danger'];
                            $statusLabels = ['active' => 'Hoạt động', 'expired' => 'Hết hạn', 'cancelled' => 'Đã hủy'];
                            $borderColor = $service->status === 'active' ? 'success' : ($service->status === 'expired' ? 'warning' : 'secondary');
                            @endphp
                            <div class="col-12">
                                <div class="card border-{{ $borderColor }} {{ $service->status !== 'active' ? 'bg-light' : '' }}">
                                    <div class="card-body p-2">
                                        <div class="d-flex align-items-center gap-3">
                                            <!-- Nút xem -->
                                            <div>
                                                @if($service->customer)
                                                <a href="{{ route('admin.customers.show', $service->customer) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Xem khách hàng">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endif
                                            </div>

                                            <!-- Thông tin khách hàng -->
                                            <div class="flex-grow-1">
                                                <div class="d-flex flex-wrap align-items-center gap-2">
                                                    @if($service->customer)
                                                    <strong>{{ $service->customer->name }}</strong>
                                                    <small class="text-muted">({{ $service->customer->customer_code ?? 'N/A' }})</small>
                                                    @else
                                                    <span class="text-muted">Đã xóa</span>
                                                    @endif
                                                    <span class="badge bg-{{ $statusColors[$service->status] ?? 'secondary' }}">
                                                        {{ $statusLabels[$service->status] ?? ucfirst($service->status) }}
                                                    </span>
                                                </div>
                                                <small class="text-muted">
                                                    📧 {{ $service->login_email ?: ($service->customer->email ?? '-') }}
                                                    @if($service->expires_at)
                                                    &nbsp;|&nbsp; 📅 {{ \Carbon\Carbon::parse($service->expires_at)->format('d/m/Y') }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div id="noResults" class="alert alert-info text-center" style="display: none;">
                        <i class="fas fa-search me-2"></i>
                        Không tìm thấy dịch vụ nào phù hợp với bộ lọc
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('serviceSearch');
        const statusFilter = document.getElementById('statusFilter');
        const clearBtn = document.getElementById('clearFilters');
        const tableBody = document.getElementById('servicesTableBody');
        const noResults = document.getElementById('noResults');

        // Store original rows
        const services = {
            !!json_encode($familyAccount - > customerServices - > map(function($service) {
                return [
                    'id' => $service - > id,
                    'customer_name' => $service - > customer ? $service - > customer - > name : 'Đã xóa',
                    'customer_code' => $service - > customer ? $service - > customer - > customer_code : null,
                    'customer_email' => $service - > customer ? $service - > customer - > email : '',
                    'login_email' => $service - > login_email,
                    'customer_id' => $service - > customer ? $service - > customer - > id : null,
                    'package_name' => $service - > servicePackage ? $service - > servicePackage - > name : 'N/A',
                    'status' => $service - > status,
                    'expires_at' => $service - > expires_at ? $service - > expires_at - > format('d/m/Y') : null,
                ];
            }) - > values()) !!
        };

        const statusColors = {
            'active': 'success',
            'expired': 'warning',
            'cancelled': 'danger'
        };

        const statusLabels = {
            'active': 'Hoạt động',
            'expired': 'Hết hạn',
            'cancelled': 'Đã hủy'
        };

        function renderTable(filteredServices) {
            if (filteredServices.length === 0) {
                tableBody.innerHTML = '';
                noResults.style.display = 'block';
                return;
            }

            noResults.style.display = 'none';

            // Sort: active first, then others
            filteredServices.sort((a, b) => {
                if (a.status === 'active' && b.status !== 'active') return -1;
                if (a.status !== 'active' && b.status === 'active') return 1;
                return 0;
            });

            tableBody.innerHTML = filteredServices.map(service => {
                const statusColor = statusColors[service.status] || 'secondary';
                const statusLabel = statusLabels[service.status] || service.status;
                const borderColor = service.status === 'active' ? 'success' : (service.status === 'expired' ? 'warning' : 'secondary');
                const bgClass = service.status !== 'active' ? 'bg-light' : '';
                const customerLink = service.customer_id ?
                    `<a href="/admin/customers/${service.customer_id}" class="btn btn-sm btn-outline-primary" title="Xem khách hàng">
                    <i class="fas fa-eye"></i>
                </a>` : '';
                const customerCode = service.customer_code ? `(${service.customer_code})` : '(N/A)';
                const displayEmail = service.login_email || service.customer_email || '-';

                return `
                <div class="col-12">
                    <div class="card border-${borderColor} ${bgClass}">
                        <div class="card-body p-2">
                            <div class="d-flex align-items-center gap-3">
                                <div>${customerLink}</div>
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        ${service.customer_name ? `<strong>${service.customer_name}</strong> <small class="text-muted">${customerCode}</small>` : '<span class="text-muted">Đã xóa</span>'}
                                        <span class="badge bg-${statusColor}">${statusLabel}</span>
                                    </div>
                                    <small class="text-muted">
                                        📧 ${displayEmail}
                                        ${service.expires_at ? `&nbsp;|&nbsp; 📅 ${service.expires_at}` : ''}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }).join('');
        }

        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const statusValue = statusFilter.value;

            let filtered = services;

            // Filter by search term
            if (searchTerm) {
                filtered = filtered.filter(service => {
                    return (service.customer_name && service.customer_name.toLowerCase().includes(searchTerm)) ||
                        (service.customer_code && service.customer_code.toLowerCase().includes(searchTerm)) ||
                        (service.customer_email && service.customer_email.toLowerCase().includes(searchTerm)) ||
                        (service.login_email && service.login_email.toLowerCase().includes(searchTerm)) ||
                        (service.package_name && service.package_name.toLowerCase().includes(searchTerm));
                });
            }

            // Filter by status
            if (statusValue) {
                filtered = filtered.filter(service => service.status === statusValue);
            }

            renderTable(filtered);
        }

        // Event listeners
        searchInput.addEventListener('input', applyFilters);
        statusFilter.addEventListener('change', applyFilters);

        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            statusFilter.value = '';
            applyFilters();
        });

        // Initial render
        renderTable(services);
    });

    // Confirm delete family account
    function confirmDeleteFamily() {
        const memberCount = {
            {
                $activeServices - > count()
            }
        };
        let message = 'Bạn có chắc chắn muốn xóa Family Account "{{ addslashes($familyAccount->family_name) }}"?';

        if (memberCount > 0) {
            message += `\n\n⚠️ CẢNH BÁO: Family này đang có ${memberCount} dịch vụ khách hàng đang sử dụng!\nXóa sẽ gỡ bỏ liên kết các dịch vụ này khỏi Family.`;
        }

        if (confirm(message)) {
            document.getElementById('delete-family-form').submit();
        }
    }
</script>
@endpush

@endsection