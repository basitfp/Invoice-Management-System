<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('agent.invoices.index');
    }
}
