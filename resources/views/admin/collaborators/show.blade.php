@extends('layouts.admin')

@section('title', 'Chi tiết Cộng tác viên')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-dark">Chi tiết Cộng tác viên</h1>
        <div>
            <a href="{{ route('admin.collaborators.edit', $collaborator) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.collaborators.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Thông tin cơ bản -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Thông tin cơ bản</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Mã cộng tác viên:</strong></td>
                            <td><span class="badge bg-primary">{{ $collaborator->collaborator_code }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Tên:</strong></td>
                            <td>{{ $collaborator->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $collaborator->email ?? 'Chưa có' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Điện thoại:</strong></td>
                            <td>{{ $collaborator->phone ?? 'Chưa có' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Địa chỉ:</strong></td>
                            <td>{{ $collaborator->address ?? 'Chưa có' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Trạng thái:</strong></td>
                            <td>
                                @if($collaborator->status === 'active')
                                <span class="badge bg-success">Hoạt động</span>
                                @else
                                <span class="badge bg-danger">Ngừng hoạt động</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Tỷ lệ hoa hồng:</strong></td>
                            <td><span class="badge bg-info">{{ number_format($collaborator->commission_rate, 2) }}%</span></td>
                        </tr>
                        <tr>
                            <td><strong>Ngày tạo:</strong></td>
                            <td>{{ $collaborator->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>

                    @if($collaborator->notes)
                    <div class="mt-3">
                        <strong>Ghi chú:</strong>
                        <p class="text-muted">{{ $collaborator->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Thống kê -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-success">Thống kê</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="p-2">
                                <h4 class="text-primary">{{ $collaborator->services->count() }}</h4>
                                <small class="text-muted">Dịch vụ</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2">
                                <h4 class="text-info">{{ $collaborator->total_accounts }}</h4>
                                <small class="text-muted">Tài khoản</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2">
                                <h4 class="text-success">{{ number_format($collaborator->total_value, 0, ',', '.') }}đ</h4>
                                <small class="text-muted">Tổng giá trị</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danh sách dịch vụ -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">Danh sách dịch vụ</h6>
                    <span class="badge bg-primary">{{ $collaborator->services->count() }} dịch vụ</span>
                </div>
                <div class="card-body">
                    @if($collaborator->services->count() > 0)
                    @foreach($collaborator->services as $service)
                    <div class="card mb-3 border-start border-primary border-5">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="text-primary">{{ $service->service_name }}</h5>
                                    <p class="text-muted mb-1">{{ $service->description ?? 'Không có mô tả' }}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> Bảo hành: {{ $service->warranty_period }} ngày
                                    </small>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="p-2">
                                        <h6 class="text-success">{{ number_format($service->price, 0, ',', '.') }}đ</h6>
                                        <small class="text-muted">Giá bán</small>
                                    </div>
                                    <div class="p-2">
                                        <h6 class="text-info">{{ $service->quantity }}</h6>
                                        <small class="text-muted">Số lượng</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h6 class="text-primary">{{ $service->accounts->count() }}</h6>
                                        <small class="text-muted">Tài khoản có sẵn</small>
                                    </div>
                                    <div class="mt-2 text-center">
                                        <button class="btn btn-sm btn-success mb-1 add-account-btn"
                                            data-service-id="{{ $service->id }}"
                                            data-service-name="{{ $service->service_name }}">
                                            <i class="fas fa-plus"></i> Thêm TK
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#accounts-{{ $service->id }}">
                                            <i class="fas fa-eye"></i> Xem TK
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Danh sách tài khoản -->
                            <div class="collapse mt-3" id="accounts-{{ $service->id }}">
                                <hr>
                                <h6 class="text-secondary">Danh sách tài khoản:</h6>
                                @if($service->accounts->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Thông tin tài khoản</th>
                                                <th>Ngày cung cấp</th>
                                                <th>Ngày hết hạn</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($service->accounts as $account)
                                            <tr>
                                                <td>
                                                    <small class="text-muted">{{ Str::limit($account->account_info, 50) }}</small>
                                                </td>
                                                <td>
                                                    <small>{{ $account->provided_date ? \Carbon\Carbon::parse($account->provided_date)->format('d/m/Y') : 'Chưa có' }}</small>
                                                </td>
                                                <td>
                                                    <small>{{ $account->expiry_date ? \Carbon\Carbon::parse($account->expiry_date)->format('d/m/Y') : 'Chưa có' }}</small>
                                                </td>
                                                <td>
                                                    @switch($account->status)
                                                    @case('active')
                                                    <span class="badge bg-success">Hoạt động</span>
                                                    @break
                                                    @case('expired')
                                                    <span class="badge bg-danger">Hết hạn</span>
                                                    @break
                                                    @case('disabled')
                                                    <span class="badge bg-secondary">Vô hiệu</span>
                                                    @break
                                                    @default
                                                    <span class="badge bg-warning">Chưa xác định</span>
                                                    @endswitch
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <p class="text-muted">Chưa có tài khoản nào cho dịch vụ này.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Cộng tác viên chưa có dịch vụ nào.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm Tài Khoản -->
<div class="modal fade" id="addAccountModal" tabindex="-1" role="dialog" aria-labelledby="addAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAccountModalLabel">Thêm Tài Khoản Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addAccountForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="account_username">Tài khoản (Email/Username) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="account_username" name="account_username" required>
                    </div>

                    <div class="form-group">
                        <label for="account_password">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="account_password" name="account_password" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="provided_date">Ngày giao <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="provided_date" name="provided_date"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expiry_date">Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="account_status">Trạng thái</label>
                        <select class="form-control" id="account_status" name="status">
                            <option value="active">Hoạt động</option>
                            <option value="disabled">Vô hiệu</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes">Ghi chú</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Ghi chú thêm về tài khoản..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Lưu tài khoản
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Handle add account button click
        $('.add-account-btn').click(function() {
            const serviceId = $(this).data('service-id');
            const serviceName = $(this).data('service-name');

            // Update modal title
            $('#addAccountModalLabel').text('Thêm Tài Khoản - ' + serviceName);

            // Set form action
            $('#addAccountForm').attr('action', '/admin/collaborators/{{ $collaborator->id }}/services/' + serviceId + '/accounts');

            // Calculate default expiry date (30 days from today)
            const today = new Date();
            const expiry = new Date(today.getTime() + (30 * 24 * 60 * 60 * 1000));
            $('#expiry_date').val(expiry.toISOString().split('T')[0]);

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('addAccountModal'));
            modal.show();
        });

        // Handle form submission
        $('#addAccountForm').submit(function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const username = formData.get('account_username');
            const password = formData.get('account_password');
            const accountInfo = `Email: ${username} | Password: ${password}`;

            // Replace account_info with combined string
            formData.set('account_info', accountInfo);

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addAccountModal'));
                    modal.hide();

                    // Show success message
                    if (response.success) {
                        // You can add a toast notification here
                        alert('Tài khoản đã được thêm thành công!');
                        // Reload page to show new account
                        location.reload();
                    }
                },
                error: function(xhr) {
                    // Handle errors
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = 'Có lỗi xảy ra:\n';
                        for (const field in errors) {
                            errorMessage += '- ' + errors[field][0] + '\n';
                        }
                        alert(errorMessage);
                    } else {
                        alert('Có lỗi xảy ra khi thêm tài khoản!');
                    }
                }
            });
        });
    });
</script>
@endpush
@endsection