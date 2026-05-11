/**
 * Admin Scroll Preserve
 * --------------------
 * Khi form submit traditional (server-side redirect) hoặc bấm link gây reload,
 * lưu vị trí scroll → restore sau khi page load lại.
 *
 * Áp dụng cho mọi form/link trong admin → user không bị "nhảy lên đầu trang"
 * sau khi xoá/sửa/lưu/đổi trạng thái 1 item ở giữa danh sách dài.
 *
 * Không can thiệp AJAX action (data-ajax-action) — vì đã không reload page.
 *
 * Cách dùng: include trong admin layout, không cần config.
 */
(function () {
    'use strict';

    // Tắt browser's native scroll restoration (mặc định có thể conflict)
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    const STORAGE_KEY = '_admin_scroll_y';
    const STORAGE_PATH_KEY = '_admin_scroll_path';

    // Lưu scrollY trước khi form submit (form traditional, không có data-ajax-action)
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form || !form.matches('form')) return;
        // Bỏ qua AJAX forms — chúng không reload page nên không cần preserve
        if (form.hasAttribute('data-ajax-action') || form.hasAttribute('data-ajax-bulk')) return;
        saveScroll();
    }, true);

    // Lưu trước khi click các link có data-preserve-scroll (optional opt-in)
    // Hoặc các <a> trong admin gây reload page hiện tại
    document.addEventListener('click', function (e) {
        const link = e.target.closest('a[data-preserve-scroll]');
        if (link) {
            saveScroll();
        }
    }, true);

    // Restore khi page load
    document.addEventListener('DOMContentLoaded', function () {
        try {
            const savedPath = sessionStorage.getItem(STORAGE_PATH_KEY);
            const savedY = sessionStorage.getItem(STORAGE_KEY);

            // Chỉ restore nếu cùng pathname (tránh restore khi navigate sang page khác)
            if (savedPath && savedPath === window.location.pathname && savedY !== null) {
                const y = parseInt(savedY, 10);
                if (!isNaN(y) && y > 0) {
                    // Dùng requestAnimationFrame để chắc DOM render xong
                    requestAnimationFrame(() => {
                        window.scrollTo({ top: y, behavior: 'instant' });
                    });
                }
            }
        } catch (err) {
            console.warn('[admin-scroll-preserve] restore failed:', err);
        } finally {
            // Clear ngay để không restore nhiều lần (vd refresh F5)
            try {
                sessionStorage.removeItem(STORAGE_KEY);
                sessionStorage.removeItem(STORAGE_PATH_KEY);
            } catch (err) { /* ignore */ }
        }
    });

    function saveScroll() {
        try {
            sessionStorage.setItem(STORAGE_KEY, String(window.scrollY));
            sessionStorage.setItem(STORAGE_PATH_KEY, window.location.pathname);
        } catch (err) {
            console.warn('[admin-scroll-preserve] save failed:', err);
        }
    }
})();
