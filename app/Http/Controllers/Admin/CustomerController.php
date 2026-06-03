<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    // ----------------------------
    // INDEX - Show all customers
    // ----------------------------
    public function index()
    {
        $customers = Customer::latest()->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    // ----------------------------
    // STORE - Save new customer
    // ----------------------------
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:customers,email',
            'phone'          => 'nullable|string|max:20',
            'customer_type'  => 'required|in:regular,business',
            'address'        => 'nullable|string',
            'vat_registered' => 'nullable|in:0,1',
            'vat_number'     => 'nullable|string|max:50|required_if:vat_registered,1',
        ]);

        // Checkbox value normalize karo
        $vatRegistered = $request->vat_registered == '1' ? 1 : 0;

        $customer = Customer::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'customer_type'  => $request->customer_type,
            'address'        => $request->address,
            'vat_registered' => $vatRegistered,
            'vat_number'     => $vatRegistered ? $request->vat_number : null,
            'status'         => 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully.',
                'data'    => $customer
            ]);
        }

        return back()->with('success', 'Customer created successfully.');
    }

    // ----------------------------
    // UPDATE - Edit existing customer
    // ----------------------------
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => ['required', 'email', Rule::unique('customers')->ignore($customer->id)],
            'phone'          => 'nullable|string|max:20',
            'customer_type'  => 'required|in:regular,business',
            'address'        => 'nullable|string',
            'vat_registered' => 'nullable|in:0,1',
            'vat_number'     => 'nullable|string|max:50|required_if:vat_registered,1',
        ]);

        // Checkbox value normalize karo
        $vatRegistered = $request->vat_registered == '1' ? 1 : 0;

        $customer->update([
            'name'           => $request->name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'customer_type'  => $request->customer_type,
            'address'        => $request->address,
            'vat_registered' => $vatRegistered,
            'vat_number'     => $vatRegistered ? $request->vat_number : null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully.',
                'data'    => $customer
            ]);
        }

        return back()->with('success', 'Customer updated successfully.');
    }

    // ----------------------------
    // TOGGLE STATUS - Active / Inactive
    // ----------------------------
    public function toggle(Request $request, Customer $customer)
    {
        $customer->update([
            'status' => $customer->status ? 0 : 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Customer status updated.',
                'new_status' => $customer->status
            ]);
        }

        return back()->with('success', 'Customer status updated.');
    }

    // ----------------------------
    // DESTROY - Delete customer
    // ----------------------------
    public function destroy(Request $request, Customer $customer)
    {
        $customer->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully.'
            ]);
        }

        return back()->with('success', 'Customer deleted successfully.');
    }
}