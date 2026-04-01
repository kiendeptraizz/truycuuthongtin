@extends('layouts.admin')

@section('title', 'Lịch đăng bài')
@section('page-title', 'Lịch đăng bài')

@section('content')
<!-- Header Card -->
<div class="glass-card page-header-card ">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="icon-circle bg-gradient-info me-3">
                    <i class="fas fa-calendar-alt text-white"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1">Lịch đăng bài</h4>
                    <p class="text-muted mb-0">Quản lý và theo dõi lịch đăng bài trên các nền tảng</p>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <!-- View Toggle -->
                <div class="btn-group view-toggle" role="group">
                    <button type="button" class="btn btn-outline-secondary" id="calendarView">
                        <i class="fas fa-calendar me-1"></i>Lịch
                    </button>
                    <button type="button" class="btn btn-outline-secondary active" id="listView">
                        <i class="fas fa-list me-1"></i>Danh sách
                    </button>
                </div>
                <button onclick="refreshData()" class="btn btn-outline-secondary btn-sm" title="Làm mới dữ liệu">
                    <i class="fas fa-sync-alt me-1"></i>
                    Làm mới
                </button>
                <a href="{{ route('admin.content-scheduler.create') }}" class="btn btn-gradient-info btn-hover-lift">
                    <i class="fas fa-plus me-1"></i>
                    Tạo bài đăng mới
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stats-card glass-card border-0 ">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-gradient-primary">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-number">{{ $contentPosts->where('status', 'scheduled')->count() }}</div>
                        <div class="stats-label">Đã lên lịch</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stats-card glass-card border-0 ">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-gradient-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-number">{{ $contentPosts->where('status', 'posted')->count() }}</div>
                        <div class="stats-label">Đã đăng</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stats-card glass-card border-0 ">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-gradient-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-number">
                            {{ $contentPosts->filter(fn($post) => $post->needsReminder())->count() }}
                        </div>
                        <div class="stats-label">Sắp đến giờ</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stats-card glass-card border-0 ">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-gradient-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-number">{{ $contentPosts->filter(fn($post) => $post->isOverdue())->count() }}
                        </div>
                        <div class="stats-label">Quá hạn</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Calendar View -->
<div class="glass-card calendar-view-card" id="calendar-container" style="display: none;">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-calendar-alt me-2 text-info"></i>Lịch đăng bài
        </h5>
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-outline-primary btn-hover-lift" id="todayBtn">
                <i class="fas fa-calendar-day me-1"></i>Hôm nay
            </button>
            <button type="button" class="btn btn-outline-success btn-hover-lift" id="addEventBtn">
                <i class="fas fa-plus me-1"></i>Thêm bài đăng
            </button>
        </div>
    </div>
    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>

<!-- List View -->
<div id="list-container">
    <!-- Advanced Filters Card -->
    <div class="filter-card glass-card border-0 mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-semibold text-muted mb-0">
                    <i class="fas fa-filter me-1 text-primary"></i>
                    Bộ lọc nâng cao
                </h6>
                @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                <div class="filter-badge">
                    <i class="fas fa-check-circle me-1"></i>
                    {{ collect(request()->only(['search', 'status', 'date_from', 'date_to']))->filter()->count() }} bộ
                    lọc đang áp dụng
                </div>
                @endif
            </div>

            <form method="GET" id="filterForm" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small text-muted fw-semibold">
                        <i class="fas fa-search me-1"></i>Tìm kiếm
                    </label>
                    <div class="input-group input-group-modern">
                        <span class="input-group-text">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="Tiêu đề, nội dung...">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-semibold">
                        <i class="fas fa-toggle-on me-1"></i>Trạng thái
                    </label>
                    <select name="status" class="form-select form-select-modern">
                        <option value="">Tất cả</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>
                            📅 Đã lên lịch
                        </option>
                        <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>
                            ✅ Đã đăng
                        </option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                            ❌ Đã hủy
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-semibold">
                        <i class="fas fa-calendar-alt me-1"></i>Từ ngày
                    </label>
                    <input type="date" name="date_from" class="form-control form-control-modern"
                        value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-semibold">
                        <i class="fas fa-calendar-check me-1"></i>Đến ngày
                    </label>
                    <input type="date" name="date_to" class="form-control form-control-modern"
                        value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-gradient-primary btn-hover-lift flex-fill">
                            <i class="fas fa-search me-1"></i>
                            Lọc
                        </button>
                        @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('admin.content-scheduler.index') }}"
                            class="btn btn-outline-secondary btn-hover-lift" title="Xóa bộ lọc">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Quick Filter Buttons -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="quick-filters">
                        <span class="quick-filters-label">Lọc nhanh:</span>
                        <div class="quick-filters-buttons">
                            <a href="{{ route('admin.content-scheduler.index', ['status' => 'scheduled']) }}"
                                class="btn btn-sm {{ request('status') === 'scheduled' ? 'btn-primary' : 'btn-outline-primary' }} btn-hover-lift">
                                <i class="fas fa-calendar-plus me-1"></i>Đã lên lịch
                            </a>
                            <a href="{{ route('admin.content-scheduler.index', ['status' => 'posted']) }}"
                                class="btn btn-sm {{ request('status') === 'posted' ? 'btn-success' : 'btn-outline-success' }} btn-hover-lift">
                                <i class="fas fa-check-circle me-1"></i>Đã đăng
                            </a>
                            <a href="{{ route('admin.content-scheduler.index', ['date_from' => now()->format('Y-m-d')]) }}"
                                class="btn btn-sm btn-outline-info btn-hover-lift">
                                <i class="fas fa-calendar-day me-1"></i>Hôm nay
                            </a>
                            <a href="{{ route('admin.content-scheduler.index', ['date_from' => now()->format('Y-m-d'), 'date_to' => now()->addDays(7)->format('Y-m-d')]) }}"
                                class="btn btn-sm btn-outline-warning btn-hover-lift">
                                <i class="fas fa-calendar-week me-1"></i>7 ngày tới
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Posts List -->
    <div class="glass-card main-content-card">
        <div class="card-body">
            @if($contentPosts->count() > 0)
            <!-- Results Info -->
            <div class="results-info d-flex justify-content-between align-items-center mb-3">
                <div class="results-count">
                    <i class="fas fa-list me-1 text-muted"></i>
                    Hiển thị <strong>{{ $contentPosts->firstItem() ?? 0 }}</strong> -
                    <strong>{{ $contentPosts->lastItem() ?? 0 }}</strong>
                    trong tổng số <strong class="text-primary">{{ $contentPosts->total() }}</strong> bài đăng
                </div>
                <div class="table-actions">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportData()"
                            title="Xuất dữ liệu">
                            <i class="fas fa-download me-1"></i>
                            Xuất Excel
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive-enhanced horizontal-scroll-container">
                <table class="table table-modern table-hover table-fixed-layout table-min-width-large">
                    <thead class="table-header-modern">
                        <tr>
                            <th class="sortable" data-sort="title">
                                <i class="fas fa-heading me-1"></i>Tiêu đề
                                <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th>
                                <i class="fas fa-users me-1"></i>Nhóm đích
                            </th>
                            <th class="sortable" data-sort="scheduled_at">
                                <i class="fas fa-clock me-1"></i>Thời gian đăng
                                <i class="fas fa-sort ms-1"></i>
                            </th>
                            <th>
                                <i class="fas fa-toggle-on me-1"></i>Trạng thái
                            </th>
                            <th class="text-center table-action-column">
                                <i class="fas fa-cogs me-1"></i>Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contentPosts as $post)
                        <tr class="table-row-modern ">
                            <td>
                                <div class="post-info">
                                    <div class="post-title">
                                        <i class="fas fa-file-alt me-1 text-primary"></i>
                                        {{ $post->title }}
                                    </div>
                                    <div class="post-preview">
                                        {{ Str::limit($post->content, 80) }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="target-groups">
                                    @foreach(explode(',', $post->target_groups_string) as $group)
                                    <span class="target-group-badge">
                                        <i class="fas fa-users me-1"></i>
                                        {{ trim($group) }}
                                    </span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div class="schedule-info">
                                    <div class="schedule-date">
                                        <i class="fas fa-calendar me-1 text-info"></i>
                                        {{ $post->scheduled_at->format('d/m/Y') }}
                                    </div>
                                    <div class="schedule-time">
                                        <i class="fas fa-clock me-1 text-secondary"></i>
                                        {{ $post->scheduled_at->format('H:i') }}
                                    </div>
                                    <div class="schedule-status">
                                        @if($post->isOverdue())
                                        <span class="text-danger">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Quá hạn {{ $post->scheduled_at->diffForHumans() }}
                                        </span>
                                        @elseif($post->needsReminder())
                                        <span class="text-warning">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $post->scheduled_at->diffForHumans() }}
                                        </span>
                                        @else
                                        <span class="text-success">
                                            <i class="fas fa-check me-1"></i>
                                            {{ $post->scheduled_at->diffForHumans() }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($post->status == 'scheduled')
                                <span class="status-badge status-scheduled">
                                    <i class="fas fa-calendar-plus me-1"></i>
                                    Đã lên lịch
                                </span>
                                @elseif($post->status == 'posted')
                                <span class="status-badge status-posted">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Đã đăng
                                </span>
                                @else
                                <span class="status-badge status-cancelled">
                                    <i class="fas fa-ban me-1"></i>
                                    Đã hủy
                                </span>
                                @endif
                            </td>
                            <td class="table-action-column">
                                <div class="action-buttons">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.content-scheduler.show', $post) }}"
                                            class="btn btn-sm btn-outline-info btn-hover-lift" title="Xem chi tiết"
                                            data-bs-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.content-scheduler.edit', $post) }}"
                                            class="btn btn-sm btn-outline-warning btn-hover-lift" title="Chỉnh sửa"
                                            data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($post->status == 'scheduled')
                                        <button type="button" class="btn btn-sm btn-outline-success btn-hover-lift"
                                            title="Đánh dấu đã đăng" data-bs-toggle="tooltip"
                                            onclick="markAsPosted({{ $post->id }}, '{{ $post->title }}')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-hover-lift"
                                            title="Xóa bài đăng" data-bs-toggle="tooltip"
                                            onclick="confirmDelete({{ $post->id }}, '{{ addslashes($post->title) }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper d-flex justify-content-between align-items-center mt-4">
                <div class="pagination-info">
                    Hiển thị <strong>{{ $contentPosts->firstItem() ?? 0 }}</strong> đến
                    <strong>{{ $contentPosts->lastItem() ?? 0 }}</strong>
                    trong tổng số <strong class="text-primary">{{ $contentPosts->total() }}</strong> bài đăng
                </div>
                <div class="pagination-nav">
                    {{ $contentPosts->appends(request()->query())->links() }}
                </div>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h5 class="empty-state-title">Chưa có bài đăng nào</h5>
                @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                <p class="empty-state-text">
                    Thử thay đổi bộ lọc hoặc
                    <a href="{{ route('admin.content-scheduler.index') }}" class="text-primary">xóa bộ lọc</a>
                </p>
                @else
                <p class="empty-state-text">
                    Hãy <a href="{{ route('admin.content-scheduler.create') }}" class="text-primary">tạo bài đăng đầu
                        tiên</a> của bạn!
                </p>
                @endif
                <div class="empty-state-actions mt-3">
                    <a href="{{ route('admin.content-scheduler.create') }}"
                        class="btn btn-gradient-info btn-hover-lift">
                        <i class="fas fa-plus me-1"></i>
                        Tạo bài đăng mới
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Hidden forms -->
<form id="markPostedForm" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
</form>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<!-- FullCalendar CSS & JS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const calendarEl = document.getElementById('calendar');
    const calendarContainer = document.getElementById('calendar-container');
    const listContainer = document.getElementById('list-container');
    const calendarViewBtn = document.getElementById('calendarView');
    const listViewBtn = document.getElementById('listView');

    // Initialize FullCalendar
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        height: 'auto',
        locale: 'vi',
        firstDay: 1, // Monday
        events: '{{ route("admin.content-scheduler.calendar") }}',
        eventClick: function(info) {
            showEventModal(info.event);
        },
        eventDidMount: function(info) {
            // Add tooltip with content preview
            const content = info.event.extendedProps.content;
            const truncatedContent = content.length > 100 ? content.substring(0, 100) + '...' :
                content;
            info.el.setAttribute('title', truncatedContent);

            // Add status indicator
            const status = info.event.extendedProps.status;
            if (status === 'posted') {
                info.el.style.opacity = '0.7';
                info.el.style.textDecoration = 'line-through';
            }
        },
        dateClick: function(info) {
            // Quick add event on date click
            const selectedDate = info.dateStr;
            window.location.href =
                `{{ route('admin.content-scheduler.create') }}?date=${selectedDate}`;
        },
        eventDrop: function(info) {
            // Handle drag and drop (if we implement it later)
            updateEventDate(info.event.id, info.event.start);
        }
    });

    // View toggle functionality
    calendarViewBtn.addEventListener('click', function() {
        calendarContainer.style.display = 'block';
        listContainer.style.display = 'none';
        calendarViewBtn.classList.add('active');
        listViewBtn.classList.remove('active');
        calendar.render();
    });

    listViewBtn.addEventListener('click', function() {
        calendarContainer.style.display = 'none';
        listContainer.style.display = 'block';
        listViewBtn.classList.add('active');
        calendarViewBtn.classList.remove('active');
    });

    // Today button functionality
    document.getElementById('todayBtn')?.addEventListener('click', function() {
        calendar.today();
    });

    // Add event button functionality
    document.getElementById('addEventBtn')?.addEventListener('click', function() {
        window.location.href = '{{ route("admin.content-scheduler.create") }}';
    });

    // Auto-submit on select changes
    const statusSelect = document.querySelector('select[name="status"]');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            showLoadingState();
            document.querySelector('#filterForm').submit();
        });
    }

    // Date validation
    const dateFromInput = document.querySelector('input[name="date_from"]');
    const dateToInput = document.querySelector('input[name="date_to"]');

    if (dateFromInput && dateToInput) {
        dateFromInput.addEventListener('change', function() {
            if (this.value && dateToInput.value && this.value > dateToInput.value) {
                dateToInput.value = this.value;
            }
            dateToInput.min = this.value;
        });

        dateToInput.addEventListener('change', function() {
            if (this.value && dateFromInput.value && this.value < dateFromInput.value) {
                dateFromInput.value = this.value;
            }
            dateFromInput.max = this.value;
        });
    }

    // Real-time search with debounce
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const value = this.value.trim();

            if (value.length === 0 || value.length >= 3) {
                searchTimeout = setTimeout(() => {
                    showLoadingState();
                    document.querySelector('#filterForm').submit();
                }, 800);
            }
        });
    }

    // Table sorting
    document.querySelectorAll('.sortable').forEach(header => {
        header.addEventListener('click', function() {
            const sortBy = this.dataset.sort;
            toggleSort(sortBy);
        });
    });

    // Ripple effect for buttons
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Show event details modal
    function showEventModal(event) {
        const modal = document.getElementById('eventModal');
        if (!modal) {
            createEventModal();
        }

        document.getElementById('modalTitle').textContent = event.title;
        document.getElementById('modalContent').textContent = event.extendedProps.content;
        document.getElementById('modalTargetGroups').textContent = event.extendedProps.target_groups;
        document.getElementById('modalStatus').textContent = getStatusText(event.extendedProps.status);
        document.getElementById('modalStatus').className =
            `badge bg-${getStatusColor(event.extendedProps.status)}`;
        document.getElementById('modalScheduledAt').textContent = event.start.toLocaleString('vi-VN');

        // Set action buttons
        const editUrl = '{{ route("admin.content-scheduler.edit", ":id") }}'.replace(':id', event.id);
        const viewUrl = '{{ route("admin.content-scheduler.show", ":id") }}'.replace(':id', event.id);
        document.getElementById('editBtn').href = editUrl;
        document.getElementById('viewBtn').href = viewUrl;

        const markPostedBtn = document.getElementById('markPostedBtn');
        if (event.extendedProps.status === 'scheduled') {
            markPostedBtn.style.display = 'inline-block';
            markPostedBtn.onclick = function() {
                markAsPosted(event.id, event.title);
            };
        } else {
            markPostedBtn.style.display = 'none';
        }

        new bootstrap.Modal(modal).show();
    }

    // Create event modal if it doesn't exist
    function createEventModal() {
        const modalHtml = `
            <div class="modal fade" id="eventModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTitle"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <strong>Nội dung:</strong>
                                <p id="modalContent" class="mt-1"></p>
                            </div>
                            <div class="mb-3">
                                <strong>Nhóm đích:</strong>
                                <span id="modalTargetGroups" class="badge bg-info ms-2"></span>
                            </div>
                            <div class="mb-3">
                                <strong>Trạng thái:</strong>
                                <span id="modalStatus" class="badge ms-2"></span>
                            </div>
                            <div class="mb-3">
                                <strong>Thời gian đăng:</strong>
                                <span id="modalScheduledAt"></span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" id="viewBtn" class="btn btn-info">
                                <i class="fas fa-eye me-1"></i>Xem chi tiết
                            </a>
                            <a href="#" id="editBtn" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i>Chỉnh sửa
                            </a>
                            <button type="button" id="markPostedBtn" class="btn btn-success">
                                <i class="fas fa-check me-1"></i>Đánh dấu đã đăng
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    // Helper functions
    function getStatusText(status) {
        switch (status) {
            case 'scheduled':
                return 'Đã lên lịch';
            case 'posted':
                return 'Đã đăng';
            case 'cancelled':
                return 'Đã hủy';
            default:
                return status;
        }
    }

    function getStatusColor(status) {
        switch (status) {
            case 'scheduled':
                return 'primary';
            case 'posted':
                return 'success';
            case 'cancelled':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    function updateEventDate(eventId, newDate) {
        // This would be used for drag & drop functionality
        // Implementation depends on your backend API
    }
});

// Mark as posted function
function markAsPosted(postId, postTitle) {
    if (confirm(`Đánh dấu bài đăng "${postTitle}" là đã đăng?\n\nHành động này sẽ cập nhật trạng thái bài đăng.`)) {
        const form = document.getElementById('markPostedForm');
        form.action = `/admin/content-scheduler/${postId}/mark-posted`;
        showLoadingState();
        form.submit();
    }
}

// Confirm delete function
function confirmDelete(postId, postTitle) {
    if (confirm(`Bạn có chắc chắn muốn xóa bài đăng "${postTitle}"?\n\nHành động này không thể hoàn tác!`)) {
        const form = document.getElementById('deleteForm');
        form.action = `/admin/content-scheduler/${postId}`;
        showLoadingState();
        form.submit();
    }
}

// Refresh data
function refreshData() {
    showLoadingState();
    window.location.reload();
}

// Export data
function exportData() {
    showToast('Tính năng xuất dữ liệu sẽ được phát triển trong phiên bản tới', 'info');
}

// Sort toggle
function toggleSort(sortBy) {
    const url = new URL(window.location);
    const currentSort = url.searchParams.get('sort');
    const currentDir = url.searchParams.get('dir') || 'asc';

    if (currentSort === sortBy) {
        url.searchParams.set('dir', currentDir === 'asc' ? 'desc' : 'asc');
    } else {
        url.searchParams.set('sort', sortBy);
        url.searchParams.set('dir', 'asc');
    }

    showLoadingState();
    window.location.href = url.toString();
}

// Loading state
function showLoadingState() {
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = `
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Đang tải...</span>
            </div>
            <div class="mt-2">Đang tải dữ liệu...</div>
        </div>
    `;
    document.body.appendChild(loadingOverlay);
}

// Show notification for posts due soon
document.addEventListener('DOMContentLoaded', function() {
    const dueSoon = document.querySelectorAll('.schedule-status .text-warning').length;
    const overdue = document.querySelectorAll('.schedule-status .text-danger').length;

    if (dueSoon > 0) {
        showToast(`Có ${dueSoon} bài đăng sắp đến giờ`, 'warning', 5000);
    }

    if (overdue > 0) {
        showToast(`Có ${overdue} bài đăng đã quá hạn`, 'error', 5000);
    }
});
</script>
@endsection