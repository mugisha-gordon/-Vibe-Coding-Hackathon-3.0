<?php
session_start();
require_once '../../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if required fields are provided
if (!isset($_POST['staff_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Staff ID and status are required']);
    exit;
}

$staff_id = mysqli_real_escape_string($conn, $_POST['staff_id']);
$status = mysqli_real_escape_string($conn, $_POST['status']);

// Validate status
if (!in_array($status, ['active', 'inactive'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// Check if staff exists and is not the current user
$check_sql = "SELECT id FROM users WHERE id = '$staff_id' AND role IN ('staff', 'board') AND id != '{$_SESSION['user_id']}'";
$check_result = mysqli_query($conn, $check_sql);

if (!$check_result || mysqli_num_rows($check_result) === 0) {
    echo json_encode(['success' => false, 'message' => 'Staff member not found or cannot update own status']);
    exit;
}

// Update staff status
$update_sql = "UPDATE users SET status = '$status' WHERE id = '$staff_id'";
$update_result = mysqli_query($conn, $update_sql);

if ($update_result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update staff status']);
} 