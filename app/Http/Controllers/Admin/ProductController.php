<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        $categories = Category::where('status', 1)->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:150',
                Rule::unique('products', 'name'),
            ],
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'qty' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:purchase_price',
            'vat' => 'required|in:0,20',
            'moq' => 'required|integer|min:1',
            'status' => 'nullable|boolean',
        ]);

        Product::create($request->all());

        return back()->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:150',
                Rule::unique('products', 'name')->ignore($product->id),
            ],
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'qty' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:purchase_price',
            'vat' => 'required|in:0,20',
            'moq' => 'required|integer|min:1',
        ]);

        $product->update($request->all());

        return back()->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->update(['status' => 0]);

        return back()->with('success', 'Product disabled successfully.');
    }

    public function toggleStatus(Product $product)
    {
        $product->update([
            'status' => !$product->status
        ]);

        return back()->with('success', 'Product status updated.');
    }
}