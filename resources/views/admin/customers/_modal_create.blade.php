<div class="modal fade" id="customerCreateModal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <form id="customerCreateForm" method="POST" action="{{ route('admin.customers.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    {{-- ── Section: Basic Info ── --}}
                    <p class="customer-section-label">Basic Information</p>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label>Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="c-name"
                                class="form-control validate-name" data-label="Name">
                            <span class="field-error text-danger small" id="c-name-error"></span>
                        </div>

                        <div class="col-md-6">
                            <label>Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="c-email"
                                class="form-control validate-email" data-label="Email">
                            <span class="field-error text-danger small" id="c-email-error"></span>
                        </div>

                        <div class="col-md-6">
                            <label>Phone Number</label>
                            <input type="text" name="phone" id="c-phone"
                                class="form-control pak-phone validate-phone" data-label="Phone number"
                                placeholder="03XX XXXXXXX or +923XX XXXXXXX">
                            <span class="field-error text-danger small" id="c-phone-error"></span>
                        </div>

                        <div class="col-md-3">
                            <label>Customer Type</label>
                            <select name="customer_type" id="c-type" class="form-control" data-label="Customer type">
                                <option value="individual" selected>Individual</option>
                                <option value="company">Company</option>
                            </select>
                            <span class="field-error text-danger small" id="c-type-error"></span>
                        </div>

                        <div class="col-md-3">
                            <label>Gender</label>
                            <select name="gender" id="c-gender" class="form-control">
                                <option value="">-- Select --</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <span class="field-error text-danger small" id="c-gender-error"></span>
                        </div>

                        <div class="col-md-3">
                            <label>Date of Birth</label>
                            <input type="date" name="birthdate" id="c-birthdate" class="form-control">
                            <span class="field-error text-danger small" id="c-birthdate-error"></span>
                        </div>

                    </div>

                    {{-- ── Section: Address ── --}}
                    <p class="customer-section-label mt-4">Address</p>

                    <div class="row g-3">

                        <div class="col-12">
                            <label>Billing Address</label>
                            <textarea name="address" id="c-address" class="form-control" rows="2"
                                data-label="Address"></textarea>
                            <span class="field-error text-danger small" id="c-address-error"></span>
                        </div>

                        <div class="col-12">
                            <label>Shipping Address</label>
                            <textarea name="shipping_address" id="c-shipping-address" class="form-control"
                                rows="2" placeholder="Leave blank if same as billing"></textarea>
                            <span class="field-error text-danger small" id="c-shipping-address-error"></span>
                        </div>

                        <div class="col-md-6">
                            <label>City</label>
                            <input type="text" name="city" id="c-city" class="form-control">
                            <span class="field-error text-danger small" id="c-city-error"></span>
                        </div>

                        <div class="col-md-6">
                            <label>PIN / ZIP Code</label>
                            <input type="text" name="pin_code" id="c-pin-code" class="form-control">
                            <span class="field-error text-danger small" id="c-pin-code-error"></span>
                        </div>

                        <div class="col-md-6">
                            <label>State / Province</label>
                            <input type="text" name="state" id="c-state" class="form-control">
                            <span class="field-error text-danger small" id="c-state-error"></span>
                        </div>

                        <div class="col-md-6">
                            <label>Country</label>
                            <input type="text" name="country" id="c-country" class="form-control">
                            <span class="field-error text-danger small" id="c-country-error"></span>
                        </div>

                        <div class="col-md-6">
                            <label>Landmark</label>
                            <input type="text" name="landmark" id="c-landmark" class="form-control"
                                placeholder="Nearby landmark">
                            <span class="field-error text-danger small" id="c-landmark-error"></span>
                        </div>

                        <div class="col-md-6">
                            <label>Area</label>
                            <select name="area_id" id="c-area-id" class="form-control">
                                <option value="">-- Select Area --</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                            <span class="field-error text-danger small" id="c-area-id-error"></span>
                        </div>

                    </div>

                    {{-- ── Section: Credit & VAT ── --}}
                    <p class="customer-section-label mt-4">Credit & Tax</p>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label>Credit Days</label>
                            <input type="number" name="credit_days" id="c-credit-days"
                                class="form-control validate-non-negative" min="0" max="65535"
                                placeholder="e.g. 30">
                            <span class="field-error text-danger small" id="c-credit-days-error"></span>
                        </div>

                        <div class="col-md-6">
                            <label>Credit Limit</label>
                            <input type="number" name="credit_limit" id="c-credit-limit"
                                class="form-control validate-non-negative" min="0" step="0.01"
                                placeholder="e.g. 50000.00">
                            <span class="field-error text-danger small" id="c-credit-limit-error"></span>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" value="1" name="vat_registered"
                                    id="c-vat-registered" class="form-check-input">
                                <label class="form-check-label" for="c-vat-registered">VAT Registered</label>
                            </div>
                        </div>

                        <div class="col-12" id="c-vat-number-group" style="display: none;">
                            <label>VAT Number <span class="text-danger">*</span></label>
                            <input type="text" name="vat_number" id="c-vat-number" class="form-control">
                            <span class="field-error text-danger small" id="c-vat-number-error"></span>
                        </div>

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
