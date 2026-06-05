<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Manufacturer;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function index()
    {
        $products      = Product::with(['category', 'manufacturer'])->latest()->get();
        $categories    = Category::where('status', 1)->orderBy('name')->get();
        $manufacturers = Manufacturer::where('status', 1)->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'manufacturers'));
    }

    // -------------------------------------------------------------------------
    // Store
    // -------------------------------------------------------------------------

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name'                   => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9\s]+$/'],
                'item_code'              => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z0-9\-_]+$/', 'unique:products,item_code'],
                'item_class'             => ['required', Rule::in(['general', 'sale_only', 'raw_material'])],
                'category_id'            => ['required', 'exists:categories,id'],
                'manufacturer_id'        => ['nullable', 'exists:manufacturers,id'],
                'hsn_code'               => ['nullable', 'string', 'max:50'],
                'regional_name'          => ['nullable', 'string', 'max:255'],
                'unit'                   => ['nullable', 'string', 'max:50'],
                'purchase_price'         => ['nullable', 'numeric', 'min:0'],
                'purchase_tax_percent'   => ['nullable', 'numeric', 'min:0', 'max:100'],
                'purchase_tax_inclusive' => ['nullable', Rule::in(['0', '1'])],
                'sale_price'             => ['required', 'numeric', 'min:0'],
                'gst_vat_percent'        => ['nullable', 'numeric', 'min:0', 'max:100'],
                'sale_tax_inclusive'     => ['nullable', Rule::in(['0', '1'])],
                'discount_percent'       => ['nullable', 'numeric', 'min:0', 'max:100'],
                'cess_percent'           => ['nullable', 'numeric', 'min:0', 'max:100'],
                'additional_cess'        => ['nullable', 'numeric', 'min:0'],
                'is_weighing_item'       => ['nullable', Rule::in(['0', '1'])],
                'qty'                    => ['required', 'integer', 'min:0'],
                'moq'                    => ['required', 'integer', 'min:1'],
                'status'                 => ['nullable', Rule::in(['0', '1'])],
                'description'            => ['nullable', 'string'],
                'image'                  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ], $this->validationMessages());

            // Cross-field: sale > purchase
            $purchasePrice = (float) ($request->purchase_price ?? 0);
            $salePrice     = (float) $request->sale_price;
            if ($purchasePrice > 0 && $salePrice <= $purchasePrice) {
                return response()->json([
                    'success' => false,
                    'errors'  => ['sale_price' => ['Sale Price must be greater than Purchase Price.']],
                ], 422);
            }

            // Cross-field: moq <= qty
            if ((int) $request->moq > (int) $request->qty) {
                return response()->json([
                    'success' => false,
                    'errors'  => ['moq' => ['MOQ cannot be greater than available Quantity.']],
                ], 422);
            }

            // Image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
            }

            $product = Product::create($this->buildProductData($request, $imagePath));
            $product->load(['category', 'manufacturer']);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'product' => $this->formatProduct($product),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
                'message' => 'Please fix the highlighted errors.',
            ], 422);

        } catch (\Exception $e) {
            Log::error('Product store error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // -------------------------------------------------------------------------
    // Update
    // -------------------------------------------------------------------------

    public function update(Request $request, Product $product): JsonResponse
    {
        try {
            $request->validate([
                'name'                   => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9\s]+$/'],
                'item_code'              => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z0-9\-_]+$/', Rule::unique('products', 'item_code')->ignore($product->id)],
                'item_class'             => ['required', Rule::in(['general', 'sale_only', 'raw_material'])],
                'category_id'            => ['required', 'exists:categories,id'],
                'manufacturer_id'        => ['nullable', 'exists:manufacturers,id'],
                'hsn_code'               => ['nullable', 'string', 'max:50'],
                'regional_name'          => ['nullable', 'string', 'max:255'],
                'unit'                   => ['nullable', 'string', 'max:50'],
                'purchase_price'         => ['nullable', 'numeric', 'min:0'],
                'purchase_tax_percent'   => ['nullable', 'numeric', 'min:0', 'max:100'],
                'purchase_tax_inclusive' => ['nullable', Rule::in(['0', '1'])],
                'sale_price'             => ['required', 'numeric', 'min:0'],
                'gst_vat_percent'        => ['nullable', 'numeric', 'min:0', 'max:100'],
                'sale_tax_inclusive'     => ['nullable', Rule::in(['0', '1'])],
                'discount_percent'       => ['nullable', 'numeric', 'min:0', 'max:100'],
                'cess_percent'           => ['nullable', 'numeric', 'min:0', 'max:100'],
                'additional_cess'        => ['nullable', 'numeric', 'min:0'],
                'is_weighing_item'       => ['nullable', Rule::in(['0', '1'])],
                'qty'                    => ['required', 'integer', 'min:0'],
                'moq'                    => ['required', 'integer', 'min:1'],
                'status'                 => ['nullable', Rule::in(['0', '1'])],
                'description'            => ['nullable', 'string'],
                'image'                  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
                'remove_image'           => ['nullable', Rule::in(['0', '1'])],
            ], $this->validationMessages());

            // Cross-field: sale > purchase
            $purchasePrice = (float) ($request->purchase_price ?? 0);
            $salePrice     = (float) $request->sale_price;
            if ($purchasePrice > 0 && $salePrice <= $purchasePrice) {
                return response()->json([
                    'success' => false,
                    'errors'  => ['sale_price' => ['Sale Price must be greater than Purchase Price.']],
                ], 422);
            }

            // Cross-field: moq <= qty
            if ((int) $request->moq > (int) $request->qty) {
                return response()->json([
                    'success' => false,
                    'errors'  => ['moq' => ['MOQ cannot be greater than available Quantity.']],
                ], 422);
            }

            // Handle image
            $imagePath = $product->image;
            if ($request->hasFile('image')) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('image')->store('products', 'public');
            } elseif ($request->remove_image === '1') {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = null;
            }

            $data           = $this->buildProductData($request, $imagePath);
            $data['status'] = $request->input('status', $product->status ? '1' : '0');

            $product->update($data);
            $product->load(['category', 'manufacturer']);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'product' => $this->formatProduct($product),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
                'message' => 'Please fix the highlighted errors.',
            ], 422);

        } catch (\Exception $e) {
            Log::error('Product update error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // -------------------------------------------------------------------------
    // Toggle Status
    // -------------------------------------------------------------------------

    public function toggleStatus(Product $product): JsonResponse
    {
        try {
            $product->update(['status' => !$product->status]);

            return response()->json([
                'success' => true,
                'message' => $product->status ? 'Product enabled.' : 'Product disabled.',
                'status'  => $product->status ? '1' : '0',
            ]);
        } catch (\Exception $e) {
            Log::error('Product toggleStatus error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    // -------------------------------------------------------------------------
    // Destroy
    // -------------------------------------------------------------------------

    public function destroy(Product $product): JsonResponse
    {
        try {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Product destroy error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Build the DB column → value array from the request.
     * Only populates the actual active columns present in your database tables.
     */
    private function buildProductData(Request $request, ?string $imagePath): array
    {
        return [
            'name'             => trim($request->name),
            'item_code'        => $request->item_code ? trim($request->item_code) : null,
            'item_class'       => $request->item_class,
            'category_id'      => $request->category_id,
            'manufacturer_id'  => $request->manufacturer_id ?: null,
            'hsn_code'         => $request->hsn_code ?: null,
            'regional_name'    => $request->regional_name ?: null,
            'unit'             => $request->unit ?: null,
            'image'            => $imagePath,

            // Purchase Columns
            'purchase_price'         => $request->purchase_price ?? 0,
            'purchase_tax_inclusive' => $request->purchase_tax_inclusive ?? 0,
            // 'purchase_tax_percent' is excluded until the column migration is applied

            // Active Database Structural Columns Mapping
            'selling_price'          => $request->sale_price,
            'vat'                    => $request->gst_vat_percent ?? 0,
            'sale_tax_inclusive'     => $request->sale_tax_inclusive ?? 0,
            'discount_percentage'    => $request->discount_percent ?? 0,
            'cess_percentage'        => $request->cess_percent ?? 0,
            'additional_cess'        => $request->additional_cess ?? 0,
            'is_weighing_item'       => $request->is_weighing_item ?? 0,

            // Inventory
            'qty'                    => $request->qty,
            'moq'                    => $request->moq,
            'description'            => $request->description ?: null,
            'status'                 => $request->status ?? 1,
        ];
    }

    private function validationMessages(): array
    {
        return [
            'name.regex'           => 'Product Name may only contain letters, numbers and spaces.',
            'item_code.regex'      => 'Item Code may only contain letters, numbers, hyphens and underscores.',
            'item_code.unique'     => 'This Item Code is already in use.',
            'category_id.required' => 'Category is required.',
            'sale_price.required'  => 'Sale Price is required.',
            'qty.required'         => 'Quantity is required.',
            'moq.required'         => 'MOQ is required.',
            'image.mimes'          => 'Image must be a JPG, JPEG, PNG or WEBP file.',
            'image.max'            => 'Image must not exceed 2 MB.',
        ];
    }

    private function formatProduct(Product $product): array
    {
        $sellingPrice = $product->selling_price ?? $product->sale_price ?? 0;
        $vat          = $product->vat ?? $product->gst_vat_percent ?? 0;
        $discount     = $product->discount_percentage ?? $product->discount_percent ?? 0;
        $cess         = $product->cess_percentage ?? $product->cess_percent ?? 0;

        return [
            'id'                     => $product->id,
            'name'                   => $product->name,
            'item_code'              => $product->item_code,
            'item_class'             => $product->item_class,
            'category_id'            => $product->category_id,
            'category_name'          => $product->category?->name ?? '',
            'category'               => $product->category ? ['name' => $product->category->name] : null,
            'manufacturer_id'        => $product->manufacturer_id,
            'manufacturer_name'      => $product->manufacturer?->name ?? '',
            'manufacturer'           => $product->manufacturer ? ['name' => $product->manufacturer->name] : null,
            'hsn_code'               => $product->hsn_code,
            'regional_name'          => $product->regional_name,
            'unit'                   => $product->unit,
            'purchase_price'         => $product->purchase_price,
            'purchase_tax_percent'   => $product->purchase_tax_percent ?? 0,
            'purchase_tax_inclusive' => $product->purchase_tax_inclusive,
            'sale_price'             => $sellingPrice,
            'selling_price'          => $sellingPrice,
            'gst_vat_percent'        => $vat,
            'vat'                    => $vat,
            'sale_tax_inclusive'     => $product->sale_tax_inclusive,
            'discount_percent'       => $discount,
            'discount_percentage'    => $discount,
            'cess_percent'           => $cess,
            'cess_percentage'        => $cess,
            'additional_cess'        => $product->additional_cess,
            'is_weighing_item'       => $product->is_weighing_item,
            'qty'                    => $product->qty,
            'moq'                    => $product->moq,
            'status'                 => $product->status ? '1' : '0',
            'description'            => $product->description,
            'image'                  => $product->image,
        ];
    }
}