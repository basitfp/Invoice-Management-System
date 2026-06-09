<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    // ----------------------------
    // INDEX - Show all areas
    // ----------------------------
    public function index(Request $request)
    {
        $query = Area::orderBy('name');

        // Filter: name search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . trim($request->search) . '%');
        }

        // Filter: status ('' = all, '1' = active, '0' = inactive)
        if ($request->filled('status') && in_array($request->status, ['0', '1'])) {
            $query->where('status', $request->status);
        }

        // Filter: date range on created_at
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $areas = $query->get();

        return view('admin.areas.index', compact('areas'));
    }

    // ----------------------------
    // STORE - Save new area
    // ----------------------------
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:100',
        ]);

        $name = trim(preg_replace('/\s+/', ' ', $request->name));

        $exists = Area::where('name', $name)->exists();

        if ($exists) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['name' => ['Area with this name already exists.']]], 422);
            }
            return back()->with('error', 'Area with this name already exists.');
        }

        $area = Area::create([
            'name'   => $name,
            'status' => 1,
        ]);

        if ($request->ajax()) {
            $data = $area->toArray();
            $data['status'] = (int) $area->status;

            return response()->json([
                'success' => true,
                'message' => 'Area created successfully.',
                'data'    => $data
            ]);
        }

        return back()->with('success', 'Area created successfully.');
    }

    // ----------------------------
    // UPDATE - Edit existing area
    // ----------------------------
    public function update(Request $request, Area $area)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:100',
        ]);

        $name = trim(preg_replace('/\s+/', ' ', $request->name));

        $exists = Area::where('name', $name)
            ->where('id', '!=', $area->id)
            ->exists();

        if ($exists) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['name' => ['Area with this name already exists.']]], 422);
            }
            return back()->with('error', 'Area with this name already exists.');
        }

        $area->update([
            'name' => $name,
        ]);

        if ($request->ajax()) {
            $data = $area->toArray();
            $data['status'] = (int) $area->status;

            return response()->json([
                'success' => true,
                'message' => 'Area updated successfully.',
                'data'    => $data
            ]);
        }

        return back()->with('success', 'Area updated successfully.');
    }

    // ----------------------------
    // TOGGLE STATUS - Enable / Disable
    // ----------------------------
    public function toggleStatus(Request $request, Area $area)
    {
        $area->update([
            'status' => $area->status ? 0 : 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Area status updated successfully.',
                'new_status' => (int) $area->status
            ]);
        }

        return back()->with('success', 'Area status updated successfully.');
    }

    // ----------------------------
    // DESTROY - Delete area
    // ----------------------------
    public function destroy(Request $request, Area $area)
    {
        $area->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Area deleted successfully.'
            ]);
        }

        return back()->with('success', 'Area deleted successfully.');
    }
}