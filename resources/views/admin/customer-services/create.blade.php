@extends('layouts.admin')

@section('title', 'Gán dịch vụ mới')
@section('page-title', 'Gán dịch vụ mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>
                    Gán dịch vụ cho khách hàng
                </h5>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('admin.customer-services.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_id" class="form-label">
                                Khách hàng <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" 
                                    id="customer_id" 
                                    name="customer_id" 
                                    required>
                                <option value="">Chọn khách hàng</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" 
                                            {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->customer_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="service_package_id" class="form-label">
                                Gói dịch vụ <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('service_package_id') is-invalid @enderror" 
                                    id="service_package_id" 
                                    name="service_package_id" 
                                    required>
                                <option value="">Chọn gói dịch vụ</option>
                                @foreach($servicePackages->groupBy('category.name') as $categoryName => $packages)
                                    <optgroup label="{{ $categoryName }}">
                                        @foreach($packages as $package)
                                            <option value="{{ $package->id }}" 
                                                    data-price="{{ $package->price }}"
                                                    data-duration="{{ $package->default_duration_days }}"
                                                    {{ old('service_package_id') == $package->id ? 'selected' : '' }}>
                                                {{ $package->name }} - {{ $package->account_type }} 
                                                ({{ number_format($package->price) }}đ)
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('service_package_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="login_email" class="form-label">
                                Email đăng nhập <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('login_email') is-invalid @enderror" 
                                   id="login_email" 
                                   name="login_email" 
                                   value="{{ old('login_email') }}" 
                                   required>
                            @error('login_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="login_password" class="form-label">Mật khẩu</label>
                            <input type="text" 
                                   class="form-control @error('login_password') is-invalid @enderror" 
                                   id="login_password" 
                                   name="login_password" 
                                   value="{{ old('login_password') }}">
                            @error('login_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Để trống nếu không muốn lưu mật khẩu</div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="activated_at" class="form-label">
                                Ngày kích hoạt <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('activated_at') is-invalid @enderror" 
                                   id="activated_at" 
                                   name="activated_at" 
                                   value="{{ old('activated_at', now()->format('Y-m-d')) }}" 
                                   required>
                            @error('activated_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="expires_at" class="form-label">
                                Ngày hết hạn <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('expires_at') is-invalid @enderror" 
                                   id="expires_at" 
                                   name="expires_at" 
                                   value="{{ old('expires_at', now()->addDays(30)->format('Y-m-d')) }}" 
                                   required>
                            @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">
                                Trạng thái <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>
                                    Hoạt động
                                </option>
                                <option value="expired" {{ old('status') === 'expired' ? 'selected' : '' }}>
                                    Hết hạn
                                </option>
                                <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>
                                    Đã hủy
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="internal_notes" class="form-label">Ghi chú nội bộ</label>
                            <textarea class="form-control @error('internal_notes') is-invalid @enderror" 
                                      id="internal_notes" 
                                      name="internal_notes" 
                                      rows="3">{{ old('internal_notes') }}</textarea>
                            @error('internal_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.customer-services.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Quay lại
                        </a>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Gán dịch vụ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceSelect = document.getElementById('service_package_id');
    const activatedInput = document.getElementById('activated_at');
    const expiresInput = document.getElementById('expires_at');
    
    serviceSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const duration = parseInt(selectedOption.dataset.duration) || 30;
            const activatedDate = new Date(activatedInput.value);
            const expiresDate = new Date(activatedDate);
            expiresDate.setDate(expiresDate.getDate() + duration);
            
            expiresInput.value = expiresDate.toISOString().split('T')[0];
        }
    });
    
    activatedInput.addEventListener('change', function() {
        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
        if (selectedOption.value) {
            const duration = parseInt(selectedOption.dataset.duration) || 30;
            const activatedDate = new Date(this.value);
            const expiresDate = new Date(activatedDate);
            expiresDate.setDate(expiresDate.getDate() + duration);
            
            expiresInput.value = expiresDate.toISOString().split('T')[0];
        }
    });
});
</script>
@endpush
@endsection
