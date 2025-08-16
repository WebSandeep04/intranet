@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Attendance Management</h4>
                </div>
                <div class="card-body">
                    <!-- Today's Date -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Today: {{ \Carbon\Carbon::today()->format('l, F j, Y') }}</h5>
                        </div>
                    </div>

                    <!-- Auto-transition Info -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>🚀 Smart Attendance System:</strong> 
                                <ul class="mb-0 mt-2">
                                    <li>✅ <strong>Multiple Cycles:</strong> You can punch in/out multiple times for office and field work</li>
                                    <li>✅ <strong>Multiple Breaks:</strong> Take multiple breaks throughout the day</li>
                                    <li>✅ <strong>Office → Field:</strong> Starting field work automatically ends office work</li>
                                    <li>✅ <strong>Field → Office:</strong> Starting office work automatically ends field work</li>
                                    <li>✅ <strong>No Descriptions:</strong> All actions are automatic and seamless</li>
                                    <li>✅ <strong>Status Badges:</strong> 
                                        <ul class="mb-0 mt-1">
                                            <li><span class="badge bg-success">Punched In/In Field/On Break</span> = Currently active</li>
                                            <li><span class="badge bg-primary">Ready for New Cycle</span> = Can start new cycle</li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Stats -->
                    <div class="row mb-4" id="attendanceStats">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6>Today's Hours</h6>
                                    <h3 id="todayHours">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6>Month Hours</h6>
                                    <h3 id="monthHours">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h6>Total Days</h6>
                                    <h3 id="totalDays">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h6>Avg Hours/Day</h6>
                                    <h3 id="avgHours">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Punch In/Out Controls -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Office Attendance</h6>
                                </div>
                                <div class="card-body">
                                    <div id="officeStatus" class="mb-3">
                                        <span class="badge bg-secondary">Not Started</span>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-success" id="officePunchIn" onclick="punchIn('office')">
                                            <i class="fas fa-sign-in-alt"></i> Punch In (Start New Cycle)
                                        </button>
                                        <button type="button" class="btn btn-danger" id="officePunchOut" onclick="punchOut('office')" style="display: none;">
                                            <i class="fas fa-sign-out-alt"></i> Punch Out
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Field Work</h6>
                                </div>
                                <div class="card-body">
                                    <div id="fieldStatus" class="mb-3">
                                        <span class="badge bg-secondary">Not Started</span>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-success" id="fieldPunchIn" onclick="punchIn('field')">
                                            <i class="fas fa-map-marker-alt"></i> Start Field Work (New Cycle)
                                        </button>
                                        <button type="button" class="btn btn-danger" id="fieldPunchOut" onclick="punchOut('field')" style="display: none;">
                                            <i class="fas fa-home"></i> End Field Work
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Break Management</h6>
                                </div>
                                <div class="card-body">
                                    <div id="breakStatus" class="mb-3">
                                        <span class="badge bg-secondary">Not Started</span>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-warning" id="breakStart" onclick="startBreak()">
                                            <i class="fas fa-coffee"></i> Start Break (New Cycle)
                                        </button>
                                        <button type="button" class="btn btn-info" id="breakEnd" onclick="endBreak()" style="display: none;">
                                            <i class="fas fa-play"></i> End Break
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                                         <!-- No description modal needed - automatic transitions -->

                    <!-- Today's Movements -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6>Today's Movements</h6>
                                    <small class="text-muted">
                                        <i class="fas fa-sync-alt me-1"></i>
                                        Auto-updates every action
                                    </small>
                                </div>
                                <div class="card-body">
                                    <!-- Work Cycles Summary -->
                                    <div class="row mb-3" id="workCyclesSummary">
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6>Office Cycles</h6>
                                                    <h4 id="officeCycles">0</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6>Field Cycles</h6>
                                                    <h4 id="fieldCycles">0</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6>Break Cycles</h6>
                                                    <h4 id="breakCycles">0</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="todayMovements">
                                        <p class="text-muted">No movements recorded yet.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load attendance status on page load
document.addEventListener('DOMContentLoaded', function() {
    loadTodayStatus();
    loadAttendanceStats();
});

function punchIn(type) {
    performPunchIn(type);
}

function punchOut(type) {
    performPunchOut(type);
}

function startBreak() {
    performStartBreak();
}

function endBreak() {
    performEndBreak();
}

function performPunchIn(type) {
    $.ajax({
        url: '/attendance/punch-in',
        method: 'POST',
        data: {
            movement_type: type,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                loadTodayStatus();
                loadAttendanceStats();
            } else {
                showAlert('error', response.message);
            }
        },
        error: function(xhr) {
            console.error('Punch in error:', xhr.responseText);
            if (xhr.status === 500) {
                showAlert('error', 'Server error occurred. Please check the console for details.');
            } else {
                showAlert('error', 'An error occurred. Please try again.');
            }
        }
    });
}

function performPunchOut(type) {
    $.ajax({
        url: '/attendance/punch-out',
        method: 'POST',
        data: {
            movement_type: type,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                loadTodayStatus();
                loadAttendanceStats();
            } else {
                showAlert('error', response.message);
            }
        },
        error: function(xhr) {
            console.error('Punch out error:', xhr.responseText);
            if (xhr.status === 500) {
                showAlert('error', 'Server error occurred. Please check the console for details.');
            } else {
                showAlert('error', 'An error occurred. Please try again.');
            }
        }
    });
}

function performStartBreak() {
    $.ajax({
        url: '/attendance/start-break',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                loadTodayStatus();
                loadAttendanceStats();
            } else {
                showAlert('error', response.message);
            }
        },
        error: function(xhr) {
            console.error('Start break error:', xhr.responseText);
            if (xhr.status === 500) {
                showAlert('error', 'Server error occurred. Please check the console for details.');
            } else {
                showAlert('error', 'An error occurred. Please try again.');
            }
        }
    });
}

function performEndBreak() {
    $.ajax({
        url: '/attendance/end-break',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                loadTodayStatus();
                loadAttendanceStats();
            } else {
                showAlert('error', response.message);
            }
        },
        error: function(xhr) {
            console.error('End break error:', xhr.responseText);
            if (xhr.status === 500) {
                showAlert('error', 'Server error occurred. Please check the console for details.');
            } else {
                showAlert('error', 'An error occurred. Please try again.');
            }
        }
    });
}

function loadTodayStatus() {
    $.ajax({
        url: '/attendance/today-status',
        method: 'GET',
        success: function(response) {
            // Handle case when no attendance record exists
            if (response.status === 'not_started') {
                // Create default status structure for new users
                const defaultStatus = {
                    office: { punched_in: false, punched_out: false, break_started: false, break_ended: false },
                    field: { punched_in: false, punched_out: false, break_started: false, break_ended: false },
                    break: { punched_in: false, punched_out: false, break_started: false, break_ended: false }
                };
                updateStatusDisplay(defaultStatus);
            } else {
                updateStatusDisplay(response.status);
            }
            updateMovementsDisplay(response.movements);
        },
        error: function(xhr) {
            console.error('Error loading today status:', xhr.responseText);
            showAlert('error', 'Failed to load attendance status. Please refresh the page.');
        }
    });
}

function loadAttendanceStats() {
    $.ajax({
        url: '/attendance/stats',
        method: 'GET',
        success: function(response) {
            document.getElementById('todayHours').textContent = response.today_hours;
            document.getElementById('monthHours').textContent = response.month_hours;
            document.getElementById('totalDays').textContent = response.total_days;
            document.getElementById('avgHours').textContent = response.avg_hours_per_day;
        },
        error: function(xhr) {
            console.error('Error loading attendance stats:', xhr.responseText);
            // Don't show alert for stats errors as they're not critical
        }
    });
}

function updateStatusDisplay(status) {
    // Office status
    const officeStatus = document.getElementById('officeStatus');
    const officePunchIn = document.getElementById('officePunchIn');
    const officePunchOut = document.getElementById('officePunchOut');
    
    if (status.office.punched_in) {
        officeStatus.innerHTML = '<span class="badge bg-success">Punched In</span>';
        officePunchIn.style.display = 'none';
        officePunchOut.style.display = 'block';
    } else {
        officeStatus.innerHTML = '<span class="badge bg-primary">Ready for New Cycle</span>';
        officePunchIn.style.display = 'block';
        officePunchOut.style.display = 'none';
    }

    // Field status
    const fieldStatus = document.getElementById('fieldStatus');
    const fieldPunchIn = document.getElementById('fieldPunchIn');
    const fieldPunchOut = document.getElementById('fieldPunchOut');
    
    if (status.field.punched_in) {
        fieldStatus.innerHTML = '<span class="badge bg-success">In Field</span>';
        fieldPunchIn.style.display = 'none';
        fieldPunchOut.style.display = 'block';
    } else {
        fieldStatus.innerHTML = '<span class="badge bg-primary">Ready for New Cycle</span>';
        fieldPunchIn.style.display = 'block';
        fieldPunchOut.style.display = 'none';
    }

    // Break status
    const breakStatus = document.getElementById('breakStatus');
    const breakStart = document.getElementById('breakStart');
    const breakEnd = document.getElementById('breakEnd');
    
    if (status.break.break_started) {
        breakStatus.innerHTML = '<span class="badge bg-warning">On Break</span>';
        breakStart.style.display = 'none';
        breakEnd.style.display = 'block';
    } else {
        breakStatus.innerHTML = '<span class="badge bg-primary">Ready for New Cycle</span>';
        breakStart.style.display = 'block';
        breakEnd.style.display = 'none';
    }
}

function updateMovementsDisplay(movements) {
    const container = document.getElementById('todayMovements');
    
    if (!movements || Object.keys(movements).length === 0) {
        container.innerHTML = '<p class="text-muted">No movements recorded yet.</p>';
        updateWorkCyclesSummary({});
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-striped">';
    html += '<thead><tr><th>Time</th><th>Type</th><th>Action</th><th>Cycle</th></tr></thead><tbody>';
    
    let allMovements = [];
    Object.values(movements).forEach(typeMovements => {
        typeMovements.forEach(movement => {
            allMovements.push(movement);
        });
    });
    
    // Sort by time
    allMovements.sort((a, b) => new Date(a.time) - new Date(b.time));
    
    // Calculate cycles for each type
    const cycles = calculateWorkCycles(movements);
    updateWorkCyclesSummary(cycles);
    
    allMovements.forEach(movement => {
        const time = new Date(movement.time).toLocaleTimeString();
        const type = movement.movement_type.charAt(0).toUpperCase() + movement.movement_type.slice(1);
        const action = movement.movement_action.charAt(0).toUpperCase() + movement.movement_action.slice(1);
        
        // Check if this is an automatic transition
        const isAutoTransition = movement.description && movement.description.includes('Auto-ended');
        const actionBadge = isAutoTransition 
            ? `<span class="badge bg-secondary">${action} <i class="fas fa-magic ms-1"></i></span>`
            : `<span class="badge bg-${getActionColor(movement.movement_action)}">${action}</span>`;
        
        // Calculate cycle number for this movement
        const cycleNumber = getCycleNumber(movements, movement);
        
        html += `<tr>
            <td>${time}</td>
            <td><span class="badge bg-primary">${type}</span></td>
            <td>${actionBadge}</td>
            <td><span class="badge bg-info">Cycle ${cycleNumber}</span></td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

function calculateWorkCycles(movements) {
    const cycles = { office: 0, field: 0, break: 0 };
    
    Object.keys(movements).forEach(type => {
        const typeMovements = movements[type];
        if (type === 'break') {
            // Count completed break cycles (start-end pairs)
            let startCount = 0;
            let endCount = 0;
            typeMovements.forEach(movement => {
                if (movement.movement_action === 'start') startCount++;
                if (movement.movement_action === 'end') endCount++;
            });
            cycles[type] = Math.min(startCount, endCount);
        } else {
            // Count completed punch in-out cycles
            let inCount = 0;
            let outCount = 0;
            typeMovements.forEach(movement => {
                if (movement.movement_action === 'in') inCount++;
                if (movement.movement_action === 'out') outCount++;
            });
            cycles[type] = Math.min(inCount, outCount);
        }
    });
    
    return cycles;
}

function getCycleNumber(movements, currentMovement) {
    const type = currentMovement.movement_type;
    const action = currentMovement.movement_action;
    const typeMovements = movements[type] || [];
    
    let cycleCount = 0;
    let currentCycle = 1;
    
    for (let i = 0; i < typeMovements.length; i++) {
        const movement = typeMovements[i];
        
        if (type === 'break') {
            if (movement.movement_action === 'start') {
                cycleCount++;
                currentCycle = cycleCount;
            }
        } else {
            if (movement.movement_action === 'in') {
                cycleCount++;
                currentCycle = cycleCount;
            }
        }
        
        if (movement.id === currentMovement.id) {
            break;
        }
    }
    
    return currentCycle;
}

function updateWorkCyclesSummary(cycles) {
    document.getElementById('officeCycles').textContent = cycles.office || 0;
    document.getElementById('fieldCycles').textContent = cycles.field || 0;
    document.getElementById('breakCycles').textContent = cycles.break || 0;
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
    const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    
    // Insert at the top of the card body
    const cardBody = document.querySelector('.card-body');
    cardBody.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>
@endsection
