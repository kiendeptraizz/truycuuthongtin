@extends('layouts.admin')

@section('title', 'Quản lý dịch vụ khách hàng')
@section('page-title', 'Quản lý dịch vụ khách hàng')

@section('styles')
<style>
    /* Searchable Dropdown Portal */
    .service-dropdown-portal {
        display: none;
        position: fixed;
        z-index: 999999;
        background: #fff;
        border: 1px solid rgba(102, 126, 234, 0.3);
        border-radius: 12px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        max-height: 400px;
        overflow-y: auto;
        padding: 10px 0;
    }

    .service-dropdown-portal .dropdown-item {
        padding: 12px 20px;
        cursor: pointer;
        display: block;
        color: #333;
        text-decoration: none;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .service-dropdown-portal .dropdown-item:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        color: #667eea;
        border-left-color: #667eea;
    }

    .service-dropdown-portal .dropdown-item.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-left-color: white;
    }

    .service-dropdown-portal .dropdown-item.highlighted {
        background: rgba(102, 126, 234, 0.1);
    }

    .service-dropdown-portal .dropdown-divider {
        margin: 8px 15px;
        border-color: #eee;
    }

    #servicePackageSearch {
        border-radius: 8px;
        border: 2px solid #e0e0e0;
        transition: all 0.3s ease;
    }

    #servicePackageSearch:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
    }

    /* Sticky column styles */
    .sticky-column {
        position: sticky !important;
        left: 0 !important;
        background: white !important;
        z-index: 10 !important;
        border-right: 2px solid #dee2e6 !important;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    }

    .table thead .sticky-column {
        background: #f8f9fa !important;
        font-weight: 600;
    }

    /* Responsive table improvements */
    .table-responsive {
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .table {
        margin-bottom: 0;
        white-space: nowrap;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
        position: relative;
    }

    .table td {
        vertical-align: middle;
        border-color: #e9ecef;
    }

    /* Action buttons optimization */
    .btn-group-vertical .btn-group {
        margin-bottom: 2px;
    }

    .btn-group-vertical .btn-group:last-child {
        margin-bottom: 0;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .sticky-column {
            min-width: 80px !important;
        }

        .btn-group-vertical .btn {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    }

    /* Hover effects */
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .table tbody tr:hover .sticky-column {
        background-color: #f8f9fa !important;
    }

    /* Search highlight */
    .search-highlight {
        background-color: #fff3cd;
        padding: 1px 3px;
        border-radius: 3px;
        font-weight: 500;
    }

    /* Search info styling */
    .search-info {
        font-size: 0.8rem;
        margin-top: 2px;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Quản lý dịch vụ khách hàng</h5>
                        <small class="text-muted">Theo dõi và quản lý các dịch vụ đã gán cho khách hàng</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.shared-accounts.index') }}" class="btn btn-secondary">
                            <i class="fas fa-users me-1"></i>
                            Tài khoản dùng chung
                        </a>
                        <a href="{{ route('admin.customer-services.daily-report') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar me-1"></i>
                            Báo cáo hàng ngày
                        </a>
                        <a href="{{ route('admin.customer-services.reminder-report') }}" class="btn btn-warning">
                            <i class="fas fa-bell me-1"></i>
                            Báo cáo nhắc nhở
                        </a>
                        <a href="{{ route('admin.customer-services.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            Gán dịch vụ mới
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Thông báo cấp bách -->
                @php
                $urgentServices = $customerServices->filter(function($service) {
                return $service->getStatus() === 'expiring' && $service->getDaysRemaining() <= 1;
                    });
                    $criticalServices=$customerServices->filter(function($service) {
                    return $service->getStatus() === 'expiring' && $service->getDaysRemaining() <= 2;
                        });
                        @endphp

                        @if($urgentServices->count() > 0)
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <h6 class="alert-heading mb-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                CẢNH BÁO: {{ $urgentServices->count() }} dịch vụ sẽ hết hạn trong 24h!
                            </h6>
                            <p class="mb-0">
                                Cần liên hệ khách hàng ngay:
                                @foreach($urgentServices->take(3) as $service)
                                <strong>{{ $service->customer->name }}</strong>{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                                @if($urgentServices->count() > 3)
                                và {{ $urgentServices->count() - 3 }} khách hàng khác.
                                @endif
                            </p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @elseif($criticalServices->count() > 0)
                        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                            <h6 class="alert-heading mb-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                CHÚ Ý: {{ $criticalServices->count() }} dịch vụ sẽ hết hạn trong 2 ngày!
                            </h6>
                            <p class="mb-0">
                                Nên liên hệ khách hàng sớm để gia hạn.
                            </p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        <!-- Thống kê dịch vụ kích hoạt hôm nay -->
                        @if(request('filter') === 'activated-today' && $todayStats)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-primary text-white">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-chart-line me-2"></i>
                                            Thống kê dịch vụ kích hoạt hôm nay ({{ now()->format('d/m/Y') }})
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h3 class="mb-1">{{ $todayStats['total_services'] }}</h3>
                                                    <small>Tổng dịch vụ</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h3 class="mb-1">{{ $todayStats['unique_customers'] }}</h3>
                                                    <small>Khách hàng</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h3 class="mb-1">{{ number_format($todayStats['revenue_estimate']) }}₫</h3>
                                                    <small>Doanh thu ước tính</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h6 class="mb-1">Top gói dịch vụ:</h6>
                                                    @foreach($todayStats['top_packages'] as $packageName => $count)
                                                    <small class="d-block">{{ $packageName }}: {{ $count }}</small>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Simple Filter -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <form method="GET" class="row g-3">
                                    <div class="col-md-4">
                                        <input type="text"
                                            name="search"
                                            class="form-control"
                                            placeholder="Tìm theo tên, mã KH, email KH, SĐT, email đăng nhập, tên gói DV..."
                                            value="{{ request('search') }}">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Có thể tìm theo email đăng nhập dịch vụ
                                        </small>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="filter" class="form-select">
                                            <option value="">Tất cả trạng thái</option>
                                            <optgroup label="Trạng thái dịch vụ">
                                                <option value="active" {{ request('filter') === 'active' ? 'selected' : '' }}>
                                                    Đang hoạt động
                                                </option>
                                                <option value="expiring" {{ request('filter') === 'expiring' ? 'selected' : '' }}>
                                                    Sắp hết hạn
                                                </option>
                                                <option value="expired" {{ request('filter') === 'expired' ? 'selected' : '' }}>
                                                    Đã hết hạn
                                                </option>
                                            </optgroup>
                                            <optgroup label="Nhắc nhở">
                                                <option value="expiring-not-reminded" {{ request('filter') === 'expiring-not-reminded' ? 'selected' : '' }}>
                                                    Sắp hết hạn - Chưa nhắc
                                                </option>
                                                <option value="reminded" {{ request('filter') === 'reminded' ? 'selected' : '' }}>
                                                    Đã được nhắc nhở
                                                </option>
                                            </optgroup>
                                            <optgroup label="Ngày kích hoạt">
                                                <option value="activated-today" {{ request('filter') === 'activated-today' ? 'selected' : '' }}>
                                                    Kích hoạt hôm nay
                                                </option>
                                                <option value="activated-yesterday" {{ request('filter') === 'activated-yesterday' ? 'selected' : '' }}>
                                                    Kích hoạt hôm qua
                                                </option>
                                                <option value="activated-this-week" {{ request('filter') === 'activated-this-week' ? 'selected' : '' }}>
                                                    Kích hoạt tuần này
                                                </option>
                                                <option value="activated-this-month" {{ request('filter') === 'activated-this-month' ? 'selected' : '' }}>
                                                    Kích hoạt tháng này
                                                </option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="service-package-wrapper position-relative">
                                            <input type="text"
                                                id="servicePackageSearch"
                                                name="service_package_search"
                                                class="form-control"
                                                placeholder="Tìm gói dịch vụ..."
                                                value="{{ request('service_package_search') ?? (request('service_package_id') ? $servicePackages->find(request('service_package_id'))?->name : '') }}"
                                                autocomplete="off">
                                            <input type="hidden" name="service_package_id" id="servicePackageId" value="{{ request('service_package_id') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search me-1"></i>Lọc
                                        </button>
                                        @if(request()->hasAny(['search', 'filter', 'service_package_id', 'service_package_search']))
                                        <a href="{{ route('admin.customer-services.index') }}"
                                            class="btn btn-secondary w-100 mt-1">
                                            <i class="fas fa-times-circle me-1"></i>Xóa
                                        </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Results Info -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">
                                Hiển thị {{ $customerServices->firstItem() ?? 0 }} - {{ $customerServices->lastItem() ?? 0 }}
                                trong tổng số <strong>{{ $customerServices->total() }}</strong> dịch vụ
                            </span>
                            <small class="text-muted">
                                Cập nhật lúc {{ now()->format('H:i') }}
                            </small>
                        </div>

                        <!-- Customer Services Table -->
                        @if($customerServices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th class="sticky-column" style="position: sticky; left: 0; background: #f8f9fa; z-index: 10; min-width: 120px;">Thao tác</th>
                                        <th style="min-width: 150px;">Khách hàng</th>
                                        <th style="min-width: 180px;">Dịch vụ</th>
                                        <th style="min-width: 150px;">Family</th>
                                        <th style="min-width: 200px;">Email đăng nhập</th>
                                        <th style="min-width: 100px;">Kích hoạt</th>
                                        <th style="min-width: 100px;">Hết hạn</th>
                                        <th style="min-width: 100px;">Trạng thái</th>
                                        <th style="min-width: 120px;">Nhắc nhở</th>
                                        <th style="min-width: 120px;">Người nhập</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customerServices as $service)
                                    @php
                                    // Tính toán trạng thái theo logic mới
                                    $daysRemaining = $service->getDaysRemaining();

                                    // Xác định trạng thái
                                    if (!$service->expires_at) {
                                    $status = 'active'; // Không giới hạn thời gian
                                    } elseif ($service->expires_at->startOfDay()->isPast()) {
                                    $status = 'expired'; // Đã hết hạn
                                    } elseif ($service->isExpiringSoon()) {
                                    $status = 'expiring'; // Sắp hết hạn (5 ngày)
                                    } else {
                                    $status = 'active'; // Đang hoạt động bình thường
                                    }

                                    $rowClass = '';
                                    if ($status === 'expiring') {
                                    if ($daysRemaining <= 1) {
                                        $rowClass='table-danger' ; // Đỏ cho 0-1 ngày
                                        } elseif ($daysRemaining <=2) {
                                        $rowClass='table-warning' ; // Vàng cho 2 ngày
                                        }
                                        }
                                        @endphp
                                        <tr id="service-{{ $service->id }}" class="{{ $rowClass }}">
                                        <!-- Cột Thao tác - Di chuyển lên đầu và sticky -->
                                        <td class="sticky-column" style="position: sticky; left: 0; background: white; z-index: 5; border-right: 1px solid #dee2e6;">
                                            <div class="btn-group-vertical d-flex flex-column gap-1" style="min-width: 100px;">
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.customer-services.show', $service) }}"
                                                        class="btn btn-sm btn-outline-info"
                                                        title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.customer-services.edit', $service) }}"
                                                        class="btn btn-sm btn-outline-primary"
                                                        title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>

                                                @php
                                                $isExpiringSoon = $service->isExpiringSoon();
                                                $isExpired = $service->expires_at && $service->expires_at->startOfDay()->isPast();
                                                @endphp

                                                <div class="btn-group">
                                                    @if($isExpiringSoon)
                                                    @if(!$service->reminder_sent || $service->needsReminderAgain())
                                                    <button class="btn btn-sm btn-outline-warning"
                                                        onclick="markReminded({{ $service->id }})"
                                                        title="Đánh dấu đã nhắc nhở">
                                                        <i class="fas fa-bell"></i>
                                                    </button>
                                                    @endif

                                                    @if($service->reminder_sent)
                                                    <button class="btn btn-sm btn-outline-secondary"
                                                        onclick="resetReminder({{ $service->id }})"
                                                        title="Reset trạng thái nhắc nhở">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                    @endif
                                                    @elseif($isExpired)
                                                    @if(!$service->reminder_sent || $service->needsReminderAgain())
                                                    <button class="btn btn-sm btn-outline-danger"
                                                        onclick="markReminded({{ $service->id }})"
                                                        title="Đánh dấu đã nhắc nhở (Đã hết hạn)">
                                                        <i class="fas fa-bell-slash"></i>
                                                    </button>
                                                    @endif

                                                    @if($service->reminder_sent)
                                                    <button class="btn btn-sm btn-outline-secondary"
                                                        onclick="resetReminder({{ $service->id }})"
                                                        title="Reset trạng thái nhắc nhở">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                    @endif
                                                    @endif

                                                    <button class="btn btn-sm btn-outline-danger"
                                                        onclick="confirmDelete('{{ $service->customer->name }} - {{ $service->servicePackage->name }}', '{{ route('admin.customer-services.destroy', $service) }}')"
                                                        title="Xóa">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <!-- Cột Khách hàng -->
                                        <td>
                                            <div>
                                                <strong>{{ $service->customer->name }}</strong>
                                                <br><small class="text-muted">{{ $service->customer->customer_code }}</small>
                                                @if($status === 'expiring' && $daysRemaining <= 1)
                                                    <br><small class="text-danger fw-bold"><i class="fas fa-exclamation-triangle"></i> CẤP BÁC!</small>
                                                    @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $service->servicePackage->name }}</strong>
                                                <br><small class="text-muted">{{ $service->servicePackage->category->name ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($service->family_account_id && $service->familyAccount)
                                                <a href="{{ route('admin.family-accounts.show', $service->familyAccount) }}" 
                                                   class="text-decoration-none"
                                                   title="Xem chi tiết Family">
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-home me-1"></i>
                                                        {{ Str::limit($service->familyAccount->family_name, 20) }}
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">{{ $service->familyAccount->family_code }}</small>
                                                </a>
                                            @elseif(str_contains(strtolower($service->servicePackage->account_type ?? ''), 'family'))
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Chưa gán Family
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $service->login_email ?? 'Chưa có' }}</td>
                                        <td>{{ $service->activated_at ? $service->activated_at->format('d/m/Y') : 'Chưa kích hoạt' }}</td>
                                        <td>
                                            @if($service->expires_at)
                                            {{ $service->expires_at->format('d/m/Y') }}
                                            @if($status === 'expiring')
                                            <br><small class="fw-bold {{ $daysRemaining <= 1 ? 'text-danger' : ($daysRemaining <= 2 ? 'text-warning' : 'text-info') }}">
                                                Còn {{ $daysRemaining }} ngày
                                            </small>
                                            @endif
                                            @else
                                            Không giới hạn
                                            @endif
                                        </td>
                                        <td>
                                            @if($status === 'active')
                                            <span class="badge bg-success">Đang hoạt động</span>
                                            @elseif($status === 'expiring')
                                            @if($daysRemaining <= 1)
                                                <span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> SẮP HẾT HẠN</span>
                                                @else
                                                <span class="badge bg-warning">Sắp hết hạn</span>
                                                @endif
                                                @elseif($status === 'expired')
                                                <span class="badge bg-danger">Đã hết hạn</span>
                                                @else
                                                <span class="badge bg-secondary">Chưa kích hoạt</span>
                                                @endif
                                        </td>
                                        <td>
                                            @if($service->reminder_sent)
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-success me-2">
                                                    <i class="fas fa-check-circle"></i> Đã nhắc
                                                </span>
                                                <small class="text-muted">
                                                    {{ $service->reminder_count }}x<br>
                                                    {{ $service->reminder_sent_at ? $service->reminder_sent_at->format('d/m H:i') : 'N/A' }}
                                                </small>
                                            </div>
                                            @if($service->needsReminderAgain())
                                            <div class="mt-1">
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-exclamation-triangle"></i> Cần nhắc lại
                                                </span>
                                            </div>
                                            @endif
                                            @else
                                            @if($status === 'expiring')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-exclamation-triangle"></i> Chưa nhắc
                                            </span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                            @endif
                                        </td>
                                        <!-- Cột Người nhập -->
                                        <td>
                                            <small class="text-muted">
                                                System
                                                <br>{{ $service->created_at->format('d/m/Y') }}
                                            </small>
                                        </td>
                                        </tr>
                                        @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $customerServices->withQueryString()->links() }}
                        </div>
                        @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Không tìm thấy dịch vụ nào</h5>
                            @if(request()->hasAny(['search', 'filter', 'service_package_id', 'service_package_search']))
                            <p class="text-muted">Thử thay đổi bộ lọc hoặc <a href="{{ route('admin.customer-services.index') }}">xóa bộ lọc</a></p>
                            @else
                            <p class="text-muted">Hãy <a href="{{ route('admin.customer-services.create') }}">gán dịch vụ đầu tiên</a></p>
                            @endif
                        </div>
                        @endif
            </div>
        </div>
    </div>
</div>

<!-- Service Package Dropdown Portal -->
<div id="servicePackageDropdown" class="service-dropdown-portal">
    <a class="dropdown-item" href="#" data-id="">
        <i class="fas fa-list me-2 text-muted"></i>Tất cả gói dịch vụ
    </a>
    <div class="dropdown-divider"></div>
    @foreach($servicePackages as $package)
    <a class="dropdown-item" href="#" data-id="{{ $package->id }}">
        <i class="fas fa-cube me-2 text-primary"></i>{{ $package->name }}
    </a>
    @endforeach
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa dịch vụ này?</p>
                <p class="text-muted" id="serviceToDelete"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmDelete(serviceName, deleteUrl) {
        document.getElementById('serviceToDelete').textContent = serviceName;
        document.getElementById('deleteForm').action = deleteUrl;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    function markReminded(serviceId) {
        const notes = prompt('Ghi chú về việc nhắc nhở (tùy chọn):');
        if (notes === null) return; // User cancelled

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch(`/admin/customer-services/${serviceId}/mark-reminded`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    notes: notes
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Reload to update UI
                } else {
                    alert('Có lỗi xảy ra!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra!');
            });
    }

    function resetReminder(serviceId) {
        if (!confirm('Bạn có chắc chắn muốn reset trạng thái nhắc nhở?')) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch(`/admin/customer-services/${serviceId}/reset-reminder`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Reload to update UI
                } else {
                    alert('Có lỗi xảy ra!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra!');
            });
    }

    // Scroll to specific service if anchor is present in URL
    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.hash) {
            const targetElement = document.querySelector(window.location.hash);
            if (targetElement) {
                setTimeout(() => {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    // Add highlight effect
                    targetElement.style.backgroundColor = '#fff3cd';
                    setTimeout(() => {
                        targetElement.style.backgroundColor = '';
                    }, 3000);
                }, 100);
            }
        }

        // ============================================
        // SEARCHABLE SERVICE PACKAGE DROPDOWN
        // ============================================
        const servicePackageSearch = document.getElementById('servicePackageSearch');
        const servicePackageId = document.getElementById('servicePackageId');
        const servicePackageDropdown = document.getElementById('servicePackageDropdown');

        if (servicePackageSearch && servicePackageDropdown) {
            const allItems = servicePackageDropdown.querySelectorAll('.dropdown-item');

            // Function to position and show dropdown
            function showDropdown() {
                const rect = servicePackageSearch.getBoundingClientRect();
                servicePackageDropdown.style.top = (rect.bottom + 5) + 'px';
                servicePackageDropdown.style.left = rect.left + 'px';
                servicePackageDropdown.style.width = Math.max(rect.width, 300) + 'px';
                servicePackageDropdown.style.display = 'block';
            }

            // Function to hide dropdown
            function hideDropdown() {
                servicePackageDropdown.style.display = 'none';
            }

            // Update position on scroll
            window.addEventListener('scroll', function() {
                if (servicePackageDropdown.style.display === 'block') {
                    showDropdown();
                }
            });

            // Resize handler
            window.addEventListener('resize', function() {
                if (servicePackageDropdown.style.display === 'block') {
                    showDropdown();
                }
            });

            // Show dropdown when input is focused
            servicePackageSearch.addEventListener('focus', function() {
                showDropdown();
                filterDropdownItems('');
            });

            // Show dropdown when clicking on input
            servicePackageSearch.addEventListener('click', function(e) {
                e.stopPropagation();
                showDropdown();
            });

            // Filter items as user types
            servicePackageSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                // Clear the hidden ID field when user types - allow text search
                servicePackageId.value = '';
                filterDropdownItems(searchTerm);
                showDropdown();
            });

            // Filter function
            function filterDropdownItems(searchTerm) {
                let visibleCount = 0;
                allItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    const isAllOption = item.dataset.id === '';

                    if (searchTerm === '' || text.includes(searchTerm) || isAllOption) {
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Update divider visibility
                const divider = servicePackageDropdown.querySelector('.dropdown-divider');
                if (divider) {
                    divider.style.display = visibleCount > 1 ? 'block' : 'none';
                }

                // Show "no results" message if needed
                let noResultsMsg = servicePackageDropdown.querySelector('.no-results-message');
                if (visibleCount <= 1 && searchTerm !== '') {
                    if (!noResultsMsg) {
                        noResultsMsg = document.createElement('div');
                        noResultsMsg.className = 'no-results-message text-muted text-center py-3';
                        noResultsMsg.innerHTML = '<i class="fas fa-search me-2"></i>Không tìm thấy gói dịch vụ phù hợp';
                        servicePackageDropdown.appendChild(noResultsMsg);
                    }
                    noResultsMsg.style.display = 'block';
                } else if (noResultsMsg) {
                    noResultsMsg.style.display = 'none';
                }
            }

            // Handle item selection
            allItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const id = this.dataset.id;
                    const name = id === '' ? '' : this.textContent.trim().replace(/^\s*[\uf0c8\uf03a]\s*/, '');

                    servicePackageId.value = id;
                    servicePackageSearch.value = name;
                    hideDropdown();

                    // Add selected styling
                    allItems.forEach(i => i.classList.remove('active'));
                    if (id !== '') {
                        this.classList.add('active');
                    }
                });
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!servicePackageSearch.contains(e.target) && !servicePackageDropdown.contains(e.target)) {
                    hideDropdown();
                }
            });

            // Prevent dropdown from closing when clicking inside it
            servicePackageDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // Keyboard navigation
            servicePackageSearch.addEventListener('keydown', function(e) {
                const visibleItems = Array.from(allItems).filter(item => item.style.display !== 'none');
                const currentIndex = visibleItems.findIndex(item => item.classList.contains('highlighted'));

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    showDropdown();
                    const nextIndex = currentIndex < visibleItems.length - 1 ? currentIndex + 1 : 0;
                    highlightItem(visibleItems, nextIndex);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    showDropdown();
                    const prevIndex = currentIndex > 0 ? currentIndex - 1 : visibleItems.length - 1;
                    highlightItem(visibleItems, prevIndex);
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    const highlightedItem = visibleItems.find(item => item.classList.contains('highlighted'));
                    if (highlightedItem) {
                        highlightedItem.click();
                    } else if (visibleItems.length > 0) {
                        visibleItems[0].click();
                    }
                } else if (e.key === 'Escape') {
                    hideDropdown();
                    servicePackageSearch.blur();
                }
            });

            function highlightItem(items, index) {
                items.forEach(item => {
                    item.classList.remove('highlighted');
                    item.style.backgroundColor = '';
                });
                if (items[index]) {
                    items[index].classList.add('highlighted');
                    items[index].style.backgroundColor = '#e9ecef';
                    items[index].scrollIntoView({
                        block: 'nearest'
                    });
                }
            }

            // Add hover effect
            allItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    allItems.forEach(i => {
                        i.classList.remove('highlighted');
                        if (!i.classList.contains('active')) {
                            i.style.backgroundColor = '';
                        }
                    });
                    if (!this.classList.contains('active')) {
                        this.style.backgroundColor = '#e9ecef';
                    }
                });
                item.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('active')) {
                        this.style.backgroundColor = '';
                    }
                });
            });
        }
        // ============================================
        // END SEARCHABLE SERVICE PACKAGE DROPDOWN
        // ============================================
    });
</script>
@endsection