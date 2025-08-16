<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        return view('project.index');
    }

    public function fetchProjects()
    {
        $projects = Project::where('tenant_id', Auth::user()->tenant_id)
            ->with('modules')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'tenant_id' => Auth::user()->tenant_id,
        ]);

        return response()->json(['success' => true, 'project' => $project]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = Project::where('id', $id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->firstOrFail();

        $project->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json(['success' => true, 'project' => $project]);
    }

    public function destroy($id)
    {
        $project = Project::where('id', $id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->firstOrFail();

        $project->delete();

        return response()->json(['success' => true]);
    }
}
