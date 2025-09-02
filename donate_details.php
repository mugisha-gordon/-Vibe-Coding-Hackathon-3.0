<?php
require_once "config/database.php";
session_start();

// Redirect if no amount is set
if(!isset($_SESSION['donation_amount'])) {
    header("Location: donate.php");
    exit();
}

$amount = $_SESSION['donation_amount'];

// Handle form submission
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $city = trim($_POST["city"]);
    // $country = trim($_POST["country"]); // Removing country as it's not in the provided form HTML
    $message = trim($_POST["message"]);
    
    // Basic validation (can be enhanced)
    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($city)) {
        // Handle error - maybe set a session flash message
        $_SESSION['error_message'] = 'Please fill in all required fields.';
        header("Location: donate_details.php"); // Redirect back to the form
        exit();
    }

    // Store in session
    $_SESSION['donor_details'] = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'city' => $city,
        // 'country' => $country, // Removing country
        'message' => $message
    ];

    // Redirect to payment page
    header("Location: donate_payment.php");
    exit();
}

// Get error message from session if any
$error_message = '';
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Clear the message after displaying
}

// Calculate processing fee and total
$processing_fee = $amount * 0.029 + 0.30; // Example fee, adjust if needed
$total_amount = $amount + $processing_fee;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Details - Bumbobi Child Support Uganda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/donation.css"> <!-- Link the new CSS -->
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <!-- Progress Tracker -->
        <div class="progress-tracker">
            <div class="progress-step completed">
                <i class="fas fa-check"></i>
            </div>
            <div class="progress-step completed">
                <i class="fas fa-check"></i>
            </div>
            <div class="progress-step active">
                <span>3</span>
            </div>
            <div class="progress-step">
                <span>4</span>
            </div>
        </div>

        <div class="donation-details">
            <div class="row">
                <div class="col-lg-8">
                    <h2>Your Contact Information</h2>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Address *</label>
                                <input type="text" class="form-control" id="address" name="address" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City *</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="postal_code" class="form-label">Postal Code *</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message (Optional)</label>
                            <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="donate_amount.php" class="btn btn-outline-secondary">Back to Amount</a>
                            <button type="submit" class="btn btn-primary">Proceed to Payment</button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4">
                    <div class="payment-summary">
                        <h3 class="mb-4">Donation Summary</h3>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Donation Amount:</span>
                            <strong>$<?php echo number_format($amount, 2); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Estimated Fee:</span>
                            <strong>$<?php echo number_format($processing_fee, 2); ?></strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total Estimated:</span>
                            <strong>$<?php echo number_format($total_amount, 2); ?></strong>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Your donation helps us provide essential support to children in need.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Loading animation - remove or adapt if not needed with new design
        window.addEventListener('load', () => {
            const loading = document.querySelector('.loading');
            if (loading) {
                loading.style.display = 'none';
            }
        });
    </script>
</body>
</html> 