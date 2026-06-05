<div class="modal fade" id="vendorEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <form id="vendorEditForm" method="POST" action="#">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="edit-vnd-id" name="id">

                    <div class="row g-3">

                        {{-- Name --}}
                        <div class="col-md-6">
                            <label class="form-label" for="edit-vnd-name">Contact Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit-vnd-name"
                                class="form-control validate-name"
                                data-label="Contact name"
                                maxlength="100">
                            <span class="field-error text-danger small" id="edit-vnd-name-error"></span>
                        </div>

                        {{-- Company --}}
                        <div class="col-md-6">
                            <label class="form-label" for="edit-vnd-company">Company</label>
                            <input type="text" name="company" id="edit-vnd-company"
                                class="form-control"
                                data-label="Company"
                                maxlength="150">
                            <span class="field-error text-danger small" id="edit-vnd-company-error"></span>
                        </div>

                        {{-- Phone --}}
                        <div class="col-md-6">
                            <label class="form-label" for="edit-vnd-phone">Phone</label>
                            <input type="text" name="phone" id="edit-vnd-phone"
                                class="form-control validate-phone"
                                data-label="Phone"
                                maxlength="20">
                            <span class="field-error text-danger small" id="edit-vnd-phone-error"></span>
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label class="form-label" for="edit-vnd-email">Email</label>
                            <input type="email" name="email" id="edit-vnd-email"
                                class="form-control validate-email"
                                data-label="Email"
                                maxlength="100">
                            <span class="field-error text-danger small" id="edit-vnd-email-error"></span>
                        </div>

                        {{-- Tax Reg Number --}}
                        <div class="col-md-6">
                            <label class="form-label" for="edit-vnd-tax-reg">Tax Reg. Number</label>
                            <input type="text" name="tax_reg_number" id="edit-vnd-tax-reg"
                                class="form-control"
                                data-label="Tax registration number"
                                maxlength="50">
                            <span class="field-error text-danger small" id="edit-vnd-tax-reg-error"></span>
                        </div>

                        {{-- Divider --}}
                        <div class="col-12">
                            <hr class="my-1">
                            <p class="text-secondary small mb-0" style="font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.04em;">Address</p>
                        </div>

                        {{-- Address Line 1 --}}
                        <div class="col-md-6">
                            <label class="form-label" for="edit-vnd-addr1">Address Line 1</label>
                            <input type="text" name="address_line_1" id="edit-vnd-addr1"
                                class="form-control"
                                maxlength="255">
                            <span class="field-error text-danger small" id="edit-vnd-addr1-error"></span>
                        </div>

                        {{-- Address Line 2 --}}
                        <div class="col-md-6">
                            <label class="form-label" for="edit-vnd-addr2">Address Line 2</label>
                            <input type="text" name="address_line_2" id="edit-vnd-addr2"
                                class="form-control"
                                maxlength="255">
                            <span class="field-error text-danger small" id="edit-vnd-addr2-error"></span>
                        </div>

                        {{-- City --}}
                        <div class="col-md-4">
                            <label class="form-label" for="edit-vnd-city">City</label>
                            <input type="text" name="city" id="edit-vnd-city"
                                class="form-control"
                                maxlength="100">
                            <span class="field-error text-danger small" id="edit-vnd-city-error"></span>
                        </div>

                        {{-- Pin Code --}}
                        <div class="col-md-4">
                            <label class="form-label" for="edit-vnd-pin">Pin Code</label>
                            <input type="text" name="pin_code" id="edit-vnd-pin"
                                class="form-control"
                                maxlength="20">
                            <span class="field-error text-danger small" id="edit-vnd-pin-error"></span>
                        </div>

                        {{-- State --}}
                        <div class="col-md-4">
                            <label class="form-label" for="edit-vnd-state">State / Province</label>
                            <input type="text" name="state" id="edit-vnd-state"
                                class="form-control"
                                maxlength="100">
                            <span class="field-error text-danger small" id="edit-vnd-state-error"></span>
                        </div>

                        {{-- Country --}}
                        <div class="col-md-6">
                            <label class="form-label" for="edit-vnd-country">Country</label>
                            <input type="text" name="country" id="edit-vnd-country"
                                class="form-control"
                                maxlength="100">
                            <span class="field-error text-danger small" id="edit-vnd-country-error"></span>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal"
                        style="height: 48px; border-radius: 10px; font-weight: 600;">
                        Cancel
                    </button>
                    <button type="button" id="updateVendorBtn" class="btn btn-primary px-4"
                        style="height: 48px; border-radius: 10px; font-weight: 600;">
                        Update
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>