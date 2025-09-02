<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    header("location: login.php");
    exit;
}

require_once "config/database.php";

// Get donation ID from URL
$donation_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch donation details
$donation_query = "SELECT d.*, c.name as child_name 
                  FROM donations d 
                  LEFT JOIN children c ON d.child_id = c.id 
                  WHERE d.id = ?";
$stmt = mysqli_prepare($conn, $donation_query);
mysqli_stmt_bind_param($stmt, "i", $donation_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$donation = mysqli_fetch_assoc($result);

if(!$donation) {
    header("location: dashboard.php");
    exit;
}
?>

<?php include 'includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Details - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-shield-alt"></i> Admin Panel</h3>
            </div>
            <nav class="nav flex-column">
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="donation-details">
                <h2 class="mb-4">Donation Details</h2>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="donor-info mb-4">
                                    <div class="donor-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="donor-details">
                                        <h3 class="donor-name"><?php echo htmlspecialchars($donation['donor_name']); ?></h3>
                                        <p class="donor-email"><?php echo htmlspecialchars($donation['donor_email']); ?></p>
                                    </div>
                                </div>

                                <div class="donation-info">
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <h4>Donation Amount</h4>
                                            <p class="donation-amount">$<?php echo number_format($donation['amount'], 2); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <h4>Status</h4>
                                            <span class="donation-status status-<?php echo $donation['status']; ?>">
                                                <?php echo ucfirst($donation['status']); ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <h4>Date</h4>
                                            <p><?php echo date('F d, Y', strtotime($donation['donation_date'])); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <h4>Payment Method</h4>
                                            <p><?php echo ucfirst($donation['payment_method']); ?></p>
                                        </div>
                                    </div>

                                    <?php if($donation['child_id']): ?>
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4>Beneficiary</h4>
                                            <p><?php echo htmlspecialchars($donation['child_name']); ?></p>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if($donation['message']): ?>
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4>Donor Message</h4>
                                            <p class="donor-message"><?php echo nl2br(htmlspecialchars($donation['message'])); ?></p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="action-buttons mt-4">
                                    <?php if($donation['status'] == 'pending'): ?>
                                    <button class="btn btn-success me-2" onclick="updateStatus(<?php echo $donation['id']; ?>, 'completed')">
                                        <i class="fas fa-check"></i> Mark as Completed
                                    </button>
                                    <button class="btn btn-danger" onclick="updateStatus(<?php echo $donation['id']; ?>, 'failed')">
                                        <i class="fas fa-times"></i> Mark as Failed
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h4>Transaction Details</h4>
                                <div class="transaction-info">
                                    <p><strong>Transaction ID:</strong> <?php echo $donation['transaction_id']; ?></p>
                                    <p><strong>Payment Reference:</strong> <?php echo $donation['payment_reference']; ?></p>
                                    <p><strong>Created At:</strong> <?php echo date('F d, Y H:i:s', strtotime($donation['created_at'])); ?></p>
                                    <?php if($donation['updated_at']): ?>
                                    <p><strong>Last Updated:</strong> <?php echo date('F d, Y H:i:s', strtotime($donation['updated_at'])); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateStatus(donationId, status) {
            if(confirm('Are you sure you want to update the donation status?')) {
                $.post('actions/update_donation_status.php', {
                    donation_id: donationId,
                    status: status
                }, function(response) {
                    if(response.success) {
                        location.reload();
                    } else {
                        alert('Error updating donation status: ' + response.message);
                    }
                });
            }
        }
    </script>
</body>
</html>

<?php include 'includes/footer.php'; ?> 