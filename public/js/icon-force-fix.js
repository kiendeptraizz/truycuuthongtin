/**
 * ICON FORCE FIX - Khắc phục triệt để vấn đề icons
 * Sử dụng nhiều phương pháp để đảm bảo icons hiển thị
 */

class IconForceFix {
    constructor() {
        this.init();
    }

    init() {
        console.log("🚀 Icon Force Fix initialized");

        // Chờ DOM load xong
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", () =>
                this.startFix()
            );
        } else {
            this.startFix();
        }
    }

    startFix() {
        // Phương pháp 1: Force reload Font Awesome
        this.forceReloadFontAwesome();

        // Phương pháp 2: Inject CSS trực tiếp
        this.injectIconCSS();

        // Phương pháp 3: Replace icons bằng emoji
        this.replaceWithEmojis();

        // Phương pháp 4: Monitor và fix liên tục
        this.startMonitoring();

        // Phương pháp 5: Force fix sau delay
        setTimeout(() => this.forceFixAll(), 2000);
        setTimeout(() => this.forceFixAll(), 5000);
        setTimeout(() => this.forceFixAll(), 10000);
    }

    forceReloadFontAwesome() {
        console.log("🔄 Force reloading Font Awesome...");

        // Xóa tất cả Font Awesome links hiện tại
        const existingLinks = document.querySelectorAll(
            'link[href*="font-awesome"]'
        );
        existingLinks.forEach((link) => link.remove());

        // Tạo links mới
        const newLinks = [
            "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css",
            "https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css",
            "https://use.fontawesome.com/releases/v6.5.1/css/all.css",
        ];

        newLinks.forEach((href) => {
            const link = document.createElement("link");
            link.rel = "stylesheet";
            link.href = href;
            link.crossOrigin = "anonymous";
            document.head.appendChild(link);
        });
    }

    injectIconCSS() {
        console.log("💉 Injecting icon CSS...");

        const css = `
            /* FORCE ICON DISPLAY */
            .fas, .fa {
                font-family: "Font Awesome 6 Free" !important;
                font-weight: 900 !important;
                font-style: normal !important;
                display: inline-block !important;
                width: 1em !important;
                height: 1em !important;
                text-align: center !important;
                line-height: 1 !important;
                vertical-align: middle !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            /* FORCE ICON CONTENT */
            .fa-home:before { content: "🏠" !important; }
            .fa-dashboard:before { content: "🏠" !important; }
            .fa-users:before { content: "👥" !important; }
            .fa-user:before { content: "👤" !important; }
            .fa-user-plus:before { content: "➕" !important; }
            .fa-link:before { content: "🔗" !important; }
            .fa-eye:before { content: "👁️" !important; }
            .fa-edit:before { content: "✏️" !important; }
            .fa-trash:before { content: "🗑️" !important; }
            .fa-plus:before { content: "➕" !important; }
            .fa-minus:before { content: "➖" !important; }
            .fa-save:before { content: "💾" !important; }
            .fa-times:before { content: "❌" !important; }
            .fa-check:before { content: "✅" !important; }
            .fa-box:before { content: "📦" !important; }
            .fa-cube:before { content: "🧊" !important; }
            .fa-tags:before { content: "🏷️" !important; }
            .fa-tag:before { content: "🏷️" !important; }
            .fa-truck:before { content: "🚚" !important; }
            .fa-search:before { content: "🔍" !important; }
            .fa-filter:before { content: "🔧" !important; }
            .fa-check-circle:before { content: "✅" !important; }
            .fa-pause-circle:before { content: "⏸️" !important; }
            .fa-exclamation-circle:before { content: "⚠️" !important; }
            .fa-info-circle:before { content: "ℹ️" !important; }
            .fa-calendar:before { content: "📅" !important; }
            .fa-chart-line:before { content: "📈" !important; }
            .fa-chart-bar:before { content: "📊" !important; }
            .fa-chart-pie:before { content: "🥧" !important; }
            .fa-shield-alt:before { content: "🛡️" !important; }
            .fa-download:before { content: "⬇️" !important; }
            .fa-upload:before { content: "⬆️" !important; }
            .fa-arrow-left:before { content: "⬅️" !important; }
            .fa-arrow-right:before { content: "➡️" !important; }
            .fa-arrow-up:before { content: "⬆️" !important; }
            .fa-arrow-down:before { content: "⬇️" !important; }
            .fa-share-alt:before { content: "📤" !important; }
            .fa-external-link-alt:before { content: "🔗" !important; }
            .fa-toggle-on:before { content: "🔛" !important; }
            .fa-toggle-off:before { content: "⭕" !important; }
            .fa-list:before { content: "📋" !important; }
            .fa-cogs:before { content: "⚙️" !important; }
            .fa-cog:before { content: "⚙️" !important; }
            .fa-clock:before { content: "🕐" !important; }
            .fa-pause:before { content: "⏸️" !important; }
            .fa-play:before { content: "▶️" !important; }
            .fa-stop:before { content: "⏹️" !important; }
            .fa-spinner:before { content: "🔄" !important; }
            .fa-bars:before { content: "☰" !important; }
            .fa-chevron-down:before { content: "⌄" !important; }
            .fa-chevron-up:before { content: "⌃" !important; }
            .fa-chevron-left:before { content: "〈" !important; }
            .fa-chevron-right:before { content: "〉" !important; }
            .fa-angle-down:before { content: "⌄" !important; }
            .fa-angle-up:before { content: "⌃" !important; }
            .fa-angle-left:before { content: "〈" !important; }
            .fa-angle-right:before { content: "〉" !important; }
            
            /* Navigation icons */
            .nav-link .fas, .nav-link .fa {
                width: 16px !important;
                margin-right: 12px !important;
                font-size: 14px !important;
            }
            
            /* Button icons */
            .btn .fas, .btn .fa {
                margin-right: 4px !important;
                font-size: 14px !important;
            }
            
            /* Table action icons */
            .table .fas, .table .fa {
                font-size: 14px !important;
                margin-right: 2px !important;
            }
        `;

        const style = document.createElement("style");
        style.id = "icon-force-fix-css";
        style.textContent = css;
        document.head.appendChild(style);
    }

    replaceWithEmojis() {
        console.log("🔄 Replacing icons with emojis...");

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
            "fa-bars": "☰",
            "fa-chevron-down": "⌄",
            "fa-chevron-up": "⌃",
            "fa-chevron-left": "〈",
            "fa-chevron-right": "〉",
            "fa-angle-down": "⌄",
            "fa-angle-up": "⌃",
            "fa-angle-left": "〈",
            "fa-angle-right": "〉",
        };

        // Tìm tất cả icon elements
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
                // Thay thế trực tiếp bằng emoji
                element.textContent = iconMappings[iconName];
                element.style.fontFamily = "inherit";
                element.style.fontSize = "14px";
                element.setAttribute("data-icon-fixed", "true");
            }
        });
    }

    forceFixAll() {
        console.log("🔧 Force fixing all icons...");

        // Kiểm tra xem Font Awesome có hoạt động không
        const testIcon = document.createElement("i");
        testIcon.className = "fas fa-home";
        testIcon.style.position = "absolute";
        testIcon.style.left = "-9999px";
        document.body.appendChild(testIcon);

        setTimeout(() => {
            const computedStyle = window.getComputedStyle(testIcon, ":before");
            const content = computedStyle.getPropertyValue("content");

            if (!content || content === '""' || content === '"☐"') {
                console.log(
                    "⚠️ Font Awesome still not working, applying emoji fallback"
                );
                this.replaceWithEmojis();
            } else {
                console.log("✅ Font Awesome is working now");
            }

            document.body.removeChild(testIcon);
        }, 100);
    }

    startMonitoring() {
        // Theo dõi thay đổi DOM
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === "childList") {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            const icons = node.querySelectorAll
                                ? node.querySelectorAll(".fas, .fa")
                                : [];
                            if (icons.length > 0) {
                                setTimeout(() => this.replaceWithEmojis(), 100);
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

        // Kiểm tra định kỳ
        setInterval(() => {
            const brokenIcons = document.querySelectorAll(
                ".fas:not([data-icon-fixed]), .fa:not([data-icon-fixed])"
            );
            if (brokenIcons.length > 0) {
                this.replaceWithEmojis();
            }
        }, 5000);
    }

    // Public methods
    forceReload() {
        console.log("🔄 Force reloading icon system...");
        this.startFix();
    }

    showStatus() {
        const totalIcons = document.querySelectorAll(".fas, .fa").length;
        const fixedIcons =
            document.querySelectorAll("[data-icon-fixed]").length;
        const brokenIcons = totalIcons - fixedIcons;

        console.log(
            `📊 Icon Status: Total: ${totalIcons}, Fixed: ${fixedIcons}, Broken: ${brokenIcons}`
        );

        return { total: totalIcons, fixed: fixedIcons, broken: brokenIcons };
    }
}

// Khởi tạo Icon Force Fix
document.addEventListener("DOMContentLoaded", () => {
    window.iconForceFix = new IconForceFix();

    // Thêm global commands
    window.forceFixIcons = () => window.iconForceFix.forceReload();
    window.iconStatus = () => window.iconForceFix.showStatus();

    console.log(
        "🎯 Icon Force Fix loaded. Commands: forceFixIcons(), iconStatus()"
    );
});

// Auto start sau 1 giây
setTimeout(() => {
    if (!window.iconForceFix) {
        window.iconForceFix = new IconForceFix();
    }
}, 1000);
