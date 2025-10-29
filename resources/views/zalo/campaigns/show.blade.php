@extends('layouts.admin')

@section('title', 'Chi tiết Chiến dịch')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">{{ $campaign->campaign_name }}</h2>
            <p class="text-muted mb-0">
                <span class="badge bg-{{ $campaign->status === 'active' ? 'success' : 'secondary' }}">
                    {{ ucfirst($campaign->status) }}
                </span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.zalo.campaigns.report', $campaign) }}" class="btn btn-success">
                <i class="fas fa-chart-bar"></i> Báo cáo chi tiết
            </a>
            <a href="{{ route('admin.zalo.campaigns.edit', $campaign) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Sửa
            </a>
            <a href="{{ route('admin.zalo.campaigns.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Tin đã gửi</h6>
                    <h3 class="mb-0">{{ number_format($campaign->total_sent) }}</h3>
                    <small class="text-success">{{ number_format($campaign->total_delivered) }} thành công</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Đã chuyển đổi</h6>
                    <h3 class="mb-0 text-success">{{ number_format($campaign->total_converted) }}</h3>
                    <small class="text-muted">người</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Tỷ lệ chuyển đổi</h6>
                    <h3 class="mb-0 text-{{ $campaign->conversion_rate > 5 ? 'success' : ($campaign->conversion_rate > 2 ? 'warning' : 'danger') }}">
                        {{ $campaign->conversion_rate }}%
                    </h3>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-{{ $campaign->conversion_rate > 5 ? 'success' : 'warning' }}"
                            style="width: {{ min($campaign->conversion_rate * 10, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Còn lại hôm nay</h6>
                    <h3 class="mb-0">{{ number_format($campaign->remaining_today) }}</h3>
                    <small class="text-muted">/ {{ number_format($campaign->daily_target) }} mục tiêu</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Details -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Thông tin chiến dịch</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Nhóm mục tiêu:</th>
                                    <td>
                                        <a href="{{ route('admin.zalo.groups.show', $campaign->targetGroup) }}">
                                            {{ $campaign->targetGroup->group_name }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Nhóm của mình:</th>
                                    <td>
                                        @if($campaign->ownGroup)
                                        <a href="{{ route('admin.zalo.groups.show', $campaign->ownGroup) }}">
                                            {{ $campaign->ownGroup->group_name }}
                                        </a>
                                        @else
                                        <span class="text-muted">Không có</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngày bắt đầu:</th>
                                    <td>{{ $campaign->start_date->format('d/m/Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Ngày kết thúc:</th>
                                    <td>{{ $campaign->end_date ? $campaign->end_date->format('d/m/Y') : 'Không giới hạn' }}</td>
                                </tr>
                                <tr>
                                    <th>Mục tiêu/ngày:</th>
                                    <td>{{ number_format($campaign->daily_target) }} tin</td>
                                </tr>
                                <tr>
                                    <th>Ngày tạo:</th>
                                    <td>{{ $campaign->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h6>Mẫu tin nhắn:</h6>
                        <div class="bg-light p-3 rounded border">
                            <pre class="mb-0" style="white-space: pre-wrap;">{{ $campaign->message_template }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Hiệu suất</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tỷ lệ gửi thành công</span>
                            @php
                            $successRate = $campaign->total_sent > 0
                            ? round(($campaign->total_delivered / $campaign->total_sent) * 100, 1)
                            : 0;
                            @endphp
                            <strong>{{ $successRate }}%</strong>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" style="width: {{ $successRate }}%">
                                {{ $successRate }}%
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tiến độ hôm nay</span>
                            @php
                            $todayProgress = $campaign->daily_target > 0
                            ? round(($campaign->today_message_count / $campaign->daily_target) * 100, 1)
                            : 0;
                            @endphp
                            <strong>{{ $todayProgress }}%</strong>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-{{ $todayProgress >= 100 ? 'success' : 'info' }}"
                                style="width: {{ min($todayProgress, 100) }}%">
                                {{ $campaign->today_message_count }} / {{ $campaign->daily_target }}
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <form action="{{ route('admin.zalo.campaigns.update-stats', $campaign) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-sync"></i> Cập nhật thống kê
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Statistics Chart -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Thống kê 7 ngày gần đây</h5>
        </div>
        <div class="card-body">
            <canvas id="dailyStatsChart" height="80"></canvas>
        </div>
    </div>

    <!-- Recent Messages and Conversions -->
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tin nhắn gần đây</h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @forelse($campaign->messageLogs->take(20) as $log)
                    <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $log->groupMember->display_name ?? 'N/A' }}</strong>
                                <small class="text-muted">{{ $log->sent_at ? $log->sent_at->diffForHumans() : '-' }}</small>
                            </div>
                            <small class="text-muted">{{ $log->zaloAccount->account_name }}</small>
                            <div class="mt-1">
                                <span class="badge bg-{{ $log->status === 'delivered' ? 'success' : ($log->status === 'failed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($log->status) }}
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
                    @forelse($campaign->conversions->take(20) as $conversion)
                    <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $conversion->groupMember->display_name ?? 'N/A' }}</strong>
                                <small class="text-muted">{{ $conversion->joined_at->diffForHumans() }}</small>
                            </div>
                            <small class="text-success">→ {{ $conversion->ownGroup->group_name }}</small>
                            <div class="mt-1">
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
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Get daily stats (last 7 days)
    const dailyData = @json($dailyStats);

    const ctx = document.getElementById('dailyStatsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailyData.map(d => {
                const date = new Date(d.date);
                return date.toLocaleDateString('vi-VN', {
                    day: '2-digit',
                    month: '2-digit'
                });
            }),
            datasets: [{
                label: 'Tin gửi',
                data: dailyData.map(d => d.total),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4
            }, {
                label: 'Thành công',
                data: dailyData.map(d => d.delivered),
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                tension: 0.4
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
</script>
@endpush
@endsection