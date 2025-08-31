/**
 * FINAL ICON FIX - Khắc phục triệt để vấn đề icons chồng lên nhau
 */

console.log("🚀 Final Icon Fix initialized");

class FinalIconFix {
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
        console.log("🔧 Starting final icon fix...");

        // Phương pháp 1: Force reset tất cả icons
        this.forceResetAllIcons();

        // Phương pháp 2: Xóa tất cả content cũ
        this.clearAllOldContent();

        // Phương pháp 3: Áp dụng emoji mới
        this.applyFinalEmojis();

        // Phương pháp 4: Monitor và fix liên tục
        this.startMonitoring();

        // Phương pháp 5: Force fix sau delay
        setTimeout(() => this.forceFinalFix(), 500);
        setTimeout(() => this.forceFinalFix(), 1000);
        setTimeout(() => this.forceFinalFix(), 2000);
        setTimeout(() => this.forceFinalFix(), 5000);
    }

    forceResetAllIcons() {
        console.log("🧹 Force resetting all icons...");

        // Reset tất cả icon elements
        const icons = document.querySelectorAll(".fas, .fa, .far, .fab");
        icons.forEach((icon) => {
            // Reset style hoàn toàn
            icon.style.cssText = "";

            // Reset attributes
            icon.removeAttribute("data-icon-cleaned");
            icon.removeAttribute("data-icon-fixed");

            // Reset class (giữ lại fa- classes)
            const classes = Array.from(icon.classList);
            const faClasses = classes.filter((cls) => cls.startsWith("fa-"));
            icon.className = faClasses.join(" ");

            // Reset text content
            icon.textContent = "";

            // Reset pseudo-elements
            const computedStyle = window.getComputedStyle(icon, ":before");
            if (computedStyle.content && computedStyle.content !== "none") {
                icon.style.setProperty("--fa-content", "", "important");
            }
        });
    }

    clearAllOldContent() {
        console.log("🗑️ Clearing all old content...");

        // Xóa tất cả CSS rules cũ
        const oldStyles = document.querySelectorAll(
            'style[id*="icon"], style[id*="fix"]'
        );
        oldStyles.forEach((style) => {
            if (style.id !== "final-icon-fix-style") {
                style.remove();
            }
        });

        // Xóa tất cả CSS rules từ các file cũ
        const oldLinks = document.querySelectorAll(
            'link[href*="icon"], link[href*="fix"]'
        );
        oldLinks.forEach((link) => {
            if (
                !link.href.includes("final-icon-fix.css") &&
                !link.href.includes("icon-reset.css")
            ) {
                link.remove();
            }
        });

        // Force CSS reset
        const resetStyle = document.createElement("style");
        resetStyle.id = "force-reset-style";
        resetStyle.textContent = `
            .fas, .fa, .far, .fab {
                all: unset !important;
                display: inline-block !important;
                width: 1em !important;
                height: 1em !important;
                text-align: center !important;
                line-height: 1 !important;
                vertical-align: middle !important;
                font-family: inherit !important;
                font-weight: normal !important;
                font-style: normal !important;
                font-size: 14px !important;
                color: inherit !important;
            }
            
            .fas:before, .fa:before, .far:before, .fab:before,
            .fas:after, .fa:after, .far:after, .fab:after {
                content: "" !important;
                display: none !important;
            }
        `;
        document.head.appendChild(resetStyle);
    }

    applyFinalEmojis() {
        console.log("✨ Applying final emojis...");

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
        const iconElements = document.querySelectorAll(".fas, .fa, .far, .fab");

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
                // Xóa content cũ hoàn toàn
                element.textContent = "";

                // Thay thế bằng emoji mới
                element.textContent = iconMappings[iconName];
                element.style.fontFamily = "inherit";
                element.style.fontSize = "14px";
                element.setAttribute("data-icon-final", "true");

                console.log(
                    `✅ Final fixed ${iconName} with ${iconMappings[iconName]}`
                );
            }
        });
    }

    forceFinalFix() {
        console.log("🔧 Force final fixing all icons...");

        // Kiểm tra xem có icons nào chưa được fix không
        const unfixedIcons = document.querySelectorAll(
            ".fas:not([data-icon-final]), .fa:not([data-icon-final]), .far:not([data-icon-final]), .fab:not([data-icon-final])"
        );

        if (unfixedIcons.length > 0) {
            console.log(
                `Found ${unfixedIcons.length} unfixed icons, fixing them...`
            );
            this.applyFinalEmojis();
        } else {
            console.log("✅ All icons are already final fixed");
        }

        // Kiểm tra icons bị chồng
        const allIcons = document.querySelectorAll(".fas, .fa, .far, .fab");
        allIcons.forEach((icon) => {
            if (icon.textContent.length > 1) {
                console.log(
                    `⚠️ Found overlapping icon: ${icon.textContent}, fixing...`
                );
                icon.textContent = icon.textContent.charAt(0);
            }
        });
    }

    startMonitoring() {
        // Theo dõi thay đổi DOM
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === "childList") {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            const icons = node.querySelectorAll
                                ? node.querySelectorAll(".fas, .fa, .far, .fab")
                                : [];
                            if (icons.length > 0) {
                                setTimeout(() => this.applyFinalEmojis(), 100);
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
            this.forceFinalFix();
        }, 2000);
    }

    // Public methods
    forceFix() {
        console.log("🔄 Force fixing icon system...");
        this.startFix();
    }

    showStatus() {
        const totalIcons = document.querySelectorAll(
            ".fas, .fa, .far, .fab"
        ).length;
        const fixedIcons =
            document.querySelectorAll("[data-icon-final]").length;
        const unfixedIcons = totalIcons - fixedIcons;

        console.log(
            `📊 Icon Status: Total: ${totalIcons}, Final Fixed: ${fixedIcons}, Unfixed: ${unfixedIcons}`
        );

        return { total: totalIcons, fixed: fixedIcons, unfixed: unfixedIcons };
    }

    resetAll() {
        console.log("🔄 Resetting all icons...");
        this.forceResetAllIcons();
        this.clearAllOldContent();
        setTimeout(() => this.applyFinalEmojis(), 100);
    }
}

// Khởi tạo Final Icon Fix
document.addEventListener("DOMContentLoaded", () => {
    window.finalIconFix = new FinalIconFix();

    // Thêm global commands
    window.forceFixIcons = () => window.finalIconFix.forceFix();
    window.iconStatus = () => window.finalIconFix.showStatus();
    window.resetAllIcons = () => window.finalIconFix.resetAll();

    console.log(
        "🎯 Final Icon Fix loaded. Commands: forceFixIcons(), iconStatus(), resetAllIcons()"
    );
});

// Auto start sau 500ms
setTimeout(() => {
    if (!window.finalIconFix) {
        window.finalIconFix = new FinalIconFix();
    }
}, 500);
