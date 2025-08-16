<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Models\SalesRecord;
use App\Models\Remark;

class RemarkController extends Controller
{
   public function index(Request $request)
{
    $sales_record_id = $request->input('sales_record_id');

    $record = SalesRecord::with([
        'status',
        'prospectus',
        'city',
        'state',
        'businessType',
        'leadSource',
        'product',
        'remarks'
    ])->where('tenant_id', Auth::user()->tenant_id)
      ->findOrFail($sales_record_id);

    return view('remark', compact('record'));
}

public function store(Request $request)
{
    $validated = $request->validate([
        'remark_id' => 'nullable|integer|exists:remarks,id',
        'sales_record_id' => 'required|integer|exists:sales_records,id', // ✅ validate directly
        'remark_date' => 'required|string',
        'remark' => 'required|string',
        'ticket_value' => 'nullable|string|max:255',
        'next_follow_up_date' => 'nullable|string',
        'sales_status' => 'required|integer|exists:sales_status,id',
    ]);

    $remark_date = Carbon::createFromFormat('d/m/Y', $validated['remark_date'])->format('Y-m-d');
    $next_follow_up_date = $validated['next_follow_up_date']
        ? Carbon::createFromFormat('d/m/Y', $validated['next_follow_up_date'])->format('Y-m-d')
        : null;

    // ✅ Directly fetch by sales_record_id and tenant_id
    $salesRecord = SalesRecord::where('tenant_id', Auth::user()->tenant_id)
                             ->find($validated['sales_record_id']);

    if (!$salesRecord) {
        return response()->json([
            'success' => false,
            'message' => 'Sales record not found.'
        ], 404);
    }

    // ✅ Create or update remark based on sales_record_id and date
    $remark = Remark::updateOrCreate(
        [
            'sales_remark_id' => $salesRecord->id,
            'remark_date' => $remark_date,
        ],
        [
            'remark' => $validated['remark'],
            'tenant_id' => Auth::user()->tenant_id, // Add tenant_id
        ]
    );

    // ✅ Update sales record
    $salesRecord->update([
        'ticket_value' => $validated['ticket_value'],
        'next_follow_up_date' => $next_follow_up_date,
        'status_id' => $validated['sales_status'],
        'updatedat' => now(),
    ]);

    return response()->json([
        'success' => true,
        'message' => $remark->wasRecentlyCreated ? 'Remark created successfully.' : 'Remark updated successfully.',
        'remark_id' => $remark->id
    ]);
}
}
