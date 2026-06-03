<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;

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
            ->sum(function($invoice) {
                return $invoice->total_amount + $invoice->total_vat;
            });
            
        $pendingRevenue = Invoice::whereIn('status', ['unpaid', 'due'])
            ->get()
            ->sum(function($invoice) {
                return $invoice->total_amount + $invoice->total_vat;
            });

        $recentInvoices = Invoice::with('customer')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalCustomers', 
            'totalProducts', 
            'totalInvoices', 
            'totalRevenue', 
            'pendingRevenue',
            'recentInvoices'
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
