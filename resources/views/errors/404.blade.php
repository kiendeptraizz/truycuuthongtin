<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy trang</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0f4ff 0%, #faf5ff 50%, #fdf4ff 100%);
            padding: 1rem;
        }
        .container {
            text-align: center;
            max-width: 480px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.75rem;
        }
        .error-desc {
            color: #64748b;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.35);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.45);
        }
        .btn-outline {
            background: white;
            color: #667eea;
            border: 2px solid #e2e8f0;
            margin-left: 0.75rem;
        }
        .btn-outline:hover {
            border-color: #667eea;
        }
        @media (max-width: 480px) {
            .error-code { font-size: 5rem; }
            .error-title { font-size: 1.25rem; }
            .btn { display: flex; justify-content: center; margin-bottom: 0.5rem; }
            .btn-outline { margin-left: 0; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">404</div>
        <h1 class="error-title">Trang không tồn tại</h1>
        <p class="error-desc">Trang bạn tìm kiếm không tồn tại hoặc đã bị di chuyển.</p>
        <div>
            <a href="{{ route('lookup.index') }}" class="btn btn-primary">
                Tra Cứu Thông Tin
            </a>
            <a href="javascript:history.back()" class="btn btn-outline">
                Quay Lại
            </a>
        </div>
    </div>
</body>
</html>
