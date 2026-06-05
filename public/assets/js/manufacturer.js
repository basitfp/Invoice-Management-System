$(document).ready(function () {

    var FV = window.FormValidation;
    var ES = window.EntitySync;

    // =============================================
    // VALIDATION HELPERS
    // =============================================
    function validateCreateForm() {
        var valid = true;

        // Name — required
        var $name = $('#create-mfr-name');
        var nameOk = FV.runRules([FV.rules.name($name, 'Manufacturer name')], true);
        if (nameOk && $name.val().trim().length < 2) {
            FV.setFieldError($name, 'Manufacturer name must be at least 2 characters.');
            nameOk = false;
        }
        if (nameOk && $name.val().trim().length > 100) {
            FV.setFieldError($name, 'Manufacturer name must not exceed 100 characters.');
            nameOk = false;
        }
        if (!nameOk) valid = false;

        // Phone — optional, validate only if filled
        var $phone = $('#create-mfr-phone');
        if ($phone.val().trim() !== '') {
            var phoneOk = FV.runRules([FV.rules.phone($phone, 'Phone')], true);
            if (!phoneOk) valid = false;
        }

        // Email — optional, validate only if filled
        var $email = $('#create-mfr-email');
        if ($email.val().trim() !== '') {
            var emailOk = FV.runRules([FV.rules.email($email, 'Email')], true);
            if (!emailOk) valid = false;
        }

        return valid;
    }

    function validateEditForm() {
        var valid = true;

        // Name — required
        var $name = $('#edit-mfr-name');
        var nameOk = FV.runRules([FV.rules.name($name, 'Manufacturer name')], true);
        if (nameOk && $name.val().trim().length < 2) {
            FV.setFieldError($name, 'Manufacturer name must be at least 2 characters.');
            nameOk = false;
        }
        if (nameOk && $name.val().trim().length > 100) {
            FV.setFieldError($name, 'Manufacturer name must not exceed 100 characters.');
            nameOk = false;
        }
        if (!nameOk) valid = false;

        // Phone — optional, validate only if filled
        var $phone = $('#edit-mfr-phone');
        if ($phone.val().trim() !== '') {
            var phoneOk = FV.runRules([FV.rules.phone($phone, 'Phone')], true);
            if (!phoneOk) valid = false;
        }

        // Email — optional, validate only if filled
        var $email = $('#edit-mfr-email');
        if ($email.val().trim() !== '') {
            var emailOk = FV.runRules([FV.rules.email($email, 'Email')], true);
            if (!emailOk) valid = false;
        }

        return valid;
    }

    // =============================================
    // VIEW MODAL - Populate and open
    // =============================================
    $(document).on('click', '.btn-manufacturer-view', function () {
        var $btn   = $(this);
        var status = $btn.attr('data-status');

        $('#view-mfr-id').text($btn.attr('data-id'));
        $('#view-mfr-name').text($btn.attr('data-name'));
        $('#view-mfr-phone').text($btn.attr('data-phone')   || '-');
        $('#view-mfr-email').text($btn.attr('data-email')   || '-');
        $('#view-mfr-address').text($btn.attr('data-address') || '-');
        $('#view-mfr-created-at').text($btn.attr('data-created-at'));
        $('#view-mfr-updated-at').text($btn.attr('data-updated-at'));

        $('#view-mfr-status').html(
            ES.normalizeStatus(status) === '1'
                ? '<span class="badge-status-enabled">Enabled</span>'
                : '<span class="badge-status-disabled">Disabled</span>'
        );

        new bootstrap.Modal(document.getElementById('manufacturerViewModal')).show();
    });

    // =============================================
    // CREATE MODAL - Clear form on open
    // =============================================
    $('#manufacturerCreateModal').on('show.bs.modal', function () {
        FV.clearFormById('manufacturerCreateForm');
        $('#manufacturerCreateForm')[0].reset();
    });

    // =============================================
    // CREATE - AJAX Validation & Submit
    // =============================================
    $(document).on('click', '#createManufacturerBtn', function () {
        if (!validateCreateForm()) {
            if (typeof window.showGlobalValidationError === 'function') {
                window.showGlobalValidationError();
            }
            return;
        }

        var form     = document.getElementById('manufacturerCreateForm');
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

                var modal = bootstrap.Modal.getInstance(document.getElementById('manufacturerCreateModal'));
                if (modal) modal.hide();

                if (typeof toastr !== 'undefined') toastr.success(response.message);

                var d = response.data;
                var s = ES.normalizeStatus(d.status);

                // Remove empty-state row if present
                $('#no-manufacturers-row').remove();

                var newRow =
                    '<tr id="row-' + d.id + '">' +
                        '<td>' + d.id + '</td>' +
                        '<td id="name-'    + d.id + '">' + ES.escapeHtml(d.name)             + '</td>' +
                        '<td id="phone-'   + d.id + '">' + ES.escapeHtml(d.phone   || '-')   + '</td>' +
                        '<td id="email-'   + d.id + '">' + ES.escapeHtml(d.email   || '-')   + '</td>' +
                        '<td class="text-center" id="status-container-' + d.id + '">' +
                            ES.statusBadge(s, { on: 'Enabled', off: 'Disabled' }) +
                        '</td>' +
                        '<td class="text-center">Just now</td>' +
                        '<td>' +
                            '<div class="d-flex gap-2 justify-content-end">' +

                                '<button class="btn btn-category-action btn-manufacturer-view"' +
                                    ' data-id="'         + d.id                           + '"' +
                                    ' data-name="'       + ES.escapeHtml(d.name)          + '"' +
                                    ' data-phone="'      + ES.escapeHtml(d.phone   || '') + '"' +
                                    ' data-email="'      + ES.escapeHtml(d.email   || '') + '"' +
                                    ' data-address="'    + ES.escapeHtml(d.address || '') + '"' +
                                    ' data-status="'     + s                              + '"' +
                                    ' data-created-at="Just now"' +
                                    ' data-updated-at="Just now">' +
                                    '<i class="bi bi-eye"></i></button>' +

                                '<button class="btn btn-category-action btn-manufacturer-edit"' +
                                    ' data-id="'      + d.id                           + '"' +
                                    ' data-name="'    + ES.escapeHtml(d.name)          + '"' +
                                    ' data-phone="'   + ES.escapeHtml(d.phone   || '') + '"' +
                                    ' data-email="'   + ES.escapeHtml(d.email   || '') + '"' +
                                    ' data-address="' + ES.escapeHtml(d.address || '') + '"' +
                                    ' data-status="'  + s                              + '"' +
                                    ' data-action="/admin/manufacturers/' + d.id + '">' +
                                    '<i class="bi bi-pencil"></i></button>' +

                                '<button class="btn btn-category-action btn-manufacturer-toggle"' +
                                    ' data-id="'     + d.id                  + '"' +
                                    ' data-name="'   + ES.escapeHtml(d.name) + '"' +
                                    ' data-status="' + s                     + '"' +
                                    ' data-action="/admin/manufacturers/' + d.id + '/toggle-status">' +
                                    '<i class="bi bi-slash-circle"></i></button>' +

                                '<button class="btn btn-category-action btn-manufacturer-delete"' +
                                    ' data-id="'   + d.id                  + '"' +
                                    ' data-name="' + ES.escapeHtml(d.name) + '">' +
                                    '<i class="bi bi-trash"></i></button>' +

                            '</div>' +
                        '</td>' +
                    '</tr>';

                $('#manufacturers-table tbody').prepend(newRow);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.name)    FV.setFieldError($('#create-mfr-name'),  errors.name[0]);
                    if (errors.phone)   FV.setFieldError($('#create-mfr-phone'), errors.phone[0]);
                    if (errors.email)   FV.setFieldError($('#create-mfr-email'), errors.email[0]);
                    if (errors.address) FV.setFieldError($('#create-mfr-address'), errors.address[0]);
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
    // EDIT MODAL - Populate fields on open
    // =============================================
    $(document).on('click', '.btn-manufacturer-edit', function () {
        FV.clearFormById('manufacturerEditForm');

        var $btn = $(this);
        var form = document.getElementById('manufacturerEditForm');
        form.action = $btn.attr('data-action');

        $('#edit-mfr-id').val($btn.attr('data-id'));
        $('#edit-mfr-name').val($btn.attr('data-name'));
        $('#edit-mfr-phone').val($btn.attr('data-phone')   || '');
        $('#edit-mfr-email').val($btn.attr('data-email')   || '');
        $('#edit-mfr-address').val($btn.attr('data-address') || '');

        new bootstrap.Modal(document.getElementById('manufacturerEditModal')).show();
    });

    // =============================================
    // EDIT - AJAX Validation & Submit
    // =============================================
    $(document).on('click', '#updateManufacturerBtn', function () {
        if (!validateEditForm()) {
            if (typeof window.showGlobalValidationError === 'function') {
                window.showGlobalValidationError();
            }
            return;
        }

        var form     = document.getElementById('manufacturerEditForm');
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

                var modal = bootstrap.Modal.getInstance(document.getElementById('manufacturerEditModal'));
                if (modal) modal.hide();

                if (typeof toastr !== 'undefined') toastr.success(response.message);

                // Sync row cells + all button data-* attributes
                if (ES && response.data) {
                    ES.syncManufacturerRow(response.data);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.name)    FV.setFieldError($('#edit-mfr-name'),    errors.name[0]);
                    if (errors.phone)   FV.setFieldError($('#edit-mfr-phone'),   errors.phone[0]);
                    if (errors.email)   FV.setFieldError($('#edit-mfr-email'),   errors.email[0]);
                    if (errors.address) FV.setFieldError($('#edit-mfr-address'), errors.address[0]);
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
    $(document).on('click', '.btn-manufacturer-toggle', function () {
        var $btn       = $(this);
        var name       = $btn.attr('data-name');
        var action     = $btn.attr('data-action');
        var status     = $btn.attr('data-status');
        var id         = $btn.attr('data-id');

        var actionLabel = ES.normalizeStatus(status) === '1' ? 'Disable' : 'Enable';

        $('#manufacturer-confirm-title').text(actionLabel + ' Manufacturer');
        $('#manufacturer-confirm-body').text(
            'Are you sure you want to ' + actionLabel.toLowerCase() + ' "' + name + '"?'
        );

        var confirmForm    = document.getElementById('manufacturerConfirmForm');
        confirmForm.action = action;

        $('#manufacturer-confirm-id').val(id);
        $('#manufacturer-method-field').html('<input type="hidden" name="_method" value="PATCH">');

        var $confirmBtn = $('#manufacturer-confirm-submit');
        $confirmBtn.removeClass('btn-danger btn-warning btn-success');
        $confirmBtn.addClass(ES.normalizeStatus(status) === '1' ? 'btn-warning' : 'btn-success');
        $confirmBtn.text(actionLabel);

        new bootstrap.Modal(document.getElementById('manufacturerConfirmModal')).show();
    });

    // =============================================
    // DELETE - Open confirm modal
    // =============================================
    $(document).on('click', '.btn-manufacturer-delete', function () {
        var id   = $(this).attr('data-id');
        var name = $(this).attr('data-name');

        $('#manufacturer-confirm-title').text('Delete Manufacturer');
        $('#manufacturer-confirm-body').text(
            'Are you sure you want to permanently delete "' + name + '"? This action cannot be undone.'
        );

        var confirmForm    = document.getElementById('manufacturerConfirmForm');
        confirmForm.action = '/admin/manufacturers/' + id;

        $('#manufacturer-confirm-id').val(id);
        $('#manufacturer-method-field').html('<input type="hidden" name="_method" value="DELETE">');

        var $confirmBtn = $('#manufacturer-confirm-submit');
        $confirmBtn.removeClass('btn-warning btn-success btn-danger').addClass('btn-danger');
        $confirmBtn.text('Delete');

        new bootstrap.Modal(document.getElementById('manufacturerConfirmModal')).show();
    });

    // =============================================
    // CONFIRM SUBMIT - Toggle Status or Delete
    // =============================================
    $(document).on('click', '#manufacturer-confirm-submit', function (e) {
        e.preventDefault();

        var form     = document.getElementById('manufacturerConfirmForm');
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

                var modal = bootstrap.Modal.getInstance(document.getElementById('manufacturerConfirmModal'));
                if (modal) modal.hide();

                if (typeof toastr !== 'undefined') toastr.success(response.message);

                var id = $('#manufacturer-confirm-id').val();

                if (method === 'DELETE') {
                    $('#row-' + id).fadeOut(300, function () { $(this).remove(); });

                } else if (method === 'PATCH') {
                    if (ES) {
                        ES.syncManufacturerStatus(id, response.new_status);
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