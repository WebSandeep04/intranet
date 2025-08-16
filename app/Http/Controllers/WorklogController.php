<?php

namespace App\Http\Controllers;

use App\Models\Worklog;
use App\Models\EntryType;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Module;
use App\Models\CustomerProject;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class WorklogController extends Controller
{
    public function index()
    {
        // Check if user has worklog permission
        if (!Auth::user()->is_worklog) {
            return redirect()->back()->with('error', 'You do not have permission to access worklog functionality.');
        }
        
        return view('worklog.index');
    }



    public function getEntryTypes()
    {
        $entryTypes = EntryType::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('working_hours', 'desc')
            ->get();

        return response()->json($entryTypes);
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
            ->orderBy('name')
            ->get();

        return response()->json($projects);
    }

    public function getProjectsByCustomer($customerId)
    {
        $projects = CustomerProject::where('customer_id', $customerId)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->with('project')
            ->get()
            ->pluck('project')
            ->unique('id')
            ->values();

        return response()->json($projects);
    }

    public function getModulesByProject($projectId)
    {
        $modules = Module::where('project_id', $projectId)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('name')
            ->get();

        return response()->json($modules);
    }

    public function addToSession(Request $request)
    {
        // Check if user has worklog permission
        if (!Auth::user()->is_worklog) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to submit worklog entries.'
            ], 403);
        }

        $request->validate([
            'work_date' => 'required|date',
            'entry_type_id' => 'required|exists:entry_types,id',
            'customer_id' => 'required|exists:customers,id',
            'project_id' => 'required|exists:projects,id',
            'module_id' => 'required|exists:modules,id',
            'hours' => 'required|integer|min:0|max:24',
            'minutes' => 'required|integer|min:0|max:59',
            'description' => 'required|string|max:1000',
        ]);

        // Check date validation
        $dateValidation = $this->checkDateValidationInternal($request->work_date);
        if (!$dateValidation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $dateValidation['message']
            ], 422);
        }

        // Get entry type to check working hours
        $entryType = EntryType::find($request->entry_type_id);
        $totalMinutes = ($request->hours * 60) + $request->minutes;
        $entryTypeMinutes = $entryType->working_hours * 60;

        // Check if total time is less than entry type working hours (but allow exceeding)
        if ($totalMinutes < $entryTypeMinutes) {
            return response()->json([
                'success' => false,
                'message' => "Total time ({$request->hours}h {$request->minutes}m) cannot be less than {$entryType->name} working hours ({$entryType->working_hours}h)"
            ], 422);
        }

        // Check for duplicate entry
        $existingWorklog = Worklog::where('work_date', $request->work_date)
            ->where('entry_type_id', $request->entry_type_id)
            ->where('customer_id', $request->customer_id)
            ->where('project_id', $request->project_id)
            ->where('module_id', $request->module_id)
            ->where('user_id', Auth::user()->id)
            ->where('description', $request->description)
            ->first();

        if ($existingWorklog) {
            return response()->json([
                'success' => false,
                'message' => 'This entry already exists in the database.'
            ], 422);
        }

        // Add to session
        $sessionKey = 'worklog_entries_' . Auth::user()->id;
        $entries = Session::get($sessionKey, []);
        
        $newEntry = [
            'id' => uniqid(),
            'work_date' => $request->work_date,
            'entry_type_id' => $request->entry_type_id,
            'entry_type_name' => $entryType->name,
            'customer_id' => $request->customer_id,
            'customer_name' => Customer::find($request->customer_id)->name,
            'project_id' => $request->project_id,
            'project_name' => Project::find($request->project_id)->name,
            'module_id' => $request->module_id,
            'module_name' => Module::find($request->module_id)->name,
            'hours' => $request->hours,
            'minutes' => $request->minutes,
            'description' => $request->description,
            'total_minutes' => $totalMinutes,
        ];

        $entries[] = $newEntry;
        Session::put($sessionKey, $entries);

        return response()->json([
            'success' => true,
            'message' => 'Entry added to session successfully.',
            'entry' => $newEntry,
            'total_entries' => count($entries)
        ]);
    }

    public function getSessionEntries()
    {
        $sessionKey = 'worklog_entries_' . Auth::user()->id;
        $entries = Session::get($sessionKey, []);

        return response()->json($entries);
    }

    public function removeFromSession(Request $request)
    {
        $request->validate([
            'entry_id' => 'required|string'
        ]);

        $sessionKey = 'worklog_entries_' . Auth::user()->id;
        $entries = Session::get($sessionKey, []);

        $entries = array_filter($entries, function($entry) use ($request) {
            return $entry['id'] !== $request->entry_id;
        });

        Session::put($sessionKey, array_values($entries));

        return response()->json([
            'success' => true,
            'message' => 'Entry removed from session.',
            'total_entries' => count($entries)
        ]);
    }

    public function clearSession()
    {
        $sessionKey = 'worklog_entries_' . Auth::user()->id;
        Session::forget($sessionKey);

        return response()->json([
            'success' => true,
            'message' => 'Session cleared successfully.'
        ]);
    }

    public function submitWorklog(Request $request)
    {
        $request->validate([
            'work_date' => 'required|date',
            'entry_type_id' => 'required|exists:entry_types,id',
        ]);

        $sessionKey = 'worklog_entries_' . Auth::user()->id;
        $entries = Session::get($sessionKey, []);

        if (empty($entries)) {
            return response()->json([
                'success' => false,
                'message' => 'No entries to submit.'
            ], 422);
        }

        // Get entry type
        $entryType = EntryType::find($request->entry_type_id);
        $expectedMinutes = $entryType->working_hours * 60;

        // Calculate total minutes from session entries
        $totalMinutes = 0;
        foreach ($entries as $entry) {
            $totalMinutes += $entry['total_minutes'];
        }

        // Check if total time is at least equal to entry type (but allow exceeding)
        if ($totalMinutes < $expectedMinutes) {
            return response()->json([
                'success' => false,
                'message' => "Total logged time ({$this->formatMinutes($totalMinutes)}) is less than {$entryType->name} working hours ({$entryType->working_hours}h). Please add more entries."
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($entries as $entry) {
                // Check for duplicate before inserting
                $existingWorklog = Worklog::where('work_date', $entry['work_date'])
                    ->where('entry_type_id', $entry['entry_type_id'])
                    ->where('customer_id', $entry['customer_id'])
                    ->where('project_id', $entry['project_id'])
                    ->where('module_id', $entry['module_id'])
                    ->where('user_id', Auth::user()->id)
                    ->where('description', $entry['description'])
                    ->first();

                if ($existingWorklog) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'One or more entries already exist in the database. Please refresh and try again.'
                    ], 422);
                }

                // Determine status based on whether user has a manager
                // If user has no manager, worklog goes to admin for approval
                $status = Auth::user()->is_manager ? 'pending' : 'pending';
                
                Worklog::create([
                    'work_date' => $entry['work_date'],
                    'entry_type_id' => $entry['entry_type_id'],
                    'customer_id' => $entry['customer_id'],
                    'project_id' => $entry['project_id'],
                    'module_id' => $entry['module_id'],
                    'hours' => $entry['hours'],
                    'minutes' => $entry['minutes'],
                    'description' => $entry['description'],
                    'status' => $status,
                    'user_id' => Auth::user()->id,
                    'tenant_id' => Auth::user()->tenant_id,
                ]);
            }

            // Clear session after successful save
            Session::forget($sessionKey);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Worklog submitted successfully! Session cleared.',
                'total_entries' => count($entries)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error saving worklog entries: ' . $e->getMessage()
            ], 500);
        }
    }

    private function formatMinutes($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return "{$hours}h {$mins}m";
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

    public function checkDateValidation(Request $request)
    {
        $selectedDate = $request->input('date');
        $user = Auth::user();
        
        // Get user's creation date
        $userCreatedDate = $user->created_at->format('Y-m-d');
        
        // Check if selected date is before user creation date
        if ($selectedDate < $userCreatedDate) {
            return response()->json([
                'valid' => false,
                'message' => "You cannot log work for dates before your account creation date ({$userCreatedDate})."
            ]);
        }
        
        // Check if there are any missing dates between user creation and selected date
        $missingDates = $this->getMissingDates($userCreatedDate, $selectedDate);
        
        if (!empty($missingDates)) {
            $firstMissingDate = $missingDates[0];
            return response()->json([
                'valid' => false,
                'message' => "You must fill worklog entries in chronological order. Please fill entries for {$firstMissingDate} first."
            ]);
        }
        
        return response()->json([
            'valid' => true,
            'message' => 'Date is valid for worklog entry.'
        ]);
    }

    private function getMissingDates($startDate, $endDate)
    {
        $user = Auth::user();
        $missingDates = [];
        
        $currentDate = $startDate;
        while ($currentDate < $endDate) { // Changed from <= to < to exclude the selected date
            // Check if user has any worklog entry for this date
            $hasEntry = Worklog::where('user_id', $user->id)
                ->where('tenant_id', $user->tenant_id)
                ->where('work_date', $currentDate)
                ->exists();
            
            // Check if the date is a holiday
            $isHoliday = Holiday::where('tenant_id', $user->tenant_id)
                ->where('holiday_date', $currentDate)
                ->exists();
            
            // Check if the date is a Sunday (0 = Sunday)
            $isSunday = date('w', strtotime($currentDate)) == 0;
            
            // Only add to missing dates if it's not a holiday and not a Sunday
            if (!$hasEntry && !$isHoliday && !$isSunday) {
                $missingDates[] = $currentDate;
            }
            
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }
        
        return $missingDates;
    }

    private function checkDateValidationInternal($selectedDate)
    {
        $user = Auth::user();
        
        // Get user's creation date
        $userCreatedDate = $user->created_at->format('Y-m-d');
        
        // Check if selected date is before user creation date
        if ($selectedDate < $userCreatedDate) {
            return [
                'valid' => false,
                'message' => "You cannot log work for dates before your account creation date ({$userCreatedDate})."
            ];
        }
        
        // Check if there are any missing dates between user creation and selected date
        $missingDates = $this->getMissingDates($userCreatedDate, $selectedDate);
        
        if (!empty($missingDates)) {
            $firstMissingDate = $missingDates[0];
            return [
                'valid' => false,
                'message' => "You must fill worklog entries in chronological order. Please fill entries for {$firstMissingDate} first."
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'Date is valid for worklog entry.'
        ];
    }

    public function approveWorklog($id)
    {
        $user = Auth::user();
        
        if ($user->role_id == 1) {
            // Admin: Can approve worklogs from users without managers
            $worklog = Worklog::where('id', $id)
                ->whereHas('user', function($query) {
                    $query->whereNull('is_manager')
                          ->where('is_worklog', 1);
                })
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();
        } else {
            // Manager: Can approve worklogs from their subordinates
            $worklog = Worklog::where('id', $id)
                ->whereHas('user', function($query) use ($user) {
                    $query->where('is_manager', $user->id);
                })
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();
        }

        $worklog->update(['status' => 'approved']);

        return response()->json(['success' => true, 'message' => 'Worklog approved successfully.']);
    }

    public function rejectWorklog($id)
    {
        $user = Auth::user();
        
        if ($user->role_id == 1) {
            // Admin: Can reject worklogs from users without managers
            $worklog = Worklog::where('id', $id)
                ->whereHas('user', function($query) {
                    $query->whereNull('is_manager')
                          ->where('is_worklog', 1);
                })
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();
        } else {
            // Manager: Can reject worklogs from their subordinates
            $worklog = Worklog::where('id', $id)
                ->whereHas('user', function($query) use ($user) {
                    $query->where('is_manager', $user->id);
                })
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();
        }

        $worklog->update(['status' => 'rejected']);

        return response()->json(['success' => true, 'message' => 'Worklog rejected successfully.']);
    }

    public function getPendingApprovals()
    {
        $user = Auth::user();
        
        // If user is admin (role_id = 1), show worklogs from users without managers
        // If user is manager, show worklogs from their subordinates
        if ($user->role_id == 1) {
            // Admin: Show worklogs from users who have no manager
            $pendingWorklogs = Worklog::where('status', 'pending')
                ->whereHas('user', function($query) {
                    $query->whereNull('is_manager')
                          ->where('is_worklog', 1);
                })
                ->where('tenant_id', $user->tenant_id)
                ->with(['user', 'entryType', 'customer', 'project', 'module'])
                ->orderBy('user_id')
                ->orderBy('work_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Manager: Show worklogs from their subordinates
            $pendingWorklogs = Worklog::where('status', 'pending')
                ->whereHas('user', function($query) use ($user) {
                    $query->where('is_manager', $user->id);
                })
                ->where('tenant_id', $user->tenant_id)
                ->with(['user', 'entryType', 'customer', 'project', 'module'])
                ->orderBy('user_id')
                ->orderBy('work_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Group worklogs by user and date
        $groupedWorklogs = $pendingWorklogs->groupBy(function($worklog) {
            return $worklog->user->name . '|' . $worklog->work_date;
        })->map(function($group) {
            return [
                'user_name' => $group->first()->user->name,
                'work_date' => $group->first()->work_date,
                'entries' => $group->values()
            ];
        })->values();

        return response()->json($groupedWorklogs);
    }

    public function approveGroup(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'work_date' => 'required|date',
        ]);

        $user = Auth::user();
        
        if ($user->role_id == 1) {
            // Admin: Can approve worklogs from users without managers
            $worklogs = Worklog::where('status', 'pending')
                ->whereHas('user', function($query) use ($request) {
                    $query->where('name', $request->user_name)
                          ->whereNull('is_manager')
                          ->where('is_worklog', 1);
                })
                ->where('work_date', $request->work_date)
                ->where('tenant_id', $user->tenant_id)
                ->get();
        } else {
            // Manager: Can approve worklogs from their subordinates
            $worklogs = Worklog::where('status', 'pending')
                ->whereHas('user', function($query) use ($request, $user) {
                    $query->where('name', $request->user_name)
                          ->where('is_manager', $user->id);
                })
                ->where('work_date', $request->work_date)
                ->where('tenant_id', $user->tenant_id)
                ->get();
        }

        $worklogs->each(function($worklog) {
            $worklog->update(['status' => 'approved']);
        });

        return response()->json([
            'success' => true, 
            'message' => "All entries for {$request->user_name} on {$request->work_date} have been approved."
        ]);
    }

    public function rejectGroup(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'work_date' => 'required|date',
        ]);

        $user = Auth::user();
        
        if ($user->role_id == 1) {
            // Admin: Can reject worklogs from users without managers
            $worklogs = Worklog::where('status', 'pending')
                ->whereHas('user', function($query) use ($request) {
                    $query->where('name', $request->user_name)
                          ->whereNull('is_manager')
                          ->where('is_worklog', 1);
                })
                ->where('work_date', $request->work_date)
                ->where('tenant_id', $user->tenant_id)
                ->get();
        } else {
            // Manager: Can reject worklogs from their subordinates
            $worklogs = Worklog::where('status', 'pending')
                ->whereHas('user', function($query) use ($request, $user) {
                    $query->where('name', $request->user_name)
                          ->where('is_manager', $user->id);
                })
                ->where('work_date', $request->work_date)
                ->where('tenant_id', $user->tenant_id)
                ->get();
        }

        $worklogs->each(function($worklog) {
            $worklog->update(['status' => 'rejected']);
        });

        return response()->json([
            'success' => true, 
            'message' => "All entries for {$request->user_name} on {$request->work_date} have been rejected."
        ]);
    }
}
