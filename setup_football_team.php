<?php
require_once "config/database.php";

// Read the SQL file
$sql = file_get_contents('sql/football_team.sql');

// Execute the SQL
if (mysqli_multi_query($conn, $sql)) {
    do {
        // Store first result set
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($conn));
    
    echo "Football team table created successfully!";
} else {
    echo "Error creating football team table: " . mysqli_error($conn);
}

mysqli_close($conn);
?> 