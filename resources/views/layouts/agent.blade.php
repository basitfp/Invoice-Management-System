<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Agent Panel')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Shared Admin Theme -->
    <link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app-ui.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<style>
#toast-container > div {
    opacity: 1 !important;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
    border-radius: 10px !important;
}

.toast-success {
    background-color: #28a745 !important;
    color: #fff !important;
}

.toast-error {
    background-color: #dc3545 !important;
    color: #fff !important;
}

.toast-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.toast-info {
    background-color: #17a2b8 !important;
    color: #fff !important;
}

.toast-message {
    color: inherit !important;
}
</style>

<body>

<div id="wrapper">

    {{-- Sidebar --}}
    <div id="sidebar-wrapper">

        <div class="sidebar-brand">
            <i class="bi bi-person-badge-fill me-2"></i>
            Agent Portal
        </div>

        <div class="list-group">

            <a href="{{ route('agent.dashboard') }}"
               class="list-group-item {{ Request::routeIs('agent.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>

            <a href="{{ route('agent.invoices.index') }}"
               class="list-group-item {{ Request::routeIs('agent.invoices.index') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                My Invoices
            </a>

            <a href="{{ route('agent.invoices.create') }}"
               class="list-group-item {{ Request::routeIs('agent.invoices.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i>
                Create Invoice
            </a>

        </div>

    </div>

    {{-- Content --}}
    <div id="page-content-wrapper">

        {{-- Top Navbar --}}
        <nav class="dashboard-navbar">

            <div class="d-flex align-items-center">
                <span class="fw-semibold text-dark">
                    Agent Portal
                </span>
            </div>

            <div class="navbar-user-info">

                <div class="text-end me-2">
                    <div class="navbar-user-name">
                        {{ Auth::user()->name }}
                    </div>

                    <span class="role-badge agent">
                        Agent
                    </span>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf

                    <button type="submit"
                            class="btn btn-logout d-flex align-items-center gap-1">
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </button>
                </form>

            </div>

        </nav>

        {{-- Page Content --}}
        <div class="main-container">
            @yield('content')
        </div>

    </div>

</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Shared Scripts -->
<script src="{{ asset('assets/js/form-validation.js') }}"></script>
<script src="{{ asset('assets/js/entity-sync.js') }}"></script>
<script src="{{ asset('assets/js/app-ui.js') }}"></script>
<script src="{{ asset('assets/js/dashboard.js') }}"></script>

<script>
toastr.options = {
    closeButton: true,
    progressBar: true,
    newestOnTop: true,
    preventDuplicates: true,
    positionClass: "toast-top-right",
    timeOut: 3000
};

$(function () {

    @if(session('success'))
        toastr.success(@json(session('success')));
    @endif

    @if(session('error'))
        toastr.error(@json(session('error')));
    @endif

    @if(session('warning'))
        toastr.warning(@json(session('warning')));
    @endif

    @if(session('info'))
        toastr.info(@json(session('info')));
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
            toastr.error(@json($error));
        @endforeach
    @endif

});
</script>

@stack('scripts')

</body>
</html>