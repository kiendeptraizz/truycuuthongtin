@extends('layouts.admin')

@section('title', 'Chỉnh sửa Family Account')
@section('page-title', 'Chỉnh sửa Family Account')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-auto">
                            <h5 class="mb-0">
                                <i class="fas fa-edit me-2"></i>
                                Chỉnh sửa: {{ $familyAccount->family_name }}
                            </h5>
                            <small class="text-muted">{{ $familyAccount->family_code }}</small>
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
                    <form method="POST" action="{{ route('admin.family-accounts.update', $familyAccount) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Family Basic Info -->
                            <div class="col-md-6 mb-3">
                                <label for="family_name" class="form-label">
                                    <i class="fas fa-home me-1"></i>
                                    Tên Family <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('family_name') is-invalid @enderror" 
                                       id="family_name" 
                                       name="family_name" 
                                       value="{{ old('family_name', $familyAccount->family_name) }}" 
                                       required>
                                @error('family_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Service Package -->
                            <div class="col-md-6 mb-3">
                                <label for="service_package_id" class="form-label">
                                    <i class="fas fa-box me-1"></i>
                                    Gói Dịch Vụ <span class="text-danger">*</span>
                                </label>
                                
                                <x-service-package-selector 
                                    :service-packages="$servicePackages"
                                    :account-type-priority="$accountTypePriority"
                                    name="service_package_id"
                                    id="service_package_id"
                                    :required="true"
                                    :selected="old('service_package_id', $familyAccount->service_package_id)"
                                    placeholder="Chọn gói dịch vụ family..."
                                />
                            </div>

                            <!-- Owner Email -->
                            <div class="col-md-6 mb-3">
                                <label for="owner_email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>
                                    Email Chủ Gia Đình <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control @error('owner_email') is-invalid @enderror" 
                                       id="owner_email" 
                                       name="owner_email" 
                                       value="{{ old('owner_email', $familyAccount->owner_email) }}" 
                                       required>
                                @error('owner_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>



                            <!-- Owner Name -->
                            <div class="col-md-6 mb-3">
                                <label for="owner_name" class="form-label">
                                    <i class="fas fa-user me-1"></i>
                                    Tên Chủ Gia Đình
                                </label>
                                <input type="text" 
                                       class="form-control @error('owner_name') is-invalid @enderror" 
                                       id="owner_name" 
                                       name="owner_name" 
                                       value="{{ old('owner_name', $familyAccount->owner_name) }}">
                                @error('owner_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Max Members -->
                            <div class="col-md-6 mb-3">
                                <label for="max_members" class="form-label">
                                    <i class="fas fa-users me-1"></i>
                                    Số Thành Viên Tối Đa <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('max_members') is-invalid @enderror" 
                                        id="max_members" 
                                        name="max_members" 
                                        required>
                                    @for($i = 2; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ old('max_members', $familyAccount->max_members) == $i ? 'selected' : '' }}>
                                            {{ $i }} thành viên
                                        </option>
                                    @endfor
                                    @for($i = 15; $i <= 20; $i += 5)
                                        <option value="{{ $i }}" {{ old('max_members', $familyAccount->max_members) == $i ? 'selected' : '' }}>
                                            {{ $i }} thành viên
                                        </option>
                                    @endfor
                                </select>
                                @error('max_members')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Hiện có {{ $familyAccount->current_members }} thành viên
                                </small>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">
                                    <i class="fas fa-toggle-on me-1"></i>
                                    Trạng Thái <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status" 
                                        required>
                                    <option value="active" {{ old('status', $familyAccount->status) === 'active' ? 'selected' : '' }}>
                                        Hoạt động
                                    </option>
                                    <option value="expired" {{ old('status', $familyAccount->status) === 'expired' ? 'selected' : '' }}>
                                        Hết hạn
                                    </option>
                                    <option value="suspended" {{ old('status', $familyAccount->status) === 'suspended' ? 'selected' : '' }}>
                                        Tạm dừng
                                    </option>
                                    <option value="cancelled" {{ old('status', $familyAccount->status) === 'cancelled' ? 'selected' : '' }}>
                                        Đã hủy
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Activated At -->
                            <div class="col-md-6 mb-3">
                                <label for="activated_at" class="form-label">
                                    <i class="fas fa-calendar-check me-1"></i>
                                    Ngày Kích Hoạt <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" 
                                       class="form-control @error('activated_at') is-invalid @enderror" 
                                       id="activated_at" 
                                       name="activated_at" 
                                       value="{{ old('activated_at', $familyAccount->activated_at->format('Y-m-d\TH:i')) }}" 
                                       required>
                                @error('activated_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Expires At -->
                            <div class="col-md-6 mb-3">
                                <label for="expires_at" class="form-label">
                                    <i class="fas fa-calendar-times me-1"></i>
                                    Ngày Hết Hạn <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" 
                                       class="form-control @error('expires_at') is-invalid @enderror" 
                                       id="expires_at" 
                                       name="expires_at" 
                                       value="{{ old('expires_at', $familyAccount->expires_at->format('Y-m-d\TH:i')) }}" 
                                       required>
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>



                            <!-- Family Notes -->
                            <div class="col-md-12 mb-3">
                                <label for="family_notes" class="form-label">
                                    <i class="fas fa-sticky-note me-1"></i>
                                    Ghi Chú Family
                                </label>
                                <textarea class="form-control @error('family_notes') is-invalid @enderror" 
                                          id="family_notes" 
                                          name="family_notes" 
                                          rows="3">{{ old('family_notes', $familyAccount->family_notes) }}</textarea>
                                @error('family_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Internal Notes -->
                            <div class="col-md-12 mb-3">
                                <label for="internal_notes" class="form-label">
                                    <i class="fas fa-eye-slash me-1"></i>
                                    Ghi Chú Nội Bộ
                                </label>
                                <textarea class="form-control @error('internal_notes') is-invalid @enderror" 
                                          id="internal_notes" 
                                          name="internal_notes" 
                                          rows="3">{{ old('internal_notes', $familyAccount->internal_notes) }}</textarea>
                                @error('internal_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Status Info -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Thông tin hiện tại
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <small class="text-muted">Mã Family:</small><br>
                                                <strong>{{ $familyAccount->family_code }}</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Thành viên:</small><br>
                                                <strong>{{ $familyAccount->current_members }}/{{ $familyAccount->max_members }}</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Tạo bởi:</small><br>
                                                <strong>{{ $familyAccount->createdBy->name ?? 'N/A' }}</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Ngày tạo:</small><br>
                                                <strong>{{ $familyAccount->created_at->format('d/m/Y H:i') }}</strong>
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
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Cập nhật Family Account
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
