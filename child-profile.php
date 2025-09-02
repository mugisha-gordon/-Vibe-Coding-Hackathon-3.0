<?php
require_once "config/database.php";
session_start();

// Get child ID from URL
$child_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch child details
$child_sql = "SELECT c.*, 
              GROUP_CONCAT(DISTINCT p.name) as programs,
              GROUP_CONCAT(DISTINCT s.title) as success_stories
              FROM children c
              LEFT JOIN child_programs cp ON c.id = cp.child_id
              LEFT JOIN programs p ON cp.program_id = p.id
              LEFT JOIN success_stories s ON c.id = s.child_id
              WHERE c.id = ?
              GROUP BY c.id";
              
$stmt = mysqli_prepare($conn, $child_sql);
mysqli_stmt_bind_param($stmt, "i", $child_id);
mysqli_stmt_execute($stmt);
$child_result = mysqli_stmt_get_result($stmt);
$child = mysqli_fetch_assoc($child_result);

if (!$child) {
    header("Location: children.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($child['first_name'] . ' ' . $child['last_name']); ?> - Child Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .profile-avatar i {
            font-size: 4rem;
            color: #0d6efd;
        }
        .info-card {
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .info-card:hover {
            transform: translateY(-5px);
        }
        .info-icon {
            width: 50px;
            height: 50px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .info-icon i {
            font-size: 1.5rem;
            color: #0d6efd;
        }
        .program-badge {
            background: #e9ecef;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            margin: 0.25rem;
            display: inline-block;
        }
        .timeline {
            position: relative;
            padding-left: 2rem;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -2.5rem;
            top: 0;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            background: #0d6efd;
            border: 2px solid white;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Profile Header -->
    <section class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <div class="profile-avatar">
                        <i class="fas fa-child"></i>
                    </div>
                </div>
                <div class="col-md-8">
                    <h1 class="display-4"><?php echo htmlspecialchars($child['first_name'] . ' ' . $child['last_name']); ?></h1>
                    <p class="lead"><?php echo htmlspecialchars($child['status']); ?> Student</p>
                    <div class="d-flex gap-3">
                        <span><i class="fas fa-calendar"></i> Joined: <?php echo date('F Y', strtotime($child['admission_date'])); ?></span>
                        <span><i class="fas fa-birthday-cake"></i> Age: <?php echo date_diff(date_create($child['date_of_birth']), date_create('today'))->y; ?> years</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Profile Content -->
    <section class="profile-content py-5">
        <div class="container">
            <div class="row">
                <!-- Basic Information -->
                <div class="col-md-4 mb-4">
                    <div class="card info-card h-100">
                        <div class="card-body">
                            <div class="info-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h5 class="card-title">Basic Information</h5>
                            <ul class="list-unstyled">
                                <li><strong>Gender:</strong> <?php echo htmlspecialchars($child['gender']); ?></li>
                                <li><strong>Date of Birth:</strong> <?php echo date('F j, Y', strtotime($child['date_of_birth'])); ?></li>
                                <li><strong>Status:</strong> <span class="badge bg-primary"><?php echo htmlspecialchars($child['status']); ?></span></li>
                                <li><strong>Emergency Contact:</strong> <?php echo htmlspecialchars($child['emergency_contact']); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Health Information -->
                <div class="col-md-4 mb-4">
                    <div class="card info-card h-100">
                        <div class="card-body">
                            <div class="info-icon">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                            <h5 class="card-title">Health Information</h5>
                            <div class="mb-3">
                                <h6>Medical Conditions</h6>
                                <p><?php echo nl2br(htmlspecialchars($child['medical_conditions'] ?? 'None reported')); ?></p>
                            </div>
                            <div>
                                <h6>Allergies</h6>
                                <p><?php echo nl2br(htmlspecialchars($child['allergies'] ?? 'None reported')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Programs -->
                <div class="col-md-4 mb-4">
                    <div class="card info-card h-100">
                        <div class="card-body">
                            <div class="info-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <h5 class="card-title">Enrolled Programs</h5>
                            <?php if ($child['programs']): ?>
                                <?php foreach(explode(',', $child['programs']) as $program): ?>
                                    <span class="program-badge"><?php echo htmlspecialchars($program); ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No programs enrolled yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Success Stories -->
                <div class="col-md-8 mb-4">
                    <div class="card info-card">
                        <div class="card-body">
                            <div class="info-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <h5 class="card-title">Success Stories</h5>
                            <?php if ($child['success_stories']): ?>
                                <div class="timeline">
                                    <?php foreach(explode(',', $child['success_stories']) as $story): ?>
                                        <div class="timeline-item">
                                            <h6><?php echo htmlspecialchars($story); ?></h6>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No success stories yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="col-md-4 mb-4">
                    <div class="card info-card">
                        <div class="card-body">
                            <div class="info-icon">
                                <i class="fas fa-sticky-note"></i>
                            </div>
                            <h5 class="card-title">Additional Notes</h5>
                            <p><?php echo nl2br(htmlspecialchars($child['notes'] ?? 'No additional notes.')); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 