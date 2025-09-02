<?php
// Get volunteer requests
$requests_sql = "SELECT * FROM volunteer_requests ORDER BY created_at DESC";
$requests_result = mysqli_query($conn, $requests_sql);
?>

<!-- Volunteer Requests Section -->
<div class="tab-pane fade" id="volunteer-requests">
    <div class="content-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-hands-helping"></i> Volunteer Requests</h2>
            <div class="btn-group">
                <button class="btn btn-outline-primary" id="exportRequests">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="search-box">
                    <input type="text" class="form-control" id="requestSearch" placeholder="Search requests...">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            <div class="col-md-6">
                <select class="form-select" id="requestFilter">
                    <option value="all">All Requests</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>

        <!-- Requests Table -->
        <div class="table-container">
            <table class="table" id="requestsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Skills</th>
                        <th>Status</th>
                        <th>Applied On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($requests_result) {
                        while($request = mysqli_fetch_assoc($requests_result)) {
                            $status_class = $request['status'] === 'pending' ? 'warning' : 
                                          ($request['status'] === 'approved' ? 'success' : 'danger');
                            echo "<tr>
                                <td>{$request['first_name']} {$request['last_name']}</td>
                                <td>{$request['email']}</td>
                                <td>{$request['phone']}</td>
                                <td>" . substr($request['skills'], 0, 50) . "...</td>
                                <td><span class='status-badge bg-{$status_class}'>{$request['status']}</span></td>
                                <td>" . date('M d, Y', strtotime($request['created_at'])) . "</td>
                                <td>
                                    <button class='action-btn btn-info' onclick='viewRequest({$request['id']})'>
                                        <i class='fas fa-eye'></i>
                                    </button>
                                    " . ($request['status'] === 'pending' ? "
                                    <button class='action-btn btn-success' onclick='updateRequestStatus({$request['id']}, \"approved\")'>
                                        <i class='fas fa-check'></i>
                                    </button>
                                    <button class='action-btn btn-danger' onclick='updateRequestStatus({$request['id']}, \"rejected\")'>
                                        <i class='fas fa-times'></i>
                                    </button>" : "") . "
                                </td>
                            </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Request Modal -->
<div class="modal fade" id="viewRequestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Volunteer Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> <span id="viewName"></span></p>
                        <p><strong>Email:</strong> <span id="viewEmail"></span></p>
                        <p><strong>Phone:</strong> <span id="viewPhone"></span></p>
                        <p><strong>Status:</strong> <span id="viewStatus"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Applied On:</strong> <span id="viewDate"></span></p>
                        <p><strong>Skills:</strong> <span id="viewSkills"></span></p>
                        <p><strong>Availability:</strong> <span id="viewAvailability"></span></p>
                        <p><strong>Areas of Interest:</strong> <span id="viewInterests"></span></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Previous Experience:</strong></p>
                        <p id="viewExperience" class="text-muted"></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Motivation:</strong></p>
                        <p id="viewMotivation" class="text-muted"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// View request details
function viewRequest(id) {
    fetch(`actions/get_request.php?request_id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const request = data.data;
                document.getElementById('viewName').textContent = `${request.first_name} ${request.last_name}`;
                document.getElementById('viewEmail').textContent = request.email;
                document.getElementById('viewPhone').textContent = request.phone;
                document.getElementById('viewStatus').textContent = request.status;
                document.getElementById('viewDate').textContent = new Date(request.created_at).toLocaleDateString();
                document.getElementById('viewSkills').textContent = request.skills;
                document.getElementById('viewAvailability').textContent = request.availability;
                document.getElementById('viewInterests').textContent = request.areas_of_interest;
                document.getElementById('viewExperience').textContent = request.previous_experience || 'None provided';
                document.getElementById('viewMotivation').textContent = request.motivation;
                
                $('#viewRequestModal').modal('show');
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'An error occurred. Please try again.');
            console.error('Error:', error);
        });
}

// Update request status
function updateRequestStatus(id, status) {
    if (confirm(`Are you sure you want to ${status} this volunteer request?`)) {
        const formData = new FormData();
        formData.append('request_id', id);
        formData.append('status', status);

        fetch('actions/update_request_status.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'An error occurred. Please try again.');
            console.error('Error:', error);
        });
    }
}

// Export requests
document.getElementById('exportRequests').addEventListener('click', function() {
    window.location.href = 'actions/export_requests.php';
});

// Search and filter functionality
document.getElementById('requestSearch').addEventListener('input', function(e) {
    const searchText = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#requestsTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchText) ? '' : 'none';
    });
});

document.getElementById('requestFilter').addEventListener('change', function(e) {
    const filter = e.target.value;
    const rows = document.querySelectorAll('#requestsTable tbody tr');
    
    rows.forEach(row => {
        if (filter === 'all') {
            row.style.display = '';
        } else {
            const status = row.querySelector('.status-badge').textContent.toLowerCase();
            row.style.display = status === filter ? '' : 'none';
        }
    });
});
</script> 