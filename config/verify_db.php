<?php
require_once "database.php";

// Function to check if a table exists
function tableExists($conn, $tableName) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$tableName'");
    return mysqli_num_rows($result) > 0;
}

// Function to check if a column exists in a table
function columnExists($conn, $tableName, $columnName) {
    $result = mysqli_query($conn, "SHOW COLUMNS FROM $tableName LIKE '$columnName'");
    return mysqli_num_rows($result) > 0;
}

// Verify and create children table if it doesn't exist
if (!tableExists($conn, 'children')) {
    $sql = "CREATE TABLE children (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        age INT NOT NULL,
        guardian_name VARCHAR(255) NOT NULL,
        guardian_contact VARCHAR(50) NOT NULL,
        notes TEXT,
        profile_picture VARCHAR(255),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($conn, $sql)) {
        die("Error creating children table: " . mysqli_error($conn));
    }
    echo "Children table created successfully.<br>";
}

// Verify and create donations table if it doesn't exist
if (!tableExists($conn, 'donations')) {
    $sql = "CREATE TABLE donations (
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
    )";
    
    if (!mysqli_query($conn, $sql)) {
        die("Error creating donations table: " . mysqli_error($conn));
    }
    echo "Donations table created successfully.<br>";
}

// Verify and add missing columns to children table
$requiredColumns = [
    'name' => 'VARCHAR(255) NOT NULL',
    'age' => 'INT NOT NULL',
    'guardian_name' => 'VARCHAR(255) NOT NULL',
    'guardian_contact' => 'VARCHAR(50) NOT NULL',
    'notes' => 'TEXT',
    'profile_picture' => 'VARCHAR(255)',
    'created_at' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
    'updated_at' => 'DATETIME ON UPDATE CURRENT_TIMESTAMP'
];

foreach ($requiredColumns as $column => $definition) {
    if (!columnExists($conn, 'children', $column)) {
        $sql = "ALTER TABLE children ADD COLUMN $column $definition";
        if (!mysqli_query($conn, $sql)) {
            die("Error adding column $column to children table: " . mysqli_error($conn));
        }
        echo "Added column $column to children table.<br>";
    }
}

// Verify and add missing columns to donations table
$requiredDonationColumns = [
    'donor_name' => 'VARCHAR(255) NOT NULL',
    'donor_email' => 'VARCHAR(255) NOT NULL',
    'amount' => 'DECIMAL(10,2) NOT NULL',
    'status' => "ENUM('pending', 'completed', 'failed') DEFAULT 'pending'",
    'payment_method' => 'VARCHAR(50)',
    'transaction_id' => 'VARCHAR(255)',
    'payment_reference' => 'VARCHAR(255)',
    'message' => 'TEXT',
    'child_id' => 'INT',
    'created_at' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
    'updated_at' => 'DATETIME ON UPDATE CURRENT_TIMESTAMP'
];

foreach ($requiredDonationColumns as $column => $definition) {
    if (!columnExists($conn, 'donations', $column)) {
        $sql = "ALTER TABLE donations ADD COLUMN $column $definition";
        if (!mysqli_query($conn, $sql)) {
            die("Error adding column $column to donations table: " . mysqli_error($conn));
        }
        echo "Added column $column to donations table.<br>";
    }
}

// Add foreign key if it doesn't exist
$result = mysqli_query($conn, "SHOW CREATE TABLE donations");
$row = mysqli_fetch_row($result);
if (strpos($row[1], 'FOREIGN KEY (`child_id`) REFERENCES `children`') === false) {
    $sql = "ALTER TABLE donations ADD FOREIGN KEY (child_id) REFERENCES children(id) ON DELETE SET NULL";
    if (!mysqli_query($conn, $sql)) {
        die("Error adding foreign key to donations table: " . mysqli_error($conn));
    }
    echo "Added foreign key to donations table.<br>";
}

// Add indexes for better performance
$indexes = [
    "CREATE INDEX IF NOT EXISTS idx_donation_date ON donations(created_at)",
    "CREATE INDEX IF NOT EXISTS idx_donation_status ON donations(status)",
    "CREATE INDEX IF NOT EXISTS idx_donor_email ON donations(donor_email)",
    "CREATE INDEX IF NOT EXISTS idx_child_id ON donations(child_id)"
];

foreach ($indexes as $sql) {
    if (!mysqli_query($conn, $sql)) {
        echo "Warning: Could not create index: " . mysqli_error($conn) . "<br>";
    }
}

echo "Database verification completed successfully.";
?> 