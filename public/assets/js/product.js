/**
 * Product Module - Fully Fixed & Improved
 */

$(document).ready(function () {

    // -------------------------
    // Helper Functions
    // -------------------------

    function sanitizeText($input) {
        let val = $input.val().trim();
        val = val.replace(/\s{2,}/g, ' ');
        $input.val(val);
    }

    function isValidName(value) {
        const trimmed = value.trim();
        return trimmed.length >= 2 && trimmed.length <= 100;
    }

    function isValidNumber(value) {
        return value !== '' && !isNaN(value) && Number(value) >= 0;
    }

    function isValidMOQ(value) {
        return value !== '' && !isNaN(value) && Number(value) >= 1;
    }

    function isValidVAT(value) {
        return value === "0" || value === "20";
    }

    function showError($input, message) {
        $input.addClass('is-invalid').removeClass('is-valid');
        let $feedback = $input.closest('.mb-3').find('.invalid-feedback');
        if ($feedback.length === 0) {
            $input.after(`<div class="invalid-feedback">${message}</div>`);
        } else {
            $feedback.text(message);
        }
    }

    function clearError($input) {
        $input.removeClass('is-invalid').addClass('is-valid');
        $input.closest('.mb-3').find('.invalid-feedback').text('');
    }

    // -------------------------
    // Real-time Validation
    // -------------------------

    $(document).on('keyup input change blur', '.product-input', function () {
        const $input = $(this);
        const $form = $input.closest('form');

        setTimeout(() => {
            if ($input.hasClass('product-name')) sanitizeText($input);

            const name   = $form.find('.product-name').val();
            const qty    = $form.find('.product-qty').val();
            const moq    = $form.find('.product-moq').val();
            const vat    = $form.find('.product-vat').val();
            const buy    = $form.find('.product-purchase').val();
            const sell   = $form.find('.product-selling').val();

            // Name Validation
            if ($input.hasClass('product-name')) {
                if (!isValidName(name)) {
                    showError($input, 'Product name must be 2–100 characters.');
                } else {
                    clearError($input);
                }
            }

            // Quantity
            if ($input.hasClass('product-qty')) {
                if (!isValidNumber(qty)) {
                    showError($input, 'Quantity must be 0 or more.');
                } else {
                    clearError($input);
                }
            }

            // MOQ
            if ($input.hasClass('product-moq')) {
                if (!isValidMOQ(moq)) {
                    showError($input, 'MOQ must be at least 1.');
                } else {
                    clearError($input);
                }
            }

            // VAT
            if ($input.hasClass('product-vat')) {
                if (!isValidVAT(vat)) {
                    showError($input, 'VAT must be 0% or 20% only.');
                } else {
                    clearError($input);
                }
            }

            // Price Validation
            if ($input.hasClass('product-purchase') || $input.hasClass('product-selling')) {
                if (!isValidNumber(buy) || !isValidNumber(sell)) {
                    showError($input, 'Prices must be valid numbers.');
                } else if (Number(sell) < Number(buy)) {
                    showError($input, 'Selling price cannot be lower than purchase price.');
                } else {
                    clearError($input);
                }
            }

            checkFormValidity($form);
        }, 10);
    });

    // -------------------------
    // Form Validity Check
    // -------------------------

    function checkFormValidity($form) {
        const name = $form.find('.product-name').val();
        const qty  = $form.find('.product-qty').val();
        const moq  = $form.find('.product-moq').val();
        const vat  = $form.find('.product-vat').val();
        const buy  = $form.find('.product-purchase').val();
        const sell = $form.find('.product-selling').val();

        const valid =
            isValidName(name) &&
            isValidNumber(qty) &&
            isValidMOQ(moq) &&
            isValidVAT(vat) &&
            isValidNumber(buy) &&
            isValidNumber(sell) &&
            Number(sell) >= Number(buy);

        $form.find('button[type="submit"]').prop('disabled', !valid);
        return valid;
    }

    // -------------------------
    // Create Modal
    // -------------------------

    $('#productCreateModal').on('show.bs.modal', function () {
        const $form = $('#productCreateForm');
        $form[0].reset();
        $form.find('.form-control').removeClass('is-valid is-invalid');
        $form.find('.invalid-feedback').remove();
        checkFormValidity($form);
    });

    // -------------------------
    // Edit Modal
    // -------------------------

 // -------------------------
// Edit Modal - FIXED with AJAX
// -------------------------

$(document).on('click', '.btn-product-edit', function () {
    const id = $(this).data('id');
    const $form = $('#productEditForm');

    $form[0].reset();
    $form.find('.form-control').removeClass('is-valid is-invalid');
    $form.find('.invalid-feedback').remove();
    $form.attr('action', `/admin/products/${id}`);

    $.get(`/admin/products/${id}/edit`, function (product) {
        console.log('Product Data:', product); // For debugging

        $('#e-id').val(product.id);
        $('#e-name').val(product.name);
        $('#e-desc').val(product.description || '');
        $('#e-qty').val(product.qty);
        $('#e-moq').val(product.moq);
        $('#e-purchase').val(product.purchase_price);
        $('#e-selling').val(product.selling_price);
        $('#e-vat').val(product.vat);
        $('#e-status').val(product.status ? 1 : 0);

        // Category Select
        if (product.category_id) {
            $('#e-category').html(`<option value="${product.category_id}">${product.category ? product.category.name : 'N/A'}</option>`);
        }

        checkFormValidity($form);
    }).fail(function () {
        alert('Failed to load product data. Please check console.');
    });

    new bootstrap.Modal(document.getElementById('productEditModal')).show();
});

    // -------------------------
    // View Modal
    // -------------------------

    $(document).on('click', '.btn-product-view', function () {
        $('#v-name').text($(this).data('name'));
        $('#v-desc').text($(this).data('desc') || '—');
        $('#v-category').text($(this).data('category'));
        $('#v-qty').text($(this).data('qty'));
        $('#v-moq').text($(this).data('moq'));
        $('#v-purchase').text($(this).data('purchase'));
        $('#v-selling').text($(this).data('selling'));
        $('#v-vat').text($(this).data('vat') + '%');

        const status = parseInt($(this).data('status')) === 1
            ? '<span class="badge-status-enabled">Enabled</span>'
            : '<span class="badge-status-disabled">Disabled</span>';

        $('#v-status').html(status);

        new bootstrap.Modal(document.getElementById('productViewModal')).show();
    });

    // -------------------------
    // Toggle Status
    // -------------------------

// -------------------------
// Toggle Status - FIXED
// -------------------------

$(document).on('click', '.btn-product-toggle', function () {
    const id = $(this).data('id');
    const currentStatus = parseInt($(this).data('status'));

    const $form = $('#productConfirmForm');
    
    // ✅ Correct URL
    $form.attr('action', `/admin/products/${id}/toggle-status`);

    $('#confirm-title').text(currentStatus ? 'Disable Product' : 'Enable Product');
    $('#confirm-body').html(
        `Are you sure you want to <strong>${currentStatus ? 'disable' : 'enable'}</strong> this product?`
    );

    new bootstrap.Modal(document.getElementById('productConfirmModal')).show();
});

    // -------------------------
    // Submit Loading State
    // -------------------------

    $(document).on('submit', '#productCreateForm, #productEditForm, #productConfirmForm', function () {
        const $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm me-2"></span>Processing...
        `);
    });

});