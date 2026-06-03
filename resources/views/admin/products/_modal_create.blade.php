<div class="modal fade" id="productCreateModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="productCreateForm" method="POST" action="{{ route('admin.products.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Product Name</label>
                        <input type="text" name="name" id="c-name" class="form-control">
                        <span class="field-error text-danger small" id="c-name-error"></span>
                    </div>

                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" id="c-desc" class="form-control"></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Category</label>
                        <select name="category_id" id="c-category" class="form-control">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <span class="field-error text-danger small" id="c-category-error"></span>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Qty</label>
                            <input type="number" name="qty" id="c-qty" class="form-control">
                            <span class="field-error text-danger small" id="c-qty-error"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>MOQ</label>
                            <input type="number" name="moq" id="c-moq" class="form-control">
                            <span class="field-error text-danger small" id="c-moq-error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Purchase Price</label>
                            <input type="number" name="purchase_price" id="c-purchase" class="form-control">
                            <span class="field-error text-danger small" id="c-purchase-error"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Selling Price</label>
                            <input type="number" name="selling_price" id="c-selling" class="form-control">
                            <span class="field-error text-danger small" id="c-selling-error"></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>VAT (%)</label>
                        <select name="vat" id="c-vat" class="form-control">
                            <option value="0">0%</option>
                            <option value="20">20%</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" id="c-status" class="form-control">
                            <option value="1">Enable</option>
                            <option value="0">Disable</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="createProductBtn" class="btn btn-primary">Save Product</button>
                </div>

            </form>
        </div>
    </div>
</div>