<div class="modal fade" id="customerEditModal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
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

                    {{-- ── Section: Basic Info ── --}}
                    <p class="customer-section-label">Basic Information</p>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label>Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="e-name"
                                class="form-control validate-name" data-label="Name">
                            <span class="field-error text-danger small" id="e-name-error"></span>
                        </div>

                        <div class="col-md-6">
                            <label>Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="e-email"
                                class="form-control validate-email" data-label="Email">
                            <span class="field-error text-danger small" id="e-email-error"></span>
                        </div>

                        <div class="col-md-6">
                            <label>Phone Number</label>
                            <input type="text" name="phone" id="e-phone"
                                class="form-control pak-phone validate-phone" data-label="Phone number"
                                placeholder="03XX XXXXXXX or +923XX XXXXXXX">
                            <span class="field-error text-danger small" id="e-phone-error"></span>
                        </div>

                        <div class="col-md-3">
                            <label>Customer Type</label>
                            <select name="customer_type" id="e-type" class="form-control" data-label="Customer type">
                                <option value="individual">Individual</option>
                                <option value="company">Company</option>
                            </select>
                            <span class="field-error text-danger small" id="e-type-error"></span>
                        </div>

                        <div class="col-md-3">
                            <label>Gender</label>
                            <select name="gender" id="e-gender" class="form-control">
                                <option value="">-- Select --</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <span class="field-error text-danger small" id="e-gender-error"></span>
                        </div>

                        <div class="col-md-3">
                            <label>Date of Birth</label>
                            <input type="date" name="birthdate" id="e-birthdate" class="form-control">
                            <span class="field-error text-danger small" id="e-birthdate-error"></span>
                        </div>

                    </div>

                    {{-- ── Section: Address ── --}}
                    <p class="customer-section-label mt-4">Address</p>

                    <div class="row g-3">

                        <div class="col-12">
                            <label>Billing Address</label>
                            <textarea name="address" id="e-address" class="form-control" rows="2"
                                data-label="Address"></textarea>
                            <span class="field-error text-danger small" id="e-address-error"></span>
                        </div>

                        <div class="col-12">
                            <label>Shipping Address</label>
                            <textarea name="shipping_address" id="e-shipping-address" class="form-control"
                                rows="2" placeholder="Leave blank if same as billing"></textarea>
                            <span class="field-error text-danger small" id="e-shipping-address-error"></span>
                        </div>

                        <div class="col-md-6">
                            <label>City</label>
                            <input type="text" name="city" id="e-city" class="form-control">
                            <span class="field-error text-danger small" id="e-city-error"></span>
                        </div>

                        <div class="col-md-6">
                            <label>PIN / ZIP Code</label>
                            <input type="text" name="pin_code" id="e-pin-code" class="form-control">
                            <span class="field-error text-danger small" id="e-pin-code-error"></span>
                        </div>

                        <div class="col-md-6">
                            <label>State / Province</label>
                            <input type="text" name="state" id="e-state" class="form-control">
                            <span class="field-error text-danger small" id="e-state-error"></span>
                        </div>

                        <div class="col-md-6">
                            <label>Country</label>
                            <input type="text" name="country" id="e-country" class="form-control">
                            <span class="field-error text-danger small" id="e-country-error"></span>
                        </div>

                        <div class="col-md-6">
                            <label>Landmark</label>
                            <input type="text" name="landmark" id="e-landmark" class="form-control"
                                placeholder="Nearby landmark">
                            <span class="field-error text-danger small" id="e-landmark-error"></span>
                        </div>

                        <div class="col-md-6">
                            <label>Area</label>
                            <select name="area_id" id="e-area-id" class="form-control">
                                <option value="">-- Select Area --</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                            <span class="field-error text-danger small" id="e-area-id-error"></span>
                        </div>

                    </div>

                    {{-- ── Section: Credit & VAT ── --}}
                    <p class="customer-section-label mt-4">Credit & Tax</p>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label>Credit Days</label>
                            <input type="number" name="credit_days" id="e-credit-days"
                                class="form-control validate-non-negative" min="0" max="65535"
                                placeholder="e.g. 30">
                            <span class="field-error text-danger small" id="e-credit-days-error"></span>
                        </div>

                        <div class="col-md-6">
                            <label>Credit Limit</label>
                            <input type="number" name="credit_limit" id="e-credit-limit"
                                class="form-control validate-non-negative" min="0" step="0.01"
                                placeholder="e.g. 50000.00">
                            <span class="field-error text-danger small" id="e-credit-limit-error"></span>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" value="1" name="vat_registered"
                                    id="e-vat-registered" class="form-check-input">
                                <label class="form-check-label" for="e-vat-registered">VAT Registered</label>
                            </div>
                        </div>

                        <div class="col-12" id="e-vat-number-group" style="display: none;">
                            <label>VAT Number <span class="text-danger">*</span></label>
                            <input type="text" name="vat_number" id="e-vat-number" class="form-control">
                            <span class="field-error text-danger small" id="e-vat-number-error"></span>
                        </div>

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
