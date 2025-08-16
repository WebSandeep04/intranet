<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{
    public function index()
    {
        return view('tenant');
    }

    public function fetchTenants()
    {
        $tenants = Tenant::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $tenants
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_name' => 'required|string|max:255|unique:tenants,tenant_name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tenant = Tenant::create([
                'tenant_name' => $request->tenant_name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tenant created successfully!',
                'data' => $tenant
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tenant. Please try again.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'tenant_name' => 'required|string|max:255|unique:tenants,tenant_name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->update([
                'tenant_name' => $request->tenant_name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tenant updated successfully!',
                'data' => $tenant
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tenant. Please try again.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tenant deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tenant. Please try again.'
            ], 500);
        }
    }

    public function regenerateCode($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->update([
                'tenant_code' => 'TEN-' . strtoupper(\Illuminate\Support\Str::random(6))
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tenant code regenerated successfully!',
                'data' => $tenant
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to regenerate tenant code. Please try again.'
            ], 500);
        }
    }
}
