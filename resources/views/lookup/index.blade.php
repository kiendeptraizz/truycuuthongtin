<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tra cứu dịch vụ - KienUnlocked</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Horizontal Scroll Utilities CSS -->
    <link href="{{ asset('css/horizontal-scroll-utilities.css') }}" rel="stylesheet">
    <!-- Global Horizontal Scroll CSS -->
    <link href="{{ asset('css/global-horizontal-scroll.css') }}" rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            --dark-overlay: rgba(0, 0, 0, 0.1);
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.18);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--primary-gradient);
            min-height: 100vh;
            position: relative;
            overflow-x: auto !important;
            overflow-y: visible !important;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
        }

        /* Animated Background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: var(--primary-gradient);
        }

        .bg-animation::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%"><stop offset="0%" stop-color="%23667eea" stop-opacity="0.1"/><stop offset="100%" stop-color="%23764ba2" stop-opacity="0"/></radialGradient></defs><circle cx="50%" cy="50%" r="50%" fill="url(%23a)"/></svg>') center/cover;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        /* Floating Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: particle-float 15s infinite linear;
        }

        @keyframes particle-float {
            0% {
                transform: translateY(100vh) translateX(-50px);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                transform: translateY(-100px) translateX(50px);
                opacity: 0;
            }
        }

        /* Main Container */
        .lookup-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1rem;
            position: relative;
        }

        /* Brand Header */
        .brand-header {
            text-align: center;
            color: white;
            margin-bottom: 3rem;
            animation: fadeInUp 1s ease-out;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: logoGlow 2s ease-in-out infinite alternate;
        }

        @keyframes logoGlow {
            0% {
                box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
            }

            100% {
                box-shadow: 0 0 40px rgba(255, 255, 255, 0.4);
            }
        }

        .brand-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            letter-spacing: -0.02em;
        }

        .brand-subtitle {
            font-size: 1.25rem;
            font-weight: 400;
            opacity: 0.9;
            letter-spacing: 0.02em;
        }

        /* Glass Card Effect */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.3);
        }

        /* Search Card */
        .search-card {
            padding: 2.5rem;
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease-out 0.3s both;
        }

        .search-form {
            position: relative;
        }

        .search-input-wrapper {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .search-input {
            width: 100%;
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 500;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            transition: all 0.3s ease;
            outline: none;
        }

        .search-input:focus {
            border-color: rgba(255, 255, 255, 0.8);
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .search-input::placeholder {
            color: #666;
            font-weight: 400;
        }

        .search-btn {
            width: 100%;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
            color: #667eea;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .search-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .search-btn:hover::before {
            left: 100%;
        }

        .search-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 1);
        }

        /* Alert Styles */
        .alert-custom {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 193, 7, 0.3);
            border-radius: 16px;
            color: white;
            padding: 1rem 1.5rem;
            margin-top: 1.5rem;
        }

        /* Result Card */
        .result-card {
            animation: fadeInUp 1s ease-out 0.6s both;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .customer-header {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.05) 100%);
            backdrop-filter: blur(20px);
            padding: 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .customer-avatar {
            width: 80px;
            height: 80px;
            background: var(--success-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin: 0 auto 1rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .customer-name {
            font-size: 1.75rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .customer-details {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .customer-detail {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 12px;
            color: white;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
        }

        .services-count {
            background: var(--warning-gradient);
            padding: 1rem 1.5rem;
            border-radius: 16px;
            text-align: center;
            color: white;
            margin-top: 1rem;
        }

        .services-count-number {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
        }

        /* Service Items */
        .services-container {
            padding: 0;
        }

        .service-item {
            background: rgba(255, 255, 255, 0.95);
            margin: 1rem;
            padding: 1.5rem;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .service-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            transition: left 0.5s;
        }

        .service-item:hover::before {
            left: 100%;
        }

        .service-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
        }

        .service-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .service-category {
            color: #667eea;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .service-email {
            background: #f8f9fa;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            border-left: 4px solid #667eea;
            margin: 1rem 0;
        }

        .service-email-label {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 0.25rem;
        }

        .service-email-value {
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            font-weight: 600;
            color: #333;
            word-break: break-all;
        }

        .service-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .service-expiry {
            color: #666;
            font-size: 0.9rem;
        }

        /* Status Badges */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .status-expiring {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .status-expired {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #666;
        }

        .empty-icon {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 1rem;
        }

        /* Footer */
        .footer {
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 3rem;
            padding: 2rem 1rem;
            animation: fadeIn 1s ease-out 1s both;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .footer-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .footer-link:hover {
            color: white;
            transform: translateY(-2px);
        }

        /* Animations */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        /* Loading Animation */
        .loading {
            display: none;
            text-align: center;
            color: white;
            margin-top: 1rem;
        }

        .loading.show {
            display: block;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .brand-title {
                font-size: 2.5rem;
            }

            .brand-subtitle {
                font-size: 1.1rem;
            }

            .search-card {
                padding: 2rem 1.5rem;
            }

            .customer-header {
                padding: 1.5rem;
            }

            .service-item {
                margin: 0.5rem;
                padding: 1rem;
            }

            .service-meta {
                flex-direction: column;
                align-items: flex-start;
            }

            .footer-links {
                flex-direction: column;
                gap: 1rem;
            }
        }

        @media (max-width: 576px) {
            .lookup-container {
                padding: 1rem 0.5rem;
            }

            .brand-title {
                font-size: 2rem;
            }

            .search-card {
                padding: 1.5rem 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Animated Background -->
    <div class="bg-animation"></div>

    <!-- Floating Particles -->
    <div class="particles" id="particles"></div>

    <div class="lookup-container">
        <!-- Brand Header -->
        <div class="brand-header">
            <div class="brand-logo">
                <i class="fas fa-crown" style="font-size: 2rem; color: white;"></i>
            </div>
            <h1 class="brand-title">KienUnlocked</h1>
            <p class="brand-subtitle">Tra cứu thông tin dịch vụ của bạn một cách dễ dàng</p>
        </div>

        <!-- Search Form -->
        <div class="glass-card search-card">
            <form method="GET" action="{{ route('lookup.index') }}" class="search-form" id="searchForm">
                <div class="search-input-wrapper">
                    <input type="text"
                        name="code"
                        class="search-input"
                        placeholder="Nhập mã tra cứu của bạn (VD: KUN12345)"
                        value="{{ $code }}"
                        required
                        autocomplete="off"
                        id="searchInput">
                </div>
                <button type="submit" class="search-btn" id="searchBtn">
                    <i class="fas fa-search me-2"></i>
                    <span>Tra cứu ngay</span>
                </button>
            </form>

            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Đang tra cứu thông tin...</p>
            </div>

            @if($code && !$customer)
            <div class="alert-custom">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Không tìm thấy thông tin với mã <strong>{{ $code }}</strong>.
                Vui lòng kiểm tra lại mã tra cứu hoặc liên hệ hỗ trợ.
            </div>
            @endif
        </div>

        <!-- Results -->
        @if($customer)
        <div class="glass-card result-card">
            <!-- Customer Header -->
            <div class="customer-header">
                <div class="customer-avatar">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
                <h2 class="customer-name">{{ $customer->name }}</h2>

                <div class="customer-details">
                    <div class="customer-detail">
                        <i class="fas fa-id-card me-1"></i>
                        {{ $customer->customer_code }}
                    </div>
                    @if($customer->email)
                    <div class="customer-detail">
                        <i class="fas fa-envelope me-1"></i>
                        {{ $customer->email }}
                    </div>
                    @endif
                    @if($customer->phone)
                    <div class="customer-detail">
                        <i class="fas fa-phone me-1"></i>
                        {{ $customer->phone }}
                    </div>
                    @endif
                </div>

                <div class="services-count">
                    <div class="services-count-number">{{ $services->count() }}</div>
                    <div>Dịch vụ đang sử dụng</div>
                </div>
            </div>

            <!-- Services List -->
            <div class="services-container">
                @if($services->count() > 0)
                @foreach($services as $index => $service)
                <div class="service-item" style="animation-delay: {{ 0.8 + ($index * 0.1) }}s;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <h3 class="service-name">
                                <i class="fas fa-star me-2" style="color: #ffc107;"></i>
                                {{ $service->servicePackage->name }}
                            </h3>
                            <div class="service-category">
                                <i class="fas fa-tag me-1"></i>
                                {{ $service->servicePackage->category->name ?? 'Dịch vụ' }}
                                • {{ $service->servicePackage->account_type }}
                            </div>
                        </div>
                        <div class="text-end">
                            @if($service->status === 'active')
                            @if($service->isExpired())
                            <span class="status-badge status-expired">
                                <i class="fas fa-times-circle"></i>
                                Đã hết hạn
                            </span>
                            @elseif($service->isExpiringSoon())
                            <span class="status-badge status-expiring">
                                <i class="fas fa-exclamation-triangle"></i>
                                Sắp hết hạn
                            </span>
                            @else
                            <span class="status-badge status-active">
                                <i class="fas fa-check-circle"></i>
                                Hoạt động
                            </span>
                            @endif
                            @else
                            <span class="status-badge status-expired">
                                <i class="fas fa-ban"></i>
                                {{ $service->status === 'expired' ? 'Đã hết hạn' : 'Đã hủy' }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="service-email">
                        <div class="service-email-label">
                            <i class="fas fa-key me-1"></i>
                            Thông tin đăng nhập
                        </div>
                        <div class="service-email-value">{{ $service->login_email }}</div>
                    </div>

                    <div class="service-meta">
                        <div class="service-expiry">
                            <i class="fas fa-calendar-alt me-1"></i>
                            <strong>Hết hạn:</strong> {{ $service->expires_at->format('d/m/Y H:i') }}
                            @if(!$service->isExpired())
                            <br>
                            <small class="text-muted">
                                Còn {{ $service->getDaysRemaining() }} ngày
                            </small>
                            @endif
                        </div>
                        @if($service->isExpiringSoon())
                        <div class="alert alert-warning p-2 mb-0 small">
                            <i class="fas fa-bell me-1"></i>
                            Dịch vụ sắp hết hạn, vui lòng liên hệ để gia hạn
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
                @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h4>Chưa có dịch vụ nào</h4>
                    <p>Khách hàng này chưa được gán dịch vụ nào.</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="footer-links">
                <a href="#" class="footer-link">
                    <i class="fas fa-headset me-1"></i>
                    Hỗ trợ khách hàng
                </a>
                <a href="#" class="footer-link">
                    <i class="fas fa-shield-alt me-1"></i>
                    Chính sách bảo mật
                </a>
                <a href="#" class="footer-link">
                    <i class="fas fa-file-alt me-1"></i>
                    Điều khoản sử dụng
                </a>
            </div>
            <p class="mb-1">
                <i class="fas fa-lock me-2"></i>
                Thông tin được mã hóa và bảo mật tuyệt đối
            </p>
            <p class="mb-0">
                © {{ date('Y') }} KienUnlocked. All rights reserved.
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Create floating particles
            function createParticles() {
                const particlesContainer = document.getElementById('particles');
                const particleCount = 50;

                for (let i = 0; i < particleCount; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.animationDelay = Math.random() * 15 + 's';
                    particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
                    particlesContainer.appendChild(particle);
                }
            }

            createParticles();

            // Form submission with loading animation
            const searchForm = document.getElementById('searchForm');
            const searchBtn = document.getElementById('searchBtn');
            const loading = document.getElementById('loading');

            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    loading.classList.add('show');
                    searchBtn.disabled = true;
                    searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i><span>Đang tra cứu...</span>';
                });
            }

            // Add enter key enhancement for search
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchForm.submit();
                    }
                });

                // Auto-format input (uppercase)
                searchInput.addEventListener('input', function(e) {
                    e.target.value = e.target.value.toUpperCase();
                });
            }

            // Smooth scrolling for better UX
            if (window.location.search.includes('code=')) {
                setTimeout(() => {
                    const resultCard = document.querySelector('.result-card');
                    if (resultCard) {
                        resultCard.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }, 500);
            }

            // Add copy to clipboard functionality for email
            const emailElements = document.querySelectorAll('.service-email-value');
            emailElements.forEach(email => {
                email.style.cursor = 'pointer';
                email.title = 'Click để copy';
                email.addEventListener('click', function() {
                    navigator.clipboard.writeText(this.textContent).then(() => {
                        // Show temporary feedback
                        const originalBg = this.parentElement.style.backgroundColor;
                        this.parentElement.style.backgroundColor = '#d4edda';
                        this.parentElement.style.transition = 'background-color 0.3s';

                        setTimeout(() => {
                            this.parentElement.style.backgroundColor = originalBg;
                        }, 1000);
                    });
                });
            });

            // Add keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === '/' && e.target.tagName !== 'INPUT') {
                    e.preventDefault();
                    searchInput.focus();
                }
            });
        });
    </script>

    <!-- Global Horizontal Scroll Enhancement -->
    <script src="{{ asset('js/horizontal-scroll-global.js') }}"></script>
</body>

</html>