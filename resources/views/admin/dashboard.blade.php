@extends('layouts.admin')

@section('content')

@php
    $companyName = $settings->app_name ?? config('app.name', 'InvoicePro');
    $companyInitial = strtoupper(substr($companyName, 0, 1));
@endphp

<div class="dashboard-hero">
    <div class="dashboard-hero-main">
        <div class="dashboard-hero-logo">
            @if($settings && $settings->logo)
                <img src="{{ asset('storage/' . $settings->logo) }}" alt="{{ $companyName }}">
            @else
                <span>{{ $companyInitial }}</span>
            @endif
        </div>
        <div>
            <p class="dashboard-eyebrow">Admin Overview</p>
            <h4 class="dashboard-title">{{ $companyName }}</h4>
            <p class="dashboard-subtitle">Welcome back, {{ Auth::user()->name }}. Here is what needs attention today.</p>
        </div>
    </div>
    <div class="dashboard-hero-side">
        @if($settings && ($settings->email || $settings->phone))
            <div class="dashboard-contact">
                @if($settings->email)
                    <span><i class="bi bi-envelope"></i>{{ $settings->email }}</span>
                @endif
                @if($settings->phone)
                    <span><i class="bi bi-telephone"></i>{{ $settings->phone }}</span>
                @endif
            </div>
        @endif
        <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary dashboard-primary-action">
            <i class="bi bi-plus-lg"></i> New Invoice
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card metric-card--green">
            <div class="metric-card-head">
                <span>Total Revenue</span>
                <i class="bi bi-currency-pound"></i>
            </div>
            <strong>£{{ number_format($totalRevenue, 2) }}</strong>
            <p><span>£{{ number_format($pendingRevenue, 2) }}</span> pending</p>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card metric-card--blue">
            <div class="metric-card-head">
                <span>Total Invoices</span>
                <i class="bi bi-receipt"></i>
            </div>
            <strong>{{ number_format($totalInvoices) }}</strong>
            <p>Invoices generated</p>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card metric-card--cyan">
            <div class="metric-card-head">
                <span>Total Customers</span>
                <i class="bi bi-people"></i>
            </div>
            <strong>{{ number_format($totalCustomers) }}</strong>
            <p>Customer records</p>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card metric-card--amber">
            <div class="metric-card-head">
                <span>Total Products</span>
                <i class="bi bi-box-seam"></i>
            </div>
            <strong>{{ number_format($totalProducts) }}</strong>
            <p>Inventory items</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-8">
        <div class="dashboard-panel">
            <div class="dashboard-panel-header">
                <div>
                    <h6>Recent Invoices</h6>
                    <p>Latest invoice activity across your workspace.</p>
                </div>
                <a href="{{ route('admin.invoices.index') }}">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table dashboard-table mb-0">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentInvoices as $invoice)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a>
                                </td>
                                <td>{{ $invoice->customer->name ?? '-' }}</td>
                                <td class="text-end fw-semibold">£{{ number_format($invoice->total_amount + $invoice->total_vat, 2) }}</td>
                                <td class="text-center">
                                    @php
                                        $statusClasses = [
                                            'draft'  => 'status-pill status-pill--draft',
                                            'unpaid' => 'status-pill status-pill--unpaid',
                                            'paid'   => 'status-pill status-pill--paid',
                                            'due'    => 'status-pill status-pill--due',
                                        ];
                                        $cls = $statusClasses[$invoice->status] ?? 'status-pill status-pill--draft';
                                    @endphp
                                    <span class="{{ $cls }}">{{ ucfirst($invoice->status) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No recent invoices found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="dashboard-panel mb-4">
            <div class="dashboard-panel-header">
                <div>
                    <h6>Quick Actions</h6>
                    <p>Jump into common admin work.</p>
                </div>
            </div>
            <div class="quick-action-list">
                <a href="{{ route('admin.customers.index') }}" class="quick-action">
                    <span class="quick-action-icon quick-action-icon--cyan"><i class="bi bi-person-plus"></i></span>
                    <span>Add New Customer</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="{{ route('admin.products.index') }}" class="quick-action">
                    <span class="quick-action-icon quick-action-icon--amber"><i class="bi bi-box-seam"></i></span>
                    <span>Add New Product</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="quick-action">
                    <span class="quick-action-icon quick-action-icon--slate"><i class="bi bi-gear"></i></span>
                    <span>System Settings</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>

        <div class="dashboard-panel">
            <div class="admin-profile">
                <div class="admin-profile-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div>
                    <h6>{{ Auth::user()->name }}</h6>
                    <p>{{ Auth::user()->email }}</p>
                </div>
            </div>
            <div class="profile-line">
                <span>Role</span>
                <strong>{{ Auth::user()->role }}</strong>
            </div>
            @if($settings && $settings->address)
                <div class="profile-line">
                    <span>Business</span>
                    <strong>{{ \Illuminate\Support\Str::limit($settings->address, 34) }}</strong>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
