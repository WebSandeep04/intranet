<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SalesRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class AllDataController extends Controller
{

    public function index(){
        return view('alldata');
    }

    
    public function fetchalldata()
{
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
        )
        ->paginate(2);

    return response()->json($records);
}


// adddatafilter

 public function alldatafilter(Request $request)
{
    $userId = Auth::id();
    $query = DB::table('sales_records')
        ->join('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->leftJoin('states', 'prospectuses.state_id', '=', 'states.id')
        ->leftJoin('cities', 'prospectuses.city_id', '=', 'cities.id')
        ->leftJoin('sales_business_types', 'prospectuses.business_type_id', '=', 'sales_business_types.id')
        ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->leftJoin('remarks as r', function ($join) {
            $join->on('r.sales_remark_id', '=', 'sales_records.id')
                 ->whereRaw('r.remark_date = (
                    SELECT MAX(remark_date) 
                    FROM remarks 
                    WHERE sales_remark_id = sales_records.id
                 )');
        });

    // Apply filters
    if ($request->status) {
        $query->where('sales_records.status_id', $request->status);
    }

    if ($request->state) {
        $query->where('prospectuses.state_id', $request->state);
    }

    if ($request->city) {
        $query->where('prospectuses.city_id', $request->city);
    }

    if ($request->business) {
        $query->where('prospectuses.business_type_id', $request->business);
    }

    if ($request->source) {
        $query->where('sales_records.lead_source_id', $request->source);
    }

    if ($request->product) {
        $query->where('sales_records.products_id', $request->product);
    }

    $sales = $query->select(
        'sales_records.*',
        'prospectuses.prospectus_name',
        'states.state_name',
        'cities.city_name',
        'sales_business_types.business_name',
        'sales_lead_sources.source_name',
        'sales_products.product_name',
        'sales_status.status_name',
        'r.remark as last_remark',
        'r.remark_date'
    )->paginate(2);

    return response()->json($sales);
}

// alldatasearch
public function alldatasearch(Request $request)
{
    $userId = Auth::id();
    $searchTerm = $request->input('search');

    $query = DB::table('sales_records')
        ->leftJoin('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->leftJoin('states', 'prospectuses.state_id', '=', 'states.id')
        ->leftJoin('cities', 'prospectuses.city_id', '=', 'cities.id')
        ->leftJoin('sales_business_types', 'prospectuses.business_type_id', '=', 'sales_business_types.id')
        ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->leftJoin('remarks as r', function ($join) {
            $join->on('r.sales_remark_id', '=', 'sales_records.id')
                ->whereRaw('r.remark_date = (
                    SELECT MAX(remark_date)
                    FROM remarks 
                    WHERE sales_remark_id = sales_records.id
                )');
        });

    // Apply search if provided
    if ($searchTerm) {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('sales_records.leads_name', 'like', "%$searchTerm%")
              ->orWhere('prospectuses.contact_person', 'like', "%$searchTerm%")
              ->orWhere('prospectuses.contact_number', 'like', "%$searchTerm%")
              ->orWhere('prospectuses.prospectus_name', 'like', "%$searchTerm%")
              ->orWhere('sales_status.status_name', 'like', "%$searchTerm%")
              ->orWhere('sales_business_types.business_name', 'like', "%$searchTerm%")
              ->orWhere('sales_lead_sources.source_name', 'like', "%$searchTerm%")
              ->orWhere('sales_products.product_name', 'like', "%$searchTerm%")
              ->orWhere('states.state_name', 'like', "%$searchTerm%")
              ->orWhere('cities.city_name', 'like', "%$searchTerm%");
        });
    }

    $sales = $query->select(
        'sales_records.*',
        'prospectuses.prospectus_name',
        'prospectuses.contact_person',
        'prospectuses.contact_number',
        'prospectuses.email',
        'states.state_name',
        'cities.city_name',
        'sales_business_types.business_name',
        'sales_lead_sources.source_name',
        'sales_products.product_name',
        'sales_status.status_name',
        'r.remark as last_remark',
        'r.remark_date'
    )->paginate(2);

    return response()->json($sales);
}

// date filter
public function alldatafilterdate(Request $request)
{
    $userId = Auth::id();
    $query = DB::table('sales_records')
        ->join('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->leftJoin('states', 'prospectuses.state_id', '=', 'states.id')
        ->leftJoin('cities', 'prospectuses.city_id', '=', 'cities.id')
        ->leftJoin('sales_business_types', 'prospectuses.business_type_id', '=', 'sales_business_types.id')
        ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->leftJoin('remarks as r', function ($join) {
            $join->on('r.sales_remark_id', '=', 'sales_records.id')
                 ->whereRaw('r.remark_date = (
                    SELECT MAX(remark_date) 
                    FROM remarks 
                    WHERE sales_remark_id = sales_records.id
                 )');
        });

    // Filter by next_follow_up_date between from and to
    if ($request->from_date && $request->to_date) {
        try {
            $from = Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay();
            $to = Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay();

            $query->whereBetween('sales_records.next_follow_up_date', [
                $from->format('Y-m-d'),
                $to->format('Y-m-d')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Invalid date format'
            ], 422);
        }
    }

    // Other filters
    if ($request->status) {
        $query->where('sales_records.status_id', $request->status);
    }
    if ($request->state) {
        $query->where('prospectuses.state_id', $request->state);
    }
    if ($request->city) {
        $query->where('prospectuses.city_id', $request->city);
    }
    if ($request->business) {
        $query->where('prospectuses.business_type_id', $request->business);
    }
    if ($request->source) {
        $query->where('sales_records.lead_source_id', $request->source);
    }
    if ($request->product) {
        $query->where('sales_records.products_id', $request->product);
    }

    $sales = $query->select(
        'sales_records.*',
        'prospectuses.prospectus_name',
        'states.state_name',
        'cities.city_name',
        'sales_business_types.business_name',
        'sales_lead_sources.source_name',
        'sales_products.product_name',
        'sales_status.status_name',
        'r.remark as last_remark',
        'r.remark_date'
    )->paginate(2);

    return response()->json($sales);
}

}
