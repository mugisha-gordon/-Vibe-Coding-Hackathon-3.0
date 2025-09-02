<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

require_once "../config/database.php";

// Validate input
if(!isset($_POST['donation_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$donation_id = (int)$_POST['donation_id'];
$status = $_POST['status'];

// Validate status
$allowed_statuses = ['completed', 'failed'];
if(!in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// Update donation status
$update_query = "UPDATE donations SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
$stmt = mysqli_prepare($conn, $update_query);
mysqli_stmt_bind_param($stmt, "si", $status, $donation_id);

if(mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn); 