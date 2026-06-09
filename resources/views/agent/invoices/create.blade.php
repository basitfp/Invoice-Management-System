@extends('layouts.agent')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">

        <div class="invoice-card">

            {{-- Page Header --}}
            <div class="invoice-create-header">
                <div>
                    <h4 class="invoice-title">Create New Invoice</h4>
                    <p class="invoice-subtitle">Fill in the details below to generate an invoice.</p>
                </div>
                <a href="{{ route('agent.invoices.index') }}"
                    class="btn btn-outline-secondary d-flex align-items-center gap-2"
                    style="border-radius: 10px; font-weight: 600; font-size: 14px; height: 44px; padding: 0 20px;">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

            <form id="invoiceCreateForm" method="POST" action="{{ route('agent.invoices.store') }}">
                @csrf

                {{-- ── Section 1: Invoice Details ── --}}
                <div class="invoice-section">
                    <h6 class="invoice-section-title">Invoice Details</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="invoice-label">Invoice Date <span class="text-danger">*</span></label>
                            <input type="date" name="invoice_date" id="invoice_date"
                                class="form-control invoice-input"
                                value="{{ date('Y-m-d') }}"
                                max="{{ date('Y-m-d') }}">
                            <span class="field-error text-danger small" id="invoice_date-error"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="invoice-label">Due Date <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" id="due_date"
                                class="form-control invoice-input"
                                value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                            <span class="field-error text-danger small" id="due_date-error"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="invoice-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control invoice-input">
                                <!-- <option value="draft">Draft</option> -->
                                <option value="unpaid" selected>Unpaid</option>
                                <!-- <option value="paid">Paid</option>
                                <option value="due">Due</option> -->
                            </select>
                            <span class="field-error text-danger small" id="status-error"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="invoice-label">Invoice Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">INV-</span>
                                <input type="text" id="invoice_number_suffix"
                                    class="form-control invoice-input"
                                    placeholder="0001"
                                    autocomplete="off">
                            </div>
                            <input type="hidden" name="invoice_number" id="invoice_number" value="INV-">
                            <span class="field-error text-danger small" id="invoice_number_suffix-error"></span>
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
                                style="height:32px; font-size:13px; font-weight:600; border-radius:8px; padding:0 16px; height:38px ">
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
                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                    @if($customer->customer_type === 'company') (Company) @endif
                                </option>
                            @endforeach
                        </select>
                        <span class="field-error text-danger small" id="customer_id-error"></span>
                    </div>

                    {{-- Customer Info Labels --}}
                    <div id="customer-info-box" class="customer-info-box" style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <span class="customer-info-label">Email</span>
                                <p class="customer-info-value" id="ci-email">-</p>
                            </div>
                            <div class="col-md-4">
                                <span class="customer-info-label">Phone</span>
                                <p class="customer-info-value" id="ci-phone">-</p>
                            </div>
                            <div class="col-md-4">
                                <span class="customer-info-label">VAT Number</span>
                                <p class="customer-info-value" id="ci-vat">-</p>
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
                                    <td class="invoice-total-value" id="summary-subtotal">0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end invoice-total-label">Total VAT</td>
                                    <td class="invoice-total-value" id="summary-vat">0.00</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end invoice-grand-total-label">Grand Total</td>
                                    <td class="invoice-grand-total-value" id="summary-grand">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>

                {{-- ── Footer Actions ── --}}
                <div class="invoice-form-footer">
                    <a href="{{ route('agent.invoices.index') }}" class="btn btn-outline-secondary"
                        style="height:48px; border-radius:10px; font-weight:600; padding:9px 24px;">
                        Cancel
                    </a>
                    <button type="button" id="saveInvoiceBtn" class="btn btn-primary"
                        style="height:48px; border-radius:10px; font-weight:600; padding:0 32px;">
                        <i class="bi bi-save me-2"></i> Save & Print
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
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
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
                        <input type="text" id="nc-phone" class="form-control invoice-input" placeholder="+923121234567">
                           <span class="field-error text-danger small" id="nc-phone-error"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="invoice-label">Customer Type</label>
                        <select id="nc-type" class="form-control invoice-input">
                            <option value="individual">Individual</option>
                            <option value="company">Company</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="invoice-label">Gender</label>
                        <select id="nc-gender" class="form-control invoice-input">
                            <option value="">-- Select --</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="invoice-label">Date of Birth</label>
                        <input type="date" id="nc-birthdate" class="form-control invoice-input">
                    </div>
                    <div class="col-12">
                        <label class="invoice-label">Billing Address</label>
                        <textarea id="nc-address" rows="2"
                            class="form-control invoice-input" style="height:auto; padding:10px 14px;"
                            placeholder="123 Business St, London"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="invoice-label">Shipping Address</label>
                        <textarea id="nc-shipping-address" rows="2"
                            class="form-control invoice-input" style="height:auto; padding:10px 14px;"
                            placeholder="Leave blank if same as billing"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="invoice-label">City</label>
                        <input type="text" id="nc-city" class="form-control invoice-input">
                    </div>
                    <div class="col-md-4">
                        <label class="invoice-label">PIN / ZIP Code</label>
                        <input type="text" id="nc-pin-code" class="form-control invoice-input">
                    </div>
                    <div class="col-md-4">
                        <label class="invoice-label">State / Province</label>
                        <input type="text" id="nc-state" class="form-control invoice-input">
                    </div>
                    <div class="col-md-4">
                        <label class="invoice-label">Country</label>
                        <input type="text" id="nc-country" class="form-control invoice-input">
                    </div>
                    <div class="col-md-4">
                        <label class="invoice-label">Landmark</label>
                        <input type="text" id="nc-landmark" class="form-control invoice-input">
                    </div>
                    <div class="col-md-4">
                        <label class="invoice-label">Area</label>
                        <select id="nc-area-id" class="form-control invoice-input">
                            <option value="">-- Select Area --</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="invoice-label">Credit Days</label>
                        <input type="number" id="nc-credit-days" class="form-control invoice-input" min="0" max="65535">
                    </div>
                    <div class="col-md-6">
                        <label class="invoice-label">Credit Limit</label>
                        <input type="number" id="nc-credit-limit" class="form-control invoice-input" min="0" step="0.01">
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
</script>

@endsection

@push('styles')
    <link href="{{ asset('assets/css/invoice.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/invoice-create.js') }}"></script>
@endpush
