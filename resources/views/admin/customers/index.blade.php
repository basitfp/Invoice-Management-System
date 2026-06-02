@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="customer-card">
            
            <div class="customer-header">
                <div>
                    <h4 class="customer-title">Customer Management</h4>
                    <p class="customer-subtitle">Manage all customers from here.</p>
                </div>

                <div>
                    <button class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#customerCreateModal"
                        style="border-radius: 10px; font-weight: 600; font-size: 14px;">
                        <i class="bi bi-plus-lg"></i> Add Customer
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-customer">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Type</th>
                            <th>VAT Registered</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)

                        <!-- dd($customer); -->
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $customer->customer_type === 'business' ? 'info' : 'secondary' }}">
                                    {{ ucfirst($customer->customer_type) }}
                                </span>
                            </td>
                         <td>
                            @if($customer->vat_registered)
                                <span class="badge bg-success">Yes</span>
                                    @if($customer->vat_number)
                                        <small class="text-muted ms-1">({{ $customer->vat_number }})</small>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                @if($customer->status)
                                    <span class="badge-status-enabled">Active</span>
                                @else
                                    <span class="badge-status-disabled">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-end">

                                    <button class="btn btn-customer-action btn-customer-view"
                                        data-id="{{ $customer->id }}"
                                        data-name="{{ $customer->name }}"
                                        data-email="{{ $customer->email }}"
                                        data-phone="{{ $customer->phone }}"
                                        data-type="{{ $customer->customer_type }}"
                                        data-address="{{ $customer->address }}"
                                        data-vat-registered="{{ $customer->vat_registered }}"
                                        data-vat-number="{{ $customer->vat_number }}"
                                        data-status="{{ $customer->status }}">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <button class="btn btn-customer-action btn-customer-edit"
                                        data-id="{{ $customer->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <button class="btn btn-customer-action btn-customer-toggle"
                                        data-id="{{ $customer->id }}"
                                        data-status="{{ $customer->status }}">
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

@include('admin.customers._modal_create')
@include('admin.customers._modal_edit')
@include('admin.customers._modal_view')
@include('admin.customers._modal_confirm')
@endsection

@push('styles')
    <link href="{{ asset('assets/css/customer.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/customer.js') }}"></script>
@endpush