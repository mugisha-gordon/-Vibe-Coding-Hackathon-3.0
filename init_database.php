<?php
require_once "config/database.php";

// Read the SQL file
$sql = file_get_contents('sql/init_database.sql');

// Execute multi query
if (mysqli_multi_query($conn, $sql)) {
    do {
        // Store first result set
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($conn));
    
    echo "Database initialized successfully!";
} else {
    echo "Error initializing database: " . mysqli_error($conn);
}

mysqli_close($conn);
?> 