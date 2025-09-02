<?php
require_once "config/database.php";
session_start();

// Fetch board members
$board_sql = "SELECT * FROM board_members WHERE status = 'Active' ORDER BY position";
$board_result = mysqli_query($conn, $board_sql);

// Fetch staff members
$staff_sql = "SELECT * FROM staff WHERE status = 'Active' ORDER BY department, position";
$staff_result = mysqli_query($conn, $staff_sql);

// Fetch volunteers
$volunteers_sql = "SELECT * FROM volunteers WHERE status = 'Active' ORDER BY created_at DESC";
$volunteers_result = mysqli_query($conn, $volunteers_sql);

// Get department counts
$departments = [];
while($staff = mysqli_fetch_assoc($staff_result)) {
    if(!isset($departments[$staff['department']])) {
        $departments[$staff['department']] = 0;
    }
    $departments[$staff['department']]++;
}
mysqli_data_seek($staff_result, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Team - Bumbobi Child Support Uganda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .team-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 3rem;
        }
        .team-card {
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }
        .team-card:hover {
            transform: translateY(-5px);
        }
        .team-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .team-avatar i {
            font-size: 3rem;
            color: #0d6efd;
        }
        .department-badge {
            background: #e9ecef;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: inline-block;
        }
        .skills-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .skill-tag {
            background: #f8f9fa;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }
        .section-title {
            position: relative;
            margin-bottom: 3rem;
            padding-bottom: 1rem;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: #0d6efd;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 2rem;
        }
        .stats-card i {
            font-size: 2.5rem;
            color: #0d6efd;
            margin-bottom: 1rem;
        }
        .stats-card h3 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Team Header -->
    <section class="team-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-4 fw-bold">Our Team</h1>
                    <p class="lead">Meet the dedicated individuals who make our mission possible.</p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fas fa-users fa-4x opacity-50"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Stats -->
    <section class="team-stats py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-user-tie"></i>
                        <h3><?php echo mysqli_num_rows($board_result); ?></h3>
                        <p>Board Members</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-user-friends"></i>
                        <h3><?php echo mysqli_num_rows($staff_result); ?></h3>
                        <p>Staff Members</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-hands-helping"></i>
                        <h3><?php echo mysqli_num_rows($volunteers_result); ?></h3>
                        <p>Active Volunteers</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-building"></i>
                        <h3><?php echo count($departments); ?></h3>
                        <p>Departments</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Board Members -->
    <section class="board-members py-5">
        <div class="container">
            <h2 class="section-title">Board Members</h2>
            <div class="row">
                <?php while($board = mysqli_fetch_assoc($board_result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card team-card">
                        <div class="card-body text-center">
                            <div class="team-avatar">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($board['first_name'] . ' ' . $board['last_name']); ?></h5>
                            <span class="department-badge"><?php echo htmlspecialchars($board['position']); ?></span>
                            <p class="card-text"><?php echo htmlspecialchars($board['bio']); ?></p>
                            <div class="social-links">
                                <?php if($board['email']): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($board['email']); ?>" class="text-primary me-2">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if($board['linkedin']): ?>
                                    <a href="<?php echo htmlspecialchars($board['linkedin']); ?>" class="text-primary me-2" target="_blank">
                                        <i class="fab fa-linkedin"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Staff Members -->
    <section class="staff-members py-5 bg-light">
        <div class="container">
            <h2 class="section-title">Staff Members</h2>
            <?php 
            $current_department = '';
            while($staff = mysqli_fetch_assoc($staff_result)): 
                if($current_department != $staff['department']):
                    if($current_department != '') echo '</div></div>';
                    $current_department = $staff['department'];
            ?>
                <div class="department-section mb-5">
                    <h3 class="mb-4"><?php echo htmlspecialchars($current_department); ?></h3>
                    <div class="row">
            <?php endif; ?>
                <div class="col-md-4 mb-4">
                    <div class="card team-card">
                        <div class="card-body text-center">
                            <div class="team-avatar">
                                <i class="fas fa-user-friends"></i>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']); ?></h5>
                            <span class="department-badge"><?php echo htmlspecialchars($staff['position']); ?></span>
                            <p class="card-text"><?php echo htmlspecialchars($staff['email']); ?></p>
                            <?php if($staff['phone']): ?>
                                <p class="card-text"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($staff['phone']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; 
            if($current_department != '') echo '</div></div>'; ?>
        </div>
    </section>

    <!-- Volunteers -->
    <section class="volunteers py-5">
        <div class="container">
            <h2 class="section-title">Our Volunteers</h2>
            <div class="row">
                <?php while($volunteer = mysqli_fetch_assoc($volunteers_result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card team-card">
                        <div class="card-body text-center">
                            <div class="team-avatar">
                                <i class="fas fa-hands-helping"></i>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($volunteer['first_name'] . ' ' . $volunteer['last_name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($volunteer['email']); ?></p>
                            <?php if($volunteer['skills']): ?>
                                <div class="skills-tags">
                                    <?php foreach(explode(',', $volunteer['skills']) as $skill): ?>
                                        <span class="skill-tag"><?php echo htmlspecialchars(trim($skill)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <?php if($volunteer['availability']): ?>
                                <p class="card-text mt-3">
                                    <i class="fas fa-clock"></i> 
                                    <?php echo htmlspecialchars($volunteer['availability']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 