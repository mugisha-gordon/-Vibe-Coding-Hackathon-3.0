<?php
require_once "config/database.php";
require_once "config/performance_monitor.php";
// Initialize the session
session_start();

// Check for logout status
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    // Clear any remaining session data
    $_SESSION = array();
    session_destroy();
    
    // Redirect to remove the logout parameter from URL
    header("Location: index.php");
    exit();
}

$monitor = new PerformanceMonitor();
$monitor->start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$isAdmin = $isLoggedIn && isset($_SESSION["role"]) && $_SESSION["role"] === "admin";

// Fetch success stories
$stories_sql = "SELECT * FROM success_stories ORDER BY id DESC LIMIT 3";
$stories_result = mysqli_query($conn, $stories_sql);

// Fetch programs
$programs_sql = "SELECT * FROM programs ORDER BY name ASC";
$programs_result = mysqli_query($conn, $programs_sql);

// Initialize statistics array with default values
$stats = array(
    'total_children' => 0,
    'total_programs' => 0,
    'total_staff' => 0,
    'total_volunteers' => 0,
    'total_board_members' => 0,
    'total_events' => 0,
    'total_donations' => 0,
    'total_subscribers' => 0
);

// Function to safely get count
if (!function_exists('getTableCount')) {
    function getTableCount($conn, $table) {
        try {
            $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM $table");
            return $result ? mysqli_fetch_assoc($result)['count'] : 0;
        } catch (Exception $e) {
            return 0;
        }
    }
}

// Get counts safely
$stats['total_children'] = getTableCount($conn, 'children');
$stats['total_programs'] = getTableCount($conn, 'programs');
$stats['total_staff'] = getTableCount($conn, 'staff');
$stats['total_volunteers'] = getTableCount($conn, 'volunteers');
$stats['total_board_members'] = getTableCount($conn, 'board_members');
$stats['total_events'] = getTableCount($conn, 'events');
$stats['total_donations'] = getTableCount($conn, 'donations');
$stats['total_subscribers'] = getTableCount($conn, 'newsletter_subscribers');

// Function to safely execute query
if (!function_exists('safeQuery')) {
    function safeQuery($conn, $sql) {
        try {
            return mysqli_query($conn, $sql);
        } catch (Exception $e) {
            return false;
        }
    }
}

// Fetch recent events with error handling
$events_result = false;
$events_sql = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3";
if (safeQuery($conn, "SELECT 1 FROM events LIMIT 1")) {
    $events_result = safeQuery($conn, $events_sql);
}

// Fetch recent donations with error handling
$donations_result = false;
$donations_sql = "SELECT d.*, u.first_name, u.last_name FROM donations d 
                 LEFT JOIN users u ON d.user_id = u.id 
                 ORDER BY d.donation_date DESC LIMIT 3";
if (safeQuery($conn, "SELECT 1 FROM donations LIMIT 1")) {
    $donations_result = safeQuery($conn, $donations_sql);
}

// Fetch recent volunteers with error handling
$volunteers_result = false;
$volunteers_sql = "SELECT * FROM volunteers WHERE status = 'Active' ORDER BY created_at DESC LIMIT 3";
if (safeQuery($conn, "SELECT 1 FROM volunteers LIMIT 1")) {
    $volunteers_result = safeQuery($conn, $volunteers_sql);
}

$metrics = $monitor->getPerformanceMetrics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bumbobi Child Support Uganda - Making a Difference</title>
    
    <!-- Preload critical resources -->
    <link rel="preload" href="assets/css/style.css" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" as="style">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" as="style">
    
    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    
    <!-- DNS prefetch -->
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    
    <!-- Main CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>
    <?php include 'includes/loading.php'; ?>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-slider">
            <div class="hero-slide active" style="background-image: url('assets/images/hero-1.png');">
                <div class="container">
                    <div class="row align-items-center min-vh-100">
                        <div class="col-lg-6">
                            <div class="hero-content" data-aos="fade-right">
                                <h1 class="display-4 fw-bold mb-4">Making a Difference in Children's Lives</h1>
                                <p class="lead mb-4">Join us in our mission to provide support, education, and hope to vulnerable children in Uganda.</p>
                                <div class="d-flex gap-3">
                                    <a href="donate.php" class="btn btn-primary btn-lg">Donate Now</a>
                                    <a href="volunteer.php" class="btn btn-outline-light btn-lg">Volunteer</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero-slide" style="background-image: url('assets/images/hero-2.png');">
                <div class="container">
                    <div class="row align-items-center min-vh-100">
                        <div class="col-lg-6">
                            <div class="hero-content" data-aos="fade-right">
                                <h1 class="display-4 fw-bold mb-4">Empowering Youth Through Education</h1>
                                <p class="lead mb-4">Providing quality education and skills training to help children build a better future.</p>
                                <div class="d-flex gap-3">
                                    <a href="programs.php" class="btn btn-primary btn-lg">Our Programs</a>
                                    <a href="about.php" class="btn btn-outline-light btn-lg">Learn More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero-slide" style="background-image: url('assets/images/hero-3.jpg');">
                <div class="container">
                    <div class="row align-items-center min-vh-100">
                        <div class="col-lg-6">
                            <div class="hero-content" data-aos="fade-right">
                                <h1 class="display-4 fw-bold mb-4">Building a Stronger Community</h1>
                                <p class="lead mb-4">Working together to create lasting positive change in our community.</p>
                                <div class="d-flex gap-3">
                                    <a href="events.php" class="btn btn-primary btn-lg">Join Events</a>
                                    <a href="contact.php" class="btn btn-outline-light btn-lg">Get Involved</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero-slide" style="background-image: url('assets/images/hero-4.png');">
                <div class="container">
                    <div class="row align-items-center min-vh-100">
                        <div class="col-lg-6">
                            <div class="hero-content" data-aos="fade-right">
                                <h1 class="display-4 fw-bold mb-4">Creating Lasting Impact</h1>
                                <p class="lead mb-4">Together we can make a difference in the lives of children and their communities.</p>
                                <div class="d-flex gap-3">
                                    <a href="impact.php" class="btn btn-primary btn-lg">Our Impact</a>
                                    <a href="contact.php" class="btn btn-outline-light btn-lg">Join Us</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-controls">
            <button class="hero-prev"><i class="fas fa-chevron-left"></i></button>
            <div class="hero-dots"></div>
            <button class="hero-next"><i class="fas fa-chevron-right"></i></button>
        </div>
    </section>

    <!-- Activity Gallery Section -->
    <section class="activity-gallery py-5">
        <div class="container">
            <h2 class="text-center mb-5">Our Activities</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-image">
                            <img src="assets/images/education.png" alt="Education Programs" class="img-fluid">
                        </div>
                        <div class="activity-content">
                            <h4>Education Programs</h4>
                            <p>Providing quality education to underprivileged children</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-image">
                            <img src="assets/images/health.jpg" alt="Health Care" class="img-fluid">
                        </div>
                        <div class="activity-content">
                            <h4>Health Care</h4>
                            <p>Ensuring access to basic healthcare services</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-image">
                            <img src="assets/images/sports.png" alt="Sports Activities" class="img-fluid">
                        </div>
                        <div class="activity-content">
                            <h4>Sports Activities</h4>
                            <p>Promoting physical fitness and team spirit</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-image">
                            <img src="assets/images/arts.png" alt="Arts & Crafts" class="img-fluid">
                        </div>
                        <div class="activity-content">
                            <h4>Arts & Crafts</h4>
                            <p>Nurturing creativity and artistic expression</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-image">
                            <img src="assets/images/music.jpeg" alt="Music Programs" class="img-fluid">
                        </div>
                        <div class="activity-content">
                            <h4>Music Programs</h4>
                            <p>Developing musical talents and appreciation</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-image">
                            <img src="assets/images/community-work.jpg" alt="Community Outreach" class="img-fluid">
                        </div>
                        <div class="activity-content">
                            <h4>Community Outreach</h4>
                            <p>Building stronger community connections</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-image">
                            <img src="assets/images/mentoring.png" alt="Mentoring" class="img-fluid">
                        </div>
                        <div class="activity-content">
                            <h4>Mentoring</h4>
                            <p>Guiding youth towards a brighter future</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-image">
                            <img src="assets/images/workshops.png" alt="Skill Workshops" class="img-fluid">
                        </div>
                        <div class="activity-content">
                            <h4>Skill Workshops</h4>
                            <p>Teaching practical life skills</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-image">
                            <img src="assets/images/economic.jpg" alt="Cultural Events" class="img-fluid">
                        </div>
                        <div class="activity-content">
                            <h4>Cultural Events</h4>
                            <p>Celebrating and preserving cultural heritage</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
    /* Hero Section Styles */
    .hero-section {
        position: relative;
        height: 100vh;
        overflow: hidden;
    }

    .hero-slider {
        position: relative;
        height: 100%;
    }

    .hero-slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        opacity: 0;
        transition: opacity 1.5s ease-in-out;
    }

    .hero-slide.active {
        opacity: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        color: white;
        padding: 2rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    .hero-content h1 {
        font-size: 3.5rem;
        line-height: 1.2;
        margin-bottom: 1.5rem;
    }

    .hero-content p {
        font-size: 1.25rem;
        margin-bottom: 2rem;
    }

    .hero-controls {
        position: absolute;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        align-items: center;
        gap: 1rem;
        z-index: 3;
        background: rgba(0, 0, 0, 0.3);
        padding: 0.5rem 1rem;
        border-radius: 50px;
    }

    .hero-prev,
    .hero-next {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .hero-prev:hover,
    .hero-next:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    .hero-dots {
        display: flex;
        gap: 0.5rem;
    }

    .hero-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .hero-dot.active {
        background: white;
        transform: scale(1.2);
    }

    @media (max-width: 991px) {
        .hero-content {
            text-align: center;
            padding: 1.5rem;
        }

        .hero-content h1 {
            font-size: 2.5rem;
        }

        .hero-content p {
            font-size: 1.1rem;
        }
    }

    @media (max-width: 576px) {
        .hero-content h1 {
            font-size: 2rem;
        }

        .hero-content p {
            font-size: 1rem;
        }

        .hero-controls {
            bottom: 1rem;
        }
    }

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

    /* Activity Gallery Styles */
    .activity-gallery {
        background-color: #f8f9fa;
    }

    .activity-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .activity-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .activity-image {
        position: relative;
        overflow: hidden;
        height: 250px;
    }

    .activity-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .activity-card:hover .activity-image img {
        transform: scale(1.1);
    }

    .activity-content {
        padding: 1.5rem;
        text-align: center;
    }

    .activity-content h4 {
        margin-bottom: 0.5rem;
        color: #333;
    }

    .activity-content p {
        color: #666;
        margin-bottom: 0;
    }

    /* Enhanced Hero Transitions */
    .hero-slide {
        transition: all 1.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .hero-content {
        transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .hero-slide.active .hero-content {
        animation: fadeInUp 1s ease forwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Automatic Hero Transitions */
    .hero-slider {
        position: relative;
        height: 100%;
        transform-style: preserve-3d;
    }

    .hero-slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        opacity: 0;
        transform: scale(1.1) translateZ(-100px);
        transition: all 1.5s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: transform, opacity;
    }

    .hero-slide.active {
        opacity: 1;
        transform: scale(1) translateZ(0);
        z-index: 1;
    }

    .hero-slide.prev {
        transform: scale(0.9) translateZ(-200px);
        opacity: 0.5;
        z-index: 0;
    }

    .hero-slide.next {
        transform: scale(0.9) translateZ(-200px);
        opacity: 0.5;
        z-index: 0;
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const slider = document.querySelector('.hero-slider');
        const slides = document.querySelectorAll('.hero-slide');
        const dotsContainer = document.querySelector('.hero-dots');
        const prevBtn = document.querySelector('.hero-prev');
        const nextBtn = document.querySelector('.hero-next');
        let currentSlide = 0;
        let slideInterval;
        let isTransitioning = false;

        // Create dots
        slides.forEach((_, index) => {
            const dot = document.createElement('div');
            dot.classList.add('hero-dot');
            if (index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(index));
            dotsContainer.appendChild(dot);
        });

        const dots = document.querySelectorAll('.hero-dot');

        function updateSlider() {
            if (isTransitioning) return;
            isTransitioning = true;

            slides.forEach((slide, index) => {
                slide.classList.remove('active');
                dots[index].classList.remove('active');
            });

            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');

            setTimeout(() => {
                isTransitioning = false;
            }, 1500);
        }

        function goToSlide(index) {
            if (isTransitioning) return;
            currentSlide = index;
            updateSlider();
            resetInterval();
        }

        function nextSlide() {
            if (isTransitioning) return;
            currentSlide = (currentSlide + 1) % slides.length;
            updateSlider();
        }

        function prevSlide() {
            if (isTransitioning) return;
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            updateSlider();
        }

        function resetInterval() {
            clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, 5000);
        }

        // Event listeners
        prevBtn.addEventListener('click', () => {
            prevSlide();
            resetInterval();
        });

        nextBtn.addEventListener('click', () => {
            nextSlide();
            resetInterval();
        });

        // Touch events for mobile
        let touchStartX = 0;
        let touchEndX = 0;

        slider.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        });

        slider.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });

        function handleSwipe() {
            if (isTransitioning) return;
            const swipeThreshold = 50;
            if (touchEndX < touchStartX - swipeThreshold) {
                nextSlide();
            } else if (touchEndX > touchStartX + swipeThreshold) {
                prevSlide();
            }
            resetInterval();
        }

        // Start automatic slideshow
        resetInterval();

        // Pause slideshow when hovering over controls
        const controls = document.querySelector('.hero-controls');
        controls.addEventListener('mouseenter', () => clearInterval(slideInterval));
        controls.addEventListener('mouseleave', resetInterval);
    });
    </script>

    <!-- Statistics Section -->
    <section class="stats-section py-5 bg-light">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="stat-item">
                        <i class="fas fa-child fa-3x mb-3 text-primary"></i>
                        <h3 class="counter"><?php echo number_format($stats['total_children']); ?></h3>
                        <p>Children Supported</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="stat-item">
                        <i class="fas fa-user-tie fa-3x mb-3 text-primary"></i>
                        <h3 class="counter"><?php echo number_format($stats['total_staff']); ?></h3>
                        <p>Staff Members</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                        <h3 class="counter"><?php echo number_format($stats['total_volunteers']); ?></h3>
                        <p>Volunteers</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <i class="fas fa-user-friends fa-3x mb-3 text-primary"></i>
                        <h3 class="counter"><?php echo number_format($stats['total_board_members']); ?></h3>
                        <p>Board Members</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <i class="fas fa-calendar-alt fa-3x mb-3 text-primary"></i>
                        <h3 class="counter"><?php echo number_format($stats['total_events']); ?></h3>
                        <p>Events</p>
                    </div>
                </div>
                
                
            </div>
        </div>
    </section>



    <!-- Impact Map Section -->
    <section class="impact-map-section py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Our Impact</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31918.26709754367!2d32.59932655977606!3d0.23976195025542324!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x177d967331da276f%3A0x9917c1ce2e230f6!2sMunyonyo%2C%20Kampala!5e0!3m2!1sen!2sug!4v1747601744274!5m2!1sen!2sug" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>                    </div>
                </div>
                <div class="col-md-6">
                    <div class="impact-stats">
                        <h3 class="mb-4">Our Reach</h3>
                        <div class="impact-grid">
                            <div class="impact-item">
                                <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                                <div class="impact-info">
                                    <h4>5</h4>
                                    <p>Districts Covered</p>
                                </div>
                            </div>
                            <div class="impact-item">
                                <i class="fas fa-school fa-2x text-primary"></i>
                                <div class="impact-info">
                                    <h4>10</h4>
                                    <p>Partner Schools</p>
                                </div>
                            </div>
                            <div class="impact-item">
                                <i class="fas fa-home fa-2x text-primary"></i>
                                <div class="impact-info">
                                    <h4>3</h4>
                                    <p>Community Centers</p>
                                </div>
                            </div>
                            <div class="impact-item">
                                <i class="fas fa-users fa-2x text-primary"></i>
                                <div class="impact-info">
                                    <h4>1000+</h4>
                                    <p>Families Supported</p>
                                </div>
                            </div>
                        </div>
                        <div class="impact-description mt-4">
                            <p>Our organization has been making a significant impact across Uganda, reaching communities in need and providing essential support and resources.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">What People Say</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 testimonial-card">
                        <div class="card-body">
                            <div class="testimonial-content">
                                <div class="testimonial-header">
                                    <div class="testimonial-avatar">
                                        <img src="assets/images/testimonials/sarah.jpg" alt="Sarah Johnson" class="rounded-circle">
                                    </div>
                                    <div class="testimonial-info">
                                        <h5>Sarah Johnson</h5>
                                        <small class="text-muted">Parent</small>
                                    </div>
                                </div>
                                <div class="testimonial-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <i class="fas fa-quote-left fa-2x text-primary mb-3"></i>
                                <p class="card-text">"The support and care provided by this organization has transformed my child's life. We are forever grateful for the opportunities they've provided."</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 testimonial-card">
                        <div class="card-body">
                            <div class="testimonial-content">
                                <div class="testimonial-header">
                                    <div class="testimonial-avatar">
                                        <img src="assets/images/testimonials/michael.jpg" alt="Michael Brown" class="rounded-circle">
                                    </div>
                                    <div class="testimonial-info">
                                        <h5>Michael Brown</h5>
                                        <small class="text-muted">Volunteer</small>
                                    </div>
                                </div>
                                <div class="testimonial-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <i class="fas fa-quote-left fa-2x text-primary mb-3"></i>
                                <p class="card-text">"Volunteering here has been one of the most rewarding experiences of my life. The impact we make is incredible, and the team is amazing."</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 testimonial-card">
                        <div class="card-body">
                            <div class="testimonial-content">
                                <div class="testimonial-header">
                                    <div class="testimonial-avatar">
                                        <img src="assets/images/testimonials/emma.jpg" alt="Emma Wilson" class="rounded-circle">
                                    </div>
                                    <div class="testimonial-info">
                                        <h5>Emma Wilson</h5>
                                        <small class="text-muted">Former Student</small>
                                    </div>
                                </div>
                                <div class="testimonial-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <i class="fas fa-quote-left fa-2x text-primary mb-3"></i>
                                <p class="card-text">"The programs and support provided have helped me achieve my dreams. I'm now studying at university and couldn't be more grateful."</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section py-5 bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2>Make a Difference Today</h2>
                    <p class="lead mb-0">Join us in our mission to support and empower children in need.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="donate.php" class="btn btn-light btn-lg">Donate Now</a>
                    <a href="volunteer.php" class="btn btn-outline-light btn-lg ms-2">Volunteer</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h2>Stay Updated</h2>
                    <p class="lead mb-4">Subscribe to our newsletter for the latest news and updates.</p>
                    <form action="newsletter.php" method="POST" class="newsletter-form">
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Enter your email" required>
                            <button class="btn btn-primary" type="submit">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Script -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY"></script>
    <script>
    function initMap() {
        const map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: 0.3476, lng: 32.5825 },
            zoom: 7,
            styles: [
                {
                    "featureType": "all",
                    "elementType": "geometry",
                    "stylers": [{"color": "#f5f5f5"}]
                },
                {
                    "featureType": "water",
                    "elementType": "geometry",
                    "stylers": [{"color": "#e9e9e9"}, {"lightness": 17}]
                }
            ]
        });

        const locations = [
            { lat: 0.3476, lng: 32.5825, title: 'Main Office', icon: 'assets/images/map/marker-main.png' },
            { lat: 0.3136, lng: 32.5811, title: 'Community Center 1', icon: 'assets/images/map/marker-center.png' },
            { lat: 0.2987, lng: 32.6251, title: 'Community Center 2', icon: 'assets/images/map/marker-center.png' }
        ];

        locations.forEach(location => {
            const marker = new google.maps.Marker({
                position: { lat: location.lat, lng: location.lng },
                map: map,
                title: location.title,
                animation: google.maps.Animation.DROP
            });

            const infoWindow = new google.maps.InfoWindow({
                content: `<div class="map-info-window">
                    <h5>${location.title}</h5>
                    <p>Click to learn more about our presence here.</p>
                </div>`
            });

            marker.addListener('click', () => {
                infoWindow.open(map, marker);
            });
        });
    }

    // Initialize map when page loads
    window.addEventListener('load', initMap);
    </script>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/performance.js"></script>
    <script>
        // Initialize Bootstrap components
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
            
            // Handle admin dropdown
            const adminDropdown = document.querySelector('.dropdown-toggle');
            if (adminDropdown) {
                adminDropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    const dropdown = bootstrap.Dropdown.getInstance(this);
                    if (dropdown) {
                        dropdown.toggle();
                    }
                });
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    const dropdowns = document.querySelectorAll('.dropdown-menu.show');
                    dropdowns.forEach(dropdown => {
                        bootstrap.Dropdown.getInstance(dropdown).hide();
                    });
                }
            });

            // Remove loading screen
            const loadingScreen = document.querySelector('.loading');
            if (loadingScreen) {
                loadingScreen.style.display = 'none';
            }

            // Handle header wrapping
            const header = document.querySelector('.navbar');
            const navItems = document.querySelectorAll('.nav-item');
            
            function adjustHeaderSpacing() {
                if (window.innerWidth > 991) {
                    navItems.forEach(item => {
                        const link = item.querySelector('.nav-link');
                        if (link) {
                            const text = link.textContent.trim();
                            if (text.length > 15) {
                                link.style.fontSize = '0.85rem';
                            }
                        }
                    });
                }
            }

            // Initial adjustment
            adjustHeaderSpacing();

            // Adjust on window resize
            window.addEventListener('resize', adjustHeaderSpacing);
        });
    </script>
</body>
</html> 