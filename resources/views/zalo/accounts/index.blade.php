@extends('layouts.admin')

@section('title', 'Quản lý Tài khoản Zalo')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Quản lý Tài khoản Zalo</h2>
            <p class="text-muted mb-0">Quản lý các tài khoản Zalo để gửi tin nhắn</p>
        </div>
        <a href="{{ route('admin.zalo.accounts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm tài khoản
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tên tài khoản</th>
                            <th>Email/SĐT</th>
                            <th>Trạng thái</th>
                            <th>Giới hạn/ngày</th>
                            <th>Đã gửi hôm nay</th>
                            <th>Còn lại</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                        <tr>
                            <td>
                                <strong>{{ $account->account_name }}</strong>
                            </td>
                            <td>{{ $account->email_or_phone }}</td>
                            <td>
                                @php
                                $statusColors = [
                                'active' => 'success',
                                'inactive' => 'secondary',
                                'blocked' => 'danger',
                                'error' => 'warning'
                                ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$account->status] ?? 'secondary' }}">
                                    {{ ucfirst($account->status) }}
                                </span>
                            </td>
                            <td>{{ number_format($account->daily_message_limit) }}</td>
                            <td>
                                <span class="badge bg-{{ $account->messages_sent_today >= $account->daily_message_limit ? 'danger' : 'info' }}">
                                    {{ number_format($account->messages_sent_today) }}
                                </span>
                            </td>
                            <td>
                                <div class="progress" style="width: 100px; height: 20px;">
                                    @php
                                    $percentage = $account->daily_message_limit > 0
                                    ? ($account->remaining_messages / $account->daily_message_limit) * 100
                                    : 0;
                                    @endphp
                                    <div class="progress-bar bg-{{ $percentage > 50 ? 'success' : ($percentage > 20 ? 'warning' : 'danger') }}"
                                        style="width: {{ $percentage }}%">
                                        {{ $account->remaining_messages }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.zalo.accounts.show', $account) }}" class="btn btn-outline-info" title="Chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.zalo.accounts.edit', $account) }}" class="btn btn-outline-primary" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.zalo.accounts.reset-counter', $account) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-warning" title="Reset bộ đếm">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.zalo.accounts.destroy', $account) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa tài khoản này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Chưa có tài khoản Zalo nào. <a href="{{ route('admin.zalo.accounts.create') }}">Thêm tài khoản đầu tiên</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($accounts->hasPages())
            <div class="mt-3">
                {{ $accounts->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection