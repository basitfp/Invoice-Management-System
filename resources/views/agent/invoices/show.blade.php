@extends('layouts.agent')

@section('title', 'View Invoice')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-xl-9">

        {{-- ── Top Actions Bar (screen only) ── --}}
        <div class="invoice-actions-bar d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('agent.invoices.index') }}"
                class="btn btn-outline-secondary d-flex align-items-center gap-2"
                style="border-radius:10px; font-weight:600; font-size:14px; height:44px; padding:0 20px;">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <div class="d-flex gap-2">
                <button onclick="window.print()"
                    class="btn btn-primary d-flex align-items-center gap-2"
                    style="border-radius:10px; font-weight:600; font-size:14px; height:44px; padding:0 24px;">
                    <i class="bi bi-printer-fill"></i> Print Invoice
                </button>
            </div>
        </div>

        {{-- ══════════════ PRINTABLE INVOICE ══════════════ --}}
        <div class="invoice-print-wrapper" id="invoice-print-area">

            {{-- ── Header: Company Info + Invoice Meta ── --}}
            <div class="inv-header">
                <div class="inv-company-block">
                    @if($settings && $settings->logo)
                        <img src="{{ asset('storage/' . $settings->logo) }}" alt="Logo" class="inv-logo">
                    @endif
                    <h2 class="inv-company-name">{{ $settings->app_name ?? 'Company Name' }}</h2>
                    @if($settings && $settings->address)
                        <p class="inv-company-detail">{{ $settings->address }}</p>
                    @endif
                    @if($settings && $settings->phone)
                        <p class="inv-company-detail">{{ $settings->phone }}</p>
                    @endif
                    @if($settings && $settings->email)
                        <p class="inv-company-detail">{{ $settings->email }}</p>
                    @endif
                </div>

                <div class="inv-meta-block">
                    <h1 class="inv-title">INVOICE</h1>
                    <div class="inv-meta-row">
                        <span class="inv-meta-label">Invoice No:</span>
                        <span class="inv-meta-value">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="inv-meta-row">
                        <span class="inv-meta-label">Date:</span>
                        <span class="inv-meta-value">
                            {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}
                        </span>
                    </div>
                    <div class="inv-meta-row">
                        <span class="inv-meta-label">Status:</span>
                        <span class="inv-meta-value">
                            @php
                                $statusClasses = [
                                    'draft'  => 'invoice-status-draft',
                                    'unpaid' => 'invoice-status-sent',
                                    'paid'   => 'invoice-status-paid',
                                    'due'    => 'invoice-status-cancelled',
                                ];
                            @endphp
                            <span class="{{ $statusClasses[$invoice->status] ?? 'invoice-status-draft' }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <hr class="inv-divider">

            {{-- ── Bill To + Bank Details ── --}}
            <div class="inv-two-col">
                <div class="inv-bill-to">
                    <h6 class="inv-section-label">Bill To</h6>
                    <p class="inv-customer-name">{{ $invoice->customer->name }}</p>
                    <p class="inv-customer-detail">{{ $invoice->customer->email }}</p>
                    @if($invoice->customer->phone)
                        <p class="inv-customer-detail">{{ $invoice->customer->phone }}</p>
                    @endif
                    @if($invoice->customer->address)
                        <p class="inv-customer-detail">{{ $invoice->customer->address }}</p>
                    @endif
                    @if($invoice->customer->vat_number)
                        <p class="inv-customer-detail">VAT No: {{ $invoice->customer->vat_number }}</p>
                    @endif
                </div>

                @if($settings && ($settings->bank_name || $settings->iban || $settings->swift_code))
                <div class="inv-bank-details">
                    <h6 class="inv-section-label">Bank Details</h6>
                    @if($settings->bank_name)
                        <p class="inv-bank-row"><span>Bank:</span> {{ $settings->bank_name }}</p>
                    @endif
                    @if($settings->iban)
                        <p class="inv-bank-row"><span>IBAN:</span> {{ $settings->iban }}</p>
                    @endif
                    @if($settings->swift_code)
                        <p class="inv-bank-row"><span>SWIFT:</span> {{ $settings->swift_code }}</p>
                    @endif
                </div>
                @endif
            </div>

            {{-- ── Products Table ── --}}
            <div class="inv-items-wrap">
                <table class="inv-items-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Unit Price</th>
                            <th>VAT</th>
                            <th>Qty</th>
                            <th style="text-align:right;">Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->product->name ?? '-' }}</td>
                            <td>{{ number_format($item->selling_price, 2) }}</td>
                            <td>{{ $item->vat }}%</td>
                            <td>{{ $item->qty }}</td>
                            <td style="text-align:right; font-weight:600;">
                                {{ number_format($item->line_total, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ── Totals ── --}}
            {{-- FIX: use total_amount (DB column) instead of non-existent total_excl_vat / grand_total --}}
            <div class="inv-totals-wrap">
                <div class="inv-totals-box">
                    <div class="inv-total-row">
                        <span class="inv-total-label">Subtotal (excl. VAT)</span>
                        <span class="inv-total-value">{{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                    <div class="inv-total-row">
                        <span class="inv-total-label">Total VAT</span>
                        <span class="inv-total-value">{{ number_format($invoice->total_vat, 2) }}</span>
                    </div>
                    <div class="inv-total-row inv-grand-total-row">
                        <span class="inv-grand-label">Grand Total</span>
                        <span class="inv-grand-value">
                            {{ number_format($invoice->total_amount + $invoice->total_vat, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- ── Footer Note ── --}}
            <div class="inv-footer-note">
                <p>Thank you for your business.</p>
            </div>

        </div>
        {{-- End Printable Invoice --}}

    </div>
</div>

@endsection

@push('styles')
    <link href="{{ asset('assets/css/invoice.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
<script>
    var shouldAutoPrint = {{ $autoPrint ? 'true' : 'false' }};
    if (shouldAutoPrint) {
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 700);
        });
    }
</script>
@endpush
