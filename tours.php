<?php
require_once "config/database.php";
session_start();

$isLoggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$isAdmin = $isLoggedIn && isset($_SESSION["role"]) && $_SESSION["role"] === "admin";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tours & Travels - Bumbobi Child Support Uganda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold">Tours & Travels</h1>
                    <p class="lead">Experience the beauty of Uganda while supporting our cause.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Destinations -->
    <section class="destinations-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">Featured Destinations</h2>
            <div class="row">
                <!-- Destination 1 -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card destination-card h-100">
                        <img src="assets/images/tours/gorilla.jpg" class="card-img-top" alt="Gorilla Trekking">
                        <div class="card-body">
                            <h5 class="card-title">Gorilla Trekking</h5>
                            <p class="card-text">Experience the thrill of encountering mountain gorillas in their natural habitat.</p>
                            <div class="tour-details">
                                <p><i class="fas fa-clock"></i> Duration: 3 Days</p>
                                <p><i class="fas fa-users"></i> Group Size: 8 People</p>
                                <p><i class="fas fa-dollar-sign"></i> Price: $1,500</p>
                            </div>
                            <a href="#" class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                </div>

                <!-- Destination 2 -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card destination-card h-100">
                        <img src="assets/images/tours/safari.jpg" class="card-img-top" alt="Wildlife Safari">
                        <div class="card-body">
                            <h5 class="card-title">Wildlife Safari</h5>
                            <p class="card-text">Explore Uganda's diverse wildlife in its national parks and game reserves.</p>
                            <div class="tour-details">
                                <p><i class="fas fa-clock"></i> Duration: 5 Days</p>
                                <p><i class="fas fa-users"></i> Group Size: 6 People</p>
                                <p><i class="fas fa-dollar-sign"></i> Price: $2,000</p>
                            </div>
                            <a href="#" class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                </div>

                <!-- Destination 3 -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card destination-card h-100">
                        <img src="assets/images/tours/rafting.jpg" class="card-img-top" alt="White Water Rafting">
                        <div class="card-body">
                            <h5 class="card-title">White Water Rafting</h5>
                            <p class="card-text">Experience the thrill of rafting on the Nile River's rapids.</p>
                            <div class="tour-details">
                                <p><i class="fas fa-clock"></i> Duration: 1 Day</p>
                                <p><i class="fas fa-users"></i> Group Size: 8 People</p>
                                <p><i class="fas fa-dollar-sign"></i> Price: $150</p>
                            </div>
                            <a href="#" class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="why-choose-us py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose Us</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <i class="fas fa-hand-holding-heart fa-3x mb-3 text-primary"></i>
                        <h4>Support a Cause</h4>
                        <p>Your tour directly supports our child support programs.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <i class="fas fa-map-marked-alt fa-3x mb-3 text-primary"></i>
                        <h4>Local Expertise</h4>
                        <p>Experienced guides with deep knowledge of Uganda.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <i class="fas fa-shield-alt fa-3x mb-3 text-primary"></i>
                        <h4>Safe & Secure</h4>
                        <p>Your safety and comfort are our top priorities.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 