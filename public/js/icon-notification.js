/**
 * Icon Notification System
 * Tự động phát hiện và xử lý vấn đề với icons
 */

class IconNotification {
    constructor() {
        this.init();
    }

    init() {
        // Chờ DOM load xong
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", () =>
                this.startMonitoring()
            );
        } else {
            this.startMonitoring();
        }
    }

    startMonitoring() {
        console.log("🔍 Icon Notification System started");

        // Kiểm tra Font Awesome sau 1 giây
        setTimeout(() => this.checkFontAwesome(), 1000);

        // Kiểm tra lại sau 3 giây
        setTimeout(() => this.checkFontAwesome(), 3000);

        // Kiểm tra lại sau 5 giây
        setTimeout(() => this.checkFontAwesome(), 5000);

        // Theo dõi thay đổi DOM
        this.observeDOM();
    }

    checkFontAwesome() {
        const testIcon = document.createElement("i");
        testIcon.className = "fas fa-home";
        testIcon.style.position = "absolute";
        testIcon.style.left = "-9999px";
        document.body.appendChild(testIcon);

        setTimeout(() => {
            const computedStyle = window.getComputedStyle(testIcon, ":before");
            const content = computedStyle.getPropertyValue("content");

            if (!content || content === '""' || content === '"☐"') {
                console.warn("⚠️ Font Awesome not loaded properly");
                this.enableFallback();
                this.showNotification(
                    "Font Awesome không load được. Đang sử dụng fallback icons."
                );
            } else {
                console.log("✅ Font Awesome loaded successfully");
            }

            document.body.removeChild(testIcon);
        }, 100);
    }

    enableFallback() {
        // Thêm class để kích hoạt fallback
        document.body.classList.add("fa-fallback-enabled");

        // Thêm CSS fallback nếu chưa có
        if (!document.getElementById("icon-fallback-style")) {
            const style = document.createElement("style");
            style.id = "icon-fallback-style";
            style.textContent = `
                .fa-fallback-enabled .fas,
                .fa-fallback-enabled .fa {
                    font-family: inherit !important;
                }
                
                .fa-fallback-enabled .fas:before,
                .fa-fallback-enabled .fa:before {
                    content: attr(data-fallback) !important;
                }
            `;
            document.head.appendChild(style);
        }

        // Thêm fallback cho tất cả icons
        this.addFallbacksToIcons();
    }

    addFallbacksToIcons() {
        const iconMappings = {
            "fa-home": "🏠",
            "fa-dashboard": "🏠",
            "fa-users": "👥",
            "fa-user": "👤",
            "fa-user-plus": "➕",
            "fa-link": "🔗",
            "fa-eye": "👁️",
            "fa-edit": "✏️",
            "fa-trash": "🗑️",
            "fa-plus": "➕",
            "fa-minus": "➖",
            "fa-save": "💾",
            "fa-times": "❌",
            "fa-check": "✅",
            "fa-box": "📦",
            "fa-cube": "🧊",
            "fa-tags": "🏷️",
            "fa-tag": "🏷️",
            "fa-truck": "🚚",
            "fa-search": "🔍",
            "fa-filter": "🔧",
            "fa-check-circle": "✅",
            "fa-pause-circle": "⏸️",
            "fa-exclamation-circle": "⚠️",
            "fa-info-circle": "ℹ️",
            "fa-calendar": "📅",
            "fa-chart-line": "📈",
            "fa-chart-bar": "📊",
            "fa-chart-pie": "🥧",
            "fa-shield-alt": "🛡️",
            "fa-download": "⬇️",
            "fa-upload": "⬆️",
            "fa-arrow-left": "⬅️",
            "fa-arrow-right": "➡️",
            "fa-arrow-up": "⬆️",
            "fa-arrow-down": "⬇️",
            "fa-share-alt": "📤",
            "fa-external-link-alt": "🔗",
            "fa-toggle-on": "🔛",
            "fa-toggle-off": "⭕",
            "fa-list": "📋",
            "fa-cogs": "⚙️",
            "fa-cog": "⚙️",
            "fa-clock": "🕐",
            "fa-pause": "⏸️",
            "fa-play": "▶️",
            "fa-stop": "⏹️",
            "fa-spinner": "🔄",
        };

        const iconElements = document.querySelectorAll(".fas, .fa");

        iconElements.forEach((element) => {
            const classes = Array.from(element.classList);
            let iconName = null;

            for (const className of classes) {
                if (className.startsWith("fa-")) {
                    iconName = className;
                    break;
                }
            }

            if (iconName && iconMappings[iconName]) {
                element.setAttribute("data-fallback", iconMappings[iconName]);
            }
        });
    }

    showNotification(message, type = "warning") {
        // Tạo notification element
        const notification = document.createElement("div");
        notification.className = `icon-notification show notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            ${message}
            <button type="button" class="btn-close ms-2" onclick="this.parentElement.remove()"></button>
        `;

        // Thêm styles cho notification
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === "warning" ? "#f8d7da" : "#d1ecf1"};
            color: ${type === "warning" ? "#721c24" : "#0c5460"};
            padding: 10px 15px;
            border-radius: 5px;
            border: 1px solid ${type === "warning" ? "#f5c6cb" : "#bee5eb"};
            z-index: 9999;
            font-size: 14px;
            max-width: 300px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        `;

        document.body.appendChild(notification);

        // Tự động ẩn sau 10 giây
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 10000);
    }

    observeDOM() {
        // Theo dõi thay đổi DOM để xử lý icons mới
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === "childList") {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            const icons = node.querySelectorAll
                                ? node.querySelectorAll(".fas, .fa")
                                : [];
                            if (
                                icons.length > 0 &&
                                document.body.classList.contains(
                                    "fa-fallback-enabled"
                                )
                            ) {
                                this.addFallbacksToIcons();
                            }
                        }
                    });
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    }

    // Phương thức để force reload
    forceReload() {
        console.log("🔄 Force reloading icon system...");

        // Reload Font Awesome CSS
        const fontAwesomeLinks = document.querySelectorAll(
            'link[href*="font-awesome"]'
        );
        fontAwesomeLinks.forEach((link) => {
            const originalHref = link.href;
            link.href = "";
            setTimeout(() => {
                link.href = originalHref;
            }, 100);
        });

        // Xóa fallback class
        document.body.classList.remove("fa-fallback-enabled");

        // Chạy lại kiểm tra
        setTimeout(() => this.checkFontAwesome(), 2000);
    }
}

// Khởi tạo Icon Notification System
document.addEventListener("DOMContentLoaded", () => {
    window.iconNotification = new IconNotification();

    // Thêm global commands
    window.reloadIconSystem = () => window.iconNotification.forceReload();

    console.log(
        "🎯 Icon Notification System loaded. Commands: reloadIconSystem()"
    );
});

// Auto start sau 1 giây
setTimeout(() => {
    if (!window.iconNotification) {
        window.iconNotification = new IconNotification();
    }
}, 1000);
