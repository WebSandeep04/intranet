<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use App\Models\Module;
use App\Models\CustomerProject;
use App\Models\CustomerProjectModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerProjectController extends Controller
{
    public function index()
    {
        return view('customer-project.index');
    }

    public function fetchCustomerProjects()
    {
        $customerProjects = CustomerProject::where('customer_projects.tenant_id', Auth::user()->tenant_id)
            ->with(['customer', 'project', 'customerProjectModules.module'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($customerProjects);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'project_id' => 'required|exists:projects,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'description' => 'nullable|string',
            'module_ids' => 'required|array|min:1',
            'module_ids.*' => 'exists:modules,id',
        ]);

        DB::beginTransaction();
        try {
            $customerProject = CustomerProject::create([
                'customer_id' => $request->customer_id,
                'project_id' => $request->project_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
                'description' => $request->description,
                'tenant_id' => Auth::user()->tenant_id,
            ]);

            // Create customer project modules
            foreach ($request->module_ids as $moduleId) {
                CustomerProjectModule::create([
                    'customer_project_id' => $customerProject->id,
                    'module_id' => $moduleId,
                    'status' => 'pending',
                    'tenant_id' => Auth::user()->tenant_id,
                ]);
            }

            DB::commit();

            $customerProject->load(['customer', 'project', 'customerProjectModules.module']);

            return response()->json(['success' => true, 'customerProject' => $customerProject]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Error creating customer project'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'project_id' => 'required|exists:projects,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'description' => 'nullable|string',
        ]);

        $customerProject = CustomerProject::where('id', $id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->firstOrFail();

        $customerProject->update([
            'customer_id' => $request->customer_id,
            'project_id' => $request->project_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'description' => $request->description,
        ]);

        $customerProject->load(['customer', 'project', 'customerProjectModules.module']);

        return response()->json(['success' => true, 'customerProject' => $customerProject]);
    }

    public function updateModuleStatus(Request $request, $customerProjectId, $moduleId)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $customerProjectModule = CustomerProjectModule::where('customer_project_id', $customerProjectId)
            ->where('module_id', $moduleId)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->firstOrFail();

        $customerProjectModule->update([
            'status' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
        ]);

        return response()->json(['success' => true, 'customerProjectModule' => $customerProjectModule]);
    }

    public function destroy($id)
    {
        $customerProject = CustomerProject::where('id', $id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->firstOrFail();

        $customerProject->delete();

        return response()->json(['success' => true]);
    }

    public function getCustomers()
    {
        $customers = Customer::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('name')
            ->get();

        return response()->json($customers);
    }

    public function getProjects()
    {
        $projects = Project::where('tenant_id', Auth::user()->tenant_id)
            ->with('modules')
            ->orderBy('name')
            ->get();

        return response()->json($projects);
    }
}
