@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Attendance History</h4>
                </div>
                <div class="card-body">
                    <!-- Summary Statistics -->
                    <div class="row mb-4" id="summaryStats">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6>Total Office Hours</h6>
                                    <h4 id="totalOfficeHours">0</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6>Total Field Hours</h6>
                                    <h4 id="totalFieldHours">0</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h6>Total Days</h6>
                                    <h4 id="totalDays">0</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h6>Total Cycles</h6>
                                    <h4 id="totalCycles">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Attendance History Table -->
                    <div class="table-responsive">
                        <table class="table table-striped" id="attendanceTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Office Hours</th>
                                    <th>Field Hours</th>
                                    <th>Total Hours</th>
                                    <th>Cycles</th>
                                    <th>Movements</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        <nav aria-label="Attendance pagination">
                            <ul class="pagination" id="pagination">
                                <!-- Pagination will be generated here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Movement Details Modal -->
<div class="modal fade" id="movementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attendance Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="movementDetails">
                    <!-- Movement details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let totalPages = 1;

// Load attendance history on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, calling loadAttendanceHistory...');
    console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
    console.log('Bootstrap Modal available:', typeof bootstrap !== 'undefined' && bootstrap.Modal);
    
    // Set up fallback modal close functionality
    setupModalFallback();
    
    loadAttendanceHistory();
});

function setupModalFallback() {
    const modal = document.getElementById('movementModal');
    const closeButtons = modal.querySelectorAll('[data-bs-dismiss="modal"], .btn-close');
    
    closeButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            // Hide modal manually if Bootstrap is not available
            if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
                modal.setAttribute('aria-hidden', 'true');
            }
        });
    });
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
                modal.setAttribute('aria-hidden', 'true');
            }
        }
    });
}

function loadAttendanceHistory(page = 1) {
    console.log('loadAttendanceHistory called with page:', page);
    currentPage = page;
    
    // Show loading indicator
    const tbody = document.getElementById('attendanceTableBody');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Loading...</td></tr>';
    
    $.ajax({
        url: '/attendance/history/data',
        method: 'GET',
        data: {
            page: page,
            per_page: 10
        },
        success: function(response) {
            console.log('Attendance history response:', response);
            if (response && response.data) {
                displayAttendanceData(response.data);
                generatePagination(response.current_page, response.last_page, response.total);
            } else {
                console.error('Invalid response format:', response);
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Invalid response format</td></tr>';
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading attendance history:', {
                status: status,
                error: error,
                responseText: xhr.responseText,
                statusCode: xhr.status
            });
            
            let errorMessage = 'Failed to load attendance history';
            if (xhr.status === 404) {
                errorMessage = 'API endpoint not found. Please check the route.';
            } else if (xhr.status === 500) {
                errorMessage = 'Server error occurred. Please check the console.';
            }
            
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">' + errorMessage + '</tr>';
            showAlert('error', errorMessage);
        }
    });
}

function displayAttendanceData(attendances) {
    console.log('Displaying attendance data:', attendances);
    
    // Store attendance data globally for access in viewMovements
    window.currentAttendances = attendances;
    
    const tbody = document.getElementById('attendanceTableBody');
    
    if (!attendances || attendances.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No attendance records found</td></tr>';
        updateSummaryStats([]);
        return;
    }

    let html = '';
    let totalOfficeHours = 0;
    let totalFieldHours = 0;
    let totalCycles = 0;
    
    attendances.forEach(function(attendance) {
        const date = new Date(attendance.date).toLocaleDateString();
        const officeHours = calculateTypeHours(attendance.movements, 'office');
        const fieldHours = calculateTypeHours(attendance.movements, 'field');
        const totalHours = officeHours + fieldHours;
        
        // Calculate cycles for summary
        const cycles = calculateCycles(attendance.movements);
        
        // Add to totals
        totalOfficeHours += officeHours;
        totalFieldHours += fieldHours;
        totalCycles += cycles.office + cycles.field + cycles.break;
        
        html += '<tr>';
        html += '<td>' + date + '</td>';
        html += '<td>' + officeHours.toFixed(2) + ' hrs</td>';
        html += '<td>' + fieldHours.toFixed(2) + ' hrs</td>';
        html += '<td><strong>' + totalHours.toFixed(2) + ' hrs</strong></td>';
        html += '<td>';
        html += '<span class="badge bg-primary me-1">O:' + cycles.office + '</span>';
        html += '<span class="badge bg-success me-1">F:' + cycles.field + '</span>';
        html += '<span class="badge bg-warning">B:' + cycles.break + '</span>';
        html += '</td>';
        html += '<td>';
        html += '<button type="button" class="btn btn-sm btn-info" onclick="viewMovements(' + attendance.id + ')">';
        html += '<i class="fas fa-eye"></i> View Details';
        html += '</button>';
        html += '</td>';
        html += '</tr>';
    });
    
    tbody.innerHTML = html;
    
    // Update summary statistics
    updateSummaryStats(attendances, totalOfficeHours, totalFieldHours, totalCycles);
}

function updateSummaryStats(attendances, totalOfficeHours, totalFieldHours, totalCycles) {
    if (attendances.length === 0) {
        document.getElementById('totalOfficeHours').textContent = '0';
        document.getElementById('totalFieldHours').textContent = '0';
        document.getElementById('totalDays').textContent = '0';
        document.getElementById('totalCycles').textContent = '0';
        return;
    }
    
    document.getElementById('totalOfficeHours').textContent = totalOfficeHours.toFixed(2);
    document.getElementById('totalFieldHours').textContent = totalFieldHours.toFixed(2);
    document.getElementById('totalDays').textContent = attendances.length;
    document.getElementById('totalCycles').textContent = totalCycles;
}

function calculateTypeHours(movements, type) {
    let totalMinutes = 0;
    let inTime = null;
    
    movements.forEach(function(movement) {
        if (movement.movement_type === type) {
            if (movement.movement_action === 'in') {
                inTime = new Date(movement.time);
            } else if (movement.movement_action === 'out' && inTime) {
                totalMinutes += (new Date(movement.time) - inTime) / (1000 * 60);
                inTime = null;
            }
        }
    });
    
    // If still punched in, calculate until end of day (assuming 6 PM)
    if (inTime) {
        const endOfDay = new Date(inTime);
        endOfDay.setHours(18, 0, 0, 0); // 6 PM
        if (new Date() > endOfDay) {
            totalMinutes += (endOfDay - inTime) / (1000 * 60);
        } else {
            totalMinutes += (new Date() - inTime) / (1000 * 60);
        }
    }
    
    return totalMinutes / 60;
}

function calculateCycles(movements) {
    const cycles = { office: 0, field: 0, break: 0 };
    const groupedMovements = {};
    
    // Group movements by type
    movements.forEach(function(movement) {
        const type = movement.movement_type;
        if (!groupedMovements[type]) {
            groupedMovements[type] = [];
        }
        groupedMovements[type].push(movement);
    });
    
    // Calculate cycles for each type
    Object.keys(groupedMovements).forEach(function(type) {
        const typeMovements = groupedMovements[type];
        if (type === 'break') {
            let startCount = 0;
            let endCount = 0;
            typeMovements.forEach(function(movement) {
                if (movement.movement_action === 'start') startCount++;
                if (movement.movement_action === 'end') endCount++;
            });
            cycles[type] = Math.min(startCount, endCount);
        } else {
            let inCount = 0;
            let outCount = 0;
            typeMovements.forEach(function(movement) {
                if (movement.movement_action === 'in') inCount++;
                if (movement.movement_action === 'out') outCount++;
            });
            cycles[type] = Math.min(inCount, outCount);
        }
    });
    
    return cycles;
}

function generatePagination(currentPage, lastPage, total) {
    totalPages = lastPage;
    const pagination = document.getElementById('pagination');
    
    if (lastPage <= 1) {
        pagination.innerHTML = '';
        return;
    }
    
    let html = '';
    
    // Previous button
    if (currentPage > 1) {
        html += '<li class="page-item">';
        html += '<a class="page-link" href="#" onclick="loadAttendanceHistory(' + (currentPage - 1) + ')">Previous</a>';
        html += '</li>';
    }
    
    // Page numbers
    for (let i = 1; i <= lastPage; i++) {
        if (i === currentPage) {
            html += '<li class="page-item active">';
            html += '<span class="page-link">' + i + '</span>';
            html += '</li>';
        } else {
            html += '<li class="page-item">';
            html += '<a class="page-link" href="#" onclick="loadAttendanceHistory(' + i + ')">' + i + '</a>';
            html += '</li>';
        }
    }
    
    // Next button
    if (currentPage < lastPage) {
        html += '<li class="page-item">';
        html += '<a class="page-link" href="#" onclick="loadAttendanceHistory(' + (currentPage + 1) + ')">Next</a>';
        html += '</li>';
    }
    
    pagination.innerHTML = html;
}

function viewMovements(attendanceId) {
    console.log('Viewing movements for attendance:', attendanceId);
    
    // Find the attendance data from the current page
    const currentAttendances = window.currentAttendances || [];
    const attendance = currentAttendances.find(function(a) { return a.id == attendanceId; });
    
    if (!attendance) {
        showAlert('error', 'Attendance data not found');
        return;
    }
    
    const modal = document.getElementById('movementModal');
    const detailsContainer = document.getElementById('movementDetails');
    
    if (!modal || !detailsContainer) {
        console.error('Modal elements not found');
        showAlert('error', 'Modal elements not found');
        return;
    }
    
    // Group movements by type and calculate cycles
    const movements = attendance.movements;
    const groupedMovements = {};
    const cycles = { office: 0, field: 0, break: 0 };
    
    // Group movements by type
    movements.forEach(function(movement) {
        const type = movement.movement_type;
        if (!groupedMovements[type]) {
            groupedMovements[type] = [];
        }
        groupedMovements[type].push(movement);
    });
    
    // Calculate cycles for each type
    Object.keys(groupedMovements).forEach(function(type) {
        const typeMovements = groupedMovements[type];
        if (type === 'break') {
            let startCount = 0;
            let endCount = 0;
            typeMovements.forEach(function(movement) {
                if (movement.movement_action === 'start') startCount++;
                if (movement.movement_action === 'end') endCount++;
            });
            cycles[type] = Math.min(startCount, endCount);
        } else {
            let inCount = 0;
            let outCount = 0;
            typeMovements.forEach(function(movement) {
                if (movement.movement_action === 'in') inCount++;
                if (movement.movement_action === 'out') outCount++;
            });
            cycles[type] = Math.min(inCount, outCount);
        }
    });
    
    // Generate detailed HTML
    let html = '';
    html += '<div class="row mb-3">';
    html += '<div class="col-md-4">';
    html += '<div class="card bg-primary text-white">';
    html += '<div class="card-body text-center">';
    html += '<h6>Office Cycles</h6>';
    html += '<h4>' + cycles.office + '</h4>';
    html += '</div>';
    html += '</div>';
    html += '</div>';
    html += '<div class="col-md-4">';
    html += '<div class="card bg-success text-white">';
    html += '<div class="card-body text-center">';
    html += '<h6>Field Cycles</h6>';
    html += '<h4>' + cycles.field + '</h4>';
    html += '</div>';
    html += '</div>';
    html += '</div>';
    html += '<div class="col-md-4">';
    html += '<div class="card bg-warning text-white">';
    html += '<div class="card-body text-center">';
    html += '<h6>Break Cycles</h6>';
    html += '<h4>' + cycles.break + '</h4>';
    html += '</div>';
    html += '</div>';
    html += '</div>';
    html += '</div>';
    
    html += '<h6 class="mb-3">Detailed Movements</h6>';
    html += '<div class="table-responsive">';
    html += '<table class="table table-striped table-sm">';
    html += '<thead>';
    html += '<tr>';
    html += '<th>Time</th>';
    html += '<th>Type</th>';
    html += '<th>Action</th>';
    html += '<th>Cycle</th>';
    html += '<th>Description</th>';
    html += '</tr>';
    html += '</thead>';
    html += '<tbody>';
    
    // Sort all movements by time
    const sortedMovements = movements.sort(function(a, b) { return new Date(a.time) - new Date(b.time); });
    
    // Calculate cycle numbers for each movement
    const cycleCounters = { office: 0, field: 0, break: 0 };
    
    sortedMovements.forEach(function(movement) {
        const time = new Date(movement.time).toLocaleTimeString();
        const type = movement.movement_type.charAt(0).toUpperCase() + movement.movement_type.slice(1);
        const action = movement.movement_action.charAt(0).toUpperCase() + movement.movement_action.slice(1);
        
        // Calculate cycle number
        if (movement.movement_type === 'break') {
            if (movement.movement_action === 'start') {
                cycleCounters.break++;
            }
        } else {
            if (movement.movement_action === 'in') {
                cycleCounters[movement.movement_type]++;
            }
        }
        
        const cycleNumber = cycleCounters[movement.movement_type];
        const description = movement.description || '-';
        
        // Check if this is an automatic transition
        const isAutoTransition = description && description.includes('Auto-ended');
        const actionBadge = isAutoTransition 
            ? '<span class="badge bg-secondary">' + action + ' <i class="fas fa-magic ms-1"></i></span>'
            : '<span class="badge bg-' + getActionColor(movement.movement_action) + '">' + action + '</span>';
        
        html += '<tr>';
        html += '<td>' + time + '</td>';
        html += '<td><span class="badge bg-primary">' + type + '</span></td>';
        html += '<td>' + actionBadge + '</td>';
        html += '<td><span class="badge bg-info">Cycle ' + cycleNumber + '</span></td>';
        html += '<td>' + description + '</td>';
        html += '</tr>';
    });
    
    html += '</tbody>';
    html += '</table>';
    html += '</div>';
    
    detailsContainer.innerHTML = html;
    
    // Bootstrap 5 modal API
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        try {
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        } catch (error) {
            console.error('Error creating Bootstrap modal:', error);
            // Fallback: show modal manually
            modal.style.display = 'block';
            modal.classList.add('show');
            modal.setAttribute('aria-hidden', 'false');
        }
    } else {
        console.error('Bootstrap Modal not available, using fallback');
        // Fallback: show modal manually
        modal.style.display = 'block';
        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');
    }
}

function getActionColor(action) {
    switch(action) {
        case 'in': return 'success';
        case 'out': return 'danger';
        case 'start': return 'warning';
        case 'end': return 'info';
        default: return 'secondary';
    }
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    
    // Insert at the top of the card body
    const cardBody = document.querySelector('.card-body');
    cardBody.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto remove after 5 seconds
    setTimeout(function() {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>
@endsection
