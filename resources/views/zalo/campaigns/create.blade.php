@extends('layouts.admin')

@section('title', 'Tạo Chiến dịch')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="mb-0">Tạo Chiến dịch Mới</h2>
        <p class="text-muted mb-0">Tạo chiến dịch gửi tin nhắn và kéo thành viên</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.zalo.campaigns.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Tên chiến dịch <span class="text-danger">*</span></label>
                            <input type="text" name="campaign_name" class="form-control @error('campaign_name') is-invalid @enderror"
                                value="{{ old('campaign_name') }}" required>
                            @error('campaign_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nhóm mục tiêu (đối thủ) <span class="text-danger">*</span></label>
                            <select name="target_group_id" class="form-select @error('target_group_id') is-invalid @enderror" required>
                                <option value="">-- Chọn nhóm --</option>
                                @foreach($targetGroups as $group)
                                <option value="{{ $group->id }}" {{ old('target_group_id') == $group->id ? 'selected' : '' }}>
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
                                <option value="{{ $group->id }}" {{ old('own_group_id') == $group->id ? 'selected' : '' }}>
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
                                rows="5" required>{{ old('message_template') }}</textarea>
                            <small class="text-muted">
                                Có thể dùng biến: {name}, {group_name}
                            </small>
                            @error('message_template')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                    value="{{ old('start_date', now()->format('Y-m-d')) }}" required>
                                @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày kết thúc</label>
                                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                                    value="{{ old('end_date') }}">
                                @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mục tiêu gửi/ngày <span class="text-danger">*</span></label>
                                <input type="number" name="daily_target" class="form-control @error('daily_target') is-invalid @enderror"
                                    value="{{ old('daily_target', 50) }}" min="1" max="500" required>
                                @error('daily_target')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="paused" {{ old('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Tạo chiến dịch
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
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h5 class="mb-3">💡 Hướng dẫn</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            Chọn nhóm đối thủ để quét thành viên
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            Chọn nhóm của mình để theo dõi conversion
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-warning"></i>
                            Viết tin nhắn hấp dẫn để tăng conversion
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-info"></i>
                            Nên bắt đầu với Draft để kiểm tra
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            Không nên gửi quá 50 tin/ngày cho 1 tài khoản
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="mb-2">Ví dụ tin nhắn:</h6>
                    <div class="bg-white p-3 rounded border">
                        <p class="mb-0 small">
                            Chào {name}, mình thấy bạn trong nhóm {group_name}.
                            Mình có nhóm học tiếng Anh miễn phí, bạn có muốn tham gia không?
                            Link: [link nhóm của bạn]
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection