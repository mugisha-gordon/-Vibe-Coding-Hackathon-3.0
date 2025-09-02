<?php
session_start();

// Include config file
require_once "config/database.php";

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Default admin credentials
$admin_username = "admin";
$admin_password = "Admin@123"; // Plain text for testing, should be hashed in production

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Check for admin login first
        if($username === $admin_username && $password === $admin_password){
            // Password is correct, start a new session
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $username;
            $_SESSION["role"] = "admin";
            
            // Redirect user to dashboard
            header("location: dashboard.php");
            exit();
        } else {
            // Check regular user login
            $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
            
            if($stmt = mysqli_prepare($conn, $sql)){
                mysqli_stmt_bind_param($stmt, "s", $param_username);
                $param_username = $username;
                
                if(mysqli_stmt_execute($stmt)){
                    mysqli_stmt_store_result($stmt);
                    
                    if(mysqli_stmt_num_rows($stmt) == 1){
                        mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $role);
                        if(mysqli_stmt_fetch($stmt)){
                            if(password_verify($password, $hashed_password)){
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                $_SESSION["role"] = $role;
                                
                                if($role == "staff"){
                                    header("location: staff/dashboard.php");
                                } else {
                                    header("location: dashboard.php");
                                }
                                exit();
                            } else{
                                $login_err = "Invalid username or password.";
                            }
                        }
                    } else{
                        $login_err = "Invalid username or password.";
                    }
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
    mysqli_close($conn);
    
    // If login failed, redirect back to login page with error
    $_SESSION['login_err'] = $login_err;
    header("location: login.php");
    exit();
}
?> 