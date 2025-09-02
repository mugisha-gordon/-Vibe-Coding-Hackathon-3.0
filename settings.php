<?php
session_start();

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    header("location: login.php");
    exit;
}

require_once "config/database.php";

// Handle settings update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['settings'] as $key => $value) {
        $sql = "UPDATE settings SET setting_value = ?, updated_by = ? WHERE setting_key = ?";
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sis", $value, $_SESSION["id"], $key);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    
    // Log the activity
    $log_sql = "INSERT INTO activity_log (user_id, action, details) VALUES (?, 'update_settings', 'Updated system settings')";
    $log_stmt = mysqli_prepare($conn, $log_sql);
    mysqli_stmt_bind_param($log_stmt, "i", $_SESSION["id"]);
    mysqli_stmt_execute($log_stmt);
    
    header("location: settings.php?success=1");
    exit;
}

// Get all settings
$settings = [];
$sql = "SELECT * FROM settings ORDER BY id";
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)) {
    $settings[$row['setting_key']] = $row;
}

include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">System Settings</h1>
                </div>
                
                <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Settings updated successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="post" action="">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Organization Information</h4>
                                    <div class="mb-3">
                                        <label class="form-label">Organization Name</label>
                                        <input type="text" class="form-control" name="settings[organization_name]" 
                                               value="<?php echo htmlspecialchars($settings['organization_name']['setting_value']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Organization Email</label>
                                        <input type="email" class="form-control" name="settings[organization_email]" 
                                               value="<?php echo htmlspecialchars($settings['organization_email']['setting_value']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Organization Phone</label>
                                        <input type="text" class="form-control" name="settings[organization_phone]" 
                                               value="<?php echo htmlspecialchars($settings['organization_phone']['setting_value']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Organization Address</label>
                                        <textarea class="form-control" name="settings[organization_address]" rows="2"><?php 
                                            echo htmlspecialchars($settings['organization_address']['setting_value']); 
                                        ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h4>System Settings</h4>
                                    <div class="mb-3">
                                        <label class="form-label">Default Currency</label>
                                        <select class="form-control" name="settings[currency]">
                                            <option value="UGX" <?php echo $settings['currency']['setting_value'] == 'UGX' ? 'selected' : ''; ?>>UGX</option>
                                            <option value="USD" <?php echo $settings['currency']['setting_value'] == 'USD' ? 'selected' : ''; ?>>USD</option>
                                            <option value="EUR" <?php echo $settings['currency']['setting_value'] == 'EUR' ? 'selected' : ''; ?>>EUR</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Default Timezone</label>
                                        <select class="form-control" name="settings[timezone]">
                                            <option value="Africa/Kampala" <?php echo $settings['timezone']['setting_value'] == 'Africa/Kampala' ? 'selected' : ''; ?>>Africa/Kampala</option>
                                            <option value="UTC" <?php echo $settings['timezone']['setting_value'] == 'UTC' ? 'selected' : ''; ?>>UTC</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Annual Donation Goal</label>
                                        <input type="number" class="form-control" name="settings[donation_goal]" 
                                               value="<?php echo htmlspecialchars($settings['donation_goal']['setting_value']); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h4>System Features</h4>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="settings[maintenance_mode]" 
                                                   value="true" <?php echo $settings['maintenance_mode']['setting_value'] == 'true' ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Maintenance Mode</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="settings[volunteer_approval_required]" 
                                                   value="true" <?php echo $settings['volunteer_approval_required']['setting_value'] == 'true' ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Require Approval for Volunteers</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="settings[enable_registration]" 
                                                   value="true" <?php echo $settings['enable_registration']['setting_value'] == 'true' ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Enable User Registration</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Save Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Handle checkbox values
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                this.value = this.checked ? 'true' : 'false';
            });
        });
    </script>
</body>
</html>

<?php include 'includes/footer.php'; ?> 