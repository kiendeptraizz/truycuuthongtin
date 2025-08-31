/**
 * CLEAN ICON FIX - Khắc phục vấn đề icons chồng lên nhau
 */

console.log("🚀 Clean Icon Fix initialized");

class CleanIconFix {
    constructor() {
        this.init();
    }

    init() {
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
        console.log("🔧 Starting clean icon fix...");

        // Phương pháp 1: Xóa tất cả content cũ
        this.clearOldContent();

        // Phương pháp 2: Áp dụng emoji mới
        this.applyCleanEmojis();

        // Phương pháp 3: Monitor và fix liên tục
        this.startMonitoring();

        // Phương pháp 4: Force fix sau delay
        setTimeout(() => this.forceCleanFix(), 1000);
        setTimeout(() => this.forceCleanFix(), 3000);
    }

    clearOldContent() {
        console.log("🧹 Clearing old icon content...");

        // Xóa tất cả content cũ từ các CSS rules
        const icons = document.querySelectorAll(".fas, .fa");
        icons.forEach((icon) => {
            // Reset style
            icon.style.fontFamily = "inherit";
            icon.style.fontWeight = "normal";
            icon.style.fontStyle = "normal";

            // Xóa text content cũ
            if (icon.textContent && icon.textContent.trim() !== "") {
                icon.textContent = "";
            }
        });
    }

    applyCleanEmojis() {
        console.log("✨ Applying clean emojis...");

        const iconMappings = {
            "fa-home": "🏠",
            "fa-dashboard": "🏠",
            "fa-tachometer-alt": "🏠",
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
            "fa-bell": "🔔",
            "fa-exclamation-triangle": "⚠️",
            "fa-info": "ℹ️",
            "fa-question": "❓",
            "fa-star": "⭐",
            "fa-heart": "❤️",
            "fa-thumbs-up": "👍",
            "fa-thumbs-down": "👎",
            "fa-smile": "😊",
            "fa-frown": "😞",
            "fa-meh": "😐",
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
                // Xóa content cũ
                element.textContent = "";

                // Thay thế bằng emoji mới
                element.textContent = iconMappings[iconName];
                element.style.fontFamily = "inherit";
                element.style.fontSize = "14px";
                element.setAttribute("data-icon-cleaned", "true");

                console.log(
                    `✅ Cleaned ${iconName} with ${iconMappings[iconName]}`
                );
            }
        });
    }

    forceCleanFix() {
        console.log("🔧 Force cleaning all icons...");

        // Kiểm tra xem có icons nào chưa được clean không
        const uncleanedIcons = document.querySelectorAll(
            ".fas:not([data-icon-cleaned]), .fa:not([data-icon-cleaned])"
        );

        if (uncleanedIcons.length > 0) {
            console.log(
                `Found ${uncleanedIcons.length} uncleaned icons, cleaning them...`
            );
            this.applyCleanEmojis();
        } else {
            console.log("✅ All icons are already cleaned");
        }
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
                                setTimeout(() => this.applyCleanEmojis(), 100);
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
            const uncleanedIcons = document.querySelectorAll(
                ".fas:not([data-icon-cleaned]), .fa:not([data-icon-cleaned])"
            );
            if (uncleanedIcons.length > 0) {
                this.applyCleanEmojis();
            }
        }, 3000);
    }

    // Public methods
    forceClean() {
        console.log("🔄 Force cleaning icon system...");
        this.startFix();
    }

    showStatus() {
        const totalIcons = document.querySelectorAll(".fas, .fa").length;
        const cleanedIcons = document.querySelectorAll(
            "[data-icon-cleaned]"
        ).length;
        const uncleanedIcons = totalIcons - cleanedIcons;

        console.log(
            `📊 Icon Status: Total: ${totalIcons}, Cleaned: ${cleanedIcons}, Uncleaned: ${uncleanedIcons}`
        );

        return {
            total: totalIcons,
            cleaned: cleanedIcons,
            uncleaned: uncleanedIcons,
        };
    }
}

// Khởi tạo Clean Icon Fix
document.addEventListener("DOMContentLoaded", () => {
    window.cleanIconFix = new CleanIconFix();

    // Thêm global commands
    window.forceCleanIcons = () => window.cleanIconFix.forceClean();
    window.iconCleanStatus = () => window.cleanIconFix.showStatus();

    console.log(
        "🎯 Clean Icon Fix loaded. Commands: forceCleanIcons(), iconCleanStatus()"
    );
});

// Auto start sau 1 giây
setTimeout(() => {
    if (!window.cleanIconFix) {
        window.cleanIconFix = new CleanIconFix();
    }
}, 1000);
