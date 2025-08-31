@extends('layouts.admin')

@section('title', 'Chi tiết nhà cung cấp')
@section('page-title', 'Chi tiết nhà cung cấp')

@section('styles')
<style>
    .card {
        border-radius: 15px;
        overflow: hidden;
    }

    .info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.2s ease;
    }

    .info-item {
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 0;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-size: 1.1rem;
        color: #495057;
    }

    .product-card {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }

    .product-card:hover {
        border-color: #667eea;
        background: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header info-card py-4">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper me-4"
                        style="width: 64px; height: 64px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-truck text-white" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <h3 class="mb-1 fw-bold text-white">{{ $supplier->supplier_name }}</h3>
                        <p class="mb-0 text-light opacity-75">{{ $supplier->supplier_code }}</p>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <h5 class="text-primary mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    Thông tin chi tiết
                </h5>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-barcode me-2"></i>
                        Mã nhà cung cấp
                    </div>
                    <div class="info-value fw-bold text-primary">{{ $supplier->supplier_code }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-user me-2"></i>
                        Tên nhà cung cấp
                    </div>
                    <div class="info-value">{{ $supplier->supplier_name }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-calendar-plus me-2"></i>
                        Ngày tạo
                    </div>
                    <div class="info-value">{{ $supplier->created_at->format('d/m/Y H:i:s') }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-calendar-edit me-2"></i>
                        Cập nhật lần cuối
                    </div>
                    <div class="info-value">{{ $supplier->updated_at->format('d/m/Y H:i:s') }}</div>
                </div>
            </div>
        </div>

        <!-- Danh sách dịch vụ -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-laptop me-2 text-primary"></i>
                    Danh sách dịch vụ/tài khoản
                    <span class="badge bg-primary ms-2">{{ $supplier->products->count() }}</span>
                </h5>
            </div>
            <div class="card-body p-4">
                @if($supplier->products->count() > 0)
                <div class="row">
                    @foreach($supplier->products as $index => $product)
                    <div class="col-lg-6 mb-3">
                        <div class="product-card p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0 fw-bold text-dark">
                                    <i class="fas fa-laptop me-2 text-info"></i>
                                    {{ $product->product_name }}
                                </h6>
                                <span class="badge bg-success">Dịch vụ #{{ $index + 1 }}</span>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Giá bán:</span>
                                    <span class="fw-bold text-success fs-5">{{ $product->formatted_price }}</span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    Tạo lúc: {{ $product->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-laptop fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Chưa có dịch vụ nào</h6>
                    <p class="text-muted">Nhà cung cấp này chưa có dịch vụ nào được thêm.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0 fw-bold">
                    <i class="fas fa-cog me-2"></i>
                    Thao tác
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa thông tin
                    </a>

                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST"
                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhà cung cấp này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash-alt me-2"></i>Xóa nhà cung cấp
                        </button>
                    </form>

                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-2"></i>Danh sách nhà cung cấp
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0 fw-bold">
                    <i class="fas fa-chart-bar me-2"></i>
                    Thống kê nhanh
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12 mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted">Số dịch vụ:</span>
                            <span class="fw-bold text-info">{{ $supplier->products->count() }}</span>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted">Tổng giá trị:</span>
                            <span class="fw-bold text-success">{{ number_format($supplier->products->sum('price'), 0, ',', '.') }} VND</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted">Thời gian hoạt động:</span>
                            <span class="fw-bold text-primary">{{ $supplier->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection