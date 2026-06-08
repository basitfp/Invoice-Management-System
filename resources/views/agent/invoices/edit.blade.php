@extends('layouts.agent')

@section('title', 'Edit Invoice')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">

        <div class="invoice-card">

            {{-- Page Header --}}
            <div class="invoice-create-header">
                <div>
                    <h4 class="invoice-title">Edit Invoice</h4>
                    <p class="invoice-subtitle">Update details for invoice {{ $invoice->invoice_number }}.</p>
                </div>
                <a href="{{ route('agent.invoices.index') }}"
                    class="btn btn-outline-secondary d-flex align-items-center gap-2"
                    style="border-radius: 10px; font-weight: 600; font-size: 14px; height: 44px; padding: 0 20px;">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

            <form id="invoiceCreateForm" method="POST" action="{{ route('agent.invoices.update', $invoice->id) }}">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                {{-- ── Section 1: Invoice Details ── --}}
                <div class="invoice-section">
                    <h6 class="invoice-section-title">Invoice Details</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="invoice-label">Invoice Date <span class="text-danger">*</span></label>
                            <input type="date" name="invoice_date" id="invoice_date"
                                class="form-control invoice-input"
                                value="{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') }}">
                            <span class="field-error text-danger small" id="invoice_date-error"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="invoice-label">Due Date <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" id="due_date"
                                class="form-control invoice-input"
                                value="{{ \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') }}">
                            <span class="field-error text-danger small" id="due_date-error"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="invoice-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control invoice-input">
                                <option value="unpaid" {{ $invoice->status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="draft" {{ $invoice->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="paid" {{ $invoice->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="due" {{ $invoice->status == 'due' ? 'selected' : '' }}>Due</option>
                            </select>
                            <span class="field-error text-danger small" id="status-error"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="invoice-label">Invoice Number</label>
                            <input type="text" class="form-control invoice-input"
                                value="{{ $invoice->invoice_number }}" disabled
                                style="background: #f8fafc; color: #94a3b8;">
                        </div>
                    </div>
                </div>

                {{-- ── Section 2: Customer ── --}}
                <div class="invoice-section">
                    <h6 class="invoice-section-title">Customer</h6>

                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="invoice-label mb-0">
                                Select Customer <span class="text-danger">*</span>
                            </label>
                            <button type="button" id="openCreateCustomerBtn"
                                class="btn btn-outline-primary d-flex align-items-center gap-1"
                                style="height:32px; font-size:12px; font-weight:600; border-radius:8px; padding:0 12px;">
                                <i class="bi bi-plus-lg"></i> New Customer
                            </button>
                        </div>
                        <select name="customer_id" id="customer_id" class="form-control invoice-input select2">
                            <option value="">-- Select a customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    data-email="{{ $customer->email }}"
                                    data-phone="{{ $customer->phone ?? '' }}"
                                    data-vat="{{ $customer->vat_number ?? '' }}"
                                    {{ $invoice->customer_id == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                    @if($customer->customer_type === 'business') (Business) @endif
                                </option>
                            @endforeach
                        </select>
                        <span class="field-error text-danger small" id="customer_id-error"></span>
                    </div>

                    {{-- Customer Info Labels --}}
                    <div id="customer-info-box" class="customer-info-box" style="display: {{ $invoice->customer_id ? 'block' : 'none' }};">
                        <div class="row">
                            <div class="col-md-4">
                                <span class="customer-info-label">Email</span>
                                <p class="customer-info-value" id="ci-email">{{ $invoice->customer->email ?? '-' }}</p>
                            </div>
                            <div class="col-md-4">
                                <span class="customer-info-label">Phone</span>
                                <p class="customer-info-value" id="ci-phone">{{ $invoice->customer->phone ?? '-' }}</p>
                            </div>
                            <div class="col-md-4">
                                <span class="customer-info-label">VAT Number</span>
                                <p class="customer-info-value" id="ci-vat">{{ $invoice->customer->vat_number ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ── Section 3: Products ── --}}
                <div class="invoice-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="invoice-section-title mb-0">Products</h6>
                        <button type="button" id="addRowBtn"
                            class="btn btn-outline-primary d-flex align-items-center gap-2"
                            style="border-radius: 8px; font-size: 13px; font-weight: 600; height: 38px; padding: 0 16px;">
                            <i class="bi bi-plus-lg"></i> Add Product
                        </button>
                    </div>

                    <span class="field-error text-danger small" id="products-error"></span>

                    <div class="table-responsive invoice-items-table-wrap">
                        <table class="table invoice-items-table">
                            <thead>
                                <tr>
                                    <th style="min-width:220px;">Product</th>
                                    <th style="width:130px;">Selling Price</th>
                                    <th style="width:110px;">VAT</th>
                                    <th style="width:100px;">Qty</th>
                                    <th style="width:130px;">Line Total</th>
                                    <th style="width:50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="invoice-items-body">
                                {{-- JS rows inject karega --}}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end invoice-total-label">Total (excl. VAT)</td>
                                    <td class="invoice-total-value" id="summary-subtotal">£0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end invoice-total-label">Total VAT</td>
                                    <td class="invoice-total-value" id="summary-vat">£0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end invoice-grand-total-label">Grand Total</td>
                                    <td class="invoice-grand-total-value" id="summary-grand">£0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>

                {{-- ── Footer Actions ── --}}
                <div class="invoice-form-footer">
                    <a href="{{ route('agent.invoices.index') }}" class="btn btn-outline-secondary"
                        style="height:48px; border-radius:10px; font-weight:600; padding:0 24px;">
                        Cancel
                    </a>
                    <button type="button" id="saveInvoiceBtn" class="btn btn-primary"
                        style="height:48px; border-radius:10px; font-weight:600; padding:0 32px;">
                        <i class="bi bi-save me-2"></i> Update Invoice
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     QUICK-CREATE CUSTOMER MODAL
══════════════════════════════════════════ --}}
<div class="modal fade" id="createCustomerModal" tabindex="-1" aria-labelledby="createCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px; border:1px solid var(--border-color); overflow:hidden;">

            <div class="modal-header" style="background:#fafbfc; border-bottom:1px solid var(--border-color); padding:20px 24px;">
                <h5 class="modal-title" id="createCustomerModalLabel"
                    style="font-size:16px; font-weight:700; color:var(--text-primary);">
                    <i class="bi bi-person-plus me-2" style="color:var(--primary-color);"></i>
                    Add New Customer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" style="padding:24px;">
                <div id="customer-modal-error" class="alert alert-danger d-none mb-3" role="alert"></div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="invoice-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" id="nc-name" class="form-control invoice-input validate-name" data-label="Name" placeholder="John Smith">
                        <span class="field-error text-danger small" id="nc-name-error"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="invoice-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" id="nc-email" class="form-control invoice-input validate-email" data-label="Email" placeholder="john@example.com" autocomplete="off">
                        <span class="field-error text-danger small" id="nc-email-error"></span>
                        <div id="nc-email-hint" class="small text-primary mt-1 d-none" role="status"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="invoice-label">Phone</label>
                        <input type="text" id="nc-phone" class="form-control invoice-input" placeholder="+44 7700 000000">
                    </div>
                    <div class="col-md-6">
                        <label class="invoice-label">Customer Type</label>
                        <select id="nc-type" class="form-control invoice-input">
                            <option value="regular">Regular</option>
                            <option value="business">Business</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="invoice-label">Address</label>
                        <textarea id="nc-address" rows="2"
                            class="form-control invoice-input" style="height:auto; padding:10px 14px;"
                            placeholder="123 Business St, London"></textarea>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="nc-vat-registered" role="switch">
                                <label class="form-check-label invoice-label mb-0" for="nc-vat-registered">
                                    VAT Registered
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" id="nc-vat-number-wrap" style="display:none;">
                        <label class="invoice-label">VAT Number</label>
                        <input type="text" id="nc-vat-number" class="form-control invoice-input"
                            placeholder="GB123456789"
                            oninput="this.value = this.value.toUpperCase()">
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="border-top:1px solid var(--border-color); padding:16px 24px; background:#fafbfc;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                    style="height:44px; border-radius:10px; font-weight:600; padding:0 20px;">
                    Cancel
                </button>
                <button type="button" id="saveNewCustomerBtn" class="btn btn-primary"
                    style="height:44px; border-radius:10px; font-weight:600; padding:0 28px;">
                    <span id="saveNewCustomerSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                    <i class="bi bi-check-lg me-1" id="saveNewCustomerIcon"></i> Save Customer
                </button>
            </div>

        </div>
    </div>
</div>

{{-- Products data for JS --}}
<script>
    window.availableProducts = @json($productsData);
    window.storeCustomerUrl  = "{{ route('agent.customers.store') }}";
    window.lookupCustomerUrl = "{{ route('agent.customers.lookup-by-email') }}";
    window.csrfToken         = "{{ csrf_token() }}";
    window.invoiceItems      = @json($invoice->items);
</script>

@endsection

@push('styles')
    <link href="{{ asset('assets/css/invoice.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/invoice-create.js') }}"></script>
@endpush