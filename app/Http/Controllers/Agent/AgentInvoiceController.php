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
    // STORE
    // ----------------------------
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'  => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'due_date'     => 'required|date|after_or_equal:invoice_date',
            'status'       => 'required|in:draft,unpaid,paid,cancelled',
            'products'     => 'required|array|min:1',
            'products.*.product_id'    => 'required|exists:products,id',
            'products.*.qty'           => 'required|integer|min:1',
            'products.*.selling_price' => 'required|numeric|min:0',
            'products.*.vat'           => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // 1. Generate unique invoice number
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

            // 2. Initialise totals
            $totalExclVat = 0;
            $totalVat = 0;

            // Temp items placeholder array
            $itemsToSave = [];

            foreach ($request->products as $pData) {
                $product = Product::find($pData['product_id']);

                // Backend Safeguard: Check actual current stock
                if ($product->qty < (int)$pData['qty']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for product: {$product->name}. Current stock: {$product->qty}"
                    ], 422);
                }

                // Backend Safeguard: Check MOQ constraints
                if ((int)$pData['qty'] < (int)$product->moq) {
                    return response()->json([
                        'success' => false,
                        'message' => "Product '{$product->name}' does not meet the Minimum Order Quantity of {$product->moq} units."
                    ], 422);
                }

                $qty = (int) $pData['qty'];
                $sellingPrice = (float) $pData['selling_price'];
                $vatPercent = (float) $pData['vat'];

                $subTotal = $qty * $sellingPrice;
                $vatAmount = $subTotal * ($vatPercent / 100);

                $totalExclVat += $subTotal;
                $totalVat     += $vatAmount;

                $itemsToSave[] = [
                    'product_id'     => $product->id,
                    'qty'            => $qty,
                    'purchase_price' => $product->purchase_price, // snapshot metrics
                    'selling_price'  => $sellingPrice,
                    'vat_percent'    => $vatPercent,
                    'sub_total'      => $subTotal,
                    'vat_amount'     => $vatAmount,
                ];
            }

            $grandTotal = $totalExclVat + $totalVat;

            // 3. Create the Parent Invoice entry
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'agent_id'       => auth()->id(),
                'customer_id'    => $request->customer_id,
                'invoice_date'   => $request->invoice_date,
                'due_date'       => $request->due_date,
                'total_excl_vat' => $totalExclVat,
                'total_vat'      => $totalVat,
                'grand_total'    => $grandTotal,
                'status'         => $request->status,
            ]);

            // 4. Save snapshot child line items & update active inventories
            foreach ($itemsToSave as $item) {
                $invoice->items()->create($item);

                // Reduce inventory stock levels
                $prod = Product::find($item['product_id']);
                $prod->decrement('qty', $item['qty']);
            }

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Invoice generated successfully.',
                'redirect_url' => route('agent.invoices.index')
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