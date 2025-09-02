// Staff Management Functions
function viewStaffDetails(staffId) {
    // Show loading state
    $('#staffDetails').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>');
    $('#viewStaffModal').modal('show');

    // Fetch staff details
    $.ajax({
        url: 'actions/get_staff_details.php',
        type: 'POST',
        data: { staff_id: staffId },
        success: function(response) {
            if (response.success) {
                const staff = response.data;
                const profilePic = staff.profile_picture ? 
                    '../uploads/profiles/' + staff.profile_picture : 
                    '../assets/images/default-avatar.png';
                
                let html = `
                    <div class="text-center mb-4">
                        <img src="${profilePic}" class="rounded-circle mb-3" width="100" height="100" alt="Profile Picture">
                        <h4>${staff.username}</h4>
                        <span class="badge bg-${staff.role === 'board' ? 'info' : 'primary'}">${staff.role}</span>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Email:</strong> ${staff.email}</p>
                            <p><strong>Phone:</strong> ${staff.phone || 'Not provided'}</p>
                            <p><strong>Status:</strong> <span class="badge bg-${staff.status === 'active' ? 'success' : 'danger'}">${staff.status}</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Last Login:</strong> ${staff.last_login ? new Date(staff.last_login).toLocaleString() : 'Never'}</p>
                            <p><strong>Created:</strong> ${new Date(staff.created_at).toLocaleDateString()}</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <p><strong>Address:</strong></p>
                        <p>${staff.address || 'Not provided'}</p>
                    </div>
                    <div class="mt-3">
                        <p><strong>Notes:</strong></p>
                        <p>${staff.notes || 'No notes available'}</p>
                    </div>
                `;
                $('#staffDetails').html(html);
            } else {
                showAlert('error', 'Failed to load staff details');
            }
        },
        error: function() {
            showAlert('error', 'An error occurred while loading staff details');
        }
    });
}

function editStaff(staffId) {
    // Show loading state
    $('#editStaffForm').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>');
    $('#editStaffModal').modal('show');

    // Fetch staff details for editing
    $.ajax({
        url: 'actions/get_staff_details.php',
        type: 'POST',
        data: { staff_id: staffId },
        success: function(response) {
            if (response.success) {
                const staff = response.data;
                const profilePic = staff.profile_picture ? 
                    '../uploads/profiles/' + staff.profile_picture : 
                    '../assets/images/default-avatar.png';
                
                // Populate form fields
                $('#edit_staff_id').val(staff.id);
                $('#edit_username').val(staff.username);
                $('#edit_email').val(staff.email);
                $('#edit_role').val(staff.role);
                $('#edit_phone').val(staff.phone);
                $('#edit_address').val(staff.address);
                $('#edit_notes').val(staff.notes);
                
                // Show current profile picture
                $('#current_profile_pic').html(`
                    <img src="${profilePic}" class="rounded-circle" width="50" height="50" alt="Current Profile Picture">
                    <small class="d-block text-muted mt-1">Current profile picture</small>
                `);
            } else {
                showAlert('error', 'Failed to load staff details');
                $('#editStaffModal').modal('hide');
            }
        },
        error: function() {
            showAlert('error', 'An error occurred while loading staff details');
            $('#editStaffModal').modal('hide');
        }
    });
}

function updateStaffStatus(staffId, status) {
    if (!confirm(`Are you sure you want to ${status === 'active' ? 'activate' : 'deactivate'} this staff member?`)) {
        return;
    }

    $.ajax({
        url: 'actions/update_staff_status.php',
        type: 'POST',
        data: {
            staff_id: staffId,
            status: status
        },
        success: function(response) {
            if (response.success) {
                // Update UI
                const statusBadge = $(`#staff-${staffId} .staff-status`);
                statusBadge.removeClass('bg-success bg-danger status-active status-inactive')
                    .addClass(`bg-${status === 'active' ? 'success' : 'danger'} status-${status}`)
                    .text(status);
                
                // Update action buttons
                const activateBtn = $(`#staff-${staffId} .btn-success`);
                const deactivateBtn = $(`#staff-${staffId} .btn-danger`);
                
                if (status === 'active') {
                    activateBtn.prop('disabled', true);
                    deactivateBtn.prop('disabled', false);
                } else {
                    activateBtn.prop('disabled', false);
                    deactivateBtn.prop('disabled', true);
                }
                
                showAlert('success', `Staff member ${status === 'active' ? 'activated' : 'deactivated'} successfully`);
            } else {
                showAlert('error', response.message || 'Failed to update staff status');
            }
        },
        error: function() {
            showAlert('error', 'An error occurred while updating staff status');
        }
    });
}

// Form Submission Handlers
$('#addStaffForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                showAlert('success', 'Staff member added successfully');
                $('#addStaffModal').modal('hide');
                location.reload(); // Reload to show new staff member
            } else {
                showAlert('error', response.message || 'Failed to add staff member');
            }
        },
        error: function() {
            showAlert('error', 'An error occurred while adding staff member');
        }
    });
});

$('#editStaffForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                showAlert('success', 'Staff member updated successfully');
                $('#editStaffModal').modal('hide');
                location.reload(); // Reload to show updated staff member
            } else {
                showAlert('error', response.message || 'Failed to update staff member');
            }
        },
        error: function() {
            showAlert('error', 'An error occurred while updating staff member');
        }
    });
});

// Export Staff Data
function exportStaff() {
    window.location.href = 'actions/export_staff.php';
}

// Initialize tooltips
$(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
}); 