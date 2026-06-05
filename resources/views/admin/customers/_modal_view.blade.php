<div class="modal fade" id="customerViewModal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Customer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                {{-- ── Basic Info ── --}}
                <p class="customer-section-label">Basic Information</p>

                <div class="row g-3 mb-1">
                    <div class="col-md-6">
                        <div class="customer-view-field">
                            <span class="customer-view-label">Customer Name</span>
                            <span class="customer-view-value" id="v-name"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="customer-view-field">
                            <span class="customer-view-label">Email Address</span>
                            <span class="customer-view-value" id="v-email"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="customer-view-field">
                            <span class="customer-view-label">Phone</span>
                            <span class="customer-view-value" id="v-phone"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="customer-view-field">
                            <span class="customer-view-label">Type</span>
                            <span class="customer-view-value" id="v-type"></span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="customer-view-field">
                            <span class="customer-view-label">Gender</span>
                            <span class="customer-view-value" id="v-gender"></span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="customer-view-field">
                            <span class="customer-view-label">Date of Birth</span>
                            <span class="customer-view-value" id="v-birthdate"></span>
                        </div>
                    </div>
                </div>

                {{-- ── Address ── --}}
                <p class="customer-section-label mt-3">Address</p>

                <div class="row g-3 mb-1">
                    <div class="col-md-6">
                        <div class="customer-view-field">
                            <span class="customer-view-label">Billing Address</span>
                            <span class="customer-view-value" id="v-address"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="customer-view-field">
                            <span class="customer-view-label">Shipping Address</span>
                            <span class="customer-view-value" id="v-shipping-address"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="customer-view-field">
                            <span class="customer-view-label">City</span>
                            <span class="customer-view-value" id="v-city"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="customer-view-field">
                            <span class="customer-view-label">PIN / ZIP Code</span>
                            <span class="customer-view-value" id="v-pin-code"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="customer-view-field">
                            <span class="customer-view-label">State / Province</span>
                            <span class="customer-view-value" id="v-state"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="customer-view-field">
                            <span class="customer-view-label">Country</span>
                            <span class="customer-view-value" id="v-country"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="customer-view-field">
                            <span class="customer-view-label">Landmark</span>
                            <span class="customer-view-value" id="v-landmark"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="customer-view-field">
                            <span class="customer-view-label">Area</span>
                            <span class="customer-view-value" id="v-area"></span>
                        </div>
                    </div>
                </div>

                {{-- ── Credit & Tax ── --}}
                <p class="customer-section-label mt-3">Credit & Tax</p>

                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="customer-view-field">
                            <span class="customer-view-label">Credit Days</span>
                            <span class="customer-view-value" id="v-credit-days"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="customer-view-field">
                            <span class="customer-view-label">Credit Limit</span>
                            <span class="customer-view-value" id="v-credit-limit"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="customer-view-field">
                            <span class="customer-view-label">VAT Registered</span>
                            <span class="customer-view-value" id="v-vat-registered"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="customer-view-field">
                            <span class="customer-view-label">VAT Number</span>
                            <span class="customer-view-value" id="v-vat-number"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="customer-view-field">
                            <span class="customer-view-label">Status</span>
                            <span class="customer-view-value" id="v-status"></span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>