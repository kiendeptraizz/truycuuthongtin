@extends('layouts.admin')

@section('title', 'Quản lý danh mục dịch vụ')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Danh mục dịch vụ</h3>
                    <a href="{{ route('admin.service-categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Thêm danh mục
                    </a>
                </div>

                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Tìm kiếm danh mục..." 
                                           value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if(request('search'))
                                    <a href="{{ route('admin.service-categories.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Xóa bộ lọc
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    <!-- Categories Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên danh mục</th>
                                    <th>Mô tả</th>
                                    <th>Số gói dịch vụ</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>{{ $category->id }}</td>
                                        <td>
                                            <strong>{{ $category->name }}</strong>
                                        </td>
                                        <td>
                                            {{ Str::limit($category->description, 100) }}
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $category->service_packages_count }} gói
                                            </span>
                                        </td>
                                        <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.service-categories.show', $category) }}" 
                                                   class="btn btn-sm btn-info" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.service-categories.edit', $category) }}" 
                                                   class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($category->service_packages_count == 0)
                                                    <form method="POST" 
                                                          action="{{ route('admin.service-categories.destroy', $category) }}" 
                                                          style="display: inline;"
                                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-secondary" disabled title="Không thể xóa danh mục có gói dịch vụ">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <em>Không có danh mục nào.</em>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($categories->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $categories->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
