$(document).ready(function () {

    var FV = window.FormValidation;
    var ES = window.EntitySync;

    // =============================================
    // VALIDATION HELPERS
    // =============================================
    function validateVendorForm(prefix) {
        var valid = true;

        // Name — required
        var $name = $('#' + prefix + '-vnd-name');
        var nameOk = FV.runRules([FV.rules.name($name, 'Contact name')], true);
        if (nameOk && $name.val().trim().length < 2) {
            FV.setFieldError($name, 'Contact name must be at least 2 characters.');
            nameOk = false;
        }
        if (nameOk && $name.val().trim().length > 100) {
            FV.setFieldError($name, 'Contact name must not exceed 100 characters.');
            nameOk = false;
        }
        if (!nameOk) valid = false;

        // Phone — optional, validate only if filled
        var $phone = $('#' + prefix + '-vnd-phone');
        if ($phone.val().trim() !== '') {
            var phoneOk = FV.runRules([FV.rules.phone($phone, 'Phone')], true);
            if (!phoneOk) valid = false;
        }

       // Email — required
       var $email = $('#' + prefix + '-vnd-email');
        var emailOk = FV.runRules([
        FV.rules.email($email, 'Email')
        ], true);

        if (!emailOk) valid = false;

        return valid;
    }

    // =============================================
    // VIEW MODAL - Populate and open
    // =============================================
    $(document).on('click', '.btn-vendor-view', function () {
        var $btn   = $(this);
        var status = $btn.attr('data-status');

        $('#view-vnd-id').text($btn.attr('data-id'));
        $('#view-vnd-name').text($btn.attr('data-name')           || '-');
        $('#view-vnd-company').text($btn.attr('data-company')     || '-');
        $('#view-vnd-phone').text($btn.attr('data-phone')         || '-');
        $('#view-vnd-email').text($btn.attr('data-email')         || '-');
        $('#view-vnd-tax-reg').text($btn.attr('data-tax-reg-number') || '-');
        $('#view-vnd-addr1').text($btn.attr('data-address-line-1') || '-');
        $('#view-vnd-addr2').text($btn.attr('data-address-line-2') || '-');
        $('#view-vnd-city').text($btn.attr('data-city')           || '-');
        $('#view-vnd-pin').text($btn.attr('data-pin-code')        || '-');
        $('#view-vnd-state').text($btn.attr('data-state')         || '-');
        $('#view-vnd-country').text($btn.attr('data-country')     || '-');
        $('#view-vnd-created-at').text($btn.attr('data-created-at'));
        $('#view-vnd-updated-at').text($btn.attr('data-updated-at'));

        $('#view-vnd-status').html(
            ES.normalizeStatus(status) === '1'
                ? '<span class="badge-status-enabled">Enabled</span>'
                : '<span class="badge-status-disabled">Disabled</span>'
        );

        new bootstrap.Modal(document.getElementById('vendorViewModal')).show();
    });

    // =============================================
    // CREATE MODAL - Clear form on open
    // =============================================
    $('#vendorCreateModal').on('show.bs.modal', function () {
        FV.clearFormById('vendorCreateForm');
        $('#vendorCreateForm')[0].reset();
    });

    // =============================================
    // CREATE - AJAX Validation & Submit
    // =============================================
    $(document).on('click', '#createVendorBtn', function () {
        if (!validateVendorForm('create')) {
            if (typeof window.showGlobalValidationError === 'function') {
                window.showGlobalValidationError();
            }
            return;
        }

        var form     = document.getElementById('vendorCreateForm');
        var formData = new FormData(form);
        var $btn     = $(this);

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
            beforeSend: function () {
                $btn.prop('disabled', true)
                    .prepend('<span class="spinner-border spinner-border-sm me-1 btn-spinner"></span>');
            },
            success: function (response) {
                if (!response.success) return;

                var modal = bootstrap.Modal.getInstance(document.getElementById('vendorCreateModal'));
                if (modal) modal.hide();

                if (typeof toastr !== 'undefined') toastr.success(response.message);

                var d = response.data;
                var s = ES.normalizeStatus(d.status);

                // Remove empty-state row if present
                $('#no-vendors-row').remove();

                var newRow =
                    '<tr id="row-' + d.id + '">' +
                        '<td>' + d.id + '</td>' +
                        '<td id="name-'    + d.id + '">' + ES.escapeHtml(d.name             || '')  + '</td>' +
                        '<td id="company-' + d.id + '">' + ES.escapeHtml(d.company          || '-') + '</td>' +
                        '<td id="phone-'   + d.id + '">' + ES.escapeHtml(d.phone            || '-') + '</td>' +
                        '<td id="email-'   + d.id + '">' + ES.escapeHtml(d.email            || '-') + '</td>' +
                        '<td id="city-'    + d.id + '">' + ES.escapeHtml(d.city             || '-') + '</td>' +
                        '<td class="text-center" id="status-container-' + d.id + '">' +
                            ES.statusBadge(s, { on: 'Enabled', off: 'Disabled' }) +
                        '</td>' +
                        '<td class="text-center">Just now</td>' +
                        '<td>' +
                            '<div class="d-flex gap-2 justify-content-end">' +

                                '<button class="btn btn-category-action btn-vendor-view"' +
                                    ' data-id="'             + d.id                                        + '"' +
                                    ' data-name="'           + ES.escapeHtml(d.name             || '')     + '"' +
                                    ' data-company="'        + ES.escapeHtml(d.company          || '')     + '"' +
                                    ' data-phone="'          + ES.escapeHtml(d.phone            || '')     + '"' +
                                    ' data-email="'          + ES.escapeHtml(d.email            || '')     + '"' +
                                    ' data-tax-reg-number="' + ES.escapeHtml(d.tax_reg_number   || '')     + '"' +
                                    ' data-address-line-1="' + ES.escapeHtml(d.address_line_1   || '')     + '"' +
                                    ' data-address-line-2="' + ES.escapeHtml(d.address_line_2   || '')     + '"' +
                                    ' data-city="'           + ES.escapeHtml(d.city             || '')     + '"' +
                                    ' data-pin-code="'       + ES.escapeHtml(d.pin_code         || '')     + '"' +
                                    ' data-state="'          + ES.escapeHtml(d.state            || '')     + '"' +
                                    ' data-country="'        + ES.escapeHtml(d.country          || '')     + '"' +
                                    ' data-status="'         + s                                            + '"' +
                                    ' data-created-at="Just now"' +
                                    ' data-updated-at="Just now">' +
                                    '<i class="bi bi-eye"></i></button>' +

                                '<button class="btn btn-category-action btn-vendor-edit"' +
                                    ' data-id="'             + d.id                                        + '"' +
                                    ' data-name="'           + ES.escapeHtml(d.name             || '')     + '"' +
                                    ' data-company="'        + ES.escapeHtml(d.company          || '')     + '"' +
                                    ' data-phone="'          + ES.escapeHtml(d.phone            || '')     + '"' +
                                    ' data-email="'          + ES.escapeHtml(d.email            || '')     + '"' +
                                    ' data-tax-reg-number="' + ES.escapeHtml(d.tax_reg_number   || '')     + '"' +
                                    ' data-address-line-1="' + ES.escapeHtml(d.address_line_1   || '')     + '"' +
                                    ' data-address-line-2="' + ES.escapeHtml(d.address_line_2   || '')     + '"' +
                                    ' data-city="'           + ES.escapeHtml(d.city             || '')     + '"' +
                                    ' data-pin-code="'       + ES.escapeHtml(d.pin_code         || '')     + '"' +
                                    ' data-state="'          + ES.escapeHtml(d.state            || '')     + '"' +
                                    ' data-country="'        + ES.escapeHtml(d.country          || '')     + '"' +
                                    ' data-status="'         + s                                            + '"' +
                                    ' data-action="/admin/vendors/' + d.id + '">' +
                                    '<i class="bi bi-pencil"></i></button>' +

                                '<button class="btn btn-category-action btn-vendor-toggle"' +
                                    ' data-id="'     + d.id                    + '"' +
                                    ' data-name="'   + ES.escapeHtml(d.name)   + '"' +
                                    ' data-status="' + s                        + '"' +
                                    ' data-action="/admin/vendors/' + d.id + '/toggle-status">' +
                                    '<i class="bi bi-slash-circle"></i></button>' +

                                '<button class="btn btn-category-action btn-vendor-delete"' +
                                    ' data-id="'   + d.id                  + '"' +
                                    ' data-name="' + ES.escapeHtml(d.name) + '">' +
                                    '<i class="bi bi-trash"></i></button>' +

                            '</div>' +
                        '</td>' +
                    '</tr>';

                $('#vendors-table tbody').prepend(newRow);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.name)           FV.setFieldError($('#create-vnd-name'),    errors.name[0]);
                    if (errors.company)        FV.setFieldError($('#create-vnd-company'), errors.company[0]);
                    if (errors.phone)          FV.setFieldError($('#create-vnd-phone'),   errors.phone[0]);
                    if (errors.email)          FV.setFieldError($('#create-vnd-email'),   errors.email[0]);
                    if (errors.tax_reg_number) FV.setFieldError($('#create-vnd-tax-reg'), errors.tax_reg_number[0]);
                    if (typeof window.showGlobalValidationError === 'function') {
                        window.showGlobalValidationError();
                    }
                } else {
                    if (typeof toastr !== 'undefined') toastr.error('Something went wrong.');
                }
            },
            complete: function () {
                $btn.prop('disabled', false).find('.btn-spinner').remove();
            }
        });
    });

    // =============================================
    // EDIT MODAL - Populate and open
    // =============================================
    $(document).on('click', '.btn-vendor-edit', function () {
        var $btn = $(this);

        FV.clearFormById('vendorEditForm');

        $('#vendorEditForm')[0].reset();
        $('#vendorEditForm').attr('action', $btn.attr('data-action'));
        $('#edit-vnd-id').val($btn.attr('data-id'));

        $('#edit-vnd-name').val($btn.attr('data-name')             || '');
        $('#edit-vnd-company').val($btn.attr('data-company')       || '');
        $('#edit-vnd-phone').val($btn.attr('data-phone')           || '');
        $('#edit-vnd-email').val($btn.attr('data-email')           || '');
        $('#edit-vnd-tax-reg').val($btn.attr('data-tax-reg-number') || '');
        $('#edit-vnd-addr1').val($btn.attr('data-address-line-1')  || '');
        $('#edit-vnd-addr2').val($btn.attr('data-address-line-2')  || '');
        $('#edit-vnd-city').val($btn.attr('data-city')             || '');
        $('#edit-vnd-pin').val($btn.attr('data-pin-code')          || '');
        $('#edit-vnd-state').val($btn.attr('data-state')           || '');
        $('#edit-vnd-country').val($btn.attr('data-country')       || '');

        new bootstrap.Modal(document.getElementById('vendorEditModal')).show();
    });

    // =============================================
    // EDIT - AJAX Validation & Submit
    // =============================================
    $(document).on('click', '#updateVendorBtn', function () {
        if (!validateVendorForm('edit')) {
            if (typeof window.showGlobalValidationError === 'function') {
                window.showGlobalValidationError();
            }
            return;
        }

        var form     = document.getElementById('vendorEditForm');
        var formData = new FormData(form);
        var $btn     = $(this);

        $.ajax({
            url: form.action,
            type: 'POST',           // _method=PUT handled inside form
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            beforeSend: function () {
                $btn.prop('disabled', true)
                    .prepend('<span class="spinner-border spinner-border-sm me-1 btn-spinner"></span>');
            },
            success: function (response) {
                if (!response.success) return;

                var modal = bootstrap.Modal.getInstance(document.getElementById('vendorEditModal'));
                if (modal) modal.hide();

                if (typeof toastr !== 'undefined') toastr.success(response.message);

                if (ES && response.data) {
                    ES.syncVendorRow(response.data);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.name)           FV.setFieldError($('#edit-vnd-name'),    errors.name[0]);
                    if (errors.company)        FV.setFieldError($('#edit-vnd-company'), errors.company[0]);
                    if (errors.phone)          FV.setFieldError($('#edit-vnd-phone'),   errors.phone[0]);
                    if (errors.email)          FV.setFieldError($('#edit-vnd-email'),   errors.email[0]);
                    if (errors.tax_reg_number) FV.setFieldError($('#edit-vnd-tax-reg'), errors.tax_reg_number[0]);
                    if (typeof window.showGlobalValidationError === 'function') {
                        window.showGlobalValidationError();
                    }
                } else {
                    if (typeof toastr !== 'undefined') toastr.error('Something went wrong.');
                }
            },
            complete: function () {
                $btn.prop('disabled', false).find('.btn-spinner').remove();
            }
        });
    });

    // =============================================
    // TOGGLE STATUS - Open confirm modal
    // =============================================
    $(document).on('click', '.btn-vendor-toggle', function () {
        var $btn        = $(this);
        var name        = $btn.attr('data-name');
        var action      = $btn.attr('data-action');
        var status      = $btn.attr('data-status');
        var id          = $btn.attr('data-id');
        var actionLabel = ES.normalizeStatus(status) === '1' ? 'Disable' : 'Enable';

        $('#vendor-confirm-title').text(actionLabel + ' Vendor');
        $('#vendor-confirm-body').text(
            'Are you sure you want to ' + actionLabel.toLowerCase() + ' "' + name + '"?'
        );

        var confirmForm    = document.getElementById('vendorConfirmForm');
        confirmForm.action = action;

        $('#vendor-confirm-id').val(id);
        $('#vendor-method-field').html('<input type="hidden" name="_method" value="PATCH">');

        var $confirmBtn = $('#vendor-confirm-submit');
        $confirmBtn.removeClass('btn-danger btn-warning btn-success');
        $confirmBtn.addClass(ES.normalizeStatus(status) === '1' ? 'btn-warning' : 'btn-success');
        $confirmBtn.text(actionLabel);

        new bootstrap.Modal(document.getElementById('vendorConfirmModal')).show();
    });

    // =============================================
    // DELETE - Open confirm modal
    // =============================================
    $(document).on('click', '.btn-vendor-delete', function () {
        var id   = $(this).attr('data-id');
        var name = $(this).attr('data-name');

        $('#vendor-confirm-title').text('Delete Vendor');
        $('#vendor-confirm-body').text(
            'Are you sure you want to permanently delete "' + name + '"? This action cannot be undone.'
        );

        var confirmForm    = document.getElementById('vendorConfirmForm');
        confirmForm.action = '/admin/vendors/' + id;

        $('#vendor-confirm-id').val(id);
        $('#vendor-method-field').html('<input type="hidden" name="_method" value="DELETE">');

        var $confirmBtn = $('#vendor-confirm-submit');
        $confirmBtn.removeClass('btn-warning btn-success btn-danger').addClass('btn-danger');
        $confirmBtn.text('Delete');

        new bootstrap.Modal(document.getElementById('vendorConfirmModal')).show();
    });

    // =============================================
    // CONFIRM SUBMIT - Toggle Status or Delete
    // =============================================
    $(document).on('click', '#vendor-confirm-submit', function (e) {
        e.preventDefault();

        var form     = document.getElementById('vendorConfirmForm');
        var formData = new FormData(form);
        var method   = formData.get('_method');   // 'PATCH' or 'DELETE'
        var $btn     = $(this);

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
            beforeSend: function () {
                $btn.prop('disabled', true)
                    .prepend('<span class="spinner-border spinner-border-sm me-1 btn-spinner"></span>');
            },
            success: function (response) {
                if (!response.success) return;

                var modal = bootstrap.Modal.getInstance(document.getElementById('vendorConfirmModal'));
                if (modal) modal.hide();

                if (typeof toastr !== 'undefined') toastr.success(response.message);

                var id = $('#vendor-confirm-id').val();

                if (method === 'DELETE') {
                    $('#row-' + id).fadeOut(300, function () { $(this).remove(); });

                } else if (method === 'PATCH') {
                    if (ES) {
                        ES.syncVendorStatus(id, response.new_status);
                    }
                }
            },
            error: function () {
                if (typeof toastr !== 'undefined') toastr.error('Something went wrong.');
            },
            complete: function () {
                $btn.prop('disabled', false).find('.btn-spinner').remove();
            }
        });
    });

});