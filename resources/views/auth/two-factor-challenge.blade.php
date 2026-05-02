<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Xác thực 2FA</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/fontawesome/all.min.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .card {
            max-width: 440px;
            width: 100%;
            border: none;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
        }
        .icon-shield {
            width: 70px; height: 70px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            color: white; font-size: 2rem;
        }
        .form-control-lg {
            text-align: center;
            font-size: 1.4rem;
            letter-spacing: 0.4rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="card p-4">
        <div class="text-center mb-4">
            <div class="icon-shield mb-3"><i class="fas fa-shield-alt"></i></div>
            <h4 class="fw-bold">Xác thực 2 lớp</h4>
            <p class="text-muted small">Mở app authenticator để lấy mã 6 chữ số</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger small">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('two-factor.verify') }}">
            @csrf
            <div class="mb-3">
                <input type="text" name="code" class="form-control form-control-lg"
                       inputmode="numeric" autocomplete="one-time-code"
                       placeholder="000000" maxlength="20" autofocus required>
                <small class="text-muted d-block text-center mt-2">
                    Hoặc nhập 1 trong các <strong>mã khôi phục</strong> 10 ký tự nếu mất authenticator
                </small>
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-lg">
                <i class="fas fa-unlock me-1"></i>Xác thực
            </button>
        </form>

        <div class="text-center mt-3">
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-link btn-sm text-muted">
                    <i class="fas fa-sign-out-alt me-1"></i>Đăng xuất
                </button>
            </form>
        </div>
    </div>
</body>
</html>
