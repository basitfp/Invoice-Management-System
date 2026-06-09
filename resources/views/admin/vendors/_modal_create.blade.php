<div class="modal fade" id="vendorCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <form id="vendorCreateForm" method="POST" action="{{ route('admin.vendors.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        {{-- Name --}}
                        <div class="col-md-6">
                            <label class="form-label" for="create-vnd-name">Contact Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="create-vnd-name"
                                class="form-control validate-name"
                                data-label="Contact name"
                                placeholder="e.g. Ahmed Raza"
                                maxlength="100">
                            <span class="field-error text-danger small" id="create-vnd-name-error"></span>
                        </div>

                        {{-- Company --}}
                        <div class="col-md-6">
                            <label class="form-label" for="create-vnd-company">Company</label>
                            <input type="text" name="company" id="create-vnd-company"
                                class="form-control"
                                data-label="Company"
                                placeholder="e.g. Raza Traders Pvt Ltd"
                                maxlength="150">
                            <span class="field-error text-danger small" id="create-vnd-company-error"></span>
                        </div>

                        {{-- Phone --}}
                        <div class="col-md-6">
                            <label class="form-label" for="create-vnd-phone">Phone</label>
                            <input type="text" name="phone" id="create-vnd-phone"
                                class="form-control validate-phone"
                                data-label="Phone"
                                placeholder="e.g. 03001234567"
                                maxlength="20">
                            <span class="field-error text-danger small" id="create-vnd-phone-error"></span>
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label class="form-label" for="create-vnd-email">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="create-vnd-email"
                                class="form-control validate-email"
                                data-label="Email"
                                placeholder="e.g. info@razatraders.com"
                                maxlength="100">
                            <span class="field-error text-danger small" id="create-vnd-email-error"></span>
                        </div>

                        {{-- Tax Reg Number --}}
                        <div class="col-md-6">
                            <label class="form-label" for="create-vnd-tax-reg">Tax Reg. Number</label>
                            <input type="text" name="tax_reg_number" id="create-vnd-tax-reg"
                                class="form-control"
                                data-label="Tax registration number"
                                placeholder="e.g. 1234567-8"
                                maxlength="50">
                            <span class="field-error text-danger small" id="create-vnd-tax-reg-error"></span>
                        </div>

                        {{-- Divider --}}
                        <div class="col-12">
                            <hr class="my-1">
                            <p class="vendor-section-label">Address</p>
                        </div>

                        {{-- Address Line 1 --}}
                        <div class="col-md-6">
                            <label class="form-label" for="create-vnd-addr1">Address Line 1</label>
                            <input type="text" name="address_line_1" id="create-vnd-addr1"
                                class="form-control"
                                placeholder="e.g. Shop 4, Block B"
                                maxlength="255">
                            <span class="field-error text-danger small" id="create-vnd-addr1-error"></span>
                        </div>

                        {{-- Address Line 2 --}}
                        <div class="col-md-6">
                            <label class="form-label" for="create-vnd-addr2">Address Line 2</label>
                            <input type="text" name="address_line_2" id="create-vnd-addr2"
                                class="form-control"
                                placeholder="e.g. Jodia Bazar"
                                maxlength="255">
                            <span class="field-error text-danger small" id="create-vnd-addr2-error"></span>
                        </div>

                        {{-- City --}}
                        <div class="col-md-4">
                            <label class="form-label" for="create-vnd-city">City</label>
                            <input type="text" name="city" id="create-vnd-city"
                                class="form-control"
                                placeholder="e.g. Karachi"
                                maxlength="100">
                            <span class="field-error text-danger small" id="create-vnd-city-error"></span>
                        </div>

                        {{-- Pin Code --}}
                        <div class="col-md-4">
                            <label class="form-label" for="create-vnd-pin">Pin Code</label>
                            <input type="text" name="pin_code" id="create-vnd-pin"
                                class="form-control"
                                placeholder="e.g. 74200"
                                maxlength="20">
                            <span class="field-error text-danger small" id="create-vnd-pin-error"></span>
                        </div>

                        {{-- State --}}
                        <div class="col-md-4">
                            <label class="form-label" for="create-vnd-state">State / Province</label>
                            <input type="text" name="state" id="create-vnd-state"
                                class="form-control"
                                placeholder="e.g. Sindh"
                                maxlength="100">
                            <span class="field-error text-danger small" id="create-vnd-state-error"></span>
                        </div>

                        {{-- Country --}}
                        <div class="col-md-6">
                            <label class="form-label" for="create-vnd-country">Country</label>
                            <input type="text" name="country" id="create-vnd-country"
                                class="form-control"
                                placeholder="e.g. Pakistan"
                                maxlength="100">
                            <span class="field-error text-danger small" id="create-vnd-country-error"></span>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" id="createVendorBtn" class="btn btn-primary">
                        Save
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>