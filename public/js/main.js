// Main Application JavaScript
// File: main.js
// Description: Core functionality and utilities
// Created: 2024-01-17

class POSApplication {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupFormValidation();
        this.initializeToolTips();
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Auto-hide alerts after 5 seconds
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });

        // Form submission
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        });
    }

    /**
     * Setup form validation
     */
    setupFormValidation() {
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }

    /**
     * Initialize Bootstrap tooltips
     */
    initializeToolTips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }

    /**
     * Handle form submission
     */
    handleFormSubmit(event) {
        const form = event.target;
        const formData = new FormData(form);

        // Show loading indicator
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        }
    }

    /**
     * API call helper
     */
    async apiCall(endpoint, method = 'GET', data = null) {
        try {
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            };

            if (data) {
                options.body = JSON.stringify(data);
            }

            const response = await fetch(`/pos-system/api/v1${endpoint}`, options);
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'API Error');
            }

            return result;
        } catch (error) {
            console.error('API Error:', error);
            this.showError(error.message);
            return null;
        }
    }

    /**
     * Show success message
     */
    showSuccess(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.prepend(alertDiv);

        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertDiv);
            bsAlert.close();
        }, 5000);
    }

    /**
     * Show error message
     */
    showError(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.prepend(alertDiv);

        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertDiv);
            bsAlert.close();
        }, 5000);
    }

    /**
     * Format currency
     */
    formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    }

    /**
     * Format date
     */
    formatDate(date) {
        return new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        }).format(new Date(date));
    }

    /**
     * Debounce function
     */
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Show loading spinner
     */
    showLoading() {
        const spinner = document.createElement('div');
        spinner.className = 'position-fixed top-50 start-50 translate-middle';
        spinner.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        `;
        document.body.appendChild(spinner);
    }

    /**
     * Hide loading spinner
     */
    hideLoading() {
        const spinner = document.querySelector('.spinner-border');
        if (spinner) {
            spinner.parentElement.remove();
        }
    }
}

// Initialize application when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.app = new POSApplication();
});

// Utility functions

/**
 * Validate email format
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Validate phone number
 */
function validatePhone(phone) {
    const re = /^\d{10,15}$/;
    return re.test(phone.replace(/\D/g, ''));
}

/**
 * Format phone number
 */
function formatPhone(phone) {
    const cleaned = phone.replace(/\D/g, '');
    const match = cleaned.match(/^(\d{3})(\d{3})(\d{4})$/);
    if (match) {
        return `(${match[1]}) ${match[2]}-${match[3]}`;
    }
    return phone;
}

/**
 * Debounce search input
 */
const debounceSearch = (func, wait = 500) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

/**
 * Copy to clipboard
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        window.app?.showSuccess('Copied to clipboard!');
    }).catch(() => {
        window.app?.showError('Failed to copy to clipboard');
    });
}

/**
 * Print element
 */
function printElement(elementId) {
    const element = document.getElementById(elementId);
    if (!element) return;

    const printWindow = window.open('', '', 'height=500,width=800');
    printWindow.document.write('<html><head><title>Print</title>');
    printWindow.document.write('</head><body >');
    printWindow.document.write(element.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

/**
 * Export to CSV
 */
function exportToCSV(filename, data) {
    let csv = '';
    data.forEach(row => {
        csv += row.join(',') + '\n';
    });

    const link = document.createElement('a');
    link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Export to JSON
 */
function exportToJSON(filename, data) {
    const json = JSON.stringify(data, null, 2);
    const link = document.createElement('a');
    link.href = 'data:application/json;charset=utf-8,' + encodeURIComponent(json);
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
    // Ctrl+S: Save
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        const form = document.querySelector('form');
        if (form) form.submit();
    }

    // Ctrl+P: Print
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        window.print();
    }

    // Escape: Close modals
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) bsModal.hide();
        });
    }
});
