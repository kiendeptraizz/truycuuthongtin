@extends('layouts.admin')

@section('title', 'Thêm Tài khoản Zalo')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="mb-0">Thêm Tài khoản Zalo</h2>
        <p class="text-muted mb-0">Thêm tài khoản Zalo mới để gửi tin nhắn</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.zalo.accounts.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Tên tài khoản <span class="text-danger">*</span></label>
                            <input type="text" name="account_name" class="form-control @error('account_name') is-invalid @enderror"
                                value="{{ old('account_name') }}" required>
                            @error('account_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email hoặc Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="email_or_phone" class="form-control @error('email_or_phone') is-invalid @enderror"
                                value="{{ old('email_or_phone') }}" required>
                            @error('email_or_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            <small class="text-muted">Mật khẩu sẽ được mã hóa khi lưu</small>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Access Token</label>
                            <textarea name="access_token" class="form-control @error('access_token') is-invalid @enderror" rows="3">{{ old('access_token') }}</textarea>
                            <small class="text-muted">Token để truy cập Zalo API (nếu có)</small>
                            @error('access_token')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giới hạn tin nhắn/ngày <span class="text-danger">*</span></label>
                                <input type="number" name="daily_message_limit" class="form-control @error('daily_message_limit') is-invalid @enderror"
                                    value="{{ old('daily_message_limit', 100) }}" min="1" max="1000" required>
                                @error('daily_message_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="blocked" {{ old('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                                    <option value="error" {{ old('status') === 'error' ? 'selected' : '' }}>Error</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu tài khoản
                            </button>
                            <a href="{{ route('admin.zalo.accounts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h5 class="mb-3">💡 Hướng dẫn</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            Nhập tên dễ nhớ cho tài khoản
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            Email/SĐT dùng để đăng nhập Zalo
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            Nên giới hạn 50-100 tin/ngày để tránh spam
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            Token có thể lấy từ Zalo Developer
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection