@extends('layouts.admin')

@section('title', 'Thêm tài khoản dùng chung')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus me-2"></i>Thêm tài khoản dùng chung
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.shared-accounts.credentials.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="service_package_id" class="form-label">Gói dịch vụ <span class="text-danger">*</span></label>
                            <select class="form-select @error('service_package_id') is-invalid @enderror" 
                                    id="service_package_id" name="service_package_id" required>
                                <option value="">-- Chọn gói dịch vụ --</option>
                                @foreach($servicePackages as $package)
                                    <option value="{{ $package->id }}" 
                                            {{ old('service_package_id', $selectedPackageId) == $package->id ? 'selected' : '' }}>
                                        {{ $package->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_package_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="max_users" class="form-label">Số người dùng tối đa <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('max_users') is-invalid @enderror" 
                                       id="max_users" name="max_users" value="{{ old('max_users', 10) }}" min="1" max="100" required>
                                @error('max_users')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="text" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" value="{{ old('password') }}">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="two_factor_secret" class="form-label">2FA / OTP Secret</label>
                            <textarea class="form-control @error('two_factor_secret') is-invalid @enderror" 
                                      id="two_factor_secret" name="two_factor_secret" rows="2">{{ old('two_factor_secret') }}</textarea>
                            @error('two_factor_secret')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="recovery_codes" class="form-label">Recovery Codes</label>
                            <textarea class="form-control @error('recovery_codes') is-invalid @enderror" 
                                      id="recovery_codes" name="recovery_codes" rows="2">{{ old('recovery_codes') }}</textarea>
                            @error('recovery_codes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Ngày bắt đầu</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Ngày hết hạn</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date') }}">
                                @error('end_date')
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
                                    id="custom_duration" min="1" placeholder="Nhập số"
                                    value="{{ old('custom_duration') }}">
                                <select class="form-select" id="duration_unit" style="max-width: 120px;">
                                    <option value="days">Ngày</option>
                                    <option value="months" selected>Tháng</option>
                                    <option value="years">Năm</option>
                                </select>
                            </div>
                            <div class="form-text text-info" id="duration_info">
                                <i class="fas fa-info-circle me-1"></i>
                                Nhập thời hạn để tự động tính ngày hết hạn
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.shared-accounts.credentials', ['service_package_id' => $selectedPackageId]) }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Lưu tài khoản
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
                durationInfo.innerHTML = '<i class="fas fa-info-circle me-1"></i>Nhập thời hạn để tự động tính ngày hết hạn';
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

