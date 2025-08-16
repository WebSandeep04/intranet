<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesBusinessType;

class SalesBusinessTypeController extends Controller
{
    public function fetchSalesBusiness()
    {
        $business = SalesBusinessType::where('tenant_id', Auth::user()->tenant_id)->paginate(5);
        return response()->json($business);
    }

    public function index()
    {
        $business = SalesBusinessType::where('tenant_id', Auth::user()->tenant_id)->paginate(10);
        return view('business');
    }

    public function update(Request $request, $id)
    {
        $business = SalesBusinessType::where('tenant_id', Auth::user()->tenant_id)
                                   ->findOrFail($id);
        $business->business_name = $request->business_name;
        $business->save();

        return response()->json(['message' => 'business updated']);
    }

    public function destroy($id)
    {
        $business = SalesBusinessType::where('tenant_id', Auth::user()->tenant_id)
                                   ->findOrFail($id);
        $business->delete();

        return response()->json(['message' => 'business deleted']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
        ]);

        SalesBusinessType::create([
            'business_name' => $request->business_name,
            'tenant_id' => Auth::user()->tenant_id,
        ]);

        return response()->json(['success' => true]);
    }

    public function getbusiness(){
        $businesses = SalesBusinessType::where('tenant_id', Auth::user()->tenant_id)->get(); 
        return response()->json($businesses);
    }
}
