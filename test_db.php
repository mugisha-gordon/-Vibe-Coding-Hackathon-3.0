<?php
require_once "config/database.php";

echo "<h2>Database Connection Test</h2>";

// Test database connection
if($conn){
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
    exit;
}

// Test if tables exist
$tables = ['users', 'children', 'football_team', 'staff_assignments'];
foreach($tables as $table){
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if(mysqli_num_rows($result) > 0){
        echo "<p style='color: green;'>✓ Table '$table' exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Table '$table' does not exist</p>";
    }
}

// Test admin user
$result = mysqli_query($conn, "SELECT * FROM users WHERE role = 'admin'");
if(mysqli_num_rows($result) > 0){
    echo "<p style='color: green;'>✓ Admin user exists</p>";
} else {
    echo "<p style='color: red;'>✗ Admin user does not exist</p>";
}

// Display any errors
if(mysqli_error($conn)){
    echo "<p style='color: red;'>Error: " . mysqli_error($conn) . "</p>";
}
?> 