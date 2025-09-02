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
        if (empty($_POST['child_id'])) {
            throw new Exception("Child ID is required.");
        }

        $child_id = $_POST['child_id'];

        // Get child's profile picture before deletion
        $pic_sql = "SELECT profile_picture FROM children WHERE id = ?";
        $pic_stmt = mysqli_prepare($conn, $pic_sql);
        mysqli_stmt_bind_param($pic_stmt, "i", $child_id);
        mysqli_stmt_execute($pic_stmt);
        $pic_result = mysqli_stmt_get_result($pic_stmt);
        $child_data = mysqli_fetch_assoc($pic_result);

        // Delete the child record
        $sql = "DELETE FROM children WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $child_id);

        if (mysqli_stmt_execute($stmt)) {
            // Delete profile picture if exists
            if ($child_data && $child_data['profile_picture'] && file_exists("../../" . $child_data['profile_picture'])) {
                unlink("../../" . $child_data['profile_picture']);
            }

            $response["success"] = true;
            $response["message"] = "Child deleted successfully.";
        } else {
            throw new Exception("Error deleting child: " . mysqli_error($conn));
        }

    } catch (Exception $e) {
        $response["message"] = $e->getMessage();
    }

    echo json_encode($response);
    exit;
}
?> 