@extends('layouts.admin')

@section('title', 'Thêm Lead mới')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user-plus text-primary me-2"></i>
                        Thêm Lead mới
                    </h1>
                    <p class="text-muted mb-0">Tạo khách hàng tiềm năng mới</p>
                </div>
                <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>

            <!-- Form -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin cơ bản</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.leads.store') }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone') }}" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email') }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="source" class="form-label">Nguồn <span class="text-danger">*</span></label>
                                        <select class="form-select @error('source') is-invalid @enderror" id="source" name="source" required>
                                            <option value="">Chọn nguồn</option>
                                            <option value="website" {{ old('source') == 'website' ? 'selected' : '' }}>Website</option>
                                            <option value="facebook" {{ old('source') == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                            <option value="zalo" {{ old('source') == 'zalo' ? 'selected' : '' }}>Zalo</option>
                                            <option value="phone" {{ old('source') == 'phone' ? 'selected' : '' }}>Điện thoại</option>
                                            <option value="referral" {{ old('source') == 'referral' ? 'selected' : '' }}>Giới thiệu</option>
                                            <option value="advertisement" {{ old('source') == 'advertisement' ? 'selected' : '' }}>Quảng cáo</option>
                                            <option value="other" {{ old('source') == 'other' ? 'selected' : '' }}>Khác</option>
                                        </select>
                                        @error('source')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="new" {{ old('status') == 'new' ? 'selected' : '' }}>Mới</option>
                                            <option value="contacted" {{ old('status') == 'contacted' ? 'selected' : '' }}>Đã liên hệ</option>
                                            <option value="interested" {{ old('status') == 'interested' ? 'selected' : '' }}>Quan tâm</option>
                                            <option value="quoted" {{ old('status') == 'quoted' ? 'selected' : '' }}>Đã báo giá</option>
                                            <option value="negotiating" {{ old('status') == 'negotiating' ? 'selected' : '' }}>Đang đàm phán</option>
                                            <option value="follow_up" {{ old('status') == 'follow_up' ? 'selected' : '' }}>Cần theo dõi</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="priority" class="form-label">Độ ưu tiên <span class="text-danger">*</span></label>
                                        <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                            <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Thấp</option>
                                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Cao</option>
                                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Khẩn cấp</option>
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="assigned_to" class="form-label">Người phụ trách</label>
                                        <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                                            <option value="">Chưa gán</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('assigned_to')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="service_package_id" class="form-label">Gói dịch vụ quan tâm</label>
                                        <select class="form-select @error('service_package_id') is-invalid @enderror" 
                                                id="service_package_id" name="service_package_id">
                                            <option value="">Chưa xác định</option>
                                            @foreach($servicePackages as $package)
                                                <option value="{{ $package->id }}" {{ old('service_package_id') == $package->id ? 'selected' : '' }}>
                                                    {{ $package->name }} - {{ number_format($package->price) }} VND
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('service_package_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="estimated_value" class="form-label">Giá trị ước tính (VND)</label>
                                        <input type="number" class="form-control @error('estimated_value') is-invalid @enderror" 
                                               id="estimated_value" name="estimated_value" value="{{ old('estimated_value') }}" 
                                               min="0" step="1000">
                                        @error('estimated_value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="next_follow_up_at" class="form-label">Lịch theo dõi tiếp theo</label>
                                    <input type="datetime-local" class="form-control @error('next_follow_up_at') is-invalid @enderror" 
                                           id="next_follow_up_at" name="next_follow_up_at" value="{{ old('next_follow_up_at') }}">
                                    @error('next_follow_up_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="requirements" class="form-label">Yêu cầu khách hàng</label>
                                    <textarea class="form-control @error('requirements') is-invalid @enderror" 
                                              id="requirements" name="requirements" rows="3">{{ old('requirements') }}</textarea>
                                    @error('requirements')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Ghi chú</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Hủy
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Lưu Lead
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle me-2"></i>Hướng dẫn
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-primary">Trạng thái Lead</h6>
                                <ul class="list-unstyled text-sm">
                                    <li><span class="badge bg-primary me-2">Mới</span> Lead vừa được tạo</li>
                                    <li><span class="badge bg-info me-2">Đã liên hệ</span> Đã có liên hệ đầu tiên</li>
                                    <li><span class="badge bg-success me-2">Quan tâm</span> Khách hàng có quan tâm</li>
                                    <li><span class="badge bg-warning me-2">Đã báo giá</span> Đã gửi báo giá</li>
                                    <li><span class="badge bg-warning me-2">Đang đàm phán</span> Đang thương lượng</li>
                                    <li><span class="badge bg-secondary me-2">Cần theo dõi</span> Cần liên hệ lại</li>
                                </ul>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-primary">Độ ưu tiên</h6>
                                <ul class="list-unstyled text-sm">
                                    <li><span class="badge bg-danger me-2">Khẩn cấp</span> Xử lý ngay lập tức</li>
                                    <li><span class="badge bg-warning me-2">Cao</span> Ưu tiên cao</li>
                                    <li><span class="badge bg-info me-2">Trung bình</span> Xử lý bình thường</li>
                                    <li><span class="badge bg-secondary me-2">Thấp</span> Xử lý khi rảnh</li>
                                </ul>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-lightbulb me-2"></i>
                                <strong>Mẹo:</strong> Hãy điền đầy đủ thông tin để dễ dàng theo dõi và chăm sóc lead.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Auto-fill estimated value when service package is selected
document.getElementById('service_package_id').addEventListener('change', function() {
    const packageId = this.value;
    if (packageId) {
        const selectedOption = this.options[this.selectedIndex];
        const priceText = selectedOption.text;
        const priceMatch = priceText.match(/[\d,]+/);
        if (priceMatch) {
            const price = priceMatch[0].replace(/,/g, '');
            document.getElementById('estimated_value').value = price;
        }
    }
});

// Set default next follow up date (tomorrow at 9 AM)
document.addEventListener('DOMContentLoaded', function() {
    const nextFollowUpInput = document.getElementById('next_follow_up_at');
    if (!nextFollowUpInput.value) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow.setHours(9, 0, 0, 0);
        
        const year = tomorrow.getFullYear();
        const month = String(tomorrow.getMonth() + 1).padStart(2, '0');
        const day = String(tomorrow.getDate()).padStart(2, '0');
        const hours = String(tomorrow.getHours()).padStart(2, '0');
        const minutes = String(tomorrow.getMinutes()).padStart(2, '0');
        
        nextFollowUpInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
    }
});
</script>
@endsection
