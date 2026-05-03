<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tra Cứu Thông Tin Dịch Vụ - KienUnlocked</title>

    <meta name="description" content="Tra cứu thông tin dịch vụ tài khoản số ChatGPT, Claude, Spotify, Netflix... Nhập mã đơn, mã KH, tên Zalo, email hoặc SĐT.">
    <meta name="keywords" content="tra cứu dịch vụ, kiểm tra tài khoản, KienUnlocked, ChatGPT, Claude, Netflix">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/tra-cuu') }}">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/tra-cuu') }}">
    <meta property="og:title" content="Tra Cứu Thông Tin Dịch Vụ - KienUnlocked">
    <meta property="og:description" content="Tra cứu thông tin dịch vụ tài khoản số nhanh chóng và chính xác.">
    <meta property="og:image" content="{{ asset('logo.svg') }}">
    <meta property="og:locale" content="vi_VN">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Tra Cứu">
    <meta name="mobile-web-app-capable" content="yes">

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.svg') }}">
    <meta name="theme-color" content="#4f46e5">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/fontawesome/all.min.css') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --indigo-50: #eef2ff;
            --indigo-100: #e0e7ff;
            --indigo-500: #6366f1;
            --indigo-600: #4f46e5;
            --indigo-700: #4338ca;
            --violet-600: #7c3aed;
            --emerald-500: #10b981;
            --emerald-600: #059669;
            --amber-500: #f59e0b;
            --amber-600: #d97706;
            --rose-500: #f43f5e;
            --rose-600: #e11d48;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;

            --gradient-primary: linear-gradient(135deg, var(--indigo-600) 0%, var(--violet-600) 100%);
            --gradient-success: linear-gradient(135deg, #059669 0%, #10b981 100%);
            --gradient-warning: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
            --gradient-danger: linear-gradient(135deg, #dc2626 0%, #f43f5e 100%);

            --shadow-sm: 0 1px 2px 0 rgba(15, 23, 42, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(15, 23, 42, 0.07), 0 2px 4px -2px rgba(15, 23, 42, 0.04);
            --shadow-lg: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
            --shadow-xl: 0 25px 50px -12px rgba(79, 70, 229, 0.15);
            --shadow-indigo: 0 10px 30px -10px rgba(79, 70, 229, 0.4);

            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --radius-2xl: 32px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(180deg, #f8fafc 0%, #eef2ff 60%, #e0e7ff 100%);
            min-height: 100vh;
            color: var(--slate-800);
            position: relative;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Decorative blobs — subtle, không animation lớn */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            pointer-events: none;
        }
        body::before {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.25), transparent 70%);
            top: -150px; right: -100px;
        }
        body::after {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.18), transparent 70%);
            bottom: -200px; left: -150px;
        }

        .container-fluid {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem 1rem;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        @keyframes shimmer {
            0%   { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }

        /* ====================== HEADER ====================== */
        .hero {
            text-align: center;
            padding: 2.5rem 0 1.5rem;
            animation: fadeInUp 0.6s ease;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 0.875rem;
            text-decoration: none;
            margin-bottom: 1.25rem;
            transition: transform 0.2s ease;
        }

        .brand:hover { transform: translateY(-2px); }

        .brand-icon {
            width: 56px;
            height: 56px;
            background: var(--gradient-primary);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: var(--shadow-indigo);
        }

        .brand-text h1 {
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.02em;
        }

        .brand-text .tagline {
            font-size: 0.85rem;
            color: var(--slate-500);
            font-weight: 500;
            margin-top: 0.25rem;
        }

        .hero-headline {
            font-size: 2rem;
            font-weight: 800;
            color: var(--slate-900);
            line-height: 1.2;
            letter-spacing: -0.02em;
            max-width: 700px;
            margin: 0 auto 0.75rem;
        }

        .hero-headline .highlight {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-sub {
            color: var(--slate-600);
            font-size: 1.05rem;
            max-width: 580px;
            margin: 0 auto;
            font-weight: 500;
        }

        /* ====================== SEARCH CARD ====================== */
        .search-section {
            margin-top: 2rem;
            animation: fadeInUp 0.7s ease 0.1s both;
        }

        .search-card {
            background: white;
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-lg);
            padding: 1.75rem;
            border: 1px solid var(--slate-200);
            position: relative;
        }

        .search-wrapper { position: relative; }

        .search-input-group {
            display: flex;
            gap: 0.75rem;
            align-items: stretch;
        }

        .search-input-wrap {
            flex: 1;
            position: relative;
        }

        .search-icon-left {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--slate-400);
            font-size: 1.05rem;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .search-input {
            width: 100%;
            padding: 1.1rem 1rem 1.1rem 3rem;
            border: 2px solid var(--slate-200);
            border-radius: var(--radius-xl);
            font-size: 1rem;
            font-weight: 500;
            color: var(--slate-900);
            background: var(--slate-50);
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .search-input::placeholder { color: var(--slate-400); }

        .search-input:focus {
            outline: none;
            border-color: var(--indigo-500);
            background: white;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
        }

        .search-input:focus + .search-icon-left,
        .search-wrapper:focus-within .search-icon-left { color: var(--indigo-600); }

        .search-btn {
            padding: 1.1rem 1.75rem;
            background: var(--gradient-primary);
            border: none;
            border-radius: var(--radius-xl);
            color: white;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-indigo);
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-family: inherit;
        }

        .search-btn:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 14px 35px -10px rgba(79, 70, 229, 0.5);
        }

        .search-btn:active:not(:disabled) { transform: translateY(0); }

        .search-btn:disabled { opacity: 0.65; cursor: not-allowed; }

        .search-hint {
            margin-top: 1rem;
            text-align: center;
            color: var(--slate-500);
            font-size: 0.85rem;
            line-height: 1.6;
        }

        .search-hint strong {
            color: var(--indigo-600);
            font-weight: 700;
        }

        .search-hint code {
            background: var(--indigo-50);
            color: var(--indigo-700);
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-family: 'JetBrains Mono', monospace;
        }

        /* ====================== TRUST BADGES ====================== */
        .trust-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            margin-top: 1.5rem;
            animation: fadeInUp 0.7s ease 0.2s both;
        }

        .trust-badge {
            background: white;
            border-radius: var(--radius-lg);
            padding: 0.875rem 0.75rem;
            text-align: center;
            border: 1px solid var(--slate-200);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: var(--slate-700);
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .trust-badge:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--indigo-200);
        }

        .trust-badge i {
            color: var(--emerald-500);
            font-size: 1rem;
        }

        /* ====================== STATS BAR ====================== */
        .stats-section {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 2rem;
            animation: fadeInUp 0.7s ease 0.3s both;
        }

        .stat-card {
            background: white;
            border-radius: var(--radius-xl);
            padding: 1.5rem 1.25rem;
            text-align: center;
            border: 1px solid var(--slate-200);
            box-shadow: var(--shadow-sm);
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 3px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--indigo-200);
        }

        .stat-card:hover::before { transform: scaleX(1); }

        .stat-number {
            font-size: 2.25rem;
            font-weight: 900;
            line-height: 1;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.03em;
        }

        .stat-label {
            color: var(--slate-500);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }

        .stat-label i { color: var(--indigo-500); }

        /* ====================== CATEGORIES ====================== */
        .categories-section {
            margin-top: 2.25rem;
            animation: fadeInUp 0.7s ease 0.4s both;
        }

        .categories-title {
            color: var(--slate-700);
            font-weight: 700;
            text-align: center;
            margin-bottom: 1rem;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .categories-title::before, .categories-title::after {
            content: '';
            flex: 1;
            max-width: 50px;
            height: 1px;
            background: var(--slate-200);
        }

        .categories-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
        }

        .category-chip {
            background: white;
            border: 1px solid var(--slate-200);
            color: var(--slate-700);
            padding: 0.5rem 1rem;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            box-shadow: var(--shadow-sm);
        }

        .category-chip:hover {
            transform: translateY(-2px);
            border-color: var(--indigo-300);
            color: var(--indigo-700);
            box-shadow: var(--shadow-md);
        }

        .category-chip i { color: var(--indigo-500); font-size: 0.85rem; }

        .category-chip .count {
            background: var(--indigo-50);
            color: var(--indigo-700);
            padding: 0.1rem 0.5rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        /* ====================== AUTOCOMPLETE ====================== */
        .autocomplete-suggestions {
            position: absolute;
            top: calc(100% + 0.5rem);
            left: 0;
            right: 0;
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--slate-200);
            overflow: hidden;
            z-index: 100;
            display: none;
        }

        .autocomplete-suggestions.active {
            display: block;
            animation: fadeInUp 0.2s ease;
        }

        .suggestion-item {
            padding: 0.85rem 1.25rem;
            cursor: pointer;
            transition: background 0.15s ease;
            display: flex;
            align-items: center;
            gap: 0.875rem;
            color: var(--slate-700);
            font-weight: 500;
        }

        .suggestion-item:hover { background: var(--indigo-50); color: var(--indigo-700); }

        .suggestion-icon {
            width: 32px; height: 32px;
            background: var(--indigo-50);
            color: var(--indigo-600);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
        }

        /* ====================== LOADING ====================== */
        .loading-container {
            display: none;
            text-align: center;
            padding: 3rem 1rem;
        }

        .loading-container.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        .spinner-modern {
            display: inline-block;
            width: 48px;
            height: 48px;
            border: 3px solid var(--indigo-100);
            border-top-color: var(--indigo-600);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .loading-text {
            color: var(--slate-600);
            font-size: 1rem;
            font-weight: 600;
            margin-top: 1rem;
        }

        /* ====================== RESULTS ====================== */
        .results-container { display: none; margin-top: 2rem; }
        .results-container.active { display: block; }

        .customer-card {
            background: white;
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--slate-200);
            padding: 1.75rem;
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.5s ease;
            position: relative;
            overflow: hidden;
        }

        .customer-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 5px;
            background: var(--gradient-primary);
        }

        .customer-grid {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .customer-left {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            flex: 1;
            min-width: 0;
        }

        .customer-avatar {
            width: 72px; height: 72px;
            background: var(--gradient-primary);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.6rem;
            font-weight: 800;
            box-shadow: var(--shadow-indigo);
            flex-shrink: 0;
            letter-spacing: -0.02em;
        }

        .customer-info { flex: 1; min-width: 0; }

        .customer-info h3 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--slate-900);
            margin-bottom: 0.25rem;
            letter-spacing: -0.01em;
        }

        .customer-code-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: var(--indigo-50);
            color: var(--indigo-700);
            padding: 0.25rem 0.7rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            margin-bottom: 0.6rem;
        }

        .customer-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.875rem 1.25rem;
            color: var(--slate-600);
            font-size: 0.875rem;
        }

        .meta-item {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .meta-item i {
            color: var(--indigo-500);
            font-size: 0.85rem;
        }

        .service-count-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.1rem;
            background: var(--gradient-primary);
            color: white;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 0.95rem;
            box-shadow: var(--shadow-indigo);
            white-space: nowrap;
        }

        /* ====================== SERVICE CARDS ====================== */
        .services-section {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
            gap: 1.25rem;
        }

        .service-card {
            background: white;
            border-radius: var(--radius-xl);
            border: 1px solid var(--slate-200);
            padding: 1.5rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            animation: fadeInUp 0.4s ease both;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 3px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .service-card.is-active::before  { background: var(--gradient-success); transform: scaleX(1); }
        .service-card.is-expiring::before { background: var(--gradient-warning); transform: scaleX(1); }
        .service-card.is-expired::before  { background: var(--gradient-danger);  transform: scaleX(1); }

        .service-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--indigo-200);
        }

        .service-card:nth-child(1) { animation-delay: 0.05s; }
        .service-card:nth-child(2) { animation-delay: 0.1s; }
        .service-card:nth-child(3) { animation-delay: 0.15s; }
        .service-card:nth-child(4) { animation-delay: 0.2s; }
        .service-card:nth-child(5) { animation-delay: 0.25s; }
        .service-card:nth-child(6) { animation-delay: 0.3s; }

        .service-header {
            display: flex;
            align-items: flex-start;
            gap: 0.875rem;
            margin-bottom: 1.25rem;
        }

        .service-icon {
            width: 52px; height: 52px;
            background: var(--gradient-primary);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            flex-shrink: 0;
            box-shadow: var(--shadow-indigo);
        }

        .service-card.is-active .service-icon  { background: var(--gradient-success); box-shadow: 0 6px 18px -8px rgba(16, 185, 129, 0.5); }
        .service-card.is-expiring .service-icon { background: var(--gradient-warning); box-shadow: 0 6px 18px -8px rgba(245, 158, 11, 0.5); }
        .service-card.is-expired .service-icon  { background: var(--gradient-danger); box-shadow: 0 6px 18px -8px rgba(244, 63, 94, 0.5); }

        .service-title-group { flex: 1; min-width: 0; }

        .service-category {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            background: var(--slate-100);
            color: var(--slate-600);
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 0.4rem;
        }

        .service-name {
            font-size: 1.125rem;
            font-weight: 800;
            color: var(--slate-900);
            line-height: 1.3;
            letter-spacing: -0.01em;
        }

        .service-order-code {
            margin-top: 0.4rem;
            font-size: 0.78rem;
            color: var(--slate-500);
            font-family: 'JetBrains Mono', monospace;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .service-order-code code {
            background: var(--slate-100);
            padding: 0.1rem 0.45rem;
            border-radius: 4px;
            color: var(--slate-700);
            font-weight: 700;
        }

        .status-badge {
            padding: 0.45rem 0.85rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 1rem;
            border: 1px solid;
        }

        .status-badge.status-active {
            background: rgba(16, 185, 129, 0.1);
            color: var(--emerald-600);
            border-color: rgba(16, 185, 129, 0.25);
        }

        .status-badge.status-expiring {
            background: rgba(245, 158, 11, 0.1);
            color: var(--amber-600);
            border-color: rgba(245, 158, 11, 0.25);
        }

        .status-badge.status-expired {
            background: rgba(244, 63, 94, 0.08);
            color: var(--rose-600);
            border-color: rgba(244, 63, 94, 0.2);
        }

        .status-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-active  .status-dot { background: var(--emerald-500); box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2); animation: pulseDot 2s ease-in-out infinite; }
        .status-expiring .status-dot { background: var(--amber-500); }
        .status-expired  .status-dot { background: var(--rose-500); }

        @keyframes pulseDot {
            0%, 100% { box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2); }
            50%      { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.05); }
        }

        /* Progress bar — % thời gian đã dùng */
        .service-progress {
            margin-bottom: 1rem;
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.4rem;
            font-size: 0.78rem;
            color: var(--slate-500);
            font-weight: 600;
        }

        .progress-info .days-left {
            color: var(--slate-900);
            font-weight: 700;
        }

        .service-card.is-expiring .progress-info .days-left { color: var(--amber-600); }
        .service-card.is-expired .progress-info .days-left  { color: var(--rose-600); }

        .progress-track {
            height: 6px;
            background: var(--slate-100);
            border-radius: 999px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 999px;
            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--gradient-success);
        }

        .service-card.is-expiring .progress-fill { background: var(--gradient-warning); }
        .service-card.is-expired .progress-fill  { background: var(--gradient-danger); }

        .service-details {
            display: grid;
            gap: 0.5rem;
            padding-top: 1rem;
            border-top: 1px dashed var(--slate-200);
        }

        .detail-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.25rem 0;
            font-size: 0.875rem;
        }

        .detail-label {
            color: var(--slate-500);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }

        .detail-label i { color: var(--slate-400); font-size: 0.8rem; width: 14px; text-align: center; }

        .detail-value {
            color: var(--slate-800);
            font-weight: 600;
        }

        /* ====================== GROUP BANNER ====================== */
        .group-banner {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border-radius: var(--radius-xl);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1rem;
            box-shadow: var(--shadow-indigo);
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: fadeInUp 0.5s ease;
        }
        .group-banner-icon {
            width: 48px; height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            backdrop-filter: blur(10px);
        }
        .group-banner-text { flex: 1; }
        .group-banner-text .title {
            font-size: 1.05rem;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 0.2rem;
        }
        .group-banner-text .subtitle {
            font-size: 0.85rem;
            opacity: 0.9;
            font-weight: 500;
        }
        .group-banner-code {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.4rem 0.85rem;
            border-radius: 999px;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            font-size: 0.85rem;
            backdrop-filter: blur(10px);
            white-space: nowrap;
        }
        @media (max-width: 480px) {
            .group-banner { padding: 1rem; flex-wrap: wrap; }
            .group-banner-code { width: 100%; text-align: center; }
        }

        /* ====================== EMPTY STATE ====================== */
        .empty-state {
            text-align: center;
            padding: 3.5rem 1.5rem;
            background: white;
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--slate-200);
            animation: fadeInUp 0.5s ease;
        }

        .empty-icon {
            width: 88px; height: 88px;
            margin: 0 auto 1.25rem;
            background: var(--indigo-50);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.25rem;
            color: var(--indigo-500);
        }

        .empty-state.error .empty-icon {
            background: rgba(244, 63, 94, 0.08);
            color: var(--rose-500);
        }

        .empty-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--slate-900);
            margin-bottom: 0.5rem;
            letter-spacing: -0.01em;
        }

        .empty-text {
            color: var(--slate-600);
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            max-width: 460px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.55;
        }

        .empty-actions {
            display: inline-flex;
            gap: 0.625rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn-primary-modern {
            padding: 0.75rem 1.5rem;
            background: var(--gradient-primary);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-indigo);
            font-family: inherit;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            text-decoration: none;
        }

        .btn-primary-modern:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 30px -10px rgba(79, 70, 229, 0.5);
            color: white;
        }

        .btn-secondary-modern {
            padding: 0.75rem 1.5rem;
            background: white;
            color: var(--slate-700);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            text-decoration: none;
        }

        .btn-secondary-modern:hover {
            border-color: var(--indigo-300);
            color: var(--indigo-700);
            transform: translateY(-1px);
        }

        /* ====================== FOOTER ====================== */
        .site-footer {
            margin-top: 3rem;
            padding: 2rem 0 1.5rem;
            text-align: center;
            border-top: 1px solid var(--slate-200);
        }

        .footer-contacts {
            display: flex;
            justify-content: center;
            gap: 0.625rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .footer-contact {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1rem;
            background: white;
            border: 1px solid var(--slate-200);
            border-radius: 999px;
            color: var(--slate-700);
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-sm);
        }

        .footer-contact:hover {
            transform: translateY(-2px);
            border-color: var(--indigo-300);
            color: var(--indigo-700);
            box-shadow: var(--shadow-md);
        }

        .footer-contact.zalo:hover    { color: #0068ff; border-color: #0068ff; }
        .footer-contact.telegram:hover { color: #0088cc; border-color: #0088cc; }

        .footer-contact i { font-size: 0.95rem; }

        .footer-text {
            color: var(--slate-500);
            font-size: 0.8rem;
            font-weight: 500;
        }

        .footer-text .secure {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--emerald-600);
            font-weight: 700;
            margin-bottom: 0.4rem;
        }

        /* ====================== RESPONSIVE ====================== */
        @media (max-width: 768px) {
            .hero { padding: 1.5rem 0 1rem; }
            .hero-headline { font-size: 1.5rem; }
            .hero-sub { font-size: 0.95rem; }
            .brand-icon { width: 48px; height: 48px; font-size: 1.25rem; }
            .brand-text h1 { font-size: 1.45rem; }

            .search-card { padding: 1.25rem; border-radius: var(--radius-xl); }
            .search-input-group { flex-direction: column; gap: 0.625rem; }
            .search-input { padding: 1rem 1rem 1rem 2.85rem; font-size: 0.95rem; }
            .search-icon-left { left: 1.05rem; font-size: 1rem; }
            .search-btn { padding: 1rem; justify-content: center; }

            .trust-row { gap: 0.5rem; }
            .trust-badge { padding: 0.7rem 0.5rem; font-size: 0.75rem; flex-direction: column; gap: 0.3rem; }

            .stats-section { gap: 0.625rem; }
            .stat-card { padding: 1.1rem 0.75rem; }
            .stat-number { font-size: 1.55rem; }
            .stat-label { font-size: 0.7rem; }

            .customer-grid { flex-direction: column; align-items: stretch; }
            .customer-left { flex-direction: column; text-align: center; }
            .customer-meta { justify-content: center; }
            .service-count-badge { align-self: center; }

            .services-section { grid-template-columns: 1fr; gap: 1rem; }
            .service-card { padding: 1.25rem; }

            .empty-state { padding: 2.5rem 1rem; }
            .empty-icon { width: 72px; height: 72px; font-size: 1.85rem; }
            .empty-title { font-size: 1.15rem; }
        }

        @media (max-width: 480px) {
            .container-fluid { padding: 1rem 0.75rem; }
            .brand { gap: 0.625rem; margin-bottom: 1rem; }
            .brand-icon { width: 40px; height: 40px; font-size: 1.1rem; }
            .brand-text h1 { font-size: 1.25rem; }
            .brand-text .tagline { font-size: 0.78rem; }
            .hero-headline { font-size: 1.3rem; }
            .hero-sub { font-size: 0.88rem; }

            .stat-card { padding: 0.9rem 0.5rem; }
            .stat-number { font-size: 1.35rem; }
            .stat-label { font-size: 0.6rem; letter-spacing: 0.02em; }
            .stat-label i { display: none; }

            .trust-badge { font-size: 0.7rem; padding: 0.55rem 0.4rem; }

            .customer-card, .empty-state { padding: 1.5rem 1.25rem; border-radius: var(--radius-xl); }
            .customer-info h3 { font-size: 1.25rem; }

            .service-card { padding: 1.1rem; }
            .service-icon { width: 44px; height: 44px; font-size: 1.05rem; }
            .service-name { font-size: 1rem; }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        {{-- ========== HERO ========== --}}
        <header class="hero">
            <a href="{{ route('lookup.index') }}" class="brand">
                <div class="brand-icon"><i class="fas fa-unlock-alt"></i></div>
                <div class="brand-text text-start">
                    <h1>KienUnlocked</h1>
                    <div class="tagline">Tra cứu dịch vụ tài khoản số</div>
                </div>
            </a>
            <h2 class="hero-headline">
                Kiểm tra <span class="highlight">trạng thái dịch vụ</span> của bạn
                <br class="d-none d-md-inline">
                trong vài giây
            </h2>
            <p class="hero-sub">Nhập mã đơn, mã khách hàng, tên Zalo, email hoặc số điện thoại để xem chi tiết các dịch vụ đã mua.</p>
        </header>

        {{-- ========== SEARCH ========== --}}
        <section class="search-section">
            <div class="search-card">
                <form id="lookupForm">
                    <div class="search-wrapper">
                        <div class="search-input-group">
                            <div class="search-input-wrap">
                                <i class="fas fa-search search-icon-left"></i>
                                <input
                                    type="text"
                                    class="search-input"
                                    id="searchInput"
                                    value="{{ $code ?? '' }}"
                                    placeholder="VD: DH-260502-001 / KUN12345 / 0901234567"
                                    autocomplete="off"
                                    required>
                            </div>
                            <button type="submit" class="search-btn" id="searchBtn">
                                <i class="fas fa-arrow-right"></i>
                                <span>Tra cứu</span>
                            </button>
                        </div>
                        <div class="autocomplete-suggestions" id="autocompleteSuggestions"></div>
                    </div>
                </form>
                <div class="search-hint">
                    Hỗ trợ: <code>DH-XXX</code>, <code>KUN/CTV</code>, <strong>Tên Zalo</strong>, <strong>Email</strong>, <strong>SĐT</strong>
                </div>
            </div>

            {{-- Trust badges --}}
            <div class="trust-row">
                <div class="trust-badge"><i class="fas fa-bolt"></i><span>Kết quả tức thì</span></div>
                <div class="trust-badge"><i class="fas fa-shield-alt"></i><span>Bảo mật thông tin</span></div>
                <div class="trust-badge"><i class="fas fa-headset"></i><span>Hỗ trợ 24/7</span></div>
            </div>
        </section>

        {{-- ========== STATS ========== --}}
        @isset($stats)
        <section class="stats-section">
            <div class="stat-card">
                <div class="stat-number">{{ number_format($stats['customers']) }}+</div>
                <div class="stat-label"><i class="fas fa-users"></i>Khách hàng</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ number_format($stats['services']) }}+</div>
                <div class="stat-label"><i class="fas fa-bolt"></i>Đang hoạt động</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ number_format($stats['packages']) }}+</div>
                <div class="stat-label"><i class="fas fa-box-open"></i>Gói dịch vụ</div>
            </div>
        </section>
        @endisset

        {{-- ========== CATEGORIES ========== --}}
        @isset($categories)
        @if($categories->count() > 0)
        <section class="categories-section">
            <div class="categories-title">Danh mục dịch vụ</div>
            <div class="categories-grid">
                @foreach($categories as $cat)
                <span class="category-chip">
                    <i class="fas fa-folder-open"></i>
                    {{ $cat->name }}
                    <span class="count">{{ $cat->active_count }}</span>
                </span>
                @endforeach
            </div>
        </section>
        @endif
        @endisset

        {{-- ========== LOADING ========== --}}
        <div class="loading-container" id="loadingContainer">
            <div class="spinner-modern"></div>
            <p class="loading-text">Đang tra cứu thông tin...</p>
        </div>

        {{-- ========== RESULTS ========== --}}
        <div class="results-container" id="resultsContainer"></div>

        {{-- ========== FOOTER ========== --}}
        <footer class="site-footer">
            <div class="footer-contacts">
                {{-- TODO: thay {ZALO_URL} bằng link Zalo thực tế (vd: https://zalo.me/0901234567) --}}
                <a href="https://zalo.me/" target="_blank" rel="noopener" class="footer-contact zalo">
                    <i class="fas fa-comment-dots"></i> Zalo hỗ trợ
                </a>
                <a href="mailto:support@truycuu.io.vn" class="footer-contact">
                    <i class="fas fa-envelope"></i> Email
                </a>
            </div>
            <div class="footer-text">
                <div class="secure"><i class="fas fa-lock"></i> Thông tin được mã hoá an toàn</div>
                <div>© {{ date('Y') }} KienUnlocked · Tra cứu tài khoản số</div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('lookupForm');
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            const loadingContainer = document.getElementById('loadingContainer');
            const resultsContainer = document.getElementById('resultsContainer');
            const autocompleteSuggestions = document.getElementById('autocompleteSuggestions');

            searchInput.focus();

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                performSearch();
            });

            // Auto-search nếu URL có ?code=DH-XXX (link tracking từ Telegram noti khi paid)
            if (searchInput.value.trim() !== '') {
                performSearch();
            }

            function performSearch() {
                const query = searchInput.value.trim();
                if (!query) return;

                showLoading();

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch('{{ route("lookup.search") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ code: query })
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
                searchBtn.disabled = true;
            }

            function hideLoading() {
                loadingContainer.classList.remove('active');
                searchBtn.disabled = false;
            }

            function displayResults(data) {
                const customer = data.customer;
                const services = data.services || [];
                const groupCode = data.group_code || null;

                const initials = customer.name.split(' ')
                    .map(n => n[0])
                    .filter(Boolean)
                    .join('')
                    .substring(0, 2)
                    .toUpperCase();

                // Group banner — nếu khách search bằng GR-XXX, hiện banner trên cùng
                let html = '';
                if (groupCode) {
                    html += `
                        <div class="group-banner">
                            <div class="group-banner-icon"><i class="fas fa-shopping-cart"></i></div>
                            <div class="group-banner-text">
                                <div class="title">Lô đơn — ${services.length} dịch vụ mua cùng lúc</div>
                                <div class="subtitle">Khách hàng đã thanh toán 1 lần cho tất cả ${services.length} dịch vụ trong lô này.</div>
                            </div>
                            <div class="group-banner-code">${escapeHtml(groupCode)}</div>
                        </div>
                    `;
                }

                html += `
                    <div class="customer-card">
                        <div class="customer-grid">
                            <div class="customer-left">
                                <div class="customer-avatar">${escapeHtml(initials || 'KH')}</div>
                                <div class="customer-info">
                                    <h3>${escapeHtml(customer.name)}</h3>
                                    <div class="customer-code-pill">
                                        <i class="fas fa-id-card"></i> ${escapeHtml(customer.customer_code)}
                                    </div>
                                    <div class="customer-meta">
                                        ${customer.email ? `<div class="meta-item"><i class="fas fa-envelope"></i>${escapeHtml(customer.email)}</div>` : ''}
                                        ${customer.phone ? `<div class="meta-item"><i class="fas fa-phone"></i>${escapeHtml(customer.phone)}</div>` : ''}
                                        <div class="meta-item"><i class="fas fa-calendar"></i>Khách hàng từ ${escapeHtml(customer.created_at)}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="service-count-badge">
                                <i class="fas fa-box"></i>
                                <span>${services.length} dịch vụ</span>
                            </div>
                        </div>
                    </div>
                `;

                if (services.length > 0) {
                    html += '<div class="services-section">';
                    services.forEach(service => {
                        html += createServiceCard(service);
                    });
                    html += '</div>';
                } else {
                    html += createNoServiceState();
                }

                resultsContainer.innerHTML = html;
                resultsContainer.classList.add('active');

                setTimeout(() => {
                    resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 80);
            }

            function createServiceCard(service) {
                const status = getServiceStatus(service);
                const progress = getProgress(service);
                const categoryIcon = getCategoryIcon(service.category_name);

                return `
                    <div class="service-card ${status.cardClass}">
                        <div class="service-header">
                            <div class="service-icon"><i class="${categoryIcon}"></i></div>
                            <div class="service-title-group">
                                <span class="service-category">${escapeHtml(service.category_name || 'Dịch vụ')}</span>
                                <h4 class="service-name">${escapeHtml(service.package_name)}</h4>
                                ${service.order_code ? `
                                <div class="service-order-code">
                                    <i class="fas fa-receipt"></i> Mã đơn <code>${escapeHtml(service.order_code)}</code>
                                </div>` : ''}
                            </div>
                        </div>

                        <div class="status-badge ${status.badgeClass}">
                            <span class="status-dot"></span>
                            <span>${status.text}</span>
                        </div>

                        ${progress.show ? `
                        <div class="service-progress">
                            <div class="progress-info">
                                <span>${progress.label}</span>
                                <span class="days-left">${progress.right}</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width: ${progress.percent}%"></div>
                            </div>
                        </div>` : ''}

                        <div class="service-details">
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-calendar-check"></i>Kích hoạt</span>
                                <span class="detail-value">${formatDate(service.activated_at)}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-calendar-times"></i>Hết hạn</span>
                                <span class="detail-value">${formatDate(service.expires_at)}</span>
                            </div>
                        </div>
                    </div>
                `;
            }

            function createNoServiceState() {
                return `
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-box-open"></i></div>
                        <h3 class="empty-title">Chưa có dịch vụ</h3>
                        <p class="empty-text">Khách hàng này chưa được gán dịch vụ nào. Vui lòng liên hệ admin để được hỗ trợ.</p>
                    </div>
                `;
            }

            function displayError(message) {
                resultsContainer.innerHTML = `
                    <div class="empty-state error">
                        <div class="empty-icon"><i class="fas fa-search-minus"></i></div>
                        <h3 class="empty-title">Không tìm thấy kết quả</h3>
                        <p class="empty-text">${escapeHtml(message)}</p>
                        <div class="empty-actions">
                            <button class="btn-primary-modern" onclick="document.getElementById('searchInput').focus(); document.getElementById('searchInput').select();">
                                <i class="fas fa-redo"></i> Thử lại
                            </button>
                            <a href="https://zalo.me/" target="_blank" rel="noopener" class="btn-secondary-modern">
                                <i class="fas fa-comment-dots"></i> Liên hệ admin
                            </a>
                        </div>
                    </div>
                `;
                resultsContainer.classList.add('active');
            }

            function getServiceStatus(service) {
                const days = getDaysRemaining(service.expires_at);
                if (service.status !== 'active' || days <= 0) {
                    return { cardClass: 'is-expired',  badgeClass: 'status-expired',  text: 'Đã hết hạn' };
                }
                if (days <= 7) {
                    return { cardClass: 'is-expiring', badgeClass: 'status-expiring', text: 'Sắp hết hạn' };
                }
                return     { cardClass: 'is-active',   badgeClass: 'status-active',   text: 'Đang hoạt động' };
            }

            function getProgress(service) {
                if (!service.activated_at || !service.expires_at) {
                    return { show: false };
                }
                const start = new Date(service.activated_at).getTime();
                const end   = new Date(service.expires_at).getTime();
                const now   = Date.now();
                const total = end - start;
                if (total <= 0) return { show: false };

                const elapsed = Math.max(0, Math.min(total, now - start));
                const remaining = Math.max(0, end - now);
                const remainingDays = Math.ceil(remaining / (1000 * 60 * 60 * 24));

                // % time remaining (for visual: thanh xanh dài = còn nhiều)
                const remainingPercent = Math.max(0, Math.min(100, ((total - elapsed) / total) * 100));

                if (remaining <= 0) {
                    return { show: true, label: 'Đã hết hạn', right: '0 ngày', percent: 0 };
                }
                return {
                    show: true,
                    label: 'Thời gian còn lại',
                    right: remainingDays + ' ngày',
                    percent: remainingPercent.toFixed(1),
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
                if (!dateString) return '—';
                const date = new Date(dateString);
                return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
            }

            function getCategoryIcon(category) {
                const icons = {
                    'AI': 'fas fa-robot',
                    'ChatGPT': 'fas fa-robot',
                    'Claude': 'fas fa-robot',
                    'Gemini': 'fas fa-robot',
                    'Streaming': 'fas fa-tv',
                    'Netflix': 'fas fa-tv',
                    'Giáo dục': 'fas fa-graduation-cap',
                    'Học tập': 'fas fa-book',
                    'Làm việc': 'fas fa-briefcase',
                    'Giải trí': 'fas fa-gamepad',
                    'Âm nhạc': 'fas fa-music',
                    'Spotify': 'fas fa-music',
                    'Video': 'fas fa-video',
                    'YouTube': 'fab fa-youtube',
                    'Thiết kế': 'fas fa-paint-brush',
                    'CapCut': 'fas fa-film',
                    'Mạng xã hội': 'fas fa-share-alt'
                };
                for (const [key, icon] of Object.entries(icons)) {
                    if (category && category.includes(key)) return icon;
                }
                return 'fas fa-cube';
            }

            function escapeHtml(s) {
                if (s === null || s === undefined) return '';
                return String(s)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function saveRecentSearch(query) {
                try {
                    let recent = JSON.parse(localStorage.getItem('recentSearches') || '[]');
                    recent = [query, ...recent.filter(q => q !== query)].slice(0, 5);
                    localStorage.setItem('recentSearches', JSON.stringify(recent));
                } catch (e) { /* noop */ }
            }

            // Autocomplete from recent searches
            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                if (query.length < 2) {
                    autocompleteSuggestions.classList.remove('active');
                    return;
                }
                try {
                    const recent = JSON.parse(localStorage.getItem('recentSearches') || '[]');
                    const matches = recent.filter(item => item.toLowerCase().includes(query)).slice(0, 3);

                    if (matches.length > 0) {
                        autocompleteSuggestions.innerHTML = matches.map(match => `
                            <div class="suggestion-item" data-value="${escapeHtml(match)}">
                                <div class="suggestion-icon"><i class="fas fa-history"></i></div>
                                <div>${escapeHtml(match)}</div>
                            </div>
                        `).join('');
                        autocompleteSuggestions.classList.add('active');

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
                } catch (e) { /* noop */ }
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
