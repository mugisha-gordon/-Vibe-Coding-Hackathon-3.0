<?php
require_once "database.php";

// Create tables if they don't exist
$tables = [
    "CREATE TABLE IF NOT EXISTS children (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        age INT NOT NULL,
        guardian_name VARCHAR(255) NOT NULL,
        guardian_contact VARCHAR(50) NOT NULL,
        notes TEXT,
        profile_picture VARCHAR(255),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'staff', 'board') NOT NULL,
        position VARCHAR(255),
        phone VARCHAR(50),
        profile_picture VARCHAR(255),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS volunteers (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50),
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        profile_picture VARCHAR(255),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS donations (
        id INT PRIMARY KEY AUTO_INCREMENT,
        donor_name VARCHAR(255) NOT NULL,
        donor_email VARCHAR(255) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
        payment_method VARCHAR(50),
        transaction_id VARCHAR(255),
        payment_reference VARCHAR(255),
        message TEXT,
        child_id INT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (child_id) REFERENCES children(id) ON DELETE SET NULL
    )",
    
    "CREATE TABLE IF NOT EXISTS feedback (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )"
];

// Execute each table creation query
foreach ($tables as $sql) {
    if (!mysqli_query($conn, $sql)) {
        die("Error creating table: " . mysqli_error($conn));
    }
}

// Add indexes for better performance
$indexes = [
    "CREATE INDEX IF NOT EXISTS idx_donation_date ON donations(created_at)",
    "CREATE INDEX IF NOT EXISTS idx_donation_status ON donations(status)",
    "CREATE INDEX IF NOT EXISTS idx_donor_email ON donations(donor_email)",
    "CREATE INDEX IF NOT EXISTS idx_volunteer_status ON volunteers(status)",
    "CREATE INDEX IF NOT EXISTS idx_user_role ON users(role)"
];

foreach ($indexes as $sql) {
    mysqli_query($conn, $sql);
}

echo "Database initialization completed successfully.";
?> 