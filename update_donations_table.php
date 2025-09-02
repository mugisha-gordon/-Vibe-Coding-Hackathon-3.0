<?php
require_once "config/database.php";

// Add missing columns to donations table
$alter_queries = [
    "ALTER TABLE donations ADD COLUMN IF NOT EXISTS donor_phone VARCHAR(20) AFTER donor_email",
    "ALTER TABLE donations ADD COLUMN IF NOT EXISTS donor_address TEXT AFTER donor_phone",
    "ALTER TABLE donations ADD COLUMN IF NOT EXISTS donor_city VARCHAR(100) AFTER donor_address",
    "ALTER TABLE donations ADD COLUMN IF NOT EXISTS donor_country VARCHAR(100) AFTER donor_city"
];

foreach ($alter_queries as $query) {
    if (mysqli_query($conn, $query)) {
        echo "Successfully executed: " . $query . "\n";
    } else {
        echo "Error executing query: " . $query . "\n";
        echo "Error: " . mysqli_error($conn) . "\n";
    }
}

// Verify table structure
$result = mysqli_query($conn, "DESCRIBE donations");
if ($result) {
    echo "\nCurrent table structure:\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Error getting table structure: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
?> 