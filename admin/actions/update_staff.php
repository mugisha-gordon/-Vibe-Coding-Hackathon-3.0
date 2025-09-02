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
        if (empty($_POST['staff_id'])) {
            throw new Exception("Staff ID is required.");
        }

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

        // Get current staff data
        $staff_id = $_POST['staff_id'];
        $current_sql = "SELECT profile_picture FROM staff WHERE id = ?";
        $current_stmt = mysqli_prepare($conn, $current_sql);
        mysqli_stmt_bind_param($current_stmt, "i", $staff_id);
        mysqli_stmt_execute($current_stmt);
        $current_result = mysqli_stmt_get_result($current_stmt);
        $current_data = mysqli_fetch_assoc($current_result);

// Handle profile picture upload
        $profile_picture = $current_data['profile_picture'];
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

            // Delete old profile picture if exists
            if ($current_data['profile_picture'] && file_exists("../../" . $current_data['profile_picture'])) {
                unlink("../../" . $current_data['profile_picture']);
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

        // Update staff record
        $sql = "UPDATE staff SET 
                name = ?, 
                email = ?, 
                phone = ?, 
                position = ?, 
                department = ?, 
                role = ?, 
                bio = ?,
                profile_picture = ?,
                status = ?,
                updated_at = NOW()
                WHERE id = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssssi", 
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['position'],
            $_POST['department'],
            $_POST['role'],
            $_POST['bio'],
            $profile_picture,
            $_POST['status'],
            $staff_id
        );

        if (mysqli_stmt_execute($stmt)) {
            $response["success"] = true;
            $response["message"] = "Staff member updated successfully.";
        } else {
            throw new Exception("Error updating staff member: " . mysqli_error($conn));
        }

    } catch (Exception $e) {
        $response["message"] = $e->getMessage();
    }

    echo json_encode($response);
    exit;
}
?> 