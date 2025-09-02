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
        if (empty($_POST['staff_id'])) {
            throw new Exception("Staff ID is required.");
        }

        $staff_id = $_POST['staff_id'];

        // Get staff's profile picture before deletion
        $pic_sql = "SELECT profile_picture FROM staff WHERE id = ?";
        $pic_stmt = mysqli_prepare($conn, $pic_sql);
        mysqli_stmt_bind_param($pic_stmt, "i", $staff_id);
        mysqli_stmt_execute($pic_stmt);
        $pic_result = mysqli_stmt_get_result($pic_stmt);
        $staff_data = mysqli_fetch_assoc($pic_result);

        // Delete the staff record
        $sql = "DELETE FROM staff WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $staff_id);

        if (mysqli_stmt_execute($stmt)) {
            // Delete profile picture if exists
            if ($staff_data && $staff_data['profile_picture'] && file_exists("../../" . $staff_data['profile_picture'])) {
                unlink("../../" . $staff_data['profile_picture']);
            }

            $response["success"] = true;
            $response["message"] = "Staff member deleted successfully.";
        } else {
            throw new Exception("Error deleting staff member: " . mysqli_error($conn));
        }

    } catch (Exception $e) {
        $response["message"] = $e->getMessage();
    }

    echo json_encode($response);
    exit;
}
?> 