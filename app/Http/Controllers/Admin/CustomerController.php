<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->paginate(15);
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
      {

        // return $request->all();  
     $request->validate([
        'name'            => 'required|string|max:255',
        'email'           => 'required|email|unique:customers,email',
        'phone'           => 'nullable|string|max:20',
        'customer_type'   => 'required|in:regular,business',
        'address'         => 'nullable|string',
        'vat_registered'  => 'nullable|boolean',           // Changed
        'vat_number'      => 'required_if:vat_registered,1|string|nullable|max:50',
     ]);

    // Normalize checkbox value
    $data = $request->all();
    $data['vat_registered'] = $request->has('vat_registered') && $request->vat_registered == 'on' 
                                ? false 
                                : true;

    Customer::create($data);

    return redirect()->route('admin.customers.index')
                     ->with('success', 'Customer created successfully.');
}
public function edit(Customer $customer)
    {
        return response()->json($customer);
    }

public function update(Request $request, Customer $customer)
{
    $request->validate([
        'name'            => 'required|string|max:255',
        'email'           => ['required', 'email', Rule::unique('customers')->ignore($customer->id)],
        'phone'           => 'nullable|string|max:20',
        'customer_type'   => 'required|in:regular,business',
        'address'         => 'nullable|string',
        'vat_registered'  => 'nullable|boolean',
        'vat_number'      => 'required_if:vat_registered,1|string|nullable|max:50',
    ]);

    $data = $request->all();
    $data['vat_registered'] = $request->has('vat_registered') && $request->vat_registered == 'on' 
                                ? true 
                                : false;

    $customer->update($data);

    return redirect()->route('admin.customers.index')
                     ->with('success', 'Customer updated successfully.');
}

    public function toggle(Customer $customer)
    {
        $customer->update(['status' => !$customer->status]);

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer status updated.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer deleted successfully.');
    }
}