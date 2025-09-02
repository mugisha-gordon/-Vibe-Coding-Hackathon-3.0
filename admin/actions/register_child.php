<?php
session_start();
require_once "../config/database.php";
require_once "../includes/FileUploader.php";

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    header("location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $age = (int)$_POST["age"];
    $guardian_name = trim($_POST["guardian_name"]);
    $guardian_contact = trim($_POST["guardian_contact"]);
    $notes = trim($_POST["notes"]);
    $profile_picture = null;

    // Handle profile picture upload
    if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
        $uploader = new FileUploader();
        $profile_picture = $uploader->upload($_FILES["profile_picture"]);
        
        if ($profile_picture === false) {
            $_SESSION["error"] = "Error uploading profile picture: " . $uploader->getError();
            header("location: ../dashboard.php");
            exit;
        }
    }

    // Prepare an insert statement
    $sql = "INSERT INTO children (name, age, guardian_name, guardian_contact, notes, profile_picture) VALUES (?, ?, ?, ?, ?, ?)";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sissss", $name, $age, $guardian_name, $guardian_contact, $notes, $profile_picture);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION["success"] = "Child registered successfully.";
        } else {
            $_SESSION["error"] = "Error registering child: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }

    // Clear cache
    require_once "../includes/Cache.php";
    $cache = new Cache();
    $cache->delete('children_count');
    
    header("location: ../dashboard.php");
    exit;
} 