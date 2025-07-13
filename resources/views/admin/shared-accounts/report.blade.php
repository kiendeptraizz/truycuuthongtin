@extends('layouts.admin')

@section('title', 'Báo cáo tài khoản dùng chung')
@section('page-title', 'Báo cáo tài khoản dùng chung')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Báo cáo tài khoản dùng chung</h5>
                        <small class="text-muted">Thống kê chi tiết về các tài khoản được sử dụng bởi nhiều khách hàng</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button onclick="window.print()" class="btn btn-secondary">
                            <i class="fas fa-print me-1"></i>
                            In báo cáo
                        </button>
                        <a href="{{ route('admin.shared-accounts.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Quay lại
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Thống kê tổng quan -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-1">{{ $overallStats['total_services_with_email'] }}</h3>
                                <small>Tổng dịch vụ có email</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-1">{{ $overallStats['unique_emails'] }}</h3>
                                <small>Email duy nhất</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-1">{{ $overallStats['shared_emails'] }}</h3>
                                <small>Email dùng chung</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h3 class="mb-1">{{ $overallStats['multi_customer_emails'] }}</h3>
                                <small>Email nhiều khách hàng</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top 10 tài khoản dùng chung nhiều nhất -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-trophy me-2"></i>
                            Top 10 tài khoản dùng chung nhiều nhất
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($topSharedAccounts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Xếp hạng</th>
                                        <th>Email</th>
                                        <th>Tổng dịch vụ</th>
                                        <th>Khách hàng khác nhau</th>
                                        <th>Đang hoạt động</th>
                                        <th>Đã hết hạn</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topSharedAccounts as $index => $account)
                                    <tr>
                                        <td>
                                            @if($index === 0)
                                                <span class="badge bg-warning">🥇 #1</span>
                                            @elseif($index === 1)
                                                <span class="badge bg-secondary">🥈 #2</span>
                                            @elseif($index === 2)
                                                <span class="badge bg-dark">🥉 #3</span>
                                            @else
                                                <span class="badge bg-light text-dark">#{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <code>{{ $account->login_email }}</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $account->total_services }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $account->unique_customers }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $account->active_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">{{ $account->expired_count }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.shared-accounts.show', urlencode($account->login_email)) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>
                                                Chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-3">
                            <i class="fas fa-chart-line fa-2x text-muted mb-2"></i>
                            <p class="text-muted">Chưa có dữ liệu tài khoản dùng chung</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Tài khoản có vấn đề (nhiều khách hàng) -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                            Tài khoản có vấn đề (Nhiều khách hàng sử dụng)
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($problematicAccounts->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Lưu ý:</strong> Các tài khoản dưới đây được sử dụng bởi nhiều khách hàng khác nhau. 
                            Điều này có thể gây xung đột và cần được xử lý.
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Email</th>
                                        <th>Tổng dịch vụ</th>
                                        <th>Số khách hàng</th>
                                        <th>Tên khách hàng</th>
                                        <th>Mức độ rủi ro</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($problematicAccounts as $account)
                                    <tr>
                                        <td>
                                            <code>{{ $account->login_email }}</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $account->total_services }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ $account->unique_customers }}</span>
                                        </td>
                                        <td>
                                            <div style="max-width: 300px;">
                                                <small class="text-muted">{{ $account->customer_names }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($account->unique_customers >= 5)
                                                <span class="badge bg-danger">Rất cao</span>
                                            @elseif($account->unique_customers >= 3)
                                                <span class="badge bg-warning">Cao</span>
                                            @else
                                                <span class="badge bg-info">Trung bình</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.shared-accounts.show', urlencode($account->login_email)) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-warning" 
                                                        onclick="alert('Liên hệ khách hàng để tách riêng tài khoản cho: {{ $account->customer_names }}')">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Khuyến nghị -->
                        <div class="mt-3">
                            <h6>Khuyến nghị xử lý:</h6>
                            <ul class="mb-0">
                                <li>Liên hệ với khách hàng để tạo tài khoản riêng biệt</li>
                                <li>Cảnh báo khách hàng về rủi ro xung đột tài khoản</li>
                                <li>Ưu tiên xử lý các tài khoản có mức độ rủi ro cao</li>
                                <li>Theo dõi thường xuyên để phát hiện sớm vấn đề</li>
                            </ul>
                        </div>
                        @else
                        <div class="text-center py-3">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="text-success">Tuyệt vời! Không có tài khoản nào có vấn đề về nhiều khách hàng sử dụng.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Biểu đồ thống kê -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Phân bố dịch vụ theo email</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="emailDistributionChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Tỷ lệ tài khoản có vấn đề</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="riskLevelChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Thông tin tạo báo cáo -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center">
                <small class="text-muted">
                    Báo cáo được tạo vào {{ now()->format('d/m/Y H:i:s') }} | 
                    Dữ liệu cập nhật theo thời gian thực
                </small>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Biểu đồ phân bố email
const emailCtx = document.getElementById('emailDistributionChart').getContext('2d');
new Chart(emailCtx, {
    type: 'doughnut',
    data: {
        labels: ['Email riêng', 'Email dùng chung', 'Email nhiều khách hàng'],
        datasets: [{
            data: [
                {{ $overallStats['unique_emails'] - $overallStats['shared_emails'] }},
                {{ $overallStats['shared_emails'] - $overallStats['multi_customer_emails'] }},
                {{ $overallStats['multi_customer_emails'] }}
            ],
            backgroundColor: ['#28a745', '#ffc107', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Biểu đồ mức độ rủi ro
const riskCtx = document.getElementById('riskLevelChart').getContext('2d');
new Chart(riskCtx, {
    type: 'pie',
    data: {
        labels: ['An toàn', 'Có rủi ro'],
        datasets: [{
            data: [
                {{ $overallStats['unique_emails'] - $overallStats['multi_customer_emails'] }},
                {{ $overallStats['multi_customer_emails'] }}
            ],
            backgroundColor: ['#28a745', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

<style>
@media print {
    .btn, .card-header .d-flex .gap-2 {
        display: none !important;
    }
}
</style>
@endsection
