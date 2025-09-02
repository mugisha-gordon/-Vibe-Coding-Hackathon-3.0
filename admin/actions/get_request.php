<?php
session_start();
require_once "../../config/database.php";

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

if (isset($_GET['request_id'])) {
    $request_id = mysqli_real_escape_string($conn, $_GET['request_id']);
    
    $sql = "SELECT * FROM volunteer_requests WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $request_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($request = mysqli_fetch_assoc($result)) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $request
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Request not found'
        ]);
    }
    
    mysqli_stmt_close($stmt);
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Request ID not provided'
    ]);
}

mysqli_close($conn);
?> 