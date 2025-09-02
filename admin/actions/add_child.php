<?php
session_start();
require_once "../../config/database.php";

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

// Function to handle file upload
function handleFileUpload($file, $target_dir) {
    $target_file = $target_dir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Generate unique filename
    $new_filename = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $new_filename;
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return ['success' => false, 'error' => 'File is not an image.'];
    }
    
    // Check file size (5MB max)
    if ($file["size"] > 5000000) {
        return ['success' => false, 'error' => 'File is too large.'];
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        return ['success' => false, 'error' => 'Only JPG, JPEG & PNG files are allowed.'];
    }
    
    // Upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ['success' => true, 'filename' => $new_filename];
    } else {
        return ['success' => false, 'error' => 'Failed to upload file.'];
    }
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = ['success' => false];
    
    try {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        // Handle profile picture upload
        $profile_picture = "default-child.png"; // Default image
        if(isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
            $upload_dir = "../assets/images/children/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $upload_result = handleFileUpload($_FILES["profile_picture"], $upload_dir);
            if ($upload_result['success']) {
                $profile_picture = $upload_result['filename'];
            } else {
                throw new Exception($upload_result['error']);
            }
        }
        
        // Prepare the SQL statement
        $sql = "INSERT INTO children (
            first_name, last_name, date_of_birth, gender, 
            address, guardian_name, guardian_phone, 
            medical_info, notes, profile_picture, 
            status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "ssssssssss",
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['date_of_birth'],
            $_POST['gender'],
            $_POST['address'],
            $_POST['guardian_name'],
            $_POST['guardian_phone'],
            $_POST['medical_info'],
            $_POST['notes'],
            $profile_picture
        );
        
        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            mysqli_commit($conn);
            $response['success'] = true;
            $response['message'] = "Child added successfully";
        } else {
            throw new Exception("Error executing statement: " . mysqli_error($conn));
        }
        
        mysqli_stmt_close($stmt);
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $response['error'] = $e->getMessage();
    }
    
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// If not POST request
header('Content-Type: application/json');
echo json_encode(['success' => false, 'error' => 'Invalid request method']);
exit;
?> 