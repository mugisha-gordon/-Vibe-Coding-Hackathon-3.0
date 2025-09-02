<?php
require_once "config/database.php";
session_start();

// Redirect if no amount or donor details are set
if(!isset($_SESSION['donation_id']) || !isset($_SESSION['donation_amount']) || !isset($_SESSION['donor_details'])) {
    header("Location: donate.php");
    exit();
}

$donation_id = $_SESSION['donation_id'];
$amount = $_SESSION['donation_amount'];
$donor = $_SESSION['donor_details'];
$total_amount = $amount + ($amount * 0.029 + 0.30); // Including processing fee

// Generate a unique transaction reference
$tx_ref = 'DON-' . time() . '-' . rand(1000, 9999);

// Update donation record with transaction details
$update_sql = "UPDATE donations SET 
               payment_reference = ?,
               transaction_id = ?,
               payment_method = 'flutterwave',
               status = 'pending'
               WHERE id = ?";

$update_stmt = mysqli_prepare($conn, $update_sql);
mysqli_stmt_bind_param($update_stmt, "ssi", $tx_ref, $tx_ref, $donation_id);
mysqli_stmt_execute($update_stmt);

// Store transaction details in session
$_SESSION['transaction'] = [
    'tx_ref' => $tx_ref,
    'amount' => $total_amount,
    'status' => 'pending'
];

// Redirect to Flutterwave donation link
$flutterwave_donation_link = "https://flutterwave.com/donate/uhq4jl9ujptx";
header("Location: " . $flutterwave_donation_link);
exit();

// --- Original code for direct payment form (now removed) ---

?>

<!-- The HTML below is no longer necessary as we are redirecting -->
<!-- Keeping it commented out for reference, but it can be safely removed -->
<?php /*
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Donation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/donation.css">
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
            <div class="progress-step completed">
                <i class="fas fa-check"></i>
            </div>
            <div class="progress-step active">
                <span>4</span>
            </div>
        </div>

        <div class="payment-section">
            <div class="row">
                <div class="col-lg-8">
                    <h2 class="mb-4">Complete Your Donation</h2>
                    
                    <div class="payment-methods">
                        <h3 class="mb-4">Select Payment Method</h3>
                        
                        <div class="payment-method" onclick="initiatePayment()">
                            <div class="d-flex align-items-center">
                                <img src="assets/images/flutterwave-logo.png" alt="Flutterwave" height="40" class="me-3">
                                <div>
                                    <h4 class="mb-1">Pay with Flutterwave</h4>
                                    <p class="mb-0 text-muted">Secure payment processing</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="payment-summary">
                        <h3 class="mb-4">Donation Summary</h3>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Donation Amount:</span>
                            <strong>$<?php echo number_format($amount, 2); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Processing Fee:</span>
                            <strong>$<?php echo number_format($amount * 0.029 + 0.30, 2); ?></strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total Amount:</span>
                            <strong>$<?php echo number_format($total_amount, 2); ?></strong>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-lock me-2"></i>
                        Your payment information is secure and encrypted.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Flutterwave Script -->
    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Loading animation
        window.addEventListener('load', () => {
            document.querySelector('.loading').style.display = 'none';
        });

        function initiatePayment() {
            FlutterwaveCheckout({
                public_key: "YOUR_FLUTTERWAVE_PUBLIC_KEY",
                tx_ref: "<?php echo $tx_ref; ?>",
                amount: <?php echo $total_amount; ?>,
                currency: "USD",
                payment_options: "card, banktransfer, ussd",
                redirect_url: "<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/donate_payment.php?status=successful'; ?>",
                customer: {
                    email: "<?php echo $donor['email']; ?>",
                    phone_number: "<?php echo $donor['phone']; ?>",
                    name: "<?php echo $donor['name']; ?>",
                },
                customizations: {
                    title: "Organization Donation",
                    description: "Donation to support education",
                    logo: "assets/images/logo.png",
                },
            });
        }
    </script>
</body>
</html> 
*/ ?> 