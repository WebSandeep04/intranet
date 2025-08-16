<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesLeadSource;

class SalesLeadSourceController extends Controller
{   
    public function fetchSaleSources()
    {
        $sources = SalesLeadSource::where('tenant_id', Auth::user()->tenant_id)->paginate(5);
        return response()->json($sources);
    }

    public function index()
    {
        $leadSources = SalesLeadSource::where('tenant_id', Auth::user()->tenant_id)->paginate(10);
        return view('source');
    }

    public function update(Request $request, $id)
    {
        $lead = SalesLeadSource::where('tenant_id', Auth::user()->tenant_id)
                              ->findOrFail($id);
        $lead->source_name = $request->source_name;
        $lead->save();

        return response()->json(['message' => 'Lead updated']);
    }

    public function destroy($id)
    {
        $lead = SalesLeadSource::where('tenant_id', Auth::user()->tenant_id)
                              ->findOrFail($id);
        $lead->delete();

        return response()->json(['message' => 'Lead deleted']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'source_name' => 'required|string|max:255',
        ]);

        SalesLeadSource::create([
            'source_name' => $request->source_name,
            'tenant_id' => Auth::user()->tenant_id,
        ]);

        return response()->json(['success' => true]);
    }

    public function getsource(){
        $sources = SalesLeadSource::where('tenant_id', Auth::user()->tenant_id)->get(); 
        return response()->json($sources);
    }
}
