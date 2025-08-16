<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesRecord;
use App\Models\SalesStatus;
use App\Models\SalesLeadSource;
use App\Models\SalesProduct;
use App\Models\SalesBusinessType;
use App\Models\State;
use App\Models\City;
use App\Models\Prospectus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MyLeadsController extends Controller
{

    public function index()
    {
        return view('myleads');
    }

    // Get user's leads with pagination
    public function getMyLeads(Request $request)
    {
        $userId = Auth::id();
        $tenantId = Auth::user()->tenant_id;
        $perPage = $request->get('per_page', 10);

        $records = SalesRecord::with([
            'status',
            'prospectus',
            'city',
            'state',
            'businessType',
            'leadSource',
            'product',
            'latestRemark'
        ])
        ->where('user_id', $userId)
        ->where('tenant_id', $tenantId)
        ->orderBy('createdat', 'desc')
        ->paginate($perPage);

        return response()->json($records);
    }

    // Get filtered leads
    public function filterLeads(Request $request)
    {
        $userId = Auth::id();
        $tenantId = Auth::user()->tenant_id;
        $perPage = $request->get('per_page', 10);

        $query = SalesRecord::with([
            'status',
            'prospectus',
            'city',
            'state',
            'businessType',
            'leadSource',
            'product',
            'latestRemark'
        ])
        ->where('user_id', $userId)
        ->where('tenant_id', $tenantId);

        // Apply filters
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('state_id')) {
            $query->whereHas('prospectus', function($q) use ($request) {
                $q->where('state_id', $request->state_id);
            });
        }

        if ($request->filled('city_id')) {
            $query->whereHas('prospectus', function($q) use ($request) {
                $q->where('city_id', $request->city_id);
            });
        }

        if ($request->filled('business_type_id')) {
            $query->whereHas('prospectus', function($q) use ($request) {
                $q->where('business_type_id', $request->business_type_id);
            });
        }

        if ($request->filled('lead_source_id')) {
            $query->where('lead_source_id', $request->lead_source_id);
        }

        if ($request->filled('products_id')) {
            $query->where('products_id', $request->products_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('leads_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('prospectus', function($pq) use ($search) {
                      $pq->where('prospectus_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('createdat', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('createdat', '<=', $request->date_to);
        }

        if ($request->filled('follow_up_date_from')) {
            $query->whereDate('next_follow_up_date', '>=', $request->follow_up_date_from);
        }

        if ($request->filled('follow_up_date_to')) {
            $query->whereDate('next_follow_up_date', '<=', $request->follow_up_date_to);
        }

        $records = $query->orderBy('createdat', 'desc')->paginate($perPage);

        return response()->json($records);
    }

    // Get filter options for dropdowns
    public function getFilterOptions()
    {
        $tenantId = Auth::user()->tenant_id;

        $options = [
            'statuses' => SalesStatus::where('tenant_id', $tenantId)
                ->orderBy('status_name')
                ->get(['id', 'status_name']),
            
            'states' => State::orderBy('state_name')
                ->get(['id', 'state_name']),
            
            'cities' => City::orderBy('city_name')
                ->get(['id', 'city_name']),
            
            'business_types' => SalesBusinessType::where('tenant_id', $tenantId)
                ->orderBy('business_name')
                ->get(['id', 'business_name']),
            
            'lead_sources' => SalesLeadSource::where('tenant_id', $tenantId)
                ->orderBy('source_name')
                ->get(['id', 'source_name']),
            
            'products' => SalesProduct::where('tenant_id', $tenantId)
                ->orderBy('product_name')
                ->get(['id', 'product_name']),
        ];

        return response()->json($options);
    }

    // Get cities by state
    public function getCitiesByState($stateId)
    {
        $cities = City::where('state_id', $stateId)
            ->orderBy('city_name')
            ->get(['id', 'city_name']);

        return response()->json($cities);
    }

    // Get lead statistics for the user
    public function getLeadStats()
    {
        $userId = Auth::id();
        $tenantId = Auth::user()->tenant_id;

        $stats = [
            'total_leads' => SalesRecord::where('user_id', $userId)
                ->where('tenant_id', $tenantId)
                ->count(),
            
            'leads_this_month' => SalesRecord::where('user_id', $userId)
                ->where('tenant_id', $tenantId)
                ->whereMonth('createdat', Carbon::now()->month)
                ->whereYear('createdat', Carbon::now()->year)
                ->count(),
            
            'leads_this_week' => SalesRecord::where('user_id', $userId)
                ->where('tenant_id', $tenantId)
                ->whereBetween('createdat', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])
                ->count(),
            
            'leads_today' => SalesRecord::where('user_id', $userId)
                ->where('tenant_id', $tenantId)
                ->whereDate('createdat', Carbon::today())
                ->count(),
            
            'status_distribution' => SalesRecord::where('user_id', $userId)
                ->where('tenant_id', $tenantId)
                ->with('status')
                ->get()
                ->groupBy('status.status_name')
                ->map(function($group) {
                    return $group->count();
                }),
            
            'follow_ups_due_today' => SalesRecord::where('user_id', $userId)
                ->where('tenant_id', $tenantId)
                ->whereDate('next_follow_up_date', Carbon::today())
                ->count(),
            
            'follow_ups_due_this_week' => SalesRecord::where('user_id', $userId)
                ->where('tenant_id', $tenantId)
                ->whereBetween('next_follow_up_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])
                ->count(),
        ];

        return response()->json($stats);
    }

    // Export user's leads
    public function exportLeads(Request $request)
    {
        $userId = Auth::id();
        $tenantId = Auth::user()->tenant_id;

        $query = SalesRecord::with([
            'status',
            'prospectus',
            'city',
            'state',
            'businessType',
            'leadSource',
            'product',
            'latestRemark'
        ])
        ->where('user_id', $userId)
        ->where('tenant_id', $tenantId);

        // Apply same filters as filterLeads method
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('leads_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $leads = $query->orderBy('createdat', 'desc')->get();

        return response()->json($leads);
    }
}
