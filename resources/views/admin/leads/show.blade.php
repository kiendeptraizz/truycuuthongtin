@extends('layouts.admin')

@section('title', 'Chi tiết Lead - ' . $lead->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user text-primary me-2"></i>
                        {{ $lead->name }}
                    </h1>
                    <p class="text-muted mb-0">
                        Lead #{{ $lead->id }} - 
                        <span class="badge {{ $lead->getStatusBadgeClass() }}">{{ $lead->getStatusName() }}</span>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.leads.edit', $lead) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                    </a>
                    @if($lead->status !== 'won')
                        <button class="btn btn-success" onclick="showConvertModal()">
                            <i class="fas fa-user-check me-2"></i>Chuyển đổi
                        </button>
                    @endif
                    <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Lead Information -->
                <div class="col-lg-8">
                    <!-- Basic Info -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin cơ bản</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">Tên:</td>
                                            <td>{{ $lead->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Điện thoại:</td>
                                            <td>
                                                <a href="tel:{{ $lead->phone }}" class="text-decoration-none">
                                                    <i class="fas fa-phone me-1"></i>{{ $lead->phone }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Email:</td>
                                            <td>
                                                @if($lead->email)
                                                    <a href="mailto:{{ $lead->email }}" class="text-decoration-none">
                                                        <i class="fas fa-envelope me-1"></i>{{ $lead->email }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">Chưa có</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Nguồn:</td>
                                            <td>
                                                <span class="badge bg-info">{{ ucfirst($lead->source) }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">Trạng thái:</td>
                                            <td>
                                                <span class="badge {{ $lead->getStatusBadgeClass() }}">
                                                    {{ $lead->getStatusName() }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Độ ưu tiên:</td>
                                            <td>
                                                <span class="badge {{ $lead->getPriorityBadgeClass() }}">
                                                    {{ $lead->getPriorityName() }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Người phụ trách:</td>
                                            <td>
                                                @if($lead->assignedUser)
                                                    <span class="text-primary">{{ $lead->assignedUser->name }}</span>
                                                @else
                                                    <span class="text-muted">Chưa gán</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Lần liên hệ cuối:</td>
                                            <td>
                                                @if($lead->last_contact_at)
                                                    {{ $lead->last_contact_at->format('d/m/Y H:i') }}
                                                @else
                                                    <span class="text-muted">Chưa có</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($lead->requirements)
                                <div class="mt-3">
                                    <h6 class="fw-bold">Yêu cầu:</h6>
                                    <p class="text-muted">{{ $lead->requirements }}</p>
                                </div>
                            @endif

                            @if($lead->notes)
                                <div class="mt-3">
                                    <h6 class="fw-bold">Ghi chú:</h6>
                                    <p class="text-muted">{{ $lead->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Service & Value Info -->
                    @if($lead->servicePackage || $lead->estimated_value)
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Thông tin dịch vụ</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @if($lead->servicePackage)
                                        <div class="col-md-6">
                                            <h6 class="fw-bold">Gói dịch vụ quan tâm:</h6>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-info me-2">{{ $lead->servicePackage->name }}</span>
                                                <span class="text-success">{{ number_format($lead->servicePackage->price) }} VND</span>
                                            </div>
                                        </div>
                                    @endif
                                    @if($lead->estimated_value)
                                        <div class="col-md-6">
                                            <h6 class="fw-bold">Giá trị ước tính:</h6>
                                            <span class="text-success h5">{{ number_format($lead->estimated_value) }} VND</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Activities Timeline -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Lịch sử hoạt động</h6>
                            <button class="btn btn-sm btn-success" onclick="showActivityModal()">
                                <i class="fas fa-plus me-1"></i>Thêm hoạt động
                            </button>
                        </div>
                        <div class="card-body">
                            @if($lead->activities->count() > 0)
                                <div class="timeline">
                                    @foreach($lead->activities as $activity)
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-{{ $activity->getTypeColor() }}">
                                                <i class="fas fa-{{ $activity->getTypeIcon() }}"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1">{{ $activity->getTypeName() }}</h6>
                                                        <p class="mb-1 text-muted">{{ $activity->notes }}</p>
                                                        @if($activity->data)
                                                            <div class="text-sm text-muted">
                                                                <pre>{{ json_encode($activity->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="text-end">
                                                        <small class="text-muted">
                                                            {{ $activity->created_at->format('d/m/Y H:i') }}
                                                        </small>
                                                        @if($activity->user)
                                                            <br><small class="text-primary">{{ $activity->user->name }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <i class="fas fa-history fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">Chưa có hoạt động nào</p>
                                    <button class="btn btn-primary" onclick="showActivityModal()">
                                        <i class="fas fa-plus me-2"></i>Thêm hoạt động đầu tiên
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Quick Actions -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thao tác nhanh</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary" onclick="showActivityModal('call')">
                                    <i class="fas fa-phone me-2"></i>Ghi nhận cuộc gọi
                                </button>
                                <button class="btn btn-outline-info" onclick="showActivityModal('email')">
                                    <i class="fas fa-envelope me-2"></i>Ghi nhận email
                                </button>
                                <button class="btn btn-outline-success" onclick="showActivityModal('meeting')">
                                    <i class="fas fa-users me-2"></i>Ghi nhận cuộc họp
                                </button>
                                <button class="btn btn-outline-warning" onclick="showActivityModal('quote')">
                                    <i class="fas fa-file-invoice-dollar me-2"></i>Ghi nhận báo giá
                                </button>
                                @if($lead->status !== 'won')
                                    <hr>
                                    <button class="btn btn-success" onclick="showConvertModal()">
                                        <i class="fas fa-user-check me-2"></i>Chuyển đổi thành KH
                                    </button>
                                @endif
                                @if($lead->status !== 'lost')
                                    <button class="btn btn-warning" onclick="showLostModal()">
                                        <i class="fas fa-times me-2"></i>Đánh dấu mất
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Follow-up Schedule -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Lịch theo dõi</h6>
                        </div>
                        <div class="card-body">
                            @if($lead->next_follow_up_at)
                                <div class="alert {{ $lead->isOverdue() ? 'alert-danger' : 'alert-info' }}">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-alt fa-2x me-3"></i>
                                        <div>
                                            <h6 class="mb-1">
                                                {{ $lead->next_follow_up_at->format('d/m/Y H:i') }}
                                            </h6>
                                            @if($lead->isOverdue())
                                                <small class="text-danger">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Quá hạn {{ $lead->getDaysOverdue() }} ngày
                                                </small>
                                            @else
                                                <small class="text-muted">
                                                    {{ $lead->next_follow_up_at->diffForHumans() }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-calendar-times me-2"></i>
                                    Chưa đặt lịch theo dõi
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Lead Stats -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thống kê</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-primary">{{ $lead->activities->count() }}</h4>
                                        <small class="text-muted">Hoạt động</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success">{{ $lead->created_at->diffInDays() }}</h4>
                                    <small class="text-muted">Ngày tạo</small>
                                </div>
                            </div>
                            <hr>
                            <div class="text-center">
                                <small class="text-muted">
                                    Tạo lúc: {{ $lead->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Modal -->
<div class="modal fade" id="activityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm hoạt động</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="activityForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Loại hoạt động</label>
                        <select name="type" class="form-select" required>
                            <option value="call">Gọi điện</option>
                            <option value="email">Gửi email</option>
                            <option value="meeting">Gặp mặt</option>
                            <option value="note">Ghi chú</option>
                            <option value="quote">Báo giá</option>
                            <option value="follow_up">Theo dõi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nội dung</label>
                        <textarea name="notes" class="form-control" rows="4" required 
                                  placeholder="Mô tả chi tiết hoạt động..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lịch theo dõi tiếp theo</label>
                        <input type="datetime-local" name="next_follow_up_at" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu hoạt động</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Convert Modal -->
<div class="modal fade" id="convertModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chuyển đổi Lead thành Khách hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.leads.convert', $lead) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên khách hàng</label>
                        <input type="text" name="customer_name" class="form-control" 
                               value="{{ $lead->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="customer_email" class="form-control" 
                               value="{{ $lead->email }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="customer_phone" class="form-control" 
                               value="{{ $lead->phone }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <textarea name="customer_address" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">Chuyển đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lost Modal -->
<div class="modal fade" id="lostModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Đánh dấu Lead bị mất</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.leads.mark-lost', $lead) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Lý do mất lead</label>
                        <textarea name="reason" class="form-control" rows="3" 
                                  placeholder="Mô tả lý do tại sao lead này bị mất..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning">Đánh dấu mất</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
.timeline {
    position: relative;
    padding-left: 3rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -2.5rem;
    top: 0;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
}

.timeline-content {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    border-left: 3px solid #007bff;
}
</style>

<script>
// Show activity modal
function showActivityModal(type = null) {
    const modal = new bootstrap.Modal(document.getElementById('activityModal'));
    if (type) {
        document.querySelector('#activityForm select[name="type"]').value = type;
    }
    modal.show();
}

// Show convert modal
function showConvertModal() {
    new bootstrap.Modal(document.getElementById('convertModal')).show();
}

// Show lost modal
function showLostModal() {
    new bootstrap.Modal(document.getElementById('lostModal')).show();
}

// Handle activity form submission
document.getElementById('activityForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        type: formData.get('type'),
        notes: formData.get('notes'),
        next_follow_up_at: formData.get('next_follow_up_at') || null
    };

    fetch(`{{ route('admin.leads.add-activity', $lead) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Có lỗi xảy ra: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi thêm hoạt động');
    });
});
</script>
@endsection
