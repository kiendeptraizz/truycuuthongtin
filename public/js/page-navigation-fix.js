/**
 * Page Navigation Fix
 * Handles browser back/forward navigation issues and white screen problems
 */

(function () {
    "use strict";

    // Configuration
    const config = {
        checkInterval: 100,
        maxChecks: 50,
        reloadDelay: 500,
    };

    let checkCount = 0;
    let isChecking = false;

    /**
     * Check if page content is properly loaded
     */
    function checkPageContent() {
        if (isChecking) return;
        isChecking = true;
        checkCount++;

        try {
            const body = document.body;
            const mainContent = document.querySelector(
                ".main-content, .container, main, #app"
            );
            const hasContent =
                body && body.innerHTML && body.innerHTML.trim().length > 100;
            const hasMainContent =
                mainContent &&
                mainContent.innerHTML &&
                mainContent.innerHTML.trim().length > 50;
            const isVisible =
                body &&
                body.style.display !== "none" &&
                body.style.visibility !== "hidden";

            // Check for white screen or missing content
            if (!hasContent || !hasMainContent || !isVisible) {
                console.warn("Page content issue detected:", {
                    hasContent,
                    hasMainContent,
                    isVisible,
                    checkCount,
                });

                if (checkCount < config.maxChecks) {
                    setTimeout(() => {
                        isChecking = false;
                        checkPageContent();
                    }, config.checkInterval);
                } else {
                    console.log("Max checks reached, reloading page...");
                    window.location.reload();
                }
                return false;
            }

            console.log("Page content check passed");
            return true;
        } catch (error) {
            console.error("Error checking page content:", error);
            if (checkCount >= config.maxChecks) {
                window.location.reload();
            }
            return false;
        } finally {
            isChecking = false;
        }
    }

    /**
     * Handle page show event (back/forward navigation)
     */
    function handlePageShow(event) {
        console.log(
            "Page show event:",
            event.persisted ? "from cache" : "fresh load"
        );

        if (event.persisted) {
            // Page was loaded from cache
            setTimeout(() => {
                if (!checkPageContent()) {
                    console.log("Cache issue detected, reloading...");
                    window.location.reload();
                }
            }, config.reloadDelay);
        }
    }

    /**
     * Handle before unload event
     */
    function handleBeforeUnload() {
        // Reset any loading states
        try {
            document.querySelectorAll("button[disabled]").forEach((btn) => {
                if (btn.innerHTML.includes("fa-spinner")) {
                    btn.disabled = false;
                    btn.innerHTML =
                        btn.getAttribute("data-original-text") || "Submit";
                }
            });
        } catch (error) {
            console.error("Error resetting button states:", error);
        }
    }

    /**
     * Handle popstate event (browser navigation)
     */
    function handlePopState(event) {
        console.log("Pop state event detected");

        setTimeout(() => {
            if (!checkPageContent()) {
                console.log("Navigation issue detected, reloading...");
                window.location.reload();
            }
        }, config.checkInterval);
    }

    /**
     * Initialize navigation fix
     */
    function init() {
        // Initial content check
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", () => {
                setTimeout(checkPageContent, config.checkInterval);
            });
        } else {
            setTimeout(checkPageContent, config.checkInterval);
        }

        // Add event listeners
        window.addEventListener("pageshow", handlePageShow);
        window.addEventListener("beforeunload", handleBeforeUnload);
        window.addEventListener("popstate", handlePopState);

        // Additional safety check after a delay
        setTimeout(() => {
            if (!checkPageContent()) {
                console.log("Delayed check failed, reloading...");
                window.location.reload();
            }
        }, 1000);

        console.log("Page navigation fix initialized");
    }

    // Initialize when script loads
    init();

    // Expose for debugging
    window.pageNavigationFix = {
        checkPageContent,
        config,
        forceReload: () => window.location.reload(),
        getStatus: () => ({
            checkCount,
            isChecking,
            hasMainContent: !!document.querySelector(
                ".main-content, .container, main, #app"
            ),
            bodyContent: document.body ? document.body.innerHTML.length : 0,
        }),
    };

    // Add console info
    console.log("🔧 Page Navigation Fix loaded successfully");
    console.log(
        "📊 Debug info available at: window.pageNavigationFix.getStatus()"
    );
})();
