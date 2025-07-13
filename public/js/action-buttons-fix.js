// Action Buttons Fix - Đảm bảo tất cả nút hành động hoạt động đúng cách
document.addEventListener("DOMContentLoaded", function () {
    // Đảm bảo tất cả nút trong bảng khách hàng có thể nhấp được
    function ensureButtonsClickable() {
        const actionButtons = document.querySelectorAll(
            ".customers-table .btn-group .btn"
        );

        actionButtons.forEach((button) => {
            // Đặt style để đảm bảo nút có thể nhấp
            button.style.position = "relative";
            button.style.zIndex = "120";
            button.style.pointerEvents = "auto";
            button.style.opacity = "1";
            button.style.visibility = "visible";

            // Đặc biệt cho nút xóa
            if (
                button.classList.contains("delete-btn") ||
                button.classList.contains("btn-outline-danger") ||
                button.querySelector(".fa-trash")
            ) {
                button.style.zIndex = "125";
                button.style.background = "rgba(255, 255, 255, 0.98)";
                button.style.border = "1px solid #dc3545";
                button.style.color = "#dc3545";
            }
        });
    }

    // Kiểm tra và sửa lỗi overflow của table container
    function fixTableOverflow() {
        const tableContainers = document.querySelectorAll(
            ".table-container, .table-responsive"
        );

        tableContainers.forEach((container) => {
            container.style.overflow = "visible";
            container.style.position = "relative";
        });

        // Đảm bảo cột thao tác luôn hiển thị
        const actionColumns = document.querySelectorAll(
            ".customers-table th:nth-child(6), .customers-table td:nth-child(6)"
        );

        actionColumns.forEach((column) => {
            column.style.position = "sticky";
            column.style.right = "0";
            column.style.background = "rgba(255, 255, 255, 0.98)";
            column.style.zIndex = "100";
            column.style.borderLeft = "2px solid #e9ecef";
            column.style.boxShadow = "-2px 0 4px rgba(0, 0, 0, 0.1)";
        });
    }

    // Thêm event listener cho nút xóa
    function setupDeleteButtons() {
        const deleteButtons = document.querySelectorAll(".delete-btn");

        deleteButtons.forEach((button) => {
            button.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();

                const customerName = this.getAttribute("data-customer-name");
                const deleteUrl = this.getAttribute("data-delete-url");

                if (
                    confirm(
                        `Bạn có chắc chắn muốn xóa khách hàng "${customerName}"?`
                    )
                ) {
                    // Tạo form để submit DELETE request
                    const form = document.createElement("form");
                    form.method = "POST";
                    form.action = deleteUrl;

                    // CSRF token
                    const csrfToken = document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content");
                    const csrfInput = document.createElement("input");
                    csrfInput.type = "hidden";
                    csrfInput.name = "_token";
                    csrfInput.value = csrfToken;

                    // Method spoofing
                    const methodInput = document.createElement("input");
                    methodInput.type = "hidden";
                    methodInput.name = "_method";
                    methodInput.value = "DELETE";

                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    }

    // Theo dõi thay đổi DOM để áp dụng fix cho nội dung được load động
    function observeChanges() {
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (
                    mutation.type === "childList" &&
                    mutation.addedNodes.length > 0
                ) {
                    // Delay một chút để DOM render xong
                    setTimeout(() => {
                        ensureButtonsClickable();
                        fixTableOverflow();
                        setupDeleteButtons();
                    }, 100);
                }
            });
        });

        const targetNode =
            document.querySelector(".customers-table tbody") || document.body;
        observer.observe(targetNode, {
            childList: true,
            subtree: true,
        });
    }

    // Chạy các fix khi trang load
    ensureButtonsClickable();
    fixTableOverflow();
    setupDeleteButtons();
    observeChanges();

    // Chạy lại fix khi resize window
    window.addEventListener("resize", function () {
        setTimeout(() => {
            ensureButtonsClickable();
            fixTableOverflow();
        }, 100);
    });

    // Debug function - có thể gọi từ console để kiểm tra
    window.debugActionButtons = function () {
        console.log(
            "Action buttons:",
            document.querySelectorAll(".customers-table .btn-group .btn")
        );
        console.log(
            "Delete buttons:",
            document.querySelectorAll(".delete-btn")
        );
        console.log(
            "Action columns:",
            document.querySelectorAll(
                ".customers-table th:nth-child(6), .customers-table td:nth-child(6)"
            )
        );
    };

    console.log("Action buttons fix initialized successfully");
});
