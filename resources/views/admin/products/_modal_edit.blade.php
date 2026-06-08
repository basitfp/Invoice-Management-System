<div class="modal fade" id="productEditModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <form id="productEditForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit-id" name="id">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    {{-- IMAGE --}}
                    <div class="mb-4">
                        <label class="form-label">Product Image</label>
                        <div class="row align-items-center g-3">
                            <div class="col-auto text-center" id="edit_image_preview_wrapper" style="display: none;">
                                <img id="edit_current_image" src="" alt="Current Image"
                                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 12px; border: 1px solid var(--border-color);">
                                <div style="font-size: 11px; color: var(--text-secondary); margin-top: 4px;">Current Image</div>
                                <button type="button" id="removeEditImageBtn" class="btn btn-sm btn-outline-danger mt-1" style="font-size: 11px; padding: 2px 8px; border-radius: 6px;">
                                    <i class="bi bi-x-lg"></i> Remove
                                </button>
                            </div>
                            <div class="col">
                                <div id="editProductDropzone" class="dropzone dropzone-container">
                                    <div class="dz-message">
                                        <i class="bi bi-arrow-left-right" style="font-size: 24px; color: #64748b;"></i>
                                        <p class="mb-0" style="font-weight: 500; font-size: 13px; color: var(--text-primary);">Drag & drop or click to swap image</p>
                                        <span style="font-size: 12px; color: var(--text-secondary);">JPG, JPEG, PNG, WEBP &mdash; Max 2 MB</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="remove_image" id="edit-remove-image" value="0">
                        <span class="field-error text-danger small" id="e-image-error"></span>
                    </div>

                    {{-- GENERAL INFORMATION --}}
                    <div class="section-divider">General Information</div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="e-name" class="form-control" maxlength="255" autocomplete="off">
                            <span class="field-error text-danger small" id="e-name-error"></span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Item Code (SKU)</label>
                            <input type="text" name="item_code" id="e-item_code" class="form-control" maxlength="100" autocomplete="off">
                            <span class="field-error text-danger small" id="e-item_code-error"></span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Item Class</label>
                            <select name="item_class" id="e-item_class" class="form-control">
                                <option value="general">General</option>
                                <option value="sale_only">Sale Only</option>
                                <option value="raw_material">Raw Material</option>
                            </select>
                            <span class="field-error text-danger small" id="e-item_class-error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="e-category_id" class="form-control">
                                <option value="" disabled>Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <span class="field-error text-danger small" id="e-category_id-error"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Manufacturer</label>
                            <select name="manufacturer_id" id="e-manufacturer_id" class="form-control">
                                <option value="">Select Manufacturer (Optional)</option>
                                @foreach($manufacturers as $manufacturer)
                                    <option value="{{ $manufacturer->id }}">{{ $manufacturer->name }}</option>
                                @endforeach
                            </select>
                            <span class="field-error text-danger small" id="e-manufacturer_id-error"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>HSN Code</label>
                            <input type="text" name="hsn_code" id="e-hsn_code" class="form-control" maxlength="50" autocomplete="off">
                            <span class="field-error text-danger small" id="e-hsn_code-error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label>Regional Name</label>
                            <input type="text" name="regional_name" id="e-regional_name" class="form-control" maxlength="255" autocomplete="off">
                            <span class="field-error text-danger small" id="e-regional_name-error"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Unit</label>
                            <input type="text" name="unit" id="e-unit" class="form-control" maxlength="50" autocomplete="off">
                            <span class="field-error text-danger small" id="e-unit-error"></span>
                        </div>
                    </div>

                    {{-- PURCHASE INFORMATION --}}
                    <div class="section-divider">Purchase Information</div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Purchase Price</label>
                            <input type="number" name="purchase_price" id="e-purchase_price" class="form-control" min="0" step="0.01">
                            <span class="field-error text-danger small" id="e-purchase_price-error"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Purchase Tax %</label>
                            <input type="number" name="purchase_tax_percent" id="e-purchase_tax_percent" class="form-control" min="0" max="100" step="0.01">
                            <span class="field-error text-danger small" id="e-purchase_tax_percent-error"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Tax Inclusive</label>
                            <select name="purchase_tax_inclusive" id="e-purchase_tax_inclusive" class="form-control">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                            <span class="field-error text-danger small" id="e-purchase_tax_inclusive-error"></span>
                        </div>
                    </div>

                    {{-- SALE INFORMATION --}}
                    <div class="section-divider">Sale Information</div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Sale Price <span class="text-danger">*</span></label>
                            <input type="number" name="sale_price" id="e-sale_price" class="form-control" min="0" step="0.01">
                            <span class="field-error text-danger small" id="e-sale_price-error"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>GST / VAT %</label>
                            <input type="number" name="gst_vat_percent" id="e-gst_vat_percent" class="form-control" min="0" max="100" step="0.01">
                            <span class="field-error text-danger small" id="e-gst_vat_percent-error"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Tax Inclusive</label>
                            <select name="sale_tax_inclusive" id="e-sale_tax_inclusive" class="form-control">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                            <span class="field-error text-danger small" id="e-sale_tax_inclusive-error"></span>
                        </div>
                    </div>

                    {{-- ADDITIONAL SETTINGS --}}
                    <div class="section-divider">Additional Settings</div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label>Discount %</label>
                            <input type="number" name="discount_percent" id="e-discount_percent" class="form-control" min="0" max="100" step="0.01">
                            <span class="field-error text-danger small" id="e-discount_percent-error"></span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Cess %</label>
                            <input type="number" name="cess_percent" id="e-cess_percent" class="form-control" min="0" max="100" step="0.01">
                            <span class="field-error text-danger small" id="e-cess_percent-error"></span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Additional Cess</label>
                            <input type="number" name="additional_cess" id="e-additional_cess" class="form-control" min="0" step="0.01">
                            <span class="field-error text-danger small" id="e-additional_cess-error"></span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Weighing Item</label>
                            <select name="is_weighing_item" id="e-is_weighing_item" class="form-control">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                            <span class="field-error text-danger small" id="e-is_weighing_item-error"></span>
                        </div>
                    </div>

                    {{-- INVENTORY & STATUS --}}
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="section-divider">Inventory</div>
                            <div class="row">
                                <div class="col-6">
                                    <label>Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="qty" id="e-qty" class="form-control" min="0" step="1">
                                    <span class="field-error text-danger small" id="e-qty-error"></span>
                                </div>
                                <div class="col-6">
                                    <label>MOQ <span class="text-danger">*</span></label>
                                    <input type="number" name="moq" id="e-moq" class="form-control" min="1" step="1">
                                    <span class="field-error text-danger small" id="e-moq-error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 mb-3">
                            <div class="section-divider">Status</div>
                            <label>Product Status</label>
                            <select name="status" id="e-status" class="form-control">
                                <option value="1">Enable</option>
                                <option value="0">Disable</option>
                            </select>
                            <span class="field-error text-danger small" id="e-status-error"></span>
                        </div>
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="section-divider">Description</div>
                    <div class="mb-3">
                        <label>Description Details</label>
                        <textarea name="description" id="e-description" class="form-control" rows="3"></textarea>
                        <span class="field-error text-danger small" id="e-description-error"></span>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="updateProductBtn" class="btn btn-primary">
                        <span id="updateProductBtnText">Update Product</span>
                        <span id="updateProductBtnSpinner" class="spinner-border spinner-border-sm ms-1" role="status" style="display: none;"></span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
