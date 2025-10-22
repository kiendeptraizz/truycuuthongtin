@extends('layouts.admin')

@section('title', 'Tạo bài đăng mới')
@section('page-title', 'Tạo bài đăng mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Tạo bài đăng mới
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.content-scheduler.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                            id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div class="mb-3">
                        <label for="content" class="form-label">Nội dung <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror"
                            id="content" name="content" rows="5" required>{{ old('content') }}</textarea>
                        @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Image Options -->
                    <div class="mb-3">
                        <label class="form-label">Hình ảnh</label>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="image" class="form-label">Tải lên hình ảnh</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                    id="image" name="image" accept="image/*">
                                @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="image_url" class="form-label">Hoặc nhập link hình ảnh</label>
                                <input type="url" class="form-control @error('image_url') is-invalid @enderror"
                                    id="image_url" name="image_url" value="{{ old('image_url') }}"
                                    placeholder="https://example.com/image.jpg">
                                @error('image_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <small class="text-muted">Chọn một trong hai tùy chọn trên. Nếu cả hai được cung cấp, hình ảnh tải lên sẽ được ưu tiên.</small>
                    </div>

                    <!-- Target Groups -->
                    <div class="mb-3">
                        <label class="form-label">Nhóm cần đăng <span class="text-danger">*</span></label>
                        <div id="target-groups-container">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control @error('target_groups.0') is-invalid @enderror"
                                    name="target_groups[]" value="{{ old('target_groups.0') }}"
                                    placeholder="Tên nhóm Facebook, Zalo, v.v..." required>
                                <button type="button" class="btn btn-outline-success" onclick="addTargetGroup()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        @error('target_groups')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        @error('target_groups.*')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Scheduled Time -->
                    <div class="mb-3">
                        <label for="scheduled_at" class="form-label">Thời gian đăng <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror"
                            id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at', $selectedDate . 'T12:00') }}" required>
                        @error('scheduled_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Thời gian phải sau thời điểm hiện tại</small>
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                            id="notes" name="notes" rows="3"
                            placeholder="Ghi chú thêm về bài đăng...">{{ old('notes') }}</textarea>
                        @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.content-scheduler.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Lưu bài đăng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function addTargetGroup() {
        const container = document.getElementById('target-groups-container');
        const div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `
        <input type="text" class="form-control" name="target_groups[]" placeholder="Tên nhóm Facebook, Zalo, v.v..." required>
        <button type="button" class="btn btn-outline-danger" onclick="removeTargetGroup(this)">
            <i class="fas fa-minus"></i>
        </button>
    `;
        container.appendChild(div);
    }

    function removeTargetGroup(button) {
        const container = document.getElementById('target-groups-container');
        if (container.children.length > 1) {
            button.parentElement.remove();
        }
    }

    // Set minimum datetime to current time
    document.addEventListener('DOMContentLoaded', function() {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('scheduled_at').min = now.toISOString().slice(0, 16);
    });

    // Handle image input conflict
    document.getElementById('image').addEventListener('change', function() {
        if (this.files.length > 0) {
            document.getElementById('image_url').value = '';
        }
    });

    document.getElementById('image_url').addEventListener('input', function() {
        if (this.value.trim() !== '') {
            document.getElementById('image').value = '';
        }
    });
</script>
@endpush