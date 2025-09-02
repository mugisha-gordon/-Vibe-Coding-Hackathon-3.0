<?php
session_start();

// Check if user is logged in and has admin privileges
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    header("location: ../login.php");
    exit;
}

// Include database connection
require_once "../config/database.php";

// Process form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validate and sanitize input
    $name = trim(mysqli_real_escape_string($conn, $_POST["name"]));
    $email = trim(mysqli_real_escape_string($conn, $_POST["email"]));
    $position = trim(mysqli_real_escape_string($conn, $_POST["position"]));
    $phone = trim(mysqli_real_escape_string($conn, $_POST["phone"]));
    $term_start = trim(mysqli_real_escape_string($conn, $_POST["term_start"]));
    $term_end = trim(mysqli_real_escape_string($conn, $_POST["term_end"]));
    $bio = trim(mysqli_real_escape_string($conn, $_POST["bio"]));
    
    // Simple validation
    if(empty($name) || empty($email) || empty($position) || empty($term_start)){
        $_SESSION['error'] = "Please fill all required fields.";
        header("location: ../dashboard.php");
        exit;
    }

    // Handle profile photo upload
    $profile_photo = "default-profile.jpg"; // Default photo
    
    if(isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        // Check file type and size
        if(in_array($_FILES['profile_photo']['type'], $allowed_types) && $_FILES['profile_photo']['size'] <= $max_size) {
            
            // Create upload directory if it doesn't exist
            $upload_dir = "../uploads/board_members/";
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generate a unique filename
            $file_extension = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
            $unique_filename = uniqid('board_') . '_' . time() . '.' . $file_extension;
            $target_file = $upload_dir . $unique_filename;
            
            // Attempt to upload the file
            if(move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
                $profile_photo = "uploads/board_members/" . $unique_filename;
            } else {
                $_SESSION['warning'] = "Failed to upload profile photo. Member will be registered with default photo.";
            }
        } else {
            $_SESSION['warning'] = "Invalid file type or size. Only JPG, PNG, GIF, WEBP files under 5MB are allowed.";
        }
    }
    
    // Insert data into the database
    $sql = "INSERT INTO board_members (name, email, position, phone, term_start, term_end, bio, profile_photo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "ssssssss", $name, $email, $position, $phone, $term_start, $term_end, $bio, $profile_photo);
        
        if(mysqli_stmt_execute($stmt)){
            // Record activity
            $username = $_SESSION['username'];
            mysqli_query($conn, "INSERT INTO activity_log (username, activity) VALUES ('$username', 'Registered a new board member: $name')");
            
            $_SESSION['success'] = "Board member registered successfully!";
            header("location: ../dashboard.php#staff");
            exit;
        } else {
            $_SESSION['error'] = "Something went wrong. Please try again later.";
            header("location: ../dashboard.php");
            exit;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
}
?> 