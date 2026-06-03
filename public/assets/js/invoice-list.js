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
        form.action = '/admin/invoices/' + invoiceId + '/status';
        
        // Add ID input so we can update UI
        if ($('#status-invoice-id').length === 0) {
            $(form).append('<input type="hidden" id="status-invoice-id" value="'+invoiceId+'">');
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
        var selected = $('input[name="status_pick"]:checked');
        if (!selected.length) {
            $('#status-pick-error').text('Please select a status.');
            return;
        }

        $('#status-value').val(selected.val());
        
        var form = document.getElementById('invoiceStatusForm');
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
                    var modal = bootstrap.Modal.getInstance(document.getElementById('invoiceStatusModal'));
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    var id = $('#status-invoice-id').val();
                    var statusContainer = $('#status-container-' + id);
                    if (statusContainer.length) {
                        var newStatus = response.new_status;
                        // Map status value to correct CSS class
                        var statusClassMap = {
                            'draft':  'invoice-status-draft',
                            'unpaid': 'invoice-status-sent',
                            'paid':   'invoice-status-paid',
                            'due':    'invoice-status-cancelled'
                        };
                        var badgeClass = statusClassMap[newStatus] || 'invoice-status-draft';
                        var badgeText = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                        statusContainer.html('<span class="' + badgeClass + '">' + badgeText + '</span>');
                    }
                    var statusBtn = $('.btn-invoice-status[data-id="'+id+'"]');
                    if (statusBtn.length) {
                        statusBtn.attr('data-status', response.new_status);
                    }
                }
            },
            error: function () {
                if (typeof toastr !== 'undefined') toastr.error('Something went wrong.');
            }
        });
    });


    // =============================================
    // DELETE MODAL - Open
    // =============================================
    $(document).on('click', '.btn-invoice-delete', function () {
        var invoiceId     = $(this).attr('data-id');
        var invoiceNumber = $(this).attr('data-number');

        $('#delete-confirm-body').text('Are you sure you want to permanently delete invoice ' + invoiceNumber + '? This action cannot be undone.');

        var form = document.getElementById('invoiceDeleteForm');
        form.action = '/admin/invoices/' + invoiceId;

        // Add ID input so we can update UI
        if ($('#delete-invoice-id').length === 0) {
            $(form).append('<input type="hidden" id="delete-invoice-id" value="'+invoiceId+'">');
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
        
        var form = document.getElementById('invoiceDeleteForm');
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
                    var modal = bootstrap.Modal.getInstance(document.getElementById('invoiceDeleteModal'));
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    var id = $('#delete-invoice-id').val();
                    var row = $('#row-' + id);
                    if (row.length) row.remove();
                    else window.location.reload();
                }
            },
            error: function () {
                if (typeof toastr !== 'undefined') toastr.error('Something went wrong.');
            }
        });
    });

});