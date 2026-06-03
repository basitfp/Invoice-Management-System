$(document).ready(function () {

    // =============================================
    // HELPER - Show field-level error message
    // =============================================
    function showFieldError(errorSpanId, message) {
        let span = $('#' + errorSpanId);
        if (span.length) {
            span.text(message);
        }
    }

    // =============================================
    // HELPER - Clear all field errors inside a form
    // =============================================
    function clearAllErrors(formId) {
        let form = $('#' + formId);
        if (!form.length) return;

        form.find('.field-error').text('');
        form.find('.is-invalid').removeClass('is-invalid');
    }

    // =============================================
    // VIEW MODAL - Populate and open
    // =============================================
    $(document).on('click', '.btn-product-view', function () {
        let status = $(this).attr('data-status');

        $('#v-name').text($(this).attr('data-name'));
        $('#v-desc').text($(this).attr('data-desc') || '-');
        $('#v-category').text($(this).attr('data-category'));
        $('#v-qty').text($(this).attr('data-qty'));
        $('#v-purchase').text($(this).attr('data-purchase'));
        $('#v-selling').text($(this).attr('data-selling'));
        $('#v-vat').text($(this).attr('data-vat') + '%');
        $('#v-moq').text($(this).attr('data-moq'));
        $('#v-status').html(status === '1' ? '<span class="badge-status-enabled">Enabled</span>' : '<span class="badge-status-disabled">Disabled</span>');

        let modal = new bootstrap.Modal(document.getElementById('productViewModal'));
        modal.show();
    });

    // =============================================
    // CREATE MODAL - Clear errors when modal opens
    // =============================================
    $('#productCreateModal').on('show.bs.modal', function () {
        clearAllErrors('productCreateForm');
        $('#productCreateForm')[0].reset();
    });

    // =============================================
    // CREATE - JS Validation before submit
    // =============================================
    $(document).on('click', '#createProductBtn', function () {
        clearAllErrors('productCreateForm');

        let name     = $('#c-name').val().trim();
        let category = $('#c-category').val();
        let qty      = $('#c-qty').val().trim();
        let moq      = $('#c-moq').val().trim();
        let purchase = $('#c-purchase').val().trim();
        let selling  = $('#c-selling').val().trim();

        let hasError = false;

        if (name === '') {
            showFieldError('c-name-error', 'Product name is required.');
            $('#c-name').addClass('is-invalid');
            hasError = true;
        }

        if (category === '' || category === null) {
            showFieldError('c-category-error', 'Please select a category.');
            $('#c-category').addClass('is-invalid');
            hasError = true;
        }

        if (qty === '') {
            showFieldError('c-qty-error', 'Quantity is required.');
            $('#c-qty').addClass('is-invalid');
            hasError = true;
        }

        if (moq === '') {
            showFieldError('c-moq-error', 'MOQ is required.');
            $('#c-moq').addClass('is-invalid');
            hasError = true;
        }

        if (purchase === '') {
            showFieldError('c-purchase-error', 'Purchase price is required.');
            $('#c-purchase').addClass('is-invalid');
            hasError = true;
        }

        if (selling === '') {
            showFieldError('c-selling-error', 'Selling price is required.');
            $('#c-selling').addClass('is-invalid');
            hasError = true;
        }

        if (selling !== '' && purchase !== '' && parseFloat(selling) < parseFloat(purchase)) {
            showFieldError('c-selling-error', 'Selling price must be greater than or equal to purchase price.');
            $('#c-selling').addClass('is-invalid');
            hasError = true;
        }

        if (hasError) {
            if (typeof window.showGlobalValidationError === 'function') {
                window.showGlobalValidationError();
            }
            return;
        }

        var form = document.getElementById('productCreateForm');
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
                    var modal = bootstrap.Modal.getInstance(document.getElementById('productCreateModal'));
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    // Add new row to table
                    var newRow = `
                    <tr id="row-${response.data.id}">
                        <td>${response.data.id}</td>
                        <td id="name-${response.data.id}">${response.data.name}</td>
                        <td>${response.data.category ? response.data.category.name : '-'}</td>
                        <td class="text-center">${response.data.qty}</td>
                        <td class="text-end">£${parseFloat(response.data.selling_price).toFixed(2)}</td>
                        <td class="text-center">${response.data.vat}%</td>
                        <td class="text-center" id="status-container-${response.data.id}">
                            ${response.data.status == 1 ? '<span class="badge-status-enabled">Enabled</span>' : '<span class="badge-status-disabled">Disabled</span>'}
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-product-action btn-product-view"
                                    data-id="${response.data.id}"
                                    data-name="${response.data.name}"
                                    data-desc="${response.data.description || ''}"
                                    data-category="${response.data.category ? response.data.category.name : ''}"
                                    data-qty="${response.data.qty}"
                                    data-purchase="${response.data.purchase_price}"
                                    data-selling="${response.data.selling_price}"
                                    data-vat="${response.data.vat}"
                                    data-moq="${response.data.moq}"
                                    data-status="${response.data.status}"
                                    title="View">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-product-action btn-product-edit"
                                    data-id="${response.data.id}"
                                    data-name="${response.data.name}"
                                    data-desc="${response.data.description || ''}"
                                    data-category="${response.data.category_id}"
                                    data-qty="${response.data.qty}"
                                    data-purchase="${response.data.purchase_price}"
                                    data-selling="${response.data.selling_price}"
                                    data-vat="${response.data.vat}"
                                    data-moq="${response.data.moq}"
                                    data-status="${response.data.status}"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-product-action btn-product-toggle"
                                    data-id="${response.data.id}"
                                    data-name="${response.data.name}"
                                    data-status="${response.data.status}"
                                    title="Toggle Status">
                                    <i class="bi bi-slash-circle"></i>
                                </button>
                                <button type="button" class="btn btn-product-action btn-product-delete"
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
                        showFieldError('c-name-error', errors.name[0]);
                        $('#c-name').addClass('is-invalid');
                    }
                } else {
                    if (typeof toastr !== 'undefined') toastr.error('Something went wrong.');
                }
            }
        });
    });

    // =============================================
    // EDIT MODAL - Populate fields when edit button clicked
    // =============================================
    $(document).on('click', '.btn-product-edit', function () {
        clearAllErrors('productEditForm');

        let productId = $(this).attr('data-id');

        let form = document.getElementById('productEditForm');
        form.action = '/admin/products/' + productId;
        
        $('#edit-id').val(productId);

        $('#e-name').val($(this).attr('data-name'));
        $('#e-desc').val($(this).attr('data-desc'));
        $('#e-qty').val($(this).attr('data-qty'));
        $('#e-moq').val($(this).attr('data-moq'));
        $('#e-purchase').val($(this).attr('data-purchase'));
        $('#e-selling').val($(this).attr('data-selling'));

        $('#e-category').val($(this).attr('data-category'));
        $('#e-vat').val($(this).attr('data-vat'));
        $('#e-status').val($(this).attr('data-status'));

        let modal = new bootstrap.Modal(document.getElementById('productEditModal'));
        modal.show();
    });

    // =============================================
    // EDIT - JS Validation before submit
    // =============================================
    $(document).on('click', '#updateProductBtn', function () {
        clearAllErrors('productEditForm');

        let name     = $('#e-name').val().trim();
        let category = $('#e-category').val();
        let qty      = $('#e-qty').val().trim();
        let moq      = $('#e-moq').val().trim();
        let purchase = $('#e-purchase').val().trim();
        let selling  = $('#e-selling').val().trim();

        let hasError = false;

        if (name === '') {
            showFieldError('e-name-error', 'Product name is required.');
            $('#e-name').addClass('is-invalid');
            hasError = true;
        }

        if (category === '' || category === null) {
            showFieldError('e-category-error', 'Please select a category.');
            $('#e-category').addClass('is-invalid');
            hasError = true;
        }

        if (qty === '') {
            showFieldError('e-qty-error', 'Quantity is required.');
            $('#e-qty').addClass('is-invalid');
            hasError = true;
        }

        if (moq === '') {
            showFieldError('e-moq-error', 'MOQ is required.');
            $('#e-moq').addClass('is-invalid');
            hasError = true;
        }

        if (purchase === '') {
            showFieldError('e-purchase-error', 'Purchase price is required.');
            $('#e-purchase').addClass('is-invalid');
            hasError = true;
        }

        if (selling === '') {
            showFieldError('e-selling-error', 'Selling price is required.');
            $('#e-selling').addClass('is-invalid');
            hasError = true;
        }

        if (selling !== '' && purchase !== '' && parseFloat(selling) < parseFloat(purchase)) {
            showFieldError('e-selling-error', 'Selling price must be greater than or equal to purchase price.');
            $('#e-selling').addClass('is-invalid');
            hasError = true;
        }

        if (hasError) {
            if (typeof window.showGlobalValidationError === 'function') {
                window.showGlobalValidationError();
            }
            return;
        }

        var form = document.getElementById('productEditForm');
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
                    var modal = bootstrap.Modal.getInstance(document.getElementById('productEditModal'));
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    var id = $('#edit-id').val();
                    var nameCell = $('#name-' + id);
                    if (nameCell.length) nameCell.text(response.data.name);

                    // Update data attributes
                    var toggleBtn = $('.btn-product-toggle[data-id="'+id+'"]');
                    var editBtn = $('.btn-product-edit[data-id="'+id+'"]');
                    var viewBtn = $('.btn-product-view[data-id="'+id+'"]');
                    var delBtn = $('.btn-product-delete[data-id="'+id+'"]');

                    [toggleBtn, editBtn, viewBtn, delBtn].forEach(function(btn) {
                        if (btn.length) btn.attr('data-name', response.data.name);
                    });

                    if (editBtn.length) {
                        editBtn.attr('data-category', response.data.category_id);
                        editBtn.attr('data-qty', response.data.qty);
                        editBtn.attr('data-purchase', response.data.purchase_price);
                        editBtn.attr('data-selling', response.data.selling_price);
                        editBtn.attr('data-vat', response.data.vat);
                        editBtn.attr('data-moq', response.data.moq);
                        editBtn.attr('data-desc', response.data.description || '');
                    }
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        showFieldError('e-name-error', errors.name[0]);
                        $('#e-name').addClass('is-invalid');
                    }
                } else {
                    if (typeof toastr !== 'undefined') toastr.error('Something went wrong.');
                }
            }
        });
    });

    // =============================================
    // TOGGLE STATUS - Open confirm modal
    // =============================================
    $(document).on('click', '.btn-product-toggle', function () {
        let productId   = $(this).attr('data-id');
        let productName = $(this).attr('data-name');
        let status      = $(this).attr('data-status');

        let actionLabel = status === '1' ? 'Disable' : 'Enable';

        $('#confirm-title').text(actionLabel + ' Product');
        $('#confirm-body').text('Are you sure you want to ' + actionLabel.toLowerCase() + ' "' + productName + '"?');

        let confirmForm = document.getElementById('productConfirmForm');
        confirmForm.action = '/admin/products/' + productId + '/toggle-status';

        $('#confirm-method-field').html('<input type="hidden" name="_method" value="PATCH">');
        $('#confirm-id').val(productId);

        let confirmBtn = $('#confirm-submit-btn');
        confirmBtn.removeClass('btn-danger btn-warning btn-success');
        confirmBtn.addClass(status === '1' ? 'btn-warning' : 'btn-success');
        confirmBtn.text(actionLabel);

        let modal = new bootstrap.Modal(document.getElementById('productConfirmModal'));
        modal.show();
    });

    // =============================================
    // DELETE - Open confirm modal
    // =============================================
    $(document).on('click', '.btn-product-delete', function () {
        let productId   = $(this).attr('data-id');
        let productName = $(this).attr('data-name');

        $('#confirm-title').text('Delete Product');
        $('#confirm-body').text('Are you sure you want to permanently delete "' + productName + '"? This action cannot be undone.');

        let confirmForm = document.getElementById('productConfirmForm');
        confirmForm.action = '/admin/products/' + productId;

        $('#confirm-method-field').html('<input type="hidden" name="_method" value="DELETE">');
        $('#confirm-id').val(productId);

        let confirmBtn = $('#confirm-submit-btn');
        confirmBtn.removeClass('btn-warning btn-success btn-danger').addClass('btn-danger');
        confirmBtn.text('Delete');

        let modal = new bootstrap.Modal(document.getElementById('productConfirmModal'));
        modal.show();
    });

    // =============================================
    // CONFIRM SUBMIT (Toggle / Delete)
    // =============================================
    $(document).on('click', '#confirm-submit-btn', function (e) {
        e.preventDefault();
        
        var form = document.getElementById('productConfirmForm');
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
                    var modal = bootstrap.Modal.getInstance(document.getElementById('productConfirmModal'));
                    if (modal) modal.hide();

                    if (typeof toastr !== 'undefined') toastr.success(response.message);

                    var id = $('#confirm-id').val();

                    if (method === 'DELETE') {
                        var row = $('#row-' + id);
                        if (row.length) row.remove();
                        else window.location.reload();
                    } else if (method === 'PATCH') {
                        var statusContainer = $('#status-container-' + id);
                        if (statusContainer.length) {
                            if (response.new_status === 1) {
                                statusContainer.html('<span class="badge-status-enabled">Enabled</span>');
                            } else {
                                statusContainer.html('<span class="badge-status-disabled">Disabled</span>');
                            }
                        }
                        var toggleBtn = $('.btn-product-toggle[data-id="'+id+'"]');
                        if (toggleBtn.length) toggleBtn.attr('data-status', response.new_status);
                        var viewBtn = $('.btn-product-view[data-id="'+id+'"]');
                        if (viewBtn.length) viewBtn.attr('data-status', response.new_status);
                        var editBtn = $('.btn-product-edit[data-id="'+id+'"]');
                        if (editBtn.length) editBtn.attr('data-status', response.new_status);
                    }
                }
            },
            error: function () {
                if (typeof toastr !== 'undefined') toastr.error('Something went wrong.');
            }
        });
    });

});