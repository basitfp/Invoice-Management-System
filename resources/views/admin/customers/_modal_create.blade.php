<div class="modal fade" id="customerCreateModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="customerCreateForm" method="POST" action="{{ route('admin.customers.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add New Customer</h5>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Customer Name</label>
                        <input type="text" name="name" class="form-control customer-name customer-input">
                    </div>

                    <div class="mb-3">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control customer-email customer-input">
                    </div>

                    <div class="mb-3">
                        <label>Phone Number</label>
                        <input type="text" name="phone" class="form-control customer-phone customer-input">
                    </div>

                    <div class="mb-3">
                        <label>Customer Type</label>
                        <select name="customer_type" class="form-control customer-type customer-input">
                            <option value="regular">Regular</option>
                            <option value="business">Business</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Address</label>
                        <textarea name="address" class="form-control customer-address customer-input" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" value="1" name="vat_registered" id="vat_registered_create" class="form-check-input customer-vat-registered">
                            <label class="form-check-label" for="vat_registered_create">VAT Registered</label>
                        </div>
                    </div>

                    <div class="mb-3" id="vat_number_group_create" style="display:none;">
                        <label>VAT Number</label>
                        <input type="text" name="vat_number" class="form-control customer-vat-number customer-input">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>