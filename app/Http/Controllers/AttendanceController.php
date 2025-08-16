<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Attendance;
use App\Models\Movement;
use App\Models\User;
use App\Models\Worklog;
use App\Models\Holiday;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Check if user can perform attendance actions based on worklog completion
     * Users with isWorklog = 1 must complete previous day's worklog before attendance
     */
    private function canPerformAttendanceAction()
    {
        $user = Auth::user();
        
        // If user doesn't have worklog access, allow attendance
        if (!$user->is_worklog) {
            return ['can_perform' => true, 'message' => ''];
        }
        
        $today = Carbon::today();
        $userCreatedDate = Carbon::parse($user->created_at)->startOfDay();
        
        // Start checking from the user's creation date
        $checkDate = $userCreatedDate;
        
        // Loop through each day from creation date to yesterday
        while ($checkDate->lt($today)) {
            // Skip if this date is a holiday or Sunday
            $isHoliday = Holiday::where('tenant_id', $user->tenant_id)
                ->where('holiday_date', $checkDate->format('Y-m-d'))
                ->exists();
            
            $isSunday = $checkDate->dayOfWeek === Carbon::SUNDAY;
            
            if (!$isHoliday && !$isSunday) {
                // This is a working day, check if worklog exists or leave
                $hasWorklogEntry = Worklog::where('user_id', $user->id)
                    ->where('tenant_id', $user->tenant_id)
                    ->where('work_date', $checkDate->format('Y-m-d'))
                    ->exists();
                
                $hasLeave = Leave::where('user_id', $user->id)
                    ->where('tenant_id', $user->tenant_id)
                    ->where('date', $checkDate->format('Y-m-d'))
                    ->exists();
                
                if (!$hasWorklogEntry && !$hasLeave) {
                    $formattedDate = $checkDate->format('l, F j, Y');
                    return [
                        'can_perform' => false, 
                        'message' => "You must complete your worklog entry or have leave for {$formattedDate} before you can perform attendance actions. Please complete your worklog entries chronologically starting from your account creation date."
                    ];
                }
            }
            
            // Move to next day
            $checkDate->addDay();
        }
        
        return ['can_perform' => true, 'message' => ''];
    }

    public function index()
    {
        return view('attendance.index');
    }

    public function punchIn(Request $request): JsonResponse
    {
        $request->validate([
            'movement_type' => 'required|in:office,field,break'
        ]);

        $user = Auth::user();
        
        // Check if user can perform attendance actions
        $attendanceCheck = $this->canPerformAttendanceAction();
        if (!$attendanceCheck['can_perform']) {
            return response()->json([
                'success' => false,
                'message' => $attendanceCheck['message']
            ], 403);
        }
        
        $today = Carbon::today();
        
        $attendance = Attendance::firstOrCreate([
            'user_id' => $user->id,
            'date' => $today,
            'tenant_id' => $user->tenant_id
        ]);

        // Check if already punched in for this type (last movement should be 'out' or none)
        $lastMovement = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', $request->movement_type)
            ->orderBy('time', 'desc')
            ->first();

        if ($lastMovement && $lastMovement->movement_action === 'in') {
            return response()->json([
                'success' => false,
                'message' => 'Already punched in for ' . $request->movement_type . '. Please punch out first.'
            ]);
        }

        // If punching in for office, automatically punch out from field if active
        if ($request->movement_type === 'office') {
            $this->autoPunchOutField($attendance);
        }
        
        // If punching in for field, automatically punch out from office if active
        if ($request->movement_type === 'field') {
            $this->autoPunchOutOffice($attendance);
        }

        // Create movement record
        $movement = Movement::create([
            'attendance_id' => $attendance->id,
            'movement_type' => $request->movement_type,
            'movement_action' => 'in',
            'time' => Carbon::now(),
            'description' => null,
            'tenant_id' => $user->tenant_id
        ]);

        $message = 'Successfully punched in for ' . $request->movement_type;
        if ($request->movement_type === 'office') {
            $message .= ' (Field work automatically ended)';
        } elseif ($request->movement_type === 'field') {
            $message .= ' (Office work automatically ended)';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'movement' => $movement
        ]);
    }

    public function punchOut(Request $request): JsonResponse
    {
        $request->validate([
            'movement_type' => 'required|in:office,field,break'
        ]);

        $user = Auth::user();
        
        // Check if user can perform attendance actions
        $attendanceCheck = $this->canPerformAttendanceAction();
        if (!$attendanceCheck['can_perform']) {
            return response()->json([
                'success' => false,
                'message' => $attendanceCheck['message']
            ], 403);
        }
        
        $today = Carbon::today();
        
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'No attendance record found for today'
            ]);
        }

        // Check if punched in for this type
        $punchInMovement = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', $request->movement_type)
            ->where('movement_action', 'in')
            ->first();

        if (!$punchInMovement) {
            return response()->json([
                'success' => false,
                'message' => 'Not punched in for ' . $request->movement_type
            ]);
        }

        // Create punch out movement
        $movement = Movement::create([
            'attendance_id' => $attendance->id,
            'movement_type' => $request->movement_type,
            'movement_action' => 'out',
            'time' => Carbon::now(),
            'description' => null,
            'tenant_id' => $user->tenant_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully punched out for ' . $request->movement_type,
            'movement' => $movement
        ]);
    }

    public function startBreak(Request $request): JsonResponse
    {
        // No validation needed for description

        $user = Auth::user();
        
        // Check if user can perform attendance actions
        $attendanceCheck = $this->canPerformAttendanceAction();
        if (!$attendanceCheck['can_perform']) {
            return response()->json([
                'success' => false,
                'message' => $attendanceCheck['message']
            ], 403);
        }
        
        $today = Carbon::today();
        
        $attendance = Attendance::firstOrCreate([
            'user_id' => $user->id,
            'date' => $today,
            'tenant_id' => $user->tenant_id
        ]);

        // Check if break already started (last movement should be 'end' or none)
        $lastBreakMovement = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', 'break')
            ->orderBy('time', 'desc')
            ->first();

        if ($lastBreakMovement && $lastBreakMovement->movement_action === 'start') {
            return response()->json([
                'success' => false,
                'message' => 'Break already started. Please end the current break first.'
            ]);
        }

        $movement = Movement::create([
            'attendance_id' => $attendance->id,
            'movement_type' => 'break',
            'movement_action' => 'start',
            'time' => Carbon::now(),
            'description' => null,
            'tenant_id' => $user->tenant_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Break started successfully',
            'movement' => $movement
        ]);
    }

    public function endBreak(Request $request): JsonResponse
    {
        // No validation needed for description

        $user = Auth::user();
        
        // Check if user can perform attendance actions
        $attendanceCheck = $this->canPerformAttendanceAction();
        if (!$attendanceCheck['can_perform']) {
            return response()->json([
                'success' => false,
                'message' => $attendanceCheck['message']
            ], 403);
        }
        
        $today = Carbon::today();
        
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'No attendance record found for today'
            ]);
        }

        // Find the most recent break that was started but not ended
        $breakStart = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', 'break')
            ->where('movement_action', 'start')
            ->whereNotExists(function ($query) use ($attendance) {
                $query->select(\DB::raw(1))
                    ->from('movements as m2')
                    ->whereRaw('m2.attendance_id = movements.attendance_id')
                    ->where('m2.movement_type', 'break')
                    ->where('m2.movement_action', 'end')
                    ->whereRaw('m2.time > movements.time');
            })
            ->orderBy('time', 'desc')
            ->first();

        if (!$breakStart) {
            return response()->json([
                'success' => false,
                'message' => 'No active break found to end'
            ]);
        }

        $movement = Movement::create([
            'attendance_id' => $attendance->id,
            'movement_type' => 'break',
            'movement_action' => 'end',
            'time' => Carbon::now(),
            'description' => null,
            'tenant_id' => $user->tenant_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Break ended successfully',
            'movement' => $movement
        ]);
    }

    public function getTodayStatus(): JsonResponse
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        // Check if user can perform attendance actions
        $attendanceCheck = $this->canPerformAttendanceAction();
        
        $attendance = Attendance::with('movements')
            ->where('user_id', $user->id)
            ->where('date', $today)
            ->where('tenant_id', $user->tenant_id)
            ->first();

        if (!$attendance) {
            return response()->json([
                'attendance' => null,
                'movements' => [],
                'status' => 'not_started'
            ]);
        }

        $movements = $attendance->movements()
            ->orderBy('time')
            ->get()
            ->groupBy('movement_type');

        $status = [];
        foreach (['office', 'field', 'break'] as $type) {
            $typeMovements = $movements->get($type, collect());
            
            if ($type === 'break') {
                // For breaks, check if currently on break
                $breakStart = $typeMovements->where('movement_action', 'start')->last();
                $breakEnd = $typeMovements->where('movement_action', 'end')->last();
                
                $status[$type] = [
                    'punched_in' => false, // Not applicable for breaks
                    'punched_out' => false, // Not applicable for breaks
                    'break_started' => $breakStart && (!$breakEnd || $breakEnd->time < $breakStart->time),
                    'break_ended' => $breakEnd && $breakStart && $breakEnd->time > $breakStart->time,
                    'punch_in_time' => null,
                    'punch_out_time' => null,
                    'break_start_time' => $breakStart ? $breakStart->time : null,
                    'break_end_time' => $breakEnd ? $breakEnd->time : null,
                ];
            } else {
                // For office and field, check if currently punched in
                $punchIn = $typeMovements->where('movement_action', 'in')->last();
                $punchOut = $typeMovements->where('movement_action', 'out')->last();
                
                // Check if currently active (punched in but not punched out)
                $currentlyActive = $punchIn && (!$punchOut || $punchOut->time < $punchIn->time);
                
                $status[$type] = [
                    'punched_in' => $currentlyActive,
                    'punched_out' => !$currentlyActive, // If not currently active, they can punch in
                    'break_started' => false, // Not applicable for office/field
                    'break_ended' => false, // Not applicable for office/field
                    'punch_in_time' => $punchIn ? $punchIn->time : null,
                    'punch_out_time' => $punchOut ? $punchOut->time : null,
                    'break_start_time' => null,
                    'break_end_time' => null,
                ];
            }
        }

        return response()->json([
            'attendance' => $attendance,
            'movements' => $movements,
            'status' => $status,
            'worklog_validation' => [
                'can_perform_attendance' => $attendanceCheck['can_perform'],
                'message' => $attendanceCheck['message']
            ]
        ]);
    }

    /**
     * Check worklog validation status for attendance
     * This helps frontend show appropriate messages
     */
    public function checkWorklogValidation(): JsonResponse
    {
        $validation = $this->canPerformAttendanceAction();
        
        return response()->json([
            'can_perform_attendance' => $validation['can_perform'],
            'message' => $validation['message'],
            'user_has_worklog_access' => Auth::user()->is_worklog
        ]);
    }

    public function history()
    {
        return view('attendance.history');
    }

    public function getHistoryData(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        \Log::info('getHistoryData called for user: ' . $user->id);
        
        $perPage = $request->get('per_page', 10);
        
        $attendances = Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->where('user_id', $user->id)
            ->where('tenant_id', $user->tenant_id)
            ->orderBy('date', 'desc')
            ->paginate($perPage);
        
        \Log::info('Found attendances: ' . $attendances->count());
        
        return response()->json($attendances);
    }

    public function getAttendanceStats(): JsonResponse
    {
        $user = Auth::user();
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        $stats = [
            'today_hours' => 0,
            'month_hours' => 0,
            'total_days' => 0,
            'avg_hours_per_day' => 0
        ];

        // Today's attendance
        $todayAttendance = Attendance::with('movements')
            ->where('user_id', $user->id)
            ->where('date', $today)
            ->where('tenant_id', $user->tenant_id)
            ->first();

        if ($todayAttendance) {
            $stats['today_hours'] = $this->calculateHours($todayAttendance->movements);
        }

        // This month's attendance
        $monthAttendances = Attendance::with('movements')
            ->where('user_id', $user->id)
            ->where('date', '>=', $thisMonth)
            ->where('tenant_id', $user->tenant_id)
            ->get();

        $stats['total_days'] = $monthAttendances->count();
        
        $totalMonthHours = 0;
        foreach ($monthAttendances as $attendance) {
            $totalMonthHours += $this->calculateHours($attendance->movements);
        }
        
        $stats['month_hours'] = $totalMonthHours;
        $stats['avg_hours_per_day'] = $stats['total_days'] > 0 ? round($totalMonthHours / $stats['total_days'], 2) : 0;

        return response()->json($stats);
    }

    private function calculateHours($movements): float
    {
        $totalMinutes = 0;
        $movements = $movements->sortBy('time');
        
        $officeIn = null;
        $fieldIn = null;
        
        foreach ($movements as $movement) {
            if ($movement->movement_type === 'office') {
                if ($movement->movement_action === 'in') {
                    $officeIn = $movement->time;
                } elseif ($movement->movement_action === 'out' && $officeIn) {
                    $totalMinutes += $officeIn->diffInMinutes($movement->time);
                    $officeIn = null;
                }
            } elseif ($movement->movement_type === 'field') {
                if ($movement->movement_action === 'in') {
                    $fieldIn = $movement->time;
                } elseif ($movement->movement_action === 'out' && $fieldIn) {
                    $totalMinutes += $fieldIn->diffInMinutes($movement->time);
                    $fieldIn = null;
                }
            }
        }

        // If still punched in, calculate until now
        if ($officeIn) {
            $totalMinutes += $officeIn->diffInMinutes(Carbon::now());
        }
        if ($fieldIn) {
            $totalMinutes += $fieldIn->diffInMinutes(Carbon::now());
        }

        return round($totalMinutes / 60, 2);
    }

    /**
     * Automatically punch out from field work when starting office work
     */
    private function autoPunchOutField($attendance): void
    {
        $fieldInMovement = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', 'field')
            ->where('movement_action', 'in')
            ->first();

        if ($fieldInMovement) {
            // Create automatic field punch out
            Movement::create([
                'attendance_id' => $attendance->id,
                'movement_type' => 'field',
                'movement_action' => 'out',
                'time' => Carbon::now(),
                'description' => 'Auto-ended (Office work started)',
                'tenant_id' => $attendance->tenant_id
            ]);
        }
    }

    /**
     * Automatically punch out from office work when starting field work
     */
    private function autoPunchOutOffice($attendance): void
    {
        $officeInMovement = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', 'office')
            ->where('movement_action', 'in')
            ->first();

        if ($officeInMovement) {
            // Create automatic office punch out
            Movement::create([
                'attendance_id' => $attendance->id,
                'movement_type' => 'office',
                'movement_action' => 'out',
                'time' => Carbon::now(),
                'description' => 'Auto-ended (Field work started)',
                'tenant_id' => $attendance->tenant_id
            ]);
        }
    }

    public function testApi()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        $attendanceCount = Attendance::where('user_id', $user->id)
            ->where('tenant_id', $user->tenant_id)
            ->count();
        $movementCount = Movement::whereHas('attendance', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('tenant_id', $user->tenant_id)->count();
        
        return response()->json([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'attendance_count' => $attendanceCount,
            'movement_count' => $movementCount,
            'message' => 'API is working'
        ]);
    }
}
