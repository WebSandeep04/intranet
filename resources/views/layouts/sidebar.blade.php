<div class="sidebar position-fixed d-flex flex-column p-3 vh-100">
    <h4 class="text-center mb-4 title d-fixed">Triserv</h4>

       <button id="toggleSidebar" class="btn btn-outline-dark btn-sm ms-2">
    <i class="bi bi-list"></i>
</button>

       <div class="side-items">
        <!-- TENANT SECTION - Only Super Admin (role_id = 3) -->
        @if(auth()->check() && auth()->user()->role_id == 3)    
        <a class="d-flex justify-content-between align-items-center text-decoration-none" data-bs-toggle="collapse" href="#tenantMenu" role="button" aria-expanded="false" aria-controls="tenantMenu">
            <span><i class="bi bi-building me-4"></i>Tenant</span>
            <i class="bi bi-chevron-down"></i>
        </a>
        <div class="collapse ms-3" id="tenantMenu">
            <a href="{{route('superadmin.dashboard')}}" class="d-block py-1 ps-4">Dashboard</a>
            <a href="{{route('tenant')}}" class="d-block py-1 ps-4">Tenant Management</a>
        </div>
        @endif

        <!-- ADMIN SECTION - Admin (role_id = 1) can access all: Setup, Worklog, Sales -->
        @if(auth()->check() && auth()->user()->role_id == 1)
        <!-- Setup Section -->
        <a class="d-flex justify-content-between align-items-center text-decoration-none" data-bs-toggle="collapse" href="#setupMenu" role="button" aria-expanded="false" aria-controls="setupMenu">
            <span><i class="bi bi-gear me-4"></i>Setup</span>
            <i class="bi bi-chevron-down"></i>
        </a>
        <div class="collapse ms-3" id="setupMenu">
            <a href="{{route('alldata')}}" class="d-block py-1 ps-4">All Data</a>
            <a href="{{route('user')}}" class="d-block py-1 ps-4">User</a>
            <a href="{{route('status')}}" class="d-block py-1 ps-4">Status</a>
            <a href="{{route('source')}}" class="d-block py-1 ps-4">Source</a>
            <a href="{{route('product')}}" class="d-block py-1 ps-4">Product</a>
            <a href="{{route('business')}}" class="d-block py-1 ps-4">Business</a>
            <a href="{{route('state')}}" class="d-block py-1 ps-4">State</a>
            <a href="{{route('city')}}" class="d-block py-1 ps-4">City</a>
            <a href="{{route('customer')}}" class="d-block py-1 ps-4">Customer</a>
            <a href="{{route('project')}}" class="d-block py-1 ps-4">Project</a>
            <a href="{{route('module')}}" class="d-block py-1 ps-4">Module</a>
            <a href="{{route('customer-project')}}" class="d-block py-1 ps-4">Customer Projects</a>
            <a href="{{route('holiday')}}" class="d-block py-1 ps-4">Holidays</a>
        </div>

        <!-- Worklog Section -->
        <a class="d-flex justify-content-between align-items-center text-decoration-none" data-bs-toggle="collapse" href="#worklogMenu" role="button" aria-expanded="false" aria-controls="worklogMenu">
            <span><i class="bi bi-clock me-4"></i>Worklog</span>
            <i class="bi bi-chevron-down"></i>
        </a>
        <div class="collapse ms-3" id="worklogMenu">
            <a href="{{route('worklog')}}" class="d-block py-1 ps-4">Worklog</a>
            <a href="{{route('worklog-history')}}" class="d-block py-1 ps-4">Worklog History</a>
            @if(auth()->user()->role_id == 1 || auth()->user()->subordinates()->count() > 0)
            <a href="{{route('worklog-approvals')}}" class="d-block py-1 ps-4">Worklog Approvals</a>
            @endif
            <a href="{{route('worklog-missing-summary')}}" class="d-block py-1 ps-4">Missing Entries Summary</a>
        </div>

        <!-- Attendance Section -->
        <a class="d-flex justify-content-between align-items-center text-decoration-none" data-bs-toggle="collapse" href="#attendanceMenu" role="button" aria-expanded="false" aria-controls="attendanceMenu">
            <span><i class="bi bi-person-check me-4"></i>Attendance</span>
            <i class="bi bi-chevron-down"></i>
        </a>
        <div class="collapse ms-3" id="attendanceMenu">
            <a href="{{route('attendance')}}" class="d-block py-1 ps-4">Attendance</a>
            <a href="{{route('attendance.history')}}" class="d-block py-1 ps-4">Attendance History</a>
        </div>

        <!-- Sales Section -->
        <a class="d-flex justify-content-between align-items-center text-decoration-none" data-bs-toggle="collapse" href="#salesMenu" role="button" aria-expanded="false" aria-controls="salesMenu">
            <span><i class="bi bi-cart me-4"></i>Sales</span>
            <i class="bi bi-chevron-down"></i>
        </a>
        <div class="collapse ms-3" id="salesMenu">
            <a href="{{url('/dashboard')}}" class="d-block py-1 ps-4">Dashboard</a>
            <a href="{{route('lead')}}" class="d-block py-1 ps-4">Lead</a>
            <a href="{{route('myleads')}}" class="d-block py-1 ps-4">My Leads</a>
            <a href="{{route('followup')}}" class="d-block py-1 ps-4">Follow Up</a>
        </div>
        @endif

        <!-- WORKLOG SECTION - Regular Users (role_id = 4) -->
        @if(auth()->check() && auth()->user()->role_id == 4)
        <a class="d-flex justify-content-between align-items-center text-decoration-none" data-bs-toggle="collapse" href="#worklogMenu" role="button" aria-expanded="false" aria-controls="worklogMenu">
            <span><i class="bi bi-clock me-4"></i>Worklog</span>
            <i class="bi bi-chevron-down"></i>
        </a>
        <div class="collapse ms-3" id="worklogMenu">
            <a href="{{route('worklog')}}" class="d-block py-1 ps-4">Worklog</a>
            <a href="{{route('worklog-history')}}" class="d-block py-1 ps-4">Worklog History</a>
            @if(auth()->user()->subordinates()->count() > 0)
            <a href="{{route('worklog-approvals')}}" class="d-block py-1 ps-4">Worklog Approvals</a>
            @endif
            <a href="{{route('worklog-missing-summary')}}" class="d-block py-1 ps-4">Missing Entries Summary</a>
        </div>

        <!-- Attendance Section -->
        <a class="d-flex justify-content-between align-items-center text-decoration-none" data-bs-toggle="collapse" href="#attendanceMenu" role="button" aria-expanded="false" aria-controls="attendanceMenu">
            <span><i class="bi bi-person-check me-4"></i>Attendance</span>
            <i class="bi bi-chevron-down"></i>
        </a>
        <div class="collapse ms-3" id="attendanceMenu">
            <a href="{{route('attendance')}}" class="d-block py-1 ps-4">Attendance</a>
            <a href="{{route('attendance.history')}}" class="d-block py-1 ps-4">Attendance History</a>
        </div>
        @endif

        <!-- SALES SECTION - Sales Users (role_id = 2) -->
        @if(auth()->check() && auth()->user()->role_id == 2)
        <!-- Sales Section -->
        <a class="d-flex justify-content-between align-items-center text-decoration-none" data-bs-toggle="collapse" href="#salesMenu" role="button" aria-expanded="false" aria-controls="salesMenu">
            <span><i class="bi bi-cart me-4"></i>Sales</span>
            <i class="bi bi-chevron-down"></i>
        </a>
        <div class="collapse ms-3" id="salesMenu">
            <a href="{{url('/dashboard')}}" class="d-block py-1 ps-4">Dashboard</a>
            <a href="{{route('lead')}}" class="d-block py-1 ps-4">Lead</a>
            <a href="{{route('myleads')}}" class="d-block py-1 ps-4">My Leads</a>
            <a href="{{route('followup')}}" class="d-block py-1 ps-4">Follow Up</a>
        </div>

        <!-- Worklog Section for Sales Users with worklog access -->
        @if(auth()->user()->is_worklog)
        <a class="d-flex justify-content-between align-items-center text-decoration-none" data-bs-toggle="collapse" href="#worklogMenu" role="button" aria-expanded="false" aria-controls="worklogMenu">
            <span><i class="bi bi-clock me-4"></i>Worklog</span>
            <i class="bi bi-chevron-down"></i>
        </a>
        <div class="collapse ms-3" id="worklogMenu">
            <a href="{{route('worklog')}}" class="d-block py-1 ps-4">Worklog</a>
            <a href="{{route('worklog-history')}}" class="d-block py-1 ps-4">Worklog History</a>
            @if(auth()->user()->subordinates()->count() > 0)
            <a href="{{route('worklog-approvals')}}" class="d-block py-1 ps-4">Worklog Approvals</a>
            @endif
            <a href="{{route('worklog-missing-summary')}}" class="d-block py-1 ps-4">Missing Entries Summary</a>
        </div>

        <!-- Attendance Section for Sales Users with worklog access -->
        <a class="d-flex justify-content-between align-items-center text-decoration-none" data-bs-toggle="collapse" href="#attendanceMenu" role="button" aria-expanded="false" aria-controls="attendanceMenu">
            <span><i class="bi bi-person-check me-4"></i>Attendance</span>
            <i class="bi bi-chevron-down"></i>
        </a>
        <div class="collapse ms-3" id="attendanceMenu">
            <a href="{{route('attendance')}}" class="d-block py-1 ps-4">Attendance</a>
            <a href="{{route('attendance.history')}}" class="d-block py-1 ps-4">Attendance History</a>
        </div>
        @endif
        @endif
    </div>

