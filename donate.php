<?php
require_once "config/database.php";
require_once "config/performance_monitor.php";
session_start();

$monitor = new PerformanceMonitor();
$monitor->start();

$amount = $name = $email = $phone = $address = $city = $country = $message = "";
$amount_err = $name_err = $email_err = $phone_err = $address_err = $city_err = $country_err = $message_err = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate amount
    if (empty(trim($_POST["amount"]))) {
        $amount_err = "Please enter an amount.";
    } else {
        $amount = trim($_POST["amount"]);
        if (!is_numeric($amount) || $amount <= 0) {
            $amount_err = "Please enter a valid amount.";
        }
    }
    
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

    // Validate phone
    if (empty(trim($_POST["phone"]))) {
        $phone_err = "Please enter your phone number.";
    } else {
        $phone = trim($_POST["phone"]);
    }

    // Validate address
    if (empty(trim($_POST["address"]))) {
        $address_err = "Please enter your address.";
    } else {
        $address = trim($_POST["address"]);
    }

    // Validate city
    if (empty(trim($_POST["city"]))) {
        $city_err = "Please enter your city.";
    } else {
        $city = trim($_POST["city"]);
    }

    // Validate country
    if (empty(trim($_POST["country"]))) {
        $country_err = "Please enter your country.";
    } else {
        $country = trim($_POST["country"]);
    }
    
    // Validate message (optional)
    $message = trim($_POST["message"]);
    
    // Check input errors before inserting in database
    if (empty($amount_err) && empty($name_err) && empty($email_err) && empty($phone_err) && empty($address_err) && empty($city_err) && empty($country_err)) {
        // First, ensure the table has all required columns
        $alter_queries = [
            "ALTER TABLE donations ADD COLUMN IF NOT EXISTS donor_phone VARCHAR(20) AFTER donor_email",
            "ALTER TABLE donations ADD COLUMN IF NOT EXISTS donor_address TEXT AFTER donor_phone",
            "ALTER TABLE donations ADD COLUMN IF NOT EXISTS donor_city VARCHAR(100) AFTER donor_address",
            "ALTER TABLE donations ADD COLUMN IF NOT EXISTS donor_country VARCHAR(100) AFTER donor_city",
            "ALTER TABLE donations ADD COLUMN IF NOT EXISTS message TEXT AFTER donor_country",
            "ALTER TABLE donations ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) AFTER message",
            "ALTER TABLE donations ADD COLUMN IF NOT EXISTS payment_reference VARCHAR(100) AFTER payment_method",
            "ALTER TABLE donations ADD COLUMN IF NOT EXISTS transaction_id VARCHAR(100) AFTER payment_reference",
            "ALTER TABLE donations ADD COLUMN IF NOT EXISTS status ENUM('pending', 'completed', 'failed') DEFAULT 'pending' AFTER transaction_id"
        ];

        foreach ($alter_queries as $query) {
            mysqli_query($conn, $query);
        }

        // Now proceed with the insert
        $sql = "INSERT INTO donations (amount, donor_name, donor_email, donor_phone, donor_address, donor_city, donor_country, message, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "dsssssss", 
                $param_amount, 
                $param_name, 
                $param_email, 
                $param_phone, 
                $param_address, 
                $param_city, 
                $param_country, 
                $param_message
            );
            
            $param_amount = $amount;
            $param_name = $name;
            $param_email = $email;
            $param_phone = $phone;
            $param_address = $address;
            $param_city = $city;
            $param_country = $country;
            $param_message = $message;
            
            if (mysqli_stmt_execute($stmt)) {
                $donation_id = mysqli_insert_id($conn);
                $_SESSION['donation_id'] = $donation_id;
                $_SESSION['donation_amount'] = $amount;
                $_SESSION['donor_details'] = [
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address,
                    'city' => $city,
                    'country' => $country,
                    'message' => $message
                ];
                
                $success_message = "Thank you for your donation! We appreciate your support.";
                // Clear form fields
                $amount = $name = $email = $phone = $address = $city = $country = $message = "";
                
                // Redirect to payment page
                header("Location: donate_payment.php");
                exit();
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
    <title>Donate - Bumbobi Child Support Uganda</title>
    
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
    .donate-header {
        background: linear-gradient(135deg, #1a237e, #0d47a1);
        color: white;
        padding: 4rem 0;
        margin-bottom: 3rem;
    }

    .donation-card {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .donation-amount {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1a237e;
        margin-bottom: 1rem;
    }

    .donation-info {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
    }

    .donation-info h4 {
        color: #1a237e;
        margin-bottom: 1rem;
    }

    .donation-info p {
        color: #6c757d;
        margin-bottom: 0.5rem;
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

    .btn-donate {
        background: #1a237e;
        color: white;
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-donate:hover {
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

    .impact-card {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        text-align: center;
        height: 100%;
        transition: transform 0.3s ease;
    }

    .impact-card:hover {
        transform: translateY(-5px);
    }

    .impact-icon {
        width: 80px;
        height: 80px;
        background: #1a237e;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 1.5rem;
    }

    @media (max-width: 768px) {
        .donate-header {
            padding: 3rem 0;
        }

        .donation-card {
            padding: 1.5rem;
        }

        .donation-amount {
            font-size: 2rem;
        }

        .impact-card {
            margin-bottom: 1.5rem;
        }
    }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Donate Header -->
    <section class="donate-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1>Make a Donation</h1>
                    <p>Your support can make a real difference in the lives of children in Bumbobi</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Impact Section -->
    <section class="impact-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="impact-card">
                        <div class="impact-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h4>Education</h4>
                        <p>Support children's education and provide them with the tools they need to succeed.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="impact-card">
                        <div class="impact-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h4>Healthcare</h4>
                        <p>Ensure children have access to proper healthcare and medical attention.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="impact-card">
                        <div class="impact-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h4>Nutrition</h4>
                        <p>Provide nutritious meals and ensure children have access to clean water.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Donation Form -->
    <section class="donation-form-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="donation-card">
                        <?php if (!empty($success_message)): ?>
                        <div class="success-message">
                            <?php echo $success_message; ?>
                        </div>
                        <?php endif; ?>

                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-4">
                                <label class="form-label">Donation Amount (USD)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="amount" class="form-control <?php echo (!empty($amount_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $amount; ?>" step="0.01" min="1">
                                    <div class="invalid-feedback"><?php echo $amount_err; ?></div>
                                </div>
                            </div>

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

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" name="phone" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $phone; ?>">
                                    <div class="invalid-feedback"><?php echo $phone_err; ?></div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" class="form-control <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $address; ?>">
                                    <div class="invalid-feedback"><?php echo $address_err; ?></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control <?php echo (!empty($city_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $city; ?>">
                                    <div class="invalid-feedback"><?php echo $city_err; ?></div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Country</label>
                                    <input type="text" name="country" class="form-control <?php echo (!empty($country_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $country; ?>">
                                    <div class="invalid-feedback"><?php echo $country_err; ?></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Message (Optional)</label>
                                <textarea name="message" rows="3" class="form-control"><?php echo $message; ?></textarea>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-donate">
                                    <i class="fas fa-heart me-2"></i>Make Donation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Donation Info -->
    <section class="donation-info-section py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="donation-info">
                        <h4>Your Donation Makes a Difference</h4>
                        <p>Every contribution, no matter the size, helps us provide essential services to children in need. Your donation will be used to:</p>
                        <ul>
                            <li>Provide quality education and school supplies</li>
                            <li>Ensure access to healthcare and medical services</li>
                            <li>Supply nutritious meals and clean water</li>
                            <li>Support community development programs</li>
                        </ul>
                        <p>Thank you for your generosity and support!</p>
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