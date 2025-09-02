<?php
session_start();

// List of public pages that don't require authentication
$public_pages = [
    'index.php',
    'about.php',
    'programs.php',
    'donate.php',
    'volunteer.php',
    'contact.php',
    'login.php',
    'register.php'
];

// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);

// Check if the current page is public
if (!in_array($current_page, $public_pages)) {
    // Check if user is logged in
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: login.php");
        exit;
    }

    // Check user role for specific pages
    $admin_pages = [
        'dashboard.php',
        'manage_staff.php',
        'manage_programs.php',
        'reports.php',
        'settings.php'
    ];

    if (in_array($current_page, $admin_pages) && $_SESSION["role"] !== "admin") {
        header("location: index.php");
        exit;
    }
}
?> 