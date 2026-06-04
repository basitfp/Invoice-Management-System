@extends('layouts.agent')

@section('title', 'Agent Dashboard')

@section('content')

{{-- Welcome Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--text-primary);">Dashboard Overview</h4>
        <p class="text-muted mb-0" style="font-size: 14px;">Welcome back, {{ Auth::user()->name }}. Here is your sales summary.</p>
    </div>
    <div>
        <a href="{{ route('agent.invoices.create') }}" class="btn btn-primary d-flex align-items-center gap-2" style="border-radius: 10px; font-weight: 600;">
            <i class="bi bi-plus-lg"></i> Quick Create Invoice
        </a>
    </div>
</div>

{{-- Top KPI Cards --}}
<div class="row g-4 mb-5">
    
    {{-- Card 1: Total Revenue --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm" style="border-radius: 16px; background: #fff; overflow: hidden;">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-uppercase text-muted fw-bold mb-0" style="font-size: 11px; letter-spacing: 0.05em;">Total Revenue</h6>
                    <div class="bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 10px;">
                        <i class="bi bi-currency-pound fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bolder mb-1" style="color: var(--text-primary); font-size: 28px;">£{{ number_format($totalRevenue, 2) }}</h3>
                <p class="text-muted mb-0" style="font-size: 13px;">Lifetime billed</p>
            </div>
            <div class="bg-primary" style="height: 4px; width: 100%; position: absolute; bottom: 0;"></div>
        </div>
    </div>

    {{-- Card 2: Total Invoices --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm" style="border-radius: 16px; background: #fff; overflow: hidden;">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-uppercase text-muted fw-bold mb-0" style="font-size: 11px; letter-spacing: 0.05em;">Total Invoices</h6>
                    <div class="bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 10px;">
                        <i class="bi bi-receipt fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bolder mb-1" style="color: var(--text-primary); font-size: 28px;">{{ number_format($totalInvoices) }}</h3>
                <p class="text-muted mb-0" style="font-size: 13px;">Invoices generated</p>
            </div>
            <div class="bg-info" style="height: 4px; width: 100%; position: absolute; bottom: 0;"></div>
        </div>
    </div>

    {{-- Card 3: Paid Revenue --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm" style="border-radius: 16px; background: #fff; overflow: hidden;">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-uppercase text-muted fw-bold mb-0" style="font-size: 11px; letter-spacing: 0.05em;">Paid Revenue</h6>
                    <div class="bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 10px;">
                        <i class="bi bi-check-circle fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bolder mb-1" style="color: var(--text-primary); font-size: 28px;">£{{ number_format($paidRevenue, 2) }}</h3>
                <p class="text-muted mb-0" style="font-size: 13px;">Collected</p>
            </div>
            <div class="bg-success" style="height: 4px; width: 100%; position: absolute; bottom: 0;"></div>
        </div>
    </div>

    {{-- Card 4: Pending Revenue --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm" style="border-radius: 16px; background: #fff; overflow: hidden;">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-uppercase text-muted fw-bold mb-0" style="font-size: 11px; letter-spacing: 0.05em;">Pending Revenue</h6>
                    <div class="bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 10px;">
                        <i class="bi bi-clock-history fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bolder mb-1" style="color: var(--text-primary); font-size: 28px;">£{{ number_format($pendingRevenue, 2) }}</h3>
                <p class="text-muted mb-0" style="font-size: 13px;">Awaiting payment</p>
            </div>
            <div class="bg-warning" style="height: 4px; width: 100%; position: absolute; bottom: 0;"></div>
        </div>
    </div>

</div>

{{-- Main Content Row --}}
<div class="row g-4">
    
    {{-- Recent Invoices --}}
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm" style="border-radius: 16px; background: #fff;">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0" style="color: var(--text-primary);">Recent Invoices</h6>
                <a href="{{ route('agent.invoices.index') }}" class="text-decoration-none" style="font-size: 13px; font-weight: 600;">View All</a>
            </div>
            <div class="card-body p-0 mt-3">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 14px;">
                        <thead style="background: #fafbfc;">
                            <tr>
                                <th class="px-4 py-3 text-muted" style="font-weight: 600; font-size: 12px; text-transform: uppercase;">Invoice #</th>
                                <th class="py-3 text-muted" style="font-weight: 600; font-size: 12px; text-transform: uppercase;">Customer</th>
                                <th class="py-3 text-muted text-end" style="font-weight: 600; font-size: 12px; text-transform: uppercase;">Amount</th>
                                <th class="px-4 py-3 text-muted text-center" style="font-weight: 600; font-size: 12px; text-transform: uppercase;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInvoices as $invoice)
                            <tr>
                                <td class="px-4 py-3 align-middle">
                                    <a href="{{ route('agent.invoices.show', $invoice->id) }}" class="fw-semibold text-decoration-none">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td class="py-3 align-middle">{{ $invoice->customer->name ?? '-' }}</td>
                                <td class="py-3 align-middle text-end fw-semibold">£{{ number_format($invoice->total_amount + $invoice->total_vat, 2) }}</td>
                                <td class="px-4 py-3 align-middle text-center">
                                    @php
                                        $statusClasses = [
                                            'draft'     => 'bg-secondary bg-opacity-10 text-secondary',
                                            'unpaid'    => 'bg-primary bg-opacity-10 text-primary',
                                            'paid'      => 'bg-success bg-opacity-10 text-success',
                                            'due'       => 'bg-danger bg-opacity-10 text-danger',
                                        ];
                                        $cls = $statusClasses[$invoice->status] ?? 'bg-secondary bg-opacity-10 text-secondary';
                                    @endphp
                                    <span class="badge rounded-pill {{ $cls }}" style="font-weight: 600; padding: 6px 12px;">{{ ucfirst($invoice->status) }}</span>
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
    </div>

    {{-- Invoice Status Breakdown & User Info --}}
    <div class="col-12 col-xl-4">
        
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: #fff;">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold mb-0" style="color: var(--text-primary);">Status Breakdown</h6>
            </div>
            <div class="card-body p-4">
                @php
                    $paidPct = $totalRevenue > 0 ? ($paidRevenue / $totalRevenue) * 100 : 0;
                    $pendingPct = $totalRevenue > 0 ? ($pendingRevenue / $totalRevenue) * 100 : 0;
                @endphp
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size: 13px; font-weight: 600; color: var(--text-primary);">Paid Revenue</span>
                        <span style="font-size: 13px; font-weight: 600; color: var(--success-color);">{{ number_format($paidPct, 1) }}%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $paidPct }}%" aria-valuenow="{{ $paidPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size: 13px; font-weight: 600; color: var(--text-primary);">Pending/Due Revenue</span>
                        <span style="font-size: 13px; font-weight: 600; color: var(--warning-color);">{{ number_format($pendingPct, 1) }}%</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $pendingPct }}%" aria-valuenow="{{ $pendingPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 16px; background: #fff;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px; font-size: 20px;">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1" style="color: var(--text-primary);">{{ Auth::user()->name }}</h6>
                        <p class="text-muted mb-0" style="font-size: 13px;">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-top border-bottom mb-4" style="font-size: 14px;">
                    <span class="text-secondary">Role</span>
                    <span class="fw-semibold text-dark text-capitalize px-2 py-1 bg-light rounded">{{ Auth::user()->role }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 py-2 d-flex align-items-center justify-content-center gap-2" style="font-size: 14px; font-weight: 600; border-radius: 10px;">
                        <i class="bi bi-box-arrow-right"></i> Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection