@extends('layouts.admin')

@section('title', 'Thêm thành viên vào Family Account')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Family Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Thông tin Family Account
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Tên Family:</small><br>
                            <strong>{{ $familyAccount->family_name }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Mã Family:</small><br>
                            <code class="bg-light px-2 py-1 rounded">{{ $familyAccount->family_code }}</code>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Gói dịch vụ:</small><br>
                            <span class="badge bg-info">{{ $familyAccount->servicePackage->name }}</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Thành viên:</small><br>
                            <span class="badge {{ $familyAccount->current_members >= $familyAccount->max_members ? 'bg-danger' : 'bg-success' }}">
                                {{ $familyAccount->current_members }}/{{ $familyAccount->max_members }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Member Form -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-auto">
                            <h5 class="mb-0">
                                <i class="fas fa-plus me-2"></i>
                                Thêm thành viên mới
                            </h5>
                            <small class="text-muted">Thêm khách hàng vào family account</small>
                        </div>
                        <div>
                            <a href="{{ route('admin.family-accounts.show', $familyAccount) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Quay lại
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if($familyAccount->current_members >= $familyAccount->max_members)
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Family Account đã đầy!</strong> Không thể thêm thành viên mới. 
                            Vui lòng tăng giới hạn thành viên hoặc xóa thành viên hiện tại.
                        </div>
                    @else
                        <form method="POST" action="{{ route('admin.family-accounts.add-member', $familyAccount) }}">
                            @csrf

                            <div class="row">
                                <!-- Member Type Selection -->
                                <div class="col-12 mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-plus me-1"></i>
                                        Loại thành viên <span class="text-danger">*</span>
                                    </label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" 
                                                       type="radio" 
                                                       name="member_type" 
                                                       id="member_type_existing" 
                                                       value="existing" 
                                                       {{ old('member_type', 'existing') === 'existing' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="member_type_existing">
                                                    <i class="fas fa-users me-1"></i>
                                                    Chọn từ khách hàng có sẵn
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" 
                                                       type="radio" 
                                                       name="member_type" 
                                                       id="member_type_new" 
                                                       value="new" 
                                                       {{ old('member_type') === 'new' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="member_type_new">
                                                    <i class="fas fa-plus me-1"></i>
                                                    Tạo khách hàng mới
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Existing Customer Selection -->
                                <div id="existing_customer_section" class="col-12 mb-3">
                                    <label for="customer_id" class="form-label">
                                        <i class="fas fa-user me-1"></i>
                                        Khách hàng <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('customer_id') is-invalid @enderror" 
                                            id="customer_id" 
                                            name="customer_id">
                                        <option value="">Chọn khách hàng</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" 
                                                    data-email="{{ $customer->email }}"
                                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} - {{ $customer->email }}
                                                @if($customer->phone)
                                                    ({{ $customer->phone }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Chỉ hiển thị khách hàng chưa tham gia family nào
                                    </small>
                                </div>

                                <!-- New Customer Information -->
                                <div id="new_customer_section" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="customer_name" class="form-label">
                                                <i class="fas fa-user me-1"></i>
                                                Tên khách hàng <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('customer_name') is-invalid @enderror" 
                                                   id="customer_name" 
                                                   name="customer_name" 
                                                   value="{{ old('customer_name') }}" 
                                                   placeholder="Nhập tên khách hàng">
                                            @error('customer_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="customer_phone" class="form-label">
                                                <i class="fas fa-phone me-1"></i>
                                                Số điện thoại
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('customer_phone') is-invalid @enderror" 
                                                   id="customer_phone" 
                                                   name="customer_phone" 
                                                   value="{{ old('customer_phone') }}" 
                                                   placeholder="Nhập số điện thoại">
                                            @error('customer_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Member Email -->
                                <div class="col-md-6 mb-3">
                                    <label for="member_email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>
                                        Email thành viên <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('member_email') is-invalid @enderror" 
                                           id="member_email" 
                                           name="member_email" 
                                           value="{{ old('member_email') }}" 
                                           placeholder="email@example.com"
                                           required>
                                    @error('member_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Email để truy cập dịch vụ family
                                    </small>
                                </div>

                                <!-- Service Package Info -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-box me-1"></i>
                                        Gói dịch vụ
                                    </label>
                                    <div class="form-control-plaintext bg-light rounded p-2">
                                        @if($familyAccount->servicePackage)
                                            <strong>{{ $familyAccount->servicePackage->package_name }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                Thời hạn: {{ $familyAccount->servicePackage->duration_months ?? 1 }} tháng
                                            </small>
                                        @else
                                            <span class="text-muted">Chưa có gói dịch vụ</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Start Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        Ngày bắt đầu <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" 
                                           name="start_date" 
                                           value="{{ old('start_date', now()->format('Y-m-d')) }}"
                                           min="{{ now()->format('Y-m-d') }}"
                                           required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Ngày thành viên bắt đầu có hiệu lực
                                    </small>
                                </div>

                                <!-- End Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label">
                                        <i class="fas fa-calendar-check me-1"></i>
                                        Ngày kết thúc 
                                        @if($familyAccount->servicePackage && $familyAccount->servicePackage->duration_months)
                                            <small class="text-success">(Tự động tính)</small>
                                        @else
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" 
                                           name="end_date" 
                                           value="{{ old('end_date') }}"
                                           min="{{ now()->addDay()->format('Y-m-d') }}"
                                           {{ $familyAccount->servicePackage && $familyAccount->servicePackage->duration_months ? 'readonly' : 'required' }}>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        @if($familyAccount->servicePackage && $familyAccount->servicePackage->duration_months)
                                            Được tính tự động dựa trên gói dịch vụ
                                        @else
                                            Ngày thành viên hết hiệu lực
                                        @endif
                                    </small>
                                </div>

                                <!-- Member Notes -->
                                <div class="col-12 mb-3">
                                    <label for="member_notes" class="form-label">
                                        <i class="fas fa-sticky-note me-1"></i>
                                        Ghi chú thành viên
                                    </label>
                                    <textarea class="form-control @error('member_notes') is-invalid @enderror" 
                                              id="member_notes" 
                                              name="member_notes" 
                                              rows="3"
                                              placeholder="Ghi chú về thành viên này...">{{ old('member_notes') }}</textarea>
                                    @error('member_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('admin.family-accounts.show', $familyAccount) }}" class="btn btn-secondary">
                                            <i class="fas fa-times-circle me-1"></i>
                                            Hủy
                                        </a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-plus me-1"></i>
                                            Thêm thành viên
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            @if($customers->isEmpty())
                <div class="card mt-4">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Không có khách hàng khả dụng</h5>
                        <p class="text-muted">
                            Tất cả khách hàng đều đã tham gia family account khác hoặc chưa có khách hàng nào.
                        </p>
                        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            Tạo khách hàng mới
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill member email based on selected customer
    const customerSelect = document.getElementById('customer_id');
    const memberEmailInput = document.getElementById('member_email');
    
    customerSelect.addEventListener('change', function() {
        if (this.value && !memberEmailInput.value) {
            const selectedOption = this.options[this.selectedIndex];
            const optionText = selectedOption.textContent;
            
            // Extract email from option text (format: "Name - email@example.com")
            const emailMatch = optionText.match(/- ([\w\.-]+@[\w\.-]+\.\w+)/);
            if (emailMatch) {
                memberEmailInput.value = emailMatch[1];
            }
        }
    });

    // Search functionality for customer select
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'form-control mb-2';
    searchInput.placeholder = 'Tìm kiếm khách hàng...';
    searchInput.style.display = 'none';
    
    customerSelect.parentNode.insertBefore(searchInput, customerSelect);
    
    // Toggle search input
    customerSelect.addEventListener('focus', function() {
        searchInput.style.display = 'block';
    });
    
    customerSelect.addEventListener('blur', function() {
        setTimeout(() => {
            searchInput.style.display = 'none';
        }, 200);
    });
    
    // Filter options based on search
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const options = customerSelect.options;
        
        for (let i = 1; i < options.length; i++) { // Skip first option
            const option = options[i];
            const text = option.textContent.toLowerCase();
            
            if (text.includes(searchTerm)) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        }
    });
});
</script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const memberTypeRadios = document.querySelectorAll('input[name="member_type"]');
            const existingCustomerSection = document.getElementById('existing_customer_section');
            const newCustomerSection = document.getElementById('new_customer_section');
            const customerSelect = document.getElementById('customer_id');
            const memberEmailInput = document.getElementById('member_email');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            
            // Service package duration from server
            const packageDuration = {{ $familyAccount->servicePackage->duration_months ?? 0 }};

            // Handle member type change
            function handleMemberTypeChange() {
                const selectedType = document.querySelector('input[name="member_type"]:checked').value;
                
                if (selectedType === 'existing') {
                    existingCustomerSection.style.display = 'block';
                    newCustomerSection.style.display = 'none';
                    
                    // Make customer_id required
                    customerSelect.required = true;
                    document.getElementById('customer_name').required = false;
                } else {
                    existingCustomerSection.style.display = 'none';
                    newCustomerSection.style.display = 'block';
                    
                    // Make customer_name required
                    customerSelect.required = false;
                    document.getElementById('customer_name').required = true;
                }
            }

            // Auto-calculate end date based on service package
            function calculateEndDate() {
                const startDate = new Date(startDateInput.value);
                
                if (startDate && packageDuration > 0) {
                    const endDate = new Date(startDate);
                    endDate.setMonth(endDate.getMonth() + packageDuration);
                    endDateInput.value = endDate.toISOString().split('T')[0];
                } else if (startDate && packageDuration === 0) {
                    // Default to 30 days if no package duration
                    const endDate = new Date(startDate);
                    endDate.setDate(endDate.getDate() + 30);
                    endDateInput.value = endDate.toISOString().split('T')[0];
                }
            }

            // Validate date range
            function validateDateRange() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                if (startDate && endDate && startDate >= endDate) {
                    endDateInput.setCustomValidity('Ngày kết thúc phải sau ngày bắt đầu');
                } else {
                    endDateInput.setCustomValidity('');
                }
            }

            // Event listeners
            memberTypeRadios.forEach(radio => {
                radio.addEventListener('change', handleMemberTypeChange);
            });

            startDateInput.addEventListener('change', function() {
                const startDate = new Date(this.value);
                const minEndDate = new Date(startDate);
                minEndDate.setDate(minEndDate.getDate() + 1);
                
                endDateInput.min = minEndDate.toISOString().split('T')[0];
                
                // Auto-calculate end date if package has duration
                if (packageDuration > 0) {
                    calculateEndDate();
                }
                
                validateDateRange();
            });

            endDateInput.addEventListener('change', validateDateRange);

            // Customer selection changes
            customerSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption && selectedOption.dataset.email) {
                    memberEmailInput.value = selectedOption.dataset.email;
                }
            });

            // Auto-fill email when creating new customer
            document.getElementById('customer_name').addEventListener('input', function() {
                // Only auto-fill if member email is empty
                if (!memberEmailInput.value && this.value) {
                    // Generate a simple email suggestion
                    const name = this.value.toLowerCase().replace(/\s+/g, '');
                    memberEmailInput.placeholder = `${name}@example.com`;
                }
            });

            // Initialize on page load
            handleMemberTypeChange();
            
            // Auto-calculate end date on page load if start date is set
            if (startDateInput.value && packageDuration > 0) {
                calculateEndDate();
            }
        });
    </script>
@endpush
@endsection
