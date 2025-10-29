@extends('layouts.admin')

@section('title', 'Sửa Chiến dịch')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="mb-0">Sửa Chiến dịch</h2>
        <p class="text-muted mb-0">Cập nhật thông tin chiến dịch {{ $campaign->campaign_name }}</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.zalo.campaigns.update', $campaign) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Tên chiến dịch <span class="text-danger">*</span></label>
                            <input type="text" name="campaign_name" class="form-control @error('campaign_name') is-invalid @enderror"
                                value="{{ old('campaign_name', $campaign->campaign_name) }}" required>
                            @error('campaign_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nhóm mục tiêu (đối thủ) <span class="text-danger">*</span></label>
                            <select name="target_group_id" class="form-select @error('target_group_id') is-invalid @enderror" required>
                                <option value="">-- Chọn nhóm --</option>
                                @foreach($targetGroups as $group)
                                <option value="{{ $group->id }}" {{ old('target_group_id', $campaign->target_group_id) == $group->id ? 'selected' : '' }}>
                                    {{ $group->group_name }} ({{ number_format($group->total_members) }} thành viên)
                                </option>
                                @endforeach
                            </select>
                            @error('target_group_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nhóm của tôi (để kéo về)</label>
                            <select name="own_group_id" class="form-select @error('own_group_id') is-invalid @enderror">
                                <option value="">-- Chọn nhóm (tùy chọn) --</option>
                                @foreach($ownGroups as $group)
                                <option value="{{ $group->id }}" {{ old('own_group_id', $campaign->own_group_id) == $group->id ? 'selected' : '' }}>
                                    {{ $group->group_name }}
                                </option>
                                @endforeach
                            </select>
                            @error('own_group_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mẫu tin nhắn <span class="text-danger">*</span></label>
                            <textarea name="message_template" class="form-control @error('message_template') is-invalid @enderror"
                                rows="5" required>{{ old('message_template', $campaign->message_template) }}</textarea>
                            @error('message_template')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                    value="{{ old('start_date', $campaign->start_date->format('Y-m-d')) }}" required>
                                @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày kết thúc</label>
                                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                                    value="{{ old('end_date', $campaign->end_date?->format('Y-m-d')) }}">
                                @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mục tiêu gửi/ngày <span class="text-danger">*</span></label>
                                <input type="number" name="daily_target" class="form-control @error('daily_target') is-invalid @enderror"
                                    value="{{ old('daily_target', $campaign->daily_target) }}" min="1" max="500" required>
                                @error('daily_target')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="draft" {{ old('status', $campaign->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="active" {{ old('status', $campaign->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="paused" {{ old('status', $campaign->status) === 'paused' ? 'selected' : '' }}>Paused</option>
                                    <option value="completed" {{ old('status', $campaign->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status', $campaign->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật
                            </button>
                            <a href="{{ route('admin.zalo.campaigns.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Thống kê hiện tại</h5>
                    <div class="mb-3">
                        <small class="text-muted">Tin đã gửi</small>
                        <h4>{{ number_format($campaign->total_sent) }}</h4>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Chuyển đổi</small>
                        <h4 class="text-success">{{ number_format($campaign->total_converted) }}</h4>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Tỷ lệ chuyển đổi</small>
                        <h4>{{ $campaign->conversion_rate }}%</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection