@extends('layouts.admin')

@section('title', 'Chi tiết Tài khoản Zalo')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">{{ $account->account_name }}</h2>
            <p class="text-muted mb-0">{{ $account->email_or_phone }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.zalo.accounts.edit', $account) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Sửa
            </a>
            <a href="{{ route('admin.zalo.accounts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Trạng thái</h6>
                    <h4>
                        <span class="badge bg-{{ $account->status === 'active' ? 'success' : 'secondary' }} fs-6">
                            {{ ucfirst($account->status) }}
                        </span>
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Tin gửi hôm nay</h6>
                    <h3 class="mb-0">{{ number_format($account->messages_sent_today) }}</h3>
                    <small class="text-muted">/ {{ number_format($account->daily_message_limit) }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Còn lại hôm nay</h6>
                    <h3 class="mb-0 text-{{ $account->remaining_messages > 20 ? 'success' : 'warning' }}">
                        {{ number_format($account->remaining_messages) }}
                    </h3>
                    <small class="text-muted">tin nhắn</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Tỷ lệ thành công</h6>
                    <h3 class="mb-0 text-success">{{ $stats['success_rate'] }}%</h3>
                    <small class="text-muted">{{ number_format($stats['total_messages']) }} tin</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Info -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Thông tin tài khoản</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Email/SĐT:</th>
                            <td>{{ $account->email_or_phone }}</td>
                        </tr>
                        <tr>
                            <th>Giới hạn/ngày:</th>
                            <td>{{ number_format($account->daily_message_limit) }} tin</td>
                        </tr>
                        <tr>
                            <th>Ngày gửi cuối:</th>
                            <td>{{ $account->last_message_date ? $account->last_message_date->format('d/m/Y') : 'Chưa gửi' }}</td>
                        </tr>
                        <tr>
                            <th>Token hết hạn:</th>
                            <td>{{ $account->token_expires_at ? $account->token_expires_at->format('d/m/Y H:i') : 'Không có' }}</td>
                        </tr>
                        <tr>
                            <th>Ngày tạo:</th>
                            <td>{{ $account->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>

                    @if($account->notes)
                    <div class="mt-3">
                        <h6>Ghi chú:</h6>
                        <div class="bg-light p-3 rounded">
                            {{ $account->notes }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Thống kê</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-muted">Tổng tin đã gửi</h6>
                        <h3>{{ number_format($stats['total_messages']) }}</h3>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted">Tin gửi hôm nay</h6>
                        <h3>{{ number_format($stats['today_messages']) }}</h3>
                        <div class="progress" style="height: 25px;">
                            @php
                            $percentage = $account->daily_message_limit > 0
                            ? ($stats['today_messages'] / $account->daily_message_limit) * 100
                            : 0;
                            @endphp
                            <div class="progress-bar bg-{{ $percentage < 80 ? 'success' : 'warning' }}"
                                style="width: {{ $percentage }}%">
                                {{ number_format($percentage, 1) }}%
                            </div>
                        </div>
                    </div>

                    <div>
                        <h6 class="text-muted">Hiệu suất</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tỷ lệ thành công:</span>
                            <strong class="text-success">{{ $stats['success_rate'] }}%</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Messages -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Lịch sử gửi tin gần đây</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Thời gian</th>
                            <th>Chiến dịch</th>
                            <th>Người nhận</th>
                            <th>Trạng thái</th>
                            <th>Lỗi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($account->messageLogs as $log)
                        <tr>
                            <td>{{ $log->sent_at ? $log->sent_at->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $log->campaign->campaign_name }}</td>
                            <td>{{ $log->groupMember->display_name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $log->status === 'delivered' ? 'success' : ($log->status === 'failed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td>
                                @if($log->error_message)
                                <small class="text-danger">{{ $log->error_message }}</small>
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Chưa có lịch sử gửi tin
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection