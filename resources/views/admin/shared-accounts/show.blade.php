@extends('layouts.admin')

@section('title', 'Chi tiết tài khoản dùng chung')
@section('page-title', 'Chi tiết tài khoản dùng chung')

@section('styles')
<style>
    .table-responsive {
        overflow-x: auto;
    }

    .table th, .table td {
        white-space: nowrap;
        vertical-align: middle;
    }

    .table th {
        position: sticky;
        top: 0;
        background-color: var(--bs-dark);
        z-index: 10;
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
                        <a href="{{ route('admin.shared-accounts.edit', $email) }}" class="btn btn-primary btn-sm">
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

                <!-- Bảng dịch vụ -->
                <div class="table-responsive">
                    <!-- Responsive info alert -->
                    <div class="alert alert-info alert-sm d-lg-none mb-2">
                        <i class="fas fa-info-circle me-1"></i>
                        <small>Một số cột (Ghi chú, Mật khẩu, Kích hoạt, Nhắc nhở, Người PC) được ẩn trên màn hình nhỏ để tối ưu hiển thị.</small>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th style="min-width: 180px;">Khách hàng</th>
                                <th style="min-width: 120px;">Gói dịch vụ</th>
                                <th class="d-none-md" style="min-width: 150px;">Ghi chú</th>
                                <th class="d-none-lg" style="min-width: 150px;">Mật khẩu</th>
                                <th class="d-none-xl" style="min-width: 100px;">Kích hoạt</th>
                                <th style="min-width: 120px;">Hết hạn</th>
                                <th style="min-width: 100px;">Trạng thái</th>
                                <th class="d-none-md" style="min-width: 100px;">Nhắc nhở</th>
                                <th class="d-none-lg" style="min-width: 120px;">Người PC</th>
                                <th style="min-width: 80px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                            @php
                                $isExpired = $service->expires_at && $service->expires_at->isPast();
                                $isExpiring = $service->expires_at && $service->expires_at->isFuture() && $service->expires_at->diffInDays(now()) <= 7;
                                $statusClass = $isExpired ? 'table-danger' : ($isExpiring ? 'table-warning' : '');
                            @endphp
                            <tr class="{{ $statusClass }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <strong>{{ $service->customer->name }}</strong>
                                            @if($service->customer->customer_code)
                                                <span class="badge bg-light text-dark ms-2" 
                                                      title="Mã khách hàng">
                                                    <i class="fas fa-id-badge me-1"></i>{{ $service->customer->customer_code }}
                                                </span>
                                            @endif
                                            <br><small class="text-muted">
                                                <i class="fas fa-phone me-1"></i>{{ $service->customer->phone }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $service->servicePackage->name }}</span>
                                </td>
                                <td class="d-none-md">
                                    <div class="text-muted small">
                                        @if($service->internal_notes)
                                            {{ Str::limit($service->internal_notes, 50) }}
                                        @elseif($service->shared_account_notes)
                                            {{ Str::limit($service->shared_account_notes, 50) }}
                                        @else
                                            <em>Không có ghi chú</em>
                                        @endif
                                    </div>
                                </td>
                                <td class="d-none-lg">
                                    <div class="d-flex align-items-center">
                                        <code id="password-{{ $service->id }}" style="display: none;">{{ $service->login_password }}</code>
                                        <span id="masked-{{ $service->id }}">{{ str_repeat('*', strlen($service->login_password)) }}</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" 
                                                onclick="togglePassword({{ $service->id }})">
                                            <i class="fas fa-eye" id="eye-{{ $service->id }}"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="d-none-xl">
                                    @if($service->activated_at)
                                        <small>{{ $service->activated_at->format('d/m/Y') }}</small>
                                    @else
                                        <span class="text-muted">Chưa kích hoạt</span>
                                    @endif
                                </td>
                                <td>
                                    @if($service->expires_at)
                                        <div>
                                            {{ $service->expires_at->format('d/m/Y') }}
                                            @if($isExpired)
                                                <br><small class="text-danger">Đã hết hạn {{ $service->expires_at->diffForHumans() }}</small>
                                            @elseif($isExpiring)
                                                <br><small class="text-warning">Còn {{ $service->expires_at->diffInDays(now()) }} ngày</small>
                                            @else
                                                <br><small class="text-success">Còn {{ $service->expires_at->diffInDays(now()) }} ngày</small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">Không giới hạn</span>
                                    @endif
                                </td>
                                <td>
                                    @if($service->status === 'active')
                                        <span class="badge bg-success">Hoạt động</span>
                                    @elseif($service->status === 'inactive')
                                        <span class="badge bg-secondary">Không hoạt động</span>
                                    @elseif($service->status === 'suspended')
                                        <span class="badge bg-warning">Tạm dừng</span>
                                    @else
                                        <span class="badge bg-danger">Hết hạn</span>
                                    @endif
                                </td>
                                <td class="d-none-md">
                                    @if($service->reminder_sent)
                                        <div>
                                            <span class="badge bg-info">Đã nhắc</span>
                                            <br><small class="text-muted">{{ $service->reminder_sent_at->format('d/m') }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">Chưa</span>
                                    @endif
                                </td>
                                <td class="d-none-lg">
                                    @if($service->assignedBy)
                                        {{ $service->assignedBy->name }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.customers.show', $service->customer) }}">
                                                    <i class="fas fa-user me-2"></i>
                                                    Xem khách hàng
                                                </a>
                                            </li>
                                            @if($isExpiring || $isExpired)
                                            <li>
                                                <button type="button"
                                                        class="dropdown-item"
                                                        onclick="markReminded({{ $service->id }})">
                                                    <i class="fas fa-bell me-2"></i>
                                                    Đánh dấu đã nhắc
                                                </button>
                                            </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-primary"
                                                   href="mailto:{{ $service->customer->email }}?subject=Thông báo gia hạn dịch vụ {{ $service->servicePackage->name }}">
                                                    <i class="fas fa-envelope me-2"></i>
                                                    Gửi email
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button type="button"
                                                        class="dropdown-item text-danger"
                                                        onclick="confirmDeleteService('{{ $service->customer->name }} - {{ $service->servicePackage->name }}', '{{ route('admin.customer-services.destroy', $service) }}')">
                                                    <i class="fas fa-trash me-2"></i>
                                                    Xóa dịch vụ
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
</script>
@endsection
