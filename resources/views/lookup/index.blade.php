<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tra Cứu Thông Tin Dịch Vụ - KienUnlocked</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="Tra cứu thông tin dịch vụ, tài khoản số nhanh chóng. Nhập mã khách hàng, email hoặc số điện thoại để kiểm tra trạng thái dịch vụ.">
    <meta name="keywords" content="tra cứu dịch vụ, kiểm tra tài khoản, KienUnlocked, tra cứu thông tin">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/tra-cuu') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/tra-cuu') }}">
    <meta property="og:title" content="Tra Cứu Thông Tin Dịch Vụ - KienUnlocked">
    <meta property="og:description" content="Tra cứu thông tin dịch vụ, tài khoản số nhanh chóng. Nhập mã khách hàng, email hoặc SĐT.">
    <meta property="og:image" content="{{ asset('logo.svg') }}">
    <meta property="og:locale" content="vi_VN">

    <!-- Mobile Web App -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Tra Cứu">
    <meta name="mobile-web-app-capable" content="yes">

    <!-- Favicon and App Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.svg') }}">
    <meta name="theme-color" content="#667eea">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome - Local -->
    <link rel="stylesheet" href="{{ asset('css/fontawesome/all.min.css') }}" />
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --danger-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.3);
            --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-size: 200% 200%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        @keyframes gradientShift {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        /* Animated particles background */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: float 20s infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }

            10% {
                opacity: 0.3;
            }

            90% {
                opacity: 0.3;
            }

            100% {
                transform: translateY(-100vh) translateX(100px);
                opacity: 0;
            }
        }

        .container-fluid {
            position: relative;
            z-index: 1;
        }

        /* Header */
        .header {
            padding: 2rem 0;
            text-align: center;
            animation: fadeInDown 0.8s ease;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-container {
            display: inline-flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem 2.5rem;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--glass-border);
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 1rem;
        }

        .logo-container:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.3);
        }

        .logo-icon {
            width: 50px;
            height: 50px;
            background: var(--primary-gradient);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .logo-text h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo-text p {
            margin: 0;
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 500;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.95);
            font-size: 1.1rem;
            font-weight: 500;
            margin-top: 1rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* Search Card */
        .search-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--glass-border);
            padding: 2.5rem;
            animation: fadeInUp 0.8s ease;
            transition: all 0.3s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .search-card:hover {
            box-shadow: 0 35px 70px -15px rgba(0, 0, 0, 0.3);
        }

        .search-wrapper {
            position: relative;
        }

        .search-input-group {
            position: relative;
            display: flex;
            gap: 1rem;
        }

        .search-icon-left {
            position: absolute;
            left: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.2rem;
            z-index: 10;
            pointer-events: none;
        }

        .search-input {
            flex: 1;
            padding: 1.2rem 1.5rem 1.2rem 3.5rem;
            border: 2px solid #e5e7eb;
            border-radius: 20px;
            font-size: 1.1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            background: white;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .search-input::placeholder {
            color: #9ca3af;
        }

        .search-btn {
            padding: 1.2rem 2.5rem;
            background: var(--primary-gradient);
            border: none;
            border-radius: 20px;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            white-space: nowrap;
        }

        .search-btn:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        .search-btn:active:not(:disabled) {
            transform: translateY(-1px);
        }

        .search-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .search-btn i {
            margin-right: 0.5rem;
        }

        .search-hints {
            padding-top: 0.5rem;
        }

        .search-hints small {
            color: #6b7280;
        }

        .search-hints strong {
            color: #667eea;
        }

        /* Feature Cards */
        .features-section {
            animation: fadeInUp 1s ease;
            animation-delay: 0.3s;
            animation-fill-mode: both;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.25);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 1.5rem;
        }

        .feature-icon-success {
            background: var(--success-gradient);
        }

        .feature-icon-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .feature-card h5 {
            color: white;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .feature-card p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            margin: 0;
        }

        /* Stats Bar — số liệu thực */
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 1.5rem;
            animation: fadeInUp 1s ease;
            animation-delay: 0.2s;
            animation-fill-mode: both;
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.25rem 1rem;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-3px);
            background: rgba(255, 255, 255, 0.22);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: white;
            line-height: 1;
            margin-bottom: 0.35rem;
            background: linear-gradient(135deg, #ffffff 0%, #f0f4ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        @media (max-width: 576px) {
            .stats-bar {
                gap: 0.5rem;
                margin-top: 1.25rem;
            }
            .stat-item { padding: 0.85rem 0.5rem; }
            .stat-number { font-size: 1.4rem; }
            .stat-label { font-size: 0.65rem; letter-spacing: 0.02em; }
        }

        @media (max-width: 380px) {
            .stat-label i { display: none; }
            .stat-label { font-size: 0.6rem; }
        }

        /* Categories Preview */
        .categories-section {
            margin-top: 2rem;
            animation: fadeInUp 1s ease;
            animation-delay: 0.4s;
            animation-fill-mode: both;
        }

        .categories-title {
            color: white;
            font-weight: 700;
            text-align: center;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            letter-spacing: 0.02em;
        }

        .categories-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.6rem;
        }

        .category-chip {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(10px);
            color: white;
            padding: 0.55rem 1.1rem;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }

        .category-chip:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.3);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .category-chip .count {
            background: rgba(255, 255, 255, 0.25);
            padding: 0.1rem 0.5rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* Autocomplete suggestions */
        .autocomplete-suggestions {
            position: absolute;
            top: calc(100% + 0.5rem);
            left: 0;
            right: 0;
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            z-index: 1000;
            display: none;
        }

        .autocomplete-suggestions.active {
            display: block;
            animation: fadeInUp 0.3s ease;
        }

        .suggestion-item {
            padding: 1rem 1.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .suggestion-item:hover {
            background: #f3f4f6;
        }

        .suggestion-icon {
            width: 35px;
            height: 35px;
            background: var(--primary-gradient);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.9rem;
        }

        /* Loading State */
        .loading-container {
            display: none;
            text-align: center;
            padding: 3rem;
        }

        .loading-container.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .loader {
            display: inline-block;
            width: 60px;
            height: 60px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loading-text {
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 1.5rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* Results */
        .results-container {
            display: none;
        }

        .results-container.active {
            display: block;
            animation: fadeInUp 0.5s ease;
        }

        /* Customer Info Card */
        .customer-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--glass-border);
            padding: 2rem;
            margin-bottom: 2rem;
            animation: fadeInUp 0.6s ease;
        }

        .customer-avatar {
            width: 80px;
            height: 80px;
            background: var(--primary-gradient);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: 700;
            box-shadow: var(--shadow-md);
        }

        .customer-info h3 {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .customer-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7280;
            font-size: 0.95rem;
        }

        .meta-item i {
            color: #667eea;
        }

        .service-count-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--success-gradient);
            color: white;
            border-radius: 15px;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: var(--shadow-md);
        }

        /* Service Cards */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .service-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--glass-border);
            padding: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: fadeInUp 0.7s ease;
            animation-fill-mode: both;
            position: relative;
            overflow: hidden;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary-gradient);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .service-card:hover::before {
            transform: scaleX(1);
        }

        .service-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .service-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .service-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .service-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        .service-card:nth-child(5) {
            animation-delay: 0.5s;
        }

        .service-card:nth-child(6) {
            animation-delay: 0.6s;
        }

        .service-header {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .service-icon {
            width: 60px;
            height: 60px;
            background: var(--primary-gradient);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            flex-shrink: 0;
            box-shadow: var(--shadow-md);
        }

        .service-title-group {
            flex: 1;
        }

        .service-category {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: #f3f4f6;
            color: #6b7280;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .service-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .status-active {
            background: var(--success-gradient);
            color: white;
        }

        .status-expiring {
            background: var(--warning-gradient);
            color: white;
        }

        .status-expired {
            background: var(--danger-gradient);
            color: white;
        }

        .service-details {
            display: grid;
            gap: 0.75rem;
        }

        .detail-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-value {
            color: #1f2937;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .days-remaining {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.8rem;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.85rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            box-shadow: var(--shadow-lg);
        }

        .empty-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #9ca3af;
        }

        .empty-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .empty-text {
            color: #6b7280;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .retry-btn {
            padding: 0.75rem 2rem;
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-md);
        }

        .retry-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                padding: 1rem 0;
            }

            .logo-container {
                flex-direction: column;
                text-align: center;
                padding: 1.5rem;
            }

            .subtitle {
                font-size: 1rem;
            }

            .search-card {
                padding: 1.5rem;
            }

            .search-input-group {
                flex-direction: column;
            }

            .search-input {
                padding: 1rem 1rem 1rem 3rem;
                font-size: 1rem;
            }

            .search-btn {
                padding: 1rem 1.5rem;
                width: 100%;
            }

            .features-section .row > div {
                margin-bottom: 0.75rem;
            }

            .feature-card {
                padding: 1.25rem;
            }

            .customer-card {
                padding: 1.5rem;
            }

            .customer-info-grid {
                flex-direction: column;
                text-align: center;
            }

            .customer-avatar {
                margin: 0 auto 1rem;
            }

            .services-grid {
                grid-template-columns: 1fr;
            }

            .customer-meta {
                flex-direction: column;
                gap: 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .container-fluid {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .header {
                padding: 0.5rem 0;
            }

            .logo-container {
                padding: 1rem 1.25rem;
                gap: 0.75rem;
                border-radius: 18px;
            }

            .logo-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
                border-radius: 12px;
            }

            .logo-text h1 {
                font-size: 1.4rem;
            }

            .logo-text p {
                font-size: 0.8rem;
            }

            .subtitle {
                font-size: 0.9rem;
                margin-top: 0.5rem;
                padding: 0 0.5rem;
            }

            .search-card {
                padding: 1.25rem;
                border-radius: 22px;
            }

            .search-input {
                padding: 0.85rem 1rem 0.85rem 2.75rem;
                font-size: 0.95rem;
                border-radius: 16px;
            }

            .search-icon-left {
                left: 1rem;
                font-size: 1rem;
            }

            .search-btn {
                padding: 0.85rem 1.25rem;
                font-size: 1rem;
                border-radius: 16px;
            }

            .search-hints small {
                font-size: 0.78rem;
                line-height: 1.5;
                display: inline-block;
            }

            .feature-card {
                padding: 1rem;
                border-radius: 16px;
            }

            .feature-icon {
                width: 48px;
                height: 48px;
                font-size: 1.2rem;
                margin-bottom: 0.6rem;
            }

            .feature-card h5 {
                font-size: 0.95rem;
            }

            .feature-card p {
                font-size: 0.82rem;
            }

            .categories-title {
                font-size: 1rem;
            }

            .category-chip {
                padding: 0.45rem 0.85rem;
                font-size: 0.82rem;
            }
        }

        /* Skeleton Loading */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s ease-in-out infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }
    </style>
</head>

<body>
    <!-- Animated Particles -->
    <div class="particles" id="particles"></div>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <!-- Header -->
                <div class="header">
                    <a href="{{ route('lookup.index') }}" class="logo-container">
                        <div class="logo-icon">
                            <i class="fas fa-unlock-alt"></i>
                        </div>
                        <div class="logo-text">
                            <h1>KienUnlocked</h1>
                            <p>Tra cứu dịch vụ</p>
                        </div>
                    </a>
                    <p class="subtitle">
                        <i class="fas fa-sparkles me-2"></i>
                        Tra cứu thông tin dịch vụ của bạn một cách nhanh chóng và dễ dàng
                    </p>
                </div>

                <!-- Search Card -->
                <div class="search-card">
                    <form id="lookupForm">
                        <div class="search-wrapper">
                            <div class="search-input-group">
                                <i class="fas fa-user search-icon-left"></i>
                                <input
                                    type="text"
                                    class="search-input"
                                    id="searchInput"
                                    value="{{ $code ?? '' }}"
                                    placeholder="Nhập mã đơn, mã KH, tên Zalo, email hoặc SĐT..."
                                    autocomplete="off"
                                    required>
                                <button type="submit" class="search-btn" id="searchBtn">
                                    <i class="fas fa-search me-2"></i>
                                    <span>Tra cứu</span>
                                </button>
                            </div>
                            <div class="autocomplete-suggestions" id="autocompleteSuggestions"></div>
                        </div>
                    </form>
                    <div class="search-hints mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Tra cứu bằng: <strong>Mã đơn</strong> (DH-XXX), <strong>Mã khách hàng</strong> (KUN/CTV), <strong>Tên Zalo</strong>, <strong>Email</strong>, <strong>Số điện thoại</strong>
                        </small>
                    </div>
                </div>

                {{-- Stats Bar — số liệu thực từ DB --}}
                @isset($stats)
                <div class="stats-bar">
                    <div class="stat-item">
                        <div class="stat-number">{{ number_format($stats['customers']) }}+</div>
                        <div class="stat-label"><i class="fas fa-users me-1"></i>Khách hàng</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ number_format($stats['services']) }}+</div>
                        <div class="stat-label"><i class="fas fa-bolt me-1"></i>Dịch vụ đang hoạt động</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ number_format($stats['packages']) }}+</div>
                        <div class="stat-label"><i class="fas fa-box me-1"></i>Gói dịch vụ</div>
                    </div>
                </div>
                @endisset

                <!-- Features Section -->
                <div class="features-section mt-4" id="featuresSection">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-bolt"></i>
                                </div>
                                <h5>Tra cứu nhanh</h5>
                                <p>Kết quả hiển thị ngay lập tức</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-card">
                                <div class="feature-icon feature-icon-success">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h5>Cập nhật 24/7</h5>
                                <p>Thông tin luôn mới nhất</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-card">
                                <div class="feature-icon feature-icon-warning">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <h5>Bảo mật cao</h5>
                                <p>Thông tin được mã hóa an toàn</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Categories Preview — list các danh mục dịch vụ shop có --}}
                @isset($categories)
                @if($categories->count() > 0)
                <div class="categories-section">
                    <div class="categories-title">
                        <i class="fas fa-th-large me-2"></i>Danh mục dịch vụ
                    </div>
                    <div class="categories-grid">
                        @foreach($categories as $cat)
                        <span class="category-chip">
                            <i class="fas fa-folder-open"></i>
                            {{ $cat->name }}
                            <span class="count">{{ $cat->active_count }}</span>
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
                @endisset

                <!-- Loading State -->
                <div class="loading-container" id="loadingContainer">
                    <div class="loader"></div>
                    <p class="loading-text">
                        <i class="fas fa-search me-2"></i>
                        Đang tìm kiếm thông tin của bạn...
                    </p>
                </div>

                <!-- Results Container -->
                <div class="results-container" id="resultsContainer"></div>

                <!-- Footer -->
                <footer class="text-center py-4 mt-4">
                    <p class="text-white-50 mb-0" style="font-size: 0.9rem;">
                        <i class="fas fa-shield-alt me-1"></i>
                        Thông tin của bạn được bảo mật an toàn
                    </p>
                    <p class="text-white-50 mt-2" style="font-size: 0.8rem;">
                        © {{ date('Y') }} KienUnlocked. All rights reserved.
                    </p>
                </footer>
            </div>
        </div>
    </div>

    <!-- Bootstrap & Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Create animated particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 30;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 20 + 's';
                particle.style.animationDuration = (Math.random() * 10 + 15) + 's';
                particlesContainer.appendChild(particle);
            }
        }

        createParticles();

        // Main app logic
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('lookupForm');
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            const loadingContainer = document.getElementById('loadingContainer');
            const resultsContainer = document.getElementById('resultsContainer');
            const autocompleteSuggestions = document.getElementById('autocompleteSuggestions');

            // Auto-focus on search input
            searchInput.focus();

            // Form submit handler
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                performSearch();
            });

            // Auto-search nếu URL có ?code=DH-XXX hoặc input đã pre-fill từ server
            // (vd link tracking từ Telegram bot khi đơn paid).
            if (searchInput.value.trim() !== '') {
                performSearch();
            }

            function performSearch() {
                const query = searchInput.value.trim();
                if (!query) return;

                // Show loading
                showLoading();

                // Perform AJAX search
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                fetch('{{ route("lookup.search") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            code: query
                        })
                    })
                    .then(response => {
                        if (!response.ok && response.status !== 404) {
                            throw new Error('HTTP error! status: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        hideLoading();
                        if (data.success) {
                            displayResults(data.data);
                            saveRecentSearch(query);
                        } else {
                            displayError(data.message || 'Không tìm thấy thông tin');
                        }
                    })
                    .catch(error => {
                        hideLoading();
                        console.error('Search error:', error);
                        displayError('Có lỗi xảy ra: ' + error.message);
                    });
            }

            function showLoading() {
                loadingContainer.classList.add('active');
                resultsContainer.classList.remove('active');
                document.getElementById('featuresSection').style.display = 'none';
                searchBtn.disabled = true;
            }

            function hideLoading() {
                loadingContainer.classList.remove('active');
                searchBtn.disabled = false;
            }

            function displayResults(data) {
                const customer = data.customer;
                const services = data.services || [];

                const initials = customer.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

                let html = `
                    <div class="customer-card">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="customer-avatar">${initials}</div>
                                    <div class="customer-info">
                                        <h3>${customer.name}</h3>
                                        <div class="customer-meta">
                                            <div class="meta-item">
                                                <i class="fas fa-id-card"></i>
                                                <strong>Mã:</strong> ${customer.customer_code}
                                            </div>
                                            ${customer.email ? `
                                            <div class="meta-item">
                                                <i class="fas fa-envelope"></i>
                                                ${customer.email}
                                            </div>
                                            ` : ''}
                                            ${customer.phone ? `
                                            <div class="meta-item">
                                                <i class="fas fa-phone"></i>
                                                ${customer.phone}
                                            </div>
                                            ` : ''}
                                            <div class="meta-item">
                                                <i class="fas fa-calendar"></i>
                                                Từ ${customer.created_at}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <div class="service-count-badge">
                                    <i class="fas fa-box"></i>
                                    <span>${services.length} dịch vụ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                if (services.length > 0) {
                    html += '<div class="services-grid">';
                    services.forEach(service => {
                        html += createServiceCard(service);
                    });
                    html += '</div>';
                } else {
                    html += createEmptyState();
                }

                resultsContainer.innerHTML = html;
                resultsContainer.classList.add('active');

                // Scroll to results
                setTimeout(() => {
                    resultsContainer.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            }

            function createServiceCard(service) {
                const status = getServiceStatus(service);
                const daysRemaining = getDaysRemaining(service.expires_at);
                const categoryIcon = getCategoryIcon(service.category_name);

                return `
                    <div class="service-card">
                        <div class="service-header">
                            <div class="service-icon">
                                <i class="${categoryIcon}"></i>
                            </div>
                            <div class="service-title-group">
                                <span class="service-category">${service.category_name || 'Dịch vụ'}</span>
                                <h4 class="service-name">${service.package_name}</h4>
                                ${service.order_code ? `<small class="text-muted"><i class="fas fa-receipt me-1"></i>Mã đơn: <code>${service.order_code}</code></small>` : ''}
                            </div>
                                </div>

                        <div class="status-badge ${status.class}">
                            <i class="${status.icon}"></i>
                            <span>${status.text}</span>
                                    </div>

                        <div class="service-details">
                            <div class="detail-row">
                                <span class="detail-label">
                                    <i class="fas fa-calendar-check"></i>
                                    Ngày kích hoạt
                                </span>
                                <span class="detail-value">${formatDate(service.activated_at)}</span>
                                    </div>
                            <div class="detail-row">
                                <span class="detail-label">
                                    <i class="fas fa-calendar-times"></i>
                                    Ngày hết hạn
                                </span>
                                <span class="detail-value">${formatDate(service.expires_at)}</span>
                                </div>
                            ${daysRemaining > 0 ? `
                            <div class="detail-row">
                                <span class="detail-label">
                                    <i class="fas fa-clock"></i>
                                    Thời gian còn lại
                                </span>
                                <span class="days-remaining">
                                    <i class="fas fa-hourglass-half"></i>
                                    ${daysRemaining} ngày
                                </span>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            }

            function createEmptyState() {
                return `
                        <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3 class="empty-title">Chưa có dịch vụ</h3>
                        <p class="empty-text">Khách hàng này chưa được gán dịch vụ nào.</p>
                    </div>
                `;
            }

            function displayError(message) {
                resultsContainer.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3 class="empty-title">Không tìm thấy</h3>
                        <p class="empty-text">${message}</p>
                        <button class="retry-btn" onclick="document.getElementById('searchInput').focus()">
                            <i class="fas fa-redo me-2"></i>
                            Thử lại
                        </button>
                    </div>
                `;
                resultsContainer.classList.add('active');
            }

            function getServiceStatus(service) {
                const daysRemaining = getDaysRemaining(service.expires_at);

                if (service.status !== 'active' || daysRemaining <= 0) {
                    return {
                        class: 'status-expired',
                        text: 'Đã hết hạn',
                        icon: 'fas fa-times-circle'
                    };
                }

                if (daysRemaining <= 7) {
                    return {
                        class: 'status-expiring',
                        text: 'Sắp hết hạn',
                        icon: 'fas fa-exclamation-circle'
                    };
                }

                return {
                    class: 'status-active',
                    text: 'Đang hoạt động',
                    icon: 'fas fa-check-circle'
                };
            }

            function getDaysRemaining(expiresAt) {
                if (!expiresAt) return 0;
                const expireDate = new Date(expiresAt);
                const today = new Date();
                const diffTime = expireDate - today;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                return Math.max(0, diffDays);
            }

            function formatDate(dateString) {
                if (!dateString) return 'Chưa có';
                const date = new Date(dateString);
                return date.toLocaleDateString('vi-VN', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            }

            function getCategoryIcon(category) {
                const icons = {
                    'AI': 'fas fa-robot',
                    'Streaming': 'fas fa-tv',
                    'Giáo dục': 'fas fa-graduation-cap',
                    'Học tập': 'fas fa-book',
                    'Làm việc': 'fas fa-briefcase',
                    'Giải trí': 'fas fa-gamepad',
                    'Âm nhạc': 'fas fa-music',
                    'Video': 'fas fa-video',
                    'Thiết kế': 'fas fa-paint-brush',
                    'Mạng xã hội': 'fas fa-share-alt'
                };

                for (const [key, icon] of Object.entries(icons)) {
                    if (category && category.includes(key)) {
                        return icon;
                    }
                }

                return 'fas fa-cube';
            }

            function saveRecentSearch(query) {
                try {
                    let recent = JSON.parse(localStorage.getItem('recentSearches') || '[]');
                    recent = [query, ...recent.filter(q => q !== query)].slice(0, 5);
                    localStorage.setItem('recentSearches', JSON.stringify(recent));
                } catch (e) {
                    console.error('Error saving recent search:', e);
                }
            }

            // Auto-complete from recent searches
            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                if (query.length < 2) {
                    autocompleteSuggestions.classList.remove('active');
                    return;
                }

                try {
                    const recent = JSON.parse(localStorage.getItem('recentSearches') || '[]');
                    const matches = recent.filter(item =>
                        item.toLowerCase().includes(query)
                    ).slice(0, 3);

                    if (matches.length > 0) {
                        autocompleteSuggestions.innerHTML = matches.map(match => `
                            <div class="suggestion-item" data-value="${match}">
                                <div class="suggestion-icon">
                                    <i class="fas fa-history"></i>
                                </div>
                                <div>${match}</div>
                            </div>
                        `).join('');
                        autocompleteSuggestions.classList.add('active');

                        // Add click handlers
                        document.querySelectorAll('.suggestion-item').forEach(item => {
                            item.addEventListener('click', function() {
                                searchInput.value = this.dataset.value;
                                autocompleteSuggestions.classList.remove('active');
                                performSearch();
                            });
                        });
                    } else {
                        autocompleteSuggestions.classList.remove('active');
                    }
                } catch (e) {
                    console.error('Error loading suggestions:', e);
                }
            });

            // Close autocomplete when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !autocompleteSuggestions.contains(e.target)) {
                    autocompleteSuggestions.classList.remove('active');
                }
            });
        });
    </script>
</body>

</html>