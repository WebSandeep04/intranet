<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AllDataController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerProjectController;
use App\Http\Controllers\FollowupController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProspectusController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RemarkController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WorklogController;
use App\Http\Controllers\WorklogHistoryController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SalesBusinessTypeController;
use App\Http\Controllers\SalesCityController;
use App\Http\Controllers\SalesDashboardController;
use App\Http\Controllers\SalesLeadController;
use App\Http\Controllers\SalesLeadSourceController;
use App\Http\Controllers\SalesProductController;
use App\Http\Controllers\SalesStateController;
use App\Http\Controllers\SalesStatusController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\MyLeadsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Show login form
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Handle login
Route::post('/login', [AuthController::class, 'login']);

// Handle logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Show dashboard page (protected)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

Route::get('/superadmindashboard', function () {
    return view('superadmindashboard');
})->middleware('auth');


// Root URL logic
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect()->route('login');
});

Route::get('/prospect', function(){
    return View('/prospect');
})->name('prospect');


//status routes

Route::get('/status/fetch', [SalesStatusController::class, 'fetchSaleStatus'])->name('status.fetch');
Route::get('/status', [SalesStatusController::class, 'index'])->name('status');
Route::put('/status/{id}', [SalesStatusController::class, 'update']);
Route::delete('/status/{id}', [SalesStatusController::class, 'destroy']);
Route::post('/status/store', [SalesStatusController::class, 'store'])->name('status.store');
Route::get('/getStatuses', [SalesStatusController::class, 'getStatuses'])->name('getStatuses');

// 

//lead source
Route::get('/source/fetch', [SalesLeadSourceController::class, 'fetchSalesources'])->name('source.fetch');
Route::get('/source', [SalesLeadSourceController::class, 'index'])->name('source');
Route::put('/source/{id}', [SalesLeadSourceController::class, 'update']);
Route::delete('/source/{id}', [SalesLeadSourceController::class, 'destroy']);
Route::post('/source/store', [SalesLeadSourceController::class, 'store'])->name('source.store');
Route::get('/getsource', [SalesLeadSourceController::class, 'getsource'])->name('getsource');


//sales product
Route::get('/product/fetch', [SalesProductController::class, 'fetchSalesProducts'])->name('product.fetch');
Route::get('/product', [SalesProductController::class, 'index'])->name('product');
Route::put('/product/{id}', [SalesProductController::class, 'update']);
Route::delete('/product/{id}', [SalesProductController::class, 'destroy']);
Route::post('/product/store', [SalesProductController::class, 'store'])->name('product.store');
Route::get('/getproduct', [SalesProductController::class, 'getproduct'])->name('getproduct');


//sales business type
Route::get('/business/fetch', [SalesBusinessTypeController::class, 'fetchSalesBusiness'])->name('business.fetch');
Route::get('/business', [SalesBusinessTypeController::class, 'index'])->name('business');
Route::put('/business/{id}', [SalesBusinessTypeController::class, 'update']);
Route::delete('/business/{id}', [SalesBusinessTypeController::class, 'destroy']);
Route::post('/business/store', [SalesBusinessTypeController::class, 'store'])->name('business.store');
Route::get('/getbusiness', [SalesBusinessTypeController::class, 'getbusiness'])->name('getbusiness');

//sales state
Route::get('/state/fetch', [SalesStateController::class, 'fetchSalesStates'])->name('state.fetch');
Route::get('/state', [SalesStateController::class, 'index'])->name('state');
Route::put('/state/{id}', [SalesStateController::class, 'update']);
Route::delete('/state/{id}', [SalesStateController::class, 'destroy']);
Route::post('/state/store', [SalesStateController::class, 'store'])->name('state.store');


//sales city
Route::get('/city/fetch', [SalesCityController::class, 'fetchSalesCities'])->name('city.fetch');
Route::get('/city', [SalesCityController::class, 'index'])->name('city');
Route::put('/city/{id}', [SalesCityController::class, 'update']);
Route::delete('/city/{id}', [SalesCityController::class, 'destroy']);
Route::post('/city/store', [SalesCityController::class, 'store'])->name('city.store');
Route::get('/city/{state_id}', [SalesCityController::class, 'getCities'])->name('get.city');
Route::get('/allcity', [SalesCityController::class, 'allcity'])->name('allcity');


// prospectus
Route::post('/prospectus', [ProspectusController::class, 'store']);
Route::get('/getProspectus', [ProspectusController::class, 'getProspectus'])->name('getProspectus');
Route::get('/fillprospectus/{id}', [ProspectusController::class, 'fillprospectus'])->name('fillprospectus');

//sales lead
Route::get('/lead', [SalesLeadController::class, 'index'])->name('lead');
Route::Post('/savelead', [SalesLeadController::class, 'store'])->name('savelead');

// sales followup

Route::get('/followup', [FollowupController::class, 'index'])->name('followup');
Route::post('/filter', [FollowupController::class, 'filter'])->name('filter');
Route::post('/filterdate', [FollowupController::class, 'filterdate'])->name('filterdate');
Route::get('/sales-records', [FollowupController::class, 'getSalesRecords'])->name('sales.records');
Route::get('/search', [FollowupController::class, 'search'])->name('search');




// sales Remark
Route::get('/remark', [RemarkController::class, 'index'])->name('remark');
Route::post('/saveremark', [RemarkController::class, 'store'])->name('saveremark');


// dashboard

Route::get('/todayfollowups', [SalesDashboardController::class, 'todayfollowups'])->name('todayfollowups');
Route::get('/allleads', [SalesDashboardController::class, 'allleads'])->name('allleads');
Route::get('/underprocess', [SalesDashboardController::class, 'underprocess'])->name('underprocess');
Route::get('/todaycompleted', [SalesDashboardController::class, 'todaycompleted'])->name('todaycompleted');
Route::get('/todaypending', [SalesDashboardController::class, 'todaypending'])->name('todaypending');
Route::get('/todaynew', [SalesDashboardController::class, 'todaynew'])->name('todaynew');
Route::get('/estimateticket', [SalesDashboardController::class, 'estimateticket'])->name('estimateticket');
Route::get('/piedata', [SalesDashboardController::class, 'piedata'])->name('piedata');
Route::get('/bardata', [SalesDashboardController::class, 'bardata'])->name('bardata');
Route::get('/todayfollowupstable', [SalesDashboardController::class, 'todayfollowupstable'])->name('todayfollowupstable');
Route::get('/underprocesstable', [SalesDashboardController::class, 'underprocesstable'])->name('underprocesstable');
Route::get('/todaycompletedtable', [SalesDashboardController::class, 'todaycompletedtable'])->name('todaycompletedtable');
Route::get('/todaypendingtable', [SalesDashboardController::class, 'todaypendingtable'])->name('todaypendingtable');
Route::get('/todaynewtable', [SalesDashboardController::class, 'todaynewtable'])->name('todaynewtable');

// todal followups
Route::get('/todayfollowupstabledata', [SalesDashboardController::class, 'todayfollowupstabledata'])->name('todayfollowupstabledata');
Route::get('/todayunderprocessfollowupstabledata', [SalesDashboardController::class, 'todayunderprocessfollowupstabledata'])->name('todayunderprocessfollowupstabledata');
Route::get('/todaycompletedfollowupstabledata', [SalesDashboardController::class, 'todaycompletedfollowupstabledata'])->name('todaycompletedfollowupstabledata');
Route::get('/todaypendingfollowupstabledata', [SalesDashboardController::class, 'todaypendingfollowupstabledata'])->name('todaypendingfollowupstabledata');
Route::get('/todaynewfollowupstabledata', [SalesDashboardController::class, 'todaynewfollowupstabledata'])->name('todaynewfollowupstabledata');

Route::get('/searchfollowups', [SalesDashboardController::class, 'searchFollowups'])->name('searchFollowups');
Route::get('/searchunderprocessFollowups', [SalesDashboardController::class, 'searchunderprocessFollowups'])->name('searchunderprocessFollowups');
Route::get('/searchcompletedFollowups', [SalesDashboardController::class, 'searchcompletedFollowups'])->name('searchcompletedFollowups');
Route::get('/searchpendingFollowups', [SalesDashboardController::class, 'searchpendingFollowups'])->name('searchpendingFollowups');
Route::get('/searchnewFollowups', [SalesDashboardController::class, 'searchnewFollowups'])->name('searchnewFollowups');




// user

Route::get('/user', [UserController::class, 'index'])->name('user');
Route::get('/fetchuser', [UserController::class, 'fetchuser'])->name('fetchuser');
Route::get('/user/fetch-for-manager', [UserController::class, 'fetchUsersForManager'])->name('fetchUsersForManager');
Route::get('/fetchrole', [RoleController::class, 'fetchrole'])->name('fetchrole');
Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.destroy');


// all data for admin

Route::get('/alldata', [AllDataController::class, 'index'])->name('alldata');
Route::get('/fetchalldata', [AllDataController::class, 'fetchalldata'])->name('fetchalldata');
Route::post('/alldatafilter', [AllDataController::class, 'alldatafilter'])->name('alldatafilter');
Route::get('/alldatasearch', [AllDataController::class, 'alldatasearch'])->name('alldatasearch');
Route::post('/alldatafilterdate', [AllDataController::class, 'alldatafilterdate'])->name('alldatafilterdate');


// super admin
Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/superadmin/dashboard', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/superadmin/stats', [SuperAdminController::class, 'dashboardStats'])->name('superadmin.stats');
    Route::get('/superadmin/analytics', [SuperAdminController::class, 'systemAnalytics'])->name('superadmin.analytics');
    Route::get('/superadmin/tenant/{id}/activity', [SuperAdminController::class, 'tenantActivity'])->name('superadmin.tenant.activity');
    Route::get('/superadmin/tenant/{id}/export', [SuperAdminController::class, 'exportTenantData'])->name('superadmin.tenant.export');
    Route::get('/totaltenant',[SuperAdminController::class, 'totaltenant'])->name('totaltenant');
    Route::get('/viewtenant',[SuperAdminController::class, 'viewtenant'])->name('viewtenant');
});

// tenant management
Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/tenant', [TenantController::class, 'index'])->name('tenant');
    Route::get('/tenant/fetch', [TenantController::class, 'fetchTenants'])->name('tenant.fetch');
    Route::post('/tenant/store', [TenantController::class, 'store'])->name('tenant.store');
    Route::put('/tenant/{id}', [TenantController::class, 'update'])->name('tenant.update');
    Route::delete('/tenant/{id}', [TenantController::class, 'destroy'])->name('tenant.destroy');
    Route::post('/tenant/{id}/regenerate-code', [TenantController::class, 'regenerateCode'])->name('tenant.regenerate-code');
});

// my leads
Route::middleware(['auth'])->group(function () {
    Route::get('/myleads', [MyLeadsController::class, 'index'])->name('myleads');
    Route::get('/myleads/data', [MyLeadsController::class, 'getMyLeads'])->name('myleads.data');
    Route::post('/myleads/filter', [MyLeadsController::class, 'filterLeads'])->name('myleads.filter');
    Route::get('/myleads/filter-options', [MyLeadsController::class, 'getFilterOptions'])->name('myleads.filter-options');
    Route::get('/myleads/cities/{stateId}', [MyLeadsController::class, 'getCitiesByState'])->name('myleads.cities');
    Route::get('/myleads/stats', [MyLeadsController::class, 'getLeadStats'])->name('myleads.stats');
    Route::post('/myleads/export', [MyLeadsController::class, 'exportLeads'])->name('myleads.export');
});

// Customer Project Module Routes
Route::middleware(['auth'])->group(function () {
    // Customer routes
    Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
    Route::get('/customer/fetch', [CustomerController::class, 'fetchCustomers'])->name('customer.fetch');
    Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');
    Route::put('/customer/{id}', [CustomerController::class, 'update'])->name('customer.update');
    Route::delete('/customer/{id}', [CustomerController::class, 'destroy'])->name('customer.destroy');

    // Project routes
    Route::get('/project', [ProjectController::class, 'index'])->name('project');
    Route::get('/project/fetch', [ProjectController::class, 'fetchProjects'])->name('project.fetch');
    Route::post('/project/store', [ProjectController::class, 'store'])->name('project.store');
    Route::put('/project/{id}', [ProjectController::class, 'update'])->name('project.update');
    Route::delete('/project/{id}', [ProjectController::class, 'destroy'])->name('project.destroy');

    // Module routes
    Route::get('/module', [ModuleController::class, 'index'])->name('module');
    Route::get('/module/fetch', [ModuleController::class, 'fetchModules'])->name('module.fetch');
    Route::get('/module/project/{projectId}', [ModuleController::class, 'getModulesByProject'])->name('module.by-project');
    Route::post('/module/store', [ModuleController::class, 'store'])->name('module.store');
    Route::put('/module/{id}', [ModuleController::class, 'update'])->name('module.update');
    Route::delete('/module/{id}', [ModuleController::class, 'destroy'])->name('module.destroy');

    // Customer Project routes
    Route::get('/customer-project', [CustomerProjectController::class, 'index'])->name('customer-project');
    Route::get('/customer-project/fetch', [CustomerProjectController::class, 'fetchCustomerProjects'])->name('customer-project.fetch');
    Route::post('/customer-project/store', [CustomerProjectController::class, 'store'])->name('customer-project.store');
    Route::put('/customer-project/{id}', [CustomerProjectController::class, 'update'])->name('customer-project.update');
    Route::delete('/customer-project/{id}', [CustomerProjectController::class, 'destroy'])->name('customer-project.destroy');
    Route::get('/customer-project/customers', [CustomerProjectController::class, 'getCustomers'])->name('customer-project.customers');
    Route::get('/customer-project/projects', [CustomerProjectController::class, 'getProjects'])->name('customer-project.projects');
                 Route::put('/customer-project/{customerProjectId}/module/{moduleId}/status', [CustomerProjectController::class, 'updateModuleStatus'])->name('customer-project.module-status');
             
             // Worklog routes
             Route::get('/worklog', [WorklogController::class, 'index'])->name('worklog');
             Route::get('/worklog/entry-types', [WorklogController::class, 'getEntryTypes'])->name('worklog.entry-types');
             Route::get('/worklog/customers', [WorklogController::class, 'getCustomers'])->name('worklog.customers');
             Route::get('/worklog/projects', [WorklogController::class, 'getProjects'])->name('worklog.projects');
             Route::get('/worklog/projects/customer/{customerId}', [WorklogController::class, 'getProjectsByCustomer'])->name('worklog.projects-by-customer');
             Route::get('/worklog/modules/{projectId}', [WorklogController::class, 'getModulesByProject'])->name('worklog.modules');
             Route::post('/worklog/check-date', [WorklogController::class, 'checkDateValidation'])->name('worklog.check-date');
Route::get('/worklog/missing-users', [WorklogController::class, 'getMissingUsersForDate'])->name('worklog.missing-users');
Route::get('/worklog/missing-summary', [WorklogController::class, 'getMissingEntriesSummary'])->name('worklog.missing-summary');
Route::post('/worklog/can-submit', [WorklogController::class, 'canSubmitWorklog'])->name('worklog.can-submit');
Route::get('/worklog-missing-summary', function() {
    return view('worklog.missing-summary');
})->name('worklog-missing-summary');
             Route::post('/worklog/add-to-session', [WorklogController::class, 'addToSession'])->name('worklog.add-to-session');
             Route::get('/worklog/pending-approvals', [WorklogController::class, 'getPendingApprovals'])->name('worklog.pending-approvals');
             Route::post('/worklog/{id}/approve', [WorklogController::class, 'approveWorklog'])->name('worklog.approve');
             Route::post('/worklog/{id}/reject', [WorklogController::class, 'rejectWorklog'])->name('worklog.reject');
             Route::post('/worklog/approve-group', [WorklogController::class, 'approveGroup'])->name('worklog.approve-group');
             Route::post('/worklog/reject-group', [WorklogController::class, 'rejectGroup'])->name('worklog.reject-group');
             Route::get('/worklog/session-entries', [WorklogController::class, 'getSessionEntries'])->name('worklog.session-entries');
             Route::post('/worklog/remove-from-session', [WorklogController::class, 'removeFromSession'])->name('worklog.remove-from-session');
             Route::post('/worklog/clear-session', [WorklogController::class, 'clearSession'])->name('worklog.clear-session');
             Route::post('/worklog/submit', [WorklogController::class, 'submitWorklog'])->name('worklog.submit');
             Route::delete('/worklog/{id}', [WorklogController::class, 'destroy'])->name('worklog.destroy');
             
             // Worklog History routes
             Route::get('/worklog-history', [WorklogHistoryController::class, 'index'])->name('worklog-history');
             Route::get('/worklog-history/fetch', [WorklogHistoryController::class, 'fetchWorklogs'])->name('worklog-history.fetch');
             Route::get('/worklog-history/stats', [WorklogHistoryController::class, 'getWorklogStats'])->name('worklog-history.stats');
             Route::delete('/worklog-history/{id}', [WorklogHistoryController::class, 'destroy'])->name('worklog-history.destroy');
             
             // Worklog Approvals route
             Route::get('/worklog-approvals', function() {
                 return view('worklog.approvals');
             })->name('worklog-approvals');
             
             // Holiday routes
             Route::get('/holiday', [HolidayController::class, 'index'])->name('holiday');
             Route::get('/holiday/fetch', [HolidayController::class, 'fetchHolidays'])->name('holiday.fetch');
             Route::post('/holiday', [HolidayController::class, 'store'])->name('holiday.store');
             Route::put('/holiday/{id}', [HolidayController::class, 'update'])->name('holiday.update');
             Route::delete('/holiday/{id}', [HolidayController::class, 'destroy'])->name('holiday.destroy');
             
             // Attendance routes
             Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
             Route::post('/attendance/punch-in', [AttendanceController::class, 'punchIn'])->name('attendance.punch-in');
             Route::post('/attendance/punch-out', [AttendanceController::class, 'punchOut'])->name('attendance.punch-out');
             Route::post('/attendance/start-break', [AttendanceController::class, 'startBreak'])->name('attendance.start-break');
             Route::post('/attendance/end-break', [AttendanceController::class, 'endBreak'])->name('attendance.end-break');
             Route::get('/attendance/today-status', [AttendanceController::class, 'getTodayStatus'])->name('attendance.today-status');
             Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
             Route::get('/attendance/history/data', [AttendanceController::class, 'getHistoryData'])->name('attendance.history.data');
             Route::get('/attendance/test', [AttendanceController::class, 'testApi'])->name('attendance.test');
Route::get('/attendance/stats', [AttendanceController::class, 'getAttendanceStats'])->name('attendance.stats');
Route::get('/attendance/check-worklog-validation', [AttendanceController::class, 'checkWorklogValidation'])->name('attendance.check-worklog-validation');
         });
