<div class="modal fade" id="categoryEditModal" tabindex="-1" aria-hidden="true" aria-labelledby="editModalLabel">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="#" method="POST" id="categoryEditForm" class="ajax-validated-form" novalidate>
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label" for="edit-name">Category Name</label>
            <input type="text" name="name" id="edit-name" class="form-control category-name-input" required>
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="height: 48px; border-radius: 10px; font-weight: 600;">Cancel</button>
          <button type="submit" class="btn btn-primary px-4" id="btn-edit-submit" style="height: 48px; border-radius: 10px; font-weight: 600;">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
