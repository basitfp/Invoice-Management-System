<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Manufacturer;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    // ----------------------------
    // INDEX - Show all manufacturers
    // ----------------------------
    public function index()
    {
        $manufacturers = Manufacturer::orderBy('name')->get();

        return view('admin.manufacturers.index', compact('manufacturers'));
    }

    // ----------------------------
    // STORE - Save new manufacturer
    // ----------------------------
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|min:2|max:100',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
        ]);

        $name  = trim(preg_replace('/\s+/', ' ', $request->name));
        $email = $request->email ? strtolower(trim($request->email)) : null;

        // Manual unique check for name
        if (Manufacturer::where('name', $name)->exists()) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['name' => ['Manufacturer with this name already exists.']]], 422);
            }
            return back()->with('error', 'Manufacturer with this name already exists.');
        }

        // Manual unique check for email
        if ($email && Manufacturer::where('email', $email)->exists()) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['email' => ['This email is already registered.']]], 422);
            }
            return back()->with('error', 'This email is already registered.');
        }

        $manufacturer = Manufacturer::create([
            'name'    => $name,
            'phone'   => $request->phone ? trim($request->phone) : null,
            'email'   => $email,
            'address' => $request->address ? trim($request->address) : null,
            'status'  => 1,
        ]);

        if ($request->ajax()) {
            $data = $manufacturer->toArray();
            $data['status'] = (int) $manufacturer->status;

            return response()->json([
                'success' => true,
                'message' => 'Manufacturer created successfully.',
                'data'    => $data,
            ]);
        }

        return back()->with('success', 'Manufacturer created successfully.');
    }

    // ----------------------------
    // UPDATE - Edit existing manufacturer
    // ----------------------------
    public function update(Request $request, Manufacturer $manufacturer)
    {
        $request->validate([
            'name'    => 'required|string|min:2|max:100',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
        ]);

        $name  = trim(preg_replace('/\s+/', ' ', $request->name));
        $email = $request->email ? strtolower(trim($request->email)) : null;

        // Manual unique check for name (exclude self)
        if (Manufacturer::where('name', $name)->where('id', '!=', $manufacturer->id)->exists()) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['name' => ['Manufacturer with this name already exists.']]], 422);
            }
            return back()->with('error', 'Manufacturer with this name already exists.');
        }

        // Manual unique check for email (exclude self)
        if ($email && Manufacturer::where('email', $email)->where('id', '!=', $manufacturer->id)->exists()) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['email' => ['This email is already registered.']]], 422);
            }
            return back()->with('error', 'This email is already registered.');
        }

        $manufacturer->update([
            'name'    => $name,
            'phone'   => $request->phone ? trim($request->phone) : null,
            'email'   => $email,
            'address' => $request->address ? trim($request->address) : null,
        ]);

        if ($request->ajax()) {
            $data = $manufacturer->toArray();
            $data['status'] = (int) $manufacturer->status;

            return response()->json([
                'success' => true,
                'message' => 'Manufacturer updated successfully.',
                'data'    => $data,
            ]);
        }

        return back()->with('success', 'Manufacturer updated successfully.');
    }

    // ----------------------------
    // TOGGLE STATUS - Enable / Disable
    // ----------------------------
    public function toggleStatus(Request $request, Manufacturer $manufacturer)
    {
        $manufacturer->update([
            'status' => $manufacturer->status ? 0 : 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Manufacturer status updated successfully.',
                'new_status' => (int) $manufacturer->status,
            ]);
        }

        return back()->with('success', 'Manufacturer status updated successfully.');
    }

    // ----------------------------
    // DESTROY - Delete manufacturer
    // ----------------------------
    public function destroy(Request $request, Manufacturer $manufacturer)
    {
        $manufacturer->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Manufacturer deleted successfully.',
            ]);
        }

        return back()->with('success', 'Manufacturer deleted successfully.');
    }
}