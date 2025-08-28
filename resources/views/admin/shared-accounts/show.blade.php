@extends('layouts.admin')

@section('title', 'Chi tiết tài khoản dùng chung')
@section('page-title', 'Chi tiết tài khoản dùng chung')

@section('styles')
<style>
    /* Compact table design */
    .table-responsive {
        overflow-x: auto;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .table {
        font-size: 0.85rem;
        margin-bottom: 0;
    }

    .table th, .table td {
        white-space: nowrap;
        vertical-align: middle;
        padding: 0.4rem 0.6rem;
    }

    .table th {
        position: sticky;
        top: 0;
        background-color: var(--bs-dark);
        z-index: 10;
        font-size: 0.8rem;
        font-weight: 600;
        border-bottom: 2px solid #495057;
    }

    /* Compact header styles */
    .table th {
        padding: 0.5rem 0.4rem;
    }

    /* Sticky action column */
    .action-column {
        position: sticky;
        right: 0;
        background-color: inherit;
        z-index: 5;
        box-shadow: -2px 0 4px rgba(0,0,0,0.1);
    }

    .table-dark .action-column {
        background-color: var(--bs-dark);
    }

    /* Compact notes display */
    .notes-compact {
        max-width: 80px;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 0.75rem;
    }

    /* Tooltip styles */
    .tooltip-trigger {
        cursor: help;
        border-bottom: 1px dotted #6c757d;
    }

    /* Status badges compact */
    .badge {
        font-size: 0.7rem;
        padding: 0.25em 0.5em;
    }

    /* Logout section styles */
    .logout-section {
        border: 2px solid #dc3545;
        border-radius: 8px;
        background: linear-gradient(135deg, #fff5f5 0%, #fff 100%);
    }

    .logout-section .card-header {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border-bottom: none;
    }

    .btn-logout-primary {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border: none;
        box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
        transition: all 0.3s ease;
    }

    .btn-logout-primary:hover {
        background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4);
        transform: translateY(-1px);
    }

    /* Responsive breakpoints */
    @media (max-width: 576px) {
        .d-none-sm {
            display: none !important;
        }
    }

    @media (max-width: 768px) {
        .d-none-md {
            display: none !important;
        }
    }

    @media (max-width: 992px) {
        .d-none-lg {
            display: none !important;
        }
    }

    @media (max-width: 1200px) {
        .d-none-xl {
            display: none !important;
        }
    }

    @media (max-width: 1400px) {
        .d-none-xxl {
            display: none !important;
        }
    }

    /* Compact customer info */
    .customer-info {
        min-width: 140px;
    }

    .customer-name {
        font-weight: 600;
        font-size: 0.85rem;
        line-height: 1.2;
    }

    .customer-details {
        font-size: 0.7rem;
        color: #6c757d;
        line-height: 1.1;
    }

    /* Compact service package */
    .service-package {
        min-width: 90px;
    }

    /* Compact expiry info */
    .expiry-info {
        min-width: 85px;
        font-size: 0.8rem;
    }

    .expiry-date {
        font-weight: 600;
    }

    .expiry-days {
        font-size: 0.7rem;
        line-height: 1.1;
    }

    /* Logout history compact */
    .logout-history {
        font-size: 0.75rem;
        color: #6c757d;
    }

    /* Action buttons compact */
    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
    }

    .dropdown-toggle::after {
        font-size: 0.6rem;
    }
</style>
@endsection

@section('content')
<!-- Thông báo -->
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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Chi tiết: {{ $email }}</h5>
                        <small class="text-muted">Dịch vụ sử dụng chung email này</small>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.shared-accounts.edit', $email) }}?source=detail" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <a href="{{ route('admin.shared-accounts.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Thống kê tài khoản -->
                <div class="row mb-4">
                    <div class="col-6 col-md-2">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h4 class="mb-1">{{ $stats['total_services'] }}</h4>
                                <small>Tổng dịch vụ</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4 class="mb-1">{{ $stats['unique_customers'] }}</h4>
                                <small>Khách hàng</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4 class="mb-1">{{ $stats['active_services'] }}</h4>
                                <small>Hoạt động</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h4 class="mb-1">{{ $stats['expiring_soon'] }}</h4>
                                <small>Sắp hết hạn</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h4 class="mb-1">{{ $stats['expired_services'] }}</h4>
                                <small>Đã hết hạn</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-2 d-none d-md-block">
                        <div class="card bg-secondary text-white">
                            <div class="card-body text-center">
                                <h4 class="mb-1">{{ $services->count() }}</h4>
                                <small>Hiển thị</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thông tin tài khoản dùng chung -->
                @php
                    $firstService = $services->first();
                @endphp
                
                @if($firstService)
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-key me-2"></i>Thông tin đăng nhập</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>Email:</strong> {{ $email }}
                                </div>
                                <div class="mb-2">
                                    <strong>Mật khẩu:</strong> 
                                    @if($firstService->login_password)
                                        <span class="password-field" data-password="{{ $firstService->login_password }}">••••••••</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="togglePasswordView(this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @else
                                        <span class="text-muted">Chưa có</span>
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <strong>Hết hạn tài khoản:</strong> 
                                    @if($firstService->password_expires_at)
                                        <span class="badge bg-{{ $firstService->password_expires_at->isPast() ? 'danger' : 'info' }}">
                                            {{ $firstService->password_expires_at->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">Không giới hạn</span>
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <strong>Đã chia sẻ:</strong>
                                    @if($firstService->is_password_shared)
                                        <span class="badge bg-success">Có</span>
                                    @else
                                        <span class="badge bg-warning">Chưa</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Xác thực 2 yếu tố</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>Mã 2FA:</strong> 
                                    @if($firstService->two_factor_code)
                                        <span class="password-field" data-password="{{ $firstService->two_factor_code }}">••••••••</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="togglePasswordView(this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @else
                                        <span class="text-muted">Chưa có</span>
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <strong>Cập nhật lần cuối:</strong> 
                                    @if($firstService->two_factor_updated_at)
                                        {{ $firstService->two_factor_updated_at->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">Chưa có</span>
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <strong>Mã khôi phục:</strong> 
                                    @if($firstService->recovery_codes && count($firstService->recovery_codes) > 0)
                                        <span class="badge bg-info">{{ count($firstService->recovery_codes) }} mã</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="showRecoveryCodes({{ json_encode($firstService->recovery_codes) }})">
                                            <i class="fas fa-eye"></i> Xem
                                        </button>
                                    @else
                                        <span class="text-muted">Chưa có</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($firstService->shared_account_notes || $firstService->customer_instructions)
                <div class="row mb-4">
                    @if($firstService->shared_account_notes)
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Ghi chú nội bộ</h6>
                            </div>
                            <div class="card-body">
                                {{ $firstService->shared_account_notes }}
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($firstService->customer_instructions)
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Hướng dẫn khách hàng</h6>
                            </div>
                            <div class="card-body">
                                {!! nl2br(e($firstService->customer_instructions)) !!}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @endif
                @endif

                @if($stats['unique_customers'] > 1)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Cảnh báo:</strong> Tài khoản này được sử dụng bởi {{ $stats['unique_customers'] }} khách hàng khác nhau. 
                    Vui lòng kiểm tra và xử lý để tránh xung đột.
                </div>
                @endif

                <!-- Logout All Devices Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card logout-section">
                            <div class="card-header text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    Quản lý Logout Devices
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <p class="mb-2">
                                            <strong>Logout All Devices:</strong> Ghi nhận việc logout tất cả thiết bị của tài khoản dùng chung này.
                                        </p>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Đây chỉ là tính năng tracking/logging, không thực hiện logout thật trên các platform.
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="{{ route('admin.shared-accounts.logout-form', $email) }}"
                                           class="btn btn-danger btn-logout-primary text-white">
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            Logout All Devices
                                        </a>
                                        <button type="button" class="btn btn-outline-secondary ms-2"
                                                onclick="loadLogoutHistory()">
                                            <i class="fas fa-history me-2"></i>
                                            Lịch sử
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bảng dịch vụ -->
                <div class="table-responsive">
                    <!-- Responsive info alert -->
                    <div class="alert alert-info alert-sm d-lg-none mb-2">
                        <i class="fas fa-info-circle me-1"></i>
                        <small>Một số cột được ẩn trên màn hình nhỏ. Cuộn ngang để xem đầy đủ.</small>
                    </div>

                    <table class="table table-striped table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th class="customer-info">KH</th>
                                <th class="service-package">Gói</th>
                                <th class="d-none-md notes-compact">Ghi chú</th>
                                <th class="d-none-lg" style="min-width: 80px;">MK</th>
                                <th class="d-none-xl" style="min-width: 70px;">KH</th>
                                <th class="expiry-info">Hết hạn</th>
                                <th style="min-width: 75px;">TT</th>
                                <th class="d-none-md" style="min-width: 70px;">Nhắc</th>
                                <th class="d-none-lg" style="min-width: 80px;">PC</th>
                                <th class="d-none-xxl logout-history" style="min-width: 80px;">Logout</th>
                                <th class="action-column" style="min-width: 60px;">TC</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                            @php
                                $isExpired = $service->expires_at && $service->expires_at->isPast();
                                $isExpiring = $service->expires_at && $service->expires_at->isFuture() && $service->expires_at->diffInDays(now()) <= 5;
                                $statusClass = $isExpired ? 'table-danger' : ($isExpiring ? 'table-warning' : '');

                                // Tạo ghi chú ngắn gọn
                                $noteText = $service->internal_notes ?: $service->shared_account_notes;
                                $noteShort = $noteText ? 'TK' . str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) : '';
                            @endphp
                            <tr class="{{ $statusClass }}">
                                <td class="customer-info">
                                    <div class="customer-name">{{ Str::limit($service->customer->name, 15) }}</div>
                                    <div class="customer-details">
                                        @if($service->customer->customer_code)
                                            <span class="badge bg-light text-dark" style="font-size: 0.6rem;">
                                                {{ $service->customer->customer_code }}
                                            </span>
                                        @endif
                                        <div><i class="fas fa-phone" style="font-size: 0.6rem;"></i> {{ Str::limit($service->customer->phone, 12) }}</div>
                                    </div>
                                </td>
                                <td class="service-package">
                                    <span class="badge bg-primary" style="font-size: 0.65rem;">
                                        {{ Str::limit($service->servicePackage->name, 8) }}
                                    </span>
                                </td>
                                <td class="d-none-md notes-compact">
                                    @if($noteShort)
                                        <span class="tooltip-trigger"
                                              title="{{ $noteText }}"
                                              data-bs-toggle="tooltip">
                                            {{ $noteShort }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="d-none-lg">
                                    <div class="d-flex align-items-center">
                                        <code id="password-{{ $service->id }}" style="display: none; font-size: 0.7rem;">{{ $service->login_password }}</code>
                                        <span id="masked-{{ $service->id }}" style="font-size: 0.7rem;">{{ str_repeat('*', min(6, strlen($service->login_password))) }}</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-1"
                                                onclick="togglePassword({{ $service->id }})" style="padding: 0.1rem 0.3rem;">
                                            <i class="fas fa-eye" id="eye-{{ $service->id }}" style="font-size: 0.6rem;"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="d-none-xl">
                                    @if($service->activated_at)
                                        <small>{{ $service->activated_at->format('d/m') }}</small>
                                    @else
                                        <span class="text-muted" style="font-size: 0.7rem;">-</span>
                                    @endif
                                </td>
                                <td class="expiry-info">
                                    @if($service->expires_at)
                                        <div class="expiry-date">{{ $service->expires_at->format('d/m/y') }}</div>
                                        <div class="expiry-days">
                                            @if($isExpired)
                                                <span class="text-danger">Hết hạn</span>
                                            @elseif($isExpiring)
                                                <span class="text-warning">{{ $service->expires_at->diffInDays(now()) }}d</span>
                                            @else
                                                <span class="text-success">{{ $service->expires_at->diffInDays(now()) }}d</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size: 0.7rem;">∞</span>
                                    @endif
                                </td>
                                <td>
                                    @if($service->status === 'active')
                                        <span class="badge bg-success">ON</span>
                                    @elseif($service->status === 'inactive')
                                        <span class="badge bg-secondary">OFF</span>
                                    @elseif($service->status === 'suspended')
                                        <span class="badge bg-warning">STOP</span>
                                    @else
                                        <span class="badge bg-danger">EXP</span>
                                    @endif
                                </td>
                                <td class="d-none-md">
                                    @if($service->reminder_sent)
                                        <div>
                                            <span class="badge bg-info" style="font-size: 0.6rem;">✓</span>
                                            <div style="font-size: 0.65rem;">{{ $service->reminder_sent_at->format('d/m') }}</div>
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size: 0.7rem;">-</span>
                                    @endif
                                </td>
                                <td class="d-none-lg">
                                    @if($service->assignedBy)
                                        <span style="font-size: 0.75rem;">{{ Str::limit($service->assignedBy->name, 10) }}</span>
                                    @else
                                        <span class="text-muted" style="font-size: 0.7rem;">-</span>
                                    @endif
                                </td>
                                <td class="d-none-xxl logout-history">
                                    @if($latestLogout && $latestLogout->logout_at)
                                        <div class="tooltip-trigger"
                                             title="Logout gần nhất: {{ $latestLogout->logout_at->format('d/m/Y H:i') }}"
                                             data-bs-toggle="tooltip">
                                            <span class="badge bg-secondary" style="font-size: 0.6rem;">{{ $latestLogout->logout_at->format('d/m') }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size: 0.7rem;">-</span>
                                    @endif
                                </td>
                                <td class="action-column">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0.2rem 0.4rem;">
                                            <i class="fas fa-cog" style="font-size: 0.7rem;"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.customers.show', $service->customer) }}">
                                                    <i class="fas fa-user me-2"></i>
                                                    Xem KH
                                                </a>
                                            </li>
                                            @if($isExpiring || $isExpired)
                                            <li>
                                                <button type="button"
                                                        class="dropdown-item"
                                                        onclick="markReminded({{ $service->id }})">
                                                    <i class="fas fa-bell me-2"></i>
                                                    Đã nhắc
                                                </button>
                                            </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-primary"
                                                   href="mailto:{{ $service->customer->email }}?subject=Thông báo gia hạn dịch vụ {{ $service->servicePackage->name }}">
                                                    <i class="fas fa-envelope me-2"></i>
                                                    Email
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button type="button"
                                                        class="dropdown-item text-danger"
                                                        onclick="confirmDeleteService('{{ $service->customer->name }} - {{ $service->servicePackage->name }}', '{{ route('admin.customer-services.destroy', $service) }}')">
                                                    <i class="fas fa-trash me-2"></i>
                                                    Xóa
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($services->isEmpty())
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Không có dịch vụ nào</h5>
                    <p class="text-muted">Không tìm thấy dịch vụ nào sử dụng email này.</p>
                </div>
                @endif

                <!-- Ghi chú và hướng dẫn -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Ghi chú quan trọng</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li>Các dịch vụ sử dụng chung email có thể gây xung đột</li>
                                    <li>Nên tách riêng email cho từng khách hàng</li>
                                    <li>Kiểm tra thường xuyên trạng thái các dịch vụ</li>
                                    <li>Liên hệ khách hàng khi có dịch vụ sắp hết hạn</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Thông tin liên hệ</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Email tài khoản:</strong> {{ $email }}</p>
                                <p><strong>Khách hàng sử dụng:</strong></p>
                                <ul class="mb-0">
                                    @foreach($services->unique('customer_id') as $service)
                                    <li class="mb-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <strong>{{ $service->customer->name }}</strong>
                                                @if($service->customer->phone)
                                                    <br><small class="text-muted">
                                                        <i class="fas fa-phone me-1"></i>{{ $service->customer->phone }}
                                                    </small>
                                                @endif
                                            </div>
                                            @if($service->customer->customer_code)
                                                <span class="badge bg-primary" title="Mã khách hàng">
                                                    <i class="fas fa-id-badge me-1"></i>{{ $service->customer->customer_code }}
                                                </span>
                                            @endif
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Khởi tạo tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function togglePassword(serviceId) {
    const passwordField = document.getElementById('password-' + serviceId);
    const maskedField = document.getElementById('masked-' + serviceId);
    const eyeIcon = document.getElementById('eye-' + serviceId);

    if (passwordField.style.display === 'none') {
        passwordField.style.display = 'inline';
        maskedField.style.display = 'none';
        eyeIcon.className = 'fas fa-eye-slash';
    } else {
        passwordField.style.display = 'none';
        maskedField.style.display = 'inline';
        eyeIcon.className = 'fas fa-eye';
    }
}

// Hàm toggle hiển thị password cho thông tin tài khoản dùng chung
function togglePasswordView(button) {
    const passwordField = button.previousElementSibling;
    const icon = button.querySelector('i');
    
    if (passwordField.textContent === '••••••••') {
        passwordField.textContent = passwordField.getAttribute('data-password');
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordField.textContent = '••••••••';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Hàm hiển thị mã khôi phục
function showRecoveryCodes(codes) {
    const codesText = codes.join('\n');
    alert('Mã khôi phục:\n\n' + codesText);
}

// Hàm đánh dấu đã nhắc nhở
function markReminded(serviceId) {
    const notes = prompt('Ghi chú về việc nhắc nhở (tùy chọn):');
    if (notes === null) return; // User cancelled

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(`/admin/customer-services/${serviceId}/mark-reminded`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ notes: notes })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload(); // Reload to update UI
        } else {
            alert('Có lỗi xảy ra!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra!');
    });
}

// Hàm xác nhận xóa dịch vụ
function confirmDeleteService(serviceName, deleteUrl) {
    if (confirm('Bạn có chắc chắn muốn xóa dịch vụ "' + serviceName + '"?\n\nHành động này không thể hoàn tác!')) {
        // Tạo form để gửi DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = deleteUrl;

        // Thêm CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        // Thêm method DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);

        // Thêm form vào body và submit
        document.body.appendChild(form);
        form.submit();
    }
}

// Logout History Functions
function loadLogoutHistory() {
    const modal = new bootstrap.Modal(document.getElementById('logoutHistoryModal'));
    modal.show();

    // Load logout logs
    fetch(`{{ route('admin.shared-accounts.logout-logs', $email) }}`)
        .then(response => response.json())
        .then(data => {
            displayLogoutHistory(data.logs);
        })
        .catch(error => {
            console.error('Error loading logout history:', error);
            document.getElementById('logoutHistoryContent').innerHTML =
                '<div class="alert alert-danger">Không thể tải lịch sử logout.</div>';
        });
}

function displayLogoutHistory(logs) {
    const content = document.getElementById('logoutHistoryContent');

    if (!logs || logs.length === 0) {
        content.innerHTML = '<div class="alert alert-info">Chưa có lịch sử logout nào.</div>';
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-sm">';
    html += '<thead><tr>';
    html += '<th>Thời gian</th>';
    html += '<th>Người thực hiện</th>';
    html += '<th>Lý do</th>';
    html += '<th>Khách hàng ảnh hưởng</th>';
    html += '<th>Ghi chú</th>';
    html += '</tr></thead><tbody>';

    logs.forEach(log => {
        const logoutDate = new Date(log.logout_at).toLocaleString('vi-VN');
        html += '<tr>';
        html += `<td><small>${logoutDate}</small></td>`;
        html += `<td>${log.performed_by}</td>`;
        html += `<td>${log.reason || 'N/A'}</td>`;
        html += `<td><span class="badge bg-info">${log.affected_count} người</span></td>`;
        html += `<td><small>${log.notes || 'N/A'}</small></td>`;
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    content.innerHTML = html;
}
</script>

<!-- Logout History Modal -->
<div class="modal fade" id="logoutHistoryModal" tabindex="-1" aria-labelledby="logoutHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutHistoryModalLabel">
                    <i class="fas fa-history me-2"></i>
                    Lịch sử Logout All Devices
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="logoutHistoryContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Đang tải lịch sử...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

@endsection
