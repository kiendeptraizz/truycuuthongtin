// Performance utilities
const debounce = (fn, delay) => {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn(...args), delay);
    };
};

const throttle = (fn, limit) => {
    let inThrottle;
    return (...args) => {
        if (!inThrottle) {
            fn(...args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
};

// Auto-hide alerts & form helpers
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts (except those with 'no-auto-hide' class)
    const hideAlerts = () => {
        document.querySelectorAll('.alert:not(.no-auto-hide)').forEach(alert => {
            setTimeout(() => {
                if (alert.parentNode) {
                    new bootstrap.Alert(alert).close();
                }
            }, 5000);
        });
    };

    if ('requestIdleCallback' in window) {
        requestIdleCallback(hideAlerts);
    } else {
        setTimeout(hideAlerts, 100);
    }

    // Form submission loading state - use event delegation
    document.body.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.tagName === 'FORM') {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.setAttribute('data-original-text', submitBtn.innerHTML);
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';

                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = submitBtn.getAttribute('data-original-text') || 'Submit';
                    }
                }, 10000);
            }
        }
    }, { passive: false });

    // === TOP LOADING BAR ===
    const loadingBar = document.getElementById('topLoadingBar');
    if (loadingBar) {
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[href]:not([target="_blank"]):not([href^="#"]):not([href^="javascript"])');
            if (link && link.hostname === window.location.hostname) {
                loadingBar.classList.add('active');
            }
        });
        window.addEventListener('beforeunload', function() {
            loadingBar.classList.remove('active');
            loadingBar.classList.add('done');
        });
    }

    // === BUTTON RIPPLE EFFECT ===
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn');
        if (!btn) return;
        const existing = btn.querySelector('.ripple');
        if (existing) existing.remove();
        const ripple = document.createElement('span');
        ripple.classList.add('ripple');
        const rect = btn.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
        ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
        btn.appendChild(ripple);
        ripple.addEventListener('animationend', function() {
            ripple.remove();
        });
    });

    // === STAGGERED CARD ENTRANCE ===
    document.querySelectorAll('.content-area .row > [class*="col-"]').forEach(function(col, i) {
        col.style.opacity = '0';
        col.style.transform = 'translateY(20px)';
        col.style.animation = 'cardEntrance 0.5s cubic-bezier(0.22, 1, 0.36, 1) forwards';
        col.style.animationDelay = (i * 0.05) + 's';
    });

    // === COUNTER ANIMATION ===
    document.querySelectorAll('[data-count]').forEach(function(el) {
        const target = parseInt(el.getAttribute('data-count').replace(/[,.]/g, ''), 10);
        if (isNaN(target) || target === 0) return;
        const duration = 800;
        const start = performance.now();
        el.textContent = '0';

        function step(now) {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(eased * target).toLocaleString('vi-VN');
            if (progress < 1) requestAnimationFrame(step);
            else {
                el.textContent = target.toLocaleString('vi-VN');
                el.classList.add('counting');
            }
        }
        requestAnimationFrame(step);
    });
});
