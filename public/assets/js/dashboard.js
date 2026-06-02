/**
 * Custom Dashboard Interactive Script
 * Handles UI interactions and prevents double form submissions on dashboard actions
 */

$(document).ready(function () {
    // Prevent double form submission on dashboard forms (like logout)
    $('form').on('submit', function () {
        const $submitBtn = $(this).find('button[type="submit"]');
        if ($submitBtn.length > 0) {
            $submitBtn.prop('disabled', true);
            // If it's a logout button, display exit state
            if ($submitBtn.hasClass('btn-logout')) {
                $submitBtn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Exiting...');
            } else {
                $submitBtn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Processing...');
            }
        }
    });
});
