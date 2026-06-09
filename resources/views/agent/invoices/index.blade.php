@extends('layouts.agent')

@section('title', 'My Invoices')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="area-card">
            {{-- ===================== FILTER BAR ===================== --}}
            <div id="area-filter-bar" class="mb-3">
                <div class="row g-2 align-items-end">

                    {{-- Search Field --}}
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="area-filter-label" for="filter-search">Search</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" 
                                   id="filter-search" 
                                   class="form-control" 
                                   placeholder="Search invoices..."
                                   autocomplete="off">
                        </div>
                    </div>

                    {{-- Status Filter --}}
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="area-filter-label" for="filter-status">Status</label>
                        <select id="filter-status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="draft">Draft</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="paid">Paid</option>
                            <option value="due">Due</option> 
                        </select>
                    </div>

                    {{-- Date Range --}}
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="area-filter-label" for="filter-date-range">Date Range</label>
                        <input type="text"
                               id="filter-date-range"
                               class="form-control"
                               placeholder="Select date range"
                               autocomplete="off">
                    </div>

                    {{-- Reset Button --}}
                    <div class="col-12 col-sm-auto col-lg-1">
                        <button type="button" 
                                id="filter-reset" 
                                class="btn btn-outline-secondary w-100" 
                                title="Clear all filters">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </button>
                    </div>
                </div>
            </div>

            <div class="invoice-list-header">
                <div>
                    <h4 class="invoice-title">My Invoices</h4>
                    <p class="invoice-subtitle">View and manage your generated invoices.</p>
                </div>
                <div>
                    <a href="{{ route('agent.invoices.create') }}"
                        class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2"
                        style="border-radius: 10px; font-weight: 600; font-size: 14px;">
                        <i class="bi bi-plus-lg"></i> New Invoice
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-invoice" id="invoicesTable">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Invoice #</th>
                            <th>Customer</th>
                            <th class="text-center">Date</th>
                            <th class="text-end">Total (excl. VAT)</th>
                            <th class="text-end">Total VAT</th>
                            <th class="text-end">Grand Total</th>
                            <th class="text-center">Status</th>
                            <th class="text-end" style="width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                       @forelse($invoices as $invoice)
                            @php 
                                $cleanStatus = strtolower(trim($invoice->status)); 
                            @endphp
                            <tr id="row-{{ $invoice->id }}"
                                data-invoice-number="{{ strtolower(trim($invoice->invoice_number)) }}"
                                data-customer="{{ strtolower(trim($invoice->customer->name ?? 'walk-in customer')) }}"
                                data-status="{{ $cleanStatus }}"
                                data-date="{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : '' }}"
                                data-amount="{{ $invoice->total_amount + $invoice->total_vat }}">
                            <td class="fw-bold text-primary">{{ $invoice->invoice_number }}</td>
                            <td>
                                <div class="customer-name">{{ $invoice->customer->name }}</div>
                                <div class="customer-email text-muted small">{{ $invoice->customer->email }}</div>
                            </td>
                            <td class="text-center">{{ date('d M Y', strtotime($invoice->invoice_date)) }}</td>
                            <td class="text-end fw-semibold">{{ number_format($invoice->total_amount, 2) }}</td>
                            <td class="text-end text-muted">{{ number_format($invoice->total_vat, 2) }}</td>
                            <td class="text-end text-dark fw-bold">{{ number_format($invoice->total_amount + $invoice->total_vat, 2) }}</td>
                            {{-- FIX: class="status-container" is what invoice-list.js targets for live update --}}
                            <td class="text-center status-container">
                                @php
                                    $statusClasses = [
                                        'draft'     => 'invoice-status-draft',
                                        'unpaid'    => 'invoice-status-sent',
                                        'paid'      => 'invoice-status-paid',
                                        'due'       => 'invoice-status-cancelled',
                                    ];
                                    $cls = $statusClasses[$invoice->status] ?? 'invoice-status-draft';
                                @endphp
                                <span class="{{ $cls }}">{{ ucfirst($invoice->status) }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('agent.invoices.show', $invoice->id) }}"
                                       class="btn btn-invoice-action btn-invoice-view"
                                       title="View & Print">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <button class="btn btn-invoice-action btn-invoice-status"
                                        title="Change Status"
                                        data-id="{{ $invoice->id }}"
                                        data-status="{{ $invoice->status }}"
                                        data-number="{{ $invoice->invoice_number }}">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>

                                    <button class="btn btn-invoice-action btn-invoice-delete"
                                        title="Delete"
                                        data-id="{{ $invoice->id }}"
                                        data-number="{{ $invoice->invoice_number }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                No invoices found. <a href="{{ route('agent.invoices.create') }}">Create one</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Fallback Element for Client-Side JS Filtering --}}
            <div id="filter-no-results" class="text-center text-muted py-5 d-none w-100" style="border-top: 1px dashed var(--clr-border); margin-top: 15px;">
                <i class="bi bi-exclamation-circle d-block mb-2" style="font-size: 24px; color: var(--clr-text-secondary);"></i>
                <span>No invoices match your filter criteria.</span>
            </div>

        </div>
    </div>
</div>

@include('admin.invoices._modal_status')
@include('admin.invoices._modal_confirm')

@endsection

@push('styles')
    <link href="{{ asset('assets/css/invoice.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/area.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script>
        window.invoiceRoutePrefix = '/agent/invoices/';
    </script>
    <script src="{{ asset('assets/js/invoice-list.js') }}"></script>
@endpush
