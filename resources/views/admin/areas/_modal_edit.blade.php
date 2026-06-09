<div class="modal fade" id="areaEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="areaEditForm" method="POST" action="#">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Area</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="edit-area-id" name="id">
                    <div class="mb-3 form-group-validation">
                        <label class="form-label" for="edit-area-name">Area Name</label>
                        <input type="text" name="name" id="edit-area-name"
                            class="form-control"
                            autocomplete="off">
                        <span class="field-error" id="edit-area-name-error"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" id="updateAreaBtn" class="btn btn-primary">
                        Update
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>