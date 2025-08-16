@extends('layouts.app')

@section('title', 'Worklog History')
@section('page_title', 'Worklog History')

@section('content')
<div class="container mt-4">
    <div id="alertBox"></div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4 id="totalEntries">0</h4>
                    <p class="mb-0">Total Entries</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4 id="totalHours">0h 0m</h4>
                    <p class="mb-0">Total Hours</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4 id="totalDays">0</h4>
                    <p class="mb-0">Total Days</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4 id="avgHours">0h</h4>
                    <p class="mb-0">Avg Hours/Day</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Worklog History Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">My Worklog History</h5>
            <div>
                <button class="btn btn-sm btn-outline-primary" onclick="loadWorklogs()">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
        </div>
                 <div class="card-body">
             <div class="table-responsive">
                 <table class="table table-hover table-bordered align-middle text-center border shadow-sm rounded" id="worklogTable">
                                                  <thead class="table-secondary">
                                 <tr>
                                     <th scope="col">Date</th>
                                     <th scope="col">Entry Type</th>
                                     <th scope="col">Customer</th>
                                     <th scope="col">Project</th>
                                     <th scope="col">Module</th>
                                     <th scope="col">Status</th>
                                     <th scope="col">Description</th>
                                     <th scope="col">Actions</th>
                                 </tr>
                             </thead>
                     <tbody>
                         <!-- Rows will be loaded via jQuery -->
                     </tbody>
                 </table>
             </div>
             
             <!-- Pagination -->
             <div class="d-flex justify-content-between align-items-center mt-3" id="paginationContainer" style="display: none;">
                 <div class="text-muted">
                     Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="totalEntries">0</span> entries
                 </div>
                 <nav aria-label="Worklog pagination">
                     <ul class="pagination pagination-sm mb-0" id="pagination">
                         <!-- Pagination will be loaded via jQuery -->
                     </ul>
                 </nav>
             </div>
            
            <!-- No Data Message -->
            <div id="noDataMessage" class="text-center py-4" style="display: none;">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">No worklog entries found</h5>
                <p class="text-muted">Start logging your work to see your history here.</p>
                <a href="{{ route('worklog') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Worklog Entry
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showAlert(type, message) {
    let colorClass = 'custom-alert-' + type;
    $('#alertBox').html(`
        <div class="custom-alert ${colorClass}">
            ${message}
            <button class="custom-alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
    `);
    setTimeout(() => $('.custom-alert').fadeOut(500, function() { $(this).remove(); }), 3000);
}

$(function () {
    loadWorklogs();
    loadStats();
});

function loadWorklogs(page = 1) {
    $.get("{{ route('worklog-history.fetch') }}", { page: page }, function (response) {
        if (response.data.length === 0) {
            $('#worklogTable').hide();
            $('#paginationContainer').hide();
            $('#noDataMessage').show();
        } else {
            $('#worklogTable').show();
            $('#noDataMessage').hide();
            
                         let rows = '';
             $.each(response.data, function (i, worklog) {
                 // Format time display
                 const timeDisplay = `${worklog.hours}h ${worklog.minutes}m`;
                 
                 // Status badge styling
                 let statusBadge = '';
                 switch(worklog.status) {
                     case 'pending':
                         statusBadge = '<span class="badge bg-warning">Pending</span>';
                         break;
                     case 'approved':
                         statusBadge = '<span class="badge bg-success">Approved</span>';
                         break;
                     case 'rejected':
                         statusBadge = '<span class="badge bg-danger">Rejected</span>';
                         break;
                 }
                 
                 // Show delete button only for pending worklogs
                 const deleteButton = worklog.status === 'pending' ? 
                     `<button class="btn btn-sm btn-danger deleteBtn" data-id="${worklog.id}" title="Delete Entry">
                         <i class="bi bi-trash3-fill"></i>
                     </button>` : '';
                 
                 rows += `<tr>
                     <td>${worklog.work_date}</td>
                     <td><span class="badge bg-primary">${worklog.entry_type.name}</span></td>
                     <td>${worklog.customer.name}</td>
                     <td>${worklog.project.name}</td>
                     <td>${worklog.module.name}</td>
                     <td>${statusBadge}</td>
                     <td>
                         <div class="text-start">
                             <small>${worklog.description}</small>
                         </div>
                     </td>
                     <td>${deleteButton}</td>
                 </tr>`;
             });
            $('#worklogTable tbody').html(rows);
            
            // Update pagination info
            $('#showingFrom').text(response.pagination.from);
            $('#showingTo').text(response.pagination.to);
            $('#totalEntries').text(response.pagination.total);
            
            // Show pagination if there are multiple pages
            if (response.pagination.last_page > 1) {
                $('#paginationContainer').show();
                generatePagination(response.pagination);
            } else {
                $('#paginationContainer').hide();
            }
        }
    }).fail(function () {
        showAlert('error', 'Error loading worklog history.');
    });
}

function generatePagination(pagination) {
    let paginationHtml = '';
    
    // Previous button
    if (pagination.has_previous_pages) {
        paginationHtml += `<li class="page-item">
            <a class="page-link" href="#" onclick="loadWorklogs(${pagination.current_page - 1})">Previous</a>
        </li>`;
    }
    
    // Page numbers
    for (let i = 1; i <= pagination.last_page; i++) {
        if (i === pagination.current_page) {
            paginationHtml += `<li class="page-item active">
                <span class="page-link">${i}</span>
            </li>`;
        } else {
            paginationHtml += `<li class="page-item">
                <a class="page-link" href="#" onclick="loadWorklogs(${i})">${i}</a>
            </li>`;
        }
    }
    
    // Next button
    if (pagination.has_more_pages) {
        paginationHtml += `<li class="page-item">
            <a class="page-link" href="#" onclick="loadWorklogs(${pagination.current_page + 1})">Next</a>
        </li>`;
    }
    
    $('#pagination').html(paginationHtml);
}

function loadStats() {
    $.get("{{ route('worklog-history.stats') }}", function (data) {
        $('#totalEntries').text(data.total_entries);
        $('#totalHours').text(`${data.total_hours}h ${data.total_minutes}m`);
        $('#totalDays').text(data.total_days);
        
        // Calculate average hours per day
        let avgHours = data.total_days > 0 ? (data.total_hours / data.total_days).toFixed(1) : 0;
        $('#avgHours').text(`${avgHours}h`);
    }).fail(function () {
        showAlert('error', 'Error loading statistics.');
    });
}

// Delete worklog entry
$(document).on('click', '.deleteBtn', function () {
    const worklogId = $(this).data('id');
    
    if (confirm('Are you sure you want to delete this worklog entry? This action cannot be undone.')) {
        $.ajax({
            url: `/worklog-history/${worklogId}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (response) {
                if (response.success) {
                    loadWorklogs();
                    loadStats();
                    showAlert('success', 'Worklog entry deleted successfully.');
                }
            },
            error: function () {
                showAlert('error', 'Error deleting worklog entry.');
            }
        });
    }
});
</script>
@endpush
