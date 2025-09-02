<?php
session_start();
require_once "../../config/database.php";

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['request_id']) && isset($_POST['status'])) {
        $request_id = mysqli_real_escape_string($conn, $_POST['request_id']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        
        // Validate status
        if (!in_array($status, ['approved', 'rejected'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Invalid status'
            ]);
            exit;
        }
        
        // Get request details for email
        $get_sql = "SELECT * FROM volunteer_requests WHERE id = ?";
        $get_stmt = mysqli_prepare($conn, $get_sql);
        mysqli_stmt_bind_param($get_stmt, "i", $request_id);
        mysqli_stmt_execute($get_stmt);
        $result = mysqli_stmt_get_result($get_stmt);
        $request = mysqli_fetch_assoc($result);
        mysqli_stmt_close($get_stmt);
        
        if ($request) {
            // Update status
            $update_sql = "UPDATE volunteer_requests SET status = ? WHERE id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "si", $status, $request_id);
            
            if (mysqli_stmt_execute($update_stmt)) {
                // Send email notification
                $to = $request['email'];
                $subject = "Volunteer Request " . ucfirst($status);
                $message = "Dear {$request['first_name']},\n\n";
                $message .= "Your volunteer request has been " . $status . ".\n\n";
                
                if ($status === 'approved') {
                    $message .= "Welcome to our team! We will contact you soon with more details about your role and responsibilities.\n\n";
                } else {
                    $message .= "Thank you for your interest in volunteering with us. We encourage you to apply again in the future.\n\n";
                }
                
                $message .= "Best regards,\nBumbobi Child Support Uganda Team";
                
                $headers = "From: admin@yourdomain.com\r\n";
                $headers .= "Reply-To: admin@yourdomain.com\r\n";
                
                mail($to, $subject, $message, $headers);
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Request status updated successfully'
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to update request status'
                ]);
            }
            
            mysqli_stmt_close($update_stmt);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Request not found'
            ]);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Missing required parameters'
        ]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method'
    ]);
}

mysqli_close($conn);
?> 