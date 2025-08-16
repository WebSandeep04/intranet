@extends('layouts.app')

@section('title', 'Super Admin Dashboard')
@section('page_title', 'Super Admin Dashboard')

@section('content')
<div class="container-fluid mt-4">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Tenants</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalTenants">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalUsers">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sales Records</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSalesRecords">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Prospectuses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalProspectuses">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tenant Management and Activity -->
    <div class="row">
        <!-- Tenant List -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tenant Management</h6>
                    <a href="{{ route('tenant') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Manage Tenants
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tenantTable">
                            <thead>
                                <tr>
                                    <th>Tenant Name</th>
                                    <th>Code</th>
                                    <th>Users</th>
                                    <th>Sales Records</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tenantTableBody">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
                </div>
                <div class="card-body">
                    <div id="recentActivities">
                        <!-- Activities will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts -->
    <div class="row">
        <!-- Monthly Growth Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Growth</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyGrowthChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Tenants -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Tenants</h6>
                </div>
                <div class="card-body">
                    <div id="topTenants">
                        <!-- Top tenants will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tenant Activity Modal -->
<div class="modal fade" id="tenantActivityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tenant Activity</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="tenantActivityContent">
                <!-- Activity content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    loadDashboardStats();
    loadRecentActivities();
    loadTenantStats();
    loadMonthlyGrowth();
    loadTopTenants();

    function loadDashboardStats() {
        $.ajax({
            url: '{{ route("superadmin.stats") }}',
            type: 'GET',
            success: function(response) {
                $('#totalTenants').text(response.total_tenants);
                $('#totalUsers').text(response.total_users);
                $('#totalSalesRecords').text(response.total_sales_records);
                $('#totalProspectuses').text(response.total_prospectuses);
            }
        });
    }

    function loadRecentActivities() {
        $.ajax({
            url: '{{ route("superadmin.stats") }}',
            type: 'GET',
            success: function(response) {
                let html = '';
                response.recent_activities.forEach(function(activity) {
                    html += `
                        <div class="activity-item mb-3">
                            <div class="small text-gray-500">${new Date(activity.date).toLocaleDateString()}</div>
                            <div class="text-sm">${activity.message}</div>
                            <div class="small text-primary">${activity.tenant}</div>
                        </div>
                    `;
                });
                $('#recentActivities').html(html);
            }
        });
    }

    function loadTenantStats() {
        $.ajax({
            url: '{{ route("superadmin.stats") }}',
            type: 'GET',
            success: function(response) {
                let html = '';
                response.tenant_stats.forEach(function(tenant) {
                    html += `
                        <tr>
                            <td>${tenant.tenant_name}</td>
                            <td><span class="badge badge-info">${tenant.tenant_code}</span></td>
                            <td>${tenant.users_count}</td>
                            <td>${tenant.sales_records_count}</td>
                            <td>${tenant.created_at}</td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewTenantActivity(${tenant.tenant_id})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success" onclick="exportTenantData(${tenant.tenant_id})">
                                    <i class="fas fa-download"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#tenantTableBody').html(html);
            }
        });
    }

    function loadMonthlyGrowth() {
        $.ajax({
            url: '{{ route("superadmin.stats") }}',
            type: 'GET',
            success: function(response) {
                const ctx = document.getElementById('monthlyGrowthChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: response.monthly_growth.map(item => item.month),
                        datasets: [{
                            label: 'Tenants',
                            data: response.monthly_growth.map(item => item.tenants),
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }, {
                            label: 'Users',
                            data: response.monthly_growth.map(item => item.users),
                            borderColor: 'rgb(255, 99, 132)',
                            tension: 0.1
                        }, {
                            label: 'Sales Records',
                            data: response.monthly_growth.map(item => item.sales_records),
                            borderColor: 'rgb(54, 162, 235)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    }

    function loadTopTenants() {
        $.ajax({
            url: '{{ route("superadmin.analytics") }}',
            type: 'GET',
            success: function(response) {
                let html = '';
                response.top_tenants.forEach(function(tenant, index) {
                    html += `
                        <div class="tenant-item mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="font-weight-bold">${index + 1}. ${tenant.tenant_name}</div>
                                    <div class="small text-gray-500">${tenant.tenant_code}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm">${tenant.sales_count} sales</div>
                                    <div class="small text-gray-500">${tenant.users_count} users</div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                $('#topTenants').html(html);
            }
        });
    }
});

function viewTenantActivity(tenantId) {
    $.ajax({
        url: `/superadmin/tenant/${tenantId}/activity`,
        type: 'GET',
        success: function(response) {
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Data Summary</h6>
                        <ul class="list-unstyled">
                            <li>Users: ${response.data_summary.total_users}</li>
                            <li>Sales Records: ${response.data_summary.total_sales_records}</li>
                            <li>Prospectuses: ${response.data_summary.total_prospectuses}</li>
                            <li>Remarks: ${response.data_summary.total_remarks}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Recent Sales Records</h6>
                        <div class="small">
            `;
            
            response.recent_sales.forEach(function(sale) {
                html += `<div class="mb-2">${sale.leads_name} - ${sale.user.name}</div>`;
            });
            
            html += `
                        </div>
                    </div>
                </div>
            `;
            
            $('#tenantActivityContent').html(html);
            $('#tenantActivityModal').modal('show');
        }
    });
}

function exportTenantData(tenantId) {
    window.open(`/superadmin/tenant/${tenantId}/export`, '_blank');
}
</script>
@endpush

@push('styles')
<style>
.activity-item {
    border-left: 3px solid #4e73df;
    padding-left: 10px;
}

.tenant-item {
    border-bottom: 1px solid #e3e6f0;
    padding-bottom: 10px;
}

.tenant-item:last-child {
    border-bottom: none;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
@endpush
