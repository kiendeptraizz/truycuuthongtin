<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel') - Quản lý tài khoản số</title>

    <!-- Favicon and App Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.svg') }}">
    <meta name="theme-color" content="#667eea">

    <!-- PWA Meta Tags -->
    <meta name="application-name" content="Truy Cứu Thông Tin">
    <meta name="description" content="Hệ thống quản lý và tra cứu thông tin tài khoản số">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Preconnect + DNS Prefetch for faster external resource loading -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

    <!-- Preload critical CSS -->
    <link rel="preload" href="{{ asset('css/modern-admin.css') }}?v={{ config('app.asset_version', '1.0') }}" as="style">

    <!-- Google Fonts - load async for better performance -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    </noscript>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 - Local -->
    <link rel="stylesheet" href="{{ asset('css/fontawesome/all.min.css') }}" />

    <!-- Modern Admin CSS -->
    <link href="{{ asset('css/modern-admin.css') }}?v={{ config('app.asset_version', '1.0') }}" rel="stylesheet">

    <!-- Navigation Fix CSS -->
    <link href="{{ asset('css/navigation-fix.css') }}?v={{ config('app.asset_version', '1.0') }}" rel="stylesheet">

    <!-- Layout Optimization CSS (Responsive) -->
    <link href="{{ asset('css/layout-optimization.css') }}?v={{ config('app.asset_version', '1.0') }}" rel="stylesheet">

    <!-- Performance Optimization CSS -->
    <link href="{{ asset('css/performance.css') }}?v={{ config('app.asset_version', '1.0') }}" rel="stylesheet">

    <!-- UI Enhancements - Smooth Animations -->
    <link href="{{ asset('css/ui-enhancements.css') }}?v={{ config('app.asset_version', '1.0') }}" rel="stylesheet">

    <style>
        /* Prevent white screen issues */
        html,
        body {
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Sidebar submenu chevron animation */
        .sidebar .nav-link[data-bs-toggle="collapse"] .fa-chevron-down {
            transition: transform 0.3s ease;
        }

        .sidebar .nav-link[data-bs-toggle="collapse"][aria-expanded="true"] .fa-chevron-down {
            transform: rotate(180deg);
        }
    </style>

    @yield('styles')
</head>

<body>
    <!-- Top Loading Bar -->
    <div class="top-loading-bar" id="topLoadingBar"></div>

    <!-- Mobile Navigation Offcanvas -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel"
        style="max-width: 280px; background: linear-gradient(180deg, #1e1b4b 0%, #312e81 50%, #3730a3 100%);">
        <div class="offcanvas-header border-bottom border-white border-opacity-10 py-3">
            <div class="d-flex align-items-center">
                <img src="{{ asset('logo.svg') }}" alt="Logo" style="height: 36px; width: auto;" class="me-2">
                <span class="text-white fw-bold">Menu</span>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
        </div>
        <div class="offcanvas-body p-0">
            <nav class="nav flex-column py-2">
                <a class="nav-link text-white py-2 px-3 {{ request()->routeIs('admin.dashboard') ? 'active bg-white bg-opacity-10' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-home me-3"></i>Dashboard
                </a>
                <hr class="text-white-50 my-1 mx-3">
                <a class="nav-link text-white py-2 px-3 {{ request()->routeIs('admin.customers.*') ? 'active bg-white bg-opacity-10' : '' }}"
                    href="{{ route('admin.customers.index') }}">
                    <i class="fas fa-users me-3"></i>Khách hàng
                </a>
                <a class="nav-link text-white py-2 px-3 {{ request()->routeIs('admin.customer-services.*') && !request()->routeIs('admin.customer-services.statistics') ? 'active bg-white bg-opacity-10' : '' }}"
                    href="{{ route('admin.customer-services.index') }}">
                    <i class="fas fa-link me-3"></i>Dịch vụ khách hàng
                </a>
                <a class="nav-link text-white py-2 px-3 {{ request()->routeIs('admin.customer-services.statistics') ? 'active bg-white bg-opacity-10' : '' }}"
                    href="{{ route('admin.customer-services.statistics') }}">
                    <i class="fas fa-chart-bar me-3"></i>Thống kê dịch vụ
                </a>
                <a class="nav-link text-white py-2 px-3 {{ request()->routeIs('admin.archived-services.*') ? 'active bg-white bg-opacity-10' : '' }}"
                    href="{{ route('admin.archived-services.index') }}">
                    <i class="fas fa-archive me-3"></i>Dịch vụ lưu trữ
                </a>
                <hr class="text-white-50 my-1 mx-3">
                <a class="nav-link text-white py-2 px-3 {{ request()->routeIs('admin.family-accounts.*') ? 'active bg-white bg-opacity-10' : '' }}"
                    href="{{ route('admin.family-accounts.index') }}">
                    <i class="fas fa-house-user me-3"></i>Family Accounts
                </a>
                <a class="nav-link text-white py-2 px-3 {{ request()->routeIs('admin.shared-accounts.index') || request()->routeIs('admin.shared-accounts.show') ? 'active bg-white bg-opacity-10' : '' }}"
                    href="{{ route('admin.shared-accounts.index') }}">
                    <i class="fas fa-share-nodes me-3"></i>Shared Accounts
                </a>
                <a class="nav-link text-white py-2 px-3 ps-5 {{ request()->routeIs('admin.shared-accounts.credentials*') ? 'active bg-white bg-opacity-10' : '' }}"
                    href="{{ route('admin.shared-accounts.credentials') }}" style="font-size: 0.85em;">
                    <i class="fas fa-key me-3"></i>Quản lý tài khoản
                </a>
                <hr class="text-white-50 my-1 mx-3">
                <a class="nav-link text-white py-2 px-3 {{ request()->routeIs('admin.service-categories.*') ? 'active bg-white bg-opacity-10' : '' }}"
                    href="{{ route('admin.service-categories.index') }}">
                    <i class="fas fa-tags me-3"></i>Danh mục dịch vụ
                </a>
                <a class="nav-link text-white py-2 px-3 {{ request()->routeIs('admin.service-packages.*') ? 'active bg-white bg-opacity-10' : '' }}"
                    href="{{ route('admin.service-packages.index') }}">
                    <i class="fas fa-cube me-3"></i>Gói dịch vụ
                </a>
                <hr class="text-white-50 my-1 mx-3">
                <a class="nav-link text-white py-2 px-3 {{ request()->routeIs('admin.zalo.*') ? 'active bg-white bg-opacity-10' : '' }}"
                    href="{{ route('admin.zalo.dashboard') }}">
                    <i class="fas fa-comments me-3"></i>Zalo Marketing
                </a>
                <hr class="text-white-50 my-1 mx-3">
                <a class="nav-link text-white py-2 px-3 {{ request()->routeIs('admin.revenue.*') ? 'active bg-white bg-opacity-10' : '' }}"
                    href="{{ route('admin.revenue.index') }}">
                    <i class="fas fa-chart-line me-3"></i>Thống kê lợi nhuận
                </a>
                <a class="nav-link text-white py-2 px-3 {{ request()->routeIs('admin.backup.*') ? 'active bg-white bg-opacity-10' : '' }}"
                    href="{{ route('admin.backup.index') }}">
                    <i class="fas fa-database me-3"></i>Backup
                </a>
                <hr class="text-white-50 my-1 mx-3">
                <a class="nav-link text-white py-2 px-3 {{ request()->routeIs('admin.resources.*') ? 'active bg-white bg-opacity-10' : '' }}"
                    href="{{ route('admin.resources.index') }}">
                    <i class="fas fa-boxes me-3"></i>Quản lý Tài nguyên
                </a>
                <hr class="text-white-50 my-1 mx-3">
                <a class="nav-link text-white py-2 px-3" href="{{ route('lookup.index') }}" target="_blank">
                    <i class="fas fa-search me-3"></i>Tra cứu công khai
                    <i class="fas fa-external-link-alt ms-2 small"></i>
                </a>
            </nav>
        </div>
    </div>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar Overlay for mobile -->
            <div class="sidebar-overlay" id="sidebarOverlay"></div>

            <!-- Sidebar -->
            <div class="sidebar" id="sidebar">
                <div class="brand">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('logo.svg') }}" alt="Logo" style="height: 40px; width: auto;" class="me-2">
                    </div>
                </div>

                <nav class="nav flex-column px-2 py-3">
                    <!-- Core Dashboard -->
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-home me-3"></i>
                        Dashboard
                    </a>

                    <hr class="text-white-50 mx-3">

                    <!-- Customer Management -->
                    <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                        href="{{ route('admin.customers.index') }}">
                        <i class="fas fa-users me-3"></i>
                        Khách hàng
                    </a>

                    <a class="nav-link {{ request()->routeIs('admin.customer-services.*') && !request()->routeIs('admin.customer-services.statistics') ? 'active' : '' }}"
                        href="{{ route('admin.customer-services.index') }}">
                        <i class="fas fa-link me-3"></i>
                        Dịch vụ khách hàng
                    </a>

                    <a class="nav-link {{ request()->routeIs('admin.customer-services.statistics') ? 'active' : '' }}"
                        href="{{ route('admin.customer-services.statistics') }}">
                        <i class="fas fa-chart-bar me-3"></i>
                        Thống kê dịch vụ
                    </a>

                    <a class="nav-link {{ request()->routeIs('admin.archived-services.*') ? 'active' : '' }}"
                        href="{{ route('admin.archived-services.index') }}">
                        <i class="fas fa-archive me-3"></i>
                        Dịch vụ lưu trữ
                    </a>

                    <hr class="text-white-50 mx-3">

                    <!-- Family & Shared Accounts -->
                    <a class="nav-link {{ request()->routeIs('admin.family-accounts.*') ? 'active' : '' }}"
                        href="{{ route('admin.family-accounts.index') }}">
                        <i class="fas fa-house-user me-3"></i>
                        Family Accounts
                    </a>

                    <a class="nav-link {{ request()->routeIs('admin.shared-accounts.index') || request()->routeIs('admin.shared-accounts.show') ? 'active' : '' }}"
                        href="{{ route('admin.shared-accounts.index') }}">
                        <i class="fas fa-share-nodes me-3"></i>
                        Shared Accounts
                    </a>

                    <a class="nav-link {{ request()->routeIs('admin.shared-accounts.credentials*') ? 'active' : '' }} ms-3"
                        href="{{ route('admin.shared-accounts.credentials') }}"
                        style="font-size: 0.85em;">
                        <i class="fas fa-key me-3"></i>
                        Quản lý tài khoản
                    </a>

                    <hr class="text-white-50 mx-3">

                    <!-- Service Management -->
                    <a class="nav-link {{ request()->routeIs('admin.service-categories.*') ? 'active' : '' }}"
                        href="{{ route('admin.service-categories.index') }}">
                        <i class="fas fa-tags me-3"></i>
                        Danh mục dịch vụ
                    </a>

                    <a class="nav-link {{ request()->routeIs('admin.service-packages.*') ? 'active' : '' }}"
                        href="{{ route('admin.service-packages.index') }}">
                        <i class="fas fa-cube me-3"></i>
                        Gói dịch vụ
                    </a>

                    <hr class="text-white-50 mx-3">

                    <!-- Zalo Marketing (Dropdown) -->
                    <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.zalo.*') ? 'active' : '' }}"
                        data-bs-toggle="collapse" href="#zaloSubmenu" role="button"
                        aria-expanded="{{ request()->routeIs('admin.zalo.*') ? 'true' : 'false' }}">
                        <span>
                            <i class="fas fa-comments me-3"></i>
                            Zalo Marketing
                        </span>
                        <i class="fas fa-chevron-down small"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.zalo.*') ? 'show' : '' }}" id="zaloSubmenu">
                        <a class="nav-link ps-5 py-1 {{ request()->routeIs('admin.zalo.dashboard') || request()->routeIs('admin.zalo.conversion-funnel') ? 'active' : '' }}"
                            href="{{ route('admin.zalo.dashboard') }}" style="font-size: 0.85em;">
                            <i class="fas fa-chart-pie me-2"></i>Dashboard
                        </a>
                        <a class="nav-link ps-5 py-1 {{ request()->routeIs('admin.zalo.accounts.*') ? 'active' : '' }}"
                            href="{{ route('admin.zalo.accounts.index') }}" style="font-size: 0.85em;">
                            <i class="fas fa-user-circle me-2"></i>Tài khoản
                        </a>
                        <a class="nav-link ps-5 py-1 {{ request()->routeIs('admin.zalo.groups.*') ? 'active' : '' }}"
                            href="{{ route('admin.zalo.groups.index') }}" style="font-size: 0.85em;">
                            <i class="fas fa-users me-2"></i>Nhóm mục tiêu
                        </a>
                        <a class="nav-link ps-5 py-1 {{ request()->routeIs('admin.zalo.campaigns.*') ? 'active' : '' }}"
                            href="{{ route('admin.zalo.campaigns.index') }}" style="font-size: 0.85em;">
                            <i class="fas fa-paper-plane me-2"></i>Chiến dịch
                        </a>
                    </div>

                    <hr class="text-white-50 mx-3">

                    <!-- Reports & Analytics -->

                    <a class="nav-link {{ request()->routeIs('admin.revenue.*') ? 'active' : '' }}"
                        href="{{ route('admin.revenue.index') }}">
                        <i class="fas fa-chart-line me-3"></i>
                        Thống kê lợi nhuận
                    </a>

                    <a class="nav-link {{ request()->routeIs('admin.backup.*') ? 'active' : '' }}"
                        href="{{ route('admin.backup.index') }}">
                        <i class="fas fa-database me-3"></i>
                        Backup
                    </a>

                    <hr class="text-white-50 mx-3">

                    <!-- Resource Management -->
                    <a class="nav-link {{ request()->routeIs('admin.resources.*') ? 'active' : '' }}"
                        href="{{ route('admin.resources.index') }}">
                        <i class="fas fa-boxes me-3"></i>
                        Quản lý Tài nguyên
                    </a>

                    <hr class="text-white-50 mx-3">

                    <!-- External Tools -->
                    <a class="nav-link" href="{{ route('lookup.index') }}" target="_blank">
                        <i class="fas fa-search me-3"></i>
                        Tra cứu công khai
                        <i class="fas fa-external-link-alt ms-auto"></i>
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Header -->
                <div class="main-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <!-- Mobile menu toggle - dùng Bootstrap Offcanvas -->
                            <button class="btn btn-outline-primary me-2 me-md-3 sidebar-toggle-btn" type="button"
                                data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                                <i class="fas fa-bars"></i>
                            </button>
                            <h1 class="page-title text-truncate">@yield('page-title', 'Dashboard')</h1>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="text-muted me-3 d-none d-md-block">
                                <i class="fas fa-calendar me-2"></i>
                                {{ now()->format('d/m/Y H:i') }}
                            </div>

                            <!-- User dropdown -->
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-circle me-1"></i>
                                    <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'Admin' }}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li>
                                        <span class="dropdown-item-text text-muted">
                                            <small>{{ Auth::user()->email ?? '' }}</small>
                                        </span>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a href="{{ route('password.change') }}" class="dropdown-item">
                                            <i class="fas fa-key me-2"></i>
                                            Đổi mật khẩu
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-sign-out-alt me-2"></i>
                                                Đăng xuất
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="content-area">
                    <!-- Alerts -->
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-times-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <!-- Main Content -->
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery + Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

    <!-- Enhanced Tables JS -->
    <script src="{{ asset('js/enhanced-tables.js') }}" defer></script>

    <!-- Enhanced Forms JS -->
    <script src="{{ asset('js/enhanced-forms.js') }}" defer></script>

    <!-- Page Navigation Fix -->
    <script src="{{ asset('js/page-navigation-fix.js') }}" defer></script>

    <!-- Currency Formatter -->
    <script src="{{ asset('js/currency-formatter.js') }}" defer></script>

    <!-- Admin Layout JS (utilities, alerts, animations) -->
    <script src="{{ asset('js/admin-layout.js') }}" defer></script>

    <!-- Page-specific scripts -->
    @yield('scripts')

    @stack('scripts')
</body>

</html>