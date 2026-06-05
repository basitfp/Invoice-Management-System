@extends('layouts.auth')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        
        <div class="auth-logo-placeholder">
            <svg xmlns="http://www.w3.org/2000/svg"
                 width="32"
                 height="32"
                 fill="currentColor"
                 class="bi bi-receipt-cutoff"
                 viewBox="0 0 16 16">
                 <path d="M3 4.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                 <path d="M20 1a1 1 0 0 1 1 1v12.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 4 14.5V2a1 1 0 0 1 1-1h15zM5 2v12.5a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5V2H5z"/>
            </svg>
            <div class="ms-3">
                <div class="fw-bold fs-5 lh-sm" style="color: var(--text-primary);">InvoicePro</div>
                <small class="text-muted" style="font-size: 12px;">Invoice Management System</small>
            </div>
        </div>

        <h2>Welcome back</h2>
        <p class="subtitle">Sign in to access your dashboard</p>

        @if ($errors->has('login_error'))
            <div class="alert alert-danger border-0 d-flex align-items-center mb-4 py-2.5 px-3" role="alert" style="font-size: 13px;">
                <i class="bi bi-exclamation-triangle-fill me-2" style="font-size: 15px; color: var(--error-color);"></i>
                <div>
                    {{ $errors->first('login_error') }}
                </div>
            </div>
        @endif

        <form id="login-form" method="POST" action="{{ route('login.post') }}" novalidate>
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="name@company.com" autocomplete="email" required>
                <div id="email-feedback" class="invalid-feedback">
                    @error('email') {{ $message }} @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="password-input-wrapper">
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" autocomplete="current-password" required>
                    <button type="button" id="password-toggle" class="password-toggle-btn" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div id="password-feedback" class="invalid-feedback">
                    @error('password') {{ $message }} @enderror
                </div>
            </div>

            <button type="submit" id="btn-submit" class="btn-auth-primary" disabled>
                Sign in
            </button>
        </form>
    </div>
</div>
@endsection