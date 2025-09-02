<?php
require_once "config/database.php";
require_once "config/performance_monitor.php";
session_start();

$monitor = new PerformanceMonitor();
$monitor->start();

$name = $email = $subject = $message = "";
$name_err = $email_err = $subject_err = $message_err = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Please enter a valid email address.";
        }
    }
    
    // Validate subject
    if (empty(trim($_POST["subject"]))) {
        $subject_err = "Please enter a subject.";
    } else {
        $subject = trim($_POST["subject"]);
    }
    
    // Validate message
    if (empty(trim($_POST["message"]))) {
        $message_err = "Please enter your message.";
    } else {
        $message = trim($_POST["message"]);
    }
    
    // Check input errors before inserting in database
    if (empty($name_err) && empty($email_err) && empty($subject_err) && empty($message_err)) {
        $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssss", $param_name, $param_email, $param_subject, $param_message);
            
            $param_name = $name;
            $param_email = $email;
            $param_subject = $subject;
            $param_message = $message;
            
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Your message has been sent successfully! We'll get back to you soon.";
                // Clear form fields
        $name = $email = $subject = $message = "";
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

$metrics = $monitor->getPerformanceMetrics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Bumbobi Child Support Uganda</title>
    
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
    .contact-header {
        background: linear-gradient(135deg, #1a237e, #0d47a1);
        color: white;
        padding: 4rem 0;
        margin-bottom: 3rem;
    }

    .contact-info-card {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        height: 100%;
        transition: transform 0.3s ease;
    }

    .contact-info-card:hover {
        transform: translateY(-5px);
    }

    .contact-icon {
        width: 60px;
        height: 60px;
        background: #1a237e;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .contact-form {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .form-label {
        font-weight: 500;
        color: #1a237e;
    }

    .form-control {
        border-radius: 10px;
        padding: 0.8rem 1rem;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #1a237e;
        box-shadow: 0 0 0 0.2rem rgba(26, 35, 126, 0.25);
    }

    .btn-send {
        background: #1a237e;
        color: white;
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-send:hover {
        background: #0d47a1;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .invalid-feedback {
        font-size: 0.875rem;
        color: #dc3545;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .contact-header {
            padding: 3rem 0;
        }

        .contact-info-card {
            margin-bottom: 1.5rem;
        }

        .contact-form {
            padding: 1.5rem;
        }
    }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Contact Header -->
    <section class="contact-header">
        <div class="container">
        <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                <h1>Contact Us</h1>
                    <p>Get in touch with us for any questions or inquiries</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="contact-info-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="contact-info-card text-center">
                        <div class="contact-icon mx-auto">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h4>Our Location</h4>
                        <p>Kampala, Uganda</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="contact-info-card text-center">
                        <div class="contact-icon mx-auto">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h4>Phone Number</h4>
                        <p>+256 774 586 279</p>
                    </div>
                        </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="contact-info-card text-center">
                        <div class="contact-icon mx-auto">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h4>Email Address</h4>
                        <p>info@childsupport-uganda.org</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form -->
    <section class="contact-form-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="contact-form">
                        <?php if (!empty($success_message)): ?>
                        <div class="success-message">
                            <?php echo $success_message; ?>
                        </div>
                        <?php endif; ?>

                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Your Name</label>
                                <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                                    <div class="invalid-feedback"><?php echo $name_err; ?></div>
                            </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                                    <div class="invalid-feedback"><?php echo $email_err; ?></div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Subject</label>
                                <input type="text" name="subject" class="form-control <?php echo (!empty($subject_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $subject; ?>">
                                <div class="invalid-feedback"><?php echo $subject_err; ?></div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Message</label>
                                <textarea name="message" rows="5" class="form-control <?php echo (!empty($message_err)) ? 'is-invalid' : ''; ?>"><?php echo $message; ?></textarea>
                                <div class="invalid-feedback"><?php echo $message_err; ?></div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-send">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/performance.js"></script>
</body>
</html> 