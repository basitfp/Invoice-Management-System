@extends('layouts.admin')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="area-card">
            <div class="area-header">
                <div>
                    <h4 class="area-title">Area Management</h4>
                    <p class="area-subtitle">Manage all active and operational delivery areas from here.</p>
                </div>
                <div>
                    <button class="btn btn-primary d-flex align-items-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#areaCreateModal">
                        <i class="bi bi-plus-lg"></i> Add Area
                    </button>
                </div>
            </div>

            {{-- ===================== FILTER BAR ===================== --}}
            <div id="area-filter-bar" class="mb-3">
                <div class="row g-2 align-items-end">

                    {{-- Name Search --}}
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="area-filter-label" for="area-filter-search">Search</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text"
                                   id="area-filter-search"
                                   class="form-control"
                                   placeholder="Area name…"
                                   autocomplete="off">
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="area-filter-label" for="area-filter-status">Status</label>
                        <select id="area-filter-status" class="form-select">
                            <option value="">All</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    {{-- Date Range --}}
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="area-filter-label" for="area-filter-date-range">Date Range</label>
                        <input
                            type="text"
                            id="area-filter-date-range"
                            class="form-control"
                            placeholder="Select date range"
                            autocomplete="off">
                    </div>

                    {{-- Reset Button --}}
                    <div class="col-12 col-sm-auto col-lg-1">
                        <button type="button"
                                id="area-filter-reset"
                                class="btn btn-outline-secondary w-100"
                                title="Clear all filters">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </button>
                    </div>

                </div>
            </div>
            {{-- ==================== END FILTER BAR ==================== --}}

            <div class="area-table-wrapper">
                <table class="table table-hover table-area" id="areas-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">#</th>
                            <th>Area Name</th>
                            <th class="text-center" style="width: 150px;">Status</th>
                            <th class="text-center" style="width: 200px;">Created Date</th>
                            <th style="width: 280px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($areas as $index => $area)
                        <tr id="row-{{ $area->id }}"
                            data-name="{{ strtolower($area->name) }}"
                            data-status="{{ $area->status }}"
                            data-created="{{ $area->created_at ? $area->created_at->format('Y-m-d') : '' }}">

                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span id="name-{{ $area->id }}" class="fw-semibold">
                                    {{ $area->name }}
                                </span>
                            </td>
                            <td class="text-center" id="status-container-{{ $area->id }}">
                                @if($area->status == 1 || $area->status === true)
                                    <span class="badge-status-enabled">Enabled</span>
                                @else
                                    <span class="badge-status-disabled">Disabled</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span id="created-at-{{ $area->id }}">
                                    {{ $area->created_at ? $area->created_at->format('Y-m-d h:i A') : '-' }}
                                </span>
                                <span id="updated-at-{{ $area->id }}" class="d-none">
                                    {{ $area->updated_at ? $area->updated_at->format('Y-m-d h:i A') : '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-end">

                                    {{-- View --}}
                                    <button class="btn btn-area-action btn-area-view"
                                        data-id="{{ $area->id }}"
                                        data-name="{{ $area->name }}"
                                        data-status="{{ $area->status }}"
                                        data-created-at="{{ $area->created_at ? $area->created_at->format('Y-m-d h:i A') : '-' }}"
                                        data-updated-at="{{ $area->updated_at ? $area->updated_at->format('Y-m-d h:i A') : '-' }}"
                                        title="View">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    {{-- Edit --}}
                                    <button class="btn btn-area-action btn-area-edit"
                                        data-id="{{ $area->id }}"
                                        data-name="{{ $area->name }}"
                                        data-action="{{ route('admin.areas.update', $area) }}"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    {{-- Toggle Status --}}
                                    <button class="btn btn-area-action btn-area-toggle"
                                        data-id="{{ $area->id }}"
                                        data-name="{{ $area->name }}"
                                        data-action="{{ route('admin.areas.toggle-status', $area) }}"
                                        data-status="{{ $area->status }}"
                                        title="Toggle Status">
                                        <i class="bi bi-slash-circle"></i>
                                    </button>

                                    {{-- Delete --}}
                                    <button class="btn btn-area-action btn-area-delete"
                                        data-id="{{ $area->id }}"
                                        data-name="{{ $area->name }}"
                                        data-action="{{ route('admin.areas.destroy', $area) }}"
                                        title="Delete">
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

@include('admin.areas._modal_create')
@include('admin.areas._modal_edit')
@include('admin.areas._modal_view')
@include('admin.areas._modal_confirm')

@endsection

@push('styles')
    <link href="{{ asset('assets/css/area.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/area.js') }}"></script>
@endpush
