<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    // ----------------------------
    // INDEX - Show all products
    // ----------------------------
    public function index()
    {
        $products   = Product::with('category')->latest()->get();
        $categories = Category::where('status', 1)->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    // ----------------------------
    // STORE - Save new product
    // ----------------------------
    public function store(Request $request)
    {
        $request->validate([
            'name'           => ['required', 'string', 'min:2', 'max:150', Rule::unique('products', 'name')],
            'description'    => ['nullable', 'string'],
            'category_id'    => ['required', 'exists:categories,id'],
            'qty'            => ['required', 'integer', 'min:0'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price'  => ['required', 'numeric', 'min:0', 'gte:purchase_price'],
            'vat'            => ['required', 'in:0,20'],
            'moq'            => ['required', 'integer', 'min:1'],
            'status'         => ['nullable', 'boolean'],
        ]);

        $product = Product::create([
            'name'           => $request->name,
            'description'    => $request->description,
            'category_id'    => $request->category_id,
            'qty'            => $request->qty,
            'purchase_price' => $request->purchase_price,
            'selling_price'  => $request->selling_price,
            'vat'            => $request->vat,
            'moq'            => $request->moq,
            'status'         => $request->has('status') ? (int) $request->status : 1,
        ]);

        $product->load('category');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'data'    => $product
            ]);
        }

        return back()->with('success', 'Product created successfully.');
    }

    // ----------------------------
    // UPDATE - Edit existing product
    // ----------------------------
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'           => ['required', 'string', 'min:2', 'max:150', Rule::unique('products', 'name')->ignore($product->id)],
            'description'    => ['nullable', 'string'],
            'category_id'    => ['required', 'exists:categories,id'],
            'qty'            => ['required', 'integer', 'min:0'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price'  => ['required', 'numeric', 'min:0', 'gte:purchase_price'],
            'vat'            => ['required', 'in:0,20'],
            'moq'            => ['required', 'integer', 'min:1'],
            'status'         => ['nullable', 'boolean'],
        ]);

        $product->update([
            'name'           => $request->name,
            'description'    => $request->description,
            'category_id'    => $request->category_id,
            'qty'            => $request->qty,
            'purchase_price' => $request->purchase_price,
            'selling_price'  => $request->selling_price,
            'vat'            => $request->vat,
            'moq'            => $request->moq,
            'status'         => $request->status ?? $product->status,
        ]);

        $product->load('category');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'data'    => $product
            ]);
        }

        return back()->with('success', 'Product updated successfully.');
    }

    // ----------------------------
    // TOGGLE STATUS - Enable / Disable
    // ----------------------------
    public function toggleStatus(Request $request, Product $product)
    {
        $product->update([
            'status' => $product->status ? 0 : 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Product status updated.',
                'new_status' => $product->status
            ]);
        }

        return back()->with('success', 'Product status updated.');
    }

    // ----------------------------
    // DESTROY - Delete product
    // ----------------------------
    public function destroy(Request $request, Product $product)
    {
        $product->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully.'
            ]);
        }

        return back()->with('success', 'Product deleted successfully.');
    }
}