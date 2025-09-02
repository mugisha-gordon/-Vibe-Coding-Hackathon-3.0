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
        if (empty($_GET['staff_id'])) {
            throw new Exception("Staff ID is required.");
        }

        $staff_id = $_GET['staff_id'];

        // Get staff details
        $sql = "SELECT * FROM staff WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $staff_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($staff = mysqli_fetch_assoc($result)) {
            // Format dates for display
            $staff['created_at'] = date('Y-m-d H:i:s', strtotime($staff['created_at']));
            $staff['updated_at'] = date('Y-m-d H:i:s', strtotime($staff['updated_at']));

            // Add full URL for profile picture if exists
            if ($staff['profile_picture']) {
                $staff['profile_picture_url'] = "/" . $staff['profile_picture'];
            }

            $response["success"] = true;
            $response["data"] = $staff;
        } else {
            throw new Exception("Staff member not found.");
        }

    } catch (Exception $e) {
        $response["message"] = $e->getMessage();
    }

    echo json_encode($response);
    exit;
}
?> 