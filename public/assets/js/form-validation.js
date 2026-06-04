/**
 * Centralized form validation for Invoice Management System.
 * Depends on jQuery. Load before app-ui.js and module scripts.
 */
(function (window, $) {
    'use strict';

    if (!$) {
        return;
    }

    var FV = {};

    FV.TOAST_MESSAGE = 'Please fix the highlighted errors before submitting.';

    // -------------------------------------------------------------------------
    // Field error UI
    // -------------------------------------------------------------------------

    function getErrorElement($field) {
        if (!$field || !$field.length) {
            return $();
        }

        var id = $field.attr('id');
        if (id) {
            var $byId = $('#' + id + '-error');
            if ($byId.length) {
                return $byId;
            }
        }

        return $field.closest('td, .mb-3, .mb-4, .col-md-6, .col-md-4, .settings-card-body')
            .find('.field-error')
            .first();
    }

    FV.setFieldError = function ($field, message) {
        if (!$field || !$field.length) {
            return;
        }
        $field.addClass('is-invalid');
        var $err = getErrorElement($field);
        if ($err.length) {
            $err.text(message);
        }
    };

    FV.clearFieldError = function ($field) {
        if (!$field || !$field.length) {
            return;
        }
        $field.removeClass('is-invalid');
        var $err = getErrorElement($field);
        if ($err.length) {
            $err.text('');
        }
    };

    /** @deprecated Use setFieldError($('#id'), msg) */
    FV.setErrorById = function (fieldId, message) {
        FV.setFieldError($('#' + fieldId), message);
    };

    FV.clearForm = function ($form) {
        if (!$form || !$form.length) {
            return;
        }
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.field-error').text('');
    };

    FV.clearFormById = function (formId) {
        FV.clearForm($('#' + formId));
    };

    // -------------------------------------------------------------------------
    // Validators
    // -------------------------------------------------------------------------

    FV.isEmpty = function (value) {
        return value === null || value === undefined || String(value).trim() === '';
    };

    FV.isValidEmail = function (email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(email).trim());
    };

    FV.isValidName = function (name) {
        var trimmed = String(name).trim();
        return trimmed.length > 0 && /^[A-Za-z\s]+$/.test(trimmed);
    };

    // FV.isValidPakistaniPhone = function (phone) {
    //     var digits = String(phone).replace(/\D/g, '');
    //     if (digits.length === 11 && digits.charAt(0) === '0' && digits.charAt(1) === '3') {
    //         return true;
    //     }
    //     if (digits.length === 10 && digits.charAt(0) === '3') {
    //         return true;
    //     }
    //     if (digits.length === 12 && digits.substring(0, 2) === '92' && digits.charAt(2) === '3') {
    //         return true;
    //     }
    //     return false;
    // };
    FV.isValidPakistaniPhone = function (phone) {

    var value = String(phone || '')
        .replace(/\s+/g, '')
        .replace(/-/g, '');

    return (

        // Mobile
        /^\+923[0-9]{9}$/.test(value) ||
        /^923[0-9]{9}$/.test(value) ||
        /^03[0-9]{9}$/.test(value) ||
        /^3[0-9]{9}$/.test(value) ||

        // Landline
        /^\+92[1-9][0-9]{8,11}$/.test(value) ||
        /^92[1-9][0-9]{8,11}$/.test(value)

    );
    };

    // FV.sanitizePakistaniPhoneInput = function (raw) {
    //     var value = String(raw);
    //     var digits = value.replace(/\D/g, '');

    //     if (value.indexOf('+') === 0 || digits.indexOf('92') === 0) {
    //         if (digits.indexOf('92') !== 0) {
    //             digits = '92' + digits.replace(/^92/, '');
    //         }
    //         digits = digits.substring(0, 12);
    //         return digits.length ? '+' + digits : '+';
    //     }

    //     if (digits.charAt(0) === '0') {
    //         digits = digits.substring(0, 11);
    //         return digits;
    //     }

    //     if (digits.charAt(0) === '3') {
    //         digits = digits.substring(0, 10);
    //         return digits;
    //     }

    //     return digits.substring(0, 11);
    // };

    FV.sanitizePakistaniPhoneInput = function (raw) {

    var value = String(raw || '').trim();

    // allow only first +
    value = value.replace(/(?!^\+)\+/g, '');

    // remove everything except digits and +
    value = value.replace(/[^\d+]/g, '');

    var digits = value.replace(/\D/g, '');

    // +92 / 92
    if (value.startsWith('+92') || digits.startsWith('92')) {

        digits = digits.replace(/^92/, '');
        digits = digits.replace(/^0/, '');
        digits = digits.substring(0, 10);

        return '+92' + digits;
    }

    // 03xxxxxxxxx
    if (digits.startsWith('03')) {
        return digits.substring(0, 11);
    }

    // 3xxxxxxxxx
    if (digits.startsWith('3')) {
        return digits.substring(0, 10);
    }

    return digits.substring(0, 11);
    };



    FV.isNonNegativeNumber = function (value, allowEmpty) {
        if (FV.isEmpty(value)) {
            return !!allowEmpty;
        }
        var num = parseFloat(value);
        return !isNaN(num) && num >= 0;
    };

    FV.isPositiveNumber = function (value) {
        var num = parseFloat(value);
        return !isNaN(num) && num > 0;
    };

    FV.isPositiveInteger = function (value) {
        if (FV.isEmpty(value)) {
            return false;
        }
        var num = Number(value);
        return Number.isInteger(num) && num > 0;
    };

    FV.isNonNegativeInteger = function (value) {
        if (FV.isEmpty(value)) {
            return false;
        }
        var num = Number(value);
        return Number.isInteger(num) && num >= 0;
    };

    FV.hasNoDecimal = function (value) {
        return String(value).indexOf('.') === -1;
    };

    FV.isVatRate = function (value) {
        return value === '0' || value === 0 || value === '20' || value === 20;
    };

    FV.fieldLabel = function ($field, fallback) {
        var label = $field.attr('data-label');
        if (label) {
            return label;
        }
        var $label = $field.closest('.mb-3, .mb-4, .col-md-6, .col-md-4').find('label').first();
        if ($label.length) {
            return $label.text().replace(/\*/g, '').trim();
        }
        return fallback || 'This field';
    };


    //
    FV.formatPakistaniPhone = function (value) {

    var digits = String(value || '').replace(/\D/g, '');

    // +92xxxxxxxxxx
    if (digits.startsWith('92')) {

        digits = digits.substring(2);
        digits = digits.substring(0, 10);

        if (!digits.length) {
            return '+92';
        }

        if (digits.length <= 3) {
            return '+92 ' + digits;
        }

        return '+92 ' +
            digits.substring(0, 3) +
            ' ' +
            digits.substring(3);
    }

    // 03xxxxxxxxx
    if (digits.startsWith('03')) {

        digits = digits.substring(0, 11);

        if (digits.length <= 4) {
            return digits;
        }

        return digits.substring(0, 4) +
            ' ' +
            digits.substring(4);
    }

    // 3xxxxxxxxx
    if (digits.startsWith('3')) {

        digits = digits.substring(0, 10);

        if (digits.length <= 3) {
            return digits;
        }

        return digits.substring(0, 3) +
            ' ' +
            digits.substring(3);
    }

    return value;
    };

    // -------------------------------------------------------------------------
    // Rule runner (submit: validate all fields at once)
    // -------------------------------------------------------------------------

    /**
     * @param {Array} rules
     * @param {boolean} clearFirst
     * @returns {boolean} true if valid
     */
    FV.runRules = function (rules, clearFirst) {
        var hasError = false;

        rules.forEach(function (rule) {
            var $field = typeof rule.field === 'string' ? $(rule.field) : rule.field;
            if (!$field || !$field.length) {
                return;
            }

            if (clearFirst) {
                FV.clearFieldError($field);
            }

            var value = rule.getValue ? rule.getValue($field) : $field.val();
            if (typeof value === 'string') {
                value = value.trim();
            }

            var label = rule.label || FV.fieldLabel($field);

            if (rule.required && FV.isEmpty(value)) {
                FV.setFieldError($field, rule.requiredMessage || (label + ' is required.'));
                hasError = true;
                return;
            }

            if (!FV.isEmpty(value) && typeof rule.check === 'function') {
                var result = rule.check(value, $field);
                if (result !== true) {
                    FV.setFieldError($field, result);
                    hasError = true;
                }
            }
        });

        return !hasError;
    };

    /**
     * Validate rules without mutating UI (for disabling submit buttons, etc.).
     * @returns {boolean} true if valid
     */
    FV.checkRules = function (rules) {
        var valid = true;

        rules.forEach(function (rule) {
            var $field = typeof rule.field === 'string' ? $(rule.field) : rule.field;
            if (!$field || !$field.length) {
                return;
            }

            var value = rule.getValue ? rule.getValue($field) : $field.val();
            if (typeof value === 'string') {
                value = value.trim();
            }

            if (rule.required && FV.isEmpty(value)) {
                valid = false;
                return;
            }

            if (!FV.isEmpty(value) && typeof rule.check === 'function') {
                if (rule.check(value, $field) !== true) {
                    valid = false;
                }
            }
        });

        return valid;
    };

    FV.showToast = function () {

    if (typeof toastr === 'undefined') {
        return;
    }

    var firstError = $('.field-error')
        .filter(function () {
            return $(this).text().trim() !== '';
        })
        .first()
        .text()
        .trim();

    toastr.error(firstError || FV.TOAST_MESSAGE);
    };

    // -------------------------------------------------------------------------
    // Single-field live validation
    // -------------------------------------------------------------------------

    FV.validateField = function ($field, rules) {
        var value = $field.val();
        if (typeof value === 'string') {
            value = value.trim();
        }
        var label = rules.label || FV.fieldLabel($field);

        if (rules.required && FV.isEmpty(value)) {
            if (rules.showEmpty === false) {
                FV.clearFieldError($field);
                return true;
            }
            FV.setFieldError($field, rules.requiredMessage || (label + ' is required.'));
            return false;
        }

        if (!FV.isEmpty(value) && typeof rules.check === 'function') {
            var result = rules.check(value, $field);
            if (result !== true) {
                FV.setFieldError($field, result);
                return false;
            }
        }

        FV.clearFieldError($field);
        return true;
    };

    FV.bindLiveValidation = function (selector, rules) {
        $(document).on('input blur change', selector, function () {
            var eventType = arguments[arguments.length - 1].type;
            var showEmpty = eventType !== 'input';
            FV.validateField($(this), $.extend({}, rules, { showEmpty: showEmpty }));
        });
    };

    // -------------------------------------------------------------------------
    // Input restrictions
    // -------------------------------------------------------------------------

    FV.filterNameInput = function ($field) {
        var val = $field.val();
        var filtered = val.replace(/[^A-Za-z\s]/g, '');
        if (val !== filtered) {
            $field.val(filtered);
        }
    };

    FV.bindNameFields = function (selector) {
        $(document).on('input', selector, function () {
            var $el = $(this);
            FV.filterNameInput($el);
            if ($el.val().trim() && FV.isValidName($el.val())) {
                FV.clearFieldError($el);
            }
        });
        $(document).on('blur change', selector, function () {
            var $el = $(this);
            var val = $el.val().trim();
            if (!val) {
                return;
            }
            if (!FV.isValidName(val)) {
                FV.setFieldError($el, FV.fieldLabel($el) + ' may only contain letters and spaces.');
            } else {
                FV.clearFieldError($el);
            }
        });
    };

    FV.bindEmailFields = function (selector) {
        $(document).on('input blur change', selector, function () {
            var $el = $(this);
            var val = $el.val().trim();
            var label = FV.fieldLabel($el);
            if (!val) {
                return;
            }
            if (!FV.isValidEmail(val)) {
                FV.setFieldError($el, 'Please enter a valid email address.');
            } else {
                FV.clearFieldError($el);
            }
        });
    };

     FV.bindPakistaniPhoneFields = function (selector) {

    $(document).on('input', selector, function () {

        var $el = $(this);

        var sanitized = FV.sanitizePakistaniPhoneInput($el.val());
        var formatted = FV.formatPakistaniPhone(sanitized);

        if ($el.val() !== formatted) {
            $el.val(formatted);
        }

        if (FV.isValidPakistaniPhone(sanitized)) {
            FV.clearFieldError($el);
        }
    });

    $(document).on('blur change', selector, function () {

        var $el = $(this);
        var val = $el.val().trim();

        if (!val) {
            return;
        }

        if (!FV.isValidPakistaniPhone(val)) {
            FV.setFieldError(
                $el,
                'Please enter a valid Pakistani phone number.'
            );
        } else {
            FV.clearFieldError($el);
        }
    });
    };

    FV.bindNonNegativeNumberFields = function (selector, options) {
        options = options || {};
        $(document).on('input blur change', selector, function () {
            var $el = $(this);
            var val = $el.val();
            if (FV.isEmpty(val)) {
                if (options.required) {
                    return;
                }
                FV.clearFieldError($el);
                return;
            }
            var num = parseFloat(val);
            if (isNaN(num) || num < 0) {
                FV.setFieldError($el, options.negativeMessage || (FV.fieldLabel($el) + ' cannot be negative.'));
            } else if (options.integerOnly && !FV.hasNoDecimal(val)) {
                FV.setFieldError($el, options.decimalMessage || 'Decimal values are not allowed.');
            } else {
                FV.clearFieldError($el);
            }
        });
    };

    // -------------------------------------------------------------------------
    // Preset rule builders
    // -------------------------------------------------------------------------

    FV.rules = {
        required: function ($field, label, message) {
            return {
                field: $field,
                label: label,
                required: true,
                requiredMessage: message
            };
        },
        name: function ($field, label) {
            return {
                field: $field,
                label: label,
                required: true,
                requiredMessage: (label || 'Name') + ' is required.',
                check: function (value) {
                    if (!FV.isValidName(value)) {
                        return (label || 'Name') + ' may only contain letters and spaces.';
                    }
                    return true;
                }
            };
        },
        email: function ($field, label, required) {
            return {
                field: $field,
                label: label,
                required: required !== false,
                requiredMessage: (label || 'Email') + ' is required.',
                check: function (value) {
                    if (!FV.isValidEmail(value)) {
                        return 'Please enter a valid email address.';
                    }
                    return true;
                }
            };
        },
        phone: function ($field, label, required) {
            return {
                field: $field,
                label: label,
                required: required !== false,
                requiredMessage: (label || 'Phone number') + ' is required.',
                check: function (value) {
                    if (!FV.isValidPakistaniPhone(value)) {
                        return 'Please enter a valid Pakistani phone number.';
                    }
                    return true;
                }
            };
        },
        nonNegativeNumber: function ($field, label, required) {
            return {
                field: $field,
                label: label,
                required: !!required,
                requiredMessage: (label || 'This field') + ' is required.',
                check: function (value) {
                    if (!FV.isNonNegativeNumber(value)) {
                        return (label || 'Value') + ' must be zero or greater.';
                    }
                    return true;
                }
            };
        },
        positiveNumber: function ($field, label) {
            return {
                field: $field,
                label: label,
                required: true,
                requiredMessage: (label || 'This field') + ' is required.',
                check: function (value) {
                    if (!FV.isPositiveNumber(value)) {
                        return (label || 'Value') + ' must be greater than zero.';
                    }
                    return true;
                }
            };
        },
        positiveInteger: function ($field, label) {
            return {
                field: $field,
                label: label,
                required: true,
                requiredMessage: (label || 'Quantity') + ' is required.',
                check: function (value) {
                    if (!FV.hasNoDecimal(value)) {
                        return 'Decimal values are not allowed.';
                    }
                    if (!FV.isPositiveInteger(value)) {
                        return (label || 'Quantity') + ' must be greater than zero.';
                    }
                    return true;
                }
            };
        },
        nonNegativeInteger: function ($field, label) {
            return {
                field: $field,
                label: label,
                required: true,
                check: function (value) {
                    if (!FV.hasNoDecimal(value)) {
                        return 'Decimal values are not allowed.';
                    }
                    if (!FV.isNonNegativeInteger(value)) {
                        return (label || 'Value') + ' must be zero or greater.';
                    }
                    return true;
                }
            };
        },
        select: function ($field, label) {
            return {
                field: $field,
                label: label,
                required: true,
                getValue: function ($f) {
                    var v = $f.val();
                    return v === null || v === undefined ? '' : String(v);
                },
                requiredMessage: (label || 'This field') + ' is required.'
            };
        },
        sellingGreaterThanPurchase: function ($selling, $purchase) {
            return {
                field: $selling,
                label: 'Selling price',
                check: function () {
                    var selling = parseFloat($selling.val());
                    var purchase = parseFloat($purchase.val());
                    if (isNaN(selling) || isNaN(purchase)) {
                        return true;
                    }
                    if (selling <= purchase) {
                        return 'Selling price must be greater than purchase price.';
                    }
                    return true;
                }
            };
        }
    };

    window.FormValidation = FV;

    window.showGlobalValidationError = (function () {
        var timeout;
        return function () {
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                FV.showToast();
            }, 100);
        };
    })();

})(window, window.jQuery);
