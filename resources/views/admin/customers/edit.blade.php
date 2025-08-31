@extends('layouts.admin')

@section('title', 'Chỉnh sửa khách hàng')
@section('page-title', 'Chỉnh sửa khách hàng')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i>
                    Chỉnh sửa thông tin khách hàng
                </h5>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('admin.customers.update', $customer) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="customer_code" class="form-label">Mã khách hàng</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="customer_code" 
                                   value="{{ $customer->customer_code }}" 
                                   readonly>
                            <div class="form-text">Mã khách hàng không thể thay đổi</div>
                        </div>

                        <!-- Collaborator Status -->
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="is_collaborator" 
                                       name="is_collaborator"
                                       value="1"
                                       {{ old('is_collaborator', $customer->is_collaborator) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_collaborator">
                                    <i class="fas fa-handshake text-primary"></i>
                                    Đây là cộng tác viên
                                </label>
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle text-info"></i>
                                Trạng thái cộng tác viên (mã {{ $customer->customer_code }} sẽ không thay đổi)
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">
                                Tên khách hàng <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $customer->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $customer->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $customer->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Quay lại
                        </a>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Cập nhật thông tin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
