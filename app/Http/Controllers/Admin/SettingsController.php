<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    // Settings ek hi row hogi — hamesha ID=1
    // -----------------------------------------

    public function index()
    {
        $settings = Setting::first();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name'   => 'required|string|max:100',
            'email'      => 'nullable|email|max:150',
            'phone'      => 'nullable|string|max:30',
            'address'    => 'nullable|string|max:500',
            'bank_name'  => 'nullable|string|max:100',
            'iban'       => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:20',
            'logo'       => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        $settings = Setting::firstOrNew(['id' => 1]);

        $settings->app_name   = $request->app_name;
        $settings->email      = $request->email;
        $settings->phone      = $request->phone;
        $settings->address    = $request->address;
        $settings->bank_name  = $request->bank_name;
        $settings->iban       = $request->iban;
        $settings->swift_code = $request->swift_code;

        // Logo upload
        if ($request->hasFile('logo')) {
            // Old logo delete
            if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
                Storage::disk('public')->delete($settings->logo);
            }
            $settings->logo = $request->file('logo')->store('logos', 'public');
        }

        // Logo remove
        if ($request->has('remove_logo') && $request->remove_logo == '1') {
            if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
                Storage::disk('public')->delete($settings->logo);
            }
            $settings->logo = null;
        }

        $settings->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Settings saved successfully.'
            ]);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings saved successfully.');
    }
}