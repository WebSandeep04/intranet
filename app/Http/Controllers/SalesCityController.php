<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\City;

class SalesCityController extends Controller
{
    public function fetchSalesCities()
    {
        $Cities = City::paginate(5);
        return response()->json($Cities);
    }

    public function index(){
        return view('City');
    }

    public function update(Request $request, $id)
    {
        $City = City::findOrFail($id);
        $City->city_name = $request->city_name;
        $City->save();

        return response()->json(['message' => 'City updated']);
    }

    public function destroy($id)
    {
        $City = City::findOrFail($id);
        $City->delete();

        return response()->json(['message' => 'City deleted']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'state_id' => 'required|exists:states,id', // assuming your table is sales_states
            'city_name' => 'required|string|max:255',
        ]);

        City::create([
            'state_id' => $validated['state_id'],
            'city_name' => $validated['city_name'],
        ]);

        return response()->json(['success' => true, 'message' => 'City created successfully']);
    }

    public function getCities($state_id)
    {
        $cities = City::where('state_id', $state_id)
                     ->pluck('city_name', 'id');
        return response()->json($cities);
    }

    public function allcity(){
        $cities = City::get();
        return response()->json($cities);
    }
}
