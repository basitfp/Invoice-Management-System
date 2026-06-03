<div class="modal fade" id="customerCreateModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="customerCreateForm" method="POST" action="{{ route('admin.customers.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Customer Name</label>
                        <input type="text" name="name" id="c-name" class="form-control">
                        <span class="field-error text-danger small" id="c-name-error"></span>
                    </div>

                    <div class="mb-3">
                        <label>Email Address</label>
                        <input type="email" name="email" id="c-email" class="form-control">
                        <span class="field-error text-danger small" id="c-email-error"></span>
                    </div>

                    <div class="mb-3">
                        <label>Phone Number</label>
                        <input type="text" name="phone" id="c-phone" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Customer Type</label>
                        <select name="customer_type" id="c-type" class="form-control">
                            <option value="regular">Regular</option>
                            <option value="business">Business</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Address</label>
                        <textarea name="address" id="c-address" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" value="1" name="vat_registered"
                                id="c-vat-registered" class="form-check-input">
                            <label class="form-check-label" for="c-vat-registered">VAT Registered</label>
                        </div>
                    </div>

                    <div class="mb-3" id="c-vat-number-group" style="display: none;">
                        <label>VAT Number</label>
                        <input type="text" name="vat_number" id="c-vat-number" class="form-control">
                        <span class="field-error text-danger small" id="c-vat-number-error"></span>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="createCustomerBtn" class="btn btn-primary">Save Customer</button>
                </div>

            </form>
        </div>
    </div>
</div>