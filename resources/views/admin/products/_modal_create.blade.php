<div class="modal fade" id="productCreateModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <form id="productCreateForm" method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    {{-- IMAGE --}}
                    <div class="mb-4">
                        <label class="form-label">Product Image</label>
                        <div id="createProductDropzone" class="dropzone dropzone-container">
                            <div class="dz-message">
                                <i class="bi bi-cloud-arrow-up" style="font-size: 32px; color: #64748b;"></i>
                                <p class="mb-1" style="font-weight: 500; color: var(--text-primary);">Drag & drop product image here, or click to browse</p>
                                <span style="font-size: 12px; color: var(--text-secondary);">Supports JPG, JPEG, PNG, WEBP &mdash; Max 2 MB</span>
                            </div>
                        </div>
                        <span class="field-error text-danger small" id="c-image-error"></span>
                    </div>

                    {{-- GENERAL INFORMATION --}}
                    <div class="section-divider">General Information</div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="c-name" class="form-control" placeholder="Enter product name" maxlength="255" autocomplete="off">
                            <span class="field-error text-danger small" id="c-name-error"></span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Item Code (SKU)</label>
                            <input type="text" name="item_code" id="c-item_code" class="form-control" maxlength="100" placeholder="e.g. SKU-100" autocomplete="off">
                            <span class="field-error text-danger small" id="c-item_code-error"></span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Item Class</label>
                            <select name="item_class" id="c-item_class" class="form-control">
                                <option value="general" selected>General</option>
                                <option value="sale_only">Sale Only</option>
                                <option value="raw_material">Raw Material</option>
                            </select>
                            <span class="field-error text-danger small" id="c-item_class-error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="c-category_id" class="form-control">
                                <option value="" disabled selected>Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <span class="field-error text-danger small" id="c-category_id-error"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Manufacturer</label>
                            <select name="manufacturer_id" id="c-manufacturer_id" class="form-control">
                                <option value="">Select Manufacturer (Optional)</option>
                                @foreach($manufacturers as $manufacturer)
                                    <option value="{{ $manufacturer->id }}">{{ $manufacturer->name }}</option>
                                @endforeach
                            </select>
                            <span class="field-error text-danger small" id="c-manufacturer_id-error"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>HSN Code</label>
                            <input type="text" name="hsn_code" id="c-hsn_code" class="form-control" maxlength="50" placeholder="HSN Code" autocomplete="off">
                            <span class="field-error text-danger small" id="c-hsn_code-error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label>Regional Name</label>
                            <input type="text" name="regional_name" id="c-regional_name" class="form-control" maxlength="255" placeholder="Local language reference name" autocomplete="off">
                            <span class="field-error text-danger small" id="c-regional_name-error"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Unit</label>
                            <input type="text" name="unit" id="c-unit" class="form-control" maxlength="50" placeholder="e.g. pcs, box, kg" autocomplete="off">
                            <span class="field-error text-danger small" id="c-unit-error"></span>
                        </div>
                    </div>

                    {{-- PURCHASE INFORMATION --}}
                    <div class="section-divider">Purchase Information</div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Purchase Price</label>
                            <input type="number" name="purchase_price" id="c-purchase_price" class="form-control" min="0" step="0.01" value="0">
                            <span class="field-error text-danger small" id="c-purchase_price-error"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Purchase Tax %</label>
                            <input type="number" name="purchase_tax_percent" id="c-purchase_tax_percent" class="form-control" min="0" max="100" step="0.01" value="0">
                            <span class="field-error text-danger small" id="c-purchase_tax_percent-error"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Tax Inclusive</label>
                            <select name="purchase_tax_inclusive" id="c-purchase_tax_inclusive" class="form-control">
                                <option value="0" selected>No</option>
                                <option value="1">Yes</option>
                            </select>
                            <span class="field-error text-danger small" id="c-purchase_tax_inclusive-error"></span>
                        </div>
                    </div>

                    {{-- SALE INFORMATION --}}
                    <div class="section-divider">Sale Information</div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Sale Price <span class="text-danger">*</span></label>
                            <input type="number" name="sale_price" id="c-sale_price" class="form-control" min="0" step="0.01" value="0">
                            <span class="field-error text-danger small" id="c-sale_price-error"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>GST / VAT %</label>
                            <input type="number" name="gst_vat_percent" id="c-gst_vat_percent" class="form-control" min="0" max="100" step="0.01" value="0">
                            <span class="field-error text-danger small" id="c-gst_vat_percent-error"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Tax Inclusive</label>
                            <select name="sale_tax_inclusive" id="c-sale_tax_inclusive" class="form-control">
                                <option value="0" selected>No</option>
                                <option value="1">Yes</option>
                            </select>
                            <span class="field-error text-danger small" id="c-sale_tax_inclusive-error"></span>
                        </div>
                    </div>

                    {{-- ADDITIONAL SETTINGS --}}
                    <div class="section-divider">Additional Settings</div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label>Discount %</label>
                            <input type="number" name="discount_percent" id="c-discount_percent" class="form-control" min="0" max="100" step="0.01" value="0">
                            <span class="field-error text-danger small" id="c-discount_percent-error"></span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Cess %</label>
                            <input type="number" name="cess_percent" id="c-cess_percent" class="form-control" min="0" max="100" step="0.01" value="0">
                            <span class="field-error text-danger small" id="c-cess_percent-error"></span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Additional Cess</label>
                            <input type="number" name="additional_cess" id="c-additional_cess" class="form-control" min="0" step="0.01" value="0">
                            <span class="field-error text-danger small" id="c-additional_cess-error"></span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Weighing Item</label>
                            <select name="is_weighing_item" id="c-is_weighing_item" class="form-control">
                                <option value="0" selected>No</option>
                                <option value="1">Yes</option>
                            </select>
                            <span class="field-error text-danger small" id="c-is_weighing_item-error"></span>
                        </div>
                    </div>

                    {{-- INVENTORY & STATUS --}}
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="section-divider">Inventory</div>
                            <div class="row">
                                <div class="col-6">
                                    <label>Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="qty" id="c-qty" class="form-control" min="0" step="1" value="1">
                                    <span class="field-error text-danger small" id="c-qty-error"></span>
                                </div>
                                <div class="col-6">
                                    <label>MOQ <span class="text-danger">*</span></label>
                                    <input type="number" name="moq" id="c-moq" class="form-control" min="1" step="1" value="1">
                                    <span class="field-error text-danger small" id="c-moq-error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 mb-3">
                            <div class="section-divider">Status</div>
                            <label>Product Status</label>
                            <select name="status" id="c-status" class="form-control">
                                <option value="1" selected>Enable</option>
                                <option value="0">Disable</option>
                            </select>
                            <span class="field-error text-danger small" id="c-status-error"></span>
                        </div>
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="section-divider">Description</div>
                    <div class="mb-3">
                        <label>Description Details</label>
                        <textarea name="description" id="c-description" class="form-control" rows="3" placeholder="Add custom configurations or product notes..."></textarea>
                        <span class="field-error text-danger small" id="c-description-error"></span>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="createProductBtn" class="btn btn-primary">
                        <span id="createProductBtnText">Save Product</span>
                        <span id="createProductBtnSpinner" class="spinner-border spinner-border-sm ms-1" role="status" style="display: none;"></span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
