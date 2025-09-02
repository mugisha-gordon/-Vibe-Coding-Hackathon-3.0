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
        if (empty($_POST['event_id'])) {
            throw new Exception("Event ID is required.");
        }

        $event_id = $_POST['event_id'];

        // Get event's image before deletion
        $pic_sql = "SELECT event_image FROM events WHERE id = ?";
        $pic_stmt = mysqli_prepare($conn, $pic_sql);
        mysqli_stmt_bind_param($pic_stmt, "i", $event_id);
        mysqli_stmt_execute($pic_stmt);
        $pic_result = mysqli_stmt_get_result($pic_stmt);
        $event_data = mysqli_fetch_assoc($pic_result);

        // Delete the event record
        $sql = "DELETE FROM events WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $event_id);

        if (mysqli_stmt_execute($stmt)) {
            // Delete event image if exists
            if ($event_data && $event_data['event_image'] && file_exists("../../" . $event_data['event_image'])) {
                unlink("../../" . $event_data['event_image']);
            }

            $response["success"] = true;
            $response["message"] = "Event deleted successfully.";
        } else {
            throw new Exception("Error deleting event: " . mysqli_error($conn));
        }

    } catch (Exception $e) {
        $response["message"] = $e->getMessage();
    }

    echo json_encode($response);
    exit;
}
?> 