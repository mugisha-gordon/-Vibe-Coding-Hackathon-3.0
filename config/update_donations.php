<?php
require_once "database.php";

// Add child_id column if it doesn't exist
$checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM donations LIKE 'child_id'");
if (mysqli_num_rows($checkColumn) == 0) {
    $alterTable = "ALTER TABLE donations 
                   ADD COLUMN child_id INT,
                   ADD FOREIGN KEY (child_id) REFERENCES children(id) ON DELETE SET NULL,
                   ADD INDEX idx_child_id (child_id)";
    
    if (!mysqli_query($conn, $alterTable)) {
        die("Error adding child_id column: " . mysqli_error($conn));
    }
    echo "Added child_id column to donations table\n";
}

// Update donation_date to created_at if it exists
$checkDateColumn = mysqli_query($conn, "SHOW COLUMNS FROM donations LIKE 'donation_date'");
if (mysqli_num_rows($checkDateColumn) > 0) {
    // First, copy data from donation_date to created_at
    $updateData = "UPDATE donations SET created_at = donation_date WHERE created_at IS NULL";
    if (!mysqli_query($conn, $updateData)) {
        die("Error updating date data: " . mysqli_error($conn));
    }
    
    // Then drop the donation_date column
    $dropColumn = "ALTER TABLE donations DROP COLUMN donation_date";
    if (!mysqli_query($conn, $dropColumn)) {
        die("Error dropping donation_date column: " . mysqli_error($conn));
    }
    echo "Updated donation_date to created_at\n";
}

// Check and create indexes only if the columns exist
$columns = [
    'created_at' => 'idx_donation_date',
    'status' => 'idx_donation_status',
    'child_id' => 'idx_child_id'
];

foreach ($columns as $column => $index_name) {
    // Check if column exists
    $checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM donations LIKE '$column'");
    if (mysqli_num_rows($checkColumn) > 0) {
        // Check if index exists
        $checkIndex = mysqli_query($conn, "SHOW INDEX FROM donations WHERE Key_name = '$index_name'");
        if (mysqli_num_rows($checkIndex) == 0) {
            $createIndex = "CREATE INDEX $index_name ON donations($column)";
            if (!mysqli_query($conn, $createIndex)) {
                echo "Warning: Could not create index $index_name: " . mysqli_error($conn) . "\n";
            } else {
                echo "Created index $index_name on $column column\n";
            }
        }
    }
}

echo "Donations table update completed successfully.";
?>
