<?php
session_start();
require_once "../../config/database.php";

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized access"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = ["success" => false, "message" => ""];
    
    try {
        if (empty($_POST['volunteer_id'])) {
            throw new Exception("Volunteer ID is required.");
        }

        $volunteer_id = $_POST['volunteer_id'];

        // Get volunteer's profile picture before deletion
        $pic_sql = "SELECT profile_picture FROM volunteers WHERE id = ?";
        $pic_stmt = mysqli_prepare($conn, $pic_sql);
        mysqli_stmt_bind_param($pic_stmt, "i", $volunteer_id);
        mysqli_stmt_execute($pic_stmt);
        $pic_result = mysqli_stmt_get_result($pic_stmt);
        $volunteer_data = mysqli_fetch_assoc($pic_result);

        // Delete the volunteer record
        $sql = "DELETE FROM volunteers WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $volunteer_id);

        if (mysqli_stmt_execute($stmt)) {
            // Delete profile picture if exists
            if ($volunteer_data && $volunteer_data['profile_picture'] && file_exists("../../" . $volunteer_data['profile_picture'])) {
                unlink("../../" . $volunteer_data['profile_picture']);
            }

            $response["success"] = true;
            $response["message"] = "Volunteer deleted successfully.";
        } else {
            throw new Exception("Error deleting volunteer: " . mysqli_error($conn));
        }

    } catch (Exception $e) {
        $response["message"] = $e->getMessage();
    }

    echo json_encode($response);
    exit;
}
?> 