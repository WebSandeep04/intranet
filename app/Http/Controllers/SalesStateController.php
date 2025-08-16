<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\State;

class SalesStateController extends Controller
{
    public function fetchSalesStates()
    {
        $states = State::paginate(5);
        return response()->json($states);
    }

    public function index()
    {
        if(request()->ajax()) {
            $states = State::pluck('state_name', 'id');
            return response()->json($states);
        }
        
        return view('state');
    }

    public function update(Request $request, $id)
    {
        $state = State::findOrFail($id);
        $state->state_name = $request->state_name;
        $state->save();

        return response()->json(['message' => 'state updated']);
    }

    public function destroy($id)
    {
        $state = State::findOrFail($id);
        $state->delete();

        return response()->json(['message' => 'state deleted']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'state_name' => 'required|string|max:255',
        ]);

        State::create([
            'state_name' => $request->state_name,
        ]);

        return response()->json(['success' => true]);
    }
}
