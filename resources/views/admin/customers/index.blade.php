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
                            <th style="width: 50px;">#</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">VAT Registered</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr id="row-{{ $customer->id }}">
                            <td>{{ $customer->id }}</td>
                            <td id="name-{{ $customer->id }}">{{ $customer->name }}</td>
                            <td id="email-{{ $customer->id }}">{{ $customer->email }}</td>
                            <td id="phone-{{ $customer->id }}">{{ $customer->phone ?? '-' }}</td>
                            <td class="text-center" id="type-{{ $customer->id }}">
                                <span class="badge bg-{{ $customer->customer_type === 'business' ? 'info' : 'secondary' }}">
                                    {{ ucfirst($customer->customer_type) }}
                                </span>
                            </td>
                            <td class="text-center" id="vat-{{ $customer->id }}">
                                @if($customer->vat_registered)
                                    <span class="badge bg-success">Yes</span>
                                    @if($customer->vat_number)
                                        <br><small class="text-muted" style="font-size: 11px; letter-spacing: 0.02em;">{{ $customer->vat_number }}</small>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td class="text-center" id="status-container-{{ $customer->id }}">
                                @if($customer->status)
                                    <span class="badge-status-enabled">Active</span>
                                @else
                                    <span class="badge-status-disabled">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-end">

                                    {{-- View Button --}}
                                    <button class="btn btn-customer-action btn-customer-view"
                                        data-id="{{ $customer->id }}"
                                        data-name="{{ $customer->name }}"
                                        data-email="{{ $customer->email }}"
                                        data-phone="{{ $customer->phone ?? '' }}"
                                        data-type="{{ $customer->customer_type }}"
                                        data-address="{{ $customer->address ?? '' }}"
                                        data-vat-registered="{{ $customer->vat_registered }}"
                                        data-vat-number="{{ $customer->vat_number ?? '' }}"
                                        data-status="{{ $customer->status }}">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    {{-- Edit Button --}}
                                    <button class="btn btn-customer-action btn-customer-edit"
                                        data-id="{{ $customer->id }}"
                                        data-name="{{ $customer->name }}"
                                        data-email="{{ $customer->email }}"
                                        data-phone="{{ $customer->phone ?? '' }}"
                                        data-type="{{ $customer->customer_type }}"
                                        data-address="{{ $customer->address ?? '' }}"
                                        data-vat-registered="{{ $customer->vat_registered }}"
                                        data-vat-number="{{ $customer->vat_number ?? '' }}"
                                        data-status="{{ $customer->status }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    {{-- Toggle Status Button --}}
                                    <button class="btn btn-customer-action btn-customer-toggle"
                                        data-id="{{ $customer->id }}"
                                        data-name="{{ $customer->name }}"
                                        data-status="{{ $customer->status }}">
                                        <i class="bi bi-slash-circle"></i>
                                    </button>

                                    {{-- Delete Button --}}
                                    <button class="btn btn-customer-action btn-customer-delete"
                                        data-id="{{ $customer->id }}"
                                        data-name="{{ $customer->name }}">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $customers->links() }}
                </div>

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