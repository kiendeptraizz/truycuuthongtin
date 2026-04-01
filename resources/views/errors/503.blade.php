<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảo trì hệ thống</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 50%, #fdf4ff 100%);
            padding: 1rem;
        }
        .container { text-align: center; max-width: 480px; }
        .icon { font-size: 4rem; margin-bottom: 1rem; }
        .error-title { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; }
        .error-desc { color: #64748b; margin-bottom: 2rem; line-height: 1.6; }
        .spinner {
            width: 40px; height: 40px; margin: 0 auto 1.5rem;
            border: 4px solid #e2e8f0; border-top-color: #667eea;
            border-radius: 50%; animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        @media (max-width: 480px) { .error-title { font-size: 1.25rem; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">&#128736;</div>
        <h1 class="error-title">Hệ thống đang bảo trì</h1>
        <p class="error-desc">Chúng tôi đang nâng cấp hệ thống. Vui lòng quay lại sau ít phút.</p>
        <div class="spinner"></div>
        <p style="color: #94a3b8; font-size: 0.85rem;">Trang sẽ tự động tải lại...</p>
    </div>
    <script>setTimeout(function(){ location.reload(); }, 30000);</script>
</body>
</html>
