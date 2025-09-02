<?php
session_start();
require_once "../../config/database.php";

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized access"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $response = ["success" => false, "message" => "", "data" => null];
    
    try {
        if (empty($_GET['child_id'])) {
            throw new Exception("Child ID is required.");
        }

        $child_id = $_GET['child_id'];

        // Get child details
        $sql = "SELECT * FROM children WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $child_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($child = mysqli_fetch_assoc($result)) {
            // Format dates for display
            $child['created_at'] = date('Y-m-d H:i:s', strtotime($child['created_at']));
            $child['updated_at'] = $child['updated_at'] ? date('Y-m-d H:i:s', strtotime($child['updated_at'])) : null;
            
            // Add full URL for profile picture if exists
            if ($child['profile_picture']) {
                $child['profile_picture_url'] = "../" . $child['profile_picture'];
            }

            $response["success"] = true;
            $response["data"] = $child;
        } else {
            throw new Exception("Child not found.");
        }

    } catch (Exception $e) {
        $response["message"] = $e->getMessage();
    }

    echo json_encode($response);
    exit;
}
?> 