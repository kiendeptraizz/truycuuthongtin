@extends('layouts.admin')

@section('title', 'Cấu hình trang chủ')
@section('page-title', 'Cấu hình trang chủ')

@push('styles')
<style>
    .form-wrapper {
        max-width: 800px;
        margin: 0 auto;
    }
    .form-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .form-card-header {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        padding: 24px 32px;
        color: #fff;
    }
    .form-card-header h2 {
        margin: 0 0 4px 0;
        font-size: 1.5rem;
        font-weight: 600;
    }
    .form-card-header p {
        margin: 0;
        opacity: 0.9;
        font-size: 0.95rem;
    }
    .form-card-body { padding: 32px; }
    .stat-row {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 18px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 16px;
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .stat-row-info { flex: 1; }
    .stat-row-info .stat-label {
        font-weight: 600;
        color: #334155;
        font-size: 1rem;
        margin-bottom: 2px;
    }
    .stat-row-info .stat-real {
        font-size: 0.875rem;
        color: #64748b;
    }
    .stat-real strong { color: #6366f1; }
    .stat-row-input {
        width: 220px;
        flex-shrink: 0;
    }
    .stat-row-input .form-control { text-align: right; font-weight: 600; }
    .info-banner {
        background: #eff6ff;
        border-left: 4px solid #3b82f6;
        border-radius: 8px;
        padding: 16px 20px;
        margin-bottom: 24px;
        color: #1e3a8a;
        font-size: 0.95rem;
        line-height: 1.55;
    }
    .form-card-footer {
        background: #f8fafc;
        padding: 20px 32px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }
    @media (max-width: 576px) {
        .form-card-body, .form-card-header, .form-card-footer { padding: 20px; }
        .stat-row { flex-direction: column; align-items: stretch; }
        .stat-row-input { width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="form-wrapper">
    <div class="form-card">
        <div class="form-card-header">
            <h2><i class="fas fa-sliders-h me-2"></i>Cấu hình trang chủ</h2>
            <p>Đè số hiển thị trên stats bar trang tra cứu công khai (truycuu.io.vn)</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success m-4 mb-0">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.home-settings.update') }}">
            @csrf
            @method('PUT')

            <div class="form-card-body">
                <div class="info-banner">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Để trống</strong> = dùng số thực từ DB.
                    <strong>Có giá trị</strong> = ưu tiên hiển thị số đó trên trang chủ (boost FOMO/uy tín).
                    Sau khi lưu, cache trang chủ sẽ được clear ngay lập tức.
                </div>

                <div class="stat-row">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-row-info">
                        <div class="stat-label">Khách hàng</div>
                        <div class="stat-real">Số thực: <strong>{{ number_format($real['customers']) }}</strong></div>
                    </div>
                    <div class="stat-row-input">
                        <input type="number"
                               name="customers_override"
                               value="{{ old('customers_override', $settings->customers_override) }}"
                               min="0"
                               max="99999999"
                               placeholder="(tự động)"
                               class="form-control @error('customers_override') is-invalid @enderror">
                        @error('customers_override')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="stat-row">
                    <div class="stat-icon"><i class="fas fa-cube"></i></div>
                    <div class="stat-row-info">
                        <div class="stat-label">Dịch vụ đang hoạt động</div>
                        <div class="stat-real">Số thực: <strong>{{ number_format($real['services']) }}</strong></div>
                    </div>
                    <div class="stat-row-input">
                        <input type="number"
                               name="services_override"
                               value="{{ old('services_override', $settings->services_override) }}"
                               min="0"
                               max="99999999"
                               placeholder="(tự động)"
                               class="form-control @error('services_override') is-invalid @enderror">
                        @error('services_override')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="stat-row">
                    <div class="stat-icon"><i class="fas fa-box-open"></i></div>
                    <div class="stat-row-info">
                        <div class="stat-label">Gói dịch vụ</div>
                        <div class="stat-real">Số thực: <strong>{{ number_format($real['packages']) }}</strong></div>
                    </div>
                    <div class="stat-row-input">
                        <input type="number"
                               name="packages_override"
                               value="{{ old('packages_override', $settings->packages_override) }}"
                               min="0"
                               max="99999999"
                               placeholder="(tự động)"
                               class="form-control @error('packages_override') is-invalid @enderror">
                        @error('packages_override')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-card-footer">
                <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-secondary">
                    <i class="fas fa-external-link-alt me-2"></i>Xem trang chủ
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Lưu cấu hình
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
