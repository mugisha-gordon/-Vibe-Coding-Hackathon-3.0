<?php
require_once "config/database.php";

// Remove test child
$sql = "DELETE FROM children WHERE name = 'Test Child'";
if (mysqli_query($conn, $sql)) {
    echo "Test child removed.<br>";
}

// Remove test staff
$sql = "DELETE FROM users WHERE username = 'test_staff'";
if (mysqli_query($conn, $sql)) {
    echo "Test staff removed.<br>";
}

// Clean up test profile pictures
$test_files = glob('uploads/profiles/test_*.jpg');
foreach ($test_files as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "Removed test file: $file<br>";
    }
}

mysqli_close($conn);
echo "Cleanup completed.";
?> 