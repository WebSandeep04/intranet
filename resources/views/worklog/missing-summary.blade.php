@extends('layouts.app')

@section('title', 'Missing Worklog Entries Summary')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="title">Missing Worklog Entries Summary</h4>
                    <p class="text-muted mb-0">Overview of missing worklog entries across all team members</p>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <button class="btn btn-primary" onclick="refreshSummary()">
                                <i class="bi bi-arrow-clockwise"></i> Refresh Summary
                            </button>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="badge bg-info" id="totalMissingDates">0</span> dates with missing entries
                        </span>
                    </div>
                    
                    <div id="summaryContainer">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading summary...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Missing Users Modal -->
<div class="modal fade" id="missingUsersModal" tabindex="-1" aria-labelledby="missingUsersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="missingUsersModalLabel">Missing Users for <span id="modalDate"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalUsersList"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    loadSummary();
});

function loadSummary() {
    $.ajax({
        url: '{{ route("worklog.missing-summary") }}',
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            displaySummary(response);
        },
        error: function(xhr) {
            console.error('Error loading summary:', xhr);
            $('#summaryContainer').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    Error loading summary. Please try again.
                </div>
            `);
        }
    });
}

function displaySummary(data) {
    if (!data.summary || data.summary.length === 0) {
        $('#summaryContainer').html(`
            <div class="text-center py-4">
                <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-success">All Caught Up!</h5>
                <p class="text-muted">All team members have completed their worklog entries.</p>
            </div>
        `);
        $('#totalMissingDates').text('0');
        return;
    }
    
    $('#totalMissingDates').text(data.total_missing_dates);
    
    let html = '<div class="table-responsive"><table class="table table-striped">';
    html += '<thead><tr><th>Date</th><th>Missing Users</th><th>Count</th><th>Action</th></tr></thead><tbody>';
    
    data.summary.forEach(function(item) {
        const date = new Date(item.date).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        html += `<tr>
            <td><strong>${date}</strong></td>
            <td>${item.missing_users.map(u => u.name).join(', ')}</td>
            <td><span class="badge bg-warning">${item.count}</span></td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="showMissingUsers('${item.date}', ${JSON.stringify(item.missing_users).replace(/"/g, '&quot;')})">
                    <i class="bi bi-eye"></i> View Details
                </button>
            </td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    
    $('#summaryContainer').html(html);
}

function showMissingUsers(date, users) {
    $('#modalDate').text(new Date(date).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }));
    
    let usersHtml = '<div class="list-group">';
    users.forEach(function(user) {
        usersHtml += `
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">${user.name}</h6>
                    <small class="text-muted">${user.email}</small>
                </div>
                <span class="badge bg-warning">Missing Entry</span>
            </div>
        `;
    });
    usersHtml += '</div>';
    
    $('#modalUsersList').html(usersHtml);
    
    const modal = new bootstrap.Modal(document.getElementById('missingUsersModal'));
    modal.show();
}

function refreshSummary() {
    $('#summaryContainer').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Refreshing summary...</p>
        </div>
    `);
    loadSummary();
}
</script>
@endpush
