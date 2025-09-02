<?php
session_start();
require_once "../../config/database.php";

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

if(!isset($_GET['id'])) {
    exit(json_encode(['error' => 'Missing staff ID']));
}

$id = (int)$_GET['id'];

// Get staff details
$sql = "SELECT * FROM staff WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if($staff = mysqli_fetch_assoc($result)) {
    $status_class = getStatusBadgeClass($staff['status']);
    
    $html = "
    <div class='staff-details'>
        <h4 class='mb-3'>{$staff['name']}</h4>
        <div class='row mb-3'>
            <div class='col-md-6'>
                <p><strong>Email:</strong> {$staff['email']}</p>
                <p><strong>Phone:</strong> {$staff['phone']}</p>
                <p><strong>Position:</strong> {$staff['position']}</p>
            </div>
            <div class='col-md-6'>
                <p><strong>Department:</strong> {$staff['department']}</p>
                <p><strong>Hire Date:</strong> " . date('F j, Y', strtotime($staff['hire_date'])) . "</p>
                <p><strong>Status:</strong> <span class='status-badge bg-{$status_class}'>{$staff['status']}</span></p>
            </div>
        </div>
        <div class='row'>
            <div class='col-12'>
                <h5>Recent Activity</h5>
                <div class='table-responsive'>
                    <table class='table table-sm'>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Activity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>" . date('M j, Y', strtotime($staff['created_at'])) . "</td>
                                <td>Staff record created</td>
                            </tr>
                            <tr>
                                <td>" . date('M j, Y', strtotime($staff['updated_at'])) . "</td>
                                <td>Last record update</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>";
    
    echo json_encode(['success' => true, 'html' => $html]);
} else {
    echo json_encode(['error' => 'Staff not found']);
}

// Helper function to get status badge class
function getStatusBadgeClass($status) {
    switch($status) {
        case 'active':
            return 'success';
        case 'inactive':
            return 'danger';
        case 'on_leave':
            return 'warning';
        default:
            return 'secondary';
    }
}
?> 