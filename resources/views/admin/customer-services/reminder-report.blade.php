@extends('layouts.admin')

@section('title', 'Báo cáo nhắc nhở khách hàng')
@section('page-title', 'Báo cáo nhắc nhở khách hàng')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Báo cáo nhắc nhở khách hàng sắp hết hạn</h5>
                        <small class="text-muted">Theo dõi trạng thái nhắc nhở cho dịch vụ sắp hết hạn</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.customer-services.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Quay lại
                        </a>
                        <button onclick="markAllAsReminded()" class="btn btn-warning">
                            <i class="fas fa-bell me-1"></i>
                            Đánh dấu tất cả
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Filter -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <form method="GET" class="d-flex gap-2">
                            <select name="days" class="form-select">
                                <option value="3" {{ request('days', 5) == 3 ? 'selected' : '' }}>3 ngày tới</option>
                                <option value="5" {{ request('days', 5) == 5 ? 'selected' : '' }}>5 ngày tới</option>
                                <option value="7" {{ request('days', 5) == 7 ? 'selected' : '' }}>7 ngày tới</option>
                                <option value="10" {{ request('days', 5) == 10 ? 'selected' : '' }}>10 ngày tới</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Lọc</button>
                        </form>
                    </div>
                </div>

                <!-- Thống kê tổng quan -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4 class="mb-0">{{ $expiringSoon }}</h4>
                                <small>Tổng sắp hết hạn</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4 class="mb-0">{{ $reminded }}</h4>
                                <small>Đã nhắc nhở</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h4 class="mb-0">{{ $notReminded }}</h4>
                                <small>Chưa nhắc nhở</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h4 class="mb-0">{{ number_format($expiringSoon > 0 ? ($reminded / $expiringSoon) * 100 : 0, 1) }}%</h4>
                                <small>Tỷ lệ đã nhắc</small>
                            </div>
                        </div>
                    </div>
                </div>

                @if($services->count() > 0)
                    <!-- Danh sách chưa được nhắc nhở -->
                    @php
                        $notRemindedServices = $services->where('reminder_sent', false);
                        $remindedServices = $services->where('reminder_sent', true);
                    @endphp

                    @if($notRemindedServices->count() > 0)
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Cần nhắc nhở ({{ $notRemindedServices->count() }})</h6>
                        </div>
                        
                        <div class="table-responsive mb-4">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes()">
                                        </th>
                                        <th>Khách hàng</th>
                                        <th>Dịch vụ</th>
                                        <th>Email</th>
                                        <th>Hết hạn</th>
                                        <th>Còn lại</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notRemindedServices as $service)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="service-checkbox" value="{{ $service->id }}">
                                            </td>
                                            <td>
                                                <strong>{{ $service->customer->name }}</strong>
                                                <br><small class="text-muted">{{ $service->customer->customer_code }}</small>
                                            </td>
                                            <td>{{ $service->servicePackage->name }}</td>
                                            <td>{{ $service->customer->email ?: 'Chưa có' }}</td>
                                            <td>
                                                <span class="badge bg-danger">{{ $service->expires_at->format('d/m/Y') }}</span>
                                            </td>
                                            <td>
                                                <strong class="text-danger">{{ $service->getDaysRemaining() }} ngày</strong>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" 
                                                        onclick="markReminded({{ $service->id }})"
                                                        title="Đánh dấu đã nhắc">
                                                    <i class="fas fa-bell"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <!-- Danh sách đã được nhắc nhở -->
                    @if($remindedServices->count() > 0)
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check me-2"></i>Đã nhắc nhở ({{ $remindedServices->count() }})</h6>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Khách hàng</th>
                                        <th>Dịch vụ</th>
                                        <th>Email</th>
                                        <th>Hết hạn</th>
                                        <th>Còn lại</th>
                                        <th>Nhắc nhở</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($remindedServices as $service)
                                        <tr>
                                            <td>
                                                <strong>{{ $service->customer->name }}</strong>
                                                <br><small class="text-muted">{{ $service->customer->customer_code }}</small>
                                            </td>
                                            <td>{{ $service->servicePackage->name }}</td>
                                            <td>{{ $service->customer->email ?: 'Chưa có' }}</td>
                                            <td>
                                                <span class="badge bg-warning">{{ $service->expires_at->format('d/m/Y') }}</span>
                                            </td>
                                            <td>
                                                <strong class="text-warning">{{ $service->getDaysRemaining() }} ngày</strong>
                                            </td>
                                            <td>
                                                <div class="text-success">
                                                    <strong>{{ $service->reminder_count }}x</strong><br>
                                                    <small>{{ $service->reminder_sent_at->format('d/m H:i') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($service->needsReminderAgain())
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-clock"></i> Cần nhắc lại
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check"></i> OK
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @if($service->needsReminderAgain())
                                                        <button class="btn btn-warning" 
                                                                onclick="markReminded({{ $service->id }})"
                                                                title="Nhắc lại">
                                                            <i class="fas fa-bell"></i>
                                                        </button>
                                                    @endif
                                                    <button class="btn btn-secondary" 
                                                            onclick="resetReminder({{ $service->id }})"
                                                            title="Reset">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-smile fa-3x text-success mb-3"></i>
                        <h5 class="text-success">Tuyệt vời!</h5>
                        <p class="text-muted">Không có dịch vụ nào sắp hết hạn trong {{ $days }} ngày tới.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleAllCheckboxes() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.service-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function markAllAsReminded() {
    const checkboxes = document.querySelectorAll('.service-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Vui lòng chọn ít nhất một dịch vụ để đánh dấu nhắc nhở.');
        return;
    }
    
    if (!confirm(`Bạn có chắc chắn muốn đánh dấu ${checkboxes.length} dịch vụ đã được nhắc nhở?`)) {
        return;
    }
    
    const serviceIds = Array.from(checkboxes).map(cb => cb.value);
    let completed = 0;
    
    serviceIds.forEach(serviceId => {
        markReminded(serviceId, () => {
            completed++;
            if (completed === serviceIds.length) {
                location.reload();
            }
        });
    });
}

function markReminded(serviceId, callback = null) {
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
            if (callback) {
                callback();
            } else {
                alert(data.message);
                location.reload();
            }
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
            location.reload();
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
