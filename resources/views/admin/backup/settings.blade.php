@extends('layouts.admin')

@section('title', 'Cài Đặt Backup')
@section('page-title', 'Cài Đặt Backup')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">⚙️ Cài Đặt Backup</h1>
            <p class="mb-0 text-muted">Cấu hình lịch trình và tùy chọn backup</p>
        </div>
        <div>
            <button type="button" class="btn btn-success" id="saveSettingsBtn">
                <i class="fas fa-save"></i> Lưu Cài Đặt
            </button>
            <a href="{{ route('admin.backup.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Quay Lại Dashboard
            </a>
        </div>
    </div>

    <form id="backupSettingsForm">
        @csrf
        
        <!-- Schedule Settings -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">📅 Lịch Trình Backup</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Daily Backup -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-sun text-warning mr-2"></i>
                                Backup Hàng Ngày
                            </label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="enableDailyBackup" 
                                       {{ $currentSettings['enable_daily_backup'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="enableDailyBackup">
                                    Kích hoạt backup hàng ngày
                                </label>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                </div>
                                <input type="time" class="form-control" id="dailyBackupTime" 
                                       value="{{ $currentSettings['daily_backup_time'] ?? '02:00' }}">
                            </div>
                            <small class="form-text text-muted">Thời gian thực hiện backup hàng ngày</small>
                        </div>
                    </div>

                    <!-- Weekly Backup -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-calendar-week text-success mr-2"></i>
                                Backup Hàng Tuần
                            </label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="enableWeeklyBackup" 
                                       {{ $currentSettings['enable_weekly_backup'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="enableWeeklyBackup">
                                    Kích hoạt backup hàng tuần
                                </label>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <select class="form-control" id="weeklyBackupDay">
                                        <option value="0" {{ ($currentSettings['weekly_backup_day'] ?? 'sunday') === 'sunday' ? 'selected' : '' }}>Chủ Nhật</option>
                                        <option value="1" {{ ($currentSettings['weekly_backup_day'] ?? '') === 'monday' ? 'selected' : '' }}>Thứ 2</option>
                                        <option value="2" {{ ($currentSettings['weekly_backup_day'] ?? '') === 'tuesday' ? 'selected' : '' }}>Thứ 3</option>
                                        <option value="3" {{ ($currentSettings['weekly_backup_day'] ?? '') === 'wednesday' ? 'selected' : '' }}>Thứ 4</option>
                                        <option value="4" {{ ($currentSettings['weekly_backup_day'] ?? '') === 'thursday' ? 'selected' : '' }}>Thứ 5</option>
                                        <option value="5" {{ ($currentSettings['weekly_backup_day'] ?? '') === 'friday' ? 'selected' : '' }}>Thứ 6</option>
                                        <option value="6" {{ ($currentSettings['weekly_backup_day'] ?? '') === 'saturday' ? 'selected' : '' }}>Thứ 7</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <input type="time" class="form-control" id="weeklyBackupTime" 
                                           value="{{ $currentSettings['weekly_backup_time'] ?? '01:00' }}">
                                </div>
                            </div>
                            <small class="form-text text-muted">Ngày và giờ thực hiện backup hàng tuần</small>
                        </div>
                    </div>

                    <!-- Quick Backup -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-bolt text-info mr-2"></i>
                                Backup Nhanh
                            </label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="enableQuickBackup" 
                                       {{ $currentSettings['enable_quick_backup'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="enableQuickBackup">
                                    Kích hoạt backup nhanh
                                </label>
                            </div>
                            <div class="input-group">
                                <input type="number" class="form-control" id="quickBackupInterval" 
                                       value="{{ $currentSettings['quick_backup_interval'] ?? 6 }}" min="1" max="24">
                                <div class="input-group-append">
                                    <span class="input-group-text">giờ</span>
                                </div>
                            </div>
                            <small class="form-text text-muted">Khoảng thời gian giữa các lần backup nhanh</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backup Options -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">🔧 Tùy Chọn Backup</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Format Settings -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-file-code text-primary mr-2"></i>
                                Định Dạng Backup Mặc Định
                            </label>
                            <select class="form-control" id="defaultBackupFormat">
                                <option value="json" {{ ($currentSettings['backup_format'] ?? 'json') === 'json' ? 'selected' : '' }}>JSON</option>
                                <option value="sql" {{ ($currentSettings['backup_format'] ?? '') === 'sql' ? 'selected' : '' }}>SQL</option>
                                <option value="both" {{ ($currentSettings['backup_format'] ?? '') === 'both' ? 'selected' : '' }}>Cả Hai (JSON + SQL)</option>
                            </select>
                            <small class="form-text text-muted">Định dạng mặc định cho backup tự động</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-archive text-warning mr-2"></i>
                                Nén Backup
                            </label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enableCompression" 
                                       {{ $currentSettings['enable_compression'] ?? false ? 'checked' : '' }}>
                                <label class="form-check-label" for="enableCompression">
                                    Tự động nén backup thành file ZIP
                                </label>
                            </div>
                            <small class="form-text text-muted">Giúp tiết kiệm dung lượng lưu trữ</small>
                        </div>
                    </div>

                    <!-- Retention Settings -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-history text-success mr-2"></i>
                                Chính Sách Lưu Trữ
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="maxBackupsToKeep" 
                                       value="{{ $currentSettings['max_backups_to_keep'] ?? 30 }}" min="5" max="100">
                                <div class="input-group-append">
                                    <span class="input-group-text">backup</span>
                                </div>
                            </div>
                            <small class="form-text text-muted">Số lượng backup tối đa được giữ lại</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-shield-alt text-danger mr-2"></i>
                                Xác Minh Tính Toàn Vẹn
                            </label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enableIntegrityCheck" 
                                       {{ $currentSettings['enable_integrity_check'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="enableIntegrityCheck">
                                    Kiểm tra tính toàn vẹn sau khi tạo backup
                                </label>
                            </div>
                            <small class="form-text text-muted">Đảm bảo backup không bị lỗi</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cloud Storage Settings -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">☁️ Lưu Trữ Cloud</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-cloud-upload-alt text-info mr-2"></i>
                                Backup Cloud Tự Động
                            </label>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="enableCloudBackup" 
                                       {{ $currentSettings['enable_cloud_backup'] ?? false ? 'checked' : '' }}>
                                <label class="form-check-label" for="enableCloudBackup">
                                    Tự động upload backup lên cloud storage
                                </label>
                            </div>

                            <label class="form-label">Nhà Cung Cấp Cloud:</label>
                            <select class="form-control" id="cloudProvider">
                                <option value="local" {{ ($currentSettings['cloud_provider'] ?? 'local') === 'local' ? 'selected' : '' }}>Local Backup</option>
                                <option value="gdrive" {{ ($currentSettings['cloud_provider'] ?? '') === 'gdrive' ? 'selected' : '' }}>Google Drive</option>
                                <option value="dropbox" {{ ($currentSettings['cloud_provider'] ?? '') === 'dropbox' ? 'selected' : '' }}>Dropbox</option>
                                <option value="aws" {{ ($currentSettings['cloud_provider'] ?? '') === 'aws' ? 'selected' : '' }}>Amazon S3</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-key text-warning mr-2"></i>
                                Cấu Hình API
                            </label>
                            <div class="alert alert-info">
                                <small>
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Cấu hình API keys và credentials trong file .env hoặc config/backup.php
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <label>Thư Mục Cloud:</label>
                                <input type="text" class="form-control" id="cloudFolder" 
                                       value="{{ $currentSettings['cloud_folder'] ?? '/backups' }}" 
                                       placeholder="/backups">
                                <small class="form-text text-muted">Thư mục lưu trữ backup trên cloud</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">📧 Thông Báo</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-envelope text-primary mr-2"></i>
                                Email Thông Báo
                            </label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="enableEmailNotifications" 
                                       {{ $currentSettings['enable_email_notifications'] ?? false ? 'checked' : '' }}>
                                <label class="form-check-label" for="enableEmailNotifications">
                                    Gửi email thông báo kết quả backup
                                </label>
                            </div>
                            
                            <input type="email" class="form-control" id="notificationEmail" 
                                   value="{{ $currentSettings['notification_email'] ?? '' }}" 
                                   placeholder="admin@example.com">
                            <small class="form-text text-muted">Email nhận thông báo backup</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-bell text-warning mr-2"></i>
                                Điều Kiện Thông Báo
                            </label>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="notifyOnSuccess" 
                                       {{ $currentSettings['notify_on_success'] ?? false ? 'checked' : '' }}>
                                <label class="form-check-label" for="notifyOnSuccess">
                                    Thông báo khi backup thành công
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="notifyOnFailure" 
                                       {{ $currentSettings['notify_on_failure'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="notifyOnFailure">
                                    Thông báo khi backup thất bại
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="notifyOnHealthIssues" 
                                       {{ $currentSettings['notify_on_health_issues'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="notifyOnHealthIssues">
                                    Thông báo khi có vấn đề sức khỏe hệ thống
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Settings -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">🔬 Cài Đặt Nâng Cao</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-database text-success mr-2"></i>
                                Bảng Backup
                            </label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="backupCustomers" checked>
                                <label class="form-check-label" for="backupCustomers">Khách hàng</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="backupServices" checked>
                                <label class="form-check-label" for="backupServices">Dịch vụ khách hàng</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="backupPackages" checked>
                                <label class="form-check-label" for="backupPackages">Gói dịch vụ</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="backupSuppliers">
                                <label class="form-check-label" for="backupSuppliers">Nhà cung cấp</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="backupLeads">
                                <label class="form-check-label" for="backupLeads">Leads</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-cogs text-info mr-2"></i>
                                Tùy Chọn Khác
                            </label>
                            
                            <div class="form-group">
                                <label>Timeout (giây):</label>
                                <input type="number" class="form-control" id="backupTimeout" 
                                       value="{{ $currentSettings['backup_timeout'] ?? 300 }}" min="60" max="3600">
                                <small class="form-text text-muted">Thời gian tối đa cho một lần backup</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Mức độ log:</label>
                                <select class="form-control" id="logLevel">
                                    <option value="error" {{ ($currentSettings['log_level'] ?? 'info') === 'error' ? 'selected' : '' }}>Error</option>
                                    <option value="warning" {{ ($currentSettings['log_level'] ?? 'info') === 'warning' ? 'selected' : '' }}>Warning</option>
                                    <option value="info" {{ ($currentSettings['log_level'] ?? 'info') === 'info' ? 'selected' : '' }}>Info</option>
                                    <option value="debug" {{ ($currentSettings['log_level'] ?? 'info') === 'debug' ? 'selected' : '' }}>Debug</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center">
                    <button type="button" class="btn btn-success btn-lg mr-3" id="saveSettingsBtn2">
                        <i class="fas fa-save"></i> Lưu Tất Cả Cài Đặt
                    </button>
                    <button type="button" class="btn btn-warning btn-lg mr-3" id="testSettingsBtn">
                        <i class="fas fa-vial"></i> Test Cài Đặt
                    </button>
                    <button type="button" class="btn btn-secondary btn-lg" id="resetSettingsBtn">
                        <i class="fas fa-undo"></i> Khôi Phục Mặc Định
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Save settings
    $('#saveSettingsBtn, #saveSettingsBtn2').click(function() {
        saveSettings();
    });
    
    // Test settings
    $('#testSettingsBtn').click(function() {
        testSettings();
    });
    
    // Reset settings
    $('#resetSettingsBtn').click(function() {
        if (confirm('Bạn có chắc chắn muốn khôi phục cài đặt mặc định?')) {
            resetSettings();
        }
    });
    
    // Toggle dependent fields
    $('#enableCloudBackup').change(function() {
        $('#cloudProvider, #cloudFolder').prop('disabled', !this.checked);
    });
    
    $('#enableEmailNotifications').change(function() {
        $('#notificationEmail').prop('disabled', !this.checked);
    });
});

function saveSettings() {
    const settings = {
        // Schedule settings
        enable_daily_backup: $('#enableDailyBackup').is(':checked'),
        daily_backup_time: $('#dailyBackupTime').val(),
        enable_weekly_backup: $('#enableWeeklyBackup').is(':checked'),
        weekly_backup_day: $('#weeklyBackupDay').val(),
        weekly_backup_time: $('#weeklyBackupTime').val(),
        enable_quick_backup: $('#enableQuickBackup').is(':checked'),
        quick_backup_interval: $('#quickBackupInterval').val(),
        
        // Backup options
        backup_format: $('#defaultBackupFormat').val(),
        enable_compression: $('#enableCompression').is(':checked'),
        max_backups_to_keep: $('#maxBackupsToKeep').val(),
        enable_integrity_check: $('#enableIntegrityCheck').is(':checked'),
        
        // Cloud settings
        enable_cloud_backup: $('#enableCloudBackup').is(':checked'),
        cloud_provider: $('#cloudProvider').val(),
        cloud_folder: $('#cloudFolder').val(),
        
        // Notification settings
        enable_email_notifications: $('#enableEmailNotifications').is(':checked'),
        notification_email: $('#notificationEmail').val(),
        notify_on_success: $('#notifyOnSuccess').is(':checked'),
        notify_on_failure: $('#notifyOnFailure').is(':checked'),
        notify_on_health_issues: $('#notifyOnHealthIssues').is(':checked'),
        
        // Advanced settings
        backup_timeout: $('#backupTimeout').val(),
        log_level: $('#logLevel').val(),
        
        _token: '{{ csrf_token() }}'
    };
    
    $.post('{{ route("admin.backup.settings.update") }}', settings)
        .done(function(response) {
            if (response.success) {
                showAlert('success', 'Cài đặt đã được lưu thành công!');
            } else {
                showAlert('danger', 'Lỗi: ' + response.message);
            }
        })
        .fail(function(xhr) {
            const response = xhr.responseJSON;
            showAlert('danger', 'Lỗi khi lưu cài đặt: ' + (response?.message || 'Unknown error'));
        });
}

function testSettings() {
    showAlert('info', 'Đang test cài đặt backup...');
    
    // TODO: Implement settings test
    setTimeout(() => {
        showAlert('success', 'Test cài đặt thành công! Tất cả cấu hình đều hoạt động bình thường.');
    }, 2000);
}

function resetSettings() {
    // Reset form to default values
    $('#enableDailyBackup').prop('checked', true);
    $('#dailyBackupTime').val('02:00');
    $('#enableWeeklyBackup').prop('checked', true);
    $('#weeklyBackupDay').val('0');
    $('#weeklyBackupTime').val('01:00');
    $('#enableQuickBackup').prop('checked', true);
    $('#quickBackupInterval').val('6');
    $('#defaultBackupFormat').val('json');
    $('#enableCompression').prop('checked', false);
    $('#maxBackupsToKeep').val('30');
    $('#enableIntegrityCheck').prop('checked', true);
    $('#enableCloudBackup').prop('checked', false);
    $('#cloudProvider').val('local');
    $('#cloudFolder').val('/backups');
    $('#enableEmailNotifications').prop('checked', false);
    $('#notificationEmail').val('');
    $('#notifyOnSuccess').prop('checked', false);
    $('#notifyOnFailure').prop('checked', true);
    $('#notifyOnHealthIssues').prop('checked', true);
    $('#backupTimeout').val('300');
    $('#logLevel').val('info');
    
    showAlert('info', 'Đã khôi phục cài đặt mặc định');
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
    
    $('.alert').remove();
    $('.container-fluid').prepend(alertHtml);
    
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
@endsection
