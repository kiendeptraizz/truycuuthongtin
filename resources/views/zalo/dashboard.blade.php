@extends('layouts.admin')

@section('title', 'Zalo Marketing Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">📱 Zalo Marketing Dashboard</h2>
            <p class="text-muted mb-0">Quản lý chiến dịch marketing và theo dõi conversion rate</p>
        </div>
        <div>
            <form method="GET" action="{{ route('admin.zalo.dashboard') }}" class="d-flex gap-2">
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                <button type="submit" class="btn btn-primary">Lọc</button>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Tài khoản Active</h6>
                            <h3 class="mb-0">{{ $stats['total_accounts'] }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-user-circle fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Nhóm mục tiêu</h6>
                            <h3 class="mb-0">{{ $stats['total_groups'] }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-users fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Tin nhắn đã gửi</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_messages_sent']) }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-envelope fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Tỷ lệ chuyển đổi</h6>
                            <h3 class="mb-0 text-success">{{ $stats['overall_conversion_rate'] }}%</h3>
                            <small class="text-muted">{{ number_format($stats['total_conversions']) }} conversions</small>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-chart-line fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tin nhắn theo ngày</h5>
                </div>
                <div class="card-body">
                    <canvas id="messagesChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Chuyển đổi theo ngày</h5>
                </div>
                <div class="card-body">
                    <canvas id="conversionsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Campaigns & Account Performance -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Top Chiến dịch</h5>
                    <a href="{{ route('admin.zalo.campaigns.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Chiến dịch</th>
                                    <th>Gửi</th>
                                    <th>Chuyển đổi</th>
                                    <th>Tỷ lệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCampaigns as $campaign)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.zalo.campaigns.show', $campaign) }}">{{ $campaign->campaign_name }}</a>
                                    </td>
                                    <td>{{ number_format($campaign->total_sent) }}</td>
                                    <td>{{ number_format($campaign->total_converted) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $campaign->conversion_rate > 5 ? 'success' : ($campaign->conversion_rate > 2 ? 'warning' : 'danger') }}">
                                            {{ $campaign->conversion_rate }}%
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Chưa có chiến dịch nào</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Hiệu suất tài khoản</h5>
                    <a href="{{ route('admin.zalo.accounts.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tài khoản</th>
                                    <th>Tin gửi</th>
                                    <th>Thành công</th>
                                    <th>Tỷ lệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($accountPerformance as $account)
                                <tr>
                                    <td>{{ $account->account_name }}</td>
                                    <td>{{ number_format($account->total_messages) }}</td>
                                    <td>{{ number_format($account->successful_messages) }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $account->success_rate > 80 ? 'success' : ($account->success_rate > 50 ? 'warning' : 'danger') }}"
                                                style="width: {{ $account->success_rate }}%">
                                                {{ $account->success_rate }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Chưa có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tin nhắn gần đây</h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @forelse($recentMessages as $message)
                    <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $message->groupMember->display_name ?? 'N/A' }}</strong>
                                <small class="text-muted">{{ $message->sent_at->diffForHumans() }}</small>
                            </div>
                            <small class="text-muted">{{ $message->campaign->campaign_name }}</small>
                            <div class="mt-1">
                                <span class="badge bg-{{ $message->status === 'delivered' ? 'success' : ($message->status === 'failed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($message->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center">Chưa có tin nhắn nào</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Chuyển đổi gần đây</h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @forelse($recentConversions as $conversion)
                    <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $conversion->groupMember->display_name ?? 'N/A' }}</strong>
                                <small class="text-muted">{{ $conversion->joined_at->diffForHumans() }}</small>
                            </div>
                            <small class="text-muted">{{ $conversion->campaign->campaign_name }}</small>
                            <div class="mt-1">
                                <span class="badge bg-success">
                                    Joined: {{ $conversion->ownGroup->group_name }}
                                </span>
                                @if($conversion->days_to_convert)
                                <span class="badge bg-info">{{ $conversion->days_to_convert }} ngày</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center">Chưa có conversion nào</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mt-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h5 class="mb-3">Thao tác nhanh</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('admin.zalo.accounts.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm tài khoản Zalo
                        </a>
                        <a href="{{ route('admin.zalo.groups.create') }}" class="btn btn-success">
                            <i class="fas fa-users"></i> Thêm nhóm mục tiêu
                        </a>
                        <a href="{{ route('admin.zalo.campaigns.create') }}" class="btn btn-info text-white">
                            <i class="fas fa-bullhorn"></i> Tạo chiến dịch mới
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Messages Chart
    const messagesCtx = document.getElementById('messagesChart').getContext('2d');
    new Chart(messagesCtx, {
        type: 'line',
        data: {
            labels: @json($dailyMessages->pluck('date')),
            datasets: [{
                label: 'Tổng tin nhắn',
                data: @json($dailyMessages->pluck('total')),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true
            }, {
                label: 'Gửi thành công',
                data: @json($dailyMessages->pluck('delivered')),
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                fill: true
            }, {
                label: 'Thất bại',
                data: @json($dailyMessages->pluck('failed')),
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Conversions Chart
    const conversionsCtx = document.getElementById('conversionsChart').getContext('2d');
    new Chart(conversionsCtx, {
        type: 'bar',
        data: {
            labels: @json($dailyConversions->pluck('date')),
            datasets: [{
                label: 'Chuyển đổi',
                data: @json($dailyConversions->pluck('count')),
                backgroundColor: '#ffc107',
                borderColor: '#ffc107',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
@endsection
