<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Invoice System') }} - Agent</title>

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Dashboard Styles -->
    <link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app-ui.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>

    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <div class="sidebar-brand">
                <i class="bi bi-receipt-cutoff me-2"></i> {{ config('app.name', 'InvoicePro') }}
            </div>
            <div class="list-group">
                <a href="{{ route('agent.dashboard') }}" class="list-group-item {{ Request::routeIs('agent.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('agent.invoices.index') }}" class="list-group-item {{ Request::routeIs('agent.invoices.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> Invoices
                </a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content Wrapper -->
        <div id="page-content-wrapper">
            <!-- Top Navbar -->
            <nav class="dashboard-navbar">
                <div class="d-flex align-items-center">
                    <span class="fw-semibold text-dark">{{ config('app.name', 'Invoice Management System') }}</span>
                </div>

                <div class="navbar-user-info">
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
                @if (session('error'))
                    <div class="alert alert-danger border-0 shadow-sm py-2 px-3 mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <script src="{{ asset('assets/js/app-ui.js') }}"></script>
    @stack('scripts')
</body>
</html>
