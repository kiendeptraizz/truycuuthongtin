/**
 * Service Packages Page Fixes
 * Đảm bảo tất cả các nút và thao tác hiển thị đúng
 */

document.addEventListener('DOMContentLoaded', function() {
    // Function to force show all action elements
    function forceShowActionElements() {
        // Show add button
        const addBtn = document.querySelector('.btn-success');
        if (addBtn) {
            addBtn.style.display = 'inline-flex';
            addBtn.style.visibility = 'visible';
            addBtn.style.opacity = '1';
            addBtn.style.position = 'relative';
            addBtn.style.zIndex = '1';
        }
        
        // Show action columns
        const actionColumns = document.querySelectorAll('.table-action-column');
        actionColumns.forEach((col, index) => {
            col.style.display = 'table-cell';
            col.style.visibility = 'visible';
            col.style.opacity = '1';
            col.style.position = 'sticky';
            col.style.right = '0';
            col.style.background = 'white';
            col.style.zIndex = '10';
            col.style.minWidth = '160px';
            col.style.maxWidth = '160px';
            col.style.width = '160px';
        });
        
        // Show button groups
        const btnGroups = document.querySelectorAll('.table-action-column .btn-group');
        btnGroups.forEach((group, index) => {
            group.style.display = 'flex';
            group.style.visibility = 'visible';
            group.style.opacity = '1';
            group.style.gap = '2px';
            group.style.justifyContent = 'center';
            group.style.alignItems = 'center';
            group.style.whiteSpace = 'nowrap';
        });
        
        // Show action buttons
        const actionBtns = document.querySelectorAll('.table-action-column .btn');
        actionBtns.forEach((btn, index) => {
            btn.style.display = 'flex';
            btn.style.visibility = 'visible';
            btn.style.opacity = '1';
            btn.style.alignItems = 'center';
            btn.style.justifyContent = 'center';
            btn.style.minWidth = '40px';
            btn.style.height = '32px';
            btn.style.flexShrink = '0';
        });
        
        // Show forms
        const actionForms = document.querySelectorAll('.table-action-column form');
        actionForms.forEach((form, index) => {
            form.style.display = 'inline';
            form.style.visibility = 'visible';
            form.style.opacity = '1';
        });
        
        // Show links
        const actionLinks = document.querySelectorAll('.table-action-column a');
        actionLinks.forEach((link, index) => {
            link.style.display = 'flex';
            link.style.visibility = 'visible';
            link.style.opacity = '1';
        });
    }
    
    // Function to ensure table is scrollable
    function ensureTableScrollable() {
        const tableResponsive = document.querySelector('.table-responsive');
        if (tableResponsive) {
            tableResponsive.style.overflowX = 'auto';
            tableResponsive.style.overflowY = 'visible';
        }
        
        const table = document.querySelector('.table');
        if (table) {
            table.style.minWidth = '1200px';
            table.style.marginBottom = '0';
        }
    }
    
    // Function to add event listeners for action buttons
    function addActionButtonListeners() {
        // Delete button confirmations
        const deleteButtons = document.querySelectorAll('button[title="Xóa"]');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const packageName = this.closest('tr').querySelector('.fw-bold').textContent;
                
                if (confirm(`Bạn có chắc chắn muốn xóa gói dịch vụ "${packageName}"?\n\nHành động này không thể hoàn tác!`)) {
                    this.closest('form').submit();
                }
            });
        });
        
        // Status toggle confirmations
        const statusButtons = document.querySelectorAll('button[title*="Tạm dừng"], button[title*="Kích hoạt"]');
        statusButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const packageName = this.closest('tr').querySelector('.fw-bold').textContent;
                const action = this.title;
                
                if (confirm(`Bạn có chắc muốn ${action.toLowerCase()} gói dịch vụ "${packageName}"?`)) {
                    this.closest('form').submit();
                }
            });
        });
        
    }
    
    // Function to highlight table rows on hover
    function addTableRowHighlight() {
        const tableRows = document.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });
        
    }
    
    // Run all fixes immediately
    forceShowActionElements();
    ensureTableScrollable();
    addActionButtonListeners();
    addTableRowHighlight();
    
    // Run fixes again after a short delay to catch any dynamic content
    setTimeout(function() {
        forceShowActionElements();
        ensureTableScrollable();
    }, 500);
    
    // Run fixes when window is resized
    window.addEventListener('resize', function() {
        setTimeout(function() {
            forceShowActionElements();
            ensureTableScrollable();
        }, 100);
    });
    
    // Observer to watch for DOM changes and apply fixes to new elements
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        setTimeout(function() {
                            forceShowActionElements();
                        }, 100);
                    }
                });
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
});

// Export functions for manual use if needed
window.ServicePackagesFix = {
    forceShowElements: function() {
        const addBtn = document.querySelector('.btn-success');
        if (addBtn) {
            addBtn.style.display = 'inline-flex';
            addBtn.style.visibility = 'visible';
            addBtn.style.opacity = '1';
        }
        
        const actionElements = document.querySelectorAll('.table-action-column, .table-action-column .btn-group, .table-action-column .btn, .table-action-column form, .table-action-column a');
        actionElements.forEach(element => {
            element.style.visibility = 'visible';
            element.style.opacity = '1';
            if (element.classList.contains('table-action-column')) {
                element.style.display = 'table-cell';
            } else if (element.classList.contains('btn-group')) {
                element.style.display = 'flex';
            } else if (element.classList.contains('btn') || element.tagName === 'A') {
                element.style.display = 'flex';
            } else if (element.tagName === 'FORM') {
                element.style.display = 'inline';
            }
        });
    }
};
