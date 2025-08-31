@extends('layouts.admin')

@section('title', 'Chỉnh sửa thành viên Family Account')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Family Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Thông tin Family Account
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Tên Family:</small><br>
                            <strong>{{ $familyAccount->family_name }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Mã Family:</small><br>
                            <code class="bg-light px-2 py-1 rounded">{{ $familyAccount->family_code }}</code>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Gói dịch vụ:</small><br>
                            <span class="badge bg-info">{{ $familyAccount->servicePackage->name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Thời hạn gói:</small><br>
                            <strong>{{ $familyAccount->servicePackage->duration_days ?? 30 }} ngày</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Member Form -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-auto">
                            <h5 class="mb-0">
                                <i class="fas fa-user-edit me-2"></i>
                                Chỉnh sửa thành viên
                            </h5>
                            <small class="text-muted">{{ $member->member_name ?? $member->member_email }}</small>
                        </div>
                        <div>
                            <a href="{{ route('admin.family-accounts.show', $familyAccount) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Quay lại
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form id="editMemberForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Member Email -->
                            <div class="col-md-6 mb-3">
                                <label for="member_email" class="form-label">
                                    Email thành viên <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="member_email" 
                                       name="member_email" 
                                       value="{{ $member->member_email }}" 
                                       required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">
                                    Trạng thái <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active" {{ $member->status === 'active' ? 'selected' : '' }}>
                                        <i class="fas fa-check-circle"></i> Hoạt động
                                    </option>
                                    <option value="suspended" {{ $member->status === 'suspended' ? 'selected' : '' }}>
                                        <i class="fas fa-pause-circle"></i> Tạm dừng
                                    </option>
                                    <option value="removed" {{ $member->status === 'removed' ? 'selected' : '' }}>
                                        <i class="fas fa-times-circle"></i> Đã xóa
                                    </option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Start Date -->
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">
                                    Ngày bắt đầu <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="start_date" 
                                       name="start_date" 
                                       value="{{ $member->start_date ? \Carbon\Carbon::parse($member->start_date)->format('Y-m-d') : '' }}" 
                                       required>
                                <div class="invalid-feedback"></div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Ngày kết thúc sẽ tự động tính dựa trên gói dịch vụ
                                </small>
                            </div>

                            <!-- End Date -->
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">
                                    Ngày kết thúc
                                </label>
                                <div class="input-group">
                                    <input type="date" 
                                           class="form-control" 
                                           id="end_date" 
                                           name="end_date" 
                                           value="{{ $member->end_date ? \Carbon\Carbon::parse($member->end_date)->format('Y-m-d') : '' }}">
                                    <button type="button" 
                                            class="btn btn-outline-secondary" 
                                            id="reset-auto-calc"
                                            title="Tính lại tự động từ ngày bắt đầu">
                                        <i class="fas fa-refresh"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback"></div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-magic"></i> 
                                    <span id="auto-calculated-info">Tự động tính từ ngày bắt đầu + {{ $familyAccount->servicePackage->duration_days ?? 30 }} ngày</span>
                                </small>
                            </div>

                            <!-- Member Notes -->
                            <div class="col-12 mb-3">
                                <label for="member_notes" class="form-label">
                                    Ghi chú về thành viên
                                </label>
                                <textarea class="form-control" 
                                          id="member_notes" 
                                          name="member_notes" 
                                          rows="3"
                                          placeholder="Ghi chú về thành viên này...">{{ $member->member_notes }}</textarea>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Current Info Display -->
                            <div class="col-12 mb-3">
                                <div class="alert alert-light border">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Thông tin hiện tại
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <small class="text-muted">Ngày tham gia:</small><br>
                                            <strong>{{ $member->joined_at ? \Carbon\Carbon::parse($member->joined_at)->format('d/m/Y') : 'N/A' }}</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted">Lần cuối hoạt động:</small><br>
                                            <strong>{{ $member->last_active_at ? \Carbon\Carbon::parse($member->last_active_at)->format('d/m/Y H:i') : 'Chưa có' }}</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted">Người thêm:</small><br>
                                            <strong>{{ $member->addedBy->name ?? 'N/A' }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.family-accounts.show', $familyAccount) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        Hủy
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save me-1"></i>
                                        Cập nhật thành viên
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const resetBtn = document.getElementById('reset-auto-calc');
    const form = document.getElementById('editMemberForm');
    const submitBtn = document.getElementById('submitBtn');
    const packageDurationDays = {{ $familyAccount->servicePackage->duration_days ?? 30 }};

    // Auto calculate end date when start date changes
    startDateInput.addEventListener('change', function() {
        console.log('Start date changed to:', this.value);
        console.log('End date user modified:', endDateInput.dataset.userModified);
        
        if (this.value) {
            const startDate = new Date(this.value);
            const endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + packageDurationDays);
            
            const formattedEndDate = endDate.toISOString().split('T')[0];
            console.log('Calculated end date:', formattedEndDate);
            
            // Always update end date unless user has manually changed it after page load
            if (!endDateInput.dataset.userModified) {
                endDateInput.value = formattedEndDate;
                console.log('End date updated automatically');
                
                // Update info text
                const autoInfo = document.getElementById('auto-calculated-info');
                autoInfo.innerHTML = `<i class="fas fa-check text-success"></i> Tự động tính: ${formatDate(endDate)} (${packageDurationDays} ngày)`;
            } else {
                console.log('End date not updated - user has modified it');
            }
        }
    });

    // Mark end date as user modified when manually changed
    endDateInput.addEventListener('change', function() {
        this.dataset.userModified = 'true';
        const autoInfo = document.getElementById('auto-calculated-info');
        autoInfo.innerHTML = `<i class="fas fa-user text-info"></i> Ngày kết thúc đã được tùy chỉnh`;
    });

    // Reset button to force auto-calculation
    resetBtn.addEventListener('click', function() {
        endDateInput.dataset.userModified = '';
        if (startDateInput.value) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + packageDurationDays);
            
            const formattedEndDate = endDate.toISOString().split('T')[0];
            endDateInput.value = formattedEndDate;
            
            // Update info text
            const autoInfo = document.getElementById('auto-calculated-info');
            autoInfo.innerHTML = `<i class="fas fa-check text-success"></i> Tự động tính: ${formatDate(endDate)} (${packageDurationDays} ngày)`;
        } else {
            alert('Vui lòng chọn ngày bắt đầu trước');
            startDateInput.focus();
        }
    });

    // Format date for display
    function formatDate(date) {
        return date.toLocaleDateString('vi-VN', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang cập nhật...';
        
        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        
        const formData = new FormData(form);
        
        fetch(`{{ route('admin.family-accounts.update-member', [$familyAccount, $member]) }}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showAlert('success', data.message);
                
                // Update auto-calculated info if end date was calculated
                if (data.data && data.data.calculated_end_date) {
                    const autoInfo = document.getElementById('auto-calculated-info');
                    autoInfo.innerHTML = `<i class="fas fa-check text-success"></i> Đã tính tự động: ${data.data.end_date}`;
                }
                
                // Redirect after delay
                setTimeout(() => {
                    window.location.href = '{{ route('admin.family-accounts.show', $familyAccount) }}';
                }, 2000);
            } else {
                throw new Error(data.error || 'Có lỗi xảy ra');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', error.message || 'Có lỗi xảy ra khi cập nhật thành viên');
        })
        .finally(() => {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Cập nhật thành viên';
        });
    });

    // Show alert message
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas ${iconClass} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Insert alert at the top of the card body
        const cardBody = document.querySelector('.card-body');
        cardBody.insertAdjacentHTML('afterbegin', alertHtml);
    }
});
</script>
@endpush
@endsection
