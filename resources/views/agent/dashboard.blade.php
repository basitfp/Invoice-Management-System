@extends('layouts.agent')

@section('content')
<div class="row">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="dashboard-card">
            <h4 class="card-title mb-3">Welcome to the Agent Workspace</h4>
            <p class="text-muted mb-4" style="font-size: 14px; line-height: 1.5;">You have successfully authenticated as an Agent. From here, you will be able to create invoices, track invoice payments, and manage customer contacts.</p>
            
            <hr class="my-4" style="border-top: 1px solid var(--border-color); opacity: 1;">
            
            <div class="mb-4">
                <h6 class="text-uppercase text-secondary mb-3" style="font-size: 11px; font-weight: 600; letter-spacing: 0.05em;">Logged In User Information</h6>
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex justify-content-between py-2 border-bottom" style="font-size: 14px;">
                        <span class="text-secondary">Name:</span>
                        <span class="fw-semibold text-dark">{{ Auth::user()->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom" style="font-size: 14px;">
                        <span class="text-secondary">Email:</span>
                        <span class="fw-semibold text-dark">{{ Auth::user()->email }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2" style="font-size: 14px;">
                        <span class="text-secondary">User Role:</span>
                        <span class="fw-bold text-dark text-capitalize">{{ Auth::user()->role }}</span>
                    </div>
                </div>
            </div>

            <!-- Logout Button inside card -->
            <form action="{{ route('logout') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="btn btn-danger w-100 py-2 d-flex align-items-center justify-content-center gap-2" style="font-size: 14px; font-weight: 500; border-radius: 6px;">
                    <i class="bi bi-box-arrow-right"></i> Sign Out of Account
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
