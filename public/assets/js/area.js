$(document).ready(function () {

    var FV = window.FormValidation;
    var ES = window.EntitySync;

    // =============================================
    // VALIDATION HELPER
    // =============================================
    function validateAreaName($field) {
        var valid = FV.runRules([
            FV.rules.name($field, 'Area name')
        ], true);
        var name = $field.val().trim();
        if (valid && name.length < 2) {
            FV.setFieldError($field, 'Area name must be at least 2 characters.');
            valid = false;
        }
        if (valid && name.length > 100) {
            FV.setFieldError($field, 'Area name must not exceed 100 characters.');
            valid = false;
        }
        return valid;
    }

    // Modal close hone par errors aur text ko flush karein
    $('#areaCreateModal, #areaEditModal').on('hidden.bs.modal', function () {
        $(this).find('.form-control').removeClass('is-invalid');
        $(this).find('.field-error').text('');
    });

    // =============================================
    // VIEW MODAL FLOW
    // =============================================
    $(document).on('click', '.btn-category-view', function () {
        // Agar buttons par direct class category-action targets hain toh yahan handle karein
        if ($(this).closest('#areas-table').length === 0) return; 

        var status = $(this).attr('data-status');

        $('#view-area-id').text($(this).attr('data-id'));
        $('#view-area-name').text($(this).attr('data-name'));
        $('#view-area-created-at').text($(this).attr('data-created-at'));
        $('#view-area-updated-at').text($(this).attr('data-updated-at'));

        var statusEl = $('#view-area-status');
        if (ES && ES.normalizeStatus(status) === '1') {
            statusEl.html('<span class="badge-status-enabled">Enabled</span>');
        } else {
            statusEl.html('<span class="badge-status-disabled">Disabled</span>');
        }

        var modal = new bootstrap.Modal(document.getElementById('areaViewModal'));
        modal.show();
    });

    // =============================================
    // CREATE FORM AJAX FLOW
    // =============================================
    $('#createAreaBtn').on('click', function (e) {
        e.preventDefault();
        var $form = $('#areaCreateForm');
        var $field = $('#create-area-name');

        if (!validateAreaName($field)) {
            if (typeof window.showGlobalValidationError === 'function') {
                window.showGlobalValidationError();
            }
            return;
        }

        var $btn = $(this);
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            headers: {
                'Accept': 'application/json'
            },
            beforeSend: function () {
                $btn.prop('disabled', true)
                    .prepend('<span class="spinner-border spinner-border-sm me-1 btn-spinner"></span>');
            },
            success: function (response) {
                if (response.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('areaCreateModal'));
                    if (modal) modal.hide();
                    $form[0].reset();
                    if (typeof toastr !== 'undefined') toastr.success(response.message || 'Area saved successfully.');
                    
                    setTimeout(function() { location.reload(); }, 800);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        FV.setFieldError($field, errors.name[0]);
                    }
                    if (typeof window.showGlobalValidationError === 'function') {
                        window.showGlobalValidationError();
                    }
                } else {
                    if (typeof toastr !== 'undefined') toastr.error('Failed to create area.');
                }
            },
            complete: function () {
                $btn.prop('disabled', false).find('.btn-spinner').remove();
            }
        });
    });

    // =============================================
    // EDIT MODAL FLOW
    // =============================================
    $(document).on('click', '.btn-category-edit', function () {
        if ($(this).closest('#areas-table').length === 0) return;

        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name') || $('#name-' + id).text().trim();

        $('#edit-area-id').val(id);
        $('#edit-area-name').val(name);

        var route = "{{ route('admin.areas.update', ':id') }}".replace(':id', id);
        $('#areaEditForm').attr('action', route);

        var modal = new bootstrap.Modal(document.getElementById('areaEditModal'));
        modal.show();
    });

    // =============================================
    // EDIT FORM AJAX FLOW
    // =============================================
    $('#updateAreaBtn').on('click', function (e) {
        e.preventDefault();
        var $form = $('#areaEditForm');
        var $field = $('#edit-area-name');

        if (!validateAreaName($field)) {
            if (typeof window.showGlobalValidationError === 'function') {
                window.showGlobalValidationError();
            }
            return;
        }

        var $btn = $(this);
        var id = $('#edit-area-id').val();

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            headers: {
                'Accept': 'application/json'
            },
            beforeSend: function () {
                $btn.prop('disabled', true)
                    .prepend('<span class="spinner-border spinner-border-sm me-1 btn-spinner"></span>');
            },
            success: function (response) {
                if (response.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('areaEditModal'));
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message || 'Area updated successfully.');

                    $('#name-' + id).text(response.data?.name || $field.val().trim());
                    
                    var $rowBtn = $('#row-' + id + ' .btn-category-action');
                    $rowBtn.attr('data-name', response.data?.name || $field.val().trim());
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        FV.setFieldError($field, errors.name[0]);
                    }
                    if (typeof window.showGlobalValidationError === 'function') {
                        window.showGlobalValidationError();
                    }
                } else {
                    if (typeof toastr !== 'undefined') toastr.error('Failed to update area.');
                }
            },
            complete: function () {
                $btn.prop('disabled', false).find('.btn-spinner').remove();
            }
        });
    });

    // =============================================
    // TOGGLE STATUS CONFIGURATION
    // =============================================
    $(document).on('click', '.btn-category-toggle', function () {
        if ($(this).closest('#areas-table').length === 0) return;

        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        var status = $(this).attr('data-status');
        var actionUrl = $(this).attr('data-action');

        var isEnabled = (ES ? ES.normalizeStatus(status) : status) === '1';
        var nextStateTxt = isEnabled ? 'disable' : 'enable';

        $('#area-confirm-id').val(id);
        $('#areaConfirmForm').attr('action', actionUrl);
        $('#area-method-field').html('<input type="hidden" name="_method" value="PATCH">');

        $('#area-confirm-title').text('Change Status');
        $('#area-confirm-body').html('Are you sure you want to <strong>' + nextStateTxt + '</strong> the area "<strong>' + name + '</strong>"?');

        $('#area-confirm-submit')
            .removeClass('btn-danger')
            .addClass('btn-warning')
            .text('Confirm');

        var modal = new bootstrap.Modal(document.getElementById('areaConfirmModal'));
        modal.show();
    });

    // =============================================
    // DELETE CONFIGURATION
    // =============================================
    $(document).on('click', '.btn-category-delete', function () {
        if ($(this).closest('#areas-table').length === 0) return;

        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        var actionUrl = $(this).attr('data-action');

        $('#area-confirm-id').val(id);
        $('#areaConfirmForm').attr('action', actionUrl);
        $('#area-method-field').html('<input type="hidden" name="_method" value="DELETE">');

        $('#area-confirm-title').text('Delete Area');
        $('#area-confirm-body').html('Are you sure you want to delete the area "<strong>' + name + '</strong>"? This action cannot be undone.');

        $('#area-confirm-submit')
            .removeClass('btn-warning')
            .addClass('btn-danger')
            .text('Delete');

        var modal = new bootstrap.Modal(document.getElementById('areaConfirmModal'));
        modal.show();
    });

    // =============================================
    // ACTION CONFIRMATION SUBMIT (PATCH & DELETE)
    // =============================================
    $('#area-confirm-submit').on('click', function (e) {
        e.preventDefault();
        var $form = $('#areaConfirmForm');
        var method = $form.find('input[name="_method"]').val();
        var $btn = $(this);

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
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

                var modal = bootstrap.Modal.getInstance(document.getElementById('areaConfirmModal'));
                if (modal) modal.hide();

                if (typeof toastr !== 'undefined') toastr.success(response.message);

                var id = $('#area-confirm-id').val();

                if (method === 'DELETE') {
                    $('#row-' + id).fadeOut(300, function () { $(this).remove(); });
                } else if (method === 'PATCH') {
                    if (ES && typeof ES.syncAreaStatus === 'function') {
                        ES.syncAreaStatus(id, response.new_status);
                    } else {
                        var s = String(response.new_status);
                        var badgeHtml = (s === '1' || s === 'true') 
                            ? '<span class="badge-status-enabled">Enabled</span>' 
                            : '<span class="badge-status-disabled">Disabled</span>';
                        
                        $('#status-container-' + id).html(badgeHtml);
                        $('#row-' + id + ' .btn-category-toggle').attr('data-status', s);
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