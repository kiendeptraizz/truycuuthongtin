@extends('layouts.admin')

@section('title', 'Mã khôi phục 2FA')
@section('page-title', 'Mã khôi phục 2FA')

@section('content')
<div class="container py-4" style="max-width: 640px;">
    <div class="card shadow-sm border-success">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Đã bật 2FA thành công!</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <strong>⚠️ QUAN TRỌNG:</strong> Lưu lại 8 mã khôi phục dưới đây ở nơi an toàn.
                <br>Mỗi mã chỉ dùng được <b>1 lần</b> để đăng nhập khi mất authenticator.
                <br>Sau khi rời trang này sẽ <b>không xem lại được</b> các mã.
            </div>

            <div class="row g-2 mb-3">
                @foreach($recoveryCodes as $code)
                    <div class="col-md-6">
                        <code class="d-block p-2 bg-light rounded user-select-all text-center" style="font-size: 1rem;">{{ $code }}</code>
                    </div>
                @endforeach
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary" onclick="copyAllCodes()">
                    <i class="fas fa-copy me-1"></i>Copy tất cả
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>In ra giấy
                </button>
                <a href="{{ route('two-factor.settings') }}" class="btn btn-success ms-auto">
                    Tôi đã lưu, tiếp tục
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function copyAllCodes() {
    const codes = @json($recoveryCodes);
    navigator.clipboard.writeText(codes.join('\n')).then(() => {
        alert('Đã copy ' + codes.length + ' mã vào clipboard. Hãy paste vào nơi lưu trữ an toàn (password manager / file mã hoá).');
    });
}
</script>
@endsection
