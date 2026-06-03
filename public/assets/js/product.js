$(document).ready(function () {

    var FV = window.FormValidation;
    var ES = window.EntitySync;

    function validateProductForm(prefix) {
        var $purchase = $('#' + prefix + '-purchase');
        var $selling = $('#' + prefix + '-selling');

        var rules = [
            FV.rules.name($('#' + prefix + '-name'), 'Name'),
            FV.rules.select($('#' + prefix + '-category'), 'Category'),
            FV.rules.nonNegativeInteger($('#' + prefix + '-qty'), 'Quantity'),
            FV.rules.nonNegativeInteger($('#' + prefix + '-moq'), 'MOQ'),
            {
                field: $purchase,
                label: 'Purchase price',
                required: true,
                requiredMessage: 'Purchase price is required.',
                check: function (value) {
                    if (!FV.isNonNegativeNumber(value)) {
                        return 'Purchase price must be zero or greater.';
                    }
                    return true;
                }
            },
            {
                field: $selling,
                label: 'Selling price',
                required: true,
                requiredMessage: 'Selling price is required.',
                check: function (value) {
                    if (!FV.isNonNegativeNumber(value)) {
                        return 'Selling price must be zero or greater.';
                    }
                    return true;
                }
            },
            FV.rules.sellingGreaterThanPurchase($selling, $purchase)
        ];

        var valid = FV.runRules(rules, true);

        if (!valid && typeof window.showGlobalValidationError === 'function') {
            window.showGlobalValidationError();
        }

        return valid;
    }

    $(document).on('blur change input', '#c-purchase, #c-selling, #e-purchase, #e-selling', function () {
        var id = $(this).attr('id');
        var prefix = id.charAt(0);
        FV.runRules([FV.rules.sellingGreaterThanPurchase($('#' + prefix + '-selling'), $('#' + prefix + '-purchase'))], true);
    });

    // =============================================
    // VIEW MODAL - Populate and open
    // =============================================
    $(document).on('click', '.btn-product-view', function () {
        let status = $(this).attr('data-status');

        $('#v-name').text($(this).attr('data-name'));
        $('#v-desc').text($(this).attr('data-desc') || '-');
        $('#v-category').text($(this).attr('data-category'));
        $('#v-qty').text($(this).attr('data-qty'));
        $('#v-purchase').text($(this).attr('data-purchase'));
        $('#v-selling').text($(this).attr('data-selling'));
        $('#v-vat').text($(this).attr('data-vat') + '%');
        $('#v-moq').text($(this).attr('data-moq'));
        $('#v-status').html(status === '1' ? '<span class="badge-status-enabled">Enabled</span>' : '<span class="badge-status-disabled">Disabled</span>');

        let modal = new bootstrap.Modal(document.getElementById('productViewModal'));
        modal.show();
    });

    // =============================================
    // CREATE MODAL - Clear errors when modal opens
    // =============================================
    $('#productCreateModal').on('show.bs.modal', function () {
        FV.clearFormById('productCreateForm');
        $('#productCreateForm')[0].reset();
    });

    // =============================================
    // CREATE - JS Validation before submit
    // =============================================
    $(document).on('click', '#createProductBtn', function () {
        if (!validateProductForm('c')) {
            return;
        }

        var form = document.getElementById('productCreateForm');
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
                if (response.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('productCreateModal'));
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    // Add new row to table
                    var newRow = `
                    <tr id="row-${response.data.id}">
                        <td>${response.data.id}</td>
                        <td id="name-${response.data.id}">${response.data.name}</td>
                        <td id="category-${response.data.id}">${response.data.category ? response.data.category.name : '-'}</td>
                        <td class="text-center" id="qty-${response.data.id}">${response.data.qty}</td>
                        <td class="text-end" id="selling-${response.data.id}">£${parseFloat(response.data.selling_price).toFixed(2)}</td>
                        <td class="text-center" id="vat-${response.data.id}">${response.data.vat}%</td>
                        <td class="text-center" id="status-container-${response.data.id}">
                            ${response.data.status == 1 ? '<span class="badge-status-enabled">Enabled</span>' : '<span class="badge-status-disabled">Disabled</span>'}
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-product-action btn-product-view"
                                    data-id="${response.data.id}"
                                    data-name="${response.data.name}"
                                    data-desc="${response.data.description || ''}"
                                    data-category="${response.data.category ? response.data.category.name : ''}"
                                    data-qty="${response.data.qty}"
                                    data-purchase="${response.data.purchase_price}"
                                    data-selling="${response.data.selling_price}"
                                    data-vat="${response.data.vat}"
                                    data-moq="${response.data.moq}"
                                    data-status="${response.data.status}"
                                    title="View">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-product-action btn-product-edit"
                                    data-id="${response.data.id}"
                                    data-name="${response.data.name}"
                                    data-desc="${response.data.description || ''}"
                                    data-category="${response.data.category_id}"
                                    data-qty="${response.data.qty}"
                                    data-purchase="${response.data.purchase_price}"
                                    data-selling="${response.data.selling_price}"
                                    data-vat="${response.data.vat}"
                                    data-moq="${response.data.moq}"
                                    data-status="${response.data.status}"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-product-action btn-product-toggle"
                                    data-id="${response.data.id}"
                                    data-name="${response.data.name}"
                                    data-status="${response.data.status}"
                                    title="Toggle Status">
                                    <i class="bi bi-slash-circle"></i>
                                </button>
                                <button type="button" class="btn btn-product-action btn-product-delete"
                                    data-id="${response.data.id}"
                                    data-name="${response.data.name}"
                                    title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                    var tbody = $('table tbody');
                    if(tbody.length) {
                        tbody.prepend(newRow);
                    } else {
                        window.location.reload();
                    }
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var fieldMap = {
                        name: '#c-name',
                        category_id: '#c-category',
                        qty: '#c-qty',
                        moq: '#c-moq',
                        purchase_price: '#c-purchase',
                        selling_price: '#c-selling'
                    };
                    Object.keys(errors).forEach(function (key) {
                        if (fieldMap[key]) {
                            FV.setFieldError($(fieldMap[key]), errors[key][0]);
                        }
                    });
                    if (typeof window.showGlobalValidationError === 'function') {
                        window.showGlobalValidationError();
                    }
                } else {
                    if (typeof toastr !== 'undefined') toastr.error('Something went wrong.');
                }
            }
        });
    });

    // =============================================
    // EDIT MODAL - Populate fields when edit button clicked
    // =============================================
    $(document).on('click', '.btn-product-edit', function () {
        FV.clearFormById('productEditForm');

        let productId = $(this).attr('data-id');

        let form = document.getElementById('productEditForm');
        form.action = '/admin/products/' + productId;
        
        $('#edit-id').val(productId);

        $('#e-name').val($(this).attr('data-name'));
        $('#e-desc').val($(this).attr('data-desc'));
        $('#e-qty').val($(this).attr('data-qty'));
        $('#e-moq').val($(this).attr('data-moq'));
        $('#e-purchase').val($(this).attr('data-purchase'));
        $('#e-selling').val($(this).attr('data-selling'));

        $('#e-category').val($(this).attr('data-category'));
        $('#e-vat').val($(this).attr('data-vat'));
        $('#e-status').val($(this).attr('data-status'));

        let modal = new bootstrap.Modal(document.getElementById('productEditModal'));
        modal.show();
    });

    // =============================================
    // EDIT - JS Validation before submit
    // =============================================
    $(document).on('click', '#updateProductBtn', function () {
        if (!validateProductForm('e')) {
            return;
        }

        var form = document.getElementById('productEditForm');
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
                if (response.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('productEditModal'));
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    if (ES && response.data) {
                        ES.syncProductRow(response.data);
                    }
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var fieldMap = {
                        name: '#e-name',
                        category_id: '#e-category',
                        qty: '#e-qty',
                        moq: '#e-moq',
                        purchase_price: '#e-purchase',
                        selling_price: '#e-selling'
                    };
                    Object.keys(errors).forEach(function (key) {
                        if (fieldMap[key]) {
                            FV.setFieldError($(fieldMap[key]), errors[key][0]);
                        }
                    });
                    if (typeof window.showGlobalValidationError === 'function') {
                        window.showGlobalValidationError();
                    }
                } else {
                    if (typeof toastr !== 'undefined') toastr.error('Something went wrong.');
                }
            }
        });
    });

    // =============================================
    // TOGGLE STATUS - Open confirm modal
    // =============================================
    $(document).on('click', '.btn-product-toggle', function () {
        let productId   = $(this).attr('data-id');
        let productName = $(this).attr('data-name');
        let status      = $(this).attr('data-status');

        let actionLabel = status === '1' ? 'Disable' : 'Enable';

        $('#confirm-title').text(actionLabel + ' Product');
        $('#confirm-body').text('Are you sure you want to ' + actionLabel.toLowerCase() + ' "' + productName + '"?');

        let confirmForm = document.getElementById('productConfirmForm');
        confirmForm.action = '/admin/products/' + productId + '/toggle-status';

        $('#confirm-method-field').html('<input type="hidden" name="_method" value="PATCH">');
        $('#confirm-id').val(productId);

        let confirmBtn = $('#confirm-submit-btn');
        confirmBtn.removeClass('btn-danger btn-warning btn-success');
        confirmBtn.addClass(status === '1' ? 'btn-warning' : 'btn-success');
        confirmBtn.text(actionLabel);

        let modal = new bootstrap.Modal(document.getElementById('productConfirmModal'));
        modal.show();
    });

    // =============================================
    // DELETE - Open confirm modal
    // =============================================
    $(document).on('click', '.btn-product-delete', function () {
        let productId   = $(this).attr('data-id');
        let productName = $(this).attr('data-name');

        $('#confirm-title').text('Delete Product');
        $('#confirm-body').text('Are you sure you want to permanently delete "' + productName + '"? This action cannot be undone.');

        let confirmForm = document.getElementById('productConfirmForm');
        confirmForm.action = '/admin/products/' + productId;

        $('#confirm-method-field').html('<input type="hidden" name="_method" value="DELETE">');
        $('#confirm-id').val(productId);

        let confirmBtn = $('#confirm-submit-btn');
        confirmBtn.removeClass('btn-warning btn-success btn-danger').addClass('btn-danger');
        confirmBtn.text('Delete');

        let modal = new bootstrap.Modal(document.getElementById('productConfirmModal'));
        modal.show();
    });

    // =============================================
    // CONFIRM SUBMIT (Toggle / Delete)
    // =============================================
    $(document).on('click', '#confirm-submit-btn', function (e) {
        e.preventDefault();
        
        var form = document.getElementById('productConfirmForm');
        var formData = new FormData(form);
        var method = formData.get('_method');

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
                if (response.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('productConfirmModal'));
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    var id = $('#confirm-id').val();

                    if (method === 'DELETE') {
                        var row = $('#row-' + id);
                        if (row.length) row.remove();
                        else window.location.reload();
                    } else if (method === 'PATCH') {
                        if (ES) {
                            ES.syncProductStatus(id, response.new_status);
                        }
                    }
                }
            },
            error: function () {
                if (typeof toastr !== 'undefined') toastr.error('Something went wrong.');
            }
        });
    });

});