<div class="modal fade" id="categoryCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="categoryCreateForm" method="POST" action="{{ route('admin.categories.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="create-name">Category Name</label>
                        <input type="text" name="name" id="create-name"
                            class="form-control"
                            placeholder="e.g. Electronics">
                        <span class="field-error text-danger small" id="create-name-error"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal"
                        style="height: 48px; border-radius: 10px; font-weight: 600;">
                        Cancel
                    </button>
                    <button type="button" id="createCategoryBtn" class="btn btn-primary px-4"
                        style="height: 48px; border-radius: 10px; font-weight: 600;">
                        Save
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>