<?php
// Get staff data with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get total records
$total_sql = "SELECT COUNT(*) as total FROM staff";
$total_result = mysqli_query($conn, $total_sql);
$total_records = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_records / $per_page);

// Get paginated staff data
$sql = "SELECT * FROM staff ORDER BY name LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $per_page, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Staff Management</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Staff Management</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-users me-1"></i>
                Staff Members
            </div>
        <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                    <i class="fas fa-plus"></i> Add New Staff
            </button>
                <button class="btn btn-success" onclick="exportData('staff')">
                    <i class="fas fa-file-export"></i> Export
            </button>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="staffSearch" placeholder="Search staff...">
                    <button class="btn btn-outline-secondary" type="button" onclick="searchTable('staff', document.getElementById('staffSearch').value)">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="departmentFilter" onchange="filterStaff()">
                        <option value="">All Departments</option>
                        <option value="Administration">Administration</option>
                        <option value="Education">Education</option>
                        <option value="Healthcare">Healthcare</option>
                        <option value="Operations">Operations</option>
                    </select>
            </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter" onchange="filterStaff()">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="on_leave">On Leave</option>
                    </select>
        </div>
            </div>
        <div class="table-responsive">
                <table class="table table-hover" id="staffTable">
                <thead>
                    <tr>
                            <th>Profile</th>
                        <th>Name</th>
                        <th>Email</th>
                            <th>Phone</th>
                            <th>Position</th>
                            <th>Department</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                    <tbody>
                        <?php while($staff = mysqli_fetch_assoc($result)): ?>
                        <tr id="staff-<?php echo $staff['id']; ?>">
                            <td>
                                <?php if($staff['profile_picture']): ?>
                                    <img src="/<?php echo $staff['profile_picture']; ?>" alt="Profile" class="rounded-circle" width="40" height="40">
                                <?php else: ?>
                                    <img src="/assets/img/default-profile.png" alt="Default Profile" class="rounded-circle" width="40" height="40">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($staff['name']); ?></td>
                            <td><?php echo htmlspecialchars($staff['email']); ?></td>
                            <td><?php echo htmlspecialchars($staff['phone']); ?></td>
                            <td><?php echo htmlspecialchars($staff['position']); ?></td>
                            <td><?php echo htmlspecialchars($staff['department']); ?></td>
                                <td>
                                <span class="badge bg-<?php echo getStatusBadgeClass($staff['status']); ?>">
                                    <?php echo ucfirst($staff['status']); ?>
                                </span>
                                </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info btn-sm" onclick="viewStaff(<?php echo $staff['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                        </button>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="editStaff(<?php echo $staff['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                        </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteStaff(<?php echo $staff['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                        </tr>
                        <?php endwhile; ?>
                </tbody>
            </table>
            </div>
            <nav aria-label="Staff pagination" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="#" onclick="loadPage('staff', <?php echo $page-1; ?>)">Previous</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="#" onclick="loadPage('staff', <?php echo $i; ?>)"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="#" onclick="loadPage('staff', <?php echo $page+1; ?>)">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addStaffForm" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                    </div>
                        <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="phone" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Position</label>
                            <input type="text" class="form-control" name="position" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" name="department" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hire Date</label>
                            <input type="date" class="form-control" name="hire_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bio</label>
                        <textarea class="form-control" name="bio" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" name="profile_picture" accept="image/*">
                        <div id="profilePreview" class="mt-2" style="display: none;">
                            <img src="" alt="Profile Preview" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveStaff()">Save Staff</button>
            </div>
        </div>
    </div>
</div>

<!-- View Staff Modal -->
<div class="modal fade" id="viewStaffModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Staff Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <img id="viewProfilePic" src="" alt="Profile Picture" class="img-fluid rounded-circle mb-2" style="max-width: 200px;">
                        <h5 id="viewName"></h5>
                        <span id="viewPosition" class="text-muted"></span>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <p id="viewEmail"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone</label>
                            <p id="viewPhone"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Department</label>
                            <p id="viewDepartment"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Role</label>
                            <p id="viewRole"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Bio</label>
                            <p id="viewBio"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p id="viewStatus"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Joined</label>
                            <p id="viewCreatedAt"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Staff Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editStaffForm" enctype="multipart/form-data">
                    <input type="hidden" name="staff_id" id="editStaffId">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="editName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="editEmail" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="phone" id="editPhone" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Position</label>
                            <input type="text" class="form-control" name="position" id="editPosition" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" name="department" id="editDepartment" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hire Date</label>
                            <input type="date" class="form-control" name="hire_date" id="editHireDate">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bio</label>
                        <textarea class="form-control" name="bio" id="editBio" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" name="profile_picture" accept="image/*">
                        <div id="editProfilePreview" class="mt-2">
                            <img src="" alt="Current Profile" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="editStatus" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="on_leave">On Leave</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateStaff()">Update Staff</button>
            </div>
        </div>
    </div>
</div>

<!-- Staff Details Modal -->
<div class="modal fade" id="staffDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Staff Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <img src="assets/images/default-avatar.png" alt="Staff Avatar" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div id="staffDetailsContent">
                            <!-- Content will be loaded dynamically -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> 

<script>
// Profile picture preview for add form
document.querySelector('input[name="profile_picture"]').addEventListener('change', function(e) {
    const preview = document.getElementById('profilePreview');
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.querySelector('img').src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});

// Profile picture preview for edit form
document.querySelector('#editStaffForm input[name="profile_picture"]').addEventListener('change', function(e) {
    const preview = document.getElementById('editProfilePreview');
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.querySelector('img').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

function saveStaff() {
    const form = document.getElementById('addStaffForm');
    const formData = new FormData(form);

    fetch('actions/add_staff.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Error adding staff member');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding staff member');
    });
}

function viewStaff(id) {
    fetch(`actions/get_staff.php?staff_id=${id}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const staff = data.data;
            document.getElementById('viewProfilePic').src = staff.profile_picture_url || '/assets/img/default-profile.png';
            document.getElementById('viewName').textContent = staff.name;
            document.getElementById('viewPosition').textContent = staff.position;
            document.getElementById('viewEmail').textContent = staff.email;
            document.getElementById('viewPhone').textContent = staff.phone;
            document.getElementById('viewDepartment').textContent = staff.department;
            document.getElementById('viewRole').textContent = staff.role.charAt(0).toUpperCase() + staff.role.slice(1);
            document.getElementById('viewBio').textContent = staff.bio || 'No bio available';
            document.getElementById('viewStatus').innerHTML = `<span class="badge bg-${staff.status === 'active' ? 'success' : 'danger'}">${staff.status.charAt(0).toUpperCase() + staff.status.slice(1)}</span>`;
            document.getElementById('viewCreatedAt').textContent = staff.created_at;

            new bootstrap.Modal(document.getElementById('viewStaffModal')).show();
        } else {
            alert(data.message || 'Error loading staff details');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading staff details');
    });
}

function editStaff(id) {
    fetch(`actions/get_staff.php?staff_id=${id}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const staff = data.data;
            document.getElementById('editStaffId').value = staff.id;
            document.getElementById('editName').value = staff.name;
            document.getElementById('editEmail').value = staff.email;
            document.getElementById('editPhone').value = staff.phone;
            document.getElementById('editPosition').value = staff.position;
            document.getElementById('editDepartment').value = staff.department;
            document.getElementById('editHireDate').value = staff.hire_date;
            document.getElementById('editBio').value = staff.bio || '';
            document.getElementById('editStatus').value = staff.status;
            
            const preview = document.getElementById('editProfilePreview');
            preview.querySelector('img').src = staff.profile_picture_url || '/assets/img/default-profile.png';

            new bootstrap.Modal(document.getElementById('editStaffModal')).show();
        } else {
            alert(data.message || 'Error loading staff details');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading staff details');
    });
}

function updateStaff() {
    const form = document.getElementById('editStaffForm');
    const formData = new FormData(form);

    fetch('actions/update_staff.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Error updating staff member');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating staff member');
    });
}

function deleteStaff(id) {
    if (confirm('Are you sure you want to delete this staff member?')) {
        const formData = new FormData();
        formData.append('staff_id', id);

        fetch('actions/delete_staff.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message || 'Error deleting staff member');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting staff member');
        });
    }
}

// Function to filter staff
function filterStaff() {
    const department = document.getElementById('departmentFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    fetch(`actions/get_table_data.php?type=staff&department=${department}&status=${status}`)
        .then(response => response.json())
        .then(data => {
            document.querySelector('#staffTable tbody').innerHTML = data.html;
            updatePagination(data.pagination);
        });
}

// Function to view staff details
function viewStaffDetails(id) {
    fetch(`actions/get_staff_details.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('staffDetailsContent').innerHTML = data.html;
            new bootstrap.Modal(document.getElementById('staffDetailsModal')).show();
        });
}
</script> 