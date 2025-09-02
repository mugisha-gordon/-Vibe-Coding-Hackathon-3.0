<?php
require_once '../config/database.php';

// Read and execute SQL file
$sql = file_get_contents(__DIR__ . '/sql/setup_database.sql');

// Split SQL file into individual queries
$queries = array_filter(array_map('trim', explode(';', $sql)));

// Execute each query
$success = true;
foreach ($queries as $query) {
    if (!empty($query)) {
        if (!mysqli_query($conn, $query)) {
            echo "Error executing query: " . mysqli_error($conn) . "\n";
            echo "Query: " . $query . "\n\n";
            $success = false;
        }
    }
}

if ($success) {
    echo "Database setup completed successfully!\n";
} else {
    echo "There were errors during database setup.\n";
} 