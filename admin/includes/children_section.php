<?php
// Get children list
$children_sql = "SELECT * FROM children ORDER BY created_at DESC";
$children_result = mysqli_query($conn, $children_sql);
?>

<!-- Children Section -->
<div class="tab-pane fade" id="children">
    <div class="content-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-child"></i> Children Management</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addChildModal">
                <i class="fas fa-plus"></i> Add New Child
            </button>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="search-box">
                <input type="text" class="form-control" id="childSearch" placeholder="Search children...">
                <i class="fas fa-search"></i>
            </div>
        </div>
        <div class="col-md-6">
            <select class="form-select" id="childFilter">
                <option value="all">All Children</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <!-- Children List -->
    <div class="table-container">
        <table class="table" id="childrenTable">
            <thead>
                <tr>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Guardian</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Table content will be loaded dynamically -->
            </tbody>
        </table>
    </div>
    </div>
</div>

<!-- Add Child Modal -->
<div class="modal fade" id="addChildModal" tabindex="-1" aria-labelledby="addChildModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addChildModalLabel">Add New Child</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addChildForm" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Profile Picture Upload -->
                        <div class="col-md-4 text-center mb-3">
                            <div class="profile-picture-container">
                                <img id="profilePreview" src="assets/images/default-child.png" alt="Profile Preview" class="img-thumbnail">
                                <div class="profile-upload-overlay">
                                    <i class="fas fa-camera"></i>
                                    <span>Upload Photo</span>
                                </div>
                                <input type="file" id="profilePicture" name="profile_picture" accept="image/*" class="d-none">
                            </div>
                        </div>

                        <!-- Child Information -->
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="first_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="last_name" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="dateOfBirth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="dateOfBirth" name="date_of_birth" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="guardianName" class="form-label">Guardian Name</label>
                                    <input type="text" class="form-control" id="guardianName" name="guardian_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="guardianPhone" class="form-label">Guardian Phone</label>
                                    <input type="tel" class="form-control" id="guardianPhone" name="guardian_phone" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="medicalInfo" class="form-label">Medical Information</label>
                                <textarea class="form-control" id="medicalInfo" name="medical_info" rows="2"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveChildBtn">Save Child</button>
            </div>
        </div>
    </div>
</div>

<style>
.profile-picture-container {
    position: relative;
    width: 200px;
    height: 200px;
    margin: 0 auto;
    border-radius: 50%;
    overflow: hidden;
    cursor: pointer;
}

.profile-picture-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-upload-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: white;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.profile-picture-container:hover .profile-upload-overlay {
    opacity: 1;
}

.profile-upload-overlay i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Profile picture upload handling
    const profileContainer = document.querySelector('.profile-picture-container');
    const profileInput = document.getElementById('profilePicture');
    const profilePreview = document.getElementById('profilePreview');

    profileContainer.addEventListener('click', () => {
        profileInput.click();
    });

    profileInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePreview.src = e.target.result;
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Form submission handling
    const addChildForm = document.getElementById('addChildForm');
    const saveChildBtn = document.getElementById('saveChildBtn');

    saveChildBtn.addEventListener('click', function() {
        if (addChildForm.checkValidity()) {
            const formData = new FormData(addChildForm);
            
            fetch('actions/add_child.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Child added successfully');
                    $('#addChildModal').modal('hide');
                    loadChildrenTable(); // Refresh the table
                } else {
                    showAlert('danger', data.error || 'Failed to add child');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while adding the child');
            });
        } else {
            addChildForm.reportValidity();
        }
    });

    // Load children table
    function loadChildrenTable() {
        fetch('actions/get_table_data.php?table=children')
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector('#childrenTable tbody');
                tbody.innerHTML = data.html;
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Failed to load children data');
            });
    }

    // Initial load
    loadChildrenTable();
});
</script>

<!-- View Child Modal -->
<div class="modal fade" id="viewChildModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Child Details</h5>
<?php
// Get children list
$children_sql = "SELECT * FROM children ORDER BY created_at DESC";
$children_result = mysqli_query($conn, $children_sql);
?>

<!-- Children Section -->
<div class="tab-pane fade" id="children">
    <div class="content-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-child"></i> Children Management</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addChildModal">
                <i class="fas fa-plus"></i> Add New Child
            </button>
    </div>

        <!-- Children List -->
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Guardian</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($children_result) {
                        while($child = mysqli_fetch_assoc($children_result)) {
                            $profile_pic = !empty($child['profile_picture']) ? '../' . $child['profile_picture'] : 'assets/images/default-child.png';
                            $status_class = $child['status'] === 'active' ? 'success' : 'warning';
                            echo "<tr>
                                <td>
                                    <img src='{$profile_pic}' alt='{$child['name']}' class='child-profile-pic'>
                                </td>
                                <td>{$child['name']}</td>
                                <td>{$child['age']}</td>
                                <td>{$child['gender']}</td>
                                <td>{$child['guardian_name']}</td>
                                <td>
                                    <div>{$child['guardian_phone']}</div>
                                    <small class='text-muted'>{$child['guardian_email']}</small>
                                </td>
                                <td><span class='status-badge bg-{$status_class}'>{$child['status']}</span></td>
                                <td>
                                    <button class='action-btn btn-info' onclick='viewChild({$child['id']})'>
                                            <i class='fas fa-eye'></i>
                                        </button>
                                    <button class='action-btn btn-warning' onclick='editChild({$child['id']})'>
                                            <i class='fas fa-edit'></i>
                                        </button>
                                    <button class='action-btn btn-danger' onclick='deleteChild({$child['id']})'>
                                        <i class='fas fa-trash'></i>
                                        </button>
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

<!-- Add Child Modal -->
<div class="modal fade" id="addChildModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Child</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addChildForm" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="profile-picture-upload">
                                <div class="upload-preview">
                                    <img src="assets/images/default-child.png" id="profilePreview" alt="Profile Preview">
                                </div>
                                <div class="upload-controls">
                                    <label for="profile_picture" class="btn btn-outline-primary">
                                        <i class="fas fa-camera"></i> Choose Photo
                                    </label>
                                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="d-none">
                                </div>
                            </div>
                    </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Child's Name *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Age *</label>
                                    <input type="number" class="form-control" name="age" min="0" max="18" required>
                    </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Gender *</label>
                                    <select class="form-select" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                        </select>
                    </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Guardian's Name *</label>
                        <input type="text" class="form-control" name="guardian_name" required>
                    </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Guardian's Phone *</label>
                                    <input type="tel" class="form-control" name="guardian_phone" required>
                                </div>
                    </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                        <label class="form-label">Guardian's Email</label>
                        <input type="email" class="form-control" name="guardian_email">
                    </div>
                                <div class="col-md-6 mb-3">
                        <label class="form-label">Address</label>
                                    <input type="text" class="form-control" name="address">
                                </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Additional Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveChild()">Save Child</button>
            </div>
        </div>
    </div>
</div>

<style>
.profile-picture-upload {
    text-align: center;
    padding: 20px;
    border: 2px dashed #ddd;
    border-radius: 10px;
    margin-bottom: 20px;
}

.upload-preview {
    width: 200px;
    height: 200px;
    margin: 0 auto 15px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #fff;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.upload-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.child-profile-pic {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.upload-controls {
    margin-top: 10px;
}

.upload-controls label {
    cursor: pointer;
    margin: 0;
}
</style>

<script>
// Preview profile picture before upload
document.getElementById('profile_picture').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePreview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

// Save new child
function saveChild() {
    const form = document.getElementById('addChildForm');
    const formData = new FormData(form);

    fetch('actions/add_child.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            $('#addChildModal').modal('hide');
            form.reset();
            document.getElementById('profilePreview').src = 'assets/images/default-child.png';
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

// View child details
function viewChild(id) {
    fetch(`actions/get_child.php?child_id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const child = data.data;
                // Populate view modal with child details
                document.getElementById('viewChildName').textContent = child.name;
                document.getElementById('viewChildAge').textContent = child.age;
                document.getElementById('viewChildGender').textContent = child.gender;
                document.getElementById('viewGuardianName').textContent = child.guardian_name;
                document.getElementById('viewGuardianPhone').textContent = child.guardian_phone;
                document.getElementById('viewGuardianEmail').textContent = child.guardian_email || 'N/A';
                document.getElementById('viewAddress').textContent = child.address || 'N/A';
                document.getElementById('viewNotes').textContent = child.notes || 'N/A';
                document.getElementById('viewProfilePic').src = child.profile_picture_url || 'assets/images/default-child.png';
                
                $('#viewChildModal').modal('show');
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'An error occurred. Please try again.');
            console.error('Error:', error);
        });
}

// Edit child
function editChild(id) {
    fetch(`actions/get_child.php?child_id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const child = data.data;
                const form = document.getElementById('editChildForm');
                
                // Populate form with child details
                form.querySelector('[name="child_id"]').value = child.id;
                form.querySelector('[name="name"]').value = child.name;
                form.querySelector('[name="age"]').value = child.age;
                form.querySelector('[name="gender"]').value = child.gender;
                form.querySelector('[name="guardian_name"]').value = child.guardian_name;
                form.querySelector('[name="guardian_phone"]').value = child.guardian_phone;
                form.querySelector('[name="guardian_email"]').value = child.guardian_email || '';
                form.querySelector('[name="address"]').value = child.address || '';
                form.querySelector('[name="notes"]').value = child.notes || '';
                
                // Set profile picture preview
                document.getElementById('editProfilePreview').src = child.profile_picture_url || 'assets/images/default-child.png';
                
                $('#editChildModal').modal('show');
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'An error occurred. Please try again.');
            console.error('Error:', error);
        });
}

// Update child
function updateChild() {
    const form = document.getElementById('editChildForm');
    const formData = new FormData(form);

    fetch('actions/update_child.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            $('#editChildModal').modal('hide');
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

// Delete child
function deleteChild(id) {
    if (confirm('Are you sure you want to delete this child? This action cannot be undone.')) {
        const formData = new FormData();
        formData.append('child_id', id);

        fetch('actions/delete_child.php', {
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

// Show alert message
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.content-section');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>

<!-- View Child Modal -->
<div class="modal fade" id="viewChildModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Child Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img id="viewProfilePic" src="assets/images/default-child.png" alt="Profile Picture" class="img-fluid rounded-circle mb-3" style="max-width: 200px;">
                    </div>
                    <div class="col-md-8">
                        <h4 id="viewChildName" class="mb-3"></h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Age:</strong> <span id="viewChildAge"></span></p>
                                <p><strong>Gender:</strong> <span id="viewChildGender"></span></p>
                                <p><strong>Guardian Name:</strong> <span id="viewGuardianName"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Guardian Phone:</strong> <span id="viewGuardianPhone"></span></p>
                                <p><strong>Guardian Email:</strong> <span id="viewGuardianEmail"></span></p>
                                <p><strong>Address:</strong> <span id="viewAddress"></span></p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p><strong>Additional Notes:</strong></p>
                            <p id="viewNotes" class="text-muted"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Child Modal -->
<div class="modal fade" id="editChildModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Child</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editChildForm" enctype="multipart/form-data">
                    <input type="hidden" name="child_id">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="profile-picture-upload">
                                <div class="upload-preview">
                                    <img src="assets/images/default-child.png" id="editProfilePreview" alt="Profile Preview">
                                </div>
                                <div class="upload-controls">
                                    <label for="edit_profile_picture" class="btn btn-outline-primary">
                                        <i class="fas fa-camera"></i> Change Photo
                                    </label>
                                    <input type="file" id="edit_profile_picture" name="profile_picture" accept="image/*" class="d-none">
                                </div>
                            </div>
                    </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Child's Name *</label>
                                    <input type="text" class="form-control" name="name" required>
                    </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Age *</label>
                                    <input type="number" class="form-control" name="age" min="0" max="18" required>
                    </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Gender *</label>
                                    <select class="form-select" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                        </select>
                    </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Guardian's Name *</label>
                                    <input type="text" class="form-control" name="guardian_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Guardian's Phone *</label>
                                    <input type="tel" class="form-control" name="guardian_phone" required>
                    </div>
                    </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                        <label class="form-label">Guardian's Email</label>
                                    <input type="email" class="form-control" name="guardian_email">
                    </div>
                                <div class="col-md-6 mb-3">
                        <label class="form-label">Address</label>
                                    <input type="text" class="form-control" name="address">
                                </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Additional Notes</label>
                                <textarea class="form-control" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateChild()">Save Changes</button>
            </div>
        </div>
    </div>
</div> 