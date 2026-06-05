/**
 * Product Module — AJAX + Validation + Dropzone
 * Depends on: jQuery, FormValidation (FV), EntitySync (ES), Dropzone, toastr
 */

(function ($) {
    'use strict';

    var FV = window.FormValidation;
    var ES = window.EntitySync;

    // =========================================================================
    // Dropzone instances
    // =========================================================================

    var createDropzone = null;
    var editDropzone   = null;

    var ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    var MAX_FILE_SIZE = 2; // MB

    Dropzone.autoDiscover = false;

    function initCreateDropzone() {
        if (createDropzone) {
            createDropzone.destroy();
            createDropzone = null;
        }

        createDropzone = new Dropzone('#createProductDropzone', {
            url: 'javascript:void(0)',
            autoProcessQueue: false,
            maxFiles: 1,
            maxFilesize: MAX_FILE_SIZE,
            acceptedFiles: ALLOWED_TYPES.join(','),
            addRemoveLinks: true,
            dictRemoveFile: '&times; Remove',
            dictMaxFilesExceeded: 'Only one image can be uploaded.',
            init: function () {
                var dz = this;

                dz.on('addedfile', function () {
                    if (dz.files.length > 1) {
                        dz.removeFile(dz.files[0]);
                    }
                    clearImageError('c');
                });

                dz.on('error', function (file, message) {
                    if (typeof message === 'string') {
                        showImageError('c', message);
                    } else {
                        showImageError('c', 'Invalid file. Use JPG, PNG or WEBP under 2 MB.');
                    }
                    dz.removeFile(file);
                });

                dz.on('removedfile', function () {
                    clearImageError('c');
                });
            }
        });
    }

    function initEditDropzone() {
        if (editDropzone) {
            editDropzone.destroy();
            editDropzone = null;
        }

        editDropzone = new Dropzone('#editProductDropzone', {
            url: 'javascript:void(0)',
            autoProcessQueue: false,
            maxFiles: 1,
            maxFilesize: MAX_FILE_SIZE,
            acceptedFiles: ALLOWED_TYPES.join(','),
            addRemoveLinks: true,
            dictRemoveFile: '&times; Remove',
            dictMaxFilesExceeded: 'Only one image can be uploaded.',
            init: function () {
                var dz = this;

                dz.on('addedfile', function () {
                    if (dz.files.length > 1) {
                        dz.removeFile(dz.files[0]);
                    }
                    clearImageError('e');
                });

                dz.on('error', function (file, message) {
                    if (typeof message === 'string') {
                        showImageError('e', message);
                    } else {
                        showImageError('e', 'Invalid file. Use JPG, PNG or WEBP under 2 MB.');
                    }
                    dz.removeFile(file);
                });

                dz.on('removedfile', function () {
                    clearImageError('e');
                });
            }
        });
    }

    function showImageError(prefix, msg) {
        $('#' + prefix + '-image-error').text(msg);
    }

    function clearImageError(prefix) {
        $('#' + prefix + '-image-error').text('');
    }

    function validateDropzoneImage(dz, prefix) {
        if (!dz || dz.files.length === 0) {
            return true;
        }
        var file = dz.files[0];
        if (ALLOWED_TYPES.indexOf(file.type) === -1) {
            showImageError(prefix, 'Only JPG, JPEG, PNG, WEBP files are allowed.');
            return false;
        }
        if (file.size > MAX_FILE_SIZE * 1024 * 1024) {
            showImageError(prefix, 'Image must be under 2 MB.');
            return false;
        }
        clearImageError(prefix);
        return true;
    }

    // =========================================================================
    // Validation rule builders
    // =========================================================================

    function getCreateRules() {
        return [
            {
                field: '#c-name',
                label: 'Product Name',
                required: true,
                requiredMessage: 'Product Name is required.',
                check: function (value) {
                    if (!/^[A-Za-z0-9\s]+$/.test(value)) {
                        return 'Name may only contain letters, numbers and spaces.';
                    }
                    return true;
                }
            },
            {
                field: '#c-item_code',
                label: 'Item Code',
                required: false,
                check: function (value) {
                    if (!/^[A-Za-z0-9\-_]+$/.test(value.trim())) {
                        return 'Item Code may only contain letters, numbers, hyphens and underscores.';
                    }
                    return true;
                }
            },
            {
                field: '#c-item_class',
                label: 'Item Class',
                required: true,
                requiredMessage: 'Item Class is required.',
                getValue: function ($f) { return String($f.val() || '').trim(); }
            },
            {
                field: '#c-category_id',
                label: 'Category',
                required: true,
                requiredMessage: 'Category is required.',
                getValue: function ($f) { return String($f.val() || '').trim(); }
            },
            {
                field: '#c-purchase_price',
                label: 'Purchase Price',
                required: false,
                check: function (value) {
                    var num = parseFloat(value);
                    if (isNaN(num) || num < 0) {
                        return 'Purchase Price must be zero or greater.';
                    }
                    return true;
                }
            },
            {
                field: '#c-purchase_tax_percent',
                label: 'Purchase Tax %',
                required: false,
                check: function (value) {
                    var num = parseFloat(value);
                    if (isNaN(num) || num < 0 || num > 100) {
                        return 'Purchase Tax must be between 0 and 100.';
                    }
                    return true;
                }
            },
            {
                field: '#c-sale_price',
                label: 'Sale Price',
                required: true,
                requiredMessage: 'Sale Price is required.',
                check: function (value) {
                    var sale     = parseFloat(value);
                    var purchase = parseFloat($('#c-purchase_price').val());
                    if (isNaN(sale) || sale < 0) {
                        return 'Sale Price must be zero or greater.';
                    }
                    if (!isNaN(purchase) && purchase > 0 && sale <= purchase) {
                        return 'Sale Price must be greater than Purchase Price.';
                    }
                    return true;
                }
            },
            {
                field: '#c-gst_vat_percent',
                label: 'GST / VAT %',
                required: false,
                check: function (value) {
                    var num = parseFloat(value);
                    if (isNaN(num) || num < 0 || num > 100) {
                        return 'GST / VAT % must be between 0 and 100.';
                    }
                    return true;
                }
            },
            {
                field: '#c-discount_percent',
                label: 'Discount %',
                required: false,
                check: function (value) {
                    var num = parseFloat(value);
                    if (isNaN(num) || num < 0 || num > 100) {
                        return 'Discount % must be between 0 and 100.';
                    }
                    return true;
                }
            },
            {
                field: '#c-cess_percent',
                label: 'Cess %',
                required: false,
                check: function (value) {
                    var num = parseFloat(value);
                    if (isNaN(num) || num < 0 || num > 100) {
                        return 'Cess % must be between 0 and 100.';
                    }
                    return true;
                }
            },
            {
                field: '#c-additional_cess',
                label: 'Additional Cess',
                required: false,
                check: function (value) {
                    var num = parseFloat(value);
                    if (isNaN(num) || num < 0) {
                        return 'Additional Cess must be zero or greater.';
                    }
                    return true;
                }
            },
            {
                field: '#c-qty',
                label: 'Quantity',
                required: true,
                requiredMessage: 'Quantity is required.',
                check: function (value) {
                    if (String(value).indexOf('.') !== -1) {
                        return 'Quantity must be a whole number.';
                    }
                    var num = parseInt(value, 10);
                    if (isNaN(num) || num < 0) {
                        return 'Quantity must be zero or greater.';
                    }
                    return true;
                }
            },
            {
                field: '#c-moq',
                label: 'MOQ',
                required: true,
                requiredMessage: 'MOQ is required.',
                check: function (value) {
                    if (String(value).indexOf('.') !== -1) {
                        return 'MOQ must be a whole number.';
                    }
                    var num = parseInt(value, 10);
                    if (isNaN(num) || num < 1) {
                        return 'MOQ must be at least 1.';
                    }
                    var qty = parseInt($('#c-qty').val(), 10);
                    if (!isNaN(qty) && num > qty) {
                        return 'MOQ cannot be greater than available Quantity.';
                    }
                    return true;
                }
            }
        ];
    }

    function getEditRules() {
        return [
            {
                field: '#e-name',
                label: 'Product Name',
                required: true,
                requiredMessage: 'Product Name is required.',
                check: function (value) {
                    if (!/^[A-Za-z0-9\s]+$/.test(value)) {
                        return 'Name may only contain letters, numbers and spaces.';
                    }
                    return true;
                }
            },
            {
                field: '#e-item_code',
                label: 'Item Code',
                required: false,
                check: function (value) {
                    if (!/^[A-Za-z0-9\-_]+$/.test(value.trim())) {
                        return 'Item Code may only contain letters, numbers, hyphens and underscores.';
                    }
                    return true;
                }
            },
            {
                field: '#e-item_class',
                label: 'Item Class',
                required: true,
                requiredMessage: 'Item Class is required.',
                getValue: function ($f) { return String($f.val() || '').trim(); }
            },
            {
                field: '#e-category_id',
                label: 'Category',
                required: true,
                requiredMessage: 'Category is required.',
                getValue: function ($f) { return String($f.val() || '').trim(); }
            },
            {
                field: '#e-purchase_price',
                label: 'Purchase Price',
                required: false,
                check: function (value) {
                    var num = parseFloat(value);
                    if (isNaN(num) || num < 0) {
                        return 'Purchase Price must be zero or greater.';
                    }
                    return true;
                }
            },
            {
                field: '#e-purchase_tax_percent',
                label: 'Purchase Tax %',
                required: false,
                check: function (value) {
                    var num = parseFloat(value);
                    if (isNaN(num) || num < 0 || num > 100) {
                        return 'Purchase Tax must be between 0 and 100.';
                    }
                    return true;
                }
            },
            {
                field: '#e-sale_price',
                label: 'Sale Price',
                required: true,
                requiredMessage: 'Sale Price is required.',
                check: function (value) {
                    var sale     = parseFloat(value);
                    var purchase = parseFloat($('#e-purchase_price').val());
                    if (isNaN(sale) || sale < 0) {
                        return 'Sale Price must be zero or greater.';
                    }
                    if (!isNaN(purchase) && purchase > 0 && sale <= purchase) {
                        return 'Sale Price must be greater than Purchase Price.';
                    }
                    return true;
                }
            },
            {
                field: '#e-gst_vat_percent',
                label: 'GST / VAT %',
                required: false,
                check: function (value) {
                    var num = parseFloat(value);
                    if (isNaN(num) || num < 0 || num > 100) {
                        return 'GST / VAT % must be between 0 and 100.';
                    }
                    return true;
                }
            },
            {
                field: '#e-discount_percent',
                label: 'Discount %',
                required: false,
                check: function (value) {
                    var num = parseFloat(value);
                    if (isNaN(num) || num < 0 || num > 100) {
                        return 'Discount % must be between 0 and 100.';
                    }
                    return true;
                }
            },
            {
                field: '#e-cess_percent',
                label: 'Cess %',
                required: false,
                check: function (value) {
                    var num = parseFloat(value);
                    if (isNaN(num) || num < 0 || num > 100) {
                        return 'Cess % must be between 0 and 100.';
                    }
                    return true;
                }
            },
            {
                field: '#e-additional_cess',
                label: 'Additional Cess',
                required: false,
                check: function (value) {
                    var num = parseFloat(value);
                    if (isNaN(num) || num < 0) {
                        return 'Additional Cess must be zero or greater.';
                    }
                    return true;
                }
            },
            {
                field: '#e-qty',
                label: 'Quantity',
                required: true,
                requiredMessage: 'Quantity is required.',
                check: function (value) {
                    if (String(value).indexOf('.') !== -1) {
                        return 'Quantity must be a whole number.';
                    }
                    var num = parseInt(value, 10);
                    if (isNaN(num) || num < 0) {
                        return 'Quantity must be zero or greater.';
                    }
                    return true;
                }
            },
            {
                field: '#e-moq',
                label: 'MOQ',
                required: true,
                requiredMessage: 'MOQ is required.',
                check: function (value) {
                    if (String(value).indexOf('.') !== -1) {
                        return 'MOQ must be a whole number.';
                    }
                    var num = parseInt(value, 10);
                    if (isNaN(num) || num < 1) {
                        return 'MOQ must be at least 1.';
                    }
                    var qty = parseInt($('#e-qty').val(), 10);
                    if (!isNaN(qty) && num > qty) {
                        return 'MOQ cannot be greater than available Quantity.';
                    }
                    return true;
                }
            }
        ];
    }

    // =========================================================================
    // Submit button state helpers
    // =========================================================================

    function setCreateBusy(busy) {
        var $btn = $('#createProductBtn');
        $btn.prop('disabled', busy);
        $('#createProductBtnText').text(busy ? 'Saving...' : 'Save Product');
        $('#createProductBtnSpinner').toggle(busy);
    }

    function setEditBusy(busy) {
        var $btn = $('#updateProductBtn');
        $btn.prop('disabled', busy);
        $('#updateProductBtnText').text(busy ? 'Updating...' : 'Update Product');
        $('#updateProductBtnSpinner').toggle(busy);
    }

    function setConfirmBusy(busy) {
        $('#confirm-submit-btn').prop('disabled', busy);
        $('#confirm-btn-text').text(busy ? 'Processing...' : 'Confirm');
        $('#confirm-btn-spinner').toggle(busy);
    }

    // =========================================================================
    // Reset helpers
    // =========================================================================

    function resetCreateModal() {
        FV.clearForm($('#productCreateForm'));
        document.getElementById('productCreateForm').reset();

        if (createDropzone) {
            createDropzone.removeAllFiles(true);
        }
        clearImageError('c');
    }

    function resetEditModal() {
        FV.clearForm($('#productEditForm'));

        if (editDropzone) {
            editDropzone.removeAllFiles(true);
        }
        clearImageError('e');
        $('#edit_image_preview_wrapper').hide();
        $('#edit_current_image').attr('src', '');
        $('#edit-remove-image').val('0');
    }

    // =========================================================================
    // Real-time (live) validation bindings — Create modal
    // =========================================================================

    function bindCreateLive() {

        // Name
        $(document).on('input', '#c-name', function () {
            var $el = $(this);
            var val = $el.val().replace(/[^A-Za-z0-9\s]/g, '');
            if ($el.val() !== val) {
                $el.val(val);
            }
            if (val.trim()) {
                FV.clearFieldError($el);
            }
        });
        $(document).on('blur', '#c-name', function () {
            FV.runRules([getCreateRules()[0]], false);
        });

        // Item Code — allow alphanumeric, hyphen, underscore only
        $(document).on('keypress', '#c-item_code', function (e) {
            var char = String.fromCharCode(e.which);
            if (!/[A-Za-z0-9\-_]/.test(char)) {
                e.preventDefault();
            }
        });
        $(document).on('blur', '#c-item_code', function () {
            var val = $(this).val().trim();
            if (val) {
                FV.runRules([getCreateRules()[1]], false);
            } else {
                FV.clearFieldError($(this));
            }
        });

        // Numbers — prevent invalid keypresses
        $(document).on('keypress', '#c-purchase_price, #c-purchase_tax_percent, #c-sale_price, #c-gst_vat_percent, #c-discount_percent, #c-cess_percent, #c-additional_cess', function (e) {
            var char = String.fromCharCode(e.which);
            if (!/[\d.]/.test(char)) {
                e.preventDefault();
            }
        });

        // Integers only for qty and moq
        $(document).on('keypress', '#c-qty, #c-moq', function (e) {
            var char = String.fromCharCode(e.which);
            if (!/\d/.test(char)) {
                e.preventDefault();
            }
        });

        // Sale price cross-field check on blur
        $(document).on('blur', '#c-sale_price, #c-purchase_price', function () {
            var $saleField = $('#c-sale_price');
            if ($saleField.val()) {
                FV.validateField($saleField, {
                    label: 'Sale Price',
                    check: function (value) {
                        var sale     = parseFloat(value);
                        var purchase = parseFloat($('#c-purchase_price').val());
                        if (!isNaN(purchase) && purchase > 0 && sale <= purchase) {
                            return 'Sale Price must be greater than Purchase Price.';
                        }
                        return true;
                    }
                });
            }
        });

        // MOQ vs Qty cross-field
        $(document).on('blur', '#c-moq, #c-qty', function () {
            var $moqField = $('#c-moq');
            if ($moqField.val()) {
                var moq = parseInt($moqField.val(), 10);
                var qty = parseInt($('#c-qty').val(), 10);
                if (!isNaN(moq) && !isNaN(qty) && moq > qty) {
                    FV.setFieldError($moqField, 'MOQ cannot be greater than available Quantity.');
                } else {
                    FV.clearFieldError($moqField);
                }
            }
        });

        // Generic number range clearing
        $(document).on('input', '#c-purchase_price, #c-purchase_tax_percent, #c-sale_price, #c-gst_vat_percent, #c-discount_percent, #c-cess_percent, #c-additional_cess, #c-qty, #c-moq', function () {
            var $el = $(this);
            if ($el.hasClass('is-invalid') && $el.val() !== '') {
                var errText = $('#' + $el.attr('id') + '-error').text();
                if (errText.indexOf('greater than Purchase') === -1 && errText.indexOf('greater than available') === -1) {
                    FV.clearFieldError($el);
                }
            }
        });

        // Selects
        $(document).on('change', '#c-category_id, #c-item_class', function () {
            if ($(this).val()) {
                FV.clearFieldError($(this));
            }
        });
    }

    // =========================================================================
    // Real-time (live) validation bindings — Edit modal
    // =========================================================================

    function bindEditLive() {

        $(document).on('input', '#e-name', function () {
            var $el = $(this);
            var val = $el.val().replace(/[^A-Za-z0-9\s]/g, '');
            if ($el.val() !== val) {
                $el.val(val);
            }
            if (val.trim()) {
                FV.clearFieldError($el);
            }
        });
        $(document).on('blur', '#e-name', function () {
            FV.runRules([getEditRules()[0]], false);
        });

        $(document).on('keypress', '#e-item_code', function (e) {
            var char = String.fromCharCode(e.which);
            if (!/[A-Za-z0-9\-_]/.test(char)) {
                e.preventDefault();
            }
        });
        $(document).on('blur', '#e-item_code', function () {
            var val = $(this).val().trim();
            if (val) {
                FV.runRules([getEditRules()[1]], false);
            } else {
                FV.clearFieldError($(this));
            }
        });

        $(document).on('keypress', '#e-purchase_price, #e-purchase_tax_percent, #e-sale_price, #e-gst_vat_percent, #e-discount_percent, #e-cess_percent, #e-additional_cess', function (e) {
            var char = String.fromCharCode(e.which);
            if (!/[\d.]/.test(char)) {
                e.preventDefault();
            }
        });

        $(document).on('keypress', '#e-qty, #e-moq', function (e) {
            var char = String.fromCharCode(e.which);
            if (!/\d/.test(char)) {
                e.preventDefault();
            }
        });

        $(document).on('blur', '#e-sale_price, #e-purchase_price', function () {
            var $saleField = $('#e-sale_price');
            if ($saleField.val()) {
                FV.validateField($saleField, {
                    label: 'Sale Price',
                    check: function (value) {
                        var sale     = parseFloat(value);
                        var purchase = parseFloat($('#e-purchase_price').val());
                        if (!isNaN(purchase) && purchase > 0 && sale <= purchase) {
                            return 'Sale Price must be greater than Purchase Price.';
                        }
                        return true;
                    }
                });
            }
        });

        $(document).on('blur', '#e-moq, #e-qty', function () {
            var $moqField = $('#e-moq');
            if ($moqField.val()) {
                var moq = parseInt($moqField.val(), 10);
                var qty = parseInt($('#e-qty').val(), 10);
                if (!isNaN(moq) && !isNaN(qty) && moq > qty) {
                    FV.setFieldError($moqField, 'MOQ cannot be greater than available Quantity.');
                } else {
                    FV.clearFieldError($moqField);
                }
            }
        });

        $(document).on('input', '#e-purchase_price, #e-purchase_tax_percent, #e-sale_price, #e-gst_vat_percent, #e-discount_percent, #e-cess_percent, #e-additional_cess, #e-qty, #e-moq', function () {
            var $el = $(this);
            if ($el.hasClass('is-invalid') && $el.val() !== '') {
                var errText = $('#' + $el.attr('id') + '-error').text();
                if (errText.indexOf('greater than Purchase') === -1 && errText.indexOf('greater than available') === -1) {
                    FV.clearFieldError($el);
                }
            }
        });

        $(document).on('change', '#e-category_id, #e-item_class', function () {
            if ($(this).val()) {
                FV.clearFieldError($(this));
            }
        });
    }

    // =========================================================================
    // Build FormData for create
    // =========================================================================

    function buildCreateFormData() {
        var form = document.getElementById('productCreateForm');
        var fd   = new FormData(form);

        if (createDropzone && createDropzone.files.length > 0) {
            fd.append('image', createDropzone.files[0]);
        }

        return fd;
    }

    // =========================================================================
    // Build FormData for edit
    // =========================================================================

    function buildEditFormData() {
        var form = document.getElementById('productEditForm');
        var fd   = new FormData(form);

        if (editDropzone && editDropzone.files.length > 0) {
            fd.append('image', editDropzone.files[0]);
        }

        return fd;
    }

    // =========================================================================
    // AJAX — Create
    // =========================================================================

    $(document).on('click', '#createProductBtn', function () {
        var imageValid = validateDropzoneImage(createDropzone, 'c');
        var formValid  = FV.runRules(getCreateRules(), true);

        if (!imageValid || !formValid) {
            window.showGlobalValidationError && window.showGlobalValidationError();
            return;
        }

        setCreateBusy(true);

        var fd  = buildCreateFormData();
        var url = $('#productCreateForm').attr('action');

        $.ajax({
            url: url,
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function (response) {
                setCreateBusy(false);

                if (response && response.success) {
                    toastr.success(response.message || 'Product created successfully.');
                    bootstrap.Modal.getInstance(document.getElementById('productCreateModal')).hide();
                    resetCreateModal();

                    if (response.product) {
                        appendProductRow(response.product);
                    } else {
                        location.reload();
                    }
                } else {
                    handleServerErrors(response, 'c');
                }
            },
            error: function (xhr) {
                setCreateBusy(false);
                handleAjaxError(xhr, 'c');
            }
        });
    });

    // =========================================================================
    // AJAX — Update
    // =========================================================================

    $(document).on('click', '#updateProductBtn', function () {
        var imageValid = validateDropzoneImage(editDropzone, 'e');
        var formValid  = FV.runRules(getEditRules(), true);

        if (!imageValid || !formValid) {
            window.showGlobalValidationError && window.showGlobalValidationError();
            return;
        }

        setEditBusy(true);

        var id  = $('#edit-id').val();
        var fd  = buildEditFormData();
        var url = '/admin/products/' + id;

        fd.append('_method', 'PUT');

        $.ajax({
            url: url,
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function (response) {
                setEditBusy(false);

                if (response && response.success) {
                    toastr.success(response.message || 'Product updated successfully.');
                    bootstrap.Modal.getInstance(document.getElementById('productEditModal')).hide();

                    if (response.product && ES) {
                        ES.syncProductRow(response.product);
                    } else {
                        location.reload();
                    }
                } else {
                    handleServerErrors(response, 'e');
                }
            },
            error: function (xhr) {
                setEditBusy(false);
                handleAjaxError(xhr, 'e');
            }
        });
    });

    // =========================================================================
    // Open — Edit Modal
    // =========================================================================

    $(document).on('click', '.btn-product-edit', function () {
        var $btn = $(this);

        resetEditModal();

        var id = $btn.data('id');

        $('#edit-id').val(id);
        $('#e-name').val($btn.data('name') || '');
        $('#e-item_code').val($btn.data('item-code') || '');
        $('#e-item_class').val($btn.data('item-class') || 'general');
        $('#e-category_id').val($btn.data('category') || '');
        $('#e-manufacturer_id').val($btn.data('manufacturer-id') || '');
        $('#e-hsn_code').val($btn.data('hsn-code') || '');
        $('#e-regional_name').val($btn.data('regional-name') || '');
        $('#e-unit').val($btn.data('unit') || '');
        $('#e-purchase_price').val($btn.data('purchase') || 0);
        $('#e-purchase_tax_percent').val($btn.data('purchase-tax-percent') || 0);
        $('#e-purchase_tax_inclusive').val(String($btn.data('purchase-tax-inclusive') || '0'));
        $('#e-sale_price').val($btn.data('selling') || 0);
        $('#e-gst_vat_percent').val($btn.data('gst-vat-percent') || 0);
        $('#e-sale_tax_inclusive').val(String($btn.data('sale-tax-inclusive') || '0'));
        $('#e-discount_percent').val($btn.data('discount-percentage') || 0);
        $('#e-cess_percent').val($btn.data('cess-percentage') || 0);
        $('#e-additional_cess').val($btn.data('additional-cess') || 0);
        $('#e-is_weighing_item').val(String($btn.data('is-weighing') || '0'));
        $('#e-qty').val($btn.data('qty') || 0);
        $('#e-moq').val($btn.data('moq') || 1);
        $('#e-status').val(String($btn.data('status') || '1'));
        $('#e-description').val($btn.data('desc') || '');
        $('#edit-remove-image').val('0');

        var imageUrl = $btn.data('image-url') || '';
        if (imageUrl) {
            $('#edit_current_image').attr('src', imageUrl);
            $('#edit_image_preview_wrapper').show();
        } else {
            $('#edit_image_preview_wrapper').hide();
        }

        var modal = new bootstrap.Modal(document.getElementById('productEditModal'));
        modal.show();
    });

    // Remove existing image in edit modal
    $(document).on('click', '#removeEditImageBtn', function () {
        $('#edit_current_image').attr('src', '');
        $('#edit_image_preview_wrapper').hide();
        $('#edit-remove-image').val('1');
    });

    // =========================================================================
    // Open — View Modal
    // =========================================================================

    $(document).on('click', '.btn-product-view', function () {
        var $btn = $(this);

        var name         = $btn.data('name') || '';
        var itemCode     = $btn.data('item-code') || '';
        var itemClass    = $btn.data('item-class') || '';
        var regionalName = $btn.data('regional-name') || '';
        var category     = $btn.data('category') || '-';
        var manufacturer = $btn.data('manufacturer') || '-';
        var hsnCode      = $btn.data('hsn-code') || '-';
        var unit         = $btn.data('unit') || '-';
        var purchase     = parseFloat($btn.data('purchase') || 0);
        var purchaseTax  = $btn.data('purchase-tax-percent') || '0';
        var purchaseTaxI = $btn.data('purchase-tax-inclusive') === '1' ? 'Tax Inclusive' : 'Tax Exclusive';
        var sale         = parseFloat($btn.data('selling') || 0);
        var gstVat       = $btn.data('gst-vat-percent') || '0';
        var saleTaxI     = $btn.data('sale-tax-inclusive') === '1' ? 'Tax Inclusive' : 'Tax Exclusive';
        var discount     = $btn.data('discount-percentage') || '0';
        var cess         = $btn.data('cess-percentage') || '0';
        var addCess      = $btn.data('additional-cess') || '0';
        var isWeighing   = $btn.data('is-weighing') === '1' ? 'Yes' : 'No';
        var qty          = $btn.data('qty') || '0';
        var moq          = $btn.data('moq') || '1';
        var status       = $btn.data('status');
        var desc         = $btn.data('desc') || '';
        var imageUrl     = $btn.data('image') || '';

        var itemClassLabel = {
            general:      'General',
            sale_only:    'Sale Only',
            raw_material: 'Raw Material'
        }[itemClass] || itemClass;

        $('#v-name').text(name);
        $('#v-item_code').text(itemCode ? '#' + itemCode : '');
        $('#v-item_class').text(itemClassLabel);
        $('#v-regional_name').text(regionalName);
        $('#v-category').text(category);
        $('#v-manufacturer').text(manufacturer);
        $('#v-hsn_code').text(hsnCode);
        $('#v-unit').text(unit);
        $('#v-purchase_price').text('$' + purchase.toFixed(2));
        $('#v-purchase_tax_percent').text(purchaseTax + '%');
        $('#v-purchase_tax_inclusive').text(purchaseTaxI);
        $('#v-sale_price').text('$' + sale.toFixed(2));
        $('#v-gst_vat_percent').text(gstVat + '%');
        $('#v-sale_tax_inclusive').text(saleTaxI);
        $('#v-discount_percent').text(discount + '%');
        $('#v-cess_percent').text(cess + '%');
        $('#v-additional_cess').text('$' + parseFloat(addCess).toFixed(2));
        $('#v-is_weighing_item').text(isWeighing);
        $('#v-qty').text(qty);
        $('#v-moq').text(moq);
        $('#v-desc').text(desc || 'No description provided.');

        var statusHtml = (status === '1' || status === 1)
            ? '<span class="badge-status-enabled">Enabled</span>'
            : '<span class="badge-status-disabled">Disabled</span>';
        $('#v-status').html(statusHtml);

        var $imageWrapper = $('#v-image-wrapper');
        if (imageUrl) {
            $imageWrapper.html(
                '<img src="' + imageUrl + '" alt="' + name + '" style="width: 80px; height: 80px; object-fit: cover; border-radius: 12px; border: 1px solid var(--border-color);">'
            );
        } else {
            $imageWrapper.html(
                '<div style="width: 80px; height: 80px; background-color: #f1f5f9; border-radius: 12px; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; color: #94a3b8;">' +
                '<i class="bi bi-image" style="font-size: 28px;"></i></div>'
            );
        }

        var modal = new bootstrap.Modal(document.getElementById('productViewModal'));
        modal.show();
    });

    // =========================================================================
    // Toggle Status — open confirm
    // =========================================================================

    $(document).on('click', '.btn-product-toggle', function () {
        var $btn   = $(this);
        var id     = $btn.data('id');
        var name   = $btn.data('name') || 'this product';
        var status = String($btn.data('status'));

        $('#confirm-id').val(id);
        $('#confirm-action').val('toggle');

        if (status === '1') {
            $('#confirm-title').text('Disable Product');
            $('#confirm-body').html('Are you sure you want to <strong>disable</strong> <em>' + escapeHtml(name) + '</em>? It will be hidden from active listings.');
        } else {
            $('#confirm-title').text('Enable Product');
            $('#confirm-body').html('Are you sure you want to <strong>enable</strong> <em>' + escapeHtml(name) + '</em>?');
        }

        $('#confirm-submit-btn').removeClass('btn-danger btn-warning').addClass('btn-primary');
        $('#confirm-btn-text').text('Confirm');

        var modal = new bootstrap.Modal(document.getElementById('productConfirmModal'));
        modal.show();
    });

    // =========================================================================
    // Delete — open confirm
    // =========================================================================

    $(document).on('click', '.btn-product-delete', function () {
        var $btn = $(this);
        var id   = $btn.data('id');
        var name = $btn.data('name') || 'this product';

        $('#confirm-id').val(id);
        $('#confirm-action').val('delete');
        $('#confirm-title').text('Delete Product');
        $('#confirm-body').html('Are you sure you want to permanently delete <em>' + escapeHtml(name) + '</em>? This action cannot be undone.');

        $('#confirm-submit-btn').removeClass('btn-primary btn-warning').addClass('btn-danger');
        $('#confirm-btn-text').text('Delete');

        var modal = new bootstrap.Modal(document.getElementById('productConfirmModal'));
        modal.show();
    });

    // =========================================================================
    // Confirm submit handler
    // =========================================================================

    $(document).on('click', '#confirm-submit-btn', function () {
        var id     = $('#confirm-id').val();
        var action = $('#confirm-action').val();

        if (!id) {
            return;
        }

        setConfirmBusy(true);

        if (action === 'toggle') {
            $.ajax({
                url: '/admin/products/' + id + '/toggle-status',
                method: 'PATCH',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    setConfirmBusy(false);
                    bootstrap.Modal.getInstance(document.getElementById('productConfirmModal')).hide();

                    if (response && response.success) {
                        toastr.success(response.message || 'Status updated.');
                        if (ES) {
                            ES.syncProductStatus(id, response.status);
                        } else {
                            location.reload();
                        }
                    } else {
                        toastr.error(response.message || 'Could not update status.');
                    }
                },
                error: function (xhr) {
                    setConfirmBusy(false);
                    bootstrap.Modal.getInstance(document.getElementById('productConfirmModal')).hide();
                    toastr.error(parseAjaxError(xhr));
                }
            });

        } else if (action === 'delete') {
            $.ajax({
                url: '/admin/products/' + id,
                method: 'DELETE',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    setConfirmBusy(false);
                    bootstrap.Modal.getInstance(document.getElementById('productConfirmModal')).hide();

                    if (response && response.success) {
                        toastr.success(response.message || 'Product deleted.');
                        $('tr[data-id="' + id + '"]').fadeOut(300, function () { $(this).remove(); });
                    } else {
                        toastr.error(response.message || 'Could not delete product.');
                    }
                },
                error: function (xhr) {
                    setConfirmBusy(false);
                    bootstrap.Modal.getInstance(document.getElementById('productConfirmModal')).hide();
                    toastr.error(parseAjaxError(xhr));
                }
            });
        }
    });

    // =========================================================================
    // Modal reset on hidden
    // =========================================================================

    document.addEventListener('hidden.bs.modal', function (e) {
        if (e.target.id === 'productCreateModal') {
            resetCreateModal();
            setCreateBusy(false);
        }
        if (e.target.id === 'productEditModal') {
            resetEditModal();
            setEditBusy(false);
        }
        if (e.target.id === 'productConfirmModal') {
            setConfirmBusy(false);
        }
    });

    // =========================================================================
    // Append new row to table after create
    // =========================================================================

    function appendProductRow(p) {
        var categoryName    = (p.category && p.category.name) ? p.category.name : (p.category_name || '-');
        var manufacturerName = (p.manufacturer && p.manufacturer.name) ? p.manufacturer.name : (p.manufacturer_name || '-');
        var salePrice       = parseFloat(p.sale_price || 0).toFixed(2);
        var statusBadge     = (p.status == 1) ? '<span class="badge-status-enabled">Enabled</span>' : '<span class="badge-status-disabled">Disabled</span>';
        var qty             = p.qty || 0;
        var moq             = p.moq || 1;
        var unit            = p.unit || 'pcs';
        var stockBadgeClass = (qty <= moq) ? 'bg-danger' : 'bg-success';

        var imageCell = p.image
            ? '<img src="/storage/' + p.image + '" alt="' + escapeHtml(p.name) + '" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);">'
            : '<div class="d-flex align-items-center justify-content-center" style="width:40px;height:40px;background-color:#f1f5f9;border-radius:8px;border:1px solid var(--border-color);color:#94a3b8;"><i class="bi bi-image" style="font-size:16px;"></i></div>';

        var imageUrl   = p.image ? window.location.origin + '/storage/' + p.image : '';
        var statusStr  = ES ? ES.normalizeStatus(p.status) : (p.status ? '1' : '0');

        var row = '<tr data-id="' + p.id + '">' +
            '<td>' + imageCell + '</td>' +
            '<td id="item-code-' + p.id + '" style="font-family:monospace;color:var(--text-secondary);">' + escapeHtml(p.item_code || 'N/A') + '</td>' +
            '<td id="name-' + p.id + '" class="fw-semibold">' + escapeHtml(p.name) + '</td>' +
            '<td id="category-' + p.id + '">' + escapeHtml(categoryName) + '</td>' +
            '<td id="manufacturer-' + p.id + '">' + escapeHtml(manufacturerName) + '</td>' +
            '<td id="selling-' + p.id + '" class="text-end fw-bold" style="color:var(--text-primary);">$' + salePrice + '</td>' +
            '<td id="qty-' + p.id + '" class="text-center"><span class="badge ' + stockBadgeClass + '" style="font-weight:600;font-size:12px;padding:6px 12px;border-radius:6px;opacity:.85;">' + qty + ' ' + escapeHtml(unit) + '</span></td>' +
            '<td class="text-center" id="status-container-' + p.id + '">' + statusBadge + '</td>' +
            '<td class="text-end">' +
                '<div class="d-flex justify-content-end gap-1">' +
                    '<button class="btn btn-product-action btn-product-view" title="View"' +
                        ' data-id="' + p.id + '" data-name="' + escapeHtml(p.name) + '"' +
                        ' data-item-code="' + escapeHtml(p.item_code || '') + '"' +
                        ' data-item-class="' + escapeHtml(p.item_class || 'general') + '"' +
                        ' data-regional-name="' + escapeHtml(p.regional_name || '') + '"' +
                        ' data-category="' + escapeHtml(categoryName) + '"' +
                        ' data-manufacturer="' + escapeHtml(manufacturerName) + '"' +
                        ' data-hsn-code="' + escapeHtml(p.hsn_code || '') + '"' +
                        ' data-unit="' + escapeHtml(unit) + '"' +
                        ' data-purchase="' + (p.purchase_price || 0) + '"' +
                        ' data-purchase-tax-percent="' + (p.purchase_tax_percent || 0) + '"' +
                        ' data-purchase-tax-inclusive="' + (p.purchase_tax_inclusive ? '1' : '0') + '"' +
                        ' data-selling="' + (p.sale_price || 0) + '"' +
                        ' data-gst-vat-percent="' + (p.gst_vat_percent || 0) + '"' +
                        ' data-sale-tax-inclusive="' + (p.sale_tax_inclusive ? '1' : '0') + '"' +
                        ' data-discount-percentage="' + (p.discount_percent || 0) + '"' +
                        ' data-cess-percentage="' + (p.cess_percent || 0) + '"' +
                        ' data-additional-cess="' + (p.additional_cess || 0) + '"' +
                        ' data-is-weighing="' + (p.is_weighing_item ? '1' : '0') + '"' +
                        ' data-qty="' + qty + '" data-moq="' + moq + '"' +
                        ' data-status="' + statusStr + '"' +
                        ' data-desc="' + escapeHtml(p.description || '') + '"' +
                        ' data-image="' + imageUrl + '">' +
                        '<i class="bi bi-eye"></i>' +
                    '</button>' +
                    '<button class="btn btn-product-action btn-product-edit" title="Edit"' +
                        ' data-id="' + p.id + '" data-name="' + escapeHtml(p.name) + '"' +
                        ' data-item-code="' + escapeHtml(p.item_code || '') + '"' +
                        ' data-item-class="' + escapeHtml(p.item_class || 'general') + '"' +
                        ' data-regional-name="' + escapeHtml(p.regional_name || '') + '"' +
                        ' data-category="' + (p.category_id || '') + '"' +
                        ' data-category-name="' + escapeHtml(categoryName) + '"' +
                        ' data-manufacturer-id="' + (p.manufacturer_id || '') + '"' +
                        ' data-hsn-code="' + escapeHtml(p.hsn_code || '') + '"' +
                        ' data-unit="' + escapeHtml(unit) + '"' +
                        ' data-purchase="' + (p.purchase_price || 0) + '"' +
                        ' data-purchase-tax-percent="' + (p.purchase_tax_percent || 0) + '"' +
                        ' data-purchase-tax-inclusive="' + (p.purchase_tax_inclusive ? '1' : '0') + '"' +
                        ' data-selling="' + (p.sale_price || 0) + '"' +
                        ' data-gst-vat-percent="' + (p.gst_vat_percent || 0) + '"' +
                        ' data-sale-tax-inclusive="' + (p.sale_tax_inclusive ? '1' : '0') + '"' +
                        ' data-discount-percentage="' + (p.discount_percent || 0) + '"' +
                        ' data-cess-percentage="' + (p.cess_percent || 0) + '"' +
                        ' data-additional-cess="' + (p.additional_cess || 0) + '"' +
                        ' data-is-weighing="' + (p.is_weighing_item ? '1' : '0') + '"' +
                        ' data-qty="' + qty + '" data-moq="' + moq + '"' +
                        ' data-status="' + statusStr + '"' +
                        ' data-desc="' + escapeHtml(p.description || '') + '"' +
                        ' data-image="' + escapeHtml(p.image || '') + '"' +
                        ' data-image-url="' + imageUrl + '">' +
                        '<i class="bi bi-pencil"></i>' +
                    '</button>' +
                    '<button class="btn btn-product-action btn-product-toggle" title="Toggle Status"' +
                        ' data-id="' + p.id + '" data-name="' + escapeHtml(p.name) + '" data-status="' + statusStr + '">' +
                        '<i class="bi bi-slash-circle"></i>' +
                    '</button>' +
                    '<button class="btn btn-product-action btn-product-delete" title="Delete"' +
                        ' data-id="' + p.id + '" data-name="' + escapeHtml(p.name) + '">' +
                        '<i class="bi bi-trash"></i>' +
                    '</button>' +
                '</div>' +
            '</td>' +
        '</tr>';

        var $tbody = $('#productsTable tbody');
        if ($tbody.find('tr td[colspan]').length) {
            $tbody.html(row);
        } else {
            $tbody.prepend(row);
        }
    }

    // =========================================================================
    // Error handlers
    // =========================================================================

    function handleServerErrors(response, prefix) {
        if (response && response.errors) {
            var fieldMap = {
                name:                  prefix + '-name',
                item_code:             prefix + '-item_code',
                item_class:            prefix + '-item_class',
                category_id:           prefix + '-category_id',
                manufacturer_id:       prefix + '-manufacturer_id',
                hsn_code:              prefix + '-hsn_code',
                regional_name:         prefix + '-regional_name',
                unit:                  prefix + '-unit',
                purchase_price:        prefix + '-purchase_price',
                purchase_tax_percent:  prefix + '-purchase_tax_percent',
                purchase_tax_inclusive: prefix + '-purchase_tax_inclusive',
                sale_price:            prefix + '-sale_price',
                gst_vat_percent:       prefix + '-gst_vat_percent',
                sale_tax_inclusive:    prefix + '-sale_tax_inclusive',
                discount_percent:      prefix + '-discount_percent',
                cess_percent:          prefix + '-cess_percent',
                additional_cess:       prefix + '-additional_cess',
                is_weighing_item:      prefix + '-is_weighing_item',
                qty:                   prefix + '-qty',
                moq:                   prefix + '-moq',
                status:                prefix + '-status',
                description:           prefix + '-description',
                image:                 prefix + '-image'
            };

            $.each(response.errors, function (field, messages) {
                var elId = fieldMap[field] || (prefix + '-' + field);
                var $el  = $('#' + elId);

                if (field === 'image') {
                    showImageError(prefix, Array.isArray(messages) ? messages[0] : messages);
                    return;
                }

                if ($el.length) {
                    FV.setFieldError($el, Array.isArray(messages) ? messages[0] : messages);
                }
            });
        }

        var msg = (response && response.message) ? response.message : 'Please fix the errors and try again.';
        toastr.error(msg);
    }

    function handleAjaxError(xhr, prefix) {
        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
            handleServerErrors(xhr.responseJSON, prefix);
        } else {
            toastr.error(parseAjaxError(xhr));
        }
    }

    function parseAjaxError(xhr) {
        if (xhr.responseJSON && xhr.responseJSON.message) {
            return xhr.responseJSON.message;
        }
        return 'An unexpected error occurred. Please try again.';
    }

    function escapeHtml(str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // =========================================================================
    // Boot
    // =========================================================================

    $(document).ready(function () {

        // Initialize Dropzones after modals are ready
        document.getElementById('productCreateModal').addEventListener('shown.bs.modal', function () {
            initCreateDropzone();
        });

        document.getElementById('productEditModal').addEventListener('shown.bs.modal', function () {
            initEditDropzone();
        });

        // Bind live validations
        bindCreateLive();
        bindEditLive();
    });

})(window.jQuery);
