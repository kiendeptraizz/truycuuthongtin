@extends('layouts.admin')

@section('title', 'Chi tiết bài đăng')
@section('page-title', 'Chi tiết bài đăng')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-eye me-2"></i>Chi tiết bài đăng
                </h5>
                <div class="btn-group">
                    <a href="{{ route('admin.content-scheduler.edit', $contentPost) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Chỉnh sửa
                    </a>
                    @if($contentPost->status == 'scheduled')
                        <form method="POST" action="{{ route('admin.content-scheduler.mark-posted', $contentPost) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Đánh dấu bài đăng này là đã đăng?')">
                                <i class="fas fa-check me-1"></i>Đánh dấu đã đăng
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Status Alert -->
                @if($contentPost->isOverdue())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Quá hạn!</strong> Bài đăng này đã qua thời gian đăng dự kiến.
                    </div>
                @elseif($contentPost->needsReminder())
                    <div class="alert alert-warning">
                        <i class="fas fa-clock me-2"></i>
                        <strong>Sắp đến giờ!</strong> Bài đăng này sẽ được đăng trong vòng 1 giờ tới.
                    </div>
                @endif

                <!-- Title -->
                <div class="mb-4">
                    <h4 class="text-primary">{{ $contentPost->title }}</h4>
                </div>

                <!-- Content -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Nội dung:</h6>
                    <div class="border rounded p-3 bg-light">
                        {!! nl2br(e($contentPost->content)) !!}
                    </div>
                </div>

                <!-- Image -->
                @if($contentPost->image_path || $contentPost->image_url)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Hình ảnh:</h6>
                        <div class="text-center">
                            @if($contentPost->image_path)
                                <img src="{{ Storage::url($contentPost->image_path) }}" 
                                     alt="Content Image" class="img-fluid rounded" style="max-height: 300px;">
                                <div class="mt-2">
                                    <small class="text-muted">Hình ảnh đã tải lên</small>
                                </div>
                            @else
                                <img src="{{ $contentPost->image_url }}" 
                                     alt="Content Image" class="img-fluid rounded" style="max-height: 300px;">
                                <div class="mt-2">
                                    <small class="text-muted">Hình ảnh từ URL: 
                                        <a href="{{ $contentPost->image_url }}" target="_blank">{{ $contentPost->image_url }}</a>
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Details -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Nhóm cần đăng:</h6>
                            @foreach($contentPost->target_groups as $group)
                                <span class="badge bg-info me-1 mb-1">{{ $group }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Trạng thái:</h6>
                            @if($contentPost->status == 'scheduled')
                                <span class="badge bg-primary fs-6">Đã lên lịch</span>
                            @elseif($contentPost->status == 'posted')
                                <span class="badge bg-success fs-6">Đã đăng</span>
                            @else
                                <span class="badge bg-danger fs-6">Đã hủy</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Thời gian đăng:</h6>
                            <p class="mb-0">
                                <i class="fas fa-calendar me-2"></i>
                                {{ $contentPost->scheduled_at->format('d/m/Y H:i') }}
                            </p>
                            <small class="text-muted">
                                ({{ $contentPost->scheduled_at->diffForHumans() }})
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Nhắc nhở:</h6>
                            @if($contentPost->notification_sent)
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Đã gửi
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-clock me-1"></i>Chưa gửi
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if($contentPost->notes)
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Ghi chú:</h6>
                        <div class="border rounded p-3 bg-light">
                            {!! nl2br(e($contentPost->notes)) !!}
                        </div>
                    </div>
                @endif

                <!-- Timestamps -->
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="fas fa-plus-circle me-1"></i>
                            Tạo: {{ $contentPost->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="fas fa-edit me-1"></i>
                            Cập nhật: {{ $contentPost->updated_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.content-scheduler.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                    </a>
                    <div>
                        <a href="{{ route('admin.content-scheduler.edit', $contentPost) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Chỉnh sửa
                        </a>
                        <form method="POST" action="{{ route('admin.content-scheduler.destroy', $contentPost) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xóa bài đăng này?')">
                                <i class="fas fa-trash me-2"></i>Xóa
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
