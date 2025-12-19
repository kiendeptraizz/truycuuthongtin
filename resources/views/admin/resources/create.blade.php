@extends('layouts.admin')

@section('title', 'Tạo danh mục tài nguyên')

@section('page-title', 'Tạo danh mục tài nguyên')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-folder-plus me-2"></i>Thêm danh mục mới
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.resources.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name') }}"
                                placeholder="Ví dụ: ChatGPT, Netflix, Spotify..." required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="icon" class="form-label">Icon (FontAwesome class)</label>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror"
                                    id="icon" name="icon" value="{{ old('icon') }}"
                                    placeholder="Ví dụ: fas fa-robot">
                                @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tham khảo: <a href="https://fontawesome.com/icons" target="_blank">FontAwesome Icons</a></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="color" class="form-label">Màu sắc</label>
                                <select class="form-select @error('color') is-invalid @enderror" id="color" name="color">
                                    <option value="primary" {{ old('color') == 'primary' ? 'selected' : '' }}>🔵 Primary (Xanh dương)</option>
                                    <option value="success" {{ old('color') == 'success' ? 'selected' : '' }}>🟢 Success (Xanh lá)</option>
                                    <option value="info" {{ old('color') == 'info' ? 'selected' : '' }}>🔷 Info (Xanh nhạt)</option>
                                    <option value="warning" {{ old('color') == 'warning' ? 'selected' : '' }}>🟡 Warning (Vàng)</option>
                                    <option value="danger" {{ old('color') == 'danger' ? 'selected' : '' }}>🔴 Danger (Đỏ)</option>
                                    <option value="secondary" {{ old('color') == 'secondary' ? 'selected' : '' }}>⚫ Secondary (Xám)</option>
                                    <option value="dark" {{ old('color') == 'dark' ? 'selected' : '' }}>⬛ Dark (Đen)</option>
                                </select>
                                @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                id="description" name="description" rows="3"
                                placeholder="Mô tả về danh mục này...">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                    id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Số nhỏ hơn sẽ hiển thị trước</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">Trạng thái</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_active"
                                        name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Hiển thị danh mục</label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.resources.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Tạo danh mục
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection