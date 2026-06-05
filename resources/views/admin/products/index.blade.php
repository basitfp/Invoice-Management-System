@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="product-card">

            <div class="product-header">
                <div>
                    <h4 class="product-title">Product Management</h4>
                    <p class="product-subtitle">Manage all products from here.</p>
                </div>

                <div>
                    <button class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#productCreateModal"
                        style="border-radius: 10px; font-weight: 600; font-size: 14px;">
                        <i class="bi bi-plus-lg"></i> Add Product
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-product" id="productsTable">
                    <thead>
                        <tr>
                            <th style="width: 60px;">Image</th>
                            <th>Item Code</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Manufacturer</th>
                            <th class="text-end">Sale Price</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Status</th>
                            <th class="text-end" style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr data-id="{{ $product->id }}">
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
                            <td id="name-{{ $product->id }}" class="fw-semibold">{{ $product->name }}</td>
                            <td id="category-{{ $product->id }}">{{ $product->category->name ?? 'N/A' }}</td>
                            <td id="manufacturer-{{ $product->id }}">{{ $product->manufacturer->name ?? 'N/A' }}</td>
                            <td id="selling-{{ $product->id }}" class="text-end fw-bold" style="color: var(--text-primary);">
                                ${{ number_format($product->sale_price, 2) }}
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
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">

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
                                        data-selling="{{ $product->sale_price }}"
                                        data-gst-vat-percent="{{ $product->gst_vat_percent ?? 0 }}"
                                        data-sale-tax-inclusive="{{ $product->sale_tax_inclusive ? '1' : '0' }}"
                                        data-discount-percentage="{{ $product->discount_percent ?? 0 }}"
                                        data-cess-percentage="{{ $product->cess_percent ?? 0 }}"
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
                                        data-selling="{{ $product->sale_price }}"
                                        data-gst-vat-percent="{{ $product->gst_vat_percent ?? 0 }}"
                                        data-sale-tax-inclusive="{{ $product->sale_tax_inclusive ? '1' : '0' }}"
                                        data-discount-percentage="{{ $product->discount_percent ?? 0 }}"
                                        data-cess-percentage="{{ $product->cess_percent ?? 0 }}"
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

        </div>
    </div>
</div>

@include('admin.products._modal_create')
@include('admin.products._modal_edit')
@include('admin.products._modal_view')
@include('admin.products._modal_confirm')

@endsection

@push('styles')
    <link href="{{ asset('assets/css/product.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/dropzone@6/dist/dropzone-min.js"></script>
    <script src="{{ asset('assets/js/product.js') }}"></script>
@endpush
