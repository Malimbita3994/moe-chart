/**
 * SweetAlert2 Helper Functions
 * Provides consistent alert and confirmation dialogs throughout the application
 */

// Default SweetAlert2 configuration
const SwalConfig = {
    confirmButtonColor: '#D4AF37',
    cancelButtonColor: '#6B7280',
    confirmButtonText: 'Yes, proceed',
    cancelButtonText: 'Cancel',
    buttonsStyling: true,
    customClass: {
        confirmButton: 'swal2-confirm',
        cancelButton: 'swal2-cancel',
        popup: 'swal2-popup-custom'
    }
};

/**
 * Show a confirmation dialog
 * @param {string} title - Dialog title
 * @param {string} text - Dialog message
 * @param {string} confirmText - Confirm button text
 * @param {string} cancelText - Cancel button text
 * @returns {Promise} - Promise that resolves to true if confirmed, false if cancelled
 */
function confirmAction(title = 'Are you sure?', text = 'This action cannot be undone.', confirmText = 'Yes, proceed', cancelText = 'Cancel') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: SwalConfig.confirmButtonColor,
        cancelButtonColor: SwalConfig.cancelButtonColor,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        buttonsStyling: true,
        customClass: SwalConfig.customClass,
        reverseButtons: true
    }).then((result) => {
        return result.isConfirmed;
    });
}

/**
 * Show a delete confirmation dialog
 * @param {string} itemName - Name of the item being deleted
 * @param {string} itemType - Type of item (e.g., 'user', 'position', 'unit')
 * @returns {Promise} - Promise that resolves to true if confirmed, false if cancelled
 */
function confirmDelete(itemName = '', itemType = 'item') {
    const title = itemName ? `Delete ${itemType}?` : 'Delete this item?';
    const text = itemName 
        ? `Are you sure you want to delete "${itemName}"? This action cannot be undone.`
        : `Are you sure you want to delete this ${itemType}? This action cannot be undone.`;
    
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: SwalConfig.cancelButtonColor,
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
        buttonsStyling: true,
        customClass: SwalConfig.customClass,
        reverseButtons: true
    }).then((result) => {
        return result.isConfirmed;
    });
}

/**
 * Show a success message
 * @param {string} title - Success title
 * @param {string} text - Success message
 * @param {number} timer - Auto-close timer in milliseconds (default: 3000)
 */
function showSuccess(title = 'Success!', text = '', timer = 3000) {
    Swal.fire({
        icon: 'success',
        title: title,
        text: text,
        timer: timer,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        customClass: SwalConfig.customClass
    });
}

/**
 * Show an error message
 * @param {string} title - Error title
 * @param {string} text - Error message
 */
function showError(title = 'Error!', text = 'Something went wrong.') {
    Swal.fire({
        icon: 'error',
        title: title,
        text: text,
        confirmButtonColor: SwalConfig.confirmButtonColor,
        customClass: SwalConfig.customClass
    });
}

/**
 * Show an info message
 * @param {string} title - Info title
 * @param {string} text - Info message
 */
function showInfo(title = 'Information', text = '') {
    Swal.fire({
        icon: 'info',
        title: title,
        text: text,
        confirmButtonColor: SwalConfig.confirmButtonColor,
        customClass: SwalConfig.customClass
    });
}

/**
 * Show a warning message
 * @param {string} title - Warning title
 * @param {string} text - Warning message
 */
function showWarning(title = 'Warning!', text = '') {
    Swal.fire({
        icon: 'warning',
        title: title,
        text: text,
        confirmButtonColor: SwalConfig.confirmButtonColor,
        customClass: SwalConfig.customClass
    });
}

/**
 * Show a loading message
 * @param {string} title - Loading title
 * @param {string} text - Loading message
 */
function showLoading(title = 'Please wait...', text = 'Processing your request.') {
    Swal.fire({
        title: title,
        text: text,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
        customClass: SwalConfig.customClass
    });
}

/**
 * Close the current SweetAlert dialog
 */
function closeSwal() {
    Swal.close();
}

/**
 * Handle form submission with confirmation
 * @param {Event} event - Form submit event
 * @param {string} title - Confirmation title
 * @param {string} text - Confirmation message
 * @param {string} confirmText - Confirm button text
 * @returns {boolean} - Returns false to prevent default submission
 */
function handleFormSubmit(event, title = 'Are you sure?', text = 'This action cannot be undone.', confirmText = 'Yes, proceed') {
    event.preventDefault();
    const form = event.target;
    
    confirmAction(title, text, confirmText).then((confirmed) => {
        if (confirmed) {
            form.submit();
        }
    });
    
    return false;
}

/**
 * Handle delete form submission
 * @param {Event} event - Form submit event
 * @param {string} itemName - Name of item being deleted
 * @param {string} itemType - Type of item
 * @returns {boolean} - Returns false to prevent default submission
 */
function handleDeleteSubmit(event, itemName = '', itemType = 'item') {
    event.preventDefault();
    const form = event.target;
    
    confirmDelete(itemName, itemType).then((confirmed) => {
        if (confirmed) {
            form.submit();
        }
    });
    
    return false;
}

// Initialize SweetAlert2 styles
document.addEventListener('DOMContentLoaded', function() {
    // Add custom styles for SweetAlert2
    const style = document.createElement('style');
    style.textContent = `
        .swal2-popup-custom {
            font-family: 'Inter', sans-serif;
        }
        .swal2-confirm {
            background-color: #D4AF37 !important;
            color: #1F2937 !important;
            font-weight: 600 !important;
            border-radius: 0.5rem !important;
            padding: 0.625rem 1.5rem !important;
        }
        .swal2-confirm:hover {
            background-color: #C4A027 !important;
        }
        .swal2-cancel {
            background-color: #6B7280 !important;
            color: white !important;
            font-weight: 600 !important;
            border-radius: 0.5rem !important;
            padding: 0.625rem 1.5rem !important;
        }
        .swal2-cancel:hover {
            background-color: #4B5563 !important;
        }
        .swal2-toast {
            font-family: 'Inter', sans-serif;
        }
    `;
    document.head.appendChild(style);
    
    // Convert flash messages to SweetAlert2 toasts
    const successMsg = document.getElementById('flash-success');
    const errorMsg = document.getElementById('flash-error');
    const infoMsg = document.getElementById('flash-info');
    const warningMsg = document.getElementById('flash-warning');
    
    if (successMsg && successMsg.textContent.trim()) {
        showSuccess('Success!', successMsg.textContent.trim());
    }
    
    if (errorMsg && errorMsg.textContent.trim()) {
        showError('Error!', errorMsg.textContent.trim());
    }
    
    if (infoMsg && infoMsg.textContent.trim()) {
        showInfo('Information', infoMsg.textContent.trim());
    }
    
    if (warningMsg && warningMsg.textContent.trim()) {
        showWarning('Warning!', warningMsg.textContent.trim());
    }
});
