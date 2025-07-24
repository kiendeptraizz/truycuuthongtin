@extends('layouts.admin')

@section('title', 'Chi tiết Family Account')
@section('page-title', $familyAccount->family_name)

@section('content')
<div class="container-fluid">
    <!-- Header Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $familyAccount->family_name }}</h4>
                    <div class="text-muted">
                        <i class="fas fa-code me-1"></i>{{ $familyAccount->family_code }}
                        <span class="mx-2">•</span>
                        <i class="fas fa-envelope me-1"></i>{{ $familyAccount->owner_email }}
                    </div>
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.family-accounts.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Quay lại
                    </a>
                    <a href="{{ route('admin.family-accounts.edit', $familyAccount) }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-1"></i>
                        Chỉnh sửa
                    </a>
                    @if($familyAccount->canAddMember())
                        <a href="{{ route('admin.family-accounts.add-member-form', $familyAccount) }}" class="btn btn-success">
                            <i class="fas fa-user-plus me-1"></i>
                            Thêm thành viên
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Thành viên</h6>
                            <h4 class="mb-0">{{ $memberStats['active_members'] }}/{{ $familyAccount->max_members }}</h4>
                        </div>
                        <div class="ms-2">
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-white" 
                             style="width: {{ $familyAccount->usage_percentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Slot Còn Lại</h6>
                            <h4 class="mb-0">{{ $familyAccount->available_slots }}</h4>
                        </div>
                        <div class="ms-2">
                            <i class="fas fa-plus-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Ngày Hết Hạn</h6>
                            <h4 class="mb-0">{{ $familyAccount->days_until_expiry }} ngày</h4>
                        </div>
                        <div class="ms-2">
                            <i class="fas fa-calendar-times fa-2x opacity-75"></i>
                        </div>
                    </div>
                    <small>{{ $familyAccount->expires_at->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-dark text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Gói Dịch Vụ</h6>
                            <h4 class="mb-0">{{ number_format($familyAccount->servicePackage->price) }}đ</h4>
                        </div>
                        <div class="ms-2">
                            <i class="fas fa-box fa-2x opacity-75"></i>
                        </div>
                    </div>
                    <small>{{ $familyAccount->servicePackage->name }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Family Information -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Thông tin Family
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Trạng thái</label>
                        <div>{!! $familyAccount->status_badge !!}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Gói dịch vụ</label>
                        <div>
                            <strong>{{ $familyAccount->servicePackage->name }}</strong><br>
                            <small class="text-muted">{{ $familyAccount->servicePackage->category->name ?? 'N/A' }}</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Chủ gia đình</label>
                        <div>
                            @if($familyAccount->owner_name)
                                <strong>{{ $familyAccount->owner_name }}</strong><br>
                            @endif
                            <small class="text-muted">{{ $familyAccount->owner_email }}</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Thời gian</label>
                        <div>
                            <small>
                                <strong>Kích hoạt:</strong> {{ $familyAccount->activated_at->format('d/m/Y H:i') }}<br>
                                <strong>Hết hạn:</strong> {{ $familyAccount->expires_at->format('d/m/Y H:i') }}<br>
                                <strong>Tạo:</strong> {{ $familyAccount->created_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>

                    @if($familyAccount->family_notes)
                        <div class="mb-3">
                            <label class="form-label text-muted">Ghi chú</label>
                            <div class="text-sm">{{ $familyAccount->family_notes }}</div>
                        </div>
                    @endif

                    @if($familyAccount->createdBy)
                        <div class="mb-0">
                            <label class="form-label text-muted">Quản lý</label>
                            <div>
                                <small>
                                    <strong>Tạo bởi:</strong> {{ $familyAccount->createdBy->name ?? 'N/A' }}<br>
                                    @if($familyAccount->managedBy && $familyAccount->managedBy->id !== $familyAccount->createdBy->id)
                                        <strong>Quản lý bởi:</strong> {{ $familyAccount->managedBy->name ?? 'N/A' }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Active Members -->
        <div class="col-md-8 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            Thành viên đang hoạt động ({{ $activeMembers->count() }})
                        </h6>
                        @if($familyAccount->canAddMember())
                            <a href="{{ route('admin.family-accounts.add-member-form', $familyAccount) }}" 
                               class="btn btn-sm btn-success">
                                <i class="fas fa-user-plus me-1"></i>
                                Thêm thành viên
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($activeMembers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Thành viên</th>
                                        <th>Vai trò</th>
                                        <th>Ngày thêm</th>
                                        <th>Sử dụng</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeMembers as $member)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $member->customer->name }}</strong><br>
                                                        <small class="text-muted">{{ $member->customer->customer_code }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{!! $member->role_badge !!}</td>
                                            <td>
                                                <small>
                                                    {{ $member->created_at->format('d/m/Y') }}<br>
                                                    <span class="text-muted">{{ $member->days_in_family }} ngày</span>
                                                </small>
                                            </td>
                                            <td>{!! $member->usage_badge !!}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-primary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editMemberModal{{ $member->id }}"
                                                            title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#removeMemberModal{{ $member->id }}"
                                                            title="Xóa khỏi family">
                                                        <i class="fas fa-user-minus"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có thành viên nào trong family.</p>
                            @if($familyAccount->canAddMember())
                                <a href="{{ route('admin.family-accounts.add-member-form', $familyAccount) }}" 
                                   class="btn btn-success">
                                    <i class="fas fa-user-plus me-1"></i>
                                    Thêm thành viên đầu tiên
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Inactive/Removed Members -->
    @if($inactiveMembers->count() > 0 || $removedMembers->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-user-slash me-2"></i>
                            Thành viên không hoạt động / đã xóa
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Thành viên</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày thêm</th>
                                        <th>Thay đổi cuối</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inactiveMembers->concat($removedMembers) as $member)
                                        <tr class="text-muted">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-light text-dark rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $member->customer->name }}</strong><br>
                                                        <small>{{ $member->customer->customer_code }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{!! $member->status_badge !!}</td>
                                            <td>
                                                <small>{{ $member->created_at->format('d/m/Y') }}</small>
                                            </td>
                                            <td>
                                                <small>
                                                    @if($member->removed_at)
                                                        {{ $member->removed_at->format('d/m/Y') }}
                                                    @else
                                                        {{ $member->updated_at->format('d/m/Y') }}
                                                    @endif
                                                </small>
                                            </td>
                                            <td>
                                                <small>{{ $member->member_notes ?: 'Không có ghi chú' }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Modals for Edit/Remove Members -->
@foreach($activeMembers as $member)
    <!-- Edit Member Modal -->
    <div class="modal fade" id="editMemberModal{{ $member->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.family-accounts.update-member', [$familyAccount, $member]) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Chỉnh sửa thành viên: {{ $member->customer->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status" required>
                                <option value="active" {{ $member->status === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ $member->status === 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vai trò</label>
                            <select class="form-select" name="member_role" required>
                                <option value="member" {{ $member->member_role === 'member' ? 'selected' : '' }}>Thành viên</option>
                                <option value="admin" {{ $member->member_role === 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea class="form-control" name="member_notes" rows="3">{{ $member->member_notes }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Remove Member Modal -->
    <div class="modal fade" id="removeMemberModal{{ $member->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.family-accounts.remove-member', [$familyAccount, $member]) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Xóa thành viên: {{ $member->customer->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Bạn có chắc chắn muốn xóa <strong>{{ $member->customer->name }}</strong> khỏi family này?
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lý do xóa</label>
                            <textarea class="form-control" name="reason" rows="3" placeholder="Nhập lý do xóa thành viên..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">Xóa thành viên</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}

.progress {
    background-color: rgba(255,255,255,0.2);
}

.card-body .table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
@endpush
