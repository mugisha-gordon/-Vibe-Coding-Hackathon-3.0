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
        if (empty($_GET['event_id'])) {
            throw new Exception("Event ID is required.");
        }

        $event_id = $_GET['event_id'];

        // Get event details
        $sql = "SELECT * FROM events WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $event_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($event = mysqli_fetch_assoc($result)) {
            // Format dates for display
            $event['event_date'] = date('Y-m-d', strtotime($event['event_date']));
            $event['created_at'] = date('Y-m-d H:i:s', strtotime($event['created_at']));
            $event['updated_at'] = $event['updated_at'] ? date('Y-m-d H:i:s', strtotime($event['updated_at'])) : null;
            
            // Add full URL for event image if exists
            if ($event['event_image']) {
                $event['event_image_url'] = "../" . $event['event_image'];
            }

            $response["success"] = true;
            $response["data"] = $event;
        } else {
            throw new Exception("Event not found.");
        }

    } catch (Exception $e) {
        $response["message"] = $e->getMessage();
    }

    echo json_encode($response);
    exit;
}
?> 