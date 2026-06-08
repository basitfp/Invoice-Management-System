@extends('layouts.admin')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="invoice-card">

            <div class="invoice-list-header">
                <div>
                    <h4 class="invoice-title">Invoice Management</h4>
                    <p class="invoice-subtitle">View and manage all invoices.</p>
                </div>
                <div>
                    <a href="{{ route('admin.invoices.create') }}"
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
                            <th class="text-end" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                        {{-- FIX: id="row-{id}" required by invoice-list.js for DOM updates --}}
                        <tr id="row-{{ $invoice->id }}">
                            <td><span class="invoice-number-badge">{{ $invoice->invoice_number }}</span></td>
                            <td id="name-{{ $invoice->id }}">{{ $invoice->customer->name ?? '-' }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</td>
                            <td class="text-end">£{{ number_format($invoice->total_amount, 2) }}</td>
                            <td class="text-end">£{{ number_format($invoice->total_vat, 2) }}</td>
                            <td class="text-end"><strong>£{{ number_format($invoice->total_amount + $invoice->total_vat, 2) }}</strong></td>
                            {{-- FIX: added class="status-container" so JS can find and update it --}}
                            <td class="text-center status-container" id="status-container-{{ $invoice->id }}">
                                @php
                                    $statusClasses = [
                                        'draft'  => 'invoice-status-draft',
                                        'unpaid' => 'invoice-status-sent',
                                        'paid'   => 'invoice-status-paid',
                                        'due'    => 'invoice-status-cancelled',
                                    ];
                                    $cls = $statusClasses[$invoice->status] ?? 'invoice-status-draft';
                                @endphp
                                <span class="{{ $cls }}">{{ ucfirst($invoice->status) }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-end">

                                    {{-- View / Print --}}
                                    <a href="{{ route('admin.invoices.show', $invoice) }}"
                                        class="btn btn-invoice-action btn-invoice-view"
                                        title="View & Print">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    {{-- Status Change --}}
                                    <button class="btn btn-invoice-action btn-invoice-status"
                                        title="Change Status"
                                        data-id="{{ $invoice->id }}"
                                        data-status="{{ $invoice->status }}"
                                        data-number="{{ $invoice->invoice_number }}">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>

                                    {{-- Delete --}}
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
                                No invoices found. <a href="{{ route('admin.invoices.create') }}">Create one</a>
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
    {{-- No invoiceRoutePrefix needed — admin default '/admin/invoices/' is set in invoice-list.js --}}
    <script src="{{ asset('assets/js/invoice-list.js') }}"></script>
@endpush