<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                'id'             => $p->id,
                'name'           => $p->name,
                'selling_price'  => $p->selling_price,
                'purchase_price' => $p->purchase_price,
                'vat'            => $p->vat,
                'moq'            => (int) $p->moq,
                'stock'          => (int) $p->qty,   // current available stock
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
            'customer_id'              => 'required|exists:customers,id',
            'invoice_date'             => 'required|date',
            'due_date'                 => 'required|date|after_or_equal:today',
            'status'                   => 'required|in:draft,paid,unpaid,due',
            'products'                 => 'required|array|min:1',
            'products.*.product_id'    => 'required|exists:products,id',
            'products.*.selling_price' => 'required|numeric|min:0',
            'products.*.vat'           => 'required|in:0,20',
            'products.*.qty'           => 'required|integer|min:1',
        ]);

        // ── Stock / MOQ validation ──────────────────────────────────────────
        foreach ($request->products as $item) {
            $product = Product::find($item['product_id']);

            if (!$product) continue;

            $stock = (int) $product->qty;
            $moq   = (int) $product->moq;
            $qty   = (int) $item['qty'];

            if ($stock <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => $product->name . ' is out of stock.',
                ], 422);
            }

            if ($qty > $stock) {
                return response()->json([
                    'success' => false,
                    'message' => $product->name . ' — only ' . $stock . ' unit(s) available in stock.',
                ], 422);
            }

            if ($moq > 0 && $qty < $moq) {
                return response()->json([
                    'success' => false,
                    'message' => $product->name . ' — minimum order quantity is ' . $moq . '.',
                ], 422);
            }

            if ($moq > 0 && $qty % $moq !== 0) {
                return response()->json([
                    'success' => false,
                    'message' => $product->name . ' — quantity must be a multiple of ' . $moq . '.',
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            // Auto-generate invoice number
            $lastInvoice   = Invoice::latest('id')->first();
            $nextNumber    = $lastInvoice ? ($lastInvoice->id + 1) : 1;
            $invoiceNumber = 'INV-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // ── Calculate totals ────────────────────────────────────────────
            $totalVat    = 0;
            $totalAmount = 0;

            foreach ($request->products as $item) {
                $lineTotal    = $item['selling_price'] * $item['qty'];
                $vatAmount    = $lineTotal * ($item['vat'] / 100);
                $totalVat    += $vatAmount;
                $totalAmount += $lineTotal;
            }

            // ── Save invoice ────────────────────────────────────────────────
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'customer_id'    => $request->customer_id,
                'invoice_date'   => $request->invoice_date,
                'due_date'       => $request->due_date,
                'total_vat'      => $totalVat,
                'total_amount'   => $totalAmount,
                'status'         => $request->status,
            ]);

            // ── Save items & deduct stock ───────────────────────────────────
            foreach ($request->products as $item) {
                $product   = Product::find($item['product_id']);
                $lineTotal = $item['selling_price'] * $item['qty'];

                InvoiceItem::create([
                    'invoice_id'    => $invoice->id,
                    'product_id'    => $item['product_id'],
                    'selling_price' => $item['selling_price'],
                    'vat'           => $item['vat'],
                    'qty'           => $item['qty'],
                    'line_total'    => $lineTotal,
                ]);

                // Deduct from stock
                $product->decrement('qty', $item['qty']);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success'      => true,
                    'message'      => 'Invoice created successfully.',
                    'redirect_url' => route('admin.invoices.show', $invoice->id) . '?print=1',
                ]);
            }

            return redirect()->route('admin.invoices.show', $invoice->id)->with('print', true);

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }

            return back()->with('error', 'Error creating invoice: ' . $e->getMessage());
        }
    }

    // ----------------------------
    // SHOW - View + Print invoice
    // ----------------------------
    public function show(Invoice $invoice)
    {
        $invoice->load('customer', 'items.product');

        $settings  = Setting::first();
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

        $invoice->update(['status' => $request->status]);

        if ($request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Invoice status updated.',
                'new_status' => $invoice->status,
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
            return response()->json(['success' => true, 'message' => 'Invoice deleted successfully.']);
        }

        return back()->with('success', 'Invoice deleted successfully.');
    }
}