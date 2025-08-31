/**
 * Icon Manager - Đảm bảo Font Awesome icons hiển thị đúng
 */
class IconManager {
    constructor() {
        this.init();
    }

    init() {
        // Kiểm tra Font Awesome đã load chưa
        this.checkFontAwesome();
        
        // Thêm fallback cho các icon quan trọng
        this.addFallbacks();
        
        // Theo dõi DOM changes để xử lý icons mới
        this.observeDOM();
    }

    checkFontAwesome() {
        // Kiểm tra Font Awesome đã load
        const testIcon = document.createElement('i');
        testIcon.className = 'fas fa-home';
        testIcon.style.position = 'absolute';
        testIcon.style.left = '-9999px';
        document.body.appendChild(testIcon);

        // Đợi một chút để font load
        setTimeout(() => {
            const computedStyle = window.getComputedStyle(testIcon, ':before');
            const content = computedStyle.getPropertyValue('content');
            
            // Nếu content rỗng hoặc là hình vuông, Font Awesome chưa load
            if (!content || content === '""' || content === '"☐"') {
                console.warn('Font Awesome not loaded properly, using fallbacks');
                this.enableFallbacks();
            }
            
            document.body.removeChild(testIcon);
        }, 1000);
    }

    enableFallbacks() {
        // Thêm class để kích hoạt fallback CSS
        document.body.classList.add('fa-fallback-enabled');
        
        // Thay thế các icon bằng emoji fallback
        this.replaceIconsWithFallbacks();
    }

    replaceIconsWithFallbacks() {
        const iconMappings = {
            'fa-home': '🏠',
            'fa-dashboard': '🏠',
            'fa-users': '👥',
            'fa-user': '👤',
            'fa-user-plus': '➕',
            'fa-link': '🔗',
            'fa-eye': '👁️',
            'fa-edit': '✏️',
            'fa-trash': '🗑️',
            'fa-plus': '➕',
            'fa-minus': '➖',
            'fa-save': '💾',
            'fa-times': '❌',
            'fa-check': '✅',
            'fa-box': '📦',
            'fa-cube': '🧊',
            'fa-tags': '🏷️',
            'fa-tag': '🏷️',
            'fa-truck': '🚚',
            'fa-search': '🔍',
            'fa-filter': '🔧',
            'fa-check-circle': '✅',
            'fa-pause-circle': '⏸️',
            'fa-exclamation-circle': '⚠️',
            'fa-info-circle': 'ℹ️',
            'fa-calendar': '📅',
            'fa-chart-line': '📈',
            'fa-chart-bar': '📊',
            'fa-chart-pie': '🥧',
            'fa-shield-alt': '🛡️',
            'fa-download': '⬇️',
            'fa-upload': '⬆️',
            'fa-arrow-left': '⬅️',
            'fa-arrow-right': '➡️',
            'fa-arrow-up': '⬆️',
            'fa-arrow-down': '⬇️',
            'fa-share-alt': '📤',
            'fa-external-link-alt': '🔗',
            'fa-toggle-on': '🔛',
            'fa-toggle-off': '⭕',
            'fa-list': '📋',
            'fa-cogs': '⚙️',
            'fa-cog': '⚙️',
            'fa-clock': '🕐',
            'fa-pause': '⏸️',
            'fa-play': '▶️',
            'fa-stop': '⏹️'
        };

        // Tìm tất cả các icon elements
        const iconElements = document.querySelectorAll('.fas, .fa');
        
        iconElements.forEach(element => {
            // Lấy tên class icon (loại bỏ fa- hoặc fas-)
            const classes = Array.from(element.classList);
            let iconName = null;
            
            for (const className of classes) {
                if (className.startsWith('fa-')) {
                    iconName = className;
                    break;
                }
            }
            
            if (iconName && iconMappings[iconName]) {
                // Thêm fallback emoji
                element.setAttribute('data-fallback', iconMappings[iconName]);
                
                // Nếu Font Awesome không hoạt động, hiển thị emoji
                if (document.body.classList.contains('fa-fallback-enabled')) {
                    element.textContent = iconMappings[iconName];
                    element.style.fontFamily = 'inherit';
                }
            }
        });
    }

    addFallbacks() {
        // Thêm CSS fallback
        const style = document.createElement('style');
        style.textContent = `
            .fa-fallback-enabled .fas,
            .fa-fallback-enabled .fa {
                font-family: inherit !important;
            }
            
            .fa-fallback-enabled .fas:before,
            .fa-fallback-enabled .fa:before {
                content: attr(data-fallback) !important;
            }
            
            /* Đảm bảo icons có kích thước phù hợp */
            .fas, .fa {
                display: inline-block !important;
                width: 1em !important;
                height: 1em !important;
                text-align: center !important;
                line-height: 1 !important;
            }
            
            /* Fix cho navigation icons */
            .nav-link .fas,
            .nav-link .fa {
                width: 16px !important;
                margin-right: 12px !important;
            }
            
            /* Fix cho button icons */
            .btn .fas,
            .btn .fa {
                margin-right: 4px !important;
            }
        `;
        document.head.appendChild(style);
    }

    observeDOM() {
        // Theo dõi thay đổi DOM để xử lý icons mới
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            const icons = node.querySelectorAll ? node.querySelectorAll('.fas, .fa') : [];
                            if (icons.length > 0) {
                                this.replaceIconsWithFallbacks();
                            }
                        }
                    });
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // Phương thức để force reload icons
    reloadIcons() {
        this.replaceIconsWithFallbacks();
    }
}

// Khởi tạo Icon Manager khi DOM ready
document.addEventListener('DOMContentLoaded', () => {
    window.iconManager = new IconManager();
});

// Export cho sử dụng global
window.IconManager = IconManager;
