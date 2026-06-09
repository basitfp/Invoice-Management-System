@extends('layouts.admin')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="vendor-card">
            <div class="vendor-header">
                <div>
                    <h4 class="vendor-title">Vendor Management</h4>
                    <p class="vendor-subtitle">Manage all suppliers and vendors from here.</p>
                </div>
                <div>
                    <button class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2"
                        style="border-radius: 10px; font-weight: 600; font-size: 14px;"
                        data-bs-toggle="modal"
                        data-bs-target="#vendorCreateModal">
                        <i class="bi bi-plus-lg"></i> Add Vendor
                    </button>
                </div>
            </div>

            {{-- ===================== FILTER BAR ===================== --}}
            <div class="mb-3" id="vendor-filter-bar">
                <div class="row g-2 align-items-end">

                    {{-- Name Search --}}
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label vendor-filter-label" for="filter-search">Search</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text"
                                   id="filter-search"
                                   class="form-control"
                                   placeholder="Vendor name…"
                                   autocomplete="off">
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label vendor-filter-label" for="filter-status">Status</label>
                        <select id="filter-status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    {{-- Date Range --}}
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label vendor-filter-label" for="filter-date-range">Date Range</label>
                        <input type="text"
                               id="filter-date-range"
                               class="form-control form-control-sm"
                               placeholder="Select date range"
                               autocomplete="off">
                    </div>

                    {{-- Reset Button --}}
                    <div class="col-12 col-sm-auto col-lg-1">
                        <button type="button"
                                id="vendor-filter-reset"
                                class="btn btn-outline-secondary btn-sm w-100"
                                title="Clear all filters">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </button>
                    </div>

                </div>
                {{-- Empty state is injected inside <tbody> by vendor.js --}}
            </div>
            {{-- ==================== END FILTER BAR ==================== --}}

            <div class="vendor-table-wrapper">
                <table class="table table-hover table-vendor" id="vendors-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>City</th>
                            <th class="text-center" style="width: 130px;">Status</th>
                            <th class="text-center" style="width: 160px;">Created Date</th>
                            <th style="width: 200px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
                        <tr id="row-{{ $vendor->id }}"
                            data-name="{{ strtolower($vendor->name) }}"
                            data-status="{{ $vendor->status }}"
                            data-created="{{ $vendor->created_at ? $vendor->created_at->format('Y-m-d') : '' }}">

                            <td>{{ $vendor->id }}</td>
                            <td id="name-{{ $vendor->id }}">{{ $vendor->name }}</td>
                            <td id="company-{{ $vendor->id }}">{{ $vendor->company ?: '-' }}</td>
                            <td id="phone-{{ $vendor->id }}">{{ $vendor->phone ?: '-' }}</td>
                            <td id="email-{{ $vendor->id }}">{{ $vendor->email ?: '-' }}</td>
                            <td id="city-{{ $vendor->id }}">{{ $vendor->city ?: '-' }}</td>
                            <td class="text-center" id="status-container-{{ $vendor->id }}">
                                @if($vendor->status)
                                    <span class="badge-status-enabled">Enabled</span>
                                @else
                                    <span class="badge-status-disabled">Disabled</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $vendor->created_at ? $vendor->created_at->format('Y-m-d') : '-' }}</td>
                            <td>
                                <div class="d-flex gap-2 justify-content-end">

                                    {{-- View Button --}}
                                    <button class="btn btn-vendor-action btn-vendor-view"
                                        data-id="{{ $vendor->id }}"
                                        data-name="{{ $vendor->name }}"
                                        data-company="{{ $vendor->company ?? '' }}"
                                        data-phone="{{ $vendor->phone ?? '' }}"
                                        data-email="{{ $vendor->email ?? '' }}"
                                        data-tax-reg-number="{{ $vendor->tax_reg_number ?? '' }}"
                                        data-address-line-1="{{ $vendor->address_line_1 ?? '' }}"
                                        data-address-line-2="{{ $vendor->address_line_2 ?? '' }}"
                                        data-city="{{ $vendor->city ?? '' }}"
                                        data-pin-code="{{ $vendor->pin_code ?? '' }}"
                                        data-state="{{ $vendor->state ?? '' }}"
                                        data-country="{{ $vendor->country ?? '' }}"
                                        data-status="{{ (int) $vendor->status }}"
                                        data-created-at="{{ $vendor->created_at ? $vendor->created_at->format('Y-m-d H:i:s') : '-' }}"
                                        data-updated-at="{{ $vendor->updated_at ? $vendor->updated_at->format('Y-m-d H:i:s') : '-' }}">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    {{-- Edit Button --}}
                                    <button class="btn btn-vendor-action btn-vendor-edit"
                                        data-id="{{ $vendor->id }}"
                                        data-name="{{ $vendor->name }}"
                                        data-company="{{ $vendor->company ?? '' }}"
                                        data-phone="{{ $vendor->phone ?? '' }}"
                                        data-email="{{ $vendor->email ?? '' }}"
                                        data-tax-reg-number="{{ $vendor->tax_reg_number ?? '' }}"
                                        data-address-line-1="{{ $vendor->address_line_1 ?? '' }}"
                                        data-address-line-2="{{ $vendor->address_line_2 ?? '' }}"
                                        data-city="{{ $vendor->city ?? '' }}"
                                        data-pin-code="{{ $vendor->pin_code ?? '' }}"
                                        data-state="{{ $vendor->state ?? '' }}"
                                        data-country="{{ $vendor->country ?? '' }}"
                                        data-status="{{ (int) $vendor->status }}"
                                        data-action="{{ route('admin.vendors.update', $vendor) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    {{-- Toggle Status Button --}}
                                    <button class="btn btn-vendor-action btn-vendor-toggle"
                                        data-id="{{ $vendor->id }}"
                                        data-name="{{ $vendor->name }}"
                                        data-status="{{ (int) $vendor->status }}"
                                        data-action="{{ route('admin.vendors.toggle-status', $vendor) }}">
                                        <i class="bi bi-slash-circle"></i>
                                    </button>

                                    {{-- Delete Button --}}
                                    <button class="btn btn-vendor-action btn-vendor-delete"
                                        data-id="{{ $vendor->id }}"
                                        data-name="{{ $vendor->name }}">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="no-vendors-row">
                            <td colspan="9" class="text-center py-5">
                                <div class="filter-empty-state">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                    <span class="text-muted">No vendors found.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

@include('admin.vendors._modal_create')
@include('admin.vendors._modal_edit')
@include('admin.vendors._modal_view')
@include('admin.vendors._modal_confirm')

@endsection

@push('styles')
    <link href="{{ asset('assets/css/vendor.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/vendor.js') }}"></script>
@endpush
