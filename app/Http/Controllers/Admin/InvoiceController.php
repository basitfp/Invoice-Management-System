<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    // ----------------------------
    // INDEX - All invoices list
    // ----------------------------
    public function index()
    {
        $invoices = Invoice::with('customer')->latest()->get();

        return view('admin.invoices.index', compact('invoices'));
    }

    // ----------------------------
    // CREATE - Show create form
    // ----------------------------
    public function create()
    {
    $customers = Customer::where('status', 1)->orderBy('name')->get();
    $products  = Product::where('status', 1)->orderBy('name')->get();

    $productsData = $products->map(function ($p) {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'selling_price' => $p->selling_price,
            'vat' => $p->vat,
        ];
    });

    return view('admin.invoices.create', compact('customers', 'products', 'productsData'));
    }

    // ----------------------------
    // STORE - Save new invoice
    // ----------------------------
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'            => 'required|exists:customers,id',
            'invoice_date'           => 'required|date',
            'due_date'               => 'required|date|after_or_equal:today',
            'status'                 => 'required|in:draft,paid,unpaid,due',
            'products'               => 'required|array|min:1',
            'products.*.product_id'  => 'required|exists:products,id',
            'products.*.selling_price' => 'required|numeric|min:0',
            'products.*.vat'         => 'required|in:0,20',
            'products.*.qty'         => 'required|integer|min:1',
        ]);

        // Auto-generate invoice number: INV-0001
        $lastInvoice   = Invoice::latest('id')->first();
        $nextNumber    = $lastInvoice ? ($lastInvoice->id + 1) : 1;
        $invoiceNumber = 'INV-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Totals calculate karo
        $totalVat    = 0;
        $totalAmount = 0;

        foreach ($request->products as $item) {
            $lineTotal    = $item['selling_price'] * $item['qty'];
            $vatAmount    = $lineTotal * ($item['vat'] / 100);
            $totalVat    += $vatAmount;
            $totalAmount += $lineTotal;
        }

        // Invoice save karo
        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'customer_id'    => $request->customer_id,
            'invoice_date'   => $request->invoice_date,
            'due_date'       => $request->due_date,
            'total_vat'      => $totalVat,
            'total_amount'   => $totalAmount,
            'status'         => $request->status,
        ]);

        // Invoice items save karo
        foreach ($request->products as $item) {
            $lineTotal = $item['selling_price'] * $item['qty'];

            InvoiceItem::create([
                'invoice_id'    => $invoice->id,
                'product_id'    => $item['product_id'],
                'selling_price' => $item['selling_price'],
                'vat'           => $item['vat'],
                'qty'           => $item['qty'],
                'line_total'    => $lineTotal,
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success'      => true,
                'message'      => 'Invoice created successfully.',
                'redirect_url' => route('admin.invoices.show', $invoice->id) . '?print=1'
            ]);
        }

        // Save ke baad seedha print preview
        return redirect()->route('admin.invoices.show', $invoice->id)
            ->with('print', true);
    }

    // ----------------------------
    // SHOW - View + Print invoice
    // ----------------------------
    public function show(Invoice $invoice)
    {
        $invoice->load('customer', 'items.product');

        // System settings se company info
        $settings = Setting::first();

        $autoPrint = request()->query('print') || session('print') ? true : false;

        return view('admin.invoices.show', compact('invoice', 'settings', 'autoPrint'));
    }

    // ----------------------------
    // TOGGLE STATUS
    // ----------------------------
    public function toggleStatus(Request $request, Invoice $invoice)
    {
        $request->validate([
            'status' => 'required|in:draft,paid,unpaid,due',
        ]);

        $invoice->update([
            'status' => $request->status,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Invoice status updated.',
                'new_status' => $invoice->status
            ]);
        }

        return back()->with('success', 'Invoice status updated.');
    }

    // ----------------------------
    // DESTROY - Delete invoice
    // ----------------------------
    public function destroy(Request $request, Invoice $invoice)
    {
        $invoice->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully.'
            ]);
        }

        return back()->with('success', 'Invoice deleted successfully.');
    }

    
}