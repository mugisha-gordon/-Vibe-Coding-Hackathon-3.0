<?php
session_start();
require_once "../../config/database.php";

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

if(!isset($_POST['feedbackId']) || !isset($_POST['replyMessage'])) {
    http_response_code(400);
    exit(json_encode(['error' => 'Missing required fields']));
}

$feedback_id = (int)$_POST['feedbackId'];
$reply_message = trim($_POST['replyMessage']);

if(empty($reply_message)) {
    http_response_code(400);
    exit(json_encode(['error' => 'Reply message cannot be empty']));
}

// Get feedback details
$sql = "SELECT * FROM feedback WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $feedback_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if($feedback = mysqli_fetch_assoc($result)) {
    // Update feedback status and add reply
    $update_sql = "UPDATE feedback SET status = 'replied', reply = ?, replied_at = CURRENT_TIMESTAMP WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "si", $reply_message, $feedback_id);
    
    if(mysqli_stmt_execute($update_stmt)) {
        // Send email notification to the feedback sender
        $to = $feedback['email'];
        $subject = "Re: " . $feedback['subject'];
        $message = "Dear " . $feedback['name'] . ",\n\n";
        $message .= "Thank you for your feedback. Here is our response:\n\n";
        $message .= $reply_message . "\n\n";
        $message .= "Best regards,\nOrganization Team";
        $headers = "From: noreply@organization.com\r\n";
        
        mail($to, $subject, $message, $headers);
        
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update feedback']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Feedback not found']);
}
?> 