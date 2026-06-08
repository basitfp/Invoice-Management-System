@extends('layouts.agent')

@section('title', 'My Invoices')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="invoice-card">

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
                <table class="table table-hover table-invoice">
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
                        {{-- FIX: id="row-{id}" required by invoice-list.js for AJAX DOM updates --}}
                        <tr id="row-{{ $invoice->id }}">
                            <td class="fw-bold text-primary">{{ $invoice->invoice_number }}</td>
                            <td>
                                <div class="customer-name">{{ $invoice->customer->name }}</div>
                                <div class="customer-email text-muted small">{{ $invoice->customer->email }}</div>
                            </td>
                            <td class="text-center">{{ date('d M Y', strtotime($invoice->invoice_date)) }}</td>
                            <td class="text-end fw-semibold">£{{ number_format($invoice->total_amount, 2) }}</td>
                            <td class="text-end text-muted">£{{ number_format($invoice->total_vat, 2) }}</td>
                            <td class="text-end text-dark fw-bold">£{{ number_format($invoice->total_amount + $invoice->total_vat, 2) }}</td>
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

        </div>
    </div>
</div>

@include('admin.invoices._modal_status')
@include('admin.invoices._modal_confirm')

@endsection

@push('styles')
    <link href="{{ asset('assets/css/invoice.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script>
        window.invoiceRoutePrefix = '/agent/invoices/';
    </script>
    <script src="{{ asset('assets/js/invoice-list.js') }}"></script>
@endpush