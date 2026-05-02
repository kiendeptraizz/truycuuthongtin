@extends('layouts.admin')

@section('title', 'Chi tiết dịch vụ khách hàng')
@section('page-title', 'Chi tiết dịch vụ khách hàng')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-3"
                            style="width: 50px; height: 50px; background: var(--primary-gradient); border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-eye fa-lg text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Chi tiết dịch vụ</h5>
                            <small class="text-muted">Thông tin chi tiết dịch vụ khách hàng</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.customer-services.edit', $customerService) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>
                            Chỉnh sửa
                        </a>
                        <a href="{{ route('admin.customer-services.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Quay lại
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <!-- Customer Information -->
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-user me-2"></i>
                                    Thông tin khách hàng
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <strong>Tên khách hàng:</strong>
                                        <div>{{ $customerService->customer->name }}</div>
                                    </div>
                                    <div class="col-12">
                                        <strong>Mã khách hàng:</strong>
                                        <div>{{ $customerService->customer->customer_code ?? 'Chưa có' }}</div>
                                    </div>
                                    <div class="col-12">
                                        <strong>Email:</strong>
                                        <div>{{ $customerService->customer->email ?? 'Chưa có' }}</div>
                                    </div>
                                    <div class="col-12">
                                        <strong>Số điện thoại:</strong>
                                        <div>{{ $customerService->customer->phone ?? 'Chưa có' }}</div>
                                    </div>
                                    <div class="col-12">
                                        <strong>Địa chỉ:</strong>
                                        <div>{{ $customerService->customer->address ?? 'Chưa có' }}</div>
                                    </div>
                                </div>
                                <div class="mt-3 d-flex gap-2 flex-wrap">
                                    <a href="{{ route('admin.customers.show', $customerService->customer) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-1"></i>
                                        Xem chi tiết khách hàng
                                    </a>
                                    <a href="{{ route('admin.customer-services.audit', $customerService) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-history me-1"></i>
                                        Lịch sử thay đổi
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Service Information -->
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-box me-2"></i>
                                    Thông tin dịch vụ
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <strong>Tên gói dịch vụ:</strong>
                                        <div>{{ $customerService->servicePackage->name }}</div>
                                    </div>
                                    <div class="col-12">
                                        <strong>Danh mục:</strong>
                                        <div>{{ $customerService->servicePackage->category->name ?? 'Chưa phân loại' }}</div>
                                    </div>
                                    <div class="col-12">
                                        <strong>Giá gói:</strong>
                                        <div class="text-primary fw-bold">
                                            {{ number_format($customerService->servicePackage->price, 0, ',', '.') }} VNĐ
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <strong>Mô tả:</strong>
                                        <div>{{ $customerService->servicePackage->description ?? 'Không có mô tả' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Thông tin đơn hàng (đồng bộ với bot Telegram) --}}
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border border-success">
                            <div class="card-header bg-success bg-opacity-10">
                                <h6 class="mb-0 text-success">
                                    <i class="fas fa-receipt me-2"></i>
                                    Thông tin đơn hàng
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12 mb-2 pb-2 border-bottom">
                                        <strong>📋 Mã đơn hàng:</strong>
                                        @if($customerService->order_code)
                                            <code class="bg-success bg-opacity-10 text-success px-2 py-1 rounded fs-6 ms-2">{{ $customerService->order_code }}</code>
                                            <button class="btn btn-sm btn-outline-secondary ms-2" onclick="navigator.clipboard.writeText('{{ $customerService->order_code }}'); this.innerHTML='<i class=\'fas fa-check\'></i> Đã copy'" title="Copy mã đơn">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        @else
                                            <span class="text-muted ms-2">— (đơn cũ chưa có mã)</span>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <strong>💰 Số tiền đơn:</strong>
                                        <div class="text-success fw-bold">
                                            @if(!is_null($customerService->order_amount))
                                                {{ number_format($customerService->order_amount, 0, ',', '.') }} VNĐ
                                            @elseif($customerService->pending_order_id)
                                                {{ optional(\App\Models\PendingOrder::find($customerService->pending_order_id))->amount ? number_format(\App\Models\PendingOrder::find($customerService->pending_order_id)->amount, 0, ',', '.') . ' VNĐ' : '—' }}
                                                <span class="badge bg-info ms-1">từ pending order</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>🛡 Bảo hành:</strong>
                                        <div>
                                            @if($customerService->warranty_days > 0)
                                                @if($customerService->warranty_days == $customerService->duration_days)
                                                    <span class="badge bg-warning text-dark">full thời hạn ({{ $customerService->warranty_days }} ngày)</span>
                                                @else
                                                    {{ $customerService->warranty_days }} ngày
                                                @endif
                                            @else
                                                <span class="text-muted">Không bảo hành</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>👥 Mã nhóm-gia đình:</strong>
                                        <div>
                                            @if($customerService->family_code)
                                                <code class="bg-light px-2 py-1 rounded">{{ $customerService->family_code }}</code>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($customerService->pending_order_id)
                                        <div class="col-12">
                                            <strong>📋 Đơn pending gốc:</strong>
                                            @php $po = \App\Models\PendingOrder::find($customerService->pending_order_id); @endphp
                                            @if($po)
                                                <div>
                                                    <code>{{ $po->order_code }}</code>
                                                    @if($po->paid_at)
                                                        <span class="badge bg-success ms-1">Đã thanh toán {{ $po->paid_at->format('H:i d/m/Y') }}</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark ms-1">Chưa thanh toán</span>
                                                    @endif
                                                    @if($po->created_via === 'telegram')
                                                        <span class="badge bg-info ms-1"><i class="fab fa-telegram me-1"></i>Tạo qua bot</span>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="text-muted">Đơn không tìm thấy (đã xoá)</div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Family Account Information -->
                @if($customerService->family_account_id && $customerService->familyAccount)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-home me-2"></i>
                                    Thông tin Family Account
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <strong>Tên Family:</strong>
                                        <div>{{ $customerService->familyAccount->family_name }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Mã Family:</strong>
                                        <div><code class="bg-light px-2 py-1 rounded">{{ $customerService->familyAccount->family_code }}</code></div>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Email chủ Family:</strong>
                                        <div>{{ $customerService->familyAccount->owner_email }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Slots sử dụng:</strong>
                                        <div>
                                            <span class="badge {{ $customerService->familyAccount->current_members >= $customerService->familyAccount->max_members ? 'bg-danger' : 'bg-success' }}">
                                                {{ $customerService->familyAccount->current_members }}/{{ $customerService->familyAccount->max_members }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Trạng thái Family:</strong>
                                        <div>
                                            @php
                                            $famStatusColors = ['active' => 'success', 'expired' => 'warning', 'suspended' => 'danger'];
                                            $famStatusLabels = ['active' => 'Hoạt động', 'expired' => 'Hết hạn', 'suspended' => 'Tạm ngưng'];
                                            @endphp
                                            <span class="badge bg-{{ $famStatusColors[$customerService->familyAccount->status] ?? 'secondary' }}">
                                                {{ $famStatusLabels[$customerService->familyAccount->status] ?? $customerService->familyAccount->status }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Hết hạn Family:</strong>
                                        <div>
                                            @if($customerService->familyAccount->expires_at)
                                            {{ $customerService->familyAccount->expires_at->format('d/m/Y') }}
                                            @else
                                            <span class="text-muted">Chưa thiết lập</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('admin.family-accounts.show', $customerService->familyAccount) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-1"></i>
                                        Xem chi tiết Family Account
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif(str_contains(strtolower($customerService->servicePackage->account_type ?? ''), 'family'))
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Chú ý:</strong> Dịch vụ này thuộc loại "add family" nhưng chưa được gán vào Family Account nào.
                        </div>
                    </div>
                </div>
                @endif

                <!-- Service Details -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    Chi tiết dịch vụ
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <strong>Ngày kích hoạt:</strong>
                                        <div>
                                            @if($customerService->activated_at)
                                            {{ $customerService->activated_at->format('d/m/Y') }}
                                            <small class="text-muted d-block">
                                                {{ $customerService->activated_at->diffForHumans() }}
                                            </small>
                                            @else
                                            <span class="text-muted">Chưa có</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Ngày hết hạn:</strong>
                                        <div>
                                            @if($customerService->expires_at)
                                            {{ $customerService->expires_at->format('d/m/Y') }}
                                            <small class="text-muted d-block">
                                                {{ $customerService->expires_at->diffForHumans() }}
                                            </small>
                                            @else
                                            <span class="text-muted">Không có hạn</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Trạng thái:</strong>
                                        <div>
                                            @php
                                            $statusClass = match($customerService->status) {
                                            'active' => 'success',
                                            'expired' => 'danger',
                                            'suspended' => 'warning',
                                            'cancelled' => 'secondary',
                                            'pending' => 'info',
                                            default => 'secondary'
                                            };

                                            $statusText = match($customerService->status) {
                                            'active' => 'Đang hoạt động',
                                            'expired' => 'Đã hết hạn',
                                            'suspended' => 'Tạm ngưng',
                                            'cancelled' => 'Đã hủy',
                                            'pending' => 'Chờ kích hoạt',
                                            default => ucfirst($customerService->status)
                                            };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ $statusText }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Thời gian còn lại:</strong>
                                        <div>
                                            @if($customerService->expires_at && $customerService->status === 'active')
                                            @php
                                            $daysLeft = now()->diffInDays($customerService->expires_at, false);
                                            @endphp
                                            @if($daysLeft > 0)
                                            <span class="text-success fw-bold">{{ $daysLeft }} ngày</span>
                                            @elseif($daysLeft === 0)
                                            <span class="text-warning fw-bold">Hết hạn hôm nay</span>
                                            @else
                                            <span class="text-danger fw-bold">Đã quá hạn {{ abs($daysLeft) }} ngày</span>
                                            @endif
                                            @else
                                            <span class="text-muted">Không áp dụng</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <strong>Ghi chú nội bộ:</strong>
                                        <div>{{ $customerService->internal_notes ?? 'Không có ghi chú' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Ngày tạo:</strong>
                                        <div>
                                            {{ $customerService->created_at->format('d/m/Y H:i:s') }}
                                            <small class="text-muted d-block">
                                                {{ $customerService->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Cập nhật lần cuối:</strong>
                                        <div>
                                            {{ $customerService->updated_at->format('d/m/Y H:i:s') }}
                                            <small class="text-muted d-block">
                                                {{ $customerService->updated_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($customerService->supplier)
                <!-- Supplier Information -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">
                                    <i class="fas fa-truck me-2"></i>
                                    Thông tin nhà cung cấp
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <strong>Mã nhà cung cấp:</strong>
                                        <div>{{ $customerService->supplier->supplier_code }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Tên nhà cung cấp:</strong>
                                        <div>{{ $customerService->supplier->supplier_name }}</div>
                                    </div>
                                    @if($customerService->supplierService)
                                    <div class="col-md-6">
                                        <strong>Dịch vụ cung cấp:</strong>
                                        <div>{{ $customerService->supplierService->product_name }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Giá dịch vụ:</strong>
                                        <div class="text-warning fw-bold">
                                            {{ number_format($customerService->supplierService->price, 0, ',', '.') }} VNĐ
                                        </div>
                                    </div>
                                    @if($customerService->supplierService->warranty_days > 0)
                                    <div class="col-md-12">
                                        <strong>Thời gian bảo hành:</strong>
                                        <div>{{ $customerService->supplierService->warranty_days }} ngày</div>
                                    </div>
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex gap-2 justify-content-center">
                            @if($customerService->status === 'active')
                            <form method="POST" action="{{ route('admin.customer-services.update', $customerService) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="suspended">
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Bạn có chắc muốn tạm ngưng dịch vụ này?')">
                                    <i class="fas fa-pause me-1"></i>
                                    Tạm ngưng
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.customer-services.update', $customerService) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="expired">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn kết thúc dịch vụ này?')">
                                    <i class="fas fa-stop me-1"></i>
                                    Kết thúc
                                </button>
                            </form>
                            @elseif($customerService->status === 'suspended')
                            <form method="POST" action="{{ route('admin.customer-services.update', $customerService) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="active">
                                <button type="submit" class="btn btn-success" onclick="return confirm('Bạn có chắc muốn kích hoạt lại dịch vụ này?')">
                                    <i class="fas fa-play me-1"></i>
                                    Kích hoạt lại
                                </button>
                            </form>
                            @elseif($customerService->status === 'pending')
                            <form method="POST" action="{{ route('admin.customer-services.update', $customerService) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="active">
                                <button type="submit" class="btn btn-success" onclick="return confirm('Bạn có chắc muốn kích hoạt dịch vụ này?')">
                                    <i class="fas fa-check me-1"></i>
                                    Kích hoạt
                                </button>
                            </form>
                            @endif

                            <a href="{{ route('admin.customer-services.edit', $customerService) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-1"></i>
                                Chỉnh sửa
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection