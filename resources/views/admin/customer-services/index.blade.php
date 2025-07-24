@extends('layouts.admin')

@section('title', 'Quản lý dịch vụ khách hàng')
@section('page-title', 'Quản lý dịch vụ khách hàng')

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
                    $criticalServices = $customerServices->filter(function($service) {
                        return $service->getStatus() === 'expiring' && $service->getDaysRemaining() <= 2;
                    });
                @endphp

                @if($urgentServices->count() > 0)
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <h6 class="alert-heading mb-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            🚨 CẢNH BÁO: {{ $urgentServices->count() }} dịch vụ sẽ hết hạn trong 24h!
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
                            <i class="fas fa-clock me-2"></i>
                            ⚠️ CHÚ Ý: {{ $criticalServices->count() }} dịch vụ sẽ hết hạn trong 2 ngày!
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
                                        📊 Thống kê dịch vụ kích hoạt hôm nay ({{ now()->format('d/m/Y') }})
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
                                       placeholder="Tìm khách hàng hoặc dịch vụ..."
                                       value="{{ request('search') }}">
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
                                            🎯 Kích hoạt hôm nay
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
                                <select name="service_package_id" class="form-select">
                                    <option value="">Tất cả gói dịch vụ</option>
                                    @foreach($servicePackages as $package)
                                        <option value="{{ $package->id }}" 
                                                {{ request('service_package_id') == $package->id ? 'selected' : '' }}>
                                            {{ $package->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Lọc
                                </button>
                                @if(request()->hasAny(['search', 'filter', 'service_package_id']))
                                    <a href="{{ route('admin.customer-services.index') }}" 
                                       class="btn btn-secondary w-100 mt-1">
                                        <i class="fas fa-times me-1"></i>Xóa
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
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Khách hàng</th>
                                    <th>Dịch vụ</th>
                                    <th>Email đăng nhập</th>
                                    <th>Kích hoạt</th>
                                    <th>Hết hạn</th>
                                    <th>Trạng thái</th>
                                    <th>Nhắc nhở</th>
                                    <th>Người nhập hàng</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customerServices as $service)
                                    @php
                                        // Tính toán trạng thái theo logic mới
                                        $daysRemaining = $service->getDaysRemaining();
                                        $daysUntilExpiry = $service->expires_at ? now()->diffInDays($service->expires_at, false) : null;

                                        // Xác định trạng thái
                                        if (!$service->expires_at) {
                                            $status = 'active'; // Không giới hạn thời gian
                                        } elseif ($service->expires_at->isPast()) {
                                            $status = 'expired'; // Đã hết hạn
                                        } elseif ($daysUntilExpiry !== null && $daysUntilExpiry <= 5) {
                                            $status = 'expiring'; // Sắp hết hạn (5 ngày)
                                        } else {
                                            $status = 'active'; // Đang hoạt động bình thường
                                        }

                                        $rowClass = '';
                                        if ($status === 'expiring') {
                                            if ($daysRemaining <= 1) {
                                                $rowClass = 'table-danger'; // Đỏ cho 0-1 ngày
                                            } elseif ($daysRemaining <= 2) {
                                                $rowClass = 'table-warning'; // Vàng cho 2 ngày
                                            }
                                        }
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td>
                                            <div>
                                                <strong>{{ $service->customer->name }}</strong>
                                                <br><small class="text-muted">{{ $service->customer->customer_code }}</small>
                                                @if($status === 'expiring' && $daysRemaining <= 1)
                                                    <br><small class="text-danger fw-bold">🚨 CẤP BÁC!</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $service->servicePackage->name }}</strong>
                                                <br><small class="text-muted">{{ $service->servicePackage->category->name ?? 'N/A' }}</small>
                                            </div>
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
                                                    <span class="badge bg-danger">🚨 SẮP HẾT HẠN</span>
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
                                                        <i class="fas fa-check"></i> Đã nhắc
                                                    </span>
                                                    <small class="text-muted">
                                                        {{ $service->reminder_count }}x<br>
                                                        {{ $service->reminder_sent_at ? $service->reminder_sent_at->format('d/m H:i') : 'N/A' }}
                                                    </small>
                                                </div>
                                                @if($service->needsReminderAgain())
                                                    <div class="mt-1">
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="fas fa-clock"></i> Cần nhắc lại
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
                                        <td>
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
                                                
                                                @php
                                                    $daysUntilExpiry = $service->expires_at ? now()->diffInDays($service->expires_at, false) : null;
                                                    $isExpiringSoon = $daysUntilExpiry !== null && $daysUntilExpiry >= 0 && $daysUntilExpiry <= 5;
                                                    $isExpired = $service->expires_at && $service->expires_at->isPast();
                                                @endphp

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
                                                            <i class="fas fa-undo"></i>
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
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                                
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="confirmDelete('{{ $service->customer->name }} - {{ $service->servicePackage->name }}', '{{ route('admin.customer-services.destroy', $service) }}')"
                                                        title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
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
                        @if(request()->hasAny(['search', 'filter', 'service_package_id']))
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
        body: JSON.stringify({ notes: notes })
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
</script>
@endsection
