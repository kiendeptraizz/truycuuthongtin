/**
 * Global Horizontal Scroll Enhancement
 * Đảm bảo tất cả các trang có thể cuộn ngang khi cần thiết
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Function to apply horizontal scroll to elements
    function applyHorizontalScroll() {
        // Main content areas
        const mainContent = document.querySelector('.main-content');
        const contentArea = document.querySelector('.content-area');
        
        if (mainContent) {
            mainContent.style.overflowX = 'auto';
            mainContent.style.overflowY = 'visible';
            mainContent.style.scrollBehavior = 'smooth';
            mainContent.style.webkitOverflowScrolling = 'touch';
        }
        
        if (contentArea) {
            contentArea.style.overflowX = 'auto';
            contentArea.style.overflowY = 'visible';
            contentArea.style.scrollBehavior = 'smooth';
            contentArea.style.webkitOverflowScrolling = 'touch';
        }
        
        // All cards
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.style.overflowX = 'auto';
            card.style.overflowY = 'visible';
            card.style.scrollBehavior = 'smooth';
            card.style.webkitOverflowScrolling = 'touch';
        });
        
        // All card bodies
        const cardBodies = document.querySelectorAll('.card-body');
        cardBodies.forEach(cardBody => {
            cardBody.style.overflowX = 'auto';
            cardBody.style.overflowY = 'visible';
            cardBody.style.scrollBehavior = 'smooth';
            cardBody.style.webkitOverflowScrolling = 'touch';
        });
        
        // All table responsive containers
        const tableResponsive = document.querySelectorAll('.table-responsive, .table-responsive-enhanced');
        tableResponsive.forEach(table => {
            table.style.overflowX = 'auto';
            table.style.overflowY = 'visible';
            table.style.scrollBehavior = 'smooth';
            table.style.webkitOverflowScrolling = 'touch';
        });
        
        // All rows
        const rows = document.querySelectorAll('.row');
        rows.forEach(row => {
            row.style.overflowX = 'auto';
            row.style.overflowY = 'visible';
            row.style.scrollBehavior = 'smooth';
            row.style.webkitOverflowScrolling = 'touch';
        });
        
        // All columns
        const columns = document.querySelectorAll('[class*="col-"]');
        columns.forEach(col => {
            col.style.overflowX = 'auto';
            col.style.overflowY = 'visible';
            col.style.scrollBehavior = 'smooth';
            col.style.webkitOverflowScrolling = 'touch';
        });
        
        // Container fluid
        const containerFluid = document.querySelectorAll('.container-fluid');
        containerFluid.forEach(container => {
            container.style.overflowX = 'auto';
            container.style.overflowY = 'visible';
            container.style.scrollBehavior = 'smooth';
            container.style.webkitOverflowScrolling = 'touch';
        });
        
        // Button groups
        const btnGroups = document.querySelectorAll('.btn-group');
        btnGroups.forEach(btnGroup => {
            btnGroup.style.overflowX = 'auto';
            btnGroup.style.overflowY = 'visible';
            btnGroup.style.scrollBehavior = 'smooth';
            btnGroup.style.webkitOverflowScrolling = 'touch';
            btnGroup.style.whiteSpace = 'nowrap';
        });
        
        // Modal content
        const modalContent = document.querySelectorAll('.modal-content');
        modalContent.forEach(modal => {
            modal.style.overflowX = 'auto';
            modal.style.overflowY = 'visible';
            modal.style.scrollBehavior = 'smooth';
            modal.style.webkitOverflowScrolling = 'touch';
        });
        
        // Dropdown menus
        const dropdownMenus = document.querySelectorAll('.dropdown-menu');
        dropdownMenus.forEach(dropdown => {
            dropdown.style.overflowX = 'auto';
            dropdown.style.overflowY = 'visible';
            dropdown.style.scrollBehavior = 'smooth';
            dropdown.style.webkitOverflowScrolling = 'touch';
        });
    }
    
    // Function to handle window resize
    function handleResize() {
        applyHorizontalScroll();
        
        // Adjust max-width based on screen size and sidebar
        const mainContent = document.querySelector('.main-content');
        if (mainContent) {
            const sidebarWidth = window.innerWidth > 1024 ? 240 : 0;
            const maxWidth = window.innerWidth - sidebarWidth - 30;
            mainContent.style.maxWidth = maxWidth + 'px';
        }
    }
    
    // Function to observe DOM changes and apply horizontal scroll to new elements
    function observeDOM() {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            // Apply horizontal scroll to new elements
                            if (node.classList && (
                                node.classList.contains('card') ||
                                node.classList.contains('table-responsive') ||
                                node.classList.contains('table-responsive-enhanced') ||
                                node.classList.contains('row') ||
                                node.classList.contains('btn-group') ||
                                node.classList.contains('modal-content') ||
                                node.classList.contains('dropdown-menu') ||
                                node.className.includes('col-')
                            )) {
                                node.style.overflowX = 'auto';
                                node.style.overflowY = 'visible';
                                node.style.scrollBehavior = 'smooth';
                                node.style.webkitOverflowScrolling = 'touch';
                            }
                            
                            // Apply to child elements as well
                            const childElements = node.querySelectorAll('.card, .table-responsive, .table-responsive-enhanced, .row, .btn-group, .modal-content, .dropdown-menu, [class*="col-"]');
                            childElements.forEach(child => {
                                child.style.overflowX = 'auto';
                                child.style.overflowY = 'visible';
                                child.style.scrollBehavior = 'smooth';
                                child.style.webkitOverflowScrolling = 'touch';
                            });
                        }
                    });
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    // Apply horizontal scroll on page load
    applyHorizontalScroll();
    
    // Handle window resize
    window.addEventListener('resize', handleResize);
    
    // Observe DOM changes
    observeDOM();
    
    // Apply horizontal scroll after AJAX requests (for dynamic content)
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        return originalFetch.apply(this, args).then(response => {
            setTimeout(applyHorizontalScroll, 100);
            return response;
        });
    };
    
    // Apply horizontal scroll after jQuery AJAX requests
    if (window.jQuery) {
        $(document).ajaxComplete(function() {
            setTimeout(applyHorizontalScroll, 100);
        });
    }
    
    // Force horizontal scroll on specific events
    document.addEventListener('click', function(e) {
        // If clicking on tabs, dropdowns, or other interactive elements
        if (e.target.closest('.nav-tabs, .dropdown-toggle, .btn-group')) {
            setTimeout(applyHorizontalScroll, 100);
        }
    });
    
    // Apply horizontal scroll when modals are shown
    document.addEventListener('shown.bs.modal', function() {
        setTimeout(applyHorizontalScroll, 100);
    });
    
    // Apply horizontal scroll when dropdowns are shown
    document.addEventListener('shown.bs.dropdown', function() {
        setTimeout(applyHorizontalScroll, 100);
    });
    
    console.log('Global horizontal scroll enhancement loaded successfully');
});

// Export functions for manual use if needed
window.HorizontalScrollUtils = {
    applyToElement: function(element) {
        if (element) {
            element.style.overflowX = 'auto';
            element.style.overflowY = 'visible';
            element.style.scrollBehavior = 'smooth';
            element.style.webkitOverflowScrolling = 'touch';
        }
    },
    
    applyToSelector: function(selector) {
        const elements = document.querySelectorAll(selector);
        elements.forEach(element => {
            this.applyToElement(element);
        });
    }
};
