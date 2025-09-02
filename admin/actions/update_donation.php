<?php
session_start();
require_once '../../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin") {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

// Check if it's a POST request
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Get and sanitize input
$donation_id = isset($_POST['donation_id']) ? intval($_POST['donation_id']) : 0;
$status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : '';

// Validate status
$valid_statuses = ['pending', 'completed', 'failed'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'error' => 'Invalid status']);
    exit;
}

// Update donation status
$sql = "UPDATE donations SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $status, $donation_id);

if (mysqli_stmt_execute($stmt)) {
    // If status is completed, update donation stats
    if ($status === 'completed') {
        // Get donation amount
        $amount_sql = "SELECT amount FROM donations WHERE id = ?";
        $amount_stmt = mysqli_prepare($conn, $amount_sql);
        mysqli_stmt_bind_param($amount_stmt, "i", $donation_id);
        mysqli_stmt_execute($amount_stmt);
        $result = mysqli_stmt_get_result($amount_stmt);
        $donation = mysqli_fetch_assoc($result);
        
        if ($donation) {
            // Update total donations
            $update_stats = "UPDATE donation_stats SET 
                           total_amount = total_amount + ?,
                           monthly_amount = monthly_amount + ?,
                           last_updated = CURRENT_TIMESTAMP";
            $stats_stmt = mysqli_prepare($conn, $update_stats);
            mysqli_stmt_bind_param($stats_stmt, "dd", $donation['amount'], $donation['amount']);
            mysqli_stmt_execute($stats_stmt);
        }
    }
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update donation status']);
}

mysqli_stmt_close($stmt);
?> 