<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentInvoiceController extends Controller
{
    // ----------------------------
    // INDEX
    // ----------------------------
    public function index()
    {
        $invoices = Invoice::with('customer')
            ->where('agent_id', auth()->id())
            ->latest()
            ->get();

        return view('agent.invoices.index', compact('invoices'));
    }

    // ----------------------------
    // CREATE
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

        return view('agent.invoices.create', compact('customers', 'products', 'productsData'));
    }

    // ----------------------------
    // STORE - Fixed & Aligned with Schema
    // ----------------------------
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'              => 'required|exists:customers,id',
            'invoice_date'             => 'required|date',
            'due_date'                 => 'required|date|after_or_equal:invoice_date',
            'status'                   => 'required|in:draft,unpaid,paid,due',
            'products'                 => 'required|array|min:1',
            'products.*.product_id'    => 'required|exists:products,id',
            'products.*.qty'           => 'required|integer|min:1',
            'products.*.selling_price' => 'required|numeric|min:0',
            'products.*.vat'           => 'required|in:0,20', // Aligned with admin validation
        ]);

        try {
            DB::beginTransaction();

            // 1. Generate unique invoice number (Keep agent specific pattern or switch to global)
            $today = date('Ymd');
            $lastInvoice = Invoice::where('invoice_number', 'LIKE', "INV-{$today}-%")
                ->orderBy('id', 'desc')
                ->first();

            if ($lastInvoice) {
                $lastNum = explode('-', $lastInvoice->invoice_number);
                $seq = (int) end($lastNum) + 1;
            } else {
                $seq = 1;
            }
            $invoiceNumber = "INV-{$today}-" . str_pad($seq, 4, '0', STR_PAD_LEFT);

            // 2. Initialize totals using standard schema attributes
            $totalVat = 0;
            $totalAmount = 0; // base subtotal amount matching admin schema

            // Stock / MOQ Safeguard loop
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
            }

            // Calculate totals explicitly
            foreach ($request->products as $item) {
                $lineTotal    = $item['selling_price'] * $item['qty'];
                $vatAmount    = $lineTotal * ($item['vat'] / 100);
                $totalVat    += $vatAmount;
                $totalAmount += $lineTotal; 
            }

            // 3. Create invoice — use forceFill so agent_id is always set
            //    even if the Invoice model's $fillable does not list agent_id
            $invoice = new Invoice();
            $invoice->forceFill([
                'invoice_number' => $invoiceNumber,
                'agent_id'       => auth()->id(),
                'customer_id'    => $request->customer_id,
                'invoice_date'   => $request->invoice_date,
                'due_date'       => $request->due_date,
                'total_vat'      => $totalVat,
                'total_amount'   => $totalAmount,
                'status'         => $request->status,
            ])->save();

            // 4. Save items & deduct inventory stock
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

                // Reduce inventory stock level safely
                Product::where('id', $item['product_id'])->decrement('qty', $item['qty']);
            }

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Invoice generated successfully.',
                'redirect_url' => route('agent.invoices.show', $invoice->id) . '?print=1'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An unexpected server error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    // ----------------------------
    // AJAX: QUICK CUSTOMER STORE
    // ----------------------------
    public function storeCustomer(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:customers,email',
            'phone'          => 'nullable|string|max:20',
            'customer_type'  => 'required|in:regular,business',
            'address'        => 'nullable|string',
            'vat_registered' => 'nullable|boolean',
            'vat_number'     => 'nullable|string|max:50',
        ]);

        $customer = Customer::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'customer_type'  => $request->customer_type,
            'address'        => $request->address,
            'vat_registered' => $request->boolean('vat_registered'),
            'vat_number'     => $request->vat_number,
            'status'         => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully.',
            'data'    => $customer,
        ]);
    }

    // ----------------------------
    // SHOW - View + Print invoice for Agent
    // ----------------------------
    public function show(Invoice $invoice)
    {
        // Security Check: Ensure the agent can only view their own invoices
        if ($invoice->agent_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $invoice->load('customer', 'items.product');

        // Setting model agar aap use kar rahe hain company layout ke liye
        // Agar Setting model imported nahi hai to check karlein top par use App\Models\Setting; hai ya nahi
        $settings  = \App\Models\Setting::first(); 
        $autoPrint = request()->query('print') || session('print') ? true : false;

        return view('agent.invoices.show', compact('invoice', 'settings', 'autoPrint'));
    }


    // ----------------------------
    // TOGGLE STATUS - Agent Specific
    // ----------------------------
    public function toggleStatus(Request $request, Invoice $invoice)
    {
        // Security Check: Ensure agent only toggles their own invoice
        if ($invoice->agent_id !== auth()->id()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }
            abort(403, 'Unauthorized action.');
        }

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
    // AJAX: LOOKUP CUSTOMER
    // ----------------------------
    public function lookupCustomer(Request $request)
    {
        $email = $request->query('email');
        if (!$email) return response()->json(['found' => false]);

        $customer = Customer::where('email', $email)->first();
        if ($customer) {
            return response()->json(['found' => true, 'data' => $customer]);
        }

        return response()->json(['found' => false]);
    }
}