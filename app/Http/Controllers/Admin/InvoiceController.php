<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Area;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    // ----------------------------
    // INDEX
    // ----------------------------
    public function index()
    {
        $invoices = Invoice::with('customer')->latest()->get();

        return view('admin.invoices.index', compact('invoices'));
    }

    // ----------------------------
    // CREATE
    // ----------------------------
    public function create()
    {
        $customers = Customer::where('status', 1)->orderBy('name')->get();
        $areas     = Area::where('status', 1)->orderBy('name')->get();
        $products  = Product::where('status', 1)->orderBy('name')->get();

        $productsData = $products->map(function ($p) {
            return [
                'id'             => $p->id,
                'name'           => $p->name,
                'selling_price'  => $p->selling_price,
                'purchase_price' => $p->purchase_price,
                'vat'            => $p->vat,
                'moq'            => (int) $p->moq,
                'stock'          => (int) $p->qty,
            ];
        });

        return view('admin.invoices.create', compact('customers', 'areas', 'products', 'productsData'));
    }

    // ----------------------------
    // STORE
    // ----------------------------
    public function store(Request $request)
    {
        $request->validate([
            'invoice_number'           => [
                'required',
                'string',
                'max:100',
                'regex:/^INV-[A-Za-z0-9\-]+$/',
                Rule::unique('invoices', 'invoice_number'),
            ],
            'customer_id'              => 'required|exists:customers,id',
            'invoice_date'             => 'required|date|before_or_equal:today',
            'due_date'                 => 'required|date|after_or_equal:invoice_date',
            'status'                   => 'required|in:draft,paid,unpaid,due',
            'products'                 => 'required|array|min:1',
            'products.*.product_id'    => 'required|exists:products,id',
            'products.*.selling_price' => 'required|numeric|min:0',
            'products.*.vat'           => 'required|in:0,20',
            'products.*.qty'           => 'required|integer|min:1',
        ], [
            'invoice_number.required' => 'Invoice number is required.',
            'invoice_number.regex' => 'Invoice number must start with INV- and use only letters, numbers, or hyphens.',
            'invoice_number.unique' => 'This invoice number already exists.',
            'invoice_date.before_or_equal' => 'Invoice date cannot be in the future.',
            'due_date.after_or_equal' => 'Due date cannot be earlier than invoice date.',
        ]);

        // ── Stock / MOQ validation ──────────────────────────────────────────
        foreach ($request->products as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) continue;

            $stock = (int) $product->qty;
            $moq   = (int) $product->moq;
            $qty   = (int) $item['qty'];

            if ($stock <= 0) {
                return response()->json(['success' => false, 'message' => $product->name . ' is out of stock.'], 422);
            }
            if ($qty > $stock) {
                return response()->json(['success' => false, 'message' => $product->name . ' — only ' . $stock . ' unit(s) available in stock.'], 422);
            }
            if ($moq > 0 && $qty < $moq) {
                return response()->json(['success' => false, 'message' => $product->name . ' — minimum order quantity is ' . $moq . '.'], 422);
            }
            if ($moq > 0 && $qty % $moq !== 0) {
                return response()->json(['success' => false, 'message' => $product->name . ' — quantity must be a multiple of ' . $moq . '.'], 422);
            }
        }

        try {
            DB::beginTransaction();

            $invoiceNumber = strtoupper(trim($request->invoice_number));

            // Calculate totals
            $totalVat    = 0;
            $totalAmount = 0;

            foreach ($request->products as $item) {
                $lineTotal    = $item['selling_price'] * $item['qty'];
                $vatAmount    = $lineTotal * ($item['vat'] / 100);
                $totalVat    += $vatAmount;
                $totalAmount += $lineTotal;
            }

            // Save invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'customer_id'    => $request->customer_id,
                'invoice_date'   => $request->invoice_date,
                'due_date'       => $request->due_date,
                'total_vat'      => $totalVat,
                'total_amount'   => $totalAmount,
                'status'         => $request->status,
            ]);

            // Save items & deduct stock
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
    // SHOW
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
                'new_status' => $invoice->fresh()->status,
            ]);
        }

        return back()->with('success', 'Invoice status updated.');
    }

    // ----------------------------
    // DESTROY - Delete invoice (restores stock)
    // ----------------------------
    public function destroy(Request $request, Invoice $invoice)
    {
        try {
            DB::beginTransaction();

            // Restore stock before deleting
            foreach ($invoice->items as $item) {
                Product::where('id', $item->product_id)->increment('qty', $item->qty);
            }

            $invoice->items()->delete();
            $invoice->delete();

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Invoice deleted successfully.']);
            }

            return back()->with('success', 'Invoice deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error deleting invoice.'], 500);
            }

            return back()->with('error', 'Error deleting invoice: ' . $e->getMessage());
        }
    }
}
