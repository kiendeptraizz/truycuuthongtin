@extends('layouts.admin')

@section('title', 'Quản Lý Backup')
@section('page-title', 'Quản Lý Backup')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">🛡️ Quản Lý Backup</h1>
            <p class="mb-0 text-muted">Giám sát và quản lý hệ thống backup tự động</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" id="createBackupBtn">
                <i class="fas fa-plus"></i> Tạo Backup Ngay
            </button>
            <a href="{{ route('admin.backup.list') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list"></i> Danh Sách Backup
            </a>
        </div>
    </div>

    <!-- Health Score Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-{{ $healthScore['status'] === 'excellent' ? 'success' : ($healthScore['status'] === 'good' ? 'info' : ($healthScore['status'] === 'warning' ? 'warning' : 'danger')) }}">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ $healthScore['status'] === 'excellent' ? 'success' : ($healthScore['status'] === 'good' ? 'info' : ($healthScore['status'] === 'warning' ? 'warning' : 'danger')) }} text-uppercase mb-1">
                                Sức Khỏe Hệ Thống Backup
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $healthScore['score'] }}/100
                                @if($healthScore['status'] === 'excellent')
                                    <span class="badge badge-success">Tuyệt Vời</span>
                                @elseif($healthScore['status'] === 'good')
                                    <span class="badge badge-info">Tốt</span>
                                @elseif($healthScore['status'] === 'warning')
                                    <span class="badge badge-warning">Cần Chú Ý</span>
                                @else
                                    <span class="badge badge-danger">Nghiêm Trọng</span>
                                @endif
                            </div>
                            @if(!empty($healthScore['issues']))
                                <div class="mt-2">
                                    @foreach($healthScore['issues'] as $issue)
                                        <small class="text-danger d-block">⚠️ {{ $issue }}</small>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-heartbeat fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Total Backups -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng Số Backup
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $backupStats['total_backups'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Size -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tổng Dung Lượng
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $backupStats['total_size_formatted'] ?? '0 B' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hdd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest Backup -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Backup Mới Nhất
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                @if($backupStats['latest_backup'])
                                    {{ $backupStats['hours_ago'] }} giờ trước
                                @else
                                    Chưa có backup
                                @endif
                            </div>
                            @if($backupStats['latest_time_formatted'])
                                <small class="text-muted">{{ $backupStats['latest_time_formatted'] }}</small>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backup Types -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Phân Loại
                            </div>
                            <div class="small text-gray-800">
                                <div>Tự động: {{ $backupStats['daily_count'] }}</div>
                                <div>Thủ công: {{ $backupStats['manual_count'] }}</div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Backups -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">📋 Backup Gần Đây</h6>
                    <a href="{{ route('admin.backup.list') }}" class="btn btn-sm btn-outline-primary">
                        Xem Tất Cả
                    </a>
                </div>
                <div class="card-body">
                    @if(empty($recentBackups))
                        <div class="text-center py-4">
                            <i class="fas fa-database fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Chưa có backup nào được tạo</p>
                            <button type="button" class="btn btn-primary" id="createFirstBackupBtn">
                                <i class="fas fa-plus"></i> Tạo Backup Đầu Tiên
                            </button>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Tên File</th>
                                        <th>Loại</th>
                                        <th>Kích Thước</th>
                                        <th>Thời Gian Tạo</th>
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBackups as $backup)
                                        <tr>
                                            <td>
                                                <i class="fas fa-database text-muted mr-1"></i>
                                                {{ $backup['filename'] }}
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $backup['type'] === 'Tự động' ? 'primary' : 'warning' }}">
                                                    {{ $backup['type'] }}
                                                </span>
                                            </td>
                                            <td>{{ $backup['size_formatted'] }}</td>
                                            <td>{{ $backup['created_at_formatted'] }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('admin.backup.download', $backup['filename']) }}" 
                                                       class="btn btn-outline-primary btn-sm" title="Tải xuống">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-success btn-sm restore-btn" 
                                                            data-filename="{{ $backup['filename'] }}" title="Khôi phục">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger btn-sm delete-btn" 
                                                            data-filename="{{ $backup['filename'] }}" title="Xóa">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">⚡ Hành Động Nhanh</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.backup.report') }}" class="btn btn-outline-info btn-block">
                            <i class="fas fa-chart-line"></i> Xem Báo Cáo
                        </a>
                        <a href="{{ route('admin.backup.history') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-history"></i> Lịch Sử Backup
                        </a>
                        <a href="{{ route('admin.backup.settings') }}" class="btn btn-outline-warning btn-block">
                            <i class="fas fa-cog"></i> Cài Đặt
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📊 Trạng Thái Hệ Thống</h6>
                </div>
                <div class="card-body">
                    <div id="systemStatus">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Đang tải...</span>
                            </div>
                            <p class="mt-2 text-muted">Đang kiểm tra trạng thái hệ thống...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto refresh status every 30 seconds
    setInterval(refreshSystemStatus, 30000);
    refreshSystemStatus();
    
    // Create backup button handlers
    $('#createBackupBtn, #createFirstBackupBtn').click(function() {
        createBackup();
    });
    
    // Delete backup handlers
    $('.delete-btn').click(function() {
        const filename = $(this).data('filename');
        if (confirm('Bạn có chắc chắn muốn xóa backup này?')) {
            deleteBackup(filename);
        }
    });
    
    // Restore backup handlers
    $('.restore-btn').click(function() {
        const filename = $(this).data('filename');
        if (confirm('⚠️ CẢNH BÁO: Thao tác này sẽ ghi đè toàn bộ dữ liệu hiện tại. Bạn có chắc chắn?')) {
            restoreBackup(filename);
        }
    });
});

function refreshSystemStatus() {
    $.get('{{ route("admin.backup.status") }}')
        .done(function(data) {
            updateSystemStatus(data);
        })
        .fail(function() {
            $('#systemStatus').html('<div class="alert alert-danger">Không thể tải trạng thái hệ thống</div>');
        });
}

function updateSystemStatus(data) {
    const html = `
        <div class="row">
            <div class="col-md-6">
                <h6>📊 Thống Kê:</h6>
                <ul class="list-unstyled">
                    <li>Tổng backup: <strong>${data.stats.total_backups}</strong></li>
                    <li>Dung lượng: <strong>${data.stats.total_size_formatted || '0 B'}</strong></li>
                    <li>Backup mới nhất: <strong>${data.stats.hours_ago || 'N/A'} giờ trước</strong></li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6>💚 Sức Khỏe:</h6>
                <div class="progress mb-2">
                    <div class="progress-bar bg-${getHealthColor(data.health.status)}" 
                         style="width: ${data.health.score}%">${data.health.score}/100</div>
                </div>
                <small class="text-muted">Cập nhật: ${new Date(data.last_updated).toLocaleString('vi-VN')}</small>
            </div>
        </div>
    `;
    $('#systemStatus').html(html);
}

function getHealthColor(status) {
    const colors = {
        'excellent': 'success',
        'good': 'info', 
        'warning': 'warning',
        'critical': 'danger'
    };
    return colors[status] || 'secondary';
}

function createBackup() {
    const btn = $('#createBackupBtn');
    const originalHtml = btn.html();
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Đang tạo...');

    $.post('{{ route("admin.backup.create") }}', { _token: '{{ csrf_token() }}' })
        .done(function(response) {
            if (response.success) {
                showAlert('success', 'Backup được tạo thành công: ' + response.backup_name);
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('danger', 'Lỗi: ' + response.message);
            }
        })
        .fail(function(xhr) {
            const response = xhr.responseJSON;
            showAlert('danger', 'Lỗi khi tạo backup: ' + (response?.message || 'Lỗi không xác định'));
        })
        .always(function() {
            btn.prop('disabled', false).html(originalHtml);
        });
}

function deleteBackup(filename) {
    $.ajax({
        url: '{{ route("admin.backup.delete", ":filename") }}'.replace(':filename', filename),
        method: 'DELETE',
        data: { _token: '{{ csrf_token() }}' }
    })
    .done(function(response) {
        if (response.success) {
            showAlert('success', response.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('danger', response.message);
        }
    })
    .fail(function(xhr) {
        const response = xhr.responseJSON;
        showAlert('danger', 'Lỗi khi xóa backup: ' + (response?.message || 'Unknown error'));
    });
}

function restoreBackup(filename) {
    $.post('{{ route("admin.backup.restore") }}', {
        filename: filename,
        _token: '{{ csrf_token() }}'
    })
    .done(function(response) {
        if (response.success) {
            showAlert('success', response.message);
            setTimeout(() => location.reload(), 2000);
        } else {
            showAlert('danger', response.message);
        }
    })
    .fail(function(xhr) {
        const response = xhr.responseJSON;
        showAlert('danger', 'Lỗi khi khôi phục: ' + (response?.message || 'Unknown error'));
    });
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;
    
    // Remove existing alerts
    $('.alert').remove();
    
    // Add new alert at top of container
    $('.container-fluid').prepend(alertHtml);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
@endsection
