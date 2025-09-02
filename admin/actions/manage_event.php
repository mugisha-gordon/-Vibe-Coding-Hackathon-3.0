<?php
session_start();
require_once "../config/database.php";

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    header("location: ../login.php");
    exit;
}

// Handle event deletion
if(isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    
    // Get event image before deletion
    $sql = "SELECT image_url FROM events WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $event_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if($row = mysqli_fetch_assoc($result)) {
            // Delete image file if exists
            if($row['image_url'] && file_exists("../" . $row['image_url'])) {
                unlink("../" . $row['image_url']);
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    // Delete event
    $sql = "DELETE FROM events WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $event_id);
        if(mysqli_stmt_execute($stmt)) {
            // Log the activity
            $log_sql = "INSERT INTO activity_log (user_id, action, details) VALUES (?, 'delete_event', ?)";
            $log_stmt = mysqli_prepare($conn, $log_sql);
            $details = "Deleted event ID: " . $event_id;
            mysqli_stmt_bind_param($log_stmt, "is", $_SESSION["id"], $details);
            mysqli_stmt_execute($log_stmt);
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete event']);
        }
        mysqli_stmt_close($stmt);
    }
    exit;
}

// Handle event update
if(isset($_POST['action']) && $_POST['action'] == 'update' && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = trim($_POST['event_date']);
    $end_date = !empty($_POST['end_date']) ? trim($_POST['end_date']) : null;
    $location = trim($_POST['location']);
    $event_type = trim($_POST['event_type']);
    $status = trim($_POST['status']);
    
    // Handle image upload if new image is provided
    $image_url = null;
    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $allowed = ["jpg" => "image/jpeg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png"];
        $filename = $_FILES["image"]["name"];
        $filetype = $_FILES["image"]["type"];
        $filesize = $_FILES["image"]["size"];
        
        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) {
            die("Error: Please select a valid file format.");
        }
        
        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if($filesize > $maxsize) {
            die("Error: File size is larger than the allowed limit.");
        }
        
        // Verify MIME type of the file
        if(in_array($filetype, $allowed)) {
            // Create unique filename
            $new_filename = uniqid() . "." . $ext;
            $upload_path = "../uploads/events/" . $new_filename;
            
            // Create directory if it doesn't exist
            if (!file_exists("../uploads/events")) {
                mkdir("../uploads/events", 0777, true);
            }
            
            // Delete old image if exists
            $old_image_sql = "SELECT image_url FROM events WHERE id = ?";
            if($old_stmt = mysqli_prepare($conn, $old_image_sql)) {
                mysqli_stmt_bind_param($old_stmt, "i", $event_id);
                mysqli_stmt_execute($old_stmt);
                $old_result = mysqli_stmt_get_result($old_stmt);
                if($old_row = mysqli_fetch_assoc($old_result)) {
                    if($old_row['image_url'] && file_exists("../" . $old_row['image_url'])) {
                        unlink("../" . $old_row['image_url']);
                    }
                }
                mysqli_stmt_close($old_stmt);
            }
            
            // Move uploaded file
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $upload_path)) {
                $image_url = "uploads/events/" . $new_filename;
            }
        }
    }
    
    // Prepare update statement
    $sql = "UPDATE events SET 
            title = ?, 
            description = ?, 
            event_date = ?, 
            end_date = ?, 
            location = ?, 
            event_type = ?, 
            status = ?";
    
    // Add image_url to update if new image was uploaded
    if($image_url) {
        $sql .= ", image_url = ?";
    }
    
    $sql .= " WHERE id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        if($image_url) {
            mysqli_stmt_bind_param($stmt, "ssssssssi", 
                $title, 
                $description, 
                $event_date, 
                $end_date, 
                $location, 
                $event_type,
                $status,
                $image_url,
                $event_id
            );
        } else {
            mysqli_stmt_bind_param($stmt, "sssssssi", 
                $title, 
                $description, 
                $event_date, 
                $end_date, 
                $location, 
                $event_type,
                $status,
                $event_id
            );
        }
        
        if(mysqli_stmt_execute($stmt)) {
            // Log the activity
            $log_sql = "INSERT INTO activity_log (user_id, action, details) VALUES (?, 'update_event', ?)";
            $log_stmt = mysqli_prepare($conn, $log_sql);
            $details = "Updated event: " . $title;
            mysqli_stmt_bind_param($log_stmt, "is", $_SESSION["id"], $details);
            mysqli_stmt_execute($log_stmt);
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update event']);
        }
        
        mysqli_stmt_close($stmt);
    }
    exit;
}

// Handle event data retrieval for editing
if(isset($_GET['action']) && $_GET['action'] == 'get' && isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    
    $sql = "SELECT * FROM events WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $event_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if($event = mysqli_fetch_assoc($result)) {
            echo json_encode($event);
        } else {
            echo json_encode(['error' => 'Event not found']);
        }
        
        mysqli_stmt_close($stmt);
    }
    exit;
}

mysqli_close($conn);
?> 