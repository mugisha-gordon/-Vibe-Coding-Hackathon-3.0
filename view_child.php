<?php
session_start();
require_once "config/database.php";

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Get child ID from URL
$child_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Fetch child details
$child = null;
$sql = "SELECT * FROM children WHERE id = ?";
if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $child_id);
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        $child = mysqli_fetch_assoc($result);
    }
}

if(!$child){
    header("location: children.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Profile - Bumbobi Child Support Uganda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1 class="card-title">Child Details</h1>
                            <div>
                                <a href="edit_child.php?id=<?php echo $child['id']; ?>" class="btn btn-warning">Edit</a>
                                <a href="children.php" class="btn btn-secondary">Back to List</a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h3>Personal Information</h3>
                                <table class="table">
                                    <tr>
                                        <th>Full Name:</th>
                                        <td><?php echo $child['first_name'] . ' ' . $child['last_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date of Birth:</th>
                                        <td><?php echo date('M d, Y', strtotime($child['date_of_birth'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Gender:</th>
                                        <td><?php echo $child['gender']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Admission Date:</th>
                                        <td><?php echo date('M d, Y', strtotime($child['admission_date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td><?php echo $child['status']; ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h3>Additional Information</h3>
                                <table class="table">
                                    <tr>
                                        <th>Medical Conditions:</th>
                                        <td><?php echo $child['medical_conditions'] ?: 'None'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Allergies:</th>
                                        <td><?php echo $child['allergies'] ?: 'None'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Emergency Contact:</th>
                                        <td><?php echo $child['emergency_contact'] ?: 'Not specified'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Notes:</th>
                                        <td><?php echo $child['notes'] ?: 'No notes'; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h3>Program Participation</h3>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Program</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT p.name, cp.start_date, cp.end_date, cp.status 
                                                   FROM child_programs cp 
                                                   JOIN programs p ON cp.program_id = p.id 
                                                   WHERE cp.child_id = ?";
                                            if($stmt = mysqli_prepare($conn, $sql)){
                                                mysqli_stmt_bind_param($stmt, "i", $child_id);
                                                if(mysqli_stmt_execute($stmt)){
                                                    $result = mysqli_stmt_get_result($stmt);
                                                    while($row = mysqli_fetch_assoc($result)){
                                                        echo "<tr>";
                                                        echo "<td>" . $row['name'] . "</td>";
                                                        echo "<td>" . date('M d, Y', strtotime($row['start_date'])) . "</td>";
                                                        echo "<td>" . ($row['end_date'] ? date('M d, Y', strtotime($row['end_date'])) : 'Ongoing') . "</td>";
                                                        echo "<td>" . $row['status'] . "</td>";
                                                        echo "</tr>";
                                                    }
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 