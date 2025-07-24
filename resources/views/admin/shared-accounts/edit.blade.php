@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users-cog me-2"></i>
                            Chỉnh sửa thông tin tài khoản dùng chung
                        </h5>
                        <small class="text-muted">Email: {{ $email }}</small>
                    </div>
                    <div>
                        <a href="{{ route('admin.shared-accounts.show', $email) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Thông tin khách hàng sử dụng -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Thông tin khách hàng đang sử dụng:</h6>
                                <div class="row">
                                    @foreach($allServices as $service)
                                        <div class="col-md-6 col-lg-4 mb-2">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <strong>{{ $service->customer->name ?? 'N/A' }}</strong><br>
                                                    <small class="text-muted">{{ $service->servicePackage->name ?? 'N/A' }}</small><br>
                                                    <small class="badge bg-{{ $service->status == 'active' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($service->status) }}
                                                    </small>
                                                </div>
                                                @if($service->customer && $service->customer->customer_code)
                                                    <span class="badge bg-primary ms-2" title="Mã khách hàng">
                                                        <i class="fas fa-id-badge me-1"></i>{{ $service->customer->customer_code }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.shared-accounts.update', $email) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Cột trái: Thông tin đăng nhập -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-key me-2"></i>Thông tin đăng nhập</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Email đăng nhập</label>
                                            <input type="text" class="form-control" value="{{ $email }}" disabled>
                                            <small class="text-muted">Email không thể thay đổi</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="login_password" class="form-label">Mật khẩu</label>
                                            <div class="input-group">
                                                <input type="password" 
                                                       class="form-control" 
                                                       id="login_password" 
                                                       name="login_password" 
                                                       value="{{ old('login_password', $sharedAccountInfo->login_password) }}"
                                                       placeholder="Nhập mật khẩu mới">
                                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('login_password')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted">Để trống nếu không muốn thay đổi</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="password_expires_at" class="form-label">Ngày hết hạn tài khoản</label>
                                            <input type="date" 
                                                   class="form-control" 
                                                   id="password_expires_at" 
                                                   name="password_expires_at" 
                                                   value="{{ old('password_expires_at', $sharedAccountInfo->password_expires_at ? $sharedAccountInfo->password_expires_at->format('Y-m-d') : '') }}">
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       class="form-check-input" 
                                                       id="is_password_shared" 
                                                       name="is_password_shared" 
                                                       value="1" 
                                                       {{ old('is_password_shared', $sharedAccountInfo->is_password_shared) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_password_shared">
                                                    Mật khẩu đã được chia sẻ với khách hàng
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Thông tin 2FA -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Xác thực 2 yếu tố (2FA)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="two_factor_code" class="form-label">Mã 2FA/Secret Key</label>
                                            <div class="input-group">
                                                <input type="password" 
                                                       class="form-control" 
                                                       id="two_factor_code" 
                                                       name="two_factor_code" 
                                                       value="{{ old('two_factor_code', $sharedAccountInfo->two_factor_code) }}"
                                                       placeholder="Nhập mã 2FA">
                                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('two_factor_code')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            @if($sharedAccountInfo->two_factor_updated_at)
                                                <small class="text-muted">
                                                    Cập nhật lần cuối: {{ $sharedAccountInfo->two_factor_updated_at->format('d/m/Y H:i') }}
                                                </small>
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <label for="recovery_codes" class="form-label">Mã khôi phục</label>
                                            <textarea class="form-control" 
                                                      id="recovery_codes" 
                                                      name="recovery_codes" 
                                                      rows="5" 
                                                      placeholder="Nhập mã khôi phục, mỗi mã trên một dòng">{{ old('recovery_codes', is_array($sharedAccountInfo->recovery_codes) ? implode("\n", $sharedAccountInfo->recovery_codes) : '') }}</textarea>
                                            <small class="text-muted">Mỗi mã khôi phục trên một dòng</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cột phải: Ghi chú và hướng dẫn -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Ghi chú nội bộ</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="shared_account_notes" class="form-label">Ghi chú tài khoản dùng chung</label>
                                            <textarea class="form-control" 
                                                      id="shared_account_notes" 
                                                      name="shared_account_notes" 
                                                      rows="6" 
                                                      placeholder="Ghi chú riêng về tài khoản này (không gửi cho khách hàng)">{{ old('shared_account_notes', $sharedAccountInfo->shared_account_notes) }}</textarea>
                                            <small class="text-muted">Ghi chú này chỉ dành cho nội bộ sử dụng</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Thông tin gửi khách hàng</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="customer_instructions" class="form-label">Hướng dẫn/Ghi chú cho khách hàng</label>
                                            <textarea class="form-control" 
                                                      id="customer_instructions" 
                                                      name="customer_instructions" 
                                                      rows="8" 
                                                      placeholder="Nhập hướng dẫn sử dụng, lưu ý đặc biệt để gửi cho khách hàng...">{{ old('customer_instructions', $sharedAccountInfo->customer_instructions) }}</textarea>
                                            <small class="text-muted">Thông tin này có thể được gửi cho khách hàng</small>
                                        </div>

                                        <div class="alert alert-warning">
                                            <small>
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                <strong>Lưu ý bảo mật:</strong> 
                                                Hãy cẩn thận khi chia sẻ thông tin nhạy cảm như mật khẩu và mã 2FA với khách hàng.
                                                Nên sử dụng kênh liên lạc an toàn.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.shared-accounts.show', $email) }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Hủy
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Lưu thông tin
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

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection
