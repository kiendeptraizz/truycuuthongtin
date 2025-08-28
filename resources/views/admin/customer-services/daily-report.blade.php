@extends('layouts.admin')

@section('title', 'Báo cáo thống kê hàng ngày')
@section('page-title', 'Báo cáo thống kê hàng ngày')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Báo cáo thống kê dịch vụ hàng ngày</h5>
                        <small class="text-muted">Xem tổng quan dịch vụ kích hoạt, hết hạn trong ngày</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.customer-services.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Quay lại
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Date Filter -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <form method="GET" class="d-flex gap-2">
                            <input type="date" 
                                   name="date" 
                                   class="form-control" 
                                   value="{{ request('date', $selectedDate->format('Y-m-d')) }}">
                            <button type="submit" class="btn btn-primary">Xem</button>
                            <a href="{{ route('admin.customer-services.daily-report') }}" class="btn btn-outline-secondary">Hôm nay</a>
                        </form>
                    </div>
                    <div class="col-md-8 text-end">
                        <h4 class="text-primary">{{ $selectedDate->format('d/m/Y') }} - {{ $selectedDate->translatedFormat('l') }}</h4>
                    </div>
                </div>

                <!-- Thống kê tổng quan -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h2 class="mb-1">{{ $stats['activated']['total_services'] }}</h2>
                                <h6 class="mb-1">Dịch vụ kích hoạt</h6>
                                <small>{{ $stats['activated']['unique_customers'] }} khách hàng</small>
                                <hr class="my-2">
                                <strong>{{ formatPrice($stats['activated']['revenue_estimate']) }}</strong>
                                <br><small>Doanh thu ước tính</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h2 class="mb-1">{{ $stats['expired']['total_services'] }}</h2>
                                <h6 class="mb-1">Dịch vụ hết hạn</h6>
                                <small>{{ $stats['expired']['unique_customers'] }} khách hàng</small>
                                <hr class="my-2">
                                <small>Cần liên hệ gia hạn</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h2 class="mb-1">{{ $stats['expiring_soon']['total_services'] }}</h2>
                                <h6 class="mb-1">Sắp hết hạn (5 ngày)</h6>
                                <small>{{ $stats['expiring_soon']['not_reminded'] }} chưa nhắc</small>
                                <hr class="my-2">
                                <small>{{ $stats['expiring_soon']['reminded'] }} đã nhắc</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chi tiết dịch vụ kích hoạt -->
                @if($activatedToday->count() > 0)
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-play-circle me-2"></i>
                                        Dịch vụ kích hoạt hôm nay ({{ $activatedToday->count() }})
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Khách hàng</th>
                                                    <th>Dịch vụ</th>
                                                    <th>Giá</th>
                                                    <th>Hết hạn</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($activatedToday as $service)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $service->customer->name }}</strong>
                                                            <br><small class="text-muted">{{ $service->customer->customer_code }}</small>
                                                        </td>
                                                        <td>{{ $service->servicePackage->name }}</td>
                                                        <td>{{ formatPrice($service->servicePackage->price ?? 0) }}</td>
                                                        <td>{{ $service->expires_at ? $service->expires_at->format('d/m/Y') : 'Không giới hạn' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Top gói dịch vụ</h6>
                                </div>
                                <div class="card-body">
                                    @foreach($stats['activated']['by_package'] as $packageName => $data)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>{{ $packageName }}</span>
                                            <div class="text-end">
                                                <strong>{{ $data['count'] }}</strong>
                                                <br><small class="text-muted">{{ number_format($data['revenue']) }}₫</small>
                                            </div>
                                        </div>
                                        @if(!$loop->last)<hr class="my-1">@endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Chi tiết dịch vụ hết hạn -->
                @if($expiredToday->count() > 0)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-stop-circle me-2"></i>
                                        Dịch vụ hết hạn hôm nay ({{ $expiredToday->count() }})
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Khách hàng</th>
                                                    <th>Dịch vụ</th>
                                                    <th>Email liên hệ</th>
                                                    <th>Ngày kích hoạt</th>
                                                    <th>Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($expiredToday as $service)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $service->customer->name }}</strong>
                                                            <br><small class="text-muted">{{ $service->customer->customer_code }}</small>
                                                        </td>
                                                        <td>{{ $service->servicePackage->name }}</td>
                                                        <td>{{ $service->customer->email ?: 'Chưa có' }}</td>
                                                        <td>{{ $service->activated_at ? $service->activated_at->format('d/m/Y') : 'N/A' }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.customer-services.edit', $service) }}" 
                                                               class="btn btn-sm btn-warning"
                                                               title="Gia hạn">
                                                                <i class="fas fa-clock"></i>
                                                            </a>
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

                <!-- Dịch vụ sắp hết hạn trong 5 ngày -->
                @if($expiringSoon->count() > 0)
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-warning">
                                    <h6 class="mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Dịch vụ sắp hết hạn trong 5 ngày ({{ $expiringSoon->count() }})
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Khách hàng</th>
                                                    <th>Dịch vụ</th>
                                                    <th>Hết hạn</th>
                                                    <th>Còn lại</th>
                                                    <th>Nhắc nhở</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($expiringSoon as $service)
                                                    <tr class="{{ $service->getDaysRemaining() <= 1 ? 'table-danger' : ($service->getDaysRemaining() <= 2 ? 'table-warning' : '') }}">
                                                        <td>
                                                            <strong>{{ $service->customer->name }}</strong>
                                                            <br><small class="text-muted">{{ $service->customer->customer_code }}</small>
                                                        </td>
                                                        <td>{{ $service->servicePackage->name }}</td>
                                                        <td>{{ $service->expires_at->format('d/m/Y') }}</td>
                                                        <td>
                                                            <strong class="{{ $service->getDaysRemaining() <= 1 ? 'text-danger' : 'text-warning' }}">
                                                                {{ $service->getDaysRemaining() }} ngày
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            @if($service->reminder_sent)
                                                                <span class="badge bg-success">Đã nhắc</span>
                                                            @else
                                                                <span class="badge bg-danger">Chưa nhắc</span>
                                                            @endif
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

                <!-- Trường hợp không có dữ liệu -->
                @if($activatedToday->count() === 0 && $expiredToday->count() === 0 && $expiringSoon->count() === 0)
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Không có hoạt động nào trong ngày {{ $selectedDate->format('d/m/Y') }}</h5>
                        <p class="text-muted">Không có dịch vụ kích hoạt, hết hạn hoặc sắp hết hạn.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
