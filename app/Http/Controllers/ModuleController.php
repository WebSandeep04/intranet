<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    public function index()
    {
        return view('module.index');
    }

    public function fetchModules()
    {
        $modules = Module::where('modules.tenant_id', Auth::user()->tenant_id)
            ->with('project')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($modules);
    }

    public function getModulesByProject($projectId)
    {
        $modules = Module::where('project_id', $projectId)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->get();

        return response()->json($modules);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
        ]);

        $module = Module::create([
            'name' => $request->name,
            'description' => $request->description,
            'project_id' => $request->project_id,
            'tenant_id' => Auth::user()->tenant_id,
        ]);

        return response()->json(['success' => true, 'module' => $module]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
        ]);

        $module = Module::where('id', $id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->firstOrFail();

        $module->update([
            'name' => $request->name,
            'description' => $request->description,
            'project_id' => $request->project_id,
        ]);

        return response()->json(['success' => true, 'module' => $module]);
    }

    public function destroy($id)
    {
        $module = Module::where('id', $id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->firstOrFail();

        $module->delete();

        return response()->json(['success' => true]);
    }
}
