@extends('layouts.admin')

@section('title', 'Test UI Components')
@section('page-title', 'Test UI Components')

@section('content')
<div class="row g-3">
    <!-- Stats Cards Test -->
    <div class="col-12">
        <h4 class="mb-3">Stats Cards</h4>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card card-stats">
            <div class="card-body text-center">
                <div class="stats-icon mx-auto mb-3 bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <h2 class="stats-number">1,234</h2>
                <p class="stats-label mb-0">Khách hàng</p>
                <small class="text-success">
                    <i class="fas fa-arrow-up me-1"></i>
                    Tăng trưởng tốt
                </small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card card-stats">
            <div class="card-body text-center">
                <div class="stats-icon mx-auto mb-3 bg-success">
                    <i class="fas fa-box"></i>
                </div>
                <h2 class="stats-number">56</h2>
                <p class="stats-label mb-0">Gói dịch vụ</p>
                <small class="text-info">
                    <i class="fas fa-chart-line me-1"></i>
                    Đa dạng sản phẩm
                </small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card card-stats">
            <div class="card-body text-center">
                <div class="stats-icon mx-auto mb-3 bg-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 class="stats-number">12</h2>
                <p class="stats-label mb-0">Sắp hết hạn</p>
                <small class="text-warning">
                    <i class="fas fa-clock me-1"></i>
                    Cần chú ý
                </small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card card-stats">
            <div class="card-body text-center">
                <div class="stats-icon mx-auto mb-3 bg-danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h2 class="stats-number">3</h2>
                <p class="stats-label mb-0">Hết hạn</p>
                <small class="text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Cần xử lý
                </small>
            </div>
        </div>
    </div>

    <!-- Buttons Test -->
    <div class="col-12 mt-4">
        <h4 class="mb-3">Buttons</h4>
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6>Primary Buttons</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-primary">Primary</button>
                            <button class="btn btn-primary btn-sm">Small</button>
                            <button class="btn btn-primary btn-lg">Large</button>
                            <button class="btn btn-outline-primary">Outline</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Other Colors</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-success">Success</button>
                            <button class="btn btn-warning">Warning</button>
                            <button class="btn btn-danger">Danger</button>
                            <button class="btn btn-info">Info</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Test -->
    <div class="col-12 mt-4">
        <h4 class="mb-3">Table</h4>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên</th>
                                <th>Email</th>
                                <th class="d-none d-md-table-cell">Ngày tạo</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initial bg-primary text-white me-2">N</div>
                                        <div>Nguyễn Văn A</div>
                                    </div>
                                </td>
                                <td>nguyenvana@example.com</td>
                                <td class="d-none d-md-table-cell">01/01/2024</td>
                                <td><span class="badge bg-success">Hoạt động</span></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initial bg-success text-white me-2">T</div>
                                        <div>Trần Thị B</div>
                                    </div>
                                </td>
                                <td>tranthib@example.com</td>
                                <td class="d-none d-md-table-cell">02/01/2024</td>
                                <td><span class="badge bg-warning">Tạm dừng</span></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Forms Test -->
    <div class="col-12 mt-4">
        <h4 class="mb-3">Forms</h4>
        <div class="card">
            <div class="card-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tên</label>
                            <input type="text" class="form-control" placeholder="Nhập tên">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" placeholder="Nhập email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select">
                                <option>Chọn trạng thái</option>
                                <option>Hoạt động</option>
                                <option>Tạm dừng</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ghi chú</label>
                            <textarea class="form-control" rows="3" placeholder="Nhập ghi chú"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Lưu</button>
                            <button type="button" class="btn btn-outline-secondary ms-2">Hủy</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Alerts Test -->
    <div class="col-12 mt-4">
        <h4 class="mb-3">Alerts</h4>
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            Thành công! Dữ liệu đã được lưu.
        </div>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Cảnh báo! Vui lòng kiểm tra lại thông tin.
        </div>
        <div class="alert alert-danger">
            <i class="fas fa-times-circle me-2"></i>
            Lỗi! Không thể thực hiện thao tác.
        </div>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Thông tin: Hệ thống sẽ bảo trì vào 2h sáng.
        </div>
    </div>

    <!-- Responsive Test Info -->
    <div class="col-12 mt-4">
        <h4 class="mb-3">Responsive Test</h4>
        <div class="card">
            <div class="card-body">
                <p>Để test responsive design:</p>
                <ul>
                    <li>Thay đổi kích thước cửa sổ trình duyệt</li>
                    <li>Sử dụng Developer Tools (F12) để test các breakpoints</li>
                    <li>Test trên mobile: 375px, 768px</li>
                    <li>Test trên tablet: 768px, 1024px</li>
                    <li>Test trên desktop: 1200px, 1920px</li>
                </ul>
                <div class="d-block d-sm-none">
                    <span class="badge bg-danger">XS - Extra Small (&lt;576px)</span>
                </div>
                <div class="d-none d-sm-block d-md-none">
                    <span class="badge bg-warning">SM - Small (≥576px)</span>
                </div>
                <div class="d-none d-md-block d-lg-none">
                    <span class="badge bg-info">MD - Medium (≥768px)</span>
                </div>
                <div class="d-none d-lg-block d-xl-none">
                    <span class="badge bg-success">LG - Large (≥992px)</span>
                </div>
                <div class="d-none d-xl-block">
                    <span class="badge bg-primary">XL - Extra Large (≥1200px)</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
