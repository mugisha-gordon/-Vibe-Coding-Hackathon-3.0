<?php
session_start();
require_once "../../config/database.php";

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized access']));
}

// Get all staff data
$sql = "SELECT * FROM staff ORDER BY name";
$result = mysqli_query($conn, $sql);

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="staff_export_' . date('Y-m-d') . '.csv"');

// Create file pointer for output
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for proper Excel encoding
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add headers
fputcsv($output, [
    'Name',
    'Email',
    'Phone',
    'Position',
    'Department',
    'Hire Date',
    'Status',
    'Created At',
    'Last Updated'
]);

// Add data rows
while($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, [
        $row['name'],
        $row['email'],
        $row['phone'],
        $row['position'],
        $row['department'],
        $row['hire_date'],
        $row['status'],
        $row['created_at'],
        $row['updated_at']
    ]);
}

// Close file pointer
fclose($output);
?> 