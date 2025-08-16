@extends('layouts.app')

@section('title', 'Worklog Approvals')
@section('page_title', 'Worklog Approvals')

@section('content')
<div class="container mt-4">
    <div id="alertBox"></div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pending Worklog Approvals</h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="loadPendingApprovals()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div id="groupedApprovals">
                        <!-- Grouped approvals will be loaded via jQuery -->
                    </div>
                </div>
                    
                    <!-- No Data Message -->
                    <div id="noDataMessage" class="text-center py-4" style="display: none;">
                        <i class="bi bi-check-circle fs-1 text-muted"></i>
                        <h5 class="text-muted mt-3">No pending approvals</h5>
                        <p class="text-muted">All worklog entries have been reviewed.</p>
                    </div>
                </div>
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
    loadPendingApprovals();
});

function loadPendingApprovals() {
    $.get("{{ route('worklog.pending-approvals') }}", function (data) {
        if (data.length === 0) {
            $('#groupedApprovals').hide();
            $('#noDataMessage').show();
        } else {
            $('#groupedApprovals').show();
            $('#noDataMessage').hide();
            
            let html = '';
            $.each(data, function (i, group) {
                const totalTime = group.entries.reduce((total, entry) => {
                    return total + (parseInt(entry.hours) * 60 + parseInt(entry.minutes));
                }, 0);
                const totalHours = Math.floor(totalTime / 60);
                const totalMinutes = totalTime % 60;
                const timeDisplay = `${totalHours}h ${totalMinutes}m`;
                
                html += `
                <div class="card mb-3 border-primary">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="bi bi-person-circle me-2"></i>
                            ${group.user_name} - ${group.work_date}
                        </h6>
                        <div>
                            <span class="badge bg-light text-dark me-2">${group.entries.length} entries</span>
                            <span class="badge bg-light text-dark">Total: ${timeDisplay}</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Entry Type</th>
                                        <th scope="col">Customer</th>
                                        <th scope="col">Project</th>
                                        <th scope="col">Module</th>
                                        <th scope="col">Time</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                
                $.each(group.entries, function (j, worklog) {
                    const timeDisplay = `${worklog.hours}h ${worklog.minutes}m`;
                    
                    html += `<tr>
                        <td><span class="badge bg-primary">${worklog.entry_type.name}</span></td>
                        <td>${worklog.customer.name}</td>
                        <td>${worklog.project.name}</td>
                        <td>${worklog.module.name}</td>
                        <td>${timeDisplay}</td>
                        <td>
                            <div class="text-start">
                                <small>${worklog.description}</small>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-success approveBtn" data-id="${worklog.id}" title="Approve">
                                <i class="bi bi-check-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-danger rejectBtn" data-id="${worklog.id}" title="Reject">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </td>
                    </tr>`;
                });
                
                html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Group Actions:</small>
                            <div>
                                <button class="btn btn-sm btn-success approveGroupBtn" data-user="${group.user_name}" data-date="${group.work_date}" title="Approve All">
                                    <i class="bi bi-check-circle"></i> Approve All
                                </button>
                                <button class="btn btn-sm btn-danger rejectGroupBtn" data-user="${group.user_name}" data-date="${group.work_date}" title="Reject All">
                                    <i class="bi bi-x-circle"></i> Reject All
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
            });
            $('#groupedApprovals').html(html);
        }
    }).fail(function () {
        showAlert('error', 'Error loading pending approvals.');
    });
}

// Approve worklog
$(document).on('click', '.approveBtn', function () {
    const worklogId = $(this).data('id');
    
    if (confirm('Are you sure you want to approve this worklog entry?')) {
        $.post(`/worklog/${worklogId}/approve`, {
            _token: '{{ csrf_token() }}'
        }, function (response) {
            if (response.success) {
                loadPendingApprovals();
                showAlert('success', response.message);
            }
        }).fail(function () {
            showAlert('error', 'Error approving worklog entry.');
        });
    }
});

// Reject worklog
$(document).on('click', '.rejectBtn', function () {
    const worklogId = $(this).data('id');
    
    if (confirm('Are you sure you want to reject this worklog entry?')) {
        $.post(`/worklog/${worklogId}/reject`, {
            _token: '{{ csrf_token() }}'
        }, function (response) {
            if (response.success) {
                loadPendingApprovals();
                showAlert('success', response.message);
            }
        }).fail(function () {
            showAlert('error', 'Error rejecting worklog entry.');
        });
    }
});

// Approve group
$(document).on('click', '.approveGroupBtn', function () {
    const userName = $(this).data('user');
    const workDate = $(this).data('date');
    
    if (confirm(`Are you sure you want to approve all entries for ${userName} on ${workDate}?`)) {
        $.post("{{ route('worklog.approve-group') }}", {
            user_name: userName,
            work_date: workDate,
            _token: '{{ csrf_token() }}'
        }, function (response) {
            if (response.success) {
                loadPendingApprovals();
                showAlert('success', response.message);
            }
        }).fail(function () {
            showAlert('error', 'Error approving group entries.');
        });
    }
});

// Reject group
$(document).on('click', '.rejectGroupBtn', function () {
    const userName = $(this).data('user');
    const workDate = $(this).data('date');
    
    if (confirm(`Are you sure you want to reject all entries for ${userName} on ${workDate}?`)) {
        $.post("{{ route('worklog.reject-group') }}", {
            user_name: userName,
            work_date: workDate,
            _token: '{{ csrf_token() }}'
        }, function (response) {
            if (response.success) {
                loadPendingApprovals();
                showAlert('success', response.message);
            }
        }).fail(function () {
            showAlert('error', 'Error rejecting group entries.');
        });
    }
});
</script>
@endpush
