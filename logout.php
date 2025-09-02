<?php
// Initialize the session
session_start();
require_once "config/database.php";

// Log the logout activity if user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $activity_query = "INSERT INTO activity_log (user_id, action, details) VALUES (?, 'logout', 'User logged out')";
    $activity_stmt = mysqli_prepare($conn, $activity_query);
    mysqli_stmt_bind_param($activity_stmt, "i", $user_id);
    mysqli_stmt_execute($activity_stmt);
}

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroy the session
session_destroy();

// Clear any existing output
ob_clean();

// Set cache control headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to login page
header("Location: login.php");
exit();
?> 