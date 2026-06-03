@extends('layouts.admin')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">

        {{-- Page Header --}}
        <div class="settings-page-header mb-4">
            <div>
                <h4 class="settings-page-title">System Settings</h4>
                <p class="settings-page-subtitle">Configure your business profile, branding, and bank details.</p>
            </div>
        </div>

        {{-- Success Alert --}}
        @if(session('success'))
        <div class="alert settings-alert-success d-flex align-items-center gap-3 mb-4" role="alert">
            <div class="settings-alert-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div>{{ session('success') }}</div>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" id="settingsForm">
            @csrf
            @method('PUT')

            <div class="row g-4">

                {{-- ══ LEFT COLUMN ══ --}}
                <div class="col-12 col-lg-7">

                    {{-- Business Identity --}}
                    <div class="settings-card mb-4">
                        <div class="settings-card-header">
                            <div class="settings-card-icon">
                                <i class="bi bi-building"></i>
                            </div>
                            <div>
                                <h6 class="settings-card-title">Business Identity</h6>
                                <p class="settings-card-desc">Your company name and contact info shown on invoices.</p>
                            </div>
                        </div>
                        <div class="settings-card-body">

                            <div class="mb-4">
                                <label class="settings-label">
                                    App / Company Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="app_name" id="app_name"
                                    class="form-control settings-input @error('app_name') is-invalid @enderror"
                                    value="{{ old('app_name', $settings->app_name ?? '') }}"
                                    placeholder="e.g. Acme Ltd.">
                                @error('app_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="settings-label">Email Address</label>
                                    <div class="settings-input-icon-wrap">
                                        <i class="bi bi-envelope settings-input-icon"></i>
                                        <input type="email" name="email"
                                            class="form-control settings-input settings-input-with-icon @error('email') is-invalid @enderror"
                                            value="{{ old('email', $settings->email ?? '') }}"
                                            placeholder="hello@company.com">
                                    </div>
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="settings-label">Phone Number</label>
                                    <div class="settings-input-icon-wrap">
                                        <i class="bi bi-telephone settings-input-icon"></i>
                                        <input type="text" name="phone"
                                            class="form-control settings-input settings-input-with-icon @error('phone') is-invalid @enderror"
                                            value="{{ old('phone', $settings->phone ?? '') }}"
                                            placeholder="+44 7700 000000">
                                    </div>
                                    @error('phone')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="settings-label">Business Address</label>
                                <textarea name="address" rows="3"
                                    class="form-control settings-input settings-textarea @error('address') is-invalid @enderror"
                                    placeholder="123 Business Lane, London, EC1A 1BB">{{ old('address', $settings->address ?? '') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- Bank Details --}}
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <div class="settings-card-icon settings-card-icon--bank">
                                <i class="bi bi-bank"></i>
                            </div>
                            <div>
                                <h6 class="settings-card-title">Bank Details</h6>
                                <p class="settings-card-desc">Printed on invoices for payment reference.</p>
                            </div>
                        </div>
                        <div class="settings-card-body">

                            <div class="mb-4">
                                <label class="settings-label">Bank Name</label>
                                <input type="text" name="bank_name"
                                    class="form-control settings-input @error('bank_name') is-invalid @enderror"
                                    value="{{ old('bank_name', $settings->bank_name ?? '') }}"
                                    placeholder="e.g. Barclays Bank">
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="settings-label">IBAN</label>
                                <input type="text" name="iban"
                                    class="form-control settings-input settings-input-mono @error('iban') is-invalid @enderror"
                                    value="{{ old('iban', $settings->iban ?? '') }}"
                                    placeholder="GB29 NWBK 6016 1331 9268 19"
                                    oninput="this.value = this.value.toUpperCase()">
                                @error('iban')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-0">
                                <label class="settings-label">SWIFT / BIC Code</label>
                                <input type="text" name="swift_code"
                                    class="form-control settings-input settings-input-mono @error('swift_code') is-invalid @enderror"
                                    value="{{ old('swift_code', $settings->swift_code ?? '') }}"
                                    placeholder="NWBKGB2L"
                                    oninput="this.value = this.value.toUpperCase()">
                                @error('swift_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                </div>

                {{-- ══ RIGHT COLUMN ══ --}}
                <div class="col-12 col-lg-5">

                    {{-- Logo Upload --}}
                    <div class="settings-card mb-4">
                        <div class="settings-card-header">
                            <div class="settings-card-icon settings-card-icon--logo">
                                <i class="bi bi-image"></i>
                            </div>
                            <div>
                                <h6 class="settings-card-title">Company Logo</h6>
                                <p class="settings-card-desc">PNG, JPG, SVG or WebP. Max 2MB.</p>
                            </div>
                        </div>
                        <div class="settings-card-body">

                            {{-- Current Logo Preview --}}
                            <div id="logo-preview-wrap" class="{{ ($settings && $settings->logo) ? '' : 'd-none' }} mb-3">
                                <div class="settings-logo-preview-box">
                                    <img id="logo-preview-img"
                                        src="{{ ($settings && $settings->logo) ? asset('storage/' . $settings->logo) : '' }}"
                                        alt="Logo Preview">
                                    <button type="button" id="removeLogo" class="settings-logo-remove-btn">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Upload Drop Zone --}}
                            <div id="logo-dropzone" class="settings-dropzone {{ ($settings && $settings->logo) ? 'd-none' : '' }}"
                                onclick="document.getElementById('logo-file').click()">
                                <div class="settings-dropzone-icon">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                </div>
                                <p class="settings-dropzone-text">Click to upload logo</p>
                                <p class="settings-dropzone-hint">PNG, JPG, SVG, WebP · max 2MB</p>
                            </div>

                            <input type="file" id="logo-file" name="logo" accept="image/*" class="d-none">
                            <input type="hidden" name="remove_logo" id="remove_logo_flag" value="0">

                            @error('logo')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror

                        </div>
                    </div>

                    {{-- Live Preview Card --}}
                    <div class="settings-card settings-preview-card">
                        <div class="settings-card-header">
                            <div class="settings-card-icon settings-card-icon--preview">
                                <i class="bi bi-eye"></i>
                            </div>
                            <div>
                                <h6 class="settings-card-title">Invoice Preview</h6>
                                <p class="settings-card-desc">How your details appear on invoices.</p>
                            </div>
                        </div>
                        <div class="settings-card-body">
                            <div class="settings-invoice-preview">
                                <div class="sip-logo-wrap" id="sip-logo-wrap">
                                    @if($settings && $settings->logo)
                                        <img id="sip-logo" src="{{ asset('storage/' . $settings->logo) }}" alt="logo">
                                    @else
                                        <img id="sip-logo" src="" alt="logo" style="display:none;">
                                    @endif
                                </div>
                                <p class="sip-company-name" id="sip-name">{{ $settings->app_name ?? 'Company Name' }}</p>
                                <p class="sip-detail" id="sip-address">{{ $settings->address ?? 'Business Address' }}</p>
                                <p class="sip-detail" id="sip-phone">{{ $settings->phone ?? '+44 0000 000000' }}</p>
                                <p class="sip-detail" id="sip-email">{{ $settings->email ?? 'email@company.com' }}</p>
                                <div class="sip-divider"></div>
                                <p class="sip-bank-label">Bank Details</p>
                                <p class="sip-detail" id="sip-bank">{{ $settings->bank_name ?? 'Bank Name' }}</p>
                                <p class="sip-detail sip-mono" id="sip-iban">{{ $settings->iban ?? 'IBAN' }}</p>
                                <p class="sip-detail sip-mono" id="sip-swift">{{ $settings->swift_code ?? 'SWIFT' }}</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Save Button --}}
            <div class="settings-form-footer mt-4">
                <div class="settings-save-hint">
                    <i class="bi bi-info-circle me-1"></i>
                    Changes apply site-wide — invoices, headers, and all printed documents.
                </div>
                <button type="submit" class="btn btn-primary settings-save-btn">
                    <i class="bi bi-floppy me-2"></i> Save Settings
                </button>
            </div>

        </form>

    </div>
</div>

@endsection

@push('styles')
    <link href="{{ asset('assets/css/settings.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/settings.js') }}"></script>
@endpush