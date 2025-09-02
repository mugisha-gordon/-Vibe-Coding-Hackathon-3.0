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
        $required_fields = ['name', 'email', 'phone', 'position', 'department', 'role'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Please fill in all required fields.");
            }
}

        // Validate email format
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address.");
        }

// Handle profile picture upload
        $profile_picture = null;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($_FILES['profile_picture']['type'], $allowed_types)) {
                throw new Exception("Invalid file type. Please upload a JPG or PNG image.");
    }
    
    if ($_FILES['profile_picture']['size'] > $max_size) {
                throw new Exception("File size too large. Maximum size is 5MB.");
        }
        
            $upload_dir = "../../uploads/staff/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . '.' . $file_extension;
            $target_path = $upload_dir . $file_name;
    
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_path)) {
                $profile_picture = "uploads/staff/" . $file_name;
            } else {
                throw new Exception("Failed to upload profile picture.");
        }
}

        // Prepare and execute the SQL query
        $sql = "INSERT INTO staff (name, email, phone, position, department, role, bio, profile_picture, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssss", 
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['position'],
            $_POST['department'],
            $_POST['role'],
            $_POST['bio'],
            $profile_picture
        );

        if (mysqli_stmt_execute($stmt)) {
            $response["success"] = true;
            $response["message"] = "Staff member added successfully.";
            $response["staff_id"] = mysqli_insert_id($conn);
} else {
            throw new Exception("Error adding staff member: " . mysqli_error($conn));
        }

    } catch (Exception $e) {
        $response["message"] = $e->getMessage();
    }

    echo json_encode($response);
    exit;
}
?> 