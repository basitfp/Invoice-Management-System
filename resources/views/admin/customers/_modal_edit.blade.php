<div class="modal fade" id="customerEditModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="customerEditForm" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="id" id="e-id">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Customer</h5>
                </div>

                <div class="modal-body">
                    <!-- Same fields as create but with ids -->
                    <div class="mb-3">
                        <label>Customer Name</label>
                        <input type="text" name="name" id="e-name" class="form-control customer-name customer-input">
                    </div>

                    <div class="mb-3">
                        <label>Email Address</label>
                        <input type="email" name="email" id="e-email" class="form-control customer-email customer-input">
                    </div>

                    <div class="mb-3">
                        <label>Phone Number</label>
                        <input type="text" name="phone" id="e-phone" class="form-control customer-phone customer-input">
                    </div>

                    <div class="mb-3">
                        <label>Customer Type</label>
                        <select name="customer_type" id="e-type" class="form-control customer-type customer-input">
                            <option value="regular">Regular</option>
                            <option value="business">Business</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Address</label>
                        <textarea name="address" id="e-address" class="form-control customer-address customer-input" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" value="1" name="vat_registered" id="vat_registered_edit" class="form-check-input customer-vat-registered">
                            <label class="form-check-label" for="vat_registered_edit">VAT Registered</label>
                        </div>
                    </div>

                    <div class="mb-3" id="vat_number_group_edit" style="display:none;">
                        <label>VAT Number</label>
                        <input type="text" name="vat_number" id="e-vat-number" class="form-control customer-vat-number customer-input">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>