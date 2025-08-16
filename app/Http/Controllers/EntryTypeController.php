<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EntryType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EntryTypeController extends Controller
{
    public function index()
    {
        return view('entry-type.index');
    }

    public function fetch()
    {
        try {
            $user = Auth::user();
            
            // Debug: Log user information
            \Log::info('EntryType fetch - User: ' . $user->id . ', Email: ' . $user->email . ', Tenant ID: ' . $user->tenant_id);
            
            if (!$user->tenant_id) {
                \Log::warning('EntryType fetch - User has no tenant_id: ' . $user->id . '. Returning all entry types as fallback.');
                
                // Fallback: Return all entry types if user has no tenant_id
                $entryTypes = EntryType::orderBy('name')->get();
                
                return response()->json([
                    'data' => $entryTypes,
                    'warning' => 'User does not have a tenant ID assigned. Showing all entry types. Please contact administrator.',
                    'debug' => [
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'user_tenant_id' => $user->tenant_id,
                        'fallback_mode' => true,
                        'total_entry_types' => $entryTypes->count()
                    ]
                ]);
            }
            
            // Get entry types for the current user's tenant only
            $entryTypes = EntryType::where('tenant_id', $user->tenant_id)
                ->orderBy('name')
                ->get();
            
            // Debug: Check what entry types exist for this tenant
            $totalEntryTypes = EntryType::count();
            $tenantEntryTypes = EntryType::where('tenant_id', $user->tenant_id)->count();
            
            \Log::info('EntryType fetch - Found ' . $tenantEntryTypes . ' entry types for tenant ' . $user->tenant_id);
            
            return response()->json([
                'data' => $entryTypes,
                'debug' => [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_tenant_id' => $user->tenant_id,
                    'tenant_filtered_count' => $entryTypes->count(),
                    'total_system_entry_types' => $totalEntryTypes,
                    'total_tenant_entry_types' => $tenantEntryTypes,
                    'message' => 'Returning tenant-specific entry types'
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('EntryType fetch error: ' . $e->getMessage());
            \Log::error('EntryType fetch error trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to fetch entry types: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        return view('entry-type.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'working_hours' => 'required|integer|min:0|max:24',
            'description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $entryType = EntryType::create([
            'name' => $request->name,
            'working_hours' => $request->working_hours,
            'description' => $request->description,
            'tenant_id' => Auth::user()->tenant_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Entry Type created successfully!',
            'data' => $entryType
        ]);
    }

    public function edit($id)
    {
        $entryType = EntryType::where('id', $id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->firstOrFail();
        
        return view('entry-type.edit', compact('entryType'));
    }

    public function update(Request $request, $id)
    {
        $entryType = EntryType::where('id', $id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'working_hours' => 'required|integer|min:0|max:24',
            'description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $entryType->update([
            'name' => $request->name,
            'working_hours' => $request->working_hours,
            'description' => $request->description
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Entry Type updated successfully!',
            'data' => $entryType
        ]);
    }

    public function destroy($id)
    {
        $entryType = EntryType::where('id', $id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->firstOrFail();

        // Check if this entry type is being used in worklogs
        if ($entryType->worklogs()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete Entry Type. It is being used in worklog entries.'
            ], 422);
        }

        $entryType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Entry Type deleted successfully!'
        ]);
    }
}
