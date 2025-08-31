<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Icon Test Page</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.1/css/all.css">
    
    <!-- Custom Icon CSS -->
    <link rel="stylesheet" href="{{ asset('css/icons-fix.css') }}">
    
    <style>
        .test-button {
            margin: 10px;
            min-width: 120px;
        }
        .icon-test {
            font-size: 1.2em;
            margin: 0 5px;
        }
        .test-section {
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-5">🧪 Icon Loading Test</h1>
        
        <!-- Font Awesome Test Section -->
        <div class="test-section">
            <h3>Font Awesome Icons Test</h3>
            <div class="row">
                <div class="col-md-6">
                    <h5>Các nút action thông thường:</h5>
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-info btn-sm test-button" title="Xem chi tiết">
                            <i class="fas fa-eye icon-test" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;">👁️</i>
                            Xem
                        </button>
                        <button class="btn btn-outline-warning btn-sm test-button" title="Chỉnh sửa">
                            <i class="fas fa-edit icon-test" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;">✏️</i>
                            Sửa
                        </button>
                        <button class="btn btn-outline-success btn-sm test-button" title="Thêm">
                            <i class="fas fa-plus icon-test" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;">➕</i>
                            Thêm
                        </button>
                        <button class="btn btn-outline-danger btn-sm test-button" title="Xóa">
                            <i class="fas fa-trash icon-test" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;">🗑️</i>
                            Xóa
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5>Toggle buttons:</h5>
                    <button class="btn btn-success btn-sm test-button">
                        <i class="fas fa-toggle-on icon-test" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;">🟢</i>
                        Bật
                    </button>
                    <button class="btn btn-secondary btn-sm test-button">
                        <i class="fas fa-toggle-off icon-test" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;">⭕</i>
                        Tắt
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Browser Info -->
        <div class="test-section">
            <h3>Browser & Loading Info</h3>
            <div id="browser-info"></div>
            <div id="font-loading-status" class="mt-3"></div>
        </div>
        
        <!-- Debug Console -->
        <div class="test-section">
            <h3>Debug Console</h3>
            <div id="debug-console" style="background: #f8f9fa; padding: 15px; font-family: monospace; min-height: 200px;">
                <div><strong>🔍 Icon Loading Debug:</strong></div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Enhanced Icon Test Script -->
    <script>
        // Debug logging function
        function debugLog(message) {
            const console = document.getElementById('debug-console');
            const time = new Date().toLocaleTimeString();
            console.innerHTML += `<div>[${time}] ${message}</div>`;
            console.scrollTop = console.scrollHeight;
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            debugLog('📄 DOM Content Loaded');
            
            // Browser info
            const browserInfo = document.getElementById('browser-info');
            browserInfo.innerHTML = `
                <p><strong>User Agent:</strong> ${navigator.userAgent}</p>
                <p><strong>Platform:</strong> ${navigator.platform}</p>
                <p><strong>Languages:</strong> ${navigator.languages.join(', ')}</p>
            `;
            
            // Font loading test
            function testFontAwesome() {
                debugLog('🔍 Testing Font Awesome loading...');
                
                const testElement = document.createElement('i');
                testElement.className = 'fas fa-heart';
                testElement.style.position = 'absolute';
                testElement.style.left = '-9999px';
                testElement.style.visibility = 'hidden';
                document.body.appendChild(testElement);
                
                setTimeout(() => {
                    const computedStyle = window.getComputedStyle(testElement, ':before');
                    const content = computedStyle.getPropertyValue('content');
                    const fontFamily = computedStyle.getPropertyValue('font-family');
                    
                    debugLog(`Font Family: ${fontFamily}`);
                    debugLog(`CSS Content: ${content}`);
                    
                    const isLoaded = content && content !== 'none' && content !== '""' && content !== 'normal';
                    
                    const statusEl = document.getElementById('font-loading-status');
                    if (isLoaded) {
                        statusEl.innerHTML = '<div class="alert alert-success">✅ Font Awesome loaded successfully!</div>';
                        debugLog('✅ Font Awesome LOADED');
                    } else {
                        statusEl.innerHTML = '<div class="alert alert-warning">⚠️ Font Awesome not detected, using emoji fallbacks</div>';
                        debugLog('❌ Font Awesome NOT LOADED');
                    }
                    
                    document.body.removeChild(testElement);
                }, 100);
            }
            
            // Test multiple times
            setTimeout(testFontAwesome, 100);
            setTimeout(testFontAwesome, 500);
            setTimeout(testFontAwesome, 1000);
            
            // Test individual icons
            setTimeout(() => {
                const icons = document.querySelectorAll('.icon-test');
                debugLog(`🎯 Found ${icons.length} test icons`);
                
                icons.forEach((icon, index) => {
                    const classes = icon.className;
                    const text = icon.textContent;
                    const computedStyle = window.getComputedStyle(icon);
                    debugLog(`Icon ${index}: ${classes} - Text: "${text}" - Font: ${computedStyle.fontFamily}`);
                });
            }, 1500);
        });
    </script>
</body>
</html>
