<?php
session_start();
require_once "config/database.php";

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$message = "";

// Handle adding new team member
if(isset($_POST["add_member"])){
    $child_id = $_POST["child_id"];
    $position = $_POST["position"];
    $jersey_number = $_POST["jersey_number"];
    $join_date = date("Y-m-d");
    
    // Check if jersey number is already taken
    $check_sql = "SELECT id FROM football_team WHERE jersey_number = ? AND status = 'Active'";
    if($stmt = mysqli_prepare($conn, $check_sql)){
        mysqli_stmt_bind_param($stmt, "i", $jersey_number);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if(mysqli_stmt_num_rows($stmt) > 0){
            $message = "Error: This jersey number is already taken.";
        } else {
            // Insert new team member
            $sql = "INSERT INTO football_team (child_id, position, jersey_number, join_date) VALUES (?, ?, ?, ?)";
            if($stmt = mysqli_prepare($conn, $sql)){
                mysqli_stmt_bind_param($stmt, "isis", $child_id, $position, $jersey_number, $join_date);
                if(mysqli_stmt_execute($stmt)){
                    $message = "Team member added successfully!";
                } else {
                    $message = "Error adding team member.";
                }
            }
        }
    }
}

// Handle removing team member
if(isset($_POST["remove_member"])){
    $team_member_id = $_POST["team_member_id"];
    
    $sql = "UPDATE football_team SET status = 'Inactive' WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $team_member_id);
        if(mysqli_stmt_execute($stmt)){
            $message = "Team member removed successfully!";
        } else {
            $message = "Error removing team member.";
        }
    }
}

// Redirect back to football page with message
$_SESSION["message"] = $message;
header("location: football.php");
exit;
?> 