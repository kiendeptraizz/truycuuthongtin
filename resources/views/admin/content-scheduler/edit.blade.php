@extends('layouts.admin')

@section('title', 'Chỉnh sửa bài đăng')
@section('page-title', 'Chỉnh sửa bài đăng')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Chỉnh sửa bài đăng
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.content-scheduler.update', $contentPost) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $contentPost->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div class="mb-3">
                        <label for="content" class="form-label">Nội dung <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                  id="content" name="content" rows="5" required>{{ old('content', $contentPost->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Current Image -->
                    @if($contentPost->image_path || $contentPost->image_url)
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh hiện tại</label>
                            <div class="text-center border rounded p-3">
                                @if($contentPost->image_path)
                                    <img src="{{ Storage::url($contentPost->image_path) }}" 
                                         alt="Current Image" class="img-fluid rounded" style="max-height: 200px;">
                                    <div class="mt-2">
                                        <small class="text-muted">Hình ảnh đã tải lên</small>
                                    </div>
                                @else
                                    <img src="{{ $contentPost->image_url }}" 
                                         alt="Current Image" class="img-fluid rounded" style="max-height: 200px;">
                                    <div class="mt-2">
                                        <small class="text-muted">URL: {{ $contentPost->image_url }}</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Image Options -->
                    <div class="mb-3">
                        <label class="form-label">Thay đổi hình ảnh</label>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="image" class="form-label">Tải lên hình ảnh mới</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="image_url" class="form-label">Hoặc nhập link hình ảnh mới</label>
                                <input type="url" class="form-control @error('image_url') is-invalid @enderror" 
                                       id="image_url" name="image_url" value="{{ old('image_url', $contentPost->image_url) }}" 
                                       placeholder="https://example.com/image.jpg">
                                @error('image_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <small class="text-muted">Để trống nếu không muốn thay đổi hình ảnh. Nếu cả hai được cung cấp, hình ảnh tải lên sẽ được ưu tiên.</small>
                    </div>

                    <!-- Target Groups -->
                    <div class="mb-3">
                        <label class="form-label">Nhóm cần đăng <span class="text-danger">*</span></label>
                        <div id="target-groups-container">
                            @foreach(old('target_groups', $contentPost->target_groups) as $index => $group)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control @error('target_groups.'.$index) is-invalid @enderror" 
                                           name="target_groups[]" value="{{ $group }}" 
                                           placeholder="Tên nhóm Facebook, Telegram, v.v..." required>
                                    @if($index == 0)
                                        <button type="button" class="btn btn-outline-success" onclick="addTargetGroup()">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-danger" onclick="removeTargetGroup(this)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    @endif
                                </div>
                            @endforeach
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
                               id="scheduled_at" name="scheduled_at" 
                               value="{{ old('scheduled_at', $contentPost->scheduled_at->format('Y-m-d\TH:i')) }}" required>
                        @error('scheduled_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="scheduled" {{ old('status', $contentPost->status) == 'scheduled' ? 'selected' : '' }}>Đã lên lịch</option>
                            <option value="posted" {{ old('status', $contentPost->status) == 'posted' ? 'selected' : '' }}>Đã đăng</option>
                            <option value="cancelled" {{ old('status', $contentPost->status) == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Ghi chú thêm về bài đăng...">{{ old('notes', $contentPost->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.content-scheduler.show', $contentPost) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Cập nhật bài đăng
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
        <input type="text" class="form-control" name="target_groups[]" placeholder="Tên nhóm Facebook, Telegram, v.v..." required>
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
