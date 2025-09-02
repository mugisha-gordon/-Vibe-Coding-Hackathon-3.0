<?php
require_once "../../config/database.php";

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS bumbobi_db";
if (mysqli_query($conn, $sql)) {
    echo "Database created successfully or already exists<br>";
} else {
    echo "Error creating database: " . mysqli_error($conn) . "<br>";
}

// Select the database
mysqli_select_db($conn, "bumbobi_db");

// Create volunteer_requests table
$sql = "CREATE TABLE IF NOT EXISTS volunteer_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    skills TEXT NOT NULL,
    availability TEXT NOT NULL,
    areas_of_interest TEXT NOT NULL,
    previous_experience TEXT,
    motivation TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "Volunteer requests table created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?> 