<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customer.index');
    }

    public function fetchCustomers()
    {
        $customers = Customer::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($customers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'company_name' => $request->company_name,
            'tenant_id' => Auth::user()->tenant_id,
        ]);

        return response()->json(['success' => true, 'customer' => $customer]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
        ]);

        $customer = Customer::where('id', $id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->firstOrFail();

        $customer->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'company_name' => $request->company_name,
        ]);

        return response()->json(['success' => true, 'customer' => $customer]);
    }

    public function destroy($id)
    {
        $customer = Customer::where('id', $id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->firstOrFail();

        $customer->delete();

        return response()->json(['success' => true]);
    }
}
