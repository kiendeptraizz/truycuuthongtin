<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tra cứu dịch vụ - KienUnlocked</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .lookup-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .lookup-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .search-box {
            position: relative;
        }
        
        .search-input {
            border: none;
            border-radius: 15px;
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        .search-btn {
            border-radius: 15px;
            padding: 1rem 2rem;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: all 0.3s ease;
        }
        
        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .result-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .result-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .status-active {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        
        .status-expired {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
        }
        
        .status-expiring {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }
        
        .service-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }
        
        .loading-spinner {
            display: none;
        }
        
        .header-brand {
            color: white;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .header-brand:hover {
            color: white;
            text-decoration: none;
        }
        
        @media (max-width: 768px) {
            .lookup-container {
                padding: 1rem;
            }
            
            .search-input {
                font-size: 1rem;
                padding: 0.8rem 1.2rem;
            }
            
            .search-btn {
                padding: 0.8rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="lookup-container">
        <!-- Header -->
        <div class="text-center mb-4">
            <a href="#" class="header-brand">
                <i class="fas fa-search me-2"></i>
                KienUnlocked - Tra Cứu Dịch Vụ
            </a>
            <p class="text-white-50 mt-2">Tra cứu thông tin dịch vụ của bạn một cách nhanh chóng</p>
        </div>

        <!-- Search Form -->
        <div class="lookup-card p-4 mb-4">
            <form id="lookupForm">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="search-box">
                            <input type="text" 
                                   class="form-control search-input" 
                                   id="searchInput" 
                                   placeholder="Nhập mã khách hàng, email hoặc số điện thoại..."
                                   required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary search-btn w-100">
                            <i class="fas fa-search me-2"></i>
                            Tra Cứu
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Loading -->
        <div class="text-center loading-spinner" id="loadingSpinner">
            <div class="spinner-border text-white" role="status">
                <span class="visually-hidden">Đang tìm kiếm...</span>
            </div>
            <p class="text-white mt-2">Đang tìm kiếm thông tin...</p>
        </div>

        <!-- Results -->
        <div id="searchResults"></div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('lookupForm');
            const searchInput = document.getElementById('searchInput');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const searchResults = document.getElementById('searchResults');

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                performSearch();
            });

            function performSearch() {
                const query = searchInput.value.trim();
                if (!query) return;

                // Show loading
                loadingSpinner.style.display = 'block';
                searchResults.innerHTML = '';

                // Perform AJAX search
                fetch(`{{ route('lookup.search') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ query: query })
                })
                .then(response => response.json())
                .then(data => {
                    loadingSpinner.style.display = 'none';
                    displayResults(data);
                })
                .catch(error => {
                    loadingSpinner.style.display = 'none';
                    displayError('Có lỗi xảy ra khi tìm kiếm. Vui lòng thử lại.');
                });
            }

            function displayResults(data) {
                if (!data.success || !data.customer) {
                    displayError('Không tìm thấy thông tin khách hàng.');
                    return;
                }

                const customer = data.customer;
                const services = data.services || [];

                let html = `
                    <div class="lookup-card p-4 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-1">
                                    <i class="fas fa-user text-primary me-2"></i>
                                    ${customer.name}
                                </h4>
                                <p class="text-muted mb-2">
                                    <strong>Mã KH:</strong> ${customer.customer_code} | 
                                    <strong>Email:</strong> ${customer.email || 'Chưa có'} | 
                                    <strong>SĐT:</strong> ${customer.phone || 'Chưa có'}
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    Khách hàng từ: ${formatDate(customer.created_at)}
                                </small>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="badge bg-success fs-6 px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>
                                    ${services.length} dịch vụ
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                if (services.length > 0) {
                    html += '<div class="row g-3">';
                    services.forEach(service => {
                        html += createServiceCard(service);
                    });
                    html += '</div>';
                } else {
                    html += `
                        <div class="lookup-card p-4">
                            <div class="empty-state">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <h5>Chưa có dịch vụ nào</h5>
                                <p>Khách hàng này chưa được gán dịch vụ nào.</p>
                            </div>
                        </div>
                    `;
                }

                searchResults.innerHTML = html;
            }

            function createServiceCard(service) {
                const statusClass = getStatusClass(service.status, service.expires_at);
                const statusText = getStatusText(service.status, service.expires_at);
                const daysRemaining = getDaysRemaining(service.expires_at);

                return `
                    <div class="col-md-6 mb-3">
                        <div class="result-card p-4 h-100">
                            <div class="d-flex align-items-start">
                                <div class="service-icon me-3">
                                    <i class="fas fa-cube"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-2">${service.service_package.name}</h6>
                                    <p class="text-muted small mb-2">${service.service_package.description || 'Không có mô tả'}</p>
                                    
                                    <div class="mb-2">
                                        <span class="status-badge ${statusClass}">
                                            ${statusText}
                                        </span>
                                    </div>
                                    
                                    <div class="small text-muted">
                                        <div><strong>Kích hoạt:</strong> ${formatDate(service.activated_at)}</div>
                                        <div><strong>Hết hạn:</strong> ${formatDate(service.expires_at)}</div>
                                        ${daysRemaining !== null ? `<div class="text-warning"><strong>Còn lại:</strong> ${daysRemaining} ngày</div>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            function displayError(message) {
                searchResults.innerHTML = `
                    <div class="lookup-card p-4">
                        <div class="empty-state">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h5>Không tìm thấy</h5>
                            <p>${message}</p>
                            <button class="btn btn-outline-primary" onclick="document.getElementById('searchInput').focus()">
                                <i class="fas fa-search me-1"></i>
                                Thử lại
                            </button>
                        </div>
                    </div>
                `;
            }

            function getStatusClass(status, expiresAt) {
                if (status !== 'active') return 'status-expired';
                
                const daysRemaining = getDaysRemaining(expiresAt);
                if (daysRemaining === null) return 'status-expired';
                if (daysRemaining <= 5) return 'status-expiring';
                return 'status-active';
            }

            function getStatusText(status, expiresAt) {
                if (status !== 'active') return 'Đã hết hạn';
                
                const daysRemaining = getDaysRemaining(expiresAt);
                if (daysRemaining === null) return 'Đã hết hạn';
                if (daysRemaining <= 0) return 'Đã hết hạn';
                if (daysRemaining <= 5) return 'Sắp hết hạn';
                return 'Đang hoạt động';
            }

            function getDaysRemaining(expiresAt) {
                if (!expiresAt) return null;
                
                const expireDate = new Date(expiresAt);
                const today = new Date();
                const diffTime = expireDate - today;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                return diffDays;
            }

            function formatDate(dateString) {
                if (!dateString) return 'Chưa có';
                
                const date = new Date(dateString);
                return date.toLocaleDateString('vi-VN');
            }

            // Auto focus on search input
            searchInput.focus();
        });
    </script>
</body>
</html>
