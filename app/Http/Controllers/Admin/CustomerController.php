<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
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
        $customers = Customer::with('area')->latest()->paginate(15);
        $areas     = Area::where('status', 1)->orderBy('name')->get();

        return view('admin.customers.index', compact('customers', 'areas'));
    }

    // ----------------------------
    // LOOKUP - Find customer by email (invoice quick-add)
    // ----------------------------
    public function lookupByEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if ($customer) {
            $data = $this->formatForResponse($customer);
        }

        return response()->json([
            'found' => (bool) $customer,
            'data'  => $customer ? $data : null,
        ]);
    }

    // ----------------------------
    // STORE - Save new customer
    // ----------------------------
    public function store(Request $request)
    {
        if ($request->boolean('from_invoice')) {
            $existing = Customer::where('email', $request->email)->first();
            if ($existing) {
                return response()->json([
                    'success' => true,
                    'exists'  => true,
                    'message' => 'Customer already exists.',
                    'data'    => $this->formatForResponse($existing),
                ]);
            }
        }

        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:customers,email',
            'phone'            => 'nullable|string|max:20',
            'customer_type'    => 'nullable|in:individual,company',
            'gender'           => 'nullable|in:male,female,other',
            'birthdate'        => 'nullable|date|before:today',
            'address'          => 'nullable|string',
            'shipping_address' => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:100',
            'pin_code'         => 'nullable|string|max:20',
            'state'            => 'nullable|string|max:100',
            'country'          => 'nullable|string|max:100',
            'landmark'         => 'nullable|string|max:255',
            'area_id'          => 'nullable|exists:areas,id',
            'credit_days'      => 'nullable|integer|min:0|max:65535',
            'credit_limit'     => 'nullable|numeric|min:0',
            'vat_registered'   => 'nullable|in:0,1',
            'vat_number'       => 'nullable|string|max:50|required_if:vat_registered,1',
        ]);

        $vatRegistered = $request->vat_registered == '1' ? 1 : 0;

        $customer = Customer::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'phone'            => $request->phone,
            'customer_type'    => $request->customer_type ?: 'individual',
            'gender'           => $request->gender ?: null,
            'birthdate'        => $request->birthdate ?: null,
            'address'          => $request->address,
            'shipping_address' => $request->shipping_address ?: null,
            'city'             => $request->city ?: null,
            'pin_code'         => $request->pin_code ?: null,
            'state'            => $request->state ?: null,
            'country'          => $request->country ?: null,
            'landmark'         => $request->landmark ?: null,
            'area_id'          => $request->area_id ?: null,
            'credit_days'      => $request->credit_days ?: null,
            'credit_limit'     => $request->credit_limit ?: null,
            'vat_registered'   => $vatRegistered,
            'vat_number'       => $vatRegistered ? $request->vat_number : null,
            'status'           => 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully.',
                'data'    => $this->formatForResponse($customer->load('area')),
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
            'name'             => 'required|string|max:255',
            'email'            => ['required', 'email', Rule::unique('customers')->ignore($customer->id)],
            'phone'            => 'nullable|string|max:20',
            'customer_type'    => 'nullable|in:individual,company',
            'gender'           => 'nullable|in:male,female,other',
            'birthdate'        => 'nullable|date|before:today',
            'address'          => 'nullable|string',
            'shipping_address' => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:100',
            'pin_code'         => 'nullable|string|max:20',
            'state'            => 'nullable|string|max:100',
            'country'          => 'nullable|string|max:100',
            'landmark'         => 'nullable|string|max:255',
            'area_id'          => 'nullable|exists:areas,id',
            'credit_days'      => 'nullable|integer|min:0|max:65535',
            'credit_limit'     => 'nullable|numeric|min:0',
            'vat_registered'   => 'nullable|in:0,1',
            'vat_number'       => 'nullable|string|max:50|required_if:vat_registered,1',
        ]);

        $vatRegistered = $request->vat_registered == '1' ? 1 : 0;

        $customer->update([
            'name'             => $request->name,
            'email'            => $request->email,
            'phone'            => $request->phone,
            'customer_type'    => $request->customer_type ?: 'individual',
            'gender'           => $request->gender ?: null,
            'birthdate'        => $request->birthdate ?: null,
            'address'          => $request->address,
            'shipping_address' => $request->shipping_address ?: null,
            'city'             => $request->city ?: null,
            'pin_code'         => $request->pin_code ?: null,
            'state'            => $request->state ?: null,
            'country'          => $request->country ?: null,
            'landmark'         => $request->landmark ?: null,
            'area_id'          => $request->area_id ?: null,
            'credit_days'      => $request->credit_days ?: null,
            'credit_limit'     => $request->credit_limit ?: null,
            'vat_registered'   => $vatRegistered,
            'vat_number'       => $vatRegistered ? $request->vat_number : null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully.',
                'data'    => $this->formatForResponse($customer->fresh()->load('area')),
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
                'new_status' => (int) $customer->status,
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
                'message' => 'Customer deleted successfully.',
            ]);
        }

        return back()->with('success', 'Customer deleted successfully.');
    }

    // ----------------------------
    // PRIVATE - Consistent response shape
    // ----------------------------
    private function formatForResponse(Customer $customer): array
    {
        $data                  = $customer->toArray();
        $data['status']        = (int) $customer->status;
        $data['vat_registered'] = (int) $customer->vat_registered;
        $data['area_name']     = $customer->area ? $customer->area->name : '';

        return $data;
    }
}
