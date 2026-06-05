@extends('layouts.admin')

@section('content')

<div class="row">
    <div class="col-12">
        {{-- Direct Category classes ko use kiya hai design inherit karne ke liye --}}
        <div class="category-card">
            <div class="category-header">
                <div>
                    <h4 class="category-title">Area Management</h4>
                    <p class="category-subtitle">Manage all active and operational delivery areas from here.</p>
                </div>
                <div>
                    <button class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2"
                        style="border-radius: 10px; font-weight: 600; font-size: 14px;"
                        data-bs-toggle="modal"
                        data-bs-target="#areaCreateModal">
                        <i class="bi bi-plus-lg"></i> Add Area
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-category" id="areas-table">
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
                        <tr id="row-{{ $area->id }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span id="name-{{ $area->id }}" class="fw-semibold text-dark">
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
                            <td class="text-center text-secondary small">
                                <span id="created-at-{{ $area->id }}">
                                    {{ $area->created_at ? $area->created_at->format('Y-m-d h:i A') : '-' }}
                                </span>
                                <span id="updated-at-{{ $area->id }}" class="d-none">
                                    {{ $area->updated_at ? $area->updated_at->format('Y-m-d h:i A') : '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-end">
                                    {{-- View Button --}}
                                    <button class="btn btn-category-action btn-category-view"
                                        data-id="{{ $area->id }}"
                                        data-name="{{ $area->name }}"
                                        data-status="{{ $area->status }}"
                                        data-created-at="{{ $area->created_at ? $area->created_at->format('Y-m-d h:i A') : '-' }}"
                                        data-updated-at="{{ $area->updated_at ? $area->updated_at->format('Y-m-d h:i A') : '-' }}">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    {{-- Edit Button --}}
                                    <button class="btn btn-category-action btn-category-edit"
                                        data-id="{{ $area->id }}"
                                        data-name="{{ $area->name }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    {{-- Toggle Status Button --}}
                                    <button class="btn btn-category-action btn-category-toggle"
                                        data-id="{{ $area->id }}"
                                        data-name="{{ $area->name }}"
                                        data-action="{{ route('admin.areas.toggle-status', $area) }}"
                                        data-status="{{ $area->status }}">
                                        <i class="bi bi-slash-circle"></i>
                                    </button>

                                    {{-- Delete Button --}}
                                    <button class="btn btn-category-action btn-category-delete"
                                        data-id="{{ $area->id }}"
                                        data-name="{{ $area->name }}"
                                        data-action="{{ route('admin.areas.destroy', $area) }}">
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

{{-- Alag Alag Dedicated Modals --}}
@include('admin.areas._modal_create')
@include('admin.areas._modal_edit')
@include('admin.areas._modal_view')
@include('admin.areas._modal_confirm')

@endsection

@push('styles')
    {{-- Yahan direct category.css ko hi inject kar diya --}}
    <link href="{{ asset('assets/css/category.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/area.js') }}"></script>
@endpush