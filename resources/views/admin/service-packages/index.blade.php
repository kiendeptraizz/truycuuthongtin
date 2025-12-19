@extends('layouts.admin')

@section('title', 'Quản lý gói dịch vụ')
@section('page-title', 'Quản lý gói dịch vụ')

@push('styles')
<style>
    .service-packages-table {
        font-size: 0.875rem;
        min-width: 1000px;
    }

    .service-packages-table th,
    .service-packages-table td {
        padding: 0.75rem 0.5rem;
        vertical-align: middle;
    }

    @media (max-width: 1200px) {
        .service-packages-table .d-none-lg {
            display: none !important;
        }
    }

    @media (max-width: 768px) {
        .service-packages-table .d-none-md {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center mb-2 mb-md-0">
                        <div class="icon-wrapper me-3" style="width: 50px; height: 50px; background: var(--success-gradient); border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-box fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Danh sách gói dịch vụ</h5>
                            <small class="text-muted">Quản lý các gói dịch vụ và sản phẩm</small>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Search và Actions -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" data-table-search="servicePackagesTable"
                            class="form-control" placeholder="Tìm kiếm gói dịch vụ...">
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted" data-table-count="servicePackagesTable">
                            Hiển thị {{ $servicePackages->count() }} gói dịch vụ
                        </small>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="btn-group">
                            <button class="btn btn-outline-secondary btn-sm" data-export-csv="servicePackagesTable">
                                <i class="fas fa-file-csv me-1"></i>
                                Xuất CSV
                            </button>
                            <a href="{{ route('admin.service-packages.create') }}"
                               class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>
                                Thêm gói dịch vụ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
    
    <div class="card-body">
        <!-- Advanced Filters Card -->
        <div class="card bg-light mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold text-muted mb-0">
                        <i class="fas fa-filter me-1"></i>
                        Bộ lọc nâng cao
                    </h6>
                    @if(request()->hasAny(['search', 'category_id', 'status', 'date_from', 'date_to']))
                        <div class="badge bg-primary">
                            {{ collect(request()->only(['search', 'category_id', 'status', 'date_from', 'date_to']))->filter()->count() }} bộ lọc đang áp dụng
                        </div>
                    @endif
                </div>
                <form method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-semibold">
                                <i class="fas fa-search me-1"></i>Tìm kiếm
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" 
                                       name="search" 
                                       class="form-control border-start-0 ps-0" 
                                       placeholder="Tên gói, loại tài khoản..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted fw-semibold">
                                <i class="fas fa-tags me-1"></i>Danh mục
                            </label>
                            <select name="category_id" class="form-select">
                                <option value="">Tất cả danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted fw-semibold">
                                <i class="fas fa-toggle-on me-1"></i>Trạng thái
                            </label>
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted fw-semibold">
                                <i class="fas fa-calendar-alt me-1"></i>Từ ngày
                            </label>
                            <input type="date" 
                                   name="date_from" 
                                   class="form-control"
                                   value="{{ request('date_from') }}"
                                   max="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted fw-semibold">
                                <i class="fas fa-calendar-check me-1"></i>Đến ngày
                            </label>
                            <input type="date" 
                                   name="date_to" 
                                   class="form-control"
                                   value="{{ request('date_to') }}"
                                   max="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <div class="d-flex flex-column gap-2 w-100">
                                <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center" title="Áp dụng bộ lọc">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request()->hasAny(['search', 'category_id', 'status', 'date_from', 'date_to']))
                                    <a href="{{ route('admin.service-packages.index') }}" 
                                       class="btn btn-outline-secondary d-flex align-items-center justify-content-center"
                                       title="Xóa tất cả bộ lọc">
                                        <i class="fas fa-times-circle"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Filter Buttons -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2">
                                <span class="small text-muted fw-semibold">Lọc nhanh:</span>
                                <a href="{{ route('admin.service-packages.index', ['date_from' => now()->format('Y-m-d')]) }}" 
                                   class="btn btn-sm {{ request('date_from') === now()->format('Y-m-d') ? 'btn-primary' : 'btn-outline-primary' }}">
                                    <i class="fas fa-calendar-day me-1"></i>Hôm nay
                                </a>
                                <a href="{{ route('admin.service-packages.index', ['date_from' => now()->startOfWeek()->format('Y-m-d'), 'date_to' => now()->endOfWeek()->format('Y-m-d')]) }}" 
                                   class="btn btn-sm {{ request('date_from') === now()->startOfWeek()->format('Y-m-d') && request('date_to') === now()->endOfWeek()->format('Y-m-d') ? 'btn-primary' : 'btn-outline-primary' }}">
                                    <i class="fas fa-calendar-week me-1"></i>Tuần này
                                </a>
                                <a href="{{ route('admin.service-packages.index', ['date_from' => now()->startOfMonth()->format('Y-m-d'), 'date_to' => now()->endOfMonth()->format('Y-m-d')]) }}" 
                                   class="btn btn-sm {{ request('date_from') === now()->startOfMonth()->format('Y-m-d') && request('date_to') === now()->endOfMonth()->format('Y-m-d') ? 'btn-primary' : 'btn-outline-primary' }}">
                                    <i class="fas fa-calendar me-1"></i>Tháng này
                                </a>
                                <a href="{{ route('admin.service-packages.index', ['status' => 'active']) }}" 
                                   class="btn btn-sm {{ request('status') === 'active' ? 'btn-success' : 'btn-outline-success' }}">
                                    <i class="fas fa-check-circle me-1"></i>Đang hoạt động
                                </a>
                                <a href="{{ route('admin.service-packages.index', ['status' => 'inactive']) }}" 
                                   class="btn btn-sm {{ request('status') === 'inactive' ? 'btn-warning' : 'btn-outline-warning' }}">
                                    <i class="fas fa-pause-circle me-1"></i>Tạm dừng
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Account Type Filter -->
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-body py-2">
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <span class="small text-muted fw-semibold me-2">
                                            <i class="fas fa-filter me-1"></i>Loại tài khoản:
                                        </span>
                                        <a href="{{ route('admin.service-packages.index', ['account_type' => 'Tài khoản chính chủ']) }}" 
                                           class="btn btn-sm {{ request('account_type') === 'Tài khoản chính chủ' ? 'btn-primary' : 'btn-outline-primary' }}">
                                            <i class="fas fa-user me-1"></i>Chính chủ
                                            @if(isset($accountTypeStats['Tài khoản chính chủ']))
                                                <span class="badge {{ request('account_type') === 'Tài khoản chính chủ' ? 'bg-light text-dark' : 'bg-primary text-white' }} ms-1">{{ $accountTypeStats['Tài khoản chính chủ'] }}</span>
                                            @endif
                                        </a>
                                        <a href="{{ route('admin.service-packages.index', ['account_type' => 'Tài khoản dùng chung']) }}" 
                                           class="btn btn-sm {{ request('account_type') === 'Tài khoản dùng chung' ? 'btn-info' : 'btn-outline-info' }}">
                                            <i class="fas fa-users me-1"></i>Dùng chung
                                            @if(isset($accountTypeStats['Tài khoản dùng chung']))
                                                <span class="badge {{ request('account_type') === 'Tài khoản dùng chung' ? 'bg-light text-dark' : 'bg-info text-white' }} ms-1">{{ $accountTypeStats['Tài khoản dùng chung'] }}</span>
                                            @endif
                                        </a>
                                        <a href="{{ route('admin.service-packages.index', ['account_type' => 'Tài khoản add family']) }}" 
                                           class="btn btn-sm {{ request('account_type') === 'Tài khoản add family' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                                            <i class="fas fa-plus me-1"></i>Add Fam
                                            @if(isset($accountTypeStats['Tài khoản add family']))
                                                <span class="badge {{ request('account_type') === 'Tài khoản add family' ? 'bg-light text-dark' : 'bg-secondary text-white' }} ms-1">{{ $accountTypeStats['Tài khoản add family'] }}</span>
                                            @endif
                                        </a>
                                        <a href="{{ route('admin.service-packages.index', ['account_type' => 'Tài khoản cấp (dùng riêng)']) }}" 
                                           class="btn btn-sm {{ request('account_type') === 'Tài khoản cấp (dùng riêng)' ? 'btn-success' : 'btn-outline-success' }}">
                                            <i class="fas fa-crown me-1"></i>Cấp riêng
                                            @if(isset($accountTypeStats['Tài khoản cấp (dùng riêng)']))
                                                <span class="badge {{ request('account_type') === 'Tài khoản cấp (dùng riêng)' ? 'bg-light text-dark' : 'bg-success text-white' }} ms-1">{{ $accountTypeStats['Tài khoản cấp (dùng riêng)'] }}</span>
                                            @endif
                                        </a>
                                        <a href="{{ route('admin.service-packages.index', ['account_type' => 'family']) }}" 
                                           class="btn btn-sm {{ request('account_type') === 'family' ? 'btn-warning' : 'btn-outline-warning' }}">
                                            <i class="fas fa-home me-1"></i>Family
                                            @if(isset($accountTypeStats['family']))
                                                <span class="badge {{ request('account_type') === 'family' ? 'bg-light text-dark' : 'bg-warning text-white' }} ms-1">{{ $accountTypeStats['family'] }}</span>
                                            @endif
                                        </a>
                                        <div class="vr mx-2"></div>
                                        <a href="{{ route('admin.service-packages.index') }}" 
                                           class="btn btn-sm {{ !request('account_type') ? 'btn-dark' : 'btn-outline-dark' }}">
                                            <i class="fas fa-list me-1"></i>Tất cả
                                            <span class="badge {{ !request('account_type') ? 'bg-light text-dark' : 'bg-dark text-white' }} ms-1">{{ $servicePackages->total() }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Results Info with Enhanced Statistics -->
        <div class="row mb-3">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="text-muted">
                        <i class="fas fa-list me-1"></i>
                        Hiển thị <strong>{{ $servicePackages->firstItem() ?? 0 }} - {{ $servicePackages->lastItem() ?? 0 }}</strong> 
                        trong tổng số <strong>{{ $servicePackages->total() }}</strong> gói dịch vụ
                    </div>
                    @if($servicePackages->total() > 0)
                        <div class="vr"></div>
                        <div class="d-flex gap-3">
                            <div class="small">
                                <i class="fas fa-check-circle text-success me-1"></i>
                                <span class="text-success fw-semibold">{{ $servicePackages->where('is_active', true)->count() }}</span> 
                                <span class="text-muted">hoạt động</span>
                            </div>
                            <div class="small">
                                <i class="fas fa-pause-circle text-warning me-1"></i>
                                <span class="text-warning fw-semibold">{{ $servicePackages->where('is_active', false)->count() }}</span> 
                                <span class="text-muted">tạm dừng</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-4 text-end">
                @if(request()->hasAny(['search', 'category_id', 'status', 'date_from', 'date_to']))
                    <div class="small text-muted">
                        <i class="fas fa-filter me-1"></i>
                        Đã áp dụng {{ collect(request()->only(['search', 'category_id', 'status', 'date_from', 'date_to']))->filter()->count() }} bộ lọc
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Active Filters Summary -->
        @if(request()->hasAny(['search', 'category_id', 'status', 'date_from', 'date_to']))
            <div class="alert alert-info mb-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle me-2"></i>
                    <div class="flex-grow-1">
                        <strong>Bộ lọc đang áp dụng:</strong>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @if(request('search'))
                                <span class="badge bg-primary">
                                    <i class="fas fa-search me-1"></i>
                                    "{{ request('search') }}"
                                </span>
                            @endif
                            @if(request('category_id'))
                                <span class="badge bg-secondary">
                                    <i class="fas fa-tags me-1"></i>
                                    {{ $categories->find(request('category_id'))->name ?? 'Danh mục không xác định' }}
                                </span>
                            @endif
                            @if(request('status'))
                                <span class="badge {{ request('status') === 'active' ? 'bg-success' : 'bg-warning' }}">
                                    <i class="fas {{ request('status') === 'active' ? 'fa-check-circle' : 'fa-pause-circle' }} me-1"></i>
                                    {{ request('status') === 'active' ? 'Hoạt động' : 'Tạm dừng' }}
                                </span>
                            @endif
                            @if(request('date_from'))
                                <span class="badge bg-info">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Từ {{ \Carbon\Carbon::parse(request('date_from'))->format('d/m/Y') }}
                                </span>
                            @endif
                            @if(request('date_to'))
                                <span class="badge bg-info">
                                    <i class="fas fa-calendar-check me-1"></i>
                                    Đến {{ \Carbon\Carbon::parse(request('date_to'))->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('admin.service-packages.index') }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-times-circle me-1"></i>
                        Xóa tất cả
                    </a>
                </div>
            </div>
        @endif
        
        <!-- Service Packages Table -->
        @if($servicePackages->count() > 0)
            <div class="table-responsive">
                <table id="servicePackagesTable" class="table table-hover service-packages-table enhanced-table">
                    <thead>
                        <tr>
                            <th style="min-width: 250px;">
                                <i class="fas fa-box me-2"></i>
                                Thông tin gói
                            </th>
                            <th style="min-width: 120px;">
                                <i class="fas fa-tag me-2"></i>
                                Danh mục
                            </th>
                            <th style="min-width: 140px;">
                                <i class="fas fa-user-circle me-2"></i>
                                Loại tài khoản
                            </th>
                            <th style="min-width: 150px;">
                                <i class="fas fa-server me-2"></i>
                                Số dịch vụ
                            </th>
                            <th style="min-width: 140px;">
                                <i class="fas fa-users me-2"></i>
                                Khách hàng
                            </th>
                            <th style="min-width: 150px;">
                                <i class="fas fa-toggle-on me-2"></i>
                                Trạng thái
                            </th>
                            <th class="text-center table-action-column" style="min-width: 160px;">
                                <i class="fas fa-cogs me-2"></i>
                                Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($servicePackages as $package)
                            <tr id="package-{{ $package->id }}">
                                <td>
                                    <div class="d-flex align-items-start">
                                        <div class="icon-wrapper me-3" style="width: 40px; height: 40px; background: var(--success-gradient); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i class="fas fa-box text-white"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $package->name }}</div>
                                            @if($package->description)
                                                <small class="text-muted d-block">{{ Str::limit($package->description, 60) }}</small>
                                            @endif
                                            <div class="mt-1">
                                                <span class="badge bg-info">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $package->default_duration_days }} ngày
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $package->category->name ?? 'Chưa phân loại' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $package->account_type }}</span>
                                </td>
                                <td>
                                    @php
                                        $serviceCount = $package->customerServices->count();
                                        $activeServiceCount = $package->customerServices->where('status', 'active')->count();
                                    @endphp
                                    <div>
                                        @if($serviceCount > 0)
                                            <div class="fw-bold text-primary mb-1">
                                                <i class="fas fa-server me-1"></i>
                                                {{ $serviceCount }} dịch vụ
                                            </div>
                                            <div class="d-flex gap-2">
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    {{ $activeServiceCount }} hoạt động
                                                </span>
                                                @if($serviceCount > $activeServiceCount)
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-pause-circle me-1"></i>
                                                        {{ $serviceCount - $activeServiceCount }} tạm dừng
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-minus me-1"></i>
                                                Chưa có dịch vụ
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $customerCount = $package->customerServices->pluck('customer_id')->unique()->count();
                                    @endphp
                                    @if($customerCount > 0)
                                        <span class="badge bg-primary">
                                            <i class="fas fa-users me-1"></i>
                                            {{ $customerCount }} khách hàng
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-minus me-1"></i>
                                            Chưa có khách hàng
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($package->is_active)
                                            <span class="badge bg-success">Hoạt động</span>
                                        @else
                                            <span class="badge bg-danger">Tạm dừng</span>
                                        @endif

                                        <form action="{{ route('admin.service-packages.toggle-status', $package) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-sm {{ $package->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                    title="{{ $package->is_active ? 'Tạm dừng' : 'Kích hoạt' }}"
                                                    onclick="return confirm('Bạn có chắc muốn {{ $package->is_active ? 'tạm dừng' : 'kích hoạt' }} gói dịch vụ này?')">
                                                <i class="fas {{ $package->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td class="table-action-column">
                                    <div class="btn-group" role="group" style="white-space: nowrap;">
                                        <a href="{{ route('admin.service-packages.show', $package) }}"
                                           class="btn btn-sm btn-info text-white"
                                           title="Xem chi tiết"
                                           style="min-width: 40px;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.service-packages.edit', $package) }}"
                                           class="btn btn-sm btn-warning text-white"
                                           title="Chỉnh sửa"
                                           style="min-width: 40px;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST"
                                              action="{{ route('admin.service-packages.destroy', $package) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa gói dịch vụ này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-danger text-white"
                                                    title="Xóa"
                                                    style="min-width: 40px;">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Hiển thị {{ $servicePackages->firstItem() ?? 0 }} đến {{ $servicePackages->lastItem() ?? 0 }}
                    trong tổng số {{ $servicePackages->total() }} gói dịch vụ
                </div>
                <div>
                    {{ $servicePackages->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Không tìm thấy gói dịch vụ nào</h5>
                @if(request()->hasAny(['search', 'category_id', 'status', 'date_from', 'date_to']))
                    <p class="text-muted">Thử thay đổi bộ lọc hoặc <a href="{{ route('admin.service-packages.index') }}">xóa bộ lọc</a></p>
                @else
                    <p class="text-muted">Hãy <a href="{{ route('admin.service-packages.create') }}">thêm gói dịch vụ đầu tiên</a></p>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<style>
/* Đảm bảo nút thêm gói dịch vụ luôn hiển thị */
.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    border: none !important;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3) !important;
    transition: all 0.3s ease !important;
}

.btn-success:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4) !important;
}

/* Đảm bảo các nút thao tác luôn hiển thị */
.table-action-column {
    position: sticky !important;
    right: 0 !important;
    background: white !important;
    z-index: 10 !important;
    box-shadow: -2px 0 4px rgba(0, 0, 0, 0.1) !important;
    min-width: 160px !important;
    max-width: 160px !important;
    width: 160px !important;
}

.table-action-column .btn-group {
    display: flex !important;
    gap: 2px !important;
    justify-content: center !important;
    align-items: center !important;
    white-space: nowrap !important;
}

.table-action-column .btn {
    flex-shrink: 0 !important;
    min-width: 40px !important;
    height: 32px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

/* Đảm bảo table có thể cuộn ngang */
.table-responsive {
    overflow-x: auto !important;
    overflow-y: visible !important;
}

.table {
    min-width: 1200px !important;
    margin-bottom: 0 !important;
}

/* Đảm bảo header cũng sticky */
.table thead th.table-action-column {
    position: sticky !important;
    right: 0 !important;
    background: #f8f9fa !important;
    z-index: 11 !important;
    box-shadow: -2px 0 4px rgba(0, 0, 0, 0.1) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to specific package if anchor is present in URL
    if (window.location.hash) {
        const targetElement = document.querySelector(window.location.hash);
        if (targetElement) {
            setTimeout(() => {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                // Add highlight effect
                targetElement.style.backgroundColor = '#fff3cd';
                setTimeout(() => {
                    targetElement.style.backgroundColor = '';
                }, 3000);
            }, 100);
        }
    }

    // Auto-validation for date inputs
    const dateFromInput = document.querySelector('input[name="date_from"]');
    const dateToInput = document.querySelector('input[name="date_to"]');
    
    if (dateFromInput && dateToInput) {
        dateFromInput.addEventListener('change', function() {
            if (this.value && dateToInput.value && this.value > dateToInput.value) {
                dateToInput.value = this.value;
            }
            dateToInput.min = this.value;
        });
        
        dateToInput.addEventListener('change', function() {
            if (this.value && dateFromInput.value && this.value < dateFromInput.value) {
                dateFromInput.value = this.value;
            }
            dateFromInput.max = this.value;
        });
    }
    
    // Real-time search with debounce
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const value = this.value.trim();
            
            if (value.length === 0 || value.length >= 3) {
                searchTimeout = setTimeout(() => {
                    // Auto-submit form when search has 3+ characters or is empty
                    if (value.length >= 3) {
                        document.querySelector('form').submit();
                    }
                }, 500);
            }
        });
    }
    
    // Enhanced status display
    const statusSelects = document.querySelectorAll('select[name="status"]');
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value === 'active') {
                this.classList.remove('border-warning');
                this.classList.add('border-success');
            } else if (selectedOption.value === 'inactive') {
                this.classList.remove('border-success');
                this.classList.add('border-warning');
            } else {
                this.classList.remove('border-success', 'border-warning');
            }
        });
    });
    
    // Tooltip initialization
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Show filter summary
    const activeFilters = [];
    if (document.querySelector('input[name="search"]').value) {
        activeFilters.push('Tìm kiếm: ' + document.querySelector('input[name="search"]').value);
    }
    if (document.querySelector('select[name="category_id"]').value) {
        const categoryText = document.querySelector('select[name="category_id"] option:checked').text;
        activeFilters.push('Danh mục: ' + categoryText);
    }
    if (document.querySelector('select[name="status"]').value) {
        const statusText = document.querySelector('select[name="status"] option:checked').text.trim();
        activeFilters.push('Trạng thái: ' + statusText);
    }
    if (document.querySelector('input[name="date_from"]').value) {
        activeFilters.push('Từ ngày: ' + document.querySelector('input[name="date_from"]').value);
    }
    if (document.querySelector('input[name="date_to"]').value) {
        activeFilters.push('Đến ngày: ' + document.querySelector('input[name="date_to"]').value);
    }
    
    if (activeFilters.length > 0) {
        console.log('Bộ lọc đang áp dụng:', activeFilters.join(', '));
    }

    // Force show action buttons
    setTimeout(function() {
        const actionColumns = document.querySelectorAll('.table-action-column');
        actionColumns.forEach(col => {
            col.style.display = 'table-cell';
            col.style.visibility = 'visible';
            col.style.opacity = '1';
        });

        const btnGroups = document.querySelectorAll('.table-action-column .btn-group');
        btnGroups.forEach(group => {
            group.style.display = 'flex';
            group.style.visibility = 'visible';
            group.style.opacity = '1';
        });

        const actionBtns = document.querySelectorAll('.table-action-column .btn');
        actionBtns.forEach(btn => {
            btn.style.display = 'flex';
            btn.style.visibility = 'visible';
            btn.style.opacity = '1';
        });

        // Ensure add button is visible
        const addBtn = document.querySelector('.btn-success');
        if (addBtn) {
            addBtn.style.display = 'inline-flex';
            addBtn.style.visibility = 'visible';
            addBtn.style.opacity = '1';
        }
    }, 100);
});
</script>

<!-- Service Packages Fix Script -->
<script src="{{ asset('js/service-packages-fix.js') }}"></script>
@endsection
