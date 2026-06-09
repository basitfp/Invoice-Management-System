@extends('layouts.admin')

@section('content')

    @php
        $companyName = $settings->app_name ?? config('app.name', 'InvoicePro');
        $companyInitial = strtoupper(substr($companyName, 0, 1));
    @endphp

    {{-- Chart.js (premium analytics visuals) - loaded via CDN only --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>


    <div class="dashboard-hero">
        <div class="dashboard-hero-main">
            <div class="dashboard-hero-logo">
                @if ($settings && $settings->logo)
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
            @if ($settings && ($settings->email || $settings->phone))
                <div class="dashboard-contact">
                    @if ($settings->email)
                        <span><i class="bi bi-envelope"></i>{{ $settings->email }}</span>
                    @endif
                    @if ($settings->phone)
                        <span><i class="bi bi-telephone"></i>{{ $settings->phone }}</span>
                    @endif
                </div>
            @endif
            <div class="d-flex gap-2 justify-content-md-end mt-2 mt-md-0">
                <a href="{{ route('admin.invoices.create') }}"
                    class="btn btn-primary d-inline-flex align-items-center gap-2"
                    style="border-radius:10px; font-weight:600; font-size:14px; height:42px;">
                    <i class="bi bi-plus-lg"></i> New Invoice
                </a>
            </div>
        </div>
    </div>

    {{-- Standardized KPI Grid --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted fw-bold text-uppercase" style="font-size:11px; letter-spacing:0.05em;">Total
                            Customers</span>
                        <div class="bg-primary bg-opacity-10 text-primary rounded d-flex align-items-center justify-content-center"
                            style="width:40px; height:40px; border-radius:10px !important;">
                            <i class="bi bi-people fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bolder mb-1" style="color:var(--text-primary); font-size:28px;">
                        {{ number_format($totalCustomers) }}</h3>
                    <p class="kpi-kicker text-truncate">Registered accounts</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted fw-bold text-uppercase" style="font-size:11px; letter-spacing:0.05em;">Total
                            Products</span>
                        <div class="bg-success bg-opacity-10 text-success rounded d-flex align-items-center justify-content-center"
                            style="width:40px; height:40px; border-radius:10px !important;">
                            <i class="bi bi-box-seam fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bolder mb-1" style="color:var(--text-primary); font-size:28px;">
                        {{ number_format($totalProducts) }}</h3>
                    <p class="kpi-kicker text-truncate">Active inventory items</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted fw-bold text-uppercase" style="font-size:11px; letter-spacing:0.05em;">Paid
                            Revenue</span>
                        <div class="bg-info bg-opacity-10 text-info rounded d-flex align-items-center justify-content-center"
                            style="width:40px; height:40px; border-radius:10px !important;">
                            <i class="bi bi-cash-stack fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bolder mb-1" style="color:var(--text-primary); font-size:28px;">
                        {{ number_format($totalRevenue, 2) }}</h3>
                    <p class="kpi-kicker text-truncate">Collected earnings</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm" style="border-radius:16px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted fw-bold text-uppercase"
                            style="font-size:11px; letter-spacing:0.05em;">Pending Revenue</span>
                        <div class="bg-warning bg-opacity-10 text-warning rounded d-flex align-items-center justify-content-center"
                            style="width:40px; height:40px; border-radius:10px !important;">
                            <i class="bi bi-clock-history fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bolder mb-1" style="color:var(--text-primary); font-size:28px;">
                        {{ number_format($pendingRevenue, 2) }}</h3>
                    <p class="kpi-kicker text-truncate">Unpaid & due invoices</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Analytics Dashboard Metrics Block --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-7">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                <div
                    class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0" style="color:var(--text-primary);">Financial Trajectory</h6>
                        <p class="text-muted mb-0" style="font-size:12px; font-weight:600;">Monthly revenue tracking (Paid
                            vs Pending)</p>
                    </div>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="chart-wrap chart-wrap--tall">
                        <canvas id="financialTrajectoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold mb-0" style="color:var(--text-primary);">Invoice Allocation</h6>
                    <p class="text-muted mb-0" style="font-size:12px; font-weight:600;">Status breakdown metrics</p>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="chart-wrap chart-wrap--tall">
                        @if ($totalInvoices > 0)
                            <canvas id="invoiceAllocationChart"></canvas>
                        @else
                            <div class="chart-empty">
                                <p class="text-muted"><i class="bi bi-pie-chart d-block fs-3 mb-2"></i> No analytics
                                    available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold mb-0" style="color:var(--text-primary);">Customer Onboarding</h6>
                    <p class="text-muted mb-0" style="font-size:12px; font-weight:600;">New client registrations trend</p>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="chart-wrap">
                        <canvas id="customerGrowthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold mb-0" style="color:var(--text-primary);">Inventory Scaling</h6>
                    <p class="text-muted mb-0" style="font-size:12px; font-weight:600;">Product catalog additions trend
                    </p>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="chart-wrap">
                        <canvas id="productGrowthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Data Panels --}}
    <div class="row g-4">
        {{-- Recent Invoices --}}
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                <div
                    class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0" style="color:var(--text-primary);">Recent Invoices</h6>
                    <a href="{{ route('admin.invoices.index') }}" class="btn btn-link p-0 text-decoration-none fw-bold"
                        style="font-size:13px; color:var(--primary-color);">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle" style="font-size:14px;">
                            <thead class="table-light text-uppercase text-muted"
                                style="font-size:11px; font-weight:700; letter-spacing:0.05em;">
                                <tr>
                                    <th class="px-4 py-3">Invoice #</th>
                                    <th class="py-3">Customer</th>
                                    <th class="py-3 text-end">Gross Amount</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentInvoices as $inv)
                                    <tr>
                                        <td class="px-4 py-3 fw-semibold">
                                            <a href="{{ route('admin.invoices.show', $inv->id) }}"
                                                class="text-decoration-none" style="color:var(--primary-color);">
                                                {{ $inv->invoice_number }}
                                            </a>
                                        </td>
                                        <td class="py-3 text-truncate" style="max-width:180px;">
                                            {{ $inv->customer->name ?? '-' }}</td>
                                        <td class="py-3 text-end fw-bold text-dark">
                                            {{ number_format($inv->total_amount + $inv->total_vat, 2) }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @php
                                                $badges = [
                                                    'paid' => 'bg-success bg-opacity-10 text-success',
                                                    'unpaid' => 'bg-primary bg-opacity-10 text-primary',
                                                    'due' => 'bg-danger bg-opacity-10 text-danger',
                                                    'draft' => 'bg-secondary bg-opacity-10 text-secondary',
                                                ];
                                                $badgeClass = $badges[$inv->status] ?? 'bg-light text-dark';
                                            @endphp
                                            <span class="badge rounded-pill {{ $badgeClass }}"
                                                style="font-weight:600; padding:6px 12px; font-size:11px; text-transform:capitalize;">
                                                {{ $inv->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="bi bi-receipt d-block fs-2 mb-2 opacity-50"></i> No records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Performance Indicators Side-panel --}}
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2">
                    <h6 class="fw-bold mb-0" style="color:var(--text-primary);">Top Spending Customers</h6>
                </div>
                <div class="card-body px-4 pb-4 pt-0">
                    <div class="premium-list">
                        @forelse($topCustomers as $tc)
                            @php
                                $tc = (object) $tc;
                            @endphp
                            <div class="premium-row d-flex justify-content-between align-items-center w-100 mb-3"
                                style="border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                                <div class="premium-row-main d-flex align-items-center" style="max-width: 70%;">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-secondary fw-bold"
                                        style="width:36px; height:36px; font-size:13px; flex-shrink: 0;">
                                        {{ strtoupper(substr($tc->name ?? '', 0, 2)) }}
                                    </div>
                                    <div class="ms-3 overflow-hidden">
                                        <p class="premium-row-title mb-0"
                                            style="font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $tc->name ?? 'Unknown' }}</p>
                                        <p class="premium-row-meta mb-0 text-muted"
                                            style="font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $tc->email ?? 'No email address' }}</p>
                                    </div>
                                </div>
                                <div class="premium-row-value text-end fw-bold ps-2"
                                    style="color: var(--text-primary); font-size: 14px; white-space: nowrap;">
                                    {{ number_format($tc->total_spent ?? 0, 2) }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-star d-block fs-2 mb-2 opacity-50"></i> No customer metrics recorded.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                const monthLabels = @json($monthLabels ?? []);
                const paidSeries = @json($paidSeries ?? []);
                const pendingSeries = @json($pendingSeries ?? []);
                const statusLabels = @json($statusLabels ?? []);
                const statusCounts = @json($statusCounts ?? []);
                const custGrowth = @json($customerGrowthSeries ?? []);
                const prodGrowth = @json($productGrowthSeries ?? []);

                function safeArray(v, expectedLen) {
                    if (!Array.isArray(v)) return [];
                    return v;
                }

                const labels = safeArray(monthLabels);

                if (labels.length > 0) {
                    const ctx1 = document.getElementById('financialTrajectoryChart');
                    if (ctx1) {
                        new Chart(ctx1, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                        label: 'Paid Revenue',
                                        data: safeArray(paidSeries),
                                        borderColor: '#10b981',
                                        backgroundColor: 'rgba(16,185,129,0.06)',
                                        tension: 0.35,
                                        borderWidth: 2,
                                        fill: true,
                                        pointRadius: 3
                                    },
                                    {
                                        label: 'Pending / Due',
                                        data: safeArray(pendingSeries),
                                        borderColor: '#f59e0b',
                                        backgroundColor: 'rgba(245,158,11,0.06)',
                                        tension: 0.35,
                                        borderWidth: 2,
                                        fill: true,
                                        pointRadius: 3
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'bottom',
                                        labels: {
                                            boxWidth: 12,
                                            font: {
                                                weight: 600
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        grid: {
                                            color: '#f1f5f9'
                                        },
                                        ticks: {
                                            callback: function(val) {
                                                return val.toLocaleString();
                                            }
                                        }
                                    },
                                    x: {
                                        grid: {
                                            display: false
                                        }
                                    }
                                }
                            }
                        });
                    }

                    const ctx2 = document.getElementById('invoiceAllocationChart');
                    if (ctx2 && statusLabels.length) {
                        new Chart(ctx2, {
                            type: 'doughnut',
                            data: {
                                labels: statusLabels.map(s => s.charAt(0).toUpperCase() + s.slice(1)),
                                datasets: [{
                                    data: safeArray(statusCounts),
                                    backgroundColor: ['#10b981', '#3b82f6', '#ef4444', '#64748b', '#a855f7',
                                        '#ec4899'
                                    ],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'bottom',
                                        labels: {
                                            boxWidth: 12,
                                            padding: 12,
                                            font: {
                                                weight: 600
                                            }
                                        }
                                    }
                                },
                                cutout: '72%'
                            }
                        });
                    }

                    const ctx3 = document.getElementById('customerGrowthChart');
                    if (ctx3 && custGrowth.length) {
                        new Chart(ctx3, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'New accounts',
                                    data: safeArray(custGrowth, labels.length),
                                    borderColor: '#3b82f6',
                                    backgroundColor: 'rgba(59,130,246,0.10)',
                                    tension: 0.3,
                                    borderWidth: 2,
                                    pointRadius: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    x: {
                                        ticks: {
                                            autoSkip: true
                                        }
                                    }
                                }
                            }
                        });
                    }

                    const ctx4 = document.getElementById('productGrowthChart');
                    if (ctx4 && prodGrowth.length) {
                        new Chart(ctx4, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'New products',
                                    data: safeArray(prodGrowth, labels.length),
                                    borderColor: '#d97706',
                                    backgroundColor: 'rgba(217,119,6,0.10)',
                                    tension: 0.3,
                                    borderWidth: 2,
                                    pointRadius: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    x: {
                                        ticks: {
                                            autoSkip: true
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            })();
        </script>
    @endpush

@endsection
