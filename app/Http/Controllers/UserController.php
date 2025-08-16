<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(){
        return view('user');
    }

   public function fetchuser()
{
    $users = User::with(['role', 'manager'])
                    ->where('tenant_id', Auth::user()->tenant_id)
                    ->get();
    return response()->json($users);
}

public function fetchUsersForManager()
{
    $users = User::where('tenant_id', Auth::user()->tenant_id)
                    ->select('id', 'name')
                    ->get();
    return response()->json($users);
}

public function update(Request $request, $id)
{
            $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
            'is_manager' => 'nullable',
            'is_worklog' => 'nullable',
        ]);

    // Additional validation for manager - ensure manager exists in same tenant and is not self
    if ($request->is_manager) {
        if ($request->is_manager == $id) {
            return response()->json([
                'message' => 'A user cannot be assigned as their own manager.'
            ], 422);
        }
        
        $managerExists = User::where('id', $request->is_manager)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->exists();
        
        if (!$managerExists) {
            return response()->json([
                'message' => 'Selected manager does not exist in your organization.'
            ], 422);
        }
    }

    $user = User::where('tenant_id', Auth::user()->tenant_id)
                   ->findOrFail($id);
    $user->update([
        'name' => $request->name,
        'email' => $request->email,
        'role_id' => $request->role_id,
        'is_manager' => $request->is_manager ?: null,
        'is_worklog' => $request->has('is_worklog') ? 1 : 0,
    ]);

    return response()->json(['message' => 'User updated successfully']);
}

public function store(Request $request)
{
    try {
        // Log the incoming data for debugging
        \Log::info('User creation request data:', $request->all());
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'is_manager' => 'nullable',
            'is_worklog' => 'nullable',
        ]);

        // Additional validation for manager - ensure manager exists in same tenant
        if ($request->is_manager) {
            $managerExists = User::where('id', $request->is_manager)
                ->where('tenant_id', Auth::user()->tenant_id)
                ->exists();
            
            if (!$managerExists) {
                return response()->json([
                    'message' => 'Selected manager does not exist in your organization.'
                ], 422);
            }
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
            'is_manager' => $request->is_manager ?: null,
            'is_worklog' => $request->has('is_worklog') ? 1 : 0,
            'tenant_id' => Auth::user()->tenant_id,
        ];

        User::create($userData);

        return response()->json(['message' => 'User created successfully']);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error creating user: ' . $e->getMessage()
        ], 500);
    }
}

public function destroy($id)
{
    $user = User::where('tenant_id', Auth::user()->tenant_id)
                   ->findOrFail($id);
    $user->delete();

    return response()->json(['message' => 'User deleted successfully']);
}


}
