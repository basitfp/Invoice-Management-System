<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function adminDashboard()
    {
        $totalCustomers = Customer::count();
        $totalProducts  = Product::count();
        $totalInvoices  = Invoice::count();

        $totalRevenue = Invoice::where('status', 'paid')
            ->get()
            ->sum(function ($invoice) {
                return $invoice->total_amount + $invoice->total_vat;
            });

        $pendingRevenue = Invoice::whereIn('status', ['unpaid', 'due'])
            ->get()
            ->sum(function ($invoice) {
                return $invoice->total_amount + $invoice->total_vat;
            });

        $recentInvoices = Invoice::with('customer')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $settings = Setting::first();

        // -----------------------------
        // Premium analytics (real data)
        // -----------------------------
        // Use only existing schema fields:
        // - invoices: invoice_date, total_amount, total_vat, status
        // - customers/products: created_at, status/name
        $monthsBack = 6; // keep lightweight + meaningful

        // Month labels aligned to invoice_date
        $monthLabels = [];
        for ($i = $monthsBack - 1; $i >= 0; $i--) {
            $monthLabels[] = now()->subMonths($i)->format('M Y');
        }

        // Revenue trend: paid vs pending (treat sent/draft/etc as pending)
        $paidSeries = [];
        $pendingSeries = [];
        $monthlyInvoiceTotals = [];

        $statusToIsPaid = fn ($s) => $s === 'paid';
        $statusToIsPending = fn ($s) => in_array($s, ['draft', 'sent', 'unpaid', 'due', 'cancelled'], true) && $s !== 'paid';

        // Fetch aggregates in one query grouped by month+status.
        // Note: invoice_date is a DATE field.
        $invoiceAgg = Invoice::query()
            ->select([
                DB::raw("DATE_FORMAT(invoice_date, '%Y-%m') as ym"),
                'status',
                DB::raw('SUM(total_amount + total_vat) as total'),
                DB::raw('COUNT(*) as cnt'),
            ])
            ->whereNotNull('invoice_date')
            ->where('invoice_date', '>=', now()->subMonths($monthsBack - 1)->startOfMonth())
            ->groupBy(DB::raw("DATE_FORMAT(invoice_date, '%Y-%m')"), 'status')
            ->get();

        // Build map: [ym][status] => ['total'=>..,'cnt'=>..]

        $invoiceAggMap = [];
        foreach ($invoiceAgg as $row) {
            $ym = $row->ym;
            $invoiceAggMap[$ym][$row->status] = [
                'total' => (float) $row->total,
                'cnt' => (int) $row->cnt,
            ];
        }

        for ($i = $monthsBack - 1; $i >= 0; $i--) {
            $ym = now()->subMonths($i)->format('Y-m');

            $paidTotal = 0.0;
            $pendingTotal = 0.0;
            $monthTotal = 0.0;

            if (isset($invoiceAggMap[$ym])) {
                foreach ($invoiceAggMap[$ym] as $status => $vals) {
                    $monthTotal += $vals['total'];

                    if ($statusToIsPaid($status)) {
                        $paidTotal += $vals['total'];
                    } elseif ($statusToIsPending($status)) {
                        $pendingTotal += $vals['total'];
                    }
                }
            }

            $paidSeries[] = round($paidTotal, 2);
            $pendingSeries[] = round($pendingTotal, 2);
            $monthlyInvoiceTotals[] = (int) round($monthTotal, 0);
        }

        // Invoice status breakdown for current totals (real status counts)
        // Avoid Collection helper methods to reduce IDE/static-analysis warnings.
        $invoiceStatusCountsRows = Invoice::query()
            ->select('status', DB::raw('COUNT(*) as cnt'))
            ->groupBy('status')
            ->get();

        $invoiceStatusCounts = [];
        foreach ($invoiceStatusCountsRows as $row) {
            $invoiceStatusCounts[] = [
                'status' => $row->status,
                'count'  => (int) $row->cnt,
            ];
        }

        $statusOrder = ['paid', 'unpaid', 'due', 'draft', 'sent', 'cancelled'];
        $statusLabels = [];
        $statusCounts = [];

        foreach ($statusOrder as $st) {
            $foundCount = null;
            foreach ($invoiceStatusCounts as $row) {
                if (($row['status'] ?? null) === $st) {
                    $foundCount = (int) ($row['count'] ?? 0);
                    break;
                }
            }
            if ($foundCount !== null) {
                $statusLabels[] = ucfirst($st);
                $statusCounts[] = $foundCount;
            }
        }

        // If schema has different statuses than expected, fall back to whatever exists
        if (count($statusLabels) === 0) {
            foreach ($invoiceStatusCounts as $row) {
                $statusLabels[] = ucfirst((string) ($row['status'] ?? ''));
                $statusCounts[] = (int) ($row['count'] ?? 0);
            }
        }


        // Customer growth (new customers per month)
        $customerGrowthSeries = [];
        $customerAgg = Customer::query()
            ->select([DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"), DB::raw('COUNT(*) as cnt')])
            ->where('created_at', '>=', now()->subMonths($monthsBack - 1)->startOfMonth())
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->get();

        $customerAggMap = [];
        foreach ($customerAgg as $row) {
            $customerAggMap[$row->ym] = (int) $row->cnt;
        }

        for ($i = $monthsBack - 1; $i >= 0; $i--) {
            $ym = now()->subMonths($i)->format('Y-m');
            $customerGrowthSeries[] = $customerAggMap[$ym] ?? 0;
        }

        // Product growth (new products per month)
        $productGrowthSeries = [];
        $productAgg = Product::query()
            ->select([DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"), DB::raw('COUNT(*) as cnt')])
            ->where('created_at', '>=', now()->subMonths($monthsBack - 1)->startOfMonth())
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->get();

        $productAggMap = [];
        foreach ($productAgg as $row) {
            $productAggMap[$row->ym] = (int) $row->cnt;
        }

        for ($i = $monthsBack - 1; $i >= 0; $i--) {
            $ym = now()->subMonths($i)->format('Y-m');
            $productGrowthSeries[] = $productAggMap[$ym] ?? 0;
        }

        // ── TOP CUSTOMERS FIX BLOCK ──
        $topCustomers = [];
        $topCustomerRows = Invoice::query()
            ->join('customers', 'invoices.customer_id', '=', 'customers.id') // <--- Customers table join kiya
            ->select([
                'invoices.customer_id',
                'customers.name',   // <--- Name select kiya
                'customers.email',  // <--- Email select kiya
                DB::raw('SUM(invoices.total_amount + invoices.total_vat) as total_spent'), // <--- Total custom name diya jo view me chahiye
                DB::raw('COUNT(invoices.id) as paid_invoices'),
            ])
            ->where('invoices.status', 'paid')
            ->groupBy('invoices.customer_id', 'customers.name', 'customers.email') // <--- Group by rules fixed
            ->orderByDesc(DB::raw('SUM(invoices.total_amount + invoices.total_vat)'))
            ->limit(5)
            ->get();

        // Eager load customer names
        $customerIds = $topCustomerRows->pluck('customer_id')->filter()->values()->all();
        $customersById = [];
        if (count($customerIds) > 0) {
            $customersById = Customer::query()
                ->whereIn('id', $customerIds)
                ->get()
                ->keyBy('id');
        }

       foreach ($topCustomerRows as $row) {
            $topCustomers[] = [
                'customer_id'   => (int) $row->customer_id,
                'name'          => $row->name ?? 'Customer',
                'email'         => $row->email ?? 'No email address', // <--- Ab email yahan real aayega
                'total_spent'   => round((float) $row->total_spent, 2),
                'paid_invoices' => (int) $row->paid_invoices,
            ];
        }

        // Recent activity: newest invoices + newest customers + newest products (no dummy data)
        $recentCustomers = Customer::query()
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get(['id', 'name', 'created_at']);

        $recentProducts = Product::query()
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get(['id', 'name', 'created_at']);

        $activityInvoices = Invoice::with('customer')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get(['id', 'invoice_number', 'customer_id', 'invoice_date', 'total_amount', 'total_vat', 'status', 'created_at']);

        // Merge into one list and sort by created_at desc
        $recentActivity = collect()
            ->merge(
                $activityInvoices->map(function ($inv) {
                    return (object) [
                        'type' => 'invoice',
                        'title' => $inv->invoice_number,
                        'meta' => ($inv->customer?->name ?? 'Customer') . ' • ' . ($inv->invoice_date ? (is_object($inv->invoice_date) && method_exists($inv->invoice_date, 'format') ? $inv->invoice_date->format('d M Y') : $inv->invoice_date) : '—'),
                        'value' => '£' . number_format((float) ($inv->total_amount + $inv->total_vat), 2),
                        'status' => $inv->status,
                        'created_at' => $inv->created_at,
                    ];
                })
            )
            ->merge(
                $recentCustomers->map(function ($c) {
                    return (object) [
                        'type' => 'customer',
                        'title' => $c->name,
                        'meta' => 'New customer',
                        'value' => null,
                        'status' => null,
                        'created_at' => $c->created_at,
                    ];
                })
            )
            ->merge(
                $recentProducts->map(function ($p) {
                    return (object) [
                        'type' => 'product',
                        'title' => $p->name,
                        'meta' => 'New product',
                        'value' => null,
                        'status' => null,
                        'created_at' => $p->created_at,
                    ];
                })
            )
            ->sortByDesc('created_at')
            ->take(8)
            ->values();

        return view('admin.dashboard', compact(
            'totalCustomers',
            'totalProducts',
            'totalInvoices',
            'totalRevenue',
            'pendingRevenue',
            'recentInvoices',
            'settings',
            'monthLabels',
            'paidSeries',
            'pendingSeries',
            'monthlyInvoiceTotals',
            'statusLabels',
            'statusCounts',
            'customerGrowthSeries',
            'productGrowthSeries',
            'topCustomers',
            'recentActivity'
        ));
    }

    /**
     * Show the agent dashboard.
     */
    public function agentDashboard()
    {
        return view('agent.dashboard');
    }
}
