@extends('layouts.admin')

@section('title', 'Đổi mật khẩu')
@section('page-title', 'Đổi mật khẩu')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-key me-2"></i>
                        Đổi mật khẩu
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="current_password" class="form-label">
                                <i class="fas fa-lock me-1 text-muted"></i>
                                Mật khẩu hiện tại
                            </label>
                            <input type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" 
                                   name="current_password" 
                                   required 
                                   autofocus>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1 text-muted"></i>
                                Mật khẩu mới
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            <div class="form-text">Mật khẩu phải có ít nhất 8 ký tự.</div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-check-circle me-1 text-muted"></i>
                                Xác nhận mật khẩu mới
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Lưu mật khẩu mới
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4 border-warning">
                <div class="card-body">
                    <h6 class="text-warning mb-2">
                        <i class="fas fa-lightbulb me-2"></i>
                        Gợi ý mật khẩu an toàn
                    </h6>
                    <ul class="small text-muted mb-0">
                        <li>Sử dụng ít nhất 8 ký tự</li>
                        <li>Kết hợp chữ hoa, chữ thường, số và ký tự đặc biệt</li>
                        <li>Không sử dụng thông tin cá nhân dễ đoán</li>
                        <li>Không sử dụng mật khẩu đã dùng trước đó</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

