<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesStatus;

class SalesStatusController extends Controller
{
    public function fetchSaleStatus()
    {
        $statuses = SalesStatus::where('tenant_id', Auth::user()->tenant_id)->paginate(5);
        return response()->json($statuses);
    }

    public function index(){
        return view('status');
    }

    public function update(Request $request, $id)
    {
        $status = SalesStatus::where('tenant_id', Auth::user()->tenant_id)
                            ->findOrFail($id);
        $status->status_name = $request->status_name;
        $status->save();

        return response()->json(['message' => 'Status updated']);
    }

    public function destroy($id)
    {
        $status = SalesStatus::where('tenant_id', Auth::user()->tenant_id)
                            ->findOrFail($id);
        $status->delete();

        return response()->json(['message' => 'Status deleted']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'status_name' => 'required|string|max:255',
        ]);

        SalesStatus::create([
            'status_name' => $request->status_name,
            'tenant_id' => Auth::user()->tenant_id,
        ]);

        return response()->json(['success' => true]);
    }

    public function getStatuses()
    {
        $statuses = SalesStatus::where('tenant_id', Auth::user()->tenant_id)->get(); 
        return response()->json($statuses);
    }
}
