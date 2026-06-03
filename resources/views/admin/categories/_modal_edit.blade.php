<div class="modal fade" id="categoryEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="categoryEditForm" method="POST" action="#">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="mb-3">
                        <label class="form-label" for="edit-name">Category Name</label>
                        <input type="text" name="name" id="edit-name" class="form-control">
                        <span class="field-error text-danger small" id="edit-name-error"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal"
                        style="height: 48px; border-radius: 10px; font-weight: 600;">
                        Cancel
                    </button>
                    <button type="button" id="updateCategoryBtn" class="btn btn-primary px-4"
                        style="height: 48px; border-radius: 10px; font-weight: 600;">
                        Update
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>