<?php
require_once "../config/database.php";
require_once "../config/auth.php";

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login.php");
    exit();
}

$success_message = '';
$error_message = '';

// Handle volunteer request actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && isset($_POST['request_id'])) {
        $request_id = (int)$_POST['request_id'];
        $action = $_POST['action'];
        $admin_notes = isset($_POST['admin_notes']) ? $_POST['admin_notes'] : '';
        
        if ($action === 'approve') {
            // Get volunteer request details
            $request_sql = "SELECT * FROM volunteer_requests WHERE id = ?";
            $request_stmt = mysqli_prepare($conn, $request_sql);
            mysqli_stmt_bind_param($request_stmt, "i", $request_id);
            mysqli_stmt_execute($request_stmt);
            $request_result = mysqli_stmt_get_result($request_stmt);
            $request = mysqli_fetch_assoc($request_result);
            
            if ($request) {
                // Start transaction
                mysqli_begin_transaction($conn);
                
                try {
                    // Insert into volunteers table
                    $volunteer_sql = "INSERT INTO volunteers (first_name, last_name, email, phone, skills, status, join_date) 
                                    VALUES (?, ?, ?, ?, ?, 'active', CURDATE())";
                    $volunteer_stmt = mysqli_prepare($conn, $volunteer_sql);
                    mysqli_stmt_bind_param($volunteer_stmt, "sssss", 
                        $request['first_name'],
                        $request['last_name'],
                        $request['email'],
                        $request['phone'],
                        $request['skills']
                    );
                    mysqli_stmt_execute($volunteer_stmt);
                    
                    // Update volunteer request status
                    $update_sql = "UPDATE volunteer_requests SET status = 'approved', admin_notes = ? WHERE id = ?";
                    $update_stmt = mysqli_prepare($conn, $update_sql);
                    mysqli_stmt_bind_param($update_stmt, "si", $admin_notes, $request_id);
                    mysqli_stmt_execute($update_stmt);
                    
                    // Send approval email
                    $to = $request['email'];
                    $subject = "Volunteer Application Approved";
                    $message = "Dear " . $request['first_name'] . ",\n\n";
                    $message .= "Your volunteer application has been approved. Welcome to our team!\n\n";
                    $message .= "We will contact you shortly with more details about your role and responsibilities.\n\n";
                    $message .= "Best regards,\nBumbobi Child Support Uganda Team";
                    
                    $headers = "From: noreply@yourdomain.com\r\n";
                    $headers .= "Reply-To: admin@yourdomain.com\r\n";
                    
                    mail($to, $subject, $message, $headers);
                    
                    mysqli_commit($conn);
                    $success_message = "Volunteer request approved successfully.";
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $error_message = "Error approving volunteer request: " . $e->getMessage();
                }
            }
        } elseif ($action === 'reject') {
            $update_sql = "UPDATE volunteer_requests SET status = 'rejected', admin_notes = ? WHERE id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "si", $admin_notes, $request_id);
            
            if (mysqli_stmt_execute($update_stmt)) {
                // Get volunteer request details for email
                $request_sql = "SELECT * FROM volunteer_requests WHERE id = ?";
                $request_stmt = mysqli_prepare($conn, $request_sql);
                mysqli_stmt_bind_param($request_stmt, "i", $request_id);
                mysqli_stmt_execute($request_stmt);
                $request_result = mysqli_stmt_get_result($request_stmt);
                $request = mysqli_fetch_assoc($request_result);
                
                // Send rejection email
                $to = $request['email'];
                $subject = "Volunteer Application Status";
                $message = "Dear " . $request['first_name'] . ",\n\n";
                $message .= "Thank you for your interest in volunteering with us. After careful consideration, we regret to inform you that we are unable to accept your application at this time.\n\n";
                $message .= "We appreciate your interest and encourage you to apply again in the future.\n\n";
                $message .= "Best regards,\nBumbobi Child Support Uganda Team";
                
                $headers = "From: noreply@yourdomain.com\r\n";
                $headers .= "Reply-To: admin@yourdomain.com\r\n";
                
                mail($to, $subject, $message, $headers);
                
                $success_message = "Volunteer request rejected successfully.";
            } else {
                $error_message = "Error rejecting volunteer request.";
            }
        }
    }
}

// Fetch pending volunteer requests
$requests_sql = "SELECT * FROM volunteer_requests WHERE status = 'pending' ORDER BY created_at DESC";
$requests_result = mysqli_query($conn, $requests_sql);

// Fetch active volunteers
$volunteers_sql = "SELECT * FROM volunteers WHERE status = 'active' ORDER BY join_date DESC";
$volunteers_result = mysqli_query($conn, $volunteers_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Volunteers - Admin Dashboard</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'includes/admin_header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/admin_sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <h1 class="h2 mb-4">Manage Volunteers</h1>
                
                <?php if ($success_message): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <!-- Pending Requests -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Pending Volunteer Requests</h5>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($requests_result) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Skills</th>
                                            <th>Applied On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($request = mysqli_fetch_assoc($requests_result)): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?></td>
                                                <td><?php echo htmlspecialchars($request['email']); ?></td>
                                                <td><?php echo htmlspecialchars($request['phone']); ?></td>
                                                <td><?php echo htmlspecialchars($request['skills']); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($request['created_at'])); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $request['id']; ?>">
                                                        View
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal<?php echo $request['id']; ?>">
                                                        Approve
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo $request['id']; ?>">
                                                        Reject
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- View Modal -->
                                            <div class="modal fade" id="viewModal<?php echo $request['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">View Volunteer Request</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row mb-3">
                                                                <div class="col-md-6">
                                                                    <strong>Name:</strong>
                                                                    <p><?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?></p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <strong>Contact:</strong>
                                                                    <p>
                                                                        Email: <?php echo htmlspecialchars($request['email']); ?><br>
                                                                        Phone: <?php echo htmlspecialchars($request['phone']); ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>Skills:</strong>
                                                                <p><?php echo nl2br(htmlspecialchars($request['skills'])); ?></p>
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>Availability:</strong>
                                                                <p><?php echo nl2br(htmlspecialchars($request['availability'])); ?></p>
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>Areas of Interest:</strong>
                                                                <p><?php echo nl2br(htmlspecialchars($request['areas_of_interest'])); ?></p>
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>Previous Experience:</strong>
                                                                <p><?php echo nl2br(htmlspecialchars($request['previous_experience'])); ?></p>
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>Motivation:</strong>
                                                                <p><?php echo nl2br(htmlspecialchars($request['motivation'])); ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Approve Modal -->
                                            <div class="modal fade" id="approveModal<?php echo $request['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Approve Volunteer Request</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST">
                                                            <div class="modal-body">
                                                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                                <input type="hidden" name="action" value="approve">
                                                                <div class="mb-3">
                                                                    <label for="admin_notes" class="form-label">Admin Notes (optional)</label>
                                                                    <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-success">Approve</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Reject Modal -->
                                            <div class="modal fade" id="rejectModal<?php echo $request['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Reject Volunteer Request</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST">
                                                            <div class="modal-body">
                                                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                                <input type="hidden" name="action" value="reject">
                                                                <div class="mb-3">
                                                                    <label for="admin_notes" class="form-label">Reason for Rejection</label>
                                                                    <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-danger">Reject</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No pending volunteer requests.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Active Volunteers -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Active Volunteers</h5>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($volunteers_result) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Skills</th>
                                            <th>Join Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($volunteer = mysqli_fetch_assoc($volunteers_result)): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($volunteer['first_name'] . ' ' . $volunteer['last_name']); ?></td>
                                                <td><?php echo htmlspecialchars($volunteer['email']); ?></td>
                                                <td><?php echo htmlspecialchars($volunteer['phone']); ?></td>
                                                <td><?php echo htmlspecialchars($volunteer['skills']); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($volunteer['join_date'])); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewVolunteerModal<?php echo $volunteer['id']; ?>">
                                                        View
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- View Volunteer Modal -->
                                            <div class="modal fade" id="viewVolunteerModal<?php echo $volunteer['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">View Volunteer Details</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row mb-3">
                                                                <div class="col-md-6">
                                                                    <strong>Name:</strong>
                                                                    <p><?php echo htmlspecialchars($volunteer['first_name'] . ' ' . $volunteer['last_name']); ?></p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <strong>Contact:</strong>
                                                                    <p>
                                                                        Email: <?php echo htmlspecialchars($volunteer['email']); ?><br>
                                                                        Phone: <?php echo htmlspecialchars($volunteer['phone']); ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>Skills:</strong>
                                                                <p><?php echo nl2br(htmlspecialchars($volunteer['skills'])); ?></p>
                                                            </div>
                                                            <div class="mb-3">
                                                                <strong>Join Date:</strong>
                                                                <p><?php echo date('F j, Y', strtotime($volunteer['join_date'])); ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No active volunteers.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 