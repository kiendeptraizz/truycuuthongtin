/**
 * Enhanced Tables - Fast and smooth table functionality
 * Optimized for local use with minimal overhead
 */

class EnhancedTable {
    constructor(tableElement) {
        this.table = tableElement;
        this.tbody = this.table.querySelector('tbody');
        this.originalRows = Array.from(this.tbody.querySelectorAll('tr'));
        this.currentRows = [...this.originalRows];
        this.init();
    }

    init() {
        this.addSearchFunctionality();
        this.addSortFunctionality();
        this.addRowHoverEffects();
        this.addBulkActions();
    }

    // Fast search functionality
    addSearchFunctionality() {
        const searchInput = document.querySelector(`[data-table-search="${this.table.id}"]`);
        if (!searchInput) return;

        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.search(e.target.value.toLowerCase());
            }, 150); // Debounce for performance
        });
    }

    search(query) {
        if (!query.trim()) {
            this.showAllRows();
            return;
        }

        this.currentRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const shouldShow = text.includes(query);
            row.style.display = shouldShow ? '' : 'none';
        });

        this.updateRowCount();
    }

    // Simple sort functionality
    addSortFunctionality() {
        const headers = this.table.querySelectorAll('th[data-sort]');
        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.innerHTML += ' <i class="fas fa-sort text-muted ms-1"></i>';
            
            header.addEventListener('click', () => {
                const column = header.dataset.sort;
                const currentDirection = header.dataset.direction || 'asc';
                const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
                
                this.sort(column, newDirection);
                
                // Update header indicators
                headers.forEach(h => {
                    h.dataset.direction = '';
                    h.querySelector('i').className = 'fas fa-sort text-muted ms-1';
                });
                
                header.dataset.direction = newDirection;
                header.querySelector('i').className = `fas fa-sort-${newDirection === 'asc' ? 'up' : 'down'} text-primary ms-1`;
            });
        });
    }

    sort(column, direction) {
        const columnIndex = Array.from(this.table.querySelectorAll('th')).findIndex(th => th.dataset.sort === column);
        if (columnIndex === -1) return;

        this.currentRows.sort((a, b) => {
            const aValue = a.cells[columnIndex]?.textContent.trim() || '';
            const bValue = b.cells[columnIndex]?.textContent.trim() || '';
            
            // Try to parse as numbers
            const aNum = parseFloat(aValue.replace(/[^\d.-]/g, ''));
            const bNum = parseFloat(bValue.replace(/[^\d.-]/g, ''));
            
            let comparison;
            if (!isNaN(aNum) && !isNaN(bNum)) {
                comparison = aNum - bNum;
            } else {
                comparison = aValue.localeCompare(bValue, 'vi');
            }
            
            return direction === 'asc' ? comparison : -comparison;
        });

        // Re-append rows in new order
        this.currentRows.forEach(row => this.tbody.appendChild(row));
    }

    // Smooth hover effects
    addRowHoverEffects() {
        this.currentRows.forEach(row => {
            row.addEventListener('mouseenter', () => {
                row.style.transform = 'translateX(2px)';
                row.style.transition = 'transform 0.1s ease';
            });
            
            row.addEventListener('mouseleave', () => {
                row.style.transform = '';
            });
        });
    }

    // Bulk actions functionality
    addBulkActions() {
        const bulkContainer = document.querySelector(`[data-bulk-actions="${this.table.id}"]`);
        if (!bulkContainer) return;

        // Add checkboxes to rows
        this.currentRows.forEach((row, index) => {
            const firstCell = row.cells[0];
            if (firstCell && !firstCell.querySelector('input[type="checkbox"]')) {
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'form-check-input me-2';
                checkbox.dataset.rowIndex = index;
                firstCell.insertBefore(checkbox, firstCell.firstChild);
            }
        });

        // Add select all checkbox to header
        const firstHeader = this.table.querySelector('th');
        if (firstHeader && !firstHeader.querySelector('input[type="checkbox"]')) {
            const selectAllCheckbox = document.createElement('input');
            selectAllCheckbox.type = 'checkbox';
            selectAllCheckbox.className = 'form-check-input me-2';
            selectAllCheckbox.addEventListener('change', (e) => {
                const checkboxes = this.table.querySelectorAll('tbody input[type="checkbox"]');
                checkboxes.forEach(cb => cb.checked = e.target.checked);
                this.updateBulkActions();
            });
            firstHeader.insertBefore(selectAllCheckbox, firstHeader.firstChild);
        }

        // Listen for checkbox changes
        this.table.addEventListener('change', (e) => {
            if (e.target.type === 'checkbox') {
                this.updateBulkActions();
            }
        });
    }

    updateBulkActions() {
        const checkedBoxes = this.table.querySelectorAll('tbody input[type="checkbox"]:checked');
        const bulkContainer = document.querySelector(`[data-bulk-actions="${this.table.id}"]`);
        
        if (bulkContainer) {
            bulkContainer.style.display = checkedBoxes.length > 0 ? 'block' : 'none';
            const countElement = bulkContainer.querySelector('[data-selected-count]');
            if (countElement) {
                countElement.textContent = checkedBoxes.length;
            }
        }
    }

    showAllRows() {
        this.currentRows.forEach(row => row.style.display = '');
        this.updateRowCount();
    }

    updateRowCount() {
        const visibleRows = this.currentRows.filter(row => row.style.display !== 'none');
        const countElement = document.querySelector(`[data-table-count="${this.table.id}"]`);
        if (countElement) {
            countElement.textContent = `Hiển thị ${visibleRows.length} / ${this.currentRows.length} bản ghi`;
        }
    }

    // Export functionality
    exportToCSV() {
        const visibleRows = this.currentRows.filter(row => row.style.display !== 'none');
        const headers = Array.from(this.table.querySelectorAll('th')).map(th => th.textContent.trim());
        
        let csv = headers.join(',') + '\n';
        
        visibleRows.forEach(row => {
            const cells = Array.from(row.cells).map(cell => {
                return '"' + cell.textContent.trim().replace(/"/g, '""') + '"';
            });
            csv += cells.join(',') + '\n';
        });

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `export_${new Date().toISOString().split('T')[0]}.csv`;
        link.click();
    }
}

// Auto-initialize enhanced tables
document.addEventListener('DOMContentLoaded', function() {
    const tables = document.querySelectorAll('.enhanced-table');
    tables.forEach(table => {
        if (!table.id) {
            table.id = 'table_' + Math.random().toString(36).substr(2, 9);
        }
        new EnhancedTable(table);
    });

    // Add export buttons functionality
    document.querySelectorAll('[data-export-csv]').forEach(button => {
        button.addEventListener('click', function() {
            const tableId = this.dataset.exportCsv;
            const table = document.getElementById(tableId);
            if (table && table.enhancedTable) {
                table.enhancedTable.exportToCSV();
            }
        });
    });
});

// Utility functions for smooth interactions
function showLoading(element) {
    element.style.opacity = '0.6';
    element.style.pointerEvents = 'none';
}

function hideLoading(element) {
    element.style.opacity = '';
    element.style.pointerEvents = '';
}

// Fast form submission with loading state
document.addEventListener('submit', function(e) {
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
    
    if (submitBtn && !submitBtn.disabled) {
        submitBtn.disabled = true;
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
        
        // Re-enable after 10 seconds as fallback
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 10000);
    }
});

// Smooth page transitions
document.addEventListener('click', function(e) {
    const link = e.target.closest('a[href]');
    if (link && link.href && !link.target && !e.ctrlKey && !e.metaKey) {
        const href = link.href;
        if (href.includes(window.location.origin) && !href.includes('#')) {
            e.preventDefault();
            showLoading(document.body);
            window.location.href = href;
        }
    }
});
