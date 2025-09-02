<?php
require_once "config/database.php";

// Get the webhook payload
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_VERIF_HASH'] ?? '';

// Verify webhook signature (you should implement proper signature verification)
// For now, we'll process the webhook without verification
$event = json_decode($payload, true);

if ($event && isset($event['event']) && $event['event'] === 'charge.completed') {
    $tx_ref = $event['data']['tx_ref'];
    $status = $event['data']['status'];
    
    // Update donation status
    $update_sql = "UPDATE donations SET 
                   status = ?,
                   updated_at = NOW()
                   WHERE payment_reference = ?";
    
    $update_stmt = mysqli_prepare($conn, $update_sql);
    $db_status = ($status === 'successful') ? 'completed' : 'failed';
    mysqli_stmt_bind_param($update_stmt, "ss", $db_status, $tx_ref);
    
    if (mysqli_stmt_execute($update_stmt)) {
        // Get donation details
        $select_sql = "SELECT * FROM donations WHERE payment_reference = ?";
        $select_stmt = mysqli_prepare($conn, $select_sql);
        mysqli_stmt_bind_param($select_stmt, "s", $tx_ref);
        mysqli_stmt_execute($select_stmt);
        $result = mysqli_stmt_get_result($select_stmt);
        $donation = mysqli_fetch_assoc($result);
        
        if ($donation && $db_status === 'completed') {
            // Send confirmation email to donor
            $to = $donation['donor_email'];
            $subject = "Thank you for your donation!";
            $message = "Dear " . $donation['donor_name'] . ",\n\n";
            $message .= "Thank you for your generous donation of $" . number_format($donation['amount'], 2) . ".\n";
            $message .= "Your support helps us make a difference in the lives of children in Bumbobi.\n\n";
            $message .= "Best regards,\nBumbobi Child Support Uganda";
            
            $headers = "From: noreply@bumbobi.org";
            
            mail($to, $subject, $message, $headers);
        }
        
        http_response_code(200);
        echo json_encode(['status' => 'success']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to update donation status']);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid webhook payload']);
}
?> 