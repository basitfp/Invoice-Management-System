<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Invoice; 
use App\Models\User;
use App\Models\Customer;


class AgentDashboardController extends Controller
{
    public function index()
    {
        $agentId = auth()->id();

        // Optimized aggregates using single query for counts and sums
        $stats = Invoice::where('agent_id', $agentId)
            ->selectRaw('
                COUNT(id) as total_invoices,
                SUM(CASE WHEN status = "paid" THEN total_amount + total_vat ELSE 0 END) as paid_revenue,
                SUM(CASE WHEN status IN ("unpaid", "due") THEN total_amount + total_vat ELSE 0 END) as pending_revenue,
                SUM(total_amount + total_vat) as total_revenue
            ')
            ->first();

        $totalInvoices  = $stats->total_invoices ?? 0;
        $totalRevenue   = $stats->total_revenue ?? 0;
        $paidRevenue    = $stats->paid_revenue ?? 0;
        $pendingRevenue = $stats->pending_revenue ?? 0;

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
            'recentInvoices'
        ));
    }
}
