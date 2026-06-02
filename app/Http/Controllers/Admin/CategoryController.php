<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name', 'asc')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:100',
        ]);

        $name = trim(preg_replace('/\s+/', ' ', $validated['name']));

        if (Category::where('name', $name)->exists()) {
            return back()
                ->withInput()
                ->with('error', 'Category already exists.');
        }

        Category::create([
            'name' => $name,
            'status' => 1
        ]);

        return back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:100',
        ]);

        $name = trim(preg_replace('/\s+/', ' ', $validated['name']));

        if (Category::where('name', $name)
            ->where('id', '!=', $category->id)
            ->exists()) {

            return back()
                ->withInput()
                ->with('error', 'Category already exists.');
        }

        $category->update([
            'name' => $name
        ]);

        return back()->with('success', 'Category updated successfully.');
    }

    public function toggleStatus(Category $category)
    {
        $category->update([
            'status' => !$category->status
        ]);

        return back()->with('success', 'Category status updated successfully.');
    }
}