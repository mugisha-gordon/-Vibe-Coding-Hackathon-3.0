<?php
session_start();
require_once "../../config/database.php";

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    http_response_code(403);
    exit("Unauthorized access");
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'overview';

switch($tab) {
    case 'overview':
        include '../includes/overview_section.php';
        break;
    case 'children':
        include '../includes/children_section.php';
        break;
    case 'volunteers':
        include '../includes/volunteers_section.php';
        break;
    case 'staff':
        include '../includes/staff_section.php';
        break;
    case 'events':
        include '../includes/events_section.php';
        break;
    case 'donations':
        include '../includes/donations_section.php';
        break;
    case 'feedback':
        include '../includes/feedback_section.php';
        break;
    default:
        include '../includes/overview_section.php';
}
?> 