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
                <table class="table table-hover table-product">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Selling Price</th>
                            <th class="text-center">VAT</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr id="row-{{ $product->id }}">
                            <td>{{ $product->id }}</td>
                            <td id="name-{{ $product->id }}">{{ $product->name }}</td>
                            <td id="category-{{ $product->id }}">{{ $product->category->name ?? '-' }}</td>
                            <td class="text-center" id="qty-{{ $product->id }}">{{ $product->qty }}</td>
                            <td class="text-end" id="selling-{{ $product->id }}">£{{ number_format($product->selling_price, 2) }}</td>
                            <td class="text-center" id="vat-{{ $product->id }}">{{ $product->vat }}%</td>
                            <td class="text-center" id="status-container-{{ $product->id }}">
                                @if($product->status)
                                    <span class="badge-status-enabled">Enabled</span>
                                @else
                                    <span class="badge-status-disabled">Disabled</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-end">

                                    {{-- View Button --}}
                                    <button class="btn btn-product-action btn-product-view"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-desc="{{ $product->description ?? '' }}"
                                        data-category="{{ $product->category->name ?? '-' }}"
                                        data-qty="{{ $product->qty }}"
                                        data-purchase="{{ $product->purchase_price }}"
                                        data-selling="{{ $product->selling_price }}"
                                        data-vat="{{ $product->vat }}"
                                        data-moq="{{ $product->moq }}"
                                        data-status="{{ $product->status }}">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    {{-- Edit Button --}}
                                    <button class="btn btn-product-action btn-product-edit"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-desc="{{ $product->description ?? '' }}"
                                        data-category="{{ $product->category_id }}"
                                        data-qty="{{ $product->qty }}"
                                        data-purchase="{{ $product->purchase_price }}"
                                        data-selling="{{ $product->selling_price }}"
                                        data-vat="{{ $product->vat }}"
                                        data-moq="{{ $product->moq }}"
                                        data-status="{{ $product->status }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    {{-- Toggle Status Button --}}
                                    <button class="btn btn-product-action btn-product-toggle"
                                        data-id="{{ $product->id }}"
                                        data-status="{{ $product->status }}"
                                        data-name="{{ $product->name }}">
                                        <i class="bi bi-slash-circle"></i>
                                    </button>

                                    {{-- Delete Button --}}
                                    <button class="btn btn-product-action btn-product-delete"
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
    <script src="{{ asset('assets/js/product.js') }}"></script>
@endpush