<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Yêu Cầu Hoàn Tiền - KienUnlocked</title>

    <meta name="description" content="Gửi yêu cầu hoàn tiền dịch vụ. Nhập tên, mã đơn hàng, số tài khoản và ảnh QR để nhận hoàn tiền nhanh chóng.">
    <meta name="robots" content="noindex, nofollow">

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta name="theme-color" content="#4f46e5">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/fontawesome/all.min.css') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --indigo-50: #eef2ff;
            --indigo-100: #e0e7ff;
            --indigo-500: #6366f1;
            --indigo-600: #4f46e5;
            --indigo-700: #4338ca;
            --emerald-500: #10b981;
            --emerald-600: #059669;
        }

        * { font-family: 'Inter', system-ui, -apple-system, sans-serif; }

        body {
            background: linear-gradient(135deg, #eef2ff 0%, #faf5ff 50%, #eff6ff 100%);
            min-height: 100vh;
            padding: 24px 12px 48px;
        }

        .refund-card {
            max-width: 560px;
            margin: 0 auto;
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 20px 50px -20px rgba(79, 70, 229, 0.35);
            overflow: hidden;
            border: 1px solid rgba(99, 102, 241, 0.10);
        }

        .refund-head {
            background: linear-gradient(135deg, var(--indigo-600), var(--indigo-700));
            color: #fff;
            padding: 28px 28px 24px;
            text-align: center;
        }

        .refund-head .icon-badge {
            width: 60px; height: 60px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.16);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 26px; margin-bottom: 12px;
        }

        .refund-head h1 { font-size: 1.4rem; font-weight: 800; margin: 0; }
        .refund-head p { margin: 6px 0 0; opacity: .85; font-size: .9rem; }

        .refund-body { padding: 26px 28px 30px; }

        .form-label { font-weight: 600; font-size: .9rem; color: #374151; margin-bottom: 6px; }
        .form-label .req { color: #dc2626; }

        .form-control {
            border-radius: 12px;
            border: 1.5px solid #e5e7eb;
            padding: 11px 14px;
            font-size: .95rem;
        }
        .form-control:focus {
            border-color: var(--indigo-500);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
        }

        .input-hint { font-size: .8rem; color: #9ca3af; margin-top: 5px; }

        .qr-drop {
            border: 2px dashed #c7d2fe;
            border-radius: 14px;
            background: var(--indigo-50);
            padding: 22px;
            text-align: center;
            cursor: pointer;
            transition: all .15s ease;
        }
        .qr-drop:hover { border-color: var(--indigo-500); background: var(--indigo-100); }
        .qr-drop i { font-size: 28px; color: var(--indigo-600); }
        .qr-drop .qr-text { font-weight: 600; color: var(--indigo-700); margin-top: 8px; font-size: .9rem; }
        .qr-drop .qr-sub { font-size: .78rem; color: #9ca3af; margin-top: 2px; }

        #qrPreviewWrap { margin-top: 12px; text-align: center; display: none; }
        #qrPreview {
            max-width: 220px; max-height: 220px;
            border-radius: 14px; border: 1px solid #e5e7eb;
            box-shadow: 0 8px 20px -10px rgba(0,0,0,.2);
        }
        .qr-clear { font-size: .82rem; color: #dc2626; cursor: pointer; margin-top: 8px; display: inline-block; }

        .btn-submit {
            background: linear-gradient(135deg, var(--indigo-600), var(--indigo-700));
            color: #fff; font-weight: 700; border: none;
            border-radius: 13px; padding: 13px; width: 100%;
            font-size: 1rem; margin-top: 6px;
            box-shadow: 0 12px 24px -12px rgba(79, 70, 229, .8);
            transition: transform .1s ease;
        }
        .btn-submit:hover { transform: translateY(-1px); color: #fff; }
        .btn-submit:active { transform: translateY(0); }

        .success-box {
            text-align: center; padding: 8px 4px 4px;
        }
        .success-box .check {
            width: 76px; height: 76px; border-radius: 50%;
            background: #dcfce7; color: var(--emerald-600);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 38px; margin-bottom: 14px;
        }
        .code-pill {
            display: inline-block; margin-top: 6px;
            background: var(--indigo-50); color: var(--indigo-700);
            border: 1.5px dashed var(--indigo-500);
            font-weight: 800; letter-spacing: 1px;
            padding: 8px 18px; border-radius: 12px; font-size: 1.15rem;
        }

        .foot-link { text-align: center; margin-top: 18px; font-size: .85rem; }
        .foot-link a { color: var(--indigo-600); text-decoration: none; font-weight: 600; }
    </style>
</head>

<body>
    <div class="refund-card">
        <div class="refund-head">
            <div class="icon-badge"><i class="fas fa-hand-holding-usd"></i></div>
            <h1>Yêu Cầu Hoàn Tiền</h1>
            <p>Điền thông tin bên dưới, bộ phận CSKH sẽ xử lý hoàn tiền cho bạn sớm nhất.</p>
        </div>

        <div class="refund-body">
            @if(session('refund_success'))
                {{-- Màn hình xác nhận đã gửi --}}
                <div class="success-box">
                    <div class="check"><i class="fas fa-check"></i></div>
                    <h2 style="font-size:1.25rem; font-weight:800; color:#111827;">Đã gửi yêu cầu thành công!</h2>
                    <p class="text-muted" style="font-size:.92rem;">
                        Vui lòng lưu lại mã theo dõi bên dưới để đối chiếu khi cần:
                    </p>
                    <div class="code-pill">{{ session('refund_success') }}</div>
                    <p class="text-muted" style="font-size:.85rem; margin-top:16px;">
                        CSKH sẽ kiểm tra và tiến hành hoàn tiền vào tài khoản/QR bạn đã cung cấp.
                        Xin cảm ơn bạn đã tin tưởng dịch vụ!
                    </p>
                    <a href="{{ route('refund.create') }}" class="btn-submit d-inline-block" style="width:auto; padding:11px 26px; text-decoration:none; margin-top:10px;">
                        <i class="fas fa-plus me-2"></i>Gửi yêu cầu khác
                    </a>
                </div>
            @else
                @if($errors->any())
                    <div class="alert alert-danger" style="border-radius:12px; font-size:.88rem;">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        Vui lòng kiểm tra lại thông tin:
                        <ul class="mb-0 mt-1 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('refund.store') }}" method="POST" enctype="multipart/form-data" id="refundForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Tên khách hàng <span class="req">*</span></label>
                        <input type="text" name="customer_name" class="form-control"
                               value="{{ old('customer_name') }}" placeholder="Nhập họ tên của bạn" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mã đơn hàng cần hoàn tiền <span class="req">*</span></label>
                        <input type="text" name="order_code" class="form-control"
                               value="{{ old('order_code') }}" placeholder="VD: DH-260720-006" required>
                        <div class="input-hint">Mã đơn có dạng DH-XXXXXX (xem trên hoá đơn / tin nhắn đặt hàng).</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số tài khoản nhận hoàn tiền <span class="req">*</span></label>
                        <input type="text" name="bank_account" class="form-control"
                               value="{{ old('bank_account') }}" placeholder="Nhập số tài khoản ngân hàng" required
                               inputmode="numeric">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Ảnh mã QR nhận tiền <span class="req">*</span></label>
                        <div class="qr-drop" id="qrDrop">
                            <i class="fas fa-qrcode"></i>
                            <div class="qr-text">Bấm để chọn / chụp ảnh mã QR</div>
                            <div class="qr-sub">JPG, PNG, WEBP · tối đa 5MB</div>
                        </div>
                        <input type="file" name="qr_image" id="qrInput" accept="image/*" capture="environment"
                               class="d-none" required>
                        <div id="qrPreviewWrap">
                            <img id="qrPreview" src="" alt="Xem trước QR">
                            <div><span class="qr-clear" id="qrClear"><i class="fas fa-times me-1"></i>Chọn ảnh khác</span></div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-paper-plane me-2"></i>Gửi yêu cầu hoàn tiền
                    </button>
                </form>

                <div class="foot-link">
                    <a href="{{ route('lookup.index') }}"><i class="fas fa-search me-1"></i>Tra cứu dịch vụ của tôi</a>
                </div>
            @endif
        </div>
    </div>

    <script>
        const qrDrop = document.getElementById('qrDrop');
        const qrInput = document.getElementById('qrInput');
        const qrPreviewWrap = document.getElementById('qrPreviewWrap');
        const qrPreview = document.getElementById('qrPreview');
        const qrClear = document.getElementById('qrClear');

        if (qrDrop) {
            qrDrop.addEventListener('click', () => qrInput.click());

            qrInput.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = e => {
                    qrPreview.src = e.target.result;
                    qrPreviewWrap.style.display = 'block';
                    qrDrop.style.display = 'none';
                };
                reader.readAsDataURL(file);
            });

            qrClear.addEventListener('click', function () {
                qrInput.value = '';
                qrPreview.src = '';
                qrPreviewWrap.style.display = 'none';
                qrDrop.style.display = 'block';
            });

            // Chống double submit
            document.getElementById('refundForm').addEventListener('submit', function () {
                const btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...';
            });
        }
    </script>
</body>

</html>
