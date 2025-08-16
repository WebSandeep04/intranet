<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesDashboardController extends Controller
{
public function todayfollowups()
{
    $userId = Auth::id();
    $today = Carbon::today()->toDateString();

    $totalLeads = DB::table('sales_records')
        ->where('user_id', $userId)
        ->where(function ($query) use ($today) {
            $query->whereDate('next_follow_up_date', '<=', $today)
                  ->orWhere(function ($q) use ($today) {
                      $q->whereDate('next_follow_up_date', '>', $today)
                        ->whereDate('updatedat', $today);
                  });
        })
        ->whereNotIn('status_id', [1, 2])
        ->count();

    return response()->json(['totalLeads' => $totalLeads]);
}


 public function underprocess()
{
    $userId = Auth::id();
    $today = Carbon::today()->toDateString();

    $underprocess = DB::table('sales_records')
        ->where('user_id', $userId)
        ->whereNotIn('status_id', [1, 2])
        ->whereDate('updatedat', $today)
        ->whereDate('next_follow_up_date', $today)
        ->count();

    return response()->json(['underprocess' => $underprocess]);
}

 public function todaycompleted()
{
    $userId = Auth::id();
    $today = Carbon::today()->toDateString();

    $todaycompleted = DB::table('sales_records')
        ->where('user_id', $userId)
        ->whereNotIn('status_id', [1, 2])
        ->whereDate('updatedat', $today)
        ->whereDate('next_follow_up_date', '>', $today)
        ->count();

    return response()->json(['todaycompleted' => $todaycompleted]);
}

public function todaypending()
{
    $userId = Auth::id();
    $today = Carbon::today()->toDateString();

    $todaypending = DB::table('sales_records')
        ->where('user_id', $userId)
        ->whereNotIn('status_id', [1, 2])
        ->where(function ($query) use ($today) {
            $query->whereDate('next_follow_up_date', '<=', $today)
                  ->orWhereNull('next_follow_up_date');
        })
        ->count();

    return response()->json(['todaypending' => $todaypending]);
}

public function todaynew()
{
    $userId = Auth::id();
    $today = Carbon::today()->toDateString();

    $todaynew = DB::table('sales_records')
        ->where('user_id', $userId)
        ->whereNotIn('status_id', [1, 2])
        ->whereDate('createdat', $today)
        ->count();

    return response()->json(['todaynew' => $todaynew]);
}

public function allleads()
{
    $userId = Auth::id();

    $leadCount = DB::table('sales_records')
        ->where('user_id', $userId)
        ->count();

    return response()->json(['allleads' => $leadCount]);
}


public function estimateticket()
{
    $userId = Auth::id();

    $estimateticket = DB::table('sales_records')
        ->where('user_id', $userId)
        ->sum('ticket_value');

    return response()->json(['estimateticket' => $estimateticket]);
}


public function piedata()
{
    $data = DB::table('sales_status as ss')
        ->leftJoin('sales_records as sr', 'ss.id', '=', 'sr.status_id')
        ->select('ss.status_name', DB::raw('COUNT(sr.id) as count'))
        ->groupBy('ss.status_name')
        ->get();

    return response()->json($data);
}


public function bardata()
{
    $userId = Auth::id();
    $monthlyData = DB::table('sales_records')
        ->selectRaw('MONTH(createdat) as month, COUNT(*) as count')
        ->whereYear('createdat', Carbon::now()->year)
        ->where('user_id', $userId) // Optional
        ->groupBy(DB::raw('MONTH(createdat)'))
        ->pluck('count', 'month');

    // Format data for Chart.js
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $data = [];

    foreach (range(1, 12) as $month) {
        $data[] = $monthlyData[$month] ?? 0;
    }

    return response()->json([
        'labels' => $months,
        'data' => $data
    ]);
}


public function todayfollowupstable(){
    return view('todayfollowupstable');
}

public function underprocesstable(){
    return view('underprocess');
}

public function todaycompletedtable(){
    return view('todaycompletedtable');
}
public function todaypendingtable(){
    return view('todaypendingtable');
}
public function todaynewtable(){
    return view('todaynewtable');
}



// for table data


public function todayfollowupstabledata()
{
    $userId = Auth::id();
    $today = Carbon::today()->toDateString();

    $records = DB::table('sales_records')
        ->join('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->join('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->join('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
        ->join('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->join('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->join('states', 'sales_records.state_id', '=', 'states.id')
        ->join('cities', 'sales_records.city_id', '=', 'cities.id')
        ->leftJoin(DB::raw('(
            SELECT r1.id, r1.sales_remark_id, r1.remark
            FROM remarks r1
            INNER JOIN (
                SELECT sales_remark_id, MAX(remark_date) as latest_date
                FROM remarks
                GROUP BY sales_remark_id
            ) r2 ON r1.sales_remark_id = r2.sales_remark_id AND r1.remark_date = r2.latest_date
        ) as latest_remarks'), 'sales_records.id', '=', 'latest_remarks.sales_remark_id')
        ->where('sales_records.user_id', $userId)
        ->where(function ($query) use ($today) {
            $query->whereDate('sales_records.next_follow_up_date', '<=', $today)
                  ->orWhere(function ($q) use ($today) {
                      $q->whereDate('sales_records.next_follow_up_date', '>', $today)
                        ->whereDate('sales_records.updatedat', '=', $today);
                  });
        })
        ->whereNotIn('sales_records.status_id', [1, 2])
        ->orderBy('sales_records.next_follow_up_date', 'asc')
        ->select(
            'sales_records.*',
            'sales_status.status_name',
            'prospectuses.prospectus_name',
            'sales_business_types.business_name',
            'sales_lead_sources.source_name',
            'sales_products.product_name',
            'states.state_name',
            'cities.city_name',
            'latest_remarks.remark as latest_remark'
        )->paginate(5);

    return response()->json($records);
}


public function todayunderprocessfollowupstabledata()
{
    $userId = Auth::id();
    $today =Carbon::today()->toDateString();

$records = DB::table('sales_records')
    ->join('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
    ->join('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
    ->join('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
    ->join('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
    ->join('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
    ->join('states', 'sales_records.state_id', '=', 'states.id')
    ->join('cities', 'sales_records.city_id', '=', 'cities.id')
    ->leftJoin(DB::raw('(
        SELECT r1.id, r1.sales_remark_id, r1.remark
        FROM remarks r1
        INNER JOIN (
            SELECT sales_remark_id, MAX(remark_date) as latest_date
            FROM remarks
            GROUP BY sales_remark_id
        ) r2 ON r1.sales_remark_id = r2.sales_remark_id AND r1.remark_date = r2.latest_date
    ) as latest_remarks'), 'sales_records.id', '=', 'latest_remarks.sales_remark_id')
    ->where('sales_records.user_id', $userId)
    ->whereDate('sales_records.next_follow_up_date', '=', $today)
    ->whereDate('sales_records.updatedat', '=', $today)
    ->whereNotIn('sales_records.status_id', [1, 2])
    ->orderBy('sales_records.next_follow_up_date', 'asc')
    ->select(
        'sales_records.*',
        'sales_status.status_name',
        'prospectuses.prospectus_name',
        'sales_business_types.business_name',
        'sales_lead_sources.source_name',
        'sales_products.product_name',
        'states.state_name',
        'cities.city_name',
        'latest_remarks.remark as latest_remark'
    )
    ->paginate(10);


    return response()->json($records);
}
public function todaycompletedfollowupstabledata()
{
    $userId = Auth::id();
    $today =Carbon::today()->toDateString();

$records = DB::table('sales_records')
    ->join('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
    ->join('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
    ->join('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
    ->join('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
    ->join('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
    ->join('states', 'sales_records.state_id', '=', 'states.id')
    ->join('cities', 'sales_records.city_id', '=', 'cities.id')
    ->leftJoin(DB::raw('(
        SELECT r1.id, r1.sales_remark_id, r1.remark
        FROM remarks r1
        INNER JOIN (
            SELECT sales_remark_id, MAX(remark_date) as latest_date
            FROM remarks
            GROUP BY sales_remark_id
        ) r2 ON r1.sales_remark_id = r2.sales_remark_id AND r1.remark_date = r2.latest_date
    ) as latest_remarks'), 'sales_records.id', '=', 'latest_remarks.sales_remark_id')
    ->where('sales_records.user_id', $userId)
    ->whereDate('sales_records.next_follow_up_date', '>', $today)
    ->whereDate('sales_records.updatedat', '=', $today)
    ->whereNotIn('sales_records.status_id', [1, 2])
    ->orderBy('sales_records.next_follow_up_date', 'asc')
    ->select(
        'sales_records.*',
        'sales_status.status_name',
        'prospectuses.prospectus_name',
        'sales_business_types.business_name',
        'sales_lead_sources.source_name',
        'sales_products.product_name',
        'states.state_name',
        'cities.city_name',
        'latest_remarks.remark as latest_remark'
    )
    ->paginate(10);


    return response()->json($records);
}
public function todaypendingfollowupstabledata()
{
    $userId = Auth::id();
    $today =Carbon::today()->toDateString();

$records = DB::table('sales_records')
    ->join('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
    ->join('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
    ->join('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
    ->join('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
    ->join('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
    ->join('states', 'sales_records.state_id', '=', 'states.id')
    ->join('cities', 'sales_records.city_id', '=', 'cities.id')
    ->leftJoin(DB::raw('(
        SELECT r1.id, r1.sales_remark_id, r1.remark
        FROM remarks r1
        INNER JOIN (
            SELECT sales_remark_id, MAX(remark_date) as latest_date
            FROM remarks
            GROUP BY sales_remark_id
        ) r2 ON r1.sales_remark_id = r2.sales_remark_id AND r1.remark_date = r2.latest_date
    ) as latest_remarks'), 'sales_records.id', '=', 'latest_remarks.sales_remark_id')
    ->where('sales_records.user_id', $userId)
    ->whereDate('sales_records.next_follow_up_date', '<=', $today)
    // ->whereDate('sales_records.updatedat', '=', $today)
    ->whereNotIn('sales_records.status_id', [1, 2])
    ->orderBy('sales_records.next_follow_up_date', 'asc')
    ->select(
        'sales_records.*',
        'sales_status.status_name',
        'prospectuses.prospectus_name',
        'sales_business_types.business_name',
        'sales_lead_sources.source_name',
        'sales_products.product_name',
        'states.state_name',
        'cities.city_name',
        'latest_remarks.remark as latest_remark'
    )
    ->paginate(10);


    return response()->json($records);
}
public function todaynewfollowupstabledata()
{
    $userId = Auth::id();
    $today =Carbon::today()->toDateString();

$records = DB::table('sales_records')
    ->join('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
    ->join('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
    ->join('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
    ->join('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
    ->join('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
    ->join('states', 'sales_records.state_id', '=', 'states.id')
    ->join('cities', 'sales_records.city_id', '=', 'cities.id')
    ->leftJoin(DB::raw('(
        SELECT r1.id, r1.sales_remark_id, r1.remark
        FROM remarks r1
        INNER JOIN (
            SELECT sales_remark_id, MAX(remark_date) as latest_date
            FROM remarks
            GROUP BY sales_remark_id
        ) r2 ON r1.sales_remark_id = r2.sales_remark_id AND r1.remark_date = r2.latest_date
    ) as latest_remarks'), 'sales_records.id', '=', 'latest_remarks.sales_remark_id')
    ->where('sales_records.user_id', $userId)
    ->whereDate('sales_records.createdat', '=', $today)
    ->whereNotIn('sales_records.status_id', [1, 2])
    ->orderBy('sales_records.next_follow_up_date', 'asc')
    ->select(
        'sales_records.*',
        'sales_status.status_name',
        'prospectuses.prospectus_name',
        'sales_business_types.business_name',
        'sales_lead_sources.source_name',
        'sales_products.product_name',
        'states.state_name',
        'cities.city_name',
        'latest_remarks.remark as latest_remark'
    )
    ->paginate(10);


    return response()->json($records);
}


// for searching

public function searchFollowups(Request $request)
{
    $userId = Auth::id();
    $today = Carbon::today()->toDateString();
    $search = $request->input('search');

    $query = DB::table('sales_records')
        ->join('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->join('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->join('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
        ->join('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->join('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->join('states', 'sales_records.state_id', '=', 'states.id')
        ->join('cities', 'sales_records.city_id', '=', 'cities.id')
        ->leftJoin('remarks', function ($join) {
            $join->on('remarks.sales_remark_id', '=', 'sales_records.id')
                ->whereRaw('remarks.remark_date = (select max(r2.remark_date) from remarks r2 where r2.sales_remark_id = sales_records.id)');
        })
        ->select(
            'sales_records.*',
            'sales_status.status_name',
            'prospectuses.prospectus_name',
            'sales_business_types.business_name',
            'sales_lead_sources.source_name',
            'sales_products.product_name',
            'states.state_name',
            'cities.city_name',
            'remarks.remark as latest_remark'
        )
                ->where('sales_records.user_id', $userId)
        ->where(function ($query) use ($today) {
            $query->whereDate('sales_records.next_follow_up_date', '<=', $today)
                  ->orWhere(function ($q) use ($today) {
                      $q->whereDate('sales_records.next_follow_up_date', '>', $today)
                        ->whereDate('sales_records.updatedat', '=', $today);
                  });
        })
        ->whereNotIn('sales_records.status_id', [1, 2])
        ->orderBy('sales_records.next_follow_up_date', 'asc');

    if (!empty($search)) {
        $query->where('sales_records.leads_name', 'LIKE', "%{$search}%");
    }

    $records = $query->orderBy('sales_records.next_follow_up_date')->get();

    return response()->json($records);
}



public function searchunderprocessFollowups(Request $request)
{
    $userId = Auth::id();
    $today = Carbon::today()->toDateString();
    $search = $request->input('search');

    $query = DB::table('sales_records')
        ->join('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->join('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->join('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
        ->join('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->join('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->join('states', 'sales_records.state_id', '=', 'states.id')
        ->join('cities', 'sales_records.city_id', '=', 'cities.id')
        ->leftJoin('remarks', function ($join) {
            $join->on('remarks.sales_remark_id', '=', 'sales_records.id')
                ->whereRaw('remarks.remark_date = (select max(r2.remark_date) from remarks r2 where r2.sales_remark_id = sales_records.id)');
        })
        ->select(
            'sales_records.*',
            'sales_status.status_name',
            'prospectuses.prospectus_name',
            'sales_business_types.business_name',
            'sales_lead_sources.source_name',
            'sales_products.product_name',
            'states.state_name',
            'cities.city_name',
            'remarks.remark as latest_remark'
        )
       ->where('sales_records.user_id', $userId)
    ->whereDate('sales_records.next_follow_up_date', '=', $today)
    ->whereDate('sales_records.updatedat', '=', $today)
    ->whereNotIn('sales_records.status_id', [1, 2])
    ->orderBy('sales_records.next_follow_up_date', 'asc');

    if (!empty($search)) {
        $query->where('sales_records.leads_name', 'LIKE', "%{$search}%");
    }

    $records = $query->orderBy('sales_records.next_follow_up_date')->get();

    return response()->json($records);
}

public function searchcompletedFollowups(Request $request)
{
    $userId = Auth::id();
    $today = Carbon::today()->toDateString();
    $search = $request->input('search');

    $query = DB::table('sales_records')
        ->join('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->join('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->join('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
        ->join('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->join('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->join('states', 'sales_records.state_id', '=', 'states.id')
        ->join('cities', 'sales_records.city_id', '=', 'cities.id')
        ->leftJoin('remarks', function ($join) {
            $join->on('remarks.sales_remark_id', '=', 'sales_records.id')
                ->whereRaw('remarks.remark_date = (select max(r2.remark_date) from remarks r2 where r2.sales_remark_id = sales_records.id)');
        })
        ->select(
            'sales_records.*',
            'sales_status.status_name',
            'prospectuses.prospectus_name',
            'sales_business_types.business_name',
            'sales_lead_sources.source_name',
            'sales_products.product_name',
            'states.state_name',
            'cities.city_name',
            'remarks.remark as latest_remark'
        )
           ->where('sales_records.user_id', $userId)
    ->whereDate('sales_records.next_follow_up_date', '>', $today)
    ->whereDate('sales_records.updatedat', '=', $today)
    ->whereNotIn('sales_records.status_id', [1, 2])
    ->orderBy('sales_records.next_follow_up_date', 'asc');

    if (!empty($search)) {
        $query->where('sales_records.leads_name', 'LIKE', "%{$search}%");
    }

    $records = $query->orderBy('sales_records.next_follow_up_date')->get();

    return response()->json($records);
}
public function searchpendingFollowups(Request $request)
{
    $userId = Auth::id();
    $today = Carbon::today()->toDateString();
    $search = $request->input('search');

    $query = DB::table('sales_records')
        ->join('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->join('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->join('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
        ->join('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->join('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->join('states', 'sales_records.state_id', '=', 'states.id')
        ->join('cities', 'sales_records.city_id', '=', 'cities.id')
        ->leftJoin('remarks', function ($join) {
            $join->on('remarks.sales_remark_id', '=', 'sales_records.id')
                ->whereRaw('remarks.remark_date = (select max(r2.remark_date) from remarks r2 where r2.sales_remark_id = sales_records.id)');
        })
        ->select(
            'sales_records.*',
            'sales_status.status_name',
            'prospectuses.prospectus_name',
            'sales_business_types.business_name',
            'sales_lead_sources.source_name',
            'sales_products.product_name',
            'states.state_name',
            'cities.city_name',
            'remarks.remark as latest_remark'
        )
           ->where('sales_records.user_id', $userId)
    ->whereDate('sales_records.next_follow_up_date', '<=', $today)
    // ->whereDate('sales_records.updatedat', '=', $today)
    ->whereNotIn('sales_records.status_id', [1, 2])
    ->orderBy('sales_records.next_follow_up_date', 'asc');

    if (!empty($search)) {
        $query->where('sales_records.leads_name', 'LIKE', "%{$search}%");
    }

    $records = $query->orderBy('sales_records.next_follow_up_date')->get();

    return response()->json($records);
}
public function searchnewFollowups(Request $request)
{
    $userId = Auth::id();
    $today = Carbon::today()->toDateString();
    $search = $request->input('search');

    $query = DB::table('sales_records')
        ->join('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->join('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->join('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
        ->join('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->join('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->join('states', 'sales_records.state_id', '=', 'states.id')
        ->join('cities', 'sales_records.city_id', '=', 'cities.id')
        ->leftJoin('remarks', function ($join) {
            $join->on('remarks.sales_remark_id', '=', 'sales_records.id')
                ->whereRaw('remarks.remark_date = (select max(r2.remark_date) from remarks r2 where r2.sales_remark_id = sales_records.id)');
        })
        ->select(
            'sales_records.*',
            'sales_status.status_name',
            'prospectuses.prospectus_name',
            'sales_business_types.business_name',
            'sales_lead_sources.source_name',
            'sales_products.product_name',
            'states.state_name',
            'cities.city_name',
            'remarks.remark as latest_remark'
        )
          ->where('sales_records.user_id', $userId)
    ->whereDate('sales_records.createdat', '=', $today)
    ->whereNotIn('sales_records.status_id', [1, 2])
    ->orderBy('sales_records.next_follow_up_date', 'asc');

    if (!empty($search)) {
        $query->where('sales_records.leads_name', 'LIKE', "%{$search}%");
    }

    $records = $query->orderBy('sales_records.next_follow_up_date')->get();

    return response()->json($records);
}










}
