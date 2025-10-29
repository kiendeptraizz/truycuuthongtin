@extends('layouts.admin')

@section('title', 'Báo cáo Chiến dịch')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Báo cáo: {{ $campaign->campaign_name }}</h2>
            <p class="text-muted mb-0">Chi tiết hiệu suất và tỷ lệ chuyển đổi</p>
        </div>
        <a href="{{ route('admin.zalo.campaigns.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Overall Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Tổng tin gửi</h6>
                    <h3 class="mb-0">{{ number_format($campaign->total_sent) }}</h3>
                    <small class="text-success">{{ number_format($campaign->total_delivered) }} thành công</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Chuyển đổi</h6>
                    <h3 class="mb-0 text-success">{{ number_format($campaign->total_converted) }}</h3>
                    <small class="text-muted">người đã join</small>
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
                    <div class="progress" style="height: 5px; margin-top: 10px;">
                        <div class="progress-bar bg-{{ $campaign->conversion_rate > 5 ? 'success' : 'warning' }}"
                            style="width: {{ min($campaign->conversion_rate * 10, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Thời gian TB để convert</h6>
                    <h3 class="mb-0">{{ number_format($avgDaysToConvert ?? 0, 1) }}</h3>
                    <small class="text-muted">ngày</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Info -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Thông tin chiến dịch</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="180">Nhóm mục tiêu:</th>
                            <td>{{ $campaign->targetGroup->group_name }}</td>
                        </tr>
                        <tr>
                            <th>Nhóm của mình:</th>
                            <td>{{ $campaign->ownGroup?->group_name ?? 'Không có' }}</td>
                        </tr>
                        <tr>
                            <th>Thời gian:</th>
                            <td>
                                {{ $campaign->start_date->format('d/m/Y') }}
                                @if($campaign->end_date)
                                - {{ $campaign->end_date->format('d/m/Y') }}
                                @else
                                - Không giới hạn
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Mục tiêu/ngày:</th>
                            <td>{{ number_format($campaign->daily_target) }} tin</td>
                        </tr>
                        <tr>
                            <th>Trạng thái:</th>
                            <td>
                                <span class="badge bg-{{ $campaign->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Mẫu tin nhắn</h5>
                </div>
                <div class="card-body">
                    <div class="bg-light p-3 rounded">
                        <pre class="mb-0" style="white-space: pre-wrap;">{{ $campaign->message_template }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tin nhắn theo ngày</h5>
                </div>
                <div class="card-body">
                    <canvas id="dailyMessagesChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Chuyển đổi theo ngày</h5>
                </div>
                <div class="card-body">
                    <canvas id="dailyConversionsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics Table -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Thống kê chi tiết theo ngày</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Ngày</th>
                                    <th>Tổng gửi</th>
                                    <th>Thành công</th>
                                    <th>Thất bại</th>
                                    <th>Tỷ lệ thành công</th>
                                    <th>Chuyển đổi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dailyStats as $stat)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($stat->date)->format('d/m/Y') }}</td>
                                    <td>{{ number_format($stat->total) }}</td>
                                    <td class="text-success">{{ number_format($stat->delivered) }}</td>
                                    <td class="text-danger">{{ number_format($stat->failed) }}</td>
                                    <td>
                                        @php
                                        $successRate = $stat->total > 0 ? ($stat->delivered / $stat->total) * 100 : 0;
                                        @endphp
                                        <div class="progress" style="width: 100px; height: 20px;">
                                            <div class="progress-bar bg-{{ $successRate > 80 ? 'success' : 'warning' }}"
                                                style="width: {{ $successRate }}%">
                                                {{ number_format($successRate, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                        $conversion = $conversionStats->firstWhere('date', $stat->date);
                                        @endphp
                                        <strong class="text-success">{{ $conversion ? number_format($conversion->count) : 0 }}</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Daily Messages Chart
    const messagesCtx = document.getElementById('dailyMessagesChart').getContext('2d');
    new Chart(messagesCtx, {
        type: 'bar',
        data: {
            labels: @json($dailyStats - > pluck('date') - > map(fn($d) => \Carbon\ Carbon::parse($d) - > format('d/m'))),
            datasets: [{
                label: 'Thành công',
                data: @json($dailyStats - > pluck('delivered')),
                backgroundColor: 'rgba(25, 135, 84, 0.8)'
            }, {
                label: 'Thất bại',
                data: @json($dailyStats - > pluck('failed')),
                backgroundColor: 'rgba(220, 53, 69, 0.8)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true
                }
            }
        }
    });

    // Daily Conversions Chart
    const conversionsCtx = document.getElementById('dailyConversionsChart').getContext('2d');
    new Chart(conversionsCtx, {
        type: 'line',
        data: {
            labels: @json($conversionStats - > pluck('date') - > map(fn($d) => \Carbon\ Carbon::parse($d) - > format('d/m'))),
            datasets: [{
                label: 'Chuyển đổi',
                data: @json($conversionStats - > pluck('count')),
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
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