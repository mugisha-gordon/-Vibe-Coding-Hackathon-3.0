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
        // Validate required fields
        $required_fields = ['title', 'description', 'event_date', 'location', 'capacity'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Please fill in all required fields.");
            }
        }

        // Handle event image upload
        $event_image = null;
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            $max_size = 5 * 1024 * 1024; // 5MB

            if (!in_array($_FILES['event_image']['type'], $allowed_types)) {
                throw new Exception("Invalid file type. Please upload a JPG or PNG image.");
        }
        
            if ($_FILES['event_image']['size'] > $max_size) {
                throw new Exception("File size too large. Maximum size is 5MB.");
            }

            $upload_dir = "../../uploads/events/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['event_image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . '.' . $file_extension;
            $target_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['event_image']['tmp_name'], $target_path)) {
                $event_image = "uploads/events/" . $file_name;
            } else {
                throw new Exception("Failed to upload event image.");
        }
    }
    
        // Prepare and execute the SQL query
        $sql = "INSERT INTO events (title, description, event_date, location, capacity, event_image, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'upcoming', NOW())";
    
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssis", 
            $_POST['title'],
            $_POST['description'],
            $_POST['event_date'],
            $_POST['location'],
            $_POST['capacity'],
            $event_image
        );
        
        if (mysqli_stmt_execute($stmt)) {
            $response["success"] = true;
            $response["message"] = "Event added successfully.";
            $response["event_id"] = mysqli_insert_id($conn);
        } else {
            throw new Exception("Error adding event: " . mysqli_error($conn));
        }
        
    } catch (Exception $e) {
        $response["message"] = $e->getMessage();
    }
    
    echo json_encode($response);
    exit;
}
?> 