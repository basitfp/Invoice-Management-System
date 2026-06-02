@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="product-card">  <!-- Changed from category-card -->
            
            <div class="product-header">  <!-- Changed from category-header -->
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
                <table class="table table-hover table-product">  <!-- Changed from table-category -->
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Qty</th>
                            <th>Selling Price</th>
                            <th>VAT</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td>{{ $product->qty }}</td>
                            <td>{{ $product->selling_price }}</td>
                            <td>{{ $product->vat }}%</td>
                            <td>
                                @if($product->status)
                                    <span class="badge-status-enabled">Enabled</span>
                                @else
                                    <span class="badge-status-disabled">Disabled</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-end">

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

                                    <button class="btn btn-product-action btn-product-edit"
                                        data-id="{{ $product->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <button class="btn btn-product-action btn-product-toggle"
                                        data-id="{{ $product->id }}"
                                        data-status="{{ $product->status }}">
                                        <i class="bi bi-slash-circle"></i>
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