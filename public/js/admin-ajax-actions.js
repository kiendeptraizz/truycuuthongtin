/**
 * Generic AJAX helper cho các action xoá/khôi phục trong admin.
 *
 * Cách dùng (Single):
 *   <form data-ajax-action data-row-target="closest:tr" method="POST" action="...">
 *     @csrf
 *     @method('DELETE')   <!-- nếu cần -->
 *     <button type="submit" data-confirm="Xoá ?">...</button>
 *   </form>
 *
 * Sau khi server trả JSON {success: true, message: "...", id?, ids?, stats?}, JS sẽ:
 *   - Toast success message
 *   - Remove element trỏ bởi data-row-target (hoặc closest tr nếu không khai báo)
 *   - Nếu response trả `stats`, cập nhật mọi element [data-stats-key="<key>"]
 *
 * Cách dùng (Bulk):
 *   <form data-ajax-bulk data-row-target="closest:tr" data-checkbox=".row-check"
 *         method="POST" action="...">
 *     @csrf
 *     @method('DELETE')
 *   </form>
 *   ... và button bên ngoài có data-trigger="<form-id>"
 *
 * Có thể dùng:
 *   <form id="bulkForm" data-ajax-bulk ...>
 *   <button data-trigger="bulkForm" data-confirm="Xoá {n} item?">Xoá</button>
 */

(function () {
    'use strict';
    window.__ajaxActionsLoaded = 'v3-' + Date.now();
    console.log('[admin-ajax-actions] LOADED', window.__ajaxActionsLoaded);

    function showToast(message, type = 'success') {
        // Bootstrap 5 toast — fallback alert nếu không có Bootstrap
        if (typeof bootstrap === 'undefined' || !bootstrap.Toast) {
            alert(message);
            return;
        }
        let container = document.getElementById('ajax-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'ajax-toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '11000';
            document.body.appendChild(container);
        }
        const bg = type === 'success' ? 'bg-success' : (type === 'error' ? 'bg-danger' : 'bg-info');
        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-white ${bg} border-0`;
        toastEl.setAttribute('role', 'alert');
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>`;
        container.appendChild(toastEl);
        const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
        toast.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }

    function resolveRowTarget(form, fallback = 'closest:tr') {
        const expr = form.dataset.rowTarget || fallback;
        if (expr.startsWith('closest:')) {
            return form.closest(expr.slice(8));
        }
        if (expr.startsWith('selector:')) {
            return document.querySelector(expr.slice(9));
        }
        return form.closest(expr);
    }

    function updateStats(stats) {
        if (!stats || typeof stats !== 'object') return;
        for (const key in stats) {
            document.querySelectorAll(`[data-stats-key="${key}"]`).forEach(el => {
                el.textContent = Number(stats[key]).toLocaleString('vi-VN');
            });
        }
    }

    async function submitForm(form, extraData = null) {
        const action = form.action;
        const method = (form.querySelector('input[name=_method]')?.value || form.method || 'POST').toUpperCase();
        const fd = new FormData(form);
        if (extraData) {
            for (const [k, v] of Object.entries(extraData)) {
                if (Array.isArray(v)) {
                    v.forEach(item => fd.append(k + '[]', item));
                } else {
                    fd.append(k, v);
                }
            }
        }
        // Force POST (Laravel hidden _method handles real DELETE/PUT)
        const resp = await fetch(action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: fd,
        });
        let data = null;
        try { data = await resp.json(); } catch (e) {}
        if (!resp.ok || !data || data.success === false) {
            const msg = (data && data.message) || `Lỗi ${resp.status}`;
            showToast(msg, 'error');
            return null;
        }
        return data;
    }

    // === SINGLE FORMS ===
    document.addEventListener('submit', async function (ev) {
        const form = ev.target;
        if (!form.matches || !form.matches('form[data-ajax-action]')) return;
        ev.preventDefault();
        ev.stopPropagation();
        console.log('[admin-ajax-actions] intercepted submit', form.action);

        const btn = form.querySelector('button[type=submit]');
        const confirmMsg = btn?.dataset.confirm || form.dataset.confirm;
        if (confirmMsg && !confirm(confirmMsg)) return;

        if (btn) btn.disabled = true;

        const data = await submitForm(form);
        if (btn) btn.disabled = false;

        if (!data) return;

        const row = resolveRowTarget(form);
        if (row) {
            row.style.transition = 'opacity 0.3s';
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 300);
        }
        updateStats(data.stats);
        showToast(data.message || 'Thành công', 'success');
    });

    // === BULK FORMS — triggered by external button ===
    document.addEventListener('click', async function (ev) {
        const trigger = ev.target.closest('[data-trigger]');
        if (!trigger) return;

        const formId = trigger.dataset.trigger;
        const form = document.getElementById(formId);
        if (!form || !form.matches('form[data-ajax-bulk]')) return;

        ev.preventDefault();

        const checkboxSel = form.dataset.checkbox || 'input[type=checkbox][name="ids[]"]';
        const checked = Array.from(form.querySelectorAll(checkboxSel + ':checked'));
        if (checked.length === 0) {
            showToast('Vui lòng chọn ít nhất 1 mục.', 'error');
            return;
        }

        const confirmMsg = trigger.dataset.confirm;
        if (confirmMsg && !confirm(confirmMsg.replace('{n}', checked.length))) return;

        // Override action/method theo trigger nếu có
        const originalAction = form.action;
        const originalMethodInput = form.querySelector('input[name=_method]');
        if (trigger.dataset.action) form.action = trigger.dataset.action;
        if (trigger.dataset.method) {
            if (originalMethodInput) {
                originalMethodInput.value = trigger.dataset.method;
            } else {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = '_method';
                inp.value = trigger.dataset.method;
                form.appendChild(inp);
            }
        }

        trigger.disabled = true;
        const ids = checked.map(cb => cb.value);
        const data = await submitForm(form, { ids });
        trigger.disabled = false;

        // Restore action/method
        form.action = originalAction;
        if (trigger.dataset.method && !originalMethodInput) {
            form.querySelector('input[name=_method]')?.remove();
        } else if (trigger.dataset.method && originalMethodInput) {
            originalMethodInput.value = (form.method || 'POST').toUpperCase();
        }

        if (!data) return;

        // Remove các row tương ứng
        checked.forEach(cb => {
            const row = cb.closest(form.dataset.rowTarget?.startsWith('closest:')
                ? form.dataset.rowTarget.slice(8)
                : 'tr');
            if (row) {
                row.style.transition = 'opacity 0.3s';
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 300);
            }
        });
        updateStats(data.stats);
        showToast(data.message || 'Thành công', 'success');
    });
})();
