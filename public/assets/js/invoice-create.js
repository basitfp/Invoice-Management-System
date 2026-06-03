$(document).ready(function () {

    var availableProducts = window.availableProducts || [];
    var rowIndex = 0;

    // =============================================
    // HELPERS
    // =============================================
    function showFieldError(id, message) {
        var el = $('#' + id);
        if (el.length) el.text(message);
    }

    function clearFieldError(id) {
        var el = $('#' + id);
        if (el.length) el.text('');
    }

    function formatCurrency(amount) {
        return '£' + parseFloat(amount).toFixed(2);
    }

    // =============================================
    // CUSTOMER DROPDOWN - Show info box on select
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
        clearFieldError('customer_id-error');
    });

    // =============================================
    // QUICK CREATE CUSTOMER MODAL
    // =============================================
    $(document).on('click', '#openCreateCustomerBtn', function () {
        // Clear modal fields
        ['nc-name','nc-email','nc-phone','nc-address','nc-vat-number'].forEach(function(id){
            $('#' + id).val('');
        });
        $('#nc-type').val('regular');
        $('#nc-vat-registered').prop('checked', false);
        $('#nc-vat-number-wrap').hide();
        $('#customer-modal-error').addClass('d-none');
        clearFieldError('nc-name-error');
        clearFieldError('nc-email-error');

        var modal = new bootstrap.Modal(document.getElementById('createCustomerModal'));
        modal.show();
    });

    // Toggle VAT number field
    $(document).on('change', '#nc-vat-registered', function () {
        if ($(this).is(':checked')) {
            $('#nc-vat-number-wrap').show();
        } else {
            $('#nc-vat-number-wrap').hide();
        }
    });

    // Save new customer via AJAX
    $(document).on('click', '#saveNewCustomerBtn', function () {
        var btn = $(this);
        var name     = $('#nc-name').val().trim();
        var email    = $('#nc-email').val().trim();
        var phone    = $('#nc-phone').val().trim();
        var type     = $('#nc-type').val();
        var address  = $('#nc-address').val().trim();
        var vatReg   = $('#nc-vat-registered').is(':checked');
        var vatNum   = $('#nc-vat-number').val().trim();

        clearFieldError('nc-name-error');
        clearFieldError('nc-email-error');
        $('#customer-modal-error').addClass('d-none');

        var hasError = false;
        if (!name)  { showFieldError('nc-name-error',  'Name is required.');  hasError = true; }
        if (!email) { showFieldError('nc-email-error', 'Email is required.'); hasError = true; }
        if (hasError) return;

        // Show spinner
        $('#saveNewCustomerSpinner').removeClass('d-none');
        $('#saveNewCustomerIcon').addClass('d-none');
        btn.prop('disabled', true);

        $.ajax({
            url: window.storeCustomerUrl,
            type: 'POST',
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
            },
            data: JSON.stringify({
                name:          name,
                email:         email,
                phone:         phone,
                customer_type: type,
                address:       address,
                vat_registered: vatReg ? 1 : 0,
                vat_number:    vatNum,
                status:        1,
            }),
            success: function (data) {
                $('#saveNewCustomerSpinner').addClass('d-none');
                $('#saveNewCustomerIcon').removeClass('d-none');
                btn.prop('disabled', false);

                if (data.id) {
                    var optionText = data.name + (data.customer_type === 'business' ? ' (Business)' : '');
                    var newOption = $('<option>', {
                        value: data.id,
                        text: optionText,
                        'data-email': data.email || '',
                        'data-phone': data.phone || '',
                        'data-vat': data.vat_number || ''
                    });
                    
                    $('#customer_id').append(newOption).val(data.id).trigger('change');

                    var modal = bootstrap.Modal.getInstance(document.getElementById('createCustomerModal'));
                    if (modal) modal.hide();
                    
                    if (typeof toastr !== 'undefined') toastr.success('Customer added successfully!');
                }
            },
            error: function (xhr) {
                $('#saveNewCustomerSpinner').addClass('d-none');
                $('#saveNewCustomerIcon').removeClass('d-none');
                btn.prop('disabled', false);
                
                var errBox = $('#customer-modal-error');
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    var firstError = Object.values(xhr.responseJSON.errors)[0][0];
                    errBox.text(firstError);
                } else {
                    errBox.text('Something went wrong. Please try again.');
                }
                errBox.removeClass('d-none');
            }
        });
    });

    // =============================================
    // PRODUCT ROWS
    // =============================================
    function buildProductOptions(selectedId) {
        var opts = '<option value="">-- Select Product --</option>';
        availableProducts.forEach(function (p) {
            var sel = (p.id == selectedId) ? 'selected' : '';
            opts += '<option value="' + p.id + '" ' +
                    'data-price="' + p.selling_price + '" ' +
                    'data-vat="'   + p.vat           + '" ' +
                    sel + '>' + p.name + '</option>';
        });
        return opts;
    }

    function addProductRow(productId, price, vat, qty) {
        var idx   = rowIndex++;
        productId = productId || '';
        price     = price     !== undefined ? price : '';
        vat       = vat       !== undefined ? vat   : '0';
        qty       = qty       !== undefined ? qty   : 1;

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
                        'class="form-control invoice-input-sm row-price" ' +
                        'value="' + price + '" min="0" step="0.01" data-row="' + idx + '" ' +
                        'style="padding-left:22px;">' +
                '</div>' +
            '</td>' +
            '<td>' +
                '<select name="products[' + idx + '][vat]" class="form-control invoice-input-sm row-vat" data-row="' + idx + '">' +
                    '<option value="0"  ' + (vat == '0'  ? 'selected' : '') + '>0%</option>'  +
                    '<option value="20" ' + (vat == '20' ? 'selected' : '') + '>20%</option>' +
                '</select>' +
            '</td>' +
            '<td>' +
                '<input type="number" name="products[' + idx + '][qty]" ' +
                    'class="form-control invoice-input-sm row-qty" ' +
                    'value="' + qty + '" min="1" data-row="' + idx + '">' +
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
        recalculateRow(idx);
        recalculateSummary();
    }

    // Initial empty row
    addProductRow();

    $(document).on('click', '#addRowBtn', function () {
        addProductRow();
        $('#invoice-items-body tr').last()[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    // =============================================
    // PRODUCT SELECT - Auto-fill price and VAT
    // =============================================
    $(document).on('change', '.row-product', function () {
        var idx = $(this).attr('data-row');
        var opt = $(this).find('option:selected');
        
        if ($(this).val() !== '') {
            var row = $('tr[data-row="' + idx + '"]');
            row.find('.row-price').val(opt.attr('data-price') || 0);
            row.find('.row-vat').val(opt.attr('data-vat') || '0');
            clearFieldError('row-product-error-' + idx);
        }
        
        recalculateRow(idx);
        recalculateSummary();
    });

    $(document).on('input', '.row-price, .row-qty, .row-vat', function () {
        var idx = $(this).attr('data-row');
        recalculateRow(idx);
        recalculateSummary();
    });

    // =============================================
    // REMOVE ROW
    // =============================================
    $(document).on('click', '.btn-remove-row', function () {
        if ($('#invoice-items-body tr').length === 1) return;
        var row = $('tr[data-row="' + $(this).attr('data-row') + '"]');
        if (row.length) {
            row.remove();
            recalculateSummary();
        }
    });

    // =============================================
    // CALCULATIONS
    // =============================================
    function recalculateRow(idx) {
        var row = $('tr[data-row="' + idx + '"]');
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
            var price = parseFloat($(this).find('.row-price').val()) || 0;
            var qty   = parseInt($(this).find('.row-qty').val())     || 0;
            var vat   = parseFloat($(this).find('.row-vat').val())   || 0;
            
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
        var hasError = false;

        // Date
        if (!$('#invoice_date').val()) {
            showFieldError('invoice_date-error', 'Invoice date is required.');
            hasError = true;
        } else {
            clearFieldError('invoice_date-error');
        }

        // Due Date & Validation
        var dueDateVal = $('#due_date').val();
        if (!dueDateVal) {
            showFieldError('due_date-error', 'Due date is required.');
            hasError = true;
        } else {
            var today = new Date();
            today.setHours(0,0,0,0);
            var selectedDate = new Date(dueDateVal);
            selectedDate.setHours(0,0,0,0);
            
            if (selectedDate < today) {
                showFieldError('due_date-error', 'Due date cannot be in the past.');
                hasError = true;
            } else {
                clearFieldError('due_date-error');
            }
        }

        // Customer
        if (!$('#customer_id').val()) {
            showFieldError('customer_id-error', 'Please select a customer.');
            hasError = true;
        } else {
            clearFieldError('customer_id-error');
        }

        // Products
        var hasValidProduct = false;
        $('#invoice-items-body tr').each(function () {
            var idx       = $(this).attr('data-row');
            var productId = $(this).find('.row-product').val();
            if (productId !== '') {
                hasValidProduct = true;
                clearFieldError('row-product-error-' + idx);
            } else {
                showFieldError('row-product-error-' + idx, 'Select a product.');
                hasError = true;
            }
        });

        if (!hasValidProduct) {
            showFieldError('products-error', 'Please add at least one product.');
            hasError = true;
        } else {
            clearFieldError('products-error');
        }

        if (hasError) return;

        var form = document.getElementById('invoiceCreateForm');
        var formData = new FormData(form);

        $.ajax({
            url: form.action,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function (response) {
                if (response.success && response.redirect_url) {
                    if (typeof toastr !== 'undefined') toastr.success(response.message);
                    window.location.href = response.redirect_url;
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.due_date) showFieldError('due_date-error', errors.due_date[0]);
                    if (errors.status) showFieldError('status-error', errors.status[0]);
                    if (errors.invoice_date) showFieldError('invoice_date-error', errors.invoice_date[0]);
                    if (typeof toastr !== 'undefined') toastr.error('Please fix the form errors.');
                } else {
                    if (typeof toastr !== 'undefined') toastr.error('Something went wrong.');
                }
            }
        });
    });

});