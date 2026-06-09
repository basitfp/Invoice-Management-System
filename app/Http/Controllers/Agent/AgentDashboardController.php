<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class AgentDashboardController extends Controller
{
    public function index()
    {
        $agentId = auth()->id();
        if (! $agentId) {
            abort(403);
        }

        // Optimized aggregates using single query for counts and sums
        $stats = Invoice::where('agent_id', $agentId)
            ->selectRaw('
                COUNT(id) as total_invoices,
                SUM(CASE WHEN status = "paid" THEN total_amount + total_vat ELSE 0 END) as paid_revenue,
                SUM(CASE WHEN status IN ("unpaid", "due") THEN total_amount + total_vat ELSE 0 END) as pending_revenue,
                SUM(total_amount + total_vat) as total_revenue
            ')
            ->first();

        $totalInvoices = $stats->total_invoices ?? 0;
        $totalRevenue = $stats->total_revenue ?? 0;
        $paidRevenue = $stats->paid_revenue ?? 0;
        $pendingRevenue = $stats->pending_revenue ?? 0;

        // -----------------------------
        // Premium analytics for agent (real data)
        // -----------------------------
        // Use existing invoice schema fields: invoice_date, status, total_amount, total_vat.
        $monthsBack = 6;

        $monthLabels = [];
        for ($i = $monthsBack - 1; $i >= 0; $i--) {
            $monthLabels[] = now()->subMonths($i)->format('M Y');
        }

        $paidSeries = [];
        $pendingSeries = [];

        // Aggregate paid vs pending per month for this agent.
        // If invoice_date is a DATE, DATE_FORMAT works on MySQL. (project uses mysql in deployment.)
        $invoiceAgg = Invoice::query()
            ->select([
                DB::raw("DATE_FORMAT(invoice_date, '%Y-%m') as ym"),
                'status',
                DB::raw('SUM(total_amount + total_vat) as total'),
            ])
            ->where('agent_id', $agentId)
            ->whereNotNull('invoice_date')
            ->where('invoice_date', '>=', now()->subMonths($monthsBack - 1)->startOfMonth())
            ->groupBy(DB::raw("DATE_FORMAT(invoice_date, '%Y-%m')"), 'status')
            ->get();

        $invoiceAggMap = [];
        foreach ($invoiceAgg as $row) {
            $ym = $row->ym;
            $invoiceAggMap[$ym][$row->status] = (float) ($row->total ?? 0);
        }

        $statusToIsPaid = fn ($s) => $s === 'paid';
        $statusToIsPending = fn ($s) => in_array($s, ['draft', 'sent', 'unpaid', 'due', 'cancelled'], true) && $s !== 'paid';

        for ($i = $monthsBack - 1; $i >= 0; $i--) {
            $ym = now()->subMonths($i)->format('Y-m');
            $paidTotal = 0.0;
            $pendingTotal = 0.0;

            if (isset($invoiceAggMap[$ym])) {
                foreach ($invoiceAggMap[$ym] as $status => $total) {
                    if ($statusToIsPaid($status)) {
                        $paidTotal += (float) $total;
                    } elseif ($statusToIsPending($status)) {
                        $pendingTotal += (float) $total;
                    }
                }
            }

            $paidSeries[] = round($paidTotal, 2);
            $pendingSeries[] = round($pendingTotal, 2);
        }

        // Status breakdown for this agent.
        $statusCountsRows = Invoice::query()
            ->select('status', DB::raw('COUNT(*) as cnt'))
            ->where('agent_id', $agentId)
            ->groupBy('status')
            ->get();

        $invoiceStatusCounts = [];
        foreach ($statusCountsRows as $row) {
            $invoiceStatusCounts[] = [
                'status' => $row->status,
                'count' => (int) $row->cnt,
            ];
        }

        $statusOrder = ['paid', 'unpaid', 'due', 'draft', 'sent', 'cancelled'];
        $statusLabels = [];
        $statusCounts = [];
        foreach ($statusOrder as $st) {
            $foundCount = 0;
            foreach ($invoiceStatusCounts as $row) {
                if (($row['status'] ?? null) === $st) {
                    $foundCount = (int) ($row['count'] ?? 0);
                    break;
                }
            }
            // This will now push all status labels and their 0 counts safely
            $statusLabels[] = ucfirst($st);
            $statusCounts[] = $foundCount;
            // if ($foundCount !== null) {
            //     $statusLabels[] = ucfirst($st);
            //     $statusCounts[] = $foundCount;
            // }
        }

        if (count($statusLabels) === 0) {
            foreach ($invoiceStatusCounts as $row) {
                $statusLabels[] = ucfirst((string) ($row['status'] ?? ''));
                $statusCounts[] = (int) ($row['count'] ?? 0);
            }
        }

        $recentInvoices = Invoice::with('customer')
            ->where('agent_id', $agentId)
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('agent.dashboard', compact(
            'totalInvoices',
            'totalRevenue',
            'paidRevenue',
            'pendingRevenue',
            'recentInvoices',
            'monthLabels',
            'paidSeries',
            'pendingSeries',
            'statusLabels',
            'statusCounts'
        ));
    }
}
