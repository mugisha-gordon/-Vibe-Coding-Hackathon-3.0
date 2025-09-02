<?php
// Get donation statistics
$donation_stats = [
    'total' => $total_donations,
    'monthly' => $monthly_donations,
    'pending' => $pending_donations
];
?>

<div class="tab-pane fade" id="donations">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Donation Management</h2>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDonationModal">
                <i class="fas fa-plus me-2"></i>Add Donation
            </button>
            <button class="btn btn-success" onclick="exportDonations()">
                <i class="fas fa-file-export me-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Donation Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <i class="fas fa-hand-holding-usd text-success"></i>
                <h3 id="stat-total_donations">$<?php echo number_format($total_donations, 2); ?></h3>
                <p>Total Donations</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <i class="fas fa-calendar-alt text-primary"></i>
                <h3 id="stat-monthly_donations">$<?php echo number_format($monthly_donations, 2); ?></h3>
                <p>Monthly Donations</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <i class="fas fa-clock text-warning"></i>
                <h3 id="stat-pending_donations"><?php echo $pending_donations; ?></h3>
                <p>Pending Donations</p>
            </div>
        </div>
    </div>

    <!-- Donations Table -->
    <div class="data-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Donor</th>
                        <th>Amount</th>
                        <th>Child</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="donationsTableBody">
                    <?php
                    $donations_sql = "SELECT d.*, c.name as child_name 
                                    FROM donations d 
                                    LEFT JOIN children c ON d.child_id = c.id 
                                    ORDER BY d.created_at DESC";
                    $donations_result = mysqli_query($conn, $donations_sql);
                    
                    if ($donations_result) {
                        while($donation = mysqli_fetch_assoc($donations_result)) {
                            $status_class = $donation['status'] === 'completed' ? 'success' : 
                                          ($donation['status'] === 'pending' ? 'warning' : 'danger');
                            echo "<tr id='donation-{$donation['id']}'>
                                <td>{$donation['donor_name']}</td>
                                <td class='donation-amount'>$" . number_format($donation['amount'], 2) . "</td>
                                <td>{$donation['child_name']}</td>
                                <td class='donation-date'>" . date('M d, Y', strtotime($donation['created_at'])) . "</td>
                                <td><span class='badge bg-{$status_class} donation-status status-{$donation['status']}'>{$donation['status']}</span></td>
                                <td>
                                    <div class='btn-group'>
                                        <button class='btn btn-sm btn-success' onclick='updateDonationStatus({$donation['id']}, \"completed\")' " . 
                                        ($donation['status'] === 'completed' ? 'disabled' : '') . ">
                                            <i class='fas fa-check'></i>
                                        </button>
                                        <button class='btn btn-sm btn-danger' onclick='updateDonationStatus({$donation['id']}, \"failed\")' " . 
                                        ($donation['status'] === 'failed' ? 'disabled' : '') . ">
                                            <i class='fas fa-times'></i>
                                        </button>
                                        <button class='btn btn-sm btn-primary' onclick='viewDonationDetails({$donation['id']})'>
                                            <i class='fas fa-eye'></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Donation Modal -->
<div class="modal fade" id="addDonationModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Donation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addDonationForm" action="actions/add_donation.php" method="post">
                    <div class="mb-3">
                        <label class="form-label">Donor Name</label>
                        <input type="text" class="form-control" name="donor_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Donor Email</label>
                        <input type="email" class="form-control" name="donor_email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" name="amount" step="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Child (Optional)</label>
                        <select class="form-control" name="child_id">
                            <option value="">Select a child</option>
                            <?php
                            $children_sql = "SELECT id, name FROM children WHERE status = 'active' ORDER BY name";
                            $children_result = mysqli_query($conn, $children_sql);
                            while($child = mysqli_fetch_assoc($children_result)) {
                                echo "<option value='{$child['id']}'>{$child['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-control" name="payment_method" required>
                            <option value="credit_card">Credit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Donation</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Donation Details Modal -->
<div class="modal fade" id="viewDonationModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Donation Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="donationDetails">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<script>
function viewDonation(id) {
    // Implement view donation details
    console.log('View donation:', id);
}

function updateDonationStatus(id, status) {
    if (confirm('Are you sure you want to update this donation status?')) {
        fetch('actions/update_donation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id}&status=${status}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to update donation status: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update donation status');
        });
    }
}
</script> 