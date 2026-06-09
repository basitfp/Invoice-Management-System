$(document).ready(function () {

    var FV = window.FormValidation;
    var ES = window.EntitySync;
    var filterStartDate = null;
    var filterEndDate = null;

    $('#customer-filter-date-range').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'YYYY-MM-DD'
        }
    });

    $('#customer-filter-date-range').on('apply.daterangepicker', function (ev, picker) {
        filterStartDate = picker.startDate.format('YYYY-MM-DD');
        filterEndDate = picker.endDate.format('YYYY-MM-DD');
        $(this).val(filterStartDate + ' - ' + filterEndDate);
        applyFilters();
    });

    $('#customer-filter-date-range').on('cancel.daterangepicker', function () {
        $(this).val('');
        filterStartDate = null;
        filterEndDate = null;
        applyFilters();
    });

    // =============================================
    // FILTERS - Live client-side filtering
    // =============================================
    function applyFilters() {
        var search    = $('#customer-filter-search').val().trim().toLowerCase();
        var status    = $('#customer-filter-status').val();          // '' | '0' | '1'
        var visibleCount = 0;

        $('#customers-table tbody tr').not('.filter-empty-row').each(function () {
            var $row       = $(this);
            var rowName    = $row.attr('data-name')    || '';
            var rowStatus  = $row.attr('data-status')  || '';
            var rowDate    = $row.attr('data-created') || '';

            var matchSearch = search    === '' || rowName.indexOf(search) !== -1;
            var matchStatus = status    === '' || rowStatus === status;
            var matchDate   = true;

            if (filterStartDate && filterEndDate) {
                matchDate = rowDate >= filterStartDate && rowDate <= filterEndDate;
            }

            if (matchSearch && matchStatus && matchDate) {
                $row.show();
                visibleCount++;
            } else {
                $row.hide();
            }
        });

        // Remove any existing empty-state row, then re-inject if needed
        var $tbody = $('#customers-table tbody');
        $tbody.find('.filter-empty-row').remove();

        if (visibleCount === 0) {
            var colCount = $('#customers-table thead th').length;
            $tbody.append(
                '<tr class="filter-empty-row">' +
                    '<td colspan="' + colCount + '" class="text-center py-5">' +
                        '<div class="filter-empty-state">' +
                            '<i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>' +
                            '<span class="text-muted">No customers match the current filters.</span>' +
                        '</div>' +
                    '</td>' +
                '</tr>'
            );
        }
    }

    // Bind filter inputs — live on every keystroke / change
    $('#customer-filter-search').on('input', applyFilters);
    $('#customer-filter-status').on('change', applyFilters);

    // Reset button — clear all inputs and re-run (shows all rows)
    $('#customer-filter-reset').on('click', function () {
        $('#customer-filter-search').val('');
        $('#customer-filter-status').val('');
        $('#customer-filter-date-range').val('');
        filterStartDate = null;
        filterEndDate = null;
        applyFilters();
    });

    // Re-run filters only if any are currently active
    function refilterAfterDomChange() {
        var hasActive =
            $('#customer-filter-search').val().trim()  !== '' ||
            $('#customer-filter-status').val()         !== '' ||
            filterStartDate !== null ||
            filterEndDate !== null;

        if (hasActive) {
            applyFilters();
        }
    }

    // =============================================
    // HELPERS
    // =============================================

    function orDash(val) {
        var s = String(val || '').trim();
        return s === '' ? '-' : s;
    }

    function ucfirst(str) {
        if (!str) { return '-'; }
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // =============================================
    // VALIDATION
    // =============================================

    function validateCustomerForm(prefix) {
        var rules = [
            FV.rules.name($('#' + prefix + '-name'), 'Name'),
            FV.rules.email($('#' + prefix + '-email'), 'Email'),
            FV.rules.phone($('#' + prefix + '-phone'), 'Phone number', false)
        ];

        var valid = FV.runRules(rules, true);

        // VAT number required when VAT Registered is checked
        if ($('#' + prefix + '-vat-registered').is(':checked')) {
            var $vat = $('#' + prefix + '-vat-number');
            if (FV.isEmpty($vat.val())) {
                FV.setFieldError($vat, 'VAT number is required.');
                valid = false;
            }
        }

        // Credit days: non-negative integer if provided
        var $creditDays = $('#' + prefix + '-credit-days');
        if (!FV.isEmpty($creditDays.val())) {
            var cdVal = parseInt($creditDays.val(), 10);
            if (isNaN(cdVal) || cdVal < 0 || cdVal > 65535) {
                FV.setFieldError($creditDays, 'Credit days must be between 0 and 65535.');
                valid = false;
            }
        }

        // Credit limit: non-negative number if provided
        var $creditLimit = $('#' + prefix + '-credit-limit');
        if (!FV.isEmpty($creditLimit.val())) {
            var clVal = parseFloat($creditLimit.val());
            if (isNaN(clVal) || clVal < 0) {
                FV.setFieldError($creditLimit, 'Credit limit must be zero or greater.');
                valid = false;
            }
        }

        if (!valid && typeof window.showGlobalValidationError === 'function') {
            window.showGlobalValidationError();
        }

        return valid;
    }

    // =============================================
    // SERVER ERROR MAP - maps field keys → #id
    // =============================================

    function buildErrorFieldMap(prefix) {
        return {
            name:             '#' + prefix + '-name',
            email:            '#' + prefix + '-email',
            phone:            '#' + prefix + '-phone',
            customer_type:    '#' + prefix + '-type',
            gender:           '#' + prefix + '-gender',
            birthdate:        '#' + prefix + '-birthdate',
            address:          '#' + prefix + '-address',
            shipping_address: '#' + prefix + '-shipping-address',
            city:             '#' + prefix + '-city',
            pin_code:         '#' + prefix + '-pin-code',
            state:            '#' + prefix + '-state',
            country:          '#' + prefix + '-country',
            landmark:         '#' + prefix + '-landmark',
            area_id:          '#' + prefix + '-area-id',
            credit_days:      '#' + prefix + '-credit-days',
            credit_limit:     '#' + prefix + '-credit-limit',
            vat_number:       '#' + prefix + '-vat-number'
        };
    }

    function applyServerErrors(errors, fieldMap) {
        Object.keys(errors).forEach(function (key) {
            if (fieldMap[key]) {
                FV.setFieldError($(fieldMap[key]), errors[key][0]);
            }
        });
        if (typeof window.showGlobalValidationError === 'function') {
            window.showGlobalValidationError();
        }
    }

    // =============================================
    // ROW BUILDER - used when inserting new row
    // =============================================

    function buildCustomerRow(c) {
        var s      = ES ? ES.normalizeStatus(c.status) : (c.status ? '1' : '0');
        var vr     = ES ? ES.normalizeStatus(c.vat_registered) : (c.vat_registered ? '1' : '0');
        var typeBadge = c.customer_type === 'company' ? 'info' : 'secondary';
        var typeLabel = ucfirst(c.customer_type);

        var vatCol = vr === '1'
            ? '<span class="badge bg-success">Yes</span>' + (c.vat_number ? '<br><small class="text-muted" style="font-size:11px;">' + ES.escapeHtml(c.vat_number) + '</small>' : '')
            : '<span class="badge bg-secondary">No</span>';

        var statusCol = s === '1'
            ? '<span class="badge-status-enabled">Active</span>'
            : '<span class="badge-status-disabled">Inactive</span>';

        var areaName        = c.area_name || '';
        var gender          = c.gender || '';
        var birthdate       = c.birthdate || '';
        var shippingAddress = c.shipping_address || '';
        var city            = c.city || '';
        var pinCode         = c.pin_code || '';
        var state           = c.state || '';
        var country         = c.country || '';
        var landmark        = c.landmark || '';
        var areaId          = c.area_id || '';
        var creditDays      = c.credit_days || '';
        var creditLimit     = c.credit_limit || '';

        return `
        <tr id="row-${c.id}"
            data-name="${ES.escapeHtml(c.name).toLowerCase()}"
            data-status="${s}"
            data-created="${c.created_at ? c.created_at.substring(0, 10) : ''}">
            <td>${c.id}</td>
            <td id="name-${c.id}">${ES.escapeHtml(c.name)}</td>
            <td id="email-${c.id}">${ES.escapeHtml(c.email)}</td>
            <td id="phone-${c.id}">${c.phone ? ES.escapeHtml(c.phone) : '-'}</td>
            <td class="text-center" id="type-${c.id}">
                <span class="badge bg-${typeBadge}">${typeLabel}</span>
            </td>
            <td class="text-center" id="vat-${c.id}">${vatCol}</td>
            <td class="text-center" id="status-container-${c.id}">${statusCol}</td>
            <td>
                <div class="d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-customer-action btn-customer-view"
                        data-id="${c.id}"
                        data-name="${ES.escapeHtml(c.name)}"
                        data-email="${ES.escapeHtml(c.email)}"
                        data-phone="${ES.escapeHtml(c.phone || '')}"
                        data-type="${c.customer_type}"
                        data-gender="${gender}"
                        data-birthdate="${birthdate}"
                        data-address="${ES.escapeHtml(c.address || '')}"
                        data-shipping-address="${ES.escapeHtml(shippingAddress)}"
                        data-city="${ES.escapeHtml(city)}"
                        data-pin-code="${ES.escapeHtml(pinCode)}"
                        data-state="${ES.escapeHtml(state)}"
                        data-country="${ES.escapeHtml(country)}"
                        data-landmark="${ES.escapeHtml(landmark)}"
                        data-area-id="${areaId}"
                        data-area-name="${ES.escapeHtml(areaName)}"
                        data-credit-days="${creditDays}"
                        data-credit-limit="${creditLimit}"
                        data-vat-registered="${vr}"
                        data-vat-number="${ES.escapeHtml(c.vat_number || '')}"
                        data-status="${s}"
                        title="View">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button type="button" class="btn btn-customer-action btn-customer-edit"
                        data-id="${c.id}"
                        data-name="${ES.escapeHtml(c.name)}"
                        data-email="${ES.escapeHtml(c.email)}"
                        data-phone="${ES.escapeHtml(c.phone || '')}"
                        data-type="${c.customer_type}"
                        data-gender="${gender}"
                        data-birthdate="${birthdate}"
                        data-address="${ES.escapeHtml(c.address || '')}"
                        data-shipping-address="${ES.escapeHtml(shippingAddress)}"
                        data-city="${ES.escapeHtml(city)}"
                        data-pin-code="${ES.escapeHtml(pinCode)}"
                        data-state="${ES.escapeHtml(state)}"
                        data-country="${ES.escapeHtml(country)}"
                        data-landmark="${ES.escapeHtml(landmark)}"
                        data-area-id="${areaId}"
                        data-area-name="${ES.escapeHtml(areaName)}"
                        data-credit-days="${creditDays}"
                        data-credit-limit="${creditLimit}"
                        data-vat-registered="${vr}"
                        data-vat-number="${ES.escapeHtml(c.vat_number || '')}"
                        data-status="${s}"
                        title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-customer-action btn-customer-toggle"
                        data-id="${c.id}"
                        data-name="${ES.escapeHtml(c.name)}"
                        data-status="${s}"
                        title="Toggle Status">
                        <i class="bi bi-slash-circle"></i>
                    </button>
                    <button type="button" class="btn btn-customer-action btn-customer-delete"
                        data-id="${c.id}"
                        data-name="${ES.escapeHtml(c.name)}"
                        title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>`;
    }

    // =============================================
    // VAT NUMBER TOGGLE
    // =============================================

    $(document).on('change', '#c-vat-registered', function () {
        if (this.checked) {
            $('#c-vat-number-group').show();
        } else {
            $('#c-vat-number-group').hide();
            $('#c-vat-number').val('');
            FV.clearFieldError($('#c-vat-number'));
        }
    });

    $(document).on('change', '#e-vat-registered', function () {
        if (this.checked) {
            $('#e-vat-number-group').show();
        } else {
            $('#e-vat-number-group').hide();
            $('#e-vat-number').val('');
            FV.clearFieldError($('#e-vat-number'));
        }
    });

    // =============================================
    // CREATE MODAL - Reset on open
    // =============================================

    $('#customerCreateModal').on('show.bs.modal', function () {
        FV.clearFormById('customerCreateForm');
        $('#customerCreateForm')[0].reset();
        $('#c-vat-number-group').hide();
    });

    // =============================================
    // CREATE - Submit via AJAX
    // =============================================

    $(document).on('click', '#createCustomerBtn', function () {
        if (!validateCustomerForm('c')) {
            return;
        }

        var form     = document.getElementById('customerCreateForm');
        var formData = new FormData(form);
        var $btn     = $(this);

        $.ajax({
            url:         form.action,
            type:        'POST',
            data:        formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept':       'application/json'
            },
            beforeSend: function () {
                $btn.prop('disabled', true)
                    .prepend('<span class="spinner-border spinner-border-sm me-1 btn-spinner"></span>');
            },
            success: function (response) {
                if (response.success) {
                    bootstrap.Modal.getInstance(
                        document.getElementById('customerCreateModal')
                    ).hide();

                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    }

                    $('table.table-customer tbody').prepend(buildCustomerRow(response.data));
                    refilterAfterDomChange();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    applyServerErrors(xhr.responseJSON.errors, buildErrorFieldMap('c'));
                } else {
                    if (typeof toastr !== 'undefined') { toastr.error('Something went wrong.'); }
                }
            },
            complete: function () {
                $btn.prop('disabled', false).find('.btn-spinner').remove();
            }
        });
    });

    // =============================================
    // EDIT MODAL - Populate fields
    // =============================================

    $(document).on('click', '.btn-customer-edit', function () {
        var $btn = $(this);

        FV.clearFormById('customerEditForm');

        var id            = $btn.data('id');
        var vatRegistered = ES
            ? ES.normalizeStatus($btn.attr('data-vat-registered'))
            : ($btn.attr('data-vat-registered') === '1' ? '1' : '0');

        // Set form action
        document.getElementById('customerEditForm').action = '/admin/customers/' + id;
        $('#edit-id').val(id);

        // Basic
        $('#e-name').val($btn.attr('data-name'));
        $('#e-email').val($btn.attr('data-email'));
        $('#e-phone').val($btn.attr('data-phone'));
        $('#e-type').val($btn.attr('data-type') || 'individual');
        $('#e-gender').val($btn.attr('data-gender') || '');
        $('#e-birthdate').val($btn.attr('data-birthdate') || '');

        // Address
        $('#e-address').val($btn.attr('data-address') || '');
        $('#e-shipping-address').val($btn.attr('data-shipping-address') || '');
        $('#e-city').val($btn.attr('data-city') || '');
        $('#e-pin-code').val($btn.attr('data-pin-code') || '');
        $('#e-state').val($btn.attr('data-state') || '');
        $('#e-country').val($btn.attr('data-country') || '');
        $('#e-landmark').val($btn.attr('data-landmark') || '');
        $('#e-area-id').val($btn.attr('data-area-id') || '');

        // Credit
        $('#e-credit-days').val($btn.attr('data-credit-days') || '');
        $('#e-credit-limit').val($btn.attr('data-credit-limit') || '');

        // VAT
        if (vatRegistered === '1') {
            $('#e-vat-registered').prop('checked', true);
            $('#e-vat-number-group').show();
            $('#e-vat-number').val($btn.attr('data-vat-number') || '');
        } else {
            $('#e-vat-registered').prop('checked', false);
            $('#e-vat-number-group').hide();
            $('#e-vat-number').val('');
        }

        new bootstrap.Modal(document.getElementById('customerEditModal')).show();
    });

    // =============================================
    // EDIT - Submit via AJAX
    // =============================================

    $(document).on('click', '#updateCustomerBtn', function () {
        if (!validateCustomerForm('e')) {
            return;
        }

        var form     = document.getElementById('customerEditForm');
        var formData = new FormData(form);
        var $btn     = $(this);

        $.ajax({
            url:         form.action,
            type:        'POST',
            data:        formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept':       'application/json'
            },
            beforeSend: function () {
                $btn.prop('disabled', true)
                    .prepend('<span class="spinner-border spinner-border-sm me-1 btn-spinner"></span>');
            },
            success: function (response) {
                if (response.success) {
                    bootstrap.Modal.getInstance(
                        document.getElementById('customerEditModal')
                    ).hide();

                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    }

                    if (ES && response.data) {
                        ES.syncCustomerRow(response.data);
                    }

                    refilterAfterDomChange();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    applyServerErrors(xhr.responseJSON.errors, buildErrorFieldMap('e'));
                } else {
                    if (typeof toastr !== 'undefined') { toastr.error('Something went wrong.'); }
                }
            },
            complete: function () {
                $btn.prop('disabled', false).find('.btn-spinner').remove();
            }
        });
    });

    // =============================================
    // VIEW MODAL - Populate and open
    // =============================================

    $(document).on('click', '.btn-customer-view', function () {
        var $btn = $(this);

        var vatRegistered = ES
            ? ES.normalizeStatus($btn.attr('data-vat-registered'))
            : ($btn.attr('data-vat-registered') === '1' ? '1' : '0');

        var status = ES
            ? ES.normalizeStatus($btn.attr('data-status'))
            : ($btn.attr('data-status') === '1' ? '1' : '0');

        // Basic
        $('#v-name').text(orDash($btn.attr('data-name')));
        $('#v-email').text(orDash($btn.attr('data-email')));
        $('#v-phone').text(orDash($btn.attr('data-phone')));
        $('#v-type').text(ucfirst($btn.attr('data-type')));
        $('#v-gender').text(ucfirst($btn.attr('data-gender')));
        $('#v-birthdate').text(orDash($btn.attr('data-birthdate')));

        // Address
        $('#v-address').text(orDash($btn.attr('data-address')));
        $('#v-shipping-address').text(orDash($btn.attr('data-shipping-address')));
        $('#v-city').text(orDash($btn.attr('data-city')));
        $('#v-pin-code').text(orDash($btn.attr('data-pin-code')));
        $('#v-state').text(orDash($btn.attr('data-state')));
        $('#v-country').text(orDash($btn.attr('data-country')));
        $('#v-landmark').text(orDash($btn.attr('data-landmark')));
        $('#v-area').text(orDash($btn.attr('data-area-name')));

        // Credit
        var creditDays  = $btn.attr('data-credit-days');
        var creditLimit = $btn.attr('data-credit-limit');
        $('#v-credit-days').text(creditDays ? creditDays + ' days' : '-');
        $('#v-credit-limit').text(creditLimit ? '£' + parseFloat(creditLimit).toFixed(2) : '-');

        // VAT
        $('#v-vat-registered').text(vatRegistered === '1' ? 'Yes' : 'No');
        var vatNumber = $btn.attr('data-vat-number');
        $('#v-vat-number').text(vatRegistered === '1' && vatNumber ? vatNumber : '-');

        // Status
        $('#v-status').html(
            status === '1'
                ? '<span class="badge-status-enabled">Active</span>'
                : '<span class="badge-status-disabled">Inactive</span>'
        );

        new bootstrap.Modal(document.getElementById('customerViewModal')).show();
    });

    // =============================================
    // TOGGLE STATUS - Open confirm modal
    // =============================================

    $(document).on('click', '.btn-customer-toggle', function () {
        var $btn   = $(this);
        var id     = $btn.attr('data-id');
        var name   = $btn.attr('data-name');
        var status = ES ? ES.normalizeStatus($btn.attr('data-status')) : $btn.attr('data-status');

        var actionLabel = status === '1' ? 'Deactivate' : 'Activate';

        $('#customer-confirm-title').text(actionLabel + ' Customer');
        $('#customer-confirm-body').html(
            'Are you sure you want to <strong>' + actionLabel.toLowerCase() + '</strong> the customer "<strong>' + name + '</strong>"?'
        );

        document.getElementById('customerConfirmForm').action = '/admin/customers/' + id + '/toggle';
        $('#customer-method-field').html('<input type="hidden" name="_method" value="PATCH">');
        $('#confirm-id').val(id);

        var $confirmBtn = $('#customer-confirm-submit');
        $confirmBtn.removeClass('btn-danger btn-warning btn-success')
                   .addClass(status === '1' ? 'btn-warning' : 'btn-success')
                   .text(actionLabel);

        new bootstrap.Modal(document.getElementById('customerConfirmModal')).show();
    });

    // =============================================
    // DELETE - Open confirm modal
    // =============================================

    $(document).on('click', '.btn-customer-delete', function () {
        var $btn = $(this);
        var id   = $btn.attr('data-id');
        var name = $btn.attr('data-name');

        $('#customer-confirm-title').text('Delete Customer');
        $('#customer-confirm-body').html(
            'Are you sure you want to permanently delete "<strong>' + name + '</strong>"? This action cannot be undone.'
        );

        document.getElementById('customerConfirmForm').action = '/admin/customers/' + id;
        $('#customer-method-field').html('<input type="hidden" name="_method" value="DELETE">');
        $('#confirm-id').val(id);

        $('#customer-confirm-submit')
            .removeClass('btn-warning btn-success btn-danger')
            .addClass('btn-danger')
            .text('Delete');

        new bootstrap.Modal(document.getElementById('customerConfirmModal')).show();
    });

    // =============================================
    // CONFIRM SUBMIT (Toggle / Delete)
    // =============================================

    $(document).on('click', '#customer-confirm-submit', function (e) {
        e.preventDefault();

        var form     = document.getElementById('customerConfirmForm');
        var formData = new FormData(form);
        var method   = formData.get('_method');
        var $btn     = $(this);

        $.ajax({
            url:         form.action,
            type:        'POST',
            data:        formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept':       'application/json'
            },
            beforeSend: function () {
                $btn.prop('disabled', true)
                    .prepend('<span class="spinner-border spinner-border-sm me-1 btn-spinner"></span>');
            },
            success: function (response) {
                if (response.success) {
                    bootstrap.Modal.getInstance(
                        document.getElementById('customerConfirmModal')
                    ).hide();

                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    }

                    var id = $('#confirm-id').val();

                    if (method === 'DELETE') {
                        $('#row-' + id).fadeOut(300, function () {
                            $(this).remove();
                            applyFilters();
                        });
                    } else if (method === 'PATCH') {
                        if (ES) {
                            ES.syncCustomerStatus(id, response.new_status);
                        } else {
                            var s = String(response.new_status);
                            var badgeHtml = (s === '1' || s === 'true')
                                ? '<span class="badge-status-enabled">Active</span>'
                                : '<span class="badge-status-disabled">Inactive</span>';

                            $('#status-container-' + id).html(badgeHtml);

                            var $row = $('#row-' + id);
                            $row.attr('data-status', s);
                            $row.find('.btn-customer-toggle').attr('data-status', s);
                        }
                        // Re-run filters so status filter is respected immediately
                        applyFilters();
                    }
                }
            },
            error: function () {
                if (typeof toastr !== 'undefined') { toastr.error('Something went wrong.'); }
            },
            complete: function () {
                $btn.prop('disabled', false).find('.btn-spinner').remove();
            }
        });
    });

});
