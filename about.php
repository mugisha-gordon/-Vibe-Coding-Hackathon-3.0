<?php
require_once "config/database.php";
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Bumbobi Child Support Uganda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- About Hero Section -->
    <section class="about-hero slide-in-left">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1>About Bumbobi Child Support Uganda</h1>
                    <p class="lead">Enhancing safety, survival and learning of vulnerable children</p>
                    <p>Child Support Uganda (C.S.U) is a non-profit government organization striving to support vulnerable children, communities, and disabled children of Uganda. We work with schools that have needs and our acts lead to great empowerment of the most vulnerable local Ugandans that need help.</p>
                </div>
                <div class="col-md-6">
                    <div class="img-hover-zoom">
                        <img src="assets/images/hero-1.png" alt="About CSU" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Support Section -->
    <section class="our-support slide-in-right">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="img-hover-zoom">
                        <img src="assets/images/support-activities.jpg" alt="Support Activities" class="img-fluid rounded mb-4">
                    </div>
                </div>
                <div class="col-md-6">
                    <h2>Our Support Services</h2>
                    <p>We provide comprehensive support through:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-primary"></i> Food and nutrition support</li>
                        <li><i class="fas fa-check-circle text-primary"></i> Shelter and housing assistance</li>
                        <li><i class="fas fa-check-circle text-primary"></i> Sanitation facilities</li>
                        <li><i class="fas fa-check-circle text-primary"></i> Educational tools and resources</li>
                        <li><i class="fas fa-check-circle text-primary"></i> School fees support</li>
                        <li><i class="fas fa-check-circle text-primary"></i> Medical care and assistance</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Four Pillars Section -->
    <section class="four-pillars scale-in">
        <div class="container">
            <h2 class="text-center mb-5">Our Four Pillars</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="pillar-card">
                        <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                        <h3>Education</h3>
                        <p>Quality education for all children</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="pillar-card">
                        <i class="fas fa-heartbeat fa-3x mb-3"></i>
                        <h3>Health</h3>
                        <p>Accessible healthcare services</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="pillar-card">
                        <i class="fas fa-building fa-3x mb-3"></i>
                        <h3>Infrastructure</h3>
                        <p>Basic amenities and facilities</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="pillar-card">
                        <i class="fas fa-seedling fa-3x mb-3"></i>
                        <h3>Sustainability</h3>
                        <p>Long-term development solutions</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Who We Are Section -->
    <section class="who-we-are slide-in-left">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>Who We Are</h2>
                    <p>We're a non-profit making organisation working with communities to lift themselves out of poverty for good. We believe in the power of children by making sure they survive, learn and get protection.</p>
                    <p>Our projects are based on four pillars derived from the most pressing needs of people living in hard-to-reach rural areas of Wakiso district, while considering the United Nations Millennium Development Goals.</p>
                </div>
                <div class="col-md-6">
                    <div class="img-hover-zoom">
                        <img src="assets/images/community-work.jpg" alt="Community Work" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision Mission Section -->
    <section class="vision-mission scale-in">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3>Our Vision</h3>
                            <p>We envision a society where every child gets the opportunity to quality education, healthcare, safe and clean water as well as better shelter.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3>Our Mission</h3>
                            <p>We're focused at creating an enabling environment where every child is safe and protected against any form of violence, has an opportunity to learn from a quality education centre, has access to quality healthcare, safe and clean water as well as living decently.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values slide-in-right">
        <div class="container">
            <h2 class="text-center mb-5">Our Values</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="value-card">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h3>Family</h3>
                        <p>At C.S.U we're a family despite our different colors and backgrounds, we cherish living and working together.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="value-card">
                        <i class="fas fa-hands-helping fa-3x mb-3"></i>
                        <h3>Team Work</h3>
                        <p>At C.S.U, it's never 'I' but we, we work together and individual's success is a team's success.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="value-card">
                        <i class="fas fa-balance-scale fa-3x mb-3"></i>
                        <h3>Transparency</h3>
                        <p>Every penny counts and goes to the rightful owners. We achieve much at the lowest costs possible.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Major Activities -->
    <section class="activities scale-in">
        <div class="container">
            <h2 class="text-center mb-5">Major Activities</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="img-hover-zoom">
                            <img src="assets/images/sponsorship.jpg" alt="Child Sponsorship" class="img-fluid rounded mb-3">
                        </div>
                        <h3>Child Sponsorship</h3>
                        <p>We assess and register OVCs, following strict selection criteria and working with local leaders to identify those in need. We've grown from 15 children in July 2020 to over 30 sponsored children.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="img-hover-zoom">
                            <img src="assets/images/care.jpg" alt="Care and Support" class="img-fluid rounded mb-3">
                        </div>
                        <h3>Care and Support</h3>
                        <p>Providing essential care and support to OVCs, including food, shelter, and basic necessities. We focus on becoming parents and caretakers for those whose parents died or are unable to look after themselves.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="img-hover-zoom">
                            <img src="assets/images/economic.jpg" alt="Economic Empowerment" class="img-fluid rounded mb-3">
                        </div>
                        <h3>Economic Empowerment</h3>
                        <p>Empowering OVC households economically through resource mapping and VSLA/SILC groups to break the cycle of poverty.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Building Projects Section -->
    <section class="building-projects slide-in-left">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>Building Projects</h2>
                    <p>We have worked tirelessly through cost sharing to improve the living conditions of vulnerable families. Our achievements include:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-primary"></i> Repaired and rebuilt two houses</li>
                        <li><i class="fas fa-check-circle text-primary"></i> Installed proper flooring to prevent jiggers</li>
                        <li><i class="fas fa-check-circle text-primary"></i> Repaired and plastered walls</li>
                        <li><i class="fas fa-check-circle text-primary"></i> Installed strong doors for protection</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <div class="img-hover-zoom">
                        <img src="assets/images/building-projects.jpg" alt="Building Projects" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Future Plans -->
    <section class="future-plans scale-in">
        <div class="container">
            <h2 class="text-center mb-5">Plans for the Next 3-5 Years</h2>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-arrow-right text-primary"></i> Scale up outreach program to 1000 children</li>
                        <li><i class="fas fa-arrow-right text-primary"></i> Establish a health centre</li>
                        <li><i class="fas fa-arrow-right text-primary"></i> Build Bumbobi Child Support Uganda schools</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-arrow-right text-primary"></i> Provide clean water to 100 villages</li>
                        <li><i class="fas fa-arrow-right text-primary"></i> Establish income generating projects</li>
                        <li><i class="fas fa-arrow-right text-primary"></i> Partner with global organizations</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section slide-in-right">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2>Contact Us</h2>
                    <p><i class="fas fa-envelope"></i> ugandachilds@gmail.com</p>
                    <p><i class="fas fa-phone"></i> +256707907918</p>
                    <p><i class="fas fa-phone"></i> +256774586279</p>
                </div>
                <div class="col-md-6">
                    <h2>Get Involved</h2>
                    <p>Join us in our mission to support vulnerable children in Uganda. Your contribution can make a significant difference in their lives.</p>
                    <a href="donate.php" class="btn btn-primary">Donate Now</a>
                    <a href="volunteer.php" class="btn btn-outline-primary">Volunteer</a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/animations.js"></script>
</body>
</html> 