@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="area-card"> {{-- Using area-card container class to inherit matching padding & styling --}}

            <div class="area-header">
                <div>
                    <h4 class="area-title">Product Management</h4>
                    <p class="area-subtitle">Manage all products from here.</p>
                </div>

                <div>
                    <button class="btn btn-primary d-flex align-items-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#productCreateModal">
                        <i class="bi bi-plus-lg"></i> Add Product
                    </button>
                </div>
            </div>

            {{-- ===================== FILTER BAR (Exact structural & selector copy) ===================== --}}
            <div id="area-filter-bar" class="mb-3">
                <div class="row g-2 align-items-end">
                    
                    {{-- Search Field --}}
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="area-filter-label" for="filter-search">Search</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" 
                                   id="filter-search" 
                                   class="form-control" 
                                   placeholder="Search products..."
                                   autocomplete="off">
                        </div>
                    </div>

                    {{-- Status Filter --}}
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="area-filter-label" for="filter-status">Status</label>
                        <select id="filter-status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    {{-- Date Range --}}
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="area-filter-label" for="filter-date-range">Date Range</label>
                        <input type="text"
                               id="filter-date-range"
                               class="form-control"
                               placeholder="Select date range"
                               autocomplete="off">
                    </div>

                    {{-- Reset Button --}}
                    <div class="col-12 col-sm-auto col-lg-1">
                        <button type="button" 
                                id="filter-reset" 
                                class="btn btn-outline-secondary w-100" 
                                title="Clear all filters">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
            {{-- ==================== END FILTER BAR ==================== --}}

            <div class="table-responsive">
                <table class="table table-hover table-product" id="productsTable">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Image</th>
                            <th>Item Code</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Manufacturer</th>
                            <th class="text-end">Sale Price</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center" style="width: 150px;">Status</th>
                            <th style="width: 280px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                       @foreach($products as $product)
                        <tr id="row-{{ $product->id }}"
                            data-id="{{ $product->id }}"
                            data-name="{{ strtolower(trim($product->name)) }}"
                            data-status="{{ $product->status ? '1' : '0' }}"
                            data-created="{{ $product->created_at ? $product->created_at->format('Y-m-d') : '' }}">
                            <td>
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}"
                                         alt="{{ $product->name }}"
                                         style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);">
                                @else
                                    <div class="d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px; background-color: #f1f5f9; border-radius: 8px; border: 1px solid var(--border-color); color: #94a3b8;">
                                        <i class="bi bi-image" style="font-size: 16px;"></i>
                                    </div>
                                @endif
                            </td>
                            <td id="item-code-{{ $product->id }}" style="font-family: monospace; color: var(--text-secondary);">
                                {{ $product->item_code ?? 'N/A' }}
                            </td>
                            <td id="name-{{ $product->id }}">
                                <span class="fw-semibold">{{ $product->name }}</span>
                            </td>
                            <td id="category-{{ $product->id }}">{{ $product->category->name ?? 'N/A' }}</td>
                            <td id="manufacturer-{{ $product->id }}">{{ $product->manufacturer->name ?? 'N/A' }}</td>
                            <td id="selling-{{ $product->id }}" class="text-end fw-bold" style="color: var(--text-primary);">
                                {{ number_format($product->selling_price, 2) }}
                            </td>
                            <td id="qty-{{ $product->id }}" class="text-center">
                                <span class="badge {{ $product->qty <= $product->moq ? 'bg-danger' : 'bg-success' }}"
                                      style="font-weight: 600; font-size: 12px; padding: 6px 12px; border-radius: 6px; opacity: 0.85;">
                                    {{ $product->qty }} {{ $product->unit ?? 'pcs' }}
                                </span>
                            </td>
                            <td class="text-center" id="status-container-{{ $product->id }}">
                                @if($product->status)
                                    <span class="badge-status-enabled">Enabled</span>
                                @else
                                    <span class="badge-status-disabled">Disabled</span>
                                @endif
                            </td>
                       
                            <td>
                                <div class="d-flex justify-content-end gap-2">

                                    {{-- View Button --}}
                                    <button class="btn btn-product-action btn-product-view"
                                        title="View"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-item-code="{{ $product->item_code ?? '' }}"
                                        data-item-class="{{ $product->item_class ?? 'general' }}"
                                        data-regional-name="{{ $product->regional_name ?? '' }}"
                                        data-category="{{ $product->category->name ?? '' }}"
                                        data-manufacturer="{{ $product->manufacturer->name ?? '' }}"
                                        data-hsn-code="{{ $product->hsn_code ?? '' }}"
                                        data-unit="{{ $product->unit ?? '' }}"
                                        data-purchase="{{ $product->purchase_price }}"
                                        data-purchase-tax-percent="{{ $product->purchase_tax_percent ?? 0 }}"
                                        data-purchase-tax-inclusive="{{ $product->purchase_tax_inclusive ? '1' : '0' }}"
                                        data-selling="{{ $product->selling_price }}"
                                        data-gst-vat-percent="{{ $product->vat ?? 0 }}"
                                        data-sale-tax-inclusive="{{ $product->sale_tax_inclusive ? '1' : '0' }}"
                                        data-discount-percentage="{{ $product->discount_percentage ?? 0 }}"
                                        data-cess-percentage="{{ $product->cess_percentage ?? 0 }}"
                                        data-additional-cess="{{ $product->additional_cess ?? 0 }}"
                                        data-is-weighing="{{ $product->is_weighing_item ? '1' : '0' }}"
                                        data-qty="{{ $product->qty }}"
                                        data-moq="{{ $product->moq }}"
                                        data-status="{{ $product->status ? '1' : '0' }}"
                                        data-desc="{{ $product->description ?? '' }}"
                                        data-image="{{ $product->image ? asset('storage/' . $product->image) : '' }}">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    {{-- Edit Button --}}
                                    <button class="btn btn-product-action btn-product-edit"
                                        title="Edit"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-item-code="{{ $product->item_code ?? '' }}"
                                        data-item-class="{{ $product->item_class ?? 'general' }}"
                                        data-regional-name="{{ $product->regional_name ?? '' }}"
                                        data-category="{{ $product->category_id ?? '' }}"
                                        data-category-name="{{ $product->category->name ?? '' }}"
                                        data-manufacturer-id="{{ $product->manufacturer_id ?? '' }}"
                                        data-hsn-code="{{ $product->hsn_code ?? '' }}"
                                        data-unit="{{ $product->unit ?? '' }}"
                                        data-purchase="{{ $product->purchase_price }}"
                                        data-purchase-tax-percent="{{ $product->purchase_tax_percent ?? 0 }}"
                                        data-purchase-tax-inclusive="{{ $product->purchase_tax_inclusive ? '1' : '0' }}"
                                        data-selling="{{ $product->selling_price }}"
                                        data-gst-vat-percent="{{ $product->vat ?? 0 }}"
                                        data-sale-tax-inclusive="{{ $product->sale_tax_inclusive ? '1' : '0' }}"
                                        data-discount-percentage="{{ $product->discount_percentage ?? 0 }}"
                                        data-cess-percentage="{{ $product->cess_percentage ?? 0 }}"
                                        data-additional-cess="{{ $product->additional_cess ?? 0 }}"
                                        data-is-weighing="{{ $product->is_weighing_item ? '1' : '0' }}"
                                        data-qty="{{ $product->qty }}"
                                        data-moq="{{ $product->moq }}"
                                        data-status="{{ $product->status ? '1' : '0' }}"
                                        data-desc="{{ $product->description ?? '' }}"
                                        data-image="{{ $product->image ?? '' }}"
                                        data-image-url="{{ $product->image ? asset('storage/' . $product->image) : '' }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    {{-- Toggle Status Button --}}
                                    <button class="btn btn-product-action btn-product-toggle"
                                        title="Toggle Status"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-status="{{ $product->status ? '1' : '0' }}">
                                        <i class="bi bi-slash-circle"></i>
                                    </button>

                                    {{-- Delete Button --}}
                                    <button class="btn btn-product-action btn-product-delete"
                                        title="Delete"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Unified Empty Results Fallback Element --}}
            <div id="filter-no-results" class="text-center text-muted py-5 d-none w-100" style="border-top: 1px dashed var(--border-color); margin-top: 15px;">
                <i class="bi bi-exclamation-circle d-block mb-2" style="font-size: 24px; color: var(--text-secondary);"></i>
                <span>No products match your filter criteria.</span>
            </div>

        </div>
    </div>
</div>

@include('admin.products._modal_create')
@include('admin.products._modal_edit')
@include('admin.products._modal_view')
@include('admin.products._modal_confirm')

@endsection

@push('styles')
    {{-- Pulling in area.css temporarily here fixes the spacing issues immediately until your final CSS step --}}
    <link href="{{ asset('assets/css/area.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/product.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/dropzone@6/dist/dropzone-min.js"></script>
    <script src="{{ asset('assets/js/product.js') }}"></script>
@endpush
