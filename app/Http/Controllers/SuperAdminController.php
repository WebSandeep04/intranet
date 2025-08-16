<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;
use App\Models\User;
use App\Models\SalesRecord;
use App\Models\SalesStatus;
use App\Models\SalesLeadSource;
use App\Models\SalesProduct;
use App\Models\SalesBusinessType;
use App\Models\State;
use App\Models\City;
use App\Models\Prospectus;
use App\Models\Remark;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    // Middleware is applied in routes, not in constructor

    public function dashboard()
    {
        return view('superadmin.dashboard');
    }

    public function totaltenant()
    {
        $total = Tenant::count();
        return response()->json(['total_tenants' => $total]);
    }

    public function viewtenant(){
        return redirect()->route('tenant');
    }

    // Dashboard Statistics
    public function dashboardStats()
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'total_users' => User::count(),
            'total_sales_records' => SalesRecord::count(),
            'total_prospectuses' => Prospectus::count(),
            'recent_activities' => $this->getRecentActivities(),
            'tenant_stats' => $this->getTenantStats(),
            'monthly_growth' => $this->getMonthlyGrowth(),
        ];

        return response()->json($stats);
    }

    // Get recent activities across all tenants
    public function getRecentActivities()
    {
        $activities = [];
        
        // Recent sales records
        $recentSales = SalesRecord::with(['tenant', 'user'])
            ->orderBy('createdat', 'desc')
            ->limit(10)
            ->get();
        
        foreach ($recentSales as $sale) {
            $activities[] = [
                'type' => 'sales_record',
                'message' => "New sales record created by {$sale->user->name} in {$sale->tenant->tenant_name}",
                'date' => $sale->createdat,
                'tenant' => $sale->tenant->tenant_name,
                'user' => $sale->user->name
            ];
        }

        // Recent users
        $recentUsers = User::with('tenant')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        foreach ($recentUsers as $user) {
            $activities[] = [
                'type' => 'user',
                'message' => "New user {$user->name} registered in {$user->tenant->tenant_name}",
                'date' => $user->created_at,
                'tenant' => $user->tenant->tenant_name,
                'user' => $user->name
            ];
        }

        // Sort by date
        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($activities, 0, 15);
    }

    // Get statistics for each tenant
    public function getTenantStats()
    {
        $tenants = Tenant::withCount([
            'users',
            'salesRecords',
            'prospectuses'
        ])->get();

        $stats = [];
        foreach ($tenants as $tenant) {
            $stats[] = [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->tenant_name,
                'tenant_code' => $tenant->tenant_code,
                'users_count' => $tenant->users_count,
                'sales_records_count' => $tenant->sales_records_count,
                'prospectuses_count' => $tenant->prospectuses_count,
                'created_at' => $tenant->created_at->format('Y-m-d'),
                'last_activity' => $this->getLastActivity($tenant->id)
            ];
        }

        return $stats;
    }

    // Get last activity for a tenant
    private function getLastActivity($tenantId)
    {
        $lastSalesRecord = SalesRecord::where('tenant_id', $tenantId)
            ->orderBy('createdat', 'desc')
            ->first();

        $lastUser = User::where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastSalesRecord && $lastUser) {
            return max($lastSalesRecord->createdat, $lastUser->created_at);
        } elseif ($lastSalesRecord) {
            return $lastSalesRecord->createdat;
        } elseif ($lastUser) {
            return $lastUser->created_at;
        }

        return null;
    }

    // Get monthly growth statistics
    public function getMonthlyGrowth()
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = [
                'month' => $date->format('M Y'),
                'tenants' => Tenant::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'users' => User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'sales_records' => SalesRecord::whereYear('createdat', $date->year)
                    ->whereMonth('createdat', $date->month)
                    ->count()
            ];
        }

        return $months;
    }

    // Tenant Activity Monitoring
    public function tenantActivity($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        
        $activities = [
            'recent_sales' => SalesRecord::where('tenant_id', $tenantId)
                ->with(['user', 'status'])
                ->orderBy('createdat', 'desc')
                ->limit(10)
                ->get(),
            'recent_users' => User::where('tenant_id', $tenantId)
                ->with('role')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            'recent_prospectuses' => Prospectus::where('tenant_id', $tenantId)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            'data_summary' => [
                'total_users' => User::where('tenant_id', $tenantId)->count(),
                'total_sales_records' => SalesRecord::where('tenant_id', $tenantId)->count(),
                'total_prospectuses' => Prospectus::where('tenant_id', $tenantId)->count(),
                'total_remarks' => Remark::where('tenant_id', $tenantId)->count(),
            ]
        ];

        return response()->json($activities);
    }

    // System-wide analytics
    public function systemAnalytics()
    {
        $analytics = [
            'total_data' => [
                'tenants' => Tenant::count(),
                'users' => User::count(),
                'sales_records' => SalesRecord::count(),
                'prospectuses' => Prospectus::count(),
                'remarks' => Remark::count(),
            ],
            'top_tenants' => $this->getTopTenants(),
            'user_distribution' => $this->getUserDistribution(),
            'sales_trends' => $this->getSalesTrends(),
        ];

        return response()->json($analytics);
    }

    // Get top tenants by activity
    private function getTopTenants()
    {
        return Tenant::withCount('salesRecords')
            ->orderBy('sales_records_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($tenant) {
                return [
                    'tenant_name' => $tenant->tenant_name,
                    'tenant_code' => $tenant->tenant_code,
                    'sales_count' => $tenant->sales_records_count,
                    'users_count' => $tenant->users()->count()
                ];
            });
    }

    // Get user distribution by role
    private function getUserDistribution()
    {
        return DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->select('roles.name as role_name', DB::raw('count(*) as count'))
            ->groupBy('roles.id', 'roles.name')
            ->get();
    }

    // Get sales trends
    private function getSalesTrends()
    {
        $trends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $trends[] = [
                'date' => $date->format('Y-m-d'),
                'count' => SalesRecord::whereDate('createdat', $date->format('Y-m-d'))->count()
            ];
        }
        return $trends;
    }

    // Export tenant data
    public function exportTenantData($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        
        $data = [
            'tenant_info' => $tenant,
            'users' => User::where('tenant_id', $tenantId)->get(),
            'sales_records' => SalesRecord::where('tenant_id', $tenantId)->get(),
            'prospectuses' => Prospectus::where('tenant_id', $tenantId)->get(),
            'remarks' => Remark::where('tenant_id', $tenantId)->get(),
        ];

        return response()->json($data);
    }
}
