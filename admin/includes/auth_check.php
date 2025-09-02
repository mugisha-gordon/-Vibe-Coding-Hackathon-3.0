<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// Check if user has admin role
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("location: ../login.php");
    exit;
}
?> 