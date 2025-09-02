<?php
require_once "config/database.php";
session_start();

// Fetch programs from database
$programs_sql = "SELECT * FROM programs ORDER BY name ASC";
$programs_result = mysqli_query($conn, $programs_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programs - Bumbobi Child Support Uganda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Programs Hero Section -->
    <section class="programs-hero">
        <div class="container">
            <h1>Our Programs</h1>
            <p class="lead">Discover how Bumbobi Child Support Uganda is making a difference in the lives of vulnerable children in Uganda through our comprehensive programs and community initiatives.</p>
        </div>
    </section>

    <!-- Four Pillars Section -->
    <section class="pillars-section">
        <div class="container">
            <h2 class="text-center mb-5">Our Four Pillars</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="pillar-card">
                        <i class="fas fa-graduation-cap"></i>
                        <h3>Education</h3>
                        <p>Providing quality basic education to vulnerable children in hard-to-reach rural areas, ensuring every child has access to learning opportunities.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="pillar-card">
                        <i class="fas fa-heartbeat"></i>
                        <h3>Health</h3>
                        <p>Ensuring access to quality and affordable healthcare services in rural areas, promoting child health and well-being.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="pillar-card">
                        <i class="fas fa-building"></i>
                        <h3>Infrastructure</h3>
                        <p>Building and improving living conditions for vulnerable families, creating safe and sustainable environments.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="pillar-card">
                        <i class="fas fa-seedling"></i>
                        <h3>Sustainability</h3>
                        <p>Creating lasting impact through community development and empowerment, ensuring long-term positive change.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Major Activities Section -->
    <section class="activities-section">
        <div class="container">
            <h2 class="text-center mb-5">Major Activities</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="activity-card">
                        <i class="fas fa-child"></i>
                        <h3>Child Sponsorship</h3>
                        <p>We reach out to villages to assess and register OVCs, following strict selection criteria and working with local leaders to identify those in need.</p>
                        <ul>
                            <li>Village outreach and assessment</li>
                            <li>OVC registration and verification</li>
                            <li>Sponsorship program management</li>
                            <li>Regular monitoring and support</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <i class="fas fa-hands-helping"></i>
                        <h3>Care and Support</h3>
                        <p>Providing essential care and support to OVCs, including food, shelter, and basic necessities to ensure their well-being and development.</p>
                        <ul>
                            <li>Emergency support services</li>
                            <li>Food security initiatives</li>
                            <li>Basic healthcare access</li>
                            <li>Shelter improvements</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <i class="fas fa-chart-line"></i>
                        <h3>Economic Empowerment</h3>
                        <p>Empowering OVC households economically through resource mapping and VSLA/SILC groups to break the cycle of poverty.</p>
                        <ul>
                            <li>Resource mapping and assessment</li>
                            <li>VSLA/SILC group formation</li>
                            <li>Income generation training</li>
                            <li>Small business support</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Future Plans Section -->
    <section class="future-plans-section">
        <div class="container">
            <h2 class="text-center mb-5">Plans for the Next 3-5 Years</h2>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-arrow-right"></i> Scale up outreach program to 1000 children</li>
                        <li><i class="fas fa-arrow-right"></i> Establish a health centre</li>
                        <li><i class="fas fa-arrow-right"></i> Build Bumbobi Child Support Uganda schools</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-arrow-right"></i> Provide clean water to 100 villages</li>
                        <li><i class="fas fa-arrow-right"></i> Establish income generating projects</li>
                        <li><i class="fas fa-arrow-right"></i> Partner with global organizations</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Child Sponsorship Program -->
    <section class="sponsorship-program">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>Child Sponsorship Program</h2>
                    <p>Our child sponsorship program is the cornerstone of our work. We:</p>
                    <ul>
                        <li>Reach out to villages to assess and register OVCs</li>
                        <li>Follow strict sponsorship selection criteria</li>
                        <li>Work with local leaders to identify those in need</li>
                        <li>Conduct household visits to understand living conditions</li>
                        <li>Support education, shelter, and basic needs</li>
                    </ul>
                    <a href="donate.php" class="btn btn-primary">Sponsor a Child</a>
                </div>
                <div class="col-md-6">
                    <img src="assets/images/sponsorship.jpg" alt="Child Sponsorship" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </section>

    <!-- Care and Support Program -->
    <section class="care-program">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="assets/images/care.jpg" alt="Care and Support" class="img-fluid rounded">
                </div>
                <div class="col-md-6">
                    <h2>Care and Support Program</h2>
                    <p>We provide comprehensive care and support to OVCs through:</p>
                    <ul>
                        <li>Emergency support services</li>
                        <li>Food security initiatives</li>
                        <li>Basic healthcare access</li>
                        <li>Shelter improvements</li>
                        <li>Family planning support</li>
                    </ul>
                    <a href="donate.php" class="btn btn-primary">Support Our Work</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Economic Empowerment Program -->
    <section class="empowerment-program">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>Economic Empowerment Program</h2>
                    <p>We break the cycle of poverty through:</p>
                    <ul>
                        <li>Resource mapping and assessment</li>
                        <li>VSLA/SILC group formation</li>
                        <li>Income generation training</li>
                        <li>Small business support</li>
                        <li>Agricultural development</li>
                    </ul>
                    <a href="volunteer.php" class="btn btn-primary">Get Involved</a>
                </div>
                <div class="col-md-6">
                    <img src="assets/images/economic.jpg" alt="Economic Empowerment" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </section>

    <!-- Community Education Program -->
    <section class="education-program">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="assets/images/hero-4.png" alt="Community Education" class="img-fluid rounded">
                </div>
                <div class="col-md-6">
                    <h2>Community Education Program</h2>
                    <p>We work to protect children's rights through:</p>
                    <ul>
                        <li>Community training on children's rights</li>
                        <li>Awareness campaigns</li>
                        <li>Child protection initiatives</li>
                        <li>Advocacy and policy engagement</li>
                        <li>Family support services</li>
                    </ul>
                    <a href="contact.php" class="btn btn-primary">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8 mx-auto text-center">
                    <h2>Join Us in Making a Difference</h2>
                    <p>Your support can help us reach more vulnerable children and create lasting change in their lives.</p>
                    <div class="cta-buttons">
                        <a href="donate.php" class="btn btn-primary">Donate Now</a>
                        <a href="volunteer.php" class="btn btn-outline-primary">Volunteer</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 