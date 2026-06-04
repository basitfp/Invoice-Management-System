$(document).ready(function () {

    var FV = window.FormValidation;
    var ES = window.EntitySync;

    function validateCategoryName($field) {
        var valid = FV.runRules([
            FV.rules.name($field, 'Category name')
        ], true);
        var name = $field.val().trim();
        if (valid && name.length < 2) {
            FV.setFieldError($field, 'Category name must be at least 2 characters.');
            valid = false;
        }
        return valid;
    }

    // =============================================
    // VIEW MODAL - Populate and open
    // =============================================
    $(document).on('click', '.btn-category-view', function () {
        var status = $(this).attr('data-status');

        $('#view-id').text($(this).attr('data-id'));
        $('#view-name').text($(this).attr('data-name'));
        $('#view-created-at').text($(this).attr('data-created-at'));
        $('#view-updated-at').text($(this).attr('data-updated-at'));

        // Status badge — use normalizeStatus for safe comparison
        var statusEl = $('#view-status');
        if (ES && ES.normalizeStatus(status) === '1') {
            statusEl.html('<span class="badge-status-enabled">Enabled</span>');
        } else {
            statusEl.html('<span class="badge-status-disabled">Disabled</span>');
        }

        var modal = new bootstrap.Modal(document.getElementById('categoryViewModal'));
        modal.show();
    });

    // =============================================
    // CREATE MODAL - Clear form when modal opens
    // =============================================
    $('#categoryCreateModal').on('show.bs.modal', function () {
        FV.clearFormById('categoryCreateForm');
        $('#categoryCreateForm')[0].reset();
    });

    // =============================================
    // CREATE - AJAX Validation & Submit
    // =============================================
    $(document).on('click', '#createCategoryBtn', function () {
        var $name = $('#create-name');
        var valid = validateCategoryName($name);

        if (valid && $name.val().trim().length > 100) {
            FV.setFieldError($name, 'Category name must not exceed 100 characters.');
            valid = false;
        }

        if (!valid) {
            if (typeof window.showGlobalValidationError === 'function') {
                window.showGlobalValidationError();
            }
            return;
        }

        var form = document.getElementById('categoryCreateForm');
        var formData = new FormData(form);
        var $btn = $(this);

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
                $btn.prop('disabled', true).prepend('<span class="spinner-border spinner-border-sm me-1 btn-spinner"></span>');
            },
            success: function (response) {
                if (response.success) {
                    var modalEl = document.getElementById('categoryCreateModal');
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    var s = ES ? ES.normalizeStatus(response.data.status) : '1';

                    // Add new row to table
                    var newRow = `
                    <tr id="row-${response.data.id}">
                        <td>${response.data.id}</td>
                        <td id="name-${response.data.id}">${response.data.name}</td>
                        <td class="text-center" id="status-container-${response.data.id}">
                            ${s === '1' ? '<span class="badge-status-enabled">Enabled</span>' : '<span class="badge-status-disabled">Disabled</span>'}
                        </td>
                        <td class="text-center">Just now</td>
                        <td>
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-category-action btn-category-view"
                                    data-id="${response.data.id}"
                                    data-name="${response.data.name}"
                                    data-status="${s}"
                                    data-created-at="Just now"
                                    data-updated-at="Just now"
                                    title="View">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <button type="button" class="btn btn-category-action btn-category-edit"
                                    data-id="${response.data.id}"
                                    data-name="${response.data.name}"
                                    data-status="${s}"
                                    data-action="/admin/categories/${response.data.id}"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <button type="button" class="btn btn-category-action btn-category-toggle"
                                    data-id="${response.data.id}"
                                    data-name="${response.data.name}"
                                    data-status="${s}"
                                    data-action="/admin/categories/${response.data.id}/toggle-status"
                                    title="Toggle Status">
                                    <i class="bi bi-slash-circle"></i>
                                </button>

                                <button type="button" class="btn btn-category-action btn-category-delete"
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
                    }
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        FV.setFieldError($('#create-name'), errors.name[0]);
                        if (typeof window.showGlobalValidationError === 'function') {
                            window.showGlobalValidationError();
                        }
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
    // EDIT MODAL - Populate fields when edit clicked
    // =============================================
    $(document).on('click', '.btn-category-edit', function () {
        FV.clearFormById('categoryEditForm');

        var form = document.getElementById('categoryEditForm');
        form.action = $(this).attr('data-action');

        $('#edit-name').val($(this).attr('data-name'));
        $('#edit-id').val($(this).attr('data-id')); 

        var modal = new bootstrap.Modal(document.getElementById('categoryEditModal'));
        modal.show();
    });

    // =============================================
    // EDIT - AJAX Validation & Submit
    // =============================================
    $(document).on('click', '#updateCategoryBtn', function () {
        if (!validateCategoryName($('#edit-name'))) {
            if (typeof window.showGlobalValidationError === 'function') {
                window.showGlobalValidationError();
            }
            return;
        }

        var form = document.getElementById('categoryEditForm');
        var formData = new FormData(form);
        var $btn = $(this);

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
            beforeSend: function () {
                $btn.prop('disabled', true).prepend('<span class="spinner-border spinner-border-sm me-1 btn-spinner"></span>');
            },
            success: function (response) {
                if (response.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('categoryEditModal'));
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    if (ES && response.data) {
                        ES.syncCategoryRow(response.data);
                    }
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        FV.setFieldError($('#edit-name'), errors.name[0]);
                        if (typeof window.showGlobalValidationError === 'function') {
                            window.showGlobalValidationError();
                        }
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
    // TOGGLE STATUS - Confirm Modal
    // =============================================
    $(document).on('click', '.btn-category-toggle', function () {
        var categoryName   = $(this).attr('data-name');
        var categoryAction = $(this).attr('data-action');
        var status         = $(this).attr('data-status');
        var id             = $(this).attr('data-id');

        var actionLabel = (ES ? ES.normalizeStatus(status) : status) === '1' ? 'Disable' : 'Enable';

        $('#category-confirm-title').text(actionLabel + ' Category');
        $('#category-confirm-body').text('Are you sure you want to ' + actionLabel.toLowerCase() + ' "' + categoryName + '"?');

        var confirmForm = document.getElementById('categoryConfirmForm');
        confirmForm.action = categoryAction;
        
        $('#confirm-id').val(id);
        $('#category-method-field').html('<input type="hidden" name="_method" value="PATCH">');

        var confirmBtn = $('#category-confirm-submit');
        confirmBtn.removeClass('btn-danger btn-warning btn-success');
        if ((ES ? ES.normalizeStatus(status) : status) === '1') {
            confirmBtn.addClass('btn-warning');
        } else {
            confirmBtn.addClass('btn-success');
        }
        confirmBtn.text(actionLabel);

        var modal = new bootstrap.Modal(document.getElementById('categoryConfirmModal'));
        modal.show();
    });

    // =============================================
    // DELETE - Open confirm modal
    // =============================================
    $(document).on('click', '.btn-category-delete', function () {
        var categoryId   = $(this).attr('data-id');
        var categoryName = $(this).attr('data-name');

        $('#category-confirm-title').text('Delete Category');
        $('#category-confirm-body').text('Are you sure you want to permanently delete "' + categoryName + '"? This action cannot be undone.');

        var confirmForm = document.getElementById('categoryConfirmForm');
        confirmForm.action = '/admin/categories/' + categoryId;

        $('#confirm-id').val(categoryId);
        $('#category-method-field').html('<input type="hidden" name="_method" value="DELETE">');

        var confirmBtn = $('#category-confirm-submit');
        confirmBtn.removeClass('btn-warning btn-success btn-danger').addClass('btn-danger');
        confirmBtn.text('Delete');

        var modal = new bootstrap.Modal(document.getElementById('categoryConfirmModal'));
        modal.show();
    });

    // =============================================
    // CONFIRM SUBMIT (Toggle / Delete)
    // =============================================
    $(document).on('click', '#category-confirm-submit', function (e) {
        e.preventDefault(); // Just in case it's still type="submit"
        
        var form = document.getElementById('categoryConfirmForm');
        var formData = new FormData(form);
        var method = formData.get('_method');
        var $btn = $(this);

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
                $btn.prop('disabled', true).prepend('<span class="spinner-border spinner-border-sm me-1 btn-spinner"></span>');
            },
            success: function (response) {
                if (response.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('categoryConfirmModal'));
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    var id = $('#confirm-id').val();

                    if (method === 'DELETE') {
                        var row = $('#row-' + id);
                        if (row.length) {
                            row.fadeOut(300, function () { $(this).remove(); });
                        }
                    } else if (method === 'PATCH') {
                        if (ES) {
                            ES.syncCategoryStatus(id, response.new_status);
                        }
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