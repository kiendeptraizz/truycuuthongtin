@extends('layouts.admin')

@section('title', 'Cài đặt 2FA')
@section('page-title', 'Cài đặt 2FA')

@section('content')
<div class="container py-4" style="max-width: 640px;">
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('info')) <div class="alert alert-info">{{ session('info') }}</div> @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Xác thực 2 lớp (2FA)</h5>
        </div>
        <div class="card-body">
            @if($user->hasTwoFactorEnabled())
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-1"></i>
                    <strong>2FA đang BẬT</strong> — bật từ {{ $user->two_factor_enabled_at->format('H:i d/m/Y') }}.
                </div>
                <p class="text-muted small">
                    Còn <strong>{{ count($user->two_factor_recovery_codes ?? []) }}</strong> mã khôi phục.
                </p>

                <hr>
                <h6 class="text-danger">Tắt 2FA</h6>
                <p class="text-muted small">Cảnh báo: tài khoản sẽ chỉ bảo vệ bằng mật khẩu sau khi tắt.</p>
                <form method="POST" action="{{ route('two-factor.disable') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu để xác nhận</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Tắt 2FA? Tài khoản sẽ kém an toàn hơn.')">
                        <i class="fas fa-times me-1"></i>Tắt 2FA
                    </button>
                </form>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>2FA đang TẮT</strong> — tài khoản chỉ được bảo vệ bằng mật khẩu.
                </div>
                <p>Bật 2FA giúp bảo vệ tài khoản nếu mật khẩu bị lộ. Cần app authenticator (Google Authenticator / Authy / Microsoft Authenticator).</p>
                <a href="{{ route('two-factor.setup') }}" class="btn btn-primary">
                    <i class="fas fa-shield-alt me-1"></i>Bật 2FA ngay
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
