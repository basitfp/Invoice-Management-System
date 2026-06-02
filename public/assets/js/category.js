/**
 * Category Module (Refactored)
 * Clean validation system aligned with Auth module approach
 * No plugins, no AJAX, no DataTables dependency
 */

$(document).ready(function () {

    // -----------------------------
    // Helpers
    // -----------------------------

    function sanitizeNameInput($input) {
        let val = $input.val();

        // remove leading spaces
        val = val.replace(/^\s+/, '');

        // collapse multiple spaces
        val = val.replace(/\s{2,}/g, ' ');

        $input.val(val);
    }

    function isValidName(value) {
        const trimmed = value.trim();
        return trimmed.length >= 2 && trimmed.length <= 100;
    }

    function showError($input, message) {
        $input.addClass('is-invalid');
        $input.removeClass('is-valid');

        $input.closest('.mb-3').find('.invalid-feedback').text(message);
    }

    function clearError($input) {
        $input.removeClass('is-invalid');
        $input.addClass('is-valid');
        $input.closest('.mb-3').find('.invalid-feedback').text('');
    }

    function checkFormValidity($form) {
        const $input = $form.find('.category-name-input');
        const $btn = $form.find('button[type="submit"]');

        const value = $input.val();

        if (!value || value.trim().length === 0) {
            $btn.prop('disabled', true);
            return false;
        }

        if (!isValidName(value)) {
            $btn.prop('disabled', true);
            return false;
        }

        $btn.prop('disabled', false);
        return true;
    }

    // -----------------------------
    // Input Validation (Real-time)
    // -----------------------------

    $(document).on('keyup input change blur paste', '.category-name-input', function () {
        const $input = $(this);
        const $form = $input.closest('form');

        setTimeout(function () {
            sanitizeNameInput($input);

            const value = $input.val();

            if (!value || value.trim().length === 0) {
                showError($input, 'Category name is required.');
            } else if (!isValidName(value)) {
                showError($input, 'Category name must be between 2 and 100 characters.');
            } else {
                clearError($input);
            }

            checkFormValidity($form);
        }, 10);
    });

    // Prevent leading/consecutive spaces (typing level)
    $(document).on('keydown', '.category-name-input', function (e) {
        const val = $(this).val();
        const cursor = this.selectionStart;

        if (e.which === 32) {
            if (cursor === 0) return e.preventDefault();
            if (val.charAt(cursor - 1) === ' ') return e.preventDefault();
        }
    });

    // -----------------------------
    // Modal Reset (Create)
    // -----------------------------

    $(document).on('click', '[data-bs-target="#categoryCreateModal"]', function () {
        const $form = $('#categoryCreateForm');

        $form[0].reset();
        $form.find('.category-name-input')
            .removeClass('is-valid is-invalid')
            .val('');

        $form.find('.invalid-feedback').text('');
        checkFormValidity($form);
    });

    // -----------------------------
    // Edit Modal Fill
    // -----------------------------

    $(document).on('click', '.btn-category-edit', function () {

        const id = $(this).data('id');
        const name = $(this).data('name');
        const actionUrl = $(this).data('action') || `/admin/categories/${id}`;

        const $form = $('#categoryEditForm');
        const $input = $form.find('.category-name-input');

        $form[0].reset();

        $input.val(name)
            .removeClass('is-valid is-invalid');

        $form.find('.invalid-feedback').text('');
        $form.attr('action', actionUrl);

        checkFormValidity($form);

        const modal = new bootstrap.Modal(document.getElementById('categoryEditModal'));
        modal.show();
    });

    // -----------------------------
    // View Modal
    // -----------------------------

    $(document).on('click', '.btn-category-view', function () {

        $('#view-id').text($(this).data('id'));
        $('#view-name').text($(this).data('name'));

        const status = parseInt($(this).data('status')) === 1
            ? '<span class="badge-status-enabled">Enabled</span>'
            : '<span class="badge-status-disabled">Disabled</span>';

        $('#view-status').html(status);
        $('#view-created-at').text($(this).data('created-at') || '-');
        $('#view-updated-at').text($(this).data('updated-at') || '-');

        new bootstrap.Modal(document.getElementById('categoryViewModal')).show();
    });

    // -----------------------------
    // Status Toggle Modal
    // -----------------------------

    $(document).on('click', '.btn-toggle-status', function () {

        const id = $(this).data('id');
        const name = $(this).data('name');
        const actionUrl = $(this).data('action');
        const status = parseInt($(this).data('status'));

        const isDisabling = status === 1;

        const $form = $('#statusConfirmForm');
        $form.attr('action', actionUrl);

        $form.find('input[name="_method"]').val('PATCH');
        $form.attr('action', actionUrl);    
        $('#status-confirm-title').text(isDisabling ? 'Disable Category' : 'Enable Category');

        $('#status-confirm-body').html(
            `Are you sure you want to <strong>${isDisabling ? 'disable' : 'enable'}</strong>
             category "<strong>${name}</strong>"?`
        );

        const $btn = $form.find('button[type="submit"]');

        $btn
            .removeClass('btn-danger btn-success')
            .addClass(isDisabling ? 'btn-danger' : 'btn-success')
            .text(isDisabling ? 'Disable' : 'Enable')
            .prop('disabled', false);

        new bootstrap.Modal(document.getElementById('statusConfirmModal')).show();
    });

    // -----------------------------
    // Submit Handling (Auth-style)
    // -----------------------------

    $(document).on('submit', '#categoryCreateForm, #categoryEditForm, #statusConfirmForm', function (e) {

        const $form = $(this);
        const $input = $form.find('.category-name-input');
        const $btn = $form.find('button[type="submit"]');

        if ($input.length && !isValidName($input.val() || '')) {
            e.preventDefault();

            if (window.notifyError) {
                notifyError('Please fix validation errors before submitting.');
            }

            return false;
        }

        $btn.prop('disabled', true);

        $btn.html(`
            <span class="spinner-border spinner-border-sm me-2"></span>
            Processing...
        `);
    });

});