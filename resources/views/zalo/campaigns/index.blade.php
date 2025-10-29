@extends('layouts.admin')

@section('title', 'Quản lý Chiến dịch')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Quản lý Chiến dịch</h2>
            <p class="text-muted mb-0">Quản lý các chiến dịch gửi tin nhắn và theo dõi conversion</p>
        </div>
        <a href="{{ route('admin.zalo.campaigns.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tạo chiến dịch
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Lọc</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Chiến dịch</th>
                            <th>Nhóm mục tiêu</th>
                            <th>Thời gian</th>
                            <th>Đã gửi</th>
                            <th>Chuyển đổi</th>
                            <th>Tỷ lệ</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaigns as $campaign)
                        <tr>
                            <td>
                                <strong>{{ $campaign->campaign_name }}</strong>
                                <br>
                                <small class="text-muted">Mục tiêu: {{ number_format($campaign->daily_target) }}/ngày</small>
                            </td>
                            <td>
                                <div>{{ $campaign->targetGroup->group_name }}</div>
                                @if($campaign->ownGroup)
                                <small class="text-success">→ {{ $campaign->ownGroup->group_name }}</small>
                                @endif
                            </td>
                            <td>
                                <div>{{ $campaign->start_date->format('d/m/Y') }}</div>
                                @if($campaign->end_date)
                                <small class="text-muted">đến {{ $campaign->end_date->format('d/m/Y') }}</small>
                                @endif
                            </td>
                            <td>
                                <strong>{{ number_format($campaign->total_sent) }}</strong>
                                <div class="progress" style="height: 5px; margin-top: 5px;">
                                    @php
                                    $deliveredPercentage = $campaign->total_sent > 0
                                    ? ($campaign->total_delivered / $campaign->total_sent) * 100
                                    : 0;
                                    @endphp
                                    <div class="progress-bar bg-success" style="width: {{ $deliveredPercentage }}%"></div>
                                </div>
                                <small class="text-muted">{{ number_format($campaign->total_delivered) }} thành công</small>
                            </td>
                            <td>
                                <strong class="text-success">{{ number_format($campaign->total_converted) }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ $campaign->conversion_rate > 5 ? 'success' : ($campaign->conversion_rate > 2 ? 'warning' : 'danger') }} fs-6">
                                    {{ $campaign->conversion_rate }}%
                                </span>
                            </td>
                            <td>
                                @php
                                $statusColors = [
                                'draft' => 'secondary',
                                'active' => 'success',
                                'paused' => 'warning',
                                'completed' => 'primary',
                                'cancelled' => 'danger'
                                ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$campaign->status] ?? 'secondary' }}">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.zalo.campaigns.show', $campaign) }}" class="btn btn-outline-info" title="Chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.zalo.campaigns.report', $campaign) }}" class="btn btn-outline-success" title="Báo cáo">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    <a href="{{ route('admin.zalo.campaigns.edit', $campaign) }}" class="btn btn-outline-primary" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.zalo.campaigns.destroy', $campaign) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa chiến dịch này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Chưa có chiến dịch nào. <a href="{{ route('admin.zalo.campaigns.create') }}">Tạo chiến dịch đầu tiên</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($campaigns->hasPages())
            <div class="mt-3">
                {{ $campaigns->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection