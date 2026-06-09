<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Invoice System') }} - Admin</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">

    <!-- Custom Styles -->
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

/* Success */
.toast-success {
    background-color: #28a745 !important;
    color: #fff !important;
}

/* Error */
.toast-error {
    background-color: #dc3545 !important;
    color: #fff !important;
}

/* Warning */
.toast-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

/* Info */
.toast-info {
    background-color: #17a2b8 !important;
    color: #fff !important;
}

/* Message text fix */
.toast-message {
    color: inherit !important;
}
</style>
<body>
    @php
        $appSettings = \App\Models\Setting::first();
        $brandName = $appSettings->app_name ?? config('app.name', 'InvoicePro');
        $brandInitial = strtoupper(substr($brandName, 0, 1));
    @endphp

    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <div class="sidebar-brand">
                <div class="brand-mark">
                    @if($appSettings && $appSettings->logo)
                        <img src="{{ asset('storage/' . $appSettings->logo) }}" alt="{{ $brandName }}">
                    @else
                        <span>{{ $brandInitial }}</span>
                    @endif
                </div>
                <div class="brand-copy">
                    <span class="brand-name">{{ $brandName }}</span>
                    <span class="brand-subtitle">Admin Workspace</span>
                </div>
            </div>

            <div class="list-group">
                <a href="{{ route('admin.dashboard') }}"
                   class="list-group-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('admin.categories.index') }}"
                   class="list-group-item {{ Request::routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i> Categories
                </a>
                <a href="{{ route('admin.areas.index') }}"
                   class="list-group-item {{ Request::routeIs('admin.areas.*') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt"></i> Areas
                </a>    
                <a href="{{ route('admin.manufacturers.index') }}"
                   class="list-group-item {{ Request::routeIs('admin.manufacturers.*') ? 'active' : '' }}">
                    <i class="bi bi-bricks"></i> Manufacturers
                </a>
                <a href="{{ route('admin.vendors.index') }}"
                   class="list-group-item {{ Request::routeIs('admin.vendors.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Vendors
                </a>    
                <a href="{{ route('admin.products.index') }}"
                   class="list-group-item {{ Request::routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> Products
                </a>
                <a href="{{ route('admin.customers.index') }}"
                   class="list-group-item {{ Request::routeIs('admin.customers.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Customers
                </a>
                <a href="{{ route('admin.invoices.index') }}"
                   class="list-group-item {{ Request::routeIs('admin.invoices.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> Invoices
                </a>
                <a href="{{ route('admin.settings.index') }}"
                   class="list-group-item {{ Request::routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i> Settings
                </a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content Wrapper -->
        <div id="page-content-wrapper">

            <!-- Top Navbar -->
            <nav class="dashboard-navbar">
                <div class="navbar-business">
                    <span class="navbar-business-name">{{ $brandName }}</span>
                    <span class="navbar-business-meta">
                        @if($appSettings && $appSettings->email)
                            <i class="bi bi-envelope"></i> {{ $appSettings->email }}
                        @elseif($appSettings && $appSettings->phone)
                            <i class="bi bi-telephone"></i> {{ $appSettings->phone }}
                        @else
                            Invoice Management System
                        @endif
                    </span>
                </div>

                <div class="navbar-user-info">
                    <div class="navbar-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                    <div class="text-end me-2">
                        <div class="navbar-user-name">{{ Auth::user()->name }}</div>
                        <span class="role-badge {{ Auth::user()->role }}">{{ Auth::user()->role }}</span>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-logout d-flex align-items-center gap-1">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </nav>

            <div class="main-container">
                @yield('content')
            </div>

        </div>
        <!-- /#page-content-wrapper -->
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> 
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <script src="{{ asset('assets/js/form-validation.js') }}"></script>
    <script src="{{ asset('assets/js/entity-sync.js') }}"></script>
    <script src="{{ asset('assets/js/app-ui.js') }}"></script>
<script>
    toastr.options = {
        closeButton: true,
        progressBar: true,
        newestOnTop: true,
        preventDuplicates: true,
        positionClass: "toast-top-right",
        timeOut: 3000,
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
