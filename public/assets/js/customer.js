$(document).ready(function () {

    var FV = window.FormValidation;
    var ES = window.EntitySync;

    function validateCustomerForm(prefix) {
        var rules = [
            FV.rules.name($('#' + prefix + '-name'), 'Name'),
            FV.rules.email($('#' + prefix + '-email'), 'Email'),
            FV.rules.phone($('#' + prefix + '-phone'), 'Phone number'),
            FV.rules.select($('#' + prefix + '-type'), 'Customer type'),
            {
                field: $('#' + prefix + '-address'),
                label: 'Address',
                required: true,
                requiredMessage: 'Address is required.'
            }
        ];

        var valid = FV.runRules(rules, true);

        if ($('#' + prefix + '-vat-registered').is(':checked')) {
            var $vat = $('#' + prefix + '-vat-number');
            if (FV.isEmpty($vat.val())) {
                FV.setFieldError($vat, 'VAT number is required.');
                valid = false;
            }
        }

        if (!valid && typeof window.showGlobalValidationError === 'function') {
            window.showGlobalValidationError();
        }

        return valid;
    }

    // =============================================
    // VAT NUMBER TOGGLE - Create Modal
    // =============================================
    $(document).on('change', '#c-vat-registered', function () {
        var vatGroup = $('#c-vat-number-group');
        if (this.checked) {
            vatGroup.show();
        } else {
            vatGroup.hide();
            $('#c-vat-number').val('');
            FV.clearFieldError($('#c-vat-number'));
        }
    });

    // =============================================
    // VAT NUMBER TOGGLE - Edit Modal
    // =============================================
    $(document).on('change', '#e-vat-registered', function () {
        var vatGroup = $('#e-vat-number-group');
        if (this.checked) {
            vatGroup.show();
        } else {
            vatGroup.hide();
            $('#e-vat-number').val('');
            FV.clearFieldError($('#e-vat-number'));
        }
    });

    // =============================================
    // CREATE MODAL - Clear form when modal opens
    // =============================================
    $('#customerCreateModal').on('show.bs.modal', function () {
        FV.clearFormById('customerCreateForm');
        $('#customerCreateForm')[0].reset();
        $('#c-vat-number-group').hide();
    });

    // =============================================
    // CREATE - JS Validation before submit
    // =============================================
    $(document).on('click', '#createCustomerBtn', function () {
        if (!validateCustomerForm('c')) {
            return;
        }

        var form = document.getElementById('customerCreateForm');
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
                    var modal = bootstrap.Modal.getInstance(document.getElementById('customerCreateModal'));
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    // Add new row to table
                    var c = response.data;
                    var typeBadge = c.customer_type === 'business' ? 'info' : 'secondary';
                    var typeLabel = c.customer_type.charAt(0).toUpperCase() + c.customer_type.slice(1);
                    var vatCol = '';
                    if (c.vat_registered) {
                        vatCol = '<span class="badge bg-success">Yes</span>';
                        if (c.vat_number) {
                            vatCol += '<small class="text-muted ms-1">(' + c.vat_number + ')</small>';
                        }
                    } else {
                        vatCol = '<span class="badge bg-secondary">No</span>';
                    }

                    var newRow = `
                    <tr id="row-${c.id}">
                        <td>${c.id}</td>
                        <td id="name-${c.id}">${c.name}</td>
                        <td id="email-${c.id}">${c.email}</td>
                        <td id="phone-${c.id}">${c.phone ? c.phone : '-'}</td>
                        <td class="text-center" id="type-${c.id}">
                            <span class="badge bg-${typeBadge}">${typeLabel}</span>
                        </td>
                        <td class="text-center" id="vat-${c.id}">${vatCol}</td>
                        <td class="text-center" id="status-container-${c.id}">
                            <span class="badge-status-enabled">Active</span>
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-customer-action btn-customer-view"
                                    data-id="${c.id}"
                                    data-name="${c.name}"
                                    data-email="${c.email}"
                                    data-phone="${c.phone || ''}"
                                    data-type="${c.customer_type}"
                                    data-address="${c.address || ''}"
                                    data-vat-registered="${c.vat_registered}"
                                    data-vat-number="${c.vat_number || ''}"
                                    data-status="${c.status}"
                                    title="View">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-customer-action btn-customer-edit"
                                    data-id="${c.id}"
                                    data-name="${c.name}"
                                    data-email="${c.email}"
                                    data-phone="${c.phone || ''}"
                                    data-type="${c.customer_type}"
                                    data-address="${c.address || ''}"
                                    data-vat-registered="${c.vat_registered}"
                                    data-vat-number="${c.vat_number || ''}"
                                    data-status="${c.status}"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-customer-action btn-customer-toggle"
                                    data-id="${c.id}"
                                    data-name="${c.name}"
                                    data-status="${c.status}"
                                    title="Toggle Status">
                                    <i class="bi bi-slash-circle"></i>
                                </button>
                                <button type="button" class="btn btn-customer-action btn-customer-delete"
                                    data-id="${c.id}"
                                    data-name="${c.name}"
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
                        email: '#c-email',
                        phone: '#c-phone',
                        customer_type: '#c-type',
                        address: '#c-address',
                        vat_number: '#c-vat-number'
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
    // EDIT MODAL - Populate fields when edit clicked
    // =============================================
    $(document).on('click', '.btn-customer-edit', function () {
        FV.clearFormById('customerEditForm');

        var customerId   = $(this).attr('data-id');
        var vatRegistered = $(this).attr('data-vat-registered');

        var form = document.getElementById('customerEditForm');
        form.action = '/admin/customers/' + customerId;
        
        $('#edit-id').val(customerId);

        $('#e-name').val($(this).attr('data-name'));
        $('#e-email').val($(this).attr('data-email'));
        $('#e-phone').val($(this).attr('data-phone'));
        $('#e-address').val($(this).attr('data-address'));
        $('#e-type').val($(this).attr('data-type'));

        var vatCheckbox = $('#e-vat-registered');
        var vatGroup    = $('#e-vat-number-group');

        if (vatRegistered === '1') {
            vatCheckbox.prop('checked', true);
            vatGroup.show();
            $('#e-vat-number').val($(this).attr('data-vat-number'));
        } else {
            vatCheckbox.prop('checked', false);
            vatGroup.hide();
            $('#e-vat-number').val('');
        }

        var modal = new bootstrap.Modal(document.getElementById('customerEditModal'));
        modal.show();
    });

    // =============================================
    // EDIT - JS Validation before submit
    // =============================================
    $(document).on('click', '#updateCustomerBtn', function () {
        if (!validateCustomerForm('e')) {
            return;
        }

        var form = document.getElementById('customerEditForm');
        var formData = new FormData(form);

        $.ajax({
            url: form.action,
            type: 'POST', // Method overriding handled by _method=PUT in form
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function (response) {
                if (response.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('customerEditModal'));
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    if (ES && response.data) {
                        ES.syncCustomerRow(response.data);
                    }
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var fieldMap = {
                        name: '#e-name',
                        email: '#e-email',
                        phone: '#e-phone',
                        customer_type: '#e-type',
                        address: '#e-address',
                        vat_number: '#e-vat-number'
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
    // VIEW MODAL - Populate and open
    // =============================================
    $(document).on('click', '.btn-customer-view', function () {
        var vatRegistered = $(this).attr('data-vat-registered');
        var status        = $(this).attr('data-status');

        $('#v-name').text($(this).attr('data-name'));
        $('#v-email').text($(this).attr('data-email'));
        $('#v-phone').text($(this).attr('data-phone') || '-');
        $('#v-type').text($(this).attr('data-type'));
        $('#v-address').text($(this).attr('data-address') || '-');

        $('#v-vat-registered').text(vatRegistered === '1' ? 'Yes' : 'No');

        var vatNumber = $(this).attr('data-vat-number');
        $('#v-vat-number').text((vatRegistered === '1' && vatNumber) ? vatNumber : '-');

        $('#v-status').html(status === '1' ? '<span class="badge-status-enabled">Active</span>' : '<span class="badge-status-disabled">Inactive</span>');

        var modal = new bootstrap.Modal(document.getElementById('customerViewModal'));
        modal.show();
    });

    // =============================================
    // TOGGLE STATUS - Open confirm modal
    // =============================================
    $(document).on('click', '.btn-customer-toggle', function () {
        var customerId   = $(this).attr('data-id');
        var customerName = $(this).attr('data-name');
        var status       = $(this).attr('data-status');

        var actionLabel = status === '1' ? 'Deactivate' : 'Activate';

        $('#customer-confirm-title').text(actionLabel + ' Customer');
        $('#customer-confirm-body').text('Are you sure you want to ' + actionLabel.toLowerCase() + ' "' + customerName + '"?');

        var confirmForm = document.getElementById('customerConfirmForm');
        confirmForm.action = '/admin/customers/' + customerId + '/toggle';

        $('#customer-method-field').html('<input type="hidden" name="_method" value="PATCH">');
        $('#confirm-id').val(customerId);

        var confirmBtn = $('#customer-confirm-submit');
        confirmBtn.removeClass('btn-danger btn-warning btn-success');
        confirmBtn.addClass(status === '1' ? 'btn-warning' : 'btn-success');
        confirmBtn.text(actionLabel);

        var modal = new bootstrap.Modal(document.getElementById('customerConfirmModal'));
        modal.show();
    });

    // =============================================
    // DELETE - Open confirm modal
    // =============================================
    $(document).on('click', '.btn-customer-delete', function () {
        var customerId   = $(this).attr('data-id');
        var customerName = $(this).attr('data-name');

        $('#customer-confirm-title').text('Delete Customer');
        $('#customer-confirm-body').text('Are you sure you want to permanently delete "' + customerName + '"? This action cannot be undone.');

        var confirmForm = document.getElementById('customerConfirmForm');
        confirmForm.action = '/admin/customers/' + customerId;

        $('#customer-method-field').html('<input type="hidden" name="_method" value="DELETE">');
        $('#confirm-id').val(customerId);

        var confirmBtn = $('#customer-confirm-submit');
        confirmBtn.removeClass('btn-warning btn-success btn-danger').addClass('btn-danger');
        confirmBtn.text('Delete');

        var modal = new bootstrap.Modal(document.getElementById('customerConfirmModal'));
        modal.show();
    });

    // =============================================
    // CONFIRM SUBMIT (Toggle / Delete)
    // =============================================
    $(document).on('click', '#customer-confirm-submit', function (e) {
        e.preventDefault();
        
        var form = document.getElementById('customerConfirmForm');
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
                    var modal = bootstrap.Modal.getInstance(document.getElementById('customerConfirmModal'));
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    var id = $('#confirm-id').val();

                    if (method === 'DELETE') {
                        var row = $('#row-' + id);
                        if (row.length) row.remove();
                        else window.location.reload();
                    } else if (method === 'PATCH') {
                        if (ES) {
                            ES.syncCustomerStatus(id, response.new_status);
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