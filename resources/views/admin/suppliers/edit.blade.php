@extends('layouts.admin')

@section('title', 'Chỉnh sửa nhà cung cấp')
@section('page-title', 'Chỉnh sửa nhà cung cấp')

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

    #add-product-btn {
        position: relative;
        z-index: 1000;
        pointer-events: all !important;
    }

    .text-center {
        position: relative;
        z-index: 999;
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
                        <i class="fas fa-edit text-white" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-white">Chỉnh sửa nhà cung cấp</h4>
                        <p class="mb-0 text-light opacity-75">{{ $supplier->supplier_code }}</p>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" id="supplier-form">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Thông tin cơ bản -->
                        <div class="col-12 mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Thông tin cơ bản
                            </h5>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="supplier_code" class="form-label fw-semibold">
                                <i class="fas fa-barcode me-2 text-secondary"></i>
                                Mã nhà cung cấp
                            </label>
                            <input type="text"
                                class="form-control form-control-lg"
                                id="supplier_code"
                                value="{{ $supplier->supplier_code }}"
                                readonly
                                style="background-color: #f8f9fa;">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Mã được tự động sinh và không thể thay đổi
                            </div>
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
                                value="{{ old('supplier_name', $supplier->supplier_name) }}"
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
                                @forelse($supplier->products as $index => $product)
                                <div class="product-item p-3 mb-3" data-index="{{ $index }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 text-secondary">
                                            <i class="fas fa-laptop me-2"></i>Dịch vụ #{{ $index + 1 }}
                                        </h6>
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-product" style="display: {{ count($supplier->products) > 1 ? 'flex' : 'none' }};">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label class="form-label fw-semibold">
                                                Tên dịch vụ/tài khoản <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control"
                                                name="products[{{ $index }}][product_name]"
                                                value="{{ old('products.'.$index.'.product_name', $product->product_name) }}"
                                                required
                                                placeholder="VD: Netflix Premium, ChatGPT Plus, Office 365...">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-semibold">
                                                Giá bán <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control"
                                                    name="products[{{ $index }}][price]"
                                                    value="{{ old('products.'.$index.'.price', $product->price) }}"
                                                    required
                                                    min="0"
                                                    step="1000"
                                                    placeholder="0">
                                                <span class="input-group-text">VND</span>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="products[{{ $index }}][id]" value="{{ $product->id }}">
                                </div>
                                @empty
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
                                        <div class="col-md-8 mb-3">
                                            <label class="form-label fw-semibold">
                                                Tên dịch vụ/tài khoản <span class="text-danger">*</span>
                                            </label>
                                            <input type="text"
                                                class="form-control"
                                                name="products[0][product_name]"
                                                required
                                                placeholder="VD: Netflix Premium, ChatGPT Plus, Office 365...">
                                        </div>
                                        <div class="col-md-4 mb-3">
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
                                    </div>
                                </div>
                                @endforelse
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
                                <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-light px-4">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                                </a>

                                <div>
                                    <button type="reset" class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-undo me-2"></i>Đặt lại
                                    </button>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i>Cập nhật
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
    console.log('Script starting...');

    // Test if basic elements exist
    setTimeout(function() {
        const container = document.getElementById('products-container');
        const button = document.getElementById('add-product-btn');

        console.log('Container exists:', !!container);
        console.log('Button exists:', !!button);

        if (button) {
            console.log('Button innerHTML:', button.innerHTML);
            console.log('Button disabled:', button.disabled);
            console.log('Button style display:', button.style.display);

            // Remove any existing listeners and add new one
            button.onclick = null;

            button.onclick = function(event) {
                event.preventDefault();
                event.stopPropagation();
                console.log('BUTTON CLICKED!');

                if (!container) {
                    console.error('Container not found!');
                    return;
                }

                // Get current product count
                const currentProducts = container.querySelectorAll('.product-item').length;
                const newIndex = currentProducts;

                console.log('Current products:', currentProducts);
                console.log('New index will be:', newIndex);

                // Simple HTML without template literals
                const newDiv = document.createElement('div');
                newDiv.className = 'product-item p-3 mb-3';
                newDiv.setAttribute('data-index', newIndex);

                newDiv.innerHTML =
                    '<div class="d-flex justify-content-between align-items-center mb-3">' +
                    '<h6 class="mb-0 text-secondary">' +
                    '<i class="fas fa-laptop me-2"></i>Dịch vụ #' + (newIndex + 1) +
                    '</h6>' +
                    '<button type="button" class="btn btn-outline-danger btn-sm btn-remove-product">' +
                    '<i class="fas fa-times"></i>' +
                    '</button>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-md-8 mb-3">' +
                    '<label class="form-label fw-semibold">Tên dịch vụ/tài khoản <span class="text-danger">*</span></label>' +
                    '<input type="text" class="form-control" name="products[' + newIndex + '][product_name]" required placeholder="VD: Netflix Premium, ChatGPT Plus...">' +
                    '</div>' +
                    '<div class="col-md-4 mb-3">' +
                    '<label class="form-label fw-semibold">Giá bán <span class="text-danger">*</span></label>' +
                    '<div class="input-group">' +
                    '<input type="number" class="form-control" name="products[' + newIndex + '][price]" required min="0" step="1000" placeholder="0">' +
                    '<span class="input-group-text">VND</span>' +
                    '</div>' +
                    '</div>' +
                    '</div>';

                container.appendChild(newDiv);
                console.log('New product added!');

                // Update remove buttons
                updateRemoveButtons();
            };

            // Also try addEventListener as backup
            button.addEventListener('click', function(e) {
                console.log('addEventListener triggered');
            });

            console.log('Click handler attached');
        }

        // Function to update remove buttons
        function updateRemoveButtons() {
            const allProducts = document.querySelectorAll('.product-item');
            const removeButtons = document.querySelectorAll('.btn-remove-product');

            console.log('Updating remove buttons. Total products:', allProducts.length);

            removeButtons.forEach(function(btn) {
                btn.style.display = allProducts.length > 1 ? 'flex' : 'none';
            });
        }

        // Remove product handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-product')) {
                console.log('Remove button clicked');
                const productItem = e.target.closest('.product-item');
                if (productItem) {
                    productItem.remove();

                    // Renumber products
                    const allProducts = document.querySelectorAll('.product-item');
                    allProducts.forEach(function(item, index) {
                        const title = item.querySelector('h6');
                        if (title) {
                            title.innerHTML = '<i class="fas fa-laptop me-2"></i>Dịch vụ #' + (index + 1);
                        }
                    });

                    updateRemoveButtons();
                }
            }
        });

        // Initial setup
        updateRemoveButtons();
        console.log('Setup complete');

    }, 500);

    // Additional test - direct button test
    function testButton() {
        console.log('Testing button manually...');
        const btn = document.getElementById('add-product-btn');
        if (btn) {
            btn.click();
        } else {
            console.log('Button not found for manual test');
        }
    }

    // Expose test function globally
    window.testButton = testButton;

    console.log('You can test manually by typing: testButton() in console');
</script>
@endsection