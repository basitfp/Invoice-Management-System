<div class="modal fade" id="productEditModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="productEditForm" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="id" id="e-id">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" id="e-name" class="form-control product-name product-input">
                    </div>

                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" id="e-desc" class="form-control product-desc product-input"></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Category</label>
                        <select name="category_id" id="e-category" class="form-control product-category product-input"></select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Qty</label>
                            <input type="number" name="qty" id="e-qty" class="form-control product-qty product-input">
                        </div>
                        <div class="col-md-6">
                            <label>MOQ</label>
                            <input type="number" name="moq" id="e-moq" class="form-control product-moq product-input">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label>Purchase Price</label>
                            <input type="number" name="purchase_price" id="e-purchase" class="form-control product-purchase product-input">
                        </div>
                        <div class="col-md-6">
                            <label>Selling Price</label>
                            <input type="number" name="selling_price" id="e-selling" class="form-control product-selling product-input">
                        </div>
                    </div>

                    <div class="mb-3 mt-2">
                        <label>VAT (%)</label>
                        <select name="vat" id="e-vat" class="form-control product-vat product-input">
                            <option value="0">0%</option>
                            <option value="20">20%</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" id="e-status" class="form-control product-status product-input">
                            <option value="1">Enable</option>
                            <option value="0">Disable</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>