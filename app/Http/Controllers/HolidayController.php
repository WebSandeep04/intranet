<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
    public function index()
    {
        return view('holiday.index');
    }

    public function fetchHolidays()
    {
        $holidays = Holiday::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('holiday_date', 'desc')
            ->get();

        return response()->json($holidays);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'holiday_date' => 'required|date|unique:holidays,holiday_date,NULL,id,tenant_id,' . Auth::user()->tenant_id,
        ]);

        Holiday::create([
            'name' => $request->name,
            'holiday_date' => $request->holiday_date,
            'tenant_id' => Auth::user()->tenant_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Holiday added successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'holiday_date' => 'required|date|unique:holidays,holiday_date,' . $id . ',id,tenant_id,' . Auth::user()->tenant_id,
        ]);

        $holiday = Holiday::where('id', $id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->firstOrFail();

        $holiday->update([
            'name' => $request->name,
            'holiday_date' => $request->holiday_date,
        ]);

        return response()->json(['success' => true, 'message' => 'Holiday updated successfully.']);
    }

    public function destroy($id)
    {
        $holiday = Holiday::where('id', $id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->firstOrFail();

        $holiday->delete();

        return response()->json(['success' => true, 'message' => 'Holiday deleted successfully.']);
    }
}
