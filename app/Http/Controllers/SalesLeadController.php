<?php

namespace App\Http\Controllers;

use App\Models\SalesRecord;
use App\Models\Remark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesLeadController extends Controller
{
    public function index(){
        return view('lead');
    }

  public function store(Request $request)
{
    $validated = $request->validate([
        'prospectus_id' => 'required|integer',
        'leads_name' => 'required|string',
        'contact_person' => 'required|string',
        'contact_number' => 'required|string',
        'status_id' => 'required|string',
        'address' => 'nullable|string',
        'state_id' => 'required|integer',
        'city_id' => 'nullable|integer',
        'email' => 'nullable|email',
        'next_follow_up_date' => 'nullable|date',
        'business_type_id' => 'required|integer',
        'remark' => 'nullable|string',
        'lead_source_id' => 'nullable|string',
        'products_id' => 'nullable|string',
    ]);

    // Set additional fields
    $validated['user_id'] = Auth::id();
    $validated['createdat'] = now();
    $validated['tenant_id'] = Auth::user()->tenant_id; // Add tenant_id

    // Extract remark before saving SalesRecord
    $remarkText = $validated['remark'] ?? null;
    unset($validated['remark']);

    // Save sales record
    $salesRecord = SalesRecord::create($validated);

    // Save remark in 'remarks' table
    if ($remarkText) {
        Remark::create([
            'remark_date' => now()->toDateString(),
            'remark' => $remarkText,
            'sales_remark_id' => $salesRecord->id,
            'tenant_id' => Auth::user()->tenant_id, // Add tenant_id to remark
        ]);
    }

    return response()->json(['message' => 'Sales record saved successfully']);
}

}


