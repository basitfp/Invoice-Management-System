/* App UI JS helpers (placeholder)
   Add global JS behaviors here: Select2 init, form prevention helpers, validation glue
*/

$(document).ready(function () {
    // Initialize Select2 for any elements marked with .select2
    if ($.fn.select2) {
        $('.select2').select2({
            width: '100%'
        });
    }

    // Prevent double clicks on buttons with data-loading attribute
    $(document).on('click', '[data-loading]', function () {
        var $btn = $(this);
        $btn.prop('disabled', true);
        var loadingText = $btn.data('loading-text') || 'Processing...';
        $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' + loadingText);
    });
});
