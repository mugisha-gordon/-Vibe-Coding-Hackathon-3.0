<?php
require_once "database.php";

// Read the SQL file
$sql = file_get_contents(__DIR__ . '/database.sql');

// Split the SQL file into individual queries
$queries = array_filter(array_map('trim', explode(';', $sql)));

// Execute each query
foreach ($queries as $query) {
    if (!empty($query)) {
        if (mysqli_query($conn, $query)) {
            echo "Query executed successfully: " . substr($query, 0, 50) . "...<br>";
        } else {
            echo "Error executing query: " . mysqli_error($conn) . "<br>";
            echo "Query: " . $query . "<br>";
        }
    }
}

echo "Database import completed!";
?> 