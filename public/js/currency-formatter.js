/**
 * Currency Formatter for Vietnamese VND
 * Định dạng tiền tệ theo chuẩn Việt Nam với dấu chấm phân cách hàng nghìn
 */

/**
 * Format currency with Vietnamese locale
 * @param {number|string} amount - Số tiền cần format
 * @param {string} currency - Đơn vị tiền tệ (mặc định: VND)
 * @param {boolean} showCurrency - Hiển thị đơn vị tiền tệ (mặc định: true)
 * @returns {string} Chuỗi tiền tệ đã format
 */
function formatCurrency(amount, currency = 'VND', showCurrency = true) {
    if (amount === null || amount === undefined || amount === '') {
        return '0';
    }

    // Convert to number
    const numAmount = parseFloat(amount) || 0;
    
    // Format with dot as thousand separator
    const formatted = new Intl.NumberFormat('vi-VN', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(numAmount);
    
    // Add currency if requested
    if (showCurrency) {
        return formatted + ' ' + currency;
    }
    
    return formatted;
}

/**
 * Format price with đ suffix (shorthand)
 * @param {number|string} amount - Số tiền cần format
 * @returns {string} Chuỗi giá đã format với đuôi đ
 */
function formatPrice(amount) {
    return formatCurrency(amount, 'đ', true);
}

/**
 * Format money without currency symbol
 * @param {number|string} amount - Số tiền cần format
 * @returns {string} Chuỗi số tiền đã format không có đơn vị
 */
function formatMoney(amount) {
    return formatCurrency(amount, '', false);
}

/**
 * Parse formatted currency string back to number
 * @param {string} formattedAmount - Chuỗi tiền tệ đã format
 * @returns {number} Số tiền dạng number
 */
function parseCurrency(formattedAmount) {
    if (!formattedAmount || typeof formattedAmount !== 'string') {
        return 0;
    }
    
    // Remove currency symbols and spaces
    const cleaned = formattedAmount
        .replace(/[^\d.,]/g, '') // Remove all non-digit, non-comma, non-dot
        .replace(/\./g, '') // Remove dots (thousand separators)
        .replace(/,/g, '.'); // Convert comma to dot for decimal
    
    return parseFloat(cleaned) || 0;
}

/**
 * Auto-format currency input fields
 * @param {HTMLInputElement} input - Input element to format
 * @param {Object} options - Formatting options
 */
function autoFormatCurrencyInput(input, options = {}) {
    const {
        currency = 'VND',
        showCurrency = false,
        allowNegative = false
    } = options;
    
    input.addEventListener('input', function(e) {
        let value = e.target.value;
        
        // Remove all non-digit characters
        value = value.replace(/[^\d]/g, '');
        
        if (value === '') {
            e.target.value = '';
            return;
        }
        
        // Convert to number and format
        const numValue = parseInt(value);
        if (!isNaN(numValue)) {
            e.target.value = formatCurrency(numValue, currency, showCurrency);
        }
    });
    
    // Handle paste events
    input.addEventListener('paste', function(e) {
        setTimeout(() => {
            let value = e.target.value;
            const numValue = parseCurrency(value);
            e.target.value = formatCurrency(numValue, currency, showCurrency);
        }, 10);
    });
}

/**
 * Initialize currency formatting for all currency input fields
 */
function initializeCurrencyFormatting() {
    // Auto-format all inputs with data-currency attribute
    document.querySelectorAll('input[data-currency]').forEach(input => {
        const currency = input.dataset.currency || 'VND';
        const showCurrency = input.dataset.showCurrency !== 'false';
        
        autoFormatCurrencyInput(input, {
            currency: currency,
            showCurrency: showCurrency
        });
    });
    
    // Format all existing currency displays
    document.querySelectorAll('[data-format-currency]').forEach(element => {
        const amount = element.textContent || element.innerText;
        const currency = element.dataset.currency || 'VND';
        const showCurrency = element.dataset.showCurrency !== 'false';
        
        const numAmount = parseCurrency(amount);
        element.textContent = formatCurrency(numAmount, currency, showCurrency);
    });
}

/**
 * Update currency display in real-time
 * @param {string} selector - CSS selector for elements to update
 * @param {number} amount - New amount to display
 * @param {Object} options - Formatting options
 */
function updateCurrencyDisplay(selector, amount, options = {}) {
    const {
        currency = 'VND',
        showCurrency = true
    } = options;
    
    document.querySelectorAll(selector).forEach(element => {
        element.textContent = formatCurrency(amount, currency, showCurrency);
    });
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeCurrencyFormatting();
});

// Export functions for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        formatCurrency,
        formatPrice,
        formatMoney,
        parseCurrency,
        autoFormatCurrencyInput,
        initializeCurrencyFormatting,
        updateCurrencyDisplay
    };
}
