/* App UI JS helpers */

$(document).ready(function () {
    if ($.fn.select2) {
        $('.select2').select2({
            width: '100%'
        });
    }

    // GLOBAL AJAX LOADER
    $(document).ajaxStart(function () {
        if ($('.global-loader-overlay').length === 0) {
            $('body').append('<div class="global-loader-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        }
    });

    $(document).ajaxStop(function () {
        $('.global-loader-overlay').remove();
    });

    $(document).on('click', 'a.btn:not([data-bs-toggle])', function () {
        if ($(this).attr('href') && $(this).attr('href') !== '#' && !$(this).attr('target')) {
            if ($('.global-loader-overlay').length === 0) {
                $('body').append('<div class="global-loader-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            }
        }
    });

    var FV = window.FormValidation;
    if (!FV) {
        return;
    }

    // Live restrictions & blur validation for marked fields
    FV.bindNameFields('.validate-name, .alpha-only, .letters-only');
    FV.bindEmailFields('.validate-email, input[type="email"]');
    FV.bindPakistaniPhoneFields('.validate-phone, .pak-phone');

    FV.bindNonNegativeNumberFields('input.validate-non-negative, input[data-validate="non-negative"]', {
        negativeMessage: 'Value cannot be negative.'
    });

    FV.bindNonNegativeNumberFields('input.validate-qty-int', {
        integerOnly: true,
        decimalMessage: 'Decimal values are not allowed.'
    });

    // Clear errors as user types valid values (generic)
    $(document).on('input change', '.form-control, .form-select, .invoice-input, .settings-input', function () {
        var $el = $(this);
        if (!$el.hasClass('is-invalid')) {
            return;
        }
        var val = $el.val();
        if (typeof val === 'string') {
            val = val.trim();
        }
        if (val !== '' && val !== null) {
            var $err = $('#' + $el.attr('id') + '-error');
            if ($err.length && $err.text() !== '') {
                // Leave cross-field rules to blur handlers in module scripts
                if ($err.text().indexOf('greater than purchase') === -1) {
                    FV.clearFieldError($el);
                }
            }
        }
    });

    $(document).on('change', 'select', function () {
        var $el = $(this);
        if ($el.val() && $el.val() !== '') {
            FV.clearFieldError($el);
        }
    });
});
