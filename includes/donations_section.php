<?php
// First, verify the database structure
require_once __DIR__ . '/../config/verify_db.php';

// Get recent donations with proper error handling
$recent_donations_query = "SELECT d.*, 
    CASE 
        WHEN d.child_id IS NOT NULL AND ch.name IS NOT NULL THEN ch.name 
        ELSE 'General Fund' 
    END as child_name
    FROM donations d 
    LEFT JOIN children ch ON d.child_id = ch.id 
    ORDER BY d.created_at DESC 
    LIMIT 5";

$recent_donations = mysqli_query($conn, $recent_donations_query);
if (!$recent_donations) {
    die("Error fetching donations: " . mysqli_error($conn));
}

// Get donation statistics with error handling
$total_amount_query = "SELECT COALESCE(SUM(amount), 0) as total FROM donations WHERE status = 'completed'";
$total_amount = mysqli_query($conn, $total_amount_query);
if (!$total_amount) {
    die("Error fetching total amount: " . mysqli_error($conn));
}

$monthly_amount_query = "SELECT COALESCE(SUM(amount), 0) as total FROM donations 
    WHERE status = 'completed' 
    AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$monthly_amount = mysqli_query($conn, $monthly_amount_query);
if (!$monthly_amount) {
    die("Error fetching monthly amount: " . mysqli_error($conn));
}
?>

<!-- Donations Section -->
<div class="tab-pane fade" id="donations">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Donations Management</h2>
        <div class="date-filter">
            <select class="form-select" id="donationDateRange">
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month" selected>This Month</option>
                <option value="year">This Year</option>
            </select>
        </div>
    </div>

    <!-- Donation Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="donation-summary">
                <h4>Total Donations</h4>
                <div class="summary-item">
                    <span class="summary-label">Total Amount</span>
                    <span class="summary-value">$<?php echo number_format(mysqli_fetch_assoc($total_amount)['total'], 2); ?></span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">This Month</span>
                    <span class="summary-value">$<?php echo number_format(mysqli_fetch_assoc($monthly_amount)['total'], 2); ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="donations-table">
                <h4>Recent Donations</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Donor</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Beneficiary</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($donation = mysqli_fetch_assoc($recent_donations)): ?>
                            <tr>
                                <td>
                                    <div class="donor-info">
                                        <div class="donor-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="donor-details">
                                            <div class="donor-name"><?php echo htmlspecialchars($donation['donor_name']); ?></div>
                                            <div class="donor-email"><?php echo htmlspecialchars($donation['donor_email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="donation-amount">$<?php echo number_format($donation['amount'], 2); ?></td>
                                <td class="donation-date"><?php echo date('M d, Y', strtotime($donation['created_at'])); ?></td>
                                <td>
                                    <span class="donation-status status-<?php echo $donation['status']; ?>">
                                        <?php echo ucfirst($donation['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($donation['child_name']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="viewDonation(<?php echo $donation['id']; ?>)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewDonation(id) {
    // Implement donation details view functionality
    window.location.href = 'donation_details.php?id=' + id;
}
</script> 