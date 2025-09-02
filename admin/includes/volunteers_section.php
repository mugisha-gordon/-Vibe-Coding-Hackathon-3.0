<?php
// Get all volunteers
$volunteers_sql = "SELECT * FROM volunteers ORDER BY created_at DESC";
$volunteers_result = mysqli_query($conn, $volunteers_sql);

// Get pending volunteer requests
$requests_sql = "SELECT * FROM volunteer_requests WHERE status = 'pending' ORDER BY created_at DESC";
$requests_result = mysqli_query($conn, $requests_sql);
?>

<!-- Volunteers Section -->
<div class="tab-pane fade" id="volunteers">
    <div class="content-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Volunteers Management</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVolunteerModal">
                <i class="fas fa-plus"></i> Add New Volunteer
            </button>
    </div>

        <!-- Volunteers List -->
    <div class="row mb-4">
            <div class="col-12">
                <div class="content-section">
                    <h3><i class="fas fa-users"></i> All Volunteers</h3>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Skills</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($volunteers_result && mysqli_num_rows($volunteers_result) > 0) {
                                    while($volunteer = mysqli_fetch_assoc($volunteers_result)) {
                                        $status_class = $volunteer['status'] === 'active' ? 'success' : 
                                                      ($volunteer['status'] === 'pending' ? 'warning' : 'danger');
                                        echo "<tr>
                                            <td>
                                                <img src='" . ($volunteer['profile_picture'] ? "../" . $volunteer['profile_picture'] : "assets/images/default-avatar.png") . "' 
                                                     alt='Profile Picture' class='profile-thumbnail'>
                                            </td>
                                            <td>{$volunteer['name']}</td>
                                            <td>{$volunteer['email']}</td>
                                            <td>{$volunteer['phone']}</td>
                                            <td>{$volunteer['skills']}</td>
                                            <td><span class='status-badge bg-{$status_class}'>{$volunteer['status']}</span></td>
                                            <td>
                                                <button class='action-btn btn-info' onclick='viewVolunteer({$volunteer['id']})'>
                                                    <i class='fas fa-eye'></i> View
                                                </button>
                                                <button class='action-btn btn-warning' onclick='editVolunteer({$volunteer['id']})'>
                                                    <i class='fas fa-edit'></i> Edit
                                                </button>
                                                <button class='action-btn btn-danger' onclick='deleteVolunteer({$volunteer['id']})'>
                                                    <i class='fas fa-trash'></i> Delete
                                                </button>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center'>No volunteers found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
            </div>
            </div>
        </div>
    </div>

        <!-- Pending Requests -->
        <div class="row">
            <div class="col-12">
                <div class="content-section">
                    <h3><i class="fas fa-clock"></i> Pending Requests</h3>
                    <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                                    <th>Skills</th>
                                    <th>Applied Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                            <tbody>
                    <?php
                                if ($requests_result && mysqli_num_rows($requests_result) > 0) {
                                    while($request = mysqli_fetch_assoc($requests_result)) {
                                        echo "<tr>
                                            <td>{$request['name']}</td>
                                            <td>{$request['email']}</td>
                                            <td>{$request['phone']}</td>
                                            <td>{$request['skills']}</td>
                                            <td>" . date('M d, Y', strtotime($request['created_at'])) . "</td>
                                            <td>
                                                <button class='action-btn btn-success' onclick='approveRequest({$request['id']})'>
                                                    <i class='fas fa-check'></i> Approve
                                        </button>
                                                <button class='action-btn btn-danger' onclick='rejectRequest({$request['id']})'>
                                                    <i class='fas fa-times'></i> Reject
                                        </button>
                                </td>
                            </tr>";
                        }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>No pending requests</td></tr>";
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

<!-- Add Volunteer Modal -->
<div class="modal fade" id="addVolunteerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Volunteer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addVolunteerForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" name="profile_picture" accept="image/*" onchange="previewImage(this, 'addPreview')">
                        <img id="addPreview" src="assets/images/default-avatar.png" class="mt-2 preview-image" style="display: none;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Skills</label>
                        <input type="text" class="form-control" name="skills" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Availability</label>
                        <input type="text" class="form-control" name="availability" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveVolunteer()">Save Volunteer</button>
            </div>
        </div>
    </div>
</div>

<!-- View Volunteer Modal -->
<div class="modal fade" id="viewVolunteerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Volunteer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <img id="viewProfilePicture" src="assets/images/default-avatar.png" class="profile-image">
                </div>
                <div class="volunteer-details">
                    <p><strong>Name:</strong> <span id="viewName"></span></p>
                    <p><strong>Email:</strong> <span id="viewEmail"></span></p>
                    <p><strong>Phone:</strong> <span id="viewPhone"></span></p>
                    <p><strong>Address:</strong> <span id="viewAddress"></span></p>
                    <p><strong>Skills:</strong> <span id="viewSkills"></span></p>
                    <p><strong>Availability:</strong> <span id="viewAvailability"></span></p>
                    <p><strong>Status:</strong> <span id="viewStatus"></span></p>
                    <p><strong>Joined:</strong> <span id="viewCreatedAt"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Volunteer Modal -->
<div class="modal fade" id="editVolunteerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Volunteer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editVolunteerForm" enctype="multipart/form-data">
                    <input type="hidden" name="volunteer_id">
                    <div class="mb-3">
                        <label class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" name="profile_picture" accept="image/*" onchange="previewImage(this, 'editPreview')">
                        <img id="editPreview" src="assets/images/default-avatar.png" class="mt-2 preview-image" style="display: none;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Skills</label>
                        <input type="text" class="form-control" name="skills" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Availability</label>
                        <input type="text" class="form-control" name="availability" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateVolunteer()">Update Volunteer</button>
            </div>
        </div>
    </div>
</div> 

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function saveVolunteer() {
    const form = document.getElementById('addVolunteerForm');
    const formData = new FormData(form);
    
    fetch('actions/add_volunteer.php', {
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
        alert('An error occurred while saving the volunteer');
    });
}

function viewVolunteer(volunteerId) {
    fetch(`actions/get_volunteer.php?volunteer_id=${volunteerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const volunteer = data.data;
                document.getElementById('viewProfilePicture').src = volunteer.profile_picture_url || 'assets/images/default-avatar.png';
                document.getElementById('viewName').textContent = volunteer.name;
                document.getElementById('viewEmail').textContent = volunteer.email;
                document.getElementById('viewPhone').textContent = volunteer.phone;
                document.getElementById('viewAddress').textContent = volunteer.address;
                document.getElementById('viewSkills').textContent = volunteer.skills;
                document.getElementById('viewAvailability').textContent = volunteer.availability;
                document.getElementById('viewStatus').textContent = volunteer.status;
                document.getElementById('viewCreatedAt').textContent = volunteer.created_at;
                
                new bootstrap.Modal(document.getElementById('viewVolunteerModal')).show();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while fetching volunteer details');
        });
}

function editVolunteer(volunteerId) {
    fetch(`actions/get_volunteer.php?volunteer_id=${volunteerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const volunteer = data.data;
                const form = document.getElementById('editVolunteerForm');
                
                form.volunteer_id.value = volunteer.id;
                form.name.value = volunteer.name;
                form.email.value = volunteer.email;
                form.phone.value = volunteer.phone;
                form.address.value = volunteer.address;
                form.skills.value = volunteer.skills;
                form.availability.value = volunteer.availability;
                form.status.value = volunteer.status;
                
                const preview = document.getElementById('editPreview');
                preview.src = volunteer.profile_picture_url || 'assets/images/default-avatar.png';
                preview.style.display = 'block';
                
                new bootstrap.Modal(document.getElementById('editVolunteerModal')).show();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while fetching volunteer details');
        });
}

function updateVolunteer() {
    const form = document.getElementById('editVolunteerForm');
    const formData = new FormData(form);
    
    fetch('actions/update_volunteer.php', {
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
        alert('An error occurred while updating the volunteer');
    });
}

function deleteVolunteer(volunteerId) {
    if (confirm('Are you sure you want to delete this volunteer?')) {
        const formData = new FormData();
        formData.append('volunteer_id', volunteerId);
        
        fetch('actions/delete_volunteer.php', {
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
            alert('An error occurred while deleting the volunteer');
        });
    }
}

function approveRequest(requestId) {
    if (confirm('Are you sure you want to approve this volunteer request?')) {
        const formData = new FormData();
        formData.append('request_id', requestId);
        formData.append('status', 'approved');
        
        fetch('actions/update_volunteer_request.php', {
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
            alert('An error occurred while approving the request');
        });
    }
}

function rejectRequest(requestId) {
    if (confirm('Are you sure you want to reject this volunteer request?')) {
        const formData = new FormData();
        formData.append('request_id', requestId);
        formData.append('status', 'rejected');
        
        fetch('actions/update_volunteer_request.php', {
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
            alert('An error occurred while rejecting the request');
        });
    }
}
</script> 