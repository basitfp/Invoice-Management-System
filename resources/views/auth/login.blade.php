@extends('layouts.auth')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <!-- Logo Placeholder -->
      <div class="auth-logo-placeholder">

     <svg xmlns="http://www.w3.org/2000/svg"
         width="34"
         height="34"
         fill="currentColor"
         class="bi bi-receipt-cutoff"
         viewBox="0 0 16 16">
         ...
       </svg>

          <div class="ms-3">

             <div class="fw-bold fs-5">
                InvoicePro
             </div>

            <small class="text-muted">
                Invoice Management System
            </small>

        </div>

      </div>

        <h2>Welcome back</h2>
        <p class="subtitle">Sign in to access your dashboard</p>

        <!-- Backend Authentication Error Alert -->
        @if ($errors->has('login_error'))
            <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-4 py-2 px-3" role="alert" style="font-size: 13px; border-radius: 8px;">
                <i class="bi bi-exclamation-triangle-fill me-2" style="font-size: 16px;"></i>
                <div>
                    {{ $errors->first('login_error') }}
                </div>
            </div>
        @endif

        <form id="login-form" method="POST" action="{{ route('login.post') }}" novalidate>
            @csrf

            <!-- Email Field -->
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="name@company.com" autocomplete="email" required>
                <div id="email-feedback" class="invalid-feedback">
                    @error('email') {{ $message }} @enderror
                </div>
            </div>

            <!-- Password Field -->
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="password-input-wrapper">
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" autocomplete="current-password" required>
                    <button type="button" id="password-toggle" class="password-toggle-btn" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div id="password-feedback" class="invalid-feedback d-block">
                    @error('password') {{ $message }} @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="btn-submit" class="btn-auth-primary" disabled>
                Sign in
            </button>
        </form>
    </div>
</div>
@endsection
