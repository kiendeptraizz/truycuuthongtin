@extends('layouts.admin')

@section('title', 'Sửa tài khoản - ' . $resource->name)

@section('page-title', 'Sửa tài khoản')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header bg-{{ $resource->color ?? 'primary' }} text-white">
                    <h5 class="card-title mb-0">
                        @if($resource->icon)
                        <i class="{{ $resource->icon }} me-2"></i>
                        @else
                        <i class="fas fa-folder me-2"></i>
                        @endif
                        Sửa tài khoản: {{ $account->email ?: $account->username ?: $account->name ?: '#' . $account->id }}
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.resources.accounts.update', [$resource, $account]) }}">
                        @csrf
                        @method('PUT')

                        <!-- Subcategory selection -->
                        @if($subcategories->count() > 0)
                        <div class="mb-4">
                            <label for="resource_subcategory_id" class="form-label">
                                <i class="fas fa-tag me-1"></i>Danh mục con
                            </label>
                            <select class="form-select @error('resource_subcategory_id') is-invalid @enderror"
                                id="resource_subcategory_id" name="resource_subcategory_id">
                                <option value="">-- Chưa phân loại --</option>
                                @foreach($subcategories as $sub)
                                <option value="{{ $sub->id }}" {{ old('resource_subcategory_id', $account->resource_subcategory_id) == $sub->id ? 'selected' : '' }}>
                                    {{ $sub->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('resource_subcategory_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-user-circle me-2"></i>Thông tin đăng nhập
                                </h6>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Tên tài khoản (tùy chọn)</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $account->name) }}"
                                        placeholder="Tên để phân biệt">
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $account->email) }}"
                                        placeholder="Email đăng nhập">
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                        id="username" name="username" value="{{ old('username', $account->username) }}"
                                        placeholder="Username (nếu khác email)">
                                    @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password" value="{{ old('password', $account->password) }}"
                                            placeholder="Mật khẩu">
                                        <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard(document.getElementById('password').value)">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="generatePassword()">
                                            <i class="fas fa-random"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-shield-alt me-2"></i>Bảo mật & Thời hạn
                                </h6>

                                <div class="mb-3">
                                    <label for="two_factor_secret" class="form-label">2FA Secret / Backup Code</label>
                                    <textarea class="form-control @error('two_factor_secret') is-invalid @enderror"
                                        id="two_factor_secret" name="two_factor_secret" rows="2"
                                        placeholder="Mã 2FA hoặc backup codes">{{ old('two_factor_secret', $account->two_factor_secret) }}</textarea>
                                    @error('two_factor_secret')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="recovery_codes" class="form-label">Recovery Codes (khác)</label>
                                    <textarea class="form-control @error('recovery_codes') is-invalid @enderror"
                                        id="recovery_codes" name="recovery_codes" rows="2"
                                        placeholder="Các mã recovery khác">{{ old('recovery_codes', $account->recovery_codes) }}</textarea>
                                    @error('recovery_codes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="start_date" class="form-label">Ngày kích hoạt</label>
                                        <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                            id="start_date" name="start_date"
                                            value="{{ old('start_date', $account->start_date?->format('Y-m-d')) }}">
                                        @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="end_date" class="form-label">Ngày hết hạn</label>
                                        <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                            id="end_date" name="end_date"
                                            value="{{ old('end_date', $account->end_date?->format('Y-m-d')) }}">
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
                                            id="custom_duration" min="1" placeholder="Nhập số">
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
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="active" {{ old('status', $account->status) == 'active' ? 'selected' : '' }}>
                                        🟢 Đang hoạt động
                                    </option>
                                    <option value="expired" {{ old('status', $account->status) == 'expired' ? 'selected' : '' }}>
                                        🔴 Đã hết hạn
                                    </option>
                                    <option value="sold" {{ old('status', $account->status) == 'sold' ? 'selected' : '' }}>
                                        🔵 Đã bán
                                    </option>
                                    <option value="reserved" {{ old('status', $account->status) == 'reserved' ? 'selected' : '' }}>
                                        🟡 Đã đặt trước
                                    </option>
                                    <option value="suspended" {{ old('status', $account->status) == 'suspended' ? 'selected' : '' }}>
                                        ⚫ Tạm ngưng
                                    </option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">Khả dụng</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_available"
                                        name="is_available" value="1"
                                        {{ old('is_available', $account->is_available) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_available">
                                        Tài khoản còn khả dụng (chưa bán, còn dùng được)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                id="notes" name="notes" rows="3"
                                placeholder="Ghi chú thêm về tài khoản này...">{{ old('notes', $account->notes) }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Info box -->
                        <div class="alert alert-light">
                            <div class="row text-muted small">
                                <div class="col-md-6">
                                    <i class="fas fa-clock me-1"></i> Tạo: {{ $account->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="col-md-6">
                                    <i class="fas fa-edit me-1"></i> Cập nhật: {{ $account->updated_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.resources.show', $resource) }}" class="btn btn-secondary">
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
    function generatePassword() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
        let password = '';
        for (let i = 0; i < 16; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('password').value = password;
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            const toast = document.createElement('div');
            toast.className = 'position-fixed bottom-0 end-0 p-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
            <div class="toast show bg-success text-white" role="alert">
                <div class="toast-body">
                    <i class="fas fa-check me-2"></i>Đã copy!
                </div>
            </div>
        `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        });
    }

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