@extends('layouts.admin')

@section('title', 'Chỉnh sửa gói dịch vụ')
@section('page-title', 'Chỉnh sửa gói dịch vụ')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Chỉnh sửa: {{ $servicePackage->name }}
                </h5>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('admin.service-packages.update', $servicePackage) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">
                                Danh mục <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" 
                                    name="category_id" 
                                    required>
                                <option value="">Chọn danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $servicePackage->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">
                                Tên gói dịch vụ <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $servicePackage->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="account_type" class="form-label">
                                Loại tài khoản <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('account_type') is-invalid @enderror" 
                                    id="account_type" 
                                    name="account_type" 
                                    required>
                                <option value="">Chọn loại tài khoản</option>
                                <option value="Tài khoản chính chủ" {{ old('account_type', $servicePackage->account_type) === 'Tài khoản chính chủ' ? 'selected' : '' }}>
                                    Tài khoản chính chủ
                                </option>
                                <option value="Tài khoản cấp (dùng riêng)" {{ old('account_type', $servicePackage->account_type) === 'Tài khoản cấp (dùng riêng)' ? 'selected' : '' }}>
                                    Tài khoản cấp (dùng riêng)
                                </option>
                                <option value="Tài khoản add family" {{ old('account_type', $servicePackage->account_type) === 'Tài khoản add family' ? 'selected' : '' }}>
                                    Tài khoản add family
                                </option>
                                <option value="Tài khoản dùng chung" {{ old('account_type', $servicePackage->account_type) === 'Tài khoản dùng chung' ? 'selected' : '' }}>
                                    Tài khoản dùng chung
                                </option>
                            </select>
                            @error('account_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="default_duration_days" class="form-label">
                                Thời hạn mặc định (ngày) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('default_duration_days') is-invalid @enderror" 
                                   id="default_duration_days" 
                                   name="default_duration_days" 
                                   value="{{ old('default_duration_days', $servicePackage->default_duration_days) }}" 
                                   min="1"
                                   required>
                            @error('default_duration_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">
                                Giá bán (VNĐ) <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('price') is-invalid @enderror"
                                   id="price"
                                   name="price"
                                   value="{{ old('price', formatMoney($servicePackage->price)) }}"
                                   data-currency="VND"
                                   data-show-currency="false"
                                   placeholder="0"
                                   required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cost_price" class="form-label">
                                Giá nhập (VNĐ)
                            </label>
                            <input type="text"
                                   class="form-control @error('cost_price') is-invalid @enderror"
                                   id="cost_price"
                                   name="cost_price"
                                   value="{{ old('cost_price', $servicePackage->cost_price ? formatMoney($servicePackage->cost_price) : '') }}"
                                   data-currency="VND"
                                   data-show-currency="false"
                                   placeholder="0">
                            @error('cost_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Để trống nếu không muốn theo dõi lợi nhuận</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="is_active" class="form-label">Trạng thái</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $servicePackage->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Hoạt động
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description', $servicePackage->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.service-packages.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Quay lại
                        </a>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Cập nhật gói dịch vụ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
