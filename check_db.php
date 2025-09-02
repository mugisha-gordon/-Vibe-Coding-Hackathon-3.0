<?php
require_once "config/database.php";

// Check if tables exist
$tables = ['users', 'children', 'programs', 'staff', 'success_stories', 'child_programs', 'donations', 'volunteers', 'newsletter_subscribers', 'activity_log'];

foreach ($tables as $table) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) > 0) {
        echo "Table '$table' exists.<br>";
    } else {
        echo "Table '$table' does NOT exist.<br>";
    }
}

// Close connection
mysqli_close($conn);
?> 