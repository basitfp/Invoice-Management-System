<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function adminDashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Show the agent dashboard.
     */
    public function agentDashboard()
    {
        return view('agent.dashboard');
    }
}
