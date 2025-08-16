<?php

namespace App\Http\Controllers;

use App\Models\Worklog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorklogHistoryController extends Controller
{
    public function index()
    {
        // Check if user has worklog permission
        if (!Auth::user()->is_worklog) {
            return redirect()->back()->with('error', 'You do not have permission to access worklog functionality.');
        }
        
        return view('worklog.history');
    }

    public function fetchWorklogs(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = 10;

        $worklogs = Worklog::where('worklogs.tenant_id', Auth::user()->tenant_id)
            ->where('user_id', Auth::user()->id)
            ->with(['entryType', 'customer', 'project', 'module'])
            ->orderBy('work_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Format the data to remove time from date and add status
        $formattedData = collect($worklogs->items())->map(function($worklog) {
            return [
                'id' => $worklog->id,
                'work_date' => $worklog->work_date,
                'entry_type' => $worklog->entryType,
                'customer' => $worklog->customer,
                'project' => $worklog->project,
                'module' => $worklog->module,
                'hours' => $worklog->hours,
                'minutes' => $worklog->minutes,
                'description' => $worklog->description,
                'status' => $worklog->status,
                'created_at' => $worklog->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'data' => $formattedData,
            'pagination' => [
                'current_page' => $worklogs->currentPage(),
                'last_page' => $worklogs->lastPage(),
                'per_page' => $worklogs->perPage(),
                'total' => $worklogs->total(),
                'from' => $worklogs->firstItem(),
                'to' => $worklogs->lastItem(),
                'has_more_pages' => $worklogs->hasMorePages(),
                'has_previous_pages' => $worklogs->hasPages() && $worklogs->currentPage() > 1,
            ]
        ]);
    }

    public function destroy($id)
    {
        $worklog = Worklog::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->firstOrFail();

        // Prevent deletion if worklog is approved or rejected
        if (in_array($worklog->status, ['approved', 'rejected'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete worklog that has been approved or rejected.'
            ], 422);
        }

        $worklog->delete();

        return response()->json(['success' => true]);
    }

    public function getWorklogStats()
    {
        $stats = Worklog::where('worklogs.tenant_id', Auth::user()->tenant_id)
            ->where('user_id', Auth::user()->id)
            ->selectRaw('
                COUNT(*) as total_entries,
                SUM(hours) as total_hours,
                SUM(minutes) as total_minutes,
                COUNT(DISTINCT DATE(work_date)) as total_days
            ')
            ->first();

        // Convert total minutes to hours
        $totalHours = $stats->total_hours + floor($stats->total_minutes / 60);
        $remainingMinutes = $stats->total_minutes % 60;

        return response()->json([
            'total_entries' => $stats->total_entries ?? 0,
            'total_hours' => $totalHours ?? 0,
            'total_minutes' => $remainingMinutes ?? 0,
            'total_days' => $stats->total_days ?? 0
        ]);
    }
}
