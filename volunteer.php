<?php
require_once "config/database.php";
require_once "config/performance_monitor.php";
require_once "includes/MailHelper.php";

$monitor = new PerformanceMonitor();
$monitor->start();

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate required fields
    $required_fields = ['first_name', 'last_name', 'email', 'phone', 'skills', 'availability', 'areas_of_interest', 'motivation'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (empty($missing_fields)) {
        try {
            // Check if email already exists in pending requests
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $check_sql = "SELECT id FROM volunteer_requests WHERE email = ? AND status = 'pending'";
            $check_stmt = mysqli_prepare($conn, $check_sql);
            
            if (!$check_stmt) {
                throw new Exception("Database error: " . mysqli_error($conn));
            }
            
            mysqli_stmt_bind_param($check_stmt, "s", $email);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $error_message = "You already have a pending volunteer request. Please wait for our response.";
            } else {
                // Insert into volunteer_requests table
                $sql = "INSERT INTO volunteer_requests (
                    first_name, last_name, email, phone, 
                    skills, availability, areas_of_interest, 
                    previous_experience, motivation
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($conn, $sql);
                
                if (!$stmt) {
                    throw new Exception("Database error: " . mysqli_error($conn));
                }
                
                mysqli_stmt_bind_param($stmt, "sssssssss", 
                    $_POST['first_name'],
                    $_POST['last_name'],
                    $_POST['email'],
                    $_POST['phone'],
                    $_POST['skills'],
                    $_POST['availability'],
                    $_POST['areas_of_interest'],
                    $_POST['previous_experience'],
                    $_POST['motivation']
                );
                
                if (mysqli_stmt_execute($stmt)) {
                    $success_message = "Your volunteer request has been submitted successfully. We will review your application and get back to you soon.";
                    
                    // Prepare volunteer data for email
                    $volunteerData = [
                        'first_name' => $_POST['first_name'],
                        'last_name' => $_POST['last_name'],
                        'email' => $_POST['email'],
                        'phone' => $_POST['phone'],
                        'skills' => $_POST['skills'],
                        'availability' => $_POST['availability'],
                        'areas_of_interest' => $_POST['areas_of_interest'],
                        'previous_experience' => $_POST['previous_experience'],
                        'motivation' => $_POST['motivation']
                    ];
                    
                    // Send emails using MailHelper
                    $mailHelper = new MailHelper();
                    
                    // Send notification to admin
                    $mailHelper->sendVolunteerNotification($volunteerData);
                    
                    // Send confirmation to volunteer
                    $mailHelper->sendVolunteerConfirmation($volunteerData);
                    
                } else {
                    throw new Exception("Error executing statement: " . mysqli_stmt_error($stmt));
                }
                mysqli_stmt_close($stmt);
            }
            mysqli_stmt_close($check_stmt);
        } catch (Exception $e) {
            $error_message = "An error occurred: " . $e->getMessage();
        }
    } else {
        $error_message = "Please fill in all required fields: " . implode(", ", $missing_fields);
    }
}

$metrics = $monitor->getPerformanceMetrics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer - Bumbobi Child Support Uganda</title>
    
    <!-- Preload critical resources -->
    <link rel="preload" href="assets/css/style.css" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" as="style">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" as="style">
    
    <!-- Main CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Volunteer With Us</h1>
            <p class="lead">Join our team and make a difference in children's lives</p>
        </div>
    </section>

    <!-- Volunteer Opportunities -->
    <section class="content-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-chalkboard-teacher fa-3x text-primary mb-3"></i>
                            <h3>Teaching</h3>
                            <p>Share your knowledge and help children learn and grow.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-futbol fa-3x text-primary mb-3"></i>
                            <h3>Sports Coaching</h3>
                            <p>Help develop children's physical and team-building skills.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-hands-helping fa-3x text-primary mb-3"></i>
                            <h3>General Support</h3>
                            <p>Assist with various activities and programs.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Volunteer Application Form -->
    <section class="form-section py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center mb-4">Volunteer Application</h2>
                            
                            <?php if ($success_message): ?>
                                <div class="alert alert-success" role="alert">
                                    <?php echo htmlspecialchars($success_message); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($error_message): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo htmlspecialchars($error_message); ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">First Name *</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Last Name *</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone *</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="skills" class="form-label">Skills and Qualifications *</label>
                                    <textarea class="form-control" id="skills" name="skills" rows="3" required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="availability" class="form-label">Availability *</label>
                                    <textarea class="form-control" id="availability" name="availability" rows="2" required></textarea>
                                    <small class="text-muted">Please specify your available days and times</small>
                                </div>

                                <div class="mb-3">
                                    <label for="areas_of_interest" class="form-label">Areas of Interest *</label>
                                    <textarea class="form-control" id="areas_of_interest" name="areas_of_interest" rows="2" required></textarea>
                                    <small class="text-muted">What areas would you like to volunteer in?</small>
                                </div>

                                <div class="mb-3">
                                    <label for="previous_experience" class="form-label">Previous Experience</label>
                                    <textarea class="form-control" id="previous_experience" name="previous_experience" rows="3"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="motivation" class="form-label">Why do you want to volunteer with us? *</label>
                                    <textarea class="form-control" id="motivation" name="motivation" rows="3" required></textarea>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-lg">Submit Application</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 