<?php
session_start();
require_once "../../config/database.php";

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    die('Unauthorized access');
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="volunteer_requests_' . date('Y-m-d') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for proper Excel encoding
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add CSV headers
fputcsv($output, [
    'ID',
    'First Name',
    'Last Name',
    'Email',
    'Phone',
    'Skills',
    'Availability',
    'Areas of Interest',
    'Previous Experience',
    'Motivation',
    'Status',
    'Applied On'
]);

// Get all requests
$sql = "SELECT * FROM volunteer_requests ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

// Add data rows
while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, [
        $row['id'],
        $row['first_name'],
        $row['last_name'],
        $row['email'],
        $row['phone'],
        $row['skills'],
        $row['availability'],
        $row['areas_of_interest'],
        $row['previous_experience'],
        $row['motivation'],
        $row['status'],
        $row['created_at']
    ]);
}

// Close the output stream
fclose($output);
mysqli_close($conn);
?> 