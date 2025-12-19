@extends('layouts.admin')

@section('title', 'Sửa tài khoản dùng chung')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Sửa tài khoản: {{ $credential->email }}
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.shared-accounts.credentials.update', $credential) }}">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-info">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>Gói dịch vụ:</strong> {{ $credential->servicePackage->name }}
                                </div>
                                <div>
                                    <strong>Đang sử dụng:</strong> 
                                    <span class="badge bg-{{ $credential->current_users_count >= $credential->max_users ? 'danger' : 'success' }}">
                                        {{ $credential->current_users_count }}/{{ $credential->max_users }} người
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $credential->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="max_users" class="form-label">Số người dùng tối đa <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('max_users') is-invalid @enderror" 
                                       id="max_users" name="max_users" value="{{ old('max_users', $credential->max_users) }}" 
                                       min="{{ $credential->current_users_count }}" max="100" required>
                                @error('max_users')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tối thiểu: {{ $credential->current_users_count }} (số người đang dùng)</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="text" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" value="{{ old('password', $credential->password) }}">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="two_factor_secret" class="form-label">2FA / OTP Secret</label>
                            <textarea class="form-control @error('two_factor_secret') is-invalid @enderror" 
                                      id="two_factor_secret" name="two_factor_secret" rows="2">{{ old('two_factor_secret', $credential->two_factor_secret) }}</textarea>
                            @error('two_factor_secret')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="recovery_codes" class="form-label">Recovery Codes</label>
                            <textarea class="form-control @error('recovery_codes') is-invalid @enderror" 
                                      id="recovery_codes" name="recovery_codes" rows="2">{{ old('recovery_codes', $credential->recovery_codes) }}</textarea>
                            @error('recovery_codes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label">Ngày bắt đầu</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" 
                                       value="{{ old('start_date', $credential->start_date?->format('Y-m-d')) }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">Ngày hết hạn</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" 
                                       value="{{ old('end_date', $credential->end_date?->format('Y-m-d')) }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status', $credential->status) == 'active' ? 'selected' : '' }}>
                                        Đang hoạt động
                                    </option>
                                    <option value="expired" {{ old('status', $credential->status) == 'expired' ? 'selected' : '' }}>
                                        Hết hạn
                                    </option>
                                    <option value="suspended" {{ old('status', $credential->status) == 'suspended' ? 'selected' : '' }}>
                                        Tạm ngưng
                                    </option>
                                    <option value="full" {{ old('status', $credential->status) == 'full' ? 'selected' : '' }}>
                                        Đã đầy
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Thời hạn tùy chỉnh -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-clock me-1"></i>Thời hạn tùy chỉnh
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control"
                                    id="custom_duration" min="1" placeholder="Nhập số">
                                <select class="form-select" id="duration_unit" style="max-width: 120px;">
                                    <option value="days">Ngày</option>
                                    <option value="months" selected>Tháng</option>
                                    <option value="years">Năm</option>
                                </select>
                            </div>
                            <div class="form-text text-info" id="duration_info">
                                <i class="fas fa-info-circle me-1"></i>
                                Nhập thời hạn để tự động tính ngày hết hạn từ ngày bắt đầu
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="2">{{ old('notes', $credential->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Danh sách người dùng -->
                        @if($credential->customerServices->count() > 0)
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-users me-2"></i>Khách hàng đang sử dụng</h6>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Khách hàng</th>
                                            <th>Trạng thái</th>
                                            <th>Hết hạn</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($credential->customerServices as $service)
                                        <tr>
                                            <td>{{ $service->customer->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $service->status == 'active' ? 'success' : 'secondary' }}">
                                                    {{ $service->status }}
                                                </span>
                                            </td>
                                            <td>{{ $service->expires_at?->format('d/m/Y') ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.shared-accounts.credentials', ['service_package_id' => $credential->service_package_id]) }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Duration calculator
    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const customDurationInput = document.getElementById('custom_duration');
        const durationUnitSelect = document.getElementById('duration_unit');
        const durationInfo = document.getElementById('duration_info');

        function calculateEndDate() {
            const startDate = startDateInput.value;
            const duration = parseInt(customDurationInput.value) || 0;
            const unit = durationUnitSelect.value;

            if (!startDate || duration <= 0) {
                durationInfo.innerHTML = '<i class="fas fa-info-circle me-1"></i>Nhập thời hạn để tự động tính ngày hết hạn từ ngày bắt đầu';
                return;
            }

            const start = new Date(startDate);
            let end = new Date(start);
            let daysText = '';

            if (unit === 'days') {
                end.setDate(start.getDate() + duration);
                daysText = `${duration} ngày`;
            } else if (unit === 'months') {
                end.setMonth(start.getMonth() + duration);
                const days = Math.round((end - start) / (1000 * 60 * 60 * 24));
                daysText = `${duration} tháng (~${days} ngày)`;
            } else if (unit === 'years') {
                end.setFullYear(start.getFullYear() + duration);
                const days = Math.round((end - start) / (1000 * 60 * 60 * 24));
                daysText = `${duration} năm (~${days} ngày)`;
            }

            // Format date as YYYY-MM-DD
            const formattedDate = end.toISOString().split('T')[0];
            endDateInput.value = formattedDate;

            // Update info text
            const formattedDisplay = end.toLocaleDateString('vi-VN', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            durationInfo.innerHTML = `<i class="fas fa-check-circle me-1 text-success"></i>Thời hạn: ${daysText} → Hết hạn: <strong>${formattedDisplay}</strong>`;
        }

        // Event listeners
        customDurationInput.addEventListener('input', calculateEndDate);
        durationUnitSelect.addEventListener('change', calculateEndDate);
        startDateInput.addEventListener('change', calculateEndDate);
    });
</script>
@endsection

