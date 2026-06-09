<div class="modal fade" id="manufacturerCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="manufacturerCreateForm" method="POST" action="{{ route('admin.manufacturers.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Manufacturer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    {{-- Name --}}
                    <div class="mb-3">
                        <label class="form-label" for="create-mfr-name">Manufacturer Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="create-mfr-name"
                            class="form-control validate-name"
                            data-label="Manufacturer name"
                            placeholder="e.g. Acme Corp"
                            maxlength="100">
                        <span class="field-error text-danger small" id="create-mfr-name-error"></span>
                    </div>

                    {{-- Phone --}}
                    <div class="mb-3">
                        <label class="form-label" for="create-mfr-phone">Phone</label>
                        <input type="text" name="phone" id="create-mfr-phone"
                            class="form-control validate-phone"
                            data-label="Phone"
                            placeholder="e.g. 03001234567"
                            maxlength="20">
                        <span class="field-error text-danger small" id="create-mfr-phone-error"></span>
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label" for="create-mfr-email">Email</label>
                        <input type="email" name="email" id="create-mfr-email"
                            class="form-control validate-email"
                            data-label="Email"
                            placeholder="e.g. info@acme.com"
                            maxlength="100">
                        <span class="field-error text-danger small" id="create-mfr-email-error"></span>
                    </div>

                    {{-- Address --}}
                    <div class="mb-3">
                        <label class="form-label" for="create-mfr-address">Address</label>
                        <input type="text" name="address" id="create-mfr-address"
                            class="form-control"
                            data-label="Address"
                            placeholder="e.g. 123 Industrial Zone, Karachi"
                            maxlength="255">
                        <span class="field-error text-danger small" id="create-mfr-address-error"></span>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" id="createManufacturerBtn" class="btn btn-primary">
                        Save
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>