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
                                <th>Kích Thước</th>
                                <th>Thời Gian Tạo</th>
                                <th>Trạng Thái</th>
                                <th width="150">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($backups as $backup)
                                <tr data-status="{{ $backup['status'] }}">
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
                                        <span class="badge badge-{{ $backup['type'] === 'Tự động' ? 'primary' : 'warning' }}">
                                            {{ $backup['type'] }}
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

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreConfirmModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-4">
                <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                <h4 class="modal-title mb-3">Xác Nhận Khôi Phục</h4>
                <p class="text-muted">Bạn có chắc chắn muốn khôi phục cơ sở dữ liệu từ file <strong id="restore-filename-confirm"></strong>? <br> <strong>Toàn bộ dữ liệu hiện tại sẽ bị ghi đè.</strong> Thao tác này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer justify-content-center border-0">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-warning" id="confirmRestoreBtn">Tôi hiểu, Tiếp tục</button>
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
    let fileToRestore = null;

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Create backup button handlers
    $('#createBackupBtn, #createFirstBackupBtn').click(function() {
        createBackup();
    });

    // Restore backup handlers
    $('.restore-btn').click(function() {
        fileToRestore = $(this).data('filename');
        $('#restore-filename-confirm').text(fileToRestore);
        $('#restoreConfirmModal').modal('show');
    });

    // Confirm restore
    $('#confirmRestoreBtn').click(function() {
        if (fileToRestore) {
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Đang khôi phục...');
            restoreBackup(fileToRestore);
        }
    });

    // Delete backup handlers
    $('.delete-btn').click(function() {
        const filename = $(this).data('filename');
        if (confirm('Bạn có chắc chắn muốn xóa backup này? Hành động này không thể hoàn tác.')) {
            deleteBackup(filename);
        }
    });
});

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
        showAlert('danger', 'Lỗi khi xóa backup: ' + (response?.message || 'Lỗi không xác định'));
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
        showAlert('danger', 'Lỗi khi khôi phục: ' + (response?.message || 'Lỗi không xác định'));
    })
    .always(function() {
        $('#restoreConfirmModal').modal('hide');
        $('#confirmRestoreBtn').prop('disabled', false).text('Tôi hiểu, Tiếp tục');
    });
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;
    $('.alert').remove();
    $('body').append(alertHtml);
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
@endsection
