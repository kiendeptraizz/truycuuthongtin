@extends('layouts.admin')

@section('title', 'Chỉnh sửa Lead - ' . $lead->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Chỉnh sửa Lead
                    </h1>
                    <p class="text-muted mb-0">{{ $lead->name }} - Lead #{{ $lead->id }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-info">
                        <i class="fas fa-eye me-2"></i>Xem chi tiết
                    </a>
                    <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>
                </div>
            </div>

            <!-- Form -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin cơ bản</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.leads.update', $lead) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $lead->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', $lead->phone) }}" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $lead->email) }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="source" class="form-label">Nguồn <span class="text-danger">*</span></label>
                                        <select class="form-select @error('source') is-invalid @enderror" id="source" name="source" required>
                                            <option value="">Chọn nguồn</option>
                                            <option value="website" {{ old('source', $lead->source) == 'website' ? 'selected' : '' }}>Website</option>
                                            <option value="facebook" {{ old('source', $lead->source) == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                            <option value="zalo" {{ old('source', $lead->source) == 'zalo' ? 'selected' : '' }}>Zalo</option>
                                            <option value="phone" {{ old('source', $lead->source) == 'phone' ? 'selected' : '' }}>Điện thoại</option>
                                            <option value="referral" {{ old('source', $lead->source) == 'referral' ? 'selected' : '' }}>Giới thiệu</option>
                                            <option value="advertisement" {{ old('source', $lead->source) == 'advertisement' ? 'selected' : '' }}>Quảng cáo</option>
                                            <option value="other" {{ old('source', $lead->source) == 'other' ? 'selected' : '' }}>Khác</option>
                                        </select>
                                        @error('source')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="new" {{ old('status', $lead->status) == 'new' ? 'selected' : '' }}>Mới</option>
                                            <option value="contacted" {{ old('status', $lead->status) == 'contacted' ? 'selected' : '' }}>Đã liên hệ</option>
                                            <option value="interested" {{ old('status', $lead->status) == 'interested' ? 'selected' : '' }}>Quan tâm</option>
                                            <option value="quoted" {{ old('status', $lead->status) == 'quoted' ? 'selected' : '' }}>Đã báo giá</option>
                                            <option value="negotiating" {{ old('status', $lead->status) == 'negotiating' ? 'selected' : '' }}>Đang đàm phán</option>
                                            <option value="follow_up" {{ old('status', $lead->status) == 'follow_up' ? 'selected' : '' }}>Cần theo dõi</option>
                                            <option value="won" {{ old('status', $lead->status) == 'won' ? 'selected' : '' }}>Thành công</option>
                                            <option value="lost" {{ old('status', $lead->status) == 'lost' ? 'selected' : '' }}>Thất bại</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="priority" class="form-label">Độ ưu tiên <span class="text-danger">*</span></label>
                                        <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                            <option value="low" {{ old('priority', $lead->priority) == 'low' ? 'selected' : '' }}>Thấp</option>
                                            <option value="medium" {{ old('priority', $lead->priority) == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                            <option value="high" {{ old('priority', $lead->priority) == 'high' ? 'selected' : '' }}>Cao</option>
                                            <option value="urgent" {{ old('priority', $lead->priority) == 'urgent' ? 'selected' : '' }}>Khẩn cấp</option>
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="assigned_to" class="form-label">Người phụ trách</label>
                                        <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                                            <option value="">Chưa gán</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('assigned_to', $lead->assigned_to) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('assigned_to')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="service_package_id" class="form-label">Gói dịch vụ quan tâm</label>
                                        <select class="form-select @error('service_package_id') is-invalid @enderror" 
                                                id="service_package_id" name="service_package_id">
                                            <option value="">Chưa xác định</option>
                                            @foreach($servicePackages as $package)
                                                <option value="{{ $package->id }}" {{ old('service_package_id', $lead->service_package_id) == $package->id ? 'selected' : '' }}>
                                                    {{ $package->name }} - {{ number_format($package->price) }} VND
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('service_package_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="estimated_value" class="form-label">Giá trị ước tính (VND)</label>
                                        <input type="number" class="form-control @error('estimated_value') is-invalid @enderror" 
                                               id="estimated_value" name="estimated_value" value="{{ old('estimated_value', $lead->estimated_value) }}" 
                                               min="0" step="1000">
                                        @error('estimated_value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="next_follow_up_at" class="form-label">Lịch theo dõi tiếp theo</label>
                                    <input type="datetime-local" class="form-control @error('next_follow_up_at') is-invalid @enderror" 
                                           id="next_follow_up_at" name="next_follow_up_at" 
                                           value="{{ old('next_follow_up_at', $lead->next_follow_up_at?->format('Y-m-d\TH:i')) }}">
                                    @error('next_follow_up_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="requirements" class="form-label">Yêu cầu khách hàng</label>
                                    <textarea class="form-control @error('requirements') is-invalid @enderror" 
                                              id="requirements" name="requirements" rows="3">{{ old('requirements', $lead->requirements) }}</textarea>
                                    @error('requirements')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Ghi chú</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3">{{ old('notes', $lead->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Hủy
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Cập nhật Lead
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Current Status -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Trạng thái hiện tại</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <span class="badge {{ $lead->getStatusBadgeClass() }} p-2">
                                        {{ $lead->getStatusName() }}
                                    </span>
                                </div>
                                <div>
                                    <span class="badge {{ $lead->getPriorityBadgeClass() }} p-2">
                                        {{ $lead->getPriorityName() }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($lead->isOverdue())
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Quá hạn theo dõi {{ $lead->getDaysOverdue() }} ngày
                                </div>
                            @endif

                            <div class="text-sm text-muted">
                                <div class="mb-2">
                                    <i class="fas fa-calendar-plus me-2"></i>
                                    Tạo: {{ $lead->created_at->format('d/m/Y H:i') }}
                                </div>
                                @if($lead->last_contact_at)
                                    <div class="mb-2">
                                        <i class="fas fa-phone me-2"></i>
                                        Liên hệ cuối: {{ $lead->last_contact_at->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                                @if($lead->converted_at)
                                    <div class="mb-2">
                                        <i class="fas fa-check-circle me-2"></i>
                                        Chuyển đổi: {{ $lead->converted_at->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thao tác nhanh</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye me-2"></i>Xem chi tiết
                                </a>
                                @if($lead->status !== 'won')
                                    <button class="btn btn-outline-success" onclick="showConvertModal()">
                                        <i class="fas fa-user-check me-2"></i>Chuyển đổi
                                    </button>
                                @endif
                                @if($lead->status !== 'lost')
                                    <button class="btn btn-outline-warning" onclick="showLostModal()">
                                        <i class="fas fa-times me-2"></i>Đánh dấu mất
                                    </button>
                                @endif
                                <button class="btn btn-outline-danger" onclick="confirmDelete()">
                                    <i class="fas fa-trash me-2"></i>Xóa Lead
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
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
                        </div>
                    </div>
                </div>
            </div>
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
<script>
// Auto-fill estimated value when service package is selected
document.getElementById('service_package_id').addEventListener('change', function() {
    const packageId = this.value;
    if (packageId) {
        const selectedOption = this.options[this.selectedIndex];
        const priceText = selectedOption.text;
        const priceMatch = priceText.match(/[\d,]+/);
        if (priceMatch) {
            const price = priceMatch[0].replace(/,/g, '');
            const estimatedValueInput = document.getElementById('estimated_value');
            if (!estimatedValueInput.value) {
                estimatedValueInput.value = price;
            }
        }
    }
});

// Show convert modal
function showConvertModal() {
    new bootstrap.Modal(document.getElementById('convertModal')).show();
}

// Show lost modal
function showLostModal() {
    new bootstrap.Modal(document.getElementById('lostModal')).show();
}

// Confirm delete
function confirmDelete() {
    if (confirm('Bạn có chắc chắn muốn xóa lead này?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.leads.destroy", $lead) }}';
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
