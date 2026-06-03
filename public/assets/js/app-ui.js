/* App UI JS helpers 
   Add global JS behaviors here: Select2 init, form prevention helpers, validation glue
*/

$(document).ready(function () {
    // Initialize Select2 for any elements marked with .select2
    if ($.fn.select2) {
        $('.select2').select2({
            width: '100%'
        });
    }

    // ==========================================
    // GLOBAL AJAX LOADER
    // ==========================================
    $(document).ajaxStart(function () {
        if ($('.global-loader-overlay').length === 0) {
            $('body').append('<div class="global-loader-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        }
    });

    $(document).ajaxStop(function () {
        $('.global-loader-overlay').remove();
    });

    $(document).on('click', 'a.btn:not([data-bs-toggle])', function (e) {
        if ($(this).attr('href') && $(this).attr('href') !== '#' && !$(this).attr('target')) {
            if ($('.global-loader-overlay').length === 0) {
                $('body').append('<div class="global-loader-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            }
        }
    });

    // ==========================================
    // GLOBAL VALIDATION HELPERS
    // ==========================================
    window.debounce = function (func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };

    window.showGlobalValidationError = window.debounce(function () {
        if (typeof toastr !== 'undefined') {
            toastr.error('Please fill in all required fields properly');
        }
    }, 500);

    // ==========================================
    // KEYDOWN / INPUT RESTRICTIONS
    // ==========================================

    // Numeric only (block letters)
    $(document).on('keydown', '.numeric-only, input[type="number"]', function (e) {
        // Allow: backspace, delete, tab, escape, enter, numpad decimal, period
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    // Letters only (block numbers)
    $(document).on('keydown', '.letters-only, .alpha-only', function (e) {
        var key = e.keyCode;
        // Allow spaces (32), letters (65-90), backspace (8), tab (9), delete (46), arrows (37-40)
        if ((key >= 65 && key <= 90) || key === 32 || key === 8 || key === 9 || key === 46 || (key >= 37 && key <= 40) || (key === 65 && (e.ctrlKey || e.metaKey))) {
            return;
        }
        e.preventDefault();
    });

    // ==========================================
    // ONBLUR / ONCHANGE REQUIRED VALIDATION
    // ==========================================
    
    // Generic empty field check on blur for inputs with 'required' attribute or 'validate-required' class
    $(document).on('blur', 'input[required], select[required], textarea[required], .validate-required', function () {
        if ($(this).val().trim() === '') {
            $(this).addClass('is-invalid');
            
            // Check if there's a specific error span for this
            var errorSpanId = $(this).attr('id') + '-error';
            var errorSpan = $('#' + errorSpanId);
            if (errorSpan.length && errorSpan.text() === '') {
                // Determine field name for message
                var name = $(this).prev('label').text() || $(this).attr('name') || 'This field';
                errorSpan.text(name.replace('*', '').trim() + ' is required.');
            }
        } else {
            $(this).removeClass('is-invalid');
            var errorSpanId2 = $(this).attr('id') + '-error';
            var errorSpan2 = $('#' + errorSpanId2);
            if (errorSpan2.length) {
                errorSpan2.text('');
            }
        }
    });

    // Handle selects on change to instantly remove errors
    $(document).on('change', 'select', function () {
        if ($(this).val() && $(this).val() !== '') {
            $(this).removeClass('is-invalid');
            var errorSpanId = $(this).attr('id') + '-error';
            $('#' + errorSpanId).text('');
        }
    });

});
