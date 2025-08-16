<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Prospectus;

class ProspectusController extends Controller
{
    public function store(Request $request)
{
    $validated = $request->validate([
        'prospectus_name' => 'required|string|max:255',
        'contact_person' => 'nullable|string|max:100',
        'contact_number' => 'nullable|string|max:20',
        'address' => 'nullable|string',
        'state_id' => 'nullable|integer',
        'city_id' => 'nullable|integer',
        'email' => 'nullable|email',
        'business_type_id' => 'nullable|integer',
    ]);

    $validated['tenant_id'] = Auth::user()->tenant_id; // Add tenant_id

    Prospectus::create($validated);

    return response()->json(['message' => 'Prospectus saved successfully.']);
}

public function getProspectus(){
     $prospectus = Prospectus::where('tenant_id', Auth::user()->tenant_id)->get(); 
    return response()->json($prospectus);
}

public function fillprospectus($id){
    $prospectus = Prospectus::where('tenant_id', Auth::user()->tenant_id)
                           ->where('id', $id)
                           ->first();

    if (!$prospectus) {
        return response()->json(['error' => 'Not found'], 404);
    }

    return response()->json($prospectus);
}
}
