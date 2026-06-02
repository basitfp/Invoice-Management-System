@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="category-card">
            <div class="category-header">
                <div>
                    <h4 class="category-title">Category Management</h4>
                    <p class="category-subtitle">Manage all product categories from here.</p>
                </div>
                <div>
                    <button class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2"
                        style="border-radius: 10px; font-weight: 600; font-size: 14px;"
                        data-bs-toggle="modal"
                        data-bs-target="#categoryCreateModal">
                        <i class="bi bi-plus-lg"></i> Add Category
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-category" id="categories-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">#</th>
                            <th>Category Name</th>
                            <th style="width: 150px;">Status</th>
                            <th style="width: 200px;">Created Date</th>
                            <th style="width: 280px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr id="category-row-{{ $category->id }}">
                                <td>{{ $category->id }}</td>
                                <td class="category-name-cell">{{ $category->name }}</td>
                                <td class="category-status-cell">
                                    @if($category->status)
                                        <span class="badge-status-enabled">Enabled</span>
                                    @else
                                        <span class="badge-status-disabled">Disabled</span>
                                    @endif
                                </td>
                                <td>{{ $category->created_at ? $category->created_at->format('Y-m-d H:i:s') : '-' }}</td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-end">

                                        {{-- View Button --}}
                                        <button class="btn btn-category-action btn-category-view"
                                            data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            data-status="{{ $category->status }}"
                                            data-created-at="{{ $category->created_at ? $category->created_at->format('Y-m-d H:i:s') : '-' }}"
                                            data-updated-at="{{ $category->updated_at ? $category->updated_at->format('Y-m-d H:i:s') : '-' }}">
                                            <i class="bi bi-eye"></i> View
                                        </button>

                                        {{-- Edit Button --}}
                                        <button class="btn btn-category-action btn-category-edit"
                                            data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            data-action="{{ route('admin.categories.update', $category) }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>

                                        {{-- Toggle Status Button --}}
                                        <button class="btn btn-category-action btn-toggle-status"
                                            data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            data-action="{{ route('admin.categories.toggle-status', $category) }}"
                                            data-status="{{ $category->status }}">
                                            @if ($category->status == 1)
                                                <i class="bi bi-slash-circle"></i> Disable
                                            @else
                                                <i class="bi bi-check-circle"></i> Enable
                                            @endif
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

<!-- Create Modal -->
@include('admin.categories._modal_create')

<!-- Edit Modal -->
@include('admin.categories._modal_edit')

<!-- View Modal -->
@include('admin.categories._modal_view')

<!-- Status Confirm Modal -->
@include('admin.categories._modal_confirm')

@endsection

@push('styles')
    <link href="{{ asset('assets/css/category.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/category.js') }}"></script>
@endpush