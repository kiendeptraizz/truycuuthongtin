@extends('layouts.admin')

@section('title', 'Chi tiết Nhóm')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">{{ $group->group_name }}</h2>
            <p class="text-muted mb-0">
                <span class="badge bg-{{ $group->group_type === 'own' ? 'success' : 'info' }}">
                    {{ $group->group_type === 'own' ? 'Nhóm của tôi' : 'Nhóm đối thủ' }}
                </span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.zalo.groups.members', $group) }}" class="btn btn-success">
                <i class="fas fa-users"></i> Xem thành viên
            </a>
            <a href="{{ route('admin.zalo.groups.edit', $group) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Sửa
            </a>
            <a href="{{ route('admin.zalo.groups.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Tổng thành viên</h6>
                    <h3 class="mb-0">{{ number_format($stats['total_members']) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Chưa liên hệ</h6>
                    <h3 class="mb-0 text-info">{{ number_format($stats['new_members']) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Đã liên hệ</h6>
                    <h3 class="mb-0 text-warning">{{ number_format($stats['contacted']) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Đã chuyển đổi</h6>
                    <h3 class="mb-0 text-success">{{ number_format($stats['converted']) }}</h3>
                    <small class="text-muted">{{ $stats['conversion_rate'] }}%</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Group Info and Conversion Funnel -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Thông tin nhóm</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="180">Link nhóm:</th>
                            <td>
                                <a href="{{ $group->group_link }}" target="_blank" class="text-break">
                                    {{ Str::limit($group->group_link, 50) }}
                                    <i class="fas fa-external-link-alt ms-1"></i>
                                </a>
                            </td>
                        </tr>
                        @if($group->group_id)
                        <tr>
                            <th>ID nhóm:</th>
                            <td><code>{{ $group->group_id }}</code></td>
                        </tr>
                        @endif
                        <tr>
                            <th>Chủ đề:</th>
                            <td>{{ $group->topic ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Ngày khai giảng:</th>
                            <td>{{ $group->opening_date ? $group->opening_date->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Lần quét cuối:</th>
                            <td>{{ $group->last_scanned_at ? $group->last_scanned_at->format('d/m/Y H:i') : 'Chưa quét' }}</td>
                        </tr>
                        <tr>
                            <th>Trạng thái:</th>
                            <td>
                                <span class="badge bg-{{ $group->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($group->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Ngày tạo:</th>
                            <td>{{ $group->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>

                    @if($group->description)
                    <div class="mt-3">
                        <h6>Mô tả:</h6>
                        <div class="bg-light p-3 rounded">
                            {{ $group->description }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Phễu chuyển đổi</h5>
                </div>
                <div class="card-body">
                    <div class="conversion-funnel">
                        <div class="funnel-step mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Tổng thành viên</span>
                                <strong>{{ number_format($stats['total_members']) }}</strong>
                            </div>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-info" style="width: 100%">100%</div>
                            </div>
                        </div>

                        <div class="funnel-step mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Đã liên hệ</span>
                                <strong>{{ number_format($stats['contacted'] + $stats['converted']) }}</strong>
                            </div>
                            <div class="progress" style="height: 25px;">
                                @php
                                $contactedPercentage = $stats['total_members'] > 0
                                ? (($stats['contacted'] + $stats['converted']) / $stats['total_members']) * 100
                                : 0;
                                @endphp
                                <div class="progress-bar bg-warning" style="width: {{ $contactedPercentage }}%">
                                    {{ number_format($contactedPercentage, 1) }}%
                                </div>
                            </div>
                        </div>

                        <div class="funnel-step mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Đã chuyển đổi</span>
                                <strong>{{ number_format($stats['converted']) }}</strong>
                            </div>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-success" style="width: {{ $stats['conversion_rate'] }}%">
                                    {{ $stats['conversion_rate'] }}%
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle"></i>
                        <strong>Tỷ lệ chuyển đổi:</strong> {{ $stats['conversion_rate'] }}%
                        @if($stats['conversion_rate'] > 5)
                        <span class="text-success">(Rất tốt!)</span>
                        @elseif($stats['conversion_rate'] > 2)
                        <span class="text-warning">(Khá tốt)</span>
                        @else
                        <span class="text-danger">(Cần cải thiện)</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaigns -->
    @if($group->isCompetitorGroup())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Chiến dịch liên quan</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Chiến dịch</th>
                            <th>Trạng thái</th>
                            <th>Đã gửi</th>
                            <th>Chuyển đổi</th>
                            <th>Tỷ lệ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($group->campaigns as $campaign)
                        <tr>
                            <td>
                                <a href="{{ route('admin.zalo.campaigns.show', $campaign) }}">
                                    {{ $campaign->campaign_name }}
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-{{ $campaign->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </td>
                            <td>{{ number_format($campaign->total_sent) }}</td>
                            <td class="text-success">{{ number_format($campaign->total_converted) }}</td>
                            <td>
                                <span class="badge bg-{{ $campaign->conversion_rate > 5 ? 'success' : 'warning' }}">
                                    {{ $campaign->conversion_rate }}%
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                Chưa có chiến dịch nào
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Members -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Thành viên gần đây</h5>
            <a href="{{ route('admin.zalo.groups.members', $group) }}" class="btn btn-sm btn-outline-primary">
                Xem tất cả
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tên</th>
                            <th>Zalo ID</th>
                            <th>Trạng thái</th>
                            <th>Liên hệ lần cuối</th>
                            <th>Số lần liên hệ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($group->members->take(10) as $member)
                        <tr>
                            <td>{{ $member->display_name ?? 'N/A' }}</td>
                            <td><code>{{ $member->zalo_id }}</code></td>
                            <td>
                                @php
                                $statusColors = [
                                'new' => 'info',
                                'contacted' => 'warning',
                                'converted' => 'success',
                                'failed' => 'danger',
                                'blocked' => 'dark'
                                ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$member->status] ?? 'secondary' }}">
                                    {{ ucfirst($member->status) }}
                                </span>
                            </td>
                            <td>{{ $member->last_contacted_at ? $member->last_contacted_at->diffForHumans() : '-' }}</td>
                            <td>{{ $member->contact_count }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                Chưa có thành viên nào.
                                <a href="#">Import thành viên</a>
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