<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    // ----------------------------
    // INDEX - Show all vendors
    // ----------------------------
    public function index()
    {
        $vendors = Vendor::orderBy('name')->get();

        return view('admin.vendors.index', compact('vendors'));
    }

    // ----------------------------
    // STORE - Save new vendor
    // ----------------------------
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|min:2|max:100',
            'company'        => 'nullable|string|max:150',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'tax_reg_number' => 'nullable|string|max:50',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:100',
            'pin_code'       => 'nullable|string|max:20',
            'state'          => 'nullable|string|max:100',
            'country'        => 'nullable|string|max:100',
        ]);

        $name  = trim(preg_replace('/\s+/', ' ', $request->name));
        $email = $request->email ? strtolower(trim($request->email)) : null;

        // Manual unique check for name
        if (Vendor::where('name', $name)->exists()) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['name' => ['A vendor with this name already exists.']]], 422);
            }
            return back()->with('error', 'A vendor with this name already exists.');
        }

        // Manual unique check for email
        if ($email && Vendor::where('email', $email)->exists()) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['email' => ['This email is already registered.']]], 422);
            }
            return back()->with('error', 'This email is already registered.');
        }

        // Manual unique check for tax_reg_number
        $taxReg = $request->tax_reg_number ? trim($request->tax_reg_number) : null;
        if ($taxReg && Vendor::where('tax_reg_number', $taxReg)->exists()) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['tax_reg_number' => ['This tax registration number is already in use.']]], 422);
            }
            return back()->with('error', 'This tax registration number is already in use.');
        }

        $vendor = Vendor::create([
            'name'           => $name,
            'company'        => $request->company  ? trim($request->company)        : null,
            'phone'          => $request->phone    ? trim($request->phone)           : null,
            'email'          => $email,
            'tax_reg_number' => $taxReg,
            'address_line_1' => $request->address_line_1 ? trim($request->address_line_1) : null,
            'address_line_2' => $request->address_line_2 ? trim($request->address_line_2) : null,
            'city'           => $request->city     ? trim($request->city)            : null,
            'pin_code'       => $request->pin_code ? trim($request->pin_code)        : null,
            'state'          => $request->state    ? trim($request->state)           : null,
            'country'        => $request->country  ? trim($request->country)         : null,
            'status'         => 1,
        ]);

        if ($request->ajax()) {
            $data           = $vendor->toArray();
            $data['status'] = (int) $vendor->status;

            return response()->json([
                'success' => true,
                'message' => 'Vendor created successfully.',
                'data'    => $data,
            ]);
        }

        return back()->with('success', 'Vendor created successfully.');
    }

    // ----------------------------
    // UPDATE - Edit existing vendor
    // ----------------------------
    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'name'           => 'required|string|min:2|max:100',
            'company'        => 'nullable|string|max:150',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'tax_reg_number' => 'nullable|string|max:50',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:100',
            'pin_code'       => 'nullable|string|max:20',
            'state'          => 'nullable|string|max:100',
            'country'        => 'nullable|string|max:100',
        ]);

        $name  = trim(preg_replace('/\s+/', ' ', $request->name));
        $email = $request->email ? strtolower(trim($request->email)) : null;

        // Manual unique check for name (exclude self)
        if (Vendor::where('name', $name)->where('id', '!=', $vendor->id)->exists()) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['name' => ['A vendor with this name already exists.']]], 422);
            }
            return back()->with('error', 'A vendor with this name already exists.');
        }

        // Manual unique check for email (exclude self)
        if ($email && Vendor::where('email', $email)->where('id', '!=', $vendor->id)->exists()) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['email' => ['This email is already registered.']]], 422);
            }
            return back()->with('error', 'This email is already registered.');
        }

        // Manual unique check for tax_reg_number (exclude self)
        $taxReg = $request->tax_reg_number ? trim($request->tax_reg_number) : null;
        if ($taxReg && Vendor::where('tax_reg_number', $taxReg)->where('id', '!=', $vendor->id)->exists()) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['tax_reg_number' => ['This tax registration number is already in use.']]], 422);
            }
            return back()->with('error', 'This tax registration number is already in use.');
        }

        $vendor->update([
            'name'           => $name,
            'company'        => $request->company        ? trim($request->company)        : null,
            'phone'          => $request->phone          ? trim($request->phone)           : null,
            'email'          => $email,
            'tax_reg_number' => $taxReg,
            'address_line_1' => $request->address_line_1 ? trim($request->address_line_1) : null,
            'address_line_2' => $request->address_line_2 ? trim($request->address_line_2) : null,
            'city'           => $request->city           ? trim($request->city)            : null,
            'pin_code'       => $request->pin_code       ? trim($request->pin_code)        : null,
            'state'          => $request->state          ? trim($request->state)           : null,
            'country'        => $request->country        ? trim($request->country)         : null,
        ]);

        if ($request->ajax()) {
            $data           = $vendor->fresh()->toArray();
            $data['status'] = (int) $vendor->status;

            return response()->json([
                'success' => true,
                'message' => 'Vendor updated successfully.',
                'data'    => $data,
            ]);
        }

        return back()->with('success', 'Vendor updated successfully.');
    }

    // ----------------------------
    // TOGGLE STATUS - Enable / Disable
    // ----------------------------
    public function toggleStatus(Request $request, Vendor $vendor)
    {
        $vendor->update([
            'status' => $vendor->status ? 0 : 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Vendor status updated successfully.',
                'new_status' => (int) $vendor->status,
            ]);
        }

        return back()->with('success', 'Vendor status updated successfully.');
    }

    // ----------------------------
    // DESTROY - Delete vendor
    // ----------------------------
    public function destroy(Request $request, Vendor $vendor)
    {
        $vendor->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Vendor deleted successfully.',
            ]);
        }

        return back()->with('success', 'Vendor deleted successfully.');
    }
}