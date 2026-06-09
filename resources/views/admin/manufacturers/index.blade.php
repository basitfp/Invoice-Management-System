@extends('layouts.admin')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="manufacturer-card">
            <div class="manufacturer-header">
                <div>
                    <h4 class="manufacturer-title">Manufacturer Management</h4>
                    <p class="manufacturer-subtitle">Manage all product manufacturers from here.</p>
                </div>
                <div>
                    <button class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2"
                        style="border-radius: 10px; font-weight: 600; font-size: 14px;"
                        data-bs-toggle="modal"
                        data-bs-target="#manufacturerCreateModal">
                        <i class="bi bi-plus-lg"></i> Add Manufacturer
                    </button>
                </div>
            </div>

            {{-- ===================== FILTER BAR ===================== --}}
            <div class="mb-3" id="manufacturer-filter-bar">
                <div class="row g-2 align-items-end">

                    {{-- Name Search --}}
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label manufacturer-filter-label" for="filter-search">Search</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text"
                                   id="filter-search"
                                   class="form-control"
                                   placeholder="Manufacturer name…"
                                   autocomplete="off">
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label manufacturer-filter-label" for="filter-status">Status</label>
                        <select id="filter-status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    {{-- Date Range --}}
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label manufacturer-filter-label" for="filter-date-range">Date Range</label>
                        <input type="text"
                               id="filter-date-range"
                               class="form-control form-control-sm">
                    </div>

                    {{-- Reset Button --}}
                    <div class="col-12 col-sm-auto col-lg-1">
                        <button type="button"
                                id="manufacturer-filter-reset"
                                class="btn btn-outline-secondary btn-sm w-100"
                                title="Clear all filters">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </button>
                    </div>

                </div>
                {{-- Empty state is injected inside <tbody> by manufacturer.js --}}
            </div>
            {{-- ==================== END FILTER BAR ==================== --}}

            <div class="manufacturer-table-wrapper">
                <table class="table table-hover table-manufacturer" id="manufacturers-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th class="text-center" style="width: 130px;">Status</th>
                            <th class="text-center" style="width: 160px;">Created Date</th>
                            <th style="width: 200px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($manufacturers as $manufacturer)
                        <tr id="row-{{ $manufacturer->id }}"
                            data-name="{{ strtolower($manufacturer->name) }}"
                            data-status="{{ $manufacturer->status }}"
                            data-created="{{ $manufacturer->created_at ? $manufacturer->created_at->format('Y-m-d') : '' }}">

                            <td>{{ $manufacturer->id }}</td>
                            <td id="name-{{ $manufacturer->id }}">{{ $manufacturer->name }}</td>
                            <td id="phone-{{ $manufacturer->id }}">{{ $manufacturer->phone ?: '-' }}</td>
                            <td id="email-{{ $manufacturer->id }}">{{ $manufacturer->email ?: '-' }}</td>
                            <td class="text-center" id="status-container-{{ $manufacturer->id }}">
                                @if($manufacturer->status)
                                    <span class="badge-status-enabled">Enabled</span>
                                @else
                                    <span class="badge-status-disabled">Disabled</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $manufacturer->created_at ? $manufacturer->created_at->format('Y-m-d') : '-' }}</td>
                            <td>
                                <div class="d-flex gap-2 justify-content-end">

                                    {{-- View Button --}}
                                    <button class="btn btn-manufacturer-action btn-manufacturer-view"
                                        data-id="{{ $manufacturer->id }}"
                                        data-name="{{ $manufacturer->name }}"
                                        data-phone="{{ $manufacturer->phone ?? '' }}"
                                        data-email="{{ $manufacturer->email ?? '' }}"
                                        data-address="{{ $manufacturer->address ?? '' }}"
                                        data-status="{{ (int) $manufacturer->status }}"
                                        data-created-at="{{ $manufacturer->created_at ? $manufacturer->created_at->format('Y-m-d H:i:s') : '-' }}"
                                        data-updated-at="{{ $manufacturer->updated_at ? $manufacturer->updated_at->format('Y-m-d H:i:s') : '-' }}">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    {{-- Edit Button --}}
                                    <button class="btn btn-manufacturer-action btn-manufacturer-edit"
                                        data-id="{{ $manufacturer->id }}"
                                        data-name="{{ $manufacturer->name }}"
                                        data-phone="{{ $manufacturer->phone ?? '' }}"
                                        data-email="{{ $manufacturer->email ?? '' }}"
                                        data-address="{{ $manufacturer->address ?? '' }}"
                                        data-status="{{ (int) $manufacturer->status }}"
                                        data-action="{{ route('admin.manufacturers.update', $manufacturer) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    {{-- Toggle Status Button --}}
                                    <button class="btn btn-manufacturer-action btn-manufacturer-toggle"
                                        data-id="{{ $manufacturer->id }}"
                                        data-name="{{ $manufacturer->name }}"
                                        data-status="{{ (int) $manufacturer->status }}"
                                        data-action="{{ route('admin.manufacturers.toggle-status', $manufacturer) }}">
                                        <i class="bi bi-slash-circle"></i>
                                    </button>

                                    {{-- Delete Button --}}
                                    <button class="btn btn-manufacturer-action btn-manufacturer-delete"
                                        data-id="{{ $manufacturer->id }}"
                                        data-name="{{ $manufacturer->name }}">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="no-manufacturers-row">
                            <td colspan="7" class="text-center py-5">
                                <div class="filter-empty-state">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                    <span class="text-muted">No manufacturers found.</span>
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

@include('admin.manufacturers._modal_create')
@include('admin.manufacturers._modal_edit')
@include('admin.manufacturers._modal_view')
@include('admin.manufacturers._modal_confirm')

@endsection

@push('styles')
    <link href="{{ asset('assets/css/manufacturer.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/manufacturer.js') }}"></script>
@endpush
