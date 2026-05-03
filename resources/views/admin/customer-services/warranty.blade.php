@extends('layouts.admin')

@section('title', 'Bảo hành — ' . ($cs->order_code ?? '#' . $cs->id))
@section('page-title', 'Bảo hành đơn hàng')

@section('content')
<div class="container-fluid py-3" style="max-width: 960px;">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-shield-alt text-info me-2"></i>
                Bảo hành đơn hàng
            </h4>
            <div class="text-muted small">
                @if($cs->order_code)<code class="text-success">{{ $cs->order_code }}</code> · @endif
                {{ $cs->servicePackage->name ?? '?' }} ·
                KH: {{ $cs->customer->name ?? '?' }} ·
                Email TK hiện tại: <code>{{ $cs->login_email ?? '—' }}</code>
            </div>
        </div>
        <a href="{{ route('admin.customer-services.show', $cs) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Quay lại
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{!! session('success') !!}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="row g-3">
        {{-- Form thêm bảo hành mới --}}
        <div class="col-lg-6">
            <div class="card shadow-sm border-info">
                <div class="card-header bg-info bg-opacity-25">
                    <h6 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Ghi nhận bảo hành mới</h6>
                </div>
                <div class="card-body">
                    @if($cs->status === 'cancelled')
                        <div class="alert alert-secondary">Đơn đã huỷ — không thể bảo hành.</div>
                    @else
                        <form method="POST" action="{{ route('admin.customer-services.warranty.store', $cs) }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-envelope me-1 text-primary"></i>
                                    TK thay (email mới) <span class="text-muted small">— bỏ trống nếu không đổi</span>
                                </label>
                                <input type="email"
                                    class="form-control @error('replacement_email') is-invalid @enderror"
                                    name="replacement_email"
                                    placeholder="vd: newaccount@gmail.com"
                                    value="{{ old('replacement_email') }}">
                                @error('replacement_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Sẽ ghi đè <code>login_email</code> của đơn này.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-key me-1 text-primary"></i>
                                    Mật khẩu TK mới <span class="text-muted small">— tuỳ chọn</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('replacement_password') is-invalid @enderror"
                                    name="replacement_password"
                                    placeholder="vd: Password123!"
                                    value="{{ old('replacement_password') }}">
                                @error('replacement_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-clock me-1 text-success"></i>
                                    Gia hạn thêm <span class="text-muted small">— ngày, bỏ trống = 0</span>
                                </label>
                                <div class="input-group">
                                    <input type="number"
                                        class="form-control @error('extended_days') is-invalid @enderror"
                                        name="extended_days"
                                        min="0" max="3650"
                                        placeholder="0"
                                        value="{{ old('extended_days') }}">
                                    <span class="input-group-text">ngày</span>
                                </div>
                                @error('extended_days')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                <div class="form-text">
                                    Cộng vào <code>expires_at</code> hiện tại
                                    @if($cs->expires_at)
                                        ({{ $cs->expires_at->format('d/m/Y') }})
                                    @endif.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-comment-dots me-1 text-info"></i>
                                    Ghi chú bảo hành <span class="text-danger">*</span>
                                </label>
                                <textarea
                                    class="form-control @error('note') is-invalid @enderror"
                                    name="note" rows="4"
                                    placeholder="VD: Khách báo TK lỗi đăng nhập, đã đổi sang TK mới ngày 03/05. / Khách báo lỗi tạm thời, đã hỗ trợ qua Zalo, không cần đổi TK."
                                    required>{{ old('note') }}</textarea>
                                @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <button type="submit" class="btn btn-info text-white">
                                <i class="fas fa-shield-alt me-1"></i>Ghi nhận bảo hành
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Lịch sử bảo hành --}}
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Lịch sử bảo hành ({{ $warranties->count() }})</h6>
                </div>
                <div class="card-body p-0">
                    @if($warranties->isEmpty())
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-shield-alt fa-2x mb-2 opacity-50"></i>
                            <div>Chưa có bảo hành nào cho đơn này.</div>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($warranties as $w)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between mb-1">
                                        <strong class="text-info">
                                            <i class="fas fa-shield-alt me-1"></i>{{ $w->getTypeLabel() }}
                                        </strong>
                                        <small class="text-muted">{{ $w->created_at->format('H:i d/m/Y') }}</small>
                                    </div>

                                    @if($w->replacement_email)
                                        <div class="small mb-1">
                                            <i class="fas fa-envelope text-primary me-1"></i>
                                            TK mới: <code>{{ $w->replacement_email }}</code>
                                            @if($w->replacement_password)
                                                · pass: <code>{{ $w->replacement_password }}</code>
                                            @endif
                                        </div>
                                    @endif

                                    @if($w->extended_days)
                                        <div class="small mb-1">
                                            <i class="fas fa-clock text-success me-1"></i>
                                            Gia hạn thêm <strong>{{ $w->extended_days }}</strong> ngày
                                        </div>
                                    @endif

                                    <div class="mb-1" style="white-space: pre-wrap;">{{ $w->note }}</div>

                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $w->actor_label ?: 'Hệ thống' }}
                                        @if($w->actor_type)
                                            <span class="badge bg-light text-dark ms-1">{{ $w->actor_type }}</span>
                                        @endif
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
