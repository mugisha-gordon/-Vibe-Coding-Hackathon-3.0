<?php
require_once "config/database.php";
session_start();

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $name = trim($_POST["name"]);
    $preferences = isset($_POST["preferences"]) ? implode(", ", $_POST["preferences"]) : "";
    
    if (empty($email)) {
        $error = "Please enter your email address.";
    } else {
        // Check if email already exists
        $check_sql = "SELECT id FROM newsletter_subscribers WHERE email = ?";
        if ($check_stmt = mysqli_prepare($conn, $check_sql)) {
            mysqli_stmt_bind_param($check_stmt, "s", $email);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $error = "This email is already subscribed to our newsletter.";
            } else {
                // Insert new subscriber
                $sql = "INSERT INTO newsletter_subscribers (email, name, preferences, status) VALUES (?, ?, ?, 'active')";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sss", $email, $name, $preferences);
                    if (mysqli_stmt_execute($stmt)) {
                        $success = "Thank you for subscribing to our newsletter!";
                    } else {
                        $error = "Something went wrong. Please try again later.";
                    }
                }
            }
        }
    }
}

// Fetch recent newsletters
$newsletters = [];
$sql = "SELECT * FROM newsletters ORDER BY publish_date DESC LIMIT 3";
$result = mysqli_query($conn, $sql);
if($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $newsletters[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter - Bumbobi Child Support Uganda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="page-header text-center">
        <div class="container">
            <h1>Newsletter</h1>
            <p class="lead">Stay updated with our latest news, events, and impact stories</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <!-- Newsletter Subscription -->
            <div class="content-card mb-5">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2>Subscribe to Our Newsletter</h2>
                        <p class="lead">Get regular updates about our work and the children we support.</p>
                        <p>By subscribing, you'll receive:</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-primary me-2"></i> Monthly impact reports</li>
                            <li><i class="fas fa-check text-primary me-2"></i> Event announcements</li>
                            <li><i class="fas fa-check text-primary me-2"></i> Success stories</li>
                            <li><i class="fas fa-check text-primary me-2"></i> Volunteer opportunities</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <div class="form-section">
                            <?php if($success): ?>
                                <div class="alert alert-success" role="alert">
                                    <?php echo $success; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($error): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>

                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label required-field">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Newsletter Preferences</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="preferences[]" value="Impact Reports" id="impact">
                                        <label class="form-check-label" for="impact">Impact Reports</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="preferences[]" value="Events" id="events">
                                        <label class="form-check-label" for="events">Events</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="preferences[]" value="Success Stories" id="stories">
                                        <label class="form-check-label" for="stories">Success Stories</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="preferences[]" value="Volunteer Opportunities" id="volunteer">
                                        <label class="form-check-label" for="volunteer">Volunteer Opportunities</label>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Subscribe Now</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Newsletters -->
            <div class="mb-5">
                <h2 class="text-center mb-4">Recent Newsletters</h2>
                <div class="card-grid">
                    <?php if(empty($newsletters)): ?>
                        <div class="content-card text-center">
                            <i class="fas fa-newspaper fa-3x mb-3 text-primary"></i>
                            <h3>No Newsletters Yet</h3>
                            <p>Our first newsletter will be published soon.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($newsletters as $newsletter): ?>
                            <div class="content-card">
                                <div class="newsletter-date mb-3">
                                    <i class="far fa-calendar-alt text-primary"></i>
                                    <?php echo date('F j, Y', strtotime($newsletter['publish_date'])); ?>
                                </div>
                                <h3><?php echo htmlspecialchars($newsletter['title']); ?></h3>
                                <p><?php echo htmlspecialchars($newsletter['description']); ?></p>
                                <a href="<?php echo htmlspecialchars($newsletter['file_url']); ?>" class="btn btn-outline-primary mt-3" target="_blank">
                                    <i class="fas fa-download me-2"></i>Download Newsletter
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 