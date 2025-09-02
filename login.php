<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect to dashboard
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: dashboard.php");
    exit;
}

// Include config file
require_once "config/database.php";

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Default admin credentials
$admin_username = "admin";
$admin_password = "Admin@123";

// Processing form data when form is submitted
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
        // Check for admin login
        if($username === $admin_username && $password === $admin_password) {
            session_start();
            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = 1;
            $_SESSION["username"] = $username;
            $_SESSION["role"] = "admin";
            header("location: admin/dashboard.php");
            exit;
        }
        
        // Check database for other users
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
                                session_start();
                                
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                $_SESSION["role"] = $role;
                                
                                    header("location: admin/dashboard.php");
                            } else{
                                $login_err = "Invalid username or password.";
                            }
                        }
                    } else{
                        $login_err = "Invalid username or password.";
                    }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($conn);
}
?>

<?php include 'includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bumbobi Child Support Uganda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #50c878;
            --accent-color: #ff6b6b;
            --text-color: #333;
            --light-bg: #f8f9fa;
            --dark-bg: #343a40;
            --gradient-primary: linear-gradient(135deg, #4a90e2 0%, #50c878 100%);
            --gradient-secondary: linear-gradient(135deg, #ff6b6b 0%, #ff8e53 100%);
            --box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--gradient-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-page {
            width: 100%;
            max-width: 400px;
            position: relative;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--box-shadow);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transform: translateY(0);
            transition: var(--transition);
        }

        .login-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header img {
            width: 80px;
            height: auto;
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .login-header h1 {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .form-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            font-size: 1.2rem;
            transition: var(--transition);
            cursor: pointer;
        }

        .form-control {
            padding: 15px 15px 15px 45px;
            border-radius: 50px;
            border: 2px solid #eee;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
        }

        .form-check {
            margin: 20px 0;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            border-radius: 50px;
            background: var(--gradient-primary);
            border: none;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: var(--transition);
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(74, 144, 226, 0.3);
        }

        .login-footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
        }

        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .login-footer a:hover {
            color: var(--secondary-color);
        }

        .alert {
            border-radius: 50px;
            padding: 15px 25px;
            margin-bottom: 25px;
            border: none;
            background: rgba(255, 107, 107, 0.1);
            color: var(--accent-color);
            font-weight: 500;
        }

        .invalid-feedback {
            color: var(--accent-color);
            font-size: 0.9rem;
            margin-top: 5px;
            padding-left: 15px;
        }

        .is-invalid {
            border-color: var(--accent-color) !important;
        }

        .is-invalid:focus {
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 107, 0.25) !important;
        }

        /* Loading Animation */
        .loading {
            position: relative;
        }

        .loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin: -10px 0 0 -10px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }

            .login-header h1 {
                font-size: 1.8rem;
            }

            .login-header p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <div class="login-header">
                <img src="assets/images/logo.png" alt="Logo" class="mb-4">
                <h1>Welcome Back</h1>
                <p>Please login to your account</p>
            </div>
            
            <?php if(!empty($login_err)): ?>
                <div class="alert"><?php echo $login_err; ?></div>
            <?php endif; ?>
            
            <form class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" placeholder="Username" value="<?php echo $username; ?>">
                                <span class="invalid-feedback"><?php echo $username_err; ?></span>
                            </div>    
                
                <div class="form-group">
                    <i class="fas fa-lock input-icon" id="togglePassword"></i>
                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" placeholder="Password">
                                <span class="invalid-feedback"><?php echo $password_err; ?></span>
                            </div>
                
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                
                <button type="submit" class="btn btn-login">Login</button>
            </form>
            
            <div class="login-footer">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
                <a href="forgot-password.php">Forgot Password?</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = this.nextElementSibling;
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-lock');
            this.classList.toggle('fa-lock-open');
        });

        // Form validation
        document.querySelector('.login-form').addEventListener('submit', function(e) {
            const username = document.querySelector('input[name="username"]');
            const password = document.querySelector('input[name="password"]');
            const submitBtn = document.querySelector('.btn-login');
            let isValid = true;

            if (!username.value.trim()) {
                username.classList.add('is-invalid');
                isValid = false;
            }

            if (!password.value.trim()) {
                password.classList.add('is-invalid');
                isValid = false;
            }

            if (isValid) {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
            } else {
                e.preventDefault();
            }
        });

        // Remove invalid class on input
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    </script>
</body>
</html>

<?php include 'includes/footer.php'; ?> 