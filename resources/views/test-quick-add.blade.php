<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Quick Add</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>
                            Test Thêm Nhanh Khách Hàng
                        </h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Có lỗi xảy ra:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.customers.store') }}" id="testForm">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-2"></i>
                                    Tên khách hàng <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required
                                       placeholder="Nhập tên khách hàng">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>
                                    Email
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       placeholder="email@example.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-2"></i>
                                    Số điện thoại
                                </label>
                                <input type="text" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}"
                                       placeholder="0xxx xxx xxx">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Mã khách hàng sẽ được tự động tạo theo định dạng KUN#####
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" name="action" value="save">
                                    <i class="fas fa-save me-2"></i>
                                    Lưu khách hàng
                                </button>
                                <button type="submit" class="btn btn-success" name="action" value="save_and_assign">
                                    <i class="fas fa-plus me-2"></i>
                                    Lưu & Gán dịch vụ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Quay lại danh sách khách hàng
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('testForm');
            
            form.addEventListener('submit', function(e) {
                console.log('🚀 Test form submitted!');
                
                const formData = new FormData(form);
                console.log('📝 Form data:');
                for (let [key, value] of formData.entries()) {
                    console.log(`   ${key}: ${value}`);
                }
                
                // Show loading state
                const submitButtons = form.querySelectorAll('button[type="submit"]');
                submitButtons.forEach(btn => {
                    btn.disabled = true;
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
                });
            });
        });
    </script>
</body>
</html>
