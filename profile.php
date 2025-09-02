<?php
require_once "config/database.php";
require_once "config/performance_monitor.php";
session_start();

$monitor = new PerformanceMonitor();
$monitor->start();

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Get user information
$user_id = $_SESSION["id"];
$sql = "SELECT * FROM users WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt);
}

$metrics = $monitor->getPerformanceMetrics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Bumbobi Child Support Uganda</title>
    
    <!-- Preload critical resources -->
    <link rel="preload" href="assets/css/style.css" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" as="style">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" as="style">
    
    <!-- Main CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
    .profile-header {
        background: linear-gradient(135deg, #1a237e, #0d47a1);
        color: white;
        padding: 4rem 0;
        margin-bottom: 3rem;
    }

    .profile-card {
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .profile-card:hover {
        transform: translateY(-5px);
    }

    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        margin: -75px auto 20px;
        border: 5px solid white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        color: #1a237e;
    }

    .profile-info {
        padding: 2rem;
    }

    .profile-info h3 {
        color: #1a237e;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    .info-item {
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }

    .info-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .info-label {
        font-weight: 500;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .info-value {
        color: #212529;
        font-size: 1.1rem;
    }

    .edit-profile-btn {
        background: #1a237e;
        color: white;
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .edit-profile-btn:hover {
        background: #0d47a1;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    @media (max-width: 768px) {
        .profile-header {
            padding: 3rem 0;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            margin-top: -60px;
            font-size: 3rem;
        }

        .profile-info {
            padding: 1.5rem;
        }

        .info-value {
            font-size: 1rem;
        }
    }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Profile Header -->
    <section class="profile-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1>My Profile</h1>
                    <p>View and manage your account information</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Profile Content -->
    <section class="profile-content py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card profile-card">
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="profile-info">
                            <h3 class="text-center mb-4"><?php echo htmlspecialchars($user['username']); ?></h3>
                            
                            <div class="info-item">
                                <div class="info-label">Email Address</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">Account Type</div>
                                <div class="info-value"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">Member Since</div>
                                <div class="info-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></div>
                            </div>

                            <div class="text-center mt-4">
                                <a href="edit_profile.php" class="btn edit-profile-btn">
                                    <i class="fas fa-edit me-2"></i>Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/performance.js"></script>
</body>
</html> 