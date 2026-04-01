<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Lỗi hệ thống</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fff5f5 0%, #fef2f2 50%, #fdf4ff 100%);
            padding: 1rem;
        }
        .container { text-align: center; max-width: 480px; }
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .error-title { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; }
        .error-desc { color: #64748b; margin-bottom: 2rem; line-height: 1.6; }
        .btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.75rem 1.5rem; border-radius: 12px; text-decoration: none;
            font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.35);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102, 126, 234, 0.45); }
        @media (max-width: 480px) { .error-code { font-size: 5rem; } .error-title { font-size: 1.25rem; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">500</div>
        <h1 class="error-title">Lỗi hệ thống</h1>
        <p class="error-desc">Đã xảy ra lỗi. Vui lòng thử lại sau hoặc liên hệ quản trị viên.</p>
        <a href="{{ route('lookup.index') }}" class="btn btn-primary">Về Trang Tra Cứu</a>
    </div>
</body>
</html>
