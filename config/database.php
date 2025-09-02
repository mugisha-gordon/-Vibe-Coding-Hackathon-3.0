<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'org_db');

// Attempt to connect to MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (!mysqli_query($conn, $sql)) {
    die("Error creating database: " . mysqli_error($conn));
}

// Select the database
if (!mysqli_select_db($conn, DB_NAME)) {
    die("Error selecting database: " . mysqli_error($conn));
}

// Set charset to ensure proper encoding
mysqli_set_charset($conn, "utf8mb4");

// Drop existing volunteer_requests table if it exists (to ensure clean structure)
$drop_table = "DROP TABLE IF EXISTS volunteer_requests";
mysqli_query($conn, $drop_table);

// Create necessary tables if they don't exist
$tables = [
    "CREATE TABLE IF NOT EXISTS volunteer_requests (
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
    )",
    
    "CREATE TABLE IF NOT EXISTS children (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        date_of_birth DATE,
        gender ENUM('Male', 'Female', 'Other'),
        address TEXT,
        guardian_name VARCHAR(100),
        guardian_phone VARCHAR(20),
        guardian_email VARCHAR(100),
        medical_info TEXT,
        notes TEXT,
        profile_picture VARCHAR(255),
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) UNIQUE,
        role ENUM('admin', 'staff') NOT NULL,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )"
];

// Execute each table creation query
foreach ($tables as $sql) {
    if (!mysqli_query($conn, $sql)) {
        die("Error creating table: " . mysqli_error($conn));
    }
}

// Insert default admin user if not exists
$check_admin = "SELECT id FROM users WHERE username = 'admin' LIMIT 1";
$result = mysqli_query($conn, $check_admin);

if (mysqli_num_rows($result) == 0) {
    $admin_password = password_hash("Admin@123", PASSWORD_DEFAULT);
    $insert_admin = "INSERT INTO users (username, password, email, role) 
                    VALUES ('admin', '$admin_password', 'admin@example.com', 'admin')";
    mysqli_query($conn, $insert_admin);
}
?> 
?> 