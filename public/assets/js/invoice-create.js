$(document).ready(function () {

    var availableProducts = window.availableProducts || [];
    var rowIndex = 0;
    var FV = window.FormValidation;
    var ES = window.EntitySync;
    var ncLookupTimer = null;
    var ncFoundCustomer = null;

    if (FV) {
        FV.bindNameFields('#nc-name');
        FV.bindEmailFields('#nc-email');
        FV.bindPakistaniPhoneFields('#nc-phone');
    }

    function getProductById(id) {
        for (var i = 0; i < availableProducts.length; i++) {
            if (String(availableProducts[i].id) === String(id)) {
                return availableProducts[i];
            }
        }
        return null;
    }

    function setErrorBySpanId(spanId, $field, message) {
        if ($field && $field.length) {
            FV.setFieldError($field, message);
        } else {
            var el = $('#' + spanId);
            if (el.length) el.text(message);
        }
    }

    function clearSpanError(spanId, $field) {
        if ($field && $field.length) {
            FV.clearFieldError($field);
        } else {
            $('#' + spanId).text('');
        }
    }

    function formatCurrency(amount) {
        return '£' + parseFloat(amount).toFixed(2);
    }

    // =============================================
    // CUSTOMER DROPDOWN
    // =============================================
    $(document).on('change', '#customer_id', function () {
        var opt = $(this).find('option:selected');
        if ($(this).val() === '') {
            $('#customer-info-box').hide();
            return;
        }
        $('#ci-email').text(opt.attr('data-email') || '-');
        $('#ci-phone').text(opt.attr('data-phone') || '-');
        $('#ci-vat').text(opt.attr('data-vat') || '-');
        $('#customer-info-box').show();
        FV.clearFieldError($('#customer_id'));
    });

    $(document).on('blur change', '#invoice_date', function () {
        FV.validateField($(this), {
            label: 'Invoice date',
            required: true,
            requiredMessage: 'Invoice date is required.'
        });
    });

    $(document).on('blur change', '#due_date', function () {
        var $el = $(this);
        var val = $el.val();
        if (!val) {
            FV.setFieldError($el, 'Due date is required.');
            return;
        }
        var today = new Date(); today.setHours(0, 0, 0, 0);
        var selected = new Date(val); selected.setHours(0, 0, 0, 0);
        if (selected < today) {
            FV.setFieldError($el, 'Due date cannot be in the past.');
        } else {
            FV.clearFieldError($el);
        }
    });

    $(document).on('blur change', '#customer_id', function () {
        if ($(this).val()) FV.clearFieldError($(this));
    });

    // =============================================
    // QUICK CREATE CUSTOMER MODAL
    // =============================================
    function resetInvoiceCustomerModal() {
        ['nc-name', 'nc-email', 'nc-phone', 'nc-address', 'nc-vat-number'].forEach(function (id) {
            $('#' + id).val('');
        });
        $('#nc-type').val('regular');
        $('#nc-vat-registered').prop('checked', false);
        $('#nc-vat-number-wrap').hide();
        $('#customer-modal-error').addClass('d-none');
        $('#nc-email-hint').addClass('d-none').empty();
        ncFoundCustomer = null;
        FV.clearFieldError($('#nc-name'));
        FV.clearFieldError($('#nc-email'));
    }

    function setInvoiceCustomerModalLoading(loading) {
        var btn = $('#saveNewCustomerBtn');
        if (loading) {
            $('#saveNewCustomerSpinner').removeClass('d-none');
            $('#saveNewCustomerIcon').addClass('d-none');
            btn.prop('disabled', true);
        } else {
            $('#saveNewCustomerSpinner').addClass('d-none');
            $('#saveNewCustomerIcon').removeClass('d-none');
            btn.prop('disabled', false);
        }
    }

    function handleInvoiceCustomerResult(response) {
        var customer = response.data;
        if (!customer || !customer.id) return;

        if (response.exists) {
            if (typeof toastr !== 'undefined') toastr.info(response.message || 'Customer already exists.');
        } else if (typeof toastr !== 'undefined') {
            toastr.success(response.message || 'Customer added successfully!');
        }

        if (ES) ES.selectInvoiceCustomer(customer);
    }

    $(document).on('click', '#openCreateCustomerBtn', function () {
        resetInvoiceCustomerModal();
        var modal = new bootstrap.Modal(document.getElementById('createCustomerModal'));
        modal.show();
    });

    $(document).on('change', '#nc-vat-registered', function () {
        if ($(this).is(':checked')) {
            $('#nc-vat-number-wrap').show();
        } else {
            $('#nc-vat-number-wrap').hide();
        }
    });

    $(document).on('input blur', '#nc-email', function () {
        var email = $(this).val().trim();
        clearTimeout(ncLookupTimer);

        if (!email || !FV.isValidEmail(email) || !window.lookupCustomerUrl) {
            ncFoundCustomer = null;
            $('#nc-email-hint').addClass('d-none').empty();
            return;
        }

        ncLookupTimer = setTimeout(function () {
            $.ajax({
                url: window.lookupCustomerUrl,
                type: 'GET',
                data: { email: email },
                headers: { 'Accept': 'application/json' },
                success: function (res) {
                    if (res.found && res.data) {
                        ncFoundCustomer = res.data;
                        $('#nc-email-hint')
                            .removeClass('d-none')
                            .html('<i class="bi bi-person-check me-1"></i> Found <strong>' + (ES ? ES.escapeHtml(res.data.name) : res.data.name) + '</strong>. Click Save to use this customer.');
                    } else {
                        ncFoundCustomer = null;
                        $('#nc-email-hint').addClass('d-none').empty();
                    }
                }
            });
        }, 350);
    });

    $(document).on('click', '#saveNewCustomerBtn', function () {
        var name    = $('#nc-name').val().trim();
        var email   = $('#nc-email').val().trim();
        var phone   = $('#nc-phone').val().trim();
        var type    = $('#nc-type').val();
        var address = $('#nc-address').val().trim();
        var vatReg  = $('#nc-vat-registered').is(':checked');
        var vatNum  = $('#nc-vat-number').val().trim();

        $('#customer-modal-error').addClass('d-none');

        var valid = FV.runRules([
            FV.rules.name($('#nc-name'), 'Name'),
            FV.rules.email($('#nc-email'), 'Email')
        ], true);

        if (!valid) {
            if (typeof window.showGlobalValidationError === 'function') window.showGlobalValidationError();
            return;
        }

        if (ncFoundCustomer && ncFoundCustomer.email && ncFoundCustomer.email.toLowerCase() === email.toLowerCase()) {
            handleInvoiceCustomerResult({ exists: true, message: 'Customer already exists.', data: ncFoundCustomer });
            return;
        }

        setInvoiceCustomerModalLoading(true);

        var formData = new FormData();
        formData.append('_token', window.csrfToken);
        formData.append('from_invoice', '1');
        formData.append('name', name);
        formData.append('email', email);
        formData.append('phone', phone);
        formData.append('customer_type', type || 'regular');
        formData.append('address', address);
        if (vatReg) {
            formData.append('vat_registered', '1');
            formData.append('vat_number', vatNum);
        }

        $.ajax({
            url: window.storeCustomerUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': window.csrfToken, 'Accept': 'application/json' },
            success: function (response) {
                setInvoiceCustomerModalLoading(false);
                if (response.success && response.data) handleInvoiceCustomerResult(response);
            },
            error: function (xhr) {
                setInvoiceCustomerModalLoading(false);
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.exists && xhr.responseJSON.data) {
                    handleInvoiceCustomerResult(xhr.responseJSON);
                    return;
                }
                var errBox = $('#customer-modal-error');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var firstError = Object.values(xhr.responseJSON.errors)[0][0];
                    errBox.text(firstError);
                } else {
                    errBox.text('Something went wrong. Please try again.');
                }
                errBox.removeClass('d-none');
            }
        });
    });

    if ($.fn.select2 && $('#customer_id').length) {
        $('#customer_id').select2({
            width: '100%',
            placeholder: 'Search or select a customer',
            allowClear: true
        });
    }

    // =============================================
    // PRODUCT ROWS
    // =============================================
    function buildProductOptions(selectedId) {
        var opts = '<option value="">-- Select Product --</option>';
        availableProducts.forEach(function (p) {
            var sel   = (p.id == selectedId) ? 'selected' : '';
            var stock = parseInt(p.stock || 0);
            var label = p.name + (stock <= 0 ? ' [Out of Stock]' : '');
            opts += '<option value="' + p.id + '" ' +
                    'data-price="'    + p.selling_price    + '" ' +
                    'data-purchase="' + p.purchase_price   + '" ' +
                    'data-vat="'      + p.vat              + '" ' +
                    'data-moq="'      + (p.moq || 1)       + '" ' +
                    'data-stock="'    + stock              + '" ' +
                    sel + '>' + label + '</option>';
        });
        return opts;
    }

    function applyQtyConstraints($row, idx, product) {
        var $qty  = $row.find('.row-qty');
        var stock = parseInt(product.stock || 0);
        var moq   = parseInt(product.moq   || 1);

        if (stock <= 0) {
            // Out of stock — disable field, show error
            $qty.val('').prop('disabled', true)
                .removeAttr('min').removeAttr('max').removeAttr('step');
            setErrorBySpanId('row-qty-error-' + idx, $qty, 'Out of stock.');
            if (typeof toastr !== 'undefined') {
                toastr.error(product.name + ' is currently out of stock.');
            }
            return false;
        }

        // Enable with correct min/max/step
        $qty.prop('disabled', false)
            .attr('min',  moq)
            .attr('max',  stock)
            .attr('step', moq);

        // Set value to MOQ only if field is empty or below MOQ (e.g. first select)
        var currentVal = parseInt($qty.val()) || 0;
        if (currentVal < moq || currentVal === 0) {
            $qty.val(moq);
        } else if (currentVal > stock) {
            $qty.val(stock - ((stock % moq) || moq)); // largest valid multiple ≤ stock
        }

        clearSpanError('row-qty-error-' + idx, $qty);
        return true;
    }

    function addProductRow(productId, price, vat, qty) {
        var idx   = rowIndex++;
        productId = productId || '';
        price     = price !== undefined ? price : '';
        vat       = vat   !== undefined ? vat   : '0';
        qty       = qty   !== undefined ? qty   : '';

        var row = document.createElement('tr');
        row.setAttribute('data-row', idx);
        row.innerHTML =
            '<td>' +
                '<select name="products[' + idx + '][product_id]" class="form-control invoice-input-sm row-product" data-row="' + idx + '">' +
                    buildProductOptions(productId) +
                '</select>' +
                '<span class="field-error text-danger small" id="row-product-error-' + idx + '"></span>' +
            '</td>' +
            '<td>' +
                '<div class="input-group-sm" style="position:relative;">' +
                    '<span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-secondary);font-size:13px;z-index:1;">£</span>' +
                    '<input type="number" name="products[' + idx + '][selling_price]" ' +
                        'class="form-control invoice-input-sm row-price validate-non-negative" ' +
                        'value="' + price + '" min="0.01" step="0.01" data-row="' + idx + '" data-label="Selling price" ' +
                        'style="padding-left:22px;">' +
                '</div>' +
                '<span class="field-error text-danger small" id="row-price-error-' + idx + '"></span>' +
            '</td>' +
            '<td>' +
                '<select name="products[' + idx + '][vat]" class="form-control invoice-input-sm row-vat" data-row="' + idx + '">' +
                    '<option value="0"  ' + (vat == '0'  ? 'selected' : '') + '>0%</option>'  +
                    '<option value="20" ' + (vat == '20' ? 'selected' : '') + '>20%</option>' +
                '</select>' +
            '</td>' +
            '<td>' +
                '<input type="number" name="products[' + idx + '][qty]" ' +
                    'class="form-control invoice-input-sm row-qty validate-qty-int" ' +
                    'value="' + (qty || '') + '" min="1" step="1" data-row="' + idx + '" data-label="Quantity">' +
                '<span class="field-error text-danger small" id="row-qty-error-' + idx + '"></span>' +
            '</td>' +
            '<td>' +
                '<span class="row-line-total" id="row-total-' + idx + '">£0.00</span>' +
            '</td>' +
            '<td>' +
                '<button type="button" class="btn btn-remove-row" data-row="' + idx + '" title="Remove row">' +
                    '<i class="bi bi-x-lg"></i>' +
                '</button>' +
            '</td>';

        $('#invoice-items-body').append(row);

        // If editing and product pre-selected, apply constraints right away
        if (productId) {
            var $newRow = $('tr[data-row="' + idx + '"]');
            var product = getProductById(productId);
            if (product) {
                applyQtyConstraints($newRow, idx, product);
                if (qty !== '') $newRow.find('.row-qty').val(qty); // keep edit value
            }
        }

        recalculateRow(idx);
        recalculateSummary();
    }

    // Initial rows (edit mode or empty)
    if (window.invoiceItems && window.invoiceItems.length > 0) {
        window.invoiceItems.forEach(function (item) {
            addProductRow(item.product_id, item.selling_price, item.vat, item.qty);
        });
    } else {
        addProductRow();
    }

    $(document).on('click', '#addRowBtn', function () {
        addProductRow();
        $('#invoice-items-body tr').last()[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    // =============================================
    // PRODUCT SELECT - Auto-fill price, VAT, qty constraints
    // =============================================
    $(document).on('change', '.row-product', function () {
        var idx     = $(this).attr('data-row');
        var opt     = $(this).find('option:selected');
        var $row    = $('tr[data-row="' + idx + '"]');
        var val     = $(this).val();

        if (!val) {
            // Reset qty field
            $row.find('.row-qty')
                .val('').prop('disabled', false)
                .attr('min', 1).removeAttr('max').attr('step', 1);
            clearSpanError('row-qty-error-' + idx);
            recalculateRow(idx);
            recalculateSummary();
            return;
        }

        // Fill price & VAT from option attributes
        $row.find('.row-price').val(opt.attr('data-price') || 0);
        $row.find('.row-vat').val(opt.attr('data-vat') || '0');

        var product = getProductById(val);
        if (product) {
            applyQtyConstraints($row, idx, product);
        }

        FV.clearFieldError($(this));
        clearSpanError('row-product-error-' + idx);
        recalculateRow(idx);
        recalculateSummary();
    });

    // =============================================
    // QTY / PRICE / VAT INPUT - Validate + Recalc
    // =============================================
    $(document).on('input blur change', '.row-price, .row-qty, .row-vat', function () {
        var idx  = $(this).attr('data-row');
        var $row = $('tr[data-row="' + idx + '"]');

        if ($(this).hasClass('row-qty')) {
            var productId = $row.find('.row-product').val();
            var product   = getProductById(productId);

            if (product) {
                var stock = parseInt(product.stock || 0);
                var moq   = parseInt(product.moq   || 1);
                var qty   = parseInt($(this).val()) || 0;

                if (stock <= 0) {
                    setErrorBySpanId('row-qty-error-' + idx, $(this), 'Out of stock.');
                } else if (qty > stock) {
                    setErrorBySpanId('row-qty-error-' + idx, $(this),
                        'Only ' + stock + ' unit(s) in stock.');
                    if (typeof toastr !== 'undefined' && !$(this).data('stock-toast')) {
                        toastr.warning('Only ' + stock + ' unit(s) available in stock.');
                        $(this).data('stock-toast', true);
                    }
                } else if (qty < moq) {
                    setErrorBySpanId('row-qty-error-' + idx, $(this),
                        'Minimum order quantity is ' + moq + '.');
                } else if (qty % moq !== 0) {
                    setErrorBySpanId('row-qty-error-' + idx, $(this),
                        'Quantity must be a multiple of ' + moq + '.');
                } else {
                    clearSpanError('row-qty-error-' + idx, $(this));
                    $(this).data('stock-toast', false);
                }
            }
        }

        validateInvoiceRow($row, idx, false);
        recalculateRow(idx);
        recalculateSummary();
    });

    // =============================================
    // ROW VALIDATION
    // =============================================
    function validateInvoiceRow($row, idx, showEmpty) {
        if (!$row || !$row.length) return true;

        var valid    = true;
        var $product = $row.find('.row-product');
        var $price   = $row.find('.row-price');
        var $qty     = $row.find('.row-qty');
        var productId = $product.val();

        if (!productId) {
            if (showEmpty !== false) {
                setErrorBySpanId('row-product-error-' + idx, $product, 'Product is required.');
                valid = false;
            }
        } else {
            clearSpanError('row-product-error-' + idx, $product);
        }

        if (productId) {
            // Price
            var priceVal = $price.val();
            if (FV.isEmpty(priceVal) || !FV.isPositiveNumber(priceVal)) {
                setErrorBySpanId('row-price-error-' + idx, $price, 'Selling price must be greater than zero.');
                valid = false;
            } else {
                clearSpanError('row-price-error-' + idx, $price);
                var catalogProduct = getProductById(productId);
                if (catalogProduct) {
                    var purchase = parseFloat(catalogProduct.purchase_price);
                    var selling  = parseFloat(priceVal);
                    if (!isNaN(purchase) && !isNaN(selling) && selling <= purchase) {
                        setErrorBySpanId('row-price-error-' + idx, $price, 'Selling price must be greater than purchase price.');
                        valid = false;
                    }
                }
            }

            // Qty — stock & MOQ checks
            var product = getProductById(productId);
            var qty     = parseInt($qty.val()) || 0;

            if (product) {
                var stock = parseInt(product.stock || 0);
                var moq   = parseInt(product.moq   || 1);

                if (stock <= 0) {
                    setErrorBySpanId('row-qty-error-' + idx, $qty, 'Out of stock.');
                    valid = false;
                } else if (qty > stock) {
                    setErrorBySpanId('row-qty-error-' + idx, $qty,
                        'Only ' + stock + ' unit(s) available in stock.');
                    if (typeof toastr !== 'undefined' && !$qty.data('stock-toast')) {
                        toastr.warning('Only ' + stock + ' unit(s) available in stock.');
                        $qty.data('stock-toast', true);
                    }
                    valid = false;
                } else if (qty < moq) {
                    setErrorBySpanId('row-qty-error-' + idx, $qty,
                        'Minimum quantity is ' + moq + '.');
                    valid = false;
                } else if (qty % moq !== 0) {
                    setErrorBySpanId('row-qty-error-' + idx, $qty,
                        'Quantity must be a multiple of ' + moq + '.');
                    valid = false;
                } else {
                    $qty.removeData('stock-toast');
                    clearSpanError('row-qty-error-' + idx, $qty);
                }
            }
        }

        return valid;
    }

    // =============================================
    // FULL FORM VALIDATION
    // =============================================
    function validateInvoiceForm() {
        var valid = true;

        if (!FV.runRules([{
            field: $('#invoice_date'),
            label: 'Invoice date',
            required: true,
            requiredMessage: 'Invoice date is required.'
        }], true)) valid = false;

        var $due   = $('#due_date');
        var dueVal = $due.val();
        if (!dueVal) {
            FV.setFieldError($due, 'Due date is required.');
            valid = false;
        } else {
            var today    = new Date(); today.setHours(0, 0, 0, 0);
            var selected = new Date(dueVal); selected.setHours(0, 0, 0, 0);
            if (selected < today) {
                FV.setFieldError($due, 'Due date cannot be in the past.');
                valid = false;
            } else {
                FV.clearFieldError($due);
            }
        }

        if (!FV.runRules([FV.rules.select($('#customer_id'), 'Customer')], true)) valid = false;

        var rowCount  = 0;
        var rowsValid = true;

        $('#invoice-items-body tr').each(function () {
            var idx = $(this).attr('data-row');
            if (!validateInvoiceRow($(this), idx, true)) rowsValid = false;
            if ($(this).find('.row-product').val()) rowCount++;
        });

        if (rowCount === 0) {
            $('#products-error').text('Please add at least one product line.');
            valid = false;
        } else {
            $('#products-error').text('');
        }

        if (!rowsValid) valid = false;

        return valid;
    }

    // =============================================
    // REMOVE ROW
    // =============================================
    $(document).on('click', '.btn-remove-row', function () {
        if ($('#invoice-items-body tr').length === 1) return;
        var row = $('tr[data-row="' + $(this).attr('data-row') + '"]');
        if (row.length) { row.remove(); recalculateSummary(); }
    });

    // =============================================
    // CALCULATIONS
    // =============================================
    function recalculateRow(idx) {
        var row   = $('tr[data-row="' + idx + '"]');
        if (!row.length) return;
        var price = parseFloat(row.find('.row-price').val()) || 0;
        var qty   = parseInt(row.find('.row-qty').val())     || 0;
        var vat   = parseFloat(row.find('.row-vat').val())   || 0;
        var lineTotal = (price + (price * vat / 100)) * qty;
        $('#row-total-' + idx).text(formatCurrency(lineTotal));
    }

    function recalculateSummary() {
        var subtotal = 0, totalVat = 0;
        $('#invoice-items-body tr').each(function () {
            var price    = parseFloat($(this).find('.row-price').val()) || 0;
            var qty      = parseInt($(this).find('.row-qty').val())     || 0;
            var vat      = parseFloat($(this).find('.row-vat').val())   || 0;
            var lineBase = price * qty;
            var vatAmnt  = lineBase * (vat / 100);
            subtotal += lineBase;
            totalVat += vatAmnt;
        });
        $('#summary-subtotal').text(formatCurrency(subtotal));
        $('#summary-vat').text(formatCurrency(totalVat));
        $('#summary-grand').text(formatCurrency(subtotal + totalVat));
    }

    // =============================================
    // SAVE - Validate then submit
    // =============================================
    $(document).on('click', '#saveInvoiceBtn', function () {
        if (!validateInvoiceForm()) {
            if (typeof window.showGlobalValidationError === 'function') window.showGlobalValidationError();
            return;
        }

        var form     = document.getElementById('invoiceCreateForm');
        var formData = new FormData(form);
        var $btn     = $(this);

        $.ajax({
            url:         form.action,
            type:        'POST',
            data:        formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept':       'application/json'
            },
            beforeSend: function () {
                $btn.prop('disabled', true)
                    .prepend('<span class="spinner-border spinner-border-sm me-1 btn-spinner"></span>');
            },
            success: function (response) {
                if (response.success && response.redirect_url) {
                    if (typeof toastr !== 'undefined') toastr.success(response.message);
                    window.location.href = response.redirect_url;
                }
            },
            error: function (xhr) {
                var res = xhr.responseJSON || {};

                if (xhr.status === 422) {
                    // Custom stock/MOQ errors: { success: false, message: '...' }
                    if (res.message && !res.errors) {
                        if (typeof toastr !== 'undefined') toastr.error(res.message);
                        return;
                    }

                    // Laravel field validation errors
                    if (res.errors) {
                        if (res.errors.due_date)     FV.setFieldError($('#due_date'),     res.errors.due_date[0]);
                        if (res.errors.status)       FV.setFieldError($('#status'),       res.errors.status[0]);
                        if (res.errors.invoice_date) FV.setFieldError($('#invoice_date'), res.errors.invoice_date[0]);
                        if (res.errors.customer_id)  FV.setFieldError($('#customer_id'),  res.errors.customer_id[0]);
                        if (res.errors.products)     $('#products-error').text(res.errors.products[0]);
                        if (typeof window.showGlobalValidationError === 'function') window.showGlobalValidationError();
                    }
                } else {
                    // Show the real server error message if available, otherwise generic
                    var msg = (res.message && res.message.length < 300)
                        ? res.message
                        : 'Something went wrong. Please try again.';
                    if (typeof toastr !== 'undefined') toastr.error(msg);
                }
            },
            complete: function (xhr) {
                var res = xhr.responseJSON;
                if (!(res && res.success && res.redirect_url)) {
                    $btn.prop('disabled', false).find('.btn-spinner').remove();
                }
            }
        });
    });

});