@extends('layouts.admin')

@section('title', 'Tạo Family Account')

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
                                       placeholder="email@example.com"
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
                                       placeholder="Tên đầy đủ của chủ gia đình">
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
                                <input type="number" 
                                       class="form-control @error('max_members') is-invalid @enderror" 
                                       id="max_members" 
                                       name="max_members" 
                                       value="{{ old('max_members', 5) }}" 
                                       min="1" 
                                       max="20"
                                       required>
                                @error('max_members')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Từ 1 đến 20 thành viên</small>
                            </div>

                            <!-- Expires At -->
                            <div class="col-md-6 mb-3">
                                <label for="expires_at" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>
                                    Ngày Hết Hạn <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('expires_at') is-invalid @enderror" 
                                       id="expires_at" 
                                       name="expires_at" 
                                       value="{{ old('expires_at', now()->addDays(30)->format('Y-m-d')) }}"
                                       min="{{ now()->addDay()->format('Y-m-d') }}"
                                       required>
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="family_notes" class="form-label">
                                    <i class="fas fa-sticky-note me-1"></i>
                                    Ghi Chú Family
                                </label>
                                <textarea class="form-control @error('family_notes') is-invalid @enderror" 
                                          id="family_notes" 
                                          name="family_notes" 
                                          rows="3"
                                          placeholder="Ghi chú dành cho family và thành viên...">{{ old('family_notes') }}</textarea>
                                @error('family_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Ghi chú hiển thị cho thành viên family</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="internal_notes" class="form-label">
                                    <i class="fas fa-lock me-1"></i>
                                    Ghi Chú Nội Bộ
                                </label>
                                <textarea class="form-control @error('internal_notes') is-invalid @enderror" 
                                          id="internal_notes" 
                                          name="internal_notes" 
                                          rows="3"
                                          placeholder="Ghi chú nội bộ dành cho admin...">{{ old('internal_notes') }}</textarea>
                                @error('internal_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Ghi chú chỉ admin có thể xem</small>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate family name based on owner email
    const ownerEmailInput = document.getElementById('owner_email');
    const familyNameInput = document.getElementById('family_name');
    
    ownerEmailInput.addEventListener('blur', function() {
        if (!familyNameInput.value && this.value) {
            const emailPrefix = this.value.split('@')[0];
            const capitalizedPrefix = emailPrefix.charAt(0).toUpperCase() + emailPrefix.slice(1);
            familyNameInput.value = `Family ${capitalizedPrefix}`;
        }
    });

    // Auto-calculate expiry date based on service package selection
    const servicePackageSelect = document.getElementById('service_package_id');
    const expiresAtInput = document.getElementById('expires_at');
    
    servicePackageSelect.addEventListener('change', function() {
        if (this.value) {
            // Get selected option's data attributes if available
            const selectedOption = this.options[this.selectedIndex];
            const duration = selectedOption.dataset.duration || 30; // Default 30 days
            
            const futureDate = new Date();
            futureDate.setDate(futureDate.getDate() + parseInt(duration));
            expiresAtInput.value = futureDate.toISOString().split('T')[0];
        }
    });
});
</script>
@endpush
@endsection
