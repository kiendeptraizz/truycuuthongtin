/**
 * Icon Test and Debug Script
 * Kiểm tra và debug các vấn đề với icons
 */

class IconTester {
    constructor() {
        this.init();
    }

    init() {
        // Chờ DOM load xong
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.runTests());
        } else {
            this.runTests();
        }
    }

    runTests() {
        console.log('🔍 Bắt đầu kiểm tra icons...');
        
        // Test 1: Kiểm tra Font Awesome đã load chưa
        this.testFontAwesome();
        
        // Test 2: Kiểm tra các icon quan trọng
        this.testCriticalIcons();
        
        // Test 3: Kiểm tra fallback system
        this.testFallbackSystem();
        
        // Test 4: Hiển thị kết quả
        this.showResults();
    }

    testFontAwesome() {
        console.log('📋 Test 1: Kiểm tra Font Awesome...');
        
        const testIcon = document.createElement('i');
        testIcon.className = 'fas fa-home';
        testIcon.style.position = 'absolute';
        testIcon.style.left = '-9999px';
        document.body.appendChild(testIcon);

        setTimeout(() => {
            const computedStyle = window.getComputedStyle(testIcon, ':before');
            const content = computedStyle.getPropertyValue('content');
            
            if (content && content !== '""' && content !== '"☐"') {
                console.log('✅ Font Awesome đã load thành công');
                this.fontAwesomeLoaded = true;
            } else {
                console.warn('⚠️ Font Awesome chưa load hoặc có vấn đề');
                this.fontAwesomeLoaded = false;
            }
            
            document.body.removeChild(testIcon);
        }, 1000);
    }

    testCriticalIcons() {
        console.log('📋 Test 2: Kiểm tra các icon quan trọng...');
        
        const criticalIcons = [
            'fa-home', 'fa-dashboard', 'fa-users', 'fa-user-plus', 
            'fa-link', 'fa-eye', 'fa-edit', 'fa-trash', 'fa-plus'
        ];
        
        this.criticalIconResults = {};
        
        criticalIcons.forEach(iconClass => {
            const testElement = document.createElement('i');
            testElement.className = `fas ${iconClass}`;
            testElement.style.position = 'absolute';
            testElement.style.left = '-9999px';
            document.body.appendChild(testElement);
            
            setTimeout(() => {
                const computedStyle = window.getComputedStyle(testElement, ':before');
                const content = computedStyle.getPropertyValue('content');
                
                this.criticalIconResults[iconClass] = {
                    loaded: content && content !== '""' && content !== '"☐"',
                    content: content
                };
                
                document.body.removeChild(testElement);
            }, 100);
        });
    }

    testFallbackSystem() {
        console.log('📋 Test 3: Kiểm tra fallback system...');
        
        // Test emoji fallback
        const testElement = document.createElement('i');
        testElement.className = 'fas fa-home';
        testElement.setAttribute('data-fallback', '🏠');
        document.body.appendChild(testElement);
        
        setTimeout(() => {
            const computedStyle = window.getComputedStyle(testElement, ':before');
            const content = computedStyle.getPropertyValue('content');
            
            this.fallbackWorking = content === '"🏠"' || content !== '""';
            document.body.removeChild(testElement);
        }, 100);
    }

    showResults() {
        setTimeout(() => {
            console.log('📊 Kết quả kiểm tra icons:');
            console.log('- Font Awesome loaded:', this.fontAwesomeLoaded);
            console.log('- Critical icons:', this.criticalIconResults);
            console.log('- Fallback system:', this.fallbackWorking);
            
            // Hiển thị notification nếu có vấn đề
            if (!this.fontAwesomeLoaded) {
                this.showNotification('⚠️ Font Awesome chưa load đúng cách. Đang sử dụng fallback icons.');
            }
            
            // Đếm số icon bị lỗi
            const brokenIcons = Object.values(this.criticalIconResults || {})
                .filter(result => !result.loaded).length;
            
            if (brokenIcons > 0) {
                this.showNotification(`⚠️ Có ${brokenIcons} icon quan trọng bị lỗi.`);
            }
            
            console.log('✅ Hoàn thành kiểm tra icons');
        }, 2000);
    }

    showNotification(message) {
        // Tạo notification element
        const notification = document.createElement('div');
        notification.className = 'icon-notification show';
        notification.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            ${message}
            <button type="button" class="btn-close ms-2" onclick="this.parentElement.remove()"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Tự động ẩn sau 10 giây
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 10000);
    }

    // Phương thức để force reload icons
    forceReload() {
        console.log('🔄 Force reloading icons...');
        
        // Reload Font Awesome CSS
        const fontAwesomeLinks = document.querySelectorAll('link[href*="font-awesome"]');
        fontAwesomeLinks.forEach(link => {
            const originalHref = link.href;
            link.href = '';
            setTimeout(() => {
                link.href = originalHref;
            }, 100);
        });
        
        // Reload icon manager
        if (window.iconManager) {
            window.iconManager.reloadIcons();
        }
        
        // Chạy lại tests
        setTimeout(() => this.runTests(), 2000);
    }

    // Phương thức để debug icons
    debugIcons() {
        console.log('🐛 Bật chế độ debug icons...');
        document.body.classList.add('icon-debug');
        
        // Hiển thị thông tin debug cho tất cả icons
        const icons = document.querySelectorAll('.fas, .fa');
        icons.forEach(icon => {
            const classes = Array.from(icon.classList);
            const iconClass = classes.find(cls => cls.startsWith('fa-'));
            
            if (iconClass) {
                const computedStyle = window.getComputedStyle(icon, ':before');
                const content = computedStyle.getPropertyValue('content');
                
                console.log(`Icon ${iconClass}:`, {
                    element: icon,
                    content: content,
                    loaded: content && content !== '""' && content !== '"☐"'
                });
            }
        });
    }
}

// Khởi tạo Icon Tester
document.addEventListener('DOMContentLoaded', () => {
    window.iconTester = new IconTester();
    
    // Thêm global commands
    window.testIcons = () => window.iconTester.runTests();
    window.reloadIcons = () => window.iconTester.forceReload();
    window.debugIcons = () => window.iconTester.debugIcons();
    
    console.log('🎯 Icon Tester loaded. Commands: testIcons(), reloadIcons(), debugIcons()');
});

// Auto test sau 3 giây
setTimeout(() => {
    if (window.iconTester) {
        window.iconTester.runTests();
    }
}, 3000);
