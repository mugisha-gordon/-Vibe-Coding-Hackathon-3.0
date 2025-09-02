<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    header("location: ../login.php");
    exit;
}

require_once "../config/database.php";
require_once "includes/Cache.php";
require_once "includes/dashboard_functions.php";

// Initialize cache
$cache = new Cache();

// Get dashboard statistics
$stats = getDashboardStats($conn, $cache);

// Extract statistics
$children_count = $stats['children_count'];
$volunteers_count = $stats['volunteers_count'];
$pending_requests = $stats['pending_requests'];
$feedback_count = $stats['feedback_count'];
$total_donations = $stats['total_donations'];
$monthly_donations = $stats['monthly_donations'];
$pending_donations = $stats['pending_donations'];
$impact_count = $stats['impact_count'];

// Get recent events
$events_sql = "SELECT * FROM events ORDER BY event_date DESC LIMIT 5";
$events_result = mysqli_query($conn, $events_sql);
if (!$events_result) {
    error_log("Error fetching events: " . mysqli_error($conn));
    $events_result = false;
}

// Get recent volunteer requests
$volunteer_requests_sql = "SELECT * FROM volunteer_requests WHERE status = 'pending' ORDER BY created_at DESC";
$volunteer_requests_result = mysqli_query($conn, $volunteer_requests_sql);
if (!$volunteer_requests_result) {
    error_log("Error fetching volunteer requests: " . mysqli_error($conn));
    $volunteer_requests_result = false;
}

// Get recent donations
$recent_donations_sql = "SELECT d.*, c.name as child_name 
                        FROM donations d 
                        LEFT JOIN children c ON d.child_id = c.id 
                        ORDER BY d.created_at DESC LIMIT 5";
$recent_donations_result = mysqli_query($conn, $recent_donations_sql);
if (!$recent_donations_result) {
    error_log("Error fetching donations: " . mysqli_error($conn));
    $recent_donations_result = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/dashboard.css" rel="stylesheet">
    <link href="assets/css/header.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="assets/images/logo.png" alt="Organization Logo" class="logo"a href="index.php">
                <h3><i class="fas fa-shield-alt"></i> Admin Panel</h3>
            </div>
            <button class="sidebar-toggle">
                <i class="fas fa-chevron-left"></i>
            </button>
            <nav class="nav flex-column">
                <a href="#overview" class="nav-link active" data-bs-toggle="tab">
                    <i class="fas fa-chart-line"></i>
                    <span>Overview</span>
                </a>
                <a href="#children" class="nav-link" data-bs-toggle="tab">
                    <i class="fas fa-child"></i>
                    <span>Children</span>
                </a>
                <a href="#volunteers" class="nav-link" data-bs-toggle="tab">
                    <i class="fas fa-hands-helping"></i>
                    <span>Volunteers</span>
                </a>
                <a href="#staff" class="nav-link" data-bs-toggle="tab">
                    <i class="fas fa-users-cog"></i>
                    <span>Staff & Board</span>
                </a>
                <a href="#events" class="nav-link" data-bs-toggle="tab">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Events</span>
                </a>
                <a href="#donations" class="nav-link" data-bs-toggle="tab">
                    <i class="fas fa-hand-holding-heart"></i>
                    <span>Donations</span>
                </a>
                <a href="#feedback" class="nav-link" data-bs-toggle="tab">
                    <i class="fas fa-comments"></i>
                    <span>Feedback</span>
                </a>
                <a href="#settings" class="nav-link" data-bs-toggle="tab">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </nav>
            
            <!-- Logout Section -->
            <div class="logout-section">
                <a href="../../logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Dashboard Header -->
            <header class="dashboard-header">
                <div class="logo-container">
                    <img src="assets/images/logo.svg" alt="Organization Logo" class="logo">
                    <h1 class="org-name">Organization Name</h1>
                </div>
                <div class="header-actions">
                    <div class="user-menu">
                        <button class="user-menu-btn">
                            <img src="assets/images/avatar.jpg" alt="User Avatar" class="user-avatar">
                            <span class="user-name"><?php echo $_SESSION['username']; ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
            </header>

            <div class="tab-content">
                <!-- Overview Section -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                    <div class="content-section">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2>Dashboard Overview</h2>
                            <div class="date-filter">
                                <select class="form-select" id="dateRange">
                                    <option value="today">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month" selected>This Month</option>
                                    <option value="year">This Year</option>
                                </select>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="quick-actions">
                            <a href="#children" class="quick-action-btn" data-bs-toggle="tab">
                                <i class="fas fa-plus"></i>
                                <span>Add Child</span>
                            </a>
                            <a href="#events" class="quick-action-btn" data-bs-toggle="tab">
                                <i class="fas fa-calendar-plus"></i>
                                <span>Create Event</span>
                            </a>
                            <a href="#volunteers" class="quick-action-btn" data-bs-toggle="tab">
                                <i class="fas fa-user-plus"></i>
                                <span>Add Volunteer</span>
                            </a>
                            <a href="#staff" class="quick-action-btn" data-bs-toggle="tab">
                                <i class="fas fa-user-tie"></i>
                                <span>Add Staff</span>
                            </a>
                        </div>

                        <!-- Statistics Cards -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <i class="fas fa-child text-primary"></i>
                                    <h3 id="stat-total_children"><?php echo $children_count; ?></h3>
                                    <p>Total Children</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <i class="fas fa-hands-helping text-success"></i>
                                    <h3 id="stat-total_volunteers"><?php echo $volunteers_count; ?></h3>
                                    <p>Active Volunteers</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <i class="fas fa-clock text-warning"></i>
                                    <h3 id="stat-pending_requests"><?php echo $pending_requests; ?></h3>
                                    <p>Pending Requests</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <i class="fas fa-comments text-info"></i>
                                    <h3 id="stat-feedback_count"><?php echo $feedback_count; ?></h3>
                                    <p>Feedback Messages</p>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Section -->
                        <div class="row mt-4">
                            <div class="col-md-8">
                                <div class="data-card">
                                    <h4>Donation Trends</h4>
                                    <canvas id="donationsChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="data-card">
                                    <h4>Volunteer Status</h4>
                                    <canvas id="volunteersChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity Section -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="content-section">
                                    <h3><i class="fas fa-hand-holding-heart"></i> Recent Donations</h3>
                                    <div class="table-container">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Donor</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if ($recent_donations_result) {
                                                    while($donation = mysqli_fetch_assoc($recent_donations_result)) {
                                                        $status_class = getStatusBadgeClass($donation['status']);
                                                        echo "<tr>
                                                            <td>{$donation['donor_name']}</td>
                                                            <td>$" . number_format($donation['amount'], 2) . "</td>
                                                            <td><span class='status-badge bg-{$status_class}'>{$donation['status']}</span></td>
                                                            <td>" . date('M d, Y', strtotime($donation['created_at'])) . "</td>
                                                        </tr>";
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="content-section">
                                    <h3><i class="fas fa-hands-helping"></i> Recent Volunteer Requests</h3>
                                    <div class="table-container">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if ($volunteer_requests_result) {
                                                    while($request = mysqli_fetch_assoc($volunteer_requests_result)) {
                                                        echo "<tr>
                                                            <td>{$request['name']}</td>
                                                            <td>{$request['email']}</td>
                                                            <td><span class='status-badge bg-warning'>{$request['status']}</span></td>
                                                            <td>
                                                                <button class='action-btn btn-success' onclick='updateVolunteerStatus({$request['id']}, \"approved\")'>
                                                                    <i class='fas fa-check'></i> Approve
                                                                </button>
                                                                <button class='action-btn btn-danger' onclick='updateVolunteerStatus({$request['id']}, \"rejected\")'>
                                                                    <i class='fas fa-times'></i> Reject
                                                                </button>
                                                            </td>
                                                        </tr>";
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Children Section -->
                <div class="tab-pane fade" id="children" role="tabpanel" aria-labelledby="children-tab">
                    <div class="content-section">
                        <?php include 'includes/children_section.php'; ?>
                    </div>
                </div>

                <!-- Volunteers Section -->
                <div class="tab-pane fade" id="volunteers" role="tabpanel" aria-labelledby="volunteers-tab">
                    <div class="content-section">
                        <?php include 'includes/volunteers_section.php'; ?>
                    </div>
                </div>

                <!-- Staff Section -->
                <div class="tab-pane fade" id="staff" role="tabpanel" aria-labelledby="staff-tab">
                    <div class="content-section">
                        <?php include 'includes/staff_section.php'; ?>
                    </div>
                </div>

                <!-- Events Section -->
                <div class="tab-pane fade" id="events" role="tabpanel" aria-labelledby="events-tab">
                    <div class="content-section">
                        <?php include 'includes/events_section.php'; ?>
                    </div>
                </div>

                <!-- Donations Section -->
                <div class="tab-pane fade" id="donations" role="tabpanel" aria-labelledby="donations-tab">
                    <div class="content-section">
                        <?php include 'includes/donations_section.php'; ?>
                    </div>
                </div>

                <!-- Feedback Section -->
                <div class="tab-pane fade" id="feedback" role="tabpanel" aria-labelledby="feedback-tab">
                    <div class="content-section">
                        <?php include 'includes/feedback_section.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include required scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script>
        // Initialize Bootstrap tabs
        document.addEventListener('DOMContentLoaded', function() {
            // Get all tab links
            const tabLinks = document.querySelectorAll('.nav-link[data-bs-toggle="tab"]');
            
            // Add click event listeners to all tab links
            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all tabs
                    document.querySelectorAll('.tab-pane').forEach(tab => {
                        tab.classList.remove('show', 'active');
                    });
                    
                    // Remove active class from all links
                    tabLinks.forEach(l => l.classList.remove('active'));
                    
                    // Add active class to clicked link
                    this.classList.add('active');
                    
                    // Show the corresponding tab
                    const targetId = this.getAttribute('href').substring(1);
                    const targetTab = document.getElementById(targetId);
                    if (targetTab) {
                        targetTab.classList.add('show', 'active');
                    }
                });
            });

            // Ensure overview tab is active by default
            const overviewTab = document.querySelector('.nav-link[href="#overview"]');
            if (overviewTab) {
                overviewTab.click();
            }
        });
    </script>
</body>
</html> 