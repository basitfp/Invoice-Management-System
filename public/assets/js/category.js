$(document).ready(function () {

    // =============================================
    // HELPER - Show error under a specific field
    // =============================================
    function showFieldError(errorSpanId, message) {
        var span = $('#' + errorSpanId);
        if (span.length) {
            span.text(message);
        }
    }

    // =============================================
    // HELPER - Clear all field errors in a form
    // =============================================
    function clearAllErrors(formId) {
        var form = $('#' + formId);
        if (!form.length) return;
        form.find('.field-error').text('');
        form.find('.is-invalid').removeClass('is-invalid');
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

        // Status badge
        var statusEl = $('#view-status');
        if (status === '1') {
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
        clearAllErrors('categoryCreateForm');
        $('#categoryCreateForm')[0].reset();
    });

    // =============================================
    // CREATE - AJAX Validation & Submit
    // =============================================
    $(document).on('click', '#createCategoryBtn', function () {
        clearAllErrors('categoryCreateForm');

        var name = $('#create-name').val().trim();
        var hasError = false;

        if (name === '') {
            showFieldError('create-name-error', 'Category name is required.');
            $('#create-name').addClass('is-invalid');
            hasError = true;
        } else if (name.length < 2) {
            showFieldError('create-name-error', 'Category name must be at least 2 characters.');
            $('#create-name').addClass('is-invalid');
            hasError = true;
        } else if (name.length > 100) {
            showFieldError('create-name-error', 'Category name must not exceed 100 characters.');
            $('#create-name').addClass('is-invalid');
            hasError = true;
        }

        if (hasError) {
            if (typeof window.showGlobalValidationError === 'function') {
                window.showGlobalValidationError();
            }
            return;
        }

        var form = document.getElementById('categoryCreateForm');
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
                    var modalEl = document.getElementById('categoryCreateModal');
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    // Add new row to table
                    var newRow = `
                    <tr id="row-${response.data.id}">
                        <td>${response.data.id}</td>
                        <td id="name-${response.data.id}">${response.data.name}</td>
                        <td class="text-center" id="status-container-${response.data.id}">
                            <span class="badge-status-enabled">Enabled</span>
                        </td>
                        <td class="text-center">Just now</td>
                        <td>
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-category-action btn-category-view"
                                    data-id="${response.data.id}"
                                    data-name="${response.data.name}"
                                    data-status="${response.data.status}"
                                    data-created-at="Just now"
                                    data-updated-at="Just now"
                                    title="View">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <button type="button" class="btn btn-category-action btn-category-edit"
                                    data-id="${response.data.id}"
                                    data-name="${response.data.name}"
                                    data-action="/admin/categories/${response.data.id}"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <button type="button" class="btn btn-category-action btn-category-toggle"
                                    data-id="${response.data.id}"
                                    data-name="${response.data.name}"
                                    data-status="${response.data.status}"
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
                    } else {
                        window.location.reload();
                    }
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        showFieldError('create-name-error', errors.name[0]);
                        $('#create-name').addClass('is-invalid');
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
    $(document).on('click', '.btn-category-edit', function () {
        clearAllErrors('categoryEditForm');

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
        clearAllErrors('categoryEditForm');

        var name = $('#edit-name').val().trim();
        var hasError = false;

        if (name === '') {
            showFieldError('edit-name-error', 'Category name is required.');
            $('#edit-name').addClass('is-invalid');
            hasError = true;
        } else if (name.length < 2) {
            showFieldError('edit-name-error', 'Category name must be at least 2 characters.');
            $('#edit-name').addClass('is-invalid');
            hasError = true;
        }

        if (hasError) {
            if (typeof window.showGlobalValidationError === 'function') {
                window.showGlobalValidationError();
            }
            return;
        }

        var form = document.getElementById('categoryEditForm');
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
                    var modal = bootstrap.Modal.getInstance(document.getElementById('categoryEditModal'));
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    var id = $('#edit-id').val();
                    var nameCell = $('#name-' + id);
                    if (nameCell.length) {
                        nameCell.text(response.data.name);
                    }
                    
                    // Update the edit button data-name
                    $('.btn-category-edit[data-id="'+id+'"]').attr('data-name', response.data.name);
                    $('.btn-category-view[data-id="'+id+'"]').attr('data-name', response.data.name);
                    $('.btn-category-toggle[data-id="'+id+'"]').attr('data-name', response.data.name);
                    $('.btn-category-delete[data-id="'+id+'"]').attr('data-name', response.data.name);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        showFieldError('edit-name-error', errors.name[0]);
                        $('#edit-name').addClass('is-invalid');
                    }
                } else {
                    if (typeof toastr !== 'undefined') toastr.error('Something went wrong.');
                }
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

        var actionLabel = status === '1' ? 'Disable' : 'Enable';

        $('#category-confirm-title').text(actionLabel + ' Category');
        $('#category-confirm-body').text('Are you sure you want to ' + actionLabel.toLowerCase() + ' "' + categoryName + '"?');

        var confirmForm = document.getElementById('categoryConfirmForm');
        confirmForm.action = categoryAction;
        
        $('#confirm-id').val(id);
        $('#category-method-field').html('<input type="hidden" name="_method" value="PATCH">');

        var confirmBtn = $('#category-confirm-submit');
        confirmBtn.removeClass('btn-danger btn-warning btn-success');
        if (status === '1') {
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
                    var modal = bootstrap.Modal.getInstance(document.getElementById('categoryConfirmModal'));
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    var id = $('#confirm-id').val();

                    if (method === 'DELETE') {
                        var row = $('#row-' + id);
                        if (row.length) {
                            row.remove();
                        } else {
                            window.location.reload();
                        }
                    } else if (method === 'PATCH') {
                        var statusContainer = $('#status-container-' + id);
                        if (statusContainer.length) {
                            if (response.new_status === 1) {
                                statusContainer.html('<span class="badge-status-enabled">Enabled</span>');
                            } else {
                                statusContainer.html('<span class="badge-status-disabled">Disabled</span>');
                            }
                        }
                        
                        var toggleBtn = $('.btn-category-toggle[data-id="'+id+'"]');
                        if (toggleBtn.length) {
                            toggleBtn.attr('data-status', response.new_status);
                        }
                        var viewBtn = $('.btn-category-view[data-id="'+id+'"]');
                        if (viewBtn.length) {
                            viewBtn.attr('data-status', response.new_status);
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