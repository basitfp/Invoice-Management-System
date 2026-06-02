<div class="modal fade" id="productCreateModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="productCreateForm" method="POST" action="{{ route('admin.products.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Product</h5>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Product Name</label>
                        <input type="text" name="name" class="form-control product-name product-input">
                    </div>

                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control product-desc product-input"></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Category</label>
                        <select name="category_id" class="form-control product-category product-input">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Qty</label>
                            <input type="number" name="qty" class="form-control product-qty product-input">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>MOQ</label>
                            <input type="number" name="moq" class="form-control product-moq product-input">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Purchase Price</label>
                            <input type="number" name="purchase_price" class="form-control product-purchase product-input">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Selling Price</label>
                            <input type="number" name="selling_price" class="form-control product-selling product-input">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>VAT (%)</label>
                        <select name="vat" class="form-control product-vat product-input">
                            <option value="0">0%</option>
                            <option value="20">20%</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control product-status product-input">
                            <option value="1">Enable</option>
                            <option value="0">Disable</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>