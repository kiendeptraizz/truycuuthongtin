@extends('layouts.admin')

@section('title', 'Tạo Family Account')
@section('page-title', 'Tạo Family Account')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-auto">
                            <h5 class="mb-0">
                                <i class="fas fa-users me-2"></i>
                                Tạo Family Account Mới
                            </h5>
                            <small class="text-muted">Tạo tài khoản gia đình cho nhiều thành viên</small>
                        </div>
                        <div>
                            <a href="{{ route('admin.family-accounts.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Quay lại
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.family-accounts.store') }}">
                        @csrf

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
                                       value="{{ old('family_name') }}" 
                                       placeholder="Ví dụ: Family Nguyễn Văn A"
                                       required>
                                @error('family_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Tên để nhận diện family account</small>
                            </div>

                            <!-- Service Package -->
                            <div class="col-md-6 mb-3">
                                <label for="service_package_id" class="form-label">
                                    <i class="fas fa-box me-1"></i>
                                    Gói Dịch Vụ <span class="text-danger">*</span>
                                    <small class="text-muted ms-2">(Chỉ gói Family)</small>
                                </label>
                                
                                <x-service-package-selector 
                                    :service-packages="$servicePackages"
                                    :account-type-priority="$accountTypePriority"
                                    name="service_package_id"
                                    id="service_package_id"
                                    :required="true"
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
                                       value="{{ old('owner_email') }}" 
                                       placeholder="owner@example.com"
                                       required>
                                @error('owner_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Email đăng nhập chính của family account</small>
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
                                       value="{{ old('owner_name') }}" 
                                       placeholder="Nguyễn Văn A">
                                @error('owner_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Tên thật của chủ gia đình</small>
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
                                    <option value="">Chọn số thành viên</option>
                                    @for($i = 2; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ old('max_members') == $i ? 'selected' : '' }}>
                                            {{ $i }} thành viên
                                        </option>
                                    @endfor
                                    @for($i = 15; $i <= 20; $i += 5)
                                        <option value="{{ $i }}" {{ old('max_members') == $i ? 'selected' : '' }}>
                                            {{ $i }} thành viên
                                        </option>
                                    @endfor
                                </select>
                                @error('max_members')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Số lượng thành viên tối đa cho phép trong family</small>
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
                                       value="{{ old('activated_at', now()->format('Y-m-d\TH:i')) }}" 
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
                                       value="{{ old('expires_at', now()->addMonth()->format('Y-m-d\TH:i')) }}" 
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
                                          rows="3" 
                                          placeholder="Ghi chú về family account này...">{{ old('family_notes') }}</textarea>
                                @error('family_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Thông tin bổ sung về family account</small>
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
                                          rows="3" 
                                          placeholder="Ghi chú nội bộ (chỉ admin thấy)...">{{ old('internal_notes') }}</textarea>
                                @error('internal_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Ghi chú chỉ dành cho admin</small>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.family-accounts.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        Hủy
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Tạo Family Account
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceSelect = document.getElementById('service_package_id');
    const activatedInput = document.getElementById('activated_at');
    const expiresInput = document.getElementById('expires_at');

    
    // Auto-calculate expires_at based on activated_at
    activatedInput.addEventListener('change', function() {
        if (this.value) {
            const activatedDate = new Date(this.value);
            const expiresDate = new Date(activatedDate);
            expiresDate.setMonth(expiresDate.getMonth() + 1);
            
            expiresInput.value = expiresDate.toISOString().slice(0, 16);
        }
    });
    

});
</script>
@endpush
