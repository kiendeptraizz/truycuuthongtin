@extends('layouts.admin')

@section('title', 'Danh Sách Backup')
@section('page-title', 'Danh Sách Backup')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">📋 Danh Sách Backup</h1>
            <p class="mb-0 text-muted">Quản lý tất cả file backup trong hệ thống</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" id="createBackupBtn">
                <i class="fas fa-plus"></i> Tạo Backup Mới
            </button>
            <a href="{{ route('admin.backup.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Quay Lại Dashboard
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">🔍 Bộ Lọc</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="filterType">Loại Backup:</label>
                    <select class="form-control" id="filterType">
                        <option value="">Tất cả</option>
                        <option value="daily">Hàng Ngày</option>
                        <option value="weekly">Hàng Tuần</option>
                        <option value="quick">Nhanh</option>
                        <option value="manual">Thủ Công</option>
                        <option value="auto">Tự Động</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterFormat">Định Dạng:</label>
                    <select class="form-control" id="filterFormat">
                        <option value="">Tất cả</option>
                        <option value="json">JSON</option>
                        <option value="zip">ZIP</option>
                        <option value="sql">SQL</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterStatus">Trạng Thái:</label>
                    <select class="form-control" id="filterStatus">
                        <option value="">Tất cả</option>
                        <option value="success">Thành Công</option>
                        <option value="error">Lỗi</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <div>
                        <button type="button" class="btn btn-info btn-block" id="applyFilters">
                            <i class="fas fa-filter"></i> Áp Dụng Bộ Lọc
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">📁 Danh Sách File Backup</h6>
            <div>
                <span class="badge badge-info">{{ count($backups) }} file</span>
                <button type="button" class="btn btn-sm btn-outline-danger ml-2" id="bulkDeleteBtn" style="display: none;">
                    <i class="fas fa-trash"></i> Xóa Đã Chọn
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(empty($backups))
                <div class="text-center py-5">
                    <i class="fas fa-database fa-4x text-gray-300 mb-3"></i>
                    <h5 class="text-muted">Chưa có backup nào</h5>
                    <p class="text-muted">Hãy tạo backup đầu tiên để bảo vệ dữ liệu của bạn</p>
                    <button type="button" class="btn btn-primary" id="createFirstBackupBtn">
                        <i class="fas fa-plus"></i> Tạo Backup Đầu Tiên
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="backupTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>Tên File</th>
                                <th>Loại</th>
                                <th>Định Dạng</th>
                                <th>Kích Thước</th>
                                <th>Thời Gian Tạo</th>
                                <th>Trạng Thái</th>
                                <th width="150">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($backups as $backup)
                                <tr data-type="{{ $backup['type'] }}" 
                                    data-format="{{ $backup['extension'] }}" 
                                    data-status="{{ $backup['status'] }}">
                                    <td>
                                        <input type="checkbox" class="backup-checkbox" value="{{ $backup['filename'] }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-{{ $backup['extension'] === 'json' ? 'code' : ($backup['extension'] === 'zip' ? 'archive' : 'database') }} text-muted mr-2"></i>
                                            <div>
                                                <div class="font-weight-bold">{{ $backup['filename'] }}</div>
                                                <small class="text-muted">{{ $backup['created_at_human'] }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $typeColors = [
                                                'daily' => 'primary',
                                                'weekly' => 'success', 
                                                'quick' => 'info',
                                                'auto' => 'secondary',
                                                'manual' => 'warning'
                                            ];
                                            $typeLabels = [
                                                'daily' => 'Hàng Ngày',
                                                'weekly' => 'Hàng Tuần',
                                                'quick' => 'Nhanh',
                                                'auto' => 'Tự Động',
                                                'manual' => 'Thủ Công'
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $typeColors[$backup['type']] ?? 'secondary' }}">
                                            {{ $typeLabels[$backup['type']] ?? ucfirst($backup['type']) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-outline-{{ $backup['extension'] === 'json' ? 'info' : ($backup['extension'] === 'zip' ? 'warning' : 'success') }}">
                                            {{ strtoupper($backup['extension']) }}
                                        </span>
                                    </td>
                                    <td>{{ $backup['size_formatted'] }}</td>
                                    <td>
                                        <div>{{ $backup['created_at_formatted'] }}</div>
                                        <small class="text-muted">{{ $backup['created_at_human'] }}</small>
                                    </td>
                                    <td>
                                        @if($backup['status'] === 'success')
                                            <span class="badge badge-success">
                                                <i class="fas fa-check"></i> Thành Công
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                <i class="fas fa-exclamation-triangle"></i> Lỗi
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.backup.download', $backup['filename']) }}" 
                                               class="btn btn-outline-primary btn-sm" 
                                               title="Tải xuống"
                                               data-toggle="tooltip">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-success btn-sm restore-btn" 
                                                    data-filename="{{ $backup['filename'] }}" 
                                                    title="Khôi phục"
                                                    data-toggle="tooltip">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-outline-info btn-sm info-btn" 
                                                    data-filename="{{ $backup['filename'] }}" 
                                                    title="Thông tin chi tiết"
                                                    data-toggle="tooltip">
                                                <i class="fas fa-info"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm delete-btn" 
                                                    data-filename="{{ $backup['filename'] }}" 
                                                    title="Xóa"
                                                    data-toggle="tooltip">
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

    <!-- Summary Stats -->
    <div class="row">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng File
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($backups) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tổng Dung Lượng
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ collect($backups)->sum('size') > 0 ? number_format(collect($backups)->sum('size') / 1024 / 1024, 1) . ' MB' : '0 MB' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hdd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Backup Thành Công
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ collect($backups)->where('status', 'success')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Backup Lỗi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ collect($backups)->where('status', 'error')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Backup Modal -->
<div class="modal fade" id="createBackupModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🔄 Tạo Backup Mới</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createBackupForm">
                    <div class="form-group">
                        <label for="backupType">Loại Backup:</label>
                        <select class="form-control" id="backupType" name="type">
                            <option value="manual">Thủ Công</option>
                            <option value="daily">Hàng Ngày</option>
                            <option value="weekly">Hàng Tuần</option>
                            <option value="quick">Nhanh</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="backupFormat">Định Dạng:</label>
                        <select class="form-control" id="backupFormat" name="format">
                            <option value="json">JSON</option>
                            <option value="sql">SQL</option>
                            <option value="both">Cả Hai</option>
                        </select>
                    </div>
                </form>
                <div id="backupProgress" style="display: none;">
                    <div class="progress mb-3">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: 100%"></div>
                    </div>
                    <p class="text-center text-muted">Đang tạo backup...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmCreateBackup">
                    <i class="fas fa-play"></i> Tạo Backup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Backup Info Modal -->
<div class="modal fade" id="backupInfoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📊 Thông Tin Chi Tiết Backup</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="backupInfoContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Đang tải...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Create backup button handlers
    $('#createBackupBtn, #createFirstBackupBtn').click(function() {
        $('#createBackupModal').modal('show');
    });
    
    // Confirm create backup
    $('#confirmCreateBackup').click(function() {
        createBackup();
    });
    
    // Select all checkbox
    $('#selectAll').change(function() {
        $('.backup-checkbox').prop('checked', this.checked);
        toggleBulkDeleteButton();
    });
    
    // Individual checkboxes
    $('.backup-checkbox').change(function() {
        toggleBulkDeleteButton();
        
        // Update select all checkbox
        const totalCheckboxes = $('.backup-checkbox').length;
        const checkedCheckboxes = $('.backup-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
    });
    
    // Apply filters
    $('#applyFilters').click(function() {
        applyFilters();
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
    
    // Info backup handlers
    $('.info-btn').click(function() {
        const filename = $(this).data('filename');
        showBackupInfo(filename);
    });
    
    // Bulk delete
    $('#bulkDeleteBtn').click(function() {
        const selectedFiles = $('.backup-checkbox:checked').map(function() {
            return this.value;
        }).get();
        
        if (selectedFiles.length === 0) {
            alert('Vui lòng chọn ít nhất một file để xóa');
            return;
        }
        
        if (confirm(`Bạn có chắc chắn muốn xóa ${selectedFiles.length} file backup đã chọn?`)) {
            bulkDeleteBackups(selectedFiles);
        }
    });
});

function toggleBulkDeleteButton() {
    const checkedCount = $('.backup-checkbox:checked').length;
    if (checkedCount > 0) {
        $('#bulkDeleteBtn').show();
    } else {
        $('#bulkDeleteBtn').hide();
    }
}

function applyFilters() {
    const typeFilter = $('#filterType').val();
    const formatFilter = $('#filterFormat').val();
    const statusFilter = $('#filterStatus').val();
    
    $('#backupTable tbody tr').each(function() {
        const row = $(this);
        const type = row.data('type');
        const format = row.data('format');
        const status = row.data('status');
        
        let show = true;
        
        if (typeFilter && type !== typeFilter) show = false;
        if (formatFilter && format !== formatFilter) show = false;
        if (statusFilter && status !== statusFilter) show = false;
        
        if (show) {
            row.show();
        } else {
            row.hide();
        }
    });
}

function createBackup() {
    const formData = {
        type: $('#backupType').val(),
        format: $('#backupFormat').val(),
        _token: '{{ csrf_token() }}'
    };
    
    $('#confirmCreateBackup').prop('disabled', true);
    $('#backupProgress').show();
    
    $.post('{{ route("admin.backup.create") }}', formData)
        .done(function(response) {
            if (response.success) {
                showAlert('success', 'Backup được tạo thành công: ' + response.backup_name);
                $('#createBackupModal').modal('hide');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('danger', 'Lỗi: ' + response.message);
            }
        })
        .fail(function(xhr) {
            const response = xhr.responseJSON;
            showAlert('danger', 'Lỗi khi tạo backup: ' + (response?.message || 'Unknown error'));
        })
        .always(function() {
            $('#confirmCreateBackup').prop('disabled', false);
            $('#backupProgress').hide();
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

function showBackupInfo(filename) {
    $('#backupInfoModal').modal('show');
    $('#backupInfoContent').html(`
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Đang tải...</span>
            </div>
            <p class="mt-2">Đang tải thông tin backup...</p>
        </div>
    `);
    
    // TODO: Implement backup info loading
    setTimeout(() => {
        $('#backupInfoContent').html(`
            <h6>📁 ${filename}</h6>
            <div class="row">
                <div class="col-md-6">
                    <strong>Thông tin file:</strong>
                    <ul class="list-unstyled mt-2">
                        <li>Kích thước: <span class="text-info">2.5 MB</span></li>
                        <li>Định dạng: <span class="badge badge-info">JSON</span></li>
                        <li>Trạng thái: <span class="badge badge-success">Thành công</span></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <strong>Nội dung backup:</strong>
                    <ul class="list-unstyled mt-2">
                        <li>Khách hàng: <span class="text-primary">118 records</span></li>
                        <li>Dịch vụ: <span class="text-primary">142 records</span></li>
                        <li>Gói dịch vụ: <span class="text-primary">8 records</span></li>
                    </ul>
                </div>
            </div>
        `);
    }, 1000);
}

function bulkDeleteBackups(filenames) {
    // TODO: Implement bulk delete
    showAlert('info', 'Tính năng xóa hàng loạt đang được phát triển');
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
