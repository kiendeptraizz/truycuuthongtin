@extends('layouts.admin')

@section('title', 'Thêm thành viên vào Family')
@section('page-title', 'Thêm thành viên vào Family')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-auto">
                            <h5 class="mb-0">
                                <i class="fas fa-user-plus me-2"></i>
                                Thêm thành viên vào: <strong>{{ $familyAccount->family_name }}</strong>
                            </h5>
                            <small class="text-muted">
                                Slot còn lại: {{ $familyAccount->available_slots }}/{{ $familyAccount->max_members }}
                            </small>
                        </div>
                        <div>
                            <a href="{{ route('admin.family-accounts.show', $familyAccount) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Quay lại
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if($familyAccount->available_slots <= 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Family này đã đầy. Không thể thêm thành viên mới.
                        </div>
                    @elseif($availableCustomers->count() === 0)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Không có khách hàng nào có thể thêm vào family này. Tất cả khách hàng đã là thành viên của family khác hoặc đã có trong family này.
                        </div>
                    @else
                        <form method="POST" action="{{ route('admin.family-accounts.add-member', $familyAccount) }}">
                            @csrf

                            <div class="row">
                                <!-- Customer Selection -->
                                <div class="col-md-12 mb-4">
                                    <label for="customer_id" class="form-label">
                                        <i class="fas fa-user me-1"></i>
                                        Chọn Khách Hàng <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('customer_id') is-invalid @enderror" 
                                            id="customer_id" 
                                            name="customer_id" 
                                            required>
                                        <option value="">Chọn khách hàng để thêm vào family</option>
                                        @foreach($availableCustomers as $customer)
                                            <option value="{{ $customer->id }}" 
                                                    data-name="{{ $customer->name }}"
                                                    data-email="{{ $customer->email }}"
                                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} ({{ $customer->customer_code }})
                                                @if($customer->email)
                                                    - {{ $customer->email }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Chỉ hiển thị khách hàng chưa tham gia family nào khác
                                    </small>
                                </div>



                                <!-- Member Email -->
                                <div class="col-md-12 mb-3">
                                    <label for="member_email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>
                                        Email Thành Viên
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('member_email') is-invalid @enderror" 
                                           id="member_email" 
                                           name="member_email" 
                                           value="{{ old('member_email') }}" 
                                           placeholder="email@example.com">
                                    @error('member_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Để trống sẽ sử dụng email từ thông tin khách hàng
                                    </small>
                                </div>

                                <!-- Member Role -->
                                <div class="col-md-12 mb-3">
                                    <label for="member_role" class="form-label">
                                        <i class="fas fa-user-shield me-1"></i>
                                        Vai Trò <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('member_role') is-invalid @enderror" 
                                            id="member_role" 
                                            name="member_role" 
                                            required>
                                        <option value="">Chọn vai trò</option>
                                        <option value="member" {{ old('member_role') === 'member' ? 'selected' : '' }}>
                                            <i class="fas fa-user"></i> Thành viên
                                        </option>
                                        <option value="admin" {{ old('member_role') === 'admin' ? 'selected' : '' }}>
                                            <i class="fas fa-user-shield"></i> Quản trị viên
                                        </option>
                                    </select>
                                    @error('member_role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Quản trị viên có thể quản lý các thành viên khác trong family
                                    </small>
                                </div>

                                <!-- Member Notes -->
                                <div class="col-md-12 mb-3">
                                    <label for="member_notes" class="form-label">
                                        <i class="fas fa-sticky-note me-1"></i>
                                        Ghi Chú
                                    </label>
                                    <textarea class="form-control @error('member_notes') is-invalid @enderror" 
                                              id="member_notes" 
                                              name="member_notes" 
                                              rows="3" 
                                              placeholder="Ghi chú về thành viên này...">{{ old('member_notes') }}</textarea>
                                    @error('member_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Thông tin bổ sung về thành viên
                                    </small>
                                </div>
                            </div>

                            <!-- Family Info Summary -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Thông tin Family
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <small class="text-muted">Tên Family:</small><br>
                                                    <strong>{{ $familyAccount->family_name }}</strong>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Gói dịch vụ:</small><br>
                                                    <strong>{{ $familyAccount->servicePackage->name }}</strong>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Thành viên hiện tại:</small><br>
                                                    <strong>{{ $familyAccount->current_members }}/{{ $familyAccount->max_members }}</strong>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Trạng thái:</small><br>
                                                    {!! $familyAccount->status_badge !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('admin.family-accounts.show', $familyAccount) }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-1"></i>
                                            Hủy
                                        </a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-user-plus me-1"></i>
                                            Thêm Thành Viên
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif

                    <!-- Available Customers Info -->
                    @if($availableCustomers->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-users me-2"></i>
                                            Khách hàng có thể thêm ({{ $availableCustomers->count() }})
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($availableCustomers->take(12) as $customer)
                                                <div class="col-md-4 col-sm-6 mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                        <div>
                                                            <small>
                                                                <strong>{{ $customer->name }}</strong><br>
                                                                <span class="text-muted">{{ $customer->customer_code }}</span>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if($availableCustomers->count() > 12)
                                                <div class="col-12">
                                                    <small class="text-muted">
                                                        ... và {{ $availableCustomers->count() - 12 }} khách hàng khác
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerSelect = document.getElementById('customer_id');

    const memberEmailInput = document.getElementById('member_email');
    
    // Auto-fill member email when customer is selected
    customerSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (selectedOption.value) {
            // Auto-fill member email if empty
            if (!memberEmailInput.value && selectedOption.dataset.email) {
                memberEmailInput.value = selectedOption.dataset.email;
            }
        } else {
            // Clear fields if no customer selected
            memberEmailInput.value = '';
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}

.card-body .row .col-md-3 {
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .card-body .row .col-md-3 {
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush
