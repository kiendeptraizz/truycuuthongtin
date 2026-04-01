@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Tổng Quan')

@section('styles')
<style>
    /* ==========================================
       DASHBOARD REDESIGN - Modern Premium Style
       ========================================== */

    /* Stat Cards - Full Gradient */
    .stat-card {
        border-radius: 16px;
        padding: 1.5rem;
        color: white;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1),
                    box-shadow 0.3s cubic-bezier(0.22, 1, 0.36, 1);
        border: none;
        min-height: 140px;
    }

    .stat-card:hover {
        transform: translateY(-6px) !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -20%;
        width: 160px;
        height: 160px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        pointer-events: none;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        bottom: -40%;
        left: -10%;
        width: 120px;
        height: 120px;
        background: rgba(255, 255, 255, 0.06);
        border-radius: 50%;
        pointer-events: none;
    }

    .stat-card.gradient-purple {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.35);
    }

    .stat-card.gradient-green {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        box-shadow: 0 8px 25px rgba(17, 153, 142, 0.35);
    }

    .stat-card.gradient-blue {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        box-shadow: 0 8px 25px rgba(79, 172, 254, 0.35);
    }

    .stat-card.gradient-orange {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        box-shadow: 0 8px 25px rgba(245, 87, 108, 0.35);
    }

    .stat-card .stat-icon {
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        backdrop-filter: blur(10px);
        transition: transform 0.3s ease;
    }

    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(-5deg);
    }

    .stat-card .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        opacity: 0.85;
        margin-bottom: 0.3rem;
    }

    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .stat-card .stat-change {
        font-size: 0.8rem;
        opacity: 0.85;
        margin-top: 0.25rem;
    }

    /* Quick Action Cards */
    .quick-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.25rem 1rem;
        border-radius: 14px;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
        border: 2px solid transparent;
        text-align: center;
        min-height: 100px;
        position: relative;
        overflow: hidden;
    }

    .quick-action::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }

    .quick-action:hover::before {
        left: 100%;
    }

    .quick-action:hover {
        transform: translateY(-6px);
        border-color: rgba(255, 255, 255, 0.3);
    }

    .quick-action .action-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-bottom: 0.6rem;
        transition: transform 0.3s ease;
    }

    .quick-action:hover .action-icon {
        transform: scale(1.15) rotate(-5deg);
    }

    .quick-action .action-label {
        font-size: 0.8rem;
        font-weight: 600;
    }

    .qa-purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); }
    .qa-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3); }
    .qa-cyan { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3); }
    .qa-yellow { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); color: white; box-shadow: 0 4px 15px rgba(246, 211, 101, 0.3); }
    .qa-outline { background: rgba(255, 255, 255, 0.9); color: #667eea; border: 2px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06); }
    .qa-outline:hover { border-color: #667eea; color: #667eea; box-shadow: 0 6px 20px rgba(102, 126, 234, 0.2); }

    .qa-purple .action-icon, .qa-green .action-icon, .qa-cyan .action-icon, .qa-yellow .action-icon {
        background: rgba(255, 255, 255, 0.2);
    }
    .qa-outline .action-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    /* Expiring item card */
    .expiring-item {
        padding: 0.75rem;
        border-radius: 10px;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .expiring-item:hover {
        background: rgba(245, 87, 108, 0.04);
        border-left-color: #f5576c;
    }

    /* System status items */
    .status-item {
        padding: 0.6rem 0.75rem;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .status-item:hover {
        background: rgba(99, 102, 241, 0.04);
    }

    .status-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.5rem;
        animation: statusPulse 2s ease-in-out infinite;
    }

    .status-indicator.online {
        background: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
    }

    @keyframes statusPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
        50% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
    }

    /* Welcome section */
    .welcome-section {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.05) 100%);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(102, 126, 234, 0.1);
    }

    /* Dashboard responsive */
    @media (max-width: 992px) {
        .stat-card .stat-value { font-size: 1.6rem; }
        .stat-card { min-height: 120px; padding: 1.25rem; }
    }

    @media (max-width: 768px) {
        .stat-card .stat-value { font-size: 1.4rem; }
        .stat-card .stat-icon { width: 44px; height: 44px; font-size: 1.1rem; }
        .quick-action { min-height: 80px; padding: 0.75rem; }
        .quick-action .action-icon { width: 36px; height: 36px; font-size: 0.9rem; }
    }

    @media (max-width: 576px) {
        .stat-card .stat-value { font-size: 1.2rem; }
    }
</style>
@endsection

@section('content')
<!-- Welcome Section -->
<div class="welcome-section">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="h4 mb-1" style="font-weight: 700; color: #1e293b;">Xin chào, {{ Auth::user()->name ?? 'Admin' }}!</h1>
            <p class="mb-0 text-muted d-none d-md-block">Tổng quan hệ thống ngày {{ now()->format('d/m/Y') }}</p>
        </div>
        <div class="text-end">
            <span class="badge bg-success pulse" style="padding: 0.5rem 1rem; font-size: 0.8rem;">
                <span class="status-indicator online"></span>
                Hệ thống hoạt động tốt
            </span>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row mb-4 g-3">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card gradient-purple">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Tổng Khách Hàng</div>
                    <div class="stat-value counter-value" data-count="{{ $totalCustomers }}">{{ number_format($totalCustomers) }}</div>
                    <div class="stat-change">
                        <i class="fas fa-arrow-up me-1"></i> Tăng trưởng ổn định
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stat-card gradient-green">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Dịch Vụ Hoạt Động</div>
                    <div class="stat-value counter-value" data-count="{{ $totalActiveServices }}">{{ number_format($totalActiveServices) }}</div>
                    <div class="stat-change">
                        <i class="fas fa-sync-alt me-1"></i> Đang vận hành
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stat-card gradient-blue">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Gói Dịch Vụ</div>
                    <div class="stat-value counter-value" data-count="{{ $totalServicePackages }}">{{ number_format($totalServicePackages) }}</div>
                    <div class="stat-change">
                        <i class="fas fa-box me-1"></i> Đa dạng sản phẩm
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-cube"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stat-card gradient-orange">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Sắp Hết Hạn</div>
                    <div class="stat-value counter-value" data-count="{{ $expiringSoonServices }}">{{ number_format($expiringSoonServices) }}</div>
                    <div class="stat-change">
                        <i class="fas fa-exclamation-triangle me-1"></i> Cần chú ý
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm" style="border: none;">
            <div class="card-header py-3" style="background: transparent; border-bottom: 1px solid #f1f5f9;">
                <h6 class="m-0" style="font-weight: 700; color: #1e293b;">
                    <i class="fas fa-bolt text-warning me-2"></i>Hành Động Nhanh
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg col-md-4 col-6">
                        <a href="{{ route('admin.customers.create') }}" class="quick-action qa-purple">
                            <div class="action-icon"><i class="fas fa-user-plus"></i></div>
                            <span class="action-label">Thêm KH</span>
                        </a>
                    </div>
                    <div class="col-lg col-md-4 col-6">
                        <a href="{{ route('admin.customer-services.create') }}" class="quick-action qa-green">
                            <div class="action-icon"><i class="fas fa-link"></i></div>
                            <span class="action-label">Gán Dịch Vụ</span>
                        </a>
                    </div>
                    <div class="col-lg col-md-4 col-6">
                        <a href="{{ route('admin.service-packages.create') }}" class="quick-action qa-cyan">
                            <div class="action-icon"><i class="fas fa-box-open"></i></div>
                            <span class="action-label">Thêm Gói</span>
                        </a>
                    </div>
                    <div class="col-lg col-md-4 col-6">
                        <a href="{{ route('admin.backup.index') }}" class="quick-action qa-yellow">
                            <div class="action-icon"><i class="fas fa-shield-alt"></i></div>
                            <span class="action-label">Backup</span>
                        </a>
                    </div>
                    <div class="col-lg col-md-4 col-6">
                        <a href="{{ route('lookup.index') }}" target="_blank" class="quick-action qa-outline">
                            <div class="action-icon"><i class="fas fa-search"></i></div>
                            <span class="action-label">Tra Cứu</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <!-- Recent Activities -->
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4" style="border: none;">
            <div class="card-header py-3 d-flex align-items-center justify-content-between" style="background: transparent; border-bottom: 1px solid #f1f5f9;">
                <h6 class="m-0" style="font-weight: 700; color: #1e293b;">
                    <i class="fas fa-history text-primary me-2"></i>Hoạt Động Gần Đây
                </h6>
                <a href="{{ route('admin.customer-services.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 20px;">
                    Xem Tất Cả <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentAssignments->count() > 0)
                    <div class="table-responsive">
                        <table class="table mb-0" style="border-collapse: separate; border-spacing: 0;">
                            <thead>
                                <tr>
                                    <th style="padding: 0.85rem 1.25rem; background: #f8fafc; border-bottom: 2px solid #e2e8f0; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b;">Khách Hàng</th>
                                    <th style="padding: 0.85rem 1.25rem; background: #f8fafc; border-bottom: 2px solid #e2e8f0; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b;">Dịch Vụ</th>
                                    <th style="padding: 0.85rem 1.25rem; background: #f8fafc; border-bottom: 2px solid #e2e8f0; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b;">Ngày Gán</th>
                                    <th style="padding: 0.85rem 1.25rem; background: #f8fafc; border-bottom: 2px solid #e2e8f0; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b;">Trạng Thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAssignments as $assignment)
                                    <tr>
                                        <td style="padding: 0.85rem 1.25rem;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                    <i class="fas fa-user text-white" style="font-size: 0.75rem;"></i>
                                                </div>
                                                <div>
                                                    <div style="font-weight: 600; color: #1e293b; font-size: 0.85rem;">{{ $assignment->customer->name }}</div>
                                                    <div style="font-size: 0.75rem; color: #94a3b8;">{{ $assignment->customer->customer_code }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 0.85rem 1.25rem;">
                                            <div style="font-weight: 600; color: #334155; font-size: 0.85rem;">{{ $assignment->servicePackage->name }}</div>
                                            <div style="font-size: 0.75rem; color: #94a3b8;">{{ number_format($assignment->servicePackage->price) }}đ</div>
                                        </td>
                                        <td style="padding: 0.85rem 1.25rem; color: #64748b; font-size: 0.85rem;">
                                            {{ $assignment->created_at->format('d/m/Y') }}
                                        </td>
                                        <td style="padding: 0.85rem 1.25rem;">
                                            @if($assignment->status === 'active')
                                                <span class="badge" style="background: linear-gradient(135deg, #10b981, #059669); padding: 0.4rem 0.85rem; border-radius: 20px; font-size: 0.72rem;">Hoạt động</span>
                                            @elseif($assignment->status === 'pending')
                                                <span class="badge" style="background: linear-gradient(135deg, #f59e0b, #d97706); padding: 0.4rem 0.85rem; border-radius: 20px; font-size: 0.72rem;">Chờ kích hoạt</span>
                                            @else
                                                <span class="badge" style="background: linear-gradient(135deg, #64748b, #475569); padding: 0.4rem 0.85rem; border-radius: 20px; font-size: 0.72rem;">{{ ucfirst($assignment->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div style="width: 64px; height: 64px; margin: 0 auto 1rem; background: linear-gradient(135deg, #f1f5f9, #e2e8f0); border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-inbox fa-lg" style="color: #94a3b8;"></i>
                        </div>
                        <p class="text-muted mb-0">Chưa có hoạt động nào gần đây</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <!-- Expiring Services -->
        <div class="card shadow-sm mb-4" style="border: none;">
            <div class="card-header py-3" style="background: transparent; border-bottom: 1px solid #f1f5f9;">
                <h6 class="m-0" style="font-weight: 700; color: #1e293b;">
                    <i class="fas fa-clock text-warning me-2"></i>Sắp Hết Hạn
                </h6>
            </div>
            <div class="card-body">
                @if($expiringSoon->count() > 0)
                    @foreach($expiringSoon->take(5) as $service)
                        <div class="expiring-item d-flex align-items-center mb-2 gap-3">
                            <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #fef3c7, #fde68a); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-clock" style="color: #d97706; font-size: 0.8rem;"></i>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div style="font-weight: 600; color: #1e293b; font-size: 0.85rem;" class="text-truncate">{{ $service->customer->name }}</div>
                                <div style="font-size: 0.75rem; color: #64748b;" class="text-truncate">{{ $service->servicePackage->name }}</div>
                                <div style="font-size: 0.72rem; color: #ef4444; font-weight: 500;">
                                    <i class="fas fa-calendar-times me-1"></i>{{ $service->expires_at->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($expiringSoon->count() > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.customer-services.index', ['filter' => 'expiring']) }}" class="btn btn-sm btn-outline-warning" style="border-radius: 20px;">
                                Xem thêm {{ $expiringSoon->count() - 5 }} dịch vụ <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <div style="width: 48px; height: 48px; margin: 0 auto 0.75rem; background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check" style="color: #059669;"></i>
                        </div>
                        <p class="text-muted small mb-0">Không có dịch vụ sắp hết hạn</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- System Status -->
        <div class="card shadow-sm mb-4" style="border: none;">
            <div class="card-header py-3" style="background: transparent; border-bottom: 1px solid #f1f5f9;">
                <h6 class="m-0" style="font-weight: 700; color: #1e293b;">
                    <i class="fas fa-cog text-primary me-2"></i>Trạng Thái Hệ Thống
                </h6>
            </div>
            <div class="card-body">
                <div class="status-item d-flex align-items-center mb-2 gap-3">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #d1fae5, #a7f3d0); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-server" style="color: #059669; font-size: 0.8rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-weight: 600; color: #1e293b; font-size: 0.85rem;">Database</div>
                        <div style="font-size: 0.75rem; color: #10b981;">
                            <span class="status-indicator online"></span>Hoạt động bình thường
                        </div>
                    </div>
                </div>

                <div class="status-item d-flex align-items-center mb-2 gap-3">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #d1fae5, #a7f3d0); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shield-alt" style="color: #059669; font-size: 0.8rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-weight: 600; color: #1e293b; font-size: 0.85rem;">Backup System</div>
                        <div style="font-size: 0.75rem; color: #10b981;">
                            <span class="status-indicator online"></span>Tự động hàng ngày
                        </div>
                    </div>
                </div>

                <div class="status-item d-flex align-items-center gap-3">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #dbeafe, #bfdbfe); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line" style="color: #2563eb; font-size: 0.8rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-weight: 600; color: #1e293b; font-size: 0.85rem;">Performance</div>
                        <div style="font-size: 0.75rem; color: #3b82f6;">
                            <span class="status-indicator online"></span>Tối ưu
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto refresh dashboard every 5 minutes
    setInterval(function() {
        location.reload();
    }, 300000);
});
</script>
@endsection
