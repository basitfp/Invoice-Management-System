<div class="modal fade" id="areaCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="areaCreateForm" method="POST" action="{{ route('admin.areas.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Area</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3 form-group-validation">
                        <label class="form-label" for="create-area-name">Area Name</label>
                        <input type="text" name="name" id="create-area-name"
                            class="form-control"
                            placeholder="e.g. Downtown Sector"
                            autocomplete="off">
                        <span class="field-error" id="create-area-name-error"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" id="createAreaBtn" class="btn btn-primary">
                        Save
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>