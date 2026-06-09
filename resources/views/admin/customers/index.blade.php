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
                    <button class="btn btn-primary d-flex align-items-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#customerCreateModal"
                        style="height: 40px; border-radius: var(--radius-lg); font-weight: 600; font-size: 14px; padding: 0 20px;">
                        <i class="bi bi-plus-lg"></i> Add Customer
                    </button>
                </div>
            </div>

            {{-- ===================== FILTER BAR ===================== --}}
            <div id="customer-filter-bar" class="mb-3">
                <div class="row g-2 align-items-end">

                    {{-- Name Search --}}
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="customer-filter-label" for="customer-filter-search">Search</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text"
                                   id="customer-filter-search"
                                   class="form-control"
                                   placeholder="Customer name…"
                                   autocomplete="off">
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="customer-filter-label" for="customer-filter-status">Status</label>
                        <select id="customer-filter-status" class="form-select">
                            <option value="">All</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    {{-- Date Range --}}
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="customer-filter-label" for="customer-filter-date-range">Date Range</label>
                        <input type="text"
                               id="customer-filter-date-range"
                               class="form-control"
                               placeholder="Select date range"
                               autocomplete="off">
                    </div>

                    {{-- Reset Button --}}
                    <div class="col-12 col-sm-auto col-lg-1">
                        <button type="button"
                                id="customer-filter-reset"
                                class="btn btn-outline-secondary w-100"
                                title="Clear all filters">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </button>
                    </div>

                </div>
            </div>
            {{-- ==================== END FILTER BAR ==================== --}}

            <div class="customer-table-wrapper">
                <table class="table table-customer" id="customers-table">
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
                        <tr id="row-{{ $customer->id }}"
                            data-name="{{ strtolower($customer->name) }}"
                            data-status="{{ $customer->status ? '1' : '0' }}"
                            data-created="{{ $customer->created_at ? $customer->created_at->format('Y-m-d') : '' }}">

                            <td>{{ $customer->id }}</td>
                            <td id="name-{{ $customer->id }}">{{ $customer->name }}</td>
                            <td id="email-{{ $customer->id }}">{{ $customer->email }}</td>
                            <td id="phone-{{ $customer->id }}">{{ $customer->phone ?? '-' }}</td>
                            <td class="text-center" id="type-{{ $customer->id }}">
                                <span class="badge bg-{{ $customer->customer_type === 'company' ? 'info' : 'secondary' }}">
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
                                        data-gender="{{ $customer->gender ?? '' }}"
                                        data-birthdate="{{ $customer->birthdate ? $customer->birthdate->format('Y-m-d') : '' }}"
                                        data-address="{{ $customer->address ?? '' }}"
                                        data-shipping-address="{{ $customer->shipping_address ?? '' }}"
                                        data-city="{{ $customer->city ?? '' }}"
                                        data-pin-code="{{ $customer->pin_code ?? '' }}"
                                        data-state="{{ $customer->state ?? '' }}"
                                        data-country="{{ $customer->country ?? '' }}"
                                        data-landmark="{{ $customer->landmark ?? '' }}"
                                        data-area-id="{{ $customer->area_id ?? '' }}"
                                        data-area-name="{{ $customer->area ? $customer->area->name : '' }}"
                                        data-credit-days="{{ $customer->credit_days ?? '' }}"
                                        data-credit-limit="{{ $customer->credit_limit ?? '' }}"
                                        data-vat-registered="{{ $customer->vat_registered ? '1' : '0' }}"
                                        data-vat-number="{{ $customer->vat_number ?? '' }}"
                                        data-status="{{ $customer->status ? '1' : '0' }}"
                                        title="View">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    {{-- Edit Button --}}
                                    <button class="btn btn-customer-action btn-customer-edit"
                                        data-id="{{ $customer->id }}"
                                        data-name="{{ $customer->name }}"
                                        data-email="{{ $customer->email }}"
                                        data-phone="{{ $customer->phone ?? '' }}"
                                        data-type="{{ $customer->customer_type }}"
                                        data-gender="{{ $customer->gender ?? '' }}"
                                        data-birthdate="{{ $customer->birthdate ? $customer->birthdate->format('Y-m-d') : '' }}"
                                        data-address="{{ $customer->address ?? '' }}"
                                        data-shipping-address="{{ $customer->shipping_address ?? '' }}"
                                        data-city="{{ $customer->city ?? '' }}"
                                        data-pin-code="{{ $customer->pin_code ?? '' }}"
                                        data-state="{{ $customer->state ?? '' }}"
                                        data-country="{{ $customer->country ?? '' }}"
                                        data-landmark="{{ $customer->landmark ?? '' }}"
                                        data-area-id="{{ $customer->area_id ?? '' }}"
                                        data-area-name="{{ $customer->area ? $customer->area->name : '' }}"
                                        data-credit-days="{{ $customer->credit_days ?? '' }}"
                                        data-credit-limit="{{ $customer->credit_limit ?? '' }}"
                                        data-vat-registered="{{ $customer->vat_registered ? '1' : '0' }}"
                                        data-vat-number="{{ $customer->vat_number ?? '' }}"
                                        data-status="{{ $customer->status ? '1' : '0' }}"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    {{-- Toggle Status Button --}}
                                    <button class="btn btn-customer-action btn-customer-toggle"
                                        data-id="{{ $customer->id }}"
                                        data-name="{{ $customer->name }}"
                                        data-status="{{ $customer->status ? '1' : '0' }}"
                                        title="Toggle Status">
                                        <i class="bi bi-slash-circle"></i>
                                    </button>

                                    {{-- Delete Button --}}
                                    <button class="btn btn-customer-action btn-customer-delete"
                                        data-id="{{ $customer->id }}"
                                        data-name="{{ $customer->name }}"
                                        title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="mt-3 px-3 pb-3">
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
