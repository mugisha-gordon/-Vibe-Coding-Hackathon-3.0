<?php
session_start();

// Check if user is logged in and has admin privileges
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    header("location: ../login.php");
    exit;
}

// Include database connection
require_once "../config/database.php";

// Process form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validate and sanitize input
    $donor_name = trim(mysqli_real_escape_string($conn, $_POST["donor_name"]));
    $donor_email = trim(mysqli_real_escape_string($conn, $_POST["donor_email"]));
    $donor_phone = trim(mysqli_real_escape_string($conn, $_POST["donor_phone"]));
    $amount = floatval($_POST["amount"]);
    $donation_type = trim(mysqli_real_escape_string($conn, $_POST["donation_type"]));
    $payment_method = trim(mysqli_real_escape_string($conn, $_POST["payment_method"]));
    $donation_purpose = trim(mysqli_real_escape_string($conn, $_POST["donation_purpose"]));
    $notes = trim(mysqli_real_escape_string($conn, $_POST["notes"]));
    $donation_date = trim(mysqli_real_escape_string($conn, $_POST["donation_date"]));
    
    // If no date specified, use current date
    if(empty($donation_date)) {
        $donation_date = date("Y-m-d H:i:s");
    }
    
    // Simple validation
    if(empty($donor_name) || ($donation_type == 'Money' && $amount <= 0)){
        $_SESSION['error'] = "Please fill all required fields with valid data.";
        header("location: ../dashboard.php#donations");
        exit;
    }
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert data into the donations table
        $sql = "INSERT INTO donations (donor_name, donor_email, donor_phone, amount, donation_type, 
                donation_date, payment_method, donation_purpose, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "sssdsssss", $donor_name, $donor_email, $donor_phone, $amount, 
                                $donation_type, $donation_date, $payment_method, $donation_purpose, $notes);
            
            if(!mysqli_stmt_execute($stmt)){
                throw new Exception("Error executing donation insert: " . mysqli_stmt_error($stmt));
            }
            
            $donation_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            
            // If donation type is 'Goods', handle donation items
            if($donation_type == 'Goods' && isset($_POST['item_name']) && is_array($_POST['item_name'])) {
                $item_names = $_POST['item_name'];
                $item_descriptions = $_POST['item_description'];
                $quantities = $_POST['quantity'];
                $estimated_values = $_POST['estimated_value'];
                
                for($i = 0; $i < count($item_names); $i++) {
                    if(!empty($item_names[$i]) && !empty($quantities[$i])) {
                        $item_name = trim(mysqli_real_escape_string($conn, $item_names[$i]));
                        $item_description = isset($item_descriptions[$i]) ? trim(mysqli_real_escape_string($conn, $item_descriptions[$i])) : '';
                        $quantity = intval($quantities[$i]);
                        $estimated_value = isset($estimated_values[$i]) ? floatval($estimated_values[$i]) : 0.00;
                        
                        $item_sql = "INSERT INTO donation_items (donation_id, item_name, item_description, quantity, estimated_value) 
                                    VALUES (?, ?, ?, ?, ?)";
                        
                        if($item_stmt = mysqli_prepare($conn, $item_sql)){
                            mysqli_stmt_bind_param($item_stmt, "issid", $donation_id, $item_name, $item_description, $quantity, $estimated_value);
                            
                            if(!mysqli_stmt_execute($item_stmt)){
                                throw new Exception("Error executing donation item insert: " . mysqli_stmt_error($item_stmt));
                            }
                            
                            mysqli_stmt_close($item_stmt);
                        }
                    }
                }
            }
            
            // Record activity
            $username = $_SESSION['username'];
            $activity = "Registered a new " . strtolower($donation_type) . " donation from: $donor_name";
            
            $activity_sql = "INSERT INTO activity_log (username, activity) VALUES (?, ?)";
            if($activity_stmt = mysqli_prepare($conn, $activity_sql)){
                mysqli_stmt_bind_param($activity_stmt, "ss", $username, $activity);
                mysqli_stmt_execute($activity_stmt);
                mysqli_stmt_close($activity_stmt);
            }
            
            // Commit transaction
            mysqli_commit($conn);
            
            $_SESSION['success'] = "Donation registered successfully!";
            header("location: ../dashboard.php#donations");
            exit;
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        
        $_SESSION['error'] = "Something went wrong: " . $e->getMessage();
        header("location: ../dashboard.php#donations");
        exit;
    }
    
    mysqli_close($conn);
}
?> 