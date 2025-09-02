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

// Delete feedback
$sql = "DELETE FROM feedback WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);

if(mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete feedback']);
}
?> 