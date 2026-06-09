$(document).ready(function () {
    var filterStartDate = null;
    var filterEndDate = null;

    $('#filter-date-range').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'YYYY-MM-DD'
        }
    });

    $('#filter-date-range').on('apply.daterangepicker', function (ev, picker) {
        filterStartDate = picker.startDate.format('YYYY-MM-DD');
        filterEndDate = picker.endDate.format('YYYY-MM-DD');
        $(this).val(filterStartDate + ' - ' + filterEndDate);
        applyFilters();
    });

    $('#filter-date-range').on('cancel.daterangepicker', function () {
        $(this).val('');
        filterStartDate = null;
        filterEndDate = null;
        applyFilters();
    });

  // =============================================
    // FILTERS - Area Module Standard (Enhanced Search)
    // =============================================
    function applyFilters() {
        var search     = $('#filter-search').val().trim().toLowerCase();
        var status     = $('#filter-status').val();
        var visibleCount = 0;

        $('#invoicesTable tbody tr').each(function () {
            var $row = $(this);
            if ($row.hasClass('filter-empty-row')) return;

            var rowInvoice = ($row.attr('data-invoice-number') || '').toLowerCase();
            var rowCustomer = ($row.attr('data-customer') || '').toLowerCase();
            var rowStatus   = String($row.attr('data-status') || '');
            var rowDate     = $row.attr('data-date') || '';
            var rowAmount   = parseFloat($row.attr('data-amount') || 0);

            var matchSearch = search === '' || 
                             rowInvoice.indexOf(search) !== -1 || 
                             rowCustomer.indexOf(search) !== -1 ||
                             String(rowAmount).indexOf(search) !== -1;

            var matchStatus = status === '' || rowStatus === status;
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

        var $tbody = $('#invoicesTable tbody');
        $tbody.find('.filter-empty-row').remove();

        if (visibleCount === 0) {
            var colCount = $('#invoicesTable thead th').length;
            $tbody.append(
                '<tr class="filter-empty-row">' +
                    '<td colspan="' + colCount + '" class="text-center py-5">' +
                        '<div class="filter-empty-state">' +
                            '<i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>' +
                            '<span class="text-muted">No invoices match the current filters.</span>' +
                        '</div>' +
                    '</td>' +
                '</tr>'
            );
        }
    }

    // Reset
    $('#filter-reset').on('click', function () {
        $('#filter-search').val('');
        $('#filter-status').val('');
        $('#filter-date-range').val('');
        filterStartDate = null;
        filterEndDate = null;
        applyFilters();
    });

    // Bind filters
    $('#filter-search').on('input', applyFilters);
    $('#filter-status').on('change', applyFilters);

    function refilterAfterDomChange() {
        var hasActive =
            $('#filter-search').val().trim() !== '' ||
            $('#filter-status').val() !== '' ||
            filterStartDate !== null ||
            filterEndDate !== null;

        if (hasActive) {
            applyFilters();
        }
    }

    // =============================================
    // STATUS OPTION LABELS - Toggle active class on click
    // =============================================
    $(document).on('click', '.status-option-label', function () {
        $('.status-option-label').removeClass('active');
        $(this).addClass('active');
        $(this).find('input[type="radio"]').prop('checked', true);
        $('#status-pick-error').text('');
    });

    // =============================================
    // STATUS MODAL - Open
    // =============================================
    $(document).on('click', '.btn-invoice-status', function () {
        var invoiceId     = $(this).attr('data-id');
        var invoiceNumber = $(this).attr('data-number');
        var currentStatus = $(this).attr('data-status');

        $('#status-modal-invoice-number').text('Invoice: ' + invoiceNumber);

        // Reset all labels then pre-select current status
        $('.status-option-label').removeClass('active');
        $('input[name="status_pick"]').each(function () {
            var isMatch = $(this).val() === currentStatus;
            $(this).prop('checked', isMatch);
            if (isMatch) {
                $(this).closest('.status-option-label').addClass('active');
            }
        });

        $('#status-pick-error').text('');

        var form = document.getElementById('invoiceStatusForm');
        var prefix = window.invoiceRoutePrefix || '/admin/invoices/';
        form.action = prefix + invoiceId + '/status';

        // Store invoice ID for UI update after submit
        if ($('#status-invoice-id').length === 0) {
            $(form).append('<input type="hidden" id="status-invoice-id" value="' + invoiceId + '">');
        } else {
            $('#status-invoice-id').val(invoiceId);
        }

        var modal = new bootstrap.Modal(document.getElementById('invoiceStatusModal'));
        modal.show();
    });

    // =============================================
    // STATUS MODAL - Submit (AJAX)
    // =============================================
    $(document).on('click', '#confirmStatusBtn', function () {
        var selectedStatus = $('input[name="status_pick"]:checked').val();

        if (!selectedStatus) {
            $('#status-pick-error').text('Please select a status.');
            return;
        }

        // Populate the hidden status field so FormData sends the correct value
        $('#status-value').val(selectedStatus);

        var form      = document.getElementById('invoiceStatusForm');
        var invoiceId = $('#status-invoice-id').val();
        var formData  = new FormData(form);
        var $btn      = $(this);

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
                    var modal = bootstrap.Modal.getInstance(document.getElementById('invoiceStatusModal'));
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    var newStatus   = response.new_status || selectedStatus;
                    var statusLabel = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);

                    // Build badge HTML
                    var badgeHtml = '';
                    if (newStatus === 'paid') {
                        badgeHtml = '<span class="invoice-status-paid">' + statusLabel + '</span>';
                    } else if (newStatus === 'unpaid') {
                        badgeHtml = '<span class="invoice-status-sent">' + statusLabel + '</span>';
                    } else if (newStatus === 'due') {
                        badgeHtml = '<span class="invoice-status-cancelled">' + statusLabel + '</span>';
                    } else {
                        badgeHtml = '<span class="invoice-status-draft">' + statusLabel + '</span>';
                    }

                    var $row = $('#row-' + invoiceId);
                    if ($row.length) {
                        var $statusCell = $row.find('.status-container');
                        if (!$statusCell.length) {
                            $statusCell = $('#status-container-' + invoiceId);
                        }
                        $statusCell.html(badgeHtml);

                        // Update action button dynamic status metadata
                        $row.find('.btn-invoice-status').attr('data-status', newStatus);

                        // FIXED: Synchronize altered values on container row attributes to maintain correct filtering logic
                        $row.attr('data-status', newStatus);
                        
                        // FIXED: Re-run filtration process in case mutated row no longer matches filtering requirements
                        refilterAfterDomChange();
                    } else {
                        window.location.reload();
                    }
                }
            },
            error: function (xhr) {
                var msg = 'Something went wrong.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                if (typeof toastr !== 'undefined') toastr.error(msg);
            },
            complete: function () {
                $btn.prop('disabled', false).find('.btn-spinner').remove();
            }
        });
    });

    // =============================================
    // DELETE MODAL - Open
    // =============================================
    $(document).on('click', '.btn-invoice-delete', function () {
        var invoiceId     = $(this).attr('data-id');
        var invoiceNumber = $(this).attr('data-number');

        $('#delete-confirm-body').text(
            'Are you sure you want to permanently delete invoice ' + invoiceNumber +
            '? This action cannot be undone.'
        );

        var form   = document.getElementById('invoiceDeleteForm');
        var prefix = window.invoiceRoutePrefix || '/admin/invoices/';
        form.action = prefix + invoiceId;

        if ($('#delete-invoice-id').length === 0) {
            $(form).append('<input type="hidden" id="delete-invoice-id" value="' + invoiceId + '">');
        } else {
            $('#delete-invoice-id').val(invoiceId);
        }

        var modal = new bootstrap.Modal(document.getElementById('invoiceDeleteModal'));
        modal.show();
    });

    // =============================================
    // DELETE MODAL - Submit (AJAX)
    // =============================================
    $(document).on('click', '#invoice-confirm-submit', function (e) {
        e.preventDefault();

        var form     = document.getElementById('invoiceDeleteForm');
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
                    var modal = bootstrap.Modal.getInstance(document.getElementById('invoiceDeleteModal'));
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    var id  = $('#delete-invoice-id').val();
                    var row = $('#row-' + id);
                    if (row.length) {
                        row.fadeOut(300, function() {
                            $(this).remove();
                            // Re-evaluate current listing count to display 'no results' notice if required
                            applyFilters();
                        });
                    } else {
                        window.location.reload();
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
