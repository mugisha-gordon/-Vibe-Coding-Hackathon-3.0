<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<style>
    /* Header Navigation Styles */
    .navbar-nav {
        flex-wrap: wrap;
        justify-content: center;
    }

    .nav-item {
        margin: 0 0.2rem;
    }

    .nav-link {
        font-size: 0.9rem;
        padding: 0.5rem 0.7rem !important;
        white-space: nowrap;
    }

    .navbar-brand {
        font-size: 1.1rem;
    }

    .navbar-brand .brand-text {
        font-size: 1rem;
    }

    .dropdown-item {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }

    @media (max-width: 991px) {
        .navbar-nav {
            flex-direction: column;
            align-items: center;
        }

        .nav-item {
            margin: 0.2rem 0;
            width: 100%;
            text-align: center;
        }

        .nav-link {
            padding: 0.5rem 1rem !important;
        }

        .dropdown-menu {
            text-align: center;
        }
    }
    </style>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="assets/images/logo.png" alt="Logo" class="logo-img me-2">
            <span class="brand-text">Bumbobi Child Support Uganda</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php"><i class="fas fa-info-circle"></i> About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="programs.php"><i class="fas fa-hands-helping"></i> Programs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="donate.php"><i class="fas fa-heart"></i> Donate</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="volunteer.php"><i class="fas fa-users"></i> Volunteer</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                    <?php if($_SESSION["role"] === "admin"): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-shield"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header">Dashboard</h6></li>
                                <li><a class="dropdown-item" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
                                <li><hr class="dropdown-divider"></li>
                                
                                <li><h6 class="dropdown-header">Management</h6></li>
                                <li><a class="dropdown-item" href="manage_staff.php"><i class="fas fa-user-cog"></i> Staff</a></li>
                                <li><a class="dropdown-item" href="manage_children.php"><i class="fas fa-child"></i> Children</a></li>
                                <li><a class="dropdown-item" href="manage_board.php"><i class="fas fa-users-cog"></i> Board Members</a></li>
                                <li><a class="dropdown-item" href="manage_programs.php"><i class="fas fa-tasks"></i> Programs</a></li>
                                <li><hr class="dropdown-divider"></li>
                                
                                <li><h6 class="dropdown-header">Reports & Analytics</h6></li>
                                <li><a class="dropdown-item" href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                                <li><a class="dropdown-item" href="analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
                                <li><hr class="dropdown-divider"></li>
                                
                                <li><h6 class="dropdown-header">System</h6></li>
                                <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                                <li><a class="dropdown-item" href="activity_log.php"><i class="fas fa-history"></i> Activity Log</a></li>
                                <li><a class="dropdown-item" href="backup.php"><i class="fas fa-database"></i> Backup</a></li>
                                <li><hr class="dropdown-divider"></li>
                                
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION["username"]); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                    </li>

                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav> 