<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize authentication variables
$isLoggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$isAdmin = $isLoggedIn && isset($_SESSION["role"]) && $_SESSION["role"] === "admin";
$username = $isLoggedIn ? $_SESSION["username"] : "";

// Get current page for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bumbobi Child Support Uganda</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
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
    
        /* Navbar Styles */
        .navbar {
            background: linear-gradient(135deg, #1a237e, #0d47a1);
            padding: 1rem 0;
            transition: all 0.3s ease;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: white !important;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }
        
        .nav-link i {
            margin-right: 5px;
            width: 20px;
            text-align: center;
        }
        
        /* Dropdown Styles */
        .dropdown-menu {
            background: white;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 10px;
            padding: 1rem 0;
            margin-top: 0.5rem;
        }
        
        .dropdown-item {
            padding: 0.7rem 1.5rem;
            color: #333;
            font-weight: 500;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .dropdown-item:hover {
            background: #f8f9fa;
            color: #1a237e;
            transform: translateX(5px);
        }
        
        .dropdown-item i {
            width: 20px;
            margin-right: 10px;
            color: #1a237e;
        }
        
        .dropdown-divider {
            margin: 0.5rem 0;
        }
        
        /* Mobile Menu Styles */
        @media (max-width: 991px) {
            .navbar-collapse {
                background: #1a237e;
                padding: 1rem;
                border-radius: 10px;
                margin-top: 1rem;
                max-height: 80vh;
                overflow-y: auto;
            }
            
            .navbar-nav {
                margin: 0.5rem 0;
            }
            
            .nav-link {
                padding: 0.8rem 1rem !important;
            }
            
            .dropdown-menu {
                background: rgba(255, 255, 255, 0.1);
                border: none;
                box-shadow: none;
                margin-top: 0;
                margin-left: 1rem;
            }
            
            .dropdown-item {
                color: white;
                padding: 0.8rem 1.5rem;
            }
            
            .dropdown-item:hover {
                background: rgba(255, 255, 255, 0.1);
                color: white;
            }
            
            .dropdown-item i {
                color: white;
            }
            
            .navbar-brand .brand-text {
                font-size: 1rem;
            }
        }
        
        /* Small Screen Adjustments */
        @media (max-width: 576px) {
            .navbar-brand .brand-text {
                font-size: 0.9rem;
            }
            
            .nav-link {
                font-size: 0.9rem;
            }
            
            .dropdown-item {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="index.php">
                    <img src="assets/images/logo.png" alt="Logo" class="logo-img me-2" width="40" height="40">
                    <span class="brand-text">Bumbobi Child Support Uganda</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="index.php">
                                <i class="fas fa-home"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'about.php' ? 'active' : ''; ?>" href="about.php">
                                <i class="fas fa-info-circle"></i> About
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'programs.php' ? 'active' : ''; ?>" href="programs.php">
                                <i class="fas fa-hands-helping"></i> Programs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'tours.php' ? 'active' : ''; ?>" href="tours.php">
                                <i class="fas fa-plane"></i> Tours & Travels
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'events.php' ? 'active' : ''; ?>" href="events.php">
                                <i class="fas fa-calendar-alt"></i> Events
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'donate.php' ? 'active' : ''; ?>" href="donate.php">
                                <i class="fas fa-heart"></i> Donate
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'volunteer.php' ? 'active' : ''; ?>" href="volunteer.php">
                                <i class="fas fa-users"></i> Volunteer
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>" href="contact.php">
                                <i class="fas fa-envelope"></i> Contact
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <?php if($isLoggedIn): ?>
                            <?php if($isAdmin): ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-user-shield"></i> Admin
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                                        <li><a class="dropdown-item" href="admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="admin/manage_staff.php"><i class="fas fa-user-cog"></i> Staff</a></li>
                                        <li><a class="dropdown-item" href="admin/manage_children.php"><i class="fas fa-child"></i> Children</a></li>
                                        <li><a class="dropdown-item" href="admin/manage_board.php"><i class="fas fa-users-cog"></i> Board Members</a></li>
                                        <li><a class="dropdown-item" href="admin/manage_programs.php"><i class="fas fa-tasks"></i> Programs</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="admin/reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                                        <li><a class="dropdown-item" href="admin/analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="admin/settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                                        <li><a class="dropdown-item" href="admin/activity_log.php"><i class="fas fa-history"></i> Activity Log</a></li>
                                        <li><a class="dropdown-item" href="admin/backup.php"><i class="fas fa-database"></i> Backup</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                                    </ul>
                                </li>
                            <?php else: ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($username); ?>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $current_page == 'login.php' ? 'active' : ''; ?>" href="login.php">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Navigation JS -->
    <script src="assets/js/navigation.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all dropdowns
        var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        var dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl, {
                offset: [0, 10],
                boundary: 'viewport'
            });
        });

        // Handle mobile menu
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarCollapse = document.querySelector('.navbar-collapse');
        
        if (navbarToggler && navbarCollapse) {
            navbarToggler.addEventListener('click', function() {
                navbarCollapse.classList.toggle('show');
            });
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!navbarCollapse.contains(e.target) && !navbarToggler.contains(e.target)) {
                    navbarCollapse.classList.remove('show');
                }
            });
            
            // Handle dropdowns in mobile view
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    if (window.innerWidth < 992) {
                        e.preventDefault();
                        const dropdown = bootstrap.Dropdown.getInstance(this);
                        if (dropdown) {
                            dropdown.toggle();
                        }
                    }
                });
            });
        }
    });
    </script>
</body>
</html> 