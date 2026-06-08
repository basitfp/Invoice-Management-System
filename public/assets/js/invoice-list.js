$(document).ready(function () {

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

        // FIX: Populate the hidden status field so FormData sends the correct value
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
                        // draft or unknown
                        badgeHtml = '<span class="invoice-status-draft">' + statusLabel + '</span>';
                    }

                    // FIX: target both id-based container (admin) and class-based (agent)
                    var $row = $('#row-' + invoiceId);
                    if ($row.length) {
                        // Try class selector first (agent index), then id selector (admin index)
                        var $statusCell = $row.find('.status-container');
                        if (!$statusCell.length) {
                            $statusCell = $('#status-container-' + invoiceId);
                        }
                        $statusCell.html(badgeHtml);

                        // Update data-status attribute so next modal open shows correct current status
                        $row.find('.btn-invoice-status').attr('data-status', newStatus);
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
                    if (row.length) row.remove();
                    else window.location.reload();
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