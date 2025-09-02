<?php
require_once "config/database.php";
require_once "config/performance_monitor.php";
session_start();

$monitor = new PerformanceMonitor();
$monitor->start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$isAdmin = $isLoggedIn && isset($_SESSION["role"]) && $_SESSION["role"] === "admin";

// Fetch success stories with proper date handling
$stories_sql = "SELECT s.*, p.name as program_name, c.first_name, c.last_name 
                FROM success_stories s 
                LEFT JOIN programs p ON s.program_id = p.id 
                LEFT JOIN children c ON s.child_id = c.id 
                ORDER BY s.created_at DESC";
$stories_result = mysqli_query($conn, $stories_sql);

$metrics = $monitor->getPerformanceMetrics();

// Get unique icons for each story
$icons = [
    'fa-graduation-cap',
    'fa-star',
    'fa-trophy',
    'fa-medal',
    'fa-award',
    'fa-certificate',
    'fa-book',
    'fa-heart',
    'fa-lightbulb',
    'fa-rocket'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success Stories - Bumbobi Child Support Uganda</title>
    
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
    .story-card {
        transition: transform 0.3s ease;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .story-card:hover {
        transform: translateY(-5px);
    }

    .story-icon {
        width: 80px;
        height: 80px;
        background: #f8f9fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }

    .story-icon i {
        font-size: 2.5rem;
        color: #0d6efd;
    }

    .story-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #eee;
    }

    .story-program {
        background: #e9ecef;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.9rem;
    }

    .story-date {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .story-content {
        font-size: 1.1rem;
        line-height: 1.6;
    }

    .story-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #1a237e;
    }

    .page-header {
        background: linear-gradient(135deg, #1a237e, #0d47a1);
        color: white;
        padding: 4rem 0;
        margin-bottom: 3rem;
    }

    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .page-header p {
        font-size: 1.2rem;
        opacity: 0.9;
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 3rem 0;
        }

        .page-header h1 {
            font-size: 2rem;
        }

        .page-header p {
            font-size: 1.1rem;
        }

        .story-title {
            font-size: 1.3rem;
        }

        .story-content {
            font-size: 1rem;
        }
    }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1>Success Stories</h1>
                    <p>Read about the lives we've touched and the impact we've made in our community.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Stories Section -->
    <section class="success-stories-section py-5">
        <div class="container">
            <div class="row">
                <?php 
                $iconIndex = 0;
                while($story = mysqli_fetch_assoc($stories_result)): 
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 story-card">
                        <div class="card-body">
                            <div class="story-icon">
                                <i class="fas <?php echo $icons[$iconIndex % count($icons)]; ?>"></i>
                            </div>
                            <h5 class="card-title text-center"><?php echo htmlspecialchars($story['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($story['content']); ?></p>
                            <div class="story-meta">
                                <div class="story-program">
                                    <i class="fas fa-graduation-cap"></i> 
                                    <?php echo htmlspecialchars($story['program_name'] ?? 'General Program'); ?>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> 
                                    <?php echo date('F j, Y', strtotime($story['created_at'])); ?>
                                </small>
                            </div>
                            <?php if($story['child_id']): ?>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> 
                                    <?php echo htmlspecialchars($story['first_name'] . ' ' . $story['last_name']); ?>
                                </small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php 
                $iconIndex++;
                endwhile; 
                ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/performance.js"></script>
</body>
</html> 