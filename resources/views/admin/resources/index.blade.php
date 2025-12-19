@extends('layouts.admin')

@section('title', 'Quản lý Tài nguyên')

@section('page-title', 'Quản lý Tài nguyên')

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Tổng danh mục</h6>
                            <h2 class="card-title mb-0">{{ number_format($stats['total_categories']) }}</h2>
                        </div>
                        <i class="fas fa-folder-open fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Tổng tài khoản</h6>
                            <h2 class="card-title mb-0">{{ number_format($stats['total_accounts']) }}</h2>
                        </div>
                        <i class="fas fa-key fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Còn khả dụng</h6>
                            <h2 class="card-title mb-0">{{ number_format($stats['available_accounts']) }}</h2>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Sắp hết hạn</h6>
                            <h2 class="card-title mb-0">{{ number_format($stats['expiring_soon']) }}</h2>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title mb-0">
                <i class="fas fa-boxes me-2"></i>Danh mục Tài nguyên
            </h5>
            <div class="d-flex gap-2">
                <form action="{{ route('admin.resources.update-expired') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning"
                        onclick="return confirm('Cập nhật trạng thái tất cả tài khoản hết hạn?')">
                        <i class="fas fa-sync-alt me-1"></i> Cập nhật hết hạn
                    </button>
                </form>
                <a href="{{ route('admin.resources.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Thêm danh mục
                </a>
            </div>
        </div>

        <div class="card-body">
            <!-- Search Form -->
            <form method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Tìm kiếm danh mục..."
                                value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        @if(request('search'))
                        <a href="{{ route('admin.resources.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times-circle me-1"></i> Xóa bộ lọc
                        </a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Categories Grid -->
            @if($categories->count() > 0)
            <div class="row">
                @foreach($categories as $category)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm {{ !$category->is_active ? 'opacity-75' : '' }}">
                        <div class="card-header bg-{{ $category->color ?? 'primary' }} text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    @if($category->icon)
                                    <i class="{{ $category->icon }} me-2"></i>
                                    @else
                                    <i class="fas fa-folder me-2"></i>
                                    @endif
                                    {{ $category->name }}
                                </h6>
                                @if(!$category->is_active)
                                <span class="badge bg-dark">Ẩn</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if($category->description)
                            <p class="text-muted small mb-3">{{ Str::limit($category->description, 80) }}</p>
                            @endif

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tổng tài khoản:</span>
                                <span class="fw-bold">{{ $category->accounts_count }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Còn khả dụng:</span>
                                <span class="fw-bold text-success">{{ $category->available_accounts_count }}</span>
                            </div>

                            @if($category->accounts_count > 0)
                            <div class="progress mb-3" style="height: 8px;">
                                @php
                                $percentage = ($category->available_accounts_count / $category->accounts_count) * 100;
                                @endphp
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $percentage }}%"></div>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex gap-2 justify-content-between">
                                <a href="{{ route('admin.resources.show', $category) }}"
                                    class="btn btn-sm btn-outline-primary flex-grow-1">
                                    <i class="fas fa-eye me-1"></i> Xem
                                </a>
                                <a href="{{ route('admin.resources.edit', $category) }}"
                                    class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($category->accounts_count == 0)
                                <form method="POST"
                                    action="{{ route('admin.resources.destroy', $category) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($categories->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $categories->appends(request()->query())->links() }}
            </div>
            @endif
            @else
            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Chưa có danh mục nào</h5>
                <p class="text-muted">Tạo danh mục đầu tiên để bắt đầu quản lý tài nguyên</p>
                <a href="{{ route('admin.resources.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Tạo danh mục
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection