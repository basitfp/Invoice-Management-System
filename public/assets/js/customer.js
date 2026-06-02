$(document).ready(function () {

    // Validation Helpers
    function isValidName(value) {
        return value.trim().length >= 3 && value.trim().length <= 255;
    }

    function isValidEmail(value) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    }

    function isValidPhone(value) {
        return value === '' || /^[\d\s\-\+\(\)]{8,20}$/.test(value);
    }

    function showError($input, message) {
        $input.addClass('is-invalid').removeClass('is-valid');
        let $fb = $input.closest('.mb-3').find('.invalid-feedback');
        if ($fb.length === 0) {
            $input.after(`<div class="invalid-feedback">${message}</div>`);
        } else {
            $fb.text(message);
        }
    }

    function clearError($input) {
        $input.removeClass('is-invalid').addClass('is-valid');
        $input.closest('.mb-3').find('.invalid-feedback').text('');
    }

    // VAT Toggle Logic
    function toggleVatField(checkboxId, groupId) {
        $(`#${checkboxId}`).on('change', function () {
            $(`#${groupId}`).toggle(this.checked);
        });
    }

    toggleVatField('vat_registered_create', 'vat_number_group_create');
    toggleVatField('vat_registered_edit', 'vat_number_group_edit');

    // Real-time Validation
    $(document).on('input change blur', '.customer-input', function () {
        const $input = $(this);
        const $form = $input.closest('form');

        setTimeout(() => {
            const name = $form.find('.customer-name').val();
            const email = $form.find('.customer-email').val();
            const phone = $form.find('.customer-phone').val();

            if ($input.hasClass('customer-name')) {
                !isValidName(name) ? showError($input, 'Name must be 3-255 characters.') : clearError($input);
            }
            if ($input.hasClass('customer-email')) {
                !isValidEmail(email) ? showError($input, 'Please enter a valid email.') : clearError($input);
            }
            if ($input.hasClass('customer-phone')) {
                !isValidPhone(phone) ? showError($input, 'Invalid phone number.') : clearError($input);
            }

            checkFormValidity($form);
        }, 10);
    });

    function checkFormValidity($form) {
        const name = $form.find('.customer-name').val();
        const email = $form.find('.customer-email').val();

        const valid = isValidName(name) && isValidEmail(email);

        $form.find('button[type="submit"]').prop('disabled', !valid);
    }

    // Create Modal Reset
    $('#customerCreateModal').on('show.bs.modal', function () {
        const $form = $('#customerCreateForm');
        $form[0].reset();
        $form.find('.form-control').removeClass('is-valid is-invalid');
        $('#vat_number_group_create').hide();
        checkFormValidity($form);
    });

// ==================== EDIT MODAL - FIXED ====================
    $(document).on('click', '.btn-customer-edit', function () {
        const id = $(this).data('id');
        const $form = $('#customerEditForm');

        console.log(id);
        // $form[0].reset();
        $form.find('.form-control').removeClass('is-valid is-invalid');
        $form.attr('action', `/admin/customers/${id}`);

        $.get(`/admin/customers/${id}/edit`, function (customer) {
            $('#e-id').val(customer.id);
            $('#e-name').val(customer.name);
            $('#e-email').val(customer.email);
            $('#e-phone').val(customer.phone || '');
            $('#e-type').val(customer.customer_type);
            $('#e-address').val(customer.address || '');

            // VAT Registered Checkbox
            const isVatRegistered = customer.vat_registered === true || customer.vat_registered === 1;
            $('#vat_registered_edit').prop('checked', isVatRegistered);
            $('#vat_number_group_edit').toggle(isVatRegistered);
            $('#e-vat-number').val(customer.vat_number || '');

            checkFormValidity($form);
        }).fail(function () {
            alert('Failed to load customer data. Please try again.');
        });

        new bootstrap.Modal(document.getElementById('customerEditModal')).show();
    });

    // View Modal
    $(document).on('click', '.btn-customer-view', function () {
        $('#v-name').text($(this).data('name'));
        $('#v-email').text($(this).data('email'));
        $('#v-phone').text($(this).data('phone') || '-');
        $('#v-type').text(ucfirst($(this).data('type')));
        $('#v-address').text($(this).data('address') || '-');

        const vatRegistered = $(this).data('vat-registered') == 1;
        $('#v-vat-registered').text(vatRegistered ? 'Yes' : 'No');
        $('#v-vat-number').text(vatRegistered ? $(this).data('vat-number') : '-');

        const status = parseInt($(this).data('status')) === 1 
            ? '<span class="badge-status-enabled">Active</span>' 
            : '<span class="badge-status-disabled">Inactive</span>';
        
        $('#v-status').html(status);

        new bootstrap.Modal(document.getElementById('customerViewModal')).show();
    });

    // Status Toggle
    $(document).on('click', '.btn-customer-toggle', function () {
        const id = $(this).data('id');
        const status = parseInt($(this).data('status'));

        $('#customerConfirmForm').attr('action', `/admin/customers/${id}/toggle`);
        $('#confirm-title').text(status ? 'Deactivate Customer' : 'Activate Customer');
        $('#confirm-body').html(`Are you sure you want to <strong>${status ? 'deactivate' : 'activate'}</strong> this customer?`);

        new bootstrap.Modal(document.getElementById('customerConfirmModal')).show();
    });

    // Submit Loader
    $(document).on('submit', '#customerCreateForm, #customerEditForm, #customerConfirmForm', function () {
        const $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
    });

    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
});