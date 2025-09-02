<?php
session_start();
require_once "../../config/database.php";

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

if(!isset($_GET['id'])) {
    http_response_code(400);
    exit(json_encode(['error' => 'Feedback ID is required']));
}

$id = (int)$_GET['id'];

// Get feedback details
$sql = "SELECT * FROM feedback WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if($feedback = mysqli_fetch_assoc($result)) {
    // Update status to 'read' if it's 'unread'
    if($feedback['status'] === 'unread') {
        $update_sql = "UPDATE feedback SET status = 'read' WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "i", $id);
        mysqli_stmt_execute($update_stmt);
        $feedback['status'] = 'read';
    }
    
    echo json_encode($feedback);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Feedback not found']);
}
?> 