<div class="modal fade" id="customerEditModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="customerEditForm" method="POST" action="#">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="edit-id" name="id">

                    <div class="mb-3">
                        <label>Customer Name</label>
                        <input type="text" name="name" id="e-name" class="form-control validate-name" data-label="Name">
                        <span class="field-error text-danger small" id="e-name-error"></span>
                    </div>

                    <div class="mb-3">
                        <label>Email Address</label>
                        <input type="email" name="email" id="e-email" class="form-control validate-email" data-label="Email">
                        <span class="field-error text-danger small" id="e-email-error"></span>
                    </div>

                    <div class="mb-3">
                        <label>Phone Number</label>
                        <input type="text" name="phone" id="e-phone" class="form-control pak-phone validate-phone" data-label="Phone number" placeholder="03XX XXXXXXX or +923XX XXXXXXX">
                        <span class="field-error text-danger small" id="e-phone-error"></span>
                    </div>

                    <div class="mb-3">
                        <label>Customer Type</label>
                        <select name="customer_type" id="e-type" class="form-control" data-label="Customer type">
                            <option value="regular">Regular</option>
                            <option value="business">Business</option>
                        </select>
                        <span class="field-error text-danger small" id="e-type-error"></span>
                    </div>

                    <div class="mb-3">
                        <label>Address</label>
                        <textarea name="address" id="e-address" class="form-control" rows="3" data-label="Address"></textarea>
                        <span class="field-error text-danger small" id="e-address-error"></span>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" value="1" name="vat_registered"
                                id="e-vat-registered" class="form-check-input">
                            <label class="form-check-label" for="e-vat-registered">VAT Registered</label>
                        </div>
                    </div>

                    <div class="mb-3" id="e-vat-number-group" style="display: none;">
                        <label>VAT Number</label>
                        <input type="text" name="vat_number" id="e-vat-number" class="form-control">
                        <span class="field-error text-danger small" id="e-vat-number-error"></span>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="updateCustomerBtn" class="btn btn-primary">Update Customer</button>
                </div>

            </form>
        </div>
    </div>
</div>