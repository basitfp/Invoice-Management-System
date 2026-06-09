<div class="modal fade" id="manufacturerEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="manufacturerEditForm" method="POST" action="#">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Manufacturer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="edit-mfr-id" name="id">

                    {{-- Name --}}
                    <div class="mb-3">
                        <label class="form-label" for="edit-mfr-name">Manufacturer Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit-mfr-name"
                            class="form-control validate-name"
                            data-label="Manufacturer name"
                            maxlength="100">
                        <span class="field-error text-danger small" id="edit-mfr-name-error"></span>
                    </div>

                    {{-- Phone --}}
                    <div class="mb-3">
                        <label class="form-label" for="edit-mfr-phone">Phone</label>
                        <input type="text" name="phone" id="edit-mfr-phone"
                            class="form-control validate-phone"
                            data-label="Phone"
                            maxlength="20">
                        <span class="field-error text-danger small" id="edit-mfr-phone-error"></span>
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label" for="edit-mfr-email">Email</label>
                        <input type="email" name="email" id="edit-mfr-email"
                            class="form-control validate-email"
                            data-label="Email"
                            maxlength="100">
                        <span class="field-error text-danger small" id="edit-mfr-email-error"></span>
                    </div>

                    {{-- Address --}}
                    <div class="mb-3">
                        <label class="form-label" for="edit-mfr-address">Address</label>
                        <input type="text" name="address" id="edit-mfr-address"
                            class="form-control"
                            data-label="Address"
                            maxlength="255">
                        <span class="field-error text-danger small" id="edit-mfr-address-error"></span>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" id="updateManufacturerBtn" class="btn btn-primary">
                        Update
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>