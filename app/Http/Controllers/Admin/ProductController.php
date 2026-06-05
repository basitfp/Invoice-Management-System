<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products      = Product::with(['category', 'manufacturer'])->latest()->get();
        $categories    = Category::where('status', 1)->orderBy('name')->get();
        $manufacturers = Manufacturer::where('status', 1)->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'manufacturers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image'                  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'name'                   => ['required', 'string', 'min:2', 'max:150', Rule::unique('products', 'name')],
            'item_code'              => ['nullable', 'string', 'max:100', Rule::unique('products', 'item_code')],
            'category_id'            => ['required', 'exists:categories,id'],
            'manufacturer_id'        => ['nullable', 'exists:manufacturers,id'],
            'item_class'             => ['required', 'string', Rule::in(['general', 'sale_only', 'raw_material'])],
            'hsn_code'               => ['nullable', 'string', 'max:50'],
            'regional_name'          => ['nullable', 'string', 'max:255'],
            'unit'                   => ['nullable', 'string', 'max:50'],
            
            // Purchase Section
            'purchase_price'         => ['required', 'numeric', 'min:0'],
            'purchase_tax_percent'   => ['required', 'numeric', 'min:0', 'max:100'],
            'purchase_tax_inclusive' => ['boolean'],
            
            // Sale Section
            'sale_price'             => ['required', 'numeric', 'min:0', 'gte:purchase_price'],
            'gst_vat_percent'        => ['required', 'numeric', 'min:0', 'max:100'],
            'sale_tax_inclusive'     => ['boolean'],
            
            'discount_percent'       => ['nullable', 'numeric', 'min:0', 'max:100'],
            'cess_percent'           => ['nullable', 'numeric', 'min:0', 'max:100'],
            'additional_cess'        => ['nullable', 'numeric', 'min:0'],
            'is_weighing_item'       => ['boolean'],
            'qty'                    => ['required', 'integer', 'min:0'],
            'moq'                    => ['required', 'integer', 'min:1'],
            'description'            => ['nullable', 'string'],
            'status'                 => ['nullable', 'boolean'],
        ]);

        $data = $request->except(['image']);

        // Handle structural boolean presence flags
        $data['purchase_tax_inclusive'] = $request->has('purchase_tax_inclusive');
        $data['sale_tax_inclusive']     = $request->has('sale_tax_inclusive');
        $data['is_weighing_item']       = $request->has('is_weighing_item');
        $data['status']                 = $request->has('status') ? (int)$request->status : 1;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        $product->load(['category', 'manufacturer']);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'data'    => $product
            ]);
        }

        return back()->with('success', 'Product created successfully.');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'image'                  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'name'                   => ['required', 'string', 'min:2', 'max:150', Rule::unique('products', 'name')->ignore($product->id)],
            'item_code'              => ['nullable', 'string', 'max:100', Rule::unique('products', 'item_code')->ignore($product->id)],
            'category_id'            => ['required', 'exists:categories,id'],
            'manufacturer_id'        => ['nullable', 'exists:manufacturers,id'],
            'item_class'             => ['required', 'string', Rule::in(['general', 'sale_only', 'raw_material'])],
            'hsn_code'               => ['nullable', 'string', 'max:50'],
            'regional_name'          => ['nullable', 'string', 'max:255'],
            'unit'                   => ['nullable', 'string', 'max:50'],
            
            // Purchase Section
            'purchase_price'         => ['required', 'numeric', 'min:0'],
            'purchase_tax_percent'   => ['required', 'numeric', 'min:0', 'max:100'],
            'purchase_tax_inclusive' => ['boolean'],
            
            // Sale Section
            'sale_price'             => ['required', 'numeric', 'min:0', 'gte:purchase_price'],
            'gst_vat_percent'        => ['required', 'numeric', 'min:0', 'max:100'],
            'sale_tax_inclusive'     => ['boolean'],
            
            'discount_percent'       => ['nullable', 'numeric', 'min:0', 'max:100'],
            'cess_percent'           => ['nullable', 'numeric', 'min:0', 'max:100'],
            'additional_cess'        => ['nullable', 'numeric', 'min:0'],
            'is_weighing_item'       => ['boolean'],
            'qty'                    => ['required', 'integer', 'min:0'],
            'moq'                    => ['required', 'integer', 'min:1'],
            'description'            => ['nullable', 'string'],
            'status'                 => ['nullable', 'boolean'],
        ]);

        $data = $request->except(['image']);

        // Explicitly handle updates for HTML inputs that omit unselected checkboxes
        $data['purchase_tax_inclusive'] = $request->has('purchase_tax_inclusive');
        $data['sale_tax_inclusive']     = $request->has('sale_tax_inclusive');
        $data['is_weighing_item']       = $request->has('is_weighing_item');
        $data['status']                 = $request->has('status');

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        $product->load(['category', 'manufacturer']);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'data'    => $product
            ]);
        }

        return back()->with('success', 'Product updated successfully.');
    }

    public function toggleStatus(Request $request, Product $product)
    {
        $product->update(['status' => !$product->status]);

        if ($request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Product status updated.',
                'new_status' => (int) $product->status
            ]);
        }

        return back()->with('success', 'Product status updated.');
    }

    public function destroy(Request $request, Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
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