<?php
require_once "config/database.php";

// Function to execute SQL file
function executeSQLFile($conn, $filename) {
    $sql = file_get_contents($filename);
    
    // Split SQL file into individual queries
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    $success = true;
    $errors = [];
    
    foreach ($queries as $query) {
        if (!empty($query)) {
            if (!mysqli_query($conn, $query)) {
                $success = false;
                $errors[] = mysqli_error($conn);
            }
        }
    }
    
    return ['success' => $success, 'errors' => $errors];
}

// Create required directories
$directories = [
    'uploads',
    'uploads/profiles',
    'uploads/events',
    'uploads/documents',
    'uploads/inventory'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Execute database initialization
$result = executeSQLFile($conn, 'sql/init_database.sql');

// Set proper permissions for upload directories
foreach ($directories as $dir) {
    chmod($dir, 0777);
}

// Output result
if ($result['success']) {
    echo "<h2>Installation Successful!</h2>";
    echo "<p>The database has been initialized successfully.</p>";
    echo "<p>You can now <a href='login.php'>login to the system</a>.</p>";
    
    // Default admin credentials
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
    echo "<h3>Default Admin Credentials:</h3>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><strong>Email:</strong> admin@example.com</p>";
    echo "</div>";
    
    echo "<p style='color: #dc3545; margin-top: 20px;'>";
    echo "<strong>Important:</strong> Please change the default admin password after your first login!";
    echo "</p>";
} else {
    echo "<h2>Installation Failed</h2>";
    echo "<p>The following errors occurred:</p>";
    echo "<ul>";
    foreach ($result['errors'] as $error) {
        echo "<li>" . htmlspecialchars($error) . "</li>";
    }
    echo "</ul>";
}

mysqli_close($conn);
?> 