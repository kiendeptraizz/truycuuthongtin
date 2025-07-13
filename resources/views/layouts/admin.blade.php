<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Quản lý tài khoản số</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Optimized Admin CSS -->
    <link href="{{ asset('css/admin-optimized.css') }}" rel="stylesheet">

    <style>
        /* Minimal inline styles for critical rendering */
        :root {
            --primary: #667eea;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --dark: #374151;
            --light: #f8fafc;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #f8fafc;
            margin: 0;
            font-size: 14px;
            line-height: 1.5;
            color: #374151;
        }

        /* Critical layout styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 240px;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            z-index: 1000;
            overflow-y: auto;
        }

        .main-content {
            margin-left: 240px;
            min-height: 100vh;
        }

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }








    </style>

    @yield('styles')
</head>

<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="sidebar" id="sidebar">
                <div class="brand">
                    <h4>
                        <i class="fas fa-crown me-2"></i>
                        KienUnlocked
                    </h4>
                </div>

                <nav class="nav flex-column px-2 py-3">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-3"></i>
                        Dashboard
                    </a>

                    <a class="nav-link {{ request()->routeIs('admin.test-ui') ? 'active' : '' }}"
                        href="{{ route('admin.test-ui') }}">
                        <i class="fas fa-vial me-3"></i>
                        Test UI
                    </a>

                    <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                        href="{{ route('admin.customers.index') }}">
                        <i class="fas fa-users me-3"></i>
                        Khách hàng
                    </a>

                    <a class="nav-link {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}"
                        href="{{ route('admin.leads.index') }}">
                        <i class="fas fa-user-plus me-3"></i>
                        Lead (KH tiềm năng)
                    </a>

                    <a class="nav-link {{ request()->routeIs('admin.service-packages.*') ? 'active' : '' }}"
                        href="{{ route('admin.service-packages.index') }}">
                        <i class="fas fa-box me-3"></i>
                        Gói dịch vụ
                    </a>

                    <a class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}"
                        href="{{ route('admin.suppliers.index') }}">
                        <i class="fas fa-truck me-3"></i>
                        Nhà cung cấp
                    </a>

                    <a class="nav-link {{ request()->routeIs('admin.collaborators.*') ? 'active' : '' }}"
                        href="{{ route('admin.collaborators.index') }}">
                        <i class="fas fa-user-friends me-3"></i>
                        Cộng tác viên
                    </a>

                    <a class="nav-link {{ request()->routeIs('admin.customer-services.*') ? 'active' : '' }}"
                        href="{{ route('admin.customer-services.index') }}">
                        <i class="fas fa-link me-3"></i>
                        Dịch vụ khách hàng
                    </a>

                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                        href="{{ route('admin.reports.profit') }}">
                        <i class="fas fa-chart-line me-3"></i>
                        Báo cáo lợi nhuận
                    </a>

                    <hr class="text-white-50 mx-3">

                    <a class="nav-link {{ request()->routeIs('admin.content-scheduler.*') ? 'active' : '' }}"
                        href="{{ route('admin.content-scheduler.index') }}">
                        <i class="fas fa-calendar-alt me-3"></i>
                        Lịch đăng bài
                    </a>

                    <hr class="text-white-50 mx-3">

                    <a class="nav-link" href="{{ route('lookup.index') }}" target="_blank">
                        <i class="fas fa-search me-3"></i>
                        Trang tra cứu
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
                            <!-- Mobile menu toggle -->
                            <button class="btn btn-outline-primary d-lg-none me-3" type="button" id="sidebarToggle">
                                <i class="fas fa-bars"></i>
                            </button>
                            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="text-muted me-4 d-none d-md-block">
                                <i class="fas fa-calendar me-2"></i>
                                {{ now()->format('d/m/Y H:i') }}
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user me-2"></i>
                                    <span class="d-none d-sm-inline">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li>
                                        <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-sign-out-alt me-2 text-danger"></i>
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
                        <i class="fas fa-exclamation-circle me-2"></i>
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Optimized admin layout JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            let overlay;

            // Mobile sidebar toggle
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');

                    if (sidebar.classList.contains('show')) {
                        // Create overlay
                        if (!overlay) {
                            overlay = document.createElement('div');
                            overlay.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;';
                            overlay.addEventListener('click', () => {
                                sidebar.classList.remove('show');
                                overlay.remove();
                                overlay = null;
                            });
                            document.body.appendChild(overlay);
                        }
                    } else if (overlay) {
                        overlay.remove();
                        overlay = null;
                    }
                });
            }

            // Auto-hide alerts
            document.querySelectorAll('.alert').forEach(alert => {
                setTimeout(() => {
                    if (alert.parentNode) {
                        new bootstrap.Alert(alert).close();
                    }
                }, 5000);
            });

            // Form submission loading state
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.disabled) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
                        setTimeout(() => {
                            submitBtn.disabled = false;
                        }, 10000);
                    }
                });
            });
        });
    </script>

    <!-- Page-specific scripts -->
    @yield('scripts')

    <!-- Push scripts stack -->
    @stack('scripts')
</body>

</html>