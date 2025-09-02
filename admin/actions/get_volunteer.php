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
        if (empty($_GET['volunteer_id'])) {
            throw new Exception("Volunteer ID is required.");
        }

        $volunteer_id = $_GET['volunteer_id'];

        // Get volunteer details
        $sql = "SELECT * FROM volunteers WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $volunteer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($volunteer = mysqli_fetch_assoc($result)) {
            // Format dates for display
            $volunteer['created_at'] = date('Y-m-d H:i:s', strtotime($volunteer['created_at']));
            $volunteer['updated_at'] = $volunteer['updated_at'] ? date('Y-m-d H:i:s', strtotime($volunteer['updated_at'])) : null;
            
            // Add full URL for profile picture if exists
            if ($volunteer['profile_picture']) {
                $volunteer['profile_picture_url'] = "../" . $volunteer['profile_picture'];
            }

            $response["success"] = true;
            $response["data"] = $volunteer;
        } else {
            throw new Exception("Volunteer not found.");
        }

    } catch (Exception $e) {
        $response["message"] = $e->getMessage();
    }

    echo json_encode($response);
    exit;
}
?> 