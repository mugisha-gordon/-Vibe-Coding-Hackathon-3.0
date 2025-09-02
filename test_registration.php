<?php
session_start();
require_once "config/database.php";
require_once "includes/FileUploader.php";

// Test child registration
function testChildRegistration($conn) {
    $test_data = [
        'name' => 'Test Child',
        'age' => 10,
        'guardian_name' => 'Test Guardian',
        'guardian_contact' => '1234567890',
        'notes' => 'Test notes'
    ];
    
    // Create test image
    $test_image = imagecreatetruecolor(100, 100);
    $bg = imagecolorallocate($test_image, 255, 255, 255);
    imagefill($test_image, 0, 0, $bg);
    $test_image_path = 'uploads/profiles/test_image.jpg';
    imagejpeg($test_image, $test_image_path);
    imagedestroy($test_image);
    
    // Simulate file upload
    $_FILES['profile_picture'] = [
        'name' => 'test_image.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => $test_image_path,
        'error' => 0,
        'size' => filesize($test_image_path)
    ];
    
    // Test FileUploader
    $uploader = new FileUploader();
    $profile_picture = $uploader->upload($_FILES['profile_picture']);
    
    if ($profile_picture) {
        echo "Profile picture upload successful.<br>";
        
        // Test database insertion
        $sql = "INSERT INTO children (name, age, guardian_name, guardian_contact, notes, profile_picture) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sissss", 
                $test_data['name'],
                $test_data['age'],
                $test_data['guardian_name'],
                $test_data['guardian_contact'],
                $test_data['notes'],
                $profile_picture
            );
            
            if (mysqli_stmt_execute($stmt)) {
                echo "Child registration successful.<br>";
            } else {
                echo "Child registration failed: " . mysqli_error($conn) . "<br>";
            }
            
            mysqli_stmt_close($stmt);
        }
    } else {
        echo "Profile picture upload failed: " . $uploader->getError() . "<br>";
    }
    
    // Cleanup
    unlink($test_image_path);
}

// Test staff registration
function testStaffRegistration($conn) {
    $test_data = [
        'username' => 'test_staff',
        'email' => 'test@example.com',
        'password' => 'test123',
        'role' => 'staff',
        'position' => 'Test Position',
        'phone' => '1234567890'
    ];
    
    // Create test image
    $test_image = imagecreatetruecolor(100, 100);
    $bg = imagecolorallocate($test_image, 255, 255, 255);
    imagefill($test_image, 0, 0, $bg);
    $test_image_path = 'uploads/profiles/test_staff_image.jpg';
    imagejpeg($test_image, $test_image_path);
    imagedestroy($test_image);
    
    // Simulate file upload
    $_FILES['profile_picture'] = [
        'name' => 'test_staff_image.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => $test_image_path,
        'error' => 0,
        'size' => filesize($test_image_path)
    ];
    
    // Test FileUploader
    $uploader = new FileUploader();
    $profile_picture = $uploader->upload($_FILES['profile_picture']);
    
    if ($profile_picture) {
        echo "Staff profile picture upload successful.<br>";
        
        // Test database insertion
        $hashed_password = password_hash($test_data['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password, role, position, phone, profile_picture) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssssss", 
                $test_data['username'],
                $test_data['email'],
                $hashed_password,
                $test_data['role'],
                $test_data['position'],
                $test_data['phone'],
                $profile_picture
            );
            
            if (mysqli_stmt_execute($stmt)) {
                echo "Staff registration successful.<br>";
            } else {
                echo "Staff registration failed: " . mysqli_error($conn) . "<br>";
            }
            
            mysqli_stmt_close($stmt);
        }
    } else {
        echo "Staff profile picture upload failed: " . $uploader->getError() . "<br>";
    }
    
    // Cleanup
    unlink($test_image_path);
}

// Run tests
echo "<h2>Testing Registration Process</h2>";

echo "<h3>Testing Child Registration</h3>";
testChildRegistration($conn);

echo "<h3>Testing Staff Registration</h3>";
testStaffRegistration($conn);

mysqli_close($conn);
?> 