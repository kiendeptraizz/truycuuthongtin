@extends('layouts.admin')

@section('title', 'Nhập hàng loạt - ' . $resource->name)

@section('page-title', 'Nhập hàng loạt tài khoản')

@section('styles')
<style>
    .preview-table {
        font-family: 'Courier New', monospace;
        font-size: 12px;
    }

    .preview-table td,
    .preview-table th {
        padding: 4px 8px;
    }

    .format-example {
        font-family: 'Courier New', monospace;
        background: #f8f9fa;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
    }

    #accounts_data {
        font-family: 'Courier New', monospace;
        font-size: 13px;
        line-height: 1.5;
    }

    .count-badge {
        font-size: 14px;
        padding: 4px 12px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Form nhập -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-{{ $resource->color ?? 'primary' }} text-white">
                    <h5 class="card-title mb-0">
                        @if($resource->icon)
                        <i class="{{ $resource->icon }} me-2"></i>
                        @else
                        <i class="fas fa-folder me-2"></i>
                        @endif
                        Nhập hàng loạt vào: {{ $resource->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.resources.accounts.bulk-import', $resource) }}" id="bulkImportForm">
                        @csrf

                        <!-- Format selection -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-cog me-1"></i>Định dạng dữ liệu
                            </label>
                            <div class="row">
                                <div class="col-md-6">
                                    <select class="form-select @error('format') is-invalid @enderror"
                                        id="format" name="format" onchange="updateFormatExample()">
                                        <option value="auto" selected>🔄 Tự động nhận diện</option>
                                        <option value="email_pass">📧 Email | Password</option>
                                        <option value="email_pass_2fa">🔐 Email | Password | 2FA</option>
                                        <option value="custom">⚙️ Tùy chỉnh</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text">Ký tự phân cách</span>
                                        <select class="form-select" id="delimiter" name="delimiter">
                                            <option value="|" selected>| (pipe)</option>
                                            <option value=":">: (colon)</option>
                                            <option value=";">; (semicolon)</option>
                                            <option value=",">,(comma)</option>
                                            <option value="	">Tab</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-text" id="formatExample">
                                <i class="fas fa-info-circle me-1"></i>
                                Ví dụ: <span class="format-example">email@example.com|password123|2FA_CODE</span>
                            </div>
                        </div>

                        <!-- Data input -->
                        <div class="mb-3">
                            <label for="accounts_data" class="form-label fw-bold">
                                <i class="fas fa-list me-1"></i>Danh sách tài khoản
                                <span class="badge bg-secondary count-badge ms-2" id="lineCount">0 dòng</span>
                            </label>
                            <textarea class="form-control @error('accounts_data') is-invalid @enderror"
                                id="accounts_data" name="accounts_data" rows="12"
                                placeholder="Nhập mỗi tài khoản một dòng...&#10;email1@example.com|password1|2fa_code1&#10;email2@example.com|password2|2fa_code2&#10;email3@example.com|password3">{{ old('accounts_data') }}</textarea>
                            @error('accounts_data')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <!-- Subcategory selection -->
                        @if($subcategories->count() > 0)
                        <div class="mb-3">
                            <label for="resource_subcategory_id" class="form-label fw-bold">
                                <i class="fas fa-tag me-1"></i>Danh mục con (áp dụng cho tất cả)
                            </label>
                            <select class="form-select" id="resource_subcategory_id" name="resource_subcategory_id">
                                <option value="">-- Chưa phân loại --</option>
                                @foreach($subcategories as $sub)
                                <option value="{{ $sub->id }}" {{ old('resource_subcategory_id') == $sub->id ? 'selected' : '' }}>
                                    {{ $sub->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- Thời hạn -->
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-calendar me-1"></i>Thời hạn chung (áp dụng cho tất cả)
                        </h6>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label">Ngày kích hoạt</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="{{ old('start_date', date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">Ngày hết hạn</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="{{ old('end_date') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Thời hạn nhanh</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="quick_duration" min="1" placeholder="Số">
                                    <select class="form-select" id="quick_unit" style="max-width: 100px;">
                                        <option value="days">Ngày</option>
                                        <option value="months" selected>Tháng</option>
                                        <option value="years">Năm</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" selected>🟢 Đang hoạt động</option>
                                    <option value="reserved">🟡 Đã đặt trước</option>
                                    <option value="suspended">⚫ Tạm ngưng</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">Khả dụng</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_available"
                                        name="is_available" value="1" checked>
                                    <label class="form-check-label" for="is_available">
                                        Tài khoản còn khả dụng
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.resources.show', $resource) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </a>
                            <div>
                                <button type="button" class="btn btn-outline-info me-2" onclick="previewData()">
                                    <i class="fas fa-eye me-1"></i> Xem trước
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-1"></i> Nhập tài khoản
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview & Help -->
        <div class="col-lg-5">
            <!-- Preview Card -->
            <div class="card mb-3" id="previewCard" style="display: none;">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-eye me-2"></i>Xem trước dữ liệu</h6>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered preview-table mb-0" id="previewTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Email</th>
                                    <th>Password</th>
                                    <th>2FA</th>
                                </tr>
                            </thead>
                            <tbody id="previewBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-question-circle me-2"></i>Hướng dẫn</h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">Định dạng hỗ trợ:</h6>
                    <ul class="small mb-3">
                        <li><code>email|password</code></li>
                        <li><code>email|password|2fa</code></li>
                        <li><code>email|password|2fa|recovery</code></li>
                        <li><code>email:password:2fa</code> (dấu :)</li>
                        <li><code>email;password;2fa</code> (dấu ;)</li>
                        <li><code>email,password,2fa</code> (dấu ,)</li>
                        <li><code>email[TAB]password[TAB]2fa</code> (Tab)</li>
                    </ul>

                    <h6 class="text-primary">Ví dụ dữ liệu:</h6>
                    <pre class="bg-light p-2 rounded small mb-3">user1@gmail.com|Pass123!|ABCD1234
user2@gmail.com|SecureP@ss|XYZ789
user3@gmail.com|MyPassword
user4@gmail.com|P@ssw0rd|2FA_SECRET|RECOVERY123</pre>

                    <h6 class="text-primary">Lưu ý:</h6>
                    <ul class="small mb-0">
                        <li>Mỗi tài khoản một dòng</li>
                        <li>Có thể để trống các trường không cần thiết</li>
                        <li>Hệ thống sẽ tự động nhận diện ký tự phân cách</li>
                        <li>Dòng trống sẽ được bỏ qua</li>
                        <li>Thời hạn sẽ áp dụng cho tất cả tài khoản</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Update line count
    const accountsData = document.getElementById('accounts_data');
    const lineCount = document.getElementById('lineCount');

    accountsData.addEventListener('input', updateLineCount);

    function updateLineCount() {
        const lines = accountsData.value.split('\n').filter(line => line.trim() !== '');
        lineCount.textContent = `${lines.length} dòng`;
    }

    // Update format example
    function updateFormatExample() {
        const format = document.getElementById('format').value;
        const delimiter = document.getElementById('delimiter').value;
        const example = document.getElementById('formatExample');

        let exampleText = '';
        const del = delimiter === '\t' ? '[TAB]' : delimiter;

        switch (format) {
            case 'email_pass':
                exampleText = `email@example.com${del}password123`;
                break;
            case 'email_pass_2fa':
                exampleText = `email@example.com${del}password123${del}2FA_CODE`;
                break;
            case 'custom':
            case 'auto':
            default:
                exampleText = `email@example.com${del}password${del}2fa${del}recovery${del}notes`;
        }

        example.innerHTML = `<i class="fas fa-info-circle me-1"></i>Ví dụ: <span class="format-example">${exampleText}</span>`;
    }

    document.getElementById('delimiter').addEventListener('change', updateFormatExample);

    // Quick duration calculator
    const quickDuration = document.getElementById('quick_duration');
    const quickUnit = document.getElementById('quick_unit');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    function calculateQuickDuration() {
        const duration = parseInt(quickDuration.value) || 0;
        const unit = quickUnit.value;
        const start = startDate.value;

        if (!start || duration <= 0) return;

        const startD = new Date(start);
        let endD = new Date(startD);

        if (unit === 'days') {
            endD.setDate(startD.getDate() + duration);
        } else if (unit === 'months') {
            endD.setMonth(startD.getMonth() + duration);
        } else if (unit === 'years') {
            endD.setFullYear(startD.getFullYear() + duration);
        }

        endDate.value = endD.toISOString().split('T')[0];
    }

    quickDuration.addEventListener('input', calculateQuickDuration);
    quickUnit.addEventListener('change', calculateQuickDuration);
    startDate.addEventListener('change', calculateQuickDuration);

    // Preview data
    function previewData() {
        const data = accountsData.value.trim();
        const delimiter = document.getElementById('delimiter').value;
        const previewCard = document.getElementById('previewCard');
        const previewBody = document.getElementById('previewBody');

        if (!data) {
            alert('Vui lòng nhập dữ liệu trước khi xem trước!');
            return;
        }

        const lines = data.split('\n').filter(line => line.trim() !== '');
        let html = '';

        lines.slice(0, 20).forEach((line, index) => {
            // Auto detect delimiter
            let del = delimiter;
            const delimiters = ['|', ':', '\t', ';', ','];
            for (const d of delimiters) {
                if (line.includes(d)) {
                    del = d;
                    break;
                }
            }

            const parts = line.split(del).map(p => p.trim());

            html += `<tr>
            <td>${index + 1}</td>
            <td>${escapeHtml(parts[0] || '-')}</td>
            <td>${escapeHtml(parts[1] || '-')}</td>
            <td>${escapeHtml(parts[2] || '-')}</td>
        </tr>`;
        });

        if (lines.length > 20) {
            html += `<tr><td colspan="4" class="text-center text-muted">... và ${lines.length - 20} dòng nữa</td></tr>`;
        }

        previewBody.innerHTML = html;
        previewCard.style.display = 'block';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Initial count
    updateLineCount();
</script>
@endsection