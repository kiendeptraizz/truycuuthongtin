@extends('layouts.admin')

@section('title', 'Sửa Tài khoản Zalo')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="mb-0">Sửa Tài khoản Zalo</h2>
        <p class="text-muted mb-0">Cập nhật thông tin tài khoản {{ $account->account_name }}</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.zalo.accounts.update', $account) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Tên tài khoản <span class="text-danger">*</span></label>
                            <input type="text" name="account_name" class="form-control @error('account_name') is-invalid @enderror"
                                value="{{ old('account_name', $account->account_name) }}" required>
                            @error('account_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email hoặc Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="email_or_phone" class="form-control @error('email_or_phone') is-invalid @enderror"
                                value="{{ old('email_or_phone', $account->email_or_phone) }}" required>
                            @error('email_or_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            <small class="text-muted">Để trống nếu không muốn thay đổi</small>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Access Token</label>
                            <textarea name="access_token" class="form-control @error('access_token') is-invalid @enderror" rows="3" placeholder="Để trống nếu không muốn thay đổi">{{ old('access_token') }}</textarea>
                            @error('access_token')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giới hạn tin nhắn/ngày <span class="text-danger">*</span></label>
                                <input type="number" name="daily_message_limit" class="form-control @error('daily_message_limit') is-invalid @enderror"
                                    value="{{ old('daily_message_limit', $account->daily_message_limit) }}" min="1" max="1000" required>
                                @error('daily_message_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', $account->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $account->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="blocked" {{ old('status', $account->status) === 'blocked' ? 'selected' : '' }}>Blocked</option>
                                    <option value="error" {{ old('status', $account->status) === 'error' ? 'selected' : '' }}>Error</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $account->notes) }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật
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
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Thống kê</h5>
                    <div class="mb-3">
                        <small class="text-muted">Tin gửi hôm nay</small>
                        <h4>{{ number_format($account->messages_sent_today) }} / {{ number_format($account->daily_message_limit) }}</h4>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Ngày gửi cuối</small>
                        <p>{{ $account->last_message_date ? $account->last_message_date->format('d/m/Y') : 'Chưa có' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection