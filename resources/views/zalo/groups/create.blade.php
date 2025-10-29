@extends('layouts.admin')

@section('title', 'Thêm Nhóm Mục tiêu')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="mb-0">Thêm Nhóm Mục tiêu</h2>
        <p class="text-muted mb-0">Thêm nhóm Zalo mới để quét thành viên</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.zalo.groups.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Tên nhóm <span class="text-danger">*</span></label>
                            <input type="text" name="group_name" class="form-control @error('group_name') is-invalid @enderror"
                                value="{{ old('group_name') }}" required>
                            @error('group_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Link nhóm Zalo <span class="text-danger">*</span></label>
                            <input type="url" name="group_link" class="form-control @error('group_link') is-invalid @enderror"
                                value="{{ old('group_link') }}" placeholder="https://zalo.me/g/..." required>
                            @error('group_link')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ID nhóm</label>
                            <input type="text" name="group_id" class="form-control @error('group_id') is-invalid @enderror"
                                value="{{ old('group_id') }}">
                            <small class="text-muted">ID nhóm từ Zalo (nếu có)</small>
                            @error('group_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Loại nhóm <span class="text-danger">*</span></label>
                                <select name="group_type" class="form-select @error('group_type') is-invalid @enderror" required>
                                    <option value="competitor" {{ old('group_type') === 'competitor' ? 'selected' : '' }}>Nhóm đối thủ</option>
                                    <option value="own" {{ old('group_type') === 'own' ? 'selected' : '' }}>Nhóm của tôi</option>
                                </select>
                                @error('group_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Chủ đề</label>
                            <input type="text" name="topic" class="form-control @error('topic') is-invalid @enderror"
                                value="{{ old('topic') }}" placeholder="Ví dụ: Tiếng Anh giao tiếp">
                            @error('topic')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số thành viên</label>
                                <input type="number" name="total_members" class="form-control @error('total_members') is-invalid @enderror"
                                    value="{{ old('total_members', 0) }}" min="0">
                                @error('total_members')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày khai giảng</label>
                                <input type="date" name="opening_date" class="form-control @error('opening_date') is-invalid @enderror"
                                    value="{{ old('opening_date') }}">
                                @error('opening_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu nhóm
                            </button>
                            <a href="{{ route('admin.zalo.groups.index') }}" class="btn btn-secondary">
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
                            <strong>Nhóm đối thủ:</strong> Nhóm cần quét thành viên để gửi tin
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Nhóm của tôi:</strong> Nhóm để kéo thành viên về
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-warning"></i>
                            Cần có tool quét thành viên riêng
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-info"></i>
                            Số thành viên sẽ tự động cập nhật khi quét
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection