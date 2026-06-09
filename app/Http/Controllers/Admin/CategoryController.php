<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    // ----------------------------
    // INDEX - Show all categories
    // ----------------------------
   public function index()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    // ----------------------------
    // STORE - Save new category
    // ----------------------------
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
        ]);

        $name = trim(preg_replace('/\s+/', ' ', $request->name));

        if ($validator->fails() || strlen($name) < 2) {
            $errors = $validator->errors();

            if (strlen($name) < 2 && !$errors->has('name')) {
                $errors->add('name', 'The name field must be at least 2 characters.');
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $errors,
                ], 422);
            }

            return back()->withErrors($errors)->withInput();
        }

        $exists = Category::where('name', $name)->exists();

        if ($exists) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => ['name' => ['Category with this name already exists.']],
                ], 422);
            }
            return back()->with('error', 'Category with this name already exists.');
        }

        $category = Category::create([
            'name'   => $name,
            'status' => 1,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            $data = $category->toArray();
            $data['status'] = (int) $category->status;

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully.',
                'data'    => $data
            ]);
        }

        return back()->with('success', 'Category created successfully.');
    }

    // ----------------------------
    // UPDATE - Edit existing category
    // ----------------------------
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
        ]);

        $name = trim(preg_replace('/\s+/', ' ', $request->name));

        if ($validator->fails() || strlen($name) < 2) {
            $errors = $validator->errors();

            if (strlen($name) < 2 && !$errors->has('name')) {
                $errors->add('name', 'The name field must be at least 2 characters.');
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $errors,
                ], 422);
            }

            return back()->withErrors($errors)->withInput();
        }

        $exists = Category::where('name', $name)
            ->where('id', '!=', $category->id)
            ->exists();

        if ($exists) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => ['name' => ['Category with this name already exists.']],
                ], 422);
            }
            return back()->with('error', 'Category with this name already exists.');
        }

        $category->update([
            'name' => $name,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            $data = $category->toArray();
            $data['status'] = (int) $category->status;

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.',
                'data'    => $data
            ]);
        }

        return back()->with('success', 'Category updated successfully.');
    }

    public function show(Request $request, Category $category)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $category,
            ]);
        }

        return back();
    }

    // ----------------------------
    // TOGGLE STATUS - Enable / Disable
    // ----------------------------
    public function toggleStatus(Request $request, Category $category)
    {
        $category->update([
            'status' => $category->status ? 0 : 1,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Category status updated successfully.',
                'new_status' => (int) $category->status
            ]);
        }

        return back()->with('success', 'Category status updated successfully.');
    }

    // ----------------------------
    // DESTROY - Delete category
    // ----------------------------
    public function destroy(Request $request, Category $category)
    {
        $category->update(['status' => 0]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category disabled successfully.',
                'data' => [
                    'id' => $category->id,
                    'status' => (bool) $category->status,
                ],
            ]);
        }

        return back()->with('success', 'Category disabled successfully.');
    }

    public function enable(Request $request, Category $category)
    {
        $category->update(['status' => 1]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category enabled successfully.',
                'data' => [
                    'id' => $category->id,
                    'status' => (bool) $category->status,
                ],
            ]);
        }

        return back()->with('success', 'Category enabled successfully.');
    }
}
