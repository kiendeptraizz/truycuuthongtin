@extends('layouts.admin')

@section('title', 'Chi tiết nhà cung cấp tiềm năng')

@section('styles')
<style>
    .info-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out;
    }

    .info-card:hover {
        transform: translateY(-2px);
    }

    .info-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid #e3e6f0;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 0.25rem;
    }

    .info-value {
        color: #858796;
    }

    .service-card {
        border: 1px solid #e3e6f0;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        background: #f8f9fc;
    }

    .service-header {
        display: flex;
        justify-content-between;
        align-items: center;
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #e3e6f0;
    }

    .service-name {
        font-weight: 600;
        color: #5a5c69;
        margin: 0;
    }

    .service-price {
        font-weight: 700;
        color: #1cc88a;
        font-size: 1.1rem;
    }

    .service-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .service-detail-item {
        display: flex;
        flex-direction: column;
    }

    .service-detail-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .service-detail-value {
        color: #495057;
    }

    .priority-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .priority-high {
        background: linear-gradient(45deg, #e74a3b, #c0392b);
        color: white;
    }

    .priority-medium {
        background: linear-gradient(45deg, #f39c12, #e67e22);
        color: white;
    }

    .priority-low {
        background: linear-gradient(45deg, #95a5a6, #7f8c8d);
        color: white;
    }

    .stats-summary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .stats-item {
        text-align: center;
    }

    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stats-label {
        font-size: 0.875rem;
        opacity: 0.9;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800 fw-bold">
                <i class="fas fa-eye me-2 text-primary"></i>
                Chi tiết nhà cung cấp tiềm năng
            </h1>
            <p class="mb-0 text-muted">{{ $potentialSupplier->supplier_code }} - {{ $potentialSupplier->supplier_name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.potential-suppliers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
            <a href="{{ route('admin.potential-suppliers.edit', $potentialSupplier) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Chỉnh sửa
            </a>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="stats-summary">
        <div class="row">
            <div class="col-md-3">
                <div class="stats-item">
                    <div class="stats-number">{{ $potentialSupplier->services->count() }}</div>
                    <div class="stats-label">Dịch vụ</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-item">
                    <div class="stats-number">{{ number_format($potentialSupplier->total_estimated_value, 0, '.', ',') }}</div>
                    <div class="stats-label">Giá trị ước tính (VND)</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-item">
                    <div class="stats-number">
                        <span class="priority-badge priority-{{ $potentialSupplier->priority }}">
                            {{ $potentialSupplier->priority_label }}
                        </span>
                    </div>
                    <div class="stats-label">Mức độ ưu tiên</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-item">
                    <div class="stats-number">{{ $potentialSupplier->created_at->diffForHumans() }}</div>
                    <div class="stats-label">Được tạo</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Thông tin cơ bản -->
        <div class="col-lg-4">
            <div class="card info-card">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0 fw-bold">
                        <i class="fas fa-info-circle me-2"></i>
                        Thông tin cơ bản
                    </h6>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-barcode me-2"></i>Mã nhà cung cấp
                        </div>
                        <div class="info-value fw-bold text-primary">{{ $potentialSupplier->supplier_code }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-building me-2"></i>Tên nhà cung cấp
                        </div>
                        <div class="info-value fw-semibold">{{ $potentialSupplier->supplier_name }}</div>
                    </div>

                    @if($potentialSupplier->contact_person)
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user me-2"></i>Người liên hệ
                        </div>
                        <div class="info-value">{{ $potentialSupplier->contact_person }}</div>
                    </div>
                    @endif

                    @if($potentialSupplier->phone)
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-phone me-2"></i>Số điện thoại
                        </div>
                        <div class="info-value">
                            <a href="tel:{{ $potentialSupplier->phone }}" class="text-decoration-none">
                                {{ $potentialSupplier->phone }}
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($potentialSupplier->email)
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-envelope me-2"></i>Email
                        </div>
                        <div class="info-value">
                            <a href="mailto:{{ $potentialSupplier->email }}" class="text-decoration-none">
                                {{ $potentialSupplier->email }}
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($potentialSupplier->website)
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-globe me-2"></i>Website
                        </div>
                        <div class="info-value">
                            <a href="{{ $potentialSupplier->website }}" target="_blank" class="text-decoration-none">
                                {{ $potentialSupplier->website }}
                                <i class="fas fa-external-link-alt ms-1"></i>
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($potentialSupplier->address)
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-map-marker-alt me-2"></i>Địa chỉ
                        </div>
                        <div class="info-value">{{ $potentialSupplier->address }}</div>
                    </div>
                    @endif

                    @if($potentialSupplier->expected_cooperation_date)
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-calendar me-2"></i>Ngày dự kiến hợp tác
                        </div>
                        <div class="info-value">{{ $potentialSupplier->expected_cooperation_date->format('d/m/Y') }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-clock me-2"></i>Ngày tạo
                        </div>
                        <div class="info-value">{{ $potentialSupplier->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-edit me-2"></i>Cập nhật lần cuối
                        </div>
                        <div class="info-value">{{ $potentialSupplier->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- Thao tác -->
            <div class="card info-card">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0 fw-bold">
                        <i class="fas fa-cog me-2"></i>
                        Thao tác
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.potential-suppliers.edit', $potentialSupplier) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Chỉnh sửa thông tin
                        </a>

                        <form action="{{ route('admin.potential-suppliers.convert', $potentialSupplier) }}" method="POST"
                            onsubmit="return confirm('Bạn có chắc chắn muốn chuyển đổi nhà cung cấp này thành nhà cung cấp chính thức? Hành động này không thể hoàn tác.')">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-exchange-alt me-2"></i>Chuyển đổi thành NCC chính thức
                            </button>
                        </form>

                        <form action="{{ route('admin.potential-suppliers.destroy', $potentialSupplier) }}" method="POST"
                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhà cung cấp tiềm năng này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash-alt me-2"></i>Xóa nhà cung cấp
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin chi tiết và dịch vụ -->
        <div class="col-lg-8">
            <!-- Lý do tiềm năng và ghi chú -->
            @if($potentialSupplier->reason_potential || $potentialSupplier->notes)
            <div class="card info-card mb-4">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0 fw-bold">
                        <i class="fas fa-lightbulb me-2"></i>
                        Thông tin bổ sung
                    </h6>
                </div>
                <div class="card-body">
                    @if($potentialSupplier->reason_potential)
                    <div class="mb-3">
                        <div class="info-label">
                            <i class="fas fa-star me-2"></i>Lý do được coi là tiềm năng
                        </div>
                        <div class="info-value">{{ $potentialSupplier->reason_potential }}</div>
                    </div>
                    @endif

                    @if($potentialSupplier->notes)
                    <div class="mb-0">
                        <div class="info-label">
                            <i class="fas fa-sticky-note me-2"></i>Ghi chú
                        </div>
                        <div class="info-value">{{ $potentialSupplier->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Danh sách dịch vụ -->
            <div class="card info-card">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0 fw-bold">
                        <i class="fas fa-laptop me-2"></i>
                        Danh sách dịch vụ ({{ $potentialSupplier->services->count() }} dịch vụ)
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($potentialSupplier->services as $service)
                    <div class="service-card">
                        <div class="service-header">
                            <h6 class="service-name">
                                <i class="fas fa-laptop me-2"></i>{{ $service->service_name }}
                            </h6>
                            <div class="service-price">{{ $service->formatted_price }}</div>
                        </div>
                        
                        <div class="service-details">
                            @if($service->unit)
                            <div class="service-detail-item">
                                <div class="service-detail-label">Đơn vị</div>
                                <div class="service-detail-value">{{ $service->unit }}</div>
                            </div>
                            @endif

                            @if($service->warranty_days)
                            <div class="service-detail-item">
                                <div class="service-detail-label">Bảo hành</div>
                                <div class="service-detail-value">{{ $service->warranty_days }} ngày</div>
                            </div>
                            @endif

                            @if($service->description)
                            <div class="service-detail-item">
                                <div class="service-detail-label">Mô tả</div>
                                <div class="service-detail-value">{{ $service->description }}</div>
                            </div>
                            @endif

                            @if($service->notes)
                            <div class="service-detail-item">
                                <div class="service-detail-label">Ghi chú</div>
                                <div class="service-detail-value">{{ $service->notes }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Chưa có dịch vụ nào được thêm</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
