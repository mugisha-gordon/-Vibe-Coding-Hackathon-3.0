<?php
require_once '../config/database.php';

// Check if user is logged in and is admin
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin") {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$id = isset($data['id']) ? intval($data['id']) : 0;
$status = isset($data['status']) ? $data['status'] : '';

// Validate input
if ($id <= 0 || !in_array($status, ['active', 'pending', 'rejected'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Update volunteer status
$sql = "UPDATE volunteers SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $status, $id);

if (mysqli_stmt_execute($stmt)) {
    // If status is active, create a notification
    if ($status === 'active') {
        $volunteer_sql = "SELECT name, email FROM volunteers WHERE id = ?";
        $volunteer_stmt = mysqli_prepare($conn, $volunteer_sql);
        mysqli_stmt_bind_param($volunteer_stmt, "i", $id);
        mysqli_stmt_execute($volunteer_stmt);
        $volunteer = mysqli_fetch_assoc(mysqli_stmt_get_result($volunteer_stmt));

        if ($volunteer) {
            $notification_sql = "INSERT INTO notifications (user_id, type, message, created_at) 
                               VALUES (?, 'volunteer_approved', ?, CURRENT_TIMESTAMP)";
            $notification_stmt = mysqli_prepare($conn, $notification_sql);
            $message = "Your volunteer application has been approved! Welcome to our team.";
            mysqli_stmt_bind_param($notification_stmt, "is", $id, $message);
            mysqli_stmt_execute($notification_stmt);

            // Send email notification
            $to = $volunteer['email'];
            $subject = "Volunteer Application Approved";
            $message = "Dear " . $volunteer['name'] . ",\n\n";
            $message .= "Your volunteer application has been approved. Welcome to our team!\n\n";
            $message .= "Best regards,\nThe Organization Team";
            $headers = "From: noreply@organization.com";

            mail($to, $subject, $message, $headers);
        }
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating status']);
} 