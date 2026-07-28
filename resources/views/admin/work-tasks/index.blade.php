@extends('layouts.admin')

@section('title', 'Công việc cần xử lý')
@section('page-title', '🏷 Công việc cần xử lý')

@section('content')
<div class="container-fluid px-0">

    {{-- Tạo mã mới --}}
    <div class="card shadow-sm mb-3 border-0">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.work-tasks.store') }}" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-9">
                    <label class="form-label small text-muted mb-1">Ghi chú (tùy chọn) — mô tả việc để dễ nhận ra khi xem lại</label>
                    <input type="text" name="note" class="form-control" maxlength="500"
                        placeholder="Vd: KH chờ cấp Netflix / cần bảo hành CapCut ...">
                </div>
                <div class="col-md-3 d-grid">
                    <button class="btn btn-primary"><i class="fas fa-plus me-1"></i> Tạo mã công việc</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabs + tìm kiếm --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'pending' ? 'active' : '' }}"
                    href="{{ route('admin.work-tasks.index', ['tab' => 'pending', 'q' => $search]) }}">
                    Chưa xong <span class="badge bg-light text-dark ms-1">{{ $pendingCount }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'done' ? 'active' : '' }}"
                    href="{{ route('admin.work-tasks.index', ['tab' => 'done', 'q' => $search]) }}">
                    Đã xong <span class="badge bg-light text-dark ms-1">{{ $doneCount }}</span>
                </a>
            </li>
        </ul>
        <form method="GET" class="d-flex gap-2" style="max-width: 340px;">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm"
                placeholder="Tìm mã / ghi chú...">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
        </form>
    </div>

    {{-- Bảng --}}
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã</th>
                        <th>Ghi chú</th>
                        <th class="text-nowrap">Tạo lúc</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $t)
                        <tr>
                            <td class="text-nowrap">
                                <code class="fs-6">{{ $t->code }}</code>
                                <button type="button" class="btn btn-sm btn-link p-0 ms-1 copy-btn"
                                    data-code="{{ $t->code }}" title="Copy mã"><i class="far fa-copy"></i></button>
                                @if($t->isDone())
                                    <span class="badge bg-success ms-1">Đã xong</span>
                                @endif
                            </td>
                            <td style="min-width: 260px;">
                                <form method="POST" action="{{ route('admin.work-tasks.note', $t) }}" class="d-flex gap-1">
                                    @csrf @method('PATCH')
                                    <input type="text" name="note" value="{{ $t->note }}" maxlength="500"
                                        class="form-control form-control-sm" placeholder="(chưa có ghi chú)">
                                    <button class="btn btn-sm btn-outline-secondary" title="Lưu ghi chú"><i class="fas fa-save"></i></button>
                                </form>
                            </td>
                            <td class="text-nowrap small text-muted">
                                {{ $t->created_at->format('H:i d/m/Y') }}
                                @if($t->isDone() && $t->completed_at)
                                    <br><span class="text-success">✓ {{ $t->completed_at->format('H:i d/m') }}</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <form method="POST" action="{{ route('admin.work-tasks.toggle', $t) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    @if($t->isDone())
                                        <button class="btn btn-sm btn-outline-warning" title="Mở lại"><i class="fas fa-undo"></i> Mở lại</button>
                                    @else
                                        <button class="btn btn-sm btn-success" title="Đánh dấu hoàn thành"><i class="fas fa-check"></i> Xong</button>
                                    @endif
                                </form>
                                <form method="POST" action="{{ route('admin.work-tasks.destroy', $t) }}" class="d-inline"
                                    onsubmit="return confirm('Xoá mã {{ $t->code }}?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Xoá"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Không có công việc nào ở mục này.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $tasks->links('pagination::bootstrap-5') }}</div>

    <script>
        document.querySelectorAll('.copy-btn').forEach(function (b) {
            b.addEventListener('click', function () {
                navigator.clipboard.writeText(b.dataset.code).then(function () {
                    var old = b.innerHTML;
                    b.innerHTML = '<i class="fas fa-check text-success"></i>';
                    setTimeout(function () { b.innerHTML = old; }, 1200);
                });
            });
        });
    </script>
</div>
@endsection
