@extends('layouts.admin')

@section('title', 'Bật xác thực 2 lớp (2FA)')
@section('page-title', 'Bật 2FA')

@section('content')
<div class="container py-4" style="max-width: 640px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Bật xác thực 2 lớp (TOTP)</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>Bước 1:</strong> Cài app <a href="https://apps.apple.com/app/google-authenticator/id388497605" target="_blank">Google Authenticator</a> /
                <a href="https://authy.com/download/" target="_blank">Authy</a> / Microsoft Authenticator trên điện thoại.
            </div>

            <p class="mb-2"><strong>Bước 2:</strong> Quét QR bên dưới HOẶC nhập thủ công secret:</p>
            <div class="text-center mb-3">
                <img src="{{ $qrUrl }}" alt="2FA QR" class="border rounded" style="max-width: 240px;">
            </div>
            <div class="text-center mb-4">
                <small class="text-muted">Secret thủ công:</small><br>
                <code class="user-select-all" style="font-size: 1rem;">{{ $secret }}</code>
            </div>

            <p><strong>Bước 3:</strong> Nhập mã 6 chữ số hiện trên app để xác nhận:</p>
            <form method="POST" action="{{ route('two-factor.enable') }}">
                @csrf
                <div class="mb-3">
                    <input type="text" name="code" class="form-control form-control-lg text-center @error('code') is-invalid @enderror"
                           inputmode="numeric" maxlength="6" autocomplete="one-time-code"
                           placeholder="123456" autofocus required>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-check me-1"></i>Xác nhận và bật 2FA
                </button>
                <a href="{{ route('two-factor.settings') }}" class="btn btn-link w-100 mt-2">Huỷ</a>
            </form>
        </div>
    </div>
</div>
@endsection
