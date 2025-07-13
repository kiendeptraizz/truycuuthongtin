@extends('layouts.admin')

@section('title', 'Thêm nhà cung cấp mới')
@section('page-title', 'Thêm nhà cung cấp mới')

@section('styles')
<style>
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.15);
    }

    .card {
        border-radius: 15px;
        overflow: hidden;
    }

    .form-control-lg {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .form-control-lg:focus {
        transform: translateY(-1px);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.2s ease;
    }

    .product-item {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }

    .product-item:hover {
        border-color: #667eea;
        background: #fff;
    }

    .btn-remove-product {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary text-white py-4">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper me-4"
                        style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-truck text-white" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-white">Thêm nhà cung cấp mới</h4>
                        <p class="mb-0 text-light opacity-75">Nhập thông tin cơ bản về nhà cung cấp</p>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.suppliers.store') }}" id="supplier-form">
                    @csrf

                    <div class="row">
                        <!-- Thông tin cơ bản -->
                        <div class="col-12 mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Thông tin cơ bản
                            </h5>
                        </div>

                        <div class="col-md-12 mb-4">
                            <label for="supplier_name" class="form-label fw-semibold">
                                <i class="fas fa-user me-2 text-primary"></i>
                                Tên nhà cung cấp <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                class="form-control form-control-lg @error('supplier_name') is-invalid @enderror"
                                id="supplier_name"
                                name="supplier_name"
                                value="{{ old('supplier_name') }}"
                                required
                                placeholder="VD: Nguyễn Văn A, Công ty ABC...">
                            @error('supplier_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Tên người hoặc công ty cung cấp hàng hóa
                            </div>
                        </div>

                        <!-- Danh sách sản phẩm -->
                        <div class="col-12 mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-laptop me-2"></i>
                                Danh sách dịch vụ/tài khoản
                            </h5>
                        </div>

                        <div class="col-12 mb-4">
                            <div id="products-container">
                                <div class="product-item p-3 mb-3" data-index="0">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 text-secondary">
                                            <i class="fas fa-laptop me-2"></i>Dịch vụ #1
                                        </h6>
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-product" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">
                                                Tên dịch vụ/tài khoản <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control"
                                                name="products[0][product_name]"
                                                required
                                                placeholder="VD: Netflix Premium, ChatGPT Plus, Office 365...">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-semibold">
                                                Giá bán <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control"
                                                    name="products[0][price]"
                                                    required
                                                    min="0"
                                                    step="1000"
                                                    placeholder="0">
                                                <span class="input-group-text">VND</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-semibold">
                                                Số ngày bảo hành
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control"
                                                    name="products[0][warranty_days]"
                                                    min="0"
                                                    value="30"
                                                    placeholder="30">
                                                <span class="input-group-text">ngày</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn-outline-primary" id="add-product-btn">
                                    <i class="fas fa-plus me-2"></i>Thêm dịch vụ
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-light px-4">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                                </a>

                                <div>
                                    <button type="reset" class="btn btn-outline-secondary me-2" id="reset-btn">
                                        <i class="fas fa-undo me-2"></i>Đặt lại
                                    </button>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i>Lưu nhà cung cấp
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let productIndex = 1;

        const productsContainer = document.getElementById('products-container');
        const addProductBtn = document.getElementById('add-product-btn');
        const resetBtn = document.getElementById('reset-btn');

        // Add product function
        addProductBtn.addEventListener('click', function() {
            const productHtml = `
            <div class="product-item p-3 mb-3" data-index="${productIndex}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 text-secondary">
                        <i class="fas fa-laptop me-2"></i>Dịch vụ #${productIndex + 1}
                    </h6>
                    <button type="button" class="btn btn-outline-danger btn-sm btn-remove-product">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Tên dịch vụ/tài khoản <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               name="products[${productIndex}][product_name]" 
                               required
                               placeholder="VD: Netflix Premium, ChatGPT Plus, Office 365...">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">
                            Giá bán <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" 
                                   class="form-control" 
                                   name="products[${productIndex}][price]" 
                                   required
                                   min="0"
                                   step="1000"
                                   placeholder="0">
                            <span class="input-group-text">VND</span>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">
                            Số ngày bảo hành
                        </label>
                        <div class="input-group">
                            <input type="number" 
                                   class="form-control" 
                                   name="products[${productIndex}][warranty_days]" 
                                   min="0"
                                   value="30"
                                   placeholder="30">
                            <span class="input-group-text">ngày</span>
                        </div>
                    </div>
                </div>
            </div>
        `;

            productsContainer.insertAdjacentHTML('beforeend', productHtml);
            productIndex++;
            updateRemoveButtons();
        });

        // Remove product function
        productsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-product')) {
                const productItem = e.target.closest('.product-item');
                productItem.remove();
                updateProductNumbers();
                updateRemoveButtons();
            }
        });

        // Update product numbers
        function updateProductNumbers() {
            const productItems = productsContainer.querySelectorAll('.product-item');
            productItems.forEach((item, index) => {
                const title = item.querySelector('h6');
                title.innerHTML = `<i class="fas fa-laptop me-2"></i>Dịch vụ #${index + 1}`;
            });
        }

        // Update remove buttons visibility
        function updateRemoveButtons() {
            const productItems = productsContainer.querySelectorAll('.product-item');
            const removeButtons = productsContainer.querySelectorAll('.btn-remove-product');

            removeButtons.forEach(btn => {
                btn.style.display = productItems.length > 1 ? 'flex' : 'none';
            });
        }

        // Reset form
        resetBtn.addEventListener('click', function() {
            // Keep only first product
            const firstProduct = productsContainer.querySelector('.product-item');
            productsContainer.innerHTML = '';
            productsContainer.appendChild(firstProduct);

            // Reset first product form
            firstProduct.querySelectorAll('input').forEach(input => {
                input.value = '';
            });

            productIndex = 1;
            updateRemoveButtons();
        });

        // Auto focus first input
        document.getElementById('supplier_name').focus();

        // Initialize remove buttons
        updateRemoveButtons();
    });
</script>
@endsection