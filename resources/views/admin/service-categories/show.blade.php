@extends('layouts.admin')

@section('title', 'Chi tiết danh mục dịch vụ')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chi tiết danh mục: {{ $serviceCategory->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.service-categories.edit', $serviceCategory) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </a>
                        <a href="{{ route('admin.service-categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">ID</th>
                                    <td>{{ $serviceCategory->id }}</td>
                                </tr>
                                <tr>
                                    <th>Tên danh mục</th>
                                    <td><strong>{{ $serviceCategory->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Mô tả</th>
                                    <td>{{ $serviceCategory->description ?: 'Không có mô tả' }}</td>
                                </tr>
                                <tr>
                                    <th>Số gói dịch vụ</th>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $serviceCategory->servicePackages->count() }} gói
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngày tạo</th>
                                    <td>{{ $serviceCategory->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Cập nhật lần cuối</th>
                                    <td>{{ $serviceCategory->updated_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Packages in this Category -->
    @if($serviceCategory->servicePackages->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Gói dịch vụ trong danh mục này</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.service-packages.create') }}?category_id={{ $serviceCategory->id }}" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Thêm gói dịch vụ
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Tên gói</th>
                                        <th>Loại tài khoản</th>
                                        <th>Giá</th>
                                        <th>Thời hạn</th>
                                        <th>Số khách hàng</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serviceCategory->servicePackages as $package)
                                        <tr>
                                            <td>
                                                <strong>{{ $package->name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">
                                                    {{ $package->account_type }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    {{ formatCurrency($package->price) }}
                                                </strong>
                                            </td>
                                            <td>
                                                {{ $package->custom_duration ?: $package->default_duration_days . ' ngày' }}
                                            </td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $package->customer_services_count }} khách hàng
                                                </span>
                                            </td>
                                            <td>
                                                @if($package->is_active)
                                                    <span class="badge badge-success">Hoạt động</span>
                                                @else
                                                    <span class="badge badge-danger">Tạm dừng</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.service-packages.show', $package) }}" 
                                                       class="btn btn-sm btn-info" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.service-packages.edit', $package) }}" 
                                                       class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
