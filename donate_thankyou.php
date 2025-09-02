<?php
require_once "config/database.php";
session_start();

// Retrieve donation data from session if needed for display
$donation_amount = $_SESSION['donation_amount'] ?? 0;
$donor_details = $_SESSION['donor_details'] ?? [];

// Clear donation session data after displaying thank you
unset($_SESSION['donation_amount']);
unset($_SESSION['donor_details']);
unset($_SESSION['transaction']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Bumbobi Child Support Uganda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/donation.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="thank-you-section">
        <div class="container">
            <div class="confirmation-card">
                <i class="fas fa-heart confirmation-icon"></i>
                <h1>Your Gift Will Change Lives!</h1>
                <p class="lead mb-4">Thank you for your incredible generosity. Your donation of <strong>$<?php echo number_format($donation_amount, 2); ?></strong> will directly support our programs and bring hope to underprivileged children in Bumbobi.</p>
                
                <div class="alert alert-success">
                    <i class="fas fa-envelope me-2"></i>
                    A confirmation and receipt email has been sent to <strong><?php echo htmlspecialchars($donor_details['email'] ?? ''); ?></strong>
                </div>

                <div class="share-options">
                    <h4>Spread the Word of Hope!</h4>
                    <p class="text-muted mb-3">Share your support and inspire others to make a difference.</p>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . '/donate.php'); ?>" 
                       class="share-button facebook" target="_blank">
                        <i class="fab fa-facebook-f"></i> Share on Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode('I just helped change a child\'s life with a donation to Bumbobi Child Support Uganda!'); ?>&url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . '/donate.php'); ?>" 
                       class="share-button twitter" target="_blank">
                        <i class="fab fa-twitter"></i> Share on Twitter
                    </a>
                    <a href="https://wa.me/?text=<?php echo urlencode('I just supported Bumbobi Child Support Uganda to help underprivileged children! Join me: http://' . $_SERVER['HTTP_HOST'] . '/donate.php'); ?>" 
                       class="share-button whatsapp" target="_blank">
                        <i class="fab fa-whatsapp"></i> Share on WhatsApp
                    </a>
                </div>
            </div>

            <div class="next-steps">
                <h3>Continue Your Journey of Impact</h3>
                <div class="row">
                    <div class="col-md-4">
                        <div class="next-steps-card">
                            <i class="fas fa-newspaper fa-2x mb-3 text-primary"></i>
                            <h4>Stay Connected</h4>
                            <p class="text-light-text">Subscribe to our newsletter for inspiring stories and updates on your impact.</p>
                            <a href="newsletter.php" class="btn btn-outline-primary">Subscribe Now</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="next-steps-card">
                            <i class="fas fa-handshake fa-2x mb-3 text-primary"></i>
                            <h4>Become a Volunteer</h4>
                            <p class="text-light-text">Join our dedicated team and make a direct impact in the lives of children.</p>
                            <a href="volunteer.php" class="btn btn-outline-primary">Learn More</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="next-steps-card">
                            <i class="fas fa-calendar-check fa-2x mb-3 text-primary"></i>
                            <h4>Explore Our Events</h4>
                            <p class="text-light-text">Discover upcoming events and opportunities to get involved with our community.</p>
                            <a href="events.php" class="btn btn-outline-primary">View Events</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('load', () => {
            const loading = document.querySelector('.loading');
            if (loading) {
                loading.style.display = 'none';
            }
        });
    </script>
</body>
</html> 