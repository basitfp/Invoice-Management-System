<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

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
        $request->validate([
            'name' => 'required|string|min:2|max:100',
        ]);

        $name = trim(preg_replace('/\s+/', ' ', $request->name));

        $exists = Category::where('name', $name)->exists();

        if ($exists) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['name' => ['Category with this name already exists.']]], 422);
            }
            return back()->with('error', 'Category with this name already exists.');
        }

        $category = Category::create([
            'name'   => $name,
            'status' => 1,
        ]);

        if ($request->ajax()) {
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
        $request->validate([
            'name' => 'required|string|min:2|max:100',
        ]);

        $name = trim(preg_replace('/\s+/', ' ', $request->name));

        $exists = Category::where('name', $name)
            ->where('id', '!=', $category->id)
            ->exists();

        if ($exists) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['name' => ['Category with this name already exists.']]], 422);
            }
            return back()->with('error', 'Category with this name already exists.');
        }

        $category->update([
            'name' => $name,
        ]);

        if ($request->ajax()) {
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

    // ----------------------------
    // TOGGLE STATUS - Enable / Disable
    // ----------------------------
    public function toggleStatus(Request $request, Category $category)
    {
        $category->update([
            'status' => $category->status ? 0 : 1,
        ]);

        if ($request->ajax()) {
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
        $category->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.'
            ]);
        }

        return back()->with('success', 'Category deleted successfully.');
    }
}