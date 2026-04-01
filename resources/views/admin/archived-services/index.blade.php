@extends('layouts.admin')

@section('title', 'Dịch vụ đã lưu trữ')
@section('page-title', 'Dịch vụ đã lưu trữ')

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border-left: 4px solid;
    }
    .stat-card.total { border-left-color: #6366f1; }
    .stat-card.week { border-left-color: #10b981; }
    .stat-card.month { border-left-color: #f59e0b; }
    .stat-number {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
    }
    .stat-label {
        font-size: 0.875rem;
        color: #64748b;
        margin-top: 4px;
    }
    .archive-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #fff;
    }
    .archive-icon.total { background: linear-gradient(135deg, #6366f1, #4f46e5); }
    .archive-icon.week { background: linear-gradient(135deg, #10b981, #059669); }
    .archive-icon.month { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .filter-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .table-container {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .table-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .table-header h5 {
        margin: 0;
        font-weight: 600;
        color: #334155;
    }
    .table th {
        background: #f8fafc;
        font-weight: 600;
        color: #475569;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
    }
    .table td {
        vertical-align: middle;
        color: #334155;
    }
    .customer-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .customer-avatar {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .customer-name {
        font-weight: 500;
        color: #334155;
    }
    .customer-code {
        font-size: 0.8rem;
        color: #64748b;
    }
    .badge-deleted {
        background: #fef2f2;
        color: #dc2626;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .btn-restore {
        background: #10b981;
        color: #fff;
        border: none;
    }
    .btn-restore:hover {
        background: #059669;
        color: #fff;
    }
    .btn-force-delete {
        background: #ef4444;
        color: #fff;
        border: none;
    }
    .btn-force-delete:hover {
        background: #dc2626;
        color: #fff;
    }
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #64748b;
    }
    .empty-state i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }
    .bulk-action-bar {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff;
        padding: 12px 20px;
        border-radius: 10px;
        margin-bottom: 1rem;
        display: none;
        align-items: center;
        justify-content: space-between;
    }
    .bulk-action-bar.show {
        display: flex;
    }
    .cleanup-card {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #f59e0b;
        border-radius: 12px;
        padding: 1.25rem;
    }
    .cleanup-card h5 {
        color: #92400e;
    }
    .cleanup-result {
        background: #fff;
        border-radius: 8px;
        padding: 1rem;
        border: 1px solid #e2e8f0;
    }
    .cleanup-result.success {
        background: #ecfdf5;
        border-color: #10b981;
    }
    .cleanup-result.warning {
        background: #fffbeb;
        border-color: #f59e0b;
    }
    .cleanup-result.info {
        background: #eff6ff;
        border-color: #3b82f6;
    }
</style>
@endpush

@section('content')
<!-- Nút chạy Cleanup thủ công -->
<div class="cleanup-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h5 class="mb-1"><i class="fas fa-broom text-warning me-2"></i>Dọn dẹp dịch vụ hết hạn</h5>
            <p class="text-muted mb-0 small">Xóa tự động các dịch vụ đã hết hạn quá số ngày được chọn</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <label class="small text-muted mb-0">Hết hạn quá:</label>
                <select id="cleanupDays" class="form-select form-select-sm" style="width: 120px;">
                    <option value="7">7 ngày</option>
                    <option value="14">14 ngày</option>
                    <option value="30" selected>30 ngày</option>
                    <option value="60">60 ngày</option>
                    <option value="90">90 ngày</option>
                </select>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="previewCleanup()">
                <i class="fas fa-eye me-1"></i>Xem trước
            </button>
            <button type="button" class="btn btn-warning btn-sm" onclick="runCleanup()">
                <i class="fas fa-play me-1"></i>Chạy dọn dẹp
            </button>
        </div>
    </div>
    <div id="cleanupResult" class="mt-3" style="display: none;"></div>
</div>

<!-- Thống kê -->
<div class="stats-grid">
    <div class="stat-card total">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="stat-number">{{ $stats['total'] }}</div>
                <div class="stat-label">Tổng dịch vụ lưu trữ</div>
            </div>
            <div class="archive-icon total">
                <i class="fas fa-archive"></i>
            </div>
        </div>
    </div>
    <div class="stat-card week">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="stat-number">{{ $stats['last_7_days'] }}</div>
                <div class="stat-label">Xóa trong 7 ngày qua</div>
            </div>
            <div class="archive-icon week">
                <i class="fas fa-calendar-week"></i>
            </div>
        </div>
    </div>
    <div class="stat-card month">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="stat-number">{{ $stats['last_30_days'] }}</div>
                <div class="stat-label">Xóa trong 30 ngày qua</div>
            </div>
            <div class="archive-icon month">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
    </div>
</div>

<!-- Bộ lọc -->
<div class="filter-card">
    <form method="GET" action="{{ route('admin.archived-services.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-medium">Tìm kiếm</label>
            <input type="text" 
                   class="form-control" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="Tên KH, mã KH, email, gói dịch vụ...">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-medium">Từ ngày xóa</label>
            <input type="date" class="form-control" name="deleted_from" value="{{ request('deleted_from') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-medium">Đến ngày xóa</label>
            <input type="date" class="form-control" name="deleted_to" value="{{ request('deleted_to') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search me-1"></i> Lọc
            </button>
        </div>
    </form>
</div>

<!-- Bulk Action Bar -->
<div class="bulk-action-bar alert no-auto-hide" id="bulkActionBar">
    <div>
        <i class="fas fa-check-circle me-2"></i>
        Đã chọn <strong id="selectedCount">0</strong> dịch vụ
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-light" onclick="deselectAll()">
            <i class="fas fa-times me-1"></i> Bỏ chọn
        </button>
        <button class="btn btn-sm btn-success" onclick="bulkRestore()">
            <i class="fas fa-undo me-1"></i> Khôi phục
        </button>
        <button class="btn btn-sm btn-danger" onclick="bulkForceDelete()">
            <i class="fas fa-trash me-1"></i> Xóa vĩnh viễn
        </button>
    </div>
</div>

<!-- Bảng dữ liệu -->
<div class="table-container">
    <div class="table-header">
        <h5><i class="fas fa-archive me-2 text-secondary"></i>Danh sách dịch vụ đã lưu trữ</h5>
        <span class="text-muted">{{ $archivedServices->total() }} dịch vụ</span>
    </div>
    
    @if($archivedServices->isEmpty())
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h5>Không có dịch vụ nào trong lưu trữ</h5>
            <p class="text-muted">Các dịch vụ bị xóa sẽ được lưu trữ tại đây</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" class="form-check-input" id="selectAll" onchange="toggleAll()">
                        </th>
                        <th>Khách hàng</th>
                        <th>Gói dịch vụ</th>
                        <th>Ngày hết hạn</th>
                        <th>Ngày xóa</th>
                        <th style="width: 180px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($archivedServices as $service)
                    <tr>
                        <td>
                            <input type="checkbox" 
                                   class="form-check-input service-checkbox" 
                                   value="{{ $service->id }}"
                                   onchange="updateBulkBar()">
                        </td>
                        <td>
                            <div class="customer-info">
                                <div class="customer-avatar">
                                    {{ strtoupper(substr($service->customer->name ?? 'N', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="customer-name">{{ $service->customer->name ?? 'N/A' }}</div>
                                    <div class="customer-code">{{ $service->customer->customer_code ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>{{ $service->servicePackage->name ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $service->servicePackage->category->name ?? '' }}</small>
                        </td>
                        <td>
                            {{ $service->expires_at?->format('d/m/Y') ?? 'N/A' }}
                        </td>
                        <td>
                            <span class="badge-deleted">
                                <i class="fas fa-trash-alt me-1"></i>
                                {{ $service->deleted_at?->format('d/m/Y H:i') }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <form action="{{ route('admin.archived-services.restore', $service->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-restore" 
                                            title="Khôi phục">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.archived-services.force-delete', $service->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa VĨNH VIỄN dịch vụ này? Hành động này không thể hoàn tác!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-force-delete" 
                                            title="Xóa vĩnh viễn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="p-3 border-top">
            {{ $archivedServices->withQueryString()->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleAll() {
    const selectAll = document.getElementById('selectAll');
    document.querySelectorAll('.service-checkbox').forEach(cb => {
        cb.checked = selectAll.checked;
    });
    updateBulkBar();
}

function deselectAll() {
    document.getElementById('selectAll').checked = false;
    document.querySelectorAll('.service-checkbox').forEach(cb => {
        cb.checked = false;
    });
    updateBulkBar();
}

function updateBulkBar() {
    const checked = document.querySelectorAll('.service-checkbox:checked');
    const bar = document.getElementById('bulkActionBar');
    const count = document.getElementById('selectedCount');
    
    if (checked.length > 0) {
        bar.classList.add('show');
        count.textContent = checked.length;
    } else {
        bar.classList.remove('show');
    }
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.service-checkbox:checked')).map(cb => cb.value);
}

function bulkRestore() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;
    
    if (!confirm(`Bạn có chắc muốn khôi phục ${ids.length} dịch vụ?`)) return;
    
    fetch('{{ route("admin.archived-services.bulk-restore") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ids: ids })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    });
}

function bulkForceDelete() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;
    
    if (!confirm(`⚠️ CẢNH BÁO: Bạn có chắc muốn XÓA VĨNH VIỄN ${ids.length} dịch vụ?\n\nHành động này KHÔNG THỂ hoàn tác!`)) return;
    
    fetch('{{ route("admin.archived-services.bulk-force-delete") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ids: ids })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    });
}

// Cleanup functions
function previewCleanup() {
    const days = document.getElementById('cleanupDays').value;
    const resultDiv = document.getElementById('cleanupResult');
    
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '<div class="cleanup-result info"><i class="fas fa-spinner fa-spin me-2"></i>Đang kiểm tra...</div>';
    
    fetch('{{ route("admin.archived-services.run-cleanup") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ days: days, dry_run: true })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (data.preview_count > 0) {
                resultDiv.innerHTML = `
                    <div class="cleanup-result warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Tìm thấy ${data.preview_count} dịch vụ</strong> hết hạn quá ${days} ngày sẽ bị xóa.
                        <br><small class="text-muted">Nhấn "Chạy dọn dẹp" để thực hiện xóa.</small>
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="cleanup-result success">
                        <i class="fas fa-check-circle me-2"></i>
                        ${data.message}
                    </div>
                `;
            }
        } else {
            resultDiv.innerHTML = `<div class="cleanup-result" style="background:#fef2f2;border-color:#ef4444;"><i class="fas fa-times-circle me-2 text-danger"></i>${data.message}</div>`;
        }
    })
    .catch(err => {
        resultDiv.innerHTML = `<div class="cleanup-result" style="background:#fef2f2;border-color:#ef4444;"><i class="fas fa-times-circle me-2 text-danger"></i>Lỗi kết nối</div>`;
    });
}

function runCleanup() {
    const days = document.getElementById('cleanupDays').value;
    const resultDiv = document.getElementById('cleanupResult');
    
    if (!confirm(`Bạn có chắc muốn xóa tất cả dịch vụ hết hạn quá ${days} ngày?\n\nCác dịch vụ sẽ được chuyển vào lưu trữ và có thể khôi phục sau.`)) {
        return;
    }
    
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '<div class="cleanup-result info"><i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...</div>';
    
    fetch('{{ route("admin.archived-services.run-cleanup") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ days: days, dry_run: false })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="cleanup-result success">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>${data.message}</strong>
                </div>
            `;
            // Reload sau 2 giây
            if (data.deleted_count > 0) {
                setTimeout(() => location.reload(), 2000);
            }
        } else {
            resultDiv.innerHTML = `<div class="cleanup-result" style="background:#fef2f2;border-color:#ef4444;"><i class="fas fa-times-circle me-2 text-danger"></i>${data.message}</div>`;
        }
    })
    .catch(err => {
        resultDiv.innerHTML = `<div class="cleanup-result" style="background:#fef2f2;border-color:#ef4444;"><i class="fas fa-times-circle me-2 text-danger"></i>Lỗi kết nối</div>`;
    });
}
</script>
@endpush
@endsection

