/**
 * SIMPLE ICON TEST - Test đơn giản nhất
 */

console.log("🚀 Simple Icon Test loaded");

// Test function
function testIcons() {
    console.log("🔍 Testing icons...");

    const icons = document.querySelectorAll(".fas, .fa");
    console.log(`Found ${icons.length} icons`);

    icons.forEach((icon, index) => {
        const classes = Array.from(icon.classList);
        const iconClass = classes.find((cls) => cls.startsWith("fa-"));

        if (iconClass) {
            console.log(`Icon ${index + 1}: ${iconClass}`);

            // Force emoji fallback
            const emojiMap = {
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

            if (emojiMap[iconClass]) {
                icon.textContent = emojiMap[iconClass];
                icon.style.fontFamily = "inherit";
                icon.style.fontSize = "14px";
                console.log(
                    `✅ Fixed ${iconClass} with ${emojiMap[iconClass]}`
                );
            }
        }
    });

    console.log("✅ Icon test completed");
}

// Auto test after 2 seconds
setTimeout(testIcons, 2000);

// Global function
window.testSimpleIcons = testIcons;
