/**
 * Enhanced Forms - Fast and smooth form functionality
 * Optimized for local use with minimal overhead
 */

class EnhancedForm {
    constructor(formElement) {
        this.form = formElement;
        this.fields = this.form.querySelectorAll('input, select, textarea');
        this.submitButton = this.form.querySelector('button[type="submit"], input[type="submit"]');
        this.init();
    }

    init() {
        this.addRealTimeValidation();
        this.addAutoSave();
        this.addSubmitHandler();
        this.addFieldEnhancements();
    }

    // Real-time validation
    addRealTimeValidation() {
        this.fields.forEach(field => {
            // Remove existing validation on input
            field.addEventListener('input', () => {
                this.clearFieldError(field);
                this.validateField(field);
            });

            // Validate on blur for better UX
            field.addEventListener('blur', () => {
                this.validateField(field);
            });
        });
    }

    validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name || field.id;
        let isValid = true;
        let errorMessage = '';

        // Required validation
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = `${this.getFieldLabel(field)} là bắt buộc.`;
        }

        // Email validation
        if (field.type === 'email' && value && !this.isValidEmail(value)) {
            isValid = false;
            errorMessage = 'Email không hợp lệ.';
        }

        // Phone validation
        if (field.type === 'tel' && value && !this.isValidPhone(value)) {
            isValid = false;
            errorMessage = 'Số điện thoại không hợp lệ.';
        }

        // Min length validation
        const minLength = field.getAttribute('minlength');
        if (minLength && value && value.length < parseInt(minLength)) {
            isValid = false;
            errorMessage = `${this.getFieldLabel(field)} phải có ít nhất ${minLength} ký tự.`;
        }

        // Max length validation
        const maxLength = field.getAttribute('maxlength');
        if (maxLength && value && value.length > parseInt(maxLength)) {
            isValid = false;
            errorMessage = `${this.getFieldLabel(field)} không được vượt quá ${maxLength} ký tự.`;
        }

        // Custom pattern validation
        const pattern = field.getAttribute('pattern');
        if (pattern && value && !new RegExp(pattern).test(value)) {
            isValid = false;
            errorMessage = field.getAttribute('data-pattern-message') || 'Định dạng không hợp lệ.';
        }

        // Show/hide error
        if (!isValid) {
            this.showFieldError(field, errorMessage);
        } else {
            this.clearFieldError(field);
        }

        return isValid;
    }

    showFieldError(field, message) {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');

        // Remove existing error message
        this.clearFieldError(field, false);

        // Add new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }

    clearFieldError(field, addValid = true) {
        field.classList.remove('is-invalid');
        if (addValid && field.value.trim()) {
            field.classList.add('is-valid');
        } else {
            field.classList.remove('is-valid');
        }

        // Remove error message
        const errorDiv = field.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    getFieldLabel(field) {
        const label = this.form.querySelector(`label[for="${field.id}"]`);
        return label ? label.textContent.replace('*', '').trim() : field.name || 'Trường này';
    }

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    isValidPhone(phone) {
        return /^[\d\s\-\+\(\)]{10,15}$/.test(phone);
    }

    // Auto-save functionality (for drafts)
    addAutoSave() {
        if (!this.form.dataset.autoSave) return;

        const saveKey = `form_draft_${this.form.id || 'default'}`;
        
        // Load saved data
        this.loadDraft(saveKey);

        // Save on input with debounce
        let saveTimeout;
        this.fields.forEach(field => {
            field.addEventListener('input', () => {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    this.saveDraft(saveKey);
                }, 1000);
            });
        });
    }

    saveDraft(key) {
        const formData = {};
        this.fields.forEach(field => {
            if (field.type !== 'password') {
                formData[field.name] = field.value;
            }
        });
        localStorage.setItem(key, JSON.stringify(formData));
        
        // Show save indicator
        this.showSaveIndicator();
    }

    loadDraft(key) {
        const savedData = localStorage.getItem(key);
        if (savedData) {
            try {
                const formData = JSON.parse(savedData);
                Object.keys(formData).forEach(fieldName => {
                    const field = this.form.querySelector(`[name="${fieldName}"]`);
                    if (field && field.type !== 'password') {
                        field.value = formData[fieldName];
                    }
                });
            } catch (e) {
                console.warn('Could not load form draft:', e);
            }
        }
    }

    showSaveIndicator() {
        // Simple save indicator
        const indicator = document.createElement('small');
        indicator.className = 'text-muted ms-2';
        indicator.innerHTML = '<i class="fas fa-check text-success"></i> Đã lưu tự động';
        
        // Remove existing indicator
        const existing = this.form.querySelector('.auto-save-indicator');
        if (existing) existing.remove();
        
        indicator.className += ' auto-save-indicator';
        this.submitButton?.parentNode.appendChild(indicator);
        
        // Remove after 2 seconds
        setTimeout(() => indicator.remove(), 2000);
    }

    // Enhanced submit handler
    addSubmitHandler() {
        this.form.addEventListener('submit', (e) => {
            // Validate all fields
            let isFormValid = true;
            this.fields.forEach(field => {
                if (!this.validateField(field)) {
                    isFormValid = false;
                }
            });

            if (!isFormValid) {
                e.preventDefault();
                // Focus first invalid field
                const firstInvalid = this.form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            // Show loading state
            if (this.submitButton) {
                this.submitButton.disabled = true;
                const originalText = this.submitButton.innerHTML;
                this.submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
                
                // Clear auto-save draft on successful submit
                const saveKey = `form_draft_${this.form.id || 'default'}`;
                localStorage.removeItem(saveKey);
                
                // Re-enable after timeout as fallback
                setTimeout(() => {
                    this.submitButton.disabled = false;
                    this.submitButton.innerHTML = originalText;
                }, 10000);
            }
        });
    }

    // Field enhancements
    addFieldEnhancements() {
        this.fields.forEach(field => {
            // Auto-format phone numbers
            if (field.type === 'tel') {
                field.addEventListener('input', (e) => {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length >= 10) {
                        value = value.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
                    }
                    e.target.value = value;
                });
            }

            // Auto-capitalize names
            if (field.dataset.capitalize) {
                field.addEventListener('input', (e) => {
                    e.target.value = e.target.value.replace(/\b\w/g, l => l.toUpperCase());
                });
            }

            // Character counter for textareas
            if (field.tagName === 'TEXTAREA' && field.hasAttribute('maxlength')) {
                this.addCharacterCounter(field);
            }
        });
    }

    addCharacterCounter(textarea) {
        const maxLength = parseInt(textarea.getAttribute('maxlength'));
        const counter = document.createElement('small');
        counter.className = 'text-muted character-counter';
        textarea.parentNode.appendChild(counter);

        const updateCounter = () => {
            const remaining = maxLength - textarea.value.length;
            counter.textContent = `${textarea.value.length}/${maxLength} ký tự`;
            counter.className = remaining < 20 ? 'text-warning character-counter' : 'text-muted character-counter';
        };

        textarea.addEventListener('input', updateCounter);
        updateCounter();
    }
}

// Auto-initialize enhanced forms
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.enhanced-form, form[data-enhance]');
    forms.forEach(form => {
        new EnhancedForm(form);
    });
});

// Utility functions
window.FormUtils = {
    // Clear all form fields
    clearForm: function(formSelector) {
        const form = document.querySelector(formSelector);
        if (form) {
            form.reset();
            form.querySelectorAll('.is-invalid, .is-valid').forEach(field => {
                field.classList.remove('is-invalid', 'is-valid');
            });
            form.querySelectorAll('.invalid-feedback').forEach(error => {
                error.remove();
            });
        }
    },

    // Populate form with data
    populateForm: function(formSelector, data) {
        const form = document.querySelector(formSelector);
        if (form) {
            Object.keys(data).forEach(key => {
                const field = form.querySelector(`[name="${key}"]`);
                if (field) {
                    field.value = data[key];
                }
            });
        }
    },

    // Get form data as object
    getFormData: function(formSelector) {
        const form = document.querySelector(formSelector);
        if (form) {
            const formData = new FormData(form);
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            return data;
        }
        return {};
    }
};
