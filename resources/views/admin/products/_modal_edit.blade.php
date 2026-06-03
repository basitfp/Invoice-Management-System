<div class="modal fade" id="productEditModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="productEditForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="edit-id" name="id">

                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" id="e-name" class="form-control validate-name" data-label="Name">
                        <span class="field-error text-danger small" id="e-name-error"></span>
                    </div>

                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" id="e-desc" class="form-control"></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Category</label>
                        <select name="category_id" id="e-category" class="form-control" data-label="Category">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <span class="field-error text-danger small" id="e-category-error"></span>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Qty</label>
                            <input type="number" name="qty" id="e-qty" class="form-control validate-qty-int validate-non-negative" data-label="Quantity" min="0" step="1">
                            <span class="field-error text-danger small" id="e-qty-error"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>MOQ</label>
                            <input type="number" name="moq" id="e-moq" class="form-control validate-qty-int validate-non-negative" data-label="MOQ" min="0" step="1">
                            <span class="field-error text-danger small" id="e-moq-error"></span>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6 mb-3">
                            <label>Purchase Price</label>
                            <input type="number" name="purchase_price" id="e-purchase" class="form-control validate-non-negative" data-label="Purchase price" min="0" step="0.01">
                            <span class="field-error text-danger small" id="e-purchase-error"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Selling Price</label>
                            <input type="number" name="selling_price" id="e-selling" class="form-control validate-non-negative" data-label="Selling price" min="0" step="0.01">
                            <span class="field-error text-danger small" id="e-selling-error"></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>VAT (%)</label>
                        <select name="vat" id="e-vat" class="form-control">
                            <option value="0">0%</option>
                            <option value="20">20%</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" id="e-status" class="form-control">
                            <option value="1">Enable</option>
                            <option value="0">Disable</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="updateProductBtn" class="btn btn-primary">Update Product</button>
                </div>

            </form>
        </div>
    </div>
</div>