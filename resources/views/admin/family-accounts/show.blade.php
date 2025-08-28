@extends('layouts.admin')

@section('title', 'Chi tiết Family Account')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-home me-2"></i>
                        {{ $familyAccount->family_name }}
                    </h1>
                    <p class="text-muted mb-0">
                        <code class="bg-light px-2 py-1 rounded">{{ $familyAccount->family_code }}</code>
                        •
                        <span class="badge bg-{{ $familyAccount->status === 'active' ? 'success' : 'warning' }} ms-2">
                            {{ ucfirst($familyAccount->status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.family-accounts.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>
                        Quay lại danh sách
                    </a>
                    <a href="{{ route('admin.family-accounts.edit', $familyAccount) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i>
                        Chỉnh sửa
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Family Info Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Thông tin cơ bản
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Tên Family:</small><br>
                            <strong>{{ $familyAccount->family_name }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Mã Family:</small><br>
                            <code class="bg-light px-2 py-1 rounded">{{ $familyAccount->family_code }}</code>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Email chủ:</small><br>
                            <a href="mailto:{{ $familyAccount->owner_email }}">{{ $familyAccount->owner_email }}</a>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Tên chủ gia đình:</small><br>
                            <strong>{{ $familyAccount->owner_name ?: 'Chưa cập nhật' }}</strong>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Gói dịch vụ:</small><br>
                            <span class="badge bg-info">{{ $familyAccount->servicePackage->name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Trạng thái:</small><br>
                            @php
                                $statusColors = [
                                    'active' => 'success',
                                    'expired' => 'warning',
                                    'suspended' => 'danger',
                                    'cancelled' => 'secondary',
                                ];
                                $statusLabels = [
                                    'active' => 'Hoạt động',
                                    'expired' => 'Hết hạn',
                                    'suspended' => 'Tạm ngưng',
                                    'cancelled' => 'Đã hủy',
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$familyAccount->status] ?? 'secondary' }}">
                                {{ $statusLabels[$familyAccount->status] ?? $familyAccount->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar me-2"></i>
                        Thông tin thời gian
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Ngày tạo:</small><br>
                            <strong>{{ $familyAccount->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Cập nhật cuối:</small><br>
                            <strong>{{ $familyAccount->updated_at->format('d/m/Y H:i') }}</strong>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Ngày hết hạn:</small><br>
                            @if($familyAccount->expires_at)
                                <strong class="{{ $familyAccount->expires_at->isPast() ? 'text-danger' : ($familyAccount->expires_at->diffInDays() <= 7 ? 'text-warning' : 'text-success') }}">
                                    {{ $familyAccount->expires_at->format('d/m/Y') }}
                                </strong>
                                <br>
                                <small class="text-muted">{{ $familyAccount->expires_at->diffForHumans() }}</small>
                            @else
                                <span class="text-muted">Chưa thiết lập</span>
                            @endif
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Thành viên:</small><br>
                            <span class="badge {{ $familyAccount->current_members >= $familyAccount->max_members ? 'bg-danger' : 'bg-success' }} fs-6">
                                {{ $familyAccount->current_members }}/{{ $familyAccount->max_members }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Members Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            Danh sách thành viên 
                            <span class="badge bg-secondary ms-2">{{ $familyAccount->members->count() }}</span>
                        </h5>
                        @if($familyAccount->current_members < $familyAccount->max_members)
                            <a href="{{ route('admin.family-accounts.add-member-form', $familyAccount) }}" class="btn btn-success">
                                <i class="fas fa-user-plus me-1"></i>
                                Thêm thành viên
                            </a>
                        @else
                            <span class="badge bg-warning">Family đã đầy</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($familyAccount->members->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Khách hàng</th>
                                        <th>Email thành viên</th>
                                        <th>Thời gian hiệu lực</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tham gia</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($familyAccount->members as $member)
                                        <tr>
                                            <td>
                                                <span class="fw-bold">#{{ $member->id }}</span>
                                            </td>
                                            <td>
                                                @if($member->customer)
                                                    <div>
                                                        <strong>{{ $member->customer->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $member->customer->email }}</small>
                                                        @if($member->customer->phone)
                                                            <br>
                                                            <small class="text-muted">{{ $member->customer->phone }}</small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">Khách hàng đã bị xóa</span>
                                                @endif
                                            </td>
                                            <td>{{ $member->member_email }}</td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <div class="mb-1">
                                                        <small class="text-muted">Bắt đầu:</small>
                                                        <span class="fw-bold">
                                                            {{ $member->start_date ? $member->start_date->format('d/m/Y') : 'Chưa xác định' }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted">Kết thúc:</small>
                                                        <span class="fw-bold {{ $member->end_date && $member->end_date->isPast() ? 'text-danger' : 'text-success' }}">
                                                            {{ $member->end_date ? $member->end_date->format('d/m/Y') : 'Không giới hạn' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($member->start_date && $member->end_date)
                                                    @php
                                                        $now = now();
                                                        $isActive = $now->between($member->start_date, $member->end_date);
                                                        $isExpired = $now->gt($member->end_date);
                                                        $isUpcoming = $now->lt($member->start_date);
                                                    @endphp
                                                    
                                                    @if($isActive)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check-circle me-1"></i>
                                                            Đang hoạt động
                                                        </span>
                                                    @elseif($isExpired)
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times-circle me-1"></i>
                                                            Đã hết hạn
                                                        </span>
                                                    @elseif($isUpcoming)
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-clock me-1"></i>
                                                            Sắp hoạt động
                                                        </span>
                                                    @endif
                                                @else
                                                    @php
                                                        $memberStatusColors = [
                                                            'active' => 'success',
                                                            'suspended' => 'warning',
                                                            'removed' => 'danger',
                                                        ];
                                                        $memberStatusLabels = [
                                                            'active' => 'Hoạt động',
                                                            'suspended' => 'Tạm ngưng',
                                                            'removed' => 'Đã xóa',
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $memberStatusColors[$member->status] ?? 'secondary' }}">
                                                        {{ $memberStatusLabels[$member->status] ?? $member->status }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($member->first_usage_at)
                                                    <div>
                                                        {{ $member->first_usage_at->format('d/m/Y') }}
                                                        <br>
                                                        <small class="text-muted">{{ $member->first_usage_at->diffForHumans() }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">{{ $member->created_at->format('d/m/Y') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($member->status === 'active')
                                                    <form method="POST" 
                                                          action="{{ route('admin.family-accounts.remove-member', [$familyAccount, $member]) }}" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Bạn có chắc muốn xóa thành viên này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i> Xóa
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có thành viên nào</h5>
                            <p class="text-muted mb-4">Family account này chưa có thành viên nào</p>
                            <a href="{{ route('admin.family-accounts.add-member-form', $familyAccount) }}" class="btn btn-success">
                                <i class="fas fa-user-plus me-1"></i>
                                Thêm thành viên đầu tiên
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

