<?php
// Get events for the current month
$current_month = date('m');
$current_year = date('Y');
$events_sql = "SELECT * FROM events 
               WHERE MONTH(event_date) = ? AND YEAR(event_date) = ?
               ORDER BY event_date ASC";
$stmt = mysqli_prepare($conn, $events_sql);
mysqli_stmt_bind_param($stmt, "ii", $current_month, $current_year);
mysqli_stmt_execute($stmt);
$events_result = mysqli_stmt_get_result($stmt);

// Get upcoming events
$upcoming_sql = "SELECT * FROM events 
                 WHERE event_date > CURRENT_DATE()
                 ORDER BY event_date ASC LIMIT 5";
$upcoming_result = mysqli_query($conn, $upcoming_sql);

// Get past events
$past_sql = "SELECT * FROM events 
             WHERE event_date < CURRENT_DATE()
             ORDER BY event_date DESC LIMIT 5";
$past_result = mysqli_query($conn, $past_sql);
?>

<div class="tab-pane fade" id="events">
    <div class="content-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Events Management</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                <i class="fas fa-plus"></i> Add New Event
            </button>
        </div>

        <!-- Current Month Events -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="content-section">
                    <h3><i class="fas fa-calendar-alt"></i> Current Month Events</h3>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Event Name</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($events_result && mysqli_num_rows($events_result) > 0) {
                                    while($event = mysqli_fetch_assoc($events_result)) {
                                        $status_class = $event['event_date'] < date('Y-m-d') ? 'danger' : 
                                                      ($event['event_date'] == date('Y-m-d') ? 'warning' : 'success');
                                        echo "<tr>
                                            <td>{$event['event_name']}</td>
                                            <td>" . date('M d, Y', strtotime($event['event_date'])) . "</td>
                                            <td>{$event['event_time']}</td>
                                            <td>{$event['location']}</td>
                                            <td><span class='status-badge bg-{$status_class}'>" . 
                                                ($event['event_date'] < date('Y-m-d') ? 'Past' : 
                                                ($event['event_date'] == date('Y-m-d') ? 'Today' : 'Upcoming')) . 
                                            "</span></td>
                                            <td>
                                                <button class='action-btn btn-info' onclick='editEvent({$event['id']})'>
                                                    <i class='fas fa-edit'></i> Edit
                                                </button>
                                                <button class='action-btn btn-danger' onclick='deleteEvent({$event['id']})'>
                                                    <i class='fas fa-trash'></i> Delete
                                                </button>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>No events for this month</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming and Past Events -->
        <div class="row">
            <div class="col-md-6">
                <div class="content-section">
                    <h3><i class="fas fa-calendar-plus"></i> Upcoming Events</h3>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Event Name</th>
                                    <th>Date</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($upcoming_result && mysqli_num_rows($upcoming_result) > 0) {
                                    while($event = mysqli_fetch_assoc($upcoming_result)) {
                                        echo "<tr>
                                            <td>{$event['event_name']}</td>
                                            <td>" . date('M d, Y', strtotime($event['event_date'])) . "</td>
                                            <td>{$event['location']}</td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3' class='text-center'>No upcoming events</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="content-section">
                    <h3><i class="fas fa-history"></i> Past Events</h3>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Event Name</th>
                                    <th>Date</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($past_result && mysqli_num_rows($past_result) > 0) {
                                    while($event = mysqli_fetch_assoc($past_result)) {
                                        echo "<tr>
                                            <td>{$event['event_name']}</td>
                                            <td>" . date('M d, Y', strtotime($event['event_date'])) . "</td>
                                            <td>{$event['location']}</td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3' class='text-center'>No past events</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addEventForm">
                    <div class="mb-3">
                        <label class="form-label">Event Name</label>
                        <input type="text" class="form-control" name="event_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Event Date</label>
                        <input type="date" class="form-control" name="event_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Event Time</label>
                        <input type="time" class="form-control" name="event_time" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveEvent()">Save Event</button>
            </div>
        </div>
    </div>
</div>

<script>
function saveEvent() {
    const form = document.getElementById('addEventForm');
    const formData = new FormData(form);
    
    fetch('actions/add_event.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the event');
    });
}

function editEvent(eventId) {
    // Fetch event details and populate edit modal
    fetch(`actions/get_event.php?id=${eventId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate form and show modal
                const event = data.event;
                document.getElementById('editEventForm').event_name.value = event.event_name;
                document.getElementById('editEventForm').event_date.value = event.event_date;
                document.getElementById('editEventForm').event_time.value = event.event_time;
                document.getElementById('editEventForm').location.value = event.location;
                document.getElementById('editEventForm').description.value = event.description;
                document.getElementById('editEventForm').event_id.value = event.id;
                
                new bootstrap.Modal(document.getElementById('editEventModal')).show();
            }
        });
}

function deleteEvent(eventId) {
    if (confirm('Are you sure you want to delete this event?')) {
        fetch(`actions/delete_event.php?id=${eventId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
    }
}
</script> 