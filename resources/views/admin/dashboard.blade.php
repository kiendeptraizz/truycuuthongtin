@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Thống kê tổng quan -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card card-stats">
            <div class="card-body text-center">
                <div class="stats-icon mx-auto mb-3 bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <h2 class="stats-number">{{ number_format($totalCustomers) }}</h2>
                <p class="stats-label mb-0">Khách hàng</p>
                <small class="text-success">
                    <i class="fas fa-arrow-up me-1"></i>
                    Tăng trưởng tốt
                </small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card card-stats">
            <div class="card-body text-center">
                <div class="stats-icon mx-auto mb-3 bg-success">
                    <i class="fas fa-box"></i>
                </div>
                <h2 class="stats-number">{{ number_format($totalServicePackages) }}</h2>
                <p class="stats-label mb-0">Gói dịch vụ</p>
                <small class="text-info">
                    <i class="fas fa-chart-line me-1"></i>
                    Đa dạng sản phẩm
                </small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card card-stats">
            <div class="card-body text-center">
                <div class="stats-icon mx-auto mb-3 bg-info">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="stats-number">{{ number_format($totalActiveServices) }}</h2>
                <p class="stats-label mb-0">Dịch vụ hoạt động</p>
                <small class="text-primary">
                    <i class="fas fa-sync-alt me-1"></i>
                    Đang vận hành
                </small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card card-stats">
            <div class="card-body text-center">
                <div class="stats-icon mx-auto mb-3 bg-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 class="stats-number">{{ number_format($expiringSoonServices) }}</h2>
                <p class="stats-label mb-0">Sắp hết hạn</p>
                <small class="text-warning">
                    <i class="fas fa-clock me-1"></i>
                    Cần chú ý
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Lead Statistics -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <h4 class="mb-3">
            <i class="fas fa-user-plus text-primary me-2"></i>
            Thống kê Lead (Khách hàng tiềm năng)
        </h4>
    </div>

    <div class="col-xl-2-4 col-lg-4 col-md-6">
        <div class="card card-stats">
            <div class="card-body text-center">
                <div class="stats-icon mx-auto mb-3 bg-info">
                    <i class="fas fa-users"></i>
                </div>
                <h2 class="stats-number">{{ number_format($totalLeads) }}</h2>
                <p class="stats-label mb-0">Tổng Lead</p>
                <small class="text-info">
                    <i class="fas fa-chart-line me-1"></i>
                    Tất cả lead
                </small>
            </div>
        </div>
    </div>

    <div class="col-xl-2-4 col-lg-4 col-md-6">
        <div class="card card-stats">
            <div class="card-body text-center">
                <div class="stats-icon mx-auto mb-3 bg-success">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2 class="stats-number">{{ number_format($newLeads) }}</h2>
                <p class="stats-label mb-0">Lead mới</p>
                <small class="text-success">
                    <i class="fas fa-plus me-1"></i>
                    Chưa xử lý
                </small>
            </div>
        </div>
    </div>

    <div class="col-xl-2-4 col-lg-4 col-md-6">
        <div class="card card-stats">
            <div class="card-body text-center">
                <div class="stats-icon mx-auto mb-3 bg-warning">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <h2 class="stats-number">{{ number_format($followUpTodayLeads) }}</h2>
                <p class="stats-label mb-0">Cần theo dõi hôm nay</p>
                <small class="text-warning">
                    <i class="fas fa-clock me-1"></i>
                    Hôm nay
                </small>
            </div>
        </div>
    </div>

    <div class="col-xl-2-4 col-lg-4 col-md-6">
        <div class="card card-stats">
            <div class="card-body text-center">
                <div class="stats-icon mx-auto mb-3 bg-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 class="stats-number">{{ number_format($overdueLeads) }}</h2>
                <p class="stats-label mb-0">Quá hạn</p>
                <small class="text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Cần xử lý gấp
                </small>
            </div>
        </div>
    </div>

    <div class="col-xl-2-4 col-lg-4 col-md-6">
        <div class="card card-stats">
            <div class="card-body text-center">
                <div class="stats-icon mx-auto mb-3 bg-primary">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="stats-number">{{ number_format($convertedThisMonth) }}</h2>
                <p class="stats-label mb-0">Chuyển đổi tháng này</p>
                <small class="text-primary">
                    <i class="fas fa-trophy me-1"></i>
                    Thành công
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Lead cần chú ý -->
<div class="row g-4 mb-4">
    <div class="col-xl-6 col-lg-6">
        <div class="card ">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                        Lead khẩn cấp
                    </h5>
                    <a href="{{ route('admin.leads.index', ['priority' => 'urgent']) }}"
                        class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-eye me-1"></i>
                        Xem tất cả
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($urgentLeads->count() > 0)
                @foreach($urgentLeads as $lead)
                <div class="d-flex align-items-center p-3 border-bottom">
                    <div class="avatar-sm me-3">
                        <div class="avatar-initial bg-danger rounded-circle">
                            {{ substr($lead->name, 0, 1) }}
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $lead->name }}</h6>
                        <p class="text-muted mb-1">
                            <i class="fas fa-phone me-1"></i>{{ $lead->phone }}
                            @if($lead->servicePackage)
                            | {{ $lead->servicePackage->name }}
                            @endif
                        </p>
                        <small class="text-muted">
                            Tạo: {{ $lead->created_at->diffForHumans() }}
                            @if($lead->assignedUser)
                            | PV: {{ $lead->assignedUser->name }}
                            @endif
                        </small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-danger">{{ $lead->getPriorityName() }}</span>
                        <br><small class="text-muted">{{ $lead->getStatusName() }}</small>
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-center py-4">
                    <div class="empty-icon mb-3">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <h5 class="text-muted">Không có lead khẩn cấp</h5>
                    <p class="text-muted mb-0">Tất cả lead đều được xử lý tốt</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-lg-6">
        <div class="card ">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2 text-warning"></i>
                        Lead quá hạn theo dõi
                    </h5>
                    <a href="{{ route('admin.leads.index', ['overdue' => 1]) }}"
                        class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-eye me-1"></i>
                        Xem tất cả
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($overdueLeadsList->count() > 0)
                @foreach($overdueLeadsList as $lead)
                <div class="d-flex align-items-center p-3 border-bottom">
                    <div class="avatar-sm me-3">
                        <div class="avatar-initial bg-warning rounded-circle">
                            {{ substr($lead->name, 0, 1) }}
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $lead->name }}</h6>
                        <p class="text-muted mb-1">
                            <i class="fas fa-phone me-1"></i>{{ $lead->phone }}
                            @if($lead->servicePackage)
                            | {{ $lead->servicePackage->name }}
                            @endif
                        </p>
                        <small class="text-warning">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Quá hạn {{ $lead->getDaysOverdue() }} ngày
                        </small>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">
                            {{ $lead->next_follow_up_at->format('d/m/Y') }}
                        </small>
                        <br><span class="badge {{ $lead->getStatusBadgeClass() }}">{{ $lead->getStatusName() }}</span>
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-center py-4">
                    <div class="empty-icon mb-3">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <h5 class="text-muted">Không có lead quá hạn</h5>
                    <p class="text-muted mb-0">Tất cả lead đều được theo dõi đúng hạn</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Dịch vụ sắp hết hạn -->
    <div class="col-xl-8 col-lg-7">
        <div class="card ">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2 text-warning"></i>
                        Dịch vụ sắp hết hạn (5 ngày tới)
                    </h5>
                    <a href="{{ route('admin.customer-services.index', ['filter' => 'expiring']) }}"
                        class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-eye me-1"></i>
                        Xem tất cả
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($expiringSoon->count() > 0)
                <div class="table-container">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user me-2"></i>Khách hàng</th>
                                <th><i class="fas fa-box-open me-2"></i>Dịch vụ</th>
                                <th><i class="fas fa-calendar-times me-2"></i>Hết hạn</th>
                                <th><i class="fas fa-hourglass-half me-2"></i>Còn lại</th>
                                <th><i class="fas fa-cogs me-2"></i>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expiringSoon as $service)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initial rounded-circle bg-primary text-white me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            {{ substr($service->customer->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $service->customer->name }}</div>
                                            <small class="text-muted">{{ $service->customer->customer_code }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $service->servicePackage->name }}</div>
                                    <small class="text-muted">{{ $service->servicePackage->category->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $service->expires_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $service->expires_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    @php
                                    $daysRemaining = $service->getDaysRemaining();
                                    @endphp
                                    @if($daysRemaining <= 1)
                                        <span class="badge bg-danger">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        {{ $daysRemaining }} ngày
                                        </span>
                                        @elseif($daysRemaining <= 3)
                                            <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $daysRemaining }} ngày
                                            </span>
                                            @else
                                            <span class="badge bg-info">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $daysRemaining }} ngày
                                            </span>
                                            @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.customers.show', $service->customer) }}"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="tooltip"
                                            title="Xem chi tiết khách hàng">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.customer-services.show', $service) }}"
                                            class="btn btn-sm btn-outline-info"
                                            data-bs-toggle="tooltip"
                                            title="Xem chi tiết dịch vụ">
                                            <i class="fas fa-info-circle"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-check-circle fa-3x text-success opacity-50"></i>
                    </div>
                    <h5 class="text-muted">Không có dịch vụ nào sắp hết hạn</h5>
                    <p class="text-muted mb-0">Tất cả dịch vụ đều hoạt động bình thường</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Khách hàng mới nhất -->
    <div class="col-xl-4 col-lg-5">
        <div class="card ">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user-plus me-2 text-primary"></i>
                    Khách hàng mới nhất
                </h5>
            </div>
            <div class="card-body">
                @if($recentCustomers->count() > 0)
                <div class="customer-list">
                    @foreach($recentCustomers as $customer)
                    <div class="d-flex align-items-center p-3 mb-3 bg-light rounded">
                        <div class="avatar-initial bg-primary text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            {{ substr($customer->name, 0, 1) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $customer->name }}</div>
                            <div class="text-muted small">
                                <span class="me-3">{{ $customer->customer_code }}</span>
                                <span>{{ $customer->customerServices->count() }} dịch vụ</span>
                            </div>
                            <small class="text-muted">
                                {{ $customer->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <a href="{{ route('admin.customers.show', $customer) }}"
                            class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    @endforeach
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-users me-2"></i>
                        Xem tất cả khách hàng
                    </a>
                </div>
                @else
                <div class="text-center py-4">
                    <div class="mb-3">
                        <i class="fas fa-user-plus fa-2x text-muted opacity-50"></i>
                    </div>
                    <p class="text-muted mb-0">Chưa có khách hàng mới</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Dịch vụ phổ biến và Bài đăng -->
<div class="row g-4 mt-2">
    <!-- Dịch vụ phổ biến -->
    <div class="col-12">
        <div class="card ">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2 text-success"></i>
                    Dịch vụ phổ biến nhất
                </h5>
            </div>
            <div class="card-body">
                @if($popularServices->count() > 0)
                <div class="row g-3">
                    @foreach($popularServices as $index => $service)
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                        <div class="service-card text-center p-4 rounded-3 h-100" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%); border: 2px solid rgba(16, 185, 129, 0.2); transition: all 0.3s ease;">
                            <div class="service-rank position-absolute top-0 start-0 m-2">
                                <span class="badge bg-success rounded-pill">#{{ $index + 1 }}</span>
                            </div>
                            <div class="service-icon mb-3">
                                <div class="icon-wrapper mx-auto" style="width: 60px; height: 60px; background: var(--success-gradient); border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-box fa-2x text-white"></i>
                                </div>
                            </div>
                            <h6 class="fw-bold mb-2">{{ $service->name }}</h6>
                            <div class="mb-2">
                                <span class="badge bg-primary">
                                    <i class="fas fa-users me-1"></i>
                                    {{ $service->customer_services_count }} khách hàng
                                </span>
                            </div>
                            @if($service->category)
                            <small class="text-muted">{{ $service->category->name }}</small>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-chart-bar fa-3x text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-muted">Chưa có dữ liệu thống kê</h5>
                    <p class="text-muted mb-0">Dữ liệu sẽ hiển thị khi có khách hàng sử dụng dịch vụ</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Content Posts Status -->
<div class="row g-4 mt-2">
    <div class="col-lg-6">
        <div class="card ">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2 text-info"></i>
                        Bài đăng sắp tới (24h)
                    </h5>
                    <a href="{{ route('admin.content-scheduler.index') }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-eye me-1"></i>
                        Xem tất cả
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($upcomingPosts->count() > 0)
                <div class="post-list">
                    @foreach($upcomingPosts as $post)
                    <div class="post-item d-flex align-items-center p-3 rounded-3 mb-3" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(29, 78, 216, 0.05) 100%); border-left: 4px solid var(--info-gradient);">
                        <div class="post-icon me-3">
                            <div class="icon-wrapper" style="width: 45px; height: 45px; background: var(--info-gradient); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-calendar text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold mb-1">{{ $post->title }}</div>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="fas fa-clock me-1"></i>
                                <span class="me-3">{{ $post->scheduled_at->format('d/m/Y H:i') }}</span>
                                <i class="fas fa-users me-1"></i>
                                <span>{{ $post->target_groups_string }}</span>
                            </div>
                            <div class="mt-1">
                                <span class="badge bg-info">
                                    {{ $post->scheduled_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        <div class="ms-2">
                            <a href="{{ route('admin.content-scheduler.show', $post) }}"
                                class="btn btn-sm btn-outline-info rounded-pill"
                                data-bs-toggle="tooltip"
                                title="Xem chi tiết">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <div class="mb-3">
                        <i class="fas fa-calendar-check fa-2x text-success opacity-50"></i>
                    </div>
                    <h6 class="text-muted">Không có bài đăng nào trong 24h tới</h6>
                    <p class="text-muted mb-0 small">Lịch đăng bài trống</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card ">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                        Bài đăng quá hạn
                    </h5>
                    <a href="{{ route('admin.content-scheduler.index', ['status' => 'scheduled']) }}" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-eye me-1"></i>
                        Xem tất cả
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($overduePosts->count() > 0)
                <div class="post-list">
                    @foreach($overduePosts as $post)
                    <div class="post-item d-flex align-items-center p-3 rounded-3 mb-3" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.05) 0%, rgba(220, 38, 38, 0.05) 100%); border-left: 4px solid var(--danger-gradient);">
                        <div class="post-icon me-3">
                            <div class="icon-wrapper" style="width: 45px; height: 45px; background: var(--danger-gradient); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-exclamation text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold mb-1">{{ $post->title }}</div>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="fas fa-clock me-1"></i>
                                <span class="me-3">Đã quá {{ $post->scheduled_at->diffForHumans() }}</span>
                                <i class="fas fa-users me-1"></i>
                                <span>{{ $post->target_groups_string }}</span>
                            </div>
                            <div class="mt-1">
                                <span class="badge bg-danger">
                                    Quá hạn
                                </span>
                            </div>
                        </div>
                        <div class="ms-2">
                            <a href="{{ route('admin.content-scheduler.edit', $post) }}"
                                class="btn btn-sm btn-outline-danger rounded-pill"
                                data-bs-toggle="tooltip"
                                title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <div class="mb-3">
                        <i class="fas fa-check-circle fa-2x text-success opacity-50"></i>
                    </div>
                    <h6 class="text-muted">Không có bài đăng quá hạn</h6>
                    <p class="text-muted mb-0 small">Lịch đăng bài được quản lý tốt</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="card ">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2 text-warning"></i>
                    Thao tác nhanh
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4" style="min-height: 120px;">
                            <i class="fas fa-user-plus fa-2x mb-2"></i>
                            <span class="fw-bold">Thêm khách hàng</span>
                            <small class="text-white-50 mt-1">Khách hàng mới</small>
                        </a>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                        <a href="{{ route('admin.service-packages.create') }}" class="btn btn-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4" style="min-height: 120px;">
                            <i class="fas fa-box fa-2x mb-2"></i>
                            <span class="fw-bold">Thêm gói dịch vụ</span>
                            <small class="text-white-50 mt-1">Sản phẩm mới</small>
                        </a>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                        <a href="{{ route('admin.customer-services.create') }}" class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4" style="min-height: 120px;">
                            <i class="fas fa-link fa-2x mb-2"></i>
                            <span class="fw-bold">Gán dịch vụ</span>
                            <small class="text-white-50 mt-1">Kích hoạt dịch vụ</small>
                        </a>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                        <a href="{{ route('admin.content-scheduler.create') }}" class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4" style="min-height: 120px;">
                            <i class="fas fa-calendar-plus fa-2x mb-2"></i>
                            <span class="fw-bold">Tạo bài đăng</span>
                            <small class="text-white-50 mt-1">Lên lịch đăng</small>
                        </a>
                    </div>

                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                        <a href="{{ route('admin.reports.profit') }}" class="btn btn-dark w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4" style="min-height: 120px;">
                            <i class="fas fa-chart-line fa-2x mb-2"></i>
                            <span class="fw-bold">Báo cáo</span>
                            <small class="text-white-50 mt-1">Lợi nhuận</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* 5-column layout for lead stats */
    .col-xl-2-4 {
        flex: 0 0 auto;
        width: 20%;
    }

    @media (max-width: 1199.98px) {
        .col-xl-2-4 {
            width: 25%;
        }
    }

    @media (max-width: 991.98px) {
        .col-xl-2-4 {
            width: 50%;
        }
    }

    @media (max-width: 575.98px) {
        .col-xl-2-4 {
            width: 100%;
        }
    }
</style>
@endpush

@section('scripts')
<script>
    // Simple dashboard interactions
    document.addEventListener('DOMContentLoaded', function() {
        // Track user activity for potential auto-refresh
        ['mousemove', 'keypress', 'scroll', 'click'].forEach(event => {
            document.addEventListener(event, () => {
                localStorage.setItem('lastActivity', Date.now());
            });
        });
    });
</script>
@endsection