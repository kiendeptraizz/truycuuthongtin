@extends('layouts.admin')

@section('title', 'Logout All Devices - ' . $email)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">
                            <i class="fas fa-sign-out-alt me-2 text-danger"></i>
                            Logout All Devices
                        </h5>
                        <small class="text-muted">Tài khoản: {{ $email }}</small>
                    </div>
                    <div>
                        <a href="{{ route('admin.shared-accounts.show', $email) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Cảnh báo -->
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Cảnh báo:</strong> Thao tác này sẽ ghi nhận việc logout tất cả thiết bị của tài khoản dùng chung. 
                        Đây chỉ là tính năng tracking/logging, không thực hiện logout thật trên các platform.
                    </div>

                    <!-- Thông tin khách hàng bị ảnh hưởng -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-users me-2"></i>
                                Khách hàng bị ảnh hưởng ({{ $affectedCustomers->count() }} người)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tên khách hàng</th>
                                            <th>Email</th>
                                            <th>Điện thoại</th>
                                            <th>Gói dịch vụ</th>
                                            <th>Hết hạn</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($affectedCustomers as $customer)
                                        <tr>
                                            <td>{{ $customer['name'] }}</td>
                                            <td>{{ $customer['email'] }}</td>
                                            <td>{{ $customer['phone'] ?? 'N/A' }}</td>
                                            <td>{{ $customer['service_name'] }}</td>
                                            <td>
                                                @php
                                                    $expiresAt = \Carbon\Carbon::parse($customer['expires_at']);
                                                    $isExpired = $expiresAt->isPast();
                                                    $isExpiringSoon = $expiresAt->diffInDays(now()) <= 5 && !$isExpired;
                                                @endphp
                                                <span class="badge {{ $isExpired ? 'bg-danger' : ($isExpiringSoon ? 'bg-warning' : 'bg-success') }}">
                                                    {{ $expiresAt->format('d/m/Y') }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Form logout -->
                    <form action="{{ route('admin.shared-accounts.logout', $email) }}" method="POST" id="logoutForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reason" class="form-label">Lý do logout</label>
                                    <select class="form-select @error('reason') is-invalid @enderror" name="reason" id="reason">
                                        <option value="">-- Chọn lý do --</option>
                                        <option value="Thành viên hết hạn" {{ old('reason') == 'Thành viên hết hạn' ? 'selected' : '' }}>Thành viên hết hạn</option>
                                        <option value="Bảo mật tài khoản" {{ old('reason') == 'Bảo mật tài khoản' ? 'selected' : '' }}>Bảo mật tài khoản</option>
                                        <option value="Yêu cầu khách hàng" {{ old('reason') == 'Yêu cầu khách hàng' ? 'selected' : '' }}>Yêu cầu khách hàng</option>
                                        <option value="Bảo trì hệ thống" {{ old('reason') == 'Bảo trì hệ thống' ? 'selected' : '' }}>Bảo trì hệ thống</option>
                                        <option value="Khác" {{ old('reason') == 'Khác' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                    @error('reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú thêm</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      name="notes" id="notes" rows="3" 
                                      placeholder="Nhập ghi chú thêm về việc logout này...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input @error('confirm_logout') is-invalid @enderror" 
                                       type="checkbox" name="confirm_logout" id="confirm_logout" value="1">
                                <label class="form-check-label" for="confirm_logout">
                                    <strong>Tôi xác nhận thực hiện logout all devices cho tài khoản này</strong>
                                </label>
                                @error('confirm_logout')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger btn-lg" id="submitBtn" disabled>
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout All Devices ({{ $affectedCustomers->count() }} khách hàng)
                            </button>
                            <a href="{{ route('admin.shared-accounts.show', $email) }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>
                                Hủy bỏ
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmCheckbox = document.getElementById('confirm_logout');
    const submitBtn = document.getElementById('submitBtn');
    
    confirmCheckbox.addEventListener('change', function() {
        submitBtn.disabled = !this.checked;
    });
    
    // Confirmation dialog
    document.getElementById('logoutForm').addEventListener('submit', function(e) {
        const affectedCount = {{ $affectedCustomers->count() }};
        const confirmMessage = `Bạn có chắc chắn muốn thực hiện logout all devices cho tài khoản này không?\n\nThao tác này sẽ ảnh hưởng đến ${affectedCount} khách hàng.`;

        if (!confirm(confirmMessage)) {
            e.preventDefault();
        } else {
            // Disable submit button to prevent double submission
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
        }
    });
});
</script>
@endsection
