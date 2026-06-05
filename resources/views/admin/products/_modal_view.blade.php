<div class="modal fade" id="productViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body py-3">

                {{-- Product identity banner --}}
                <div class="d-flex align-items-start gap-3 mb-4 p-3 rounded" style="background-color: #f8fafc; border: 1px solid var(--border-color);">
                    <div id="v-image-wrapper" style="flex-shrink: 0;"></div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge bg-primary bg-opacity-10 text-primary" id="v-item_class" style="font-size: 11px; padding: 3px 10px; border-radius: 20px;"></span>
                            <span style="font-family: monospace; font-size: 12px; color: var(--text-secondary);" id="v-item_code"></span>
                        </div>
                        <h4 class="mb-1" id="v-name" style="font-weight: 700; color: var(--text-primary);"></h4>
                        <p class="mb-0 small" id="v-regional_name" style="color: var(--text-secondary); font-style: italic;"></p>
                    </div>
                </div>

                {{-- Category & Manufacturer --}}
                <div class="row mb-2">
                    <div class="col-md-6 mb-3">
                        <strong>Category</strong>
                        <p id="v-category" class="mb-0"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Manufacturer</strong>
                        <p id="v-manufacturer" class="mb-0"></p>
                    </div>
                </div>

                {{-- HSN & Unit --}}
                <div class="row mb-2">
                    <div class="col-md-6 mb-3">
                        <strong>HSN Code</strong>
                        <p id="v-hsn_code" class="mb-0 font-monospace"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Unit</strong>
                        <p id="v-unit" class="mb-0"></p>
                    </div>
                </div>

                {{-- Pricing --}}
                <div class="section-divider">Pricing Information</div>
                <div class="row mb-2">
                    <div class="col-md-6 mb-3" style="border-right: 1px solid var(--border-color);">
                        <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: #10b981; display: block; margin-bottom: 8px;">Purchase Framework</span>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <span class="text-muted" style="font-size: 12px;">Purchase Price</span>
                                <p id="v-purchase_price" class="fw-bold mb-0 text-dark"></p>
                            </div>
                            <div class="col-6 mb-2">
                                <span class="text-muted" style="font-size: 12px;">Tax %</span>
                                <p id="v-purchase_tax_percent" class="mb-0"></p>
                            </div>
                            <div class="col-12">
                                <span class="text-muted" style="font-size: 12px;">Tax Config</span>
                                <p id="v-purchase_tax_inclusive" class="mb-0"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3 ps-md-4">
                        <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: #ef4444; display: block; margin-bottom: 8px;">Sale Framework</span>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <span class="text-muted" style="font-size: 12px;">Sale Price</span>
                                <p id="v-sale_price" class="fw-bold mb-0 text-dark" style="font-size: 16px;"></p>
                            </div>
                            <div class="col-6 mb-2">
                                <span class="text-muted" style="font-size: 12px;">GST / VAT %</span>
                                <p id="v-gst_vat_percent" class="mb-0"></p>
                            </div>
                            <div class="col-12">
                                <span class="text-muted" style="font-size: 12px;">Tax Config</span>
                                <p id="v-sale_tax_inclusive" class="mb-0"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Additional Settings --}}
                <div class="section-divider">Additional Settings</div>
                <div class="row mb-2">
                    <div class="col-md-3 mb-2">
                        <span class="text-muted" style="font-size: 12px;">Discount Rate</span>
                        <p id="v-discount_percent" class="mb-0"></p>
                    </div>
                    <div class="col-md-3 mb-2">
                        <span class="text-muted" style="font-size: 12px;">Cess Surcharge</span>
                        <p id="v-cess_percent" class="mb-0"></p>
                    </div>
                    <div class="col-md-3 mb-2">
                        <span class="text-muted" style="font-size: 12px;">Add. Cess Fixed</span>
                        <p id="v-additional_cess" class="mb-0"></p>
                    </div>
                    <div class="col-md-3 mb-2">
                        <span class="text-muted" style="font-size: 12px;">Weighing Item</span>
                        <p id="v-is_weighing_item" class="mb-0"></p>
                    </div>
                </div>

                {{-- Inventory & Status --}}
                <div class="section-divider">Inventory &amp; Status</div>
                <div class="row mb-2">
                    <div class="col-md-4 mb-2">
                        <strong>Current Stock</strong>
                        <p id="v-qty" class="mb-0 text-primary fw-bold" style="font-size: 16px;"></p>
                    </div>
                    <div class="col-md-4 mb-2">
                        <strong>Min. Order Qty (MOQ)</strong>
                        <p id="v-moq" class="mb-0"></p>
                    </div>
                    <div class="col-md-4 mb-2">
                        <strong>Status</strong>
                        <p class="mb-0"><span id="v-status"></span></p>
                    </div>
                </div>

                {{-- Description --}}
                <div class="section-divider">Description</div>
                <div class="mb-2">
                    <p id="v-desc" class="mb-0 p-2 rounded" style="background-color: #f8fafc; font-size: 13px; line-height: 1.6; min-height: 40px; color: var(--text-secondary);"></p>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
