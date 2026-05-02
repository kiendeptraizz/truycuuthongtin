@extends('layouts.admin')

@section('title', 'Lịch sử thay đổi dịch vụ #' . $customerService->id)
@section('page-title', 'Lịch sử thay đổi')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-history text-info me-2"></i>
                Lịch sử thay đổi dịch vụ
            </h4>
            <div class="text-muted small">
                @if($customerService->order_code)
                    <code class="text-success">{{ $customerService->order_code }}</code> ·
                @endif
                {{ $customerService->servicePackage->name ?? '?' }} ·
                KH: {{ $customerService->customer->name ?? '?' }}
            </div>
        </div>
        <a href="{{ route('admin.customer-services.show', $customerService) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Quay lại
        </a>
    </div>

    @if($audits->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Chưa có audit log cho dịch vụ này. (Audit chỉ ghi từ thời điểm bật observer trở đi.)
        </div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 140px;">Thời gian</th>
                            <th style="width: 90px;">Sự kiện</th>
                            <th style="width: 200px;">Người / nguồn</th>
                            <th>Thay đổi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($audits as $audit)
                        @php
                            $eventBadge = match($audit->event) {
                                'created' => 'bg-success',
                                'updated' => 'bg-warning text-dark',
                                'deleted' => 'bg-danger',
                                'restored' => 'bg-info',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <tr>
                            <td>
                                <small>
                                    {{ $audit->created_at->format('H:i:s') }}<br>
                                    <span class="text-muted">{{ $audit->created_at->format('d/m/Y') }}</span>
                                </small>
                            </td>
                            <td>
                                <span class="badge {{ $eventBadge }}">{{ $audit->event }}</span>
                            </td>
                            <td>
                                <small>
                                    <strong>{{ $audit->actor_label ?: 'Hệ thống' }}</strong>
                                    @if($audit->actor_type)
                                        <br><span class="text-muted">{{ $audit->actor_type }}</span>
                                    @endif
                                    @if($audit->ip_address)
                                        <br><code class="text-muted">{{ $audit->ip_address }}</code>
                                    @endif
                                </small>
                            </td>
                            <td>
                                @if($audit->event === 'updated' && $audit->changed_fields)
                                    <table class="table table-sm mb-0" style="font-size: 0.85rem;">
                                        <thead>
                                            <tr class="text-muted">
                                                <th style="width: 25%;">Field</th>
                                                <th style="width: 37.5%;">Trước</th>
                                                <th style="width: 37.5%;">Sau</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($audit->changed_fields as $field)
                                            <tr>
                                                <td><code>{{ $field }}</code></td>
                                                <td class="text-muted">{{ \Illuminate\Support\Str::limit((string) data_get($audit->old_values, $field, '—'), 80) }}</td>
                                                <td class="text-success">{{ \Illuminate\Support\Str::limit((string) data_get($audit->new_values, $field, '—'), 80) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @elseif($audit->event === 'created')
                                    <span class="text-muted small">Khởi tạo dịch vụ mới</span>
                                @elseif($audit->event === 'deleted')
                                    <span class="text-danger small">Đã xoá (soft delete)</span>
                                @elseif($audit->event === 'restored')
                                    <span class="text-info small">Khôi phục từ thùng rác</span>
                                @endif
                                @if($audit->note)
                                    <br><small class="text-muted"><i class="fas fa-comment me-1"></i>{{ $audit->note }}</small>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $audits->links() }}
        </div>
    @endif
</div>
@endsection
