@extends('layouts.admin')

@section('title', 'Thêm khách hàng mới')
@section('page-title', 'Thêm khách hàng mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-10">
        <div class="form-container">
            <div class="form-header">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper me-3" style="width: 50px; height: 50px; background: var(--primary-gradient); border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-plus fa-lg text-white"></i>
                    </div>
                    <div>
                        <h1 class="form-title">Thêm khách hàng mới</h1>
                        <p class="form-subtitle">Nhập thông tin để tạo khách hàng mới trong hệ thống</p>
                    </div>
                </div>
            </div>
            
            <form method="POST" action="{{ route('admin.customers.store') }}">
                @csrf

                <!-- Hidden fields để giữ thông tin trang hiện tại -->
                @if(isset($returnPage))
                    <input type="hidden" name="return_page" value="{{ $returnPage }}">
                @endif
                @if(isset($returnSearch))
                    <input type="hidden" name="return_search" value="{{ $returnSearch }}">
                @endif
                
                <div class="form-body">
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-user text-primary"></i>
                            Thông tin cơ bản
                        </h3>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name" class="form-label">
                                        Tên khách hàng <span class="required">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="Nhập tên khách hàng"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-address-book text-info"></i>
                            Thông tin liên hệ
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email') }}"
                                               placeholder="example@email.com">
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-phone"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" 
                                               name="phone" 
                                               value="{{ old('phone') }}"
                                               placeholder="0123456789">
                                    </div>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-sticky-note text-warning"></i>
                            Ghi chú
                        </h3>
                        
                        <div class="form-group">
                            <label for="notes" class="form-label">Ghi chú</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="4"
                                      placeholder="Nhập ghi chú về khách hàng (tùy chọn)">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Bạn có thể thêm thông tin bổ sung về khách hàng</div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.customers.index', ['page' => $returnPage ?? 1, 'search' => $returnSearch ?? '']) }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Lưu khách hàng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
