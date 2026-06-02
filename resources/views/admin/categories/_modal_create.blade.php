<div class="modal fade" id="categoryCreateModal" tabindex="-1" aria-hidden="true" aria-labelledby="createModalLabel">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="productCreateForm" method="POST" action="{{ route('admin.products.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="createModalLabel">Add Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label" for="create-name">Category Name</label>
            <input type="text" name="name" id="create-name" class="form-control category-name-input" placeholder="e.g. Electronics" required>
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="height: 48px; border-radius: 10px; font-weight: 600;">Cancel</button>
          <button type="submit" class="btn btn-primary px-4" id="btn-create-submit" disabled style="height: 48px; border-radius: 10px; font-weight: 600;">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
